<?php

/**
 * Setting a new cache time for feeds in WordPress
 * @param  integer 		$seconds 			Current cache time in seconds
 * @return integer 							1
 */
function ebay_feeds_for_wordpress_set_feed_cache_time( $seconds ) {
  return 1;
}

/**
 * Force the feed should you wish to do so.
 *
 * 
 * @param  object 		$feed  			The current feed object
 * @param  string 		$url  			The URL of the feed
 * @return void
 */
function ebay_feeds_for_wordpress_force_feed($feed, $url) {
  if ( get_option( 'ebay-feed-for-wordpress-force-feed' ) == 1 ) {
    $feed->force_feed(true);
  }
} add_action('wp_feed_options', 'ebay_feeds_for_wordpress_force_feed', 10, 2); 