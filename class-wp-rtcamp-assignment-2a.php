<?php
/**
 *  Assignment-2a: WordPress-Slideshow Plugin
 *
 * @package rtCamp
 * @version 0.1
 */

/*
Plugin Name: Assignment-2a: WordPress-Slideshow Plugin
Plugin URI:  http://tymescripts.com/rtCamp-assignment
Description: Assignment-2a: WordPress-Slideshow Plugin
Version:     0.1
Author:      Abhijeet Bendre
Author URI:  http://tymescripts.com
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
Text Domain: wp_rtcamp_assignment_2a
*/

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

namespace  Wp_Rtcamp_Assignment_2a;

define( 'WPRTC_2A_PLUGIN_NAME', 'wp-rtcamp-assignment-2a' );
define( 'WPRTC_2A_PLUGIN_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );

/**
 * rtCamp Assignment 2a Class.
 *
 * @category Class
 *
 * @since 0.1
 */
class Wp_Rtcamp_Assignment_2a {

	public function __construct() {
	}

}

new Wp_Rtcamp_Assignment_2a();
