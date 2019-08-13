jQuery(document).ready(function($) {
  let data = wpApiDashboard.data

  // update_needed();

  $('#active_contacts').html(data.active_contacts)
  $('#needs_accepting').html(data.accept_needed.total)
  let needs_accepting_list = ``
  data.accept_needed.contacts.slice(0, 3).forEach( contact =>{
    needs_accepting_list += `<div style="margin-top:10px; display: flex">
        <div style="display: inline-block; vertical-align: middle"><i class="fi-torso large"></i></div>
        <div style="display: inline-block; margin-left: 10px; vertical-align: middle; flex-grow: 1">
            <a style="font-size: 1.1rem" href="${wpApiDashboard.site_url}/contacts/${contact.ID}">${_.escape(
      contact.post_title)}</a>
        </div>
        <div>
            <button class="button small dt-green" style="color: white; margin-bottom: 0">${_.escape(wpApiDashboard.translations.accept)}</button>
            <button class="button small" style="background-color: #f43636; color: white; margin-bottom: 0">${_.escape(wpApiDashboard.translations.decline)}</button>
        </div>
    </div>`
  })
  $('#needs_accepting_list').html( needs_accepting_list )

  /**
   * Update Needed
   */
  $('#update_needed').html(data.update_needed.total)
  let up_list = ``
  data.update_needed.contacts.slice(0, 3).forEach( contact =>{
    let row = `<div style="margin-top:5px">
        <div style="display: inline-block"><i class="fi-torso large"></i></div>
        <div style="display: inline-block; margin-left: 10px">
            <a style="font-size: 1.1rem" href="${wpApiDashboard.site_url}/contacts/${contact.ID}">${_.escape( contact.post_title ) }</a>
            <br>
            <span style="font-size: 0.9rem">${_.escape(contact.last_modified_msg)}</span>
        </div>
    </div>`
    up_list += row
  })
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
      url: `${wpApiShare.root}dt-dashboard/v1/stats`,
      beforeSend: xhr => {
          xhr.setRequestHeader('X-WP-Nonce', wpApiShare.nonce);
      }
  }
  jQuery.ajax(options).then(resp=>{
    $(".loading-spinner").removeClass("active")
    _.merge(data, resp)
    benchmarks_chart()
    seeker_path_chart()
    milestones()
  })



  function benchmarks_chart() {
    let thirty_days_ago = moment().add( -30, "days")
    let sixty_days_ago = moment().add( -60, "days")
    $('#benchmarks_current').html(`${thirty_days_ago.format("MMMM D, YYYY")} to ${moment().format("MMMM D, YYYY")}`)
    $('#benchmarks_previous').html(`${sixty_days_ago.format("MMMM D, YYYY")} to ${thirty_days_ago.format("MMMM D, YYYY")}`)

    am4core.useTheme(am4themes_animated);
    let chart = am4core.create("benchmark_chart", am4charts.XYChart);

    chart.data = [ {
      "year": _.escape(wpApiDashboard.translations.number_contacts_assigned),
      "previous": data.benchmarks.contacts.previous,
      "current": data.benchmarks.contacts.current
    }, {
      "year": _.escape(wpApiDashboard.translations.number_meetings),
      "previous": data.benchmarks.meetings.previous,
      "current": data.benchmarks.meetings.current
    }, {
      "year": _.escape(wpApiDashboard.translations.number_milestones),
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



    let  valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
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
    _.pullAllBy( data.seeker_path_personal, [{ value: 0 }], "value" )
    chart.data = data.seeker_path_personal
    if ( _.isEmpty( chart.data ) ){
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
        <button style="color: white" class="group-progress-button"> ${_.escape( m.value )} </button>
        <p>${_.escape( m.milestones )}</p>
      </div>`
    })
    $("#milestones").html(milestones)

  }

})
