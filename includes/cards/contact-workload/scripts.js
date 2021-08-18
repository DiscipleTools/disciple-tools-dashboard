(function($) {
  window.dt_dashboard.onAdd('DT_Dashboard_Plugin_Contact_Workload', function (context) {
    let status_buttons =  $(context.element).find('.status-button')
    let color_workload_buttons = (name) =>{
      // status_buttons.css('background-color', "")
      status_buttons.addClass("hollow")
      if ( name ){
        let selected =  $(context.element).find(`.status-button[name=${name}]`)
        selected.removeClass("hollow")
        // selected.css('background-color', window.lodash.get(context.wpApiDashboard, `workload_status_options.${name}.color`))
        selected.blur()
      }
    }
    color_workload_buttons(context.wpApiDashboard.workload_status )
    status_buttons.on( 'click', function () {
      $(context.element).find("#workload-spinner").addClass("active")
      let name = $(this).attr('name')
      color_workload_buttons(name)
      let data = { 'workload_status': name };
      makeRequest( "post", `user`, data , 'dt-dashboard/v1/')
        .then(()=>{
          $(context.element).find("#workload-spinner").removeClass("active")
        }).fail(()=>{
        // status_buttons.css('background-color', "")
        $(context.element).find("#workload-spinner").removeClass("active")
        status_buttons.addClass("hollow")
      })
    })
  })
})(window.jQuery)



