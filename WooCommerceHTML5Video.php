<?php
/**
 * Plugin Name: WooCommerce HTML5 Video
 * Plugin URI: http://www.webilop.com/products/woocommerce-html5-video/
 * Description: Show videos in product pages of your WooCommerce store using HTML5. It supports Mp4, Ogg, Webm and embedded videos from websites like youtube or vimeo
 * Author: Webilop
 * Author URI: http://www.webilop.com
 * Version: 1.7.7
 * License: GPLv2 or later
 */
namespace WooCommerceHTML5Video;

defined('ABSPATH') or die("No script kiddies please!");
include 'autoload.php';

class WooCommerceHTML5Video {

  public static function init() {
    //Activation / Deactivation hooks
    register_activation_hook(__FILE__, array(__CLASS__, 'check_plugin_activated'));
    register_uninstall_hook(__FILE__, array(__CLASS__, 'uninstall'));

    //Verify dependencies
    add_action('admin_init', array(__CLASS__, 'check_plugin_activated'));
    add_action('wp_head', array(__CLASS__, 'print_ajax_url'));

    //Woocommerce integration
    add_action('woocommerce_init', array(__CLASS__, 'init_woo_integration'));
    add_action('admin_footer-post.php',
      array('\\WooCommerceHTML5Video\\WooCommerceIntegrationBackend', 'popups_add_edit_video'));
    add_action('admin_footer-post-new.php',
      array('\\WooCommerceHTML5Video\\WooCommerceIntegrationBackend', 'popups_add_edit_video'));
    add_action('wp_ajax_oembed_video',
      array('\\WooCommerceHTML5Video\\WooCommerceIntegrationBackend', 'oembed_video'));

    //Settings
    add_action('admin_init',
      array('\\WooCommerceHTML5Video\\Settings', 'register_my_setting'));
    add_action('admin_menu',
      array('\\WooCommerceHTML5Video\\Settings', 'settings_page'));
    $plugin = plugin_basename( __FILE__ );
    add_filter("plugin_action_links_$plugin",
      array('\\WooCommerceHTML5Video\\Settings', 'add_settings_link'));
    add_action('admin_enqueue_scripts', array(__CLASS__, 'admin_scripts'));
    add_action('plugins_loaded', array(__CLASS__, 'plugin_textdomain'));

    add_action('admin_notices', array(__CLASS__, 'review_notice'));
    add_action('wp_ajax_save_review', array(__CLASS__, 'save_review'));
  }

/******************************************************************************/
/*           Activation / Deactivation hooks. Verify dependencies             */
/******************************************************************************/

  public static function init_woo_integration() {
    $version = get_option('wo_di_config_version');
    if ($version == false) {
      self::activate_plugin();
    }
    // backend stuff
    add_action('woocommerce_product_write_panel_tabs',
      array('\\WooCommerceHTML5Video\\WooCommerceIntegrationBackend', 'product_tab'));
    add_action('woocommerce_product_write_panels',
      array('\\WooCommerceHTML5Video\\WooCommerceIntegrationBackend', 'product_tab_content'));
    add_action('woocommerce_process_product_meta',
      array('\\WooCommerceHTML5Video\\WooCommerceIntegrationBackend', 'product_save_data'), 10, 2);

    // frontend stuff
    add_filter('woocommerce_product_tabs',
      array('\\WooCommerceHTML5Video\\WooCommerceIntegrationFrontend','video_product_tabs'), 25);
    add_action('woocommerce_product_tab_panels',
      array('\\WooCommerceHTML5Video\\WooCommerceIntegrationFrontend', 'video_product_tabs_panel'), 25);
  }

  /**
   * Check woocommerce dependency
   */
  public static function check_plugin_activated() {
    $plugin = is_plugin_active("woocommerce/woocommerce.php");
    if (!$plugin) {
      deactivate_plugins(plugin_basename(__FILE__));
      add_action('admin_notices', array(__CLASS__, 'disabled_notice'));
      if (isset($_GET['activate']))
        unset($_GET['activate']);
    }
    else {
      self::activate_plugin();
    }
  }

  public static function print_ajax_url() {
    ?>
    <script>
      var ajaxurl = '<?= admin_url('admin-ajax.php'); ?>';
    </script>
    <?php
  }

  /**
   * Things to do when the plugin is activated
   */
  private static function activate_plugin() {
    $version = get_option('wo_di_config_version');

    //Update database to version 2.0 if necessary
    if(is_bool($version) && $version == false):
      $products = get_posts(array(
        'post_type'      => array('product', 'product_variation'),
        'posts_per_page' => -1,
        'fields'         => 'ids'
      ));

      foreach ($products as $id):
        $video_type = get_post_meta($id, 'wo_di_video_type', true);
        $arrayJson = array();
        $size = 0;
        if ($video_type == 'embebido'):
          for ($i = 0; $i <= 2; $i++) {
            $video = get_post_meta($id, 'wo_di_video_product'.$i, true);
            $video = addslashes($video);
            if (!empty($video)) {
              $size++;
              //add video to new array
              $arrayJson[] = array(
                "type"     => "Embedded",
                "title"    => "",
                "width"    => "-",
                "height"   => "-",
                "embebido" => $video,
                "mp4"      => "",
                "ogg"      => "",
                "active"   => 1
              );
            }
          }
        elseif ($video_type == 'servidor'):
          $mp4 = get_post_meta($id, 'wo_di_video_url_mp4', true);
          $ogg = get_post_meta($id, 'wo_di_video_url_ogg', true);
          $height_video = get_post_meta($id, 'height_video_woocommerce', true);
          $width_video = get_post_meta($id, 'width_video_woocommerce', true);
          if (empty($height_video)) {
            $height_video = "-";
          }
          if (empty($width_video)) {
            $width_video = "-";
          }
          $size = 1;
          //add video to new array
          $arrayJson[] = array(
            "type"     => "WP Library",
            "title"    => "",
            "width"    => $width_video,
            "height"   => $height_video,
            "embebido" => "",
            "mp4"      => $mp4,
            "ogg"      => $ogg,
            "active"   => 1
          );
        endif;
        //Update new options
        update_post_meta($id, 'wo_di_number_of_videos', $size);
        update_post_meta($id, 'wo_di_video_product_videos', json_encode($arrayJson));

        //Delete old options
        delete_post_meta($id, 'wo_di_video_type');
        for ($i = 0; $i <= 2; $i++) {
          delete_post_meta($id, 'wo_di_video_product'.$i);
        }
        delete_post_meta($id, 'wo_di_video_product_html5');
        delete_post_meta($id, 'height_video_woocommerce');
        delete_post_meta($id, 'width_video_woocommerce');
        delete_post_meta($id, 'wo_di_video_url_mp4');
        delete_post_meta($id, 'wo_di_video_url_ogg');
        delete_post_meta($id, 'wo_di_video_check_mp4');
        delete_post_meta($id, 'wo_di_video_check_ogg');
      endforeach;
      delete_option( 'video_height' );
      delete_option( 'video_width' );
    endif;
    update_option('wo_di_config_version', 2);
  }

  /**
   * Message information when the plugin was deactivated
   */
  public static function disabled_notice() {
    global $current_screen;
    if ($current_screen->parent_base == 'plugins'):
      ?>
      <div class="error" style="padding: 8px 8px;">
        <strong>
          <?= __('WooCommerce HTML5 Video requires <a href="http://www.woothemes.com/woocommerce/" target="_blank">WooCommerce</a> activated in order to work. Please install and activate <a href="' . admin_url('plugin-install.php?tab=search&type=term&s=WooCommerce') . '" target="_blank">WooCommerce</a> first.','html5_video') ?>
        </strong>
      </div>
      <?php
    endif;
  }

  /**
   * Things to do when the plugin is deactivated
   */
  public static function uninstall() {
    $products = get_posts(array(
      'post_type'      => array('product', 'product_variation'),
      'posts_per_page' => -1,
      'fields'         => 'ids'
    ));
    //Delete post meta related with the plugin
    foreach ($products as $id) {
      delete_post_meta($id, 'wo_di_video_product_videos');
      delete_post_meta($id, 'wo_di_number_of_videos');
      delete_post_meta($id, 'wo_di_editormce_video');
      delete_option('wo_di_config_version');
      delete_option('wo_di_video_hide_tab');
      delete_option('wo_di_config_video_height');
      delete_option('wo_di_config_video_width');
      delete_option('wo_di_config_video_tab_name');
      delete_option('wo_di_video_size_forcing');
      delete_option('wo_di_video_disable_iframe');
      delete_option('wo_di_config_video_description');
      delete_option('wo_di_config_video_tab_position');
    }
  }

/******************************************************************************/
/*                               Other settings                               */
/******************************************************************************/

  public static function admin_scripts($hook) {
    wp_register_script('admin-notices', plugins_url('js/admin_notices.js', __FILE__), array('jquery'));
    wp_enqueue_script('admin-notices');

    //check if a product page is displayed (creation or edition)
    global $post;
    if(empty($post->post_type) || 'product' != $post->post_type || ($hook != 'post.php' && $hook != 'post-new.php'))
      return;

    wp_enqueue_script('media-upload');
    wp_enqueue_script('thickbox');
    wp_enqueue_style('thickbox');
    wp_enqueue_script("jquery-ui-core");
    wp_enqueue_script("jquery-ui-dialog");
    wp_enqueue_script("jquery-ui-sortable");
    wp_enqueue_script('tiny_mce');

    wp_register_script('jquery-validate', plugins_url('js/jquery.validate.min.js', __FILE__), array('jquery'));
    wp_enqueue_script('jquery-validate');

    wp_enqueue_media();

    wp_register_style('woohv-styles', plugins_url('css/style.css', __FILE__));
    wp_enqueue_style('woohv-styles' );

    wp_register_script('woohv-scripts', plugins_url('js/js-script.js', __FILE__), array('jquery'));
    wp_enqueue_script('woohv-scripts' );

    wp_register_script('my-upload', plugins_url('js/button_actions.js', __FILE__), array('jquery', 'media-upload', 'thickbox'));
    wp_enqueue_script('my-upload');
  }

  /**
   * Set up localization
   */
  public static function plugin_textdomain() {
    load_plugin_textdomain(
      'html5_video',
      false,
      dirname(plugin_basename(__FILE__)) . '/languages'
    );
  }

  /**
   * Print admin notice to ask for plugin review
   */
  public static function review_notice() {
    //verify option to check if user already dismiss or post the review
    $userId = get_current_user_id();
    $meta = get_user_meta($userId, 'woo_html5_review', true);
    if (empty($meta) || false == $meta): ?>
      <div id="review-notice" class="notice notice-info">
        <p>
          Help others to make good choices when they are seeking for plugins, please add a review in WooCommerce HTML5 Video and help us to create confidence in more people.
        </p>
        <p>
          <a id="post-review" href="https://wordpress.org/support/view/plugin-reviews/woocommerce-html5-video#postform" class="button-primary" target="_blank">Post review</a>
          <a id="skip-review" class="button-secondary" href="">Dismiss</a>
        </p>
      </div>
    <?php endif;
  }

  /**
   * Save that current user already made a review or doesn't want to make it
   */
  public static function save_review() {
    $userId = get_current_user_id();
    update_user_meta($userId, 'woo_html5_review', true);
  }
}

WooCommerceHTML5Video::init();
