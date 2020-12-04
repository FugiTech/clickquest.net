import State from './state'

import text from './text'
import escape from './escape'
import bold from './bold'
import italic from './italic'
import media from './media'
import autolink from './auto_link'

let rules = [
  text,
  escape,
  bold,
  italic,
  media
]

export default function tokenize (message, privileged) {
  let state = new State(message, privileged)
  let ok, i
  let len = rules.length
  let end = state.posMax
  let maxNesting = 100

  while (state.pos < end) {
    if (state.level < maxNesting) {
      for (i = 0; i < len; i++) {
        ok = rules[i](state)
        if (ok) break
      }
    }

    if (ok) continue

    state.pending += state.src[state.pos++]
  }

  if (state.pending) { state.pushPending() }

  autolink(state)

  return state.tokens
}
