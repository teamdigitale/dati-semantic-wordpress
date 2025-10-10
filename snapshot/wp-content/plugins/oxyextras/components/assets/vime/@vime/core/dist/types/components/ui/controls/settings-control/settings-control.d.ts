import { PlayerProps } from '../../../core/player/PlayerProps';
import { TooltipDirection, TooltipPosition } from '../../tooltip/types';
export declare class SettingsControl {
  private id;
  el: HTMLVimeSettingsControlElement;
  /**
   * The URL to an SVG element or fragment to load.
   */
  icon: string;
  /**
   * Whether the tooltip is positioned above/below the control.
   */
  tooltipPosition: TooltipPosition;
  /**
   * The direction in which the tooltip should grow.
   */
  tooltipDirection: TooltipDirection;
  /**
   * The DOM `id` of the settings menu this control is responsible for opening/closing.
   */
  menu?: string;
  /**
   * Whether the settings menu this control manages is open.
   */
  expanded: boolean;
  /**
   * @internal
   */
  i18n: PlayerProps['i18n'];
  constructor();
  connectedCallback(): void;
  componentDidLoad(): void;
  private findSettings;
  render(): any;
}
