<?php
/*
Plugin Name: WP Pageviews
Plugin URI: https://php.quicoto.com/
Description: Store post / page Pageviews
Version: 1.0.0
Author: quicoto
Author URI: https://php.quicoto.com/
*/

add_action('admin_head','display_pageviews_style');
add_action('manage_posts_custom_column','display_pageviews_row',10,2);
add_filter('manage_pages_columns', 'display_pageviews');
add_filter('manage_posts_columns', 'display_pageviews');

function display_pageviews_style(){ ?>
  <style type="text/css">
    .column-pv{width:60px;}
  </style>
<?php
}

function es_pageviews(){
  if(is_single() && !is_user_logged_in()){
    global $post;

    $pv = get_post_meta($post->ID, '_pageviews',true);
    update_post_meta($post->ID, '_pageviews', $pv+1);
  }
}

function display_pageviews($columns){
  $columns['pv'] = __('PV');

  return $columns;
}

function display_pageviews_row($column_name,$post_id){
  if ($column_name != 'pv') return;
  $pv = get_post_meta($post_id, '_pageviews',true);
  echo $pv ? $pv : 0;
}

if(!function_exists('the_pageview')){
  function the_pageview(){
    global $post;

    $pv = get_post_meta($post->ID, '_pageviews',true);
    $pv = number_format( $pv , 0 , ",", ".");
    echo $pv ? $pv : 0;
  }
}
