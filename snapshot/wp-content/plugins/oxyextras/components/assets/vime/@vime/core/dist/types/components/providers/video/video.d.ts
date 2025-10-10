import { MediaFileProvider, MediaPreloadOption, MediaCrossOriginOption } from '../file/MediaFileProvider';
/**
 * @slot - Pass `<source>` and `<track>` elements to the underlying HTML5 media player.
 */
export declare class Video implements MediaFileProvider<HTMLMediaElement> {
  private fileProvider;
  /**
   * @internal Whether an external SDK will attach itself to the media player and control it.
   */
  willAttach: boolean;
  /**
   * @inheritdoc
   */
  crossOrigin?: MediaCrossOriginOption;
  /**
   * @inheritdoc
   */
  preload?: MediaPreloadOption;
  /**
   * @inheritdoc
   */
  poster?: string;
  /**
   * @inheritdoc
   */
  controlsList?: string;
  /**
   * @inheritdoc
   */
  autoPiP?: boolean;
  /**
   * @inheritdoc
   */
  disablePiP?: boolean;
  /**
   * @inheritdoc
   */
  disableRemotePlayback?: boolean;
  /**
   * The title of the current media.
   */
  mediaTitle?: string;
  constructor();
  /**
   * @internal
   */
  getAdapter(): Promise<{
    getInternalPlayer: () => Promise<HTMLMediaElement>;
    play: () => Promise<void | undefined>;
    pause: () => Promise<void | undefined>;
    canPlay: (type: any) => Promise<boolean>;
    setCurrentTime: (time: number) => Promise<void>;
    setMuted: (muted: boolean) => Promise<void>;
    setVolume: (volume: number) => Promise<void>;
    canSetPlaybackRate: () => Promise<boolean>;
    setPlaybackRate: (rate: number) => Promise<void>;
    canSetPlaybackQuality: () => Promise<boolean>;
    setPlaybackQuality: (quality: string) => Promise<void>;
    canSetPiP: () => Promise<boolean>;
    enterPiP: () => Promise<any>;
    exitPiP: () => Promise<any>;
    canSetFullscreen: () => Promise<boolean>;
    enterFullscreen: () => Promise<any>;
    exitFullscreen: () => Promise<any>;
  }>;
  render(): any;
}
