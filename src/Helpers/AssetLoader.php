<?php
/**
 * AssetLoader.php
 *
 * @package   ng-commentlove
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   GPL2+
 * @since     2.0
 */

namespace Ashleyfae\CommentLove\Helpers;

use Ashleyfae\CommentLove\Api\RouteRegistration;

class AssetLoader
{

    /**
     * Loads front-end assets.
     *
     * @since 2.0
     *
     * @return void
     */
    public function loadFrontend(): void
    {
        if (! is_singular()) {
            return;
        }

        // CSS
        wp_enqueue_style('ng-comment-love', $this->assetUrl('css/frontend.css'), [], NGLOVE_VERSION);

        // JavaScript
        wp_enqueue_script('ng-comment-love', $this->assetUrl('js/frontend.js'), [], NGLOVE_VERSION, true);
        $current_user = wp_get_current_user();
        wp_localize_script(
            'ng-comment-love',
            'NGLOVE',
            wp_parse_args([
                'ajaxurl'        => admin_url('admin-ajax.php'),
                'nonce'          => wp_create_nonce('ng_comment_love_ajax_nonce'),
                'website_url'    => $current_user ? $current_user->user_url : '',
                'message_no_url' => ng_comment_love_get_option(
                    'text_error_website_url',
                    __('Please enter your website URL first!', 'ng-comment-love')
                ),
                'loadingPosts'   => esc_html__('Loading posts...', 'ng-comment-love'),
                'noPostsFound'   => esc_html__('No posts found.', 'ng-comment-love'),
                'noLove'         => esc_html__('None', 'ng-comment-love'),
            ], $this->apiVars())
        );
    }

    /**
     * Loads admin assets.
     *
     * @since 2.0
     *
     * @param  string  $hook
     *
     * @return void
     */
    public function loadAdmin(string $hook): void
    {
        if ($hook !== 'comment.php') {
            return;
        }

        wp_enqueue_script(
            'ng-comment-love',
            $this->assetUrl('js/admin.js'),
            [],
            NGLOVE_VERSION,
            true
        );

        wp_localize_script(
            'ng-comment-love',
            'NGLOVE',
            wp_parse_args([
                'removeError' => esc_html__('Failed to remove love.', 'ng-comment-love'),
            ], $this->apiVars())
        );
    }

    /**
     * Builds a URL to an asset file.
     *
     * @since 2.0
     *
     * @param  string  $filePath  Path to the asset, relative to the build directory.
     *
     * @return string
     */
    private function assetUrl(string $filePath): string
    {
        return NGLOVE_URL.'assets/build/'.$filePath;
    }

    /**
     * Common API variables shared between front and back-end.
     *
     * @since 2.0
     *
     * @return array
     */
    private function apiVars(): array
    {
        return [
            'restBase'  => rest_url(RouteRegistration::API_NAMESPACE.'/v1'),
            'restNonce' => wp_create_nonce('wp_rest'),
        ];
    }

}
