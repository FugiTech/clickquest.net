import tokenize from './tokenize'

export default function italic (state) {
  // Must be at the beginning or preceded by a space
  if (state.pos !== 0 && state.src.charCodeAt(state.pos - 1) !== 0x20/* <space> */) { return false }
  // Current character must be _
  if (state.src.charCodeAt(state.pos) !== 0x5F/* _ */) { return false }
  // Next character must NOT be space
  if (state.pos === state.posMax - 1 || state.src.charCodeAt(state.pos + 1) === 0x20/* <space> */) { return false }

  let origPos = state.pos
  let content = ''
  for (state.pos++; state.pos < state.posMax; state.pos++) {
    if (
      // Look for the ending _
      state.src.charCodeAt(state.pos) === 0x5F/* _ */ &&
      // Which must be at the end or followed by a space
      (state.pos === state.posMax - 1 || state.src.charCodeAt(state.pos + 1) === 0x20/* <space> */) &&
      // And preceded by not a space
      state.src.charCodeAt(state.pos - 1) !== 0x20/* <space> */
    ) {
      state.push('italic_open', 'i', 1)

      state.tokens.push.apply(state.tokens, tokenize(content, state.privileged))

      state.push('italic_close', 'i', -1)

      state.pos++
      return true
    }

    content += state.src[state.pos]
  }

  state.pos = origPos
}
