(function ($) {
  window.dt_dashboard.onAdd(
    "DT_Dashboard_Plugin_Update_Needed",
    function (context) {
      let data = context.wpApiDashboard.data;
      $(context.element).find("#active_contacts").html(data.active_contacts);
      $(context.element).find("#update_needed").html(data.update_needed.total);
      let up_list = ``;
      data.update_needed.posts.slice(0, 7).forEach((contact) => {
        let row = `<div class="update-needed-container">
        <div class="update-needed-info">
          <a href="${
            context.wpApiDashboard.site_url
          }/contacts/${window.lodash.escape(
          contact.ID
        )}">${window.lodash.escape(contact.post_title)}</a>
          <span>${window.lodash.escape(contact.last_modified_msg)}</span>
        </div>
    </div>`;
        up_list += row;
      });
      if (!up_list) {
        up_list = `<p style="margin-top:40px; font-size: 1.1rem; text-align: center">${window.lodash.escape(
          context.wpApiDashboard.translations.caught_up
        )}</p>`;
      }
      if (data.update_needed.total > 7) {
        console.log(context.wpApiDashboard.site_url);
        console.log(`${context.wpApiDashboard.site_url}/contacts/?filter_id=my_update_needed`)
        up_list += `<a class="button view-all" href="${context.wpApiDashboard.site_url}/contacts/?filter_id=my_update_needed">${window.lodash.escape( context.wpApiDashboard.translations.see_all )}</a>`;
      }
      $(context.element).find("#update_needed_list").html(up_list);

      $(context.element)
        .find("#view_updated_needed_button")
        .on("click", function () {
          document.location = `${context.wpApiDashboard.site_url}/contacts?list-tab=update_needed`;
        });
      $(context.element)
        .find("#view_needs_accepted_button")
        .on("click", function () {
          document.location = `${context.wpApiDashboard.site_url}/contacts?list-tab=needs_accepted`;
        });
    }
  );
})(window.jQuery);
