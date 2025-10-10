import { h, Method, Component, Prop, Watch, State, Event, Listen, } from '@stencil/core';
import { isString, isUndefined } from '../../../utils/unit';
import { dashRegex } from '../file/utils';
import { loadSDK } from '../../../utils/network';
import { MediaType } from '../../core/player/MediaType';
import { withPlayerContext } from '../../core/player/PlayerContext';
import { createProviderDispatcher } from '../ProviderDispatcher';
import { withProviderConnect } from '../MediaProvider';
export class Dash {
  constructor() {
    this.hasAttached = false;
    /**
     * The NPM package version of the `dashjs` library to download and use.
     */
    this.version = 'latest';
    /**
     * The `dashjs` configuration.
     */
    this.config = {};
    /**
     * @internal
     */
    this.autoplay = false;
    /**
     * @inheritdoc
     */
    this.preload = 'metadata';
    withProviderConnect(this);
    withPlayerContext(this, ['autoplay']);
  }
  onSrcChange() {
    if (!this.hasAttached)
      return;
    this.vLoadStart.emit();
    this.dash.attachSource(this.src);
  }
  connectedCallback() {
    this.dispatch = createProviderDispatcher(this);
    if (this.mediaEl)
      this.setupDash();
  }
  disconnectedCallback() {
    this.destroyDash();
  }
  async setupDash() {
    try {
      const url = `https://cdn.jsdelivr.net/npm/dashjs@${this.version}/dist/dash.all.min.js`;
      // eslint-disable-next-line no-shadow
      const DashSDK = await loadSDK(url, 'dashjs');
      this.dash = DashSDK.MediaPlayer(this.config).create();
      this.dash.initialize(this.mediaEl, null, this.autoplay);
      this.dash.on(DashSDK.MediaPlayer.events.CAN_PLAY, () => {
        this.dispatch('mediaType', MediaType.Video);
        this.dispatch('currentSrc', this.src);
        this.dispatch('playbackReady', true);
      });
      this.dash.on(DashSDK.MediaPlayer.events.ERROR, (e) => {
        this.dispatch('errors', [e]);
      });
      this.hasAttached = true;
    }
    catch (e) {
      this.dispatch('errors', [e]);
    }
  }
  async destroyDash() {
    var _a;
    (_a = this.dash) === null || _a === void 0 ? void 0 : _a.reset();
    this.hasAttached = false;
  }
  async onMediaElChange(event) {
    this.destroyDash();
    if (isUndefined(event.detail))
      return;
    this.mediaEl = event.detail;
    await this.setupDash();
  }
  /**
   * @internal
   */
  async getAdapter() {
    const adapter = await this.videoProvider.getAdapter();
    const canVideoProviderPlay = adapter.canPlay;
    return Object.assign(Object.assign({}, adapter), { getInternalPlayer: async () => this.dash, canPlay: async (type) => (isString(type) && dashRegex.test(type))
        || canVideoProviderPlay(type) });
  }
  render() {
    return (h("vime-video", { willAttach: true, crossOrigin: this.crossOrigin, preload: this.preload, poster: this.poster, controlsList: this.controlsList, autoPiP: this.autoPiP, disablePiP: this.disablePiP, disableRemotePlayback: this.disableRemotePlayback, mediaTitle: this.mediaTitle, ref: (el) => { this.videoProvider = el; } }));
  }
  static get is() { return "vime-dash"; }
  static get properties() { return {
    "src": {
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
        "text": "The URL of the `manifest.mpd` file to use."
      },
      "attribute": "src",
      "reflect": false
    },
    "version": {
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
        "tags": [],
        "text": "The NPM package version of the `dashjs` library to download and use."
      },
      "attribute": "version",
      "reflect": false,
      "defaultValue": "'latest'"
    },
    "config": {
      "type": "unknown",
      "mutable": false,
      "complexType": {
        "original": "Record<string, any>",
        "resolved": "{ [x: string]: any; }",
        "references": {
          "Record": {
            "location": "global"
          }
        }
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [],
        "text": "The `dashjs` configuration."
      },
      "defaultValue": "{}"
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
    "crossOrigin": {
      "type": "string",
      "mutable": false,
      "complexType": {
        "original": "MediaCrossOriginOption",
        "resolved": "\"\" | \"anonymous\" | \"use-credentials\" | undefined",
        "references": {
          "MediaCrossOriginOption": {
            "location": "import",
            "path": "../file/MediaFileProvider"
          }
        }
      },
      "required": false,
      "optional": true,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritdoc"
          }],
        "text": "Whether to use CORS to fetch the related image. See\n[MDN](https://developer.mozilla.org/en-US/docs/Web/HTML/Attributes/crossorigin) for more\ninformation."
      },
      "attribute": "cross-origin",
      "reflect": false
    },
    "preload": {
      "type": "string",
      "mutable": false,
      "complexType": {
        "original": "MediaPreloadOption",
        "resolved": "\"\" | \"auto\" | \"metadata\" | \"none\" | undefined",
        "references": {
          "MediaPreloadOption": {
            "location": "import",
            "path": "../file/MediaFileProvider"
          }
        }
      },
      "required": false,
      "optional": true,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritdoc"
          }],
        "text": "Provides a hint to the browser about what the author thinks will lead to the best user\nexperience with regards to what content is loaded before the video is played. See\n[MDN](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/video#attr-preload) for more\ninformation."
      },
      "attribute": "preload",
      "reflect": false,
      "defaultValue": "'metadata'"
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
        "tags": [{
            "text": undefined,
            "name": "inheritdoc"
          }],
        "text": "A URL for an image to be shown while the video is downloading. If this attribute isn't\nspecified, nothing is displayed until the first frame is available, then the first frame is\nshown as the poster frame."
      },
      "attribute": "poster",
      "reflect": false
    },
    "controlsList": {
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
        "tags": [{
            "text": undefined,
            "name": "inheritdoc"
          }],
        "text": "Determines what controls to show on the media element whenever the browser shows its own set\nof controls (e.g. when the controls attribute is specified)."
      },
      "attribute": "controls-list",
      "reflect": false
    },
    "autoPiP": {
      "type": "boolean",
      "mutable": false,
      "complexType": {
        "original": "boolean",
        "resolved": "boolean | undefined",
        "references": {}
      },
      "required": false,
      "optional": true,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritdoc"
          }],
        "text": "**EXPERIMENTAL:** Whether the browser should automatically toggle picture-in-picture mode as\nthe user switches back and forth between this document and another document or application."
      },
      "attribute": "auto-pip",
      "reflect": false
    },
    "disablePiP": {
      "type": "boolean",
      "mutable": false,
      "complexType": {
        "original": "boolean",
        "resolved": "boolean | undefined",
        "references": {}
      },
      "required": false,
      "optional": true,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritdoc"
          }],
        "text": "**EXPERIMENTAL:** Prevents the browser from suggesting a picture-in-picture context menu or to\nrequest picture-in-picture automatically in some cases."
      },
      "attribute": "disable-pip",
      "reflect": false
    },
    "disableRemotePlayback": {
      "type": "boolean",
      "mutable": false,
      "complexType": {
        "original": "boolean",
        "resolved": "boolean | undefined",
        "references": {}
      },
      "required": false,
      "optional": true,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "inheritdoc"
          }],
        "text": "**EXPERIMENTAL:** Whether to disable the capability of remote playback in devices that are\nattached using wired (HDMI, DVI, etc.) and wireless technologies\n(Miracast, Chromecast, DLNA, AirPlay, etc)."
      },
      "attribute": "disable-remote-playback",
      "reflect": false
    },
    "mediaTitle": {
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
        "text": "The title of the current media."
      },
      "attribute": "media-title",
      "reflect": false
    }
  }; }
  static get states() { return {
    "hasAttached": {}
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
        "signature": "() => Promise<{ getInternalPlayer: () => Promise<any>; canPlay: (type: any) => Promise<boolean>; play: () => Promise<void | undefined>; pause: () => Promise<void | undefined>; setCurrentTime: (time: number) => Promise<void>; setMuted: (muted: boolean) => Promise<void>; setVolume: (volume: number) => Promise<void>; canSetPlaybackRate: () => Promise<boolean>; setPlaybackRate: (rate: number) => Promise<void>; canSetPlaybackQuality: () => Promise<boolean>; setPlaybackQuality: (quality: string) => Promise<void>; canSetPiP: () => Promise<boolean>; enterPiP: () => Promise<any>; exitPiP: () => Promise<any>; canSetFullscreen: () => Promise<boolean>; enterFullscreen: () => Promise<any>; exitFullscreen: () => Promise<any>; }>",
        "parameters": [],
        "references": {
          "Promise": {
            "location": "global"
          }
        },
        "return": "Promise<{ getInternalPlayer: () => Promise<any>; canPlay: (type: any) => Promise<boolean>; play: () => Promise<void | undefined>; pause: () => Promise<void | undefined>; setCurrentTime: (time: number) => Promise<void>; setMuted: (muted: boolean) => Promise<void>; setVolume: (volume: number) => Promise<void>; canSetPlaybackRate: () => Promise<boolean>; setPlaybackRate: (rate: number) => Promise<void>; canSetPlaybackQuality: () => Promise<boolean>; setPlaybackQuality: (quality: string) => Promise<void>; canSetPiP: () => Promise<boolean>; enterPiP: () => Promise<any>; exitPiP: () => Promise<any>; canSetFullscreen: () => Promise<boolean>; enterFullscreen: () => Promise<any>; exitFullscreen: () => Promise<any>; }>"
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
      "propName": "src",
      "methodName": "onSrcChange"
    }, {
      "propName": "hasAttached",
      "methodName": "onSrcChange"
    }]; }
  static get listeners() { return [{
      "name": "vMediaElChange",
      "method": "onMediaElChange",
      "target": undefined,
      "capture": false,
      "passive": false
    }]; }
}
