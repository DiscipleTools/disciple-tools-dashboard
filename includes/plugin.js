window.dt_dashboard = {
  initialized: false,
  cards: [],
  init() {
    this.cards = JSON.parse(this.root().dataset.cards).map(function(card) {
      card.element = this.findEl(card.handle);
      card.onAdd = function(){};
      card.onRemove = function(){};
      return card
    }.bind(this))
    this.initCards()
    this.initalized = true
  },
  root() {
    return document.querySelector('#dashboard')
  },
  cardsEls() {
    return document.querySelector('.dash-cards')
  },
  findEl: function(handle) {
    return document.querySelector('#dash-card--' + handle)
  },
  cardContext: function(handle) {
    const card = this.find(handle)
    return {
      wpApiDashboard: window.wpApiDashboard,
      element: card.element
    }
  },
  activeCards() {
    return this.cards.filter(function(card) {
      return !!card.element
    })
  },
  initCards() {
    this.activeCards().forEach(function(card) {
      card.onAdd(this.cardContext(card.handle))
    }.bind(this))
  },
  find: function(handle) {
    return this.cards.find(function(card) {
      return card.handle === handle
    })
  },
  isActive(handle) {
    !!this.find(handle).element
  },
  add: function(handle) {
    if (this.isActive(handle)) {
      return
    }

    let body = new URLSearchParams()
    body.append('card_handle', handle)

    fetch('/wp-json/dt-dashboard/v1/user/cards/show', {
      method: 'POST',
      headers: {
        'X-WP-Nonce': window.wpApiShare.nonce
      },
      body: body,
    })

    fetch('/wp-json/dt-dashboard/v1/card?' + body.toString(), {
      method: 'GET',
      headers: {
        'X-WP-Nonce': window.wpApiShare.nonce
      },
    })
      .then(response => response.json())
      .then(function(data) {
        this.addCardHtml(handle, data.template)
      }.bind(this))
  },
  addCardHtml(handle, html = null) {
    if (this.findEl(handle)) {
      return
    }
    const card = this.find(handle)
    this.root().insertAdjacentHTML('beforeend', html)
    card.element = this.findEl(handle)
    card.onAdd(this.cardContext(handle))
  },
  remove: function(handle) {
    const card = this.find(handle)
    card.element.remove()
    card.element = null
    card.onRemove(this.cardContext(handle))

    let body = new URLSearchParams()
    body.append('card_handle', handle)

    fetch('/wp-json/dt-dashboard/v1/user/cards/hide', {
      method: 'POST',
      headers: {
        'X-WP-Nonce': window.wpApiShare.nonce
      },
      body: body,
    })
  },
  onAdd: function(handle, callback) {
    const card = this.find(handle)
    card.onAdd = callback
    if (this.initalized) {
      card.onAdd(this.cardContext(card.handle))
    }
  },
  onRemove: function(handle, callback) {
    this.find(handle).onRemove = callback
  }
}
