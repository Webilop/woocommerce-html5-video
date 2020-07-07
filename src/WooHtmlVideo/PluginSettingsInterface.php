<?php
/**
 * Interface to handle settings of the plugin.
 * 
 * @since 2.0.0
 */
namespace Webilop\WooHtmlVideo;
defined('ABSPATH') or die("No script kiddies please!");

interface PluginSettingsInterface
{
    /**
     * Get the options field name used in the database to store the settings.
     * 
     * @return string options name.
     */
    public function getSettingsField();

    /**
     * Get a single settings value.
     * 
     * @param string $name Settings name to get.
     * 
     * @return mixed value stored.
     */
    public function get($name);

    /**
     * Get a all settings of the plugin.
     * 
     * @return array associative array with names and values of settings.
     */
    public function getAll();

    /**
     * Save a single settings value.
     * 
     * @param string $name Settings name to save.
     * @param mixed Value to save. Must be serializable if non-scalar. Expected to not be SQL-escaped.
     * 
     * @return boolean true on success, otherwise false.
     */
    public function save($name, $value);

    /**
     * Save a single settings value.
     * 
     * @param array $settings associative array with names and values to store as settings. Each value must be serializable if non-scalar and it is expected to not be SQL-escaped.
     * 
     * @return boolean true on success, otherwise false.
     */
    public function saveAll($settings);
}