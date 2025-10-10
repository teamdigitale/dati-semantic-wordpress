import { r as registerInstance, h, H as Host } from './index-e4fee97f.js';
import { f as isUndefined } from './dom-888fcf0c.js';
import { c as createDispatcher } from './PlayerDispatcher-4e41fc0b.js';
import { w as withPlayerContext } from './PlayerContext-da67ca53.js';
import { f as formatTime } from './formatters-edb63282.js';

const CurrentTime = class {
  constructor(hostRef) {
    registerInstance(this, hostRef);
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

const EndTime = class {
  constructor(hostRef) {
    registerInstance(this, hostRef);
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

const MuteControl = class {
  constructor(hostRef) {
    registerInstance(this, hostRef);
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

const timeCss = "vime-time{display:flex;align-items:center;color:var(--vm-time-color);font-size:var(--vm-time-font-size);font-weight:var(--vm-time-font-weight)}";

const Time = class {
  constructor(hostRef) {
    registerInstance(this, hostRef);
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
};
Time.style = timeCss;

export { CurrentTime as vime_current_time, EndTime as vime_end_time, MuteControl as vime_mute_control, Time as vime_time };
