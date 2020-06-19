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

class Core {

    /** Singleton instance */
    private static $instance = null;

    /**
     * Private constructor of the singleton class.
     */
    private function __construct()
    {

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

        return self::$instance;
    }
}