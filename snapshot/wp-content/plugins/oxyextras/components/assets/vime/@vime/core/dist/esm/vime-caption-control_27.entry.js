import { r as registerInstance, h, H as Host, c as createEvent, g as getElement, f as forceUpdate } from './index-e4fee97f.js';
import { f as isUndefined, l as listen, a as isNull, c as isNullOrUndefined, k as isColliding, m as findShadowRoot } from './dom-888fcf0c.js';
import { c as createDispatcher } from './PlayerDispatcher-4e41fc0b.js';
import { w as withPlayerContext, f as findRootPlayer } from './PlayerContext-da67ca53.js';
import { a as IS_MOBILE, e as IS_IOS } from './support-c9ac4820.js';
import { l as loadSprite } from './network-3c30465e.js';
import { P as Provider } from './Provider-99c71269.js';
import { D as Disposal } from './Disposal-525363e0.js';
import { L as LazyLoader } from './LazyLoader-fff3b265.js';
import { f as formatTime } from './formatters-edb63282.js';

const captionControlCss = "vime-caption-control.hidden{display:none}";

const CaptionControl = class {
  constructor(hostRef) {
    registerInstance(this, hostRef);
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
};
CaptionControl.style = captionControlCss;

const captionsCss = "vime-captions{position:absolute;left:0;bottom:0;width:100%;text-align:center;color:var(--vm-captions-text-color);font-size:var(--vm-captions-font-size);padding:var(--vm-control-spacing);z-index:var(--vm-captions-z-index);display:none;pointer-events:none;transition:transform 0.4s ease-in-out}vime-captions.enabled{display:inline-block}vime-captions.hidden{display:none !important}@media (min-width: 768px){vime-captions{font-size:var(--vm-captions-font-size-medium)}}@media (min-width: 992px){vime-captions{font-size:var(--vm-captions-font-size-large)}}@media (min-width: 1200px){vime-captions{font-size:var(--vm-captions-font-size-xlarge)}}vime-captions span{display:inline-block;background:var(--vm-captions-cue-bg-color);border-radius:var(--vm-captions-cue-border-radius);box-decoration-break:clone;line-height:185%;padding:var(--vm-captions-cue-padding);white-space:pre-wrap;pointer-events:none}vime-captions span :global(div){display:inline}vime-captions span:empty{display:none}";

const Captions = class {
  constructor(hostRef) {
    registerInstance(this, hostRef);
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
};
Captions.style = captionsCss;

const clickToPlayCss = "vime-click-to-play{position:absolute;top:0;left:0;width:100%;height:100%;pointer-events:none;display:none;z-index:var(--vm-click-to-play-z-index)}vime-click-to-play.enabled{display:inline-block;pointer-events:auto}";

const ClickToPlay = class {
  constructor(hostRef) {
    registerInstance(this, hostRef);
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
};
ClickToPlay.style = clickToPlayCss;

const controlGroupCss = "vime-control-group{width:100%;display:flex;flex-wrap:wrap;flex-direction:inherit;align-items:inherit;justify-content:inherit}vime-control-group.spaceTop{margin-top:var(--vm-control-group-spacing)}vime-control-group.spaceBottom{margin-bottom:var(--vm-control-group-spacing)}vime-control-group>*{margin-left:var(--vm-controls-spacing)}vime-control-group>*:first-child{margin-left:0}";

const ControlNewLine = class {
  constructor(hostRef) {
    registerInstance(this, hostRef);
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
  get el() { return getElement(this); }
};
ControlNewLine.style = controlGroupCss;

const controlSpacerCss = "vime-control-spacer{flex:1}";

const ControlSpacer = class {
  constructor(hostRef) {
    registerInstance(this, hostRef);
  }
  render() {
    return (h(Host, null));
  }
};
ControlSpacer.style = controlSpacerCss;

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
const Controls = class {
  constructor(hostRef) {
    registerInstance(this, hostRef);
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
  get el() { return getElement(this); }
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
};
Controls.style = controlsCss;

const dblClickFullscreenCss = "vime-dbl-click-fullscreen{position:absolute;top:0;left:0;width:100%;height:100%;pointer-events:none;display:none;z-index:var(--vm-dbl-click-fullscreen-z-index)}vime-dbl-click-fullscreen.enabled{display:inline-block;pointer-events:auto}";

const DblClickFullscreen = class {
  constructor(hostRef) {
    registerInstance(this, hostRef);
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
};
DblClickFullscreen.style = dblClickFullscreenCss;

const defaultControlsCss = "vime-default-controls{width:100%}";

const DefaultControls = class {
  constructor(hostRef) {
    registerInstance(this, hostRef);
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
};
DefaultControls.style = defaultControlsCss;

const DefaultSettings = class {
  constructor(hostRef) {
    registerInstance(this, hostRef);
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

const fullscreenControlCss = "vime-fullscreen-control.hidden{display:none}";

const FullscreenControl = class {
  constructor(hostRef) {
    registerInstance(this, hostRef);
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
};
FullscreenControl.style = fullscreenControlCss;

const Icons = class {
  constructor(hostRef) {
    registerInstance(this, hostRef);
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
  get el() { return getElement(this); }
  static get watchers() { return {
    "href": ["loadIcons"]
  }; }
};

const liveIndicatorCss = "vime-live-indicator{display:flex;align-items:center;font-size:13px;font-weight:bold;letter-spacing:0.6px;color:var(--vm-control-color)}vime-live-indicator.hidden{display:none}vime-live-indicator>.indicator{display:inline-block;width:8px;height:8px;border-radius:50%;margin-right:4px;background-color:var(--vm-live-indicator-color, red)}";

const LiveIndicator = class {
  constructor(hostRef) {
    registerInstance(this, hostRef);
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
};
LiveIndicator.style = liveIndicatorCss;

const MenuRadio = class {
  constructor(hostRef) {
    registerInstance(this, hostRef);
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

const MenuRadioGroup = class {
  constructor(hostRef) {
    registerInstance(this, hostRef);
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
  get el() { return getElement(this); }
  static get watchers() { return {
    "value": ["onValueChange"]
  }; }
};

const pipControlCss = "vime-pip-control.hidden{display:none}";

const PiPControl = class {
  constructor(hostRef) {
    registerInstance(this, hostRef);
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
};
PiPControl.style = pipControlCss;

const PlaybackControl = class {
  constructor(hostRef) {
    registerInstance(this, hostRef);
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

const posterCss = "vime-poster{position:absolute;top:0;left:0;width:100%;height:100%;background:#000;z-index:var(--vm-poster-z-index);display:inline-block;pointer-events:none;opacity:0;visibility:hidden;transition:var(--vm-fade-transition)}vime-poster.hidden{display:none}vime-poster.active{opacity:1;visibility:visible}vime-poster img{width:100%;height:100%;pointer-events:none}";

const Poster = class {
  constructor(hostRef) {
    registerInstance(this, hostRef);
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
  get el() { return getElement(this); }
  static get watchers() { return {
    "currentPoster": ["onCurrentPosterChange", "onEnabledChange"],
    "isVideoView": ["onEnabledChange"],
    "currentTime": ["onActiveChange"],
    "playbackStarted": ["onActiveChange"]
  }; }
};
Poster.style = posterCss;

const scrimCss = "vime-scrim{position:absolute;top:0;left:0;width:100%;height:100%;background:var(--vm-scrim-bg);z-index:var(--vm-scrim-z-index);display:inline-block;pointer-events:none;opacity:0;visibility:hidden;transition:var(--vm-fade-transition)}vime-scrim.gradient{height:258px;background:none;background-position:bottom;background-image:url(\"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAECCAYAAAA/9r2TAAABKklEQVQ4T2XI50cFABiF8dvee++67b33uM17b1MkkSSSSBJJJIkkkkQSSSKJ9Efmeb8cr86HH88JBP4thkfEkiKOFPGkSCCNRE8SKZJJkUIaqZ40UqSTIoMUmaSR5ckmRQ4pckkjz5NPigJSFJKiiDSKPSWkKCVFGWmUeypIUUmKKlJUk0aNJ0iKWlLUkUa9p4EUjaRoIkUzabR4WknRRop20ujwdJKiixTdpOghjV5PHyn6STFAGoOeIVIMk2KEFKOkMeYZJ8UEKUKkMemZIsU0KWZIMUsac54wKSKkiJLGvGeBFIukWCLFMrkCq7AG67ABm7AF27ADu7AH+3AAh3AEx3ACp3AG53ABl3AF13ADt3AH9/AAj/AEz/ACr/AG7/ABn/AF3/ADv39LujSyJPVJ0QAAAABJRU5ErkJggg==\")}vime-scrim.gradientUp{top:unset;bottom:0}vime-scrim.gradientDown{transform:rotate(180deg)}vime-scrim.hidden{display:none}vime-scrim.active{opacity:1;visibility:visible}";

const Scrim = class {
  constructor(hostRef) {
    registerInstance(this, hostRef);
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
};
Scrim.style = scrimCss;

const scrubberControlCss = "vime-scrubber-control{flex:1;cursor:pointer;position:relative;pointer-events:auto;left:calc(var(--vm-slider-thumb-width) / 2);margin-right:var(--vm-slider-thumb-width);margin-bottom:var(--vm-slider-track-height)}@keyframes progress{to{background-position:var(--vm-scrubber-loading-stripe-size) 0}}vime-scrubber-control vime-slider,vime-scrubber-control progress{margin-left:calc(0px - calc(var(--vm-slider-thumb-width) / 2));margin-right:calc(0px - calc(var(--vm-slider-thumb-width) / 2));width:calc(100% + var(--vm-slider-thumb-width));height:var(--vm-slider-track-height)}vime-scrubber-control vime-slider:hover,vime-scrubber-control progress:hover{cursor:pointer}vime-scrubber-control vime-slider{position:absolute;top:0;left:0;z-index:3}vime-scrubber-control progress{-webkit-appearance:none;background:transparent;border:0;border-radius:100px;position:absolute;left:0;top:50%;padding:0;color:var(--vm-scrubber-buffered-bg);height:var(--vm-slider-track-height)}vime-scrubber-control progress::-webkit-progress-bar{background:transparent}vime-scrubber-control progress::-webkit-progress-value{background:currentColor;border-radius:100px;min-width:var(--vm-slider-track-height);transition:width 0.2s ease}vime-scrubber-control progress::-moz-progress-bar{background:currentColor;border-radius:100px;min-width:var(--vm-slider-track-height);transition:width 0.2s ease}vime-scrubber-control progress::-ms-fill{border-radius:100px;transition:width 0.2s ease}vime-scrubber-control progress.loading{animation:progress 1s linear infinite;background-image:linear-gradient(-45deg, var(--vm-scrubber-loading-stripe-color) 25%, transparent 25%, transparent 50%, var(--vm-scrubber-loading-stripe-color) 50%, var(--vm-scrubber-loading-stripe-color) 75%, transparent 75%, transparent);background-repeat:repeat-x;background-size:var(--vm-scrubber-loading-stripe-size) var(--vm-scrubber-loading-stripe-size);color:transparent;background-color:transparent}";

const ScrubberControl = class {
  constructor(hostRef) {
    registerInstance(this, hostRef);
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
  get el() { return getElement(this); }
  static get watchers() { return {
    "noKeyboard": ["onNoKeyboardChange"],
    "duration": ["onDurationChange"]
  }; }
};
ScrubberControl.style = scrubberControlCss;

const settingsCss = "vime-settings{position:absolute;opacity:0;pointer-events:auto;overflow-x:hidden;overflow-y:auto;background-color:var(--vm-menu-bg);max-height:var(--vm-settings-max-height);border-radius:var(--vm-settings-border-radius);width:var(--vm-settings-width);padding:var(--vm-settings-padding);box-shadow:var(--vm-settings-shadow);z-index:var(--vm-menu-z-index);scrollbar-width:thin;scroll-behavior:smooth;scrollbar-color:var(--vm-settings-scroll-thumb-color) var(--vm-settings-scroll-track-color);transform:translateY(8px);transition:var(--vm-settings-transition)}vime-settings.hydrated{visibility:hidden !important}vime-settings::-webkit-scrollbar{width:var(--vm-settings-scroll-width)}vime-settings::-webkit-scrollbar-track{background:var(--vm-settings-scroll-track-color)}vime-settings::-webkit-scrollbar-thumb{border-radius:var(--vm-settings-scroll-width);background-color:var(--vm-settings-scroll-thumb-color);border:2px solid var(--vm-menu-bg)}vime-settings>vime-menu[aria-hidden=true]{display:flex !important}vime-settings.active{transform:translateY(0);opacity:1;visibility:visible !important}vime-settings.mobile{position:fixed;top:auto !important;left:0 !important;right:0 !important;bottom:0 !important;width:100%;min-height:56px;max-height:50%;border-radius:0;z-index:2147483647;transform:translateY(100%)}vime-settings.mobile.active{transform:translateY(0)}vime-settings.mobile vime-menu{height:100% !important;overflow:auto !important}";

let idCount = 0;
const Settings = class {
  constructor(hostRef) {
    registerInstance(this, hostRef);
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
    idCount += 1;
    this.id = `vime-settings-${idCount}`;
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
  get el() { return getElement(this); }
  static get watchers() { return {
    "active": ["onActiveChange"]
  }; }
};
Settings.style = settingsCss;

const settingsControlCss = "vime-settings-control.hidden{display:none}vime-settings-control svg{transition:transform 0.3s ease}vime-settings-control.active svg{transform:rotate(90deg) !important}";

let idCount$1 = 0;
const SettingsControl = class {
  constructor(hostRef) {
    registerInstance(this, hostRef);
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
    idCount$1 += 1;
    this.id = `vime-settings-control-${idCount$1}`;
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
  get el() { return getElement(this); }
};
SettingsControl.style = settingsControlCss;

const skeletonCss = "vime-skeleton{position:absolute;top:0;left:0;width:100%;height:100%;display:flex;min-height:1rem;z-index:var(--vm-skeleton-z-index)}@keyframes sheen{0%{background-position:200% 0}to{background-position:-200% 0}}vime-skeleton.hidden{opacity:0;visibility:hidden;transition:var(--vm-fade-transition);pointer-events:none}vime-skeleton .indicator{flex:1 1 auto;background:var(--vm-skeleton-color)}vime-skeleton.sheen .indicator{background:linear-gradient(270deg, var(--vm-skeleton-sheen-color), var(--vm-skeleton-color), var(--vm-skeleton-color), var(--vm-skeleton-sheen-color));background-size:400% 100%;background-size:400% 100%;animation:sheen 8s ease-in-out infinite}";

const Skeleton = class {
  constructor(hostRef) {
    registerInstance(this, hostRef);
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
};
Skeleton.style = skeletonCss;

const spinnerCss = "vime-spinner{position:absolute;top:0;left:0;width:100%;height:100%;display:flex;justify-content:center;align-items:center;pointer-events:none;z-index:var(--vm-spinner-z-index);opacity:0;visibility:hidden;transition:var(--vm-fade-transition)}vime-spinner.hidden{display:none}vime-spinner.active{opacity:1;visibility:visible}@keyframes spin{0%{transform:rotate(0deg)}100%{transform:rotate(360deg)}}vime-spinner>div{background:transparent;margin:60px auto;font-size:10px;position:relative;text-indent:-9999em;pointer-events:none;border-top:var(--vm-spinner-thickness) solid var(--vm-spinner-fill-color);border-left:var(--vm-spinner-thickness) solid var(--vm-spinner-fill-color);border-right:var(--vm-spinner-thickness) solid var(--vm-spinner-track-color);border-bottom:var(--vm-spinner-thickness) solid var(--vm-spinner-track-color);transform:translateZ(0);animation:spin var(--vm-spinner-spin-duration) infinite var(--vm-spinner-spin-timing-func)}vime-spinner>div,vime-spinner>div::after{border-radius:50%;width:var(--vm-spinner-width);height:var(--vm-spinner-height)}";

const Spinner = class {
  constructor(hostRef) {
    registerInstance(this, hostRef);
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
};
Spinner.style = spinnerCss;

let idCount$2 = 0;
const Submenu = class {
  constructor(hostRef) {
    registerInstance(this, hostRef);
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
    idCount$2 += 1;
    this.id = `vime-submenu-${idCount$2}`;
  }
  getControllerId() {
    return `${this.id}-controller`;
  }
  render() {
    return (h(Host, null, h("vime-menu-item", { identifier: this.getControllerId(), hidden: this.hidden, menu: this.id, label: this.label, hint: this.hint, expanded: this.active }), h("vime-menu", { identifier: this.id, controller: this.getControllerId(), active: this.active, onVOpen: this.onOpen.bind(this), onVClose: this.onClose.bind(this) }, h("slot", null))));
  }
};

const timeProgressCss = "vime-time-progress{display:flex;align-items:center;color:var(--vm-time-color)}vime-time-progress>span{margin:0 4px}";

const TimeProgress = class {
  constructor(hostRef) {
    registerInstance(this, hostRef);
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
};
TimeProgress.style = timeProgressCss;

const uiCss = "vime-ui{pointer-events:none;width:100%;z-index:var(--vm-ui-z-index)}vime-ui.hidden{display:none}vime-ui.video{position:absolute;top:0;left:0;height:100%}";

const UI = class {
  constructor(hostRef) {
    registerInstance(this, hostRef);
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
};
UI.style = uiCss;

const volumeControlCss = "vime-volume-control{align-items:center;display:flex;position:relative;pointer-events:auto;margin-left:calc(var(--vm-control-spacing) / 2) !important}vime-volume-control vime-slider{width:75px;height:100%;margin:0;max-width:0;position:relative;z-index:3;transition:margin 0.2s cubic-bezier(0.4, 0, 1, 1), max-width 0.2s cubic-bezier(0.4, 0, 1, 1);margin-left:calc(var(--vm-control-spacing) / 2) !important;visibility:hidden}vime-volume-control vime-slider:hover{cursor:pointer}vime-volume-control vime-slider.hidden{display:none}vime-volume-control vime-slider.active{max-width:75px;margin:0 var(--vm-control-spacing)/2;visibility:visible}";

const VolumeControl = class {
  constructor(hostRef) {
    registerInstance(this, hostRef);
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
};
VolumeControl.style = volumeControlCss;

export { CaptionControl as vime_caption_control, Captions as vime_captions, ClickToPlay as vime_click_to_play, ControlNewLine as vime_control_group, ControlSpacer as vime_control_spacer, Controls as vime_controls, DblClickFullscreen as vime_dbl_click_fullscreen, DefaultControls as vime_default_controls, DefaultSettings as vime_default_settings, FullscreenControl as vime_fullscreen_control, Icons as vime_icons, LiveIndicator as vime_live_indicator, MenuRadio as vime_menu_radio, MenuRadioGroup as vime_menu_radio_group, PiPControl as vime_pip_control, PlaybackControl as vime_playback_control, Poster as vime_poster, Scrim as vime_scrim, ScrubberControl as vime_scrubber_control, Settings as vime_settings, SettingsControl as vime_settings_control, Skeleton as vime_skeleton, Spinner as vime_spinner, Submenu as vime_submenu, TimeProgress as vime_time_progress, UI as vime_ui, VolumeControl as vime_volume_control };
