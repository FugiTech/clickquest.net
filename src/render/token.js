export default class Token {
  constructor (type, tag, nesting) {
    this.type = type       // Type of the token (string, e.g. "paragraph_open")
    this.tag = tag         // html tag name, e.g. "p"
    this.attrs = null      // Html attributes. Format: `[ [ name1, value1 ], [ name2, value2 ] ]`
    this.nesting = nesting // Level change (number in {-1, 0, 1} set), where: 1 = open, 0 = self-close, -1 = close
    this.level = 0         // nesting level, the same as `state.level`
    this.content = ''      // In a case of self-closing tag (code, html, fence, etc.), it has contents of this tag.
  }
}
