/**
 * Main Js File for Assignment-2a: WordPress-Slideshow Plugin
 *
 * @package Wp_Rtcamp_Assignment_2a
 */

jQuery( document ).ready(
	function() {
			'use strict';
			/*global wp, post , wprtc_get_single_slide_html_nonce, ajaxurl */

			/*
			* Open media dialog on "Add new Slide" click.
			*/
			jQuery( '#wprtc_add_new_slide' ).on(
				'click', function( event ) {
					// Uploading files.
					var file_frame;
					event.preventDefault();

					// If the media frame already exists, reopen it.
					if ( file_frame ) {
						// Set the post ID to what we want.
						file_frame.uploader.uploader.param( 'post_id', post.ID );
						// Open frame.
						file_frame.open();
						return;
					} else {
						// Set the wp.media post id so the uploader grabs the ID we want when initialised.
						wp.media.model.settings.post.id = post.ID;
					}

					// Create the media frame.
					file_frame = wp.media.frames.file_frame = wp.media(
						{
							title: 'Select a image to upload',
							button: {
								text: 'Use this image',
							},
							multiple: false // Set to true to allow multiple files to be selected.
						}
					);

					// When an image is selected, run a callback.
					file_frame.on(
						'select', function() {
							// We set multiple to false so only get one image from the uploader.
							var attachment = file_frame.state().get( 'selection' ).first().toJSON();
							var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id.

							var slide_order = jQuery( '.wprtc_image_preview_wrapper' ).length;
							slide_order = slide_order + 1;

							var data = {
								'action': 'wprtc_get_single_slide_html',
								'wprtc_attachment_id': attachment.id,
								'wprtc_slide_order': slide_order,
								'wprtc_ajax_nonce' : wprtc_get_single_slide_html_nonce
							};

							// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php.
							jQuery.post(
								ajaxurl, data, function( single_slide_html )   {
									jQuery( '.wprtc_slideshow_wrapper' ).append( single_slide_html );
								}
							);

							// Restore the main post ID.
							wp.media.model.settings.post.id = wp_media_post_id;
						}
					); // "Add new Slide" click ends.

					// Finally, open the modal.
					file_frame.open();
				}
			);

			  // Restore the main ID when the add media button is pressed.
			  jQuery( 'a.add_media' ).on(
				  'click', function() {
					var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id.
					wp.media.model.settings.post.id = wp_media_post_id;
				  }
			  );

			  /*
				* On "Edit Slide" click, update with newly selected image.
				*/
			  jQuery( '.wprtc_slideshow_wrapper' ).on(
				  'click', '.wprtc_edit_slide_button', function( event ){
					// Uploading files.
					var file_frame;
					// Get current Slide order no.
					var current_slide_order = jQuery( this ).attr( 'data-slide-order' );
					event.preventDefault();

					// If the media frame already exists, reopen it.
					if (file_frame ) {
						// Set the post ID to what we want.
						file_frame.uploader.uploader.param( 'post_id', post.ID );
						// Open frame.
						file_frame.open();
						return;
					} else {
						// Set the wp.media post id so the uploader grabs the ID we want when initialised.
						wp.media.model.settings.post.id = post.ID;
					}

					// Create the media frame.
					file_frame = wp.media.frames.file_frame = wp.media(
						{
							title: 'Select a image to upload',
							button: {
								text: 'Use this image',
							},
							multiple: false    // Set to true to allow multiple files to be selected.
						}
					);

					// When an image is selected, run a callback.
					file_frame.on(
						'select', function() {
							// We set multiple to false so only get one image from the uploader.
							var attachment = file_frame.state().get( 'selection' ).first().toJSON();
							var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id.

													/*
                            * 1)Find the the element whose slide order matches with data('slide-order')
                            * 2)Find parent of it and search for child image element of this parent.
                            */
							var slide_order = jQuery( 'input[name=_wprtc_slide_order\\[' + current_slide_order + '\\]]' );
							var image_preview_wrapper = slide_order.parent();
							image_preview_wrapper.find( 'img' ).attr( 'src', attachment.url );
							// Update newly attachment id.
							slide_order.attr( 'value', attachment.id );
							// Restore the main post ID.
							wp.media.model.settings.post.id = wp_media_post_id;
						}
					);

					// Finally, open the modal.
					file_frame.open();
				  }
			  ); // Edit Slide" click ends.

			  /*
				* On "Delete Slide" click remove the slide.
				*/
			  jQuery( '.wprtc_slideshow_wrapper' ).on(
				  'click' , '.wprtc_delete_slide_button', function( event ) {

					var current_slide_order = jQuery( this ).attr( 'data-slide-order' );
					event.preventDefault();

					// Confirm if Slide is to be deleted.
					var delete_slide = window.confirm( 'Do you want to delete this Slide?' );
					if ( ! delete_slide) {
						return;
					}

								/*
            		* 1)Find the the element whose slide order matches with data-slide-order
            		* 2)Find parent of it and remove it.
            		*/
					jQuery( 'input[name=_wprtc_slide_order\\[' + current_slide_order + '\\]]' ).parent().remove();
					// Update Slide orders.
					jQuery( '.wprtc_image_preview_wrapper' ).each(
						function( i, el ){
							var slide_order = jQuery( el ).index() + 1;
							jQuery( this ).find( 'input[type=hidden]' ).attr( 'name', '_wprtc_slide_order[' + slide_order + ']' );
							// Update corresponding slide order.
							jQuery( this ).find( '.wprtc_edit_slide_button' ).attr( 'data-slide-order', slide_order );
							jQuery( this ).find( '.wprtc_delete_slide_button' ).attr( 'data-slide-order', slide_order );
						}
					);
				  }
			  ); // on "Delete Slide" click ends.

				 /*
				 * Sort slider Images.
				 */
				// @codingStandardsIgnoreStart
			  jQuery(
						function() {
									  jQuery( '#wprtc_sortable' ).sortable(
										  {// @codingStandardsIgnoreEnd
												placeholder: 'wprtc_state_highlight',
												start: function( e, ui ){
													ui.placeholder.height( ui.item.height() );
												},
												update: function() {
													jQuery( '.wprtc_image_preview_wrapper' ).each(
														function( i, el ){
															var slide_order = jQuery( el ).index() + 1;
															// Update corresponding slide order ids.
															jQuery( this ).find( 'input[type=hidden]' ).attr( 'name', '_wprtc_slide_order[' + slide_order + ']' );
															jQuery( this ).find( '.wprtc_edit_slide_button' ).attr( 'data-slide-order', slide_order );
															jQuery( this ).find( '.wprtc_delete_slide_button' ).attr( 'data-slide-order', slide_order );
														}
													);
												}
												}
									  );

										jQuery( '#wprtc_sortable' ).disableSelection();
						}
			  ); // Sort slider Images end.

				/*
				* If no. of slides is less than 2, prevent slider from saving.
				*/
				jQuery( '#post' ).submit( function() {
					if ( 'wprtc_slideshow' !== jQuery( '#post_type' ).val() ) {
						return ;
					}
					var slide_order = jQuery( '.wprtc_image_preview_wrapper' ).length;
					if ( slide_order < 2 ) {
						window.alert( 'Please add atleast 2 Slides' );
						return false;
					}
					return true;
				}); // post submit ends.

	}
);
