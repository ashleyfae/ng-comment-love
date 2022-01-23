<?php
/**
 * SettingsPage.php
 *
 * @package   ng-commentlove
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   GPL2+
 * @since     2.0
 */

namespace Ashleyfae\CommentLove\Admin;

class SettingsPage
{

    public function register(): void
    {
        add_options_page(
            __('NG Comment Love Settings', 'ng-comment-love'),
            __('Comment Love', 'ng-comment-love'),
            'manage_options',
            'ng-comment-love-settings',
            [$this, 'render']
        );
    }

    public function render(): void
    {

    }

}
