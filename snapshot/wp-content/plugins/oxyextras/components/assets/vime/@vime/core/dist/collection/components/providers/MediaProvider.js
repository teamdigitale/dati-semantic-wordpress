/* eslint-disable func-names, no-param-reassign */
import { getElement } from '@stencil/core';
import { withPlayerContext } from '../core/player/PlayerContext';
export function withProviderConnect(host) {
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
export const withProviderContext = (provider, additionalProps = []) => withPlayerContext(provider, [
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
