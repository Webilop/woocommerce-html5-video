$ = jQuery;
function clone_embedded(){
   var length = $('textarea[name="_tab_video[]"]').length;
   if(length <= 3){
      cloned = $("#_tab_video0").clone().val("");
      cloned.insertBefore("a#clone_video").wrap("<p></p>");
      if(length == 2) $("a#clone_video").hide();
   }
}
function remove_video(index){
   $("textarea#_tab_video"+index).val("");
}
