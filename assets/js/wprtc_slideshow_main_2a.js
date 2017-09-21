jQuery(document).ready(function(){
  'use strict';
    /*global
      wp, post
    */
  jQuery('#wprtc_add_new_slide').on('click', function( event ){
    // Uploading files
    var file_frame;
    event.preventDefault();

    // If the media frame already exists, reopen it.
    if ( file_frame ) {
      // Set the post ID to what we want
      file_frame.uploader.uploader.param( 'post_id', post.ID );
      // Open frame
      file_frame.open();
      return;
    } else {
      // Set the wp.media post id so the uploader grabs the ID we want when initialised
      wp.media.model.settings.post.id = post.ID;
    }

    // Create the media frame.
    file_frame = wp.media.frames.file_frame = wp.media({
      title: 'Select a image to upload',
      button: {
        text: 'Use this image',
      },
      multiple: false	// Set to true to allow multiple files to be selected
    });

    // When an image is selected, run a callback.
    file_frame.on( 'select', function() {
      // We set multiple to false so only get one image from the uploader
      var attachment = file_frame.state().get('selection').first().toJSON();
      var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id
      var num_of_slides = jQuery('.wprtc_image_preview_wrapper').length;
      num_of_slides =  num_of_slides + 1;

      var image_preview = "<div class='wprtc_image_preview_wrapper'>";
      image_preview += "<img class='wprtc_image_preview' src='"+ attachment.url +"' height='150'>";
      image_preview += "<input type='hidden' name='wprtc_slide_order["+ num_of_slides +"]' value='"+ attachment.id +"'>";
      image_preview += "</div";

      jQuery('.wprtc_slideshow_wrapper').append(image_preview);
      // Restore the main post ID
      wp.media.model.settings.post.id = wp_media_post_id;
    });

    // Finally, open the modal
    file_frame.open();
  });

  // Restore the main ID when the add media button is pressed
  jQuery( 'a.add_media' ).on( 'click', function() {
    var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id
    wp.media.model.settings.post.id = wp_media_post_id;
  });

  // Sort slider Images.
  jQuery( function() {
    jQuery( '#wprtc_sortable' ).sortable({
      placeholder: 'wprtc_state_highlight',
      start: function(e, ui){
        ui.placeholder.height(ui.item.height());
      },
      update: function() {
        jQuery('.wprtc_image_preview_wrapper').each(function(i, el){
          var slide_order = jQuery(el).index()+1;
          jQuery(this).find('input[type=hidden]').attr('name', 'wprtc_slide_order['+ slide_order +']' );
          jQuery(this).find('.wprtc_delete_slide_button').attr('data-slide-order', slide_order);
        });
      }
    });

    jQuery( '#wprtc_sortable' ).disableSelection();
  });

  jQuery('.wprtc_delete_slide_button').on('click', function( event ){
    event.preventDefault();
    var current_slide_order = jQuery(this).data('slide-order');

    // Find the input name whose slide remove The
    jQuery('input[name=wprtc_slide_order\\[' + current_slide_order + '\\]]').parent().remove();
    jQuery('.wprtc_image_preview_wrapper').each(function(i, el){
      var slide_order = jQuery(el).index()+1;
      jQuery(this).find('input[type=hidden]').attr('name', 'wprtc_slide_order['+ slide_order +']' );
      jQuery(this).find('.wprtc_delete_slide_button').attr('data-slide-order', slide_order);
    });

  });

});
