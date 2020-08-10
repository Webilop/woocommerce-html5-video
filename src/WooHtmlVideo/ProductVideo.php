<?php
/**
 * Class to represent a video attached to a WooCommerce product.
 * 
 * @since 2.0.0
 */
namespace Webilop\WooHtmlVideo;
defined('ABSPATH') or die("No script kiddies please!");

abstract class ProductVideo
{
    /** video properties */
    private $title;
    private $width;
    private $height;

    public function __construct(string $title = null, string $width = null, string $height = null)
    {
        $this->title = $title;
        $this->width = $width;
        $this->height = $height;
    }

    /**
     * Get the title of the video.
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get the height of the video. 
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Get the width of the video.
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Create the HTML code to show the video.
     * 
     * @return string the HTML code.
     */
    public function render(): string
    {
        // prepare data to send to the template
        $title = $this->getTitle();
        $width = $this->getWidth();
        $height = $this->getHeight();

        // get the video content
        $video = $this->innerRender();

        // get the content of the template
        ob_start();
        include 'templates/product-video.php';
        $content = \ob_get_contents();
        \ob_end_clean();

        return $content;
    }

    /**
     * Create the HTML code of the video to inject in the main video template.
     * 
     * @return string the HTML code.
     */
    abstract protected function innerRender(): string;
}