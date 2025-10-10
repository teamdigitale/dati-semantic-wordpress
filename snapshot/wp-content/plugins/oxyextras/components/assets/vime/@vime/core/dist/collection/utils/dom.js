/**
 * Listen to an event on the given DOM node. Returns a callback to remove the event listener.
 */
export function listen(node, event, handler, options) {
  node.addEventListener(event, handler, options);
  return () => node.removeEventListener(event, handler, options);
}
export const findShadowRoot = (el) => {
  if (el instanceof ShadowRoot)
    return el;
  if (!el.parentNode)
    return null;
  return findShadowRoot(el.parentNode);
};
export const isColliding = (a, b, translateAx = 0, translateAy = 0, translateBx = 0, translateBy = 0) => {
  const aRect = a.getBoundingClientRect();
  const bRect = b.getBoundingClientRect();
  return ((aRect.left + translateAx) < (bRect.right + translateBx))
    && ((aRect.right + translateAx) > (bRect.left + translateBx))
    && ((aRect.top + translateAy) < (bRect.bottom + translateBy))
    && ((aRect.bottom + translateAy) > (bRect.top + translateBy));
};
export const buildNoAncestorSelector = (root, ancestor, selector, depth) => {
  const baseQuery = (modifier) => `${root} > ${modifier} ${selector}, `;
  const buildQuery = (deep = 1) => baseQuery(`:not(${ancestor}) >`.repeat(deep));
  let query = buildQuery(1);
  for (let i = 2; i < (depth + 1); i += 1) {
    query += buildQuery(i);
  }
  return query.slice(0, -2);
};
