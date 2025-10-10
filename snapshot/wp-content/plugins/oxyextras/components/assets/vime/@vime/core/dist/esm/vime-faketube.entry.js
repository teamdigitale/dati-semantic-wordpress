import { r as registerInstance, c as createEvent } from './index-e4fee97f.js';
import './dom-888fcf0c.js';
import { f as findRootPlayer } from './PlayerContext-da67ca53.js';
import { w as withProviderContext, c as createProviderDispatcher } from './ProviderDispatcher-9bc874bc.js';

const faketubeCss = "vime-faketube{display:block;width:100%;height:auto}";

const FakeTube = class {
  constructor(hostRef) {
    registerInstance(this, hostRef);
    this.vLoadStart = createEvent(this, "vLoadStart", 7);
    /**
     * @internal
     */
    this.language = 'en';
    /**
     * @internal
     */
    this.autoplay = false;
    /**
     * @internal
     */
    this.controls = false;
    /**
     * @internal
     */
    this.loop = false;
    /**
     * @internal
     */
    this.muted = false;
    /**
     * @internal
     */
    this.playsinline = false;
    withProviderContext(this);
  }
  connectedCallback() {
    this.dispatch = createProviderDispatcher(this);
  }
  componentWillLoad() {
    const player = findRootPlayer(this);
    player.setProvider(this);
  }
  /**
   * Returns a mock adapter.
   */
  async getAdapter() {
    return {
      getInternalPlayer: jest.fn(),
      play: jest.fn(),
      pause: jest.fn(),
      canPlay: jest.fn(),
      setCurrentTime: jest.fn(),
      setMuted: jest.fn(),
      setVolume: jest.fn(),
      canSetPlaybackRate: jest.fn(),
      setPlaybackRate: jest.fn(),
      canSetPlaybackQuality: jest.fn(),
      setPlaybackQuality: jest.fn(),
      canSetFullscreen: jest.fn(),
      enterFullscreen: jest.fn(),
      exitFullscreen: jest.fn(),
      canSetPiP: jest.fn(),
      enterPiP: jest.fn(),
      exitPiP: jest.fn(),
    };
  }
  /**
   * Dispatches the `vLoadStart` event.
   */
  async dispatchLoadStart() {
    this.vLoadStart.emit();
  }
  /**
   * Dispatches a state change event.
   */
  async dispatchChange(prop, value) {
    this.dispatch(prop, value);
  }
};
FakeTube.style = faketubeCss;

export { FakeTube as vime_faketube };
