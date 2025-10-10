import { g as getElement } from './index-e4fee97f.js';
import { i as isInstanceOf, a as isNull, l as listen } from './dom-888fcf0c.js';

var createDeferredPromise = function () {
    var resolve;
    var promise = new Promise(function (res) { resolve = res; });
    return { promise: promise, resolve: resolve };
};

var openWormhole = function (Component, props, isBlocking) {
    if (isBlocking === void 0) { isBlocking = true; }
    var isConstructor = (Component.constructor.name === 'Function');
    var Proto = isConstructor ? Component.prototype : Component;
    var componentWillLoad = Proto.componentWillLoad;
    Proto.componentWillLoad = function () {
        var _this = this;
        var el = getElement(this);
        var onOpen = createDeferredPromise();
        var event = new CustomEvent('openWormhole', {
            bubbles: true,
            composed: true,
            detail: {
                consumer: this,
                fields: props,
                updater: function (prop, value) { _this[prop] = value; },
                onOpen: onOpen,
            },
        });
        el.dispatchEvent(event);
        var willLoad = function () {
            if (componentWillLoad) {
                return componentWillLoad.call(_this);
            }
        };
        return isBlocking ? onOpen.promise.then(function () { return willLoad(); }) : (willLoad());
    };
};

/**
 * Finds the closest ancestor player element.
 *
 * @param ref A HTMLElement that is within the player's subtree.
 */
const findRootPlayer = (ref) => {
  const root = isInstanceOf(ref, HTMLElement) ? ref : getElement(ref);
  let player = root;
  while (!isNull(player) && !(/^VIME-PLAYER$/.test(player === null || player === void 0 ? void 0 : player.nodeName))) {
    player = player.parentElement;
  }
  if (isNull(player)) {
    throw Error(`Can't find root player given: ${root}`);
  }
  return player;
};

// Events that toggle state and the prop is named `is{PropName}...`.
const isToggleStateEvent = new Set([
  'isFullscreenActive',
  'isControlsActive',
  'isPiPActive',
  'isLive',
  'isTouch',
  'isAudio',
  'isVideo',
  'isAudioView',
  'isVideoView',
]);
// Events that are emitted without the 'Change' postfix.
const hasShortenedEventName = new Set([
  'ready',
  'playbackStarted',
  'playbackEnded',
  'playbackReady',
]);
const getEventName = (prop) => {
  // Example: isFullscreenActive -> vFullscreenChange
  if (isToggleStateEvent.has(prop)) {
    return `v${prop.replace('is', '').replace('Active', '')}Change`;
  }
  // Example: playbackStarted -> vPlaybackStarted
  if (hasShortenedEventName.has(prop)) {
    return `v${prop.charAt(0).toUpperCase()}${prop.slice(1)}`;
  }
  // Example: currentTime -> vCurrentTimeChange
  return `v${prop.charAt(0).toUpperCase()}${prop.slice(1)}Change`;
};

/**
 * Binds props between an instance of a given component class and it's closest ancestor player.
 *
 * @param Component A Stencil component class or instance.
 * @param props A set of props to watch and update on the given component instance.
 */
const withPlayerContext = (Component, props) => openWormhole(Component, props);
/**
 * Finds the closest ancestor player to the given `ref` and watches the given props for changes. On
 * a prop change the given `updater` fn is called.
 *
 * @param ref A element within any player's subtree.
 * @param props A set of props to watch and call the `updater` fn with.
 * @param updater This function is called with the prop/value of any watched properties.
 */
const usePlayerContext = (ref, props, updater, playerRef) => {
  const player = playerRef !== null && playerRef !== void 0 ? playerRef : findRootPlayer(ref);
  const listeners = props.map((prop) => {
    const event = getEventName(prop);
    return listen(player, event, () => { updater(prop, player[prop]); });
  });
  return () => {
    listeners.forEach((off) => off());
  };
};

export { findRootPlayer as f, getEventName as g, usePlayerContext as u, withPlayerContext as w };
