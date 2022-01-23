<?php
/**
 * CommentFields.php
 *
 * @package   ng-commentlove
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   GPL2+
 * @since     2.0
 */

namespace Ashleyfae\CommentLove\Helpers;

class CommentFields
{

    /**
     * Modifies the URL field to:
     *      1. Include Comment Love markup.
     *      2. Add help text to let the user know about the feature.
     *
     * This is only used for guests (not logged in).
     *
     * @since 2.0
     *
     * @param $field
     *
     * @return string
     */
    public function urlField($field): string
    {
        if (! $this->shouldAddUrlFields()) {
            return $field;
        }

        $message = '<span id="comment-love-message"> '.$this->getHelpText().'</span>';

        if (false !== strpos($field, '</p>')) {
            $field = str_replace('</p>', $message.'</p>', $field);
        } else {
            $field = $field.$message;
        }

        return $field.$this->getCommentLoveMarkup();
    }

    /**
     * Modifies the logged-in user message to include a notice about Comment Love.
     *
     * @since 2.0
     *
     * @param  string  $message  Message shown to the logged-in user.
     * @param  array  $commenter  An array containing the comment author's username, email, and URL.
     * @param  string  $identity  If the commenter is a registered user, the display name, blank otherwise.
     *
     * @return string
     */
    public function loggedInUserMessage($message, $commenter, $identity): string
    {
        if (ng_comment_love_get_option('show_for_logged_in', 'yes') === 'no') {
            return $message;
        }

        return $this->getHelpText().$this->getCommentLoveMarkup();
    }

    /**
     * Whether we should support Comment Love fields.
     *
     * @since 2.0
     *
     * @return bool
     */
    protected function shouldAddUrlFields(): bool
    {
        return ! is_user_logged_in() || ng_comment_love_get_option('show_for_logged_in', 'yes') !== 'no';
    }

    /**
     * Returns Comment Love markup that's essential to making the JavaScript work.
     *
     * @since 2.0
     *
     * @return string
     */
    protected function getCommentLoveMarkup(): string
    {
        ob_start();
        ?>
        <div id="commentlove">
            <div id="comment-love-messages"></div>
            <div id="comment-love-latest-posts"></div>
            <input type="hidden" name="cl_post_url" id="cl_post_url">
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Returns the URL field help texst.
     *
     * @return string
     */
    protected function getHelpText(): string
    {
        return ng_comment_love_get_option(
            'text_comment_form',
            sprintf(
                __(
                /* Translators: %1$s opening anchor tag; %2$s closing anchor tag */
                    '(Enter your URL then %1$sclick here%2$s to include a link to one of your blog posts.)',
                    'ng-comment-love'
                ),
                '<a href="#" id="comment-love-get-posts">',
                '</a>'
            )
        );
    }

}
