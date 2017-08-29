import tokenize from './tokenize'
import escapeHTML from './escape_html'

let processors = {
  text: (tokens, idx) => { return escapeHTML(tokens[idx].content) }
}

const renderAttrs = (token) => {
  let i, l, result

  if (!token.attrs) { return '' }

  result = ''
  for (i = 0, l = token.attrs.length; i < l; i++) {
    result += ' ' + escapeHTML(token.attrs[i][0]) + '="' + escapeHTML(token.attrs[i][1]) + '"'
  }

  return result
}

export default function render (message, privileged) {
  let type
  let tokens = tokenize(message, privileged)
  let result = ''

  for (let i = 0, len = tokens.length; i < len; i++) {
    type = tokens[i].type

    if (typeof processors[type] !== 'undefined') {
      result += processors[type](tokens, i)
    } else {
      result += (tokens[i].nesting === -1 ? '</' : '<') + tokens[i].tag
      result += renderAttrs(tokens[i])
      result += '>'
    }
  }

  return result
}
