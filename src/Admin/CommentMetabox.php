<?php
/**
 * CommentMetabox.php
 *
 * @package   ng-commentlove
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   GPL2+
 * @since     2.0
 */

namespace Ashleyfae\CommentLove\Admin;

use Ashleyfae\CommentLove\ValueObjects\CommentLove;

class CommentMetabox
{

    /**
     * Registers the metabox.
     *
     * @since 2.0
     *
     * @return void
     */
    public function register(): void
    {
        add_meta_box('ng_commentlove', __('Comment Love', 'ng-comment-love'), [$this, 'render'], 'comment', 'normal');
    }

    /**
     * Renders the metabox content.
     *
     * @todo Make button work with ajax to remove love.
     *
     * @since 2.0
     *
     * @param  \WP_Comment  $comment
     *
     * @return void
     */
    public function render(\WP_Comment $comment): void
    {
        $loveData = get_comment_meta($comment->comment_ID, 'cl_data', true);

        if (is_array($loveData)) {
            $loveData = CommentLove::fromArray($loveData);
            ?>
            <p id="ng-comment-love-link">
                <a href="<?php echo esc_url($loveData->postUrl); ?>">
                    <?php echo esc_html($loveData->postTitle); ?>
                </a>
            </p>
            <div id="ng-comment-love-wrap">
                <button
                    type="button"
                    id="comment-love-remove-love"
                    class="button button-secondary"
                    data-comment-id="<?php echo esc_attr($comment->comment_ID); ?>"
                >
                    <?php esc_html_e('Remove Love', 'ng-comment-love'); ?>
                </button>

                <div id="ng-comment-love-errors"></div>
            </div>
            <?php
        } else {
            echo '<p>'.esc_html__('None', 'ng-comment-love').'</p>';
        }
    }

}
