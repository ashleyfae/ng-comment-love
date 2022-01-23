<?php
/**
 * Plugin Name: NG Comment Love
 * Plugin URI: https://www.nosegraze.com
 * Description: Fetches the commenter's most recent post to display below their comment
 * Version: 1.2.2
 * Author: Ashley Gibson
 * Author URI: https://www.nosegraze.com
 * License: GPL2
 *
 * @package   ng-comment-love
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   GPL2+
 */

if (version_compare(phpversion(), '7.4', '<')) {
    return;
}

/**
 * Define constants.
 */
const NGLOVE_VERSION = '1.2.2';
const NGLOVE_FILE    = __FILE__;

define('NGLOVE_URL', plugin_dir_url(__FILE__));
define('NGLOVE_DIR', plugin_dir_path(__FILE__));

require_once dirname(__FILE__).'/vendor/autoload.php';

/**
 * Returns an instance of the plugin.
 *
 * @since 2.0
 *
 * @return \Ashleyfae\CommentLove\Plugin
 */
function ngCommentLove()
{
    return \Ashleyfae\CommentLove\Plugin::instance();
}

/**
 * Boots the plugin.
 */
ngCommentLove()->boot();

/**
 * Include settings panel.
 */
global $ng_comment_love_options;
require_once NGLOVE_DIR.'includes/admin/settings/register-settings.php';
if (empty($ng_comment_love_options)) {
    $ng_comment_love_options = ng_comment_love_get_settings();
}

/**
 * Include admin files.
 */
if ( is_admin() ) {
	require_once NGLOVE_DIR . 'includes/admin/settings/display-settings.php';
}
