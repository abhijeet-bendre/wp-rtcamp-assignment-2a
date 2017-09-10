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
Text Domain: wprtc_assignment_2a
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
				'name'                => _x( 'rtCamp Slideshow', 'rtcamp_slideshow', 'wprtc_assignment_2a' ),
				'singular_name'       => _x( 'rtCamp slideshow', 'rtcamp_slideshow', 'wprtc_assignment_2a' ),
				'menu_name'           => _x( 'rtCamp slideshow', 'wprtc_assignment_2a' ),
				'name_admin_bar'      => __( 'rtCamp slideshow', 'wprtc_assignment_2a' ),
				'all_items'           => __( 'All rtCamp Sliders', 'wprtc_assignment_2a' ),
				'add_new_item'        => __( 'Add New rtCamp Slider', 'wprtc_assignment_2a' ),
				'add_new'             => __( 'Add New', 'wprtc_assignment_2a' ),
				'new_item'            => __( 'New rtCamp Slider', 'wprtc_assignment_2a' ),
				'edit_item'           => __( 'Edit rtCamp Slider', 'wprtc_assignment_2a' ),
				'update_item'         => __( 'Update rtCamp Slider', 'wprtc_assignment_2a' ),
				'view_item'           => __( 'View rtCamp Slider', 'wprtc_assignment_2a' ),
				'not_found'           => __( 'Not found', 'wprtc_assignment_2a' ),
				'not_found_in_trash'  => __( 'Not found in Trash', 'wprtc_assignment_2a' ),
			);

			$args = array(
				'label'               => __( 'rtCamp Slideshow', 'wprtc_assignment_2a' ),
				'description'         => __( 'rtCamp Slideshow', 'wprtc_assignment_2a' ),
				'labels'              => $labels,
				'supports'            => array( 'title' ),
				'register_meta_box_cb' => array( $this, 'wprtc_setup_slideshow_metaboxes' ),
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

			register_post_type( 'wprtc_slideshow' , $args );
	}
	/**
	 * Init assets such as JS/CSS, required by plugin
	 *
	 * @since 0.1
	 */
	public function wprtc_init_assets() {
		// Register and Enqueue Style.
		wp_register_style( 'wprtc_slideshow_main_2a_css', plugin_dir_url( __FILE__ ) . 'assets/css/wprtc_slideshow_main_2a.css',null );
		wp_enqueue_style( 'wprtc_slideshow_main_2a_css' );

		// Register and Enqueue Script.
		wp_register_script( 'wprtc_slideshow_main_2a_js', plugin_dir_url( __FILE__ ) . 'assets/js/wprtc_slideshow_main_2a.js' );
		wp_enqueue_script( 'wprtc_slideshow_main_2a_js', array( 'jquery' ) );
		wp_localize_script( 'wprtc_slideshow_main_2a_js', 'ajaxurl', admin_url( 'admin-ajax.php' ) );

		if ( ! wp_script_is( 'jquery-ui', 'enqueued' ) ) {
			wp_enqueue_script( 'jquery-ui' );
		}
	}

	/**
	 * Setup Metaboxes for CPT 'wprtc_slideshow'
	 *
	 * @since 0.1
	 */
	public function wprtc_setup_slideshow_metaboxes() {
		add_meta_box( 'wprtc_slideshow',  __( 'Add Slides', 'wprtc_assignment_2a' ), array( $this, 'wprtc_render_slideshow_metaboxes' ), 'wprtc_slideshow', 'normal', 'default' );
	}

	/**
	 * Render Metaboxes for CPT 'rtcamp_slideshow'
	 *
	 * @since 0.1
	 */
	public function wprtc_render_slideshow_metaboxes() {
		global $post;
		wp_enqueue_media();
		wp_localize_script( 'wprtc_slideshow_main_2a_js', 'post', array( 'ID' => $post->ID ) );
		$slide_images = get_post_meta( $post->ID, '_wprtc_slideshow_slides' );

		ob_start();
		echo "<div class='wprtc_slideshow_wrapper' id='wprtc_sortable'>";
		if ( ! empty( $slide_images ) ) {
			foreach ( $slide_images as $slide_order => $slide_url ) {
				echo "<div class='wprtc_image_preview_wrapper'>
			 					<img class='wprtc_image_preview' src='" . wp_get_attachment_url( $slide_url ) . "' height='150'>
			 					<input type='hidden' name='wprtc_slide_order_" . $slide_order . "' value='" . $slide_order_url . "'>
			 				</div>";
			}
		}
		echo '</div>';
		echo "<div class='wprtc_button_wrapper'>
						<input id='wprtc_add_new_slide' type='button upload_image_button' class='button' value='" . __( 'Add New Slide', 'wprtc_assignment_2a' ) . "'/>
					</div>";
		ob_get_flush();
	}
}

new Wp_Rtcamp_Assignment_2a();
