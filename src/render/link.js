import tokenize from './tokenize'

export default function link (state, label) {
  if (state.src.charCodeAt(state.pos) !== 0x28/* ( */) { return false }

  let url = ''
  for (state.pos++; state.pos < state.posMax; state.pos++) {
    if (state.src.charCodeAt(state.pos) === 0x29/* ) */) {
      let token = state.push('link_open', 'a', 1)
      token.attrs = [['href', url], ['target', '_blank']]

      state.tokens.push.apply(state.tokens, tokenize(label, state.privileged))

      state.push('link_close', 'a', -1)

      state.pos++
      return true
    }
    url += state.src[state.pos]
  }
}
