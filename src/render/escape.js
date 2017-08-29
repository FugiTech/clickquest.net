let ESCAPED = []
for (let i = 0; i < 256; i++) { ESCAPED.push(0) }
'\\*_[]'.split('').forEach(function (ch) { ESCAPED[ch.charCodeAt(0)] = 1 })

// Process escaped chars
export default function escape (state) {
  let ch
  let pos = state.pos
  let max = state.posMax
  if (state.src.charCodeAt(pos) !== 0x5C/* \ */) { return false }

  pos++

  if (pos < max) {
    ch = state.src.charCodeAt(pos)

    if (ch < 256 && ESCAPED[ch] !== 0) {
      state.pending += state.src[pos]
      state.pos += 2
      return true
    }
  }

  state.pending += '\\'
  state.pos++
  return true
}
