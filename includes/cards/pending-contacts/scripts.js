(function($) {
  window.dt_dashboard.onAdd('DT_Dashboard_Plugin_Pending_Contacts', function (context) {
    console.log('here')
    let data = context.wpApiDashboard.data
    $(context.element).find('#needs_accepting').html(data.accept_needed.total)
    let needs_accepting_list = ``
    data.accept_needed.posts.slice(0, 3).forEach( contact =>{
      needs_accepting_list += `<div class="pending-contacts-container" id="pending-${window.lodash.escape(contact.ID)}">
        <div class="pending-contacts-icon">
          <i class="fi-torso medium"></i>
        </div>

        <div class="pending-contacts-info">
          <a href="${context.wpApiDashboard.site_url}/contacts/${window.lodash.escape( contact.ID )}">${window.lodash.escape(
          contact.post_title)}</a>
          <div class="pending-contacts-btns">
            <button class="button small dt-green accept-button accept_contact_button" data-id="${window.lodash.escape( contact.ID )}" data-action="accept">${window.lodash.escape(wpApiDashboard.translations.accept)}</button>
            <button class="button small decline-button accept_contact_button" data-id="${window.lodash.escape( contact.ID )}" data-action="decline">${window.lodash.escape(wpApiDashboard.translations.decline)}</button>
          </div>
        </div>
    </div>`
    })
    if ( !needs_accepting_list ){
      needs_accepting_list = `<p style="margin-top:40px; font-size: 1.1rem; text-align: center">${window.lodash.escape(wpApiDashboard.translations.caught_up)}</p>`
    }
    $(context.element).find('#needs_accepting_list').html( needs_accepting_list )


    $(context.element).find('.accept_contact_button').on('click', function () {
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
  })
})(window.jQuery)

