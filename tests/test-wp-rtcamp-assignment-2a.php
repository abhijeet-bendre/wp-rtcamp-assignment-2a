<?php
/**
 * Class Wp_Rtcamp_Assignment_2a_Test
 *
 * @package Wp_Rtcamp_Assignment_2a
 */

/**
 * Sample test case.
 */
class Wp_Rtcamp_Assignment_2a_Test extends WP_UnitTestCase {
	/**
	 * CPT slug for slide show.
	 *
	 * @var static protected
	 */
	protected static $post_type = 'wprtc_slideshow';

	/**
	 * Setup of 'setUpBeforeClass' test fixture
	 */
	public static function setUpBeforeClass() {
		// Call parent's setUpBeforeClass method.
		parent::setUpBeforeClass();

		register_post_type( self::$post_type,
			array(
				'supports' => array(
					'title',
				),
			)
		);
	}

	/**
	 * Test if Plugin is active.
	 */
	function test_is_plugin_active() {
		$this->assertTrue( is_plugin_active( WPRTC_2A_PLUGIN_NAME . '/' . WPRTC_2A_PLUGIN_NAME . 'php' ) );
	}

	/**
	 * Test if slideshow post_type exists
	 */
	function test_if_slideshow_post_type_exists() {
		$this->assertTrue( post_type_exists( 'wprtc_slideshow' ) );
	}

	/**
	 * Test if slideshow slides are saved
	 */
	function test_if_wprtc_slideshow_slides_are_saved() {
		// Simulate $_POST variable for save_post hook.
		$_POST['post_type'] = self::$post_type;

		// Simulate $_POST variable for nonce.
		$slide_show_nonce = wp_create_nonce( '_wprtc_slideshow_slides_nonce' );
		$_POST['_wprtc_slideshow_slides_nonce'] = $slide_show_nonce;

		// Simulate $_POST with Slides for fake attachment id (for eg. 180).
		$_POST['_wprtc_slide_order'] = array( 180 );
		$post_id  = $this->factory()->post->create(
			array(
				'post_status' => 'publish',
				'post_title' => 'Post Title ',
				'post_type' => 'wprtc_slideshow',
			)
		);

		$slider_images = get_post_meta( $post_id, '_wprtc_slideshow_slides' );

		$slider_images = $slider_images[0];
		$this->assertEquals( 180, $slider_images[0] );
	}
}
