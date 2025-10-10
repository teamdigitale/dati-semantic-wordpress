import { h, Component, Prop, Watch, Host, State, Event, } from '@stencil/core';
import { Disposal } from '../../core/player/Disposal';
import { isUndefined } from '../../../utils/unit';
import { listen } from '../../../utils/dom';
import { withPlayerContext } from '../../core/player/PlayerContext';
export class Captions {
  constructor() {
    this.textTracksDisposal = new Disposal();
    this.textTrackDisposal = new Disposal();
    this.state = new Map();
    this.isEnabled = false;
    this.activeCues = [];
    /**
     * Whether the captions should be visible or not.
     */
    this.hidden = false;
    /**
     * The height of any lower control bar in pixels so that the captions can reposition when it's
     * active.
     */
    this.controlsHeight = 0;
    /**
     * @internal
     */
    this.isControlsActive = false;
    /**
     * @internal
     */
    this.isVideoView = false;
    /**
     * @internal
     */
    this.playbackStarted = false;
    withPlayerContext(this, [
      'isVideoView',
      'playbackStarted',
      'isControlsActive',
      'textTracks',
    ]);
  }
  onActiveTrackChange() {
    this.vTrackChange.emit(this.activeTrack);
  }
  onActiveCuesChange() {
    this.vCuesChange.emit(this.activeCues);
  }
  disconnectedCallback() {
    this.cleanup();
  }
  cleanup() {
    this.state.clear();
    this.textTracksDisposal.empty();
    this.textTrackDisposal.empty();
  }
  onCueChange() {
    var _a, _b;
    this.activeCues = Array.from((_b = (_a = this.activeTrack) === null || _a === void 0 ? void 0 : _a.activeCues) !== null && _b !== void 0 ? _b : []);
  }
  onTrackChange() {
    this.activeCues = [];
    this.textTrackDisposal.empty();
    if (isUndefined(this.activeTrack))
      return;
    this.textTrackDisposal.add(listen(this.activeTrack, 'cuechange', this.onCueChange.bind(this)));
  }
  findActiveTrack() {
    let activeTrack;
    Array.from(this.textTracks).forEach((track) => {
      if (isUndefined(activeTrack) && (track.mode === 'showing')) {
        // eslint-disable-next-line no-param-reassign
        track.mode = 'hidden';
        activeTrack = track;
        this.state.set(track, 'hidden');
      }
      else {
        // eslint-disable-next-line no-param-reassign
        track.mode = 'disabled';
        this.state.set(track, 'disabled');
      }
    });
    return activeTrack;
  }
  onTracksChange() {
    let hasChanged = false;
    Array.from(this.textTracks).forEach((track) => {
      if (!hasChanged) {
        hasChanged = !this.state.has(track) || (track.mode !== this.state.get(track));
      }
      this.state.set(track, track.mode);
    });
    if (hasChanged) {
      const activeTrack = this.findActiveTrack();
      if (this.activeTrack !== activeTrack) {
        this.activeTrack = activeTrack;
        this.onTrackChange();
      }
    }
  }
  onTextTracksListChange() {
    this.cleanup();
    if (isUndefined(this.textTracks))
      return;
    this.onTracksChange();
    this.textTracksDisposal.add(listen(this.textTracks, 'change', this.onTracksChange.bind(this)));
  }
  onEnabledChange() {
    this.isEnabled = this.playbackStarted && this.isVideoView;
  }
  renderCurrentCue() {
    const currentCue = this.activeCues[0];
    if (isUndefined(currentCue))
      return '';
    const div = document.createElement('div');
    div.append(currentCue.getCueAsHTML());
    return div.innerHTML.trim();
  }
  render() {
    return (h(Host, { style: {
        transform: `translateY(-${this.isControlsActive ? this.controlsHeight : 24}px)`,
      }, class: {
        enabled: this.isEnabled,
        hidden: this.hidden,
      } },
      h("span", null, this.renderCurrentCue())));
  }
  static get is() { return "vime-captions"; }
  static get originalStyleUrls() { return {
    "$": ["captions.scss"]
  }; }
  static get styleUrls() { return {
    "$": ["captions.css"]
  }; }
  static get properties() { return {
    "hidden": {
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
        "text": "Whether the captions should be visible or not."
      },
      "attribute": "hidden",
      "reflect": false,
      "defaultValue": "false"
    },
    "controlsHeight": {
      "type": "number",
      "mutable": false,
      "complexType": {
        "original": "number",
        "resolved": "number",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [],
        "text": "The height of any lower control bar in pixels so that the captions can reposition when it's\nactive."
      },
      "attribute": "controls-height",
      "reflect": false,
      "defaultValue": "0"
    },
    "isControlsActive": {
      "type": "boolean",
      "mutable": false,
      "complexType": {
        "original": "PlayerProps['isControlsActive']",
        "resolved": "boolean",
        "references": {
          "PlayerProps": {
            "location": "import",
            "path": "../../core/player/PlayerProps"
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
      "attribute": "is-controls-active",
      "reflect": false,
      "defaultValue": "false"
    },
    "isVideoView": {
      "type": "boolean",
      "mutable": false,
      "complexType": {
        "original": "PlayerProps['isVideoView']",
        "resolved": "boolean",
        "references": {
          "PlayerProps": {
            "location": "import",
            "path": "../../core/player/PlayerProps"
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
      "attribute": "is-video-view",
      "reflect": false,
      "defaultValue": "false"
    },
    "playbackStarted": {
      "type": "boolean",
      "mutable": false,
      "complexType": {
        "original": "PlayerProps['playbackStarted']",
        "resolved": "boolean",
        "references": {
          "PlayerProps": {
            "location": "import",
            "path": "../../core/player/PlayerProps"
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
      "attribute": "playback-started",
      "reflect": false,
      "defaultValue": "false"
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
            "path": "../../core/player/PlayerProps"
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
  static get states() { return {
    "isEnabled": {},
    "activeTrack": {},
    "activeCues": {}
  }; }
  static get events() { return [{
      "method": "vTrackChange",
      "name": "vTrackChange",
      "bubbles": false,
      "cancelable": true,
      "composed": true,
      "docs": {
        "tags": [],
        "text": "Emitted when the current track changes."
      },
      "complexType": {
        "original": "TextTrack | undefined",
        "resolved": "TextTrack | undefined",
        "references": {
          "TextTrack": {
            "location": "global"
          }
        }
      }
    }, {
      "method": "vCuesChange",
      "name": "vCuesChange",
      "bubbles": false,
      "cancelable": true,
      "composed": true,
      "docs": {
        "tags": [],
        "text": "Emitted when the active cues change. A cue is active when\n`currentTime >= cue.startTime && currentTime <= cue.endTime`."
      },
      "complexType": {
        "original": "TextTrackCue[]",
        "resolved": "TextTrackCue[]",
        "references": {
          "TextTrackCue": {
            "location": "global"
          }
        }
      }
    }]; }
  static get watchers() { return [{
      "propName": "activeTrack",
      "methodName": "onActiveTrackChange"
    }, {
      "propName": "activeCues",
      "methodName": "onActiveCuesChange"
    }, {
      "propName": "textTracks",
      "methodName": "onTextTracksListChange"
    }, {
      "propName": "isVideoView",
      "methodName": "onEnabledChange"
    }, {
      "propName": "playbackStarted",
      "methodName": "onEnabledChange"
    }]; }
}
