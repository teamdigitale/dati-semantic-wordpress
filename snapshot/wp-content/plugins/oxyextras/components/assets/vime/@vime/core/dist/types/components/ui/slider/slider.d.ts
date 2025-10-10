import { EventEmitter } from '../../../stencil-public-runtime';
export declare class Slider {
  el: HTMLVimeSliderElement;
  /**
   * A number that specifies the granularity that the value must adhere to.
   */
  step: number;
  /**
   * The lowest value in the range of permitted values.
   */
  min: number;
  /**
   * The greatest permitted value.
   */
  max: number;
  /**
   * The current value.
   */
  value: number;
  /**
   * Human-readable text alternative for the current value. Defaults to `value:max` percentage.
   */
  valueText?: string;
  /**
   * A human-readable label for the purpose of the slider.
   */
  label?: string;
  /**
   * Emitted when the value of the underlying `input` field changes.
   */
  vValueChange: EventEmitter<number>;
  private getPercentage;
  private onValueChange;
  private calcTouchedValue;
  /**
   * Basically input[range="type"] on touch devices sucks (particularly iOS), so this helps make it
   * better.
   *
   * @see https://github.com/sampotts/rangetouch
   */
  private onTouch;
  render(): any;
}
