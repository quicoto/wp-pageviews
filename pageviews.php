<?php
/*
Plugin Name: WP Pageviews
Plugin URI: https://php.quicoto.com/
Description: Store post / page Pageviews
Version: 2.0.4
Author: quicoto
Author URI: https://ricard.blog/
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$path = $_SERVER['DOCUMENT_ROOT'];

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
    global $wpdb;

    // Check the nonce - security
    // We can not have security check if the page is cached.
    // RATIONALE: I would rather have this off and potentially let others add page views
    // tha have no pageviews at all.
    // Alternative solution is to lower the time the page is cached. But it defeats purpose.
    // check_ajax_referer( 'wp-pageviews-nonce', 'nonce' );

    if ($_POST['is_single'] && !$_POST['is_user_logged_in']){
      $post_ID = intval( $_POST['postid'] );

      $pv = intval(get_post_meta($post_ID, '_pageviews', true));

      $newValue = $pv + 1;

      update_post_meta($post_ID, '_pageviews', $newValue);

      // Return the total count
      $page_views = $wpdb->get_results( "SELECT SUM(meta_value) as total FROM wp_postmeta WHERE meta_key LIKE '_pageviews' LIMIT 0,1" );

      $response = array('page_views' => number_format($page_views[0]->total, 0, '.', ','));

      die(json_encode($response));
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
      wp_enqueue_script('wp_pageviews_scripts', wp_pageviews_url . '/main.js', '', '2.0.4', true);
      wp_localize_script( 'wp_pageviews_scripts', 'wp_pageviews_ajax', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce' => wp_create_nonce( 'wp-pageviews-nonce' ),
        'is_user_logged_in' => is_user_logged_in(),
        'is_single' => is_single()
      ) );
    }
  }

  add_action('wp_enqueue_scripts', 'wp_pageviews_scripts');
}
