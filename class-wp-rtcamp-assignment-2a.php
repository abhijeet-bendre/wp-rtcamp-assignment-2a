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
		add_action( 'wp_enqueue_scripts', array( $this, 'wprtc_init_front_end_assets' ) );
		add_action( 'save_post', array( $this, 'wprtc_save_slides' ), 10 );
		add_shortcode( 'wprtc_slideshow', array( $this, 'wprtc_slideshow' ) );

		// Attach hook for adding Custom Column to wprtc_slideshow CPT.
		add_filter( 'manage_posts_columns', array( $this, 'wprtc_slideshow_cpt_table_columns_title' ) );
		add_action( 'manage_posts_custom_column', array( $this, 'wprtc_slideshow_cpt_table_columns_content' ), 10, 2 );
		add_action( 'add_meta_boxes',array( $this, 'wprtc_setup_slideshow_metaboxes' ) );
	}

	/**
	 * Register CPT 'wprtc_slideshow'
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
				//'register_meta_box_cb' => array( $this, 'wprtc_setup_slideshow_metaboxes' ),
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
		wp_register_style( 'wprtc_slideshow_main_2a_css', plugin_dir_url( __FILE__ ) . 'assets/css/wprtc_slideshow_main_2a.css', null );
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
	 * Init Front-end assets such as JS/CSS, required by plugin
	 *
	 * @since 0.1
	 */
	public function wprtc_init_front_end_assets() {

		// Register and Enqueue Style only if its not previously enqueued.
		if ( ! wp_style_is( 'flexslider_style', 'enqueued' ) ) {
			wp_register_style( 'flexslider_style', plugin_dir_url( __FILE__ ) . 'assets/css/lib/flexslider.css', null );
			$registered  = wp_enqueue_style( 'flexslider_style' );
		}

		// Register and Enqueue Style slideshow frontend js.
		//wp_register_script( 'wprtc_slideshow_frontend_2a_js', plugin_dir_url( __FILE__ ) . 'assets/js/wprtc_slideshow_frontend_2a.js' );
		//wp_enqueue_script( 'wprtc_slideshow_frontend_2a_js', array( 'jquery' ) );

		// Register and Enqueue flexslider_script only if its not previously enqueued.
		if ( ! wp_script_is( 'flexslider_script', 'enqueued' ) ) {
			wp_register_script( 'flexslider_script', plugin_dir_url( __FILE__ ) . 'assets/js/lib/jquery.flexslider-min.js' );
			wp_enqueue_script( 'flexslider_script', array( 'jquery' ) );
		}
	}

	/**
	 * Add Column Title to wprtc_slideshow post_type.
	 *
	 * @param array $defaults Default Columns for CPT.
	 *
	 * @since 0.1
	 */
	public function wprtc_slideshow_cpt_table_columns_title( $defaults ) {
		$post_type = $_GET['post_type'];
		if ( isset( $post_type ) == true && 'wprtc_slideshow' === $post_type ) {
			 $defaults['wprtc_slideshow_shortcode'] = 'Shortcode';
		}
		return $defaults;
	}

	/**
	 * Add Column Content to wprtc_slideshow post_type.
	 *
	 * @param string $column_name Column Name.
	 *
	 * @param string $slider_id Contains slider id (post_id).
	 *
	 * @since 0.1
	 */
	public function wprtc_slideshow_cpt_table_columns_content( $column_name, $slider_id ) {
		if ( 'wprtc_slideshow_shortcode' === $column_name ) {
			echo '[wprtc_slideshow slider_id=' . $slider_id . ']';
		}
	}

	/**
	 * Setup Metaboxes for CPT 'wprtc_slideshow'
	 *
	 * @since 0.1
	 */
	public function wprtc_setup_slideshow_metaboxes() {
		add_meta_box( 'wprtc_slideshow_sliders',  __( 'Add Slides', 'wprtc_assignment_2a' ), array( $this, 'wprtc_render_slideshow_slides_metabox' ), 'wprtc_slideshow', 'normal', 'low' );
		add_meta_box( 'wprtc_slideshow_settings',  __( 'Add Slider Settings', 'wprtc_assignment_2a' ), array( $this, 'wprtc_render_slideshow_settings_metabox' ), 'wprtc_slideshow', 'side', 'low' );
	}

	/**
	 * Render Metaboxes for CPT 'wprtc_slideshow'
	 *
	 * @since 0.1
	 */
	public function wprtc_render_slideshow_slides_metabox() {
		global $post;
		wp_enqueue_media();
		wp_localize_script( 'wprtc_slideshow_main_2a_js', 'post',
			array(
				'ID' => $post->ID,
			)
		);
		$slider_images = get_post_meta( $post->ID, '_wprtc_slideshow_slides' );
		ob_start();
		echo "<div class='wprtc_slideshow_wrapper' id='wprtc_sortable'>";
		if ( ! empty( $slider_images ) ) {
			$slider_images = $slider_images[0];
			foreach ( $slider_images as $slide_order => $slide_atachment_id ) {
				echo "<div class='wprtc_image_preview_wrapper'>
			 					<img class='wprtc_image_preview' src='" . wp_get_attachment_url( $slide_atachment_id ) . "' height='150'>
			 					<input type='hidden' name='wprtc_slide_order[" . $slide_order . "]' value='" . $slide_atachment_id . "'>
			 				</div>";
			}
		}
		echo '</div>';
		echo "<div class='wprtc_button_wrapper'>
						<input id='wprtc_add_new_slide' type='button upload_image_button' class='button' value='" . __( 'Add New Slide', 'wprtc_assignment_2a' ) . "'/>
					</div>";
		ob_get_flush();
	}


	/**
	 * Render Settings Metabox for CPT 'wprtc_slideshow'
	 *
	 * @since 0.1
	 */
	public function wprtc_render_slideshow_settings_metabox() {
		global $post;
		$animation_type = '';
		$animation_speed = '';
		$slider_settings = get_post_meta( $post->ID, '_wprtc_slideshow_settings' );
		$animation = '';
		ob_start();
		echo "<div class='wprtc_slideshow_settings_wrapper'>";
		if ( ! empty( $slider_settings ) ) {
			$slider_settings = $slider_settings[0];
			$animation_type  = isset( $slider_settings['animation_type'] ) ? sanitize_text_field( $slider_settings['animation_type'] ) : '';
			$animation_speed  = isset( $slider_settings['animation_speed'] ) ? sanitize_text_field( $slider_settings['animation_speed'] ) : '';
		}
		echo "<div>
						<label for='_wprtc_slider_settings[animation_type]'>Animation Type</label>
						<br/>
						<input type='radio' name='_wprtc_slider_settings[animation_type]' value='_wprtc_animation_type_fade'" . checked( $animation_type, '_wprtc_animation_type_fade', false ) . "'>Fade
						<input type='radio' name='_wprtc_slider_settings[animation_type]' value='_wprtc_animation_type_slide'" . checked( $animation_type, '_wprtc_animation_type_slide', false ) . "'>Slide
					</div>
						<br/>
					<div>
						<label for='_wprtc_slider_settings[animation_speed]'>Animation speed</label>
						<br/>
						<input type='text' name='_wprtc_slider_settings[animation_speed]' value='" . esc_html( $animation_speed ) . "'>
					</div>";
		echo '</div>';
		ob_get_flush();
	}

	/**
	 * 'save_post' callback for saving Slides.
	 *
	 * @param int $post_id Post Id.
	 *
	 * @since 0.1
	 */
	public function wprtc_save_slides( $post_id ) {
		global $post;
		$wprtc_slides = array();
		$wprtc_slider_settings = array();
		// If doing auto save return.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check if valid post_type.
		if ( isset( $_POST['post_type'] ) ) {
			if ( 'wprtc_slideshow' !== sanitize_text_field( $_POST['post_type'] ) ) {
				return;
			}
		}

		$output = print_r( $_POST, true );
		file_put_contents( 'file.txt', $output );

		foreach ( $_POST as $post_key => $post_value ) {
			// $key is input hidden , $value is attachment id
			if ( strpos( $post_key, 'wprtc_slide_order' ) !== false ) {
				$wprtc_slides = $post_value;
				// Build Slides array to save in to post meta.
				array_walk( $wprtc_slides, function( &$wprtc_value, &$wprtc_key ) {
						$wprtc_slides[ $wprtc_key ] = $wprtc_value;
				});
				// Update Slides.
				update_post_meta( $post_id, '_wprtc_slideshow_slides', $wprtc_slides );

			} elseif ( strpos( $post_key, '_wprtc_slider_settings' ) !== false ) {

				$wprtc_slider_settings = $post_value;
				// Build settings array to save in to post meta.
				array_walk( $wprtc_slider_settings, function( &$wprtc_settings_value, &$wprtc_settings_key ) {
						$wprtc_slider_settings[ $wprtc_settings_key ] = $wprtc_settings_value;
				});
				// Update Slider Settings.
				update_post_meta( $post_id, '_wprtc_slideshow_settings', $wprtc_slider_settings );

			}
		}

	}

	/**
	 * Call back function for [wprtc_slideshow] shortcode
	 * Usage : [wprtc_slideshow slider_id=1].
	 *
	 * @param int $args shortcode args.
	 *
	 * @since 0.1
	 */
	public function wprtc_slideshow( $args ) {

		// Normalize attribute keys, lowercase.
		$args = array_change_key_case( (array) $args, CASE_LOWER );

		// Extract shorcode atts.
		shortcode_atts( array(), $args , 'wprtc_slideshow' );

		if ( ! isset( $args['slider_id'] ) ) {
			return __( 'Illegal shortcode parameters detected !', 'wprtc_assignment_2a' );
		}

		$slider_images = get_post_meta( $args['slider_id'], '_wprtc_slideshow_slides' );
		$slider_settings = get_post_meta( $args['slider_id'], '_wprtc_slideshow_settings' );
		$slider_settings = $slider_settings[0];
		var_dump($slider_settings);
		ob_start();

		echo '<div class="flexslider">';
		if ( ! empty( $slider_images ) ) {
			echo '<ul class="slides">';
			$slider_images = $slider_images[0];
			foreach ( $slider_images as $slide_order => $slide_atachment_id ) {
				echo "<li>
						 		<img class='' src='" . wp_get_attachment_url( $slide_atachment_id ) . "'/>
						 </li>";
			}
		} else {
			_e( 'Sliders not Found. Please add atleast one slider.', 'wprtc_assignment_2a' );
		}
		echo '</div>';
		?>
		<script type="text/javascript">
			jQuery(document).ready(function(){
				// Hook up the flexslider
				//var slider_fade =
				jQuery('.flexslider').flexslider({
					animation: <?php echo isset( $slider_settings['animation_type'] ) ? json_encode( sanitize_text_field( $slider_settings['animation_type'] ) ) : 'fade'; ?>,
					animationSpeed: <?php echo isset( $slider_settings['animation_speed'] ) ? json_encode( sanitize_text_field( $slider_settings['animation_speed'] ) ) : '600'; ?>,
					direction: "horizontal",
					slideshowSpeed: 7000,
				});
			});
		</script>
		<?php
		return ob_get_clean();
	}
}

new Wp_Rtcamp_Assignment_2a();
