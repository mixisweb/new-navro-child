jQuery(document).ready(function($){
	// Only show the "remove image" button when needed
	if ( ! jQuery('.up_thumb_fb_img').val() ) {
		jQuery('.rm_thumb_fb_button').hide();
	}
	
    var custom_uploader;
    $('.up_thumb_fb_button').click(function(e) {
        e.preventDefault();
        //If the uploader object has already been created, reopen the dialog
        if (custom_uploader) {
            custom_uploader.open();
            return;
        }
        //Extend the wp.media object
        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            },
            multiple: false
        });
 
        //When a file is selected, grab the URL and set it as the text field's value
        custom_uploader.on('select', function() {
            attachment = custom_uploader.state().get('selection').first().toJSON();
            jQuery('.up_thumb_fb_img').val(attachment.url);
            jQuery('.thumb_fb_img img').attr('src', attachment.url );
			jQuery('.rm_thumb_fb_button').show();
        });
 
        //Open the uploader dialog
        custom_uploader.open();
    });
    $('.rm_thumb_fb_button').click(function(e) {
    	jQuery('.up_thumb_fb_img').val('');
		jQuery('.thumb_fb_img img').attr('src', '<?php echo wc_placeholder_img_src(); ?>');
		jQuery('.rm_thumb_fb_button').hide();
		return false;
	});
});