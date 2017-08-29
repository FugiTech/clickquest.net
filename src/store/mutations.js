export const login = (state, user) => {
  state.isLoggedIn = true
  state.user = user
}

export const updateUser = (state, user) => {
  state.user = {...state.user, ...user}
}

export const logout = (state) => {
  state.isLoggedIn = false
  state.user = {}
}

let lastClick = 0
export const click = (state) => {
  if (!state.isLoggedIn) return
  let now = new Date()
  if (now - lastClick < 1000.0 / 12) return // 12cps is a lot
  if (
    state.user.clicks > 6666600 &&
    state.user.hardcore === 0 &&
    now - lastClick < 1000
  ) return // Slow down clicks right before premotion to hardcore

  state.user.clicks++
  lastClick = now
}

export const setError = (state, error) => {
  state.error = error
}

export const registerConn = (state, data) => {
  state.send = data
}

export const conn$stats = (state, data) => {
  state.stats = data
}

export const conn$chat = (state, data) => {
  state.chat.push.apply(state.chat, data)
  state.chat = state.chat.slice(-100)
}

export const conn$players = (state, data) => {
  state.players = data
}

export const conn$logout = (state) => {
  logout(state)
  state.error = 'Disconnected due to login from another location'
}

export const heartbeat = (state, data) => {
  if (!state.isLoggedIn || !data) return
  state.user.clicks += data.ClickAdjustment
  state.user.hardcore = data.Hardcore
  state.user.color = data.Color
}
