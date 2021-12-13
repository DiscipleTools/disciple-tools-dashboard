/**
 * The dashboard js plugin. Loads in the head so it's available for other scripts.
 *
 * @type {{container(): Element | null, moveForward: (function(*=): undefined), cards: [], renderAddMenu(): undefined, cardsEls(): NodeListOf<Element>, storeSort(): void, findEl: (function(*): Element), isActive(*=): void, onAdd: Window.dt_dashboard.onAdd, remove: Window.dt_dashboard.remove, find: (function(*): *), root(): Element | null, initialized: boolean, cardContext: (function(*=): {wpApiDashboard: *, element: *}), add: (function(*=): undefined), init(): void, initCards(): void, moveBack: (function(*=): undefined), fireOnAdd(*=): void, addCardEl(): Element | null, inactiveCards(): *, addCardHtml(*=, *=): undefined, fireOnRemove(*=): void, onRemove: Window.dt_dashboard.onRemove, activeCards(): *}}
 */
window.dt_dashboard = {
  /**
   * Has the dashboard been initialized?
   */
  initialized: false,

  /**
   * An array of card objects
   *
   * [{
   *    element, //The DOM element for the card
   *    onAdd, //An array of callbacks for after cards are added to the dashboard
   *    onRemove, //An array of callbacks for after cards are removed from the dashboard
   *  }]
   */
  cards: [],

  /**
   * Init the plugin. Should be called after the dom is ready
   */
  init() {
    //Build up the cards array
    this.cards = Object.values(wpApiDashboard.cards).map(function(card) {
      card.element = this.findEl(card.handle);
      card.onAdd = []
      card.onRemove = []
      return card
    }.bind(this))
    this.initCards()
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
   * The card container element
   * @returns {Element}
   */
  container() {
    return document.querySelector('.dash-cards')
  },

  /**
   * All the card elements (excluding the dynamically  generated add card should it exist)
   * @returns {NodeListOf<Element>}
   */
  cardEls() {
    return document.querySelectorAll('.dash-card:not(.add-card)')
  },

  /**
   * The add card element. May not exist in the case that there are no hidden cards
   * @returns {Element}
   */
  addCardEl() {
    return document.querySelector('#add-card')
  },

  /**
   * Find a card object by handle
   * @param handle
   * @returns {*}
   */
  find: function(handle) {
    return this.cards.find(function(card) {
      return card.handle === handle
    })
  },

  /**
   * Query for a card element by handle
   * @param handle
   * @returns {Element}
   */
  findEl: function(handle) {
    return document.querySelector('#dash-card--' + handle)
  },

  canMoveBack: function(handle) {
    const cardEl = this.findEl(handle)
    if (!cardEl) {
      return false
    }
    const prevEl = cardEl.previousElementSibling

    //The add card is also in the container, so we need to ignore it.
    if (!prevEl || prevEl === this.addCardEl()) {
      return false
    }

    return true
  },

  canMoveForward: function(handle) {
    const cardEl = this.findEl(handle)
    if (!cardEl) {
      return false
    }
    return !!cardEl.nextElementSibling
  },

  /**
   * The context object passed to card events
   * {
   *   wpApiDashboard,
   *   element
   * }
   * @param handle
   * @returns {{wpApiDashboard, element}}
   */
  cardContext: function(handle) {
    const card = this.find(handle)
    return {
      wpApiDashboard: window.wpApiDashboard,
      element: card.element
    }
  },

  /**
   * Get all the active cards
   * @returns {*[]}
   */
  activeCards() {
    return this.cards.filter(function(card) {
      return !!card.element
    })
  },

  /**
   * Get all the inactive cards
   * @returns {*[]}
   */
  inactiveCards() {
    return this.cards.filter(function(card) {
      return !card.element
    })
  },

  /**
   * Fire the add events for all cards. This will cause the javascript for individual cards to execute.
   */
  initCards() {
    this.activeCards().forEach(function(card) {
      this.fireOnAdd(card.handle)
    }.bind(this))
  },

  /**
   * Is a card active?
   * @param handle
   */
  isActive(handle) {
    return !!this.find(handle).element
  },

  /**
   * Add a card to the dashboard
   * @param handle
   */
  add: function(handle) {
    if (this.isActive(handle)) {
      return
    }

    //Tell the server that the card is now active for the user
    let body = new URLSearchParams()
    body.append('card_handle', handle)
    fetch('/wp-json/dt-dashboard/v1/user/cards/show', {
      method: 'POST',
      headers: {
        'X-WP-Nonce': window.wpApiShare.nonce
      },
      body: body,
    })

    //Fetch the card HTML
    fetch('/wp-json/dt-dashboard/v1/card?' + body.toString(), {
      method: 'GET',
      headers: {
        'X-WP-Nonce': window.wpApiShare.nonce
      },
    })
      .then(response => response.json())
      .then(function(data) {
        let response = JSON.parse(data)
        this.addCardHtml(handle, response.template)
        this.renderAddMenu()
        this.storeSort()
        this.refreshClasses()
      }.bind(this))
  },

  /**
   * Fire any add callbacks for a card
   * @param handle
   */
  fireOnAdd(handle) {
    window.setTimeout(function() {
      this.registerScrollbar(handle)
      const card = this.find(handle)
      card.onAdd.forEach(function (callback) {
        callback(this.cardContext(card.handle))
      }.bind(this))
    }.bind(this), 1)
  },

  /**
   * Fire any remove callbacks for a card
   * @param handle
   */
  fireOnRemove(handle) {
    window.setTimeout(function() {
      const card = this.find(handle)
      card.onRemove.forEach(function (callback) {
        callback(this.cardContext(card.handle))
      }.bind(this))
    }.bind(this), 1)
  },

  /**
   * Add card HTML to the dashboard
   * @param handle
   * @param html
   */
  addCardHtml(handle, html = null) {
    if (this.findEl(handle)) {
      return
    }
    const card = this.find(handle)
    this.container().insertAdjacentHTML('beforeend', html)
    card.element = this.findEl(handle)
    this.fireOnAdd(handle)
  },

  /**
   * Remove a card from the dashboard
   * @param handle
   */
  remove: function(handle) {
    const card = this.find(handle)
    card.element.remove()
    card.element = null
    this.fireOnRemove(handle)
    let body = new URLSearchParams()
    body.append('card_handle', handle)

    //Tell the server about it
    fetch('/wp-json/dt-dashboard/v1/user/cards/hide', {
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
    const cardEl = this.findEl(handle)
    const cardBody = cardEl.querySelector('.card-body--scroll')
    if (!cardBody) {
      return
     }
    let Scrollbar = window.Scrollbar
    Scrollbar.init(cardBody)
  },
  /**
   * Add an add callback to a card
   * @param handle
   * @param callback
   */
  onAdd: function(handle, callback) {
    const card = this.find(handle)
    card.onAdd.push(callback)
    if (this.initalized) {
      callback(this.cardContext(card.handle))
    }
  },

  /**
   * Add a remove callback to a card
   * @param handle
   * @param callback
   */
  onRemove: function(handle, callback) {
    this.find(handle).onRemove.push(callback)
  },

  /**
   * Render the add menu. If there are no active cards, it will remove it.
   */
  renderAddMenu() {
    let addCardContainer = this.addCardEl()

    let addCardEl
    if (!addCardContainer) {
      addCardContainer = document.createElement('div')
      addCardContainer.classList.add('dash-card', 'add-card', 'item')
      addCardContainer.setAttribute('id', 'add-card')
      addCardContainer.style.order = this.cards.length + 1
      this.container().prepend(addCardContainer)

      addCardEl = document.createElement('div')
      addCardEl.classList.add('card')
      addCardContainer.appendChild(addCardEl)
    } else {
      addCardEl = addCardContainer.querySelector('.card')
    }

    addCardEl.innerHTML = ''

    let heading = document.createElement('span')
    heading.innerText = 'Add cards'
    heading.classList.add('card-header')
    addCardEl.appendChild(heading)

    let addMenu = document.createElement('ul')
    addMenu.setAttribute('id', 'add-menu')
    addCardEl.appendChild(addMenu)

    this.cards.forEach(function(card) {
      const menuItem = document.createElement('li')
      const lightSwitch = document.createElement('input')
      lightSwitch.type = 'checkbox'
      lightSwitch.checked = this.isActive(card.handle)
      menuItem.innerHTML = card.label
      menuItem.appendChild(lightSwitch)
      new Switchery(lightSwitch, {
        color: '#4BAF50',
        secondaryColor: '#B4B4B4',
      });
      lightSwitch.addEventListener('change', function() {
        setTimeout(() => lightSwitch.checked ? this.add(card.handle) : this.remove(card.handle), 500)
      }.bind(this))
      addMenu.appendChild(menuItem)
    }.bind(this))
  },

  /**
   * Move a card forward in the DOM
   * @param handle
   */
  moveForward: function(handle) {
    if (!this.canMoveForward(handle)) {
      return
    }
    const cardEl = this.findEl(handle)
    const nextEl = cardEl.nextElementSibling
    nextEl.after(cardEl)
    this.storeSort()
    this.refreshClasses()
  },

  /**
   * Move a card back in the DOM
   * @param handle
   */
  moveBack: function(handle) {
    if (!this.canMoveBack(handle)) {
      return
    }
    const cardEl = this.findEl(handle)
    const prevEl = cardEl.previousElementSibling
    prevEl.before(cardEl)
    this.storeSort()
    this.refreshClasses()
  },

  /**
   * Store the sort on the server
   */
  storeSort() {
    const sort = Array.from(this.cardEls()).map(function(el) {
      return el.dataset.cardHandle
    })

    let body = new URLSearchParams()
    body.append('card_sort', JSON.stringify(sort))

    fetch('/wp-json/dt-dashboard/v1/user/cards/sort', {
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
    if (this.inactiveCards().length) {
      this.root().classList.add('dash-cards--has-inactive')
    } else {
      this.root().classList.remove('dash-cards--has-inactive')
    }

    Array.from(this.cardEls()).forEach(function(cardEl) {
      if (this.canMoveBack(cardEl.dataset.cardHandle)) {
        cardEl.classList.remove('data-card--first')
      } else {
        cardEl.classList.add('data-card--first')
      }

      if (this.canMoveForward(cardEl.dataset.cardHandle)) {
        cardEl.classList.remove('data-card--last')
      } else {
        cardEl.classList.add('data-card--last')
      }
    }.bind(this))
  }
}
