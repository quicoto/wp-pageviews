(function () {
  const $el = document.getElementById('wp-pageviews');

  if ($el && $el.dataset.postid) {
    var data = new FormData();

    data.append( 'action', 'wp_pageviews_add_pageview' );
    data.append( 'nonce', wp_pageviews_ajax.nonce );
    data.append( 'postid', $el.dataset.postid);

    fetch(wp_pageviews_ajax.ajax_url, {
      method: "POST",
      credentials: 'same-origin',
      body: data
    })
    .then((resp) => resp.json())
    .then(function(data) {
      if(data.status == "success"){
        console.log('[WP Pageviews Plugin]');
        console.log('Pageview recorded');

        if (response) {
          $el.innerText = response;
        }
      }
    })
    .catch((error) => {
      console.log('[WP Pageviews Plugin]');
      console.error(error);
    });
  }
}());
