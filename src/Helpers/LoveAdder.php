<?php
/**
 * LoveAdder.php
 *
 * @package   ng-commentlove
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   GPL2+
 */

namespace Ashleyfae\CommentLove\Helpers;

use Ashleyfae\CommentLove\ValueObjects\CommentLove;

class LoveAdder
{

    public function insertAfterComment($commentId): void
    {
        // If there's no Comment Love data - bail.
        if (empty($_POST['cl_post_title']) || empty($_POST['cl_post_url'])) {
            return;
        }

        $this->addForComment((int) $commentId, $_POST['cl_post_title'], $_POST['cl_post_url']);
    }

    public function addForComment(int $commentId, string $title, string $url): void
    {
        update_comment_meta(
            $commentId,
            'cl_data',
            [
                'cl_post_title' => sanitize_text_field(wp_strip_all_tags($title)),
                'cl_post_url'   => esc_url_raw(wp_strip_all_tags($url)),
            ]
        );
    }

    /**
     * Displays comment love after the comment text.
     *
     * @since 2.0
     *
     * @param  string  $commentText
     *
     * @return string
     */
    public function displayLoveWithComment(string $commentText): string
    {
        if (is_admin()) {
            return $commentText;
        }

        $comment = get_comment();
        if (! $comment instanceof \WP_Comment) {
            return $commentText;
        }

        $loveData = get_comment_meta($comment->comment_ID, 'cl_data', true);
        if (empty($loveData) || ! is_array($loveData)) {
            return $commentText;
        }

        $loveData = CommentLove::fromArray($loveData);
        $linkRel  = ng_comment_love_get_option('link_type', 'nofollow');
        ob_start();
        ?>
        <div class="ng-comment-love">
            <?php
            printf(
            /* Translators: %1$s name of the commenter; %2$s link to a post */
                esc_html__('%1$s recently posted: %2$s', 'ng-comment-love'),
                esc_html($comment->comment_author),
                '<a href="'.esc_url($loveData->postUrl).'" target="_blank" rel="'.esc_attr($linkRel).'">'.esc_html($loveData->postTitle).'</a>'
            )
            ?>
        </div>
        <?php
        /**
         * Filters the love string.
         *
         * @param  string  $loveString
         * @param  int  $commentId
         */
        $loveString = apply_filters('ng-comment-love/love-link', ob_get_clean(), $comment->comment_ID);

        return $commentText.$loveString;
    }

}
