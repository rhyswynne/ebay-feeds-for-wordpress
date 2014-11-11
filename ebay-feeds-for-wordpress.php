<?php
/*
Plugin Name:  Ebay Feeds for WordPress
Plugin URI:   http://winwar.co.uk/plugins/ebay-feeds-wordpress/
Description:  Parser of ebay RSS feeds to display on Wordpress posts, widgets and pages.
Version:      1.5.1
Author:       Winwar Media
Author URI:   http://winwar.co.uk/

*/

define( "EBFW_PLUGIN_NAME", "eBay Feeds For WordPress" );
define( "EBFW_PLUGIN_TAGLINE", "Parser of ebay RSS feeds to display on Wordpress posts, widgets and pages." );
define( "EBFW_PLUGIN_URL", "http://winwar.co.uk/plugins/ebay-feeds-wordpress/" );
define( "EBFW_EXTEND_URL", "http://wordpress.org/extend/plugins/ebay-feeds-for-wordpress/" );
define( "EBFW_AUTHOR_TWITTER", "rhyswynne" );
define( "EBFW_DONATE_LINK", "https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=F852ZPEANV7C6" );

register_activation_hook( __FILE__, 'ebay_feeds_for_wordpress_install' );

function ebay_feeds_for_wordpress( $url = "", $num = "" ) {

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

  $rss = fetch_feed( $url );

  if ( !is_wp_error( $rss ) ) {
    $rss->enable_order_by_date( false );
    $rss_items = $rss->get_items( 0, $num );
  } else {
    if ( current_user_can('manage_options') && 1 == $debug ) {

        $error_string = $rss->get_error_message();
        echo '<div id="message" class="error"><p>' . $error_string . '</p></div>';

      }
  }

  echo "<div class='ebayfeed'>";

  if ( !is_wp_error( $rss ) && $rss_items ) {

    foreach ( $rss_items as $item ) {

      echo "<h4 class='ebayfeedtitle'><a ";

      if ( $blank == "1" ) {

        echo "target='_blank' ";

      }

      if ( $nofollow == "1" ) {

        echo " rel='nofollow' ";

      }

      echo "href='".$item->get_permalink()."'  class='ebayfeedlink'>".$item->get_title()."</a></h4>";

      if ( $blank == "1" ) {

        echo $item->get_description();

      } else {

        $newdescription = str_replace( 'target="_blank"', '', $item->get_description() );
        echo $newdescription;

      }

    }
  }

  echo "</div>";

  if ( $link == 1 ) {

    echo "<a href='http://winwar.co.uk/plugins/ebay-feeds-wordpress/'>eBay Feeds for WordPress</a> by <a href='http://winwar.co.uk'>Winwar Media</a><br/><br/>";

  }
}

if ( is_admin() ) { // admin actions

  add_action( 'admin_menu', 'ebay_feeds_for_wordpress_menus' );
  add_action( 'admin_init', 'ebay_feeds_for_wordpress_options_process' );
  add_action( 'admin_init', 'ebay_feeds_for_wordpress_add_admin_stylesheet' );

}

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
          Want to know about updates to this plugin without having to log into your site every time? Want to know about other cool plugins we've made? Add your email and we'll add you to our very rare mail outs.

          <!-- Begin MailChimp Signup Form -->
          <div id="mc_embed_signup">
            <form action="http://peadig.us5.list-manage2.com/subscribe/post?u=e16b7a214b2d8a69e134e5b70&amp;id=eb50326bdf" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
              <div class="mc-field-group">
                <label for="mce-EMAIL">Email Address
                </label>
                <input type="email" value="" name="EMAIL" class="required email" id="mce-EMAIL"><button type="submit" name="subscribe" id="mc-embedded-subscribe" class="pea_admin_green">Sign Up!</button>
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

                <th scope="row" style="width:400px">Default eBay Feed:</th>

                <td><input type="text" name="ebay-feeds-for-wordpress-default" class="regular-text code" value="<?php echo get_option( 'ebay-feeds-for-wordpress-default' ); ?>" /></td>

              </tr>

              <tr valign="top">

                <th scope="row" style="width:400px">Default Number of Items To Show:</th>

                <td><input type="text" name="ebay-feeds-for-wordpress-default-number" class="regular-text code" value="<?php echo get_option( 'ebay-feeds-for-wordpress-default-number' ); ?>" /></td>

              </tr>

              <tr valign="top">

                <th scope="row" style="width:400px"><label>Open Links In New Window?</label></th>

                <td><input type="checkbox" name="ebay-feeds-for-wordpress-link-open-blank" value="1"

                  <?php

                  if ( get_option( 'ebay-feeds-for-wordpress-link-open-blank' ) == 1 ) { echo "checked"; } ?>

                  ></td>

                </tr>
                <tr valign="top">

                  <th scope="row" style="width:400px"><label>Nofollow Links?</label></th>

                  <td><input type="checkbox" name="ebay-feeds-for-wordpress-nofollow-links" value="1"

                    <?php

                    if ( get_option( 'ebay-feeds-for-wordpress-nofollow-links' ) == 1 ) { echo "checked"; } ?>

                    ></td>

                  </tr>


                  <tr valign="top">

                    <th scope="row" style="width:400px"><label>Link to us (optional, but appreciated)</label></th>

                    <td><input type="checkbox" name="ebay-feeds-for-wordpress-link" value="1"

                      <?php

                      if ( get_option( 'ebay-feeds-for-wordpress-link' ) == 1 ) { echo "checked"; } ?>

                      ></td>

                    </tr>

                    <tr valign="top">

                      <th scope="row" style="width:400px"><label>Switch Debug Mode On?</label></th>

                      <td><input type="checkbox" name="ebay-feeds-for-wordpress-debug" value="1"

                        <?php

                        if ( get_option( 'ebay-feeds-for-wordpress-debug' ) == 1 ) { echo "checked"; } ?>

                        >

                        <p><em>Use this to identify problems with the feed or plugin. If switched on, logged in users will be able to see errors of the feed. If you <a href="http://winwar.co.uk/priority-support/">ask for support</a>, this will be the first thing we ask you to do!</em></p></td>

                      </tr>

                      <tr valign="top">

                        <th scope="row" style="width:400px"><label>Fallback Text</label></th>

                        <td>
                          <?php

                          $fallback = get_option( 'ebay_feeds_for_wordpress_fallback' );

                          wp_editor( $fallback, 'ebay_feeds_for_wordpress_fallback' );

                          ?>
                          <em>If for any reason, the feed doesn't work, this will be displayed instead. Use this to link to your eBay shop.</em>
                        </td>

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
              <h2>Like this Plugin?</h2>
              <a href="<?php echo EBFW_EXTEND_URL; ?>" target="_blank"><button type="submit" class="pea_admin_green">Rate this plugin &#9733; &#9733; &#9733; &#9733; &#9733;</button></a><br><br>
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
              <h2>About the Author</h2>

              <?php
              
              $size = 70;
              $rhys_url = "http://www.gravatar.com/avatar/" . md5( strtolower( trim( "rhys@rhyswynne.co.uk" ) ) ) . "?s=" . $size;
              
              ?>

              <p class="pea_admin_clear"><img class="pea_admin_fl" src="<?php echo $rhys_url; ?>" alt="Rhys Wynne" /> <h3>Rhys Wynne</h3><br><a href="https://twitter.com/rhyswynne" class="twitter-follow-button" data-show-count="false">Follow @rhyswynne</a>
                <div class="fb-subscribe" data-href="https://www.facebook.com/rhysywynne" data-layout="button_count" data-show-faces="false" data-width="220"></div>
              </p>
              <p class="pea_admin_clear">Rhys Wynne is the Lead Developer at FireCask and a freelance WordPress developer and blogger. His plugins have had a total of 100,000 downloads, and his premium plugins have generated four figure sums in terms of sales. Rhys likes rubbish football (supporting Colwyn Bay FC) and Professional Wrestling.</p>
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

}


// Check to see required Widget API functions are defined...

if ( !function_exists( 'register_sidebar_widget' ) || !function_exists( 'register_widget_control' ) )

  return; // ...and if not, exit gracefully from the script.



// This function prints the sidebar widget--the cool stuff!
class ebay_feeds_for_wordpress_Widget_class extends WP_Widget {

  function ebay_feeds_for_wordpress_Widget_class() {
    parent::WP_Widget( 'ebay_feeds_for_wordpress_widget', 'eBay Feeds For Wordpress', array( 'description' => 'Widget for an eBay Feed' ) );
  }


  function widget( $args, $instance ) {



    // $args is an array of strings which help your widget
    // conform to the active theme: before_widget, before_title,
    // after_widget, and after_title are the array keys.

    extract( $args );
    extract( $args, EXTR_SKIP );

    $title = empty( $instance['widget_title'] ) ? 'eBay Items' : apply_filters( 'widget_title', $instance['widget_title'] );
    $text = empty( $instance['widget_text'] ) ? 'Here are our eBay auctions' : $instance['widget_text'];
    
    if ( empty( $instance['widget_num'] ) && !is_numeric( $instance['widget_num'] ) ) {

      $num = 3;

    } else {

      $num = $instance['widget_num'];

    }
    
    $feed = empty( $instance['widget_feed'] ) ? get_option( 'ebay-feeds-for-wordpress-default' ) : $instance['widget_feed'];

    echo $before_widget;

    echo $before_title . $title . $after_title;

    echo $text;

    ebay_feeds_for_wordpress( $feed, $num );

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

    echo "\r\n".'<p><label for="'.$title_id.'">'.__( 'Title' ).': <input type="text" class="widefat" id="'.$title_id.'" name="'.$title_name.'" value="'.esc_attr( $title ).'" /><label></p>';
    echo "\r\n".'<p><label for="'.$text_id.'">'.__( 'Text' ).': <input type="text" class="widefat" id="'.$text_id.'" name="'.$text_name .'" value="'.esc_attr( $text ).'" /><label></p>';
    echo "\r\n".'<p><label for="'.$num_id.'">'.__( 'Number of Items' ).': <input type="text" class="widefat" id="'.$num_id.'" name="'.$num_name .'" value="'.esc_attr( $num ).'" /><label></p>';
    echo "\r\n".'<p><label for="'.$feed_id.'">'.__( 'eBay Feed' ).': <input type="text" class="widefat" id="'.$feed_id.'" name="'.$feed_name .'" value="'.esc_attr( $feed ).'" /><label></p>';
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
  $url = trim( get_bloginfo( 'url' ), "/" )."/wp-content/plugins/ebay-feeds-for-wordpress/editor_plugin.js";
  $plugin_array['ebffwplugin'] = $url;
  return $plugin_array;
}

// init process for button control
add_action( 'init', 'ebay_feeds_for_wordpress_addbuttons' );

add_shortcode( 'ebayfeedsforwordpress', 'ebayfeedsforwordpress_shortcode' );

function ebayfeedsforwordpress_shortcode( $atts ) {
  $url = get_option( 'ebay-feeds-for-wordpress-default' );
  $num = get_option( 'ebay-feeds-for-wordpress-default-number' );
  extract( shortcode_atts( array(
    'feed' => $url, 'items' => $num
    ), $atts ) );

  $feeddisplay = ebay_feeds_for_wordpress_notecho( esc_attr( $feed ), esc_attr( $items ) );

  return $feeddisplay;
}

function ebay_feeds_for_wordpress_notecho( $dispurl = "", $dispnum = "" ) {

  $link = get_option( "ebay-feeds-for-wordpress-link" );
  $blank = get_option( "ebay-feeds-for-wordpress-link-open-blank" );
  $nofollow = get_option( "ebay-feeds-for-wordpress-nofollow-links" );
  $debug = get_option("ebay-feeds-for-wordpress-debug");
  $disprss_items = "";
  $display = "";

  if ( $dispnum == "" || $dispnum == "null" ) {

    $dispnum = get_option( 'ebay-feeds-for-wordpress-default-number' );

  }

  if ( $dispurl == "" || $dispurl == "null" ) {

    $dispurl = get_option( 'ebay-feeds-for-wordpress-default' );
    $disprss = fetch_feed( $dispurl );

    if ( $disprss ) {

      $disprss_items = $disprss->get_items( 0, $dispnum );

    } else {

      $fallback = get_option( 'ebay_feeds_for_wordpress_fallback' );

      $display .=  "<div class='ebayfeed'>";
      $display .= $fallback;
      $display .=  "</div>";
    }

  } else {
    $dispurl = str_replace( "&amp;", "&", $dispurl );
    $disprss = fetch_feed( $dispurl );

    if ( !is_wp_error( $disprss ) ) {

      $disprss_items = $disprss->get_items( 0, $dispnum );

    } else {

      if ( current_user_can('manage_options') && 1 == $debug ) {

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
    $display .= "<a href='http://winwar.co.uk/plugins/ebay-feeds-wordpress/'>eBay Feeds for WordPress</a> by <a href='http://winwar.co.uk/'>Winwar Media</a><br/><br/>";
  }


  return $display;

}

function ebay_feeds_for_wordpress_install() {
  add_option( 'ebay-feeds-for-wordpress-default', 'http://rest.ebay.com/epn/v1/find/item.rss?keyword=Ferrari&categoryId1=18180&sortOrder=BestMatch&programid=15&campaignid=5336886189&toolid=10039&listingType1=All&lgeo=1&descriptionSearch=true&feedType=rss' );
  add_option( 'ebay-feeds-for-wordpress-default-number', 3 );
  add_option( 'ebay-feeds-for-wordpress-link', 0 );
  add_option( 'ebay-feeds-for-wordpress-link-open-blank', 0 );
}

?>
