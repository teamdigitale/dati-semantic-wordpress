'use strict';

Object.defineProperty(exports, '__esModule', { value: true });

const index = require('./index-e8963331.js');
const dom = require('./dom-4b0c36e3.js');
const PlayerDispatcher = require('./PlayerDispatcher-4f3d4f9d.js');
const PlayerContext = require('./PlayerContext-14c5c012.js');
const formatters = require('./formatters-e51f2218.js');

const CurrentTime = class {
  constructor(hostRef) {
    index.registerInstance(this, hostRef);
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
    PlayerContext.withPlayerContext(this, ['currentTime', 'i18n']);
  }
  render() {
    return (index.h("vime-time", { label: this.i18n.currentTime, seconds: this.currentTime, alwaysShowHours: this.alwaysShowHours }));
  }
};

const EndTime = class {
  constructor(hostRef) {
    index.registerInstance(this, hostRef);
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
    PlayerContext.withPlayerContext(this, ['duration', 'i18n']);
  }
  render() {
    return (index.h("vime-time", { label: this.i18n.duration, seconds: Math.max(0, this.duration), alwaysShowHours: this.alwaysShowHours }));
  }
};

const MuteControl = class {
  constructor(hostRef) {
    index.registerInstance(this, hostRef);
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
    PlayerContext.withPlayerContext(this, ['muted', 'volume', 'i18n']);
  }
  connectedCallback() {
    this.dispatch = PlayerDispatcher.createDispatcher(this);
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
    const tooltipWithHint = !dom.isUndefined(this.keys) ? `${tooltip} (${this.keys})` : tooltip;
    return (index.h("vime-control", { label: this.i18n.mute, pressed: this.muted, keys: this.keys, onClick: this.onClick.bind(this) }, index.h("vime-icon", { href: this.getIcon() }), index.h("vime-tooltip", { hidden: this.hideTooltip, position: this.tooltipPosition, direction: this.tooltipDirection }, tooltipWithHint)));
  }
};

const timeCss = "vime-time{display:flex;align-items:center;color:var(--vm-time-color);font-size:var(--vm-time-font-size);font-weight:var(--vm-time-font-weight)}";

const Time = class {
  constructor(hostRef) {
    index.registerInstance(this, hostRef);
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
    return (index.h(index.Host, { "aria-label": this.label }, formatters.formatTime(Math.max(0, this.seconds), this.alwaysShowHours)));
  }
};
Time.style = timeCss;

exports.vime_current_time = CurrentTime;
exports.vime_end_time = EndTime;
exports.vime_mute_control = MuteControl;
exports.vime_time = Time;
