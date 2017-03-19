<?php
/**
 * Load Assets on the Front-End
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
 * Adds JavaScript to the front-end.
 *
 * @since 1.0.0
 * @return void
 */
function ng_comment_love_scripts() {
	if ( ! is_singular() ) {
		return;
	}

	// Use minified libraries if SCRIPT_DEBUG is turned off
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	/*
	 * CSS
	 */
	wp_enqueue_style( 'ng-comment-love', NGLOVE_URL . 'css/ng-commentlove' . $suffix . '.css', array(), NGLOVE_VERSION );

	/*
	 * JavaScript
	 */

	$deps = array(
		'jquery',
		'wp-util'
	);

	$current_user = wp_get_current_user();

	wp_enqueue_script( 'ng-comment-love', NGLOVE_URL . 'js/commentlove' . $suffix . '.js', $deps, NGLOVE_VERSION, true );
	$data = array(
		'ajaxurl'        => admin_url( 'admin-ajax.php' ),
		'nonce'          => wp_create_nonce( 'ng_comment_love_ajax_nonce' ),
		'website_url'    => $current_user ? $current_user->user_url : '',
		'message_no_url' => ng_comment_love_get_option( 'text_error_website_url', __( 'Please enter your website URL first!', 'ng-comment-love' ) )
	);
	wp_localize_script( 'ng-comment-love', 'NGLOVE', $data );
}

add_action( 'wp_enqueue_scripts', 'ng_comment_love_scripts' );

/**
 * Load Underscore.js Templates
 *
 * @todo  template function
 *
 * @since 1.0.0
 * @return void
 */
function ng_comment_love_underscores_templates() {
	if ( ! is_singular() ) {
		return;
	}

	include NGLOVE_DIR . '/templates/posts-list.php';
}

add_action( 'wp_footer', 'ng_comment_love_underscores_templates' );