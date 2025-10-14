(function ($) {
  window.dt_dashboard.onAdd("DT_Dashboard_Plugin_User_Activity", function (context) {
    const wpApiDashboard = context.wpApiDashboard;

    window
      .makeRequest(
        'get',
        `activity-log`,
        null,
        'dt-users/v1/',
      )
      .done((activity) => {

        const activity_html = window.dtActivityLogs.makeActivityList(
          activity,
          wpApiDashboard.translations,
        );

        $('#user_activities').html(activity_html);

        $(context.element).find(".stats-spinner").removeClass("active");
      })
      .catch((e) => {
        console.log('error in loading user activity');
        console.log(e);
      });
  });
})(window.jQuery);
