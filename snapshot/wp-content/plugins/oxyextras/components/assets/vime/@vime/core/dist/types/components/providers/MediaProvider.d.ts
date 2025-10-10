import { ComponentInterface, EventEmitter } from '../../stencil-public-runtime';
import { PlayerProp, PlayerProps } from '../core/player/PlayerProps';
export interface MediaProviderAdapter<InternalPlayerType = any> {
  getInternalPlayer(): Promise<InternalPlayerType>;
  play(): Promise<void>;
  pause(): Promise<void>;
  canPlay(type: string): Promise<boolean>;
  setCurrentTime(time: number): Promise<void>;
  setMuted(muted: boolean): Promise<void>;
  setVolume(volume: number): Promise<void>;
  canSetPlaybackRate?(): Promise<boolean>;
  setPlaybackRate?(rate: number): Promise<void>;
  canSetPlaybackQuality?(): Promise<boolean>;
  setPlaybackQuality?(quality: string): Promise<void>;
  canSetFullscreen?(): Promise<boolean>;
  enterFullscreen?(options?: FullscreenOptions): Promise<void>;
  exitFullscreen?(): Promise<void>;
  canSetPiP?(): Promise<boolean>;
  enterPiP?(): Promise<void>;
  exitPiP?(): Promise<void>;
}
export declare type MockMediaProviderAdapter = {
  [P in keyof MediaProviderAdapter]: any;
};
export interface AdapterHost<InternalPlayerType = any> extends ComponentInterface {
  getAdapter(): Promise<MediaProviderAdapter<InternalPlayerType>>;
}
export interface MediaProvider<InternalPlayerType = any> extends AdapterHost<InternalPlayerType> {
  logger?: PlayerProps['logger'];
  controls: PlayerProps['controls'];
  language: PlayerProps['language'];
  loop: PlayerProps['loop'];
  autoplay: PlayerProps['autoplay'];
  playsinline: PlayerProps['playsinline'];
  muted: PlayerProps['muted'];
  vLoadStart: EventEmitter<void>;
}
export declare function withProviderConnect(host: AdapterHost): void;
export declare const withProviderContext: (provider: MediaProvider, additionalProps?: PlayerProp[]) => void;
