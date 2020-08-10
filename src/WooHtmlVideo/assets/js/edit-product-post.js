/**
 * This script handles the button and dialog to edit videos in a product.
 */
(function($){
    $(document).ready(function(){
        // create the dialog
        let amountVideosTag = $("#amountVideosTag");
        let videosDialog = jQuery("#wh5vVideosDialog").dialog({
            autoOpen: false,
            modal: true,
            draggable: false,
            width: window.innerWidth * 0.9,
            height: window.innerHeight * 0.9,
            close: function(event, ui) {
                $.get(wh5v_edit_product.ajaxurl, {
                    'action': 'wh5v_get_amount_videos',
                    'post_id': videosDialog.data('postid')
                }, function(res){
                    const label = (res == 1) ? wh5v_edit_product.amount_label.singular : wh5v_edit_product.amount_label.plural;
                    amountVideosTag.html(res + " " + label);
                });
            }
        });
        // add listener to the button to open the dialog
        jQuery("#wh5vOpenVideosBtn").click(function(){
            videosDialog.dialog('open');
            amountVideosTag.html("...");
            return false;
        });
    });
})(jQuery);