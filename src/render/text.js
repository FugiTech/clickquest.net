let isTerminatorChar = (ch) => {
  switch (ch) {
    case 0x5C: // \
    case 0x2A: // *
    case 0x5F: // _
    case 0x5B: // [
      return true
    default:
      return false
  }
}

// Skip pure text
export default function text (state) {
  let pos = state.pos

  while (pos < state.posMax && !isTerminatorChar(state.src.charCodeAt(pos))) {
    pos++
  }

  if (pos === state.pos) { return false }

  state.pending += state.src.slice(state.pos, pos)
  state.pos = pos
  return true
}
