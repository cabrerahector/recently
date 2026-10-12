export function strip_html(value) {
  if (!value) return '';

  const parser = new DOMParser();
  const doc = parser.parseFromString(value, 'text/html');

  return doc.body.textContent || '';
}
