<?php
/*
Plugin Name:  Ebay Feeds for WordPress
Plugin URI:   https://www.winwar.co.uk/plugins/ebay-feeds-wordpress/?utm_source=plugin-link&utm_medium=plugin&utm_campaign=ebayfeedsforwordpress
Description:  Parser of ebay RSS feeds to display on Wordpress posts, widgets and pages.
Version:      2.0-beta
Author:       Winwar Media
Author URI:   https://www.winwar.co.uk/?utm_source=author-link&utm_medium=plugin&utm_campaign=ebayfeedsforwordpress

*/

define('EBAYFEEDSFORWORDPRESS_PLUGIN_PATH',dirname(__FILE__));
define('EBAYFEEDSFORWORDPRESS_PLUGIN_URL',plugins_url('', __FILE__));


define( "EBFW_PLUGIN_NAME", "eBay Feeds For WordPress" );
define( "EBFW_PLUGIN_TAGLINE", __( "Parser of ebay RSS feeds to display on Wordpress posts, widgets and pages.", "ebay-feeds-for-wordpress" ) );
define( "EBFW_PLUGIN_URL", "https://winwar.co.uk/plugins/ebay-feeds-wordpress/" );
define( "EBFW_EXTEND_URL", "https://wordpress.org/extend/plugins/ebay-feeds-for-wordpress/" );
define( "EBFW_AUTHOR_TWITTER", "rhyswynne" );
define( "EBFW_DONATE_LINK", "https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=F852ZPEANV7C6" );

require_once( EBAYFEEDSFORWORDPRESS_PLUGIN_PATH . '/inc/core.php' );


// Admin Functions
if ( is_admin() ) { // admin actions

  add_action( 'admin_menu', 'ebay_feeds_for_wordpress_menus' );
  add_action( 'admin_init', 'ebay_feeds_for_wordpress_options_process' );
  add_action( 'admin_init', 'ebay_feeds_for_wordpress_add_admin_stylesheet' );

}

// non-admin functions
add_action( 'init', 'ebay_feeds_for_wordpress_addbuttons' );
add_action( 'plugins_loaded', 'ebay_feeds_for_wordpress_textdomain' );
add_shortcode( 'ebayfeedsforwordpress', 'ebayfeedsforwordpress_shortcode' );

// Debug Functions
if ( 1 == get_option( 'ebay-feed-for-wordpress-flush-cache' ) ) {

  add_filter( 'wp_feed_cache_transient_lifetime' , 'ebay_feeds_for_wordpress_set_feed_cache_time', 99, 1 );
  add_filter( 'wp_feed_cache_transient_lifetime' , 'ebay_feeds_for_wordpress_set_feed_cache_time', 100, 1 );
}

// Gutenberg Support
  //if ( function_exists( 'the_gutenberg_project' ) ) {
    //wp_die( "In here?");
    add_action( 'enqueue_block_editor_assets', 'ebay_feeds_for_wordpress_enqueue_block_editor_assets', 10 );
    //add_action( 'enqueue_block_assets', 'ebay_feeds_for_wordpress_enqueue_block_editor_css', 10 );  
    add_action( 'init', 'ebay_feeds_for_wordpress_init_gutenberg_block' );  
  //}


// Installation Function
register_activation_hook( __FILE__, 'ebay_feeds_for_wordpress_install' );


/**
 * Preset options on install
 * 
 * @return void
 */
function ebay_feeds_for_wordpress_install() {
  add_option( 'ebay-feeds-for-wordpress-default', 'http://rest.ebay.com/epn/v1/find/item.rss?keyword=Ferrari&categoryId1=18180&sortOrder=BestMatch&programid=15&campaignid=5336886189&toolid=10039&listingType1=All&lgeo=1&descriptionSearch=true&feedType=rss' );
  add_option( 'ebay-feeds-for-wordpress-default-number', 3 );
  add_option( 'ebay-feeds-for-wordpress-link', 0 );
  add_option( 'ebay-feeds-for-wordpress-link-open-blank', 0 );
  add_option( 'ebay-feed-for-wordpress-flush-cache', 0 );
}


/**
 * Load the Textdomain and set it
 *
 * Must be in the main wrapper, for...y'know...reasons
 * @return void
 */
function ebay_feeds_for_wordpress_textdomain() {
  $plugin_dir = basename( dirname( __FILE__ ) );
  load_plugin_textdomain( 'ebay-feeds-for-wordpress', false, $plugin_dir .'/languages' );
}
