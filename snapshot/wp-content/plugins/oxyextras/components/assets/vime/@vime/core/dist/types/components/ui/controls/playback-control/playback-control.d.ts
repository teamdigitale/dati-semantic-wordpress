import { PlayerProps } from '../../../core/player/PlayerProps';
import { TooltipDirection, TooltipPosition } from '../../tooltip/types';
import { KeyboardControl } from '../control/KeyboardControl';
export declare class PlaybackControl implements KeyboardControl {
  private dispatch;
  /**
   * The URL to an SVG element or fragment to load.
   */
  playIcon: string;
  /**
   * The URL to an SVG element or fragment to load.
   */
  pauseIcon: string;
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
  paused: PlayerProps['paused'];
  /**
   * @internal
   */
  i18n: PlayerProps['i18n'];
  constructor();
  connectedCallback(): void;
  private onClick;
  render(): any;
}
