<?php
/**
 * Handle the settings of the plugin.
 * 
 * @since 2.0.0
 */
namespace Webilop\WooHtmlVideo;
defined('ABSPATH') or die("No script kiddies please!");

class Settings implements PluginSettingsInterface {

    /**
     * Field name where the settings are stored.
     */
    private $settings_field = 'woohv_config';

    /**
     * Get the options field name used in the database to store the settings.
     * 
     * @return string options name.
     */
    public function getSettingsField()
    {
        return $this->settings_field;
    }

    /**
     * Get a single settings value.
     * 
     * @param string $name Settings name to get.
     * 
     * @return mixed value stored.
     */
    public function get($name)
    {
        $settings = $this->getAll();
        return $settings[$name] ?? null;
    }

    /**
     * Get a all settings of the plugin.
     * 
     * @return array associative array with names and values of settings.
     */
    public function getAll()
    {
        $settings = get_option($this->settings_field, []);
        if (isset($settings['_t'])) {
            unset($settings['_t']);
        }
        return $settings;
    }

    /**
     * Save a single settings value.
     * 
     * @param string $name Settings name to save.
     * @param mixed Value to save. Must be serializable if non-scalar. Expected to not be SQL-escaped.
     * 
     * @return boolean true on success, otherwise false.
     */
    public function save($name, $value)
    {
        $settings = $this->getAll();
        $settings[$name] = $value;
        return $this->saveAll($settings);
    }

    /**
     * Save a single settings value.
     * 
     * @param array $settings associative array with names and values to store as settings. Each value must be serializable if non-scalar and it is expected to not be SQL-escaped.
     * 
     * @return boolean true on success, otherwise false.
     */
    public function saveAll($settings)
    {
        //save a timestamp when settings are saved to avoid a false response from update_option
        return update_option($this->settings_field, array_merge($settings, [
            '_t' => time() . '-' . rand(1000, 9999)
        ]));
    }
}