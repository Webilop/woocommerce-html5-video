<?php
namespace WooCommerceHTML5Video;

class WooCommerceIntegrationBackend {

  /**
   * Creates tab in the product creation/edition page
   * attached to: woocommerce_product_write_panel_tabs action
   */
  public static function product_tab() {
    ?>
    <li class='html5_video'>
      <a href='#video_tab'><?= __('Video','html5_video') ?></a>
    </li>
    <?php
  }

  /**
   * Print the admin panel content
   * attached to: woocommerce_product_write_panels action
   */
  public static function product_tab_content() {
    ?>
    <div id="video_tab" class="panel woocommerce_options_panel">
      <?php self::video_tab_form(array(
        'id'          => '_tab_video',
        'label'       => __('Embed Code','html5_video'),
        'placeholder' => __('Place your embedded video code here.','html5_video'),
        'style'       => 'width:70%;height:21.5em;'
      )); ?>
    </div>
    <?php
  }

  /**
   * Update post meta when the product is saved
   * attached to: woocommerce_process_product_meta action
   */
  public static function product_save_data($post_id, $post) {
    $number_of_videos = $_POST['wo_di_number_of_videos'];
    $arrayJson = array();
    update_post_meta($post_id, 'wo_di_number_of_videos', $number_of_videos);
    if ($number_of_videos > 0) {
      $video_types = $_POST['wo_di_video_types'];
      $video_titles = $_POST['wo_di_video_titles'];
      $video_embebido = $_POST['wo_di_video_embebido'];
      $videoUrl = $_POST['wo_di_video_url'];
      $video_mp4 = $_POST['wo_di_video_mp4'];
      $video_ogg = $_POST['wo_di_video_ogg'];
      $video_webm = $_POST['wo_di_video_webm'];
      $video_width = $_POST['wo_di_video_widths'];
      $video_height = $_POST['wo_di_video_heights'];
      $video_active = $_POST['wo_di_video_active'];
      //Organize every video information to set the post meta
      foreach ($video_types as $key => $type) {
        $arrayJson[] = array(
          "type"     => $type,
          "title"    => $video_titles[$key],
          "width"    => $video_width[$key],
          "height"   => $video_height[$key],
          "embebido" => $video_embebido[$key],
          'url'      => $videoUrl[$key],
          "mp4"      => $video_mp4[$key],
          "ogg"      => $video_ogg[$key],
          "webm"      => $video_webm[$key],
          "active"   => $video_active[$key]
        );
      }
    }
    //encode data
    $data = '5.4.0' <= phpversion() ? json_encode($arrayJson, JSON_UNESCAPED_UNICODE) : json_encode($arrayJson);
    update_post_meta($post_id, 'wo_di_video_product_videos', $data);
    //update text of tinymce editor
    $mce_editor_content = $_POST['wo_di_editormce_video'];
    update_post_meta($post_id, 'wo_di_editormce_video', $mce_editor_content);
  }

  /**
   * Popups for add and edit videos.
   * attached to: admin_footer-post.php and admin_footer-post-new.php actions
   */
  public static function popups_add_edit_video() {
    global $post;
    if($post->post_type == "product"):
      $placeholder = __('Place your embedded video code here.','html5_video');
      $disable_iframe = get_option('wo_di_video_disable_iframe');
      ?>
      <?php //When adding a video ?>
      <div id="dialog_form_add_video" title="<?= __("Add Video", 'html5_video') ?>" style="display: none;">
        <form id="wo_di_form_add_video" action="<?= admin_url( 'admin-ajax.php' )?>" method="post" onsubmit="return false;">
          <fieldset>
            <div id="div_errores_add_video"></div>
            <div class="options_group">
              <label for="wo_di_video_title">
                <?= __("Title: ","html5_video") ?>
              </label>
              <input class="wo_di_form_input" id="wo_di_video_title" type="text"  value="" name="wo_di_video_title" style="margin-left: 21px;">
            </div>
            <?php
            if (get_option('wo_di_video_size_forcing') == 1) {
              $width = "value='" . get_option('wo_di_config_video_width') . "' readonly";
              $height = "value='" . get_option('wo_di_config_video_height') . "' readonly";
            }
            else {
              $width = "";
              $height = "";
            }
            ?>
            <div class="options_group">
              <label for="width_video_woocommerce">
                <?= __("Width","html5_video")?>:
              </label>
              <input type="text" id="width_video_woocommerce" name="width_video_woocommerce" placeholder="<?= get_option('wo_di_config_video_width'); ?>" <?= $width ?> class="dimension-input">
              <label for="height_video_woocommerce">
                <?php echo __("Height","html5_video")?>:
              </label>
              <input type="text" id="height_video_woocommerce" name="height_video_woocommerce" placeholder="<?= get_option('wo_di_config_video_height'); ?>" <?= $height ?> class="dimension-input">
            </div>
            <div class="options_group">
              <label>
                <?= __("Source:","html5_video") ?>
              </label>
              <input class="radio" id="wo_di_video_oembed" type="radio" value="oembed" name="wo_di_tipo_video" checked="checked">
              <label class="radio" for="wo_di_video_oembed">
                <?php echo __('URL', 'html5_video') ?>
              </label>
              <input class="radio" id="wo_di_video_servidor" type="radio" value="servidor" name="wo_di_tipo_video">
              <label class="radio" for="wo_di_video_servidor">
                <?= __("Select/Upload","html5_video") ?>
              </label>
              <?php if ($disable_iframe == 0): ?>
                <input class="radio" id="video_embebido" type="radio"  value="embebido" name="wo_di_tipo_video">
                <label class="radio" for="video_embebido">
                  <?= __("Embedded code","html5_video") ?>
                </label>
              <?php endif; ?>
            </div>
            <hr/>
            <div class="video-option oembed-video">
              <p>
                <?= __('Type the URL of your video, supports URLs of videos in websites like Youtube or Vimeo.', 'html5_video')?>
              </p>
              <input class="wo_di_form_input" type="url" id="video_text_url" name="video_text_url" value="">
            </div>
            <div class="video-option servidor-video" hidden>
              <p>
                <?= __('You can upload a video to the Media Library or select a video from the Media Library.', 'html5_video')?>
              </p>
              <dt>
                <label for="video_text_mp4"> Mp4 </label>
                <img src="<?= WP_PLUGIN_URL.'/woocommerce-html5-video/images/info.png' ?>" title="<?= __("Supported by", "html5_video")?> IE 9+, Chrome 6+, Safari 5" alt="info" />
              </dt>
              <dd>
                <input class="wo_di_form_input" type="url" id="video_text_mp4" name="video_text_mp4" value="">
              </dd>
              <dt>
                <label for="video_text_ogg"> Ogg </label>
                <img src="<?= WP_PLUGIN_URL.'/woocommerce-html5-video/images/info.png' ?>" title="<?= __("Supported by", "html5_video")?> Chrome 6+, Firefox 3.6+, Opera 10.6+" alt="info" />
              </dt>
              <dd>
                <input class="wo_di_form_input" type="url" id="video_text_ogg" name="video_text_ogg" value="">
              </dd>
              <dt>
                <label for="video_text_webm"> WebM </label>
                <img src="<?= WP_PLUGIN_URL.'/woocommerce-html5-video/images/info.png' ?>" title="<?= __("Supported by", "html5_video")?> Chrome 6+, Firefox 43+, Opera 35+" alt="info" />
              </dt>
              <dd>
                <input class="wo_di_form_input" type="url" id="video_text_webm" name="video_text_webm" value="">
              </dd>
              <input id="wo_di_select_video" type="button" value="<?= __("Select/Upload video","html5_video")?>" class="button tagadd">
            </div>
            <?php if ($disable_iframe == 0): ?>
              <div class="video-option embebido-video" hidden>
                <p>
                  <textarea class="wo_di_form_textarea" name="video_text_embebido" id="video_text_embebido" placeholder="<?php echo  $placeholder ?>" rows="2" cols="20"></textarea>
                </p>
                <p>
                  <?= __('The embedded code should be taken from a video page like Youtube', 'html5_video') ?>
                </p>
              </div>
            <?php endif; ?>
          </fieldset>
        </form>
      </div>

      <?php //When editing a video ?>
      <div id="dialog_form_edit_video" title="<?= __("Edit Video", 'html5_video') ?>" style="display: none;">
        <form id="wo_di_form_edit_video" action="<?= admin_url( 'admin-ajax.php' )?>" onsubmit="return false;" method="post">
          <fieldset>
            <div class="options_group">
              <label for="wo_di_video_title_edit">
                <?= __("Title: ","html5_video") ?>
              </label>
              <input class="wo_di_form_input" id="wo_di_video_title_edit" type="text"  value="" name="wo_di_video_title_edit" style="margin-left: 21px;" />
            </div>
            <?php
            if (get_option('wo_di_video_size_forcing') == 1) {
              $width = "value='" . get_option('wo_di_config_video_width') . "' readonly";
              $height = "value='" . get_option('wo_di_config_video_height') . "' readonly";
            }
            else {
              $width = "";
              $height = "";
            }
            ?>
            <div class="options_group">
              <label for="width_video_woocommerce_edit">
                <?= __("Width","html5_video")?>:
              </label>
              <input type="text" id="width_video_woocommerce_edit" name="width_video_woocommerce_edit" placeholder="<?= get_option('wo_di_config_video_width'); ?>" <?= $width ?> class="dimension-input">
              <label for="height_video_woocommerce_edit">
                <?php echo __("Height","html5_video")?>:
              </label>
              <input type="text" id="height_video_woocommerce_edit" name="height_video_woocommerce_edit" placeholder="<?= get_option('wo_di_config_video_height'); ?>" <?= $height ?> class="dimension-input">
            </div>
            <div class="options_group">
              <label>
                <?= __("Source:","html5_video") ?>
              </label>
              <input class="radio" id="wo_di_video_oembed_edit" type="radio" value="oembed" name="wo_di_tipo_video_edit">
              <label class="radio" for="wo_di_video_oembed_edit">
                <?php echo __('URL', 'html5_video') ?>
              </label>
              <input class="radio" class="margin-bottom" id="wo_di_video_servidor_edit" type="radio" value="servidor" name="wo_di_tipo_video_edit">
              <label class="radio" for="wo_di_video_servidor_edit">
                <?= __("Upload video","html5_video")?>
              </label>
              <?php if ($disable_iframe == 0): ?>
                <input class="radio" id="wo_di_video_embebido_edit" type="radio"  value="embebido" name="wo_di_tipo_video_edit">
                <label class="radio" for="wo_di_video_embebido_edit">
                  <?= __("Embedded code","html5_video")?>
                </label>
              <?php endif; ?>
            </div>
            <hr/>
            <div class="video-option oembed-video" hidden>
              <p>
                <?= __('Type the URL of your video, supports URLs of videos in websites like Youtube or Vimeo.', 'html5_video')?>
              </p>
              <input class="wo_di_form_input" type="url" id="video_text_url_edit" name="video_text_url_edit" value="">
            </div>
            <div class="video-option servidor-video" hidden>
              <p>
                <?= __('You can upload a video to the Media Gallery or select a video from the Media Gallery.', 'html5_video')?>
              </p>
              <dt>
                <label for="video_text_mp4_edit"> Mp4 </label>
                <img src="<?= WP_PLUGIN_URL.'/woocommerce-html5-video/images/info.png' ?>" title="<?= __("Supported by", "html5_video")?> IE 9+, Chrome 6+, Safari 5" alt="info" />
              </dt>
              <dd>
                <input class="wo_di_form_input" type="url" id="video_text_mp4_edit" name="video_text_mp4_edit" value="">
              </dd>
              <dt>
                <label for="video_text_ogg_edit"> Ogg </label>
                <img src="<?= WP_PLUGIN_URL.'/woocommerce-html5-video/images/info.png' ?>" title="<?= __("Supported by", "html5_video")?> Chrome 6+, Firefox 3.6+, Opera 10.6+" alt="info" />
              </dt>
              <dd>
                <input class="wo_di_form_input" type="url" id="video_text_ogg_edit" name="video_text_ogg_edit" value="">
              </dd>
              <dt>
                <label for="video_text_webm_edit"> WebM </label>
                <img src="<?= WP_PLUGIN_URL.'/woocommerce-html5-video/images/info.png' ?>" title="<?= __("Supported by", "html5_video")?> Chrome 6+, Firefox 43+, Opera 35+" alt="info" />
              </dt>
              <dd>
                <input class="wo_di_form_input" type="url" id="video_text_webm_edit" name="video_text_ogg_edit" value="">
              </dd>
              <input id="wo_di_select_video_edit" type="button" value="<?php echo __("Select/Upload video","html5_video")?>" class="button tagadd">
            </div>
            <?php if($disable_iframe==0): ?>
              <div class="video-option embebido-video" hidden>
                <p>
                  <textarea class="wo_di_form_textarea" class="wo_di_form_textarea" name="video_text_embebido_edit" id="video_text_embebido_edit" placeholder="<?php echo $placeholder ?>" rows="2" cols="20"></textarea>
                </p>
                <p>
                  <?= __('The embedded code should be taken from a video page like Youtube', 'html5_video') ?>
                </p>
              </div>
            <?php endif; ?>
          </fieldset>
        </form>
      </div>

      <div id="dialog_preview_video" title="<?= __("Preview Video", 'html5_video') ?> ">
        <div id="contenedor_video"></div>
      </div>
    <?php
    endif;
  }

  public static function oembed_video() {
    $videoUrl = isset($_POST['video_url'])? $_POST['video_url'] : '';
    $height = get_option('wo_di_config_video_height');
    $height = (isset($_POST['height']) && !empty($_POST['height']))? $_POST['height'] : $height;
    $width = get_option('wo_di_config_video_width');
    $width = (isset($_POST['width']) && !empty($_POST['width']))? $_POST['width'] : $width;
    global $wp_embed;
    if (isset($_POST['post_id']) && 0 != $_POST['post_id']) {
      global $post;
      $post =  get_post($_POST['post_id']);
      echo $wp_embed->run_shortcode("[embed width='{$width}' height='{$height}']{$videoUrl}[/embed]");
    }
    else {
      echo '';
    }
    wp_die();
  }
/******************************************************************************/
/*                                  Auxiliar                                  */
/******************************************************************************/

  /*
   * Build the admin panel content
   */
  private static function video_tab_form($field) {
    //$thepostid is created by woocommerce
    global $thepostid, $post;
    ?>
    <script type="text/javascript">
      var text_add_button = "<?= __('Add','html5_video'); ?>";
      var text_edit_button = "<?= __('Edit','html5_video'); ?>";
      var text_cancel_button = "<?= __('Cancel','html5_video'); ?>";
      var text_close_button = "<?= __('Close','html5_video'); ?>";
      var text_error_min_html = "<?= __('At least one video is required','html5_video'); ?>";
      var text_error_insert_html = "<?= __('Embedded code is required','html5_video'); ?>";
      var text_error_id = "<?= __('The name is required','html5_video'); ?>";
      var text_error_dimension = "<?= __('height and width of the video is required','html5_video'); ?>";
    </script>
    <?php

    if (!is_int($thepostid))
      $thepostid = $post->ID;
    if (!isset($field['placeholder'])) {
      $field['placeholder'] = '';
    }

    $mce_editor_content = get_post_meta($thepostid, 'wo_di_editormce_video', true);
    $disable_desc = get_option('wo_di_config_video_description');
    //tinymce editor description of product
    ?>
    <div class="options_group wohv-description-container" <?= ($disable_desc == 0) ? "" : "style='display:none;'"; ?> >
      <h4 class="wohv-title"><?= __("Description", "html5_video"); ?></h4>
      <p class="wohv-description">
        <?= __("It will appear above the videos in the video tab","html5_video"); ?>
      </p>
      <div>
        <?php wp_editor(
          $mce_editor_content,
          "wo_di_editormce_video",
          array(
            'textarea_name' => 'wo_di_editormce_video',
            'wpautop'       => false,
            'textarea_rows' => 10
          )
        ); ?>
      </div>
    </div>
    <?php
    $number_of_videos = get_post_meta($thepostid, 'wo_di_number_of_videos', true);
    $tableBody = '';
    if (empty($number_of_videos)):
      $number_of_videos = 0;
    else:
      $videos = json_decode(get_post_meta($thepostid, 'wo_di_video_product_videos', true));
      //Set every video information
      for($i = 0; $i < $number_of_videos; $i++):
        $video = $videos[$i];
        $title = $video->title;
        $type = $video->type;
        $videoEmbebido = '';
        $videoMp4 = '';
        $videoOGG = '';
        $videoUrl = '';
        $height = $video->height;
        $width = $video->width;
        if($height == '' && $width == '') {
          $dimension = 'Default';
          $width = get_option('wo_di_config_video_width');
          $height = get_option('wo_di_config_video_height');
        }
        else {
          $dimension = $height .' X ' . $width;
        }

        $class = "class=''";
        $disable_iframe = get_option('wo_di_video_disable_iframe');
        switch ($type) {
          case "Embedded":
            $videoEmbebido = $video->embebido;
            $height = "-";
            $width = "-";
            $dimension = "-";
            $formats = "-";
            break;
          case 'WP Library':
            $videoMp4 = $video->mp4;
            $videoOGG = $video->ogg;
            $videoWEBM = $video->webm;
            $formats = "";
            if ($videoMp4 != "")
              $formats = " MP4";

            if ($videoOGG != "") {
              if (empty($formats))
                $formats=" OGG";
              else
                $formats.=", OGG";
            }

            if ($videoWEBM != "") {
              if (empty($formats))
                $formats=" WEBM";
              else
                $formats.=", WEBM";
            }
            break;
          case 'oEmbed':
            $videoUrl = $video->url;
            $dimension = '-';
            $formats = '-';
            break;
        }
        $checked = "";
        if ($video->active == 1) {
         $checked = "checked='checked'";
        }
        global $wp_embed;
        //Construct row for each video
        ob_start();
        ?>
        <tr id='wo_di_video_product_<?= $i ?>' <?= $class ?>>
          <td style="width:20px;">
            <span class='sort-button dashicons dashicons-sort' style="font-size:18px;" title="move"></span>
          </td>
          <td>
            <input type=hidden name='wo_di_video_titles[]' value='<?= $title ?>' />
            <span><?= $title ?></span>
          </td>
          <td>
            <input type=hidden name='wo_di_video_types[]' value='<?= $type ?>' />
            <span><?= $type ?></span>
          </td>
          <td>
            <input type=hidden name='wo_di_video_formats[]' value='<?= $formats ?>' />
            <span><?= $formats ?></span>
          </td>
          <td>
            <input type=hidden name='wo_di_video_heights[]' value='<?= $height ?>' />
            <input type=hidden name='wo_di_video_widths[]' value='<?= $width ?>' />
            <span><?= $dimension ?></span>
          </td>
          <input type=hidden name='wo_di_video_embebido[]' value='<?= $videoEmbebido ?>' />
          <input type=hidden name='wo_di_video_url[]' value='<?= $videoUrl ?>' />
          <input type=hidden name='wo_oembed[]' value='<?= $wp_embed->run_shortcode("[embed width='{$width}' height='{$height}']{$videoUrl}[/embed]") ?>' />
          <input type=hidden name='wo_di_video_mp4[]' value='<?= $videoMp4 ?>' />
          <input type=hidden name='wo_di_video_ogg[]' value='<?= $videoOGG ?>' />
          <input type=hidden name='wo_di_video_webm[]' value='<?= $videoWEBM ?>' />
          <td>
            <input type=hidden name='wo_di_video_active[]' value='<?= $video->active ?>' />
            <input type='checkbox' value='active' <?php echo $checked; ?> onchange='update_input_active(this)'/>
          </td>
          <td>
            <?php if ($type != "Embedded" || ($type == "Embedded" && $disable_iframe == 0)): ?>
              <span class='action-button dashicons dashicons-search float-right' onclick='preview_video(this)' title='preview'></span>
              <span class='action-button dashicons dashicons-edit float-right' onclick='edit_row(this)' title='edit'></span>
              <span class='action-button dashicons dashicons-trash float-right' onclick='delete_row(this)' title='delete'></span>
            <?php elseif ($type == "Embedded" && $disable_iframe == 1): ?>
              <span class='ui-icon ui-icon-circle-zoomout float-right' onclick='preview_video(this)' style='visibility:hidden;'></span>
              <span class='ui-icon ui-icon-pencil float-right' onclick='edit_row(this)'  style='visibility:hidden;'></span>
              <span class='ui-icon ui-icon-trash float-right' onclick='delete_row(this)'></span>
            <?php endif;?>
          </td>
        </tr>
        <?php
        $tableBody .= ob_get_contents();
        ob_end_clean();
      endfor;
    endif;
    //Print table with all the videos for the current product
    ?>
    <div class='options_group'>
      <h4 class='wohv-title'><?= __("Attached videos") ?></h4>
      <input id='wo_di_number_of_videos' name='wo_di_number_of_videos' type='hidden' value='<?= $number_of_videos ?>'/>
      <table id="wo_di_table_videos_html" class="wp-list-table widefat wo_di_table_videos">
        <thead>
          <tr>
            <th></th>
            <th><?= __('Title', 'html5_video') ?></th>
            <th><?= __('Type', 'html5_video') ?></th>
            <th><?= __('Formats', 'html5_video') ?></th>
            <th><?= __('Dimensions', 'html5_video') ?></th>
            <th><?= __('Active', 'html5_video') ?></th>
            <th><?= __('Actions', 'html5_video') ?></th>
          </tr>
        </thead>
        <tbody id="table-video-sortable">
         <?= $tableBody ?>
        </tbody>
      </table>
      <button id="button_add_video"><?= __("Add", 'html5_video') ?></button>
    </div>
    <?php
    //Product description, this is part of the woocommerce.
    if (isset($field['description']) && $field['description']) {
      ?>
      <span class="description"><?= $field['description'] ?></span>
      <?php
    }
  }
}
