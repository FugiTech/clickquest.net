export const login = ({ commit }) => {
  commit('login', {
    hardcore: true,
    name: 'Fugiman',
    level: 126,
    clicks: 1000018013,
    color: '#CCCCCC',
    sessionStart: (new Date()) * 1
  })
}

export const logout = ({ commit }) => {
  commit('logout')
}

export const sendMessage = ({ commit }, e) => {
  let message = e.target.elements[0].value
  e.target.elements[0].value = ''
  console.log(message)
}
