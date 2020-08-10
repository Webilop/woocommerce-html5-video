<?php
/**
 * Class to manage the form of the video tab when a WooCommerce product is edited.
 * 
 * @since 2.0.0
 */
namespace Webilop\WooHtmlVideo;
defined('ABSPATH') or die("No script kiddies please!");

class VideoTabForm
{
    const DATA_GROUP = 'wh5v-data';

    public function extractFormData($data): array
    {
        // get the data related to the form from the POST data
        return $data[self::DATA_GROUP] ?? [];
    }

    public function render($data)
    {
        $args = [
            'group' => self::DATA_GROUP,
            'data' => $data
        ];

        // include the template
        include WH5V_PLUGIN_TEMPLATES . '/video-tab-form.php';
    }
}