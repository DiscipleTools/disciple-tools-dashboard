(function($) {
  window.dt_dashboard.onAdd('DT_Dashboard_Plugin_Faith_Milestone_Totals', function (context) {
    let data = context.wpApiDashboard.data
    let wpApiDashboard = context.wpApiDashboard

    const options = {
      type: 'GET',
      contentType: 'application/json; charset=utf-8',
      dataType: 'json',
      url: `${window.wpApiShare.root}dt-dashboard/v1/milestones`,
      beforeSend: xhr => {
        xhr.setRequestHeader('X-WP-Nonce', window.wpApiShare.nonce);
      }
    }

    $.ajax(options).then(resp=>{
      $(context.element).find(".stats-spinner").removeClass("active")
      data.milestones = resp
      milestones()
    })

    function milestones() {
      let milestones = ``
      data.milestones.forEach( m=>{
        milestones += `<div class="group-progress-button-wrapper">
        <button class="group-progress-button"> ${window.lodash.escape( m.value )} </button>
        <p>${window.lodash.escape( m.milestones )}</p>
      </div>`
      })
      $(context.element).find("#milestones").html(milestones)
    }

    // smooth scrollbar
    let Scrollbar = window.Scrollbar
    Scrollbar.init(document.querySelector('#faith-milestone-totals_scrollbar'))

  })
})(window.jQuery)
