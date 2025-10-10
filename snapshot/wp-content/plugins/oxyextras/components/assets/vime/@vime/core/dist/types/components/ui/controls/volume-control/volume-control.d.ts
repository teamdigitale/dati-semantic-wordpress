import { PlayerProps } from '../../../core/player/PlayerProps';
import { TooltipDirection, TooltipPosition } from '../../tooltip/types';
export declare class VolumeControl {
  private dispatch;
  private keyboardDisposal;
  private prevMuted;
  private hideSliderTimeout?;
  currentVolume: number;
  isSliderActive: boolean;
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
   * Whether the tooltip should be hidden.
   */
  hideTooltip: boolean;
  /**
   * A pipe (`/`) separated string of JS keyboard keys, that when caught in a `keydown` event, will
   * toggle the muted state of the player.
   */
  muteKeys?: string;
  /**
   * Prevents the volume being changed using the Up/Down arrow keys.
   */
  noKeyboard: boolean;
  onNoKeyboardChange(): void;
  /**
   * @internal
   */
  muted: PlayerProps['muted'];
  /**
   * @internal
   */
  volume: PlayerProps['volume'];
  onPlayerVolumeChange(): void;
  /**
   * @internal
   */
  isMobile: PlayerProps['isMobile'];
  /**
   * @internal
   */
  i18n: PlayerProps['i18n'];
  constructor();
  connectedCallback(): void;
  disconnectedCallback(): void;
  private onShowSlider;
  private onHideSlider;
  private onVolumeChange;
  private onKeyDown;
  render(): any;
}
