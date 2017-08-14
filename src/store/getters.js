import { clicksForLevel } from '../utils'

export const userTitle = (state) => {
  return state.user.hardcore ? '[HARDCORE] ' + state.user.name : state.user.name
}

export const userStyle = (state) => {
  return {
    color: state.user.color
  }
}

export const nextClicks = (state) => {
  let next = clicksForLevel(state.user.level + 1, state.user.hardcore)
  return next - state.user.clicks
}
