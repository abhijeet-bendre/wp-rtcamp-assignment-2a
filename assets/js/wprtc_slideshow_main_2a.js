jQuery( document ).ready( function() {
  'use strict';
    /*global
      wp, post
    */
  // Open media dialog on "Add new Slide"  click.
  jQuery( '#wprtc_add_new_slide' ).on( 'click', function( event ) {
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
      var attachment = file_frame.state().get( 'selection' ).first().toJSON();
      var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id
      var single_slide_html = "";

      var slide_order = jQuery( '.wprtc_image_preview_wrapper' ).length;
      slide_order =  slide_order + 1;

      var data = {
        'action': 'wprtc_get_single_slide_html',
        'wprtc_attachment_id': attachment.id,
        'wprtc_slide_order': slide_order,
      };

      // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
      jQuery.post( ajaxurl, data, function( single_slide_html )   {
        //alert(single_slide_html);
        jQuery( '.wprtc_slideshow_wrapper' ).append( single_slide_html );
      });

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

  // On "Edit Slide" click, update with newly selected image.
  jQuery( '.wprtc_slideshow_wrapper' ).on( 'click', '.wprtc_edit_slide_button', function( event ){
    // Uploading files
    var file_frame;
    // Get current Slide order no.
    var current_slide_order = jQuery( this ).attr( 'data-slide-order' );
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
      var attachment = file_frame.state().get( 'selection' ).first().toJSON();
      var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id

      /*
       * 1)Find the the element whose slide order matches with data('slide-order')
       * 2)Find parent of it and search for child image element of this parent.
       */
      var slide_order = jQuery( 'input[name=wprtc_slide_order\\[' + current_slide_order + '\\]]' );
      var image_preview_wrapper = slide_order.parent();
      image_preview_wrapper.find( 'img' ).attr( 'src', attachment.url );
      // Update newly attachment id.
      slide_order.attr( 'value', attachment.id );
      // Restore the main post ID
      wp.media.model.settings.post.id = wp_media_post_id;
    });

    // Finally, open the modal
    file_frame.open();
  });

  // On "Delete Slide" click remove the slide
  jQuery( '.wprtc_slideshow_wrapper' ).on( 'click' , '.wprtc_delete_slide_button', function( event ){

    var current_slide_order = jQuery( this ).attr( 'data-slide-order' );
    alert(current_slide_order);
    event.preventDefault();
    /*
     * 1)Find the the element whose slide order matches with data-slide-order
     * 2)Find parent of it and remove it.
     */
    jQuery( 'input[name=wprtc_slide_order\\[' + current_slide_order + '\\]]' ).parent().remove();
    //Update Slide orders.
    jQuery( '.wprtc_image_preview_wrapper' ).each( function( i, el ){
      var slide_order = jQuery( el ).index()+1;
      jQuery( this ).find( 'input[type=hidden]' ).attr( 'name', 'wprtc_slide_order['+ slide_order +']' );
      //Uddate corresponding slide order.
      jQuery( this ).find( '.wprtc_edit_slide_button' ).attr( 'data-slide-order', slide_order );
      jQuery( this ).find( '.wprtc_delete_slide_button' ).attr( 'data-slide-order', slide_order );
    });
  });

  // Sort slider Images.
  jQuery( function() {
    jQuery( '#wprtc_sortable' ).sortable({
      placeholder: 'wprtc_state_highlight',
      start: function(e, ui){
        ui.placeholder.height( ui.item.height() );
      },
      update: function() {
        jQuery( '.wprtc_image_preview_wrapper' ).each(function( i, el ){
          var slide_order = jQuery(el).index()+1;
          //Uddate Corresponding order ids.
          jQuery(this).find( 'input[type=hidden]' ).attr( 'name', 'wprtc_slide_order['+ slide_order +']' );
          jQuery(this).find( '.wprtc_edit_slide_button' ).attr( 'data-slide-order', slide_order );
          jQuery(this).find( '.wprtc_delete_slide_button' ).attr( 'data-slide-order', slide_order );
        });
      }
    });

    jQuery( '#wprtc_sortable' ).disableSelection();
  });


});
