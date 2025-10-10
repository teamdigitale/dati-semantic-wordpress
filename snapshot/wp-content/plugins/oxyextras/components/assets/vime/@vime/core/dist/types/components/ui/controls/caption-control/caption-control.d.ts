import { PlayerProps } from '../../../core/player/PlayerProps';
import { TooltipDirection, TooltipPosition } from '../../tooltip/types';
import { KeyboardControl } from '../control/KeyboardControl';
export declare class CaptionControl implements KeyboardControl {
  /**
   * The URL to an SVG element or fragment to load.
   */
  showIcon: string;
  /**
   * The URL to an SVG element or fragment to load.
   */
  hideIcon: string;
  /**
   * Whether the tooltip is positioned above/below the control.
   */
  tooltipPosition: TooltipPosition;
  /**
   * The direction in which the tooltip should grow.
   */
  tooltipDirection: TooltipDirection;
  /**
   * Whether the tooltip should not be displayed.
   */
  hideTooltip: boolean;
  /**
   * @inheritdoc
   */
  keys?: string;
  /**
   * @internal
   */
  currentCaption?: PlayerProps['currentCaption'];
  /**
   * @internal
   */
  isCaptionsActive: PlayerProps['isCaptionsActive'];
  /**
   * @internal
   */
  i18n: PlayerProps['i18n'];
  constructor();
  private onClick;
  render(): any;
}
