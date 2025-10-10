import { getElement, getRenderingRef, h, Host, createEvent, forceUpdate, writeTask, Fragment, proxyCustomElement } from '@stencil/core/internal/client';
export { setAssetPath } from '@stencil/core/internal/client';

const en = {
  play: 'Play',
  pause: 'Pause',
  playback: 'Playback',
  scrubber: 'Scrubber',
  scrubberLabel: '{currentTime} of {duration}',
  played: 'Played',
  duration: 'Duration',
  buffered: 'Buffered',
  close: 'Close',
  currentTime: 'Current time',
  live: 'LIVE',
  volume: 'Volume',
  mute: 'Mute',
  unmute: 'Unmute',
  captions: 'Captions',
  subtitlesOrCc: 'Subtitles/CC',
  enableCaptions: 'Enable subtitles/captions',
  disableCaptions: 'Disable subtitles/captions',
  auto: 'Auto',
  fullscreen: 'Fullscreen',
  enterFullscreen: 'Enter fullscreen',
  exitFullscreen: 'Exit fullscreen',
  settings: 'Settings',
  seekForward: 'Seek forward',
  seekBackward: 'Seek backward',
  seekTotal: 'Seek total',
  normal: 'Normal',
  none: 'None',
  playbackRate: 'Playback Rate',
  playbackQuality: 'Playback Quality',
  loop: 'Loop',
  disabled: 'Disabled',
  off: 'Off',
  enabled: 'Enabled',
  pip: 'Picture-in-Picture',
  enterPiP: 'Miniplayer',
  exitPiP: 'Expand',
};

const initialState = {
  theme: undefined,
  paused: true,
  playing: false,
  duration: -1,
  currentProvider: undefined,
  mediaTitle: undefined,
  currentSrc: undefined,
  currentPoster: undefined,
  currentTime: 0,
  autoplay: false,
  attached: false,
  ready: false,
  playbackReady: false,
  loop: false,
  muted: false,
  buffered: 0,
  playbackRate: 1,
  playbackRates: [1],
  playbackQuality: undefined,
  playbackQualities: [],
  seeking: false,
  debug: false,
  playbackStarted: false,
  playbackEnded: false,
  buffering: false,
  controls: false,
  isControlsActive: false,
  errors: [],
  textTracks: undefined,
  volume: 50,
  isFullscreenActive: false,
  aspectRatio: '16:9',
  viewType: undefined,
  isAudioView: false,
  isVideoView: false,
  mediaType: undefined,
  isAudio: false,
  isVideo: false,
  isMobile: false,
  isTouch: false,
  isCaptionsActive: false,
  isSettingsActive: false,
  currentCaption: undefined,
  isLive: false,
  isPiPActive: false,
  autopause: true,
  playsinline: false,
  language: 'en',
  languages: ['en'],
  translations: { en },
  i18n: en,
};
const writableProps = new Set([
  'autoplay',
  'autopause',
  'aspectRatio',
  'controls',
  'theme',
  'debug',
  'paused',
  'currentTime',
  'language',
  'loop',
  'translations',
  'playbackQuality',
  'muted',
  'errors',
  'playbackRate',
  'playsinline',
  'volume',
  'isSettingsActive',
  'isCaptionsActive',
  'isControlsActive',
]);
const isWritableProp = (prop) => writableProps.has(prop);
/**
 * Player properties that should be reset when the media is changed.
 */
const resetableProps = new Set([
  'paused',
  'currentTime',
  'duration',
  'buffered',
  'seeking',
  'playing',
  'buffering',
  'playbackReady',
  'mediaTitle',
  'currentSrc',
  'currentPoster',
  'playbackRate',
  'playbackRates',
  'playbackStarted',
  'playbackEnded',
  'playbackQuality',
  'playbackQualities',
  'textTracks',
  'mediaType',
  'isCaptionsActive',
]);
const shouldPropResetOnMediaChange = (prop) => resetableProps.has(prop);
const providerWritableProps = new Set([
  'ready',
  'playing',
  'playbackReady',
  'playbackStarted',
  'playbackEnded',
  'seeking',
  'buffered',
  'buffering',
  'duration',
  'viewType',
  'mediaTitle',
  'mediaType',
  'textTracks',
  'currentSrc',
  'currentPoster',
  'playbackRates',
  'playbackQualities',
  'isPiPActive',
  'isFullscreenActive',
]);
const isProviderWritableProp = (prop) => isWritableProp(prop) || providerWritableProps.has(prop);

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

var multiverse = new Map();
var updateConsumer = function (_a, state) {
    var fields = _a.fields, updater = _a.updater;
    fields.forEach(function (field) { updater(field, state[field]); });
};
var Universe = {
    create: function (creator, initialState) {
        var el = getElement(creator);
        var wormholes = new Map();
        var universe = { wormholes: wormholes, state: initialState };
        multiverse.set(creator, universe);
        var connectedCallback = creator.connectedCallback;
        creator.connectedCallback = function () {
            multiverse.set(creator, universe);
            if (connectedCallback) {
                connectedCallback.call(creator);
            }
        };
        var disconnectedCallback = creator.disconnectedCallback;
        creator.disconnectedCallback = function () {
            multiverse.delete(creator);
            if (disconnectedCallback) {
                disconnectedCallback.call(creator);
            }
        };
        el.addEventListener('openWormhole', function (event) {
            event.stopPropagation();
            var _a = event.detail, consumer = _a.consumer, onOpen = _a.onOpen;
            if (wormholes.has(consumer))
                return;
            if (typeof consumer !== 'symbol') {
                var connectedCallback_1 = consumer.connectedCallback, disconnectedCallback_1 = consumer.disconnectedCallback;
                consumer.connectedCallback = function () {
                    wormholes.set(consumer, event.detail);
                    if (connectedCallback_1) {
                        connectedCallback_1.call(consumer);
                    }
                };
                consumer.disconnectedCallback = function () {
                    wormholes.delete(consumer);
                    if (disconnectedCallback_1) {
                        disconnectedCallback_1.call(consumer);
                    }
                };
            }
            wormholes.set(consumer, event.detail);
            updateConsumer(event.detail, universe.state);
            onOpen === null || onOpen === void 0 ? void 0 : onOpen.resolve(function () { wormholes.delete(consumer); });
        });
        el.addEventListener('closeWormhole', function (event) {
            var consumer = event.detail;
            wormholes.delete(consumer);
        });
    },
    Provider: function (_a, children) {
        var state = _a.state;
        var creator = getRenderingRef();
        if (multiverse.has(creator)) {
            var universe = multiverse.get(creator);
            universe.state = state;
            universe.wormholes.forEach(function (opening) { updateConsumer(opening, state); });
        }
        return children;
    }
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

var ViewType;
(function (ViewType) {
  ViewType["Audio"] = "audio";
  ViewType["Video"] = "video";
})(ViewType || (ViewType = {}));

var MediaType;
(function (MediaType) {
  MediaType["Audio"] = "audio";
  MediaType["Video"] = "video";
})(MediaType || (MediaType = {}));

const IS_CLIENT = typeof window !== 'undefined';
const UA = (IS_CLIENT ? window.navigator.userAgent.toLowerCase() : '');
const IS_IOS = /iphone|ipad|ipod|ios|CriOS|FxiOS/.test(UA);
const IS_ANDROID = /android/.test(UA);
const IS_MOBILE = (IS_IOS || IS_ANDROID);
const IS_IPHONE = (IS_CLIENT && /(iPhone|iPod)/gi.test(window.navigator.platform));
const IS_FIREFOX = (/firefox/.test(UA));
const IS_SAFARI = (IS_CLIENT && (window.safari || IS_IOS || /Apple/.test(UA)));
const onTouchInputChange = (callback) => {
  if (!IS_CLIENT)
    return () => { };
  let lastTouchTime = 0;
  const offTouchListener = listen(document, 'touchstart', () => {
    lastTouchTime = new Date().getTime();
    callback(true);
  }, true);
  const offMouseListener = listen(document, 'mousemove', () => {
    // Filter emulated events coming from touch events
    if ((new Date().getTime()) - lastTouchTime < 500)
      return;
    callback(false);
  }, true);
  return () => {
    offTouchListener();
    offMouseListener();
  };
};
/**
 * Checks if a video player can enter fullscreen.
 *
 * @see https://developer.apple.com/documentation/webkitjs/htmlvideoelement/1633500-webkitenterfullscreen
 */
const canFullscreenVideo = () => {
  if (!IS_CLIENT)
    return false;
  const video = document.createElement('video');
  // @ts-ignore
  return isFunction(video.webkitEnterFullscreen);
};
/**
 * Checks if the screen orientation can be changed.
 *
 * @see https://developer.mozilla.org/en-US/docs/Web/API/Screen/orientation
 */
const canRotateScreen = () => IS_CLIENT
  && window.screen.orientation
  && window.screen.orientation.lock;
/**
 * Checks if the native HTML5 video player can play HLS.
 */
const canPlayHLSNatively = () => {
  if (!IS_CLIENT)
    return false;
  const video = document.createElement('video');
  return video.canPlayType('application/vnd.apple.mpegurl').length > 0;
};
/**
 * Checks if the native HTML5 video player can enter picture-in-picture (PIP) mode when using
 * the Chrome browser.
 *
 * @see  https://developers.google.com/web/updates/2018/10/watch-video-using-picture-in-picture
 */
const canUsePiPInChrome = () => {
  if (!IS_CLIENT)
    return false;
  const video = document.createElement('video');
  // @ts-ignore
  return !!document.pictureInPictureEnabled && !video.disablePictureInPicture;
};
/**
 * Checks if the native HTML5 video player can enter picture-in-picture (PIP) mode when using
 * the desktop Safari browser, iOS Safari appears to "support" PiP through the check, however PiP
 * does not function.
 *
 * @see https://developer.apple.com/documentation/webkitjs/adding_picture_in_picture_to_your_safari_media_controls
 */
const canUsePiPInSafari = () => {
  if (!IS_CLIENT)
    return false;
  const video = document.createElement('video');
  // @ts-ignore
  return isFunction(video.webkitSupportsPresentationMode)
    // @ts-ignore
    && isFunction(video.webkitSetPresentationMode)
    && !IS_IPHONE;
};
// Checks if the native HTML5 video player can enter PIP.
const canUsePiP = () => canUsePiPInChrome() || canUsePiPInSafari();
/**
 * To detect autoplay, we create a video element and call play on it, if it is `paused` after
 * a `play()` call, autoplay is supported. Although this unintuitive, it works across browsers
 * and is currently the lightest way to detect autoplay without using a data source.
 *
 * @see https://github.com/ampproject/amphtml/blob/9bc8756536956780e249d895f3e1001acdee0bc0/src/utils/video.js#L25
 */
const canAutoplay = (muted = true, playsinline = true) => {
  if (!IS_CLIENT)
    return Promise.resolve(false);
  const video = document.createElement('video');
  if (muted) {
    video.setAttribute('muted', '');
    video.muted = true;
  }
  if (playsinline) {
    video.setAttribute('playsinline', '');
    video.setAttribute('webkit-playsinline', '');
  }
  video.setAttribute('height', '0');
  video.setAttribute('width', '0');
  video.style.position = 'fixed';
  video.style.top = '0';
  video.style.width = '0';
  video.style.height = '0';
  video.style.opacity = '0';
  // Promise wrapped this way to catch both sync throws and async rejections.
  // More info: https://github.com/tc39/proposal-promise-try
  new Promise((resolve) => resolve(video.play())).catch(() => { });
  return Promise.resolve(!video.paused);
};

/**
 * Attempt to parse json into a POJO.
 */
function tryParseJSON(json) {
  if (!isString(json))
    return undefined;
  try {
    return JSON.parse(json);
  }
  catch (e) {
    return undefined;
  }
}
/**
 * Check if the given input is json or a plain object.
 */
const isObjOrJSON = (input) => !isNullOrUndefined(input)
  && (isObject(input) || (isString(input) && input.startsWith('{')));
/**
 * If an object return otherwise try to parse it as json.
 */
const objOrParseJSON = (input) => (isObject(input)
  ? input
  : tryParseJSON(input));
/**
 * Load image avoiding xhr/fetch CORS issues. Server status can't be obtained this way
 * unfortunately, so this uses "naturalWidth" to determine if the image has been loaded. By
 * default it checks if it is at least 1px.
 */
const loadImage = (src, minWidth = 1) => new Promise((resolve, reject) => {
  const image = new Image();
  const handler = () => {
    // @ts-ignore
    delete image.onload;
    // @ts-ignore
    delete image.onerror;
    image.naturalWidth >= minWidth ? resolve(image) : reject(image);
  };
  Object.assign(image, { onload: handler, onerror: handler, src });
});
const loadScript = (src, onLoad, onError = (() => { })) => {
  const script = document.createElement('script');
  script.src = src;
  script.onload = onLoad;
  script.onerror = onError;
  const firstScriptTag = document.getElementsByTagName('script')[0];
  firstScriptTag.parentNode.insertBefore(script, firstScriptTag);
};
/**
 * Tries to parse json and return a object.
 */
const decodeJSON = (data) => {
  if (!isObjOrJSON(data))
    return undefined;
  return objOrParseJSON(data);
};
/**
 * Attempts to safely decode a URI component, on failure it returns the given fallback.
 */
const tryDecodeURIComponent = (component, fallback = '') => {
  if (!IS_CLIENT)
    return fallback;
  try {
    return window.decodeURIComponent(component);
  }
  catch (e) {
    return fallback;
  }
};
/**
 * Returns a simple key/value map and duplicate keys are merged into an array.
 *
 * @see https://github.com/ampproject/amphtml/blob/c7c46cec71bac92f5c5da31dcc6366c18577f566/src/url-parse-query-string.js#L31
 */
const QUERY_STRING_REGEX = /(?:^[#?]?|&)([^=&]+)(?:=([^&]*))?/g;
const parseQueryString = (qs) => {
  const params = Object.create(null);
  if (isUndefined(qs))
    return params;
  let match;
  // eslint-disable-next-line no-cond-assign
  while ((match = QUERY_STRING_REGEX.exec(qs))) {
    const name = tryDecodeURIComponent(match[1], match[1]).replace('[]', '');
    const value = isString(match[2])
      ? tryDecodeURIComponent(match[2].replace(/\+/g, ' '), match[2])
      : '';
    const currValue = params[name];
    if (currValue && !isArray(currValue))
      params[name] = [currValue];
    currValue ? params[name].push(value) : (params[name] = value);
  }
  return params;
};
/**
 * Serializes the given params into a query string.
 */
const serializeQueryString = (params) => {
  const qs = [];
  const appendQueryParam = (param, v) => {
    qs.push(`${encodeURIComponent(param)}=${encodeURIComponent(v)}`);
  };
  Object.keys(params).forEach((param) => {
    const value = params[param];
    if (isNullOrUndefined(value))
      return;
    if (isArray(value)) {
      value.forEach((v) => appendQueryParam(param, v));
    }
    else {
      appendQueryParam(param, value);
    }
  });
  return qs.join('&');
};
/**
 * Notifies the browser to start establishing a connection with the given URL.
 */
const preconnect = (url, rel = 'preconnect', as) => {
  if (!IS_CLIENT)
    return false;
  const link = document.createElement('link');
  link.rel = rel;
  link.href = url;
  if (!isUndefined(as))
    link.as = as;
  link.crossOrigin = 'true';
  document.head.append(link);
  return true;
};
/**
 * Safely appends the given query string to the given URL.
 */
const appendQueryStringToURL = (url, qs) => {
  if (isUndefined(qs) || qs.length === 0)
    return url;
  const mainAndQuery = url.split('?', 2);
  return mainAndQuery[0]
    + (!isUndefined(mainAndQuery[1]) ? `?${mainAndQuery[1]}&${qs}` : `?${qs}`);
};
/**
 * Serializes the given params into a query string and appends them to the given URL.
 */
const appendParamsToURL = (url, params) => appendQueryStringToURL(url, isObject(params) ? serializeQueryString(params) : params);
/**
 * Tries to convert a query string into a object.
 */
const decodeQueryString = (qs) => {
  if (!isString(qs))
    return undefined;
  return parseQueryString(qs);
};
const pendingSDKRequests = {};
const loadSDK = (url, sdkGlobalVar, sdkReadyVar, isLoaded = () => true, loadScriptFn = loadScript) => {
  const getGlobal = (key) => {
    if (!isUndefined(window[key]))
      return window[key];
    if (window.exports && window.exports[key])
      return window.exports[key];
    if (window.module && window.module.exports && window.module.exports[key]) {
      return window.module.exports[key];
    }
    return undefined;
  };
  const existingGlobal = getGlobal(sdkGlobalVar);
  if (existingGlobal && isLoaded(existingGlobal)) {
    return Promise.resolve(existingGlobal);
  }
  return new Promise((resolve, reject) => {
    if (!isUndefined(pendingSDKRequests[url])) {
      pendingSDKRequests[url].push({ resolve, reject });
      return;
    }
    pendingSDKRequests[url] = [{ resolve, reject }];
    const onLoaded = (sdk) => {
      pendingSDKRequests[url].forEach((request) => request.resolve(sdk));
    };
    if (!isUndefined(sdkReadyVar)) {
      const previousOnReady = window[sdkReadyVar];
      // eslint-disable-next-line func-names
      window[sdkReadyVar] = function () {
        if (!isUndefined(previousOnReady))
          previousOnReady();
        onLoaded(getGlobal(sdkGlobalVar));
      };
    }
    loadScriptFn(url, () => {
      if (isUndefined(sdkReadyVar))
        onLoaded(getGlobal(sdkGlobalVar));
    }, (e) => {
      pendingSDKRequests[url].forEach((request) => { request.reject(e); });
      delete pendingSDKRequests[url];
    });
  });
};
const loadSprite = (src, into) => {
  if (!IS_CLIENT)
    return;
  window.fetch(src)
    .then((res) => res.text())
    .then((sprite) => {
    const div = document.createElement('div');
    div.setAttribute('data-sprite', src);
    div.style.display = 'none';
    div.innerHTML = sprite;
    (into !== null && into !== void 0 ? into : document.head).append(div);
  });
};

var Provider;
(function (Provider) {
  Provider["Audio"] = "audio";
  Provider["Video"] = "video";
  Provider["HLS"] = "hls";
  Provider["Dash"] = "dash";
  Provider["YouTube"] = "youtube";
  Provider["Vimeo"] = "vimeo";
  Provider["Dailymotion"] = "dailymotion";
  Provider["FakeTube"] = "faketube";
})(Provider || (Provider = {}));

const audioRegex = /\.(m4a|mp4a|mpga|mp2|mp2a|mp3|m2a|m3a|wav|weba|aac|oga|spx)($|\?)/i;
const videoRegex = /\.(mp4|og[gv]|webm|mov|m4v)($|\?)/i;
const hlsRegex = /\.(m3u8)($|\?)/i;
const hlsTypeRegex = /^application\/(x-mpegURL|vnd\.apple\.mpegURL)$/i;
const dashRegex = /\.(mpd)($|\?)/i;

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

const Audio = class extends HTMLElement {
  constructor() {
    super();
    this.__registerHost();
    /**
     * @internal Whether an external SDK will attach itself to the media player and control it.
     */
    this.willAttach = false;
    /**
     * @inheritdoc
     */
    this.preload = 'metadata';
    if (!this.willAttach)
      withProviderConnect(this);
  }
  /**
   * @internal
   */
  async getAdapter() {
    const adapter = await this.fileProvider.getAdapter();
    adapter.canPlay = async (type) => isString(type) && audioRegex.test(type);
    return adapter;
  }
  render() {
    return (
    // @ts-ignore
    h("vime-file", { noConnect: true, willAttach: this.willAttach, crossOrigin: this.crossOrigin, preload: this.preload, disableRemotePlayback: this.disableRemotePlayback, mediaTitle: this.mediaTitle, viewType: ViewType.Audio, ref: (el) => { this.fileProvider = el; } }, h("slot", null)));
  }
};

const captionControlCss = "vime-caption-control.hidden{display:none}";

const CaptionControl = class extends HTMLElement {
  constructor() {
    super();
    this.__registerHost();
    /**
     * The URL to an SVG element or fragment to load.
     */
    this.showIcon = '#vime-captions-on';
    /**
     * The URL to an SVG element or fragment to load.
     */
    this.hideIcon = '#vime-captions-off';
    /**
     * Whether the tooltip is positioned above/below the control.
     */
    this.tooltipPosition = 'top';
    /**
     * Whether the tooltip should not be displayed.
     */
    this.hideTooltip = false;
    /**
     * @inheritdoc
     */
    this.keys = 'c';
    /**
     * @internal
     */
    this.isCaptionsActive = false;
    /**
     * @internal
     */
    this.i18n = {};
    withPlayerContext(this, [
      'isCaptionsActive',
      'currentCaption',
      'i18n',
    ]);
  }
  onClick() {
    const player = findRootPlayer(this);
    player.toggleCaptionsVisibility();
  }
  render() {
    const tooltip = this.isCaptionsActive ? this.i18n.disableCaptions : this.i18n.enableCaptions;
    const tooltipWithHint = !isUndefined(this.keys) ? `${tooltip} (${this.keys})` : tooltip;
    return (h(Host, { class: {
        hidden: isUndefined(this.currentCaption),
      } }, h("vime-control", { label: this.i18n.captions, keys: this.keys, hidden: isUndefined(this.currentCaption), pressed: this.isCaptionsActive, onClick: this.onClick.bind(this) }, h("vime-icon", { href: this.isCaptionsActive ? this.showIcon : this.hideIcon }), h("vime-tooltip", { hidden: this.hideTooltip, position: this.tooltipPosition, direction: this.tooltipDirection }, tooltipWithHint))));
  }
  static get style() { return captionControlCss; }
};

class Disposal {
  constructor(dispose = []) {
    this.dispose = dispose;
  }
  add(callback) {
    this.dispose.push(callback);
  }
  empty() {
    this.dispose.forEach((fn) => fn());
    this.dispose = [];
  }
}

const captionsCss = "vime-captions{position:absolute;left:0;bottom:0;width:100%;text-align:center;color:var(--vm-captions-text-color);font-size:var(--vm-captions-font-size);padding:var(--vm-control-spacing);z-index:var(--vm-captions-z-index);display:none;pointer-events:none;transition:transform 0.4s ease-in-out}vime-captions.enabled{display:inline-block}vime-captions.hidden{display:none !important}@media (min-width: 768px){vime-captions{font-size:var(--vm-captions-font-size-medium)}}@media (min-width: 992px){vime-captions{font-size:var(--vm-captions-font-size-large)}}@media (min-width: 1200px){vime-captions{font-size:var(--vm-captions-font-size-xlarge)}}vime-captions span{display:inline-block;background:var(--vm-captions-cue-bg-color);border-radius:var(--vm-captions-cue-border-radius);box-decoration-break:clone;line-height:185%;padding:var(--vm-captions-cue-padding);white-space:pre-wrap;pointer-events:none}vime-captions span :global(div){display:inline}vime-captions span:empty{display:none}";

const Captions = class extends HTMLElement {
  constructor() {
    super();
    this.__registerHost();
    this.vTrackChange = createEvent(this, "vTrackChange", 3);
    this.vCuesChange = createEvent(this, "vCuesChange", 3);
    this.textTracksDisposal = new Disposal();
    this.textTrackDisposal = new Disposal();
    this.state = new Map();
    this.isEnabled = false;
    this.activeCues = [];
    /**
     * Whether the captions should be visible or not.
     */
    this.hidden = false;
    /**
     * The height of any lower control bar in pixels so that the captions can reposition when it's
     * active.
     */
    this.controlsHeight = 0;
    /**
     * @internal
     */
    this.isControlsActive = false;
    /**
     * @internal
     */
    this.isVideoView = false;
    /**
     * @internal
     */
    this.playbackStarted = false;
    withPlayerContext(this, [
      'isVideoView',
      'playbackStarted',
      'isControlsActive',
      'textTracks',
    ]);
  }
  onActiveTrackChange() {
    this.vTrackChange.emit(this.activeTrack);
  }
  onActiveCuesChange() {
    this.vCuesChange.emit(this.activeCues);
  }
  disconnectedCallback() {
    this.cleanup();
  }
  cleanup() {
    this.state.clear();
    this.textTracksDisposal.empty();
    this.textTrackDisposal.empty();
  }
  onCueChange() {
    var _a, _b;
    this.activeCues = Array.from((_b = (_a = this.activeTrack) === null || _a === void 0 ? void 0 : _a.activeCues) !== null && _b !== void 0 ? _b : []);
  }
  onTrackChange() {
    this.activeCues = [];
    this.textTrackDisposal.empty();
    if (isUndefined(this.activeTrack))
      return;
    this.textTrackDisposal.add(listen(this.activeTrack, 'cuechange', this.onCueChange.bind(this)));
  }
  findActiveTrack() {
    let activeTrack;
    Array.from(this.textTracks).forEach((track) => {
      if (isUndefined(activeTrack) && (track.mode === 'showing')) {
        // eslint-disable-next-line no-param-reassign
        track.mode = 'hidden';
        activeTrack = track;
        this.state.set(track, 'hidden');
      }
      else {
        // eslint-disable-next-line no-param-reassign
        track.mode = 'disabled';
        this.state.set(track, 'disabled');
      }
    });
    return activeTrack;
  }
  onTracksChange() {
    let hasChanged = false;
    Array.from(this.textTracks).forEach((track) => {
      if (!hasChanged) {
        hasChanged = !this.state.has(track) || (track.mode !== this.state.get(track));
      }
      this.state.set(track, track.mode);
    });
    if (hasChanged) {
      const activeTrack = this.findActiveTrack();
      if (this.activeTrack !== activeTrack) {
        this.activeTrack = activeTrack;
        this.onTrackChange();
      }
    }
  }
  onTextTracksListChange() {
    this.cleanup();
    if (isUndefined(this.textTracks))
      return;
    this.onTracksChange();
    this.textTracksDisposal.add(listen(this.textTracks, 'change', this.onTracksChange.bind(this)));
  }
  onEnabledChange() {
    this.isEnabled = this.playbackStarted && this.isVideoView;
  }
  renderCurrentCue() {
    const currentCue = this.activeCues[0];
    if (isUndefined(currentCue))
      return '';
    const div = document.createElement('div');
    div.append(currentCue.getCueAsHTML());
    return div.innerHTML.trim();
  }
  render() {
    return (h(Host, { style: {
        transform: `translateY(-${this.isControlsActive ? this.controlsHeight : 24}px)`,
      }, class: {
        enabled: this.isEnabled,
        hidden: this.hidden,
      } }, h("span", null, this.renderCurrentCue())));
  }
  static get watchers() { return {
    "activeTrack": ["onActiveTrackChange"],
    "activeCues": ["onActiveCuesChange"],
    "textTracks": ["onTextTracksListChange"],
    "isVideoView": ["onEnabledChange"],
    "playbackStarted": ["onEnabledChange"]
  }; }
  static get style() { return captionsCss; }
};

const clickToPlayCss = "vime-click-to-play{position:absolute;top:0;left:0;width:100%;height:100%;pointer-events:none;display:none;z-index:var(--vm-click-to-play-z-index)}vime-click-to-play.enabled{display:inline-block;pointer-events:auto}";

const ClickToPlay = class extends HTMLElement {
  constructor() {
    super();
    this.__registerHost();
    /**
     * By default this is disabled on mobile to not interfere with playback, set this to `true` to
     * enable it.
     */
    this.useOnMobile = false;
    /**
     * @internal
     */
    this.paused = true;
    /**
     * @internal
     */
    this.isVideoView = false;
    withPlayerContext(this, ['paused', 'isVideoView']);
  }
  connectedCallback() {
    this.dispatch = createDispatcher(this);
  }
  onClick() {
    this.dispatch('paused', !this.paused);
  }
  render() {
    return (h(Host, { class: {
        enabled: this.isVideoView && (!IS_MOBILE || this.useOnMobile),
      }, onClick: this.onClick.bind(this) }));
  }
  static get style() { return clickToPlayCss; }
};

const controlCss = "vime-control.hidden{display:none}vime-control button{display:flex;align-items:center;flex-direction:row;border:var(--vm-control-border);cursor:pointer;flex-shrink:0;color:var(--vm-control-color);background:var(--vm-control-bg, transparent);border-radius:var(--vm-control-border-radius);padding:var(--vm-control-padding);position:relative;pointer-events:auto;transition:all 0.3s ease;transform:scale(var(--vm-control-scale, 1))}vime-control button:focus{outline:0}vime-control button.tapHighlight{background:var(--vm-control-tap-highlight)}vime-control button.notTouch:focus,vime-control button.notTouch:hover,vime-control button.notTouch[aria-expanded=true]{background:var(--vm-control-focus-bg);color:var(--vm-control-focus-color);transform:scale(calc(var(--vm-control-scale, 1) + 0.1))}";

const Control = class extends HTMLElement {
  constructor() {
    super();
    this.__registerHost();
    this.vInteractionChange = createEvent(this, "vInteractionChange", 7);
    this.keyboardDisposal = new Disposal();
    this.showTapHighlight = false;
    /**
     * Whether the control should be displayed or not.
     */
    this.hidden = false;
    /**
     * @internal
     */
    this.isTouch = false;
    withPlayerContext(this, ['isTouch']);
  }
  onKeysChange() {
    this.keyboardDisposal.empty();
    if (isUndefined(this.keys))
      return;
    const player = findRootPlayer(this);
    const codes = this.keys.split('/');
    this.keyboardDisposal.add(listen(player, 'keydown', (event) => {
      if (codes.includes(event.key)) {
        this.button.click();
      }
    }));
  }
  connectedCallback() {
    this.findTooltip();
    this.onKeysChange();
  }
  componentWillLoad() {
    this.findTooltip();
  }
  disconnectedCallback() {
    this.keyboardDisposal.empty();
  }
  onTouchStart() {
    this.showTapHighlight = true;
    setTimeout(() => { this.showTapHighlight = false; }, 100);
  }
  findTooltip() {
    const tooltip = this.el.querySelector('vime-tooltip');
    if (!isNull(tooltip))
      this.describedBy = tooltip.id;
    return tooltip;
  }
  onShowTooltip() {
    const tooltip = this.findTooltip();
    if (!isNull(tooltip))
      tooltip.active = true;
    this.vInteractionChange.emit(true);
  }
  onHideTooltip() {
    const tooltip = this.findTooltip();
    if (!isNull(tooltip))
      tooltip.active = false;
    this.button.blur();
    this.vInteractionChange.emit(false);
  }
  onFocus() {
    this.el.dispatchEvent(new window.Event('focus', { bubbles: true }));
    this.onShowTooltip();
  }
  onBlur() {
    this.el.dispatchEvent(new window.Event('blur', { bubbles: true }));
    this.onHideTooltip();
  }
  onMouseEnter() {
    this.onShowTooltip();
  }
  onMouseLeave() {
    this.onHideTooltip();
  }
  render() {
    return (h(Host, { class: {
        hidden: this.hidden,
      } }, h("button", { class: {
        notTouch: !this.isTouch,
        tapHighlight: this.showTapHighlight,
      }, id: this.identifier, type: "button", "aria-label": this.label, "aria-haspopup": !isUndefined(this.menu) ? 'true' : undefined, "aria-controls": this.menu, "aria-expanded": !isUndefined(this.menu) ? (this.expanded ? 'true' : 'false') : undefined, "aria-pressed": !isUndefined(this.pressed) ? (this.pressed ? 'true' : 'false') : undefined, "aria-hidden": this.hidden ? 'true' : 'false', "aria-describedby": this.describedBy, onTouchStart: this.onTouchStart.bind(this), onFocus: this.onFocus.bind(this), onBlur: this.onBlur.bind(this), onMouseEnter: this.onMouseEnter.bind(this), onMouseLeave: this.onMouseLeave.bind(this), ref: (el) => { this.button = el; } }, h("slot", null))));
  }
  get el() { return this; }
  static get watchers() { return {
    "keys": ["onKeysChange"]
  }; }
  static get style() { return controlCss; }
};

const controlGroupCss = "vime-control-group{width:100%;display:flex;flex-wrap:wrap;flex-direction:inherit;align-items:inherit;justify-content:inherit}vime-control-group.spaceTop{margin-top:var(--vm-control-group-spacing)}vime-control-group.spaceBottom{margin-bottom:var(--vm-control-group-spacing)}vime-control-group>*{margin-left:var(--vm-controls-spacing)}vime-control-group>*:first-child{margin-left:0}";

const ControlNewLine = class extends HTMLElement {
  constructor() {
    super();
    this.__registerHost();
    /**
     * Determines where to add spacing/margin. The amount of spacing is determined by the CSS variable
     * `--control-group-spacing`.
     */
    this.space = 'none';
  }
  render() {
    return (h(Host, { class: {
        spaceTop: (this.space !== 'none' && this.space !== 'bottom'),
        spaceBottom: (this.space !== 'none' && this.space !== 'top'),
      } }, h("slot", null)));
  }
  get el() { return this; }
  static get style() { return controlGroupCss; }
};

const controlSpacerCss = "vime-control-spacer{flex:1}";

const ControlSpacer = class extends HTMLElement {
  constructor() {
    super();
    this.__registerHost();
  }
  render() {
    return (h(Host, null));
  }
  static get style() { return controlSpacerCss; }
};

const debounce = (func, wait = 1000, immediate = false) => {
  let timeout;
  return function executedFunction(...args) {
    const context = this;
    const later = function delayedFunctionCall() {
      timeout = undefined;
      if (!immediate)
        func.apply(context, args);
    };
    const callNow = immediate && isUndefined(timeout);
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
    if (callNow)
      func.apply(context, args);
  };
};

const findUIRoot = (ref) => {
  let ui = getElement(ref);
  while (!isNull(ui) && !(/^VIME-UI$/.test(ui.nodeName))) {
    ui = ui.parentElement;
  }
  return ui;
};

const controlsCss = "vime-controls{display:flex;position:absolute;flex-wrap:wrap;pointer-events:auto;z-index:var(--vm-controls-z-index);background:var(--vm-controls-bg);padding:var(--vm-controls-padding);opacity:0;visibility:hidden;transition:var(--vm-fade-transition)}vime-controls.audio{position:relative}vime-controls.hidden{display:none}vime-controls.active{opacity:1;visibility:visible}vime-controls.fullWidth{width:100%}vime-controls.fullHeight{height:100%}vime-controls>*:not(vime-control-group){margin-left:var(--vm-controls-spacing)}vime-controls>*:not(vime-control-group):first-child{margin-left:0}";

/**
 * We want to keep the controls active state in-sync per player.
 */
const playerRef = {};
const hideControlsTimeout = {};
const captionsCollisions = new Map();
const settingsCollisions = new Map();
const Controls = class extends HTMLElement {
  constructor() {
    super();
    this.__registerHost();
    this.disposal = new Disposal();
    this.isInteracting = false;
    /**
     * Whether the controls are visible or not.
     */
    this.hidden = false;
    /**
     * Whether the controls container should be 100% width. This has no effect if the view is of
     * type `audio`.
     */
    this.fullWidth = false;
    /**
     * Whether the controls container should be 100% height. This has no effect if the view is of
     * type `audio`.
     */
    this.fullHeight = false;
    /**
     * Sets the `flex-direction` property that manages the direction in which the controls are layed
     * out.
     */
    this.direction = 'row';
    /**
     * Sets the `align-items` flex property that aligns the individual controls on the cross-axis.
     */
    this.align = 'center';
    /**
     * Sets the `justify-content` flex property that aligns the individual controls on the main-axis.
     */
    this.justify = 'start';
    /**
     * Pins the controls to the defined position inside the video player. This has no effect when
     * the view is of type `audio`.
     */
    this.pin = 'bottomLeft';
    /**
     * The length in milliseconds that the controls are active for before fading out. Audio players
     * are not effected by this prop.
     */
    this.activeDuration = 2750;
    /**
     * Whether the controls should wait for playback to start before being shown. Audio players
     * are not effected by this prop.
     */
    this.waitForPlaybackStart = false;
    /**
     * Whether the controls should show/hide when paused. Audio players are not effected by this prop.
     */
    this.hideWhenPaused = false;
    /**
     * Whether the controls should hide when the mouse leaves the player. Audio players are not
     * effected by this prop.
     */
    this.hideOnMouseLeave = false;
    /**
     * @internal
     */
    this.isAudioView = false;
    /**
     * @internal
     */
    this.isSettingsActive = false;
    /**
     * @internal
     */
    this.playbackReady = false;
    /**
     * @internal
     */
    this.isControlsActive = false;
    /**
     * @internal
     */
    this.paused = true;
    /**
     * @internal
     */
    this.playbackStarted = false;
    withPlayerContext(this, [
      'playbackReady',
      'isAudioView',
      'isControlsActive',
      'isSettingsActive',
      'paused',
      'playbackStarted',
    ]);
  }
  connectedCallback() {
    this.dispatch = createDispatcher(this);
    this.onControlsChange();
    this.setupPlayerListeners();
    this.checkForCaptionsCollision();
    this.checkForSettingsCollision();
  }
  componentWillLoad() {
    this.onControlsChange();
  }
  componentDidRender() {
    this.checkForCaptionsCollision();
    this.checkForSettingsCollision();
  }
  disconnectedCallback() {
    this.disposal.empty();
    delete hideControlsTimeout[playerRef[this]];
    delete playerRef[this];
    captionsCollisions.delete(this);
    settingsCollisions.delete(this);
  }
  setupPlayerListeners() {
    const player = findRootPlayer(this);
    const events = ['focus', 'keydown', 'click', 'touchstart', 'mouseleave'];
    events.forEach((event) => {
      this.disposal.add(listen(player, event, this.onControlsChange.bind(this)));
    });
    this.disposal.add(listen(player, 'mousemove', debounce(this.onControlsChange, 50, true).bind(this)));
    // @ts-ignore
    playerRef[this] = player;
  }
  getHeight() {
    return parseFloat(window.getComputedStyle(this.el).height);
  }
  adjustHeightOnCollision(selector, marginTop = 0) {
    var _a;
    const el = (_a = findUIRoot(this)) === null || _a === void 0 ? void 0 : _a.querySelector(selector);
    if (isNullOrUndefined(el))
      return;
    const height = this.getHeight() + marginTop;
    const aboveControls = (selector === 'vime-settings')
      && (el.pin.startsWith('top'));
    const hasCollided = isColliding(el, this.el);
    const willCollide = isColliding(el, this.el, 0, aboveControls ? -height : height);
    const collisions = (selector === 'vime-captions') ? captionsCollisions : settingsCollisions;
    collisions.set(this, (hasCollided || willCollide) ? height : 0);
    el.controlsHeight = Math.max(0, Math.max(...collisions.values()));
  }
  checkForCaptionsCollision() {
    if (this.isAudioView)
      return;
    this.adjustHeightOnCollision('vime-captions');
  }
  checkForSettingsCollision() {
    this.adjustHeightOnCollision('vime-settings', (this.isAudioView ? 4 : 0));
  }
  show() {
    this.dispatch('isControlsActive', true);
  }
  hide() {
    this.dispatch('isControlsActive', false);
  }
  hideWithDelay() {
    // @ts-ignore
    clearTimeout(hideControlsTimeout[playerRef[this]]);
    hideControlsTimeout[playerRef[this]] = setTimeout(() => {
      this.hide();
    }, this.activeDuration);
  }
  onControlsChange(event) {
    // @ts-ignore
    clearTimeout(hideControlsTimeout[playerRef[this]]);
    if (this.hidden || !this.playbackReady) {
      this.hide();
      return;
    }
    if (this.isAudioView) {
      this.show();
      return;
    }
    if (this.waitForPlaybackStart && !this.playbackStarted) {
      this.hide();
      return;
    }
    if (this.isInteracting || this.isSettingsActive) {
      this.show();
      return;
    }
    if (this.hideWhenPaused && this.paused) {
      this.hideWithDelay();
      return;
    }
    if (this.hideOnMouseLeave && !this.paused && ((event === null || event === void 0 ? void 0 : event.type) === 'mouseleave')) {
      this.hide();
      return;
    }
    if (!this.paused) {
      this.show();
      this.hideWithDelay();
      return;
    }
    this.show();
  }
  getPosition() {
    if (this.isAudioView)
      return {};
    if (this.pin === 'center') {
      return {
        top: '50%',
        left: '50%',
        transform: 'translate(-50%, -50%)',
      };
    }
    // topLeft => { top: 0, left: 0 }
    const pos = this.pin.split(/(?=[L|R])/).map((s) => s.toLowerCase());
    return { [pos[0]]: 0, [pos[1]]: 0 };
  }
  onStartInteraction() {
    this.isInteracting = true;
  }
  onEndInteraction() {
    this.isInteracting = false;
  }
  render() {
    return (h(Host, { style: Object.assign(Object.assign({}, this.getPosition()), { flexDirection: this.direction, alignItems: (this.align === 'center') ? 'center' : `flex-${this.align}`, justifyContent: this.justify }), class: {
        audio: this.isAudioView,
        hidden: this.hidden,
        active: this.playbackReady && this.isControlsActive,
        fullWidth: this.isAudioView || this.fullWidth,
        fullHeight: !this.isAudioView && this.fullHeight,
      }, onMouseEnter: this.onStartInteraction.bind(this), onMouseLeave: this.onEndInteraction.bind(this), onTouchStart: this.onStartInteraction.bind(this), onTouchEnd: this.onEndInteraction.bind(this) }, h("slot", null)));
  }
  get el() { return this; }
  static get watchers() { return {
    "paused": ["onControlsChange"],
    "hidden": ["onControlsChange"],
    "isAudioView": ["onControlsChange"],
    "isInteracting": ["onControlsChange"],
    "isSettingsActive": ["onControlsChange"],
    "hideWhenPaused": ["onControlsChange"],
    "hideOnMouseLeave": ["onControlsChange"],
    "playbackStarted": ["onControlsChange"],
    "waitForPlaybackStart": ["onControlsChange"],
    "playbackReady": ["onControlsChange"]
  }; }
  static get style() { return controlsCss; }
};

const CurrentTime = class extends HTMLElement {
  constructor() {
    super();
    this.__registerHost();
    /**
     * @internal
     */
    this.currentTime = 0;
    /**
     * @internal
     */
    this.i18n = {};
    /**
     * Whether the time should always show the hours unit, even if the time is less than
     * 1 hour (eg: `20:35` -> `00:20:35`).
     */
    this.alwaysShowHours = false;
    withPlayerContext(this, ['currentTime', 'i18n']);
  }
  render() {
    return (h("vime-time", { label: this.i18n.currentTime, seconds: this.currentTime, alwaysShowHours: this.alwaysShowHours }));
  }
};

const deferredPromise = () => {
  let resolve;
  let reject;
  const promise = new Promise((res, rej) => {
    resolve = res;
    reject = rej;
  });
  // @ts-ignore
  return { promise, resolve, reject };
};

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

const videoInfoCache = new Map();
const Dailymotion = class extends HTMLElement {
  constructor() {
    super();
    this.__registerHost();
    this.vLoadStart = createEvent(this, "vLoadStart", 7);
    this.defaultInternalState = {};
    this.internalState = {
      currentTime: 0,
      volume: 0,
      muted: false,
      isAdsPlaying: false,
      playbackReady: false,
    };
    this.embedSrc = '';
    this.mediaTitle = '';
    /**
     * Whether to automatically play the next video in the queue.
     */
    this.shouldAutoplayQueue = false;
    /**
     * Whether to show the 'Up Next' queue.
     */
    this.showUpNextQueue = false;
    /**
     * Whether to show buttons for sharing the video.
     */
    this.showShareButtons = false;
    /**
     * Whether to display the Dailymotion logo.
     */
    this.showDailymotionLogo = false;
    /**
     * Whether to show video information (title and owner) on the start screen.
     */
    this.showVideoInfo = true;
    /**
     * @internal
     */
    this.language = 'en';
    /**
     * @internal
     */
    this.autoplay = false;
    /**
     * @internal
     */
    this.controls = false;
    /**
     * @internal
     */
    this.loop = false;
    /**
     * @internal
     */
    this.muted = false;
    /**
     * @internal
     */
    this.playsinline = false;
    withProviderConnect(this);
    withProviderContext(this);
  }
  onVideoIdChange() {
    this.embedSrc = `${this.getOrigin()}/embed/video/${this.videoId}?api=1`;
    this.internalState = Object.assign({}, this.defaultInternalState);
    this.fetchVideoInfo = this.getVideoInfo();
    this.pendingMediaTitleCall = deferredPromise();
  }
  onControlsChange() {
    if (this.internalState.playbackReady) {
      this.remoteControl("controls" /* Controls */, this.controls);
    }
  }
  onCustomPosterChange() {
    this.dispatch('currentPoster', this.poster);
  }
  connectedCallback() {
    this.dispatch = createProviderDispatcher(this);
    this.dispatch('viewType', ViewType.Video);
    this.onVideoIdChange();
    this.initialMuted = this.muted;
    this.internalState.muted = this.muted;
    this.defaultInternalState = Object.assign({}, this.internalState);
  }
  getOrigin() {
    return 'https://www.dailymotion.com';
  }
  getPreconnections() {
    return [
      this.getOrigin(),
      'https://static1.dmcdn.net',
    ];
  }
  remoteControl(command, arg) {
    return this.embed.postMessage({
      command,
      parameters: arg ? [arg] : [],
    });
  }
  buildParams() {
    return {
      autoplay: this.autoplay,
      mute: this.initialMuted,
      'queue-autoplay-next': this.shouldAutoplayQueue,
      'queue-enable': this.showUpNextQueue,
      'sharing-enable': this.showShareButtons,
      syndication: this.syndication,
      'ui-highlight': this.color,
      'ui-logo': this.showDailymotionLogo,
      'ui-start-screen-info': this.showVideoInfo,
    };
  }
  async getVideoInfo() {
    if (videoInfoCache.has(this.videoId))
      return videoInfoCache.get(this.videoId);
    const apiEndpoint = 'https://api.dailymotion.com';
    return window
      .fetch(`${apiEndpoint}/video/${this.videoId}?fields=duration,thumbnail_1080_url`)
      .then((response) => response.json())
      .then((data) => {
      const poster = data.thumbnail_1080_url;
      const duration = parseFloat(data.duration);
      videoInfoCache.set(this.videoId, { poster, duration });
      return { poster, duration };
    });
  }
  onEmbedSrcChange() {
    this.vLoadStart.emit();
  }
  onEmbedMessage(event) {
    var _a, _b;
    const msg = event.detail;
    switch (msg.event) {
      case "playback_ready" /* PlaybackReady */:
        this.onControlsChange();
        this.dispatch('currentSrc', this.embedSrc);
        this.dispatch('mediaType', MediaType.Video);
        Promise.all([
          this.fetchVideoInfo,
          (_a = this.pendingMediaTitleCall) === null || _a === void 0 ? void 0 : _a.promise,
        ]).then(([info, mediaTitle]) => {
          var _a, _b;
          this.dispatch('duration', (_a = info === null || info === void 0 ? void 0 : info.duration) !== null && _a !== void 0 ? _a : -1);
          this.dispatch('currentPoster', (_b = this.poster) !== null && _b !== void 0 ? _b : info === null || info === void 0 ? void 0 : info.poster);
          this.dispatch('mediaTitle', mediaTitle);
          this.dispatch('playbackReady', true);
        });
        break;
      case "videochange" /* VideoChange */:
        (_b = this.pendingMediaTitleCall) === null || _b === void 0 ? void 0 : _b.resolve(msg.title);
        break;
      case "start" /* Start */:
        this.dispatch('paused', false);
        this.dispatch('playbackStarted', true);
        this.dispatch('buffering', true);
        break;
      case "video_start" /* VideoStart */:
        // Commands don't go through until ads have finished, so we store them and then replay them
        // once the video starts.
        this.remoteControl("muted" /* Muted */, this.internalState.muted);
        this.remoteControl("volume" /* Volume */, this.internalState.volume);
        if (this.internalState.currentTime > 0) {
          this.remoteControl("seek" /* Seek */, this.internalState.currentTime);
        }
        break;
      case "play" /* Play */:
        this.dispatch('paused', false);
        break;
      case "pause" /* Pause */:
        this.dispatch('paused', true);
        this.dispatch('playing', false);
        this.dispatch('buffering', false);
        break;
      case "playing" /* Playing */:
        this.dispatch('playing', true);
        this.dispatch('buffering', false);
        break;
      case "video_end" /* VideoEnd */:
        if (this.loop) {
          setTimeout(() => { this.remoteControl("play" /* Play */); }, 300);
        }
        else {
          this.dispatch('playbackEnded', true);
        }
        break;
      case "timeupdate" /* TimeUpdate */:
        this.dispatch('currentTime', parseFloat(msg.time));
        break;
      case "volumechange" /* VolumeChange */:
        this.dispatch('muted', msg.muted === 'true');
        this.dispatch('volume', Math.floor(parseFloat(msg.volume) * 100));
        break;
      case "seeking" /* Seeking */:
        this.dispatch('currentTime', parseFloat(msg.time));
        this.dispatch('seeking', true);
        break;
      case "seeked" /* Seeked */:
        this.dispatch('currentTime', parseFloat(msg.time));
        this.dispatch('seeking', false);
        break;
      case "waiting" /* Waiting */:
        this.dispatch('buffering', true);
        break;
      case "progress" /* Progress */:
        this.dispatch('buffered', parseFloat(msg.time));
        break;
      case "durationchange" /* DurationChange */:
        this.dispatch('duration', parseFloat(msg.duration));
        break;
      case "qualitiesavailable" /* QualitiesAvailable */:
        this.dispatch('playbackQualities', msg.qualities.map((q) => `${q}p`));
        break;
      case "qualitychange" /* QualityChange */:
        this.dispatch('playbackQuality', `${msg.quality}p`);
        break;
      case "fullscreenchange" /* FullscreenChange */:
        this.dispatch('isFullscreenActive', msg.fullscreen === 'true');
        break;
      case "error" /* Error */:
        this.dispatch('errors', [new Error(msg.error)]);
        break;
    }
  }
  /**
   * @internal
   */
  async getAdapter() {
    const canPlayRegex = /(?:dai\.ly|dailymotion|dailymotion\.com)\/(?:video\/|embed\/|)(?:video\/|)((?:\w)+)/;
    return {
      getInternalPlayer: async () => this.embed,
      play: async () => { this.remoteControl("play" /* Play */); },
      pause: async () => { this.remoteControl("pause" /* Pause */); },
      canPlay: async (type) => isString(type) && canPlayRegex.test(type),
      setCurrentTime: async (time) => {
        this.internalState.currentTime = time;
        this.remoteControl("seek" /* Seek */, time);
      },
      setMuted: async (muted) => {
        this.internalState.muted = muted;
        this.remoteControl("muted" /* Muted */, muted);
      },
      setVolume: async (volume) => {
        this.internalState.volume = (volume / 100);
        this.dispatch('volume', volume);
        this.remoteControl("volume" /* Volume */, (volume / 100));
      },
      canSetPlaybackQuality: async () => true,
      setPlaybackQuality: async (quality) => {
        this.remoteControl("quality" /* Quality */, quality.slice(0, -1));
      },
      canSetFullscreen: async () => true,
      enterFullscreen: async () => { this.remoteControl("fullscreen" /* Fullscreen */, true); },
      exitFullscreen: async () => { this.remoteControl("fullscreen" /* Fullscreen */, false); },
    };
  }
  render() {
    return (h("vime-embed", { embedSrc: this.embedSrc, mediaTitle: this.mediaTitle, origin: this.getOrigin(), params: this.buildParams(), decoder: decodeQueryString, preconnections: this.getPreconnections(), onVEmbedMessage: this.onEmbedMessage.bind(this), onVEmbedSrcChange: this.onEmbedSrcChange.bind(this), ref: (el) => { this.embed = el; } }));
  }
  static get watchers() { return {
    "videoId": ["onVideoIdChange"],
    "controls": ["onControlsChange"],
    "poster": ["onCustomPosterChange"]
  }; }
};

const Dash = class extends HTMLElement {
  constructor() {
    super();
    this.__registerHost();
    this.vLoadStart = createEvent(this, "vLoadStart", 7);
    this.hasAttached = false;
    /**
     * The NPM package version of the `dashjs` library to download and use.
     */
    this.version = 'latest';
    /**
     * The `dashjs` configuration.
     */
    this.config = {};
    /**
     * @internal
     */
    this.autoplay = false;
    /**
     * @inheritdoc
     */
    this.preload = 'metadata';
    withProviderConnect(this);
    withPlayerContext(this, ['autoplay']);
  }
  onSrcChange() {
    if (!this.hasAttached)
      return;
    this.vLoadStart.emit();
    this.dash.attachSource(this.src);
  }
  connectedCallback() {
    this.dispatch = createProviderDispatcher(this);
    if (this.mediaEl)
      this.setupDash();
  }
  disconnectedCallback() {
    this.destroyDash();
  }
  async setupDash() {
    try {
      const url = `https://cdn.jsdelivr.net/npm/dashjs@${this.version}/dist/dash.all.min.js`;
      // eslint-disable-next-line no-shadow
      const DashSDK = await loadSDK(url, 'dashjs');
      this.dash = DashSDK.MediaPlayer(this.config).create();
      this.dash.initialize(this.mediaEl, null, this.autoplay);
      this.dash.on(DashSDK.MediaPlayer.events.CAN_PLAY, () => {
        this.dispatch('mediaType', MediaType.Video);
        this.dispatch('currentSrc', this.src);
        this.dispatch('playbackReady', true);
      });
      this.dash.on(DashSDK.MediaPlayer.events.ERROR, (e) => {
        this.dispatch('errors', [e]);
      });
      this.hasAttached = true;
    }
    catch (e) {
      this.dispatch('errors', [e]);
    }
  }
  async destroyDash() {
    var _a;
    (_a = this.dash) === null || _a === void 0 ? void 0 : _a.reset();
    this.hasAttached = false;
  }
  async onMediaElChange(event) {
    this.destroyDash();
    if (isUndefined(event.detail))
      return;
    this.mediaEl = event.detail;
    await this.setupDash();
  }
  /**
   * @internal
   */
  async getAdapter() {
    const adapter = await this.videoProvider.getAdapter();
    const canVideoProviderPlay = adapter.canPlay;
    return Object.assign(Object.assign({}, adapter), { getInternalPlayer: async () => this.dash, canPlay: async (type) => (isString(type) && dashRegex.test(type))
        || canVideoProviderPlay(type) });
  }
  render() {
    return (h("vime-video", { willAttach: true, crossOrigin: this.crossOrigin, preload: this.preload, poster: this.poster, controlsList: this.controlsList, autoPiP: this.autoPiP, disablePiP: this.disablePiP, disableRemotePlayback: this.disableRemotePlayback, mediaTitle: this.mediaTitle, ref: (el) => { this.videoProvider = el; } }));
  }
  static get watchers() { return {
    "src": ["onSrcChange"],
    "hasAttached": ["onSrcChange"]
  }; }
};

const dblClickFullscreenCss = "vime-dbl-click-fullscreen{position:absolute;top:0;left:0;width:100%;height:100%;pointer-events:none;display:none;z-index:var(--vm-dbl-click-fullscreen-z-index)}vime-dbl-click-fullscreen.enabled{display:inline-block;pointer-events:auto}";

const DblClickFullscreen = class extends HTMLElement {
  constructor() {
    super();
    this.__registerHost();
    this.canSetFullscreen = false;
    /**
     * By default this is disabled on mobile to not interfere with playback, set this to `true` to
     * enable it.
     */
    this.useOnMobile = false;
    /**
     * @internal
     */
    this.isFullscreenActive = true;
    /**
     * @internal
     */
    this.isVideoView = false;
    /**
     * @internal
     */
    this.playbackReady = false;
    this.clicks = 0;
    withPlayerContext(this, [
      'playbackReady',
      'isFullscreenActive',
      'isVideoView',
    ]);
  }
  async onPlaybackReadyChange() {
    const player = findRootPlayer(this);
    this.canSetFullscreen = await player.canSetFullscreen();
  }
  onTriggerClickToPlay() {
    const ui = findUIRoot(this);
    const clickToPlay = ui === null || ui === void 0 ? void 0 : ui.querySelector('vime-click-to-play');
    clickToPlay === null || clickToPlay === void 0 ? void 0 : clickToPlay.dispatchEvent(new Event('click'));
  }
  onToggleFullscreen() {
    const player = findRootPlayer(this);
    this.isFullscreenActive ? player.exitFullscreen() : player.enterFullscreen();
  }
  onClick() {
    this.clicks += 1;
    if (this.clicks === 1) {
      setTimeout(() => {
        if (this.clicks === 1) {
          this.onTriggerClickToPlay();
        }
        else {
          this.onToggleFullscreen();
        }
        this.clicks = 0;
      }, 300);
    }
  }
  render() {
    return (h(Host, { class: {
        enabled: this.playbackReady
          && this.canSetFullscreen
          && this.isVideoView
          && (!IS_MOBILE || this.useOnMobile),
      }, onClick: this.onClick.bind(this) }));
  }
  static get watchers() { return {
    "playbackReady": ["onPlaybackReadyChange"]
  }; }
  static get style() { return dblClickFullscreenCss; }
};

const defaultControlsCss = "vime-default-controls{width:100%}";

const DefaultControls = class extends HTMLElement {
  constructor() {
    super();
    this.__registerHost();
    /**
     * The length in milliseconds that the controls are active for before fading out. Audio players
     * are not effected by this prop.
     */
    this.activeDuration = 2750;
    /**
     * Whether the controls should wait for playback to start before being shown. Audio players
     * are not effected by this prop.
     */
    this.waitForPlaybackStart = false;
    /**
     * Whether the controls should show/hide when paused. Audio players are not effected by this prop.
     */
    this.hideWhenPaused = false;
    /**
     * Whether the controls should hide when the mouse leaves the player. Audio players are not
     * effected by this prop.
     */
    this.hideOnMouseLeave = false;
    /**
     * @internal
     */
    this.isMobile = false;
    /**
     * @internal
     */
    this.isLive = false;
    /**
     * @internal
     */
    this.isAudioView = false;
    /**
     * @internal
     */
    this.isVideoView = false;
    withPlayerContext(this, [
      'theme',
      'isMobile',
      'isAudioView',
      'isVideoView',
      'isLive',
    ]);
  }
  buildAudioControls() {
    return (h("vime-controls", { fullWidth: true }, h("vime-playback-control", { tooltipDirection: "right" }), h("vime-volume-control", null), !this.isLive && h("vime-current-time", null), this.isLive && h("vime-control-spacer", null), !this.isLive && h("vime-scrubber-control", null), this.isLive && h("vime-live-indicator", null), !this.isLive && h("vime-end-time", null), !this.isLive && h("vime-settings-control", { tooltipDirection: "left" }), h("div", { style: { marginLeft: '0', paddingRight: '2px' } })));
  }
  buildMobileVideoControls() {
    const lowerControls = (h("vime-controls", { pin: "bottomLeft", fullWidth: true, activeDuration: this.activeDuration, waitForPlaybackStart: this.waitForPlaybackStart, hideWhenPaused: this.hideWhenPaused }, h("vime-control-group", null, h("vime-current-time", null), h("vime-control-spacer", null), h("vime-end-time", null), h("vime-fullscreen-control", null)), h("vime-control-group", { space: "top" }, h("vime-scrubber-control", null))));
    return (h(Host, null, h("vime-scrim", null), h("vime-controls", { pin: "topLeft", fullWidth: true, activeDuration: this.activeDuration, waitForPlaybackStart: this.waitForPlaybackStart, hideWhenPaused: this.hideWhenPaused }, h("vime-control-spacer", null), h("vime-volume-control", null), !this.isLive && h("vime-caption-control", null), !this.isLive && h("vime-settings-control", null), this.isLive && h("vime-fullscreen-control", null)), h("vime-controls", { pin: "center", activeDuration: this.activeDuration, waitForPlaybackStart: this.waitForPlaybackStart, hideWhenPaused: this.hideWhenPaused }, h("vime-playback-control", { style: { '--vm-control-scale': '1.5' } })), !this.isLive && lowerControls));
  }
  buildDesktopVideoControls() {
    const scrubberControlGroup = (h("vime-control-group", null, h("vime-scrubber-control", null)));
    return (h(Host, null, (this.theme !== 'light') && h("vime-scrim", { gradient: "up" }), h("vime-controls", { activeDuration: this.activeDuration, waitForPlaybackStart: this.waitForPlaybackStart, hideWhenPaused: this.hideWhenPaused, hideOnMouseLeave: this.hideOnMouseLeave, fullWidth: true }, !this.isLive && scrubberControlGroup, h("vime-control-group", { space: this.isLive ? 'none' : 'top' }, h("vime-playback-control", { tooltipDirection: "right" }), h("vime-volume-control", null), !this.isLive && h("vime-time-progress", null), h("vime-control-spacer", null), !this.isLive && h("vime-caption-control", null), this.isLive && h("vime-live-indicator", null), h("vime-pip-control", null), !this.isLive && h("vime-settings-control", null), h("vime-fullscreen-control", { tooltipDirection: "left" })))));
  }
  render() {
    if (this.isAudioView)
      return this.buildAudioControls();
    if (this.isVideoView && this.isMobile)
      return this.buildMobileVideoControls();
    if (this.isVideoView)
      return this.buildDesktopVideoControls();
    return undefined;
  }
  static get style() { return defaultControlsCss; }
};

const DefaultSettings = class extends HTMLElement {
  constructor() {
    super();
    this.__registerHost();
    this.textTracksDisposal = new Disposal();
    /**
     * Pins the settings to the defined position inside the video player. This has no effect when
     * the view is of type `audio`, it will always be `bottomRight`.
     */
    this.pin = 'bottomRight';
    /**
     * @internal
     */
    this.i18n = {};
    /**
     * @internal
     */
    this.playbackReady = false;
    /**
     * @internal
     */
    this.playbackRate = 1;
    /**
     * @internal
     */
    this.playbackRates = [1];
    /**
     * @internal
     */
    this.playbackQualities = [];
    /**
     * @internal
     */
    this.isCaptionsActive = false;
    withPlayerContext(this, [
      'i18n',
      'playbackReady',
      'playbackRate',
      'playbackRates',
      'playbackQuality',
      'playbackQualities',
      'isCaptionsActive',
      'currentCaption',
      'textTracks',
    ]);
  }
  onTextTracksChange() {
    this.textTracksDisposal.empty();
    if (isUndefined(this.textTracks))
      return;
    this.textTracksDisposal.add(listen(this.textTracks, 'change', () => {
      setTimeout(() => forceUpdate(this), 300);
    }));
  }
  connectedCallback() {
    this.player = findRootPlayer(this);
    this.dispatch = createDispatcher(this);
  }
  componentWillRender() {
    if (!this.playbackReady)
      return undefined;
    return Promise.all([
      this.buildPlaybackRateSubmenu(),
      this.buildPlaybackQualitySubmenu(),
      this.buildCaptionsSubmenu(),
    ]);
  }
  disconnectedCallback() {
    this.player = undefined;
    this.textTracksDisposal.empty();
  }
  onPlaybackRateSelect(event) {
    const radio = event.target;
    this.dispatch('playbackRate', parseFloat(radio.value));
  }
  async buildPlaybackRateSubmenu() {
    var _a;
    const canSetPlaybackRate = await ((_a = this.player) === null || _a === void 0 ? void 0 : _a.canSetPlaybackRate());
    if (this.playbackRates.length === 1 || !canSetPlaybackRate) {
      this.rateSubmenu = (h("vime-menu-item", { label: this.i18n.playbackRate, hint: this.i18n.normal }));
      return;
    }
    const formatRate = (rate) => ((rate === 1) ? this.i18n.normal : `${rate}`);
    const radios = this.playbackRates.map((rate) => (h("vime-menu-radio", { label: formatRate(rate), value: `${rate}` })));
    this.rateSubmenu = (h("vime-submenu", { label: this.i18n.playbackRate, hint: formatRate(this.playbackRate) }, h("vime-menu-radio-group", { value: `${this.playbackRate}`, onVCheck: this.onPlaybackRateSelect.bind(this) }, radios)));
  }
  onPlaybackQualitySelect(event) {
    const radio = event.target;
    this.dispatch('playbackQuality', radio.value);
  }
  async buildPlaybackQualitySubmenu() {
    var _a, _b;
    const canSetPlaybackQuality = await ((_a = this.player) === null || _a === void 0 ? void 0 : _a.canSetPlaybackQuality());
    if (this.playbackQualities.length === 0 || !canSetPlaybackQuality) {
      this.qualitySubmenu = (h("vime-menu-item", { label: this.i18n.playbackQuality, hint: (_b = this.playbackQuality) !== null && _b !== void 0 ? _b : this.i18n.auto }));
      return;
    }
    // @TODO this doesn't account for audio qualities yet.
    const getBadge = (quality) => {
      const verticalPixels = parseInt(quality.slice(0, -1), 10);
      if (verticalPixels > 2160)
        return 'UHD';
      if (verticalPixels >= 1080)
        return 'HD';
      return undefined;
    };
    const radios = this.playbackQualities.map((quality) => (h("vime-menu-radio", { label: quality, value: quality, badge: getBadge(quality) })));
    this.qualitySubmenu = (h("vime-submenu", { label: this.i18n.playbackQuality, hint: this.playbackQuality }, h("vime-menu-radio-group", { value: this.playbackQuality, onVCheck: this.onPlaybackQualitySelect.bind(this) }, radios)));
  }
  async onCaptionSelect(event) {
    var _a;
    const radio = event.target;
    const index = parseInt(radio.value, 10);
    const player = findRootPlayer(this);
    if (index === -1) {
      await player.toggleCaptionsVisibility(false);
      return;
    }
    const track = Array.from((_a = this.textTracks) !== null && _a !== void 0 ? _a : [])[index];
    if (!isUndefined(track)) {
      if (!isUndefined(this.currentCaption))
        this.currentCaption.mode = 'disabled';
      track.mode = 'showing';
      await player.toggleCaptionsVisibility(true);
    }
  }
  async buildCaptionsSubmenu() {
    var _a, _b, _c;
    const captions = Array.from((_a = this.textTracks) !== null && _a !== void 0 ? _a : [])
      .filter((track) => ['captions', 'subtitles'].includes(track.kind));
    if (captions.length === 0) {
      this.captionsSubmenu = (h("vime-menu-item", { label: this.i18n.subtitlesOrCc, hint: this.i18n.none }));
      return;
    }
    const getTrackValue = (track) => `${Array.from(this.textTracks).findIndex((t) => t === track)}`;
    const radios = [(h("vime-menu-radio", { label: this.i18n.off, value: "-1" }))].concat(captions.map((track) => (h("vime-menu-radio", { label: track.label, value: getTrackValue(track) }))));
    const groupValue = (!this.isCaptionsActive || isUndefined(this.currentCaption))
      ? '-1'
      : getTrackValue(this.currentCaption);
    this.captionsSubmenu = (h("vime-submenu", { label: this.i18n.subtitlesOrCc, hint: (_c = (this.isCaptionsActive ? (_b = this.currentCaption) === null || _b === void 0 ? void 0 : _b.label : undefined)) !== null && _c !== void 0 ? _c : this.i18n.off }, h("vime-menu-radio-group", { value: groupValue, onVCheck: this.onCaptionSelect.bind(this) }, radios)));
  }
  render() {
    return (h("vime-settings", { pin: this.pin }, this.rateSubmenu, this.qualitySubmenu, this.captionsSubmenu, h("slot", null)));
  }
  static get watchers() { return {
    "textTracks": ["onTextTracksChange"]
  }; }
};

const DefaultUI = class extends HTMLElement {
  constructor() {
    super();
    this.__registerHost();
    /**
     * Whether the default icons should not be loaded.
     */
    this.noIcons = false;
    /**
     * Whether clicking the player should not toggle playback.
     */
    this.noClickToPlay = false;
    /**
     * Whether double clicking the player should not toggle fullscreen mode.
     */
    this.noDblClickFullscreen = false;
    /**
     * Whether the custom captions UI should not be loaded.
     */
    this.noCaptions = false;
    /**
     * Whether the custom poster UI should not be loaded.
     */
    this.noPoster = false;
    /**
     * Whether the custom spinner UI should not be loaded.
     */
    this.noSpinner = false;
    /**
     * Whether the custom default controls should not be loaded.
     */
    this.noControls = false;
    /**
     * Whether the custom default settings menu should not be loaded.
     */
    this.noSettings = false;
    /**
     * Whether the skeleton loading animation should be shown while the player is loading.
     */
    this.noSkeleton = false;
  }
  render() {
    return (h("vime-ui", null, !this.noIcons && h("vime-icons", null), !this.noSkeleton && h("vime-skeleton", null), !this.noClickToPlay && h("vime-click-to-play", null), !this.noDblClickFullscreen && h("vime-dbl-click-fullscreen", null), !this.noCaptions && h("vime-captions", null), !this.noPoster && h("vime-poster", null), !this.noSpinner && h("vime-spinner", null), !this.noControls && h("vime-default-controls", null), !this.noSettings && h("vime-default-settings", null), h("slot", null)));
  }
};

/* eslint-disable no-param-reassign */
class LazyLoader {
  constructor(el, attributes, onLoad) {
    var _a;
    this.el = el;
    this.attributes = attributes;
    this.onLoad = onLoad;
    this.hasLoaded = false;
    if (isNullOrUndefined(this.el))
      return;
    this.intersectionObs = this.canObserveIntersection()
      ? (new IntersectionObserver(this.onIntersection.bind(this)))
      : undefined;
    this.mutationObs = this.canObserveMutations()
      ? (new MutationObserver(this.onMutation.bind(this)))
      : undefined;
    (_a = this.mutationObs) === null || _a === void 0 ? void 0 : _a.observe(this.el, {
      childList: true,
      subtree: true,
      attributeFilter: this.attributes,
    });
    this.lazyLoad();
  }
  didLoad() {
    return this.hasLoaded;
  }
  destroy() {
    var _a, _b;
    (_a = this.intersectionObs) === null || _a === void 0 ? void 0 : _a.disconnect();
    (_b = this.mutationObs) === null || _b === void 0 ? void 0 : _b.disconnect();
  }
  canObserveIntersection() {
    return IS_CLIENT && window.IntersectionObserver;
  }
  canObserveMutations() {
    return IS_CLIENT && window.MutationObserver;
  }
  lazyLoad() {
    var _a;
    if (this.canObserveIntersection()) {
      (_a = this.intersectionObs) === null || _a === void 0 ? void 0 : _a.observe(this.el);
    }
    else {
      this.load();
    }
  }
  onIntersection(entries) {
    entries.forEach((entry) => {
      if (entry.intersectionRatio > 0 || entry.isIntersecting) {
        this.load();
        this.intersectionObs.unobserve(entry.target);
      }
    });
  }
  onMutation() {
    if (this.hasLoaded)
      this.load();
  }
  getLazyElements() {
    return this.el.querySelectorAll('.lazy');
  }
  load() {
    window.requestAnimationFrame(() => {
      this.getLazyElements().forEach(this.loadEl.bind(this));
    });
  }
  loadEl(el) {
    var _a, _b;
    (_a = this.intersectionObs) === null || _a === void 0 ? void 0 : _a.unobserve(el);
    this.hasLoaded = true;
    (_b = this.onLoad) === null || _b === void 0 ? void 0 : _b.call(this, el);
  }
}

const embedCss = "vime-embed>iframe{position:absolute;top:0;left:0;border:0;width:100%;height:100%;user-select:none;z-index:var(--vm-media-z-index)}";

let idCount = 0;
const connected = new Set();
const Embed = class extends HTMLElement {
  constructor() {
    super();
    this.__registerHost();
    this.vEmbedSrcChange = createEvent(this, "vEmbedSrcChange", 3);
    this.vEmbedMessage = createEvent(this, "vEmbedMessage", 3);
    this.vEmbedLoaded = createEvent(this, "vEmbedLoaded", 3);
    this.srcWithParams = '';
    this.hasEnteredViewport = false;
    /**
     * A URL that will load the external player and media (Eg: https://www.youtube.com/embed/DyTCOwB0DVw).
     */
    this.embedSrc = '';
    /**
     * The title of the current media so it can be set on the inner `iframe` for screen readers.
     */
    this.mediaTitle = '';
    /**
     * The parameters to pass to the embedded player which are appended to the `embedSrc` prop. These
     * can be passed in as a query string or object.
     */
    this.params = '';
    /**
     * A collection of URLs to that the browser should immediately start establishing a connection
     * with.
     */
    this.preconnections = [];
  }
  srcChange() {
    this.srcWithParams = appendParamsToURL(this.embedSrc, this.params);
  }
  srcWithParamsChange() {
    if (!this.hasEnteredViewport && !connected.has(this.embedSrc)) {
      if (preconnect(this.srcWithParams))
        connected.add(this.embedSrc);
    }
    this.vEmbedSrcChange.emit(this.srcWithParams);
  }
  preconnectionsChange() {
    if (this.hasEnteredViewport) {
      return;
    }
    this.preconnections
      .filter((connection) => !connected.has(connection))
      .forEach((connection) => {
      if (preconnect(connection))
        connected.add(connection);
    });
  }
  connectedCallback() {
    this.lazyLoader = new LazyLoader(this.el, ['data-src'], (el) => {
      const src = el.getAttribute('data-src');
      el.removeAttribute('src');
      if (!isNull(src))
        el.setAttribute('src', src);
    });
    this.srcChange();
    this.genIframeId();
  }
  disconnectedCallback() {
    this.lazyLoader.destroy();
  }
  onWindowMessage(e) {
    var _a, _b, _c;
    const originMatches = (e.source === ((_a = this.iframe) === null || _a === void 0 ? void 0 : _a.contentWindow))
      && (!isString(this.origin) || this.origin === e.origin);
    if (!originMatches)
      return;
    const message = (_c = (_b = this.decoder) === null || _b === void 0 ? void 0 : _b.call(this, e.data)) !== null && _c !== void 0 ? _c : e.data;
    if (message)
      this.vEmbedMessage.emit(message);
  }
  /**
   * Posts a message to the embedded media player.
   */
  async postMessage(message, target) {
    var _a, _b;
    (_b = (_a = this.iframe) === null || _a === void 0 ? void 0 : _a.contentWindow) === null || _b === void 0 ? void 0 : _b.postMessage(JSON.stringify(message), (target !== null && target !== void 0 ? target : '*'));
  }
  onLoad() {
    this.vEmbedLoaded.emit();
  }
  genIframeId() {
    idCount += 1;
    this.id = `vime-iframe-${idCount}`;
  }
  render() {
    return (h("iframe", { id: this.id, class: "lazy", title: this.mediaTitle, "data-src": this.srcWithParams,
      // @ts-ignore
      allowfullscreen: "1", allow: "autoplay; encrypted-media; picture-in-picture", onLoad: this.onLoad.bind(this), ref: (el) => { this.iframe = el; } }));
  }
  get el() { return this; }
  static get watchers() { return {
    "embedSrc": ["srcChange"],
    "params": ["srcChange"],
    "srcWithParams": ["srcWithParamsChange"],
    "preconnections": ["preconnectionsChange"]
  }; }
  static get style() { return embedCss; }
};

const EndTime = class extends HTMLElement {
  constructor() {
    super();
    this.__registerHost();
    /**
     * @internal
     */
    this.duration = -1;
    /**
     * @internal
     */
    this.i18n = {};
    /**
     * Whether the time should always show the hours unit, even if the time is less than
     * 1 hour (eg: `20:35` -> `00:20:35`).
     */
    this.alwaysShowHours = false;
    withPlayerContext(this, ['duration', 'i18n']);
  }
  render() {
    return (h("vime-time", { label: this.i18n.duration, seconds: Math.max(0, this.duration), alwaysShowHours: this.alwaysShowHours }));
  }
};

const faketubeCss = "vime-faketube{display:block;width:100%;height:auto}";

const FakeTube = class extends HTMLElement {
  constructor() {
    super();
    this.__registerHost();
    this.vLoadStart = createEvent(this, "vLoadStart", 7);
    /**
     * @internal
     */
    this.language = 'en';
    /**
     * @internal
     */
    this.autoplay = false;
    /**
     * @internal
     */
    this.controls = false;
    /**
     * @internal
     */
    this.loop = false;
    /**
     * @internal
     */
    this.muted = false;
    /**
     * @internal
     */
    this.playsinline = false;
    withProviderContext(this);
  }
  connectedCallback() {
    this.dispatch = createProviderDispatcher(this);
  }
  componentWillLoad() {
    const player = findRootPlayer(this);
    player.setProvider(this);
  }
  /**
   * Returns a mock adapter.
   */
  async getAdapter() {
    return {
      getInternalPlayer: jest.fn(),
      play: jest.fn(),
      pause: jest.fn(),
      canPlay: jest.fn(),
      setCurrentTime: jest.fn(),
      setMuted: jest.fn(),
      setVolume: jest.fn(),
      canSetPlaybackRate: jest.fn(),
      setPlaybackRate: jest.fn(),
      canSetPlaybackQuality: jest.fn(),
      setPlaybackQuality: jest.fn(),
      canSetFullscreen: jest.fn(),
      enterFullscreen: jest.fn(),
      exitFullscreen: jest.fn(),
      canSetPiP: jest.fn(),
      enterPiP: jest.fn(),
      exitPiP: jest.fn(),
    };
  }
  /**
   * Dispatches the `vLoadStart` event.
   */
  async dispatchLoadStart() {
    this.vLoadStart.emit();
  }
  /**
   * Dispatches a state change event.
   */
  async dispatchChange(prop, value) {
    this.dispatch(prop, value);
  }
  static get style() { return faketubeCss; }
};

const fileCss = "vime-file audio,vime-file video{border-radius:inherit;vertical-align:middle;width:100%;outline:0}vime-file video{position:absolute;top:0;left:0;border:0;height:100%;user-select:none}";

const File = class extends HTMLElement {
  constructor() {
    super();
    this.__registerHost();
    this.vLoadStart = createEvent(this, "vLoadStart", 7);
    this.vMediaElChange = createEvent(this, "vMediaElChange", 7);
    this.vSrcSetChange = createEvent(this, "vSrcSetChange", 7);
    this.disposal = new Disposal();
    this.wasPausedBeforeSeeking = true;
    this.currentSrcSet = [];
    this.mediaQueryDisposal = new Disposal();
    /**
     * @internal Whether an external SDK will attach itself to the media player and control it.
     */
    this.willAttach = false;
    /**
     * @inheritdoc
     */
    this.preload = 'metadata';
    /**
     * The playback rates that are available for this media.
     */
    this.playbackRates = [0.25, 0.5, 0.75, 1, 1.25, 1.5, 2];
    /**
     * @internal
     */
    this.language = 'en';
    /**
     * @internal
     */
    this.autoplay = false;
    /**
     * @internal
     */
    this.controls = false;
    /**
     * @internal
     */
    this.loop = false;
    /**
     * @internal
     */
    this.muted = false;
    /**
     * @internal
     */
    this.playsinline = false;
    /**
     * @internal
     */
    this.noConnect = false;
    /**
     * @internal
     */
    this.paused = true;
    /**
     * @internal
     */
    this.currentTime = 0;
    /**
     * @internal
     */
    this.playbackStarted = false;
    if (!this.noConnect)
      withProviderConnect(this);
    withProviderContext(this, ['playbackStarted', 'currentTime', 'paused']);
  }
  onMediaTitleChange() {
    this.dispatch('mediaTitle', this.mediaTitle);
  }
  onPosterChange() {
    this.dispatch('currentPoster', this.poster);
  }
  onViewTypeChange() {
    this.dispatch('viewType', this.viewType);
  }
  connectedCallback() {
    this.initLazyLoader();
    this.dispatch = createProviderDispatcher(this);
    this.onViewTypeChange();
    this.onPosterChange();
    this.onMediaTitleChange();
    this.listenToTextTracksChanges();
  }
  componentDidRender() {
    if (this.prevMediaEl !== this.mediaEl) {
      this.prevMediaEl = this.mediaEl;
      this.vMediaElChange.emit(this.mediaEl);
    }
  }
  componentDidLoad() {
    this.onViewTypeChange();
  }
  disconnectedCallback() {
    var _a;
    this.mediaQueryDisposal.empty();
    this.cancelTimeUpdates();
    this.disposal.empty();
    (_a = this.lazyLoader) === null || _a === void 0 ? void 0 : _a.destroy();
    this.wasPausedBeforeSeeking = true;
  }
  initLazyLoader() {
    this.lazyLoader = new LazyLoader(this.el, ['data-src', 'data-poster'], () => {
      if (isNullOrUndefined(this.mediaEl))
        return;
      const poster = this.mediaEl.getAttribute('data-poster');
      if (!isNull(poster))
        this.mediaEl.setAttribute('poster', poster);
      this.refresh();
      this.didSrcSetChange();
    });
  }
  refresh() {
    if (isNullOrUndefined(this.mediaEl))
      return;
    const { children } = this.mediaEl;
    for (let i = 0; i <= children.length - 1; i += 1) {
      const child = children[i];
      const src = child.getAttribute('data-src') || child.getAttribute('src') || child.getAttribute('data-vs');
      child.removeAttribute('src');
      if (isNull(src))
        continue;
      child.setAttribute('data-vs', src);
      if (!isNull(child.getAttribute('data-quality'))) {
        const quality = child.getAttribute('data-quality');
        if (quality !== this.playbackQuality) {
          child.removeAttribute('src');
          continue;
        }
      }
      child.setAttribute('src', src);
    }
  }
  didSrcSetChange() {
    if (isNullOrUndefined(this.mediaEl))
      return;
    const sources = Array.from(this.mediaEl.querySelectorAll('source'));
    const srcSet = sources.map((source) => {
      var _a, _b;
      return ({
        src: source.getAttribute('data-vs'),
        quality: (_a = source.getAttribute('data-quality')) !== null && _a !== void 0 ? _a : undefined,
        media: (_b = source.getAttribute('data-media')) !== null && _b !== void 0 ? _b : undefined,
        ref: source,
      });
    });
    const didChange = (this.currentSrcSet.length !== srcSet.length)
      || (srcSet.some((resource, i) => ((this.currentSrcSet[i].src !== resource.src)
        || (this.currentSrcSet[i].quality !== resource.quality))));
    if (didChange) {
      this.currentSrcSet = srcSet;
      this.onSrcSetChange();
    }
  }
  onSrcSetChange() {
    var _a;
    this.mediaQueryDisposal.empty();
    this.vLoadStart.emit();
    this.vSrcSetChange.emit(this.currentSrcSet);
    if (this.hasPlaybackQualities()) {
      this.dispatch('playbackQualities', this.getPlaybackQualities());
      this.pickInitialPlaybackQuality();
      this.refresh();
    }
    (_a = this.mediaEl) === null || _a === void 0 ? void 0 : _a.load();
  }
  hasPlaybackQualities() {
    return this.currentSrcSet.every((resource) => !!resource.quality);
  }
  getPlaybackQualities() {
    if (!this.hasPlaybackQualities())
      return [];
    return this.currentSrcSet.map((resource) => resource.quality);
  }
  pickInitialPlaybackQuality() {
    if (!isUndefined(this.playbackQuality))
      return;
    const getQualityValue = (resource) => { var _a, _b; return Number((_b = (_a = resource.quality) === null || _a === void 0 ? void 0 : _a.slice(0, -1)) !== null && _b !== void 0 ? _b : 0); };
    const sortMediaResource = (a, b) => getQualityValue(a) - getQualityValue(b);
    // Try to find best quality based on media queries.
    let mediaResource = this.currentSrcSet
      .filter((resource) => {
      if (!isString(resource.media))
        return false;
      const query = window.matchMedia(resource.media);
      const dispatch = createDispatcher(this);
      this.mediaQueryDisposal.add(listen(query, 'change', (e) => {
        if (e.matches)
          dispatch('playbackQuality', resource.quality);
      }));
      return query.matches;
    })
      .sort(sortMediaResource)
      .pop();
    // Otherwise pick best quality based on window width.
    if (isUndefined(mediaResource)) {
      mediaResource = this.currentSrcSet
        .find((resource) => getQualityValue(resource) > window.innerWidth);
    }
    // Otehrwise pick best quality.
    if (isUndefined(mediaResource)) {
      mediaResource = this.currentSrcSet.sort(sortMediaResource).pop();
    }
    this.playbackQuality = mediaResource === null || mediaResource === void 0 ? void 0 : mediaResource.quality;
    this.dispatch('playbackQuality', mediaResource === null || mediaResource === void 0 ? void 0 : mediaResource.quality);
  }
  hasCustomPoster() {
    const root = findRootPlayer(this);
    return !IS_IOS && !isNull(root.querySelector('vime-ui vime-poster'));
  }
  cancelTimeUpdates() {
    if (isNumber(this.timeRAF))
      window.cancelAnimationFrame(this.timeRAF);
    this.timeRAF = undefined;
  }
  requestTimeUpdates() {
    var _a, _b;
    this.dispatch('currentTime', (_b = (_a = this.mediaEl) === null || _a === void 0 ? void 0 : _a.currentTime) !== null && _b !== void 0 ? _b : 0);
    this.timeRAF = window.requestAnimationFrame(() => { this.requestTimeUpdates(); });
  }
  getMediaType() {
    const { currentSrc } = this.mediaEl;
    if (audioRegex.test(currentSrc))
      return MediaType.Audio;
    if (videoRegex.test(currentSrc) || hlsRegex.test(currentSrc))
      return MediaType.Video;
    return undefined;
  }
  onLoadedMetadata() {
    this.onTracksChange();
    // Reset player state on quality change.
    if (this.playbackStarted) {
      this.mediaEl.muted = this.muted;
      if (this.currentTime > 0)
        this.mediaEl.currentTime = this.currentTime;
      if (!this.paused)
        this.mediaEl.play();
    }
    else {
      this.onProgress();
      this.dispatch('currentPoster', this.poster);
      this.dispatch('duration', this.mediaEl.duration);
      this.dispatch('playbackRates', this.playbackRates);
    }
    if (!this.willAttach) {
      this.dispatch('currentSrc', this.mediaEl.currentSrc);
      this.dispatch('mediaType', this.getMediaType());
      this.dispatch('playbackReady', true);
      // Re-attempt play.
      if (this.autoplay)
        this.mediaEl.play();
    }
  }
  onProgress() {
    const { buffered, duration } = this.mediaEl;
    const end = (buffered.length === 0) ? 0 : buffered.end(buffered.length - 1);
    this.dispatch('buffered', (end > duration) ? duration : end);
  }
  onPlay() {
    this.requestTimeUpdates();
    this.dispatch('paused', false);
    if (!this.playbackStarted)
      this.dispatch('playbackStarted', true);
  }
  onPause() {
    this.cancelTimeUpdates();
    this.dispatch('paused', true);
    this.dispatch('buffering', false);
  }
  onPlaying() {
    this.dispatch('playing', true);
    this.dispatch('buffering', false);
  }
  onSeeking() {
    if (!this.wasPausedBeforeSeeking)
      this.wasPausedBeforeSeeking = this.mediaEl.paused;
    this.dispatch('currentTime', this.mediaEl.currentTime);
    this.dispatch('seeking', true);
  }
  onSeeked() {
    this.dispatch('seeking', false);
    if (!this.playbackStarted || !this.wasPausedBeforeSeeking)
      this.attemptToPlay();
    this.wasPausedBeforeSeeking = true;
  }
  onRateChange() {
    this.dispatch('playbackRate', this.mediaEl.playbackRate);
  }
  onVolumeChange() {
    this.dispatch('muted', this.mediaEl.muted);
    this.dispatch('volume', this.mediaEl.volume * 100);
  }
  onDurationChange() {
    this.dispatch('duration', this.mediaEl.duration);
  }
  onWaiting() {
    this.dispatch('buffering', true);
  }
  onSuspend() {
    this.dispatch('buffering', false);
  }
  onEnded() {
    if (!this.loop)
      this.dispatch('playbackEnded', true);
  }
  onError() {
    this.dispatch('errors', [this.mediaEl.error]);
  }
  attemptToPlay() {
    var _a;
    try {
      (_a = this.mediaEl) === null || _a === void 0 ? void 0 : _a.play();
    }
    catch (e) {
      this.dispatch('errors', [e]);
    }
  }
  togglePiPInChrome(toggle) {
    var _a;
    return toggle
      ? (_a = this.mediaEl) === null || _a === void 0 ? void 0 : _a.requestPictureInPicture() : document.exitPictureInPicture();
  }
  togglePiPInSafari(toggle) {
    var _a, _b;
    const mode = toggle ? "picture-in-picture" /* PiP */ : "inline" /* Inline */;
    if (!((_a = this.mediaEl) === null || _a === void 0 ? void 0 : _a.webkitSupportsPresentationMode(mode))) {
      throw new Error('PiP API is not available.');
    }
    return (_b = this.mediaEl) === null || _b === void 0 ? void 0 : _b.webkitSetPresentationMode(mode);
  }
  async togglePiP(toggle) {
    if (canUsePiPInChrome())
      return this.togglePiPInChrome(toggle);
    if (canUsePiPInSafari())
      return this.togglePiPInSafari(toggle);
    throw new Error('PiP API is not available.');
  }
  async toggleFullscreen(toggle) {
    var _a, _b, _c;
    if (!((_a = this.mediaEl) === null || _a === void 0 ? void 0 : _a.webkitSupportsFullscreen)) {
      throw new Error('Fullscreen API is not available.');
    }
    return toggle
      ? (_b = this.mediaEl) === null || _b === void 0 ? void 0 : _b.webkitEnterFullscreen() : (_c = this.mediaEl) === null || _c === void 0 ? void 0 : _c.webkitExitFullscreen();
  }
  onPresentationModeChange() {
    var _a;
    const mode = (_a = this.mediaEl) === null || _a === void 0 ? void 0 : _a.webkitPresentationMode;
    this.dispatch('isPiPActive', (mode === "picture-in-picture" /* PiP */));
    this.dispatch('isFullscreenActive', (mode === "fullscreen" /* Fullscreen */));
  }
  onEnterPiP() {
    this.dispatch('isPiPActive', true);
  }
  onLeavePiP() {
    this.dispatch('isPiPActive', false);
  }
  onTracksChange() {
    this.dispatch('textTracks', this.mediaEl.textTracks);
  }
  listenToTextTracksChanges() {
    if (isUndefined(this.mediaEl))
      return;
    this.disposal.add(listen(this.mediaEl.textTracks, 'change', this.onTracksChange.bind(this)));
  }
  /**
   * @internal
   */
  async getAdapter() {
    return {
      getInternalPlayer: async () => this.mediaEl,
      play: async () => { var _a; return (_a = this.mediaEl) === null || _a === void 0 ? void 0 : _a.play(); },
      pause: async () => { var _a; return (_a = this.mediaEl) === null || _a === void 0 ? void 0 : _a.pause(); },
      canPlay: async (type) => isString(type)
        && (audioRegex.test(type) || videoRegex.test(type)),
      setCurrentTime: async (time) => {
        if (this.mediaEl)
          this.mediaEl.currentTime = time;
      },
      setMuted: async (muted) => {
        if (this.mediaEl)
          this.mediaEl.muted = muted;
      },
      setVolume: async (volume) => {
        if (this.mediaEl)
          this.mediaEl.volume = (volume / 100);
      },
      canSetPlaybackRate: async () => true,
      setPlaybackRate: async (rate) => {
        if (this.mediaEl)
          this.mediaEl.playbackRate = rate;
      },
      canSetPlaybackQuality: async () => this.hasPlaybackQualities(),
      setPlaybackQuality: async (quality) => {
        var _a;
        this.cancelTimeUpdates();
        this.playbackQuality = quality;
        this.refresh();
        (_a = this.mediaEl) === null || _a === void 0 ? void 0 : _a.load();
        this.dispatch('playbackQuality', this.playbackQuality);
      },
      canSetPiP: async () => canUsePiP(),
      enterPiP: () => this.togglePiP(true),
      exitPiP: () => this.togglePiP(false),
      canSetFullscreen: async () => canFullscreenVideo(),
      enterFullscreen: () => this.toggleFullscreen(true),
      exitFullscreen: () => this.toggleFullscreen(false),
    };
  }
  render() {
    const mediaProps = {
      autoplay: this.autoplay,
      muted: this.muted,
      playsinline: this.playsinline,
      playsInline: this.playsinline,
      'x5-playsinline': this.playsinline,
      'webkit-playsinline': this.playsinline,
      controls: this.controls,
      crossorigin: this.crossOrigin,
      controlslist: this.controlsList,
      'data-poster': !this.hasCustomPoster() ? this.poster : undefined,
      loop: this.loop,
      preload: this.preload,
      disablePictureInPicture: this.disablePiP,
      autoPictureInPicture: this.autoPiP,
      disableRemotePlayback: this.disableRemotePlayback,
      'x-webkit-airplay': this.disableRemotePlayback ? 'deny' : 'allow',
      ref: (el) => { this.mediaEl = el; },
      onLoadedMetadata: this.onLoadedMetadata.bind(this),
      onProgress: this.onProgress.bind(this),
      onPlay: this.onPlay.bind(this),
      onPause: this.onPause.bind(this),
      onPlaying: this.onPlaying.bind(this),
      onSeeking: this.onSeeking.bind(this),
      onSeeked: this.onSeeked.bind(this),
      onRateChange: this.onRateChange.bind(this),
      onVolumeChange: this.onVolumeChange.bind(this),
      onDurationChange: this.onDurationChange.bind(this),
      onWaiting: this.onWaiting.bind(this),
      onSuspend: this.onSuspend.bind(this),
      onEnded: this.onEnded.bind(this),
      onError: this.onError.bind(this),
    };
    const audio = (h("audio", Object.assign({ class: "lazy" }, mediaProps), h("slot", null), "Your browser does not support the", h("code", null, "audio"), "element."));
    const video = (h("video", Object.assign({ class: "lazy" }, mediaProps, {
      // @ts-ignore
      onwebkitpresentationmodechanged: this.onPresentationModeChange.bind(this), onenterpictureinpicture: this.onEnterPiP.bind(this), onleavepictureinpicture: this.onLeavePiP.bind(this)
    }), h("slot", null), "Your browser does not support the", h("code", null, "video"), "element."));
    return (this.viewType === ViewType.Audio) ? audio : video;
  }
  get el() { return this; }
  static get watchers() { return {
    "mediaTitle": ["onMediaTitleChange"],
    "poster": ["onPosterChange"],
    "viewType": ["onViewTypeChange"]
  }; }
  static get style() { return fileCss; }
};

const fullscreenControlCss = "vime-fullscreen-control.hidden{display:none}";

const FullscreenControl = class extends HTMLElement {
  constructor() {
    super();
    this.__registerHost();
    this.canSetFullscreen = false;
    /**
     * The URL to an SVG element or fragment to display for entering fullscreen.
     */
    this.enterIcon = '#vime-enter-fullscreen';
    /**
     * The URL to an SVG element or fragment to display for exiting fullscreen.
     */
    this.exitIcon = '#vime-exit-fullscreen';
    /**
     * Whether the tooltip is positioned above/below the control.
     */
    this.tooltipPosition = 'top';
    /**
     * Whether the tooltip should not be displayed.
     */
    this.hideTooltip = false;
    /**
     * @inheritdoc
     */
    this.keys = 'f';
    /**
     * @internal
     */
    this.isFullscreenActive = false;
    /**
     * @internal
     */
    this.i18n = {};
    /**
     * @internal
     */
    this.playbackReady = false;
    withPlayerContext(this, [
      'isFullscreenActive',
      'playbackReady',
      'i18n',
    ]);
  }
  async onPlaybackReadyChange() {
    const player = findRootPlayer(this);
    this.canSetFullscreen = await player.canSetFullscreen();
  }
  onClick() {
    const player = findRootPlayer(this);
    !this.isFullscreenActive ? player.enterFullscreen() : player.exitFullscreen();
  }
  render() {
    const tooltip = this.isFullscreenActive ? this.i18n.exitFullscreen : this.i18n.enterFullscreen;
    const tooltipWithHint = !isUndefined(this.keys) ? `${tooltip} (${this.keys})` : tooltip;
    return (h(Host, { class: {
        hidden: !this.canSetFullscreen,
      } }, h("vime-control", { label: this.i18n.fullscreen, keys: this.keys, pressed: this.isFullscreenActive, hidden: !this.canSetFullscreen, onClick: this.onClick.bind(this) }, h("vime-icon", { href: this.isFullscreenActive ? this.exitIcon : this.enterIcon }), h("vime-tooltip", { hidden: this.hideTooltip, position: this.tooltipPosition, direction: this.tooltipDirection }, tooltipWithHint))));
  }
  static get watchers() { return {
    "playbackReady": ["onPlaybackReadyChange"]
  }; }
  static get style() { return fullscreenControlCss; }
};

const HLS = class extends HTMLElement {
  constructor() {
    super();
    this.__registerHost();
    this.vLoadStart = createEvent(this, "vLoadStart", 7);
    this.hasAttached = false;
    /**
     * The NPM package version of the `hls.js` library to download and use if HLS is not natively
     * supported.
     */
    this.version = 'latest';
    /**
     * @inheritdoc
     */
    this.preload = 'metadata';
    withProviderConnect(this);
  }
  connectedCallback() {
    this.dispatch = createProviderDispatcher(this);
    if (this.mediaEl)
      this.setupHls();
  }
  disconnectedCallback() {
    this.destroyHls();
  }
  get src() {
    if (isNullOrUndefined(this.videoProvider))
      return undefined;
    const sources = this.videoProvider.querySelectorAll('source');
    const currSource = Array.from(sources)
      .find((source) => hlsRegex.test(source.src) || hlsTypeRegex.test(source.type));
    return currSource === null || currSource === void 0 ? void 0 : currSource.src;
  }
  async setupHls() {
    if (!isUndefined(this.hls) || canPlayHLSNatively())
      return;
    try {
      const url = `https://cdn.jsdelivr.net/npm/hls.js@${this.version}`;
      const Hls = await loadSDK(url, 'Hls');
      if (!Hls.isSupported()) {
        this.dispatch('errors', [new Error('hls.js is not supported')]);
        return;
      }
      this.hls = new Hls(this.config);
      this.hls.on(Hls.Events.MEDIA_ATTACHED, () => {
        this.hasAttached = true;
        this.onSrcChange();
      });
      this.hls.on(Hls.Events.ERROR, (e, data) => {
        if (data.fatal) {
          switch (data.type) {
            case Hls.ErrorTypes.NETWORK_ERROR:
              this.hls.startLoad();
              break;
            case Hls.ErrorTypes.MEDIA_ERROR:
              this.hls.recoverMediaError();
              break;
            default:
              this.destroyHls();
              break;
          }
        }
        this.dispatch('errors', [{ e, data }]);
      });
      this.hls.on(Hls.Events.MANIFEST_PARSED, () => {
        this.dispatch('mediaType', MediaType.Video);
        this.dispatch('currentSrc', this.src);
        this.dispatch('playbackReady', true);
      });
      this.hls.attachMedia(this.mediaEl);
    }
    catch (e) {
      this.dispatch('errors', [e]);
    }
  }
  destroyHls() {
    var _a;
    (_a = this.hls) === null || _a === void 0 ? void 0 : _a.destroy();
    this.hasAttached = false;
  }
  async onMediaElChange(event) {
    this.destroyHls();
    if (isUndefined(event.detail))
      return;
    this.mediaEl = event.detail;
    // Need a small delay incase the media element changes rapidly and Hls.js can't reattach.
    setTimeout(async () => { await this.setupHls(); }, 50);
  }
  async onSrcChange() {
    if (this.hasAttached && this.hls.url !== this.src) {
      this.vLoadStart.emit();
      this.hls.loadSource(this.src);
    }
  }
  /**
   * @internal
   */
  async getAdapter() {
    const adapter = await this.videoProvider.getAdapter();
    const canVideoProviderPlay = adapter.canPlay;
    return Object.assign(Object.assign({}, adapter), { getInternalPlayer: async () => this.hls, canPlay: async (type) => (isString(type) && hlsRegex.test(type))
        || canVideoProviderPlay(type) });
  }
  render() {
    return (h("vime-video", { willAttach: !canPlayHLSNatively(), crossOrigin: this.crossOrigin, preload: this.preload, poster: this.poster, controlsList: this.controlsList, autoPiP: this.autoPiP, disablePiP: this.disablePiP, disableRemotePlayback: this.disableRemotePlayback, mediaTitle: this.mediaTitle, ref: (el) => { this.videoProvider = el; } }, h("slot", null)));
  }
};

const iconCss = "vime-icon>svg{display:block;color:var(--vm-icon-color);width:var(--vm-icon-width, 18px);height:var(--vm-icon-height, 18px);transform:scale(var(--vm-icon-scale, 1));fill:currentColor;pointer-events:none}";

const Icon = class extends HTMLElement {
  constructor() {
    super();
    this.__registerHost();
  }
  render() {
    return (h("svg", { xmlns: "http://www.w3.org/2000/svg", role: "presentation", focusable: "false" }, isString(this.href) ? h("use", { href: this.href }) : h("slot", null)));
  }
  static get style() { return iconCss; }
};

const Icons = class extends HTMLElement {
  constructor() {
    super();
    this.__registerHost();
    /**
     * The URL to an SVG sprite to load.
     */
    this.href = 'https://cdn.jsdelivr.net/npm/@vime/core@latest/icons/sprite.svg';
  }
  loadIcons() {
    return this.hasLoaded() ? undefined : loadSprite(this.href, this.findRoot());
  }
  componentWillLoad() {
    return this.loadIcons();
  }
  disconnectedCallback() {
    var _a;
    (_a = this.findExistingSprite()) === null || _a === void 0 ? void 0 : _a.remove();
  }
  findRoot() {
    var _a;
    return (_a = findShadowRoot(this.el)) !== null && _a !== void 0 ? _a : document.head;
  }
  findExistingSprite() {
    return this.findRoot().querySelector(`div[data-sprite="${this.href}"]`);
  }
  hasLoaded() {
    return !isNull(this.findExistingSprite());
  }
  get el() { return this; }
  static get watchers() { return {
    "href": ["loadIcons"]
  }; }
};

const liveIndicatorCss = "vime-live-indicator{display:flex;align-items:center;font-size:13px;font-weight:bold;letter-spacing:0.6px;color:var(--vm-control-color)}vime-live-indicator.hidden{display:none}vime-live-indicator>.indicator{display:inline-block;width:8px;height:8px;border-radius:50%;margin-right:4px;background-color:var(--vm-live-indicator-color, red)}";

const LiveIndicator = class extends HTMLElement {
  constructor() {
    super();
    this.__registerHost();
    /**
     * @internal
     */
    this.isLive = false;
    /**
     * @internal
     */
    this.i18n = {};
    withPlayerContext(this, ['isLive', 'i18n']);
  }
  render() {
    return (h(Host, { class: {
        hidden: !this.isLive,
      } }, h("div", { class: "indicator" }), this.i18n.live));
  }
  static get style() { return liveIndicatorCss; }
};

const menuCss = "vime-menu{display:flex;flex-direction:column;position:relative;overflow:hidden;width:100%;height:auto;pointer-events:auto;text-align:left;color:var(--vm-menu-color);background:var(--vm-menu-bg);font-size:var(--vm-menu-font-size);font-weight:var(--vm-menu-font-weight);z-index:var(--vm-menu-z-index)}vime-menu[aria-hidden=true]{display:none}vime-menu:focus{outline:0}";

const Menu = class extends HTMLElement {
  constructor() {
    super();
    this.__registerHost();
    this.vOpen = createEvent(this, "vOpen", 7);
    this.vClose = createEvent(this, "vClose", 7);
    this.vMenuItemsChange = createEvent(this, "vMenuItemsChange", 7);
    this.vFocusMenuItemChange = createEvent(this, "vFocusMenuItemChange", 7);
    this.shouldFocusOnOpen = false;
    this.currFocusedMenuItem = 0;
    /**
     * Whether the menu is open/visible.
     */
    this.active = false;
  }
  onMenuItemsChange() {
    this.vMenuItemsChange.emit(this.menuItems);
  }
  async onFocusedMenuItemChange() {
    const menuItem = await this.getFocusedMenuItem();
    this.vFocusMenuItemChange.emit(menuItem);
  }
  onActiveChange() {
    if (this.active) {
      this.findMenuItems();
      this.findSubmenus();
    }
    this.active ? this.vOpen.emit() : this.vClose.emit();
  }
  connectedCallback() {
    this.findMenuItems();
  }
  componentDidRender() {
    if (this.active && this.shouldFocusOnOpen) {
      writeTask(() => {
        this.el.focus();
        this.shouldFocusOnOpen = false;
      });
    }
  }
  /**
   * Returns the controller responsible for opening/closing this menu.
   */
  async getController() {
    return document.querySelector(`#${this.controller}`);
  }
  /**
   * Returns the currently focused menu item.
   */
  async getFocusedMenuItem() {
    return this.menuItems[this.currFocusedMenuItem];
  }
  /**
   * This should be called directly before opening the menu to set the keyboard focus on it. This
   * is a one-time operation and needs to be called everytime prior to opening the menu.
   */
  async focusOnOpen() {
    this.shouldFocusOnOpen = true;
  }
  findMenuItems() {
    this.menuItems = document.querySelectorAll(buildNoAncestorSelector(`#${this.identifier}`, 'vime-menu', 'vime-menu-item', 5));
  }
  async focusController() {
    const controller = await this.getController();
    controller === null || controller === void 0 ? void 0 : controller.focus();
  }
  focusMenuItem(index) {
    var _a;
    let boundIndex = (index >= 0) ? index : (this.menuItems.length - 1);
    if (boundIndex >= this.menuItems.length)
      boundIndex = 0;
    this.currFocusedMenuItem = boundIndex;
    (_a = this.menuItems[boundIndex]) === null || _a === void 0 ? void 0 : _a.focus();
  }
  openSubmenu() {
    const menuItem = this.menuItems[this.currFocusedMenuItem];
    if (isUndefined(menuItem))
      return;
    menuItem.click();
    writeTask(() => {
      const submenu = document.querySelector(`#${menuItem.menu}`);
      submenu === null || submenu === void 0 ? void 0 : submenu.focus();
    });
  }
  onOpen(event) {
    event.stopPropagation();
    this.findMenuItems();
    this.currFocusedMenuItem = 0;
    // Prevents forwarding click event that opened the menu to menu item.
    setTimeout(() => { var _a; (_a = this.menuItems[this.currFocusedMenuItem]) === null || _a === void 0 ? void 0 : _a.focus(); }, 10);
    this.active = true;
  }
  onClose() {
    this.currFocusedMenuItem = -1;
    this.active = false;
    this.focusController();
  }
  onClick(event) {
    event.stopPropagation();
  }
  onKeyDown(event) {
    if (!this.active || this.menuItems.length === 0)
      return;
    event.preventDefault();
    event.stopPropagation();
    switch (event.key) {
      case 'Escape':
        this.onClose();
        break;
      case 'ArrowDown':
      case 'Tab':
        this.focusMenuItem(this.currFocusedMenuItem + 1);
        break;
      case 'ArrowUp':
        this.focusMenuItem(this.currFocusedMenuItem - 1);
        break;
      case 'ArrowLeft':
        this.onClose();
        break;
      case 'ArrowRight':
      case 'Enter':
      case ' ':
        this.openSubmenu();
        break;
      case 'Home':
      case 'PageUp':
        this.focusMenuItem(0);
        break;
      case 'End':
      case 'PageDown':
        this.focusMenuItem(this.menuItems.length - 1);
        break;
    }
  }
  findSubmenus() {
    this.submenus = document.querySelectorAll(buildNoAncestorSelector(`#${this.identifier}`, 'vime-menu', 'vime-menu', 4));
  }
  isValidSubmenu(submenu) {
    if (isNull(submenu))
      return false;
    return !isUndefined(Array.from(this.submenus).find((menu) => menu.id === submenu.id));
  }
  toggleSubmenu(submenu, isActive) {
    if (!this.isValidSubmenu(submenu))
      return;
    Array.from(this.menuItems)
      .filter((menuItem) => menuItem.identifier !== submenu.controller)
      .forEach((menuItem) => { menuItem.hidden = isActive; });
    submenu.active = isActive;
  }
  onSubmenuOpen(event) {
    const submenu = event.target;
    this.toggleSubmenu(submenu, true);
  }
  onSubmenuClose(event) {
    const submenu = event.target;
    this.toggleSubmenu(submenu, false);
  }
  onWindowClick() {
    if (this.active)
      this.active = false;
  }
  onWindowKeyDown(event) {
    if (this.active && (event.key === 'Escape'))
      this.onClose();
  }
  render() {
    return (h(Host, { id: this.identifier, role: "menu", tabindex: "-1", "aria-labelledby": this.controller, "aria-hidden": !this.active ? 'true' : 'false', onFocus: this.onOpen.bind(this), onClick: this.onClick.bind(this), onKeyDown: this.onKeyDown.bind(this) }, h("div", null, h("slot", null))));
  }
  get el() { return this; }
  static get watchers() { return {
    "menuItems": ["onMenuItemsChange"],
    "currFocusedMenuItem": ["onFocusedMenuItemChange"],
    "active": ["onActiveChange"]
  }; }
  static get style() { return menuCss; }
};

const menuItemCss = "vime-menu-item{display:flex;align-items:center;flex-direction:row;width:100%;cursor:pointer;color:var(--vm-menu-color);background:var(--vm-menu-bg);font-size:var(--vm-menu-font-size);font-weight:var(--vm-menu-font-weight);padding:var(--vm-menu-item-padding)}vime-menu-item:focus{outline:0}vime-menu-item.hidden{display:none}vime-menu-item.tapHighlight{background:var(--vm-menu-item-tap-highlight)}vime-menu-item.showDivider{border-bottom:0.5px solid var(--vm-menu-item-divider-color)}vime-menu-item.notTouch:hover,vime-menu-item.notTouch:focus{outline:0;color:var(--vm-menu-item-focus-color);background-color:var(--vm-menu-item-focus-bg)}vime-menu-item[aria-hidden=true]{display:none}vime-menu-item[aria-checked=true] svg{opacity:1;visibility:visible}vime-menu-item vime-icon{display:inline-block}vime-menu-item svg{fill:currentColor;pointer-events:none;width:var(--vm-menu-item-check-icon-width, 10px) !important;height:var(--vm-menu-item-check-icon-height, 10px) !important;margin-right:10px;opacity:0;visibility:hidden;transition:var(--vm-fade-transition)}vime-menu-item .hint{display:inline-block;margin-left:auto;overflow:hidden;pointer-events:none;margin-right:6px;font-size:var(--vm-menu-item-hint-font-size);opacity:var(--vm-menu-item-hint-opacity);color:var(--vm-menu-item-hint-color)}vime-menu-item .badge{display:inline-block;line-height:1;overflow:hidden;pointer-events:none;color:var(--vm-menu-item-badge-color);background:var(--vm-menu-item-badge-bg);font-size:var(--vm-menu-item-badge-font-size)}vime-menu-item .spacer{flex:1}vime-menu-item .arrow{color:var(--vm-menu-item-arrow-color);border:2px solid;padding:2px;display:inline-block;border-width:0 2px 2px 0}vime-menu-item .arrow.left{margin-right:6px;transform:rotate(135deg)}vime-menu-item .arrow.right{transform:rotate(-45deg);opacity:0.38}";

const MenuItem = class extends HTMLElement {
  constructor() {
    super();
    this.__registerHost();
    this.showTapHighlight = false;
    /**
     * Whether the item is displayed or not.
     */
    this.hidden = false;
    /**
     * The URL to an SVG element or fragment to load.
     */
    this.checkedIcon = '#vime-checkmark';
    /**
     * @internal
     */
    this.isTouch = false;
    withPlayerContext(this, ['isTouch']);
  }
  onClick() {
    if (isUndefined(this.menu))
      return;
    const submenu = document.querySelector(`#${this.menu}`);
    if (!isNull(submenu))
      submenu.active = !this.expanded;
  }
  onTouchStart() {
    this.showTapHighlight = true;
    setTimeout(() => { this.showTapHighlight = false; }, 100);
  }
  onMouseLeave() {
    this.el.blur();
  }
  render() {
    var _a;
    const isCheckedDefined = !isUndefined(this.checked);
    const isMenuDefined = !isUndefined(this.menu);
    const hasExpanded = this.expanded ? 'true' : 'false';
    const isChecked = this.checked ? 'true' : 'false';
    const showCheckedIcon = isCheckedDefined && !isUndefined(this.checkedIcon);
    const showLeftNavArrow = isMenuDefined && this.expanded;
    const showRightNavArrow = isMenuDefined && !this.expanded;
    const showHint = !isUndefined(this.hint)
      && !isCheckedDefined
      && (!isMenuDefined || !this.expanded);
    const showBadge = !isUndefined(this.badge) && (!showHint && !showRightNavArrow);
    const hasSpacer = showHint || showBadge || showRightNavArrow;
    return (h(Host, { class: {
        notTouch: !this.isTouch,
        tapHighlight: this.showTapHighlight,
        showDivider: isMenuDefined && ((_a = this.expanded) !== null && _a !== void 0 ? _a : false),
      }, id: this.identifier, role: isCheckedDefined ? 'menuitemradio' : 'menuitem', tabindex: "0", "aria-label": this.label, "aria-hidden": this.hidden ? 'true' : 'false', "aria-haspopup": isMenuDefined ? 'true' : undefined, "aria-controls": this.menu, "aria-expanded": isMenuDefined ? hasExpanded : undefined, "aria-checked": isCheckedDefined ? isChecked : undefined, onClick: this.onClick.bind(this), onTouchStart: this.onTouchStart.bind(this), onMouseLeave: this.onMouseLeave.bind(this) }, showCheckedIcon && h("vime-icon", { href: this.checkedIcon }), showLeftNavArrow && h("span", { class: "arrow left" }), this.label, hasSpacer && h("span", { class: "spacer" }), showHint && h("span", { class: "hint" }, this.hint), showBadge && h("span", { class: "badge" }, this.badge), showRightNavArrow && h("span", { class: "arrow right" })));
  }
  get el() { return this; }
  static get style() { return menuItemCss; }
};

const MenuRadio = class extends HTMLElement {
  constructor() {
    super();
    this.__registerHost();
    this.vCheck = createEvent(this, "vCheck", 7);
    /**
     * Whether the radio item is selected or not.
     */
    this.checked = false;
    /**
     * The URL to an SVG element or fragment to load.
     */
    this.checkedIcon = '#vime-checkmark';
  }
  onClick() {
    this.checked = true;
    this.vCheck.emit();
  }
  render() {
    return (h("vime-menu-item", { label: this.label, checked: this.checked, badge: this.badge, checkedIcon: this.checkedIcon, onClick: this.onClick.bind(this) }));
  }
};

const MenuRadioGroup = class extends HTMLElement {
  constructor() {
    super();
    this.__registerHost();
    this.vCheck = createEvent(this, "vCheck", 7);
  }
  onValueChange() {
    this.findRadios().forEach((radio) => {
      // eslint-disable-next-line no-param-reassign
      radio.checked = (radio.value === this.value);
    });
  }
  connectedCallback() {
    this.onValueChange();
  }
  onSelectionChange(event) {
    const radio = event.target;
    this.value = radio.value;
  }
  findRadios() {
    return this.el.querySelectorAll('vime-menu-radio');
  }
  render() {
    return (h("slot", null));
  }
  get el() { return this; }
  static get watchers() { return {
    "value": ["onValueChange"]
  }; }
};

const MuteControl = class extends HTMLElement {
  constructor() {
    super();
    this.__registerHost();
    /**
     * The URL to an SVG element or fragment.
     */
    this.lowVolumeIcon = '#vime-volume-low';
    /**
     * The URL to an SVG element or fragment.
     */
    this.highVolumeIcon = '#vime-volume-high';
    /**
     * The URL to an SVG element or fragment.
     */
    this.mutedIcon = '#vime-volume-mute';
    /**
     * Whether the tooltip is positioned above/below the control.
     */
    this.tooltipPosition = 'top';
    /**
     * Whether the tooltip should not be displayed.
     */
    this.hideTooltip = false;
    /**
     * @inheritdoc
     */
    this.keys = 'm';
    /**
     * @internal
     */
    this.volume = 50;
    /**
     * @internal
     */
    this.muted = false;
    /**
     * @internal
     */
    this.i18n = {};
    withPlayerContext(this, ['muted', 'volume', 'i18n']);
  }
  connectedCallback() {
    this.dispatch = createDispatcher(this);
  }
  getIcon() {
    const volumeIcon = (this.volume < 50) ? this.lowVolumeIcon : this.highVolumeIcon;
    return (this.muted || (this.volume === 0)) ? this.mutedIcon : volumeIcon;
  }
  onClick() {
    this.dispatch('muted', !this.muted);
  }
  render() {
    const tooltip = this.muted ? this.i18n.unmute : this.i18n.mute;
    const tooltipWithHint = !isUndefined(this.keys) ? `${tooltip} (${this.keys})` : tooltip;
    return (h("vime-control", { label: this.i18n.mute, pressed: this.muted, keys: this.keys, onClick: this.onClick.bind(this) }, h("vime-icon", { href: this.getIcon() }), h("vime-tooltip", { hidden: this.hideTooltip, position: this.tooltipPosition, direction: this.tooltipDirection }, tooltipWithHint)));
  }
};

const pipControlCss = "vime-pip-control.hidden{display:none}";

const PiPControl = class extends HTMLElement {
  constructor() {
    super();
    this.__registerHost();
    this.canSetPiP = false;
    /**
     * The URL to an SVG element or fragment to display for entering PiP.
     */
    this.enterIcon = '#vime-enter-pip';
    /**
     * The URL to an SVG element or fragment to display for exiting PiP.
     */
    this.exitIcon = '#vime-exit-pip';
    /**
     * Whether the tooltip is positioned above/below the control.
     */
    this.tooltipPosition = 'top';
    /**
     * Whether the tooltip should not be displayed.
     */
    this.hideTooltip = false;
    /**
     * @inheritdoc
     */
    this.keys = 'p';
    /**
     * @internal
     */
    this.isPiPActive = false;
    /**
     * @internal
     */
    this.i18n = {};
    /**
     * @internal
     */
    this.playbackReady = false;
    withPlayerContext(this, ['isPiPActive', 'playbackReady', 'i18n']);
  }
  async onPlaybackReadyChange() {
    const player = findRootPlayer(this);
    this.canSetPiP = await player.canSetPiP();
  }
  onClick() {
    const player = findRootPlayer(this);
    !this.isPiPActive ? player.enterPiP() : player.exitPiP();
  }
  render() {
    const tooltip = this.isPiPActive ? this.i18n.exitPiP : this.i18n.enterPiP;
    const tooltipWithHint = !isUndefined(this.keys) ? `${tooltip} (${this.keys})` : tooltip;
    return (h(Host, { class: {
        hidden: !this.canSetPiP,
      } }, h("vime-control", { label: this.i18n.pip, keys: this.keys, pressed: this.isPiPActive, hidden: !this.canSetPiP, onClick: this.onClick.bind(this) }, h("vime-icon", { href: this.isPiPActive ? this.exitIcon : this.enterIcon }), h("vime-tooltip", { hidden: this.hideTooltip, position: this.tooltipPosition, direction: this.tooltipDirection }, tooltipWithHint))));
  }
  static get watchers() { return {
    "playbackReady": ["onPlaybackReadyChange"]
  }; }
  static get style() { return pipControlCss; }
};

const PlaybackControl = class extends HTMLElement {
  constructor() {
    super();
    this.__registerHost();
    /**
     * The URL to an SVG element or fragment to load.
     */
    this.playIcon = '#vime-play';
    /**
     * The URL to an SVG element or fragment to load.
     */
    this.pauseIcon = '#vime-pause';
    /**
     * Whether the tooltip is positioned above/below the control.
     */
    this.tooltipPosition = 'top';
    /**
     * Whether the tooltip should not be displayed.
     */
    this.hideTooltip = false;
    /**
     * @inheritdoc
     */
    this.keys = 'k';
    /**
     * @internal
     */
    this.paused = true;
    /**
     * @internal
     */
    this.i18n = {};
    withPlayerContext(this, ['paused', 'i18n']);
  }
  connectedCallback() {
    this.dispatch = createDispatcher(this);
  }
  onClick() {
    this.dispatch('paused', !this.paused);
  }
  render() {
    const tooltip = this.paused ? this.i18n.play : this.i18n.pause;
    const tooltipWithHint = !isUndefined(this.keys) ? `${tooltip} (${this.keys})` : tooltip;
    return (h("vime-control", { label: this.i18n.playback, keys: this.keys, pressed: !this.paused, onClick: this.onClick.bind(this) }, h("vime-icon", { href: this.paused ? this.playIcon : this.pauseIcon }), h("vime-tooltip", { hidden: this.hideTooltip, position: this.tooltipPosition, direction: this.tooltipDirection }, tooltipWithHint)));
  }
};

const apiMap = [
  [
    'requestFullscreen',
    'exitFullscreen',
    'fullscreenElement',
    'fullscreenEnabled',
    'fullscreenchange',
    'fullscreenerror',
    'fullscreen',
  ],
  // WebKit
  [
    'webkitRequestFullscreen',
    'webkitExitFullscreen',
    'webkitFullscreenElement',
    'webkitFullscreenEnabled',
    'webkitfullscreenchange',
    'webkitfullscreenerror',
    '-webkit-full-screen',
  ],
  // Mozilla
  [
    'mozRequestFullScreen',
    'mozCancelFullScreen',
    'mozFullScreenElement',
    'mozFullScreenEnabled',
    'mozfullscreenchange',
    'mozfullscreenerror',
    '-moz-full-screen',
  ],
  // Microsoft
  [
    'msRequestFullscreen',
    'msExitFullscreen',
    'msFullscreenElement',
    'msFullscreenEnabled',
    'MSFullscreenChange',
    'MSFullscreenError',
    '-ms-fullscreen',
  ],
];
/**
 * Normalizes native fullscreen API differences across browsers.
 *
 * @ref https://github.com/videojs/video.js/blob/7.6.x/src/js/fullscreen-api.js
 */
const getFullscreenApi = () => {
  const api = { prefixed: false };
  const specApi = apiMap[0];
  let browserApi;
  // Determine the supported set of functions.
  for (let i = 0; i < apiMap.length; i += 1) {
    // Check for exitFullscreen function.
    if (apiMap[i][1] in document) {
      browserApi = apiMap[i];
      break;
    }
  }
  // Map the browser API names to the spec API names.
  if (browserApi) {
    for (let i = 0; i < browserApi.length; i += 1) {
      api[specApi[i]] = browserApi[i];
    }
    api.prefixed = browserApi[0] !== specApi[0];
  }
  return api;
};

class Fullscreen {
  constructor(el, listener) {
    this.el = el;
    this.listener = listener;
    this.disposal = new Disposal();
    this.api = getFullscreenApi();
    if (this.isSupported) {
      this.disposal.add(listen(document, this.api.fullscreenchange, this.onFullscreenChange.bind(this)));
      /* *
       * We have to listen to this on webkit, because no `fullscreenchange` event is fired when the
       * video element enters or exits fullscreen by:
       *
       *  1. Clicking the native Html5 fullscreen video control.
       *  2. Calling requestFullscreen from the video element directly.
       *  3. Calling requestFullscreen inside an iframe.
       * */
      if (document.webkitExitFullscreen) {
        this.disposal.add(listen(document, 'webkitfullscreenchange', this.onFullscreenChange.bind(this)));
      }
      // We listen to this for the same reasons as above except when the browser is Firefox.
      if (document.mozCancelFullScreen) {
        this.disposal.add(listen(document, 'mozfullscreenchange', this.onFullscreenChange.bind(this)));
      }
    }
  }
  async enterFullscreen(options) {
    if (!this.isSupported)
      throw Error('Fullscreen API is not available.');
    return this.el[this.api.requestFullscreen](options);
  }
  async exitFullscreen() {
    if (!this.isSupported)
      throw Error('Fullscreen API is not available.');
    if (!this.isActive)
      throw Error('Player is not currently in fullscreen mode to exit.');
    return document[this.api.exitFullscreen]();
  }
  get isActive() {
    if (!this.isSupported)
      return false;
    const fullscreenEl = document[this.api.fullscreenElement];
    return (this.el === fullscreenEl)
      || this.el.matches(`:${this.api.fullscreen}`)
      || this.el.contains(fullscreenEl);
  }
  get isSupported() {
    return !isUndefined(this.api.requestFullscreen);
  }
  onFullscreenChange() {
    this.listener(this.isActive);
  }
  destroy() {
    this.disposal.empty();
  }
}

let players = [];
class Autopause {
  constructor(player) {
    this.player = player;
    players.push(player);
  }
  willPlay() {
    players.forEach((p) => {
      try {
        // eslint-disable-next-line no-param-reassign
        if (p !== this.player && p.autopause)
          p.paused = true;
      }
      catch (e) {
        // Might throw when testing because `disconnectCallback` isn't called.
      }
    });
  }
  destroy() {
    players = players.filter((p) => p !== this.player);
  }
}

class Logger {
  constructor() {
    this.silent = false;
  }
  log(...args) {
    if (!this.silent && !isUndefined(console))
      console.log('[Vime tip]:', ...args);
  }
  warn(...args) {
    if (!this.silent && !isUndefined(console))
      console.error('[Vime warn]:', ...args);
  }
}

const playerCss = "vime-player{box-sizing:border-box;direction:ltr;font-family:var(--vm-player-font-family);-moz-osx-font-smoothing:auto;-webkit-font-smoothing:subpixel-antialiased;-webkit-tap-highlight-color:transparent;font-variant-numeric:tabular-nums;font-weight:500;line-height:1.7;width:100%;display:block;max-width:100%;min-width:275px;min-height:40px;position:relative;text-shadow:none;outline:0;transition:box-shadow 0.3s ease;box-shadow:var(--vm-player-box-shadow)}vime-player.idle{cursor:none}vime-player.audio{background-color:transparent !important}vime-player.video{height:0;overflow:hidden;background-color:var(--vm-player-bg, #000)}vime-player.fullscreen{margin:0;border-radius:0;width:100%;height:100%;padding-bottom:0 !important}vime-player *,vime-player *::after,vime-player *::before{box-sizing:inherit}vime-player button{font:inherit;line-height:inherit;width:auto;margin:0}vime-player button::-moz-focus-inner{border:0}vime-player a,vime-player button,vime-player input,vime-player label{touch-action:manipulation}vime-player .blocker{position:absolute;top:0;left:0;width:100%;height:100%;z-index:var(--vm-blocker-z-index);display:inline-block}";

let idCount$1 = 0;
const immediateAdapterCall = new Set(['currentTime', 'paused']);
const Player = class extends HTMLElement {
  constructor() {
    super();
    this.__registerHost();
    this.vThemeChange = createEvent(this, "vThemeChange", 7);
    this.vPausedChange = createEvent(this, "vPausedChange", 7);
    this.vPlay = createEvent(this, "vPlay", 7);
    this.vPlayingChange = createEvent(this, "vPlayingChange", 7);
    this.vSeekingChange = createEvent(this, "vSeekingChange", 7);
    this.vSeeked = createEvent(this, "vSeeked", 7);
    this.vBufferingChange = createEvent(this, "vBufferingChange", 7);
    this.vDurationChange = createEvent(this, "vDurationChange", 7);
    this.vCurrentTimeChange = createEvent(this, "vCurrentTimeChange", 7);
    this.vAttachedChange = createEvent(this, "vAttachedChange", 7);
    this.vReady = createEvent(this, "vReady", 7);
    this.vPlaybackReady = createEvent(this, "vPlaybackReady", 7);
    this.vPlaybackStarted = createEvent(this, "vPlaybackStarted", 7);
    this.vPlaybackEnded = createEvent(this, "vPlaybackEnded", 7);
    this.vBufferedChange = createEvent(this, "vBufferedChange", 7);
    this.vCurrentCaptionChange = createEvent(this, "vCurrentCaptionChange", 7);
    this.vTextTracksChange = createEvent(this, "vTextTracksChange", 7);
    this.vErrorsChange = createEvent(this, "vErrorsChange", 7);
    this.vLoadStart = createEvent(this, "vLoadStart", 7);
    this.vCurrentProviderChange = createEvent(this, "vCurrentProviderChange", 7);
    this.vCurrentSrcChange = createEvent(this, "vCurrentSrcChange", 7);
    this.vCurrentPosterChange = createEvent(this, "vCurrentPosterChange", 7);
    this.vMediaTitleChange = createEvent(this, "vMediaTitleChange", 7);
    this.vControlsChange = createEvent(this, "vControlsChange", 7);
    this.vPlaybackRateChange = createEvent(this, "vPlaybackRateChange", 7);
    this.vPlaybackRatesChange = createEvent(this, "vPlaybackRatesChange", 7);
    this.vPlaybackQualityChange = createEvent(this, "vPlaybackQualityChange", 7);
    this.vPlaybackQualitiesChange = createEvent(this, "vPlaybackQualitiesChange", 7);
    this.vMutedChange = createEvent(this, "vMutedChange", 7);
    this.vVolumeChange = createEvent(this, "vVolumeChange", 7);
    this.vViewTypeChange = createEvent(this, "vViewTypeChange", 7);
    this.vMediaTypeChange = createEvent(this, "vMediaTypeChange", 7);
    this.vLiveChange = createEvent(this, "vLiveChange", 7);
    this.vTouchChange = createEvent(this, "vTouchChange", 7);
    this.vLanguageChange = createEvent(this, "vLanguageChange", 7);
    this.vI18nChange = createEvent(this, "vI18nChange", 7);
    this.vTranslationsChange = createEvent(this, "vTranslationsChange", 7);
    this.vLanguagesChange = createEvent(this, "vLanguagesChange", 7);
    this.vFullscreenChange = createEvent(this, "vFullscreenChange", 7);
    this.vPiPChange = createEvent(this, "vPiPChange", 7);
    this.disposal = new Disposal();
    // Keeps track of state between render cycles to fire events.
    this.cache = new Map();
    // Keeps track of the state of the provider.
    this.providerCache = new Map();
    // Queue of adapter calls to be run when the media is ready for playback.
    this.adapterCalls = [];
    this.shouldCheckForProviderChange = false;
    /**
     * ------------------------------------------------------
     * Props
     * ------------------------------------------------------
     */
    /**
     * @inheritDoc
     */
    this.attached = false;
    /**
     * @internal
     */
    this.logger = new Logger();
    /**
     * @inheritDoc
     */
    this.paused = true;
    /**
     * @inheritDoc
     */
    this.playing = false;
    /**
     * @inheritDoc
     */
    this.duration = -1;
    /**
     * @inheritDoc
     */
    this.currentTime = 0;
    /**
     * @inheritDoc
     */
    this.autoplay = false;
    /**
     * @inheritDoc
     */
    this.ready = false;
    /**
     * @inheritDoc
     */
    this.playbackReady = false;
    /**
     * @inheritDoc
     */
    this.loop = false;
    /**
     * @inheritDoc
     */
    this.muted = false;
    /**
     * @inheritDoc
     */
    this.buffered = 0;
    /**
     * @inheritDoc
     */
    this.playbackRate = 1;
    this.lastRateCheck = 1;
    /**
     * @inheritDoc
     */
    this.playbackRates = [1];
    /**
     * @inheritDoc
     */
    this.playbackQualities = [];
    /**
     * @inheritDoc
     */
    this.seeking = false;
    /**
     * @inheritDoc
     */
    this.debug = false;
    /**
     * @inheritDoc
     */
    this.playbackStarted = false;
    /**
     * @inheritDoc
     */
    this.playbackEnded = false;
    /**
     * @inheritDoc
     */
    this.buffering = false;
    /**
     * @inheritDoc
     */
    this.controls = false;
    /**
     * @inheritDoc
     */
    this.isControlsActive = false;
    /**
     * @inheritDoc
     */
    this.errors = [];
    /**
     * @inheritDoc
     */
    this.isCaptionsActive = false;
    /**
     * @inheritDoc
     */
    this.isSettingsActive = false;
    /**
     * @inheritDoc
     */
    this.volume = 50;
    /**
     * @inheritDoc
     */
    this.isFullscreenActive = false;
    /**
     * @inheritDoc
     */
    this.aspectRatio = '16:9';
    /**
     * @inheritDoc
     */
    this.isAudioView = false;
    /**
     * @inheritDoc
     */
    this.isVideoView = false;
    /**
     * @inheritDoc
     */
    this.isAudio = false;
    /**
     * @inheritDoc
     */
    this.isVideo = false;
    /**
     * @inheritDoc
     */
    this.isLive = false;
    /**
     * @inheritDoc
     */
    this.isMobile = IS_MOBILE;
    /**
     * @inheritDoc
     */
    this.isTouch = false;
    /**
     * @inheritDoc
     */
    this.isPiPActive = false;
    /**
     * @inheritDoc
     */
    this.autopause = true;
    /**
     * @inheritDoc
     */
    this.playsinline = false;
    /**
     * @inheritDoc
     */
    this.language = 'en';
    /**
     * @inheritDoc
     */
    this.translations = { en };
    /**
     * @inheritDoc
     */
    this.languages = ['en'];
    /**
     * @inheritDoc
     */
    this.i18n = en;
    this.hasMediaChanged = false;
  }
  onPausedChange() {
    if (this.paused) {
      this.playing = false;
    }
    else {
      this.autopauseMgr.willPlay();
    }
    this.safeAdapterCall('paused', !this.paused ? 'play' : 'pause');
  }
  onDurationChange() {
    this.isLive = this.duration === Infinity;
  }
  onCurrentTimeChange() {
    const duration = this.playbackReady ? this.duration : Infinity;
    this.currentTime = Math.max(0, Math.min(this.currentTime, duration));
    this.safeAdapterCall('currentTime', 'setCurrentTime');
  }
  onPlaybackReadyChange() {
    if (!this.ready)
      this.ready = true;
  }
  onMutedChange() {
    this.safeAdapterCall('muted', 'setMuted');
  }
  async onPlaybackRateChange(newRate, prevRate) {
    var _a, _b;
    if (newRate === this.lastRateCheck)
      return;
    if (!(await ((_b = (_a = this.adapter) === null || _a === void 0 ? void 0 : _a.canSetPlaybackRate) === null || _b === void 0 ? void 0 : _b.call(_a)))) {
      this.logger.log('provider cannot change `playbackRate`.');
      this.lastRateCheck = prevRate;
      this.playbackRate = prevRate;
      return;
    }
    if (!this.playbackRates.includes(newRate)) {
      this.logger.log(`invalid \`playbackRate\` of ${newRate}, `
        + `valid values are [${this.playbackRates.join(', ')}]`);
      this.lastRateCheck = prevRate;
      this.playbackRate = prevRate;
      return;
    }
    this.lastRateCheck = newRate;
    this.safeAdapterCall('playbackRate', 'setPlaybackRate');
  }
  async onPlaybackQualityChange(newQuality, prevQuality) {
    var _a, _b;
    if (isUndefined(newQuality) || (newQuality === this.lastQualityCheck))
      return;
    if (!(await ((_b = (_a = this.adapter) === null || _a === void 0 ? void 0 : _a.canSetPlaybackQuality) === null || _b === void 0 ? void 0 : _b.call(_a)))) {
      this.logger.log('provider cannot change `playbackQuality`.');
      this.lastQualityCheck = prevQuality;
      this.playbackQuality = prevQuality;
      return;
    }
    if (!this.playbackQualities.includes(newQuality)) {
      this.logger.log(`invalid \`playbackQuality\` of ${newQuality}, `
        + `valid values are [${this.playbackQualities.join(', ')}]`);
      this.lastQualityCheck = prevQuality;
      this.playbackQuality = prevQuality;
      return;
    }
    this.lastQualityCheck = newQuality;
    this.safeAdapterCall('playbackQuality', 'setPlaybackQuality');
  }
  onDebugChange() {
    this.logger.silent = !this.debug;
  }
  onErrorsChange(_, prevErrors) {
    if (prevErrors.length > 0)
      this.errors.unshift(prevErrors);
    this.logger.warn(this.errors[this.errors.length - 1]);
  }
  onTextTracksChange() {
    var _a;
    if (isUndefined(this.textTracks)) {
      (_a = this.textTracksChangeListener) === null || _a === void 0 ? void 0 : _a.call(this);
      this.currentCaption = undefined;
      this.isCaptionsActive = false;
      return;
    }
    this.onActiveCaptionChange();
    this.textTracksChangeListener = listen(this.textTracks, 'change', this.onActiveCaptionChange.bind(this));
  }
  async onVolumeChange() {
    this.volume = Math.max(0, Math.min(this.volume, 100));
    this.safeAdapterCall('volume', 'setVolume');
  }
  onViewTypeChange() {
    this.isAudioView = this.viewType === ViewType.Audio;
    this.isVideoView = this.viewType === ViewType.Video;
  }
  onMediaTypeChange() {
    this.isAudio = this.mediaType === MediaType.Audio;
    this.isVideo = this.mediaType === MediaType.Video;
  }
  onLanguageChange(_, prevLanguage) {
    if (!this.languages.includes(this.language)) {
      this.logger.log(`invalid \`language\` of ${this.language}, `
        + `valid values are [${this.languages.join(', ')}]`);
      this.language = prevLanguage;
      return;
    }
    this.i18n = this.translations[this.language];
  }
  onTranslationsChange() {
    Object.assign(this.translations, { en });
    this.languages = Object.keys(this.translations);
    this.i18n = this.translations[this.language];
  }
  /**
   * ------------------------------------------------------
   * Methods
   * ------------------------------------------------------
   */
  /**
   * @inheritDoc
   */
  async getProvider() {
    return this.provider;
  }
  /**
   * @internal testing only.
   */
  async setProvider(provider) {
    this.provider = provider;
    this.adapter = await this.provider.getAdapter();
  }
  /**
   * @internal
   */
  async getAdapter() {
    return this.adapter;
  }
  /**
   * @inheritDoc
   */
  async play() {
    var _a;
    return (_a = this.adapter) === null || _a === void 0 ? void 0 : _a.play();
  }
  /**
   * @inheritDoc
   */
  async pause() {
    var _a;
    return (_a = this.adapter) === null || _a === void 0 ? void 0 : _a.pause();
  }
  /**
   * @inheritDoc
   */
  async canPlay(type) {
    var _a, _b;
    return (_b = (_a = this.adapter) === null || _a === void 0 ? void 0 : _a.canPlay(type)) !== null && _b !== void 0 ? _b : false;
  }
  /**
   * @inheritDoc
   */
  async canAutoplay() {
    return canAutoplay();
  }
  /**
   * @inheritDoc
   */
  async canMutedAutoplay() {
    return canAutoplay(true);
  }
  /**
   * @inheritDoc
   */
  async canSetPlaybackRate() {
    var _a, _b, _c;
    return (_c = (_b = (_a = this.adapter) === null || _a === void 0 ? void 0 : _a.canSetPlaybackRate) === null || _b === void 0 ? void 0 : _b.call(_a)) !== null && _c !== void 0 ? _c : false;
  }
  /**
   * @inheritDoc
   */
  async canSetPlaybackQuality() {
    var _a, _b, _c;
    return (_c = (_b = (_a = this.adapter) === null || _a === void 0 ? void 0 : _a.canSetPlaybackQuality) === null || _b === void 0 ? void 0 : _b.call(_a)) !== null && _c !== void 0 ? _c : false;
  }
  /**
   * @inheritDoc
   */
  async canSetFullscreen() {
    var _a, _b, _c;
    return this.fullscreen.isSupported || ((_c = (_b = (_a = this.adapter) === null || _a === void 0 ? void 0 : _a.canSetFullscreen) === null || _b === void 0 ? void 0 : _b.call(_a)) !== null && _c !== void 0 ? _c : false);
  }
  /**
   * @inheritDoc
   */
  async enterFullscreen(options) {
    var _a, _b, _c, _d;
    if (!this.isVideoView)
      throw Error('Cannot enter fullscreen on an audio player view.');
    if (this.fullscreen.isSupported)
      return this.fullscreen.enterFullscreen(options);
    if (await ((_b = (_a = this.adapter) === null || _a === void 0 ? void 0 : _a.canSetFullscreen) === null || _b === void 0 ? void 0 : _b.call(_a)))
      return (_d = (_c = this.adapter) === null || _c === void 0 ? void 0 : _c.enterFullscreen) === null || _d === void 0 ? void 0 : _d.call(_c, options);
    throw Error('Fullscreen API is not available.');
  }
  /**
   * @inheritDoc
   */
  async exitFullscreen() {
    var _a, _b;
    if (this.fullscreen.isSupported)
      return this.fullscreen.exitFullscreen();
    return (_b = (_a = this.adapter) === null || _a === void 0 ? void 0 : _a.exitFullscreen) === null || _b === void 0 ? void 0 : _b.call(_a);
  }
  /**
   * @inheritDoc
   */
  async canSetPiP() {
    var _a, _b, _c;
    return (_c = (_b = (_a = this.adapter) === null || _a === void 0 ? void 0 : _a.canSetPiP) === null || _b === void 0 ? void 0 : _b.call(_a)) !== null && _c !== void 0 ? _c : false;
  }
  /**
   * @inheritDoc
   */
  async enterPiP() {
    var _a, _b;
    if (!this.isVideoView)
      throw Error('Cannot enter PiP mode on an audio player view.');
    if (!(await this.canSetPiP()))
      throw Error('Picture-in-Picture API is not available.');
    return (_b = (_a = this.adapter) === null || _a === void 0 ? void 0 : _a.enterPiP) === null || _b === void 0 ? void 0 : _b.call(_a);
  }
  /**
   * @inheritDoc
   */
  async exitPiP() {
    var _a, _b;
    return (_b = (_a = this.adapter) === null || _a === void 0 ? void 0 : _a.exitPiP) === null || _b === void 0 ? void 0 : _b.call(_a);
  }
  /**
   * @inheritDoc
   */
  async extendLanguage(language, translation) {
    var _a;
    const translations = Object.assign(Object.assign({}, this.translations), { [language]: Object.assign(Object.assign({}, ((_a = this.translations[language]) !== null && _a !== void 0 ? _a : {})), translation) });
    this.translations = translations;
  }
  onMediaProviderConnect(event) {
    var _a;
    event.stopImmediatePropagation();
    const newProvider = getElement(event.detail);
    if (this.provider === newProvider)
      return;
    const newProviderName = (_a = newProvider) === null || _a === void 0 ? void 0 : _a.nodeName.toLowerCase().replace('vime-', '');
    writeTask(async () => {
      var _a;
      await this.onMediaProviderDisconnect();
      this.provider = newProvider;
      this.adapter = await ((_a = this.provider) === null || _a === void 0 ? void 0 : _a.getAdapter());
      this.currentProvider = Object.values(Provider)
        .find((provider) => newProviderName === provider);
    });
  }
  onMediaProviderDisconnect(event) {
    event === null || event === void 0 ? void 0 : event.stopImmediatePropagation();
    writeTask(async () => {
      this.ready = false;
      this.provider = undefined;
      this.adapter = undefined;
      await this.onMediaChange();
    });
  }
  async onMediaChange(event) {
    event === null || event === void 0 ? void 0 : event.stopPropagation();
    // Don't reset first time otherwise props intialized by the user will be reset.
    if (!this.hasMediaChanged) {
      this.hasMediaChanged = true;
      return;
    }
    this.adapterCalls = [];
    this.providerCache.clear();
    writeTask(() => {
      Object.keys(initialState)
        .filter(shouldPropResetOnMediaChange)
        .forEach((prop) => { this[prop] = initialState[prop]; });
    });
  }
  async onStateChange(event) {
    var _a, _b, _c;
    event.stopImmediatePropagation();
    const { by, prop, value } = event.detail;
    if (!isWritableProp(prop)) {
      this.logger.warn(`${by.nodeName} tried to change \`${prop}\` but it is readonly.`);
      return;
    }
    if (!this.playbackStarted && immediateAdapterCall.has(prop)) {
      if (prop === 'paused' && !value) {
        (_a = this.adapter) === null || _a === void 0 ? void 0 : _a.play();
      }
      if (prop === 'currentTime') {
        (_b = this.adapter) === null || _b === void 0 ? void 0 : _b.play();
        (_c = this.adapter) === null || _c === void 0 ? void 0 : _c.setCurrentTime(value);
      }
    }
    writeTask(() => { this[prop] = value; });
  }
  onProviderChange(event) {
    event.stopImmediatePropagation();
    const { by, prop, value } = event.detail;
    if (!isProviderWritableProp(prop)) {
      this.logger.warn(`${by.nodeName} tried to change \`${prop}\` but it is readonly.`);
      return;
    }
    writeTask(() => {
      this.providerCache.set(prop, value);
      this[prop] = value;
    });
  }
  connectedCallback() {
    // Initialize caches.
    const currentState = this.getPlayerState();
    Object.keys(currentState)
      .forEach((prop) => {
      this.cache.set(prop, currentState[prop]);
      this.providerCache.set(prop, initialState[prop]);
    });
    this.autopauseMgr = new Autopause(this.el);
    this.onPausedChange();
    this.onCurrentTimeChange();
    this.onVolumeChange();
    this.onMutedChange();
    this.onDebugChange();
    this.onTranslationsChange();
    this.onLanguageChange(this.language, initialState.language);
    this.fullscreen = new Fullscreen(this.el, (isActive) => {
      this.isFullscreenActive = isActive;
      this.rotateDevice();
    });
    this.disposal.add(onTouchInputChange((isTouch) => { this.isTouch = isTouch; }));
    this.attached = true;
  }
  componentWillLoad() {
    Universe.create(this, this.getPlayerState());
  }
  componentWillRender() {
    return this.playbackReady ? this.flushAdapterCalls() : undefined;
  }
  async componentDidRender() {
    const props = Array.from(this.cache.keys());
    for (let i = 0; i < props.length; i += 1) {
      const prop = props[i];
      const oldValue = this.cache.get(prop);
      const newValue = this[prop];
      if (oldValue !== newValue) {
        this.fireEvent(prop, newValue, oldValue);
        this.cache.set(prop, newValue);
      }
    }
  }
  disconnectedCallback() {
    var _a;
    this.adapterCalls = [];
    this.hasMediaChanged = false;
    (_a = this.textTracksChangeListener) === null || _a === void 0 ? void 0 : _a.call(this);
    this.fullscreen.destroy();
    this.autopauseMgr.destroy();
    this.disposal.empty();
    this.attached = false;
    this.shouldCheckForProviderChange = true;
  }
  async rotateDevice() {
    if (!IS_MOBILE || !canRotateScreen())
      return;
    try {
      if (this.isFullscreenActive) {
        await window.screen.orientation.lock('landscape');
      }
      else {
        await window.screen.orientation.unlock();
      }
    }
    catch (err) {
      this.errors = [err];
    }
  }
  getPlayerState() {
    return Object.keys(initialState)
      .reduce((state, prop) => (Object.assign(Object.assign({}, state), { [prop]: this[prop] })), {});
  }
  calcAspectRatio() {
    const [width, height] = /\d{1,2}:\d{1,2}/.test(this.aspectRatio)
      ? this.aspectRatio.split(':')
      : [16, 9];
    return (100 / Number(width)) * Number(height);
  }
  /**
   * @internal Exposed for E2E testing.
   */
  async callAdapter(method, value) {
    return this.adapter[method](value);
  }
  /**
   * @inheritdoc
   */
  async toggleCaptionsVisibility(isVisible) {
    const isActive = isVisible !== null && isVisible !== void 0 ? isVisible : !this.isCaptionsActive;
    if (isUndefined(this.textTracks)
      || (isActive && isUndefined(this.toggledCaption))
      || (!isActive && isUndefined(this.getActiveCaption())))
      return;
    if (isActive) {
      this.toggledCaption.mode = 'showing';
      this.toggledCaption = undefined;
      return;
    }
    const activeCaption = this.getActiveCaption();
    this.toggledCaption = activeCaption;
    activeCaption.mode = this.hasCustomCaptions() ? 'disabled' : 'hidden';
  }
  hasCustomControls() {
    return !isNull(this.el.querySelector('vime-ui vime-controls'));
  }
  hasCustomCaptions() {
    return !isNull(this.el.querySelector('vime-ui vime-captions'));
  }
  getActiveCaption() {
    var _a;
    return Array.from((_a = this.textTracks) !== null && _a !== void 0 ? _a : [])
      .filter((track) => (track.kind === 'subtitles') || (track.kind === 'captions'))
      .find((track) => track.mode === (this.hasCustomCaptions() ? 'hidden' : 'showing'));
  }
  onActiveCaptionChange() {
    const activeCaption = this.getActiveCaption();
    this.currentCaption = activeCaption || this.toggledCaption;
    this.isCaptionsActive = !isUndefined(activeCaption);
  }
  isAdapterCallRequired(prop, value) {
    return value !== this.providerCache.get(prop);
  }
  async safeAdapterCall(prop, method) {
    if (!this.isAdapterCallRequired(prop, this[prop]))
      return;
    const value = this[prop];
    const safeCall = async (adapter) => {
      var _a;
      // @ts-ignore
      try {
        await ((_a = adapter === null || adapter === void 0 ? void 0 : adapter[method]) === null || _a === void 0 ? void 0 : _a.call(adapter, value));
      }
      catch (e) {
        this.errors = [e];
      }
    };
    if (this.playbackReady) {
      await safeCall(this.adapter);
    }
    else {
      this.adapterCalls.push(safeCall);
    }
  }
  async flushAdapterCalls() {
    if (isUndefined(this.adapter))
      return;
    for (let i = 0; i < this.adapterCalls.length; i += 1) {
      // eslint-disable-next-line no-await-in-loop
      await this.adapterCalls[i](this.adapter);
    }
    this.adapterCalls = [];
  }
  fireEvent(prop, newValue, oldValue) {
    const events = [];
    events.push(new CustomEvent(getEventName(prop), {
      bubbles: false,
      detail: newValue,
    }));
    if ((prop === 'paused') && !newValue) {
      events.push(new CustomEvent('vPlay', { bubbles: false }));
    }
    if ((prop === 'seeking') && oldValue && !newValue) {
      events.push(new CustomEvent('vSeeked', { bubbles: false }));
    }
    events.forEach((event) => { this.el.dispatchEvent(event); });
  }
  genId() {
    var _a;
    const id = (_a = this.el) === null || _a === void 0 ? void 0 : _a.id;
    if (isString(id) && id.length > 0)
      return id;
    idCount$1 += 1;
    return `vime-player-${idCount$1}`;
  }
  render() {
    const label = `${this.isAudioView ? 'Audio Player' : 'Video Player'}`
      + `${!isUndefined(this.mediaTitle) ? ` - ${this.mediaTitle}` : ''}`;
    const canShowCustomUI = !IS_IOS
      || !this.isVideoView
      || (this.playsinline && !this.isFullscreenActive);
    if (!canShowCustomUI) {
      this.controls = true;
    }
    return (h(Host, { id: this.genId(), tabindex: "0", "aria-label": label, "aria-hidden": !this.ready ? 'true' : 'false', "aria-busy": !this.playbackReady ? 'true' : 'false', style: {
        paddingBottom: this.isVideoView ? `${this.calcAspectRatio()}%` : undefined,
      }, class: {
        idle: canShowCustomUI
          && this.hasCustomControls()
          && this.isVideoView
          && !this.paused
          && !this.isControlsActive,
        mobile: this.isMobile,
        touch: this.isTouch,
        audio: this.isAudioView,
        video: this.isVideoView,
        fullscreen: this.isFullscreenActive,
      } }, !this.controls
      && canShowCustomUI
      && this.isVideoView
      && h("div", { class: "blocker" }), h(Universe.Provider, { state: this.getPlayerState() }, h("slot", null))));
  }
  get el() { return this; }
  static get watchers() { return {
    "paused": ["onPausedChange"],
    "duration": ["onDurationChange"],
    "currentTime": ["onCurrentTimeChange"],
    "playbackReady": ["onPlaybackReadyChange"],
    "muted": ["onMutedChange"],
    "playbackRate": ["onPlaybackRateChange"],
    "playbackQuality": ["onPlaybackQualityChange"],
    "debug": ["onDebugChange"],
    "errors": ["onErrorsChange"],
    "textTracks": ["onTextTracksChange"],
    "volume": ["onVolumeChange"],
    "viewType": ["onViewTypeChange"],
    "isAudioView": ["onViewTypeChange"],
    "isVideoView": ["onViewTypeChange"],
    "mediaType": ["onMediaTypeChange"],
    "language": ["onLanguageChange"],
    "translations": ["onTranslationsChange"]
  }; }
  static get style() { return playerCss; }
};

const playgroundCss = "vime-playground{width:100%;height:100%;display:flex;align-items:center;justify-content:center;flex-direction:column}vime-playground .container{width:100%;max-width:960px}vime-playground .buttons,vime-playground .checkboxes{margin-top:24px}vime-playground .buttons>*,vime-playground .checkboxes>*{margin-left:8px}vime-playground .buttons>*:first-child,vime-playground .checkboxes>*:first-child{margin-left:0px}vime-playground .inputs{width:100%;max-width:480px;display:flex;flex-direction:column;margin-top:12px}vime-playground .inputs>input[readonly]{background-color:#f0f0f0}vime-playground .inputs>label{margin-top:16px;margin-bottom:8px}";

const BASE_MEDIA_URL = 'https://media.vimejs.com';
const Playground = class extends HTMLElement {
  constructor() {
    super();
    this.__registerHost();
    /**
     * The current media provider.
     */
    this.provider = Provider.Audio;
    /**
     * Whether to show the custom Vime UI or not.
     */
    this.showCustomUI = false;
    /**
     * The current custom UI theme, won't work if custom UI is turned off.
     */
    this.theme = 'dark';
    /**
     *  The current poster to load.
     */
    this.poster = `${BASE_MEDIA_URL}/poster.png`;
  }
  buildProviderChildren() {
    var _a;
    const defaultSrc = {
      [Provider.Audio]: `${BASE_MEDIA_URL}/audio.mp3`,
      [Provider.Video]: `${BASE_MEDIA_URL}/720p.mp4`,
      [Provider.HLS]: `${BASE_MEDIA_URL}/hls/index.m3u8`,
    };
    const mediaType = {
      [Provider.Audio]: 'audio/mp3',
      [Provider.Video]: 'video/mp4',
      [Provider.HLS]: 'application/x-mpegURL',
    };
    return (h(Fragment, null, h("source", { "data-src": (_a = this.src) !== null && _a !== void 0 ? _a : (this.src = defaultSrc[this.provider]), type: mediaType[this.provider] }), (this.provider !== Provider.HLS) && (h(Fragment, null, h("track", { default: true, kind: "subtitles", src: `${BASE_MEDIA_URL}/subs/english.vtt`, srclang: "en", label: "English" }), h("track", { kind: "subtitles", src: `${BASE_MEDIA_URL}/subs/spanish.vtt`, srclang: "es", label: "Spanish" })))));
  }
  buildProvider() {
    var _a, _b, _c, _d;
    switch (this.provider) {
      case Provider.Audio:
        return (h("vime-audio", { crossOrigin: "" }, this.buildProviderChildren()));
      case Provider.Video:
        return (h("vime-video", { crossOrigin: "", poster: this.poster }, this.buildProviderChildren()));
      case Provider.HLS:
        return (h("vime-hls", { crossOrigin: "", poster: this.poster }, this.buildProviderChildren()));
      case Provider.Dash:
        return (h("vime-dash", { crossOrigin: "", poster: this.poster, src: (_a = this.src) !== null && _a !== void 0 ? _a : (this.src = `${BASE_MEDIA_URL}/mpd/manifest.mpd`) }));
      case Provider.YouTube:
        return (h("vime-youtube", { videoId: (_b = this.src) !== null && _b !== void 0 ? _b : (this.src = 'DyTCOwB0DVw') }));
      case Provider.Vimeo:
        return (h("vime-vimeo", { videoId: (_c = this.src) !== null && _c !== void 0 ? _c : (this.src = '411652396') }));
      case Provider.Dailymotion:
        return (h("vime-dailymotion", { videoId: (_d = this.src) !== null && _d !== void 0 ? _d : (this.src = 'k3b11PemcuTrmWvYe0q') }));
      default:
        return undefined;
    }
  }
  changeProvider(newProvider) {
    this.src = undefined;
    this.provider = newProvider;
  }
  onCustomUiChange(e) {
    this.showCustomUI = e.target.checked;
  }
  onThemeChange(e) {
    this.theme = e.target.checked ? 'light' : 'dark';
  }
  onSrcChange(e) {
    this.src = e.target.value;
  }
  render() {
    const buttons = Object.values(Provider)
      .filter((provider) => provider !== 'faketube')
      .map((provider) => (h("button", { id: "audio", type: "button", onClick: () => this.changeProvider(provider) }, provider)));
    return (h(Host, null, h("div", { class: "container" }, h("vime-player", { controls: !this.showCustomUI, theme: this.theme }, this.buildProvider(), this.showCustomUI && h("vime-default-ui", null))), h("div", { class: "buttons" }, buttons), h("div", { class: "inputs" }, h("label", { htmlFor: "src" }, "Src"), h("input", { type: "text", id: "src", value: this.src, onChange: this.onSrcChange.bind(this) }), h("label", { htmlFor: "poster" }, "Poster"), h("input", { type: "text", id: "poster", value: this.poster, readonly: true }), h("div", { class: "checkboxes" }, h("label", { htmlFor: "ui" }, "Custom UI"), h("input", { type: "checkbox", id: "ui", checked: this.showCustomUI, onChange: this.onCustomUiChange.bind(this) }), h("label", { htmlFor: "theme" }, "Light Theme"), h("input", { type: "checkbox", id: "theme", checked: this.theme === 'light', onChange: this.onThemeChange.bind(this) })))));
  }
  static get style() { return playgroundCss; }
};

const posterCss = "vime-poster{position:absolute;top:0;left:0;width:100%;height:100%;background:#000;z-index:var(--vm-poster-z-index);display:inline-block;pointer-events:none;opacity:0;visibility:hidden;transition:var(--vm-fade-transition)}vime-poster.hidden{display:none}vime-poster.active{opacity:1;visibility:visible}vime-poster img{width:100%;height:100%;pointer-events:none}";

const Poster = class extends HTMLElement {
  constructor() {
    super();
    this.__registerHost();
    this.vLoaded = createEvent(this, "vLoaded", 3);
    this.vWillShow = createEvent(this, "vWillShow", 3);
    this.vWillHide = createEvent(this, "vWillHide", 3);
    this.isHidden = true;
    this.isActive = false;
    this.hasLoaded = false;
    /**
     * How the poster image should be resized to fit the container (sets the `object-fit` property).
     */
    this.fit = 'cover';
    /**
     * @internal
     */
    this.isVideoView = false;
    /**
     * @internal
     */
    this.playbackStarted = false;
    /**
     * @internal
     */
    this.currentTime = 0;
    withPlayerContext(this, [
      'mediaTitle',
      'currentPoster',
      'playbackStarted',
      'currentTime',
      'isVideoView',
    ]);
  }
  onCurrentPosterChange() {
    this.hasLoaded = false;
  }
  connectedCallback() {
    this.lazyLoader = new LazyLoader(this.el, ['data-src'], (el) => {
      const src = el.getAttribute('data-src');
      el.removeAttribute('src');
      if (!isNull(src))
        el.setAttribute('src', src);
    });
    this.onEnabledChange();
    this.onActiveChange();
  }
  disconnectedCallback() {
    this.lazyLoader.destroy();
  }
  onVisibilityChange() {
    (!this.isHidden && this.isActive) ? this.vWillShow.emit() : this.vWillHide.emit();
  }
  onEnabledChange() {
    this.isHidden = !this.isVideoView || isUndefined(this.currentPoster);
    this.onVisibilityChange();
  }
  onActiveChange() {
    this.isActive = !this.playbackStarted || this.currentTime <= 0.1;
    this.onVisibilityChange();
  }
  onPosterLoad() {
    this.vLoaded.emit();
    this.hasLoaded = true;
  }
  render() {
    return (h(Host, { class: {
        hidden: this.isHidden,
        active: this.isActive && this.hasLoaded,
      } }, h("img", { class: "lazy", "data-src": this.currentPoster, alt: !isUndefined(this.mediaTitle) ? `${this.mediaTitle} Poster` : 'Media Poster', style: { objectFit: this.fit }, onLoad: this.onPosterLoad.bind(this) })));
  }
  get el() { return this; }
  static get watchers() { return {
    "currentPoster": ["onCurrentPosterChange", "onEnabledChange"],
    "isVideoView": ["onEnabledChange"],
    "currentTime": ["onActiveChange"],
    "playbackStarted": ["onActiveChange"]
  }; }
  static get style() { return posterCss; }
};

const scrimCss = "vime-scrim{position:absolute;top:0;left:0;width:100%;height:100%;background:var(--vm-scrim-bg);z-index:var(--vm-scrim-z-index);display:inline-block;pointer-events:none;opacity:0;visibility:hidden;transition:var(--vm-fade-transition)}vime-scrim.gradient{height:258px;background:none;background-position:bottom;background-image:url(\"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAECCAYAAAA/9r2TAAABKklEQVQ4T2XI50cFABiF8dvee++67b33uM17b1MkkSSSSBJJJIkkkkQSSSKJ9Efmeb8cr86HH88JBP4thkfEkiKOFPGkSCCNRE8SKZJJkUIaqZ40UqSTIoMUmaSR5ckmRQ4pckkjz5NPigJSFJKiiDSKPSWkKCVFGWmUeypIUUmKKlJUk0aNJ0iKWlLUkUa9p4EUjaRoIkUzabR4WknRRop20ujwdJKiixTdpOghjV5PHyn6STFAGoOeIVIMk2KEFKOkMeYZJ8UEKUKkMemZIsU0KWZIMUsac54wKSKkiJLGvGeBFIukWCLFMrkCq7AG67ABm7AF27ADu7AH+3AAh3AEx3ACp3AG53ABl3AF13ADt3AH9/AAj/AEz/ACr/AG7/ABn/AF3/ADv39LujSyJPVJ0QAAAABJRU5ErkJggg==\")}vime-scrim.gradientUp{top:unset;bottom:0}vime-scrim.gradientDown{transform:rotate(180deg)}vime-scrim.hidden{display:none}vime-scrim.active{opacity:1;visibility:visible}";

const Scrim = class extends HTMLElement {
  constructor() {
    super();
    this.__registerHost();
    /**
     * @internal
     */
    this.isVideoView = false;
    /**
     * @internal
     */
    this.isControlsActive = false;
    withPlayerContext(this, ['isVideoView', 'isControlsActive']);
  }
  render() {
    return (h(Host, { class: {
        gradient: !isUndefined(this.gradient),
        gradientUp: this.gradient === 'up',
        gradientDown: this.gradient === 'down',
        hidden: !this.isVideoView,
        active: this.isControlsActive,
      } }, h("div", null)));
  }
  static get style() { return scrimCss; }
};

const getHours = (value) => Math.trunc((value / 60 / 60) % 60);
const getMinutes = (value) => Math.trunc((value / 60) % 60);
const getSeconds = (value) => Math.trunc(value % 60);
const formatTime = (seconds = 0, alwaysShowHours = false) => {
  // Format time component to add leading zero.
  const format = (value) => `0${value}`.slice(-2);
  const hours = getHours(seconds);
  const mins = getMinutes(seconds);
  const secs = getSeconds(seconds);
  return `${(alwaysShowHours || hours > 0) ? `${hours}:` : ''}${format(mins)}:${format(secs)}`;
};

const scrubberControlCss = "vime-scrubber-control{flex:1;cursor:pointer;position:relative;pointer-events:auto;left:calc(var(--vm-slider-thumb-width) / 2);margin-right:var(--vm-slider-thumb-width);margin-bottom:var(--vm-slider-track-height)}@keyframes progress{to{background-position:var(--vm-scrubber-loading-stripe-size) 0}}vime-scrubber-control vime-slider,vime-scrubber-control progress{margin-left:calc(0px - calc(var(--vm-slider-thumb-width) / 2));margin-right:calc(0px - calc(var(--vm-slider-thumb-width) / 2));width:calc(100% + var(--vm-slider-thumb-width));height:var(--vm-slider-track-height)}vime-scrubber-control vime-slider:hover,vime-scrubber-control progress:hover{cursor:pointer}vime-scrubber-control vime-slider{position:absolute;top:0;left:0;z-index:3}vime-scrubber-control progress{-webkit-appearance:none;background:transparent;border:0;border-radius:100px;position:absolute;left:0;top:50%;padding:0;color:var(--vm-scrubber-buffered-bg);height:var(--vm-slider-track-height)}vime-scrubber-control progress::-webkit-progress-bar{background:transparent}vime-scrubber-control progress::-webkit-progress-value{background:currentColor;border-radius:100px;min-width:var(--vm-slider-track-height);transition:width 0.2s ease}vime-scrubber-control progress::-moz-progress-bar{background:currentColor;border-radius:100px;min-width:var(--vm-slider-track-height);transition:width 0.2s ease}vime-scrubber-control progress::-ms-fill{border-radius:100px;transition:width 0.2s ease}vime-scrubber-control progress.loading{animation:progress 1s linear infinite;background-image:linear-gradient(-45deg, var(--vm-scrubber-loading-stripe-color) 25%, transparent 25%, transparent 50%, var(--vm-scrubber-loading-stripe-color) 50%, var(--vm-scrubber-loading-stripe-color) 75%, transparent 75%, transparent);background-repeat:repeat-x;background-size:var(--vm-scrubber-loading-stripe-size) var(--vm-scrubber-loading-stripe-size);color:transparent;background-color:transparent}";

const ScrubberControl = class extends HTMLElement {
  constructor() {
    super();
    this.__registerHost();
    this.keyboardDisposal = new Disposal();
    this.timestamp = '';
    this.endTime = 0;
    /**
     * Whether the timestamp in the tooltip should show the hours unit, even if the time is less than
     * 1 hour (eg: `20:35` -> `00:20:35`).
     */
    this.alwaysShowHours = false;
    /**
     * Whether the tooltip should not be displayed.
     */
    this.hideTooltip = false;
    /**
     * @internal
     */
    this.currentTime = 0;
    /**
     * @internal
     */
    this.duration = -1;
    /**
     * Prevents seeking forward/backward by using the Left/Right arrow keys.
     */
    this.noKeyboard = false;
    /**
     * @internal
     */
    this.buffering = false;
    /**
     * @internal
     */
    this.buffered = 0;
    /**
     * @internal
     */
    this.i18n = {};
    withPlayerContext(this, [
      'i18n',
      'currentTime',
      'duration',
      'buffering',
      'buffered',
    ]);
  }
  onNoKeyboardChange() {
    this.keyboardDisposal.empty();
    if (this.noKeyboard)
      return;
    const player = findRootPlayer(this);
    const onKeyDown = (event) => {
      if ((event.key !== 'ArrowLeft') && (event.key !== 'ArrowRight'))
        return;
      event.preventDefault();
      const isLeftArrow = (event.key === 'ArrowLeft');
      const seekTo = isLeftArrow
        ? Math.max(0, this.currentTime - 5)
        : Math.min(this.duration, this.currentTime + 5);
      this.dispatch('currentTime', seekTo);
    };
    this.keyboardDisposal.add(listen(player, 'keydown', onKeyDown));
  }
  onDurationChange() {
    // Avoid -1.
    this.endTime = Math.max(0, this.duration);
  }
  connectedCallback() {
    this.dispatch = createDispatcher(this);
    this.timestamp = formatTime(this.currentTime, this.alwaysShowHours);
    this.onNoKeyboardChange();
  }
  disconnectedCallback() {
    this.keyboardDisposal.empty();
  }
  setTooltipPosition(value) {
    const tooltipRect = this.tooltip.getBoundingClientRect();
    const bounds = this.slider.getBoundingClientRect();
    const thumbWidth = parseFloat(window.getComputedStyle(this.slider)
      .getPropertyValue('--vm-slider-thumb-width'));
    const leftLimit = (tooltipRect.width / 2) - (thumbWidth / 2);
    const rightLimit = bounds.width - (tooltipRect.width / 2) - (thumbWidth / 2);
    const xPos = Math.max(leftLimit, Math.min(value, rightLimit));
    this.tooltip.style.left = `${xPos}px`;
  }
  onSeek(event) {
    this.dispatch('currentTime', event.detail);
  }
  onSeeking(event) {
    if (this.duration < 0 || this.tooltip.hidden)
      return;
    if (event.type === 'mouseleave') {
      this.getSliderInput().blur();
      this.tooltip.active = false;
      return;
    }
    const rect = this.el.getBoundingClientRect();
    const percent = Math.max(0, Math.min(100, (100 / rect.width) * (event.pageX - rect.left)));
    this.timestamp = formatTime((this.duration / 100) * percent, this.alwaysShowHours);
    this.setTooltipPosition((percent / 100) * rect.width);
    if (!this.tooltip.active) {
      this.getSliderInput().focus();
      this.tooltip.active = true;
    }
  }
  getSliderInput() {
    return this.slider.querySelector('input');
  }
  render() {
    const sliderValueText = this.i18n.scrubberLabel
      .replace(/{currentTime}/, formatTime(this.currentTime))
      .replace(/{duration}/, formatTime(this.endTime));
    return (h(Host, { onMouseEnter: this.onSeeking.bind(this), onMouseLeave: this.onSeeking.bind(this), onMouseMove: this.onSeeking.bind(this), onTouchMove: () => { this.getSliderInput().focus(); }, onTouchEnd: () => { this.getSliderInput().blur(); } }, h("vime-slider", { step: 0.01, max: this.endTime, value: this.currentTime, label: this.i18n.scrubber, valueText: sliderValueText, onVValueChange: this.onSeek.bind(this), ref: (el) => { this.slider = el; } }), h("progress", { class: {
        loading: this.buffering,
      },
      // @ts-ignore
      min: 0, max: this.endTime, value: this.buffered, "aria-label": this.i18n.buffered, "aria-valuemin": "0", "aria-valuemax": this.endTime, "aria-valuenow": this.buffered, "aria-valuetext": `${((this.endTime > 0) ? (this.buffered / this.endTime) : 0).toFixed(0)}%` }, "% buffered"), h("vime-tooltip", { hidden: this.hideTooltip, ref: (el) => { this.tooltip = el; } }, this.timestamp)));
  }
  get el() { return this; }
  static get watchers() { return {
    "noKeyboard": ["onNoKeyboardChange"],
    "duration": ["onDurationChange"]
  }; }
  static get style() { return scrubberControlCss; }
};

const settingsCss = "vime-settings{position:absolute;opacity:0;pointer-events:auto;overflow-x:hidden;overflow-y:auto;background-color:var(--vm-menu-bg);max-height:var(--vm-settings-max-height);border-radius:var(--vm-settings-border-radius);width:var(--vm-settings-width);padding:var(--vm-settings-padding);box-shadow:var(--vm-settings-shadow);z-index:var(--vm-menu-z-index);scrollbar-width:thin;scroll-behavior:smooth;scrollbar-color:var(--vm-settings-scroll-thumb-color) var(--vm-settings-scroll-track-color);transform:translateY(8px);transition:var(--vm-settings-transition)}vime-settings.hydrated{visibility:hidden !important}vime-settings::-webkit-scrollbar{width:var(--vm-settings-scroll-width)}vime-settings::-webkit-scrollbar-track{background:var(--vm-settings-scroll-track-color)}vime-settings::-webkit-scrollbar-thumb{border-radius:var(--vm-settings-scroll-width);background-color:var(--vm-settings-scroll-thumb-color);border:2px solid var(--vm-menu-bg)}vime-settings>vime-menu[aria-hidden=true]{display:flex !important}vime-settings.active{transform:translateY(0);opacity:1;visibility:visible !important}vime-settings.mobile{position:fixed;top:auto !important;left:0 !important;right:0 !important;bottom:0 !important;width:100%;min-height:56px;max-height:50%;border-radius:0;z-index:2147483647;transform:translateY(100%)}vime-settings.mobile.active{transform:translateY(0)}vime-settings.mobile vime-menu{height:100% !important;overflow:auto !important}";

let idCount$2 = 0;
const Settings = class extends HTMLElement {
  constructor() {
    super();
    this.__registerHost();
    this.disposal = new Disposal();
    /**
     * The height of any lower control bar in pixels so that the settings can re-position itself
     * accordingly.
     */
    this.controlsHeight = 0;
    /**
     * Pins the settings to the defined position inside the video player. This has no effect when
     * the view is of type `audio` (always `bottomRight`) and on mobile devices (always bottom sheet).
     */
    this.pin = 'bottomRight';
    /**
     * Whether the settings menu is opened/closed.
     */
    this.active = false;
    /**
     * @internal
     */
    this.isMobile = false;
    /**
     * @internal
     */
    this.isAudioView = false;
    withPlayerContext(this, ['isMobile', 'isAudioView']);
  }
  onActiveChange() {
    this.dispatch('isSettingsActive', this.active);
    if (isUndefined(this.controller))
      return;
    this.controller.expanded = this.active;
  }
  connectedCallback() {
    this.dispatch = createDispatcher(this);
    idCount$2 += 1;
    this.id = `vime-settings-${idCount$2}`;
  }
  disconnectedCallback() {
    this.disposal.empty();
  }
  /**
   * Sets the controller responsible for opening/closing this settings.
   */
  async setController(id, controller) {
    this.controllerId = id;
    this.controller = controller;
    this.controller.menu = this.id;
    this.disposal.empty();
    this.disposal.add(listen(this.controller, 'click', () => { this.active = !this.active; }));
    this.disposal.add(listen(this.controller, 'keydown', (event) => {
      if (event.key !== 'Enter')
        return;
      // We're looking for !active because the `click` event above will toggle it to active.
      if (!this.active)
        this.menu.focusOnOpen();
    }));
  }
  getPosition() {
    if (this.isAudioView) {
      return {
        right: '0',
        bottom: `${this.controlsHeight}px`,
      };
    }
    // topLeft => { top: 0, left: 0 }
    const pos = this.pin.split(/(?=[L|R])/).map((s) => s.toLowerCase());
    return {
      [pos.includes('top') ? 'top' : 'bottom']: `${this.controlsHeight}px`,
      [pos.includes('left') ? 'left' : 'right']: '8px',
    };
  }
  onClose(event) {
    if (isNull(event.target) || event.target.id !== this.id)
      return;
    this.active = false;
  }
  render() {
    var _a;
    return (h(Host, { style: Object.assign({}, this.getPosition()), class: {
        active: this.active,
        mobile: this.isMobile,
      } }, h("vime-menu", { identifier: this.id, active: this.active, controller: (_a = this.controllerId) !== null && _a !== void 0 ? _a : '', onVClose: this.onClose.bind(this), ref: (el) => { this.menu = el; } }, h("slot", null))));
  }
  get el() { return this; }
  static get watchers() { return {
    "active": ["onActiveChange"]
  }; }
  static get style() { return settingsCss; }
};

const settingsControlCss = "vime-settings-control.hidden{display:none}vime-settings-control svg{transition:transform 0.3s ease}vime-settings-control.active svg{transform:rotate(90deg) !important}";

let idCount$3 = 0;
const SettingsControl = class extends HTMLElement {
  constructor() {
    super();
    this.__registerHost();
    /**
     * The URL to an SVG element or fragment to load.
     */
    this.icon = '#vime-settings';
    /**
     * Whether the tooltip is positioned above/below the control.
     */
    this.tooltipPosition = 'top';
    /**
     * Whether the settings menu this control manages is open.
     */
    this.expanded = false;
    /**
     * @internal
     */
    this.i18n = {};
    withPlayerContext(this, ['i18n']);
  }
  connectedCallback() {
    idCount$3 += 1;
    this.id = `vime-settings-control-${idCount$3}`;
    this.findSettings();
  }
  componentDidLoad() {
    this.findSettings();
  }
  findSettings() {
    var _a;
    const settings = (_a = findUIRoot(this)) === null || _a === void 0 ? void 0 : _a.querySelector('vime-settings');
    settings === null || settings === void 0 ? void 0 : settings.setController(this.id, this.el);
  }
  render() {
    const hasSettings = !isUndefined(this.menu);
    return (h(Host, { class: {
        hidden: !hasSettings,
        active: hasSettings && this.expanded,
      } }, h("vime-control", { identifier: this.id, menu: this.menu, hidden: !hasSettings, expanded: this.expanded, label: this.i18n.settings }, h("vime-icon", { href: this.icon }), h("vime-tooltip", { hidden: this.expanded, position: this.tooltipPosition, direction: this.tooltipDirection }, this.i18n.settings))));
  }
  get el() { return this; }
  static get style() { return settingsControlCss; }
};

const skeletonCss = "vime-skeleton{position:absolute;top:0;left:0;width:100%;height:100%;display:flex;min-height:1rem;z-index:var(--vm-skeleton-z-index)}@keyframes sheen{0%{background-position:200% 0}to{background-position:-200% 0}}vime-skeleton.hidden{opacity:0;visibility:hidden;transition:var(--vm-fade-transition);pointer-events:none}vime-skeleton .indicator{flex:1 1 auto;background:var(--vm-skeleton-color)}vime-skeleton.sheen .indicator{background:linear-gradient(270deg, var(--vm-skeleton-sheen-color), var(--vm-skeleton-color), var(--vm-skeleton-color), var(--vm-skeleton-sheen-color));background-size:400% 100%;background-size:400% 100%;animation:sheen 8s ease-in-out infinite}";

const Skeleton = class extends HTMLElement {
  constructor() {
    super();
    this.__registerHost();
    this.hidden = false;
    /**
     * Determines which effect the skeleton will use.
     * */
    this.effect = 'sheen';
    /**
     * @internal
     */
    this.ready = false;
    withPlayerContext(this, ['ready']);
  }
  onReadyChange() {
    if (!this.ready) {
      this.hidden = false;
    }
    else {
      setTimeout(() => {
        this.hidden = true;
      }, 500);
    }
  }
  render() {
    return (h(Host, { class: {
        hidden: this.hidden,
        sheen: (this.effect === 'sheen'),
      } }, h("div", { class: "indicator" })));
  }
  static get watchers() { return {
    "ready": ["onReadyChange"]
  }; }
  static get style() { return skeletonCss; }
};

const sliderCss = "vime-slider input{-webkit-appearance:none;background:transparent;border:0;outline:0;cursor:pointer;border-radius:calc(var(--vm-slider-thumb-height) * 2);user-select:none;-webkit-user-select:none;touch-action:manipulation;color:var(--vm-slider-value-color);display:block;height:var(--vm-slider-track-height);margin:0;padding:0;transition:box-shadow 0.3s ease;width:100%}vime-slider input::-webkit-slider-runnable-track{background:transparent;border:0;border-radius:calc(var(--vm-slider-track-height) / 2);height:var(--vm-slider-track-height);transition:box-shadow 0.3s ease;user-select:none;background-image:linear-gradient(to right, currentColor var(--vm-value, 0%), transparent var(--vm-value, 0%));background-color:var(--vm-slider-track-color)}vime-slider input::-webkit-slider-thumb{opacity:0;background:var(--vm-slider-thumb-bg);border:0;border-radius:100%;position:relative;transition:all 0.2s ease;width:var(--vm-slider-thumb-width);height:var(--vm-slider-thumb-height);box-shadow:var(--vm-slider-thumb-shadow);-webkit-appearance:none;margin-top:calc(0px - calc(calc(var(--vm-slider-thumb-height) - var(--vm-slider-track-height)) / 2))}vime-slider input::-moz-range-track{background:transparent;border:0;border-radius:calc(var(--vm-slider-track-height) / 2);height:var(--vm-slider-track-height);transition:box-shadow 0.3s ease;user-select:none;background-color:var(--vm-slider-track-color)}vime-slider input::-moz-range-thumb{opacity:0;background:var(--vm-slider-thumb-bg);border:0;border-radius:100%;position:relative;transition:all 0.2s ease;width:var(--vm-slider-thumb-width);height:var(--vm-slider-thumb-height);box-shadow:var(--vm-slider-thumb-shadow)}vime-slider input::-moz-range-progress{background:currentColor;border-radius:calc(var(--vm-slider-track-height) / 2);height:var(--vm-slider-track-height)}vime-slider input::-ms-track{background:transparent;border:0;border-radius:calc(var(--vm-slider-track-height) / 2);height:var(--vm-slider-track-height);transition:box-shadow 0.3s ease;user-select:none;color:transparent;background-color:var(--vm-slider-track-color)}vime-slider input::-ms-fill-upper{background:transparent;border:0;border-radius:calc(var(--vm-slider-track-height) / 2);height:var(--vm-slider-track-height);transition:box-shadow 0.3s ease;user-select:none}vime-slider input::-ms-fill-lower{background:transparent;border:0;border-radius:calc(var(--vm-slider-track-height) / 2);height:var(--vm-slider-track-height);transition:box-shadow 0.3s ease;user-select:none;background:currentColor}vime-slider input::-ms-thumb{opacity:0;background:var(--vm-slider-thumb-bg);border:0;border-radius:100%;position:relative;transition:all 0.2s ease;width:var(--vm-slider-thumb-width);height:var(--vm-slider-thumb-height);box-shadow:var(--vm-slider-thumb-shadow);margin-top:0}vime-slider input::-ms-tooltip{display:none}vime-slider input:hover::-webkit-slider-runnable-track{height:var(--vm-slider-track-focused-height)}vime-slider input:hover::-moz-range-track{height:var(--vm-slider-track-focused-height)}vime-slider input:hover::-ms-track{height:var(--vm-slider-track-focused-height)}vime-slider input:hover::-ms-fill-upper{height:var(--vm-slider-track-focused-height)}vime-slider input:hover::-ms-fill-lower{height:var(--vm-slider-track-focused-height)}vime-slider input:hover::-webkit-slider-thumb{opacity:1}vime-slider input:hover::-moz-range-thumb{opacity:1}vime-slider input:hover::-ms-thumb{opacity:1}vime-slider input:focus{outline:0}vime-slider input:focus::-webkit-slider-runnable-track{outline:0;height:var(--vm-slider-track-focused-height)}vime-slider input:focus::-moz-range-track{outline:0;height:var(--vm-slider-track-focused-height)}vime-slider input:focus::-ms-track{outline:0;height:var(--vm-slider-track-focused-height)}vime-slider input::-moz-focus-outer{border:0}";

const Slider = class extends HTMLElement {
  constructor() {
    super();
    this.__registerHost();
    this.vValueChange = createEvent(this, "vValueChange", 7);
    /**
     * A number that specifies the granularity that the value must adhere to.
     */
    this.step = 1;
    /**
     * The lowest value in the range of permitted values.
     */
    this.min = 0;
    /**
     * The greatest permitted value.
     */
    this.max = 10;
    /**
     * The current value.
     */
    this.value = 5;
  }
  getPercentage() {
    return `${(this.value / this.max) * 100}%`;
  }
  onValueChange(event) {
    var _a;
    const value = parseFloat((_a = event.target) === null || _a === void 0 ? void 0 : _a.value);
    this.vValueChange.emit(value);
  }
  calcTouchedValue(event) {
    const input = event.target;
    const touch = event.changedTouches[0];
    const min = parseFloat(input.getAttribute('min'));
    const max = parseFloat(input.getAttribute('max'));
    const step = parseFloat(input.getAttribute('step'));
    const delta = max - min;
    // Calculate percentage.
    let percent;
    const clientRect = input.getBoundingClientRect();
    const sliderThumbWidth = parseFloat(window.getComputedStyle(this.el).getPropertyValue('--vm-slider-thumb-width'));
    const thumbWidth = ((100 / clientRect.width) * (sliderThumbWidth / 2)) / 100;
    percent = (100 / clientRect.width) * (touch.clientX - clientRect.left);
    // Don't allow outside bounds.
    percent = Math.max(0, Math.min(percent, 100));
    // Factor in the thumb offset.
    if (percent < 50) {
      percent -= (100 - percent * 2) * thumbWidth;
    }
    else if (percent > 50) {
      percent += (percent - 50) * 2 * thumbWidth;
    }
    const position = delta * (percent / 100);
    if (step >= 1) {
      return min + Math.round(position / step) * step;
    }
    /**
     * This part differs from original implementation to save space. Only supports 2 decimal
     * places (0.01) as the step.
     */
    return min + parseFloat(position.toFixed(2));
  }
  /**
   * Basically input[range="type"] on touch devices sucks (particularly iOS), so this helps make it
   * better.
   *
   * @see https://github.com/sampotts/rangetouch
   */
  onTouch(event) {
    const input = event.target;
    if (input.disabled)
      return;
    event.preventDefault();
    this.value = this.calcTouchedValue(event);
    this.vValueChange.emit(this.value);
    input.dispatchEvent(new window.Event((event.type === 'touchend') ? 'change' : 'input', { bubbles: true }));
  }
  render() {
    var _a;
    return (h(Host, { style: {
        '--vm-value': this.getPercentage(),
      } }, h("input", { type: "range", step: this.step, min: this.min, max: this.max, value: this.value, autocomplete: "off", "aria-label": this.label, "aria-valuemin": this.min, "aria-valuemax": this.max, "aria-valuenow": this.value, "aria-valuetext": (_a = this.valueText) !== null && _a !== void 0 ? _a : this.getPercentage(), "aria-orientation": "horizontal", onInput: this.onValueChange.bind(this), onFocus: () => { this.el.dispatchEvent(new window.Event('focus', { bubbles: true })); }, onBlur: () => { this.el.dispatchEvent(new window.Event('blur', { bubbles: true })); }, onTouchStart: this.onTouch.bind(this), onTouchMove: this.onTouch.bind(this), onTouchEnd: this.onTouch.bind(this) })));
  }
  get el() { return this; }
  static get style() { return sliderCss; }
};

const spinnerCss = "vime-spinner{position:absolute;top:0;left:0;width:100%;height:100%;display:flex;justify-content:center;align-items:center;pointer-events:none;z-index:var(--vm-spinner-z-index);opacity:0;visibility:hidden;transition:var(--vm-fade-transition)}vime-spinner.hidden{display:none}vime-spinner.active{opacity:1;visibility:visible}@keyframes spin{0%{transform:rotate(0deg)}100%{transform:rotate(360deg)}}vime-spinner>div{background:transparent;margin:60px auto;font-size:10px;position:relative;text-indent:-9999em;pointer-events:none;border-top:var(--vm-spinner-thickness) solid var(--vm-spinner-fill-color);border-left:var(--vm-spinner-thickness) solid var(--vm-spinner-fill-color);border-right:var(--vm-spinner-thickness) solid var(--vm-spinner-track-color);border-bottom:var(--vm-spinner-thickness) solid var(--vm-spinner-track-color);transform:translateZ(0);animation:spin var(--vm-spinner-spin-duration) infinite var(--vm-spinner-spin-timing-func)}vime-spinner>div,vime-spinner>div::after{border-radius:50%;width:var(--vm-spinner-width);height:var(--vm-spinner-height)}";

const Spinner = class extends HTMLElement {
  constructor() {
    super();
    this.__registerHost();
    this.vWillShow = createEvent(this, "vWillShow", 3);
    this.vWillHide = createEvent(this, "vWillHide", 3);
    this.blacklist = [Provider.YouTube];
    this.isHidden = true;
    this.isActive = false;
    /**
     * @internal
     */
    this.isVideoView = false;
    /**
     * @internal
     */
    this.buffering = false;
    withPlayerContext(this, [
      'isVideoView',
      'buffering',
      'currentProvider',
    ]);
  }
  onVideoViewChange() {
    this.isHidden = !this.isVideoView;
    this.onVisiblityChange();
  }
  onActiveChange() {
    this.isActive = this.buffering;
    this.onVisiblityChange();
  }
  onVisiblityChange() {
    (!this.isHidden && this.isActive) ? this.vWillShow.emit() : this.vWillHide.emit();
  }
  render() {
    return (h(Host, { class: {
        hidden: this.isHidden || this.blacklist.includes(this.currentProvider),
        active: this.isActive,
      } }, h("div", null, "Loading...")));
  }
  static get watchers() { return {
    "isVideoView": ["onVideoViewChange"],
    "buffering": ["onActiveChange"]
  }; }
  static get style() { return spinnerCss; }
};

let idCount$4 = 0;
const Submenu = class extends HTMLElement {
  constructor() {
    super();
    this.__registerHost();
    /**
     * Whether the submenu should be displayed or not.
     */
    this.hidden = false;
    /**
     * Whether the submenu is open/closed.
     */
    this.active = false;
  }
  connectedCallback() {
    this.genId();
  }
  invalidEventTarget(event) {
    return isNull(event.target) || event.target.id !== this.id;
  }
  onOpen(event) {
    if (this.invalidEventTarget(event))
      return;
    this.active = true;
  }
  onClose(event) {
    if (this.invalidEventTarget(event))
      return;
    this.active = false;
  }
  genId() {
    idCount$4 += 1;
    this.id = `vime-submenu-${idCount$4}`;
  }
  getControllerId() {
    return `${this.id}-controller`;
  }
  render() {
    return (h(Host, null, h("vime-menu-item", { identifier: this.getControllerId(), hidden: this.hidden, menu: this.id, label: this.label, hint: this.hint, expanded: this.active }), h("vime-menu", { identifier: this.id, controller: this.getControllerId(), active: this.active, onVOpen: this.onOpen.bind(this), onVClose: this.onClose.bind(this) }, h("slot", null))));
  }
};

const timeCss = "vime-time{display:flex;align-items:center;color:var(--vm-time-color);font-size:var(--vm-time-font-size);font-weight:var(--vm-time-font-weight)}";

const Time = class extends HTMLElement {
  constructor() {
    super();
    this.__registerHost();
    /**
     * The length of time in seconds.
     */
    this.seconds = 0;
    /**
     * Whether the time should always show the hours unit, even if the time is less than
     * 1 hour (eg: `20:35` -> `00:20:35`).
     */
    this.alwaysShowHours = false;
  }
  render() {
    return (h(Host, { "aria-label": this.label }, formatTime(Math.max(0, this.seconds), this.alwaysShowHours)));
  }
  static get style() { return timeCss; }
};

const timeProgressCss = "vime-time-progress{display:flex;align-items:center;color:var(--vm-time-color)}vime-time-progress>span{margin:0 4px}";

const TimeProgress = class extends HTMLElement {
  constructor() {
    super();
    this.__registerHost();
    /**
     * The string used to separate the current time and end time.
     */
    this.separator = '/';
    /**
     * Whether the times should always show the hours unit, even if the time is less than
     * 1 hour (eg: `20:35` -> `00:20:35`).
     */
    this.alwaysShowHours = false;
  }
  render() {
    return (h(Host, null, h("vime-current-time", { alwaysShowHours: this.alwaysShowHours }), h("span", null, this.separator), h("vime-end-time", { alwaysShowHours: this.alwaysShowHours })));
  }
  static get style() { return timeProgressCss; }
};

const tooltipCss = "vime-tooltip{left:50%;transform:translateX(-50%);line-height:1.3;pointer-events:none;position:absolute;opacity:0;white-space:nowrap;visibility:hidden;background:var(--vm-tooltip-bg);border-radius:var(--vm-tooltip-border-radius);box-shadow:var(--vm-tooltip-box-shadow);color:var(--vm-tooltip-color);font-size:var(--vm-tooltip-font-size);padding:var(--vm-tooltip-padding);transition:opacity var(--vm-tooltip-fade-duration) var(--vm-tooltip-fade-timing-func);z-index:var(--vm-tooltip-z-index)}vime-tooltip[aria-hidden=false]{opacity:1;visibility:visible}vime-tooltip.hidden{display:none}vime-tooltip.onTop{bottom:100%;margin-bottom:var(--vm-tooltip-spacing)}vime-tooltip.onBottom{top:100%;margin-top:var(--vm-tooltip-spacing)}vime-tooltip.growLeft{left:auto;right:0;transform:none}vime-tooltip.growRight{left:0;transform:none}";

let tooltipIdCount = 0;
const Tooltip = class extends HTMLElement {
  constructor() {
    super();
    this.__registerHost();
    // Avoid tooltips flashing when player initializing.
    this.hasLoaded = false;
    /**
     * Whether the tooltip is displayed or not.
     */
    this.hidden = false;
    /**
     * Whether the tooltip is visible or not.
     */
    this.active = false;
    /**
     * Determines if the tooltip appears on top/bottom of it's parent.
     */
    this.position = 'top';
    /**
     * @internal
     */
    this.isTouch = false;
    withPlayerContext(this, ['isTouch']);
  }
  componentDidLoad() {
    this.hasLoaded = true;
  }
  getId() {
    // eslint-disable-next-line prefer-destructuring
    const id = this.el.id;
    if (isString(id) && id.length > 0)
      return id;
    tooltipIdCount += 1;
    return `vime-tooltip-${tooltipIdCount}`;
  }
  render() {
    return (h(Host, { id: this.getId(), role: "tooltip", "aria-hidden": (!this.active || this.isTouch) ? 'true' : 'false', class: {
        hidden: !this.hasLoaded || this.hidden,
        onTop: (this.position === 'top'),
        onBottom: (this.position === 'bottom'),
        growLeft: (this.direction === 'left'),
        growRight: (this.direction === 'right'),
      } }, h("slot", null)));
  }
  get el() { return this; }
  static get style() { return tooltipCss; }
};

const uiCss = "vime-ui{pointer-events:none;width:100%;z-index:var(--vm-ui-z-index)}vime-ui.hidden{display:none}vime-ui.video{position:absolute;top:0;left:0;height:100%}";

const UI = class extends HTMLElement {
  constructor() {
    super();
    this.__registerHost();
    /**
     * @internal
     */
    this.isVideoView = false;
    /**
     * @internal
     */
    this.playsinline = false;
    /**
     * @internal
     */
    this.isFullscreenActive = false;
    withPlayerContext(this, [
      'isVideoView',
      'playsinline',
      'isFullscreenActive',
    ]);
  }
  render() {
    const canShowCustomUI = !IS_IOS
      || !this.isVideoView
      || (this.playsinline && !this.isFullscreenActive);
    return (h(Host, { class: {
        hidden: !canShowCustomUI,
        video: this.isVideoView,
      } }, h("div", null, canShowCustomUI && h("slot", null))));
  }
  static get style() { return uiCss; }
};

const Video = class extends HTMLElement {
  constructor() {
    super();
    this.__registerHost();
    /**
     * @internal Whether an external SDK will attach itself to the media player and control it.
     */
    this.willAttach = false;
    /**
     * @inheritdoc
     */
    this.preload = 'metadata';
    if (!this.willAttach)
      withProviderConnect(this);
  }
  /**
   * @internal
   */
  async getAdapter() {
    return this.fileProvider.getAdapter();
  }
  render() {
    return (
    // @ts-ignore
    h("vime-file", { noConnect: true, willAttach: this.willAttach, crossOrigin: this.crossOrigin, poster: this.poster, preload: this.preload, controlsList: this.controlsList, autoPiP: this.autoPiP, disablePiP: this.disablePiP, disableRemotePlayback: this.disableRemotePlayback, mediaTitle: this.mediaTitle, viewType: ViewType.Video, ref: (el) => { this.fileProvider = el; } }, h("slot", null)));
  }
};

/**
 * @see https://developer.vimeo.com/player/sdk/reference#events-for-playback-controls
 */
var VimeoEvent;
(function (VimeoEvent) {
  VimeoEvent["Play"] = "play";
  VimeoEvent["Pause"] = "pause";
  VimeoEvent["Seeking"] = "seeking";
  VimeoEvent["Seeked"] = "seeked";
  VimeoEvent["TimeUpdate"] = "timeupdate";
  VimeoEvent["VolumeChange"] = "volumechange";
  VimeoEvent["DurationChange"] = "durationchange";
  VimeoEvent["FullscreenChange"] = "fullscreenchange";
  VimeoEvent["CueChange"] = "cuechange";
  VimeoEvent["Progress"] = "progress";
  VimeoEvent["Error"] = "error";
  VimeoEvent["PlaybackRateChange"] = "playbackratechange";
  VimeoEvent["Loaded"] = "loaded";
  VimeoEvent["BufferStart"] = "bufferstart";
  VimeoEvent["BufferEnd"] = "bufferend";
  VimeoEvent["TextTrackChange"] = "texttrackchange";
  VimeoEvent["Waiting"] = "waiting";
  VimeoEvent["Ended"] = "ended";
})(VimeoEvent || (VimeoEvent = {}));

const vimeoCss = "vime-vimeo>vime-embed.hideControls{display:block;width:100%;position:relative;z-index:var(--vm-media-z-index)}";

const videoInfoCache$1 = new Map();
const Vimeo = class extends HTMLElement {
  constructor() {
    super();
    this.__registerHost();
    this.vLoadStart = createEvent(this, "vLoadStart", 7);
    this.defaultInternalState = {};
    this.volume = 50;
    this.hasLoaded = false;
    this.internalState = {
      paused: true,
      playing: false,
      seeking: false,
      currentTime: 0,
      buffered: 0,
      playbackStarted: false,
      playRequest: false,
    };
    this.embedSrc = '';
    this.mediaTitle = '';
    /**
     * Whether to display the video owner's name.
     */
    this.byline = true;
    /**
     * Whether to display the video owner's portrait.
     */
    this.portrait = true;
    /**
     * Turns off automatically determining the aspect ratio of the current video.
     */
    this.noAutoAspectRatio = false;
    /**
     * @internal
     */
    this.language = 'en';
    /**
     * @internal
     */
    this.aspectRatio = '16:9';
    /**
     * @internal
     */
    this.autoplay = false;
    /**
     * @internal
     */
    this.controls = false;
    /**
     * @internal
     */
    this.loop = false;
    /**
     * @internal
     */
    this.muted = false;
    /**
     * @internal
     */
    this.playsinline = false;
    withProviderConnect(this);
    withProviderContext(this);
  }
  onVideoIdChange() {
    this.embedSrc = `${this.getOrigin()}/video/${this.videoId}`;
    this.cancelTimeUpdates();
    this.pendingDurationCall = deferredPromise();
    this.pendingMediaTitleCall = deferredPromise();
    this.fetchVideoInfo = this.getVideoInfo();
  }
  onCustomPosterChange() {
    this.dispatch('currentPoster', this.poster);
  }
  connectedCallback() {
    this.dispatch = createProviderDispatcher(this);
    this.dispatch('viewType', ViewType.Video);
    this.onVideoIdChange();
    this.initialMuted = this.muted;
    this.defaultInternalState = Object.assign({}, this.internalState);
  }
  disconnectedCallback() {
    this.cancelTimeUpdates();
    this.pendingPlayRequest = undefined;
  }
  getOrigin() {
    return 'https://player.vimeo.com';
  }
  getPreconnections() {
    return [
      this.getOrigin(),
      'https://i.vimeocdn.com',
      'https://f.vimeocdn.com',
      'https://fresnel.vimeocdn.com',
    ];
  }
  remoteControl(command, arg) {
    return this.embed.postMessage({
      method: command,
      value: arg,
    });
  }
  buildParams() {
    return {
      byline: this.byline,
      color: this.color,
      portrait: this.portrait,
      autopause: false,
      transparent: false,
      autoplay: this.autoplay,
      muted: this.initialMuted,
      playsinline: this.playsinline,
    };
  }
  async getVideoInfo() {
    if (videoInfoCache$1.has(this.videoId))
      return videoInfoCache$1.get(this.videoId);
    return window.fetch(`https://vimeo.com/api/oembed.json?url=${this.embedSrc}`)
      .then((response) => response.json())
      .then((data) => {
      const thumnailRegex = /vimeocdn\.com\/video\/([0-9]+)/;
      const thumbnailId = data === null || data === void 0 ? void 0 : data.thumbnail_url.match(thumnailRegex)[1];
      const poster = `https://i.vimeocdn.com/video/${thumbnailId}_1920x1080.jpg`;
      const info = { poster, width: data === null || data === void 0 ? void 0 : data.width, height: data === null || data === void 0 ? void 0 : data.height };
      videoInfoCache$1.set(this.videoId, info);
      return info;
    });
  }
  onTimeChange(time) {
    if (this.internalState.currentTime === time)
      return;
    this.dispatch('currentTime', time);
    // This is how we detect `seeking` early.
    if (Math.abs(this.internalState.currentTime - time) > 1.5) {
      this.internalState.seeking = true;
      this.dispatch('seeking', true);
      if (this.internalState.playing && (this.internalState.buffered < time)) {
        this.dispatch('buffering', true);
      }
      // Player doesn't resume playback once seeked.
      window.clearTimeout(this.pendingPlayRequest);
      if (!this.internalState.paused) {
        this.internalState.playRequest = true;
      }
      this.remoteControl(this.internalState.playbackStarted
        ? "pause" /* Pause */
        : "play" /* Play */);
    }
    this.internalState.currentTime = time;
  }
  cancelTimeUpdates() {
    if (isNumber(this.timeRAF))
      window.cancelAnimationFrame(this.timeRAF);
  }
  requestTimeUpdates() {
    this.remoteControl("getCurrentTime" /* GetCurrentTime */);
    this.timeRAF = window.requestAnimationFrame(() => { this.requestTimeUpdates(); });
  }
  onSeeked() {
    if (!this.internalState.seeking)
      return;
    this.dispatch('seeking', false);
    this.internalState.seeking = false;
    if (this.internalState.playRequest) {
      window.setTimeout(() => { this.remoteControl("play" /* Play */); }, 150);
    }
  }
  onVimeoMethod(method, arg) {
    var _a, _b;
    switch (method) {
      case "getCurrentTime" /* GetCurrentTime */:
        if (!this.internalState.seeking)
          this.onTimeChange(arg);
        break;
      case "getDuration" /* GetDuration */:
        (_a = this.pendingDurationCall) === null || _a === void 0 ? void 0 : _a.resolve(arg);
        break;
      case "getVideoTitle" /* GetVideoTitle */:
        (_b = this.pendingMediaTitleCall) === null || _b === void 0 ? void 0 : _b.resolve(arg);
        break;
    }
  }
  onLoaded() {
    var _a, _b;
    if (this.hasLoaded)
      return;
    this.pendingPlayRequest = undefined;
    this.internalState = Object.assign({}, this.defaultInternalState);
    this.dispatch('currentSrc', this.embedSrc);
    this.dispatch('mediaType', MediaType.Video);
    this.remoteControl("getDuration" /* GetDuration */);
    this.remoteControl("getVideoTitle" /* GetVideoTitle */);
    Promise.all([
      this.fetchVideoInfo,
      (_a = this.pendingDurationCall) === null || _a === void 0 ? void 0 : _a.promise,
      (_b = this.pendingMediaTitleCall) === null || _b === void 0 ? void 0 : _b.promise,
    ]).then(([info, duration, mediaTitle]) => {
      var _a, _b, _c;
      this.requestTimeUpdates();
      this.dispatch('aspectRatio', `${(_a = info === null || info === void 0 ? void 0 : info.width) !== null && _a !== void 0 ? _a : 16}:${(_b = info === null || info === void 0 ? void 0 : info.height) !== null && _b !== void 0 ? _b : 9}`);
      this.dispatch('currentPoster', (_c = this.poster) !== null && _c !== void 0 ? _c : info === null || info === void 0 ? void 0 : info.poster);
      this.dispatch('duration', duration !== null && duration !== void 0 ? duration : -1);
      this.dispatch('mediaTitle', mediaTitle);
      this.dispatch('playbackReady', true);
      // Re-attempt play.
      if (this.autoplay)
        this.remoteControl("play" /* Play */);
    });
    this.hasLoaded = true;
  }
  onVimeoEvent(event, payload) {
    switch (event) {
      case "ready" /* Ready */:
        Object.values(VimeoEvent).forEach((e) => {
          this.remoteControl("addEventListener" /* AddEventListener */, e);
        });
        break;
      case "loaded" /* Loaded */:
        this.onLoaded();
        break;
      case "play" /* Play */:
        // Incase of autoplay which might skip `Loaded` event.
        this.onLoaded();
        this.internalState.paused = false;
        this.dispatch('paused', false);
        break;
      case "playProgress" /* PlayProgress */:
        if (!this.internalState.playing) {
          this.dispatch('playing', true);
          this.internalState.playing = true;
          this.internalState.playbackStarted = true;
          this.pendingPlayRequest = window.setTimeout(() => {
            this.internalState.playRequest = false;
            this.pendingPlayRequest = undefined;
          }, 1000);
        }
        this.dispatch('buffering', false);
        this.onSeeked();
        break;
      case "pause" /* Pause */:
        this.internalState.paused = true;
        this.internalState.playing = false;
        this.dispatch('paused', true);
        this.dispatch('buffering', false);
        break;
      case "loadProgress" /* LoadProgress */:
        this.internalState.buffered = payload.seconds;
        this.dispatch('buffered', payload.seconds);
        break;
      case "bufferstart" /* BufferStart */:
        this.dispatch('buffering', true);
        // Attempt to detect `play` events early.
        if (this.internalState.paused) {
          this.internalState.paused = false;
          this.dispatch('paused', false);
          this.dispatch('playbackStarted', true);
        }
        break;
      case "bufferend" /* BufferEnd */:
        this.dispatch('buffering', false);
        if (this.internalState.paused)
          this.onSeeked();
        break;
      case "volumechange" /* VolumeChange */:
        if (payload.volume > 0) {
          const newVolume = Math.floor(payload.volume * 100);
          this.dispatch('muted', false);
          if (this.volume !== newVolume) {
            this.volume = newVolume;
            this.dispatch('volume', this.volume);
          }
        }
        else {
          this.dispatch('muted', true);
        }
        break;
      case "durationchange" /* DurationChange */:
        this.dispatch('duration', payload.duration);
        break;
      case "playbackratechange" /* PlaybackRateChange */:
        this.dispatch('playbackRate', payload.playbackRate);
        break;
      case "fullscreenchange" /* FullscreenChange */:
        this.dispatch('isFullscreenActive', payload.fullscreen);
        break;
      case "finish" /* Finish */:
        if (this.loop) {
          this.remoteControl("setCurrentTime" /* SetCurrentTime */, 0);
          setTimeout(() => {
            this.remoteControl("play" /* Play */);
          }, 200);
        }
        else {
          this.dispatch('playbackEnded', true);
        }
        break;
      case "error" /* Error */:
        this.dispatch('errors', [new Error(payload)]);
        break;
    }
  }
  onEmbedSrcChange() {
    this.hasLoaded = false;
    this.vLoadStart.emit();
  }
  onEmbedMessage(event) {
    const message = event.detail;
    if (!isUndefined(message.event))
      this.onVimeoEvent(message.event, message.data);
    if (!isUndefined(message.method))
      this.onVimeoMethod(message.method, message.value);
  }
  adjustPosition() {
    const [aw, ah] = this.aspectRatio.split(':').map((r) => parseInt(r, 10));
    const height = 240;
    const padding = (100 / aw) * ah;
    const offset = (height - padding) / (height / 50);
    return {
      paddingBottom: `${height}%`,
      transform: `translateY(-${offset + 0.02}%)`,
    };
  }
  /**
   * @internal
   */
  async getAdapter() {
    const canPlayRegex = /vimeo(?:\.com|)\/([0-9]{9,})/;
    const fileRegex = /vimeo\.com\/external\/[0-9]+\..+/;
    return {
      getInternalPlayer: async () => this.embed,
      play: async () => { this.remoteControl("play" /* Play */); },
      pause: async () => { this.remoteControl("pause" /* Pause */); },
      canPlay: async (type) => isString(type)
        && !fileRegex.test(type)
        && canPlayRegex.test(type),
      setCurrentTime: async (time) => {
        this.remoteControl("setCurrentTime" /* SetCurrentTime */, time);
      },
      setMuted: async (muted) => {
        if (!muted)
          this.volume = (this.volume > 0) ? this.volume : 30;
        this.remoteControl("setVolume" /* SetVolume */, muted ? 0 : (this.volume / 100));
      },
      setVolume: async (volume) => {
        if (!this.muted) {
          this.remoteControl("setVolume" /* SetVolume */, (volume / 100));
        }
        else {
          // Confirm volume was set.
          this.dispatch('volume', volume);
        }
      },
      // @TODO how to check if Vimeo pro?
      canSetPlaybackRate: async () => false,
      setPlaybackRate: async (rate) => {
        this.remoteControl("setPlaybackRate" /* SetPlaybackRate */, rate);
      },
    };
  }
  render() {
    return (h("vime-embed", { class: { hideControls: !this.controls }, style: this.adjustPosition(), embedSrc: this.embedSrc, mediaTitle: this.mediaTitle, origin: this.getOrigin(), params: this.buildParams(), decoder: decodeJSON, preconnections: this.getPreconnections(), onVEmbedMessage: this.onEmbedMessage.bind(this), onVEmbedSrcChange: this.onEmbedSrcChange.bind(this), ref: (el) => { this.embed = el; } }));
  }
  static get watchers() { return {
    "videoId": ["onVideoIdChange"],
    "poster": ["onCustomPosterChange"]
  }; }
  static get style() { return vimeoCss; }
};

const volumeControlCss = "vime-volume-control{align-items:center;display:flex;position:relative;pointer-events:auto;margin-left:calc(var(--vm-control-spacing) / 2) !important}vime-volume-control vime-slider{width:75px;height:100%;margin:0;max-width:0;position:relative;z-index:3;transition:margin 0.2s cubic-bezier(0.4, 0, 1, 1), max-width 0.2s cubic-bezier(0.4, 0, 1, 1);margin-left:calc(var(--vm-control-spacing) / 2) !important;visibility:hidden}vime-volume-control vime-slider:hover{cursor:pointer}vime-volume-control vime-slider.hidden{display:none}vime-volume-control vime-slider.active{max-width:75px;margin:0 var(--vm-control-spacing)/2;visibility:visible}";

const VolumeControl = class extends HTMLElement {
  constructor() {
    super();
    this.__registerHost();
    this.keyboardDisposal = new Disposal();
    this.prevMuted = false;
    this.currentVolume = 50;
    this.isSliderActive = false;
    /**
     * The URL to an SVG element or fragment.
     */
    this.lowVolumeIcon = '#vime-volume-low';
    /**
     * The URL to an SVG element or fragment.
     */
    this.highVolumeIcon = '#vime-volume-high';
    /**
     * The URL to an SVG element or fragment.
     */
    this.mutedIcon = '#vime-volume-mute';
    /**
     * Whether the tooltip is positioned above/below the control.
     */
    this.tooltipPosition = 'top';
    /**
     * Whether the tooltip should be hidden.
     */
    this.hideTooltip = false;
    /**
     * A pipe (`/`) separated string of JS keyboard keys, that when caught in a `keydown` event, will
     * toggle the muted state of the player.
     */
    this.muteKeys = 'm';
    /**
     * Prevents the volume being changed using the Up/Down arrow keys.
     */
    this.noKeyboard = false;
    /**
     * @internal
     */
    this.muted = false;
    /**
     * @internal
     */
    this.volume = 50;
    /**
     * @internal
     */
    this.isMobile = false;
    /**
     * @internal
     */
    this.i18n = {};
    withPlayerContext(this, [
      'volume',
      'muted',
      'isMobile',
      'i18n',
    ]);
  }
  onNoKeyboardChange() {
    this.keyboardDisposal.empty();
    if (this.noKeyboard)
      return;
    const player = findRootPlayer(this);
    this.keyboardDisposal.add(listen(player, 'keydown', (event) => {
      if ((event.key !== 'ArrowUp') && (event.key !== 'ArrowDown'))
        return;
      const isUpArrow = (event.key === 'ArrowUp');
      const newVolume = isUpArrow ? Math.min(100, this.volume + 5) : Math.max(0, this.volume - 5);
      this.dispatch('volume', parseInt(`${newVolume}`, 10));
    }));
  }
  onPlayerVolumeChange() {
    this.currentVolume = this.muted ? 0 : this.volume;
    if (!this.muted && this.prevMuted && this.volume === 0) {
      this.dispatch('volume', 30);
    }
    this.prevMuted = this.muted;
  }
  connectedCallback() {
    this.prevMuted = this.muted;
    this.dispatch = createDispatcher(this);
    this.onNoKeyboardChange();
  }
  disconnectedCallback() {
    this.keyboardDisposal.empty();
  }
  onShowSlider() {
    clearTimeout(this.hideSliderTimeout);
    this.isSliderActive = true;
  }
  onHideSlider() {
    this.hideSliderTimeout = setTimeout(() => {
      this.isSliderActive = false;
    }, 100);
  }
  onVolumeChange(event) {
    const newVolume = event.detail;
    this.currentVolume = newVolume;
    this.dispatch('volume', newVolume);
    this.dispatch('muted', newVolume === 0);
  }
  onKeyDown(event) {
    if ((event.key !== 'ArrowLeft') && (event.key !== 'ArrowRight'))
      return;
    event.stopPropagation();
  }
  render() {
    return (h(Host, { onMouseEnter: this.onShowSlider.bind(this), onMouseLeave: this.onHideSlider.bind(this) }, h("vime-mute-control", { keys: this.muteKeys, lowVolumeIcon: this.lowVolumeIcon, highVolumeIcon: this.highVolumeIcon, mutedIcon: this.mutedIcon, tooltipPosition: this.tooltipPosition, tooltipDirection: this.tooltipDirection, hideTooltip: this.hideTooltip, onFocus: this.onShowSlider.bind(this), onBlur: this.onHideSlider.bind(this) }), h("vime-slider", { class: {
        hidden: this.isMobile,
        active: this.isSliderActive,
      }, step: 5, max: 100, value: this.currentVolume, label: this.i18n.volume, onKeyDown: this.onKeyDown.bind(this), onFocus: this.onShowSlider.bind(this), onBlur: this.onHideSlider.bind(this), onVValueChange: this.onVolumeChange.bind(this) })));
  }
  static get watchers() { return {
    "noKeyboard": ["onNoKeyboardChange"],
    "muted": ["onPlayerVolumeChange"],
    "volume": ["onPlayerVolumeChange"]
  }; }
  static get style() { return volumeControlCss; }
};

const mapYouTubePlaybackQuality = (quality) => {
  switch (quality) {
    case "unknown" /* Unknown */:
      return undefined;
    case "tiny" /* Tiny */:
      return '144p';
    case "small" /* Small */:
      return '240p';
    case "medium" /* Medium */:
      return '360p';
    case "large" /* Large */:
      return '480p';
    case "hd720" /* Hd720 */:
      return '720p';
    case "hd1080" /* Hd1080 */:
      return '1080p';
    case "highres" /* Highres */:
      return '1440p';
    case "max" /* Max */:
      return '2160p';
    default:
      return undefined;
  }
};

const posterCache = new Map();
const YouTube = class extends HTMLElement {
  constructor() {
    super();
    this.__registerHost();
    this.vLoadStart = createEvent(this, "vLoadStart", 7);
    this.defaultInternalState = {};
    this.hasCued = false;
    this.internalState = {
      paused: true,
      duration: 0,
      seeking: false,
      playbackStarted: false,
      currentTime: 0,
      lastTimeUpdate: 0,
      playbackRate: 1,
      state: -1,
    };
    this.embedSrc = '';
    this.mediaTitle = '';
    /**
     * Whether cookies should be enabled on the embed.
     */
    this.cookies = false;
    /**
     * Whether the fullscreen control should be shown.
     */
    this.showFullscreenControl = true;
    /**
     * @internal
     */
    this.language = 'en';
    /**
     * @internal
     */
    this.autoplay = false;
    /**
     * @internal
     */
    this.controls = false;
    /**
     * @internal
     */
    this.loop = false;
    /**
     * @internal
     */
    this.muted = false;
    /**
     * @internal
     */
    this.playsinline = false;
    withProviderConnect(this);
    withProviderContext(this);
  }
  onVideoIdChange() {
    this.embedSrc = `${this.getOrigin()}/embed/${this.videoId}`;
    this.fetchPosterURL = this.findPosterURL();
  }
  onCustomPosterChange() {
    this.dispatch('currentPoster', this.poster);
  }
  connectedCallback() {
    this.dispatch = createProviderDispatcher(this);
    this.dispatch('viewType', ViewType.Video);
    this.onVideoIdChange();
    this.initialMuted = this.muted;
    this.defaultInternalState = Object.assign({}, this.internalState);
  }
  /**
   * @internal
   */
  async getAdapter() {
    const canPlayRegex = /(?:youtu\.be|youtube|youtube\.com|youtube-nocookie\.com)\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=|)((?:\w|-){11})/;
    return {
      getInternalPlayer: async () => this.embed,
      play: async () => { this.remoteControl("playVideo" /* Play */); },
      pause: async () => { this.remoteControl("pauseVideo" /* Pause */); },
      canPlay: async (type) => isString(type) && canPlayRegex.test(type),
      setCurrentTime: async (time) => { this.remoteControl("seekTo" /* Seek */, time); },
      setMuted: async (muted) => {
        muted ? this.remoteControl("mute" /* Mute */) : this.remoteControl("unMute" /* Unmute */);
      },
      setVolume: async (volume) => {
        this.remoteControl("setVolume" /* SetVolume */, volume);
      },
      canSetPlaybackRate: async () => true,
      setPlaybackRate: async (rate) => {
        this.remoteControl("setPlaybackRate" /* SetPlaybackRate */, rate);
      },
    };
  }
  getOrigin() {
    return !this.cookies ? 'https://www.youtube-nocookie.com' : 'https://www.youtube.com';
  }
  getPreconnections() {
    return [
      this.getOrigin(),
      'https://www.google.com',
      'https://googleads.g.doubleclick.net',
      'https://static.doubleclick.net',
      'https://s.ytimg.com',
      'https://i.ytimg.com',
    ];
  }
  remoteControl(command, arg) {
    return this.embed.postMessage({
      event: 'command',
      func: command,
      args: arg ? [arg] : undefined,
    });
  }
  buildParams() {
    return {
      enablejsapi: 1,
      cc_lang_pref: this.language,
      hl: this.language,
      widget_referrer: window.location.href,
      fs: this.showFullscreenControl ? 1 : 0,
      controls: this.controls ? 1 : 0,
      disablekb: !this.controls ? 1 : 0,
      iv_load_policy: this.controls ? 1 : 3,
      mute: this.initialMuted ? 1 : 0,
      playsinline: this.playsinline ? 1 : 0,
      autoplay: this.autoplay ? 1 : 0,
    };
  }
  onEmbedSrcChange() {
    this.hasCued = false;
    this.vLoadStart.emit();
  }
  onEmbedLoaded() {
    // Seems like we have to wait a random small delay or else YT player isn't ready.
    window.setTimeout(() => this.embed.postMessage({ event: 'listening' }), 100);
  }
  async findPosterURL() {
    if (posterCache.has(this.videoId))
      return posterCache.get(this.videoId);
    const posterURL = (quality) => `https://i.ytimg.com/vi/${this.videoId}/${quality}.jpg`;
    /**
     * We are testing a that the image has a min-width of 121px because if the thumbnail does not
     * exist YouTube returns a blank/error image that is 120px wide.
     */
    return loadImage(posterURL('maxresdefault'), 121) // 1080p (no padding)
      .catch(() => loadImage(posterURL('sddefault'), 121)) // 640p (padded 4:3)
      .catch(() => loadImage(posterURL('hqdefault'), 121)) // 480p (padded 4:3)
      .then((img) => {
      const poster = img.src;
      posterCache.set(this.videoId, poster);
      return poster;
    });
  }
  onCued() {
    if (this.hasCued)
      return;
    this.internalState = Object.assign({}, this.defaultInternalState);
    this.dispatch('currentSrc', this.embedSrc);
    this.dispatch('mediaType', MediaType.Video);
    this.fetchPosterURL.then((poster) => {
      var _a;
      this.dispatch('currentPoster', (_a = this.poster) !== null && _a !== void 0 ? _a : poster);
      this.dispatch('playbackReady', true);
      // Re-attempt play.
      if (this.autoplay)
        this.remoteControl("playVideo" /* Play */);
    });
    this.hasCued = true;
  }
  onPlayerStateChange(state) {
    if (state === -1 /* Unstarted */)
      return;
    const isPlaying = (state === 1 /* Playing */);
    const isBuffering = (state === 3 /* Buffering */);
    this.dispatch('buffering', isBuffering);
    // Attempt to detect `play` events early.
    if (this.internalState.paused && (isBuffering || isPlaying)) {
      this.internalState.paused = false;
      this.dispatch('paused', false);
      if (!this.internalState.playbackStarted) {
        this.dispatch('playbackStarted', true);
        this.internalState.playbackStarted = true;
      }
    }
    switch (state) {
      case 5 /* Cued */:
        this.onCued();
        break;
      case 1 /* Playing */:
        // Incase of autoplay which might skip `Cued` event.
        this.onCued();
        this.dispatch('playing', true);
        break;
      case 2 /* Paused */:
        this.internalState.paused = true;
        this.dispatch('paused', true);
        break;
      case 0 /* Ended */:
        if (this.loop) {
          window.setTimeout(() => { this.remoteControl("playVideo" /* Play */); }, 150);
        }
        else {
          this.dispatch('playbackEnded', true);
          this.internalState.paused = true;
          this.dispatch('paused', true);
        }
        break;
    }
    this.internalState.state = state;
  }
  calcCurrentTime(time) {
    let currentTime = time;
    if (this.internalState.state === 0 /* Ended */) {
      return this.internalState.duration;
    }
    if (this.internalState.state === 1 /* Playing */) {
      const elapsedTime = (Date.now() / 1E3 - this.defaultInternalState.lastTimeUpdate) * this.internalState.playbackRate;
      if (elapsedTime > 0)
        currentTime += Math.min(elapsedTime, 1);
    }
    return currentTime;
  }
  onTimeChange(time) {
    const currentTime = this.calcCurrentTime(time);
    this.dispatch('currentTime', currentTime);
    // This is the only way to detect `seeking`.
    if (Math.abs(this.internalState.currentTime - currentTime) > 1.5) {
      this.internalState.seeking = true;
      this.dispatch('seeking', true);
    }
    this.internalState.currentTime = currentTime;
  }
  onBufferedChange(buffered) {
    this.dispatch('buffered', buffered);
    /**
     * This is the only way to detect `seeked`. Unfortunately while the player is `paused` `seeking`
     * and `seeked` will fire at the same time, there are no updates inbetween -_-. We need an
     * artifical delay between the two events.
     */
    if (this.internalState.seeking && (buffered > this.internalState.currentTime)) {
      window.setTimeout(() => {
        this.internalState.seeking = false;
        this.dispatch('seeking', false);
      }, this.internalState.paused ? 100 : 0);
    }
  }
  onEmbedMessage(event) {
    const message = event.detail;
    const { info } = message;
    if (!info)
      return;
    if (isObject(info.videoData))
      this.dispatch('mediaTitle', info.videoData.title);
    if (isNumber(info.duration)) {
      this.internalState.duration = info.duration;
      this.dispatch('duration', info.duration);
    }
    if (isArray(info.availablePlaybackRates)) {
      this.dispatch('playbackRates', info.availablePlaybackRates);
    }
    if (isNumber(info.playbackRate)) {
      this.internalState.playbackRate = info.playbackRate;
      this.dispatch('playbackRate', info.playbackRate);
    }
    if (isNumber(info.currentTime))
      this.onTimeChange(info.currentTime);
    if (isNumber(info.currentTimeLastUpdated)) {
      this.internalState.lastTimeUpdate = info.currentTimeLastUpdated;
    }
    if (isNumber(info.videoLoadedFraction)) {
      this.onBufferedChange(info.videoLoadedFraction * this.internalState.duration);
    }
    if (isNumber(info.volume))
      this.dispatch('volume', info.volume);
    if (isBoolean(info.muted))
      this.dispatch('muted', info.muted);
    if (isArray(info.availableQualityLevels)) {
      this.dispatch('playbackQualities', info.availableQualityLevels.map((q) => mapYouTubePlaybackQuality(q)));
    }
    if (isString(info.playbackQuality)) {
      this.dispatch('playbackQuality', mapYouTubePlaybackQuality(info.playbackQuality));
    }
    if (isNumber(info.playerState))
      this.onPlayerStateChange(info.playerState);
  }
  render() {
    return (h("vime-embed", { embedSrc: this.embedSrc, mediaTitle: this.mediaTitle, origin: this.getOrigin(), params: this.buildParams(), decoder: decodeJSON, preconnections: this.getPreconnections(), onVEmbedLoaded: this.onEmbedLoaded.bind(this), onVEmbedMessage: this.onEmbedMessage.bind(this), onVEmbedSrcChange: this.onEmbedSrcChange.bind(this), ref: (el) => { this.embed = el; } }));
  }
  static get watchers() { return {
    "cookies": ["onVideoIdChange"],
    "videoId": ["onVideoIdChange"],
    "poster": ["onCustomPosterChange"]
  }; }
};

const VimeAudio = /*@__PURE__*/proxyCustomElement(Audio, [4,"vime-audio",{"willAttach":[4,"will-attach"],"crossOrigin":[1,"cross-origin"],"preload":[1],"disableRemotePlayback":[4,"disable-remote-playback"],"mediaTitle":[1,"media-title"]}]);
const VimeCaptionControl = /*@__PURE__*/proxyCustomElement(CaptionControl, [0,"vime-caption-control",{"showIcon":[1,"show-icon"],"hideIcon":[1,"hide-icon"],"tooltipPosition":[1,"tooltip-position"],"tooltipDirection":[1,"tooltip-direction"],"hideTooltip":[4,"hide-tooltip"],"keys":[1],"currentCaption":[16],"isCaptionsActive":[4,"is-captions-active"],"i18n":[16]}]);
const VimeCaptions = /*@__PURE__*/proxyCustomElement(Captions, [0,"vime-captions",{"hidden":[4],"controlsHeight":[2,"controls-height"],"isControlsActive":[4,"is-controls-active"],"isVideoView":[4,"is-video-view"],"playbackStarted":[4,"playback-started"],"textTracks":[16],"isEnabled":[32],"activeTrack":[32],"activeCues":[32]}]);
const VimeClickToPlay = /*@__PURE__*/proxyCustomElement(ClickToPlay, [0,"vime-click-to-play",{"useOnMobile":[4,"use-on-mobile"],"paused":[4],"isVideoView":[4,"is-video-view"]}]);
const VimeControl = /*@__PURE__*/proxyCustomElement(Control, [4,"vime-control",{"keys":[1],"identifier":[1],"hidden":[4],"label":[1],"menu":[1],"expanded":[4],"pressed":[4],"isTouch":[4,"is-touch"],"describedBy":[32],"showTapHighlight":[32]}]);
const VimeControlGroup = /*@__PURE__*/proxyCustomElement(ControlNewLine, [4,"vime-control-group",{"space":[1]}]);
const VimeControlSpacer = /*@__PURE__*/proxyCustomElement(ControlSpacer, [0,"vime-control-spacer"]);
const VimeControls = /*@__PURE__*/proxyCustomElement(Controls, [4,"vime-controls",{"hidden":[4],"fullWidth":[4,"full-width"],"fullHeight":[4,"full-height"],"direction":[1],"align":[1],"justify":[1],"pin":[513],"activeDuration":[2,"active-duration"],"waitForPlaybackStart":[4,"wait-for-playback-start"],"hideWhenPaused":[4,"hide-when-paused"],"hideOnMouseLeave":[4,"hide-on-mouse-leave"],"isAudioView":[4,"is-audio-view"],"isSettingsActive":[4,"is-settings-active"],"playbackReady":[4,"playback-ready"],"isControlsActive":[4,"is-controls-active"],"paused":[4],"playbackStarted":[4,"playback-started"],"isInteracting":[32]}]);
const VimeCurrentTime = /*@__PURE__*/proxyCustomElement(CurrentTime, [0,"vime-current-time",{"currentTime":[2,"current-time"],"i18n":[16],"alwaysShowHours":[4,"always-show-hours"]}]);
const VimeDailymotion = /*@__PURE__*/proxyCustomElement(Dailymotion, [0,"vime-dailymotion",{"videoId":[1,"video-id"],"shouldAutoplayQueue":[4,"should-autoplay-queue"],"showUpNextQueue":[4,"show-up-next-queue"],"showShareButtons":[4,"show-share-buttons"],"color":[1],"syndication":[1],"showDailymotionLogo":[4,"show-dailymotion-logo"],"showVideoInfo":[4,"show-video-info"],"language":[1],"autoplay":[4],"controls":[4],"poster":[1],"logger":[16],"loop":[4],"muted":[4],"playsinline":[4],"embedSrc":[32],"mediaTitle":[32]}]);
const VimeDash = /*@__PURE__*/proxyCustomElement(Dash, [0,"vime-dash",{"src":[1],"version":[1],"config":[16],"autoplay":[4],"crossOrigin":[1,"cross-origin"],"preload":[1],"poster":[1],"controlsList":[1,"controls-list"],"autoPiP":[4,"auto-pip"],"disablePiP":[4,"disable-pip"],"disableRemotePlayback":[4,"disable-remote-playback"],"mediaTitle":[1,"media-title"],"hasAttached":[32]},[[0,"vMediaElChange","onMediaElChange"]]]);
const VimeDblClickFullscreen = /*@__PURE__*/proxyCustomElement(DblClickFullscreen, [0,"vime-dbl-click-fullscreen",{"useOnMobile":[4,"use-on-mobile"],"isFullscreenActive":[4,"is-fullscreen-active"],"isVideoView":[4,"is-video-view"],"playbackReady":[4,"playback-ready"],"canSetFullscreen":[32]}]);
const VimeDefaultControls = /*@__PURE__*/proxyCustomElement(DefaultControls, [0,"vime-default-controls",{"activeDuration":[2,"active-duration"],"waitForPlaybackStart":[4,"wait-for-playback-start"],"hideWhenPaused":[4,"hide-when-paused"],"hideOnMouseLeave":[4,"hide-on-mouse-leave"],"theme":[1],"isMobile":[4,"is-mobile"],"isLive":[4,"is-live"],"isAudioView":[4,"is-audio-view"],"isVideoView":[4,"is-video-view"]}]);
const VimeDefaultSettings = /*@__PURE__*/proxyCustomElement(DefaultSettings, [4,"vime-default-settings",{"pin":[513],"i18n":[16],"playbackReady":[4,"playback-ready"],"playbackRate":[2,"playback-rate"],"playbackRates":[16],"playbackQuality":[1,"playback-quality"],"playbackQualities":[16],"isCaptionsActive":[4,"is-captions-active"],"currentCaption":[16],"textTracks":[16]}]);
const VimeDefaultUi = /*@__PURE__*/proxyCustomElement(DefaultUI, [4,"vime-default-ui",{"noIcons":[4,"no-icons"],"noClickToPlay":[4,"no-click-to-play"],"noDblClickFullscreen":[4,"no-dbl-click-fullscreen"],"noCaptions":[4,"no-captions"],"noPoster":[4,"no-poster"],"noSpinner":[4,"no-spinner"],"noControls":[4,"no-controls"],"noSettings":[4,"no-settings"],"noSkeleton":[4,"no-skeleton"]}]);
const VimeEmbed = /*@__PURE__*/proxyCustomElement(Embed, [0,"vime-embed",{"embedSrc":[1,"embed-src"],"mediaTitle":[1,"media-title"],"params":[1],"origin":[1],"preconnections":[16],"decoder":[16],"srcWithParams":[32],"hasEnteredViewport":[32]},[[8,"message","onWindowMessage"]]]);
const VimeEndTime = /*@__PURE__*/proxyCustomElement(EndTime, [0,"vime-end-time",{"duration":[2],"i18n":[16],"alwaysShowHours":[4,"always-show-hours"]}]);
const VimeFaketube = /*@__PURE__*/proxyCustomElement(FakeTube, [0,"vime-faketube",{"language":[1],"autoplay":[4],"controls":[4],"logger":[16],"loop":[4],"muted":[4],"playsinline":[4]}]);
const VimeFile = /*@__PURE__*/proxyCustomElement(File, [4,"vime-file",{"willAttach":[4,"will-attach"],"crossOrigin":[1,"cross-origin"],"preload":[1],"poster":[1],"mediaTitle":[1,"media-title"],"controlsList":[1,"controls-list"],"autoPiP":[4,"auto-pip"],"disablePiP":[4,"disable-pip"],"disableRemotePlayback":[4,"disable-remote-playback"],"viewType":[1,"view-type"],"playbackRates":[16],"language":[1],"autoplay":[4],"controls":[4],"logger":[16],"loop":[4],"muted":[4],"playsinline":[4],"noConnect":[4,"no-connect"],"paused":[4],"currentTime":[2,"current-time"],"playbackStarted":[4,"playback-started"]}]);
const VimeFullscreenControl = /*@__PURE__*/proxyCustomElement(FullscreenControl, [0,"vime-fullscreen-control",{"enterIcon":[1,"enter-icon"],"exitIcon":[1,"exit-icon"],"tooltipPosition":[1,"tooltip-position"],"tooltipDirection":[1,"tooltip-direction"],"hideTooltip":[4,"hide-tooltip"],"keys":[1],"isFullscreenActive":[4,"is-fullscreen-active"],"i18n":[16],"playbackReady":[4,"playback-ready"],"canSetFullscreen":[32]}]);
const VimeHls = /*@__PURE__*/proxyCustomElement(HLS, [4,"vime-hls",{"version":[1],"config":[8],"crossOrigin":[1,"cross-origin"],"preload":[1],"poster":[1],"controlsList":[1,"controls-list"],"autoPiP":[4,"auto-pip"],"disablePiP":[4,"disable-pip"],"disableRemotePlayback":[4,"disable-remote-playback"],"mediaTitle":[1,"media-title"],"hasAttached":[32]},[[0,"vMediaElChange","onMediaElChange"],[0,"vSrcSetChange","onSrcChange"]]]);
const VimeIcon = /*@__PURE__*/proxyCustomElement(Icon, [4,"vime-icon",{"href":[1]}]);
const VimeIcons = /*@__PURE__*/proxyCustomElement(Icons, [0,"vime-icons",{"href":[1]}]);
const VimeLiveIndicator = /*@__PURE__*/proxyCustomElement(LiveIndicator, [0,"vime-live-indicator",{"isLive":[4,"is-live"],"i18n":[16]}]);
const VimeMenu = /*@__PURE__*/proxyCustomElement(Menu, [4,"vime-menu",{"active":[1540],"identifier":[1],"controller":[1],"menuItems":[32],"currFocusedMenuItem":[32]},[[0,"vOpen","onSubmenuOpen"],[0,"vClose","onSubmenuClose"],[8,"click","onWindowClick"],[8,"keydown","onWindowKeyDown"]]]);
const VimeMenuItem = /*@__PURE__*/proxyCustomElement(MenuItem, [0,"vime-menu-item",{"identifier":[1],"hidden":[4],"label":[1],"menu":[1],"expanded":[4],"checked":[4],"hint":[1],"badge":[1],"checkedIcon":[1,"checked-icon"],"isTouch":[4,"is-touch"],"showTapHighlight":[32]}]);
const VimeMenuRadio = /*@__PURE__*/proxyCustomElement(MenuRadio, [0,"vime-menu-radio",{"label":[1],"value":[1],"checked":[1028],"badge":[1],"checkedIcon":[1,"checked-icon"]}]);
const VimeMenuRadioGroup = /*@__PURE__*/proxyCustomElement(MenuRadioGroup, [4,"vime-menu-radio-group",{"value":[1025]},[[0,"vCheck","onSelectionChange"]]]);
const VimeMuteControl = /*@__PURE__*/proxyCustomElement(MuteControl, [0,"vime-mute-control",{"lowVolumeIcon":[1,"low-volume-icon"],"highVolumeIcon":[1,"high-volume-icon"],"mutedIcon":[1,"muted-icon"],"tooltipPosition":[1,"tooltip-position"],"tooltipDirection":[1,"tooltip-direction"],"hideTooltip":[4,"hide-tooltip"],"keys":[1],"volume":[2],"muted":[4],"i18n":[16]}]);
const VimePipControl = /*@__PURE__*/proxyCustomElement(PiPControl, [0,"vime-pip-control",{"enterIcon":[1,"enter-icon"],"exitIcon":[1,"exit-icon"],"tooltipPosition":[1,"tooltip-position"],"tooltipDirection":[1,"tooltip-direction"],"hideTooltip":[4,"hide-tooltip"],"keys":[1],"isPiPActive":[4,"is-pi-p-active"],"i18n":[16],"playbackReady":[4,"playback-ready"],"canSetPiP":[32]}]);
const VimePlaybackControl = /*@__PURE__*/proxyCustomElement(PlaybackControl, [0,"vime-playback-control",{"playIcon":[1,"play-icon"],"pauseIcon":[1,"pause-icon"],"tooltipPosition":[1,"tooltip-position"],"tooltipDirection":[1,"tooltip-direction"],"hideTooltip":[4,"hide-tooltip"],"keys":[1],"paused":[4],"i18n":[16]}]);
const VimePlayer = /*@__PURE__*/proxyCustomElement(Player, [4,"vime-player",{"attached":[1028],"logger":[16],"theme":[513],"paused":[1028],"playing":[1028],"duration":[1026],"mediaTitle":[1025,"media-title"],"currentProvider":[1025,"current-provider"],"currentSrc":[1025,"current-src"],"currentPoster":[1025,"current-poster"],"currentTime":[1026,"current-time"],"autoplay":[4],"ready":[1540],"playbackReady":[1028,"playback-ready"],"loop":[4],"muted":[1028],"buffered":[1026],"playbackRate":[1026,"playback-rate"],"playbackRates":[1040],"playbackQuality":[1025,"playback-quality"],"playbackQualities":[1040],"seeking":[1028],"debug":[4],"playbackStarted":[1028,"playback-started"],"playbackEnded":[1028,"playback-ended"],"buffering":[1028],"controls":[4],"isControlsActive":[4,"is-controls-active"],"errors":[1040],"textTracks":[1040],"currentCaption":[1040],"isCaptionsActive":[1028,"is-captions-active"],"isSettingsActive":[1028,"is-settings-active"],"volume":[1026],"isFullscreenActive":[1028,"is-fullscreen-active"],"aspectRatio":[1025,"aspect-ratio"],"viewType":[1025,"view-type"],"isAudioView":[1028,"is-audio-view"],"isVideoView":[1028,"is-video-view"],"mediaType":[1025,"media-type"],"isAudio":[1028,"is-audio"],"isVideo":[1028,"is-video"],"isLive":[1028,"is-live"],"isMobile":[1028,"is-mobile"],"isTouch":[1028,"is-touch"],"isPiPActive":[1028,"is-pi-p-active"],"autopause":[4],"playsinline":[4],"language":[1025],"translations":[1040],"languages":[1040],"i18n":[1040],"shouldCheckForProviderChange":[32]},[[0,"vMediaProviderConnect","onMediaProviderConnect"],[0,"vMediaProviderDisconnect","onMediaProviderDisconnect"],[0,"vLoadStart","onMediaChange"],[0,"vStateChange","onStateChange"],[0,"vProviderChange","onProviderChange"]]]);
const VimePlayground = /*@__PURE__*/proxyCustomElement(Playground, [0,"vime-playground",{"provider":[1025],"src":[1025],"showCustomUI":[1028,"show-custom-u-i"],"theme":[1025],"poster":[1025]}]);
const VimePoster = /*@__PURE__*/proxyCustomElement(Poster, [0,"vime-poster",{"fit":[1],"isVideoView":[4,"is-video-view"],"currentPoster":[1,"current-poster"],"mediaTitle":[1,"media-title"],"playbackStarted":[4,"playback-started"],"currentTime":[2,"current-time"],"isHidden":[32],"isActive":[32],"hasLoaded":[32]}]);
const VimeScrim = /*@__PURE__*/proxyCustomElement(Scrim, [0,"vime-scrim",{"gradient":[1],"isVideoView":[4,"is-video-view"],"isControlsActive":[4,"is-controls-active"]}]);
const VimeScrubberControl = /*@__PURE__*/proxyCustomElement(ScrubberControl, [0,"vime-scrubber-control",{"alwaysShowHours":[4,"always-show-hours"],"hideTooltip":[4,"hide-tooltip"],"currentTime":[2,"current-time"],"duration":[2],"noKeyboard":[4,"no-keyboard"],"buffering":[4],"buffered":[2],"i18n":[16],"timestamp":[32],"endTime":[32]}]);
const VimeSettings = /*@__PURE__*/proxyCustomElement(Settings, [4,"vime-settings",{"controlsHeight":[2,"controls-height"],"pin":[513],"active":[1540],"isMobile":[4,"is-mobile"],"isAudioView":[4,"is-audio-view"],"controllerId":[32]}]);
const VimeSettingsControl = /*@__PURE__*/proxyCustomElement(SettingsControl, [0,"vime-settings-control",{"icon":[1],"tooltipPosition":[1,"tooltip-position"],"tooltipDirection":[1,"tooltip-direction"],"menu":[1],"expanded":[4],"i18n":[16]}]);
const VimeSkeleton = /*@__PURE__*/proxyCustomElement(Skeleton, [0,"vime-skeleton",{"effect":[1],"ready":[4],"hidden":[32]}]);
const VimeSlider = /*@__PURE__*/proxyCustomElement(Slider, [0,"vime-slider",{"step":[2],"min":[2],"max":[2],"value":[2],"valueText":[1,"value-text"],"label":[1]}]);
const VimeSpinner = /*@__PURE__*/proxyCustomElement(Spinner, [0,"vime-spinner",{"isVideoView":[4,"is-video-view"],"currentProvider":[1,"current-provider"],"buffering":[4],"isHidden":[32],"isActive":[32]}]);
const VimeSubmenu = /*@__PURE__*/proxyCustomElement(Submenu, [4,"vime-submenu",{"label":[1],"hidden":[4],"hint":[1],"active":[1540]}]);
const VimeTime = /*@__PURE__*/proxyCustomElement(Time, [0,"vime-time",{"label":[1],"seconds":[2],"alwaysShowHours":[4,"always-show-hours"]}]);
const VimeTimeProgress = /*@__PURE__*/proxyCustomElement(TimeProgress, [0,"vime-time-progress",{"separator":[1],"alwaysShowHours":[4,"always-show-hours"]}]);
const VimeTooltip = /*@__PURE__*/proxyCustomElement(Tooltip, [4,"vime-tooltip",{"hidden":[4],"active":[4],"position":[1],"direction":[1],"isTouch":[4,"is-touch"]}]);
const VimeUi = /*@__PURE__*/proxyCustomElement(UI, [4,"vime-ui",{"isVideoView":[4,"is-video-view"],"playsinline":[4],"isFullscreenActive":[4,"is-fullscreen-active"]}]);
const VimeVideo = /*@__PURE__*/proxyCustomElement(Video, [4,"vime-video",{"willAttach":[4,"will-attach"],"crossOrigin":[1,"cross-origin"],"preload":[1],"poster":[1],"controlsList":[1,"controls-list"],"autoPiP":[4,"auto-pip"],"disablePiP":[4,"disable-pip"],"disableRemotePlayback":[4,"disable-remote-playback"],"mediaTitle":[1,"media-title"]}]);
const VimeVimeo = /*@__PURE__*/proxyCustomElement(Vimeo, [0,"vime-vimeo",{"videoId":[1,"video-id"],"byline":[4],"color":[1],"portrait":[4],"noAutoAspectRatio":[4,"no-auto-aspect-ratio"],"poster":[1],"language":[1],"aspectRatio":[1,"aspect-ratio"],"autoplay":[4],"controls":[4],"logger":[16],"loop":[4],"muted":[4],"playsinline":[4],"embedSrc":[32],"mediaTitle":[32]}]);
const VimeVolumeControl = /*@__PURE__*/proxyCustomElement(VolumeControl, [0,"vime-volume-control",{"lowVolumeIcon":[1,"low-volume-icon"],"highVolumeIcon":[1,"high-volume-icon"],"mutedIcon":[1,"muted-icon"],"tooltipPosition":[1,"tooltip-position"],"tooltipDirection":[1,"tooltip-direction"],"hideTooltip":[4,"hide-tooltip"],"muteKeys":[1,"mute-keys"],"noKeyboard":[4,"no-keyboard"],"muted":[4],"volume":[2],"isMobile":[4,"is-mobile"],"i18n":[16],"currentVolume":[32],"isSliderActive":[32]}]);
const VimeYoutube = /*@__PURE__*/proxyCustomElement(YouTube, [0,"vime-youtube",{"cookies":[4],"videoId":[1,"video-id"],"showFullscreenControl":[4,"show-fullscreen-control"],"poster":[1],"language":[1],"autoplay":[4],"controls":[4],"logger":[16],"loop":[4],"muted":[4],"playsinline":[4],"embedSrc":[32],"mediaTitle":[32]}]);
const defineCustomElements = (opts) => {
  if (typeof customElements !== 'undefined') {
    [
      VimeAudio,
  VimeCaptionControl,
  VimeCaptions,
  VimeClickToPlay,
  VimeControl,
  VimeControlGroup,
  VimeControlSpacer,
  VimeControls,
  VimeCurrentTime,
  VimeDailymotion,
  VimeDash,
  VimeDblClickFullscreen,
  VimeDefaultControls,
  VimeDefaultSettings,
  VimeDefaultUi,
  VimeEmbed,
  VimeEndTime,
  VimeFaketube,
  VimeFile,
  VimeFullscreenControl,
  VimeHls,
  VimeIcon,
  VimeIcons,
  VimeLiveIndicator,
  VimeMenu,
  VimeMenuItem,
  VimeMenuRadio,
  VimeMenuRadioGroup,
  VimeMuteControl,
  VimePipControl,
  VimePlaybackControl,
  VimePlayer,
  VimePlayground,
  VimePoster,
  VimeScrim,
  VimeScrubberControl,
  VimeSettings,
  VimeSettingsControl,
  VimeSkeleton,
  VimeSlider,
  VimeSpinner,
  VimeSubmenu,
  VimeTime,
  VimeTimeProgress,
  VimeTooltip,
  VimeUi,
  VimeVideo,
  VimeVimeo,
  VimeVolumeControl,
  VimeYoutube
    ].forEach(cmp => {
      if (!customElements.get(cmp.is)) {
        customElements.define(cmp.is, cmp, opts);
      }
    });
  }
};

export { MediaType, Provider, ViewType, VimeAudio, VimeCaptionControl, VimeCaptions, VimeClickToPlay, VimeControl, VimeControlGroup, VimeControlSpacer, VimeControls, VimeCurrentTime, VimeDailymotion, VimeDash, VimeDblClickFullscreen, VimeDefaultControls, VimeDefaultSettings, VimeDefaultUi, VimeEmbed, VimeEndTime, VimeFaketube, VimeFile, VimeFullscreenControl, VimeHls, VimeIcon, VimeIcons, VimeLiveIndicator, VimeMenu, VimeMenuItem, VimeMenuRadio, VimeMenuRadioGroup, VimeMuteControl, VimePipControl, VimePlaybackControl, VimePlayer, VimePlayground, VimePoster, VimeScrim, VimeScrubberControl, VimeSettings, VimeSettingsControl, VimeSkeleton, VimeSlider, VimeSpinner, VimeSubmenu, VimeTime, VimeTimeProgress, VimeTooltip, VimeUi, VimeVideo, VimeVimeo, VimeVolumeControl, VimeYoutube, createDispatcher, defineCustomElements, findRootPlayer, initialState, isWritableProp, loadSprite, usePlayerContext, withPlayerContext };
