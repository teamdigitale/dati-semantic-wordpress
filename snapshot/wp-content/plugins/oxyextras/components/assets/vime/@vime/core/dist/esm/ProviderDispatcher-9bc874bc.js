import { g as getElement } from './index-e4fee97f.js';
import { i as isInstanceOf } from './dom-888fcf0c.js';
import { w as withPlayerContext } from './PlayerContext-da67ca53.js';

/* eslint-disable func-names, no-param-reassign */
function withProviderConnect(host) {
  const el = getElement(host);
  const buildConnectEvent = (name) => new CustomEvent(name, {
    bubbles: true,
    composed: true,
    detail: host,
  });
  const connectEvent = buildConnectEvent('vMediaProviderConnect');
  const disconnectEvent = buildConnectEvent('vMediaProviderDisconnect');
  const { componentWillLoad, connectedCallback, disconnectedCallback } = host;
  host.componentWillLoad = function () {
    el.dispatchEvent(connectEvent);
    if (componentWillLoad)
      return componentWillLoad.call(host);
    return undefined;
  };
  host.connectedCallback = function () {
    el.dispatchEvent(connectEvent);
    if (connectedCallback)
      connectedCallback.call(host);
  };
  host.disconnectedCallback = function () {
    el.dispatchEvent(disconnectEvent);
    if (disconnectedCallback)
      disconnectedCallback.call(host);
  };
}
const withProviderContext = (provider, additionalProps = []) => withPlayerContext(provider, [
  'autoplay',
  'controls',
  'language',
  'muted',
  'logger',
  'loop',
  'aspectRatio',
  'playsinline',
  ...additionalProps,
]);

/**
 * Creates a dispatcher on the given `ref`, to send updates to the closest ancestor player via
 * the custom `vProviderChange` event.
 *
 * @param ref A component reference to dispatch the state change events from.
 */
const createProviderDispatcher = (ref) => (prop, value) => {
  const el = isInstanceOf(ref, HTMLElement) ? ref : getElement(ref);
  const event = new CustomEvent('vProviderChange', {
    bubbles: true,
    composed: true,
    detail: { by: el, prop, value },
  });
  el.dispatchEvent(event);
};

export { withProviderConnect as a, createProviderDispatcher as c, withProviderContext as w };
