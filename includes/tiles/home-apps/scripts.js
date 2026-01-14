(function ($) {
  // Debounce function for resize events
  function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
      const later = () => {
        clearTimeout(timeout);
        func(...args);
      };
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
    };
  }

  window.dt_dashboard.onAdd("DT_Dashboard_Plugin_Home_Apps", function (context) {
    const carouselElement = $(context.element).find('.home-apps-carousel');
    const carouselWrapper = $(context.element).find('.home-apps-carousel-wrapper');
    const spinnerElement = $(context.element).find('.stats-spinner');

    // Get apps data from data attribute
    const appsData = carouselElement.data('apps');

    // Get Show More URL from data attribute
    // Use .attr() to get the raw value, then check if it's empty
    const showMoreUrlRaw = carouselElement.attr('data-show-more-url');
    const showMoreUrl = showMoreUrlRaw && showMoreUrlRaw !== '' ? showMoreUrlRaw : null;

    if (!appsData || !Array.isArray(appsData) || appsData.length === 0) {
      spinnerElement.removeClass('active');
      carouselElement.html('<div class="home-apps-empty">No apps available.</div>');
      return;
    }

    // Hide spinner
    spinnerElement.removeClass('active');

    // Render apps with dynamic limiting
    function renderWithLimit() {
      renderApps(appsData, carouselElement, carouselWrapper, showMoreUrl);
    }

    // Initial render
    renderWithLimit();

    // Handle window resize
    const handleResize = debounce(function() {
      renderWithLimit();
    }, 300);

    $(window).on('resize', handleResize);
  });

  /**
   * Validate URL to prevent XSS attacks
   * Only allows http://, https://, relative URLs, and protocol-relative URLs
   * Blocks dangerous schemes like javascript:, data:, vbscript:, etc.
   *
   * @param {string} url The URL to validate
   * @return {boolean} True if URL is safe, false otherwise
   */
  function isValidUrl(url) {
    if (!url || url === '#' || url.trim() === '') {
      return false;
    }

    const trimmedUrl = url.trim();
    const lowerUrl = trimmedUrl.toLowerCase();

    // Block dangerous URL schemes that could execute code
    const dangerousSchemes = ['javascript:', 'data:', 'vbscript:', 'file:', 'about:'];
    for (const scheme of dangerousSchemes) {
      if (lowerUrl.startsWith(scheme)) {
        return false;
      }
    }

    // Allow http://, https://, relative URLs (starting with /), and protocol-relative URLs (starting with //)
    const validPattern = /^(https?:\/\/|\/\/|\/|#)/i;
    return validPattern.test(trimmedUrl);
  }

  /**
   * Escape URL for safe use in HTML onclick attribute
   * Validates URL scheme and properly encodes it
   *
   * @param {string} url The URL to escape
   * @return {string} Escaped URL or '#' if invalid
   */
  function escapeUrlForOnclick(url) {
    // Validate URL first
    if (!isValidUrl(url)) {
      return '#';
    }

    // Use encodeURI for proper URL encoding, then escape for JavaScript string in HTML attribute
    try {
      const encoded = encodeURI(url);
      return encoded.replace(/\\/g, '\\\\').replace(/'/g, "\\'").replace(/"/g, '\\"');
    } catch (e) {
      // If encoding fails, return safe fallback
      return '#';
    }
  }

  /**
   * Calculate how many cards can fit in the visible area
   */
  function calculateVisibleCards(wrapper) {
    if (!wrapper || wrapper.length === 0) {
      return 5; // Default fallback
    }

    const wrapperWidth = wrapper[0].getBoundingClientRect().width;
    if (wrapperWidth <= 0) {
      return 5; // Default fallback
    }

    // Get responsive card width and gap based on window size
    let cardWidth = 75; // Desktop default
    let gap = 16; // 1rem = 16px default
    const windowWidth = window.innerWidth;

    if (windowWidth <= 480) {
      cardWidth = 60;
      gap = 8; // 0.5rem
    } else if (windowWidth <= 640) {
      cardWidth = 65;
      gap = 12; // 0.75rem
    }

    // Account for padding (left and right)
    let padding = 40; // 20px each side default
    if (windowWidth <= 480) {
      padding = 20; // 10px each side
    } else if (windowWidth <= 640) {
      padding = 30; // 15px each side
    }

    // Calculate available width
    const availableWidth = wrapperWidth - padding;

    // Calculate how many cards fit (including gap between cards)
    // Formula: (availableWidth + gap) / (cardWidth + gap)
    const cardsThatFit = Math.floor((availableWidth + gap) / (cardWidth + gap));

    // Return at least 1 card, and subtract 1 for the "Show More" card
    return Math.max(1, cardsThatFit - 1);
  }

  /**
   * Create app card HTML
   */
  function createAppCard(app, isHidden) {
    // Use full title - CSS will handle wrapping
    const trimmedTitle = app.title || '';

    // Always open apps in a new tab
    const appUrl = app.url || '#';

    // Validate and escape URL to prevent XSS attacks
    let onClickHandler = '';
    const safeUrl = escapeUrlForOnclick(appUrl);
    if (safeUrl === '#') {
      // Disable click for invalid/empty URLs
      onClickHandler = `onclick="return false;"`;
    } else {
      // Use validated and escaped URL
      onClickHandler = `onclick="window.open('${safeUrl}', '_blank'); return false;"`;
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
    const hiddenClass = isHidden ? ' hidden' : '';

    return `
      <div class="app-card-wrapper${hiddenClass}">
        <div class="app-card" ${onClickHandler} title="${safeTitle}">
          <div class="app-icon">
            ${iconHtml}
          </div>
        </div>
        <div class="app-title">${safeTitle}</div>
      </div>
    `;
  }

  /**
   * Create "Show More" card HTML
   */
  function createShowMoreCard(url) {
    // Validate URL and check if it's enabled
    const isEnabled = url && url !== null && url !== undefined && url !== '' && url !== 'null' && isValidUrl(url);

    // Build click handler or disable card
    let onClickHandler = '';
    let disabledClass = '';
    let tooltip = 'Show More';

    if (isEnabled) {
      // Card is enabled - validate and escape URL to prevent XSS attacks
      const safeUrl = escapeUrlForOnclick(url);
      onClickHandler = `onclick="window.open('${safeUrl}', '_blank'); return false;"`;
    } else {
      // Card is disabled - no click handler, add disabled class
      disabledClass = ' disabled';
      tooltip = 'Please Activate Home Screen';
      onClickHandler = `onclick="return false;"`;
    }

    // Use blue background and white icon (moved to CSS)
    const iconHtml = '<i class="mdi mdi-apps show-more-icon"></i>';

    return `
      <div class="app-card-wrapper show-more-wrapper">
        <div class="app-card show-more-card${disabledClass}" ${onClickHandler} title="${tooltip}">
          <div class="app-icon">
            ${iconHtml}
          </div>
        </div>
        <div class="app-title">Show More</div>
      </div>
    `;
  }

  /**
   * Render apps in carousel format with dynamic limiting
   */
  function renderApps(apps, container, wrapper, showMoreUrl) {
    let html = '';

    // Calculate how many cards can be visible
    const visibleLimit = calculateVisibleCards(wrapper);

    // Render visible apps (limit - 1 to make room for "Show More")
    const appsToShow = Math.min(visibleLimit, apps.length);

    // Render visible app cards
    for (let i = 0; i < appsToShow; i++) {
      html += createAppCard(apps[i], false);
    }

    // Always add "Show More" card as the last visible card
    html += createShowMoreCard(showMoreUrl);

    // Render remaining apps as hidden (for scrolling)
    for (let i = appsToShow; i < apps.length; i++) {
      html += createAppCard(apps[i], true);
    }

    container.html(html);
  }
})(window.jQuery);

