(function () {
  const data = {
    action: 'wp_pageviews_add_pageview',
    nonce: wp_pageviews_ajax.nonce
  };

  const $el = document.getElementById('wp-pageviews');

  if ($el) {
    fetch(wp_pageviews_ajax.ajax_url, {
      method: "POST",
      cache: "no-cache",
      headers: {
        "Content-Type": "application/json"
      },
      credentials: 'same-origin',
      body: JSON.stringify(data),
    })
    .then((response) => {
      console.log('[WP Pageviews Plugin]');
      console.log('Pageview recorded');

      if (response) {
        $el.innerText = response;
      }
    })
    .catch((error) => {
      console.log('[WP Pageviews Plugin]');
      console.error(error);
    });
  }
}());
