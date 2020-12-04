import Linkify from 'linkify-it'
import Token from './token'

let linkifier = new Linkify()

export default function autolink (state) {
  for (let i = state.tokens.length - 1; i >= 0; i--) {
    let token = state.tokens[i]

    // Skip tokens inside of links
    if (token.type === 'link_close') {
      i--
      while (state.tokens[i].level !== token.level && state.tokens[i].type !== 'link_open') {
        i--
      }
      continue
    }

    if (token.type === 'text' && linkifier.test(token.content)) {
      let links = linkifier.match(token.content)
      let nodes = [i, 1] // Used for splice.apply
      let pos = 0

      links.forEach((link) => {
        if (link.index > pos) {
          let t = new Token('text', '', 0)
          t.content = token.content.slice(pos, link.index)
          t.level = token.level
          nodes.push(t)
        }

        let t = new Token('link_open', 'a', 1)
        t.attrs = [['href', link.url], ['target', '_blank']]
        t.level = token.level
        nodes.push(t)

        t = new Token('text', '', 0)
        t.content = link.text
        t.level = token.level + 1
        nodes.push(t)

        t = new Token('link_close', 'a', -1)
        t.level = token.level
        nodes.push(t)

        pos = link.lastIndex
      })
      if (pos < token.content.length) {
        let t = new Token('text', '', 0)
        t.content = token.content.slice(pos)
        t.level = token.level
        nodes.push(t)
      }

      state.tokens.splice.apply(state.tokens, nodes)
    }
  }
}
