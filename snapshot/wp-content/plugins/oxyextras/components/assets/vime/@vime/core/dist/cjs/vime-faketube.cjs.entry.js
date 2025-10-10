'use strict';

Object.defineProperty(exports, '__esModule', { value: true });

const index = require('./index-e8963331.js');
require('./dom-4b0c36e3.js');
const PlayerContext = require('./PlayerContext-14c5c012.js');
const ProviderDispatcher = require('./ProviderDispatcher-797dd0b2.js');

const faketubeCss = "vime-faketube{display:block;width:100%;height:auto}";

const FakeTube = class {
  constructor(hostRef) {
    index.registerInstance(this, hostRef);
    this.vLoadStart = index.createEvent(this, "vLoadStart", 7);
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
    ProviderDispatcher.withProviderContext(this);
  }
  connectedCallback() {
    this.dispatch = ProviderDispatcher.createProviderDispatcher(this);
  }
  componentWillLoad() {
    const player = PlayerContext.findRootPlayer(this);
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

exports.vime_faketube = FakeTube;
