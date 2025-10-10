import { EventEmitter } from '../../../stencil-public-runtime';
import { PlayerProps } from '../../core/player/PlayerProps';
export declare class Spinner {
  private blacklist;
  isHidden: boolean;
  isActive: boolean;
  /**
   * @internal
   */
  isVideoView: PlayerProps['isVideoView'];
  /**
   * @internal
   */
  currentProvider?: PlayerProps['currentProvider'];
  /**
   * Emitted when the spinner will be shown.
   */
  vWillShow: EventEmitter<void>;
  /**
   * Emitted when the spinner will be hidden.
   */
  vWillHide: EventEmitter<void>;
  onVideoViewChange(): void;
  /**
   * @internal
   */
  buffering: PlayerProps['buffering'];
  onActiveChange(): void;
  constructor();
  private onVisiblityChange;
  render(): any;
}
