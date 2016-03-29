jQuery(document).ready(function(){

  jQuery('#post-review').click(function() {
    saveUserReview();
    dismissNotice(jQuery('#review-notice'));
  });

  jQuery('#skip-review').click(function(e) {
    e.preventDefault();
    saveUserReview();
    dismissNotice(jQuery('#review-notice'));
  });

  function saveUserReview() {
    jQuery.ajax({
      url: ajaxurl,
      data: {
        action: 'save_review'
      },
      method: 'POST'
    });
  }

  function dismissNotice($notice) {
    $notice.fadeTo( 100, 0, function() {
      $notice.slideUp( 100, function() {
        $notice.remove();
      });
    });
  }
});
