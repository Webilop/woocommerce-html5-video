jQuery(document).ready(function() 
    {    
        jQuery('#wo_di_upload_video').click(function() 
        {
        
            tb_show('Upload Video', 'media-upload.php?type=video&context=uploadVideo&tab=type&TB_iframe=true');
            return false;
        });

        jQuery('#wo_di_select_video').click(function() 
        {
            tb_show('Select Video', 'media-upload.php?type=video&context=selectVideo&tab=library&TB_iframe=true');
            return false;
        });

    });