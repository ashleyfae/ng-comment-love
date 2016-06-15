<?php
/**
 * Functions for the admin pages.
 *
 * @package   ng-commentlove
 * @copyright Copyright (c) 2016, Nose Graze Ltd.
 * @license   GPL2+
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Creates admin submenu pages under 'Settings'.
 *
 * @since 1.1.0
 * @return void
 */
function ng_comment_love_add_options_link() {
	add_options_page( __( 'NG Comment Love Settings', 'ng-comment-love' ), __( 'Comment Love', 'ng-comment-love' ), 'manage_options', 'ng-comment-love-settings', 'ng_comment_love_options_page' );
}

add_action( 'admin_menu', 'ng_comment_love_add_options_link', 10 );

/**
 * Load Admin Scripts
 *
 * @param string $hook
 *
 * @since 1.1.0
 * @return void
 */
function ng_admin_scripts( $hook ) {
	if ( $hook != 'comment.php' ) {
		return;
	}

	// Use minified libraries if SCRIPT_DEBUG is turned off
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	wp_enqueue_script( 'ng-comment-love-admin', NGLOVE_URL . '/js/admin-scripts' . $suffix . '.js', array( 'jquery' ), NGLOVE_VERSION );
	$data = array(
		'nonce' => wp_create_nonce( 'ng_delete_comment_love' )
	);
	wp_localize_script( 'ng-comment-love-admin', 'NGLOVE', $data );
}

add_action( 'admin_enqueue_scripts', 'ng_admin_scripts' );