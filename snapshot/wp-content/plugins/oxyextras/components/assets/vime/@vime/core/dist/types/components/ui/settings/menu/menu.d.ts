import { EventEmitter } from '../../../../stencil-public-runtime';
/**
 * @slot - Used to pass in the body of the menu which usually contains menu items, radio groups
 * and/or submenus.
 */
export declare class Menu {
  private shouldFocusOnOpen;
  private submenus;
  el: HTMLVimeMenuElement;
  menuItems: NodeListOf<HTMLVimeMenuItemElement>;
  onMenuItemsChange(): void;
  currFocusedMenuItem: number;
  onFocusedMenuItemChange(): Promise<void>;
  /**
   * Whether the menu is open/visible.
   */
  active: boolean;
  onActiveChange(): void;
  /**
   * The `id` attribute of the menu.
   */
  identifier: string;
  /**
   * The `id` attribute value of the control responsible for opening/closing this menu.
   */
  controller: string;
  /**
   * Emitted when the menu is open/active.
   */
  vOpen: EventEmitter<void>;
  /**
   * Emitted when the menu has closed/is not active.
   */
  vClose: EventEmitter<void>;
  /**
   * Emitted when the menu items present changes.
   */
  vMenuItemsChange: EventEmitter<NodeListOf<HTMLVimeMenuItemElement> | undefined>;
  /**
   * Emitted when the currently focused menu item changes.
   */
  vFocusMenuItemChange: EventEmitter<HTMLVimeMenuItemElement | undefined>;
  connectedCallback(): void;
  componentDidRender(): void;
  /**
   * Returns the controller responsible for opening/closing this menu.
   */
  getController(): Promise<HTMLElement>;
  /**
   * Returns the currently focused menu item.
   */
  getFocusedMenuItem(): Promise<HTMLVimeMenuItemElement>;
  /**
   * This should be called directly before opening the menu to set the keyboard focus on it. This
   * is a one-time operation and needs to be called everytime prior to opening the menu.
   */
  focusOnOpen(): Promise<void>;
  private findMenuItems;
  private focusController;
  private focusMenuItem;
  private openSubmenu;
  private onOpen;
  private onClose;
  private onClick;
  private onKeyDown;
  private findSubmenus;
  private isValidSubmenu;
  private toggleSubmenu;
  onSubmenuOpen(event: CustomEvent<void>): void;
  onSubmenuClose(event: CustomEvent<void>): void;
  onWindowClick(): void;
  onWindowKeyDown(event: KeyboardEvent): void;
  render(): any;
}
