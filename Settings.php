<?php
namespace WooCommerceHTML5Video;

class Settings {
  /**
   * Function to register plugin settings
   * attached to: admin_init action
   */
  public static function register_my_setting() {
    register_setting( 'dimensions_group', 'wo_di_config_video_width', 'intval' );
    register_setting( 'dimensions_group', 'wo_di_config_video_height', 'intval' );
    register_setting( 'dimensions_group', 'wo_di_config_video_tab_name' );
    register_setting( 'dimensions_group', 'wo_di_config_video_tab_position', 'intval' );
    register_setting( 'dimensions_group', 'wo_di_video_hide_tab', 'intval' );
    register_setting( 'dimensions_group', 'wo_di_video_size_forcing', 'intval' );
    register_setting( 'dimensions_group', 'wo_di_video_disable_iframe', 'intval' );
    register_setting( 'dimensions_group', 'wo_di_config_video_description', 'intval' );
    register_setting( 'dimensions_group', 'wo_di_webilop_advertisement', 'intval' );
  }

  /**
   * Function to add a plugin configuration page
   * attached to: admin_menu action
   */
  public static function settings_page() {
    $hook = add_options_page(
      'WooCommerce Html5 Video Settings',
      'WooCommerce Html5 Video',
      'manage_options',
      'html5-video-settings',
      array(__CLASS__, 'settings_page_content')
    );
    add_action("load-$hook",
      array('\\WooCommerceHTML5Video\\Premium', 'check_plugin'));
  }

  /**
   * Function to add settngs link in plugins page
   * attached to: plugin_action_links_<plugin> filter
   */
  public static function add_settings_link( $links ) {
    ob_start();
    ?>
    <a href="options-general.php?page=html5-video-settings">Settings</a>
    <?php
    $settings_link = ob_get_contents();
    ob_end_clean();
    array_push( $links, $settings_link );
    ob_start();
    ?>
    <a title="documentation" target="_blank" href="http://www.webilop.com/products/woocommerce-html5-video/">Docs</a>
    <?php
    $docs_link = ob_get_contents();
    ob_end_clean();
    array_push( $links, $docs_link);
    return $links;
  }

  /**
   * Function to create the content of the configuration page
   * callback in: settings_page
   */
  public static function settings_page_content() {
    if (!current_user_can('manage_options')) {
      wp_die(__('You do not have sufficient permissions to access this page.'));
    }
    ?>
    <div class="wrap">
      <?php screen_icon(); ?>
      <h2>WooCommerce Html5 Video Settings</h2>
      <form class="html5_video" method="post" action="options.php" onsubmit="return check_form_settings();">
        <?php
        settings_fields('dimensions_group');
        do_settings_fields('dimensions_group','html5-video-settings');
        ?>
        <p>
          <strong>
            <?= __('Configure the default video dimensions and the video tab name')?>:
          </strong>
        </p>
        <table class="form-table">
          <tr valign="top">
            <th scope="row">
              <?= __('Video Tab Name')?>:
            </th>
            <td>
              <?php
              if (strcmp(get_option('wo_di_config_video_tab_name'), ""))
                $value = get_option('wo_di_config_video_tab_name');
              else
                $value = "Video";
              ?>
              <input type="text" name="wo_di_config_video_tab_name" value="<?= $value ?>" />
            </td>
          </tr>
          <tr valign="top">
            <th scope="row">
              <?= __('Video Width')?>:
            </th>
            <td>
              <input type="text" name="wo_di_config_video_width" id="wo_di_config_video_width" value="<?= get_option('wo_di_config_video_width'); ?>" />
            </td>
          </tr>
          <tr valign="top">
            <th scope="row">
              <?= __('Video Height')?>:
            </th>
            <td>
              <input type="text" name="wo_di_config_video_height" id="wo_di_config_video_height" value="<?= get_option('wo_di_config_video_height'); ?>" />
            </td>
          </tr>
          <tr valign="top">
            <th scope="row">
              <?= __('Force video dimensions (it does not work with iframes)','html5_video')?>:
            </th>
            <td>
              <?php $checked = (get_option('wo_di_video_size_forcing') == 1)? "checked" : ""; ?>
              <input type="checkbox" name="wo_di_video_size_forcing" id="wo_di_video_size_forcing" <?= $checked ?> value="1" />
            </td>
          </tr>
          <tr valign="top">
            <th scope="row">
              <?= __('Video Tab Position (0-index)')?>:
            </th>
            <td>
              <?php
              if (strcmp(get_option('wo_di_config_video_tab_position'), ""))
                $value = get_option('wo_di_config_video_tab_position');
              else
                $value = "1";
              ?>
              <input type="text" name="wo_di_config_video_tab_position" value="<?= $value ?>" />
            </td>
          </tr>
          <tr valign="top">
            <th scope="row">
              <?= __('Show video tab if there is no video','html5_video')?>:
            </th>
            <td>
              <?php $checked = (get_option('wo_di_video_hide_tab') == 1)? "checked" : ""; ?>
              <input type="checkbox" name="wo_di_video_hide_tab" <?= $checked ?> value="1" />
            </td>
          </tr>
          <tr valign="top">
            <th scope="row">
              <?= __('Disable embedded videos with iframes','html5_video')?>:
            </th>
            <td>
              <?php $checked = (get_option('wo_di_video_disable_iframe') == 1)? "checked" : ""; ?>
              <input type="checkbox" name="wo_di_video_disable_iframe" <?= $checked ?> value="1" />
            </td>
          </tr>
          <tr valign="top">
            <th scope="row">
              <?= __('Disable general video description','html5_video')?>:
            </th>
            <td>
              <?php $checked = (get_option('wo_di_config_video_description') == 1)? "checked" : ""; ?>
              <input type="checkbox" name="wo_di_config_video_description" <?= $checked ?> value="1" />
            </td>
          </tr>
          <?php if (isset($_SESSION['premium']) && true === $_SESSION['premium']): ?>
            <tr valign="top">
              <th scope="row">
                <?= __('Hide Webilop\'s advertisement under videos','html5_video')?>:
              </th>
              <td>
                <?php $checked = (get_option('wo_di_webilop_advertisement') == 1)? "checked" : ""; ?>
                <input type="checkbox" name="wo_di_webilop_advertisement" <?= $checked ?> value="1" />
              </td>
            </tr>
          <?php endif; ?>
        </table>
        <span id="span_errors"></span>
        <?php submit_button(); ?>
        <span>
          <a title="WooCommerce HTML5 Video" href="http://www.webilop.com/products/woocommerce-html5-video/" target="_blank">WooCommerce Html5 Video Documentation</a>
        </span>
      </form>

      <?php if (!isset($_SESSION['premium']) || false == $_SESSION['premium']): ?>
        <h3>Get access to premium features by $XX</h3>
        <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="top">
          <input type="checkbox" name="terms" id="terms" value="accept" required> <label for="terms">Accept <a href="http://dev.webilop.com/webilop-3.0/" target="_blank">terms and conditions</a> </label><br/>
          <input type="hidden" name="cmd" value="s-xclick">
          <input type="hidden" name="hosted_button_id" value="D8SDYVG855UZ8">
          <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynow_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
          <input type="hidden" name="notify_url" value="http://dev.webilop.com/webilop-3.0/?AngellEYE_Paypal_Ipn_For_Wordpress&action=ipn_handler">
          <input type="hidden" name="return" value="<?= admin_url('options-general.php?page=html5-video-settings&paypal_status=success') ?>">
          <input type="hidden" name="cancel_return" value="<?= admin_url('options-general.php?page=html5-video-settings&paypal_status=cancel') ?>">
          <input type="hidden" name="on0" value="domain" />
          <input type="hidden" name="os0" value="<?= home_url() ?>" />
          <input type="hidden" name="on1" value="concept" />
          <input type="hidden" name="os1" value="woocomerce-html5-video" />
          <img alt="" border="0" src="https://www.paypalobjects.com/es_XC/i/scr/pixel.gif" width="1" height="1">
        </form>
      <?php
      endif;
      unset($_SESSION['premium']);
      ?>

      <table class="rate-about-table">
        <tr>
          <td>
            <div class="rate-div">
              <h3 class="hndle">
                <?php _e('Rate it!', 'html5_video') ?>
              </h3>
              <p>
                <?php _e('If you like this plugin please'); ?> <a title="rate it" href="https://wordpress.org/support/view/plugin-reviews/woocommerce-html5-video" target="_blank">leave us a rating</a>. In advance, thanks from Webilop team! &nbsp;
                <span class="rating">
                  <input type="radio" class="rating-input" id="rating-input-1-5" name="rating-input-1" onclick="window.open('https://wordpress.org/support/view/plugin-reviews/woocommerce-html5-video')">
                  <label for="rating-input-1-5" class="rating-star"></label>
                  <input type="radio" class="rating-input" id="rating-input-1-4" name="rating-input-1" onclick="window.open('https://wordpress.org/support/view/plugin-reviews/woocommerce-html5-video')">
                  <label for="rating-input-1-4" class="rating-star"></label>
                  <input type="radio" class="rating-input" id="rating-input-1-3" name="rating-input-1" onclick="window.open('https://wordpress.org/support/view/plugin-reviews/woocommerce-html5-video')">
                  <label for="rating-input-1-3" class="rating-star"></label>
                  <input type="radio" class="rating-input" id="rating-input-1-2" name="rating-input-1" onclick="window.open('https://wordpress.org/support/view/plugin-reviews/woocommerce-html5-video')">
                  <label for="rating-input-1-2" class="rating-star"></label>
                  <input type="radio" class="rating-input" id="rating-input-1-1" name="rating-input-1" onclick="window.open('https://wordpress.org/support/view/plugin-reviews/woocommerce-html5-video')">
                  <label for="rating-input-1-1" class="rating-star"></label>
                </span>
              </p>
            </div>
          </td>
        </tr>
        <tr>
          <td>
            <div class="about-webilop">
              <h3 class="hndle">
                <?php _e('About','html5_video');?>
              </h3>
              <div class="inside">
                <p>
                  <strong>WooCommerce Html5 video </strong><?php _e('was developed by ', 'html5_video');?><a title="Webilop. web and mobile development" href="http://www.webilop.com" target="_blank">Webilop</a>
                </p>
                <p>
                  <?php _e('Webilop is a company focused on web and mobile solutions. We develop custom mobile applications and templates and plugins for CMSs such as Wordpress and Joomla!', 'html5_video');?>
                </p>
                <div>
                  <h4><?php _e('Follow us', 'html5_video')?></h4>
                  <a title="Facebook" href="https://www.facebook.com/webilop" target="_blank"><img src="<?= WP_PLUGIN_URL;?>/woocommerce-html5-video/images/facebook.png"></a>
                  <a title="LinkedIn" href="http://www.linkedin.com/company/webilop" target="_blank"><img src="<?= WP_PLUGIN_URL;?>/woocommerce-html5-video/images/linkedin.png"></a>
                  <a title="Twitter" href="https://twitter.com/webilop" target="_blank"><img src="<?= WP_PLUGIN_URL;?>/woocommerce-html5-video/images/twitter.png"></a>
                  <a title="Google Plus" href="https://www.google.com/+Webilop" target="_blank" rel="publisher"><img src="<?= WP_PLUGIN_URL;?>/woocommerce-html5-video/images/gplus.png"></a>
                </div>
              </div>
            </div>
          </td>
        </tr>
      </table>
    </div>
   <?php
  }

  public static function paypal_notice() {
    $page = isset($_GET['page'])? $_GET['page'] : '';
    $paypal_status = isset($_GET['paypal_status'])? $_GET['paypal_status'] : '';
    if (!empty($page) && 'success' == $paypal_status) {
      $class = "notice-success";
      $message = "Payment successfully finished. Once it has been verified, premium features will be enabled";
    }
    elseif (!empty($page) && 'cancel' == $paypal_status) {
      $class = "notice-error";
      $message = "Payment canceled";
    }

    if (isset($class) && isset($message)) {
      ?>
      <div class="notice <?= $class ?>">
        <p><?= $message ?></p>
      </div>
      <?php
    }
  }
}
