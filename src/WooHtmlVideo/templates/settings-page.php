<?php
/**
 * Template to create the HTML code of the settings page of the plugin.
 */
?>
<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    <div class="wh5v-general-settings-container">
        <form action="options.php" method="post">
        <?php
            // output security fields for the registered settings
            settings_fields($options_group);
            // output setting sections and their fields of the page
            do_settings_sections($page_id);
            // output save settings button
            submit_button( 'Save Settings' );
        ?>
        </form>
    </div>

    <div class="whv5-right-sidebar">
        <div class="rate-div">
            <h2><?= __('Rate it!', 'html5_video') ?></h2>
            <p>
                <?php $ratingLink = "https://wordpress.org/support/view/plugin-reviews/woocommerce-html5-video"; ?>
                <?php printf(__('If you like this plugin or have some suggestion, please %s and help us to improve. In advance, thanks from the Webilop team!', 'html5_video'), '<a href="' . $ratingLink . '" target="_blank">' . __('share your feedback', 'html5_video') . '</a>'); ?>
            </p>
            <a class="stars-rating-link" href="<?php echo $ratingLink; ?>" target="_blank" title="<?php _e('Rate the plugin', 'html5_video') ?>">
                <span class="dashicons dashicons-star-filled"></span>
                <span class="dashicons dashicons-star-filled"></span>
                <span class="dashicons dashicons-star-filled"></span>
                <span class="dashicons dashicons-star-filled"></span>
                <span class="dashicons dashicons-star-filled"></span>
            </a>
        </div>
        <div class="about-webilop">
          <h2><?= __('About','html5_video');?></h3>
          <div class="inside">
            <p>
              <strong>WooCommerce Html5 video </strong><?php _e('was developed by ', 'html5_video');?><a title="Webilop. web and mobile development" href="http://www.webilop.com" target="_blank">Webilop</a>.
            </p>
            <p>
              <?php _e('Webilop is a company focused on web and mobile solutions. We can help you to build your website, we are experts on WordPress.', 'html5_video');?>
            </p>
          </div>
        </div>
    </div>
</div>