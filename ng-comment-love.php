<?php
/**
 * Plugin Name: NG Comment Love
 * Plugin URI: https://www.nosegraze.com
 * Description: Fetches the commenter's most recent post to display below their comment
 * Version: 1.2
 * Author: Nose Graze
 * Author URI: https://www.nosegraze.com
 * License: GPL2
 *
 * @package   ng-comment-love
 * @copyright Copyright (c) 2015, Nose Graze Ltd.
 * @license   GPL2+
 */

/**
 * Define constants.
 */
if ( ! defined( 'NGLOVE_VERSION' ) ) {
	define( 'NGLOVE_VERSION', '1.2' );
}

if ( ! defined( 'NGLOVE_DIR' ) ) {
	define( 'NGLOVE_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'NGLOVE_URL' ) ) {
	define( 'NGLOVE_URL', plugin_dir_url( __FILE__ ) );
}

/**
 * Include settings panel.
 */
global $ng_comment_love_options;
require_once NGLOVE_DIR . 'includes/admin/settings/register-settings.php';
if ( empty( $ng_comment_love_options ) ) {
	$ng_comment_love_options = ng_comment_love_get_settings();
}

/**
 * Load assets.
 */
require_once NGLOVE_DIR . 'includes/load-assets.php';

/**
 * Include admin files.
 */
if ( is_admin() ) {
	require_once NGLOVE_DIR . 'includes/admin/admin-pages.php';
	require_once NGLOVE_DIR . 'includes/admin/meta-box.php';
	require_once NGLOVE_DIR . 'includes/admin/settings/display-settings.php';
}

/**
 * Modify the URL form field to include notice and link.
 *
 * @param string $field HTML for the URL field.
 *
 * @since 1.2
 * @return string Modified form field.
 */
function ng_comment_love_url_field( $field ) {

	if ( is_user_logged_in() && ng_comment_love_get_option( 'show_for_logged_in', 'yes' ) == 'no' ) {
		return $field;
	}

	ob_start();
	?>
	<div id="commentlove">
		<div id="comment-love-messages"></div>
		<div id="comment-love-latest-posts"></div>
		<input type="hidden" name="cl_post_url" id="cl_post_url">
	</div>
	<?php
	$comment_love = ob_get_clean();
	$message      = '<span id="comment-love-message"> ' . ng_comment_love_get_option( 'text_comment_form', __( '(Enter your URL then <a href="#" id="comment-love-get-posts">click here</a> to include a link to one of your blog posts.)', 'ng-comment-love' ) ) . '</span>';

	if ( false !== strpos( $field, '</p>' ) ) {
		$field = str_replace( '</p>', $message . '</p>', $field );
	} else {
		$field = $field . $message;
	}

	return $field . $comment_love;

}

add_filter( 'comment_form_field_url', 'ng_comment_love_url_field' );

/**
 * Get latest blog posts from a URL - Ajax CB
 *
 * @since 1.0.0
 * @return void
 */
function ng_comment_love_get_blog_post() {
	// Security check.
	check_ajax_referer( 'ng_comment_love_ajax_nonce', 'nonce' );

	require_once NGLOVE_DIR . 'includes/class-ng-comment-love.php';

	$commentlove = new NG_Comment_Love();
	$commentlove->set_url( strip_tags( $_POST['url'] ) );
	$commentlove->set_number_posts( ng_comment_love_get_option( 'numb_blog_posts', 10 ) );
	$posts = $commentlove->get_posts();

	// If it's not an array, we have a problem.
	if ( ! is_array( $posts ) || empty( $posts ) ) {
		wp_send_json_error( esc_html( ng_comment_love_get_option( 'text_error_no_posts', __( 'No posts found', 'ng-comment-love' ) ) ) );
	}

	wp_send_json_success( $posts );

	exit;
}

add_action( 'wp_ajax_ng_get_latest_blog_post', 'ng_comment_love_get_blog_post' );
add_action( 'wp_ajax_nopriv_ng_get_latest_blog_post', 'ng_comment_love_get_blog_post' );

/**
 * Add Comment Love meta to the newly inserted comment.
 *
 * @param int $id Comment ID number
 *
 * @since 1.0.0
 * @return void
 */
function ng_insert_comment_love( $id ) {
	// If our fields aren't set - bail.
	if ( ! isset( $_POST['cl_post_title'] ) || empty( $_POST['cl_post_title'] ) || ! isset( $_POST['cl_post_url'] ) || empty( $_POST['cl_post_url'] ) ) {
		return;
	}

	$title = $_POST['cl_post_title'];
	$link  = $_POST['cl_post_url'];

	$data = array(
		'cl_post_title' => wp_strip_all_tags( $title ),
		'cl_post_url'   => esc_url_raw( strip_tags( $link ) )
	);

	add_comment_meta( $id, 'cl_data', $data, true );
}

add_action( 'comment_post', 'ng_insert_comment_love' );

/**
 * Display Comment Love
 *
 * Adds the latest post name and URL after the comment text.
 *
 * @param string $comment_text The comment text.
 *
 * @since 1.0.0
 * @return string
 */
function ng_comment_love_display( $comment_text ) {
	if ( is_admin() ) {
		return $comment_text;
	}

	$comment_id = get_comment_ID();

	if ( empty( $comment_id ) || ! is_numeric( $comment_id ) ) {
		return $comment_text;
	}

	$comment   = get_comment( $comment_id );
	$love_data = get_comment_meta( get_comment_ID(), 'cl_data', true );

	if ( ! $love_data || ! is_array( $love_data ) ) {
		return $comment_text;
	}

	$love_string = '<div class="ng-comment-love">';
	$love_string .= sprintf(
		__( '%1$s recently posted: %2$s', 'ng-comment-love' ),
		$comment->comment_author,
		'<a href="' . esc_url( $love_data['cl_post_url'] ) . '" target="_blank" rel="' . esc_attr( ng_comment_love_get_option( 'link_type', 'nofollow' ) ) . '">' . esc_html( $love_data['cl_post_title'] ) . '</a>'
	);
	$love_string .= '</div>';

	return $comment_text . apply_filters( 'ng-comment-love/love-link', $love_string, $comment_id );
}

add_filter( 'comment_text', 'ng_comment_love_display', 100 );

/**
 * Ajax CB: Remove Comment Love
 *
 * @since 1.1.0
 * @return void
 */
function ng_remove_love() {
	// Security check.
	check_ajax_referer( 'ng_delete_comment_love', 'nonce' );

	$comment_id = absint( $_POST['comment_id'] );

	if ( ! current_user_can( 'edit_comment', $comment_id ) ) {
		wp_send_json_error( __( 'Error: You do not have permission to edit this comment.', 'ng-comment-love' ) );
	}

	$result = delete_comment_meta( $comment_id, 'cl_data' );

	if ( $result ) {
		wp_send_json_success( '<p>' . __( 'The comment love has been removed successfully.', 'ng-comment-love' ) . '</p>' );
	}

	wp_send_json_error( __( 'An unexpected error occurred. Failed to remove comment love.', 'ng-comment-love' ) );
}

add_action( 'wp_ajax_ng_remove_love', 'ng_remove_love' );