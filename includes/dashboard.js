(function($, wpApiDashboard) {
  let data = wpApiDashboard.data

  // update_needed();

  $('#active_contacts').html(data.active_contacts)
  $('#needs_accepting').html(data.accept_needed.total)
  let needs_accepting_list = ``
  data.accept_needed.posts.slice(0, 3).forEach( contact =>{
    needs_accepting_list += `<div style="margin-top:10px; display: flex" id="pending-${window.lodash.escape(contact.ID)}">
        <div style="display: inline-block; margin-left: 10px; vertical-align: middle; flex-grow: 1"><i class="fi-torso medium"></i>
            <a style="font-size: 1.1rem" href="${wpApiDashboard.site_url}/contacts/${window.lodash.escape( contact.ID )}">${window.lodash.escape(
      contact.post_title)}</a>
        </div>
        <div>
            <button class="button small dt-green accept_contact_button" data-id="${window.lodash.escape( contact.ID )}" data-action="accept" style="color: white; margin-bottom: 0">${window.lodash.escape(wpApiDashboard.translations.accept)}</button>
            <button class="button small accept_contact_button" data-id="${window.lodash.escape( contact.ID )}" data-action="decline" style="background-color: #f43636; color: white; margin-bottom: 0">${window.lodash.escape(wpApiDashboard.translations.decline)}</button>
        </div>
    </div>`
  })
  if ( !needs_accepting_list ){
    needs_accepting_list = `<p style="margin-top:40px; font-size: 1.1rem; text-align: center">${window.lodash.escape(wpApiDashboard.translations.caught_up)}</p>`
  }
  $('#needs_accepting_list').html( needs_accepting_list )


  $( document ).on('click', '.accept_contact_button', function () {
    let contact_id = $(this).data('id')
    let data = {accept: $(this).data('action') === 'accept'}
    makeRequestOnPosts( "POST", `contacts/${contact_id}/accept`, data)
      .then(function (resp) {
        $(`#pending-${window.lodash.escape( contact_id )}`).remove()
        let total_pending_html = $('#needs_accepting')
        let total = parseInt( total_pending_html.html() ) - 1;
        total_pending_html.html(total)
      })
  })

  /**
   * Update Needed
   */
  $('#update_needed').html(data.update_needed.total)
  let up_list = ``
  data.update_needed.posts.slice(0, 3).forEach( contact =>{
    let row = `<div style="margin-top:5px">
        <div style="display: inline-block; margin-left: 10px"><i class="fi-torso medium"></i>
            <a style="font-size: 1.1rem" href="${wpApiDashboard.site_url}/contacts/${window.lodash.escape(contact.ID)}">${window.lodash.escape( contact.post_title ) }</a>
            <br>
            <span style="font-size: 0.9rem">${window.lodash.escape(contact.last_modified_msg)}</span>
        </div>
    </div>`
    up_list += row
  })
  if ( !up_list ){
    up_list = `<p style="margin-top:40px; font-size: 1.1rem; text-align: center">${window.lodash.escape(wpApiDashboard.translations.caught_up)}</p>`
  }
  $('#update_needed_list').html( up_list)

  $('#view_updated_needed_button').on( "click", function () {
    document.location = `${wpApiDashboard.site_url}/contacts?list-tab=update_needed`
  })
  $('#view_needs_accepted_button').on( "click", function () {
    document.location = `${wpApiDashboard.site_url}/contacts?list-tab=needs_accepted`
  })

  const options = {
      type: 'GET',
      contentType: 'application/json; charset=utf-8',
      dataType: 'json',
      url: `${window.wpApiShare.root}dt-dashboard/v1/stats`,
      beforeSend: xhr => {
          xhr.setRequestHeader('X-WP-Nonce', window.wpApiShare.nonce);
      }
  }
  $.ajax(options).then(resp=>{
    $(".stats-spinner").removeClass("active")
    window.lodash.merge(data, resp)
    benchmarks_chart()
    seeker_path_chart()
    milestones()
    build_tasks()
  })


  let status_buttons = $('.status-button')
  let color_workload_buttons = (name) =>{
    status_buttons.css('background-color', "")
    status_buttons.addClass("hollow")
    if ( name ){
      let selected = $(`.status-button[name=${name}]`)
      selected.removeClass("hollow")
      selected.css('background-color', window.lodash.get(wpApiDashboard, `workload_status_options.${name}.color`))
      selected.blur()
    }
  }
  color_workload_buttons(wpApiDashboard.workload_status )
  status_buttons.on( 'click', function () {
    $("#workload-spinner").addClass("active")
    let name = $(this).attr('name')
    color_workload_buttons(name)
    let data = { 'workload_status': name };
    makeRequest( "post", `user`, data , 'dt-dashboard/v1/')
    .then(()=>{
      $("#workload-spinner").removeClass("active")
    }).fail(()=>{
      status_buttons.css('background-color', "")
      $("#workload-spinner").removeClass("active")
      status_buttons.addClass("hollow")
    })
  })


  function benchmarks_chart() {
    let thirty_days_ago = moment().add( -30, "days")
    let sixty_days_ago = moment().add( -60, "days")
    $('#benchmarks_current').html(`${thirty_days_ago.format("MMMM D, YYYY")} to ${moment().format("MMMM D, YYYY")}`)
    $('#benchmarks_previous').html(`${sixty_days_ago.format("MMMM D, YYYY")} to ${thirty_days_ago.format("MMMM D, YYYY")}`)

    am4core.useTheme(am4themes_animated);
    let chart = am4core.create("benchmark_chart", am4charts.XYChart);

    chart.data = [ {
      "year": window.lodash.escape(wpApiDashboard.translations.number_contacts_assigned),
      "previous": data.benchmarks.contacts.previous,
      "current": data.benchmarks.contacts.current
    }, {
      "year": window.lodash.escape(wpApiDashboard.translations.number_meetings),
      "previous": data.benchmarks.meetings.previous,
      "current": data.benchmarks.meetings.current
    }, {
      "year": window.lodash.escape(wpApiDashboard.translations.number_milestones),
      "previous": data.benchmarks.milestones.previous,
      "current": data.benchmarks.milestones.current
    } ];

    let categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
    categoryAxis.dataFields.category = "year";
    // categoryAxis.title.text = "Local country offices";
    categoryAxis.renderer.grid.template.location = 0;
    categoryAxis.renderer.minGridDistance = 20;
    categoryAxis.renderer.cellStartLocation = 0.1;
    categoryAxis.renderer.cellEndLocation = 0.9;
    categoryAxis.renderer.grid.template.disabled = true;



    let valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
    valueAxis.min = 0;
    // valueAxis.title.text = "Expenditure (M)";
    valueAxis.renderer.grid.template.disabled = true;
    valueAxis.renderer.labels.template.disabled = true;


    // Create series
    function createSeries(field, name, stacked) {
      let series = chart.series.push(new am4charts.ColumnSeries());
      series.dataFields.valueY = field;
      series.dataFields.categoryX = "year";
      series.name = name;
      series.columns.template.tooltipText = "{name}: [bold]{valueY}[/]";
      series.stacked = stacked;
      series.columns.template.width = am4core.percent(95);
      let valueLabel = series.bullets.push(new am4charts.LabelBullet());
      valueLabel.label.text = "{valueY}";
      valueLabel.label.dy = -10;
      valueLabel.label.hideOversized = false;
      valueLabel.label.truncate = false;
    }
    chart.colors.list = [
      am4core.color("#C7E3FF"),
      am4core.color("#3f729b"),
    ];

    createSeries("previous", "Previous", false);
    createSeries("current", "Current", false);

  }


  function seeker_path_chart() {
    am4core.useTheme(am4themes_animated);

    let chart = am4core.create("seeker_path_chart", am4charts.PieChart);
    chart.hiddenState.properties.opacity = 0; // this makes initial fade in effect
    //remove empty values
    window.lodash.pullAllBy( data.seeker_path_personal, [{ value: 0 }], "value" )
    chart.data = data.seeker_path_personal
    if ( window.lodash.isEmpty( chart.data ) ){
      $('#empty_seeker_path').show()
    }


    chart.radius = am4core.percent(70);
    chart.innerRadius = am4core.percent(40);
    chart.startAngle = 180;
    chart.endAngle = 360;

    let series = chart.series.push(new am4charts.PieSeries());
    series.dataFields.value = "value";
    series.dataFields.category = "label";

    series.slices.template.cornerRadius = 10;
    series.slices.template.innerCornerRadius = 7;
    series.slices.template.draggable = true;
    series.slices.template.inert = true;
    series.alignLabels = false;
    series.slices.minHorizontalGap = 0;

    series.hiddenState.properties.startAngle = 90;
    series.hiddenState.properties.endAngle = 90;

    // series.labels.template.text = "[font-size: 12px]{category}: {value}[/]";
    series.labels.template.text = "{value}";
    series.labels.template.wrap = true
    series.labels.template.maxWidth = 90

    series.colors.list = [
      am4core.color("#C7E3FF"),
      am4core.color("#B7D6F3"),
      am4core.color("#A8C9E8"),
      am4core.color("#99BDDD"),
      am4core.color("#8AB0D2"),
      am4core.color("#7BA4C7"),
      am4core.color("#6C97BC"),
      am4core.color("#5D8BB1"),
      am4core.color("#4E7EA6"),
      am4core.color("#3F729B"),
    ]

    chart.legend = new am4charts.Legend();
    chart.legend.valueLabels.template.text = "";
    chart.legend.labels.template.text = "[font-size: 10px]{category}: {value}[/]";

  }

  function milestones() {
    let milestones = ``


    data.milestones.forEach( m=>{
      milestones += `<div class="group-progress-button-wrapper" style="flex-basis: 33%">
        <button style="color: white" class="group-progress-button"> ${window.lodash.escape( m.value )} </button>
        <p>${window.lodash.escape( m.milestones )}</p>
      </div>`
    })
    $("#milestones").html(milestones)

  }

  function build_tasks() {
    let tasks = window.lodash.sortBy(data.tasks || [], ['date'])
    let html = ``
      tasks.forEach(task=>{
        let task_done = ( task.category === "reminder" && task.value.notification === 'notification_sent' )
          || ( task.category !== "reminder" && task.value.status === 'task_complete' )
        let show_complete_button = task.category !== "reminder" && task.value.status !== 'task_complete'
        let task_row = `
            <a href="/${window.lodash.escape(task.post_type)}/${window.lodash.escape(task.post_id)}">${window.lodash.escape(task.post_title)}</a> -
            <strong>${window.lodash.escape( moment(task.date).format("MMM D YYYY") )}</strong> -
        `
        if ( task.category === "reminder" ){
          task_row += window.lodash.escape( wpApiDashboard.translations.reminder )
          task_row += ' - '
          if ( task.value.note ){
            task_row += ' ' + window.lodash.escape(task.value.note) + ' - '
          }
        } else {
          task_row += window.lodash.escape(task.value.note || wpApiDashboard.translations.no_note ) + ' - '
        }
        html += `<li>
        <span style="${task_done ? 'text-decoration:line-through' : ''}">
        ${task_row}
        ${ show_complete_button ? `<button type="button" data-id="${window.lodash.escape(task.id)}" class="existing-task-action complete-task">${window.lodash.escape(wpApiDashboard.translations.complete)}</button>` : '' }
        <button type="button" data-id="${window.lodash.escape(task.id)}" class="existing-task-action remove-task" style="color: red;">${window.lodash.escape(wpApiDashboard.translations.remove)}</button>
      </li>`
      })
      if (!html ){
        $('.existing-tasks').html(`<li>${window.lodash.escape(wpApiDashboard.translations.no_tasks)}</li>`)
      } else {
        $('.existing-tasks').html(html)
      }

      $('.complete-task').on("click", function () {
        $('#tasks-spinner').addClass('active')
        let id = $(this).data('id').toString()
        let task = window.lodash.find(data.tasks, {id})
        API.update_post(task.post_type, task.post_id, {
          "tasks": { values: [ { id, value: {status: 'task_complete'}, } ] }
        }).then(() => {
          window.lodash.pullAllBy( data.tasks, [{id}], 'id' )
          build_tasks()
          $('#tasks-spinner').removeClass('active')
        })
      })
      $('.remove-task').on("click", function () {
        $('#tasks-spinner').addClass('active')
        let id = $(this).data('id').toString()
        let task = window.lodash.find(data.tasks, {id})
        API.update_post(task.post_type, task.post_id, {
          "tasks": { values: [ { id, delete: true } ] }
        }).then(() => {
          window.lodash.pullAllBy( data.tasks, [{id}], 'id' )
          build_tasks()
          $('#tasks-spinner').removeClass('active')
        })
      })
  }

})(window.jQuery, window.wpApiDashboard)
