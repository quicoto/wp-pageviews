# wp-pageviews

Store post/page pageviews

## Markup

In the single post view:

```php
<?php if (!is_user_logged_in()){ ?>
  <span data-postid="<?=get_the_ID()?>" hidden id="wp-pageviews"></span>
<?php } ?>
```

In the footer

```php
<div class="footer-stats">
  <?php
    $page_views = $wpdb->get_results( "SELECT SUM(meta_value) as total FROM wp_postmeta WHERE meta_key LIKE '_pageviews' LIMIT 0,1" );
  ?>
  <strong class="wp-pageviews-total-stats"><?=number_format($page_views[0]->total, 0, '.', ',')?></strong> views
</div>
```