import { EventEmitter } from '../../../stencil-public-runtime';
import { MediaProvider } from '../MediaProvider';
import { ViewType } from '../../core/player/ViewType';
import { MediaFileProvider, MediaPreloadOption, MediaCrossOriginOption } from './MediaFileProvider';
import { Logger } from '../../core/player/PlayerLogger';
import { MediaResource } from './MediaResource';
/**
 * @slot - Pass `<source>` and `<track>` elements to the underlying HTML5 media player.
 */
export declare class File implements MediaFileProvider<HTMLMediaElement>, MediaProvider<HTMLMediaElement> {
  private dispatch;
  private timeRAF?;
  private disposal;
  private lazyLoader?;
  private wasPausedBeforeSeeking;
  private playbackQuality?;
  private currentSrcSet;
  private prevMediaEl?;
  private mediaEl?;
  private mediaQueryDisposal;
  el: HTMLVimeFileElement;
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
   * The title of the current media.
   */
  mediaTitle?: string;
  onMediaTitleChange(): void;
  onPosterChange(): void;
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
   * Whether to use an `audio` or `video` element to play the media.
   */
  viewType?: ViewType;
  onViewTypeChange(): void;
  /**
   * The playback rates that are available for this media.
   */
  playbackRates: number[];
  /**
   * @internal
   */
  language: string;
  /**
   * @internal
   */
  autoplay: boolean;
  /**
   * @internal
   */
  controls: boolean;
  /**
   * @internal
   */
  logger?: Logger;
  /**
   * @internal
   */
  loop: boolean;
  /**
   * @internal
   */
  muted: boolean;
  /**
   * @internal
   */
  playsinline: boolean;
  /**
   * @internal
   */
  noConnect: boolean;
  /**
   * @internal
   */
  paused: boolean;
  /**
   * @internal
   */
  currentTime: number;
  /**
   * @internal
   */
  playbackStarted: boolean;
  /**
   * @internal
   */
  vLoadStart: EventEmitter<void>;
  /**
   * Emitted when the underlying media element changes.
   */
  vMediaElChange: EventEmitter<HTMLAudioElement | HTMLVideoElement | undefined>;
  /**
   * Emitted when the child `<source />` elements are modified.
   */
  vSrcSetChange: EventEmitter<MediaResource[]>;
  constructor();
  connectedCallback(): void;
  componentDidRender(): void;
  componentDidLoad(): void;
  disconnectedCallback(): void;
  private initLazyLoader;
  private refresh;
  private didSrcSetChange;
  private onSrcSetChange;
  private hasPlaybackQualities;
  private getPlaybackQualities;
  private pickInitialPlaybackQuality;
  private hasCustomPoster;
  private cancelTimeUpdates;
  private requestTimeUpdates;
  private getMediaType;
  private onLoadedMetadata;
  private onProgress;
  private onPlay;
  private onPause;
  private onPlaying;
  private onSeeking;
  private onSeeked;
  private onRateChange;
  private onVolumeChange;
  private onDurationChange;
  private onWaiting;
  private onSuspend;
  private onEnded;
  private onError;
  private attemptToPlay;
  private togglePiPInChrome;
  private togglePiPInSafari;
  private togglePiP;
  private toggleFullscreen;
  private onPresentationModeChange;
  private onEnterPiP;
  private onLeavePiP;
  private onTracksChange;
  private listenToTextTracksChanges;
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
