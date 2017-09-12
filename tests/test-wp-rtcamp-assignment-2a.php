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

	/**
	 * Setup of 'setUpBeforeClass' test fixture
	 */
	public static function setUpBeforeClass() {
		// Call parent's setUpBeforeClass method.
		parent::setUpBeforeClass();

		$post_type = 'wprtc_slideshow';
		register_post_type( $post_type,
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

}
