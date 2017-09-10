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
	 * Test if Plugin is active.
	 */
	function test_is_plugin_active() {
		$this->assertTrue( is_plugin_active( WPRTC_2A_PLUGIN_NAME . '/' . WPRTC_2A_PLUGIN_NAME . 'php' ) );
	}
}
