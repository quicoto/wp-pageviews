<?php
/*
Plugin Name: WP Pageviews
Plugin URI: https://php.quicoto.com/
Description: Store post / page Pageviews
Version: 1.0.0
Author: quicoto
Author URI: https://ricard.blog/
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*-----------------------------------------------------------------------------------*/
/* Define the URL */
/*-----------------------------------------------------------------------------------*/

define('wp_pageviews_url', plugins_url() ."/".dirname( plugin_basename( __FILE__ ) ) );

/*-----------------------------------------------------------------------------------*/
/* Add Actions */
/*-----------------------------------------------------------------------------------*/

add_action('admin_head', 'wp_pageviews_display_pageviews_style');
add_action('manage_posts_custom_column', 'wp_pageviews_display_pageviews_row', 10, 2);
add_filter('manage_pages_columns', 'wp_pageviews_display_pageviews');
add_filter('manage_posts_columns', 'wp_pageviews_display_pageviews');

/*-----------------------------------------------------------------------------------*/
/* Admin styles */
/*-----------------------------------------------------------------------------------*/

function wp_pageviews_display_pageviews_style(){ ?>
  <style type="text/css">
    .column-pv{width:60px;}
  </style>
<?php
}

function wp_pageviews_display_pageviews($columns){
  $columns['pv'] = __('PV');

  return $columns;
}

function wp_pageviews_display_pageviews_row($column_name, $post_id){
  if ($column_name != 'pv') return;
  $pv = get_post_meta($post_id, '_pageviews', true);
  echo $pv ? $pv : 0;
}

/*-----------------------------------------------------------------------------------*/
/* Core functions */
/*-----------------------------------------------------------------------------------*/

if  ( ! function_exists( 'wp_pageviews_count_pageview_callback' ) ){

  function wp_pageviews_count_pageview_callback(){
    // Check the nonce - security
    check_ajax_referer( 'wp-pageviews-nonce', 'nonce' );

    if (is_single() && !is_user_logged_in()){
      global $post;

      $pv = intval(get_post_meta($post->ID, '_pageviews', true));

      $newValue = $pv + 1;

      update_post_meta($post->ID, '_pageviews', $newValue);

      die(number_format( $newValue , 0 , ",", "."));
    }
  }

  add_action('wp_ajax_wp_pageviews_add_pageview', 'wp_pageviews_count_pageview_callback');
  add_action('wp_ajax_nopriv_wp_pageviews_add_pageview', 'wp_pageviews_count_pageview_callback');
}

/*-----------------------------------------------------------------------------------*/
/* Encue the Scripts for the Ajax call */
/*-----------------------------------------------------------------------------------*/

if  ( ! function_exists( 'wp_pageviews_scripts' ) ){

  function wp_pageviews_scripts() {
    if(is_single() && !is_user_logged_in()){
      wp_enqueue_script('wp_pageviews_scripts', wp_pageviews_url . '/main.js', '', '1.0.7', true);
      wp_localize_script( 'wp_pageviews_scripts', 'wp_pageviews_ajax', array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'nonce' => wp_create_nonce( 'wp-pageviews-nonce' ) ) );
    }
  }

  add_action('wp_enqueue_scripts', 'wp_pageviews_scripts');
}
