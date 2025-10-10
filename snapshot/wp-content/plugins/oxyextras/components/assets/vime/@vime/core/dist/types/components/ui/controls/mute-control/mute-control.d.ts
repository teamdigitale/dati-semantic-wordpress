import { PlayerProps } from '../../../core/player/PlayerProps';
import { TooltipDirection, TooltipPosition } from '../../tooltip/types';
import { KeyboardControl } from '../control/KeyboardControl';
export declare class MuteControl implements KeyboardControl {
  private dispatch;
  /**
   * The URL to an SVG element or fragment.
   */
  lowVolumeIcon: string;
  /**
   * The URL to an SVG element or fragment.
   */
  highVolumeIcon: string;
  /**
   * The URL to an SVG element or fragment.
   */
  mutedIcon: string;
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
  volume: PlayerProps['volume'];
  /**
   * @internal
   */
  muted: PlayerProps['muted'];
  /**
   * @internal
   */
  i18n: PlayerProps['i18n'];
  constructor();
  connectedCallback(): void;
  private getIcon;
  private onClick;
  render(): any;
}
