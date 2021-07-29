window.dt_dashboard = {
  initialized: false,
  cards: [],
  init() {
    this.cards = JSON.parse(this.root().dataset.cards).map(function(card) {
      card.element = this.findEl(card.handle);
      card.onAdd = []
      card.onRemove = []
      return card
    }.bind(this))
    this.initCards()
    this.renderAddMenu()
    this.initalized = true
  },
  root() {
    return document.querySelector('#dashboard')
  },
  container() {
    return document.querySelector('.dash-cards')
  },
  cardsEls() {
    return document.querySelectorAll('.dash-card:not(.add-card)')
  },
  addCardEl() {
    return document.querySelector('#add-card')
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
  inactiveCards() {
    return this.cards.filter(function(card) {
      return !card.element
    })
  },
  initCards() {
    this.activeCards().forEach(function(card) {
      this.fireOnAdd(card.handle)
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
        let response = JSON.parse(data)
        this.addCardHtml(handle, response.template)
        this.renderAddMenu()
        this.storeSort()
      }.bind(this))
  },
  fireOnAdd(handle) {
    window.setTimeout(function() {
      const card = this.find(handle)
      card.onAdd.forEach(function (callback) {
        callback(this.cardContext(card.handle))
      }.bind(this))
    }.bind(this), 1)
  },
  fireOnRemove(handle) {
    window.setTimeout(function() {
      const card = this.find(handle)
      card.onRemove.forEach(function (callback) {
        callback(this.cardContext(card.handle))
      }.bind(this))
    }.bind(this), 1)
  },
  addCardHtml(handle, html = null) {
    if (this.findEl(handle)) {
      return
    }
    const card = this.find(handle)
    this.container().insertAdjacentHTML('beforeend', html)
    card.element = this.findEl(handle)
    this.fireOnAdd(handle)
  },
  remove: function(handle) {
    const card = this.find(handle)
    card.element.remove()
    card.element = null
    this.fireOnRemove(handle)
    let body = new URLSearchParams()
    body.append('card_handle', handle)

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
  onAdd: function(handle, callback) {
    const card = this.find(handle)
    card.onAdd.push(callback)
    if (this.initalized) {
      callback(this.cardContext(card.handle))
    }
  },
  onRemove: function(handle, callback) {
    this.find(handle).onRemove.push(callback)
  },
  renderAddMenu() {
    let addCardContainer = this.addCardEl()

    if (!this.inactiveCards().length) {
      if (addCardContainer) {
        addCardContainer.remove()
      }
      return
    }

    let addCardEl
    if (!addCardContainer) {
      addCardContainer = document.createElement('div')
      addCardContainer.classList.add('dash-card', 'add-card', 'item')
      addCardContainer.setAttribute('id', 'add-card')
      addCardContainer.style.order = this.cards.length + 1
      this.container().appendChild(addCardContainer)

      addCardEl = document.createElement('div')
      addCardEl.classList.add('card')
      addCardContainer.appendChild(addCardEl)
    } else {
      addCardEl = addCardContainer.querySelector('.card')
    }

    addCardEl.innerHTML = ''

    let heading = document.createElement('span')
    heading.innerText = 'Add cards'
    heading.classList.add('card-title')
    addCardEl.appendChild(heading)

    let addMenu = document.createElement('ul')
    addMenu.setAttribute('id', 'add-menu')
    addCardEl.appendChild(addMenu)

    this.inactiveCards().forEach(function(card) {
      const menuItem = document.createElement('li')
      menuItem.innerText = card.label
      menuItem.addEventListener('click', function() {
        this.add(card.handle)
      }.bind(this))
      addMenu.appendChild(menuItem)
    }.bind(this))
  },
  moveForward: function(handle) {
    const cardEl = this.findEl(handle)
    if (!cardEl) {
      return
    }
    const nextEl = cardEl.nextElementSibling
    if (!nextEl) {
      return
    }
    nextEl.after(cardEl)
    this.storeSort()
  },
  moveBack: function(handle) {
    const cardEl = this.findEl(handle)
    if (!cardEl) {
      return
    }
    const prevEl = cardEl.previousElementSibling
    if (!prevEl || prevEl === this.addCardEl()) {
      return
    }
    prevEl.before(cardEl)
    this.storeSort()
  },
  storeSort() {
    const sort = Array.from(this.cardsEls()).map(function(el) {
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
  }
}
