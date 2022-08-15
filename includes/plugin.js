/**
 * The dashboard js plugin. Loads in the head so it's available for other scripts.
 *
 * @type {{container(): Element | null, moveForward: (function(*=): undefined), tiles: [], renderAddMenu(): undefined, tilesEls(): NodeListOf<Element>, storeSort(): void, findEl: (function(*): Element), isActive(*=): void, onAdd: Window.dt_dashboard.onAdd, remove: Window.dt_dashboard.remove, find: (function(*): *), root(): Element | null, initialized: boolean, tileContext: (function(*=): {wpApiDashboard: *, element: *}), add: (function(*=): undefined), init(): void, initTiles(): void, moveBack: (function(*=): undefined), fireOnAdd(*=): void, addTileEl(): Element | null, inactiveTiles(): *, addTileHtml(*=, *=): undefined, fireOnRemove(*=): void, onRemove: Window.dt_dashboard.onRemove, activeTiles(): *}}
 */
window.dt_dashboard = {
  /**
   * Has the dashboard been initialized?
   */
  initialized: false,

  /**
   * An array of tile objects
   *
   * [{
   *    element, //The DOM element for the tile
   *    onAdd, //An array of callbacks for after tiles are added to the dashboard
   *    onRemove, //An array of callbacks for after tiles are removed from the dashboard
   *  }]
   */
  tiles: [],

  /**
   * Init the plugin. Should be called after the dom is ready
   */
  init() {
    //Build up the tiles array
    this.tiles = Object.values(wpApiDashboard.tiles).map(function(tile) {
      tile.element = this.findEl(tile.handle);
      tile.onAdd = []
      tile.onRemove = []
      return tile
    }.bind(this))
    this.initTiles()
    this.renderAddMenu()
    this.refreshClasses()
    this.initalized = true
  },

  /**
   * The root dom element
   * @returns {Element}
   */
  root() {
    return document.querySelector('#dashboard')
  },

  /**
   * The tile container element
   * @returns {Element}
   */
  container() {
    return document.querySelector('.dash-tiles')
  },

  /**
   * All the tile elements (excluding the dynamically  generated add tile should it exist)
   * @returns {NodeListOf<Element>}
   */
  tileEls() {
    return document.querySelectorAll('.dash-tile:not(.add-tile)')
  },

  /**
   * The add tile element. May not exist in the case that there are no hidden tiles
   * @returns {Element}
   */
  addTileEl() {
    return document.querySelector('#add-tile')
  },

  /**
   * Find a tile object by handle
   * @param handle
   * @returns {*}
   */
  find: function(handle) {
    return this.tiles.find(function(tile) {
      return tile.handle === handle
    })
  },

  /**
   * Query for a tile element by handle
   * @param handle
   * @returns {Element}
   */
  findEl: function(handle) {
    return document.querySelector('#dash-tile--' + handle)
  },

  canMoveBack: function(handle) {
    const tileEl = this.findEl(handle)
    if (!tileEl) {
      return false
    }
    const prevEl = tileEl.previousElementSibling

    //The add tile is also in the container, so we need to ignore it.
    if (!prevEl || prevEl === this.addTileEl()) {
      return false
    }

    return true
  },

  canMoveForward: function(handle) {
    const tileEl = this.findEl(handle)
    if (!tileEl) {
      return false
    }
    return !!tileEl.nextElementSibling
  },

  /**
   * The context object passed to tile events
   * {
   *   wpApiDashboard,
   *   element
   * }
   * @param handle
   * @returns {{wpApiDashboard, element}}
   */
  tileContext: function(handle) {
    const tile = this.find(handle)
    return {
      wpApiDashboard: window.wpApiDashboard,
      element: tile.element
    }
  },

  /**
   * Get all the active tiles
   * @returns {*[]}
   */
  activeTiles() {
    return this.tiles.filter(function(tile) {
      return !!tile.element
    })
  },

  /**
   * Get all the inactive tiles
   * @returns {*[]}
   */
  inactiveTiles() {
    return this.tiles.filter(function(tile) {
      return !tile.element
    })
  },

  /**
   * Fire the add events for all tiles. This will cause the javascript for individual tiles to execute.
   */
  initTiles() {
    this.activeTiles().forEach(function(tile) {
      this.fireOnAdd(tile.handle)
    }.bind(this))
  },

  /**
   * Is a tile active?
   * @param handle
   */
  isActive(handle) {
    return !!this.find(handle).element
  },

  /**
   * Add a tile to the dashboard
   * @param handle
   */
  add: function(handle) {
    if (this.isActive(handle)) {
      return
    }

    //Tell the server that the tile is now active for the user
    let body = new URLSearchParams()
    body.append('tile_handle', handle)
    fetch(window.wpApiShare.site_url + '/wp-json/dt-dashboard/v1/user/tiles/show', {
      method: 'POST',
      headers: {
        'X-WP-Nonce': window.wpApiShare.nonce
      },
      body: body,
    })

    //Fetch the tile HTML
    fetch(window.wpApiShare.site_url + '/wp-json/dt-dashboard/v1/tile?' + body.toString(), {
      method: 'GET',
      headers: {
        'X-WP-Nonce': window.wpApiShare.nonce
      },
    })
      .then(response => response.json())
      .then(function(data) {
        let response = JSON.parse(data)
        this.addTileHtml(handle, response.template)
        this.renderAddMenu()
        this.storeSort()
        this.refreshClasses()
      }.bind(this))
  },

  /**
   * Fire any add callbacks for a tile
   * @param handle
   */
  fireOnAdd(handle) {
    window.setTimeout(function() {
      this.registerScrollbar(handle)
      const tile = this.find(handle)
      tile.onAdd.forEach(function (callback) {
        callback(this.tileContext(tile.handle))
      }.bind(this))
    }.bind(this), 1)
  },

  /**
   * Fire any remove callbacks for a tile
   * @param handle
   */
  fireOnRemove(handle) {
    window.setTimeout(function() {
      const tile = this.find(handle)
      tile.onRemove.forEach(function (callback) {
        callback(this.tileContext(tile.handle))
      }.bind(this))
    }.bind(this), 1)
  },

  /**
   * Add tile HTML to the dashboard
   * @param handle
   * @param html
   */
  addTileHtml(handle, html = null) {
    if (this.findEl(handle)) {
      return
    }
    const tile = this.find(handle)
    this.container().insertAdjacentHTML('beforeend', html)
    tile.element = this.findEl(handle)
    this.fireOnAdd(handle)
  },

  /**
   * Remove a tile from the dashboard
   * @param handle
   */
  remove: function(handle) {
    const tile = this.find(handle)
    if (!tile) {
      return
    }
    tile.element.remove()
    tile.element = null
    this.fireOnRemove(handle)
    let body = new URLSearchParams()
    body.append('tile_handle', handle)

    //Tell the server about it
    fetch(window.wpApiShare.site_url + '/wp-json/dt-dashboard/v1/user/tiles/hide', {
      method: 'POST',
      headers: {
        'X-WP-Nonce': window.wpApiShare.nonce
      },
      body: body,
    })

    this.renderAddMenu()
    this.storeSort()
  },

  registerScrollbar:function(handle) {
    const tileEl = this.findEl(handle)
    const tileBody = tileEl.querySelector('.tile-body--scroll')
    if (!tileBody) {
      return
     }
    let Scrollbar = window.Scrollbar
    Scrollbar.init(tileBody)
  },
  /**
   * Add an add callback to a tile
   * @param handle
   * @param callback
   */
  onAdd: function(handle, callback) {
    const tile = this.find(handle)
    tile.onAdd.push(callback)
    if (this.initalized) {
      callback(this.tileContext(tile.handle))
    }
  },

  /**
   * Add a remove callback to a tile
   * @param handle
   * @param callback
   */
  onRemove: function(handle, callback) {
    this.find(handle).onRemove.push(callback)
  },

  /**
   * Render the add menu. If there are no active tiles, it will remove it.
   */
  renderAddMenu() {
    let addTileContainer = this.addTileEl()

    let addTileEl
    if (!addTileContainer) {
      addTileContainer = document.createElement('div')
      addTileContainer.classList.add('dash-tile', 'add-tile', 'item')
      addTileContainer.setAttribute('id', 'add-tile')
      addTileContainer.style.order = this.tiles.length + 1
      this.container().prepend(addTileContainer)

      addTileEl = document.createElement('div')
      addTileEl.classList.add('tile')
      addTileContainer.appendChild(addTileEl)
    } else {
      addTileEl = addTileContainer.querySelector('.tile')
    }

    addTileEl.innerHTML = ''

    let heading = document.createElement('span')
    heading.innerText = 'Add tiles'
    heading.classList.add('tile-header')
    addTileEl.appendChild(heading)

    let addMenu = document.createElement('ul')
    addMenu.setAttribute('id', 'add-menu')
    addTileEl.appendChild(addMenu)

    this.tiles.forEach(function(tile) {
      const menuItem = document.createElement('li')
      const lightSwitch = document.createElement('input')
      lightSwitch.type = 'checkbox'
      lightSwitch.checked = this.isActive(tile.handle)
      menuItem.innerHTML = tile.label
      menuItem.appendChild(lightSwitch)
      new Switchery(lightSwitch, {
        color: '#4BAF50',
        secondaryColor: '#B4B4B4',
      });
      lightSwitch.addEventListener('change', function() {
        setTimeout(() => lightSwitch.checked ? this.add(tile.handle) : this.remove(tile.handle), 500)
      }.bind(this))
      addMenu.appendChild(menuItem)
    }.bind(this))
  },

  /**
   * Move a tile forward in the DOM
   * @param handle
   */
  moveForward: function(handle) {
    if (!this.canMoveForward(handle)) {
      return
    }
    const tileEl = this.findEl(handle)
    const nextEl = tileEl.nextElementSibling
    nextEl.after(tileEl)
    this.storeSort()
    this.refreshClasses()
  },

  /**
   * Move a tile back in the DOM
   * @param handle
   */
  moveBack: function(handle) {
    if (!this.canMoveBack(handle)) {
      return
    }
    const tileEl = this.findEl(handle)
    const prevEl = tileEl.previousElementSibling
    prevEl.before(tileEl)
    this.storeSort()
    this.refreshClasses()
  },

  /**
   * Store the sort on the server
   */
  storeSort() {
    const sort = Array.from(this.tileEls()).map(function(el) {
      return el.dataset.tileHandle
    })

    let body = new URLSearchParams()
    body.append('tile_sort', JSON.stringify(sort))

    fetch(window.wpApiShare.site_url + '/wp-json/dt-dashboard/v1/user/tiles/sort', {
      method: 'PUT',
      headers: {
        'X-WP-Nonce': window.wpApiShare.nonce
      },
      body: body,
    })
  },

  /**
   * Add css classes depending on state
   */
  refreshClasses() {
    if (this.inactiveTiles().length) {
      this.root().classList.add('dash-tiles--has-inactive')
    } else {
      this.root().classList.remove('dash-tiles--has-inactive')
    }

    Array.from(this.tileEls()).forEach(function(tileEl) {
      if (this.canMoveBack(tileEl.dataset.tileHandle)) {
        tileEl.classList.remove('data-tile--first')
      } else {
        tileEl.classList.add('data-tile--first')
      }

      if (this.canMoveForward(tileEl.dataset.tileHandle)) {
        tileEl.classList.remove('data-tile--last')
      } else {
        tileEl.classList.add('data-tile--last')
      }
    }.bind(this))
  }
}
