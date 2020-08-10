<?php
/**
 * Settings page to setup different options for the plugin.
 * 
 * @since 2.0.0
 */
namespace Webilop\WooHtmlVideo;
defined('ABSPATH') or die("No script kiddies please!");

class SettingsPage {

    /** ID for the settings page */
    private $page_id = 'html5-video-settings';

    /**
     * Settings object to handle settings management.
     */
    private $settings;
    private $options_group;

    public function __construct(PluginSettingsInterface $settings)
    {
        $this->settings = $settings;
        $this->options_group = sprintf('%s_group', $this->settings->getSettingsField());
    }

    /**
     * Register the options for the settings page.
     * 
     * The settings options are registered through the WP Settings API.
     */
    public function registerSettings()
    {
        // register the settings to store the options
        register_setting($this->options_group, $this->settings->getSettingsField(), [
            'type' => 'array',
            'show_in_rest' => false
        ]);

        // register a section in the settings page
        $general_section = 'general';
        add_settings_section($general_section, __('General', 'html5_video'), null, $this->page_id);

        // configuration of the fields
        $fields = [
            [
                'label' => __('Video tab name', 'html5_video'),
                'name' => 'tab_name',
                'type' => 'text'
            ],
            [
                'label' => __('Video tab position', 'html5_video'),
                'name' => 'tab_position',
                'type' => 'number',
                'attrs' => [
                    'min' => 1
                ]
            ],
            [
                'label' => __('Hide video tab if there are no videos', 'html5_video'),
                'name' => 'show_empty_tab',
                'type' => 'checkbox'
            ],
            // [
            //     'label' => __('Disable video tab description', 'html5_video'),
            //     'name' => 'disable_video_tab_description',
            //     'type' => 'checkbox'
            // ],
            [
                'label' => __('Default video width', 'html5_video'),
                'name' => 'default_video_width',
                'type' => 'text',
                'description' => __('Use a valid CSS value like 100% or 400px', 'html5_video')
            ],
            [
                'label' => __('Default video height', 'html5_video'),
                'name' => 'default_video_height',
                'type' => 'text',
                'description' => __('Use a valid CSS value like 100% or 400px', 'html5_video')
            ],
            /*[
                'label' => __('Allow to override video dimensions', 'html5_video'),
                'name' => '',
                'type' => ''
            ],*/
            // [
            //     'label' => __('Disable videos through iframes', 'html5_video'),
            //     'name' => 'disable_iframe_videos',
            //     'type' => 'checkbox'
            // ]
        ];

        // add the fields to the settings page
        $settings_values = $this->settings->getAll();
        foreach ($fields as $field) {
            add_settings_field($field['name'], $field['label'], function($data) use ($field) {
                // add default attrs list to the field
                if (empty($field['attrs']) || !is_array($field['attrs'])) {
                    $field['attrs'] = [];
                }
                // add value to the field
                if (isset($data['values'][ $field['name'] ])) {
                    $field['attrs']['value'] = $data['values'][ $field['name'] ];
                }

                // if it is a checkbox, check it if required
                if ('checkbox' == $field['type'] && !empty($field['attrs']['value'])) {
                    $field['attrs']['checked'] = 'checked';
                }

                // send the settings field to the template
                $settings_field = $this->settings->getSettingsField();

                // include field input template
                require dirname(__FILE__) . '/templates/settings-page-input.php';
            }, $this->page_id, $general_section, ['values' => $settings_values]);
        }
    }

    /**
     * Register the settings page in the Settings menu of the back-end.
     * 
     * The registration is done using the WP Settings API.
     */
    public function registerPage()
    {
        // register the settings page
        $hook = add_options_page(
            __('WooCommerce Html5 Video Settings', 'html5_video'),
            __('WooCommerce Html5 Video', 'html5_video'),
            'manage_options',
            $this->page_id,
            function() {
                // check user capabilities
                if (!current_user_can('manage_options')) {
                    return;
                }

                // send vars to the template
                $options_group = $this->options_group;
                $page_id = $this->page_id;

                // load template of the settings page
                require dirname(__FILE__) . '/templates/settings-page.php';
            }
        );
        // include CSS scripts to the settings page
        add_action("load-$hook", function(){
            //add CSS for settings page
            wp_register_style('wh5v-settings', WH5V_PLUGIN_URL . '/css/settings-styles.css');
            wp_enqueue_style('wh5v-settings');
        });
    }
}