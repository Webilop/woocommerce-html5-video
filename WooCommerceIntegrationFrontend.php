<?php
namespace WooCommerceHTML5Video;

class WooCommerceIntegrationFrontend {

  const VIDEO_WIDTH = '100%';

  /**
   * creates the tab if the product has an associated video.
   * Add extra tabs to front end product page
   * attached to: woocommerce_product_tabs action
   */
  public static function video_product_tabs( $tabs ) {
    //$product is created by woocommerce
    global $post, $product;

    $tab_option = get_option('wo_di_config_video_tab_position');
    $index = (($tab_option == false && $tab_option != 0) || $tab_option == "") ? 1 : $tab_option;

    /* Due to it is unknown the priority of the other tabs, to locate tab video
       according to the index specified by the user in the configuration, we order
       the tasks according to their priority.*/
    $aux = array();

    foreach ($tabs as $key => $row)
      $aux[$key] = $row['priority'];

    array_multisort($aux, SORT_ASC, $tabs);

    /* Now, we assign a new priority to each tab according to the previous
       priority. In this way, the element with less priority (the element to
       appear first) is assigned to priority 0, and the next elemento to
       priority 5. Finally, the tab video is assigned to a priority associated
       to the index specified by the user in the configuration, i.e.
       5*wo_di_config_video_tab_position */

    $acum_priority = 0;
    $priority_video = 0;
    $current = 0;

    foreach ($tabs as $key => $row) {
      if ($current != $index) {
        $tabs[$key]["priority"] = $acum_priority;
      }
      else {
        $tabs[$key]["priority"] = $acum_priority + 5;
        $priority_video = $acum_priority;
        $acum_priority += 5;
      }

      $acum_priority += 5;
      $current++;
    }

    if($index >= $current)
      $priority_video = $acum_priority;


    if (self::product_has_video_tabs($product) || get_option("wo_di_video_hide_tab") == 1) {
      $tab_name_option = get_option('wo_di_config_video_tab_name');
      $tabname_config = strcmp($tab_name_option, "")? $tab_name_option : "Video";

      $custom_tab_options = array(
        'enabled' => get_post_meta($post->ID, 'custom_tab_enabled', true),
        'title'   => get_post_meta($post->ID, 'custom_tab_title', true),
        'content' => get_post_meta($post->ID, 'custom_tab_content', true),
      );

      if ($custom_tab_options['enabled'] != 'no') {
        $tabs['html5_video'] = array(
            'title'    => __($tabname_config,'html5_video'),
            'priority' => $priority_video,
            'callback' => array( __CLASS__, 'video_product_tabs_panel'),
            'content'  => $custom_tab_options['content']
          );
        }
    }
    return $tabs;
  }

  /**
   * Render the custom product tab panel content with the div which will
   *  show the video in html5 or embedded code.
   * attached to: woocommerce_product_tab_panels action
   * callback in: video_product_tabs
   */
  public static function video_product_tabs_panel() {
    global $product;
    $tinymce_content = htmlspecialchars_decode(get_post_meta($product->id, 'wo_di_editormce_video', true));
    $disable_desc = get_option('wo_di_config_video_description');

    if ($disable_desc == 0) {
      ?>
      <div><?= $tinymce_content ?></div>
      <?php
    }

    if (self::product_has_video_tabs($product)):
      $videos = json_decode(get_post_meta($product->id, 'wo_di_video_product_videos', true));
      if (!is_null($videos)):
        $width_config = get_option('wo_di_config_video_width');
        $height_config = get_option('wo_di_config_video_height');
        $disable_iframe = get_option('wo_di_video_disable_iframe');
        $size_forcing = get_option('wo_di_video_size_forcing');
        foreach ($videos as $video):
          if ($video->active == 1):
            if (!empty($video->title) && ($video->type != "Embedded" ||
                ($video->type == "Embedded" && $disable_iframe == 0))) {
              ?>
              <h3><?= $video->title ?></h3>
              <?php
            }
            $width = 0;
            $height = 0;

            if ($size_forcing == 1) {
              $width = $width_config;
              $height = $height_config;
            }
            else {
              if (empty($video->width)) {
                if (!empty($width_config) && $width_config != 0) {
                  $width = $width_config;
                }
                else {//REVIEW
                  $width = self::VIDEO_WIDTH;
                }
              }
              else {
                $width = $video->width;
              }
              if (empty($video->height)) {
                if (!empty($height_config) && $height_config != 0) {
                  $height = $height_config;
                }
                else {
                  $height = '';
                }
              }
              else {
                $height = $video->height;
              }
            }
            switch ($video->type) {
              case 'Embedded':
                if ($disable_iframe == 0)
                  echo $video->embebido;
                break;

              case 'oEmbed':
                global $wp_embed;
                echo $wp_embed->run_shortcode('[embed width="' . $width . '" height="' . $height . '"]' . $video->url . '[/embed]');
                break;

              case 'WP Library':
                ?>
                  <video width="<?= $width ?>" height="<?= $height ?>" controls>
                    <?php if ($video->mp4 != "") { ?>
                      <source src="<?= $video->mp4 ?>" type="video/mp4" />
                    <?php } ?>
                    <?php if ($video->ogg != "") { ?>
                      <source src="<?= $video->ogg ?>" type="video/ogg" />
                    <?php } ?>
                    <p>
                      <?= __("Your browser does not support HTML5","html5_video") ?>
                    </p>
                  </video>
                <?php
                break;
            }
            ?>
            <br>
            <?php
          endif;
        endforeach;
      endif;
    endif;
    ?>
    <p style="font-size:10px;color:#999;">
      <?= __("Video embedding powered by","html5_video") ?>
      <a target="_blank" title="Web + mobile development" rel="nofollow" href="http://www.webilop.com/products/woocommerce-html5-video/">Webilop</a>
    </p>
    <?php
  }

/******************************************************************************/
/*                                  Auxiliar                                  */
/******************************************************************************/

  /**
   * check if a product has an associated video. and save the video in the global variable codigo_video,
   * also saves a message for use in method video_product_tabs_panel.
   */
  private static function product_has_video_tabs($product) {
    $number_videos = get_post_meta($product->id, 'wo_di_number_of_videos', true);
    if ($number_videos > 0) {
      $videos = json_decode(get_post_meta($product->id, 'wo_di_video_product_videos', true));
      foreach ($videos as $video) {
        if ($video->active == 1) {
          return true;
        }
      }
      return false;
    }
    else {
      return false;
    }
  }
}
