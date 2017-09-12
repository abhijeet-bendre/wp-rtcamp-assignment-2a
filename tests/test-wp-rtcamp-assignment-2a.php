<?php
/**
 * Class SampleTest
 *
 * @package Wp_Rtcamp_Assignment_2a
 */

/**
 * Sample test case.
 */
class Wp_Rtcamp_Assignment_2a_Test extends WP_UnitTestCase {

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
	 * Test if slideshow post meta is saved
	 */
	function test_if_slideshow_post_meta_is_saved() {
		// Simulate $_POST variable for save_post hook.
		$_POST['post_type'] = self::$post_type;

		// Simulate $_POST with Slides for fake attachment id for .eg. 180.
		$_POST['wprtc_slide_order'] = array( 180 );
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
