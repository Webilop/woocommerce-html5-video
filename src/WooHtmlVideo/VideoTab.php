<?php
/**
 * Class to manage the the videos tab when a WooCommerce product is edited and displayed.
 * 
 * @since 2.0.0
 */
namespace Webilop\WooHtmlVideo;
defined('ABSPATH') or die("No script kiddies please!");

class VideoTab
{
    /**
     * Post type used to store the video tabs.
     */
    const POST_TYPE = 'wh5v-video-tab';
    const DATA_FIELDS = ['name', 'position'];

    /**
     * VideoTabForm
     */
    private $form;

    /**
     * WP_Post
     */
    private $post;

    /**
     * Plugin settings
     */
    private $settings;

    public function __construct($post, $settings)
    {
        $this->post = $post;
        $this->form = new VideoTabForm();
        $this->settings = $settings;
    }

    /**
     * Register the post type in WP to handle the videos tab.
     */
    public static function registerPostType()
    {
        register_post_type(self::POST_TYPE, [
            'label' => __('Videos Tab', 'wh5v'),
            'show_ui' => true,
            'show_in_menu' => false,
            'show_in_nav_menus' => false,
            'show_in_rest' => true,
            'supports' => ['editor'],
        ]);

        add_filter('allowed_block_types', function($allowed_blocks, $post){
            // check the post type
            if (self::POST_TYPE != $post->post_type) {
                return $allowed_blocks;
            }

            return array_merge([
                'core/paragraph',
                'core/heading'
            ], VideoTab::getAvailableVideoBlocks());
        }, 100, 2);
    }

    /**
     * Create a VideoTab instance for a product.
     * 
     * @param string $title Title for the video product post.
     * @param Settings $settings plugin settings.
     * 
     * @return VideoTab|null VideoTab object, otherwise null.
     */
    public static function create($title, $settings)
    {
        // create the post
        $id = wp_insert_post([
            'post_title' => $title,
            'post_type' => self::POST_TYPE,
            'post_status' => 'draft'
        ]);

        // if saving was sucessfully
        if ($id) {
            // return the object instance
            return new self(get_post($id), $settings);
        }

        return null;
    }

    /**
     * Get a video tab object.
     * 
     * @param string $id ID of the video tab.
     * @param Settings $settings plugin settings.
     * 
     * @return VideoTab|null VideoTab object, otherwise null.
     */
    public static function get($id, $settings)
    {
        // get the post
        $post = get_post($id);

        // check the post type
        if (self::POST_TYPE == $post->post_type) {
            return new self($post, $settings);
        }

        return null;
    }

    /**
     * Delete the video tab post.
     * 
     * @param string $id ID of the video tab.
     */
    public static function delete($id)
    {
        wp_delete_post($id, true);
    }

    /**
     * Get the ID of the video tab.
     * 
     * @return string ID of the video tab.
     */
    public function getId()
    {
        return (string)$this->post->ID;
    }

    /**
     * Get the data related to the video tab.
     * 
     * @return array data.
     */
    private function getData()
    {
        // get all meta data
        $meta_data = get_post_meta($this->getId());

        // filter the fields
        $data = [];
        foreach (self::DATA_FIELDS as $field) {
            if (!empty($meta_data[$field])) {
                $data[$field] = $meta_data[$field][0];
            }
        }

        return $data;
    }

    /**
     * Save the data related to the video tab.
     * 
     * @param array $data data to save.
     */
    private function saveData($data)
    {
        foreach (self::DATA_FIELDS as $field) {
            update_post_meta($this->getId(), $field, $data[$field] ?? '');
        }
    }

    /**
     * Show the form of the video tab.
     */
    public function showForm()
    {
        // get global settings
        $plugin_settings = $this->settings->getAll();
        $data = array_merge([
            'name' => $plugin_settings['tab_name'],
            'position' => $plugin_settings['tab_position'],
            'post_id' => $this->post->ID,
            'videos' => $this->countVideos()
        ], array_filter($this->getData()));
        $this->form->render($data);
    }

    /**
     * Save data of a video form.
     * 
     * @param array $data Data to save.
     */
    public function saveForm($data)
    {
        // filter and sanitize the data
        $data = $this->form->extractFormData($data);
        $data = $this->filterData($data);
        $data = $this->sanitizeData($data);

        // save the data
        $this->saveData($data);
    }

    /**
     * Filter the invalid values from data to show/save by the form.
     * 
     * @param $data array data.
     * 
     * @return array data filtered.
     */
    public function filterData($data): array
    {
        // filter invalid fields
        $valid_fields = self::DATA_FIELDS;
        return array_filter($data, function($v, $k) use ($valid_fields) {
            // check if the key is valid
            return \in_array($k, $valid_fields);
        }, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * Sanitize the values from the data to show/save by the form.
     * 
     * @param $data array data.
     * 
     * @return array data sanitized.
     */
    public function sanitizeData($data): array
    {
        // sanitize the name
        $name = $data['name'] ?? null;
        $name = is_string($name) ? trim(\strip_tags($name)) : '';

        // sanitize the position
        $position = $data['position'] ?? null;
        $position = \is_numeric($position) ? (int)$position : '';

        return compact('name', 'position');
    }

    /**
     * Register and show the video tab when a product is shown in the frontend.
     * 
     * @see WooCommerce hook action woocommerce_product_tabs
     */
    public function showVideoTab($tabs)
    {
        // get the data of the tab
        $data = $this->getData();

        // check if empty tabs should be displayed
        $show_empty_tab = $this->settings->get('show_empty_tab');
        if (!$show_empty_tab && 0 == $this->countVideos()) {
            return $tabs;
        }

        // use the same priority of the first element if the video tab should be in first position
        $priority = $pos == 1 && count($tabs) ? reset($tabs)['priority'] : 10;

        // build the info for the tab
        $tab_info = [
            'title' => $data['name'],
            'priority' => $priority,
            'callback' => [$this, 'renderTabContent'],
            'content' => $custom_tab_options['content']
        ];

        // insert the tab in the right position
        $new_tabs = [];
        $pos = 1;
        $inserted = false;
        foreach ($tabs as $k => $tab) {
            // if the position to insert is found
            if ($pos == $data['position']) {
                // add the videos tab first
                $new_tabs['html5_video'] = $tab_info;
                $inserted = true;
            }
            
            // add the current tab
            $new_tabs[$k] = $tab;
            if (!$inserted) {
                // use the priority of the previous tab for the videos tab
                $tab_info['priority'] = $tab['priority'];
            }

            $pos++;
        }

        // add the video tab if it is still not inserted
        if (!$inserted) {
            $new_tabs['html5_video'] = $tab_info;
        }

        return $new_tabs;
    }

    /**
     * Render the videos.
     */
    public function renderTabContent()
    {
        echo apply_filters('the_content', $this->post->post_content);
    }

    /**
     * Get the amount of videos added in the tab.
     * 
     * @return int amount of videos.
     */
    public function countVideos()
    {
        // list of available video blocks
        $video_block_names = self::getAvailableVideoBlocks();

        // parse the blocks in the content of the video tab
        $blocks = parse_blocks($this->post->post_content);

        // count video blocks
        $video_blocks = 0;
        foreach($blocks as $block) {
            if (\in_array($block['blockName'], $video_block_names)) {
                $video_blocks++;
            }
        }

        return $video_blocks;
    }

    /**
     * Get the available video blocks to use in the edition of the video tab posts.
     * 
     * @return array name of blocks.
     */
    public static function getAvailableVideoBlocks()
    {
        // TODO: filter dinamically based on the keyword video
        return [
            'core/html',
            'core/video',
            'core-embed/youtube',
            'core-embed/vimeo',
            'core-embed/dailymotion',
            'core-embed/videopress',
            'core-embed/wordpress-tv',
            'core-embed/tiktok',
        ];
    }
}