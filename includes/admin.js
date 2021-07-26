(function($) {
  $( "#sort-classes" ).sortable({
    handle: ".handle",
    stop: function( event, ui ) {
      var sort = $("#sort-classes").children('tr.card').toArray().map(function(el) {
        return el.dataset.cardHandle
      });
      $.ajax({
        url: window.dashboardWPApiShare.root + '/v1/cards/sort',
        type: 'PUT',
        data: {
          card_sort: sort
        },
        beforeSend: xhr => {
          xhr.setRequestHeader('X-WP-Nonce', window.dashboardWPApiShare.nonce);
        }
      });
    }
  })
})(window.jQuery)
