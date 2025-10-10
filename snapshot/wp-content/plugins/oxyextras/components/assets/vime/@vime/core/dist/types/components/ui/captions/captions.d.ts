import { EventEmitter } from '../../../stencil-public-runtime';
import { PlayerProps } from '../../core/player/PlayerProps';
export declare class Captions {
  private textTracksDisposal;
  private textTrackDisposal;
  private state;
  isEnabled: boolean;
  activeTrack?: TextTrack;
  onActiveTrackChange(): void;
  activeCues: TextTrackCue[];
  onActiveCuesChange(): void;
  /**
   * Whether the captions should be visible or not.
   */
  hidden: boolean;
  /**
   * The height of any lower control bar in pixels so that the captions can reposition when it's
   * active.
   */
  controlsHeight: number;
  /**
   * @internal
   */
  isControlsActive: PlayerProps['isControlsActive'];
  /**
   * @internal
   */
  isVideoView: PlayerProps['isVideoView'];
  /**
   * @internal
   */
  playbackStarted: PlayerProps['playbackStarted'];
  /**
   * @internal
   */
  textTracks?: PlayerProps['textTracks'];
  /**
   * Emitted when the current track changes.
   */
  vTrackChange: EventEmitter<TextTrack | undefined>;
  /**
   * Emitted when the active cues change. A cue is active when
   * `currentTime >= cue.startTime && currentTime <= cue.endTime`.
   */
  vCuesChange: EventEmitter<TextTrackCue[]>;
  constructor();
  disconnectedCallback(): void;
  private cleanup;
  private onCueChange;
  private onTrackChange;
  private findActiveTrack;
  private onTracksChange;
  onTextTracksListChange(): void;
  onEnabledChange(): void;
  private renderCurrentCue;
  render(): any;
}
