import { h, Component, Prop, Watch, forceUpdate, } from '@stencil/core';
import { withPlayerContext } from '../../../core/player/PlayerContext';
import { Disposal } from '../../../core/player/Disposal';
import { listen } from '../../../../utils/dom';
import { isUndefined } from '../../../../utils/unit';
import { createDispatcher } from '../../../core/player/PlayerDispatcher';
import { findRootPlayer } from '../../../core/player/utils';
/**
 * @slot - Used to extend the settings with additional menu options (see `vime-submenu` or
 * `vime-menu-item`).
 */
export class DefaultSettings {
  constructor() {
    this.textTracksDisposal = new Disposal();
    /**
     * Pins the settings to the defined position inside the video player. This has no effect when
     * the view is of type `audio`, it will always be `bottomRight`.
     */
    this.pin = 'bottomRight';
    /**
     * @internal
     */
    this.i18n = {};
    /**
     * @internal
     */
    this.playbackReady = false;
    /**
     * @internal
     */
    this.playbackRate = 1;
    /**
     * @internal
     */
    this.playbackRates = [1];
    /**
     * @internal
     */
    this.playbackQualities = [];
    /**
     * @internal
     */
    this.isCaptionsActive = false;
    withPlayerContext(this, [
      'i18n',
      'playbackReady',
      'playbackRate',
      'playbackRates',
      'playbackQuality',
      'playbackQualities',
      'isCaptionsActive',
      'currentCaption',
      'textTracks',
    ]);
  }
  onTextTracksChange() {
    this.textTracksDisposal.empty();
    if (isUndefined(this.textTracks))
      return;
    this.textTracksDisposal.add(listen(this.textTracks, 'change', () => {
      setTimeout(() => forceUpdate(this), 300);
    }));
  }
  connectedCallback() {
    this.player = findRootPlayer(this);
    this.dispatch = createDispatcher(this);
  }
  componentWillRender() {
    if (!this.playbackReady)
      return undefined;
    return Promise.all([
      this.buildPlaybackRateSubmenu(),
      this.buildPlaybackQualitySubmenu(),
      this.buildCaptionsSubmenu(),
    ]);
  }
  disconnectedCallback() {
    this.player = undefined;
    this.textTracksDisposal.empty();
  }
  onPlaybackRateSelect(event) {
    const radio = event.target;
    this.dispatch('playbackRate', parseFloat(radio.value));
  }
  async buildPlaybackRateSubmenu() {
    var _a;
    const canSetPlaybackRate = await ((_a = this.player) === null || _a === void 0 ? void 0 : _a.canSetPlaybackRate());
    if (this.playbackRates.length === 1 || !canSetPlaybackRate) {
      this.rateSubmenu = (h("vime-menu-item", { label: this.i18n.playbackRate, hint: this.i18n.normal }));
      return;
    }
    const formatRate = (rate) => ((rate === 1) ? this.i18n.normal : `${rate}`);
    const radios = this.playbackRates.map((rate) => (h("vime-menu-radio", { label: formatRate(rate), value: `${rate}` })));
    this.rateSubmenu = (h("vime-submenu", { label: this.i18n.playbackRate, hint: formatRate(this.playbackRate) },
      h("vime-menu-radio-group", { value: `${this.playbackRate}`, onVCheck: this.onPlaybackRateSelect.bind(this) }, radios)));
  }
  onPlaybackQualitySelect(event) {
    const radio = event.target;
    this.dispatch('playbackQuality', radio.value);
  }
  async buildPlaybackQualitySubmenu() {
    var _a, _b;
    const canSetPlaybackQuality = await ((_a = this.player) === null || _a === void 0 ? void 0 : _a.canSetPlaybackQuality());
    if (this.playbackQualities.length === 0 || !canSetPlaybackQuality) {
      this.qualitySubmenu = (h("vime-menu-item", { label: this.i18n.playbackQuality, hint: (_b = this.playbackQuality) !== null && _b !== void 0 ? _b : this.i18n.auto }));
      return;
    }
    // @TODO this doesn't account for audio qualities yet.
    const getBadge = (quality) => {
      const verticalPixels = parseInt(quality.slice(0, -1), 10);
      if (verticalPixels > 2160)
        return 'UHD';
      if (verticalPixels >= 1080)
        return 'HD';
      return undefined;
    };
    const radios = this.playbackQualities.map((quality) => (h("vime-menu-radio", { label: quality, value: quality, badge: getBadge(quality) })));
    this.qualitySubmenu = (h("vime-submenu", { label: this.i18n.playbackQuality, hint: this.playbackQuality },
      h("vime-menu-radio-group", { value: this.playbackQuality, onVCheck: this.onPlaybackQualitySelect.bind(this) }, radios)));
  }
  async onCaptionSelect(event) {
    var _a;
    const radio = event.target;
    const index = parseInt(radio.value, 10);
    const player = findRootPlayer(this);
    if (index === -1) {
      await player.toggleCaptionsVisibility(false);
      return;
    }
    const track = Array.from((_a = this.textTracks) !== null && _a !== void 0 ? _a : [])[index];
    if (!isUndefined(track)) {
      if (!isUndefined(this.currentCaption))
        this.currentCaption.mode = 'disabled';
      track.mode = 'showing';
      await player.toggleCaptionsVisibility(true);
    }
  }
  async buildCaptionsSubmenu() {
    var _a, _b, _c;
    const captions = Array.from((_a = this.textTracks) !== null && _a !== void 0 ? _a : [])
      .filter((track) => ['captions', 'subtitles'].includes(track.kind));
    if (captions.length === 0) {
      this.captionsSubmenu = (h("vime-menu-item", { label: this.i18n.subtitlesOrCc, hint: this.i18n.none }));
      return;
    }
    const getTrackValue = (track) => `${Array.from(this.textTracks).findIndex((t) => t === track)}`;
    const radios = [(h("vime-menu-radio", { label: this.i18n.off, value: "-1" }))].concat(captions.map((track) => (h("vime-menu-radio", { label: track.label, value: getTrackValue(track) }))));
    const groupValue = (!this.isCaptionsActive || isUndefined(this.currentCaption))
      ? '-1'
      : getTrackValue(this.currentCaption);
    this.captionsSubmenu = (h("vime-submenu", { label: this.i18n.subtitlesOrCc, hint: (_c = (this.isCaptionsActive ? (_b = this.currentCaption) === null || _b === void 0 ? void 0 : _b.label : undefined)) !== null && _c !== void 0 ? _c : this.i18n.off },
      h("vime-menu-radio-group", { value: groupValue, onVCheck: this.onCaptionSelect.bind(this) }, radios)));
  }
  render() {
    return (h("vime-settings", { pin: this.pin },
      this.rateSubmenu,
      this.qualitySubmenu,
      this.captionsSubmenu,
      h("slot", null)));
  }
  static get is() { return "vime-default-settings"; }
  static get properties() { return {
    "pin": {
      "type": "string",
      "mutable": false,
      "complexType": {
        "original": "'topLeft' | 'topRight' | 'bottomLeft' | 'bottomRight'",
        "resolved": "\"bottomLeft\" | \"bottomRight\" | \"topLeft\" | \"topRight\"",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [],
        "text": "Pins the settings to the defined position inside the video player. This has no effect when\nthe view is of type `audio`, it will always be `bottomRight`."
      },
      "attribute": "pin",
      "reflect": true,
      "defaultValue": "'bottomRight'"
    },
    "i18n": {
      "type": "unknown",
      "mutable": false,
      "complexType": {
        "original": "PlayerProps['i18n']",
        "resolved": "Translation | { [x: string]: string; }",
        "references": {
          "PlayerProps": {
            "location": "import",
            "path": "../../../core/player/PlayerProps"
          }
        }
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
      "defaultValue": "{}"
    },
    "playbackReady": {
      "type": "boolean",
      "mutable": false,
      "complexType": {
        "original": "PlayerProps['playbackReady']",
        "resolved": "boolean",
        "references": {
          "PlayerProps": {
            "location": "import",
            "path": "../../../core/player/PlayerProps"
          }
        }
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
      "attribute": "playback-ready",
      "reflect": false,
      "defaultValue": "false"
    },
    "playbackRate": {
      "type": "number",
      "mutable": false,
      "complexType": {
        "original": "PlayerProps['playbackRate']",
        "resolved": "number",
        "references": {
          "PlayerProps": {
            "location": "import",
            "path": "../../../core/player/PlayerProps"
          }
        }
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
      "attribute": "playback-rate",
      "reflect": false,
      "defaultValue": "1"
    },
    "playbackRates": {
      "type": "unknown",
      "mutable": false,
      "complexType": {
        "original": "PlayerProps['playbackRates']",
        "resolved": "number[]",
        "references": {
          "PlayerProps": {
            "location": "import",
            "path": "../../../core/player/PlayerProps"
          }
        }
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
      "defaultValue": "[1]"
    },
    "playbackQuality": {
      "type": "string",
      "mutable": false,
      "complexType": {
        "original": "PlayerProps['playbackQuality']",
        "resolved": "string | undefined",
        "references": {
          "PlayerProps": {
            "location": "import",
            "path": "../../../core/player/PlayerProps"
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
      },
      "attribute": "playback-quality",
      "reflect": false
    },
    "playbackQualities": {
      "type": "unknown",
      "mutable": false,
      "complexType": {
        "original": "PlayerProps['playbackQualities']",
        "resolved": "string[]",
        "references": {
          "PlayerProps": {
            "location": "import",
            "path": "../../../core/player/PlayerProps"
          }
        }
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
      "defaultValue": "[]"
    },
    "isCaptionsActive": {
      "type": "boolean",
      "mutable": false,
      "complexType": {
        "original": "PlayerProps['isCaptionsActive']",
        "resolved": "boolean",
        "references": {
          "PlayerProps": {
            "location": "import",
            "path": "../../../core/player/PlayerProps"
          }
        }
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
      "attribute": "is-captions-active",
      "reflect": false,
      "defaultValue": "false"
    },
    "currentCaption": {
      "type": "unknown",
      "mutable": false,
      "complexType": {
        "original": "PlayerProps['currentCaption']",
        "resolved": "TextTrack | undefined",
        "references": {
          "PlayerProps": {
            "location": "import",
            "path": "../../../core/player/PlayerProps"
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
    "textTracks": {
      "type": "unknown",
      "mutable": false,
      "complexType": {
        "original": "PlayerProps['textTracks']",
        "resolved": "TextTrackList | undefined",
        "references": {
          "PlayerProps": {
            "location": "import",
            "path": "../../../core/player/PlayerProps"
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
    }
  }; }
  static get watchers() { return [{
      "propName": "textTracks",
      "methodName": "onTextTracksChange"
    }]; }
}
