const levelForClicks = (clicks, hardcore) => {
  let initial = hardcore ? 200 : 100
  let rate = hardcore ? 1.110316 : 1.0906595 // matches faulty behavior of Clickquest v1
  return Math.floor(Math.log(1 + (rate - 1) * (clicks + 0.5) / initial) / Math.log(rate))
}

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
