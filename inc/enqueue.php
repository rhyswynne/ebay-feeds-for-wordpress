<?php

/**
 * Add the admin stylesheet.
 * 
 * @return void 
 */
function ebay_feeds_for_wordpress_add_admin_stylesheet() {
  wp_register_style( 'ebay-feeds-for-wordpress-style', EBAYFEEDSFORWORDPRESS_PLUGIN_URL . '/ebay-feeds-for-wordpress-admin.css' );
  wp_enqueue_style( 'ebay-feeds-for-wordpress-style' );
}