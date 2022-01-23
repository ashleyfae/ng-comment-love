<?php
/**
 * RemoveLove.php
 *
 * @package   ng-commentlove
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   GPL2+
 */

namespace Ashleyfae\CommentLove\Api\v1;

use Ashleyfae\CommentLove\Api\RestRoute;
use Ashleyfae\CommentLove\Api\RouteRegistration;

class RemoveLove implements RestRoute
{

    public function register(): void
    {
        register_rest_route(
            RouteRegistration::API_NAMESPACE.'/v1',
            'comments/(?P<comment_id>\d+)/love',
            [
                'methods'             => [\WP_REST_Server::DELETABLE],
                'callback'            => [$this, 'handle'],
                'permission_callback' => [$this, 'permissionCheck'],
                'args'                => [
                    'comment_id' => [
                        'required'          => true,
                        'validate_callback' => function ($param, $request, $key) {
                            return get_comment($param) instanceof \WP_Comment;
                        },
                        'sanitize_callback' => function ($param, $request, $key) {
                            return absint($param);
                        },
                    ]
                ],
            ]
        );
    }

    public function permissionCheck(\WP_REST_Request $request): bool
    {
        return current_user_can('edit_comment', $request->get_param('comment_id'));
    }

    public function handle(\WP_REST_Request $request): \WP_REST_Response
    {
        if (delete_comment_meta($request->get_param('comment_id'), 'cl_data')) {
            return new \WP_REST_Response([
                'message' => esc_html__('Comment love removed successfully.', 'ng-comment-love'),
            ], 200);
        } else {
            return new \WP_REST_Response([
                'error' => esc_html__('Failed to remove comment love.', 'ng-comment-love'),
            ], 500);
        }
    }
}
