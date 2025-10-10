import { r as registerInstance, c as createEvent, h, g as getElement } from './index-e4fee97f.js';
import { c as isNullOrUndefined, a as isNull, f as isUndefined, d as isString, l as listen, h as isNumber } from './dom-888fcf0c.js';
import { c as createDispatcher } from './PlayerDispatcher-4e41fc0b.js';
import { f as findRootPlayer } from './PlayerContext-da67ca53.js';
import { M as MediaType, V as ViewType } from './MediaType-8ac3bdc6.js';
import { e as IS_IOS, f as canUsePiPInChrome, g as canUsePiPInSafari, h as canUsePiP, i as canFullscreenVideo } from './support-c9ac4820.js';
import { a as withProviderConnect, w as withProviderContext, c as createProviderDispatcher } from './ProviderDispatcher-9bc874bc.js';
import { a as audioRegex, v as videoRegex, h as hlsRegex } from './utils-7dc44688.js';
import { D as Disposal } from './Disposal-525363e0.js';
import { L as LazyLoader } from './LazyLoader-fff3b265.js';

const fileCss = "vime-file audio,vime-file video{border-radius:inherit;vertical-align:middle;width:100%;outline:0}vime-file video{position:absolute;top:0;left:0;border:0;height:100%;user-select:none}";

const File = class {
  constructor(hostRef) {
    registerInstance(this, hostRef);
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
  get el() { return getElement(this); }
  static get watchers() { return {
    "mediaTitle": ["onMediaTitleChange"],
    "poster": ["onPosterChange"],
    "viewType": ["onViewTypeChange"]
  }; }
};
File.style = fileCss;

const Video = class {
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
    return this.fileProvider.getAdapter();
  }
  render() {
    return (
    // @ts-ignore
    h("vime-file", { noConnect: true, willAttach: this.willAttach, crossOrigin: this.crossOrigin, poster: this.poster, preload: this.preload, controlsList: this.controlsList, autoPiP: this.autoPiP, disablePiP: this.disablePiP, disableRemotePlayback: this.disableRemotePlayback, mediaTitle: this.mediaTitle, viewType: ViewType.Video, ref: (el) => { this.fileProvider = el; } }, h("slot", null)));
  }
};

export { File as vime_file, Video as vime_video };
