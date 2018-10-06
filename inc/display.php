<?php

/**
 * Main old function
 *
 * Was previously a carbon copy of the ebay_feeds_for_wordpress_notecho function. Now it simply calls it since 2.0. Easier to maintain.
 * 
 * @param  string $url 		The URL of the Feed
 * @param  string $num 		The number of items to display
 * @return mixed 			String of the feed if echo is false, string if true
 */
function ebay_feeds_for_wordpress( $url = "", $num = "", $echo = false, $args = array() ) {

	$display = ebay_feeds_for_wordpress_notecho( $url, $num, $args );

	if ( true == $echo ) {
		echo $display;
	} else {
		return $display;
	}
}


/**
 * Function to return the eBay Feed. 
 * 
 * @param  string $dispurls The URL of the Feed
 * @param  string $dispnum  The number of items to display
 * @return string          	String of the feed content
 */
function ebay_feeds_for_wordpress_notecho( $dispurls = "", $dispnum = "", $args = array() ) {
	
	$link = get_option( "ebay-feeds-for-wordpress-link" );
	$blank = get_option( "ebay-feeds-for-wordpress-link-open-blank" );
	$nofollow = get_option( "ebay-feeds-for-wordpress-nofollow-links" );
	$debug = get_option( "ebay-feeds-for-wordpress-debug" );
	$class = get_option( 'ebay-feeds-for-wordpress-item-div-wrapper' );
	$disprss_items = "";
	$display = "";
	$ssl 	= get_option( 'ebay-feed-for-wordpress-ssl' );

	if ( $dispnum == "" || $dispnum == "null" ) {

		$dispnum = get_option( 'ebay-feeds-for-wordpress-default-number' );

	}

	if ( $class ) {

		$classstring = '<div class="' . $class . '">';

	} else {
		$classstring = '<div class="">';
	}


	$classstring = apply_filters( 'ebay_feeds_for_wordpress_change_class_string', $classstring, $args );

	if ( $dispurls == "" || $dispurls == "null" ) {

		$dispurldefault = get_option( 'ebay-feeds-for-wordpress-default' );
		$disprss = fetch_feed( $dispurldefault );

		if ( $disprss ) {

			$disprss_items = $disprss->get_items( 0, $dispnum );

		} else {

			$fallback = get_option( 'ebay_feeds_for_wordpress_fallback' );

			$display .=  "<div class='ebayfeed'>";
			$display .= $fallback;
			$display .=  "</div>";
		}

	} else {
		$dispurls = str_replace( "&amp;", "&", $dispurls );
		$disprss = fetch_feed( $dispurls );

		//wp_die( print_r( $disprss ) );

		if ( !is_wp_error( $disprss ) ) {

			$disprss_items = $disprss->get_items( 0, $dispnum );

		} else {

			if ( current_user_can( 'manage_options' ) && 1 == $debug ) {

				$error_string = $disprss->get_error_message();

				$display .= '<div id="message" class="error"><p>' . $error_string . '</p></div>';

			}

			$fallback = get_option( 'ebay_feeds_for_wordpress_fallback' );
			$display .=  "<div class='ebayfeed'>";
			$display .= $fallback;
			$display .=  "</div>";

		}

	}

	$display .=  "<div class='ebayfeed'>";

	if ( $disprss_items ) {

		foreach ( $disprss_items as $dispitem ) {

			$title = "<h4 class='ebayfeedtitle'><a class='ebayfeedlink' ";

			if ( $blank == "1" ) {
				$title .= "target='_blank' ";
			}

			if ( $nofollow == "1" ) {
				$title .= " rel='nofollow' ";
			}

			$title .= "href='" . $dispitem->get_permalink()."'>" . $dispitem->get_title() . "</a></h4>";

			$title = apply_filters( 'ebay_feeds_for_wordpress_title_string', $title, $args );

			$display .= $classstring . $title; 

			if ( $blank != "1" ) {
				$newdescription = str_replace( "target='_blank'", "", $dispitem->get_description() );
			} else {
				$newdescription = $dispitem->get_description();
			}

			if ( $nofollow == "1" ) {
				$newdescription = str_replace( "<a href=", "<a rel='nofollow' href=", $newdescription );
			}
			
			if ( $ssl == "1" ) {
				
				$newdescription = str_replace( "<img src='http://","<img src='https://", $newdescription );

			}

			$newdescription = apply_filters( 'ebay_feeds_for_wordpress_description_string', $newdescription, $args );

			$display .= $newdescription;

			$display .= "</div>";

		}
	}

	$display .= "</div>";

	if ( $link == 1 ) {
		$display .= __( '<a href="https://www.winwar.co.uk/plugins/ebay-feeds-wordpress/">eBay Feeds for WordPress</a> by <a href="http://winwar.co.uk/">Winwar Media</a><br/><br/>', 'ebay-feeds-for-wordpress' );
	}


	return $display;

}


/**
 * Parse the eBay Feeds for WordPress Shortcode
 * @param  array  $atts  	array of attributes
 * @return string 			The display feed
 */
function ebayfeedsforwordpress_shortcode( $atts ) {
	$feed = get_option( 'ebay-feeds-for-wordpress-default' );
	$items = get_option( 'ebay-feeds-for-wordpress-default-number' );

	extract( shortcode_atts( array(
		'feed' => $feed, 'items' => $items
		), $atts ) );

	$feed = html_entity_decode( $feed );

	$feeddisplay = ebay_feeds_for_wordpress( esc_attr( $feed ), esc_attr( $items ) );
	return $feeddisplay;
}
