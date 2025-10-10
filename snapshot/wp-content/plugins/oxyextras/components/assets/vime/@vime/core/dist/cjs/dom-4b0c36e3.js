'use strict';

const isNull = (input) => input === null;
const isUndefined = (input) => typeof input === 'undefined';
const isNullOrUndefined = (input) => isNull(input) || isUndefined(input);
const getConstructor = (input) => (!isNullOrUndefined(input) ? input.constructor : undefined);
const isObject = (input) => getConstructor(input) === Object;
const isNumber = (input) => getConstructor(input) === Number && !Number.isNaN(input);
const isString = (input) => getConstructor(input) === String;
const isBoolean = (input) => getConstructor(input) === Boolean;
const isFunction = (input) => getConstructor(input) === Function;
const isArray = (input) => Array.isArray(input);
const isInstanceOf = (input, constructor) => Boolean(input && constructor && input instanceof constructor);

/**
 * Listen to an event on the given DOM node. Returns a callback to remove the event listener.
 */
function listen(node, event, handler, options) {
  node.addEventListener(event, handler, options);
  return () => node.removeEventListener(event, handler, options);
}
const findShadowRoot = (el) => {
  if (el instanceof ShadowRoot)
    return el;
  if (!el.parentNode)
    return null;
  return findShadowRoot(el.parentNode);
};
const isColliding = (a, b, translateAx = 0, translateAy = 0, translateBx = 0, translateBy = 0) => {
  const aRect = a.getBoundingClientRect();
  const bRect = b.getBoundingClientRect();
  return ((aRect.left + translateAx) < (bRect.right + translateBx))
    && ((aRect.right + translateAx) > (bRect.left + translateBx))
    && ((aRect.top + translateAy) < (bRect.bottom + translateBy))
    && ((aRect.bottom + translateAy) > (bRect.top + translateBy));
};
const buildNoAncestorSelector = (root, ancestor, selector, depth) => {
  const baseQuery = (modifier) => `${root} > ${modifier} ${selector}, `;
  const buildQuery = (deep = 1) => baseQuery(`:not(${ancestor}) >`.repeat(deep));
  let query = buildQuery(1);
  for (let i = 2; i < (depth + 1); i += 1) {
    query += buildQuery(i);
  }
  return query.slice(0, -2);
};

exports.buildNoAncestorSelector = buildNoAncestorSelector;
exports.findShadowRoot = findShadowRoot;
exports.isArray = isArray;
exports.isBoolean = isBoolean;
exports.isColliding = isColliding;
exports.isFunction = isFunction;
exports.isInstanceOf = isInstanceOf;
exports.isNull = isNull;
exports.isNullOrUndefined = isNullOrUndefined;
exports.isNumber = isNumber;
exports.isObject = isObject;
exports.isString = isString;
exports.isUndefined = isUndefined;
exports.listen = listen;
