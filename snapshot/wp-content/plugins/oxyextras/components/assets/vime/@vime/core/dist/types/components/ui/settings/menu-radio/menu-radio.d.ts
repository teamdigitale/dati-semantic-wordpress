import { EventEmitter } from '../../../../stencil-public-runtime';
export declare class MenuRadio {
  /**
   * The title of the radio item displayed to the user.
   */
  label: string;
  /**
   * The value associated with this radio item.
   */
  value: string;
  /**
   * Whether the radio item is selected or not.
   */
  checked: boolean;
  /**
   * This can provide additional context about the value. For example, if the option is for a set of
   * video qualities, the badge could describe whether the quality is UHD, HD etc.
   */
  badge?: string;
  /**
   * The URL to an SVG element or fragment to load.
   */
  checkedIcon?: string;
  /**
   * Emitted when the radio button is selected.
   */
  vCheck: EventEmitter<void>;
  private onClick;
  render(): any;
}
