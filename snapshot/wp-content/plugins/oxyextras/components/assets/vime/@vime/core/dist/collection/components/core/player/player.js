import { Component, Element, Event, getElement, h, Host, Listen, Method, Prop, State, Watch, writeTask, } from '@stencil/core';
import { Universe } from 'stencil-wormhole';
import { MediaType } from './MediaType';
import { Provider } from '../../providers/Provider';
import { isUndefined, isString, isNull } from '../../../utils/unit';
import { initialState, isProviderWritableProp, isWritableProp, shouldPropResetOnMediaChange, } from './PlayerProps';
import { ViewType } from './ViewType';
import { canAutoplay, IS_MOBILE, onTouchInputChange, IS_IOS, canRotateScreen, } from '../../../utils/support';
import { Fullscreen } from './fullscreen/Fullscreen';
import { en } from './lang/en';
import { Disposal } from './Disposal';
import { listen } from '../../../utils/dom';
import { Autopause } from './Autopause';
import { Logger } from './PlayerLogger';
import { getEventName } from './PlayerEvents';
let idCount = 0;
const immediateAdapterCall = new Set(['currentTime', 'paused']);
/**
 * @slot - Used to pass in providers, plugins and UI components.
 */
export class Player {
  constructor() {
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
      } },
      !this.controls
        && canShowCustomUI
        && this.isVideoView
        && h("div", { class: "blocker" }),
      h(Universe.Provider, { state: this.getPlayerState() },
        h("slot", null))));
  }
  static get is() { return "vime-player"; }
  static get originalStyleUrls() { return {
    "$": ["player.scss"]
  }; }
  static get styleUrls() { return {
    "$": ["player.css"]
  }; }
  static get properties() { return {
    "attached": {
      "type": "boolean",
      "mutable": true,
      "complexType": {
        "original": "boolean",
        "resolved": "boolean",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "`@readonly` Whether the player is attached to the DOM."
      },
      "attribute": "attached",
      "reflect": false,
      "defaultValue": "false"
    },
    "logger": {
      "type": "unknown",
      "mutable": false,
      "complexType": {
        "original": "Logger",
        "resolved": "Logger",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "internal"
          }],
        "text": ""
      },
      "defaultValue": "new Logger()"
    },
    "theme": {
      "type": "string",
      "mutable": false,
      "complexType": {
        "original": "string",
        "resolved": "string | undefined",
        "references": {}
      },
      "required": false,
      "optional": true,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "This property has no role other than scoping CSS selectors."
      },
      "attribute": "theme",
      "reflect": true
    },
    "paused": {
      "type": "boolean",
      "mutable": true,
      "complexType": {
        "original": "boolean",
        "resolved": "boolean",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "Whether playback should be paused. Defaults to `true` if no media has loaded or playback has\nnot started. Setting this to `true` will begin/resume playback."
      },
      "attribute": "paused",
      "reflect": false,
      "defaultValue": "true"
    },
    "playing": {
      "type": "boolean",
      "mutable": true,
      "complexType": {
        "original": "boolean",
        "resolved": "boolean",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "`@readonly` Whether media is actively playing back. Defaults to `false` if no media has\nloaded or playback has not started."
      },
      "attribute": "playing",
      "reflect": false,
      "defaultValue": "false"
    },
    "duration": {
      "type": "number",
      "mutable": true,
      "complexType": {
        "original": "number",
        "resolved": "number",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "`@readonly` A `double` indicating the total playback length of the media in seconds. Defaults\nto `-1` if no media has been loaded. If the media is being streamed live then the duration is\nequal to `Infinity`."
      },
      "attribute": "duration",
      "reflect": false,
      "defaultValue": "-1"
    },
    "mediaTitle": {
      "type": "string",
      "mutable": true,
      "complexType": {
        "original": "string",
        "resolved": "string | undefined",
        "references": {}
      },
      "required": false,
      "optional": true,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "`@readonly` The title of the current media. Defaults to `undefined` if no media has been\nloaded."
      },
      "attribute": "media-title",
      "reflect": false
    },
    "currentProvider": {
      "type": "string",
      "mutable": true,
      "complexType": {
        "original": "Provider",
        "resolved": "Provider.Audio | Provider.Dailymotion | Provider.Dash | Provider.FakeTube | Provider.HLS | Provider.Video | Provider.Vimeo | Provider.YouTube | undefined",
        "references": {
          "Provider": {
            "location": "import",
            "path": "../../providers/Provider"
          }
        }
      },
      "required": false,
      "optional": true,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "`@readonly` The current provider name whose responsible for loading and playing media.\nDefaults to `undefined` when no provider has been loaded."
      },
      "attribute": "current-provider",
      "reflect": false
    },
    "currentSrc": {
      "type": "string",
      "mutable": true,
      "complexType": {
        "original": "string",
        "resolved": "string | undefined",
        "references": {}
      },
      "required": false,
      "optional": true,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "`@readonly` The absolute URL of the media resource that has been chosen. Defaults to\n`undefined` if no media has been loaded."
      },
      "attribute": "current-src",
      "reflect": false
    },
    "currentPoster": {
      "type": "string",
      "mutable": true,
      "complexType": {
        "original": "string",
        "resolved": "string | undefined",
        "references": {}
      },
      "required": false,
      "optional": true,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "`@readonly` The absolute URL of the poster for the current media resource. Defaults to\n`undefined` if no media/poster has been loaded."
      },
      "attribute": "current-poster",
      "reflect": false
    },
    "currentTime": {
      "type": "number",
      "mutable": true,
      "complexType": {
        "original": "number",
        "resolved": "number",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "A `double` indicating the current playback time in seconds. Defaults to `0` if the media has\nnot started to play and has not seeked. Setting this value seeks the media to the new\ntime. The value can be set to a minimum of `0` and maximum of the total length of the\nmedia (indicated by the duration prop)."
      },
      "attribute": "current-time",
      "reflect": false,
      "defaultValue": "0"
    },
    "autoplay": {
      "type": "boolean",
      "mutable": false,
      "complexType": {
        "original": "boolean",
        "resolved": "boolean",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "Whether playback should automatically begin playing once the media is ready to do so. This\nwill only work if the browsers `autoplay` policies have been satisfied. It'll generally work\nif the player is muted, or the user frequently interacts with your site. You can check\nif it's possible to autoplay via the `canAutoplay()` or `canMutedAutoplay()` methods.\nDepending on the provider, changing this prop may cause the player to completely reset."
      },
      "attribute": "autoplay",
      "reflect": false,
      "defaultValue": "false"
    },
    "ready": {
      "type": "boolean",
      "mutable": true,
      "complexType": {
        "original": "boolean",
        "resolved": "boolean",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "`@readonly` Whether the player has loaded and is ready to be interacted with."
      },
      "attribute": "ready",
      "reflect": true,
      "defaultValue": "false"
    },
    "playbackReady": {
      "type": "boolean",
      "mutable": true,
      "complexType": {
        "original": "boolean",
        "resolved": "boolean",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "`@readonly` Whether media is ready for playback to begin."
      },
      "attribute": "playback-ready",
      "reflect": false,
      "defaultValue": "false"
    },
    "loop": {
      "type": "boolean",
      "mutable": false,
      "complexType": {
        "original": "boolean",
        "resolved": "boolean",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "Whether media should automatically start playing from the beginning every time it ends."
      },
      "attribute": "loop",
      "reflect": false,
      "defaultValue": "false"
    },
    "muted": {
      "type": "boolean",
      "mutable": true,
      "complexType": {
        "original": "boolean",
        "resolved": "boolean",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "Whether the audio is muted or not."
      },
      "attribute": "muted",
      "reflect": false,
      "defaultValue": "false"
    },
    "buffered": {
      "type": "number",
      "mutable": true,
      "complexType": {
        "original": "number",
        "resolved": "number",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "`@readonly` The length of the media in seconds that has been downloaded by the browser."
      },
      "attribute": "buffered",
      "reflect": false,
      "defaultValue": "0"
    },
    "playbackRate": {
      "type": "number",
      "mutable": true,
      "complexType": {
        "original": "number",
        "resolved": "number",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "A `double` indicating the rate at which media is being played back. If the value is `<1` then\nplayback is slowed down; if `>1` then playback is sped up. Defaults to `1`. The playback rate\ncan only be set to a rate found in the `playbackRates` prop. Some providers may not\nallow changing the playback rate, you can check if it's possible via `canSetPlaybackRate()`."
      },
      "attribute": "playback-rate",
      "reflect": false,
      "defaultValue": "1"
    },
    "playbackRates": {
      "type": "unknown",
      "mutable": true,
      "complexType": {
        "original": "number[]",
        "resolved": "number[]",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "`@readonly` The playback rates available for the current media."
      },
      "defaultValue": "[1]"
    },
    "playbackQuality": {
      "type": "string",
      "mutable": true,
      "complexType": {
        "original": "string",
        "resolved": "string | undefined",
        "references": {}
      },
      "required": false,
      "optional": true,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "Indicates the quality of the media. The value will differ between audio and video. For audio\nthis might be some combination of the encoding format (AAC, MP3), bitrate in kilobits per\nsecond (kbps) and sample rate in kilohertz (kHZ). For video this will be the number of vertical\npixels it supports. For example, if the video has a resolution of `1920x1080` then the quality\nwill return `1080p`. Defaults to `undefined` which you can interpret as the quality is unknown.\nThe quality can only be set to a quality found in the `playbackQualities` prop. Some providers\nmay not allow changing the quality, you can check if it's possible via\n`canSetPlaybackQuality()`."
      },
      "attribute": "playback-quality",
      "reflect": false
    },
    "playbackQualities": {
      "type": "unknown",
      "mutable": true,
      "complexType": {
        "original": "string[]",
        "resolved": "string[]",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "`@readonly` The media qualities available for the current media."
      },
      "defaultValue": "[]"
    },
    "seeking": {
      "type": "boolean",
      "mutable": true,
      "complexType": {
        "original": "boolean",
        "resolved": "boolean",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "`@readonly` Whether the player is in the process of seeking to a new time position."
      },
      "attribute": "seeking",
      "reflect": false,
      "defaultValue": "false"
    },
    "debug": {
      "type": "boolean",
      "mutable": false,
      "complexType": {
        "original": "boolean",
        "resolved": "boolean",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "`@readonly` Whether the player is in debug mode and should `console.x` information about\nits internal state."
      },
      "attribute": "debug",
      "reflect": false,
      "defaultValue": "false"
    },
    "playbackStarted": {
      "type": "boolean",
      "mutable": true,
      "complexType": {
        "original": "boolean",
        "resolved": "boolean",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "`@readonly` Whether the media has initiated playback. In other words it will be true if\n`currentTime > 0`."
      },
      "attribute": "playback-started",
      "reflect": false,
      "defaultValue": "false"
    },
    "playbackEnded": {
      "type": "boolean",
      "mutable": true,
      "complexType": {
        "original": "boolean",
        "resolved": "boolean",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "`@readonly` Whether media playback has reached the end. In other words it'll be true if\n`currentTime === duration`."
      },
      "attribute": "playback-ended",
      "reflect": false,
      "defaultValue": "false"
    },
    "buffering": {
      "type": "boolean",
      "mutable": true,
      "complexType": {
        "original": "boolean",
        "resolved": "boolean",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "`@readonly` Whether playback has temporarily stopped because of a lack of temporary data."
      },
      "attribute": "buffering",
      "reflect": false,
      "defaultValue": "false"
    },
    "controls": {
      "type": "boolean",
      "mutable": false,
      "complexType": {
        "original": "boolean",
        "resolved": "boolean",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "Indicates whether a user interface should be shown for controlling the resource. Set this to\n`false` when you want to provide your own custom controls, and `true` if you want the current\nprovider to supply its own default controls. Depending on the provider, changing this prop\nmay cause the player to completely reset."
      },
      "attribute": "controls",
      "reflect": false,
      "defaultValue": "false"
    },
    "isControlsActive": {
      "type": "boolean",
      "mutable": false,
      "complexType": {
        "original": "boolean",
        "resolved": "boolean",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "Whether the controls are currently visible. This is currently only supported by custom\ncontrols."
      },
      "attribute": "is-controls-active",
      "reflect": false,
      "defaultValue": "false"
    },
    "errors": {
      "type": "unknown",
      "mutable": true,
      "complexType": {
        "original": "any[]",
        "resolved": "any[]",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "`@readonly` A collection of errors that have occurred ordered by `[oldest, ..., newest]`."
      },
      "defaultValue": "[]"
    },
    "textTracks": {
      "type": "unknown",
      "mutable": true,
      "complexType": {
        "original": "TextTrackList",
        "resolved": "TextTrackList | undefined",
        "references": {
          "TextTrackList": {
            "location": "global"
          }
        }
      },
      "required": false,
      "optional": true,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "`@readonly` The text tracks (WebVTT) associated with the current media."
      }
    },
    "currentCaption": {
      "type": "unknown",
      "mutable": true,
      "complexType": {
        "original": "TextTrack",
        "resolved": "TextTrack | undefined",
        "references": {
          "TextTrack": {
            "location": "global"
          }
        }
      },
      "required": false,
      "optional": true,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "`@readonly` The selected caption/subtitle text track to display. Defaults to `undefined` if\nthere is none. This does not mean this track is active, only that is the current selection. To\nknow if it is active, check the `isCaptionsActive` prop."
      }
    },
    "isCaptionsActive": {
      "type": "boolean",
      "mutable": true,
      "complexType": {
        "original": "boolean",
        "resolved": "boolean",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "`@readonly` Whether any captions or subtitles are currently showing."
      },
      "attribute": "is-captions-active",
      "reflect": false,
      "defaultValue": "false"
    },
    "isSettingsActive": {
      "type": "boolean",
      "mutable": true,
      "complexType": {
        "original": "boolean",
        "resolved": "boolean",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "`@readonly` Whether the settings menu has been opened and is currently visible. This is\ncurrently only supported by custom settings."
      },
      "attribute": "is-settings-active",
      "reflect": false,
      "defaultValue": "false"
    },
    "volume": {
      "type": "number",
      "mutable": true,
      "complexType": {
        "original": "number",
        "resolved": "number",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "An `int` between `0` (silent) and `100` (loudest) indicating the audio volume."
      },
      "attribute": "volume",
      "reflect": false,
      "defaultValue": "50"
    },
    "isFullscreenActive": {
      "type": "boolean",
      "mutable": true,
      "complexType": {
        "original": "boolean",
        "resolved": "boolean",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "`@readonly` Whether the player is currently in fullscreen mode."
      },
      "attribute": "is-fullscreen-active",
      "reflect": false,
      "defaultValue": "false"
    },
    "aspectRatio": {
      "type": "string",
      "mutable": true,
      "complexType": {
        "original": "string",
        "resolved": "string",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "The aspect ratio of the player expressed as `width:height` (`16:9`). This is only applied if\nthe `viewType` is `video` and the player is not in fullscreen mode."
      },
      "attribute": "aspect-ratio",
      "reflect": false,
      "defaultValue": "'16:9'"
    },
    "viewType": {
      "type": "string",
      "mutable": true,
      "complexType": {
        "original": "ViewType",
        "resolved": "ViewType.Audio | ViewType.Video | undefined",
        "references": {
          "ViewType": {
            "location": "import",
            "path": "./ViewType"
          }
        }
      },
      "required": false,
      "optional": true,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "`@readonly` The type of player view that is being used, whether it's an audio player view or\nvideo player view. Normally if the media type is of audio then the view is of type audio, but\nin some cases it might be desirable to show a different view type. For example, when playing\naudio with a poster. This is subject to the provider allowing it. Defaults to `undefined`\nwhen no media has been loaded."
      },
      "attribute": "view-type",
      "reflect": false
    },
    "isAudioView": {
      "type": "boolean",
      "mutable": true,
      "complexType": {
        "original": "boolean",
        "resolved": "boolean",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "`@readonly` Whether the current view is of type `audio`, shorthand for\n`viewType === ViewType.Audio`."
      },
      "attribute": "is-audio-view",
      "reflect": false,
      "defaultValue": "false"
    },
    "isVideoView": {
      "type": "boolean",
      "mutable": true,
      "complexType": {
        "original": "boolean",
        "resolved": "boolean",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "`@readonly` Whether the current view is of type `video`, shorthand for\n`viewType === ViewType.Video`."
      },
      "attribute": "is-video-view",
      "reflect": false,
      "defaultValue": "false"
    },
    "mediaType": {
      "type": "string",
      "mutable": true,
      "complexType": {
        "original": "MediaType",
        "resolved": "MediaType.Audio | MediaType.Video | undefined",
        "references": {
          "MediaType": {
            "location": "import",
            "path": "./MediaType"
          }
        }
      },
      "required": false,
      "optional": true,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "`@readonly` The type of media that is currently active, whether it's audio or video. Defaults\nto `undefined` when no media has been loaded or the type cannot be determined."
      },
      "attribute": "media-type",
      "reflect": false
    },
    "isAudio": {
      "type": "boolean",
      "mutable": true,
      "complexType": {
        "original": "boolean",
        "resolved": "boolean",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "`@readonly` Whether the current media is of type `audio`, shorthand for\n`mediaType === MediaType.Audio`."
      },
      "attribute": "is-audio",
      "reflect": false,
      "defaultValue": "false"
    },
    "isVideo": {
      "type": "boolean",
      "mutable": true,
      "complexType": {
        "original": "boolean",
        "resolved": "boolean",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "`@readonly` Whether the current media is of type `video`, shorthand for\n`mediaType === MediaType.Video`."
      },
      "attribute": "is-video",
      "reflect": false,
      "defaultValue": "false"
    },
    "isLive": {
      "type": "boolean",
      "mutable": true,
      "complexType": {
        "original": "boolean",
        "resolved": "boolean",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "`@readonly` Whether the current media is being broadcast live (`duration === Infinity`)."
      },
      "attribute": "is-live",
      "reflect": false,
      "defaultValue": "false"
    },
    "isMobile": {
      "type": "boolean",
      "mutable": true,
      "complexType": {
        "original": "boolean",
        "resolved": "boolean",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "`@readonly` Whether the player is in mobile mode. This is determined by parsing\n`window.navigator.userAgent`."
      },
      "attribute": "is-mobile",
      "reflect": false,
      "defaultValue": "IS_MOBILE"
    },
    "isTouch": {
      "type": "boolean",
      "mutable": true,
      "complexType": {
        "original": "boolean",
        "resolved": "boolean",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "`@readonly` Whether the player is in touch mode. This is determined by listening for\nmouse/touch events and toggling this value."
      },
      "attribute": "is-touch",
      "reflect": false,
      "defaultValue": "false"
    },
    "isPiPActive": {
      "type": "boolean",
      "mutable": true,
      "complexType": {
        "original": "boolean",
        "resolved": "boolean",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "`@readonly` Whether the player is currently in picture-in-picture mode."
      },
      "attribute": "is-pi-p-active",
      "reflect": false,
      "defaultValue": "false"
    },
    "autopause": {
      "type": "boolean",
      "mutable": false,
      "complexType": {
        "original": "boolean",
        "resolved": "boolean",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "Whether the player should automatically pause when another Vime player starts/resumes playback."
      },
      "attribute": "autopause",
      "reflect": false,
      "defaultValue": "true"
    },
    "playsinline": {
      "type": "boolean",
      "mutable": false,
      "complexType": {
        "original": "boolean",
        "resolved": "boolean",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "Whether the video is to be played \"inline\", that is within the element's playback area. Note\nthat setting this to false does not imply that the video will always be played in fullscreen.\nDepending on the provider, changing this prop may cause the player to completely reset."
      },
      "attribute": "playsinline",
      "reflect": false,
      "defaultValue": "false"
    },
    "language": {
      "type": "string",
      "mutable": true,
      "complexType": {
        "original": "string",
        "resolved": "string",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "The current language of the player. This can be any code defined via the `extendLanguage`\nmethod or the default `en`. It's recommended to use an ISO 639-1 code as that'll be used by\nVime when adding new language defaults in the future."
      },
      "attribute": "language",
      "reflect": false,
      "defaultValue": "'en'"
    },
    "translations": {
      "type": "unknown",
      "mutable": true,
      "complexType": {
        "original": "Record<string, Translation>",
        "resolved": "{ [x: string]: Translation; }",
        "references": {
          "Record": {
            "location": "global"
          },
          "Translation": {
            "location": "import",
            "path": "./lang/Translation"
          }
        }
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "`@readonly` Contains each language and its respective translation map."
      },
      "defaultValue": "{ en }"
    },
    "languages": {
      "type": "unknown",
      "mutable": true,
      "complexType": {
        "original": "string[]",
        "resolved": "string[]",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "`@readonly` The languages that are currently available. You can add new languages via the\n`extendLanguage` method."
      },
      "defaultValue": "['en']"
    },
    "i18n": {
      "type": "unknown",
      "mutable": true,
      "complexType": {
        "original": "Translation",
        "resolved": "Translation",
        "references": {
          "Translation": {
            "location": "import",
            "path": "./lang/Translation"
          }
        }
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "`@readonly` A dictionary of translations for the current language."
      },
      "defaultValue": "en"
    }
  }; }
  static get states() { return {
    "shouldCheckForProviderChange": {}
  }; }
  static get events() { return [{
      "method": "vThemeChange",
      "name": "vThemeChange",
      "bubbles": true,
      "cancelable": true,
      "composed": true,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "Emitted when the `theme` prop changes value."
      },
      "complexType": {
        "original": "PlayerProps['theme']",
        "resolved": "string | undefined",
        "references": {
          "PlayerProps": {
            "location": "import",
            "path": "./PlayerProps"
          }
        }
      }
    }, {
      "method": "vPausedChange",
      "name": "vPausedChange",
      "bubbles": true,
      "cancelable": true,
      "composed": true,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "Emitted when the `paused` prop changes value."
      },
      "complexType": {
        "original": "PlayerProps['paused']",
        "resolved": "boolean",
        "references": {
          "PlayerProps": {
            "location": "import",
            "path": "./PlayerProps"
          }
        }
      }
    }, {
      "method": "vPlay",
      "name": "vPlay",
      "bubbles": true,
      "cancelable": true,
      "composed": true,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "Emitted when the media is transitioning from `paused` to `playing`. Event flow: `paused` ->\n`play` -> `playing`. The media starts `playing` once enough content has buffered to\nbegin/resume playback."
      },
      "complexType": {
        "original": "void",
        "resolved": "void",
        "references": {}
      }
    }, {
      "method": "vPlayingChange",
      "name": "vPlayingChange",
      "bubbles": true,
      "cancelable": true,
      "composed": true,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "Emitted when the `playing` prop changes value."
      },
      "complexType": {
        "original": "PlayerProps['playing']",
        "resolved": "boolean",
        "references": {
          "PlayerProps": {
            "location": "import",
            "path": "./PlayerProps"
          }
        }
      }
    }, {
      "method": "vSeekingChange",
      "name": "vSeekingChange",
      "bubbles": true,
      "cancelable": true,
      "composed": true,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "Emitted when the `seeking` prop changes value."
      },
      "complexType": {
        "original": "PlayerProps['seeking']",
        "resolved": "boolean",
        "references": {
          "PlayerProps": {
            "location": "import",
            "path": "./PlayerProps"
          }
        }
      }
    }, {
      "method": "vSeeked",
      "name": "vSeeked",
      "bubbles": true,
      "cancelable": true,
      "composed": true,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "Emitted directly after the player has successfully transitioned/seeked to a new time position.\nEvent flow: `seeking` -> `seeked`."
      },
      "complexType": {
        "original": "void",
        "resolved": "void",
        "references": {}
      }
    }, {
      "method": "vBufferingChange",
      "name": "vBufferingChange",
      "bubbles": true,
      "cancelable": true,
      "composed": true,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "Emitted when the `buffering` prop changes value."
      },
      "complexType": {
        "original": "PlayerProps['buffering']",
        "resolved": "boolean",
        "references": {
          "PlayerProps": {
            "location": "import",
            "path": "./PlayerProps"
          }
        }
      }
    }, {
      "method": "vDurationChange",
      "name": "vDurationChange",
      "bubbles": true,
      "cancelable": true,
      "composed": true,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "Emitted when the `duration` prop changes value."
      },
      "complexType": {
        "original": "PlayerProps['duration']",
        "resolved": "number",
        "references": {
          "PlayerProps": {
            "location": "import",
            "path": "./PlayerProps"
          }
        }
      }
    }, {
      "method": "vCurrentTimeChange",
      "name": "vCurrentTimeChange",
      "bubbles": true,
      "cancelable": true,
      "composed": true,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "Emitted when the `currentTime` prop changes value."
      },
      "complexType": {
        "original": "PlayerProps['currentTime']",
        "resolved": "number",
        "references": {
          "PlayerProps": {
            "location": "import",
            "path": "./PlayerProps"
          }
        }
      }
    }, {
      "method": "vAttachedChange",
      "name": "vAttachedChange",
      "bubbles": true,
      "cancelable": true,
      "composed": true,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "Emitted when the player is attached/deattached from the DOM."
      },
      "complexType": {
        "original": "void",
        "resolved": "void",
        "references": {}
      }
    }, {
      "method": "vReady",
      "name": "vReady",
      "bubbles": true,
      "cancelable": true,
      "composed": true,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "Emitted when the player has loaded and is ready to be interacted with."
      },
      "complexType": {
        "original": "void",
        "resolved": "void",
        "references": {}
      }
    }, {
      "method": "vPlaybackReady",
      "name": "vPlaybackReady",
      "bubbles": true,
      "cancelable": true,
      "composed": true,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "Emitted when the media is ready to begin playback. The following props are guaranteed to be\ndefined when this fires: `mediaTitle`, `currentSrc`, `currentPoster`, `duration`, `mediaType`,\n`viewType`."
      },
      "complexType": {
        "original": "void",
        "resolved": "void",
        "references": {}
      }
    }, {
      "method": "vPlaybackStarted",
      "name": "vPlaybackStarted",
      "bubbles": true,
      "cancelable": true,
      "composed": true,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "Emitted when the media initiates playback."
      },
      "complexType": {
        "original": "void",
        "resolved": "void",
        "references": {}
      }
    }, {
      "method": "vPlaybackEnded",
      "name": "vPlaybackEnded",
      "bubbles": true,
      "cancelable": true,
      "composed": true,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "Emitted when playback reaches the end of the media."
      },
      "complexType": {
        "original": "void",
        "resolved": "void",
        "references": {}
      }
    }, {
      "method": "vBufferedChange",
      "name": "vBufferedChange",
      "bubbles": true,
      "cancelable": true,
      "composed": true,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "Emitted when the `buffered` prop changes value."
      },
      "complexType": {
        "original": "PlayerProps['buffered']",
        "resolved": "number",
        "references": {
          "PlayerProps": {
            "location": "import",
            "path": "./PlayerProps"
          }
        }
      }
    }, {
      "method": "vCurrentCaptionChange",
      "name": "vCurrentCaptionChange",
      "bubbles": true,
      "cancelable": true,
      "composed": true,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritdoc"
          }],
        "text": "Emitted when the `currentCaption` prop changes value."
      },
      "complexType": {
        "original": "PlayerProps['currentCaption']",
        "resolved": "TextTrack | undefined",
        "references": {
          "PlayerProps": {
            "location": "import",
            "path": "./PlayerProps"
          }
        }
      }
    }, {
      "method": "vTextTracksChange",
      "name": "vTextTracksChange",
      "bubbles": true,
      "cancelable": true,
      "composed": true,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "Emitted when the `textTracks` prop changes value."
      },
      "complexType": {
        "original": "PlayerProps['textTracks']",
        "resolved": "TextTrackList | undefined",
        "references": {
          "PlayerProps": {
            "location": "import",
            "path": "./PlayerProps"
          }
        }
      }
    }, {
      "method": "vErrorsChange",
      "name": "vErrorsChange",
      "bubbles": true,
      "cancelable": true,
      "composed": true,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "Emitted when the `errors` prop changes value."
      },
      "complexType": {
        "original": "PlayerProps['errors']",
        "resolved": "any[]",
        "references": {
          "PlayerProps": {
            "location": "import",
            "path": "./PlayerProps"
          }
        }
      }
    }, {
      "method": "vLoadStart",
      "name": "vLoadStart",
      "bubbles": true,
      "cancelable": true,
      "composed": true,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "Emitted when the provider starts loading a media resource."
      },
      "complexType": {
        "original": "void",
        "resolved": "void",
        "references": {}
      }
    }, {
      "method": "vCurrentProviderChange",
      "name": "vCurrentProviderChange",
      "bubbles": true,
      "cancelable": true,
      "composed": true,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "Emitted when the `currentProvider` prop changes value."
      },
      "complexType": {
        "original": "PlayerProps['currentProvider']",
        "resolved": "Provider.Audio | Provider.Dailymotion | Provider.Dash | Provider.FakeTube | Provider.HLS | Provider.Video | Provider.Vimeo | Provider.YouTube | undefined",
        "references": {
          "PlayerProps": {
            "location": "import",
            "path": "./PlayerProps"
          }
        }
      }
    }, {
      "method": "vCurrentSrcChange",
      "name": "vCurrentSrcChange",
      "bubbles": true,
      "cancelable": true,
      "composed": true,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "Emitted when the `currentSrc` prop changes value."
      },
      "complexType": {
        "original": "PlayerProps['currentSrc']",
        "resolved": "string | undefined",
        "references": {
          "PlayerProps": {
            "location": "import",
            "path": "./PlayerProps"
          }
        }
      }
    }, {
      "method": "vCurrentPosterChange",
      "name": "vCurrentPosterChange",
      "bubbles": true,
      "cancelable": true,
      "composed": true,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "Emitted when the `currentPoster` prop changes value."
      },
      "complexType": {
        "original": "PlayerProps['currentPoster']",
        "resolved": "string | undefined",
        "references": {
          "PlayerProps": {
            "location": "import",
            "path": "./PlayerProps"
          }
        }
      }
    }, {
      "method": "vMediaTitleChange",
      "name": "vMediaTitleChange",
      "bubbles": true,
      "cancelable": true,
      "composed": true,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "Emitted when the `mediaTitle` prop changes value."
      },
      "complexType": {
        "original": "PlayerProps['mediaTitle']",
        "resolved": "string | undefined",
        "references": {
          "PlayerProps": {
            "location": "import",
            "path": "./PlayerProps"
          }
        }
      }
    }, {
      "method": "vControlsChange",
      "name": "vControlsChange",
      "bubbles": true,
      "cancelable": true,
      "composed": true,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "Emitted when the `isControlsActive` prop changes value."
      },
      "complexType": {
        "original": "PlayerProps['isControlsActive']",
        "resolved": "boolean",
        "references": {
          "PlayerProps": {
            "location": "import",
            "path": "./PlayerProps"
          }
        }
      }
    }, {
      "method": "vPlaybackRateChange",
      "name": "vPlaybackRateChange",
      "bubbles": true,
      "cancelable": true,
      "composed": true,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "Emitted when the `playbackRate` prop changes value."
      },
      "complexType": {
        "original": "PlayerProps['playbackRate']",
        "resolved": "number",
        "references": {
          "PlayerProps": {
            "location": "import",
            "path": "./PlayerProps"
          }
        }
      }
    }, {
      "method": "vPlaybackRatesChange",
      "name": "vPlaybackRatesChange",
      "bubbles": true,
      "cancelable": true,
      "composed": true,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "Emitted when the `playbackRates` prop changes value."
      },
      "complexType": {
        "original": "PlayerProps['playbackRates']",
        "resolved": "number[]",
        "references": {
          "PlayerProps": {
            "location": "import",
            "path": "./PlayerProps"
          }
        }
      }
    }, {
      "method": "vPlaybackQualityChange",
      "name": "vPlaybackQualityChange",
      "bubbles": true,
      "cancelable": true,
      "composed": true,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "Emitted when the `playbackQuality` prop changes value."
      },
      "complexType": {
        "original": "PlayerProps['playbackQuality']",
        "resolved": "string | undefined",
        "references": {
          "PlayerProps": {
            "location": "import",
            "path": "./PlayerProps"
          }
        }
      }
    }, {
      "method": "vPlaybackQualitiesChange",
      "name": "vPlaybackQualitiesChange",
      "bubbles": true,
      "cancelable": true,
      "composed": true,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "Emitted when the `playbackQualities` prop changes value."
      },
      "complexType": {
        "original": "PlayerProps['playbackQualities']",
        "resolved": "string[]",
        "references": {
          "PlayerProps": {
            "location": "import",
            "path": "./PlayerProps"
          }
        }
      }
    }, {
      "method": "vMutedChange",
      "name": "vMutedChange",
      "bubbles": true,
      "cancelable": true,
      "composed": true,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "Emitted when the `muted` prop changes value."
      },
      "complexType": {
        "original": "PlayerProps['muted']",
        "resolved": "boolean",
        "references": {
          "PlayerProps": {
            "location": "import",
            "path": "./PlayerProps"
          }
        }
      }
    }, {
      "method": "vVolumeChange",
      "name": "vVolumeChange",
      "bubbles": true,
      "cancelable": true,
      "composed": true,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "Emitted when the `volume` prop changes value."
      },
      "complexType": {
        "original": "PlayerProps['volume']",
        "resolved": "number",
        "references": {
          "PlayerProps": {
            "location": "import",
            "path": "./PlayerProps"
          }
        }
      }
    }, {
      "method": "vViewTypeChange",
      "name": "vViewTypeChange",
      "bubbles": true,
      "cancelable": true,
      "composed": true,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "Emitted when the `viewType` prop changes value."
      },
      "complexType": {
        "original": "PlayerProps['viewType']",
        "resolved": "ViewType.Audio | ViewType.Video | undefined",
        "references": {
          "PlayerProps": {
            "location": "import",
            "path": "./PlayerProps"
          }
        }
      }
    }, {
      "method": "vMediaTypeChange",
      "name": "vMediaTypeChange",
      "bubbles": true,
      "cancelable": true,
      "composed": true,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "Emitted when the `mediaType` prop changes value."
      },
      "complexType": {
        "original": "PlayerProps['mediaType']",
        "resolved": "MediaType.Audio | MediaType.Video | undefined",
        "references": {
          "PlayerProps": {
            "location": "import",
            "path": "./PlayerProps"
          }
        }
      }
    }, {
      "method": "vLiveChange",
      "name": "vLiveChange",
      "bubbles": true,
      "cancelable": true,
      "composed": true,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "Emitted when the `isLive` prop changes value."
      },
      "complexType": {
        "original": "PlayerProps['isLive']",
        "resolved": "boolean",
        "references": {
          "PlayerProps": {
            "location": "import",
            "path": "./PlayerProps"
          }
        }
      }
    }, {
      "method": "vTouchChange",
      "name": "vTouchChange",
      "bubbles": true,
      "cancelable": true,
      "composed": true,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "Emitted when the `isTouch` prop changes value."
      },
      "complexType": {
        "original": "PlayerProps['isTouch']",
        "resolved": "boolean",
        "references": {
          "PlayerProps": {
            "location": "import",
            "path": "./PlayerProps"
          }
        }
      }
    }, {
      "method": "vLanguageChange",
      "name": "vLanguageChange",
      "bubbles": true,
      "cancelable": true,
      "composed": true,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "Emitted when the `language` prop changes value."
      },
      "complexType": {
        "original": "PlayerProps['language']",
        "resolved": "string",
        "references": {
          "PlayerProps": {
            "location": "import",
            "path": "./PlayerProps"
          }
        }
      }
    }, {
      "method": "vI18nChange",
      "name": "vI18nChange",
      "bubbles": true,
      "cancelable": true,
      "composed": true,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritdoc"
          }],
        "text": "Emitted when the `i18n` prop changes value."
      },
      "complexType": {
        "original": "PlayerProps['i18n']",
        "resolved": "Translation | { [x: string]: string; }",
        "references": {
          "PlayerProps": {
            "location": "import",
            "path": "./PlayerProps"
          }
        }
      }
    }, {
      "method": "vTranslationsChange",
      "name": "vTranslationsChange",
      "bubbles": true,
      "cancelable": true,
      "composed": true,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritdoc"
          }],
        "text": "Emitted when the `translations` prop changes value."
      },
      "complexType": {
        "original": "PlayerProps['translations']",
        "resolved": "{ [x: string]: Translation; }",
        "references": {
          "PlayerProps": {
            "location": "import",
            "path": "./PlayerProps"
          }
        }
      }
    }, {
      "method": "vLanguagesChange",
      "name": "vLanguagesChange",
      "bubbles": true,
      "cancelable": true,
      "composed": true,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "Emitted when the `languages` prop changes value."
      },
      "complexType": {
        "original": "PlayerProps['languages']",
        "resolved": "string[]",
        "references": {
          "PlayerProps": {
            "location": "import",
            "path": "./PlayerProps"
          }
        }
      }
    }, {
      "method": "vFullscreenChange",
      "name": "vFullscreenChange",
      "bubbles": true,
      "cancelable": true,
      "composed": true,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "Emitted when the `isFullscreenActive` prop changes value."
      },
      "complexType": {
        "original": "PlayerProps['isFullscreenActive']",
        "resolved": "boolean",
        "references": {
          "PlayerProps": {
            "location": "import",
            "path": "./PlayerProps"
          }
        }
      }
    }, {
      "method": "vPiPChange",
      "name": "vPiPChange",
      "bubbles": true,
      "cancelable": true,
      "composed": true,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritDoc"
          }],
        "text": "Emitted when the `isPiPActive` prop changes value."
      },
      "complexType": {
        "original": "PlayerProps['isPiPActive']",
        "resolved": "boolean",
        "references": {
          "PlayerProps": {
            "location": "import",
            "path": "./PlayerProps"
          }
        }
      }
    }]; }
  static get methods() { return {
    "getProvider": {
      "complexType": {
        "signature": "<InternalPlayerType = any>() => Promise<AdapterHost<InternalPlayerType> | undefined>",
        "parameters": [],
        "references": {
          "Promise": {
            "location": "global"
          },
          "AdapterHost": {
            "location": "import",
            "path": "../../providers/MediaProvider"
          },
          "InternalPlayerType": {
            "location": "global"
          }
        },
        "return": "Promise<AdapterHost<InternalPlayerType> | undefined>"
      },
      "docs": {
        "text": "Returns the current media provider.",
        "tags": [{
            "name": "inheritDoc",
            "text": undefined
          }]
      }
    },
    "setProvider": {
      "complexType": {
        "signature": "(provider: AdapterHost) => Promise<void>",
        "parameters": [{
            "tags": [],
            "text": ""
          }],
        "references": {
          "Promise": {
            "location": "global"
          },
          "AdapterHost": {
            "location": "import",
            "path": "../../providers/MediaProvider"
          }
        },
        "return": "Promise<void>"
      },
      "docs": {
        "text": "",
        "tags": [{
            "name": "internal",
            "text": "testing only."
          }]
      }
    },
    "getAdapter": {
      "complexType": {
        "signature": "<InternalPlayerType = any>() => Promise<MediaProviderAdapter<InternalPlayerType> | undefined>",
        "parameters": [],
        "references": {
          "Promise": {
            "location": "global"
          },
          "MediaProviderAdapter": {
            "location": "import",
            "path": "../../providers/MediaProvider"
          },
          "InternalPlayerType": {
            "location": "global"
          }
        },
        "return": "Promise<MediaProviderAdapter<InternalPlayerType> | undefined>"
      },
      "docs": {
        "text": "Returns the current media provider's adapter. Shorthand for `getProvider().getAdapter()`.",
        "tags": [{
            "name": "internal",
            "text": undefined
          }]
      }
    },
    "play": {
      "complexType": {
        "signature": "() => Promise<void | undefined>",
        "parameters": [],
        "references": {
          "Promise": {
            "location": "global"
          }
        },
        "return": "Promise<void | undefined>"
      },
      "docs": {
        "text": "Begins/resumes playback of the media. If this method is called programmatically before the user\nhas interacted with the player, the promise may be rejected subject to the browser's autoplay\npolicies.",
        "tags": [{
            "name": "inheritDoc",
            "text": undefined
          }]
      }
    },
    "pause": {
      "complexType": {
        "signature": "() => Promise<void | undefined>",
        "parameters": [],
        "references": {
          "Promise": {
            "location": "global"
          }
        },
        "return": "Promise<void | undefined>"
      },
      "docs": {
        "text": "Pauses playback of the media.",
        "tags": [{
            "name": "inheritDoc",
            "text": undefined
          }]
      }
    },
    "canPlay": {
      "complexType": {
        "signature": "(type: string) => Promise<boolean>",
        "parameters": [{
            "tags": [],
            "text": ""
          }],
        "references": {
          "Promise": {
            "location": "global"
          }
        },
        "return": "Promise<boolean>"
      },
      "docs": {
        "text": "Determines whether the current provider recognizes, and can play the given type.",
        "tags": [{
            "name": "inheritDoc",
            "text": undefined
          }]
      }
    },
    "canAutoplay": {
      "complexType": {
        "signature": "() => Promise<boolean>",
        "parameters": [],
        "references": {
          "Promise": {
            "location": "global"
          }
        },
        "return": "Promise<boolean>"
      },
      "docs": {
        "text": "Determines whether the player can start playback of the current media automatically.",
        "tags": [{
            "name": "inheritDoc",
            "text": undefined
          }]
      }
    },
    "canMutedAutoplay": {
      "complexType": {
        "signature": "() => Promise<boolean>",
        "parameters": [],
        "references": {
          "Promise": {
            "location": "global"
          }
        },
        "return": "Promise<boolean>"
      },
      "docs": {
        "text": "Determines whether the player can start playback of the current media automatically given the\nplayer is muted.",
        "tags": [{
            "name": "inheritDoc",
            "text": undefined
          }]
      }
    },
    "canSetPlaybackRate": {
      "complexType": {
        "signature": "() => Promise<boolean>",
        "parameters": [],
        "references": {
          "Promise": {
            "location": "global"
          }
        },
        "return": "Promise<boolean>"
      },
      "docs": {
        "text": "Returns whether the current provider allows setting the `playbackRate` prop.",
        "tags": [{
            "name": "inheritDoc",
            "text": undefined
          }]
      }
    },
    "canSetPlaybackQuality": {
      "complexType": {
        "signature": "() => Promise<boolean>",
        "parameters": [],
        "references": {
          "Promise": {
            "location": "global"
          }
        },
        "return": "Promise<boolean>"
      },
      "docs": {
        "text": "Returns whether the current provider allows setting the `playbackQuality` prop.",
        "tags": [{
            "name": "inheritDoc",
            "text": undefined
          }]
      }
    },
    "canSetFullscreen": {
      "complexType": {
        "signature": "() => Promise<boolean>",
        "parameters": [],
        "references": {
          "Promise": {
            "location": "global"
          }
        },
        "return": "Promise<boolean>"
      },
      "docs": {
        "text": "Returns whether the native browser fullscreen API is available, or the current provider can\ntoggle fullscreen mode. This does not mean that the operation is guaranteed to be successful,\nonly that it can be attempted.",
        "tags": [{
            "name": "inheritDoc",
            "text": undefined
          }]
      }
    },
    "enterFullscreen": {
      "complexType": {
        "signature": "(options?: FullscreenOptions | undefined) => Promise<any>",
        "parameters": [{
            "tags": [],
            "text": ""
          }],
        "references": {
          "Promise": {
            "location": "global"
          },
          "FullscreenOptions": {
            "location": "global"
          }
        },
        "return": "Promise<any>"
      },
      "docs": {
        "text": "Requests to enter fullscreen mode, returning a `Promise` that will resolve if the request is\nmade, or reject with a reason for failure. This method will first attempt to use the browsers\nnative fullscreen API, and then fallback to requesting the provider to do it (if available).\nDo not rely on a resolved promise to determine if the player is in fullscreen or not. The only\nway to be certain is by listening to the `vFullscreenChange` event. Some common reasons for\nfailure are: the fullscreen API is not available, the request is made when `viewType` is audio,\nor the user has not interacted with the page yet.",
        "tags": [{
            "name": "inheritDoc",
            "text": undefined
          }]
      }
    },
    "exitFullscreen": {
      "complexType": {
        "signature": "() => Promise<any>",
        "parameters": [],
        "references": {
          "Promise": {
            "location": "global"
          }
        },
        "return": "Promise<any>"
      },
      "docs": {
        "text": "Requests to exit fullscreen mode, returning a `Promise` that will resolve if the request is\nsuccessful, or reject with a reason for failure. Refer to `enterFullscreen()` for more\ninformation.",
        "tags": [{
            "name": "inheritDoc",
            "text": undefined
          }]
      }
    },
    "canSetPiP": {
      "complexType": {
        "signature": "() => Promise<boolean>",
        "parameters": [],
        "references": {
          "Promise": {
            "location": "global"
          }
        },
        "return": "Promise<boolean>"
      },
      "docs": {
        "text": "Returns whether the current provider exposes an API for entering and exiting\npicture-in-picture mode. This does not mean the operation is guaranteed to be successful, only\nthat it can be attempted.",
        "tags": [{
            "name": "inheritDoc",
            "text": undefined
          }]
      }
    },
    "enterPiP": {
      "complexType": {
        "signature": "() => Promise<void | undefined>",
        "parameters": [],
        "references": {
          "Promise": {
            "location": "global"
          }
        },
        "return": "Promise<void | undefined>"
      },
      "docs": {
        "text": "Request to enter picture-in-picture (PiP) mode, returning a `Promise` that will resolve if\nthe request is made, or reject with a reason for failure. Do not rely on a resolved promise\nto determine if the player is in PiP mode or not. The only way to be certain is by listening\nto the `vPiPChange` event. Some common reasons for failure are the same as the reasons for\n`enterFullscreen()`.",
        "tags": [{
            "name": "inheritDoc",
            "text": undefined
          }]
      }
    },
    "exitPiP": {
      "complexType": {
        "signature": "() => Promise<void | undefined>",
        "parameters": [],
        "references": {
          "Promise": {
            "location": "global"
          }
        },
        "return": "Promise<void | undefined>"
      },
      "docs": {
        "text": "Request to exit picture-in-picture mode, returns a `Promise` that will resolve if the request\nis successful, or reject with a reason for failure. Refer to `enterPiP()` for more\ninformation.",
        "tags": [{
            "name": "inheritDoc",
            "text": undefined
          }]
      }
    },
    "extendLanguage": {
      "complexType": {
        "signature": "(language: string, translation: Partial<Translation>) => Promise<void>",
        "parameters": [{
            "tags": [],
            "text": ""
          }, {
            "tags": [],
            "text": ""
          }],
        "references": {
          "Promise": {
            "location": "global"
          },
          "Partial": {
            "location": "global"
          },
          "Translation": {
            "location": "import",
            "path": "./lang/Translation"
          },
          "Record": {
            "location": "global"
          }
        },
        "return": "Promise<void>"
      },
      "docs": {
        "text": "Extends the translation map for a given language.",
        "tags": [{
            "name": "inheritDoc",
            "text": undefined
          }]
      }
    },
    "callAdapter": {
      "complexType": {
        "signature": "(method: keyof MediaProviderAdapter, value?: any) => Promise<any>",
        "parameters": [{
            "tags": [],
            "text": ""
          }, {
            "tags": [],
            "text": ""
          }],
        "references": {
          "Promise": {
            "location": "global"
          },
          "MediaProviderAdapter": {
            "location": "import",
            "path": "../../providers/MediaProvider"
          }
        },
        "return": "Promise<any>"
      },
      "docs": {
        "text": "",
        "tags": [{
            "name": "internal",
            "text": "Exposed for E2E testing."
          }]
      }
    },
    "toggleCaptionsVisibility": {
      "complexType": {
        "signature": "(isVisible?: boolean | undefined) => Promise<void>",
        "parameters": [{
            "tags": [],
            "text": ""
          }],
        "references": {
          "Promise": {
            "location": "global"
          }
        },
        "return": "Promise<void>"
      },
      "docs": {
        "text": "Toggles the visibility of the captions.",
        "tags": [{
            "name": "inheritdoc",
            "text": undefined
          }]
      }
    }
  }; }
  static get elementRef() { return "el"; }
  static get watchers() { return [{
      "propName": "paused",
      "methodName": "onPausedChange"
    }, {
      "propName": "duration",
      "methodName": "onDurationChange"
    }, {
      "propName": "currentTime",
      "methodName": "onCurrentTimeChange"
    }, {
      "propName": "playbackReady",
      "methodName": "onPlaybackReadyChange"
    }, {
      "propName": "muted",
      "methodName": "onMutedChange"
    }, {
      "propName": "playbackRate",
      "methodName": "onPlaybackRateChange"
    }, {
      "propName": "playbackQuality",
      "methodName": "onPlaybackQualityChange"
    }, {
      "propName": "debug",
      "methodName": "onDebugChange"
    }, {
      "propName": "errors",
      "methodName": "onErrorsChange"
    }, {
      "propName": "textTracks",
      "methodName": "onTextTracksChange"
    }, {
      "propName": "volume",
      "methodName": "onVolumeChange"
    }, {
      "propName": "viewType",
      "methodName": "onViewTypeChange"
    }, {
      "propName": "isAudioView",
      "methodName": "onViewTypeChange"
    }, {
      "propName": "isVideoView",
      "methodName": "onViewTypeChange"
    }, {
      "propName": "mediaType",
      "methodName": "onMediaTypeChange"
    }, {
      "propName": "language",
      "methodName": "onLanguageChange"
    }, {
      "propName": "translations",
      "methodName": "onTranslationsChange"
    }]; }
  static get listeners() { return [{
      "name": "vMediaProviderConnect",
      "method": "onMediaProviderConnect",
      "target": undefined,
      "capture": false,
      "passive": false
    }, {
      "name": "vMediaProviderDisconnect",
      "method": "onMediaProviderDisconnect",
      "target": undefined,
      "capture": false,
      "passive": false
    }, {
      "name": "vLoadStart",
      "method": "onMediaChange",
      "target": undefined,
      "capture": false,
      "passive": false
    }, {
      "name": "vStateChange",
      "method": "onStateChange",
      "target": undefined,
      "capture": false,
      "passive": false
    }, {
      "name": "vProviderChange",
      "method": "onProviderChange",
      "target": undefined,
      "capture": false,
      "passive": false
    }]; }
}
