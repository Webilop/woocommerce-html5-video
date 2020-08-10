<?php
/**
 * This class is the core of plugin, it loads the injector and initialize some of the objects on it, and attache the WP hooks to the different objects.
 *
 * It is a singleton: The creation of the objects and attaching to the hooks is done only one time in all the execution of a requests. It doesn't make sense to re-create and re-attach hooks.
 * It is a mediator: It loads the injector and communicate the different classes through the WP hooks.
 * 
 * @since 2.0.0
 */
namespace Webilop\WooHtmlVideo;
defined('ABSPATH') or die("No script kiddies please!");

use DI\ContainerBuilder;

class Core {

    /** Singleton instance */
    private static $instance = null;

    /** DI container */
    private $container;

    /**
     * Private constructor of the singleton class.
     */
    private function __construct()
    {
        // create a container for dependency injection
        // it uses autowiring based on type-hint. Interfaces need to be specified.
        $builder = new ContainerBuilder();
        $builder->addDefinitions([
            PluginSettingsInterface::class => \DI\create(Settings::class),
        ]);
        $this->container = $builder->build();
    }

    /**
     * Create the singleton instance.
     */
    public static function create()
    {
        // create the instance if it doesn't exist yet
        if (is_null(self::$instance)) {
            self::$instance = new Core();
        }
        
        return self::$instance;
    }

    /**
     * Setup the objects and hooks in the plugin.
     */
    public static function init()
    {
        // create the singleton instance
        self::create();

        // create the settings page
        $settings_page = self::$instance->container->get(SettingsPage::class);
        add_action('admin_init', [$settings_page, 'registerSettings']);
        add_action('admin_menu', [$settings_page, 'registerPage']);

        // add listener to hooks
        self::subscribeToHooks();

        return self::$instance;
    }

    /**
     * Get plugin container.
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Register the hooks to add the videos to the woocommerce products.
     */
    private static function subscribeToHooks()
    {
        // fire at init event
        add_action('init', function() {
            // register the videos post type
            VideoTab::registerPostType();

            // enqueue CSS and JS script on edition of products
            add_action('admin_enqueue_scripts', function($hook) {
                // check if it is the edition page of posts
                if (!in_array($hook, ['post.php', 'post-new.php'])) {
                    return;
                }

                // check if the post is a product
                if ('product' == get_post_type()) {
                    // include scripts
                    wp_enqueue_script('wh5v_edit_product', WH5V_PLUGIN_ASSETS_URL . '/js/edit-product-post.js', ['jquery']);
                    wp_localize_script('wh5v_edit_product', 'wh5v_edit_product', [
                        'ajaxurl' => admin_url('admin-ajax.php'),
                        'amount_label' => [
                            'singular' => __('video block', 'wh5v'),
                            'plural' => __('videos blocks', 'wh5v')
                        ]
                    ]);

                    // include styles
                    wp_enqueue_style('wh5v_edit_product', WH5V_PLUGIN_ASSETS_URL . '/css/edit-product-post.css');
                }

                // check if the post is a video tab
                if (VideoTab::POST_TYPE == get_post_type()) {
                    // include scripts
                    wp_enqueue_style('wh5v_edit_video_tab', WH5V_PLUGIN_ASSETS_URL . '/css/edit-video-tab-post.css');
                }
            });
        });

        // add action to delete not used video tabs
        add_action('wh5v_delete_orphan_video_tabs', 'ExtendedProduct::cleanOrphanVideoTabs');

        // add events in admin side only
        add_action('admin_init', function(){
            // check if cron is NOT active
            if (defined('DISABLE_WP_CRON') && DISABLE_WP_CRON) {
                $cleaning_option_name = 'wh5v_last_video_tabs_cleaning';
                $last_cleaning = get_option($cleaning_option_name);
                if (!$last_cleaning || (time() - 60*60*24) >= $last_cleaning) {
                    ExtendedProduct::cleanOrphanVideoTabs();
                    update_option($cleaning_option_name, time());
                }
            }
            // add cron job to delete unused video tab posts
            else if (! wp_next_scheduled('wh5v_delete_orphan_video_tabs')) {
                wp_schedule_event(time(), 'daily', 'wh5v_delete_orphan_video_tabs');
            }
        });

        // add the data tab for videos in the edition page of a product
        add_filter('woocommerce_product_data_tabs', function ($tabs) {
            $tabs['wh5v-videos'] = array(
                'label'    => __('Videos', 'wh5v'),
                'target'   => 'wh5v-videos',
                'priority' => 100,
            );
            return $tabs;
        });
        // add the content of the data tab with the videos form
        add_action('woocommerce_product_data_panels', function(){
            echo '<div id="wh5v-videos" class="panel woocommerce_options_panel hidden">';

            // build the extended product and show the videos form
            $extended_product = new ExtendedProduct(get_the_ID(), self::$instance->container->get(PluginSettingsInterface::class));
            $extended_product->getVideoTab()->showForm();

            echo '</div>';
        });

        // save the videos tab data when a product is saved
        add_action('save_post_product', function($post_id, $post, $update){
            // build the extended product and show the videos form
            $extended_product = new ExtendedProduct(get_the_ID(), self::$instance->container->get(PluginSettingsInterface::class));

            // save the video data
            $extended_product->getVideoTab()->saveForm($_POST);
        }, 10, 3);

        // delete the video tab when a product is deleted
        add_action('before_delete_post', function($post_id, $post = null){
            if ($post && 'product' == $post->post_type) {
                // build the extended product and show the videos form
                $extended_product = new ExtendedProduct($post_id, self::$instance->container->get(PluginSettingsInterface::class));

                // delete the video data
                $extended_product->deleteVideoTab();
            }
        }, 10, 2);

        // show the video tab in the frontend when the product is shown
        add_filter('woocommerce_product_tabs', function($tabs){
            // build the extended product
            $extended_product = new ExtendedProduct(get_the_ID(), self::$instance->container->get(PluginSettingsInterface::class));

            // register/show the videos tab
            return $extended_product->getVideoTab()->showVideoTab($tabs);
        }, 1000);

        // ajax request to get the amount of videos in a video tab post
        add_action('wp_ajax_wh5v_get_amount_videos', function() {
            // get the ID of the video tab post
            $id = $_GET['post_id'] ?? false;
            if ($id) {
                $video_tab = VideoTab::get(intval($id), self::$instance->container->get(PluginSettingsInterface::class));
                echo $video_tab->countVideos();
            }

            // finish ajax execution
            wp_die();
        });
    }
}