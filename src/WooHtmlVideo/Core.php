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

        return self::$instance;
    }
}