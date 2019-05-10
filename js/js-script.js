function clone_embedded(){
   var length = jQuery('textarea[name="_tab_video[]"]').length;
   if(length <= 3){
      cloned = jQuery("#_tab_video0").clone().val("");
      cloned.insertBefore("a#clone_video").wrap("<p></p>");
      if(length == 2) jQuery("a#clone_video").hide();
   }
}
function remove_video(index){
   jQuery("textarea#_tab_video"+index).val("");
}

function check_form_settings(){
  if(jQuery("#wo_di_video_size_forcing").is(":checked")){
    var field_width = jQuery("#wo_di_config_video_width").val();
    var field_height = jQuery("#wo_di_config_video_height").val();

    if(field_width == "" || field_width <= 0){
      jQuery("#span_errors").html("Invalid value: Video Width");
      return false;
    }
    else if(field_height == "" || field_height <= 0){
      jQuery("#span_errors").html("Invalid value: Video Height");
      return false;
    }
  }
  return true;
}

jQuery(document).ready(function() {
  jQuery('#wo_di_form_add_video input[name="wo_di_tipo_video"]').change(function(){
    var divClass = jQuery(this).val();
    divClass = '#wo_di_form_add_video div.' + divClass + '-video';
    jQuery('#wo_di_form_add_video div.video-option').hide();
    jQuery(divClass).show();
  });

  jQuery('#wo_di_form_edit_video input[name="wo_di_tipo_video_edit"]').change(function(){
    var divClass = jQuery(this).val();
    divClass = '#wo_di_form_edit_video div.' + divClass + '-video';
    jQuery('#wo_di_form_edit_video div.video-option').hide();
    jQuery(divClass).show();
  });
});
