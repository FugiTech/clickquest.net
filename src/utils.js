
export const clicksForLevel = (level, hardcore) => {
  let initial = hardcore ? 200 : 100
  let rate = hardcore ? 1.110316 : 1.0906595 // matches faulty behavior of Clickquest v1
  return Math.round(initial * (1 - Math.pow(rate, level)) / (1 - rate))
}

export const increaseForLevel = (level, hardcore) => {
  return clicksForLevel(level, hardcore) - clicksForLevel(level - 1, hardcore)
}
