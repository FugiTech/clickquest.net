export const login = (state, user) => {
  state.isLoggedIn = true
  state.user = user
}

export const logout = (state) => {
  state.isLoggedIn = false
  state.user = {}
}

export const click = (state) => {
  if (!state.isLoggedIn) return
  state.user.clicks++
}
