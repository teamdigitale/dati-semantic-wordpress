'use strict';

Object.defineProperty(exports, '__esModule', { value: true });

const index = require('./index-e8963331.js');
const dom = require('./dom-4b0c36e3.js');
const PlayerContext = require('./PlayerContext-14c5c012.js');
const Disposal = require('./Disposal-f736adf6.js');

const controlCss = "vime-control.hidden{display:none}vime-control button{display:flex;align-items:center;flex-direction:row;border:var(--vm-control-border);cursor:pointer;flex-shrink:0;color:var(--vm-control-color);background:var(--vm-control-bg, transparent);border-radius:var(--vm-control-border-radius);padding:var(--vm-control-padding);position:relative;pointer-events:auto;transition:all 0.3s ease;transform:scale(var(--vm-control-scale, 1))}vime-control button:focus{outline:0}vime-control button.tapHighlight{background:var(--vm-control-tap-highlight)}vime-control button.notTouch:focus,vime-control button.notTouch:hover,vime-control button.notTouch[aria-expanded=true]{background:var(--vm-control-focus-bg);color:var(--vm-control-focus-color);transform:scale(calc(var(--vm-control-scale, 1) + 0.1))}";

const Control = class {
  constructor(hostRef) {
    index.registerInstance(this, hostRef);
    this.vInteractionChange = index.createEvent(this, "vInteractionChange", 7);
    this.keyboardDisposal = new Disposal.Disposal();
    this.showTapHighlight = false;
    /**
     * Whether the control should be displayed or not.
     */
    this.hidden = false;
    /**
     * @internal
     */
    this.isTouch = false;
    PlayerContext.withPlayerContext(this, ['isTouch']);
  }
  onKeysChange() {
    this.keyboardDisposal.empty();
    if (dom.isUndefined(this.keys))
      return;
    const player = PlayerContext.findRootPlayer(this);
    const codes = this.keys.split('/');
    this.keyboardDisposal.add(dom.listen(player, 'keydown', (event) => {
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
    if (!dom.isNull(tooltip))
      this.describedBy = tooltip.id;
    return tooltip;
  }
  onShowTooltip() {
    const tooltip = this.findTooltip();
    if (!dom.isNull(tooltip))
      tooltip.active = true;
    this.vInteractionChange.emit(true);
  }
  onHideTooltip() {
    const tooltip = this.findTooltip();
    if (!dom.isNull(tooltip))
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
    return (index.h(index.Host, { class: {
        hidden: this.hidden,
      } }, index.h("button", { class: {
        notTouch: !this.isTouch,
        tapHighlight: this.showTapHighlight,
      }, id: this.identifier, type: "button", "aria-label": this.label, "aria-haspopup": !dom.isUndefined(this.menu) ? 'true' : undefined, "aria-controls": this.menu, "aria-expanded": !dom.isUndefined(this.menu) ? (this.expanded ? 'true' : 'false') : undefined, "aria-pressed": !dom.isUndefined(this.pressed) ? (this.pressed ? 'true' : 'false') : undefined, "aria-hidden": this.hidden ? 'true' : 'false', "aria-describedby": this.describedBy, onTouchStart: this.onTouchStart.bind(this), onFocus: this.onFocus.bind(this), onBlur: this.onBlur.bind(this), onMouseEnter: this.onMouseEnter.bind(this), onMouseLeave: this.onMouseLeave.bind(this), ref: (el) => { this.button = el; } }, index.h("slot", null))));
  }
  get el() { return index.getElement(this); }
  static get watchers() { return {
    "keys": ["onKeysChange"]
  }; }
};
Control.style = controlCss;

const iconCss = "vime-icon>svg{display:block;color:var(--vm-icon-color);width:var(--vm-icon-width, 18px);height:var(--vm-icon-height, 18px);transform:scale(var(--vm-icon-scale, 1));fill:currentColor;pointer-events:none}";

const Icon = class {
  constructor(hostRef) {
    index.registerInstance(this, hostRef);
  }
  render() {
    return (index.h("svg", { xmlns: "http://www.w3.org/2000/svg", role: "presentation", focusable: "false" }, dom.isString(this.href) ? index.h("use", { href: this.href }) : index.h("slot", null)));
  }
};
Icon.style = iconCss;

const tooltipCss = "vime-tooltip{left:50%;transform:translateX(-50%);line-height:1.3;pointer-events:none;position:absolute;opacity:0;white-space:nowrap;visibility:hidden;background:var(--vm-tooltip-bg);border-radius:var(--vm-tooltip-border-radius);box-shadow:var(--vm-tooltip-box-shadow);color:var(--vm-tooltip-color);font-size:var(--vm-tooltip-font-size);padding:var(--vm-tooltip-padding);transition:opacity var(--vm-tooltip-fade-duration) var(--vm-tooltip-fade-timing-func);z-index:var(--vm-tooltip-z-index)}vime-tooltip[aria-hidden=false]{opacity:1;visibility:visible}vime-tooltip.hidden{display:none}vime-tooltip.onTop{bottom:100%;margin-bottom:var(--vm-tooltip-spacing)}vime-tooltip.onBottom{top:100%;margin-top:var(--vm-tooltip-spacing)}vime-tooltip.growLeft{left:auto;right:0;transform:none}vime-tooltip.growRight{left:0;transform:none}";

let tooltipIdCount = 0;
const Tooltip = class {
  constructor(hostRef) {
    index.registerInstance(this, hostRef);
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
    PlayerContext.withPlayerContext(this, ['isTouch']);
  }
  componentDidLoad() {
    this.hasLoaded = true;
  }
  getId() {
    // eslint-disable-next-line prefer-destructuring
    const id = this.el.id;
    if (dom.isString(id) && id.length > 0)
      return id;
    tooltipIdCount += 1;
    return `vime-tooltip-${tooltipIdCount}`;
  }
  render() {
    return (index.h(index.Host, { id: this.getId(), role: "tooltip", "aria-hidden": (!this.active || this.isTouch) ? 'true' : 'false', class: {
        hidden: !this.hasLoaded || this.hidden,
        onTop: (this.position === 'top'),
        onBottom: (this.position === 'bottom'),
        growLeft: (this.direction === 'left'),
        growRight: (this.direction === 'right'),
      } }, index.h("slot", null)));
  }
  get el() { return index.getElement(this); }
};
Tooltip.style = tooltipCss;

exports.vime_control = Control;
exports.vime_icon = Icon;
exports.vime_tooltip = Tooltip;
