<?php
/**
 * Plugin Name: WooCommerce HTML5 Video
 * Plugin URI: http://www.webilop.com/products/woocommerce-html5-video/
 * Description: Show videos in product pages of your WooCommerce store using HTML5. It supports Mp4, Ogg, Webm and embedded videos from websites like youtube or vimeo.
 * Author: Webilop
 * Author URI: http://www.webilop.com
 * Version: 2.0.0
 * License: GPLv2 or later
 */

namespace Webilop\WooHtmlVideo;
defined('ABSPATH') or die("No script kiddies please!");

define('WOOHV_VERSION', '2.0.0');

require dirname(__DIR__) . '/vendor/autoload.php';

/**
 * Bootstrap the plugin.
 */
$core = Core::init();