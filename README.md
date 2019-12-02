# wp-pageviews

Store post/page pageviews

## Markup

```
<?php if (!is_user_logged_in()){ ?>
  <span id="wp-pageviews" data-postid="<?=get_the_ID()?>"></span> views
<?php } ?>
```