import { EventEmitter } from '../../../stencil-public-runtime';
import { MediaProvider, MockMediaProviderAdapter } from '../MediaProvider';
import { PlayerProp } from '../../core/player/PlayerProps';
import { Logger } from '../../core/player/PlayerLogger';
export declare class FakeTube implements MediaProvider {
  private dispatch;
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
  vLoadStart: EventEmitter<void>;
  constructor();
  connectedCallback(): void;
  componentWillLoad(): void;
  /**
   * Returns a mock adapter.
   */
  getAdapter(): Promise<MockMediaProviderAdapter>;
  /**
   * Dispatches the `vLoadStart` event.
   */
  dispatchLoadStart(): Promise<void>;
  /**
   * Dispatches a state change event.
   */
  dispatchChange(prop: PlayerProp, value: any): Promise<void>;
}
