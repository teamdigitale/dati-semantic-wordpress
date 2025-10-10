import { EventEmitter } from '../../../../stencil-public-runtime';
/**
 * @slot - Used to pass in radio buttons (`vime-menu-radio`).
 */
export declare class MenuRadioGroup {
  el: HTMLVimeMenuRadioGroupElement;
  /**
   * The current value selected for this group.
   */
  value?: string;
  onValueChange(): void;
  /**
   * Emitted when a new radio button is selected for this group.
   */
  vCheck: EventEmitter<void>;
  connectedCallback(): void;
  onSelectionChange(event: Event): void;
  private findRadios;
  render(): any;
}
