import { PlayerProps } from '../../core/player/PlayerProps';
export declare class ClickToPlay {
  private dispatch;
  /**
   * By default this is disabled on mobile to not interfere with playback, set this to `true` to
   * enable it.
   */
  useOnMobile: boolean;
  /**
   * @internal
   */
  paused: PlayerProps['paused'];
  /**
   * @internal
   */
  isVideoView: PlayerProps['isVideoView'];
  constructor();
  connectedCallback(): void;
  private onClick;
  render(): any;
}
