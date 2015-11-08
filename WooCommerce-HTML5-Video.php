<?php
/**
 * Plugin Name: WooCommerce HTML5 Video
 * Plugin URI: http://www.webilop.com/products/woocommerce-html5-video/
 * Description: Include videos in products of your WooCommerce online store. This plugin uses HTML5 to render videos in your products and it supports the video formats: MP4, Ogg and embedded videos like youtube videos.
 * Author: Webilop <contact@webilop.com>
 * Author URI: http://www.webilop.com
 * Version: 1.5.2
 * License: GPLv2 or later
 */
// Exit if accessed directly
if (!defined('ABSPATH'))
  exit;


/*
 * hook for install
 */
register_activation_hook( __FILE__, array( 'WooCommerce_HTML5_Video', 'activate' ) );
register_uninstall_hook( __FILE__, array( 'WooCommerce_HTML5_Video', 'uninstall' ) );

//Checks if the WooCommerce plugins is installed and active.
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
  if (!class_exists('WooCommerce_HTML5_Video')) {

    class WooCommerce_HTML5_Video {

      private $codigo_video = ''; //Variable to save the video code.
      private $video_type = '';
      private $mensaje = ''; //informational message to the user when viewing the video.
      private $width_video = '100%';
      private $height_video = '';

      public function uninstall(){
        $products = get_posts( array(
            'post_type'      => array( 'product', 'product_variation' ),
            'posts_per_page' => -1,
            		'fields' => 'ids') );
          foreach ($products as $id) {
            delete_post_meta($id, 'wo_di_video_product_videos');
            delete_post_meta($id, 'wo_di_number_of_videos');
            delete_post_meta($id, 'wo_di_editormce_video');
            delete_option( 'wo_di_config_version' );
            delete_option( 'wo_di_video_hide_tab' );
            delete_option( 'wo_di_config_video_height' );
            delete_option( 'wo_di_config_video_width' );
          }
      }

      public function activate(){
        //update_option('wo_di_config_version',false);
        $version=get_option('wo_di_config_version');
        if($version==false){
          //update BD to new version 2.0
          $products = get_posts( array(
            'post_type'      => array( 'product', 'product_variation' ),
            'posts_per_page' => -1,
            		'fields' => 'ids') );
          $nameVideo=__('Video','html5_video');
          foreach ($products as $id) {
            $video_type = get_post_meta($id, 'wo_di_video_type', true);
            $arrayJson=array();
            $size=0;
            if ($video_type == 'embebido') {
              for ($i=0; $i<=2; $i++){
                $video= get_post_meta($id, 'wo_di_video_product'.$i, true);
                $video=addslashes($video);
                if(!empty($video)){
                  $size++;
                  //create video embebido
                  $arrayJson[]=array("name"=>$nameVideo." ".$size,"type"=>"Embedded","title"=>"",
                                "width"=>"-","height"=>"-","embebido"=>$video,
                                "mp4"=>"","ogg"=>"","active"=>1);
                }
              }
            } else {
              if($video_type=='servidor'){
                $mp4=get_post_meta($id, 'wo_di_video_url_mp4', true);
                $ogg=get_post_meta($id, 'wo_di_video_url_ogg', true);
                $height_video=get_post_meta($id, 'height_video_woocommerce', true);
                $width_video=get_post_meta($id, 'width_video_woocommerce', true);
                if(empty($height_video)) {
                  $height_video="-";
                }
                if(empty($width_video)) {
                  $width_video="-";
                }
                $size=1;
                $arrayJson[]=array("name"=>$nameVideo." ".$size,"type"=>"WP Library","title"=>"",
                                "width"=>$width_video,"height"=>$height_video,"embebido"=>"",
                                "mp4"=>$mp4,"ogg"=>$ogg,"active"=>1);
              }
            }
            update_post_meta($id, 'wo_di_number_of_videos', $size);
            update_post_meta($id, 'wo_di_video_product_videos',json_encode($arrayJson));
            delete_post_meta($id, 'wo_di_video_type');
            for ($i=0; $i<=2; $i++){
              delete_post_meta($id, 'wo_di_video_product'.$i);
            }
            delete_post_meta($id, 'wo_di_video_product_html5');
            delete_post_meta($id, 'height_video_woocommerce');
            delete_post_meta($id, 'width_video_woocommerce');
            delete_post_meta($id, 'wo_di_video_url_mp4');
            delete_post_meta($id, 'wo_di_video_url_ogg');
            delete_post_meta($id, 'wo_di_video_product_html5');
            delete_post_meta($id, 'wo_di_video_check_mp4');
            delete_post_meta($id, 'wo_di_video_check_ogg');
            //echo "<br> ******** <br>";
          }
          delete_option( 'video_height' );
          delete_option( 'video_width' );
        }
        update_option('wo_di_config_version',2);
      }

      /**
       * Gets things started by adding an action to initialize this plugin once
       * WooCommerce is known to be active and initialized
       */
      public function __construct() {
        add_action('woocommerce_init', array(&$this, 'init'));
      }

      /**
       * to add the necessary actions for the plugin
       */
      public function init() {
        $version=get_option('wo_di_config_version');
        if($version==false){
          $this->activate();
        }
        // backend stuff
        add_action('woocommerce_product_write_panel_tabs', array($this, 'product_write_panel_tab'));

		//add_action('woocommerce_product_write_panel_tabs', array($this, 'product_write_panel_tab1'));

        add_action('woocommerce_product_write_panels', array($this, 'product_write_panel'));
        add_action('woocommerce_process_product_meta', array($this, 'product_save_data'), 10, 2);
        // frontend stuff
        add_filter('woocommerce_product_tabs', array($this,'video_product_tabs'),25);
        add_action('woocommerce_product_tab_panels', array($this, 'video_product_tabs_panel'), 25);


        //add_filter( 'woocommerce_product_tabs', array($this, 'woo_new_product_tab'), 25);
      }

      /*
        function woo_new_product_tab( $tabs ) {
			
			// Adds the new tab
			
			$tabs['test_tab'] = array(
				//'title' 	=> __( 'New Product Tab', 'woocommerce' ),
				'title' 	=> __( 'New Product Tab', 'html5_video' ),
				'priority' 	=> 50,
				'callback' 	=> 'woo_new_product_tab_content'
			);

			return $tabs;

		}
		function woo_new_product_tab_content() {

			// The new tab content

			echo '<h2>New Product Tab</h2>';
			echo '<p>Here\'s your new product tab.</p>';
			
		}
		*/
		

      /**
       * creates the tab if the product has an associated video.
       */
       /** Add extra tabs to front end product page **/
        function video_product_tabs( $tabs ) {
                global $post, $product;
                if($this->product_has_video_tabs($product) || get_option("wo_di_video_hide_tab")==1){

                  $custom_tab_options = array(
                          'enabled' => get_post_meta($post->ID, 'custom_tab_enabled', true),
                          'title' => get_post_meta($post->ID, 'custom_tab_title', true),
                          'content' => get_post_meta($post->ID, 'custom_tab_content', true),
                  );

                  if ( $custom_tab_options['enabled'] != 'no' ){
                          $tabs['html5_video'] = array(
                             'title'    => __('Video','html5_video'),
                             'priority' => 25,
                             'callback' => 'woohv_htmlvideotabcontent',
                             'content'  => $custom_tab_options['content']
                           );
                   }
                }
                return $tabs;
        }

      /**
       * make the div which will show the video in html5 or embedded code.
       */
      public function video_product_tabs_panel() {
        global $product;
        $cadena_editormce=get_post_meta($product->id, 'wo_di_editormce_video', true);
        echo '<div> '.$cadena_editormce.'</div>';
        if ($this->product_has_video_tabs($product)) {

          $videos=  json_decode(get_post_meta($product->id, 'wo_di_video_product_videos', true));
          if(!is_null($videos)){
            $width_config = get_option('wo_di_config_video_width');
            $height_config = get_option('wo_di_config_video_height');
            foreach ($videos as $video) {
              if($video->active==1){
                if(!empty($video->title)){
                  echo '<h3>'.$video->title.'</h3>';
                }
                if($video->type=="Embedded"){
                  echo $video->embebido;
                }else{
                  if(empty($video->width)){
                    if(!empty($width_config) && $width_config!=0){
                     $width=$width_config;
                    }else{
                      $width=$this->width_video;
                    }
                  }else{
                    $width=$video->width;
                  }
                  if(empty($video->height)){
                    if(!empty($height_config) && $height_config!=0){
                     $height=$height_config;
                    }else{
                      $height='';
                    }
                  }else{
                    $height=$video->height;
                  }
                  $cadena_tag_video_html5 = '<video width="' .  $width . '" height="' . $height . '" controls>';
                  if($video->mp4!=""){
                    $cadena_tag_video_html5.='<source src="' . $video->mp4 . '" type="video/mp4" />';
                  }
                  if($video->ogg!=""){
                    $cadena_tag_video_html5.='<source src="' . $video->ogg . '" type="video/ogg" />';
                  }
                  $cadena_tag_video_html5.='<p>'.__("Your browser does not support HTML5","html5_video").'</p></video>';
                  echo $cadena_tag_video_html5;
                }
                echo '<br>';
              }
            }
          }
        }
        echo '<p style="font-size:10px;color:#999;">'.__("Video embedding powered by","html5_video").' <a target="_blank" title="Web + mobile development" rel="nofollow" href="http://www.webilop.com/products/woocommerce-html5-video/">Webilop</a></p>';
      }

      /**
       * check if a product has an associated video. and save the video in the global variable codigo_video,
       * also saves a message for use in method video_product_tabs_panel.
       */
      private function product_has_video_tabs($product) {
        $number_videos= get_post_meta($product->id, 'wo_di_number_of_videos', true);
        if($number_videos>0){
          $videos=  json_decode(get_post_meta($product->id, 'wo_di_video_product_videos', true));
          foreach ($videos as $video) {
            if($video->active==1){
              return true;
            }
          }
          return false;
        }else{
          return false;
        }
      }

      /**
       * creates the tab for the administrator, where administered product videos.
       */
      public function product_write_panel_tab() {
        echo "<li class='html5_video'><a href=\"#video_tab\">" . __('Video','html5_video') . "</a></li>";
      }

/*      public function product_write_panel_tab1() {
        echo "<li class='general_options general_tab hide_if_grouped active'><a href=\"#video_tab\">" . __('Videooooo','html5_video') . "</a></li>";
      }*/

      /**
       * build the panel for the administrator.
       */
      public function product_write_panel() {
        global $post;

        // Pull the video tab data out of the database
           if (empty($tab_data)) {
             $tab_data = '';
           }
           echo '<div id="video_tab" class="panel woocommerce_options_panel">';
           $this->wo_di_form_admin_video(array('id' => '_tab_video', 'label' => __('Embed Code','html5_video'), 'placeholder' => __('Place your embedded video code here.','html5_video'), 'style' => 'width:70%;height:21.5em;'));
           echo '</div>';
      }

      /*
       * build form to the administrator.
       */

      private function wo_di_form_admin_video($field) {
        global $thepostid, $post;
        ?>
        <script>
          var text_add_button = "<?php echo __("Add","html5_video"); ?>" ;
          var text_edit_button = "<?php echo __("Edit","html5_video"); ?>" ;
          var text_cancel_button = "<?php echo __("Cancel","html5_video"); ?>" ;
          var text_error_min_html = "<?php echo __("It requires at least one video","html5_video"); ?>" ;
          var text_error_insert_html = "<?php echo __("Embedded code is required","html5_video"); ?>" ;
          var text_error_id = "<?php echo __("The Name is required","html5_video"); ?>" ;
          var text_error_dimension = "<?php echo __("height and width of the video is required","html5_video"); ?>" ;
          //document.write("VariableJS = " + variableJS);
        </script>
        <?php
        wp_enqueue_script('media-upload');
        wp_enqueue_script('thickbox');
        wp_register_script('my-upload', plugins_url('js/button_actions.js', __FILE__), array( 'jquery','media-upload', 'thickbox'));
        wp_enqueue_script('my-upload');
        wp_enqueue_style('thickbox');
        wp_enqueue_script("jquery-ui-core");
        wp_enqueue_script("jquery-ui-dialog");

        wp_register_script('jquery-validate', plugins_url('js/jquery.validate.min.js', __FILE__));
        wp_enqueue_script('jquery-validate');
        wp_enqueue_style('thickbox');

        wp_enqueue_script('tiny_mce');
        if (!$thepostid)
          $thepostid = $post->ID;
        if (!isset($field['placeholder'])) {
          $field['placeholder'] = '';
        }

        $cadena_editormce=get_post_meta($thepostid, 'wo_di_editormce_video', true);
        //tynimce editor descrption of product
        ?>
        <div class="options_group ">
            <p>
                <?php echo __("Video tab description (it will appear above the videos in the Video tab)","html5_video")?>
            </p>
            <div>
              <textarea id="wo_di_editormce_video" class="mceEditorVideoHtml" name="wo_di_editormce_video" cols="20" rows="2" >
             <?php echo  $cadena_editormce ?> 
              </textarea>
            </div>
        </div>
        <?php
        $number_of_videos=get_post_meta($thepostid, 'wo_di_number_of_videos', true);
        $tableBody="";
        if(!empty($number_of_videos)){
          $videos=  json_decode(get_post_meta($post->ID, 'wo_di_video_product_videos', true));
          for($i=0;$i<$number_of_videos;$i++){
            $video=$videos[$i];
            $title=$video->title;
            $type=$video->type;
            $name=$video->name;
            //$formats=get_post_meta($post->ID, 'wo_di_video_product_formats_'.$i, true);
            $class=($i%2==0) ? "class='alternate'":"";
            if($type=="Embedded"){
              $videoEmbebido=$video->embebido;
              $height="-";
              $width="-";
              $dimension="-";
              $formats="-";
              $videoMp4="";
              $videoOGG="";
            }else{
              $videoEmbebido="";
              $height=$video->height;
              $width=$video->width;
              if($height=="" && $width==""){
                 $dimension="Default";
              }else{
               $dimension=$height." X ".$width;
              }
              $videoMp4=$video->mp4;
              $videoOGG=$video->ogg;
              $formats="";
              $b_video=false;
              if($videoMp4!=""){
                $formats=" MP4";
                $b_video=true;
              }
              if($videoOGG!=""){
                if($b_video){
                  $formats.=", OGG";
                }else{
                  $formats=" OGG";
                }
              }
            }
            $checked="";
            if($video->active==1){
             $checked="checked='checked'";
            }
            $tableBody.="<tr id='wo_di_video_product_$i' $class>
                          <td><input type=hidden name='wo_di_video_ids[]' value='$name' /><span>$name</span></td>
                          <td><input type=hidden name='wo_di_video_titles[]' value='$title' /><span>$title</span></td>
                          <td><input type=hidden name='wo_di_video_types[]' value='$type' /> <span>$type</span></td>
                          <td><input type=hidden name='wo_di_video_formats[]' value='$formats' /><span>$formats</span></td>
                          <td><input type=hidden name='wo_di_video_heights[]' value='$height' />
                              <input type=hidden name='wo_di_video_widths[]' value='$width' />
                              <span> $dimension </span>
                          </td>
                              <input type=hidden name='wo_di_video_embebido[]' value='$videoEmbebido' />
                              <input type=hidden name='wo_di_video_mp4[]' value='$videoMp4' />
                              <input type=hidden name='wo_di_video_ogg[]' value='$videoOGG' />
                          <td><input type=hidden name='wo_di_video_active[]' value='".$video->active."' /><input type='checkbox' value='active' $checked onchange='update_input_active(this)'/></td>
                          <td><span class='ui-icon ui-icon-trash float-right' onclick='delete_row(this)'></span> <span class='ui-icon ui-icon-circle-triangle-s float-right' onclick='edit_row(this)'></span>  </td>
                        </tr>";
          }
        }else{
          $number_of_videos=0;
        }
        $print=" <div class='options_group'>
                  <dl>
                  <dd><p>".__("Attached videos")."</p></dd>
                 ";
        $table="<input id='wo_di_number_of_videos' name='wo_di_number_of_videos' type='hidden' value='$number_of_videos'/>";
        $print.=$table;
        $print.='<table id="wo_di_table_videos_html" class="wp-list-table widefat wo_di_table_videos">
                  <thead>
                  <tr>
                    <th>'.__("Name").'</th>
                    <th>'.__("Title").'</th>
                    <th>'.__("Type").'</th>
                    <th>'.__("Formats").'</th>
                    <th>'.__("Dimensions").'</th>
                    <th>'.__("Active").'</th>
                    <th>'.__("Actions").'</th>
                  </tr>
                  </thead>
                  <tbody>
                   '.$tableBody.'
                  </tbody>
                </table>';

        $print.='<dd><button id="button_add_video">'.__("Add", 'html5_video').'</button></dd>
            </dl>
          </div>';

         echo $print;

        //Product description, this is part of the woocommerce.
        if (isset($field['description']) && $field['description']) {
          echo '<span class="description">' . $field['description'] . '</span>';
        }

      }
      /*
       * filter where the tabs of media-uploader are loaded,I remove what do not need depending on context.
       * thanks to context, I can delete everything without worrying that called another form in wp.
       */

      function wo_di_image_tabs($_default_tabs) {
        //print_r($_default_tabs);
        //print_r($_GET);
        if (!empty($_GET['context'])) {
          unset($_default_tabs);
          $_default_tabs = array();
          if ($_GET['context'] == 'uploadVideo') {
            $_default_tabs['type'] = 'Upload Video';
          } else if ($_GET['context'] == 'selectVideo') {
            $_default_tabs['library'] = 'select video';
          }
          //print_r($_default_tabs);
          return($_default_tabs);
        } else {
          return($_default_tabs);
        }
      }

      /*
       * filter to replace the button 'insert into post' when you select a file
       *  in the library, and add the context to not lose.
       */

      function wo_di_action_button($form_fields, $post) {

        //form_fields arreglo con todas las propiedades de la imagen o archivo.
        $send = "<input type='submit' class='button' name='send[$post->ID]' value='" . esc_attr__('Select URL') . "' />";
        //print_r($_GET['context']);
        $form_fields['buttons'] = array('tr' => "\t\t<tr class='submit'><td></td><td class='savesend'>$send</td></tr>\n");
        $form_fields['context'] = array('input' => 'hidden', 'value' => $_GET['context']);
        $form_fields['action_video'] = array('input' => 'hidden', 'value' => $_GET['action_video']);
        return $form_fields;
      }

      /*
       * filter is executed when you click the submit button on the form from
       * the library, default button is 'insert into post'
       */

      function wo_di_image_selected($html, $send_id, $attachment) {
        //wonder if it is a valid format.
        $url = $attachment['url'];
        $extension = $this->getExtensionOfVideo($url);
        $validate_extension = true;
        $name_input = '';
        //print "url: $url extension: $extension";
        switch ($extension) {
          case 'mp4':
            if($_REQUEST['action_video']=="add"){
              $name_input = 'video_text_mp4';
            }else{
              $name_input = 'video_text_mp4_edit';
            }
            break;

          case 'ogg':
            if($_REQUEST['action_video']=="add"){
              $name_input = 'video_text_ogg';
            }else{
              $name_input = 'video_text_ogg_edit';
            }
            break;

          default:
            $validate_extension = false;
            break;
        }
        if ($validate_extension) {
          ?>
          <script type="text/javascript">
            /* <![CDATA[ */
            var win = window.dialogArguments || opener || parent || top;

            win.jQuery( '#<?php echo $name_input; ?>' ).val('<?php echo $url; ?>');
            win.tb_remove();
            /* ]]> */
          </script>
          <?php
        } else {
          ?>
          <script type="text/javascript">
            /* <![CDATA[ */
            alert('You have selected a non-compatible video format, the supported extensions are: mp4 and ogg.');
            var win = window.dialogArguments || opener || parent || top;
            win.tb_remove();
            /* ]]> */
          </script>
          <?php
        }
        exit;
      }

      /**
       * updating the database post.
       */
      public function product_save_data($post_id, $post) {

        $tab_video = $_POST['_tab_video'];
        $radio_video_embebido = $_POST['wo_di_tipo_video'];
        //update the videos
        $number_of_videos = $_POST['wo_di_number_of_videos'];
        //update he new videos
        $arrayJson=array();
        update_post_meta($post_id, 'wo_di_number_of_videos', $number_of_videos);
        if($number_of_videos>0){
          //$videos_contents=$_POST['wo_di_video_product_content'];
          $video_types=$_POST['wo_di_video_types'];
          $video_titles=$_POST['wo_di_video_titles'];

          $video_embebido=$_POST['wo_di_video_embebido'];
          $video_mp4=$_POST['wo_di_video_mp4'];
          $video_ogg=$_POST['wo_di_video_ogg'];
          $video_width=$_POST['wo_di_video_widths'];
          $video_height=$_POST['wo_di_video_heights'];
          $video_ids=$_POST['wo_di_video_ids'];
          $video_active=$_POST['wo_di_video_active'];
          foreach ($video_types as $key => $type) {
            $arrayJson[]=array("name"=>$video_ids[$key],"type"=>$type,"title"=>$video_titles[$key],
                              "width"=>$video_width[$key],"height"=>$video_height[$key],"embebido"=>$video_embebido[$key],
                              "mp4"=>$video_mp4[$key],"ogg"=>$video_ogg[$key],"active"=>$video_active[$key]);
          }
        }
        update_post_meta($post_id, 'wo_di_video_product_videos',json_encode($arrayJson));
        //update text of tinymce editor
        $cadena_editormce=$_POST['wo_di_editormce_video'];
        update_post_meta($post_id, 'wo_di_editormce_video', $cadena_editormce);
      }

      /*
       * gets the name of the video from the url
       */

      private function getNameOfVideo($url) {
        $name = substr($url, strripos($url, '/') + 1);
        return $name;
      }

      /*
       * gets the extension of the video from the url or string
       */

      private function getExtensionOfVideo($url) {
        $extension = substr($url, strripos($url, '.') + 1);
        return $extension;
      }
    }

    //end of the class
  }//end of the if, if the class exists

  /*
   * Instantiate plugin class and add it to the set of globals.
   */
  $woocommerce_video_tab = new WooCommerce_HTML5_Video();

  //agrego el contexto a la url
  function woohv_add_my_context_to_url($url, $type) {
    if (isset($_REQUEST['context'])) {
      $url = add_query_arg('context', $_REQUEST['context'], $url);
    }
    if (isset($_REQUEST['action_video'])) {
      $url = add_query_arg('action_video', $_REQUEST['action_video'], $url);
    }
    return $url;
  }

  /*
   * asks if the context is the same
   */

  function woohv_check_upload_image_context($context) {
    if (isset($_REQUEST['context']) && $_REQUEST['context'] == $context) {
      add_filter('media_upload_form_url', 'woohv_add_my_context_to_url', 10, 2);
      return TRUE;
    }
    return false;
  }

  // asked by the context
  if (woohv_check_upload_image_context('uploadVideo') || woohv_check_upload_image_context('selectVideo')) {
    add_filter('media_upload_tabs', array($woocommerce_video_tab, 'wo_di_image_tabs'), 50, 1);
    add_filter('attachment_fields_to_edit', array($woocommerce_video_tab, 'wo_di_action_button'), 20, 2);
    add_filter('media_send_to_editor', array($woocommerce_video_tab, 'wo_di_image_selected'), 10, 3);
  }
  //add settings page
  add_action( 'admin_menu', 'woohv_my_plugin_menu' );

  /** Function to register plugin settings*/
  function woohv_register_my_setting() {
   register_setting( 'dimensions_group', 'wo_di_config_video_width', 'intval' );
   register_setting( 'dimensions_group', 'wo_di_config_video_height', 'intval' );
   register_setting( 'dimensions_group', 'wo_di_video_hide_tab', 'intval' );

  }
  add_action( 'admin_init', 'woohv_register_my_setting' );
  add_action('admin_footer-post.php', 'wo_di_print_popups');
  add_action('admin_footer-post-new.php', 'wo_di_print_popups');
  if(is_admin()){

  }
  /** Function to add settngs link in plugins page */
  function woohv_plugin_add_settings_link( $links ) {
    $settings_link = '<a href="options-general.php?page=html5-video-settings">Settings</a>';
    array_push( $links, $settings_link );
    $docs_link = '<a title="documentation" target="_blank" href="http://www.webilop.com/products/woocommerce-html5-video/">Docs</a>';
    array_push( $links, $docs_link);
    return $links;
  }

  $plugin = plugin_basename( __FILE__ );
  add_filter( "plugin_action_links_$plugin", 'woohv_plugin_add_settings_link' );

} else {//end if,if installed woocommerce
  add_action('admin_notices', 'woohv_video_tab_error_notice');

  function woohv_video_tab_error_notice() {
    global $current_screen;
    if ($current_screen->parent_base == 'plugins') {
      echo '<div class="error"><p>' . __('WooCommerce HTML5 Video requires <a href="http://www.woothemes.com/woocommerce/" target="_blank">WooCommerce</a> activated in order to work. Please install and activate <a href="' . admin_url('plugin-install.php?tab=search&type=term&s=WooCommerce') . '" target="_blank">WooCommerce</a> first.','html5_video') . '</p></div>';
    }
  }
}
/** Function to add a plugin configuration page */
function woohv_my_plugin_menu() {
   add_options_page( 'WooCommerce Html5 Video Settings', 'WooCommerce Html5 Video', 'manage_options', 'html5-video-settings', 'woohv_my_plugin_options' );
}

/** Function to create the content of the configuration page */
function woohv_my_plugin_options() {
   if ( !current_user_can( 'manage_options' ) )  {
      wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
   }?>
   <div class="wrap"><?php screen_icon(); ?><h2>WooCommerce Html5 Video Settings</h2>
   <form class="html5_video" method="post" action="options.php">
   <?php settings_fields( 'dimensions_group' );
   do_settings_fields( 'dimensions_group','html5-video-settings' )?>
   <p><strong><?php echo __('Configure the default video dimensions')?>:</strong></p>
   <table class="form-table">
        <tr valign="top">
        <th scope="row"><?php echo __('Video Width')?>:</th>
        <td><input type="text" name="wo_di_config_video_width" value="<?php echo get_option('wo_di_config_video_width'); ?>" /></td>
        </tr>
        <tr valign="top">
        <th scope="row"><?php echo __('Video Height')?>:</th>
        <td><input type="text" name="wo_di_config_video_height" value="<?php echo get_option('wo_di_config_video_height'); ?>" /></td>
        </tr>
        <tr valign="top">
        <th scope="row"><?php echo __('Show video tab if there is no video','html5_video')?>:</th>
        <td><input type="checkbox" name="wo_di_video_hide_tab" <?php if(get_option('wo_di_video_hide_tab')==1){echo "checked";} ?> value="1" /></td>
        </tr>
    </table>
   <?php submit_button(); ?>
   <span><a title="WooCommerce HTML5 Video" href="http://www.webilop.com/products/woocommerce-html5-video/" target="_blank">WooCommerce Html5 Video Documentation</a></span>
   </form>

   <table class="rate-about-table">
   <tr>
    <td>
    <div class="rate-div"> 
   	  <h3 class="hndle"> <?php _e('Rate it!', 'html5_video') ?></h3>

   	  <p><?php _e('If you like this plugin please'); ?> <a title="rate it" href="https://wordpress.org/support/view/plugin-reviews/woocommerce-html5-video" target="_blank">leave us a rating</a>. In advance, thanks from Webilop team! &nbsp; 
	  <span class="rating">
        <input type="radio" class="rating-input"
            id="rating-input-1-5" name="rating-input-1" onclick="window.open('https://wordpress.org/support/view/plugin-reviews/woocommerce-html5-video')">
        <label for="rating-input-1-5" class="rating-star"></label>
        <input type="radio" class="rating-input"
            id="rating-input-1-4" name="rating-input-1" onclick="window.open('https://wordpress.org/support/view/plugin-reviews/woocommerce-html5-video')">
        <label for="rating-input-1-4" class="rating-star"></label>
        <input type="radio" class="rating-input"
            id="rating-input-1-3" name="rating-input-1" onclick="window.open('https://wordpress.org/support/view/plugin-reviews/woocommerce-html5-video')">
        <label for="rating-input-1-3" class="rating-star"></label>
        <input type="radio" class="rating-input"
            id="rating-input-1-2" name="rating-input-1" onclick="window.open('https://wordpress.org/support/view/plugin-reviews/woocommerce-html5-video')">
        <label for="rating-input-1-2" class="rating-star"></label>
        <input type="radio" class="rating-input"
            id="rating-input-1-1" name="rating-input-1" onclick="window.open('https://wordpress.org/support/view/plugin-reviews/woocommerce-html5-video')">
        <label for="rating-input-1-1" class="rating-star"></label>
   	   </span>
	  </p>
    </div>

    </td>
    </tr>

   <tr><td>

    <div class="about-webilop">
    <h3 class="hndle"><?php _e('About','html5_video');?></h3>
    <div class="inside">
    <p><strong>WooCommerce Html5 video </strong><?php _e('was developed by ', 'html5_video');?><a title="Webilop. web and mobile development" href="http://www.webilop.com" target="_blank">Webilop</a></p>
    <p><?php _e('Webilop is a company focused on web and mobile solutions. We develop custom mobile applications and templates and plugins for CMSs such as Wordpress and Joomla!', 'html5_video');?></p>
   <div><h4><?php _e('Follow us', 'html5_video')?></h4><a title="Facebook" href="https://www.facebook.com/webilop" target="_blank"><img src="<?php echo WP_PLUGIN_URL;?>/woocommerce-html5-video/images/facebook.png"></a>
<a title="LinkedIn" href="http://www.linkedin.com/company/webilop" target="_blank"><img src="<?php echo WP_PLUGIN_URL;?>/woocommerce-html5-video/images/linkedin.png"></a>
<a title="Twitter" href="https://twitter.com/webilop" target="_blank"><img src="<?php echo WP_PLUGIN_URL;?>/woocommerce-html5-video/images/twitter.png"></a>
<a title="Google Plus" href="https://www.google.com/+Webilop" target="_blank" rel="publisher"><img src="<?php echo WP_PLUGIN_URL;?>/woocommerce-html5-video/images/gplus.png"></a></div>
    </div></div>

    </td>
    </tr>

    </table>

    </div>
   <?php
}
  /**
  * Enqueue plugin style-file
  */
  function woohv_add_video_scripts() {
    // Respects SSL, Style.css is relative to the current file
    wp_register_style( 'woohv-styles', plugins_url('css/style.css', __FILE__) );
    wp_register_script( 'woohv-scripts', plugins_url('js/js-script.js', __FILE__), array('jquery') );
    wp_enqueue_style( 'woohv-styles' );
    wp_enqueue_script( 'woohv-scripts' );
  }
  add_action( 'admin_enqueue_scripts', 'woohv_add_video_scripts' );

  /**
  * Set up localization
  */
  function woohv_html5_textdomain() {
    load_plugin_textdomain( 'html5_video', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
  }
  add_action('plugins_loaded', 'woohv_html5_textdomain');

  /**
  * Render the custom product tab panel content for the callback 'custom_product_tabs_panel_content'
  */
  function woohv_htmlvideotabcontent() {
   $functions = new WooCommerce_HTML5_Video();
   $var = $functions->video_product_tabs_panel();
  }

  /*
       * popups for add y edit videos.
       */
      function wo_di_print_popups(){
        global $post;
        if($post->post_type=="product"){
          $placeholder = __('Place your embedded video code here.','html5_video');
          ?>
        <div id="dialog_form_add_video" title="<?php echo __("Add Video", 'html5_video') ?>">
          <form id="wo_di_form_add_video" action="<?php echo admin_url( 'admin-ajax.php' )?>" method="post" onsubmit="return false;">
            <fieldset>
                <div class="options_group">
                  <div id="div_errores_add_video"> </div>
                  <dl>
                    <dt><label for="wo_di_video_id"><?php  echo __("Name","html5_video")?></label></dt>
                    <dd><input class="wo_di_form_input" id="wo_di_video_id" name="wo_di_video_id" type="text"   required='required' ></dd>
                    <dt><label for="wo_di_video_title"><?php echo __("Title for video","html5_video") ?></label></dt>
                    <dd><input class="wo_di_form_input" id="wo_di_video_title" type="text"  value="" name="wo_di_video_title" ></dd>
                  </dl>
                </div>
                <label><?php echo __("Select video source:","html5_video") ?></label>
                <hr/>
                <div class="options_group">
                  <dl>
                    <dt><input class="radio" id="video_embebido" type="radio"  value="embebido" name="wo_di_tipo_video" checked="checked">
                    <label class="radio" for="video_text_embebido"><?php echo __("Embedded code","html5_video") ?></label></dt>
                    <dd><p><textarea class="wo_di_form_textarea" name="video_text_embebido" id="video_text_embebido" placeholder="<?php echo  $placeholder ?>" rows="2" cols="20"></textarea></p></dd>
                    <dd><p><?php echo __('The embedded code should be taken from a video page like Youtube', 'html5_video') ?> </p></dd>
                  </dl>
                </div>

                <div class="options_group">
                  <hr/>
                  <dl>
                    <dt class="margin-bottom"><input class="radio" id="wo_di_video_servidor" type="radio" value="servidor" name="wo_di_tipo_video">
                    <label class="radio" for="wo_di_video_servidor"><?php echo __("Uploaded video","html5_video") ?></label></dt>
                    <dt><label for="video_text_mp4"> Mp4 </label><img src="<?php echo WP_PLUGIN_URL.'/woocommerce-html5-video/images/info.png' ?>" title="<?php echo __("Supported by", "html5_video")?> IE 9+, Chrome 6+, Safari 5" alt="info" /></dt>
                    <dd><input class="wo_di_form_input" type="text" id="video_text_mp4" name="video_text_mp4" value=""></dd>
                    <dt><label for="video_text_ogg"> Ogg </label><img src="<?php echo WP_PLUGIN_URL.'/woocommerce-html5-video/images/info.png' ?>" title="<?php echo __("Supported by", "html5_video")?>' Chrome 6+, Firefox 3.6+, Opera 10.6+" alt="info" /></dt>
                    <dd><input class="wo_di_form_input" type="text" id="video_text_ogg" name="video_text_ogg" value=""></dd>
                    <dd><input id="wo_di_upload_video" type="button" value="<?php echo __("Upload video","html5_video")?>" class="button tagadd">
                    <input id="wo_di_select_video" type="button" value="<?php echo __("Select video","html5_video")?>" class="button tagadd"></dd>
                    <dt><label for="width_video_woocommerce"> <?php echo __("Width","html5_video")?>: </label></dt>
                    <dd><input type="text" class="wo_di_form_input" id="width_video_woocommerce" name="width_video_woocommerce" value=""> </dd>
                    <dt><label for="height_video_woocommerce"> <?php echo __("Height","html5_video")?>: </label></dt>
                    <dd><input type="text" class="wo_di_form_input" id="height_video_woocommerce" name="height_video_woocommerce" value=""> </dd>
                  </dl>
                </div>
             </fieldset>
          </form>
        </div>


          <div id="dialog_form_edit_video" title="<?php echo __("Edit Video", 'html5_video') ?> ">
              <form id="wo_di_form_edit_video" action="<?php echo admin_url( 'admin-ajax.php' )?>" onsubmit="return false;" method="post">
              <fieldset>
                  <div class="options_group">
                    <dl>
                      <dt><label for="wo_di_video_id_edit"><?php echo __("Name","html5_video") ?></label></dt>
                      <dd><input class="wo_di_form_input" id="wo_di_video_id_edit" name="wo_di_video_id_edit" type="text"  value=""/></dd>
                      <dt><label for="wo_di_video_title_edit"><?php echo __("Title for video","html5_video")?></label></dt>
                      <dd><input class="wo_di_form_input" id="wo_di_video_title_edit" type="text"  value="" name="wo_di_video_title_edit" /></dd>
                    </dl>
                  </div>
                  <br><label><?php echo __("Select video source:","html5_video") ?> </label>
                  <hr/>
                  <div class="options_group">
                    <dl>
                    <dt><input class="radio" id="wo_di_video_embebido_edit" type="radio"  value="embebido" name="wo_di_tipo_video_edit">
                    <label class="radio" for="wo_di_video_text_embebido_edit"><?php echo __("Embedded code","html5_video")?> </label></dt>
                    <dd><p><textarea class="wo_di_form_textarea" class="wo_di_form_textarea" name="video_text_embebido_edit" id="video_text_embebido_edit" placeholder="<?php echo $placeholder ?> '" rows="2" cols="20"></textarea></p></dd>
                    <dd><p><?php echo __('The embedded code should be taken from a video page like Youtube', 'html5_video') ?> </p></dd>
                    </dl>
                  </div>

              <div class="options_group">
                <hr/>
                <dl>
                  <dt><input class="radio" class="margin-bottom" id="wo_di_video_servidor_edit" type="radio" value="servidor" name="wo_di_tipo_video_edit">
                  <label class="radio" for="wo_di_video_servidor_edit"><?php echo __("Upload video","html5_video")?></label></dt>
                  <dd><span><?php echo __("Supported video formats","html5_video")?></span></dd>
                  <dt><label class="check" for="video_text_mp4_edit"> Mp4 </label><img src="<?php echo WP_PLUGIN_URL.'/woocommerce-html5-video/images/info.png' ?>" title="<?php echo __("Supported by", "html5_video") ?> IE 9+, Chrome 6+, Safari 5+" alt="info" /></dt>
                  <dd><input class="wo_di_form_input" type="text" id="video_text_mp4_edit" name="video_text_mp4_edit" value=""></dd>
                  <dt><label for="video_text_ogg_edit"> Ogg </label><img src="<?php echo WP_PLUGIN_URL.'/woocommerce-html5-video/images/info.png' ?>" title="<?php echo __("Supported by", "html5_video") ?> Chrome 6+, Firefox 3.6+, Opera 10.6+" alt="info" /></dt>
                  <dd><input class="wo_di_form_input" type="text" id="video_text_ogg_edit" name="video_text_ogg_edit" value=""></dd>
                  <dd><input id="wo_di_upload_video_edit" type="button" value="<?php echo __("Upload video","html5_video")?>" class="button tagadd">
                  <input id="wo_di_select_video_edit" type="button" value="<?php echo __("Select video","html5_video")?>" class="button tagadd"></dd>
                  <dt><label for="width_video_woocommerce_edit"> <?php echo __("Width","html5_video")?>: </label></dt>
                  <dd><input type="text" class="wo_di_form_input" id="width_video_woocommerce_edit" name="width_video_woocommerce_edit" value=""> </dd>
                  <dt><label for="height_video_woocommerce_edit"> <?php echo __("Height","html5_video")?>: </label></dt>
                  <dd><input type="text" class="wo_di_form_input" id="height_video_woocommerce_edit" name="height_video_woocommerce_edit" value=""> </dd>
                </dl>
              </div>
            </fieldset>
          </form>
        </div>
          <?php
      }
    }
?>
