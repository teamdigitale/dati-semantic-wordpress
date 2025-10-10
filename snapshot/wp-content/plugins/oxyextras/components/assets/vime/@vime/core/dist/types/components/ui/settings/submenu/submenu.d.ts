/**
 * @slot - Used to pass in the body of the submenu which is usually a set of choices in the form
 * of a radio group (`vime-menu-radio-group`).
 */
export declare class Submenu {
  private id;
  /**
   * The title of the submenu.
   */
  label: string;
  /**
   * Whether the submenu should be displayed or not.
   */
  hidden: boolean;
  /**
   * This can provide additional context about the current state of the submenu. For example, the
   * hint could be the currently selected option if the submenu contains a radio group.
   */
  hint?: string;
  /**
   * Whether the submenu is open/closed.
   */
  active: boolean;
  connectedCallback(): void;
  private invalidEventTarget;
  private onOpen;
  private onClose;
  private genId;
  private getControllerId;
  render(): any;
}
