'use strict';

Object.defineProperty(exports, '__esModule', { value: true });

const index = require('./index-e8963331.js');

const sliderCss = "vime-slider input{-webkit-appearance:none;background:transparent;border:0;outline:0;cursor:pointer;border-radius:calc(var(--vm-slider-thumb-height) * 2);user-select:none;-webkit-user-select:none;touch-action:manipulation;color:var(--vm-slider-value-color);display:block;height:var(--vm-slider-track-height);margin:0;padding:0;transition:box-shadow 0.3s ease;width:100%}vime-slider input::-webkit-slider-runnable-track{background:transparent;border:0;border-radius:calc(var(--vm-slider-track-height) / 2);height:var(--vm-slider-track-height);transition:box-shadow 0.3s ease;user-select:none;background-image:linear-gradient(to right, currentColor var(--vm-value, 0%), transparent var(--vm-value, 0%));background-color:var(--vm-slider-track-color)}vime-slider input::-webkit-slider-thumb{opacity:0;background:var(--vm-slider-thumb-bg);border:0;border-radius:100%;position:relative;transition:all 0.2s ease;width:var(--vm-slider-thumb-width);height:var(--vm-slider-thumb-height);box-shadow:var(--vm-slider-thumb-shadow);-webkit-appearance:none;margin-top:calc(0px - calc(calc(var(--vm-slider-thumb-height) - var(--vm-slider-track-height)) / 2))}vime-slider input::-moz-range-track{background:transparent;border:0;border-radius:calc(var(--vm-slider-track-height) / 2);height:var(--vm-slider-track-height);transition:box-shadow 0.3s ease;user-select:none;background-color:var(--vm-slider-track-color)}vime-slider input::-moz-range-thumb{opacity:0;background:var(--vm-slider-thumb-bg);border:0;border-radius:100%;position:relative;transition:all 0.2s ease;width:var(--vm-slider-thumb-width);height:var(--vm-slider-thumb-height);box-shadow:var(--vm-slider-thumb-shadow)}vime-slider input::-moz-range-progress{background:currentColor;border-radius:calc(var(--vm-slider-track-height) / 2);height:var(--vm-slider-track-height)}vime-slider input::-ms-track{background:transparent;border:0;border-radius:calc(var(--vm-slider-track-height) / 2);height:var(--vm-slider-track-height);transition:box-shadow 0.3s ease;user-select:none;color:transparent;background-color:var(--vm-slider-track-color)}vime-slider input::-ms-fill-upper{background:transparent;border:0;border-radius:calc(var(--vm-slider-track-height) / 2);height:var(--vm-slider-track-height);transition:box-shadow 0.3s ease;user-select:none}vime-slider input::-ms-fill-lower{background:transparent;border:0;border-radius:calc(var(--vm-slider-track-height) / 2);height:var(--vm-slider-track-height);transition:box-shadow 0.3s ease;user-select:none;background:currentColor}vime-slider input::-ms-thumb{opacity:0;background:var(--vm-slider-thumb-bg);border:0;border-radius:100%;position:relative;transition:all 0.2s ease;width:var(--vm-slider-thumb-width);height:var(--vm-slider-thumb-height);box-shadow:var(--vm-slider-thumb-shadow);margin-top:0}vime-slider input::-ms-tooltip{display:none}vime-slider input:hover::-webkit-slider-runnable-track{height:var(--vm-slider-track-focused-height)}vime-slider input:hover::-moz-range-track{height:var(--vm-slider-track-focused-height)}vime-slider input:hover::-ms-track{height:var(--vm-slider-track-focused-height)}vime-slider input:hover::-ms-fill-upper{height:var(--vm-slider-track-focused-height)}vime-slider input:hover::-ms-fill-lower{height:var(--vm-slider-track-focused-height)}vime-slider input:hover::-webkit-slider-thumb{opacity:1}vime-slider input:hover::-moz-range-thumb{opacity:1}vime-slider input:hover::-ms-thumb{opacity:1}vime-slider input:focus{outline:0}vime-slider input:focus::-webkit-slider-runnable-track{outline:0;height:var(--vm-slider-track-focused-height)}vime-slider input:focus::-moz-range-track{outline:0;height:var(--vm-slider-track-focused-height)}vime-slider input:focus::-ms-track{outline:0;height:var(--vm-slider-track-focused-height)}vime-slider input::-moz-focus-outer{border:0}";

const Slider = class {
  constructor(hostRef) {
    index.registerInstance(this, hostRef);
    this.vValueChange = index.createEvent(this, "vValueChange", 7);
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
    return (index.h(index.Host, { style: {
        '--vm-value': this.getPercentage(),
      } }, index.h("input", { type: "range", step: this.step, min: this.min, max: this.max, value: this.value, autocomplete: "off", "aria-label": this.label, "aria-valuemin": this.min, "aria-valuemax": this.max, "aria-valuenow": this.value, "aria-valuetext": (_a = this.valueText) !== null && _a !== void 0 ? _a : this.getPercentage(), "aria-orientation": "horizontal", onInput: this.onValueChange.bind(this), onFocus: () => { this.el.dispatchEvent(new window.Event('focus', { bubbles: true })); }, onBlur: () => { this.el.dispatchEvent(new window.Event('blur', { bubbles: true })); }, onTouchStart: this.onTouch.bind(this), onTouchMove: this.onTouch.bind(this), onTouchEnd: this.onTouch.bind(this) })));
  }
  get el() { return index.getElement(this); }
};
Slider.style = sliderCss;

exports.vime_slider = Slider;
