<?php
/**
 * Deprecated Functions
 *
 * @package   ng-commentlove
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   GPL2+
 * @since     2.0
 */

use Ashleyfae\CommentLove;

/**
 * Adds JavaScript to the front-end.
 *
 * @since 1.0.0
 * @deprecated 2.0
 * @return void
 */
function ng_comment_love_scripts(): void
{
    _deprecated_function(__FUNCTION__, '2.0');

    ngCommentLove()->loadAssets();
}

/**
 * Load Underscore.js Templates
 *
 * @since 1.0.0
 * @deprecated 2.0
 * @return void
 */
function ng_comment_love_underscores_templates(): void
{
    _deprecated_function(__FUNCTION__, '2.0');
}

/**
 * Modify the URL form field to include notice and link.
 *
 * @param  string  $field  HTML for the URL field.
 *
 * @since 1.2
 * @deprecated 2.0
 * @return string Modified form field.
 */
function ng_comment_love_url_field($field)
{
    return (new CommentLove\Helpers\CommentFields())->urlField($field);
}

/**
 * Modify the logged in user message to include notice.
 *
 * @param  string  $message  Message shown to logged in user.
 * @param  array  $commenter  An array containing the comment author's username, email, and URL.
 * @param  string  $identity  If the commenter is a registered user, the display name, blank otherwise.
 *
 * @since 1.2.2
 * @deprecated 2.0
 * @return string
 */
function ng_comment_love_logged_in_field($message, $commenter, $identity)
{
    return (new CommentLove\Helpers\CommentFields())->loggedInUserMessage($message, $commenter, $identity);
}

/**
 * Add Meta Box
 *
 * @since 1.1.0
 * @deprecated 2.0
 * @return void
 */
function ng_comment_love_add_meta_box()
{
    _deprecated_function(__FUNCTION__, '2.0', CommentLove\Admin\CommentMetabox::class.':register');

    (new CommentLove\Admin\CommentMetabox())->register();
}

/**
 * Render Meta Box
 *
 * @todo  Make button work with ajax to remove love
 *
 * @param  WP_Comment  $comment
 *
 * @since 1.1.0
 * @deprecated 2.0
 * @return void
 */
function ng_comment_love_render_meta_box($comment)
{
    _deprecated_function(__FUNCTION__, '2.0', CommentLove\Admin\CommentMetabox::class.':render');

    (new CommentLove\Admin\CommentMetabox())->render($comment);
}

/**
 * Get latest blog posts from a URL - Ajax CB
 *
 * @deprecated 2.0
 *
 * @since 1.0.0
 * @return void
 */
function ng_comment_love_get_blog_post()
{
    _deprecated_function(__FUNCTION__, '2.0', CommentLove\Api\v1\ListBlogPosts::class);

    // Security check.
    check_ajax_referer('ng_comment_love_ajax_nonce', 'nonce');

    $postFetcher = new \Ashleyfae\CommentLove\Helpers\PostFetcher();

    try {
        $posts = $postFetcher->forUrl(strip_tags($_POST['url']))
            ->getPosts();

        wp_send_json_success($posts);
        exit;
    } catch (\Exception $e) {
        wp_send_json_error(wp_strip_all_tags(
            ng_comment_love_get_option('text_error_no_posts', __('No posts found', 'ng-comment-love'))
        ));
    }
}

add_action('wp_ajax_ng_get_latest_blog_post', 'ng_comment_love_get_blog_post');
add_action('wp_ajax_nopriv_ng_get_latest_blog_post', 'ng_comment_love_get_blog_post');

/**
 * Add Comment Love meta to the newly inserted comment.
 *
 * @param  int  $id  Comment ID number
 *
 * @since 1.0.0
 * @deprecated 2.0
 * @return void
 */
function ng_insert_comment_love($id)
{
    (new CommentLove\Helpers\LoveAdder())->insertAfterComment($id);
}

/**
 * Display Comment Love
 *
 * Adds the latest post name and URL after the comment text.
 *
 * @deprecated 2.0
 *
 * @param  string  $comment_text  The comment text.
 *
 * @since 1.0.0
 * @return string
 */
function ng_comment_love_display($comment_text)
{
    return (new CommentLove\Helpers\LoveAdder())->displayLoveWithComment($comment_text);
}

/**
 * Ajax CB: Remove Comment Love
 *
 * @since 1.1.0
 * @deprecated 2.0
 * @return void
 */
function ng_remove_love()
{
    _deprecated_function(__FUNCTION__, '2.0', CommentLove\Api\v1\RemoveLove::class);

    // Security check.
    check_ajax_referer('ng_delete_comment_love', 'nonce');

    $comment_id = absint($_POST['comment_id']);

    if (! current_user_can('edit_comment', $comment_id)) {
        wp_send_json_error(__('Error: You do not have permission to edit this comment.', 'ng-comment-love'));
    }

    $result = delete_comment_meta($comment_id, 'cl_data');

    if ($result) {
        wp_send_json_success('<p>'.__('The comment love has been removed successfully.', 'ng-comment-love').'</p>');
    }

    wp_send_json_error(__('An unexpected error occurred. Failed to remove comment love.', 'ng-comment-love'));
}

add_action('wp_ajax_ng_remove_love', 'ng_remove_love');

/**
 * Load Admin Scripts
 *
 * @param  string  $hook
 *
 * @since 1.1.0
 * @deprecated 2.0
 * @return void
 */
function ng_admin_scripts($hook)
{
    _deprecated_function(__FUNCTION__, '2.0', \Ashleyfae\CommentLove\Helpers\AssetLoader::class);

    (new \Ashleyfae\CommentLove\Helpers\AssetLoader())->loadAdmin($hook);
}

/**
 * Creates admin submenu pages under 'Settings'.
 *
 * @since 1.1.0
 * @deprecated 2.0
 * @return void
 */
function ng_comment_love_add_options_link()
{
    _deprecated_function(__FUNCTION__, '2.0', \Ashleyfae\CommentLove\Admin\SettingsPage::class);

    (new \Ashleyfae\CommentLove\Admin\SettingsPage())->register();
}
