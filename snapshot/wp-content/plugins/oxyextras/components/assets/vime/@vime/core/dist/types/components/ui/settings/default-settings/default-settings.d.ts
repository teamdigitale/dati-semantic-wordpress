import { PlayerProps } from '../../../core/player/PlayerProps';
/**
 * @slot - Used to extend the settings with additional menu options (see `vime-submenu` or
 * `vime-menu-item`).
 */
export declare class DefaultSettings {
  private textTracksDisposal;
  private dispatch;
  private player?;
  private rateSubmenu;
  private qualitySubmenu;
  private captionsSubmenu;
  /**
   * Pins the settings to the defined position inside the video player. This has no effect when
   * the view is of type `audio`, it will always be `bottomRight`.
   */
  pin: 'topLeft' | 'topRight' | 'bottomLeft' | 'bottomRight';
  /**
   * @internal
   */
  i18n: PlayerProps['i18n'];
  /**
   * @internal
   */
  playbackReady: PlayerProps['playbackReady'];
  /**
   * @internal
   */
  playbackRate: PlayerProps['playbackRate'];
  /**
   * @internal
   */
  playbackRates: PlayerProps['playbackRates'];
  /**
   * @internal
   */
  playbackQuality?: PlayerProps['playbackQuality'];
  /**
   * @internal
   */
  playbackQualities: PlayerProps['playbackQualities'];
  /**
   * @internal
   */
  isCaptionsActive: PlayerProps['isCaptionsActive'];
  /**
   * @internal
   */
  currentCaption?: PlayerProps['currentCaption'];
  /**
   * @internal
   */
  textTracks?: PlayerProps['textTracks'];
  onTextTracksChange(): void;
  constructor();
  connectedCallback(): void;
  componentWillRender(): Promise<[void, void, void]> | undefined;
  disconnectedCallback(): void;
  private onPlaybackRateSelect;
  private buildPlaybackRateSubmenu;
  private onPlaybackQualitySelect;
  private buildPlaybackQualitySubmenu;
  private onCaptionSelect;
  private buildCaptionsSubmenu;
  render(): any;
}
