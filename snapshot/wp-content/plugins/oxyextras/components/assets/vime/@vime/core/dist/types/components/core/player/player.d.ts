import { EventEmitter } from '../../../stencil-public-runtime';
import { MediaType } from './MediaType';
import { AdapterHost, MediaProviderAdapter } from '../../providers/MediaProvider';
import { Provider } from '../../providers/Provider';
import { MediaPlayer } from './MediaPlayer';
import { PlayerProps, ProviderWritableProps } from './PlayerProps';
import { ViewType } from './ViewType';
import { StateChange } from './PlayerDispatcher';
import { Logger } from './PlayerLogger';
import { Translation } from './lang/Translation';
/**
 * @slot - Used to pass in providers, plugins and UI components.
 */
export declare class Player implements MediaPlayer {
  private provider?;
  private adapter?;
  private fullscreen;
  private autopauseMgr;
  private disposal;
  private toggledCaption?;
  private cache;
  private providerCache;
  private adapterCalls;
  el: HTMLVimePlayerElement;
  shouldCheckForProviderChange: boolean;
  /**
   * ------------------------------------------------------
   * Props
   * ------------------------------------------------------
   */
  /**
   * @inheritDoc
   */
  attached: boolean;
  /**
   * @internal
   */
  logger: Logger;
  /**
   * @inheritDoc
   */
  theme?: string;
  /**
   * @inheritDoc
   */
  paused: boolean;
  onPausedChange(): void;
  /**
   * @inheritDoc
   */
  playing: boolean;
  /**
   * @inheritDoc
   */
  duration: number;
  onDurationChange(): void;
  /**
   * @inheritDoc
   */
  mediaTitle?: string;
  /**
   * @inheritDoc
   */
  currentProvider?: Provider;
  /**
   * @inheritDoc
   */
  currentSrc?: string;
  /**
   * @inheritDoc
   */
  currentPoster?: string;
  /**
   * @inheritDoc
   */
  currentTime: number;
  onCurrentTimeChange(): void;
  /**
   * @inheritDoc
   */
  autoplay: boolean;
  /**
   * @inheritDoc
   */
  ready: boolean;
  /**
   * @inheritDoc
   */
  playbackReady: boolean;
  onPlaybackReadyChange(): void;
  /**
   * @inheritDoc
   */
  loop: boolean;
  /**
   * @inheritDoc
   */
  muted: boolean;
  onMutedChange(): void;
  /**
   * @inheritDoc
   */
  buffered: number;
  /**
   * @inheritDoc
   */
  playbackRate: number;
  private lastRateCheck;
  onPlaybackRateChange(newRate: number, prevRate: number): Promise<void>;
  /**
   * @inheritDoc
   */
  playbackRates: number[];
  /**
   * @inheritDoc
   */
  playbackQuality?: string;
  private lastQualityCheck?;
  onPlaybackQualityChange(newQuality: string, prevQuality: string): Promise<void>;
  /**
   * @inheritDoc
   */
  playbackQualities: string[];
  /**
   * @inheritDoc
   */
  seeking: boolean;
  /**
   * @inheritDoc
   */
  debug: boolean;
  onDebugChange(): void;
  /**
   * @inheritDoc
   */
  playbackStarted: boolean;
  /**
   * @inheritDoc
   */
  playbackEnded: boolean;
  /**
   * @inheritDoc
   */
  buffering: boolean;
  /**
   * @inheritDoc
   */
  controls: boolean;
  /**
   * @inheritDoc
   */
  isControlsActive: boolean;
  /**
   * @inheritDoc
   */
  errors: any[];
  onErrorsChange(_: any[], prevErrors: any[]): void;
  /**
   * @inheritDoc
   */
  textTracks?: TextTrackList;
  /**
   * @inheritDoc
   */
  currentCaption?: TextTrack;
  /**
   * @inheritDoc
   */
  isCaptionsActive: boolean;
  private textTracksChangeListener?;
  onTextTracksChange(): void;
  /**
   * @inheritDoc
   */
  isSettingsActive: boolean;
  /**
   * @inheritDoc
   */
  volume: number;
  onVolumeChange(): Promise<void>;
  /**
   * @inheritDoc
   */
  isFullscreenActive: boolean;
  /**
   * @inheritDoc
   */
  aspectRatio: string;
  /**
   * @inheritDoc
   */
  viewType?: ViewType;
  onViewTypeChange(): void;
  /**
   * @inheritDoc
   */
  isAudioView: boolean;
  /**
   * @inheritDoc
   */
  isVideoView: boolean;
  /**
   * @inheritDoc
   */
  mediaType?: MediaType;
  onMediaTypeChange(): void;
  /**
   * @inheritDoc
   */
  isAudio: boolean;
  /**
   * @inheritDoc
   */
  isVideo: boolean;
  /**
   * @inheritDoc
   */
  isLive: boolean;
  /**
   * @inheritDoc
   */
  isMobile: boolean;
  /**
   * @inheritDoc
   */
  isTouch: boolean;
  /**
   * @inheritDoc
   */
  isPiPActive: boolean;
  /**
   * @inheritDoc
   */
  autopause: boolean;
  /**
   * @inheritDoc
   */
  playsinline: boolean;
  /**
   * @inheritDoc
   */
  language: string;
  onLanguageChange(_: string, prevLanguage: string): void;
  /**
   * @inheritDoc
   */
  translations: Record<string, Translation>;
  onTranslationsChange(): void;
  /**
   * @inheritDoc
   */
  languages: string[];
  /**
   * @inheritDoc
   */
  i18n: Translation;
  /**
   * ------------------------------------------------------
   * Events
   * ------------------------------------------------------
   */
  /**
   * @inheritDoc
   */
  vThemeChange: EventEmitter<PlayerProps['theme']>;
  /**
   * @inheritDoc
   */
  vPausedChange: EventEmitter<PlayerProps['paused']>;
  /**
   * @inheritDoc
   */
  vPlay: EventEmitter<void>;
  /**
   * @inheritDoc
   */
  vPlayingChange: EventEmitter<PlayerProps['playing']>;
  /**
   * @inheritDoc
   */
  vSeekingChange: EventEmitter<PlayerProps['seeking']>;
  /**
   * @inheritDoc
   */
  vSeeked: EventEmitter<void>;
  /**
   * @inheritDoc
   */
  vBufferingChange: EventEmitter<PlayerProps['buffering']>;
  /**
   * @inheritDoc
   */
  vDurationChange: EventEmitter<PlayerProps['duration']>;
  /**
   * @inheritDoc
   */
  vCurrentTimeChange: EventEmitter<PlayerProps['currentTime']>;
  /**
   * @inheritDoc
   */
  vAttachedChange: EventEmitter<void>;
  /**
   * @inheritDoc
   */
  vReady: EventEmitter<void>;
  /**
   * @inheritDoc
   */
  vPlaybackReady: EventEmitter<void>;
  /**
   * @inheritDoc
   */
  vPlaybackStarted: EventEmitter<void>;
  /**
   * @inheritDoc
   */
  vPlaybackEnded: EventEmitter<void>;
  /**
   * @inheritDoc
   */
  vBufferedChange: EventEmitter<PlayerProps['buffered']>;
  /**
   * @inheritdoc
   */
  vCurrentCaptionChange: EventEmitter<PlayerProps['currentCaption']>;
  /**
   * @inheritDoc
   */
  vTextTracksChange: EventEmitter<PlayerProps['textTracks']>;
  /**
   * @inheritDoc
   */
  vErrorsChange: EventEmitter<PlayerProps['errors']>;
  /**
   * @inheritDoc
   */
  vLoadStart: EventEmitter<void>;
  /**
   * @inheritDoc
   */
  vCurrentProviderChange: EventEmitter<PlayerProps['currentProvider']>;
  /**
   * @inheritDoc
   */
  vCurrentSrcChange: EventEmitter<PlayerProps['currentSrc']>;
  /**
   * @inheritDoc
   */
  vCurrentPosterChange: EventEmitter<PlayerProps['currentPoster']>;
  /**
   * @inheritDoc
   */
  vMediaTitleChange: EventEmitter<PlayerProps['mediaTitle']>;
  /**
   * @inheritDoc
   */
  vControlsChange: EventEmitter<PlayerProps['isControlsActive']>;
  /**
   * @inheritDoc
   */
  vPlaybackRateChange: EventEmitter<PlayerProps['playbackRate']>;
  /**
   * @inheritDoc
   */
  vPlaybackRatesChange: EventEmitter<PlayerProps['playbackRates']>;
  /**
   * @inheritDoc
   */
  vPlaybackQualityChange: EventEmitter<PlayerProps['playbackQuality']>;
  /**
   * @inheritDoc
   */
  vPlaybackQualitiesChange: EventEmitter<PlayerProps['playbackQualities']>;
  /**
   * @inheritDoc
   */
  vMutedChange: EventEmitter<PlayerProps['muted']>;
  /**
   * @inheritDoc
   */
  vVolumeChange: EventEmitter<PlayerProps['volume']>;
  /**
   * @inheritDoc
   */
  vViewTypeChange: EventEmitter<PlayerProps['viewType']>;
  /**
   * @inheritDoc
   */
  vMediaTypeChange: EventEmitter<PlayerProps['mediaType']>;
  /**
   * @inheritDoc
   */
  vLiveChange: EventEmitter<PlayerProps['isLive']>;
  /**
   * @inheritDoc
   */
  vTouchChange: EventEmitter<PlayerProps['isTouch']>;
  /**
   * @inheritDoc
   */
  vLanguageChange: EventEmitter<PlayerProps['language']>;
  /**
   * @inheritdoc
   */
  vI18nChange: EventEmitter<PlayerProps['i18n']>;
  /**
   * @inheritdoc
   */
  vTranslationsChange: EventEmitter<PlayerProps['translations']>;
  /**
   * @inheritDoc
   */
  vLanguagesChange: EventEmitter<PlayerProps['languages']>;
  /**
   * @inheritDoc
   */
  vFullscreenChange: EventEmitter<PlayerProps['isFullscreenActive']>;
  /**
   * @inheritDoc
   */
  vPiPChange: EventEmitter<PlayerProps['isPiPActive']>;
  /**
   * ------------------------------------------------------
   * Methods
   * ------------------------------------------------------
   */
  /**
   * @inheritDoc
   */
  getProvider<InternalPlayerType = any>(): Promise<AdapterHost<InternalPlayerType> | undefined>;
  /**
   * @internal testing only.
   */
  setProvider(provider: AdapterHost): Promise<void>;
  /**
   * @internal
   */
  getAdapter<InternalPlayerType = any>(): Promise<MediaProviderAdapter<InternalPlayerType> | undefined>;
  /**
   * @inheritDoc
   */
  play(): Promise<void | undefined>;
  /**
   * @inheritDoc
   */
  pause(): Promise<void | undefined>;
  /**
   * @inheritDoc
   */
  canPlay(type: string): Promise<boolean>;
  /**
   * @inheritDoc
   */
  canAutoplay(): Promise<boolean>;
  /**
   * @inheritDoc
   */
  canMutedAutoplay(): Promise<boolean>;
  /**
   * @inheritDoc
   */
  canSetPlaybackRate(): Promise<boolean>;
  /**
   * @inheritDoc
   */
  canSetPlaybackQuality(): Promise<boolean>;
  /**
   * @inheritDoc
   */
  canSetFullscreen(): Promise<boolean>;
  /**
   * @inheritDoc
   */
  enterFullscreen(options?: FullscreenOptions): Promise<any>;
  /**
   * @inheritDoc
   */
  exitFullscreen(): Promise<any>;
  /**
   * @inheritDoc
   */
  canSetPiP(): Promise<boolean>;
  /**
   * @inheritDoc
   */
  enterPiP(): Promise<void | undefined>;
  /**
   * @inheritDoc
   */
  exitPiP(): Promise<void | undefined>;
  /**
   * @inheritDoc
   */
  extendLanguage(language: string, translation: Partial<Translation>): Promise<void>;
  onMediaProviderConnect(event: CustomEvent<AdapterHost>): void;
  onMediaProviderDisconnect(event?: Event): void;
  private hasMediaChanged;
  onMediaChange(event?: Event): Promise<void>;
  onStateChange(event: CustomEvent<StateChange>): Promise<void>;
  onProviderChange(event: CustomEvent<StateChange<ProviderWritableProps>>): void;
  connectedCallback(): void;
  componentWillLoad(): void;
  componentWillRender(): Promise<void> | undefined;
  componentDidRender(): Promise<void>;
  disconnectedCallback(): void;
  private rotateDevice;
  private getPlayerState;
  private calcAspectRatio;
  /**
   * @internal Exposed for E2E testing.
   */
  callAdapter(method: keyof MediaProviderAdapter, value?: any): Promise<any>;
  /**
   * @inheritdoc
   */
  toggleCaptionsVisibility(isVisible?: boolean): Promise<void>;
  private hasCustomControls;
  private hasCustomCaptions;
  private getActiveCaption;
  private onActiveCaptionChange;
  private isAdapterCallRequired;
  private safeAdapterCall;
  private flushAdapterCalls;
  private fireEvent;
  private genId;
  render(): any;
}
