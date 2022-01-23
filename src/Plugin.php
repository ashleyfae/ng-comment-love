<?php
/**
 * Plugin.php
 *
 * @package   ng-commentlove
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   GPL2+
 * @since     2.0
 */

namespace Ashleyfae\CommentLove;

use Ashleyfae\CommentLove\Admin\CommentMetabox;
use Ashleyfae\CommentLove\Admin\SettingsPage;
use Ashleyfae\CommentLove\Api\RouteRegistration;
use Ashleyfae\CommentLove\Helpers\AssetLoader;
use Ashleyfae\CommentLove\Helpers\CommentFields;
use Ashleyfae\CommentLove\Helpers\LoveAdder;

class Plugin
{
    /**
     * Single instance of this class.
     *
     * @var Plugin|null
     * @since 2.0
     */
    private static ?Plugin $instance = null;

    /**
     * Returns the single instance of this class.
     *
     * @since 2.0
     *
     * @return Plugin
     */
    public static function instance(): Plugin
    {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Boots the plugin.
     *
     * @since 2.0
     *
     * @return void
     */
    public function boot(): void
    {
        // Front-end.
        $assetLoader = new AssetLoader();
        add_action('wp_enqueue_scripts', [$assetLoader, 'loadFrontend']);
        add_action('rest_api_init', [new RouteRegistration(), 'register']);

        // Admin.
        add_action('admin_menu', [new SettingsPage(), 'register']);
        add_action('add_meta_boxes', [new CommentMetabox(), 'register']);
        add_action('admin_enqueue_scripts', [$assetLoader, 'loadAdmin']);

        $this->addCommentFormFields();
        $this->displayLove();
    }

    /**
     * Modifies the comment form template to add Comment Love support.
     *
     * @since 2.0
     *
     * @return void
     */
    private function addCommentFormFields(): void
    {
        $commentFields = new CommentFields();

        add_action('comment_form_field_url', [$commentFields, 'urlField']);
        add_filter('comment_form_logged_in', [$commentFields, 'loggedInUserMessage'], 10, 3);
    }

    /**
     * Inserts and displays comment love.
     *
     * @since 2.0
     *
     * @return void
     */
    private function displayLove(): void
    {
        $loveAdder = new LoveAdder();

        add_action('comment_post', [$loveAdder, 'insertAfterComment']);
        add_filter('comment_text', [$loveAdder, 'displayLoveWithComment'], 100);
    }
}
