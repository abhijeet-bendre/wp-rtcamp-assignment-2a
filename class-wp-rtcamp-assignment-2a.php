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

define( 'WPRTC_2A_PLUGIN_NAME', 'wp-rtcamp-assignment-2a' );
define( 'WPRTC_2A_PLUGIN_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );

/**
 * RtCamp Assignment 2a Class.
 *
 * @category Class
 *
 * @since 0.1
 */
class Wp_Rtcamp_Assignment_2a {

	/**
	 * Constructor for this class
	 *
	 * @since 0.1
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'wprtc_register_rtcamp_slideshow_post_type' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'wprtc_init_assets' ) );
	}

	/**
	 * Registered CPT 'rtcamp_slideshow'
	 *
	 * @since 0.1
	 */
	public function wprtc_register_rtcamp_slideshow_post_type() {

		// Register Custom Post Type.
			$labels = array(
				'name'                => _x( 'rtCamp Slideshow', 'rtcamp_slideshow', 'wp_rtcamp_assignment_2a' ),
				'singular_name'       => _x( 'rtCamp slideshow', 'rtcamp_slideshow', 'wp_rtcamp_assignment_2a' ),
				'menu_name'           => _x( 'rtCamp slideshow', 'wp_rtcamp_assignment_2a' ),
				'name_admin_bar'      => __( 'rtCamp slideshow', 'wp_rtcamp_assignment_2a' ),
				'all_items'           => __( 'All rtCamp Sliders', 'wp_rtcamp_assignment_2a' ),
				'add_new_item'        => __( 'Add New rtCamp Slider', 'wp_rtcamp_assignment_2a' ),
				'add_new'             => __( 'Add New', 'wp_rtcamp_assignment_2a' ),
				'new_item'            => __( 'New rtCamp Slider', 'wp_rtcamp_assignment_2a' ),
				'edit_item'           => __( 'Edit rtCamp Slider', 'wp_rtcamp_assignment_2a' ),
				'update_item'         => __( 'Update rtCamp Slider', 'wp_rtcamp_assignment_2a' ),
				'view_item'           => __( 'View rtCamp Slider', 'wp_rtcamp_assignment_2a' ),
				'not_found'           => __( 'Not found', 'wp_rtcamp_assignment_2a' ),
				'not_found_in_trash'  => __( 'Not found in Trash', 'wp_rtcamp_assignment_2a' ),
			);

			$args = array(
				'label'               => __( 'rtCamp Slideshow', 'wp_rtcamp_assignment_2a' ),
				'description'         => __( 'rtCamp Slideshow', 'wp_rtcamp_assignment_2a' ),
				'labels'              => $labels,
				'supports'            => array( 'title' ),
				'register_meta_box_cb' => array( $this, 'rtcamp_setup_slideshow_metaboxes' ),
				'hierarchical'        => false,
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'menu_position'       => 5,
				'show_in_admin_bar'   => true,
				'can_export'          => true,
				'has_archive'         => true,
				'exclude_from_search' => true,
				'publicly_queryable'  => true,
			);

			register_post_type( 'rtcamp_slideshow' , $args );
	}
	/**
	 * Init assets such as JS/CSS, required by plugin
	 *
	 * @since 0.1
	 */
	public function wprtc_init_assets() {
		wp_register_script( 'wprtc_slideshow_main_2a', plugin_dir_url( __FILE__ ) . 'assets/js/wprtc_slideshow_main_2a.js' );
		wp_enqueue_script( 'wprtc_slideshow_main_2a', array( 'jquery' ) );
		wp_localize_script( 'wprtc_slideshow_main_2a', 'ajaxurl', admin_url( 'admin-ajax.php' ) );
	}


	/**
	 * Setup Metaboxes for CPT 'rtcamp_slideshow'
	 *
	 * @since 0.1
	 */
	public function rtcamp_setup_slideshow_metaboxes() {
		add_meta_box( 'rtcamp_slideshow',  __( 'Add Slides', 'wp_rtcamp_assignment_2a' ), array( $this, 'rtcamp_render_slideshow_metaboxes' ), 'rtcamp_slideshow', 'normal', 'default' );
	}
	/**
	 * Setup Metaboxes for CPT 'rtcamp_slideshow'
	 *
	 * @since 0.1
	 */
	public function rtcamp_render_slideshow_metaboxes() {
		// magic goes here.
	}
}

new Wp_Rtcamp_Assignment_2a();
