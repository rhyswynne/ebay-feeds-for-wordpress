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
function ebay_feeds_for_wordpress( $url = "", $num = "", $echo = false, $args = array(), $header = '' ) {

	$display = ebay_feeds_for_wordpress_notecho( $url, $num, $args, $header );

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
function ebay_feeds_for_wordpress_notecho( $dispurls = "", $dispnum = "", $args = array(), $header = '' ) {

	$link          = get_option( "ebay-feeds-for-wordpress-link" );
	$blank         = get_option( "ebay-feeds-for-wordpress-link-open-blank" );
	$nofollow      = get_option( "ebay-feeds-for-wordpress-nofollow-links" );
	$debug         = get_option( "ebay-feeds-for-wordpress-debug" );
	$class         = get_option( 'ebay-feeds-for-wordpress-item-div-wrapper' );
	$disableheader = get_option( 'ebay-feeds-for-wordpress-disable-header' );
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

	if ( ! $disableheader && $header ) {
		$display .=  "<h3>" . $header . "</h3>";
	}

	if ( $dispurls == "" || $dispurls == "null" ) {

		$dispurldefault = esc_attr( get_option( 'ebay-feeds-for-wordpress-default' ) );
		$disprss        = fetch_feed( $dispurldefault );

		if ( $disprss ) {

			if ( !is_wp_error( $disprss ) ) {
				$disprss->enable_order_by_date(false);
				$maxitems      = $disprss->get_item_quantity( $dispnum );
				$disprss_items = $disprss->get_items( 0, $maxitems );
			}

		} else {

			$fallback = get_option( 'ebay_feeds_for_wordpress_fallback' );

			$display .=  "<div class='ebayfeed'>";
			$display .= $fallback;
			$display .=  "</div>";
		}

	} else {
		$dispurls = str_replace( '&amp;', '&', $dispurls );
		$disprss = fetch_feed( $dispurls );
		//wp_die( print_r( $disprss ) );

		if ( !is_wp_error( $disprss ) ) {
			$disprss->enable_order_by_date(false);
			$maxitems      = $disprss->get_item_quantity( $dispnum );
			$disprss_items = $disprss->get_items( 0, $maxitems );
			//print_r( $disprss_items );
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

	//print_r( $disprss_items );

	if ( $disprss_items && $maxitems != 0 ) {

		$temptitle = false;
		$tempperm  = false;
		$temdesc   = false;

		foreach ( $disprss_items as $dispitem ) {

			if ( ! $temptitle && '' != $dispitem->get_title() ) {
				$temptitle = true;
			}

			if ( !$tempperm == '' && '' != $dispitem->get_permalink() ) {
				$tempperm = true;
			}

			if ( $temdesc == '' && '' != $dispitem->get_description() ) {
				$temdesc = true;
			}

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

			if ( !$temptitle && ! $tempperm && ! $tempdesc ) {
				$fallback = get_option( 'ebay_feeds_for_wordpress_fallback' );
				$display =  "<div class='ebayfeed'>" . $fallback;
			}

		}
	} else {
		$fallback = get_option( 'ebay_feeds_for_wordpress_fallback' );
		$display .= $fallback;
	}

	$display .= "</div>";

	if ( $link == 1 ) {
		$display .= __( '<a href="https://www.winwar.co.uk/plugins/ebay-feeds-wordpress/">WP eBay Product Feeds</a> by <a href="http://winwar.co.uk/">Winwar Media</a><br/><br/>', 'ebay-feeds-for-wordpress' );
	}


	return $display;

}


/**
 * Parse the WP eBay Product Feeds Shortcode
 * @param  array  $atts  	array of attributes
 * @return string 			The display feed
 */
function ebayfeedsforwordpress_shortcode( $atts ) {

	$feed   = '';
	$items  = '';
	$header = '';

	extract( shortcode_atts( array(
		'feed' => $feed, 'items' => $items, 'header' => $header
		), $atts ) );

	if ( ! $feed ) {
		$feed  = get_option( 'ebay-feeds-for-wordpress-default' );
	}

	if ( '' == $items ) {
		$items = get_option( 'ebay-feeds-for-wordpress-default-number' );
	}

	if ( '' == $header ) {
		$header = get_option( 'ebay-feeds-for-wordpress-default-header' );
	}

	$feed = html_entity_decode( $feed );

	$feeddisplay = ebay_feeds_for_wordpress( esc_attr( $feed ), esc_attr( $items ), false, array(), esc_attr( $header ) );
	return $feeddisplay;
}


/**
 * Add CSS to the header to display max width
 * @return void
 */
function ebayfeedsforwordpress_set_max_image_width() {
	if ( get_option( 'ebay-feeds-for-wordpress-imax-max-width' ) ) {
		?>
		<style type="text/css">
		.ebayfeed img {
			max-width: <?php echo get_option( 'ebay-feeds-for-wordpress-imax-max-width' ); ?>px;
		}
		</style>
		<?php
	}
}
