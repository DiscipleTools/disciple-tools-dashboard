(function($) {
  $( "#sort-classes" ).sortable({
    handle: ".handle",
    stop: function( event, ui ) {
      var sort = $("#sort-classes").children('tr.tile').toArray().map(function(el) {
        return el.dataset.tileHandle
      });
      $.ajax({
        url: window.dashboardWPApiShare.root + '/v1/tiles/sort',
        type: 'PUT',
        data: {
          tile_sort: sort
        },
        beforeSend: xhr => {
          xhr.setRequestHeader('X-WP-Nonce', window.dashboardWPApiShare.nonce);
        }
      });
    }
  })
})(window.jQuery)
