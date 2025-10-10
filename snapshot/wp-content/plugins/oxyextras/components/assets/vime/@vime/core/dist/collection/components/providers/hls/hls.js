import { h, Method, Component, Prop, State, Event, Listen, } from '@stencil/core';
import { isNullOrUndefined, isString, isUndefined, } from '../../../utils/unit';
import { loadSDK } from '../../../utils/network';
import { canPlayHLSNatively } from '../../../utils/support';
import { hlsRegex, hlsTypeRegex } from '../file/utils';
import { MediaType } from '../../core/player/MediaType';
import { createProviderDispatcher } from '../ProviderDispatcher';
import { withProviderConnect } from '../MediaProvider';
/**
 * @slot - Pass `<source>` elements to the underlying HTML5 media player.
 */
export class HLS {
  constructor() {
    this.hasAttached = false;
    /**
     * The NPM package version of the `hls.js` library to download and use if HLS is not natively
     * supported.
     */
    this.version = 'latest';
    /**
     * @inheritdoc
     */
    this.preload = 'metadata';
    withProviderConnect(this);
  }
  connectedCallback() {
    this.dispatch = createProviderDispatcher(this);
    if (this.mediaEl)
      this.setupHls();
  }
  disconnectedCallback() {
    this.destroyHls();
  }
  get src() {
    if (isNullOrUndefined(this.videoProvider))
      return undefined;
    const sources = this.videoProvider.querySelectorAll('source');
    const currSource = Array.from(sources)
      .find((source) => hlsRegex.test(source.src) || hlsTypeRegex.test(source.type));
    return currSource === null || currSource === void 0 ? void 0 : currSource.src;
  }
  async setupHls() {
    if (!isUndefined(this.hls) || canPlayHLSNatively())
      return;
    try {
      const url = `https://cdn.jsdelivr.net/npm/hls.js@${this.version}`;
      const Hls = await loadSDK(url, 'Hls');
      if (!Hls.isSupported()) {
        this.dispatch('errors', [new Error('hls.js is not supported')]);
        return;
      }
      this.hls = new Hls(this.config);
      this.hls.on(Hls.Events.MEDIA_ATTACHED, () => {
        this.hasAttached = true;
        this.onSrcChange();
      });
      this.hls.on(Hls.Events.ERROR, (e, data) => {
        if (data.fatal) {
          switch (data.type) {
            case Hls.ErrorTypes.NETWORK_ERROR:
              this.hls.startLoad();
              break;
            case Hls.ErrorTypes.MEDIA_ERROR:
              this.hls.recoverMediaError();
              break;
            default:
              this.destroyHls();
              break;
          }
        }
        this.dispatch('errors', [{ e, data }]);
      });
      this.hls.on(Hls.Events.MANIFEST_PARSED, () => {
        this.dispatch('mediaType', MediaType.Video);
        this.dispatch('currentSrc', this.src);
        this.dispatch('playbackReady', true);
      });
      this.hls.attachMedia(this.mediaEl);
    }
    catch (e) {
      this.dispatch('errors', [e]);
    }
  }
  destroyHls() {
    var _a;
    (_a = this.hls) === null || _a === void 0 ? void 0 : _a.destroy();
    this.hasAttached = false;
  }
  async onMediaElChange(event) {
    this.destroyHls();
    if (isUndefined(event.detail))
      return;
    this.mediaEl = event.detail;
    // Need a small delay incase the media element changes rapidly and Hls.js can't reattach.
    setTimeout(async () => { await this.setupHls(); }, 50);
  }
  async onSrcChange() {
    if (this.hasAttached && this.hls.url !== this.src) {
      this.vLoadStart.emit();
      this.hls.loadSource(this.src);
    }
  }
  /**
   * @internal
   */
  async getAdapter() {
    const adapter = await this.videoProvider.getAdapter();
    const canVideoProviderPlay = adapter.canPlay;
    return Object.assign(Object.assign({}, adapter), { getInternalPlayer: async () => this.hls, canPlay: async (type) => (isString(type) && hlsRegex.test(type))
        || canVideoProviderPlay(type) });
  }
  render() {
    return (h("vime-video", { willAttach: !canPlayHLSNatively(), crossOrigin: this.crossOrigin, preload: this.preload, poster: this.poster, controlsList: this.controlsList, autoPiP: this.autoPiP, disablePiP: this.disablePiP, disableRemotePlayback: this.disableRemotePlayback, mediaTitle: this.mediaTitle, ref: (el) => { this.videoProvider = el; } },
      h("slot", null)));
  }
  static get is() { return "vime-hls"; }
  static get properties() { return {
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
        "text": "The NPM package version of the `hls.js` library to download and use if HLS is not natively\nsupported."
      },
      "attribute": "version",
      "reflect": false,
      "defaultValue": "'latest'"
    },
    "config": {
      "type": "any",
      "mutable": false,
      "complexType": {
        "original": "any",
        "resolved": "any",
        "references": {}
      },
      "required": false,
      "optional": true,
      "docs": {
        "tags": [],
        "text": "The `hls.js` configuration."
      },
      "attribute": "config",
      "reflect": false
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
  static get listeners() { return [{
      "name": "vMediaElChange",
      "method": "onMediaElChange",
      "target": undefined,
      "capture": false,
      "passive": false
    }, {
      "name": "vSrcSetChange",
      "method": "onSrcChange",
      "target": undefined,
      "capture": false,
      "passive": false
    }]; }
}
