(function ($) {
  window.dt_dashboard.onAdd("DT_Dashboard_Plugin_Home_Apps", function (context) {
    const carouselElement = $(context.element).find('.home-apps-carousel');
    const spinnerElement = $(context.element).find('.stats-spinner');
    
    // Get apps data from data attribute
    const appsData = carouselElement.data('apps');
    
    if (!appsData || !Array.isArray(appsData) || appsData.length === 0) {
      spinnerElement.removeClass('active');
      carouselElement.html('<div class="home-apps-empty">No apps available.</div>');
      return;
    }

    // Hide spinner
    spinnerElement.removeClass('active');

    // Render apps
    renderApps(appsData, carouselElement);
  });

  /**
   * Render apps in carousel format
   */
  function renderApps(apps, container) {
    let html = '';

    apps.forEach(function(app) {
      // Trim title to max 12 characters with ellipsis to fit under card
      const trimmedTitle = app.title && app.title.length > 12 
        ? app.title.substring(0, 12) + '...' 
        : (app.title || '');

      // Determine app type: if type exists use it, otherwise use fallback logic
      let appType = app.type;
      if (!appType || (appType !== 'app' && appType !== 'link')) {
        // Fallback logic: if creation_type is 'coded', default to 'app', otherwise 'link'
        appType = (app.creation_type === 'coded') ? 'app' : 'link';
      }

      // App-type apps navigate in same tab, link-type apps open in new tab
      let onClickHandler = '';
      const appUrl = app.url || '#';
      
      if (appType === 'app') {
        // For apps, navigate in same tab
        // Check if app is cross-domain (different domain than current)
        try {
          const currentHost = window.location.hostname;
          const appUrlObj = new URL(appUrl, window.location.origin);
          const appHost = appUrlObj.hostname;
          const isCrossDomain = appHost !== currentHost && appHost !== window.location.hostname;
          
          //if (isCrossDomain) {
            // For cross-domain apps, open in new tab (dashboard doesn't have launcher wrapper)
            onClickHandler = `onclick="window.open('${appUrl.replace(/'/g, "\\'")}', '_blank'); return false;"`;
          //} else {
            // For same-domain apps, navigate in same tab
            //onClickHandler = `onclick="window.location.href = '${appUrl.replace(/'/g, "\\'")}'; return false;"`;
          //}
        } catch (e) {
          // If URL parsing fails, default to same tab navigation
          onClickHandler = `onclick="window.location.href = '${appUrl.replace(/'/g, "\\'")}'; return false;"`;
        }
      } else {
        // For links, open in new tab
        onClickHandler = `onclick="window.open('${appUrl.replace(/'/g, "\\'")}', '_blank'); return false;"`;
      }

      // Determine icon display: image or icon class
      let iconHtml = '';
      const isImageIcon = app.icon && (app.icon.startsWith('http') || app.icon.startsWith('/'));
      
      if (isImageIcon) {
        // Render image icon
        const safeIconUrl = (app.icon || '').replace(/"/g, '&quot;');
        const safeTitle = trimmedTitle.replace(/"/g, '&quot;');
        iconHtml = `<img src="${safeIconUrl}" alt="${safeTitle}" />`;
      } else {
        // Render icon class with color support
        let iconColor = null;
        const hasCustomColor = app.color && typeof app.color === 'string' && app.color.trim() !== '';
        
        // Validate hex color format
        if (hasCustomColor) {
          const hexColorPattern = /^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/;
          if (hexColorPattern.test(app.color.trim())) {
            iconColor = app.color.trim();
          }
        }

        // Use theme-aware default if no valid custom color
        if (!iconColor) {
          // Check for dark mode (dashboard may not have theme-dark class, so check body background)
          const bodyBg = window.getComputedStyle(document.body).backgroundColor;
          const isDarkMode = bodyBg && (
            bodyBg.includes('rgb(26, 26, 26)') || 
            bodyBg.includes('rgb(42, 42, 42)') ||
            document.body.classList.contains('theme-dark') ||
            document.documentElement.classList.contains('theme-dark')
          );
          iconColor = isDarkMode ? '#ffffff' : '#0a0a0a';
        }

        const safeIcon = (app.icon || 'mdi mdi-apps').replace(/"/g, '&quot;');
        iconHtml = `<i class="${safeIcon}" style="color: ${iconColor};" data-has-custom-color="${hasCustomColor}"></i>`;
      }

      const safeTitle = (trimmedTitle || '').replace(/"/g, '&quot;');
      const appHtml = `
        <div class="app-card-wrapper">
          <div class="app-card" ${onClickHandler} title="${safeTitle}">
            <div class="app-icon">
              ${iconHtml}
            </div>
          </div>
          <div class="app-title">${safeTitle}</div>
        </div>
      `;

      html += appHtml;
    });

    container.html(html);
  }
})(window.jQuery);

