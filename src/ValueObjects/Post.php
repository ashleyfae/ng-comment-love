<?php
/**
 * Post.php
 *
 * @package   ng-commentlove
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   GPL2+
 * @since     2.0
 */

namespace Ashleyfae\CommentLove\ValueObjects;

class Post
{

    public string $identifier;
    public string $title;
    public string $url;
    public bool $isSelected = false;

    public function toArray(): array
    {
        return [
            'identifier' => $this->identifier,
            'title'      => $this->title,
            'url'        => $this->url,
            'isSelected' => $this->isSelected,
        ];
    }

}
