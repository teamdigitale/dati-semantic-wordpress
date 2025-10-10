'use strict';

Object.defineProperty(exports, '__esModule', { value: true });

const index = require('./index-e8963331.js');
const dom = require('./dom-4b0c36e3.js');
const PlayerDispatcher = require('./PlayerDispatcher-4f3d4f9d.js');
const PlayerContext = require('./PlayerContext-14c5c012.js');
const MediaType = require('./MediaType-8f5423d4.js');
const support = require('./support-578168e8.js');
const ProviderDispatcher = require('./ProviderDispatcher-797dd0b2.js');
const utils = require('./utils-b8b7354f.js');
const Disposal = require('./Disposal-f736adf6.js');
const LazyLoader = require('./LazyLoader-0f7f3135.js');

const fileCss = "vime-file audio,vime-file video{border-radius:inherit;vertical-align:middle;width:100%;outline:0}vime-file video{position:absolute;top:0;left:0;border:0;height:100%;user-select:none}";

const File = class {
  constructor(hostRef) {
    index.registerInstance(this, hostRef);
    this.vLoadStart = index.createEvent(this, "vLoadStart", 7);
    this.vMediaElChange = index.createEvent(this, "vMediaElChange", 7);
    this.vSrcSetChange = index.createEvent(this, "vSrcSetChange", 7);
    this.disposal = new Disposal.Disposal();
    this.wasPausedBeforeSeeking = true;
    this.currentSrcSet = [];
    this.mediaQueryDisposal = new Disposal.Disposal();
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
      ProviderDispatcher.withProviderConnect(this);
    ProviderDispatcher.withProviderContext(this, ['playbackStarted', 'currentTime', 'paused']);
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
    this.dispatch = ProviderDispatcher.createProviderDispatcher(this);
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
    this.lazyLoader = new LazyLoader.LazyLoader(this.el, ['data-src', 'data-poster'], () => {
      if (dom.isNullOrUndefined(this.mediaEl))
        return;
      const poster = this.mediaEl.getAttribute('data-poster');
      if (!dom.isNull(poster))
        this.mediaEl.setAttribute('poster', poster);
      this.refresh();
      this.didSrcSetChange();
    });
  }
  refresh() {
    if (dom.isNullOrUndefined(this.mediaEl))
      return;
    const { children } = this.mediaEl;
    for (let i = 0; i <= children.length - 1; i += 1) {
      const child = children[i];
      const src = child.getAttribute('data-src') || child.getAttribute('src') || child.getAttribute('data-vs');
      child.removeAttribute('src');
      if (dom.isNull(src))
        continue;
      child.setAttribute('data-vs', src);
      if (!dom.isNull(child.getAttribute('data-quality'))) {
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
    if (dom.isNullOrUndefined(this.mediaEl))
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
    if (!dom.isUndefined(this.playbackQuality))
      return;
    const getQualityValue = (resource) => { var _a, _b; return Number((_b = (_a = resource.quality) === null || _a === void 0 ? void 0 : _a.slice(0, -1)) !== null && _b !== void 0 ? _b : 0); };
    const sortMediaResource = (a, b) => getQualityValue(a) - getQualityValue(b);
    // Try to find best quality based on media queries.
    let mediaResource = this.currentSrcSet
      .filter((resource) => {
      if (!dom.isString(resource.media))
        return false;
      const query = window.matchMedia(resource.media);
      const dispatch = PlayerDispatcher.createDispatcher(this);
      this.mediaQueryDisposal.add(dom.listen(query, 'change', (e) => {
        if (e.matches)
          dispatch('playbackQuality', resource.quality);
      }));
      return query.matches;
    })
      .sort(sortMediaResource)
      .pop();
    // Otherwise pick best quality based on window width.
    if (dom.isUndefined(mediaResource)) {
      mediaResource = this.currentSrcSet
        .find((resource) => getQualityValue(resource) > window.innerWidth);
    }
    // Otehrwise pick best quality.
    if (dom.isUndefined(mediaResource)) {
      mediaResource = this.currentSrcSet.sort(sortMediaResource).pop();
    }
    this.playbackQuality = mediaResource === null || mediaResource === void 0 ? void 0 : mediaResource.quality;
    this.dispatch('playbackQuality', mediaResource === null || mediaResource === void 0 ? void 0 : mediaResource.quality);
  }
  hasCustomPoster() {
    const root = PlayerContext.findRootPlayer(this);
    return !support.IS_IOS && !dom.isNull(root.querySelector('vime-ui vime-poster'));
  }
  cancelTimeUpdates() {
    if (dom.isNumber(this.timeRAF))
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
    if (utils.audioRegex.test(currentSrc))
      return MediaType.MediaType.Audio;
    if (utils.videoRegex.test(currentSrc) || utils.hlsRegex.test(currentSrc))
      return MediaType.MediaType.Video;
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
    if (support.canUsePiPInChrome())
      return this.togglePiPInChrome(toggle);
    if (support.canUsePiPInSafari())
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
    if (dom.isUndefined(this.mediaEl))
      return;
    this.disposal.add(dom.listen(this.mediaEl.textTracks, 'change', this.onTracksChange.bind(this)));
  }
  /**
   * @internal
   */
  async getAdapter() {
    return {
      getInternalPlayer: async () => this.mediaEl,
      play: async () => { var _a; return (_a = this.mediaEl) === null || _a === void 0 ? void 0 : _a.play(); },
      pause: async () => { var _a; return (_a = this.mediaEl) === null || _a === void 0 ? void 0 : _a.pause(); },
      canPlay: async (type) => dom.isString(type)
        && (utils.audioRegex.test(type) || utils.videoRegex.test(type)),
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
      canSetPiP: async () => support.canUsePiP(),
      enterPiP: () => this.togglePiP(true),
      exitPiP: () => this.togglePiP(false),
      canSetFullscreen: async () => support.canFullscreenVideo(),
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
    const audio = (index.h("audio", Object.assign({ class: "lazy" }, mediaProps), index.h("slot", null), "Your browser does not support the", index.h("code", null, "audio"), "element."));
    const video = (index.h("video", Object.assign({ class: "lazy" }, mediaProps, {
      // @ts-ignore
      onwebkitpresentationmodechanged: this.onPresentationModeChange.bind(this), onenterpictureinpicture: this.onEnterPiP.bind(this), onleavepictureinpicture: this.onLeavePiP.bind(this)
    }), index.h("slot", null), "Your browser does not support the", index.h("code", null, "video"), "element."));
    return (this.viewType === MediaType.ViewType.Audio) ? audio : video;
  }
  get el() { return index.getElement(this); }
  static get watchers() { return {
    "mediaTitle": ["onMediaTitleChange"],
    "poster": ["onPosterChange"],
    "viewType": ["onViewTypeChange"]
  }; }
};
File.style = fileCss;

const Video = class {
  constructor(hostRef) {
    index.registerInstance(this, hostRef);
    /**
     * @internal Whether an external SDK will attach itself to the media player and control it.
     */
    this.willAttach = false;
    /**
     * @inheritdoc
     */
    this.preload = 'metadata';
    if (!this.willAttach)
      ProviderDispatcher.withProviderConnect(this);
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
    index.h("vime-file", { noConnect: true, willAttach: this.willAttach, crossOrigin: this.crossOrigin, poster: this.poster, preload: this.preload, controlsList: this.controlsList, autoPiP: this.autoPiP, disablePiP: this.disablePiP, disableRemotePlayback: this.disableRemotePlayback, mediaTitle: this.mediaTitle, viewType: MediaType.ViewType.Video, ref: (el) => { this.fileProvider = el; } }, index.h("slot", null)));
  }
};

exports.vime_file = File;
exports.vime_video = Video;
