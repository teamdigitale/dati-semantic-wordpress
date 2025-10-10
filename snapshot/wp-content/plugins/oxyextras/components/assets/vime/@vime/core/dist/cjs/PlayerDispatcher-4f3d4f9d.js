'use strict';

const index = require('./index-e8963331.js');
const dom = require('./dom-4b0c36e3.js');

/**
 * Creates a dispatcher on the given `ref`, to send updates to the closest ancestor player via
 * the custom `vStateChange` event.
 *
 * @param ref An element to dispatch the state change events from.
 */
const createDispatcher = (ref) => (prop, value) => {
  const el = dom.isInstanceOf(ref, HTMLElement) ? ref : index.getElement(ref);
  const event = new CustomEvent('vStateChange', {
    bubbles: true,
    composed: true,
    detail: { by: el, prop, value },
  });
  el.dispatchEvent(event);
};

exports.createDispatcher = createDispatcher;
