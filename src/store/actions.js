export const login = ({ commit, state }, e) => {
  let form = e.target.form || e.target
  let username = form[0].value || 'Username'
  let password = form[1].value || 'Password'
  let register = !!e.isRegister
  form.reset()
  return state.send('login', { username, password, register }).then((data) => {
    commit('login', data)
  })
}

export const logout = ({ commit, state }) => {
  return state.send('logout').then((data) => {
    commit('logout')
  })
}

export const setColor = ({ commit, state }, color) => {
  return state.send('setcolor', { color }).then((data) => {
    commit('updateUser', { color: data.Color })
  })
}

export const heartbeat = ({ commit, state }) => {
  let clicks = state.user.clicks || 0
  return state.send('heartbeat', { clicks }).then((data) => {
    commit('heartbeat', data)
  })
}
