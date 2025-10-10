import { EventEmitter } from '../../../stencil-public-runtime';
import { MediaFileProvider, MediaPreloadOption, MediaCrossOriginOption } from '../file/MediaFileProvider';
export declare class Dash implements MediaFileProvider<any> {
  private dash?;
  private dispatch;
  private mediaEl?;
  private videoProvider;
  hasAttached: boolean;
  /**
   * The URL of the `manifest.mpd` file to use.
   */
  src: string;
  onSrcChange(): void;
  /**
   * The NPM package version of the `dashjs` library to download and use.
   */
  version: string;
  /**
   * The `dashjs` configuration.
   */
  config: Record<string, any>;
  /**
   * @internal
   */
  autoplay: boolean;
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
  /**
   * @internal
   */
  vLoadStart: EventEmitter<void>;
  constructor();
  connectedCallback(): void;
  disconnectedCallback(): void;
  private setupDash;
  private destroyDash;
  onMediaElChange(event: CustomEvent<HTMLVideoElement | undefined>): Promise<void>;
  /**
   * @internal
   */
  getAdapter(): Promise<{
    getInternalPlayer: () => Promise<any>;
    canPlay: (type: any) => Promise<boolean>;
    play: () => Promise<void | undefined>;
    pause: () => Promise<void | undefined>;
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
