=== WooCommerce HTML5 Video ===

Contributors: webilop
Tags: woocommerce, woocommerce video, woocommerce add-on, online store, product video, html5 video, mp4, ogg
Requires at least: 4.0
Tested up to: 4.4.2
Stable tag: 1.7.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

> #### IMPORTANT:
> If your plugin has a version less than 1.5.0, then it is highly recommended to create a backup of your database before upgrade.
> From version 1.5.4 of WooCommerce HTML5 Video, it is required WordPress 4.0 at least.

> #### Collaboration
> [The plugin is available in Github](https://github.com/Webilop/woocommerce-html5-video). We receive patches to fix bugs and translation files.

WooCommerce HTML5 Video is a WooCommerce add-on which allows you to add videos to products in your online store. The plugin creates a new tab in the product description page where all videos related to the product are placed.

This plugin uses HTML5 to render videos in your products. The supported video formats are MP4, Ogg and Webm. It also support embedded videos from websites like [youtube](https://support.google.com/youtube/answer/171780?hl=en), [vimeo](https://vimeo.com/help/faq/sharing-videos/embedding-videos) and others.

= Localization =

*English (default).

*Spanish

*Persian(Outdated)

*Chinese(Outdated)

If you want to contribute with the localization of this plugin, you can contribute in the [Github repository](https://github.com/Webilop/woocommerce-html5-video) or send us your .mo and .po files to contact[at]webilop.com

= Documentation =

The plugin has [documentation available in English](http://www.webilop.com/products/woocommerce-html5-video/) and the [documentation in Spanish](http://www.webilop.com/es_ES/productos/woocommerce-html5-video-2/) is also available.

== Installation ==

1. Install and activate the [woocommerce plugin](https://wordpress.org/plugins/woocommerce/).
1. Upload the `woocommerce-html5-video` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Create your products through 'the Products' menu option
4. Go to the Edit page of one of the created products. Under the 'Product Data' box, select the 'Video' option at the bottom.
Select the video source: Embedded code or uploaded video.
5. Update the product.

== Frequently Asked Questions ==

= Which video format can I upload and use with the plugin? =
The supported video formats are MP4, Ogg and Webm.

= Can I use embedded videos from websites like youtube or vimeo with the plugin? =
Yes, you can. When you are including videos in your products, just select the option to include embedded videos or use the option URL and paste the URL of the youtube or vimeo page.

= I cannot see the video in the frontend =
Make sure your [browser supports HTML5 video](http://www.w3schools.com/html/html5_video.asp).

== Screenshots ==

1. Options available in the 'Edit Product' pages in WP backend.
2. Addition of viedo to a product.
3. Rendering of video in product page.

== Changelog ==

= 1.0 =
* First general availability plug-in version

= 1.1 =
* Settings page was added.

= 1.2 =
* Persian localization was added. Special thanks to Khalil Delavaran (khalil.delavaran[at]gmail.com) for this contribution.

= 1.3 =
* $ replaced with jQuery to avoid conflicts in javascript files

= 1.3.1 =
* Fix of bug loading styles and scripts. Fix of bugs with the names of some functions. Organization of translation files in the folder structure.
* Chinese localization was added. Special thanks to Jinlin Cui(崔金林) (cuijinlin@gmail.com) for this contribution.

= 1.3.2 =
* Fix of a bug that will always displays the video tab even if there is no video. 
* Addition of the option in the video settings page to Show the video tab if there is no videos for the product.
* Addition of description for the videos of a product. You can use a tinyMCE editor to save a description of the videos, it will show the description above the videos in the video tab.

= 1.5.0 =
* No limits in the amount of videos to attach per product.
* New UI to manage videos attached to products.
* Compatibility up to WordPress 4.0.1
* Fix of bugs.

= 1.5.1 =
* Adjustment in powered by Webilop link to not affect SEO in websites.

= 1.5.2 =
* Fix of video icon in product addition and edition pages.
* Creation of [Github repository with the plugin code](https://github.com/Webilop/woocommerce-html5-video).

= 1.5.3 =
* Deletion of useless name property in video management for products.
* Replacement of HTML editor by tinymce editor with default WordPress settings in modal to add videos in products.
* Now users can use a custom title for the video tab.
* Replacement of action icons in video management for products.

= 1.5.4 =
* Sorting videos in products is now available.
* Change of media uploader modal to link videos to products.

= 1.6 =
* Addition of preview option to watch videos in product management page.
* Addition of feature to force size dimensions of all videos in products.
* Fix of bug in placeholder of size values in product video addition.

= 1.7 =
* Ability to hide general descriptions of videos in video tab.
* Allow to configure position of video tab between product tabs.
* Code refactoring.

= 1.7.1 =
* Fix bug of scripts loading in invalid and not useful sections.

= 1.7.2 =
* Fix bug of scripts loading in list of posts, pages and custom post types.

= 1.7.3 =
* Fix encoding video titles on saving of products.

= 1.7.4 =
* Fix encoding video data on saving of products for PHP versiones under 5.4.0.
* Fix on active checkbox in list of videos in products.

= 1.7.5 =
* Fix on active checkbox in table of videos attached to a product.
* Support for preview mode for oEmbedded videos.
* Addition of notice message about reviews of the plugin.
* Change on modal window to select or upload videos in the media library.
* Modification of styles and structure of modal window to add videos to a product.
* Support for Webm video format.
* Modifications of styles in table of videos attached to a product.
