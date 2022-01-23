<?php
/**
 * CommentLove.php
 *
 * @package   ng-commentlove
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   GPL2+
 */

namespace Ashleyfae\CommentLove\ValueObjects;

class CommentLove
{

    public string $postTitle;
    public string $postUrl;

    public static function fromArray(array $data): self
    {
        $commentLove            = new self;
        $commentLove->postTitle = $data['cl_post_title'] ?? '';
        $commentLove->postUrl   = $data['cl_post_url'] ?? '';

        return $commentLove;
    }

}
