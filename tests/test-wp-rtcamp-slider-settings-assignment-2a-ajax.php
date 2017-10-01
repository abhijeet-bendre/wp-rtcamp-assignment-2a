<?php
/**
 * Class Wp_Rtcamp_Assignment_2a_Ajax_Test
 *
 * @package Wp_Rtcamp_Assignment_2a
 */

 /**
  * Ajax Test case for Assignment-2a: WordPress-Slideshow Plugin
  *
  * @group ajax
  */
class Wp_Rtcamp_Assignment_2a_Ajax_Test extends WP_Ajax_UnitTestCase {

	/**
	 * Test if 'wprtc_get_single_slide_html' Ajax callback returns single slide string.
	 */
	public function test_if_wprtc_get_single_slide_html_ajax_callback_gives_correct_html() {

		// Simulate $_POST with Slides for fake attachment id (for eg. 180), slide order.
		$_POST['wprtc_attachment_id'] = 180;
		$_POST['wprtc_slide_order'] = 1;

		// Simulate $_POST variable for nonce.
		$slide_show_nonce = wp_create_nonce( 'wprtc_get_single_slide_html_nonce' );
		$_POST['wprtc_ajax_nonce'] = $slide_show_nonce;

		$slide_atachment_id = isset( $_POST['wprtc_attachment_id'] ) ? sanitize_text_field( wp_unslash( $_POST['wprtc_attachment_id'] ) ) : ''; // Input var okay. WPCS: CSRF ok.
		$slide_order = isset( $_POST['wprtc_slide_order'] ) ? sanitize_text_field( wp_unslash( $_POST['wprtc_slide_order'] ) ) : ''; // Input var okay. WPCS: CSRF ok.

		// Simulate $_POST variable for nonce.
		$slide_show_nonce = wp_create_nonce( 'wprtc_get_single_slide_html_nonce' );
		$_POST['wprtc_ajax_nonce'] = $slide_show_nonce;

		// Single Slide HTML.
		$single_side_html  = '<div class="wprtc_image_preview_wrapper">';
		$single_side_html .= '<div class="wprtc_image_preview">';
		$single_side_html .= '<img  src="' . esc_url( wp_get_attachment_url( $slide_atachment_id ) ) . '"  />';
		$single_side_html .= '</div>';
		$single_side_html .= '<div class="wprtc_slide_actions_wrapper">';
		$single_side_html .= '<div class="wprtc_slide_actions">';
		$single_side_html .= '<a href="#" class="wprtc_edit_slide_button" data-slide-order="' . esc_attr( $slide_order ) . '">' . esc_html__( 'Edit Slide', 'wprtc_assignment_2a' ) . '</a>';
		$single_side_html .= '<a href="#" class="wprtc_delete_slide_button" data-slide-order="' . esc_attr( $slide_order ) . '">' . esc_html__( 'Delete Slide', 'wprtc_assignment_2a' ) . '</a>';
		$single_side_html .= '</div>';
		$single_side_html .= '</div>';
		$single_side_html .= '<input type="hidden" name="_wprtc_slide_order[' . esc_attr( $slide_order ) . ']" value="' . esc_attr( $slide_atachment_id ) . '" / >';
		$single_side_html .= '</div>';

		try {
			$this->_handleAjax( 'wprtc_get_single_slide_html' );
			$this->fail( 'Expected exception: WPAjaxDieContinueException' );
		} catch ( WPAjaxDieContinueException $e ) { // @codingStandardsIgnoreLine
				// We expected this, do nothing.
		}
		// The output should be a 1 for success.
		$this->assertSame( $single_side_html, $this->_last_response );
	}
}
