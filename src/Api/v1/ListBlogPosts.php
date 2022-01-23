<?php
/**
 * ListBlogPosts.php
 *
 * @package   ng-commentlove
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   GPL2+
 * @since     2.0
 */

namespace Ashleyfae\CommentLove\Api\v1;

use Ashleyfae\CommentLove\Api\RestRoute;
use Ashleyfae\CommentLove\Api\RouteRegistration;
use Ashleyfae\CommentLove\Helpers\PostFetcher;
use Ashleyfae\CommentLove\ValueObjects\Post;

class ListBlogPosts implements RestRoute
{

    public function register(): void
    {
        register_rest_route(
            RouteRegistration::API_NAMESPACE.'/v1',
            'posts',
            [
                'methods'             => [\WP_REST_Server::READABLE, \WP_REST_Server::CREATABLE],
                'callback'            => [$this, 'handle'],
                'permission_callback' => '__return_true',
                'args'                => [
                    'url' => [
                        'required'          => true,
                        'validate_callback' => function ($param, $request, $key) {
                            return is_string($param);
                        },
                        'sanitize_callback' => function ($param, $request, $key) {
                            return wp_strip_all_tags($param);
                        },
                    ]
                ],
            ]
        );
    }

    public function handle(\WP_REST_Request $request): \WP_REST_Response
    {
        try {
            $postFetcher = (new PostFetcher())->forUrl($request->get_param('url'));

            return new \WP_REST_Response([
                'posts' => array_map(function (Post $post) {
                    return $post->toArray();
                }, $postFetcher->getPosts()),
            ]);
        } catch (\Exception $e) {
            return new \WP_REST_Response([
                'error' => $e->getMessage(),
            ], 422);
        }
    }
}
