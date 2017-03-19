<?php

/**
 * Class that fetches the most recent blog post from a given URL.
 *
 * @package   ng-comment-love
 * @copyright Copyright (c) 2015, Nose Graze Ltd.
 * @license   GPL2+
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class NG_Comment_Love {

	/**
	 * URL to get posts from.
	 *
	 * @var string
	 */
	public $url;

	/**
	 * Number of posts to get.
	 *
	 * @var int
	 */
	public $number_posts;

	/**
	 * Constructor
	 *
	 * Sets the number of posts to get.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		$this->set_number_posts();
	}

	/**
	 * Sets the URL.
	 *
	 * @param string $url
	 *
	 * @access public
	 * @return void
	 */
	public function set_url( $url ) {
		$this->url = esc_url( $url );
	}

	/**
	 * Sets the number of posts we want to get.
	 *
	 * @param int $number
	 *
	 * @access public
	 * @return void
	 */
	public function set_number_posts( $number = 10 ) {
		$this->number_posts = intval( $number );
	}

	/**
	 * Get recent blog posts from the URL.
	 *
	 * @access public
	 * @return array|bool False on failure
	 */
	public function get_posts() {
		include_once( ABSPATH . WPINC . '/feed.php' );

		// Get posts from current URL.
		if ( strstr( $this->url, home_url() ) ) {
			return $this->get_current_blog_posts();
		}

		$return = array();

		$rss = fetch_feed( $this->url );

		// If there was an error - try something else or bail.
		if ( is_wp_error( $rss ) ) {
			$rss = $this->get_specific_feeds();

			if ( $rss === false || is_wp_error( $rss ) ) {
				return false;
			}
		}

		// Limit the results.
		$maxitems = $rss->get_item_quantity( $this->number_posts );

		// If there are no items - bail.
		if ( $maxitems == 0 ) {
			return false;
		}

		// Build an array of all the items, starting with element 0 (first element).
		$rss_items = $rss->get_items( 0, $maxitems );

		$i = 0;

		// Loop through each item and add it to our array.
		foreach ( $rss_items as $item ) {
			$return[] = array(
				'ID'       => esc_attr( sanitize_title_with_dashes( $item->get_title() ) ),
				'title'    => esc_html( $item->get_title() ),
				'link'     => esc_url( $item->get_permalink() ),
				'selected' => ( $i === 0 ) ? 'selected' : ''
			);

			$i ++;
		}

		return $return;
	}

	/**
	 * Build platform-specific URLs and get those feeds.
	 *
	 * @access public
	 * @return bool|SimplePie|WP_Error
	 */
	public function get_specific_feeds() {
		/**
		 * First try to get a WordPress RSS feed.
		 */
		$wordpress_feed = trailingslashit( $this->url ) . 'feed/';
		$rss            = fetch_feed( $wordpress_feed );

		// We successfully got a WordPress feed - return it.
		if ( ! is_wp_error( $rss ) ) {
			return $rss;
		}

		/**
		 * Next, try to get a Blogspot RSS feed.
		 */
		$blogspot_feed = trailingslashit( $this->url ) . 'feeds/posts/default';
		$rss           = fetch_feed( $blogspot_feed );

		// We successfully got a Blogspot feed - return it.
		if ( ! is_wp_error( $rss ) ) {
			return $rss;
		}

		return false;
	}

	/**
	 * Gets the latest blog posts from the current blog.
	 *
	 * @access public
	 * @return array|bool False on failure.
	 */
	public function get_current_blog_posts() {
		$recent_posts = get_posts( array(
			'numberposts' => $this->number_posts
		) );

		$return = array();

		// No posts - return an error.
		if ( ! $recent_posts || ! is_array( $recent_posts ) || ! count( $recent_posts ) ) {
			return false;
		}

		$i = 0;

		// Loop through each post and add it to our array.
		foreach ( $recent_posts as $recent_post ) {
			$return[] = array(
				'ID'       => esc_attr( $recent_post->post_name ),
				'title'    => strip_tags( $recent_post->post_title ),
				'link'     => get_permalink( $recent_post->ID ),
				'selected' => ( $i === 0 ) ? 'selected' : ''
			);

			$i ++;
		}

		return $return;
	}

}