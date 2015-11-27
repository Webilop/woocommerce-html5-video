var text_add_button;
var text_edit_button;
var text_cancel_button;
var text_error_min_html;
var text_error_insert_html;
var text_error_id;
var text_error_dimension;
var type_of_action="";
var tr_edit;

function update_input_active(obj){
  var input=jQuery(obj.parentNode).find("input[name='wo_di_video_active[]']");
  if(jQuery(obj).is(":checked")){
    jQuery(input).val(1);
  }else{
    jQuery(input).val(0);
  }
}

function clean_inputs_edit(){
  jQuery("#wo_di_video_title_edit").val("");
  jQuery("#height_video_woocommerce_edit").val("");
  jQuery("#width_video_woocommerce_edit").val("");
  jQuery("#video_text_mp4_edit").val("");
  jQuery("#video_text_ogg_edit").val("");
  jQuery("#video_text_embebido_edit").val("");
  jQuery("#_checkbox_mp4_edit").attr('checked', false);
  jQuery("#_checkbox_OGG_edit").attr('checked', false);
  jQuery("#wo_di_video_embebido_edit").attr('checked', false);
  jQuery("#wo_di_video_servidor_edit").attr('checked', false);
}

function delete_row(obj){
  var i=obj.parentNode.parentNode;
  jQuery(obj.parentNode.parentNode).remove();
  var number_of_videos=jQuery("#wo_di_number_of_videos").val();
  number_of_videos--;
  jQuery("#wo_di_number_of_videos").val(number_of_videos);
  //jQuery("#wo_di_table_videos_html").deleteRow(i);
}

function edit_row(obj){
  clean_inputs_edit();
  tr_edit=obj.parentNode.parentNode;
  var type=jQuery(tr_edit).find("input[name='wo_di_video_types[]']").val();
  var title=jQuery(tr_edit).find("input[name='wo_di_video_titles[]']").val();
  var id=jQuery(tr_edit).find("input[name='wo_di_video_ids[]']").val();
  jQuery("#wo_di_video_title_edit").val(title);
  jQuery("#wo_di_video_id_edit").val(id);
  if(type=="Embedded"){
    jQuery("#wo_di_video_embebido_edit").attr('checked', true);
    var embebido=jQuery(tr_edit).find("input[name='wo_di_video_embebido[]']").val();
    jQuery("#video_text_embebido_edit").val(embebido);
  }else{
    jQuery("#wo_di_video_servidor_edit").attr('checked', true);
    var height=jQuery(tr_edit).find("input[name='wo_di_video_heights[]']").val();
    var width=jQuery(tr_edit).find("input[name='wo_di_video_widths[]']").val();
    var mp4=jQuery(tr_edit).find("input[name='wo_di_video_mp4[]']").val();
    var ogg=jQuery(tr_edit).find("input[name='wo_di_video_ogg[]']").val();
    //var formats=jQuery(tr_edit).find("input[name='wo_di_video_formats[]']").val();
    jQuery("#height_video_woocommerce_edit").val(height);
    jQuery("#width_video_woocommerce_edit").val(width);
    jQuery("#video_text_mp4_edit").val(mp4);
    jQuery("#video_text_ogg_edit").val(ogg);
    if(mp4!=""){
      jQuery("#_checkbox_mp4_edit").attr('checked', 'checked');
    }
    if(ogg!=""){
      jQuery("#_checkbox_OGG_edit").attr('checked', 'checked');
    }
    //jQuery("#video_text_embebido_edit").val(embebido);
  }
  jQuery( "#dialog_form_edit_video" ).dialog( "open" );

  //jQuery("#wo_di_table_videos_html").deleteRow(i);
}
var form_add_video;
var form_edit_video;

function initiate_rules(){
  //edit form
  jQuery.validator.addMethod(
        "insert_video_html_edit",
        function(value, element) {
            if(jQuery("#wo_di_video_servidor_edit").is(':checked')){
              if(jQuery("#video_text_mp4_edit").val()=="" && jQuery("#video_text_ogg_edit").val()==""){
                return false;
              }
            }
            jQuery("#video_text_mp4_edit").removeClass("error");
            jQuery("#video_text_mp4_edit").siblings("p").remove();
            jQuery("#video_text_ogg_edit").removeClass("error");
            jQuery("#video_text_ogg_edit").siblings("p").remove();
            return true;
        },
        text_error_min_html
    );

  jQuery.validator.addMethod(
        "insert_video_embebido_edit",
        function(value, element) {
            if(jQuery("#wo_di_video_embebido_edit").is(':checked')){
              if(jQuery("#video_text_embebido_edit").val()==""){
                return false;
              }
            }
            return true;
        },
        text_error_insert_html
    );

    jQuery.validator.addMethod(
        "insert_video_dimension_edit",
        function(value, element) {
          if(jQuery("#wo_di_video_servidor_edit").is(':checked')){
            if(jQuery("#height_video_woocommerce_edit").val()=="" && jQuery("#width_video_woocommerce_edit").val()==""){
              jQuery("#height_video_woocommerce_edit").removeClass("error");
              jQuery("#height_video_woocommerce_edit").siblings("p").remove();
              return true;
            }
            if(jQuery("#height_video_woocommerce_edit").val()!="" && jQuery("#width_video_woocommerce_edit").val()!=""){
              jQuery("#height_video_woocommerce_edit").removeClass("error");
              jQuery("#height_video_woocommerce_edit").siblings("p").remove();
              return true;
            }
            return false;
          }
          return true;
        },
        text_error_dimension
    );
   form_edit_video=jQuery('#wo_di_form_edit_video').validate({
      wrapper:"p",
    //errorLabelContainer :"div_errores_add_video",
    rules:{
      wo_di_video_id_edit :{
        required: true
      },
    wo_di_tipo_video_edit:{
      required: true
      },
      video_text_mp4_edit:{
        insert_video_html_edit: true
      },
      video_text_ogg_edit:{
        insert_video_html_edit: true
      },
      video_text_embebido_edit:{
        insert_video_embebido_edit:true
      },
      height_video_woocommerce_edit:{
        insert_video_dimension_edit:true
      }
    },
    messages: {
      wo_di_video_id_edit: {
       required: text_error_id
      }
    }
  });

  //add form
  jQuery.validator.addMethod(
        "insert_video_html",
        function(value, element) {
            if(jQuery("#wo_di_video_servidor").is(':checked')){
              if(jQuery("#video_text_mp4").val()=="" && jQuery("#video_text_ogg").val()==""){
                return false;
              }
            }
            jQuery("#video_text_mp4").removeClass("error");
            jQuery("#video_text_mp4").siblings("p").remove();
            jQuery("#video_text_ogg").removeClass("error");
            jQuery("#video_text_ogg").siblings("p").remove();
            return true;
        },
        text_error_min_html
    );

  jQuery.validator.addMethod(
        "insert_video_embebido",
        function(value, element) {
            if(jQuery("#video_embebido").is(':checked')){
              if(jQuery("#video_text_embebido").val()==""){
                return false;
              }
            }
            return true;
        },
        text_error_insert_html
    );

    jQuery.validator.addMethod(
        "insert_video_dimension",
        function(value, element) {
          if(jQuery("#wo_di_video_servidor").is(':checked')){
            if(jQuery("#height_video_woocommerce").val()=="" && jQuery("#width_video_woocommerce").val()==""){
              jQuery("#height_video_woocommerce").removeClass("error");
              jQuery("#height_video_woocommerce").siblings("p").remove();
              return true;
            }
            if(jQuery("#height_video_woocommerce").val()!="" && jQuery("#width_video_woocommerce").val()!=""){
              jQuery("#height_video_woocommerce").removeClass("error");
              jQuery("#height_video_woocommerce").siblings("p").remove();
              return true;
            }
            return false;
          }
          return true;
        },
        text_error_dimension
    );

   form_add_video=jQuery('#wo_di_form_add_video').validate({
     wrapper:"p",
    //errorLabelContainer :"div_errores_add_video",
    rules:{
      wo_di_video_id :{
        required: true
      },
    wo_di_tipo_video:{
      required: true
      },
      video_text_mp4:{
        insert_video_html: true
      },
      video_text_ogg:{
        insert_video_html: true
      },
      video_text_embebido:{
        insert_video_embebido:true
      },
      height_video_woocommerce:{
        insert_video_dimension:true
      }
    },
    messages: {
      wo_di_video_id: {
       required: text_error_id
      }
    }
  });
}

jQuery(document).ready(function()
    { 
        initiate_rules();
        jQuery('#wo_di_upload_video_edit').click(function()
        {
            tb_show('Upload Video', 'media-upload.php?type=video&context=uploadVideo&tab=type&TB_iframe=true');
            return false;
        });

        jQuery('#wo_di_select_video_edit').click(function()
        {
            tb_show('Select Video', 'media-upload.php?type=video&context=selectVideo&action_video=edit&tab=library&TB_iframe=true');
            return false;
        });

        jQuery('#wo_di_upload_video').click(function()
        {
            tb_show('Upload Video', 'media-upload.php?type=video&context=uploadVideo&tab=type&TB_iframe=true');
            return false;
        });

        jQuery('#wo_di_select_video').click(function()
        {
            tb_show('Select Video', 'media-upload.php?type=video&context=selectVideo&action_video=add&tab=library&TB_iframe=true');
            return false;
        });

        tinyMCE.init({
        mode : "specific_textareas",
        editor_selector : "mceEditorVideoHtml",
        width : "100%",
        height : "300px"
        });
        //edit
         jQuery( "#dialog_form_edit_video").dialog({
        autoOpen: false,
        draggable: false ,
        height: 550,
        width: 565,
        modal: false,
        buttons: [
          {
            text: text_edit_button,
            click: function() {
            jQuery('#wo_di_form_edit_video').submit();
              if(form_edit_video.valid()){
              var formats;
              var height;
              var width;
              var dimension;
              var video_embebido="";
              var video_mp4="";
              var video_ogg="";
              if(jQuery('#wo_di_video_embebido_edit').is(':checked')){
                type="Embedded";
                formats="-";
                height="-";
                width="-";
                dimension="-";
                video_embebido=jQuery("#video_text_embebido_edit").val();
              }else{
                type="WP Library";
                height=jQuery("#height_video_woocommerce_edit").val();
                width=jQuery("#width_video_woocommerce_edit").val();
                if(height=="" && width==""){
                 dimension="Default";
                }else{
                 dimension=height+" X "+width;
                }
                video_mp4=jQuery("#video_text_mp4_edit").val();
                video_ogg=jQuery("#video_text_ogg_edit").val();
                var b_video=false;
                if(video_mp4!=""){
                  formats=" MP4";
                  b_video=true;
                }
                if(video_ogg!=""){
                  if(b_video){
                    formats+=", OGG"
                  }else{
                    formats=" OGG";
                  }
                }
              }
              var title=jQuery("#wo_di_video_title_edit").val();
              var id=jQuery("#wo_di_video_id_edit").val();
              var input_ids=jQuery(tr_edit).find("input[name='wo_di_video_ids[]']");
              jQuery(input_ids).val(id);
              jQuery(input_ids).next().html(id);
              var input_titles=jQuery(tr_edit).find("input[name='wo_di_video_titles[]']");
              jQuery(input_titles).val(title);
              jQuery(input_titles).next().html(title);
              var input_types=jQuery(tr_edit).find("input[name='wo_di_video_types[]']");
              jQuery(input_types).val(type);
              jQuery(input_types).next().html(type);
              jQuery(tr_edit).find("input[name='wo_di_video_embebido[]']").val(video_embebido);
              var input_formats=jQuery(tr_edit).find("input[name='wo_di_video_formats[]']");
              jQuery(input_formats).val(formats);
              jQuery(input_formats).next().html(formats);
              jQuery(tr_edit).find("input[name='wo_di_video_heights[]']").val(height);
              var input_width=jQuery(tr_edit).find("input[name='wo_di_video_widths[]']");
              input_width.val(width);
              jQuery(input_width).next().html(dimension);
              jQuery(tr_edit).find("input[name='wo_di_video_mp4[]']").val(video_mp4);
              jQuery(tr_edit).find("input[name='wo_di_video_ogg[]']").val(video_ogg);
              jQuery( this ).dialog( "close" );
            }else{
              form_edit_video.showErrors();
            }
          }},
          {
            text: text_cancel_button,
            click: function() {
            clean_inputs_edit();
            jQuery( this ).dialog( "close" );
            }
          }]
        });
      //add
        jQuery( "#dialog_form_add_video").dialog({
        autoOpen: false,
        draggable: false ,
        height: 550,
        width: 565,
        modal: false,
        buttons: [
          {
          text : text_add_button,
          click: function() {
              var id=jQuery("#wo_di_video_id").val();
              //console.log("***"+id);
              jQuery('#wo_di_form_add_video').submit();
              if(form_add_video.valid()){
              var formats;
              var height;
              var width;
              var dimension;
              var video_embebido="";
              var video_mp4="";
              var video_ogg="";
              if(jQuery('#video_embebido').is(':checked')){
                type="Embedded";
                formats="-";
                height="-";
                width="-";
                dimension="-";
                video_embebido=jQuery("#video_text_embebido").val();
              }else{
                type="WP Library";
                height=jQuery("#height_video_woocommerce").val();
                width=jQuery("#width_video_woocommerce").val();
                if(height=="" && width==""){
                 dimension="Default";
                }else{
                 dimension=height+" X "+width;
                }
                video_mp4=jQuery("#video_text_mp4").val();
                video_ogg=jQuery("#video_text_ogg").val();
                var b_video=false;
                if(video_mp4!=""){
                  formats=" MP4";
                  b_video=true;
                }
                if(video_ogg!=""){
                  if(b_video){
                    formats+=", OGG"
                  }else{
                    formats=" OGG";
                  }
                }
              }
              var number_of_videos=jQuery("#wo_di_number_of_videos").val();
              number_of_videos++;
              var classColumn="";
              if((number_of_videos%2)!=0){
                classColumn="class='alternate'";
              }

              var title=jQuery("#wo_di_video_title").val();
              var video="<tr id='wo_di_video_product_"+number_of_videos+"' "+classColumn+">";
              //video+="<td><input type=hidden name='wo_di_video_ids[]' value='"+id+"' /><span>"+id+"</span></td>";
              video+="<td><input type=hidden name='wo_di_video_titles[]' value='"+title+"' /><span>"+title+"</span></td>";
              video+="<td><input type=hidden name='wo_di_video_types[]' value='"+type+"' /><span>"+type+"</span></td>";
              video+="<td> <input type=hidden name='wo_di_video_formats[]' value='"+formats+"' /><span>"+formats+"</span></td>";
              video+="<td> <input type=hidden name='wo_di_video_heights[]' value='"+height+"' /><input type=hidden name='wo_di_video_widths[]' value='"+width+"' /><span>"+dimension+"</span></td>";
              video+="<input type=hidden name='wo_di_video_embebido[]'/ value='"+video_embebido+"' >";
              video+="<input type=hidden name='wo_di_video_mp4[]' value='"+video_mp4+"' />";
              video+="<input type=hidden name='wo_di_video_ogg[]' value='"+video_ogg+"' />";
              video+="<td><input type=hidden name='wo_di_video_active[]' value='1' /><input type='checkbox' checked='checked' onchange='update_input_active(this)' /></td>";
              video+="<td><span class='ui-icon ui-icon-trash float-right' onclick='delete_row(this)'></span><span class='ui-icon ui-icon-circle-triangle-s' onclick='edit_row(this)'></span></td>";
              jQuery("#wo_di_table_videos_html").append(video);
              jQuery("#wo_di_number_of_videos").val(number_of_videos);
              jQuery( this ).dialog( "close" );
            }else{
              form_add_video.showErrors();
            }
          }},
        {
          text:text_cancel_button,
          click: function() {
            jQuery( this ).dialog( "close" );
          }
        }]}/*,
        },
        close: function() {
          jQuery( this ).dialog( "close" );
        }*/
      );

      jQuery( "#button_add_video" )
      .button()
      .click(function(event) {
        event.preventDefault();
        jQuery( "#dialog_form_add_video" ).dialog( "open" );
        return false;
      });
    });
