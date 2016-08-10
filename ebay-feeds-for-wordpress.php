<?php
/*
Plugin Name:  Ebay Feeds for WordPress
Plugin URI:   https://winwar.co.uk/plugins/ebay-feeds-wordpress/?utm_source=plugin-link&utm_medium=plugin&utm_campaign=ebayfeedsforwordpress
Description:  Parser of ebay RSS feeds to display on Wordpress posts, widgets and pages.
Version:      1.7.3
Author:       Winwar Media
Author URI:   https://winwar.co.uk/?utm_source=author-link&utm_medium=plugin&utm_campaign=ebayfeedsforwordpress

*/

define('EBAYFEEDSFORWORDPRESS_PLUGIN_PATH',dirname(__FILE__));
define('EBAYFEEDSFORWORDPRESS_PLUGIN_URL',plugins_url('', __FILE__));

function ebay_feeds_for_wordpress_textdomain() {
  $plugin_dir = basename( dirname( __FILE__ ) );
  load_plugin_textdomain( 'ebay-feeds-for-wordpress', false, $plugin_dir .'/languages' );
}
add_action( 'plugins_loaded', 'ebay_feeds_for_wordpress_textdomain' );

define( "EBFW_PLUGIN_NAME", "eBay Feeds For WordPress" );
define( "EBFW_PLUGIN_TAGLINE", __( "Parser of ebay RSS feeds to display on Wordpress posts, widgets and pages.", "ebay-feeds-for-wordpress" ) );
define( "EBFW_PLUGIN_URL", "https://winwar.co.uk/plugins/ebay-feeds-wordpress/" );
define( "EBFW_EXTEND_URL", "https://wordpress.org/extend/plugins/ebay-feeds-for-wordpress/" );
define( "EBFW_AUTHOR_TWITTER", "rhyswynne" );
define( "EBFW_DONATE_LINK", "https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=F852ZPEANV7C6" );

register_activation_hook( __FILE__, 'ebay_feeds_for_wordpress_install' );

function ebay_feeds_for_wordpress( $url = "", $num = "" ) {

  $display = "";

  $link = get_option( "ebay-feeds-for-wordpress-link" );
  $blank = get_option( "ebay-feeds-for-wordpress-link-open-blank" );
  $nofollow = get_option( "ebay-feeds-for-wordpress-nofollow-links" );
  $debug = get_option( "ebay-feeds-for-wordpress-debug" );

  if ( $url == "" ) {
    $url = get_option( 'ebay-feeds-for-wordpress-default' );
  }

  if ( $num == "" ) {
    $num = get_option( 'ebay-feeds-for-wordpress-default-number' );
  }

  $url = str_replace( '&amp;', '&', $url );

  $feed = fetch_feed( $url );

  if ( !is_wp_error( $feed ) ) {
  	
    $feed->enable_order_by_date( false );
  	$rss_items = $feed->get_items( 0, $num );

  } else {
  	if ( current_user_can( 'manage_options' ) && 1 == $debug ) {

  		$error_string = $feed->get_error_message();
  		$display .= '<div id="message" class="error"><p>' . $error_string . '</p></div>';

  	}
  }

  $display .= '<div class="ebayfeed">';

  if ( !is_wp_error( $feed ) && $rss_items ) {

  	foreach ( $rss_items as $item ) {

  		$display .= '<h4 class="ebayfeedtitle"><a ';

  		if ( $blank == "1" ) {

  			$display .= 'target="_blank" ';

  		}

  		if ( $nofollow == "1" ) {

  			$display .=' rel="nofollow" ';

  		}

  		$display .= 'href="'.$item->get_permalink().'"  class="ebayfeedlink">'.$item->get_title().'</a></h4>';

  		if ( $blank == "1" ) {

  			$display .= $item->get_description();

  		} else {

  			$newdescription = str_replace( 'target="_blank"', '', $item->get_description() );
  			$display .= $newdescription;

  		}

  	}
  }

  $display .= "</div>";

  if ( $link == 1 ) {

    $display .= __( '<a href="http://winwar.co.uk/plugins/ebay-feeds-wordpress/">eBay Feeds for WordPress</a> by <a href="http://winwar.co.uk">Winwar Media</a><br/><br/>', 'ebay-feeds-for-wordpress' );

  }

  return $display;
}

if ( is_admin() ) { // admin actions

  add_action( 'admin_menu', 'ebay_feeds_for_wordpress_menus' );
  add_action( 'admin_init', 'ebay_feeds_for_wordpress_options_process' );
  add_action( 'admin_init', 'ebay_feeds_for_wordpress_add_admin_stylesheet' );

}

if ( 1 == get_option( 'ebay-feed-for-wordpress-flush-cache' ) ) {

  add_filter( 'wp_feed_cache_transient_lifetime' , 'ebay_feeds_for_wordpress_set_feed_cache_time', 99, 1 );
  add_filter( 'wp_feed_cache_transient_lifetime' , 'ebay_feeds_for_wordpress_set_feed_cache_time', 100, 1 );
}


/**
 * Setting a new cache time for feeds in WordPress
 */
function ebay_feeds_for_wordpress_set_feed_cache_time( $seconds ) {
  return 1;
}

/* function ebay_feeds_for_wordpress_show_feed_cache_time( $seconds ) {
  wp_die( $seconds );
} */


function ebay_feeds_for_wordpress_add_admin_stylesheet() {
  wp_register_style( 'ebay-feeds-for-wordpress-style', plugins_url( 'ebay-feeds-for-wordpress-admin.css', __FILE__ ) );
  wp_enqueue_style( 'ebay-feeds-for-wordpress-style' );
}

function ebay_feeds_for_wordpress_menus() {

  add_options_page( 'eBay Feeds Options', 'ebay Feeds For Wordpress', 'manage_options', 'ebayfeedforwordpressoptions', 'ebay_feeds_for_wordpress_options' );

}

function ebay_feeds_for_wordpress_options() {
  ?>
  <div class="pea_admin_wrap">
    <div class="pea_admin_top">
      <h1><?php echo EBFW_PLUGIN_NAME; ?> <small> - <?php echo EBFW_PLUGIN_TAGLINE; ?></small></h1>
    </div>

    <div class="pea_admin_main_wrap">
      <div class="pea_admin_main_left">
        <div class="pea_admin_signup">
          <?php _e( 'Want to know about updates to this plugin without having to log into your site every time? Want to know about other cool plugins we\'ve made? Add your email and we\'ll add you to our very rare mail outs.' ); ?>

          <!-- Begin MailChimp Signup Form -->
          <div id="mc_embed_signup">
            <form action="https://gospelrhys.us1.list-manage.com/subscribe/post?u=c656fe50ec16f06f152034ea9&id=d9645e38c2" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
              <div class="mc-field-group">
                <label for="mce-EMAIL"><?php _e( 'Email Address', 'ebay-feeds-for-wordpress' ); ?>
                </label>
                <input type="email" value="" name="EMAIL" class="required email" id="mce-EMAIL"><button type="submit" name="subscribe" id="mc-embedded-subscribe" class="pea_admin_green"><?php _e( 'Sign Up!', 'ebay-feeds-for-wordpress' ); ?></button>
              </div>
              <div id="mce-responses" class="clear">
                <div class="response" id="mce-error-response" style="display:none"></div>
                <div class="response" id="mce-success-response" style="display:none"></div>
              </div>  <div class="clear"></div>
            </form>
          </div>

          <!--End mc_embed_signup-->
        </div>

        <form method="post" action="options.php" id="options">

          <?php wp_nonce_field( 'update-options' ); ?>

          <?php settings_fields( 'ebay-feeds-for-wordpress-group' ); ?>

          <table class="form-table">

            <tbody>

              <tr valign="top">

                <th scope="row" style="width:400px"><?php _e( 'Default eBay Feed: ', 'ebay-feeds-for-wordpress' ); ?></th>

                <td><input type="text" name="ebay-feeds-for-wordpress-default" class="regular-text code" value="<?php echo get_option( 'ebay-feeds-for-wordpress-default' ); ?>" /></td>

              </tr>

              <tr valign="top">

                <th scope="row" style="width:400px"><?php _e( 'Default Number of Items To Show:', 'ebay-feeds-for-wordpress' ); ?></th>

                <td><input type="text" name="ebay-feeds-for-wordpress-default-number" class="regular-text code" value="<?php echo get_option( 'ebay-feeds-for-wordpress-default-number' ); ?>" /></td>

              </tr>

              <tr valign="top">

                <th scope="row" style="width:400px"><label><?php _e( 'Open Links In New Window?', 'ebay-feeds-for-wordpress' ); ?></label></th>

                <td><input type="checkbox" name="ebay-feeds-for-wordpress-link-open-blank" value="1"

                  <?php

                  if ( get_option( 'ebay-feeds-for-wordpress-link-open-blank' ) == 1 ) { echo "checked"; } ?>

                  ></td>

                </tr>
                <tr valign="top">

                  <th scope="row" style="width:400px"><label><?php _e( 'Nofollow Links?', 'ebay-feeds-for-wordpress' ); ?></label></th>

                  <td><input type="checkbox" name="ebay-feeds-for-wordpress-nofollow-links" value="1"

                    <?php

                    if ( get_option( 'ebay-feeds-for-wordpress-nofollow-links' ) == 1 ) { echo "checked"; } ?>

                    ></td>

                  </tr>


                  <tr valign="top">

                    <th scope="row" style="width:400px"><label><?php _e( 'Link to us (optional, but appreciated)', 'ebay-feeds-for-wordpress' ); ?></label></th>

                    <td><input type="checkbox" name="ebay-feeds-for-wordpress-link" value="1"

                      <?php

                      if ( get_option( 'ebay-feeds-for-wordpress-link' ) == 1 ) { echo "checked"; } ?>

                      ></td>

                    </tr>

                    <tr valign="top">

                      <th scope="row" style="width:400px"><label><?php _e( 'Switch Debug Mode On?', 'ebay-feeds-for-wordpress' ); ?></label></th>

                      <td><input type="checkbox" name="ebay-feeds-for-wordpress-debug" value="1"

                        <?php

                        if ( get_option( 'ebay-feeds-for-wordpress-debug' ) == 1 ) { echo "checked"; } ?>

                        >

                        <p><em><?php printf( __('Use this to identify problems with the feed or plugin. If switched on, logged in users will be able to see errors of the feed. If you <a href="%s">ask for support</a>, this will be the first thing we ask you to do!</em>', 'ebay-feeds-for-wordpress' ), 'http://winwar.co.uk/priority-support/?utm_source=settings-page&utm_medium=plugin&utm_campaign=ebayfeedsforwordpress' ); ?></p></td>

                      </tr>

                      <tr valign="top">

                        <th scope="row" style="width:400px"><label><?php _e( 'Fallback Text', 'ebay-feeds-for-wordpress' ); ?></label></th>

                        <td>
                          <?php

                          $fallback = get_option( 'ebay_feeds_for_wordpress_fallback' );

                          wp_editor( $fallback, 'ebay_feeds_for_wordpress_fallback' );

                          ?>
                          <em><?php _e( 'If for any reason, the feed doesn\'t work, this will be displayed instead. Use this to link to your eBay shop.', 'ebay-feeds-for-wordpress' ); ?></em>
                        </td>

                      </tr>

                      <tr valign="top">

                        <th scope="row" style="width:400px"><label><?php _e( 'Disable Feed Caching?', 'ebay-feeds-for-wordpress' ); ?></label></th>

                        <td><input type="checkbox" name="ebay-feed-for-wordpress-flush-cache" value="1"

                          <?php

                          if ( get_option( 'ebay-feed-for-wordpress-flush-cache' ) == 1 ) { echo "checked"; } ?>

                          >

                          <p><em><?php _e( 'If enabled, this flushes the cache. Use this if you have the odd problem with the feeds displaying incorrectly', 'ebay-feeds-for-wordpress' ); ?></p></td>

                        </tr>

                      </tbody>

                    </table>



                    <input type="hidden" name="action" value="update" />

                    <input type="hidden" name="page_options" value="ebay-feeds-for-wordpress-default" />

                    <p class="submit">

                      <input type="submit" class="button-primary" value="<?php _e( 'Save Changes' ) ?>" />

                    </p>

                  </form>
                </div>

              </div>
              <div class="pea_admin_main_right">
               <div class="pea_admin_box">
                <h2><?php _e( 'Like this Plugin?', 'ebay-feeds-for-wordpress' ); ?></h2>
                <a href="<?php echo EBFW_EXTEND_URL; ?>" target="_blank"><button type="submit" class="pea_admin_green"><?php _e( 'Rate this plugin &#9733; &#9733; &#9733; &#9733; &#9733;', 'ebay-feeds-for-wordpress' ); ?></button></a><br><br>
                <div id="fb-root"></div>
                <script>(function(d, s, id) {
                  var js, fjs = d.getElementsByTagName(s)[0];
                  if (d.getElementById(id)) return;
                  js = d.createElement(s); js.id = id;
                  js.src = "//connect.facebook.net/en_GB/all.js#xfbml=1&appId=181590835206577";
                  fjs.parentNode.insertBefore(js, fjs);
                }(document, 'script', 'facebook-jssdk'));</script>
                <div class="fb-like" data-href="<?php echo EBFW_PLUGIN_URL; ?>" data-send="true" data-layout="button_count" data-width="250" data-show-faces="true"></div>
                <br>
                <a href="https://twitter.com/share" class="twitter-share-button" data-url="<?php echo EBFW_PLUGIN_URL; ?>" data-text="Just been using <?php echo EBFW_PLUGIN_NAME; ?> #WordPress plugin" data-via="<?php echo EBFW_AUTHOR_TWITTER; ?>" data-related="WPBrewers">Tweet</a>
                <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
                <br>
                <a href="http://bufferapp.com/add" class="buffer-add-button" data-text="Just been using <?php echo EBFW_PLUGIN_NAME; ?> #WordPress plugin" data-url="<?php echo EBFW_PLUGIN_URL; ?>" data-count="horizontal" data-via="<?php echo EBFW_AUTHOR_TWITTER; ?>">Buffer</a><script type="text/javascript" src="http://static.bufferapp.com/js/button.js"></script>
                <br>
                <div class="g-plusone" data-size="medium" data-href="<?php echo EBFW_PLUGIN_URL; ?>"></div>
                <script type="text/javascript">
                window.___gcfg = {lang: 'en-GB'};

                (function() {
                  var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
                  po.src = 'https://apis.google.com/js/plusone.js';
                  var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
                })();
                </script>
                <br>
                <su:badge layout="3" location="<?php echo EBFW_PLUGIN_URL?>"></su:badge>
                <script type="text/javascript">
                (function() {
                  var li = document.createElement('script'); li.type = 'text/javascript'; li.async = true;
                  li.src = ('https:' == document.location.protocol ? 'https:' : 'http:') + '//platform.stumbleupon.com/1/widgets.js';
                  var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(li, s);
                })();
                </script>
              </div>

              <center><a href="<?php echo EBFW_DONATE_LINK; ?>" target="_blank"><img class="paypal" src="<?php echo plugins_url( 'paypal.gif' , __FILE__ ); ?>" width="147" height="47" title="Please Donate - it helps support this plugin!"></a></center>

              <div class="pea_admin_box">
                <h2><?php _e( 'About the Author', 'ebay-feeds-for-wordpress' ); ?></h2>

                <?php

                $size = 70;
                $rhys_url = "http://www.gravatar.com/avatar/" . md5( strtolower( trim( "rhys@rhyswynne.co.uk" ) ) ) . "?s=" . $size;

                ?>

                <p class="pea_admin_clear"><img class="pea_admin_fl" src="<?php echo $rhys_url; ?>" alt="Rhys Wynne" /> <h3>Rhys Wynne</h3><br><a href="https://twitter.com/rhyswynne" class="twitter-follow-button" data-show-count="false">Follow @rhyswynne</a>
                  <div class="fb-subscribe" data-href="https://www.facebook.com/rhysywynne" data-layout="button_count" data-show-faces="false" data-width="220"></div>
                </p>
                <p class="pea_admin_clear"><?php _e( 'Rhys Wynne is the Lead Developer at FireCask and a freelance WordPress developer and blogger. His plugins have had a total of 100,000 downloads, and his premium plugins have generated four figure sums in terms of sales. Rhys likes rubbish football (supporting Colwyn Bay FC) and Professional Wrestling.', 'ebay-feeds-for-wordpress' ); ?></p>
              </div>



            </div>
          </div>

          <?php

        }


function ebay_feeds_for_wordpress_options_process() { // whitelist options

  register_setting( 'ebay-feeds-for-wordpress-group', 'ebay-feeds-for-wordpress-default' );
  register_setting( 'ebay-feeds-for-wordpress-group', 'ebay-feeds-for-wordpress-default-number' );
  register_setting( 'ebay-feeds-for-wordpress-group', 'ebay-feeds-for-wordpress-link' );
  register_setting( 'ebay-feeds-for-wordpress-group', 'ebay-feeds-for-wordpress-link-open-blank' );
  register_setting( 'ebay-feeds-for-wordpress-group', 'ebay-feeds-for-wordpress-nofollow-links' );
  register_setting( 'ebay-feeds-for-wordpress-group', 'ebay_feeds_for_wordpress_fallback' );
  register_setting( 'ebay-feeds-for-wordpress-group', 'ebay-feeds-for-wordpress-debug' );
  register_setting( 'ebay-feeds-for-wordpress-group', 'ebay-feed-for-wordpress-flush-cache' );

}


// Check to see required Widget API functions are defined...

if ( !function_exists( 'register_sidebar_widget' ) || !function_exists( 'register_widget_control' ) )

  return; // ...and if not, exit gracefully from the script.



// This function prints the sidebar widget--the cool stuff!
class ebay_feeds_for_wordpress_Widget_class extends WP_Widget {

  public function __construct() {
    parent::__construct( 'ebay_feeds_for_wordpress_widget', 'eBay Feeds For Wordpress', array( 'description' => 'Widget for an eBay Feed' ) );
  }


  function widget( $args, $instance ) {



    // $args is an array of strings which help your widget
    // conform to the active theme: before_widget, before_title,
    // after_widget, and after_title are the array keys.

    extract( $args );
    extract( $args, EXTR_SKIP );

    $title = empty( $instance['widget_title'] ) ? '' : apply_filters( 'widget_title', $instance['widget_title'] );
    $text = empty( $instance['widget_text'] ) ? '' : $instance['widget_text'];

    if ( empty( $instance['widget_num'] ) && !is_numeric( $instance['widget_num'] ) ) {

      $num = 3;

    } else {

      $num = $instance['widget_num'];

    }

    $feed = empty( $instance['widget_feed'] ) ? get_option( 'ebay-feeds-for-wordpress-default' ) : $instance['widget_feed'];

    echo $before_widget;

    echo $before_title . $title . $after_title;

    echo $text;

    echo ebay_feeds_for_wordpress( $feed, $num );

    echo $after_widget;


  }

  function update( $new_instance, $old_instance ) {
    $instance = $old_instance;
    $instance['widget_title'] = strip_tags( $new_instance['widget_title'] );
    $instance['widget_text'] = strip_tags( $new_instance['widget_text'] );
    $instance['widget_num'] = strip_tags( $new_instance['widget_num'] );
    $instance['widget_feed'] = strip_tags( $new_instance['widget_feed'] );
    return $instance;
  }

  /**
   * admin control form
   */
  function form( $instance ) {
    $default =  array( 'title' => __( 'eBay Items' ) );
    $instance = wp_parse_args( (array) $instance, $default );

    $title_id = $this->get_field_id( 'widget_title' );
    $title_name = $this->get_field_name( 'widget_title' );
    $text_id = $this->get_field_id( 'widget_text' );
    $text_name = $this->get_field_name( 'widget_text' );
    $num_id = $this->get_field_id( 'widget_num' );
    $num_name = $this->get_field_name( 'widget_num' );
    $feed_id = $this->get_field_id( 'widget_feed' );
    $feed_name = $this->get_field_name( 'widget_feed' );

    $title = "";
    $text = "";
    $num = "";
    $feed = "";

    if ( isset( $instance['widget_title'] ) ) { $title = $instance['widget_title']; }
    if ( isset( $instance['widget_text'] ) ) { $text = $instance['widget_text']; }
    if ( isset( $instance['widget_num'] ) ) { $num = $instance['widget_num']; }
    if ( isset( $instance['widget_feed'] ) ) { $feed = $instance['widget_feed']; }

    echo "\r\n".'<p><label for="'.$title_id.'">'.__( 'Title','ebay-feeds-for-wordpress'  ).': <input type="text" class="widefat" id="'.$title_id.'" name="'.$title_name.'" value="'.esc_attr( $title ).'" /><label></p>';
    echo "\r\n".'<p><label for="'.$text_id.'">'.__( 'Text','ebay-feeds-for-wordpress' ).': <input type="text" class="widefat" id="'.$text_id.'" name="'.$text_name .'" value="'.esc_attr( $text ).'" /><label></p>';
    echo "\r\n".'<p><label for="'.$num_id.'">'.__( 'Number of Items','ebay-feeds-for-wordpress'  ).': <input type="text" class="widefat" id="'.$num_id.'" name="'.$num_name .'" value="'.esc_attr( $num ).'" /><label></p>';
    echo "\r\n".'<p><label for="'.$feed_id.'">'.__( 'eBay Feed','ebay-feeds-for-wordpress'  ).': <input type="text" class="widefat" id="'.$feed_id.'" name="'.$feed_name .'" value="'.esc_attr( $feed ).'" /><label></p>';
  }

}

add_action( 'widgets_init', 'ebay_feeds_for_wordpress_Widget' );

function ebay_feeds_for_wordpress_Widget() {
  // curl need to be installed
  register_widget( 'ebay_feeds_for_wordpress_Widget_class' );
}

// Delays plugin execution until Dynamic Sidebar has loaded first.

//add_action('plugins_loaded', 'ebay_feeds_for_wordpress_init');


function ebay_feeds_for_wordpress_addbuttons() {
  // Don't bother doing this stuff if the current user lacks permissions
  if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) )
    return;

  // Add only in Rich Editor mode
  if ( get_user_option( 'rich_editing' ) == 'true' ) {
    add_filter( "mce_external_plugins", "add_ebay_feeds_for_wordpress_tinymce_plugin" );
    add_filter( 'mce_buttons', 'ebay_feeds_for_wordpress_button' );
  }
}

function ebay_feeds_for_wordpress_button( $buttons ) {
  array_push( $buttons, "separator", "ebffwplugin" );
  return $buttons;
}

// Load the TinyMCE plugin : editor_plugin.js (wp2.5)
function add_ebay_feeds_for_wordpress_tinymce_plugin( $plugin_array ) {
  $url = EBAYFEEDSFORWORDPRESS_PLUGIN_URL . "/editor_plugin.js";
  $plugin_array['ebffwplugin'] = $url;
  return $plugin_array;
}

// init process for button control
add_action( 'init', 'ebay_feeds_for_wordpress_addbuttons' );
add_shortcode( 'ebayfeedsforwordpress', 'ebayfeedsforwordpress_shortcode' );

function ebayfeedsforwordpress_shortcode( $atts ) {
  $feed = get_option( 'ebay-feeds-for-wordpress-default' );
  $items = get_option( 'ebay-feeds-for-wordpress-default-number' );

  extract( shortcode_atts( array(
    'feed' => $feed, 'items' => $items
    ), $atts ) );

  $feed = html_entity_decode( $feed );

  $feeddisplay = ebay_feeds_for_wordpress( esc_attr( $feed ), esc_attr( $items ) );
  //wp_die( $feeddisplay );
  return $feeddisplay;
}

function ebay_feeds_for_wordpress_notecho( $dispurls = "", $dispnum = "" ) {

  $link = get_option( "ebay-feeds-for-wordpress-link" );
  $blank = get_option( "ebay-feeds-for-wordpress-link-open-blank" );
  $nofollow = get_option( "ebay-feeds-for-wordpress-nofollow-links" );
  $debug = get_option( "ebay-feeds-for-wordpress-debug" );
  $disprss_items = "";
  $display = "";

  if ( $dispnum == "" || $dispnum == "null" ) {

    $dispnum = get_option( 'ebay-feeds-for-wordpress-default-number' );

  }



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

      $display .= "<h4 class='ebayfeedtitle'><a class='ebayfeedlink' ";

      if ( $blank == "1" ) {
        $display .= "target='_blank' ";
      }

      if ( $nofollow == "1" ) {
        $display .= " rel='nofollow' ";
      }

      $display .= "href='".$dispitem->get_permalink()."'>".$dispitem->get_title()."</a></h4>";

      if ( $blank == "1" ) {
        $display .= $dispitem->get_description();
      } else {
        $newdescription = str_replace( 'target="_blank"', '', $dispitem->get_description() );
        $display .= $newdescription;
      }
    }
  }

  $display .= "</div>";

  if ( $link == 1 ) {
    $display .= __( '<a href="http://winwar.co.uk/plugins/ebay-feeds-wordpress/">eBay Feeds for WordPress</a> by <a href="http://winwar.co.uk/">Winwar Media</a><br/><br/>', 'ebay-feeds-for-wordpress' );
  }


  return $display;

}

function ebay_feeds_for_wordpress_install() {
  add_option( 'ebay-feeds-for-wordpress-default', 'http://rest.ebay.com/epn/v1/find/item.rss?keyword=Ferrari&categoryId1=18180&sortOrder=BestMatch&programid=15&campaignid=5336886189&toolid=10039&listingType1=All&lgeo=1&descriptionSearch=true&feedType=rss' );
  add_option( 'ebay-feeds-for-wordpress-default-number', 3 );
  add_option( 'ebay-feeds-for-wordpress-link', 0 );
  add_option( 'ebay-feeds-for-wordpress-link-open-blank', 0 );
  add_option( 'ebay-feed-for-wordpress-flush-cache', 0 );
}
?>
