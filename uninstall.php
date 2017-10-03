<?php
/**
 * WordPress-Slideshow Plugin Uninstalling
 *
 * Uninstalling deletes 'wprtc_slideshow' CPT and its post meta.
 *
 * @package rtCamp
 * @version 0.1
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

/**
 * If WPRTC_DELETE_ALL_DATA constant is set to true in wp-config.php then only delete the associated data.
 */
public function wprtc_delete_plugin_cpt_and_meta() {
	global $wpdb;

	if ( defined( 'WPRTC_DELETE_ALL_DATA' ) && true === WPRTC_DELETE_ALL_DATA ) {
		$posts = get_posts(
			array(
				'numberposts' => -1,
				'post_type' => 'wprtc_slideshow',
				'post_status' => 'any',
			)
		);

		foreach ( $posts as $post ) {
			wp_delete_post( $post->ID, true );
		}
	}

}
define( 'WPRTC_DELETE_ALL_DATA', true );
wprtc_delete_plugin_cpt_and_meta();
