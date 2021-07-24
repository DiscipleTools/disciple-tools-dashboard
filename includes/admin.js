(function($) {
  $( "#sort-classes" ).sortable({
    handle: ".handle",
    change: function( event, ui ) {
      var order = $("#sort-classes").children('tr.card').toArray().map(function(el) {
        return el.dataset.cardHandle
      })
      console.log(order)
    }
  })
})(window.jQuery)
