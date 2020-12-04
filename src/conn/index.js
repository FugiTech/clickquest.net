import store from '../store'

const debug = process.env.NODE_ENV !== 'production'

class Conn {
  constructor () {
    this._id = 1
    this.waiting = {}
    this.buffer = []
    this.timer = null
    this.connect()
  }

  handle (e) {
    let data = JSON.parse(e.data)
    if (data.Data && data.Data.error) {
      store.commit('setError', data.Data.error)
      if (data.ID) delete this.waiting[data.ID]
      return
    }

    if (data.ID) {
      this.waiting[data.ID](data.Data)
      delete this.waiting[data.ID]
    } else {
      store.commit('conn$' + data.Type, data.Data)
    }
  }

  send (type, data) {
    let id = this._id++
    let p = new Promise((resolve) => {
      this.waiting[id] = resolve
    })
    this._send(JSON.stringify({ID: '' + id, Type: type, Data: data}))
    return p
  }

  _send (mes) {
    if (this._conn.readyState === WebSocket.OPEN) {
      this._conn.send(mes)
    } else {
      this.buffer.push(mes)
    }
  }

  connect () {
    this._conn = new WebSocket(debug ? 'ws://localhost:9999' : 'wss://api.clickquest.net')
    this._conn.onopen = this.connected.bind(this)
    this._conn.onmessage = this.handle.bind(this)
    this._conn.onclose = this.disconnect.bind(this)
  }

  connected () {
    this.buffer.forEach((mes) => {
      this._conn.send(mes)
    })
    this.buffer = []
    this.timer = setInterval(() => { store.dispatch('heartbeat') }, 10000) // 10 seconds
  }

  disconnect () {
    if (this.timer) {
      clearInterval(this.timer)
      this.timer = null
    }
    for (var key in this.waiting) {
      this.waiting[key]({error: 'websocket disconnected'})
      delete this.waiting[key]
    }
    store.commit('logout')
    this.connect()
  }
}

const _conn = new Conn()
store.commit('registerConn', _conn.send.bind(_conn))
