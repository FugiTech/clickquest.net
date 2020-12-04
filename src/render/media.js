import link from './link'
import image from './image'
import style from './style'

const rules = [
  link,
  image,
  style
]

export default function media (state) {
  if (state.src.charCodeAt(state.pos) !== 0x5B/* [ */) { return false }

  let marker
  let label = ''
  let level = 1
  let origPos = state.pos
  let max = state.posMax
  let found = false
  for (state.pos++; state.pos < max; state.pos++) {
    marker = state.src.charCodeAt(state.pos)
    if (marker === 0x5D /* ] */) {
      level--
      if (level === 0) {
        found = true
        break
      }
    }

    label += state.src[state.pos]

    if (marker === 0x5B /* [ */) {
      level++
    }
  }

  if (!found) {
    state.pos = origPos
    return false
  }
  state.pos++

  // Shell out to handlers for specific media-types
  let ok = false
  for (let i = 0; i < rules.length; i++) {
    ok = rules[i](state, label)
    if (ok) break
  }
  if (!ok) {
    state.pos = origPos
    return false
  }
  return true
}
