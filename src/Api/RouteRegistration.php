<?php
/**
 * RouteRegistration.php
 *
 * @package   ng-commentlove
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   GPL2+
 */

namespace Ashleyfae\CommentLove\Api;

use Ashleyfae\CommentLove\Api\v1;

class RouteRegistration
{
    const API_NAMESPACE = 'comment-love';

    public function register(): void
    {
        $routes = [
            v1\ListBlogPosts::class,
            v1\RemoveLove::class,
        ];

        foreach ($routes as $route) {
            if (in_array(RestRoute::class, class_implements($route), true)) {
                /** @var RestRoute $route */
                $route = new $route;
                $route->register();
            }
        }
    }

}
