import Token from './token'

export default class State {
  constructor (src, privileged) {
    this.src = src
    this.privileged = privileged
    this.tokens = []

    this.pos = 0
    this.posMax = src.length
    this.level = 0
    this.pending = ''
    this.pendingLevel = 0

    this.cache = {} // Used for emphasis
    this.delimiters = []
  }

  pushPending () {
    let token = new Token('text', '', 0)
    token.content = this.pending
    token.level = this.pendingLevel
    this.tokens.push(token)
    this.pending = ''
    return token
  }

  // Push new token to "stream".
  // If pending text exists - flush it as text token
  push (type, tag, nesting) {
    if (this.pending) {
      this.pushPending()
    }

    let token = new Token(type, tag, nesting)

    if (nesting < 0) { this.level-- }
    token.level = this.level
    if (nesting > 0) { this.level++ }

    this.pendingLevel = this.level
    this.tokens.push(token)
    return token
  }
}
