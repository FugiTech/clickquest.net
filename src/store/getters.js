import { levelForClicks } from '../utils'

export const userColor = (state) => {
  let types = ['Normal', 'Dark', 'Light']
  for (let i = 0; i < state.colors.length; i++) {
    for (let j = 0; j < types.length; j++) {
      if (state.colors[i][types[j]] === state.user.color) {
        let color = state.colors[i]
        color.selected = types[j]
        return color
      }
    }
  }
  return {Name: 'default', Normal: '#FFFFFF', selected: 'Normal'}
}

export const level = (state) => {
  if (!state.user) return 0
  return levelForClicks(state.user.clicks, state.user.hardcore)
}
