<?php
/**
 * PostFetcher.php
 *
 * @package   ng-commentlove
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   GPL2+
 * @since     2.0
 */

namespace Ashleyfae\CommentLove\Helpers;

use Ashleyfae\CommentLove\Exceptions;
use Ashleyfae\CommentLove\ValueObjects\Post;

class PostFetcher
{

    /**
     * URL to get posts from.
     *
     * @var string
     * @since 2.0
     */
    public string $websiteUrl;

    /**
     * Number of posts to get.
     *
     * @var int
     * @since 2.0
     */
    public int $numberPosts = 10;

    /**
     * Constructor
     */
    public function __construct()
    {
        require_once ABSPATH.WPINC.'/feed.php';

        $this->numberPosts = ng_comment_love_get_option('numb_blog_posts', 10);
    }

    /**
     * Magic getter to aid in backwards compatibility for properties
     * that no longer exist.
     *
     * @param $property
     *
     * @return int|string|null
     */
    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->{$property};
        }

        switch ($property) {
            case 'url' :
                return $this->websiteUrl;
            case 'number_posts' :
                return $this->numberPosts;
            default :
                return null;
        }
    }

    /**
     * Intercepts method calls to reroute methods that are no longer available.
     *
     * @since 2.0
     *
     * @param $name
     * @param $arguments
     *
     * @return false|mixed|null
     */
    public function __call($name, $arguments)
    {
        if (method_exists($this, $name)) {
            return call_user_func_array([$this, $name], $arguments);
        }

        switch ($name) {
            case 'set_url' :
                $method = 'forUrl';
                break;
            case 'set_number_posts' :
                $method = 'postLimit';
                break;
            case 'get_posts' :
                $method = 'getPosts';
                break;
            case 'get_current_blog_posts' :
                $method = 'getCurrentSitePosts';
                break;
            case 'get_specific_feeds' :
                $method = 'getSimplePieFeed';
                break;
            default :
                return null;
        }

        try {
            return call_user_func_array([$this, $method], $arguments);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Sets the website URL.
     *
     * @since 2.0
     *
     * @param  string  $url
     *
     * @return $this
     */
    public function forUrl(string $url): self
    {
        $this->websiteUrl = esc_url_raw($url);

        return $this;
    }

    /**
     * Limits the number of posts to retrieve.
     *
     * @since 2.0
     *
     * @param  int  $numberPosts
     *
     * @return $this
     */
    public function postLimit(int $numberPosts): self
    {
        $this->numberPosts = $numberPosts;

        return $this;
    }

    /**
     * Retrieves posts from the RSS feed.
     *
     * @since 1.0
     *
     * @return Post[]
     * @throws Exceptions\NoFeedFoundException|Exceptions\NoPostsException
     */
    public function getPosts(): array
    {
        if ($this->isCurrentSiteUrl()) {
            return $this->getCurrentSitePosts();
        }

        $formattedPosts = [];
        $rssFeed        = $this->getSimplePieFeed();

        // Get the number of results in the feed, within our limit.
        $maxItems = $rssFeed->get_item_quantity($this->numberPosts);

        if ($maxItems === 0) {
            throw new Exceptions\NoPostsException();
        }

        // Build an array of all our feed items.
        $i = 0;

        foreach ($rssFeed->get_items(0, $maxItems) as $item) {
            $post             = new Post();
            $post->identifier = sanitize_title_with_dashes($item->get_title());
            $post->title      = wp_strip_all_tags($item->get_title());
            $post->url        = esc_url_raw($item->get_permalink());
            $post->isSelected = ($i === 0);

            $formattedPosts[] = $post;
            $i++;
        }

        if (empty($formattedPosts)) {
            throw new Exceptions\NoPostsException();
        }

        return $formattedPosts;
    }

    /**
     * Determines if the provided URL is for the current site.
     *
     * @since 2.0
     *
     * @return bool
     */
    protected function isCurrentSiteUrl(): bool
    {
        return strstr($this->websiteUrl, home_url());
    }

    /**
     * Retrieves posts for from current site.
     *
     * @since 2.0
     *
     * @return array
     * @throws Exceptions\NoPostsException
     */
    protected function getCurrentSitePosts(): array
    {
        $posts = get_posts([
            'posts_per_page'         => $this->numberPosts,
            'no_found_rows'          => true,
            'update_post_term_cache' => false,
            'update_post_meta_cache' => false,
        ]);

        if (empty($posts) || ! is_array($posts)) {
            throw new Exceptions\NoPostsException();
        }

        $formattedPosts = [];
        $i              = 0;

        foreach ($posts as $post) {
            $postObject             = new Post();
            $postObject->identifier = $post->post_name;
            $postObject->title      = wp_strip_all_tags($post->post_title);
            $postObject->url        = get_permalink($post);
            $postObject->isSelected = ($i === 0);

            $formattedPosts[] = $postObject;
            $i++;
        }

        return $formattedPosts;
    }

    /**
     * Retrieves a SimplePie instance using a few different methods.
     *
     * @since 2.0
     *
     * @return \SimplePie
     * @throws Exceptions\NoFeedFoundException
     */
    protected function getSimplePieFeed(): \SimplePie
    {
        /**
         * First try the provided URL.
         */
        $rssFeed = fetch_feed($this->websiteUrl);

        if ($rssFeed instanceof \SimplePie) {
            return $rssFeed;
        }

        /**
         * Next try to get a WordPress RSS feed.
         */
        $rssFeed = fetch_feed(trailingslashit($this->websiteUrl).'feed/');

        if ($rssFeed instanceof \SimplePie) {
            return $rssFeed;
        }

        /**
         * Last resort, try to get a Blogspot RSS feed.
         */
        $rssFeed = fetch_feed(trailingslashit($this->websiteUrl).'feeds/posts/default');

        if ($rssFeed instanceof \SimplePie) {
            return $rssFeed;
        }

        throw new Exceptions\NoFeedFoundException();
    }

}
