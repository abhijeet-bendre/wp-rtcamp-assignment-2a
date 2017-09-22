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
		add_shortcode( 'wprtc_slideshow', array( $this, 'wprtc_slideshow' ) );
		add_action( 'admin_menu', array( $this, 'wprtc_hide_wprtc_slideshow_cpt_menu' ) );
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
				'register_meta_box_cb' => array( $this, 'wprtc_setup_slideshow_metaboxes' ),
				'hierarchical'        => false,
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'menu_position'       => 5,
				'menu_icon'            => 'dashicons-slides',
				'show_in_admin_bar'   => true,
				'can_export'          => true,
				'has_archive'         => true,
				'exclude_from_search' => true,
				'publicly_queryable'  => false, // Hide  Preview changes button and view post type link.
			);
			register_post_type( 'wprtc_slideshow' , $args );

			// Attach hook for adding Custom Column to wprtc_slideshow CPT.
			add_filter( 'manage_posts_columns', array( $this, 'wprtc_slideshow_cpt_table_columns_title' ) );
			add_action( 'manage_posts_custom_column', array( $this, 'wprtc_slideshow_cpt_table_columns_content' ), 10, 2 );

			// Ajax hook for handling "Add new Slide".
			add_action( 'wp_ajax_wprtc_get_single_slide_html', array( $this, 'wprtc_get_single_slide_html' ) );

			// 'save_post' callback for saving Slides.
			add_action( 'save_post', array( $this, 'wprtc_save_slides' ), 10 );
	}

	/**
	 * Init assets such as JS/CSS, required by plugin
	 *
	 * @since 0.1
	 */
	public function wprtc_init_assets() {
		global $pagenow;

		/*
		 * Register and Enqueue Style/Scripts only on 'wprtc_slideshow' post type.
		 *
		 * Check if $_GET['post_type'] exists. For "All sliders/ Add new" screen .
		 *	or
		 * Check if $_GET['post'] exists. (For Edit Slider Screen).
		 */
		$post_type = isset( $_GET['post_type'] ) ? sanitize_text_field( wp_unslash( $_GET['post_type'] ) ) : ''; // Input var okay. WPCS: CSRF ok.
		$post_id = isset( $_GET['post'] ) ? sanitize_text_field( wp_unslash( $_GET['post'] ) ) : ''; // Input var okay. WPCS: CSRF ok.

		if ( ( 'wprtc_slideshow' === $post_type && in_array( $pagenow, array( 'post-new.php', 'edit.php' ), true ) )
				||
				( 'post.php' === $pagenow && 'wprtc_slideshow' === get_post_type( $post_id ) )
			) {
			// Register and Enqueue Style.
			wp_register_style( 'wprtc_slideshow_main_2a_css', plugin_dir_url( __FILE__ ) . 'assets/css/wprtc_slideshow_main_2a.css', null );
			wp_enqueue_style( 'wprtc_slideshow_main_2a_css' );

			// Register and Enqueue Script.
			wp_register_script( 'wprtc_slideshow_main_2a_js', plugin_dir_url( __FILE__ ) . 'assets/js/wprtc_slideshow_main_2a.js' );
			wp_enqueue_script( 'wprtc_slideshow_main_2a_js', array( 'jquery' ) );
			wp_localize_script( 'wprtc_slideshow_main_2a_js', 'ajaxurl', admin_url( 'admin-ajax.php' ) );
			wp_localize_script( 'wprtc_slideshow_main_2a_js', 'wprtc_get_single_slide_html_nonce', wp_create_nonce( 'wprtc_get_single_slide_html_nonce' ) );
			if ( ! wp_script_is( 'jquery-ui', 'enqueued' ) ) {
				wp_enqueue_script( 'jquery-ui' );
			}
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

		// Register and Enqueue Style.
		wp_register_style( 'wprtc_slideshow_front_end_2a_css', plugin_dir_url( __FILE__ ) . 'assets/css/wprtc_slideshow_front_end_2a.css', null );
		wp_enqueue_style( 'wprtc_slideshow_front_end_2a_css' );

		// Register and Enqueue flexslider_script only if its not previously enqueued.
		if ( ! wp_script_is( 'flexslider_script', 'enqueued' ) ) {
			wp_register_script( 'flexslider_script', plugin_dir_url( __FILE__ ) . 'assets/js/lib/jquery.flexslider-min.js' );
			wp_enqueue_script( 'flexslider_script', array( 'jquery' ) );
		}
	}

	/**
	 * Hide 'wprtc_slideshow' CPT menu for users who don't have 'upload_files' capability.
	 *
	 * @since 0.1
	 */
	public function wprtc_hide_wprtc_slideshow_cpt_menu() {
		if ( ! current_user_can( 'upload_files' ) ) {
			remove_menu_page( 'edit.php?post_type=wprtc_slideshow' );
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
		$post_type = isset( $_GET['post_type'] ) ? sanitize_text_field( wp_unslash( $_GET['post_type'] ) ) : ''; // Input var okay. WPCS: CSRF ok.
		if ( 'wprtc_slideshow' === $post_type ) {
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
			echo '[wprtc_slideshow slider_id=' . esc_attr( $slider_id ) . ']';
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
		add_meta_box( 'wprtc_slideshow_shortcode',  __( 'Copy Shortcode', 'wprtc_assignment_2a' ), array( $this, 'wprtc_render_slideshow_shortcode_metabox' ), 'wprtc_slideshow', 'side', 'low' );
	}

	/**
	 * Render Metabox for CPT 'wprtc_slideshow'
	 *
	 * @since 0.1
	 */
	public function wprtc_render_slideshow_slides_metabox() {
		global $post;
		$slide_show_nonce = wp_create_nonce( '_wprtc_slideshow_slides_nonce' );

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
				// Get single slide html.
				$this->wprtc_get_single_slide_html( $slide_order, $slide_atachment_id );
			}
		}
		echo '<input type="hidden" name="_wprtc_slideshow_slides_nonce" value="' . esc_attr( $slide_show_nonce ) . '"/>';
		echo '</div>';
		echo "<div class='wprtc_button_wrapper'>
						<input id='wprtc_add_new_slide' type='button upload_image_button' class='button' value='" . esc_html__( 'Add New Slide', 'wprtc_assignment_2a' ) . "'/>
					</div>";

		ob_get_flush();
	}

	/**
	 * Get single slide html
	 *
	 *  @param string $slide_order Current order of slide.
	 *
	 *  @param string $slide_atachment_id Attachment Id.
	 * @since 0.1
	 */
	public function wprtc_get_single_slide_html( $slide_order, $slide_atachment_id ) {
		ob_start();
		$is_ajax_call = false;
		if ( isset( $_POST['wprtc_attachment_id'], $_POST['wprtc_slide_order'], $_POST['wprtc_ajax_nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['wprtc_ajax_nonce'] ), 'wprtc_get_single_slide_html_nonce' ) ) { // Input var okay.sanitization okay.) ) { // Input var okay.sanitization okay.
			$slide_atachment_id = sanitize_text_field( wp_unslash( $_POST['wprtc_attachment_id'] ) ); // Input var okay; sanitization okay.
			$slide_order = sanitize_text_field( wp_unslash( $_POST['wprtc_slide_order'] ) ); // Input var okay; sanitization okay.
			$is_ajax_call = true;
		}
		echo "<div class='wprtc_image_preview_wrapper'>
						<div class='wprtc_image_preview'>
		 						<img  src='" . esc_url( wp_get_attachment_url( $slide_atachment_id ) ) . "' />
							</div>
							<div class='wprtc_image_caption'>
								<label>Add Image Caption</label>
								<input type='text' name='' value='' placeholder='Add a Image Caption' size='40'>
								<div class='wprtc_slide_actions'>
									<a href='#' class='wprtc_edit_slide_button' data-slide-order='" . esc_attr( $slide_order ) . "'>" . esc_html__( 'Edit Slide', 'wprtc_assignment_2a' ) . "</a>
									<a href='#' class='wprtc_delete_slide_button' data-slide-order='" . esc_attr( $slide_order ) . "'>" . esc_html__( 'Delete Slide', 'wprtc_assignment_2a' ) . "</a>
								</div>
							</div>
							<input type='hidden' name='_wprtc_slide_order[" . esc_attr( $slide_order ) . "]' value='" . esc_attr( $slide_atachment_id ) . "' / >
		 				</div>";
		ob_get_flush();
		if ( $is_ajax_call ) {
			wp_die();
		}
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
		}
		$animation_type  = isset( $slider_settings['animation_type'] ) ? sanitize_text_field( $slider_settings['animation_type'] ) : '';
		$animation_speed  = isset( $slider_settings['animation_speed'] ) ? sanitize_text_field( $slider_settings['animation_speed'] ) : '';
		$animation_loop  = isset( $slider_settings['animation_loop'] ) ? sanitize_text_field( $slider_settings['animation_loop'] ) : '';
		$randomize  = isset( $slider_settings['randomize'] ) ? sanitize_text_field( $slider_settings['randomize'] ) : '';
		$slideshow_speed  = isset( $slider_settings['slideshow_speed'] ) ? sanitize_text_field( $slider_settings['slideshow_speed'] ) : '';

		echo "<div class='wprtc_slideshow_setting'>
						<label for='_wprtc_slider_settings[animation_type]'>" . esc_html__( 'Animation Type', 'wprtc_assignment_2a' ) . ":</label>
						<input type='radio' name='_wprtc_slider_settings[animation_type]' value='fade' " . checked( $animation_type, 'fade', false ) . "'>Fade
						<input type='radio' name='_wprtc_slider_settings[animation_type]' value='slide' " . checked( $animation_type, 'slide', false ) . "'>Slide
					</div>
					<div class='wprtc_slideshow_setting'>
						<label for='_wprtc_slider_settings[animation_speed]'>" . esc_html__( 'Animation Speed', 'wprtc_assignment_2a' ) . ":</label>
						<input type='text' name='_wprtc_slider_settings[animation_speed]' value='" . esc_html( $animation_speed ) . "'>
					</div>
					<div class='wprtc_slideshow_setting'>
						<label for='_wprtc_slider_settings[animation_loop]'>" . esc_html__( 'Animation Loop', 'wprtc_assignment_2a' ) . ":</label>
						<input type='checkbox' name='_wprtc_slider_settings[animation_loop]' value='true' " . checked( $animation_loop, 'true', false ) . ">
					</div>
					<div class='wprtc_slideshow_setting'>
						<label for='_wprtc_slider_settings[randomize]'>" . esc_html__( 'Randomize slide order', 'wprtc_assignment_2a' ) . ":</label>
						<input type='checkbox' name='_wprtc_slider_settings[randomize]' value='true' " . checked( $randomize, 'true', false ) . ">
					</div>
					<div class='wprtc_slideshow_setting'>
						<label for='_wprtc_slider_settings[slideshow_speed]'>" . esc_html__( 'SlideShow Speed', 'wprtc_assignment_2a' ) . ":</label>
						<input type='text' name='_wprtc_slider_settings[slideshow_speed]' value='" . esc_html( $slideshow_speed ) . "'>
					</div>
		</div>";
		ob_get_flush();
	}

	/**
	 * Render Metabox for displaying shortcode to copy on post/page
	 *
	 * @since 0.1
	 */
	public function wprtc_render_slideshow_shortcode_metabox() {
		global $post;
		ob_start();
		echo '<div>
							[wprtc_slideshow slider_id="' . esc_attr( $post->ID ) . '"]
					</div>';
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

		if ( ! isset( $_POST['_wprtc_slideshow_slides_nonce'] ) && ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wprtc_slideshow_slides_nonce'], '_wprtc_slideshow_slides_nonce' ) ) ) ) { // Input var okay.
			return;
		}

		// Check if valid post_type.
		if ( isset( $_POST['post_type'] ) ) { // Input var okay.
			if ( 'wprtc_slideshow' !== sanitize_text_field( wp_unslash( $_POST['post_type'] ) ) ) { // Input var okay.
				return;
			}
		}

		foreach ( $_POST as $post_key => $post_value ) { // Input var okay.
			// $key is input hidden , $value is attachment id.
			if ( strpos( $post_key, '_wprtc_slide_order' ) !== false ) {
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
		shortcode_atts(
			array(
				'slider_id' => 0,
			),
			$args , 'wprtc_slideshow'
		);

		if ( ! isset( $args['slider_id'] ) || 0 === $args['slider_id'] ) {
			return '<div class="wprtc_general_error">' .
								esc_html__( 'Illegal shortcode parameters detected. !', 'wprtc_assignment_2a' ) .
							'</div>';
		}

		$slider_images = get_post_meta( (int) $args['slider_id'], '_wprtc_slideshow_slides' );
		$slider_settings = get_post_meta( (int) $args['slider_id'], '_wprtc_slideshow_settings' );
		$post_status = get_post_status( $args['slider_id'] );

		ob_start();
		echo '<div class="flexslider">';
		if ( ! empty( $slider_images ) && 'publish' === $post_status ) {
			$slider_settings = isset( $slider_settings[0] ) ? $slider_settings[0] : '';
			$slider_images = $slider_images[0];

			echo '<ul class="slides">';
			foreach ( $slider_images as $slide_order => $slide_atachment_id ) {
				echo "<li>
						 		<img class='' src='" . esc_url( wp_get_attachment_url( $slide_atachment_id ) ) . "'/>
						 </li>";
			}
			echo '</ul>';
		} else {
			echo '<div class="wprtc_general_error">' .
				esc_html__( 'Slider not Found. !', 'wprtc_assignment_2a' ) .
			'</div>';
		}
		echo '</div>';
		?>
		<script type="text/javascript">
			jQuery(document).ready(function(){
				// Hook up the flexslider
				jQuery('.flexslider').flexslider({
					animation: <?php echo isset( $slider_settings['animation_type'] ) ? wp_json_encode( sanitize_text_field( $slider_settings['animation_type'] ) ) : wp_json_encode( 'fade' ); ?>,
					animationSpeed: <?php echo isset( $slider_settings['animation_speed'] ) ? wp_json_encode( sanitize_text_field( $slider_settings['animation_speed'] ), JSON_NUMERIC_CHECK ) : 600; ?>,
					animationLoop: <?php echo isset( $slider_settings['animation_loop'] ) ? wp_json_encode( sanitize_text_field( $slider_settings['animation_loop'] ) ) : 'false'; ?>,
					randomize: <?php echo isset( $slider_settings['randomize'] ) ? wp_json_encode( sanitize_text_field( $slider_settings['randomize'] ) ) : 'false'; ?>,
					slideshowSpeed: <?php echo isset( $slider_settings['slideshow_speed'] ) ? wp_json_encode( sanitize_text_field( $slider_settings['slideshow_speed'] ), JSON_NUMERIC_CHECK ) : 7000; ?>,
					direction: "horizontal",
				});
			});
		</script>
		<?php
		return ob_get_clean();
	}
}

new Wp_Rtcamp_Assignment_2a();
