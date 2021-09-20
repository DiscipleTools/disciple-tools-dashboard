(function ($) {
  window.dt_dashboard.onAdd("DT_Dashboard_Plugin_Tasks", function (context) {
    let data = context.wpApiDashboard.data;
    let wpApiDashboard = context.wpApiDashboard;

    const options = {
      type: "GET",
      contentType: "application/json; charset=utf-8",
      dataType: "json",
      url: `${window.wpApiShare.root}dt-dashboard/v1/tasks`,
      beforeSend: (xhr) => {
        xhr.setRequestHeader("X-WP-Nonce", window.wpApiShare.nonce);
      },
    };

    $.ajax(options).then((resp) => {
      $(context.element).find(".stats-spinner").removeClass("active");
      data.tasks = resp;
      build_tasks();
    });

    function build_tasks() {
      let tasks = window.lodash.sortBy(data.tasks || [], ["date"]);
      let html = ``;
      tasks.forEach((task) => {
        let task_done =
          (task.category === "reminder" &&
            task.value.notification === "notification_sent") ||
          (task.category !== "reminder" &&
            task.value.status === "task_complete");
        let show_complete_button =
          task.category !== "reminder" && task.value.status !== "task_complete";
        let task_row = `
            <a class="task-contact" href="/${window.lodash.escape(
              task.post_type
            )}/${window.lodash.escape(task.post_id)}">${window.lodash.escape(
          task.post_title
        )}</a>
            <strong class="task-date">${window.lodash.escape(
              moment(task.date).format("MMMM D, YYYY")
            )}</strong>
        `;
        if (task.category === "reminder") {
          task_row += window.lodash.escape(
            wpApiDashboard.translations.reminder 
          );
          task_row += " - ";
          if (task.value.note) {
            task_row += " " + window.lodash.escape(task.value.note) + " - ";
          }
        } else {
          task_row +=
          '<span class="task-description">' + 
            window.lodash.escape(
              task.value.note || wpApiDashboard.translations.no_note
            ) + '</span>';
        }
        html += `<li>
        <span style="${task_done ? "text-decoration:line-through" : ""}">
        ${task_row}
        <div class="action-buttons">
        ${
          show_complete_button
            ? `<button type="button" data-id="${window.lodash.escape(
                task.id
              )}" class="existing-task-action complete-task">${window.lodash.escape(
                wpApiDashboard.translations.complete
              )}</button>`
            : ""
        }
        <button type="button" data-id="${window.lodash.escape(
          task.id
        )}" class="existing-task-action remove-task">${window.lodash.escape(
          wpApiDashboard.translations.remove
        )}</button>
        </div>
      </li>`;
      });
      if (!html) {
        $(context.element)
          .find(".existing-tasks")
          .html(
            `<li>${window.lodash.escape(
              wpApiDashboard.translations.no_tasks
            )}</li>`
          );
      } else {
        $(context.element).find(".existing-tasks").html(html);
      }

      $(context.element)
        .find(".complete-task")
        .on("click", function () {
          $(context.element).find("#tasks-spinner").addClass("active");
          let id = $(context.element).find(this).data("id").toString();
          let task = window.lodash.find(data.tasks, { id });
          API.update_post(task.post_type, task.post_id, {
            tasks: { values: [{ id, value: { status: "task_complete" } }] },
          }).then(() => {
            window.lodash.pullAllBy(data.tasks, [{ id }], "id");
            build_tasks();
            $(context.element).find("#tasks-spinner").removeClass("active");
          });
        });
      $(context.element)
        .find(".remove-task")
        .on("click", function () {
          $(context.element).find("#tasks-spinner").addClass("active");
          let id = $(this).data("id").toString();
          let task = window.lodash.find(data.tasks, { id });
          API.update_post(task.post_type, task.post_id, {
            tasks: { values: [{ id, delete: true }] },
          }).then(() => {
            window.lodash.pullAllBy(data.tasks, [{ id }], "id");
            build_tasks();
            $(context.element).find("#tasks-spinner").removeClass("active");
          });
        });
    }
  });
})(window.jQuery);
