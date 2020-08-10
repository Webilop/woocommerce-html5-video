<?php
/**
 * Class to extend a WooCommerce product and include a videos tab.
 * 
 * It manage and render videos when a product is edited and when it is displayed.
 * 
 * @since 2.0.0
 */
namespace Webilop\WooHtmlVideo;
defined('ABSPATH') or die("No script kiddies please!");

class ExtendedProduct
{
    const VIDEO_TAB_META = 'wh5v-video-tab';

    // woo product
    private $product_id;

    // video tab
    private $tab;

    /**
     * Constructor
     * 
     * @param string $product_id ID of product to extend.
     * @param Settings $settings plugin settings.
     */
    public function __construct($product_id, $settings)
    {
        $this->product_id = $product_id;

        // check if the product already has a video tab
        $tab_id = $this->getVideoTabId();
        if ($tab_id) {
            $this->tab = VideoTab::get($tab_id, $settings);
        }
        else {
            $this->createVideoTab($settings);
        }
    }

    /**
     * Create a video tab for the product.
     *
     * @param Settings $settings plugin settings.
     */
    protected function createVideoTab($settings)
    {
        // validate if the video tab already exist
        if ($this->tab) {
            return;
        }

        // create the video tab
        $this->tab = VideoTab::create(sprintf('Product #%d', $this->product_id), $settings);
        $this->saveVideoTabId();
    }

    /**
     * Get the video tab of a product.
     * 
     * @return VideoTab|false VideoTab object, false otherwise.
     */
    public function getVideoTab()
    {
        return $this->tab;
    }

    /**
     * Delete the video tab linked to a product.
     */
    public function deleteVideoTab()
    {
        if ($this->tab) {
            VideoTab::delete($this->tab->getId());
        }
    }

    /**
     * Get the ID of the video tab linked to a product.
     * 
     * @return string ID of the video tab.
     */
    public function getVideoTabId()
    {
        return get_post_meta($this->product_id, self::VIDEO_TAB_META, true);
    }

    /**
     * Save the ID of a video tab with the product.
     * 
     * @return boolean true on success, othewise false.
     */
    protected function saveVideoTabId()
    {
        if ($this->tab) {
            return update_post_meta($this->product_id, self::VIDEO_TAB_META, $this->tab->getId());
        }

        return false;
    }

    /**
     * Delete video tab posts belonging to unexisting products.
     */
    public static function cleanOrphanVideoTabs()
    {
        // make query to get video tabs without a product linked
        global $wpdb;
        $linked_videos_query = \sprintf("SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE meta_key = '%s'", self::VIDEO_TAB_META);
        $delete_unlinked_videos_query = \sprintf("DELETE FROM {$wpdb->prefix}posts WHERE post_type = '%s' AND ID NOT IN (%s)", VideoTab::POST_TYPE, $linked_videos_query);
        return $wpdb->query($delete_unlinked_videos_query);
    }
}