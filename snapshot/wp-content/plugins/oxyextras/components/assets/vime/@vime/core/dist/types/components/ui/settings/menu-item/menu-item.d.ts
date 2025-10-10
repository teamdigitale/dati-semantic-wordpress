import { PlayerProps } from '../../../core/player/PlayerProps';
export declare class MenuItem {
  el: HTMLVimeMenuItemElement;
  showTapHighlight: boolean;
  /**
   * The `id` attribute of the item.
   */
  identifier?: string;
  /**
   * Whether the item is displayed or not.
   */
  hidden: boolean;
  /**
   * The label/title of the item.
   */
  label: string;
  /**
   * If the item has a popup menu, then this should be the `id` of said menu. Sets the
   * `aria-controls` property.
   */
  menu?: string;
  /**
   * If the item has a popup menu, this indicates whether the menu is open or not. Sets the
   * `aria-expanded` property.
   */
  expanded?: boolean;
  /**
   * If this item is to behave as a radio button, then this property determines whether the
   * radio is selected or not. Sets the `aria-checked` property.
   */
  checked?: boolean;
  /**
   * This can provide additional context about some underlying state of the item. For example, if
   * the menu item opens/closes a submenu with options, the hint could be the currently selected
   * option.
   */
  hint?: string;
  /**
   * This can provide additional context about the value of a menu item. For example, if the item
   * is a radio button for a set of video qualities, the badge could describe whether the quality
   * is UHD, HD etc.
   */
  badge?: string;
  /**
   * The URL to an SVG element or fragment to load.
   */
  checkedIcon?: string;
  /**
   * @internal
   */
  isTouch: PlayerProps['isTouch'];
  constructor();
  private onClick;
  private onTouchStart;
  private onMouseLeave;
  render(): any;
}
