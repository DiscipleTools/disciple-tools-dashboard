(function($) {
  window.dt_dashboard.onAdd('DT_Dashboard_Plugin_Personal_Benchmarks', function (context) {
    am4core.options.autoSetClassName = true;

    let data = context.wpApiDashboard.data
    let wpApiDashboard = context.wpApiDashboard

    const options = {
      type: 'GET',
      contentType: 'application/json; charset=utf-8',
      dataType: 'json',
      url: `${window.wpApiShare.root}dt-dashboard/v1/benchmarks`,
      beforeSend: xhr => {
        xhr.setRequestHeader('X-WP-Nonce', window.wpApiShare.nonce);
      }
    }

    $.ajax(options).then(resp=>{
      $(context.element).find(".stats-spinner").removeClass("active")
      data.benchmarks = resp
      benchmarks_chart()
    })

    function benchmarks_chart() {
      const formatDate = window.SHAREDFUNCTIONS.formatDate
      let thirty_days_ago = moment().add( -30, "days")
      let sixty_days_ago = moment().add( -60, "days")
      $(context.element).find('#benchmarks_current').html(`${formatDate(thirty_days_ago.unix())} to ${formatDate(moment().unix())}`)
      $(context.element).find('#benchmarks_previous').html(`${formatDate(sixty_days_ago.unix())} to ${formatDate(thirty_days_ago.unix())}`)

      am4core.options.autoSetClassName = true;
      am4core.useTheme(am4themes_animated);
      let chart = am4core.create("benchmark_chart", am4charts.XYChart);

      console.log(data.benchmarks.contacts.current)
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
        series.columns.template.column.cornerRadiusTopLeft = 10;
        series.columns.template.column.cornerRadiusTopRight = 10;

        series.columns.template.adapter.add('fill', function(fill, column, key) {
          var gradient = new am4core.LinearGradient();
          if (field === 'previous') {
            gradient.addColor(am4core.color('#99CEF8'));
            gradient.addColor(am4core.color('#6EB1E8'));
          } else {
            gradient.addColor(am4core.color('#277CBF'));
            gradient.addColor(am4core.color('#1464A5'));
          }
          gradient.rotation = 90;
          return gradient;
        });
      }

      createSeries("previous", "Previous", false);
      createSeries("current", "Current", false);
    }
  })
})(window.jQuery)






