import tokenize from './tokenize'

export default function style (state, label) {
  if (!state.privileged) { return false }
  if (state.src.charCodeAt(state.pos) !== 0x7B/* { */) { return false }

  let css = ''
  for (state.pos++; state.pos < state.posMax; state.pos++) {
    if (state.src.charCodeAt(state.pos) === 0x7D/* } */) {
      let token = state.push('style_open', 'span', 1)
      token.attrs = [['style', css]]

      state.tokens.push.apply(state.tokens, tokenize(label, state.privileged))

      state.push('style_close', 'span', -1)

      state.pos++
      return true
    }
    css += state.src[state.pos]
  }
}
