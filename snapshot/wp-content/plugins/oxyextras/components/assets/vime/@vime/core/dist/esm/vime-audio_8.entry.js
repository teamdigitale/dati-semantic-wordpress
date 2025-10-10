import { g as getElement, a as getRenderingRef, r as registerInstance, h, c as createEvent, w as writeTask, H as Host } from './index-e4fee97f.js';
import { e as en, i as initialState, s as shouldPropResetOnMediaChange, a as isWritableProp, b as isProviderWritableProp } from './PlayerProps-825b3f63.js';
import { d as isString, f as isUndefined, c as isNullOrUndefined, l as listen, a as isNull, h as isNumber, b as isObject, e as isArray, j as isBoolean } from './dom-888fcf0c.js';
import { w as withPlayerContext, g as getEventName } from './PlayerContext-da67ca53.js';
import { V as ViewType, M as MediaType } from './MediaType-8ac3bdc6.js';
import { c as canPlayHLSNatively, a as IS_MOBILE, b as canAutoplay, o as onTouchInputChange, d as canRotateScreen, e as IS_IOS } from './support-c9ac4820.js';
import { d as decodeQueryString, a as loadSDK, b as decodeJSON, c as loadImage } from './network-3c30465e.js';
import { P as Provider } from './Provider-99c71269.js';
import { a as withProviderConnect, w as withProviderContext, c as createProviderDispatcher } from './ProviderDispatcher-9bc874bc.js';
import { a as audioRegex, d as dashRegex, h as hlsRegex, b as hlsTypeRegex } from './utils-7dc44688.js';
import { D as Disposal } from './Disposal-525363e0.js';

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

const Audio = class {
  constructor(hostRef) {
    registerInstance(this, hostRef);
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

const videoInfoCache = new Map();
const Dailymotion = class {
  constructor(hostRef) {
    registerInstance(this, hostRef);
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

const Dash = class {
  constructor(hostRef) {
    registerInstance(this, hostRef);
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

const DefaultUI = class {
  constructor(hostRef) {
    registerInstance(this, hostRef);
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

const HLS = class {
  constructor(hostRef) {
    registerInstance(this, hostRef);
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

let idCount = 0;
const immediateAdapterCall = new Set(['currentTime', 'paused']);
const Player = class {
  constructor(hostRef) {
    registerInstance(this, hostRef);
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
    idCount += 1;
    return `vime-player-${idCount}`;
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
  get el() { return getElement(this); }
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
};
Player.style = playerCss;

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
const Vimeo = class {
  constructor(hostRef) {
    registerInstance(this, hostRef);
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
};
Vimeo.style = vimeoCss;

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
const YouTube = class {
  constructor(hostRef) {
    registerInstance(this, hostRef);
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

export { Audio as vime_audio, Dailymotion as vime_dailymotion, Dash as vime_dash, DefaultUI as vime_default_ui, HLS as vime_hls, Player as vime_player, Vimeo as vime_vimeo, YouTube as vime_youtube };
