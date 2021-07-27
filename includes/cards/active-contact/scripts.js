(function($) {
  window.dt_dashboard.onAdd('DT_Dashboard_Plugin_Active_Contact', function (context) {
    let data = context.wpApiDashboard.data
    $(context.element).find('#active_contacts').html(data.active_contacts)
  })
})(window.jQuery)
