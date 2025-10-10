import { getElement } from '@stencil/core';
import { isInstanceOf } from '../../../utils/unit';
/**
 * Creates a dispatcher on the given `ref`, to send updates to the closest ancestor player via
 * the custom `vStateChange` event.
 *
 * @param ref An element to dispatch the state change events from.
 */
export const createDispatcher = (ref) => (prop, value) => {
  const el = isInstanceOf(ref, HTMLElement) ? ref : getElement(ref);
  const event = new CustomEvent('vStateChange', {
    bubbles: true,
    composed: true,
    detail: { by: el, prop, value },
  });
  el.dispatchEvent(event);
};
