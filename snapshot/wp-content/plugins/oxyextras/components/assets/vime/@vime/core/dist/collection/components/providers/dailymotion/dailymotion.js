import { h, Prop, Method, Component, Event, State, Watch, } from '@stencil/core';
import { withProviderConnect, withProviderContext } from '../MediaProvider';
import { decodeQueryString } from '../../../utils/network';
import { isString } from '../../../utils/unit';
import { ViewType } from '../../core/player/ViewType';
import { deferredPromise } from '../../../utils/promise';
import { MediaType } from '../../core/player/MediaType';
import { createProviderDispatcher } from '../ProviderDispatcher';
const videoInfoCache = new Map();
export class Dailymotion {
  constructor() {
    this.defaultInternalState = {};
    this.internalState = {
      currentTime: 0,
      volume: 0,
      muted: false,
      isAdsPlaying: false,
      playbackReady: false,
    };
    this.embedSrc = '';
    this.mediaTitle = '';
    /**
     * Whether to automatically play the next video in the queue.
     */
    this.shouldAutoplayQueue = false;
    /**
     * Whether to show the 'Up Next' queue.
     */
    this.showUpNextQueue = false;
    /**
     * Whether to show buttons for sharing the video.
     */
    this.showShareButtons = false;
    /**
     * Whether to display the Dailymotion logo.
     */
    this.showDailymotionLogo = false;
    /**
     * Whether to show video information (title and owner) on the start screen.
     */
    this.showVideoInfo = true;
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
    withProviderConnect(this);
    withProviderContext(this);
  }
  onVideoIdChange() {
    this.embedSrc = `${this.getOrigin()}/embed/video/${this.videoId}?api=1`;
    this.internalState = Object.assign({}, this.defaultInternalState);
    this.fetchVideoInfo = this.getVideoInfo();
    this.pendingMediaTitleCall = deferredPromise();
  }
  onControlsChange() {
    if (this.internalState.playbackReady) {
      this.remoteControl("controls" /* Controls */, this.controls);
    }
  }
  onCustomPosterChange() {
    this.dispatch('currentPoster', this.poster);
  }
  connectedCallback() {
    this.dispatch = createProviderDispatcher(this);
    this.dispatch('viewType', ViewType.Video);
    this.onVideoIdChange();
    this.initialMuted = this.muted;
    this.internalState.muted = this.muted;
    this.defaultInternalState = Object.assign({}, this.internalState);
  }
  getOrigin() {
    return 'https://www.dailymotion.com';
  }
  getPreconnections() {
    return [
      this.getOrigin(),
      'https://static1.dmcdn.net',
    ];
  }
  remoteControl(command, arg) {
    return this.embed.postMessage({
      command,
      parameters: arg ? [arg] : [],
    });
  }
  buildParams() {
    return {
      autoplay: this.autoplay,
      mute: this.initialMuted,
      'queue-autoplay-next': this.shouldAutoplayQueue,
      'queue-enable': this.showUpNextQueue,
      'sharing-enable': this.showShareButtons,
      syndication: this.syndication,
      'ui-highlight': this.color,
      'ui-logo': this.showDailymotionLogo,
      'ui-start-screen-info': this.showVideoInfo,
    };
  }
  async getVideoInfo() {
    if (videoInfoCache.has(this.videoId))
      return videoInfoCache.get(this.videoId);
    const apiEndpoint = 'https://api.dailymotion.com';
    return window
      .fetch(`${apiEndpoint}/video/${this.videoId}?fields=duration,thumbnail_1080_url`)
      .then((response) => response.json())
      .then((data) => {
      const poster = data.thumbnail_1080_url;
      const duration = parseFloat(data.duration);
      videoInfoCache.set(this.videoId, { poster, duration });
      return { poster, duration };
    });
  }
  onEmbedSrcChange() {
    this.vLoadStart.emit();
  }
  onEmbedMessage(event) {
    var _a, _b;
    const msg = event.detail;
    switch (msg.event) {
      case "playback_ready" /* PlaybackReady */:
        this.onControlsChange();
        this.dispatch('currentSrc', this.embedSrc);
        this.dispatch('mediaType', MediaType.Video);
        Promise.all([
          this.fetchVideoInfo,
          (_a = this.pendingMediaTitleCall) === null || _a === void 0 ? void 0 : _a.promise,
        ]).then(([info, mediaTitle]) => {
          var _a, _b;
          this.dispatch('duration', (_a = info === null || info === void 0 ? void 0 : info.duration) !== null && _a !== void 0 ? _a : -1);
          this.dispatch('currentPoster', (_b = this.poster) !== null && _b !== void 0 ? _b : info === null || info === void 0 ? void 0 : info.poster);
          this.dispatch('mediaTitle', mediaTitle);
          this.dispatch('playbackReady', true);
        });
        break;
      case "videochange" /* VideoChange */:
        (_b = this.pendingMediaTitleCall) === null || _b === void 0 ? void 0 : _b.resolve(msg.title);
        break;
      case "start" /* Start */:
        this.dispatch('paused', false);
        this.dispatch('playbackStarted', true);
        this.dispatch('buffering', true);
        break;
      case "video_start" /* VideoStart */:
        // Commands don't go through until ads have finished, so we store them and then replay them
        // once the video starts.
        this.remoteControl("muted" /* Muted */, this.internalState.muted);
        this.remoteControl("volume" /* Volume */, this.internalState.volume);
        if (this.internalState.currentTime > 0) {
          this.remoteControl("seek" /* Seek */, this.internalState.currentTime);
        }
        break;
      case "play" /* Play */:
        this.dispatch('paused', false);
        break;
      case "pause" /* Pause */:
        this.dispatch('paused', true);
        this.dispatch('playing', false);
        this.dispatch('buffering', false);
        break;
      case "playing" /* Playing */:
        this.dispatch('playing', true);
        this.dispatch('buffering', false);
        break;
      case "video_end" /* VideoEnd */:
        if (this.loop) {
          setTimeout(() => { this.remoteControl("play" /* Play */); }, 300);
        }
        else {
          this.dispatch('playbackEnded', true);
        }
        break;
      case "timeupdate" /* TimeUpdate */:
        this.dispatch('currentTime', parseFloat(msg.time));
        break;
      case "volumechange" /* VolumeChange */:
        this.dispatch('muted', msg.muted === 'true');
        this.dispatch('volume', Math.floor(parseFloat(msg.volume) * 100));
        break;
      case "seeking" /* Seeking */:
        this.dispatch('currentTime', parseFloat(msg.time));
        this.dispatch('seeking', true);
        break;
      case "seeked" /* Seeked */:
        this.dispatch('currentTime', parseFloat(msg.time));
        this.dispatch('seeking', false);
        break;
      case "waiting" /* Waiting */:
        this.dispatch('buffering', true);
        break;
      case "progress" /* Progress */:
        this.dispatch('buffered', parseFloat(msg.time));
        break;
      case "durationchange" /* DurationChange */:
        this.dispatch('duration', parseFloat(msg.duration));
        break;
      case "qualitiesavailable" /* QualitiesAvailable */:
        this.dispatch('playbackQualities', msg.qualities.map((q) => `${q}p`));
        break;
      case "qualitychange" /* QualityChange */:
        this.dispatch('playbackQuality', `${msg.quality}p`);
        break;
      case "fullscreenchange" /* FullscreenChange */:
        this.dispatch('isFullscreenActive', msg.fullscreen === 'true');
        break;
      case "error" /* Error */:
        this.dispatch('errors', [new Error(msg.error)]);
        break;
    }
  }
  /**
   * @internal
   */
  async getAdapter() {
    const canPlayRegex = /(?:dai\.ly|dailymotion|dailymotion\.com)\/(?:video\/|embed\/|)(?:video\/|)((?:\w)+)/;
    return {
      getInternalPlayer: async () => this.embed,
      play: async () => { this.remoteControl("play" /* Play */); },
      pause: async () => { this.remoteControl("pause" /* Pause */); },
      canPlay: async (type) => isString(type) && canPlayRegex.test(type),
      setCurrentTime: async (time) => {
        this.internalState.currentTime = time;
        this.remoteControl("seek" /* Seek */, time);
      },
      setMuted: async (muted) => {
        this.internalState.muted = muted;
        this.remoteControl("muted" /* Muted */, muted);
      },
      setVolume: async (volume) => {
        this.internalState.volume = (volume / 100);
        this.dispatch('volume', volume);
        this.remoteControl("volume" /* Volume */, (volume / 100));
      },
      canSetPlaybackQuality: async () => true,
      setPlaybackQuality: async (quality) => {
        this.remoteControl("quality" /* Quality */, quality.slice(0, -1));
      },
      canSetFullscreen: async () => true,
      enterFullscreen: async () => { this.remoteControl("fullscreen" /* Fullscreen */, true); },
      exitFullscreen: async () => { this.remoteControl("fullscreen" /* Fullscreen */, false); },
    };
  }
  render() {
    return (h("vime-embed", { embedSrc: this.embedSrc, mediaTitle: this.mediaTitle, origin: this.getOrigin(), params: this.buildParams(), decoder: decodeQueryString, preconnections: this.getPreconnections(), onVEmbedMessage: this.onEmbedMessage.bind(this), onVEmbedSrcChange: this.onEmbedSrcChange.bind(this), ref: (el) => { this.embed = el; } }));
  }
  static get is() { return "vime-dailymotion"; }
  static get properties() { return {
    "videoId": {
      "type": "string",
      "mutable": false,
      "complexType": {
        "original": "string",
        "resolved": "string",
        "references": {}
      },
      "required": true,
      "optional": false,
      "docs": {
        "tags": [],
        "text": "The Dailymotion resource ID of the video to load."
      },
      "attribute": "video-id",
      "reflect": false
    },
    "shouldAutoplayQueue": {
      "type": "boolean",
      "mutable": false,
      "complexType": {
        "original": "boolean",
        "resolved": "boolean",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [],
        "text": "Whether to automatically play the next video in the queue."
      },
      "attribute": "should-autoplay-queue",
      "reflect": false,
      "defaultValue": "false"
    },
    "showUpNextQueue": {
      "type": "boolean",
      "mutable": false,
      "complexType": {
        "original": "boolean",
        "resolved": "boolean",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [],
        "text": "Whether to show the 'Up Next' queue."
      },
      "attribute": "show-up-next-queue",
      "reflect": false,
      "defaultValue": "false"
    },
    "showShareButtons": {
      "type": "boolean",
      "mutable": false,
      "complexType": {
        "original": "boolean",
        "resolved": "boolean",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [],
        "text": "Whether to show buttons for sharing the video."
      },
      "attribute": "show-share-buttons",
      "reflect": false,
      "defaultValue": "false"
    },
    "color": {
      "type": "string",
      "mutable": false,
      "complexType": {
        "original": "string",
        "resolved": "string | undefined",
        "references": {}
      },
      "required": false,
      "optional": true,
      "docs": {
        "tags": [],
        "text": "Change the default highlight color used in the controls (hex value without the leading #).\nColor set in the Partner HQ will override this prop."
      },
      "attribute": "color",
      "reflect": false
    },
    "syndication": {
      "type": "string",
      "mutable": false,
      "complexType": {
        "original": "string",
        "resolved": "string | undefined",
        "references": {}
      },
      "required": false,
      "optional": true,
      "docs": {
        "tags": [],
        "text": "Forwards your syndication key to the player."
      },
      "attribute": "syndication",
      "reflect": false
    },
    "showDailymotionLogo": {
      "type": "boolean",
      "mutable": false,
      "complexType": {
        "original": "boolean",
        "resolved": "boolean",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [],
        "text": "Whether to display the Dailymotion logo."
      },
      "attribute": "show-dailymotion-logo",
      "reflect": false,
      "defaultValue": "false"
    },
    "showVideoInfo": {
      "type": "boolean",
      "mutable": false,
      "complexType": {
        "original": "boolean",
        "resolved": "boolean",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [],
        "text": "Whether to show video information (title and owner) on the start screen."
      },
      "attribute": "show-video-info",
      "reflect": false,
      "defaultValue": "true"
    },
    "language": {
      "type": "string",
      "mutable": false,
      "complexType": {
        "original": "string",
        "resolved": "string",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "internal"
          }],
        "text": ""
      },
      "attribute": "language",
      "reflect": false,
      "defaultValue": "'en'"
    },
    "autoplay": {
      "type": "boolean",
      "mutable": false,
      "complexType": {
        "original": "boolean",
        "resolved": "boolean",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "internal"
          }],
        "text": ""
      },
      "attribute": "autoplay",
      "reflect": false,
      "defaultValue": "false"
    },
    "controls": {
      "type": "boolean",
      "mutable": false,
      "complexType": {
        "original": "boolean",
        "resolved": "boolean",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "internal"
          }],
        "text": ""
      },
      "attribute": "controls",
      "reflect": false,
      "defaultValue": "false"
    },
    "poster": {
      "type": "string",
      "mutable": false,
      "complexType": {
        "original": "string",
        "resolved": "string | undefined",
        "references": {}
      },
      "required": false,
      "optional": true,
      "docs": {
        "tags": [],
        "text": "The absolute URL of a custom poster to be used for the current video."
      },
      "attribute": "poster",
      "reflect": false
    },
    "logger": {
      "type": "unknown",
      "mutable": false,
      "complexType": {
        "original": "Logger",
        "resolved": "Logger | undefined",
        "references": {
          "Logger": {
            "location": "import",
            "path": "../../core/player/PlayerLogger"
          }
        }
      },
      "required": false,
      "optional": true,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "internal"
          }],
        "text": ""
      }
    },
    "loop": {
      "type": "boolean",
      "mutable": false,
      "complexType": {
        "original": "boolean",
        "resolved": "boolean",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "internal"
          }],
        "text": ""
      },
      "attribute": "loop",
      "reflect": false,
      "defaultValue": "false"
    },
    "muted": {
      "type": "boolean",
      "mutable": false,
      "complexType": {
        "original": "boolean",
        "resolved": "boolean",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "internal"
          }],
        "text": ""
      },
      "attribute": "muted",
      "reflect": false,
      "defaultValue": "false"
    },
    "playsinline": {
      "type": "boolean",
      "mutable": false,
      "complexType": {
        "original": "boolean",
        "resolved": "boolean",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "internal"
          }],
        "text": ""
      },
      "attribute": "playsinline",
      "reflect": false,
      "defaultValue": "false"
    }
  }; }
  static get states() { return {
    "embedSrc": {},
    "mediaTitle": {}
  }; }
  static get events() { return [{
      "method": "vLoadStart",
      "name": "vLoadStart",
      "bubbles": true,
      "cancelable": true,
      "composed": true,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "internal"
          }],
        "text": ""
      },
      "complexType": {
        "original": "void",
        "resolved": "void",
        "references": {}
      }
    }]; }
  static get methods() { return {
    "getAdapter": {
      "complexType": {
        "signature": "() => Promise<{ getInternalPlayer: () => Promise<HTMLVimeEmbedElement>; play: () => Promise<void>; pause: () => Promise<void>; canPlay: (type: any) => Promise<boolean>; setCurrentTime: (time: number) => Promise<void>; setMuted: (muted: boolean) => Promise<void>; setVolume: (volume: number) => Promise<void>; canSetPlaybackQuality: () => Promise<boolean>; setPlaybackQuality: (quality: string) => Promise<void>; canSetFullscreen: () => Promise<boolean>; enterFullscreen: () => Promise<void>; exitFullscreen: () => Promise<void>; }>",
        "parameters": [],
        "references": {
          "Promise": {
            "location": "global"
          },
          "HTMLVimeEmbedElement": {
            "location": "global"
          }
        },
        "return": "Promise<{ getInternalPlayer: () => Promise<HTMLVimeEmbedElement>; play: () => Promise<void>; pause: () => Promise<void>; canPlay: (type: any) => Promise<boolean>; setCurrentTime: (time: number) => Promise<void>; setMuted: (muted: boolean) => Promise<void>; setVolume: (volume: number) => Promise<void>; canSetPlaybackQuality: () => Promise<boolean>; setPlaybackQuality: (quality: string) => Promise<void>; canSetFullscreen: () => Promise<boolean>; enterFullscreen: () => Promise<void>; exitFullscreen: () => Promise<void>; }>"
      },
      "docs": {
        "text": "",
        "tags": [{
            "name": "internal",
            "text": undefined
          }]
      }
    }
  }; }
  static get watchers() { return [{
      "propName": "videoId",
      "methodName": "onVideoIdChange"
    }, {
      "propName": "controls",
      "methodName": "onControlsChange"
    }, {
      "propName": "poster",
      "methodName": "onCustomPosterChange"
    }]; }
}
