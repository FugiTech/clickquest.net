let HTML_ESCAPE_TEST_RE = /[&<>"]/
let HTML_ESCAPE_REPLACE_RE = /[&<>"]/g
let HTML_REPLACEMENTS = {
  '&': '&amp;',
  '<': '&lt;',
  '>': '&gt;',
  '"': '&quot;'
}

const replaceUnsafeChar = (ch) => {
  return HTML_REPLACEMENTS[ch]
}

export default function escapeHtml (str) {
  if (HTML_ESCAPE_TEST_RE.test(str)) {
    return str.replace(HTML_ESCAPE_REPLACE_RE, replaceUnsafeChar)
  }
  return str
}
