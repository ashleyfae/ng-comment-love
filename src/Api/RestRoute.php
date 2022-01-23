<?php
/**
 * RestRoute.php
 *
 * @package   ng-commentlove
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   GPL2+
 */

namespace Ashleyfae\CommentLove\Api;

interface RestRoute
{

    public function register(): void;

    public function handle(\WP_REST_Request $request): \WP_REST_Response;

}
