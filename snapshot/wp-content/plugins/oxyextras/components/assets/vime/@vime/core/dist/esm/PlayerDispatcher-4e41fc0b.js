import { g as getElement } from './index-e4fee97f.js';
import { i as isInstanceOf } from './dom-888fcf0c.js';

/**
 * Creates a dispatcher on the given `ref`, to send updates to the closest ancestor player via
 * the custom `vStateChange` event.
 *
 * @param ref An element to dispatch the state change events from.
 */
const createDispatcher = (ref) => (prop, value) => {
  const el = isInstanceOf(ref, HTMLElement) ? ref : getElement(ref);
  const event = new CustomEvent('vStateChange', {
    bubbles: true,
    composed: true,
    detail: { by: el, prop, value },
  });
  el.dispatchEvent(event);
};

export { createDispatcher as c };
