export default function image (state, label) {
  if (!state.privileged) { return false }
  if (state.src.charCodeAt(state.pos) !== 0x5B/* [ */) { return false }

  let url = ''
  for (state.pos++; state.pos < state.posMax; state.pos++) {
    if (state.src.charCodeAt(state.pos) === 0x5D/* ] */) {
      let token = state.push('link_open', 'a', 1)
      token.attrs = [['href', url], ['target', '_blank']]

      token = state.push('image', 'img', 0)
      token.attrs = [['src', url], ['alt', label]]

      state.push('link_close', 'a', -1)

      state.pos++
      return true
    }
    url += state.src[state.pos]
  }
}
