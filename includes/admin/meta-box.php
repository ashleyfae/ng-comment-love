<?php
/**
 * Comment Meta Box
 *
 * Displays the comment love on the "Edit Comment" page.
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
 * Add Meta Box
 *
 * @since 1.1.0
 * @return void
 */
function ng_comment_love_add_meta_box() {
	add_meta_box( 'ng_commentlove', __( 'Comment Love', 'ng-comment-love' ), 'ng_comment_love_render_meta_box', 'comment', 'normal' );
}

add_action( 'add_meta_boxes', 'ng_comment_love_add_meta_box' );

/**
 * Render Meta Box
 *
 * @todo  Make button work with ajax to remove love
 *
 * @param WP_Comment $comment
 *
 * @since 1.1.0
 * @return void
 */
function ng_comment_love_render_meta_box( $comment ) {
	$love_data = get_comment_meta( $comment->comment_ID, 'cl_data', true );

	if ( ! is_array( $love_data ) ) {
		echo '<p>' . __( 'None', 'ng-comment-love' ) . '</p>';

		return;
	}

	$post_title = array_key_exists( 'cl_post_title', $love_data ) ? $love_data['cl_post_title'] : '';
	$post_url   = array_key_exists( 'cl_post_url', $love_data ) ? $love_data['cl_post_url'] : '';
	?>
	<p id="ng-comment-love-link">
		<a href="<?php echo esc_url( $post_url ); ?>" target="_blank"><?php echo esc_html( $post_title ); ?></a>
	</p>
	<p>
		<button type="button" id="comment-love-remove-love" data-comment-id="<?php echo esc_attr( $comment->comment_ID ); ?>" class="button button-secondary"><?php _e( 'Remove Love', 'ng-comment-love' ); ?></button>
	</p>
	<?php

}