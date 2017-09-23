<?php
/**
 * Class Wp_Rtcamp_Slider_Settings_Assignment_2a_Test
 *
 * @package Wp_Rtcamp_Assignment_2a
 */

/**
 * Sample test case.
 */
class Wp_Rtcamp_Slider_Settings_Assignment_2a_Test extends WP_UnitTestCase {
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
	 * Setup test fixture to run for every test method.
	 */
	public function setUp() {

		// Simulate $_POST variable for save_post hook.
		$_POST['post_type'] = self::$post_type;

		// Simulate $_POST variable for nonce.
		$slide_show_nonce = wp_create_nonce( '_wprtc_slideshow_slides_nonce' );
		$_POST['_wprtc_slideshow_slides_nonce'] = $slide_show_nonce;
	}

	/**
	 * Test if slideshow setting "animation_type : fade" is saved.
	 */
	function test_if_wprtc_slideshow_setting_animation_type_fade_is_saved() {

		// Simulate $_POST with slider setting animation_type = fade.
		$_POST['_wprtc_slider_settings[animation_type]'] = array( 'fade' );

		$post_id  = $this->factory()->post->create(
			array(
				'post_status' => 'publish',
				'post_title' => 'Post Title ',
				'post_type' => 'wprtc_slideshow',
			)
		);

		$slider_settings = get_post_meta( $post_id, '_wprtc_slideshow_settings' );

		$slider_settings = $slider_settings[0];
		$this->assertEquals( 'fade', $slider_settings[0] );
	}

	/**
	 * Test if slideshow setting "animation_type : slide" is saved.
	 */
	function test_if_wprtc_slideshow_setting_animation_type_slide_is_saved() {

		// Simulate $_POST with slider setting animation_type = slide.
		$_POST['_wprtc_slider_settings[animation_type]'] = array( 'slide' );

		$post_id  = $this->factory()->post->create(
			array(
				'post_status' => 'publish',
				'post_title' => 'Post Title ',
				'post_type' => 'wprtc_slideshow',
			)
		);

		$slider_settings = get_post_meta( $post_id, '_wprtc_slideshow_settings' );

		$slider_settings = $slider_settings[0];
		$this->assertEquals( 'slide', $slider_settings[0] );
	}
}
