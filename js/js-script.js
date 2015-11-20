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
