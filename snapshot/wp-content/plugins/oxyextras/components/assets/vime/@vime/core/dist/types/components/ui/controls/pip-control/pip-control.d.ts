import { PlayerProps } from '../../../core/player/PlayerProps';
import { TooltipDirection, TooltipPosition } from '../../tooltip/types';
import { KeyboardControl } from '../control/KeyboardControl';
export declare class PiPControl implements KeyboardControl {
  canSetPiP: boolean;
  /**
   * The URL to an SVG element or fragment to display for entering PiP.
   */
  enterIcon: string;
  /**
   * The URL to an SVG element or fragment to display for exiting PiP.
   */
  exitIcon: string;
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
  isPiPActive: PlayerProps['isPiPActive'];
  /**
   * @internal
   */
  i18n: PlayerProps['i18n'];
  /**
   * @internal
   */
  playbackReady: PlayerProps['playbackReady'];
  onPlaybackReadyChange(): Promise<void>;
  constructor();
  private onClick;
  render(): any;
}
