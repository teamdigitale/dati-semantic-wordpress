import { h, Component, State, Prop, Watch, Host, Event, } from '@stencil/core';
import { withPlayerContext } from '../../core/player/PlayerContext';
import { Provider } from '../../providers/Provider';
export class Spinner {
  constructor() {
    this.blacklist = [Provider.YouTube];
    this.isHidden = true;
    this.isActive = false;
    /**
     * @internal
     */
    this.isVideoView = false;
    /**
     * @internal
     */
    this.buffering = false;
    withPlayerContext(this, [
      'isVideoView',
      'buffering',
      'currentProvider',
    ]);
  }
  onVideoViewChange() {
    this.isHidden = !this.isVideoView;
    this.onVisiblityChange();
  }
  onActiveChange() {
    this.isActive = this.buffering;
    this.onVisiblityChange();
  }
  onVisiblityChange() {
    (!this.isHidden && this.isActive) ? this.vWillShow.emit() : this.vWillHide.emit();
  }
  render() {
    return (h(Host, { class: {
        hidden: this.isHidden || this.blacklist.includes(this.currentProvider),
        active: this.isActive,
      } },
      h("div", null, "Loading...")));
  }
  static get is() { return "vime-spinner"; }
  static get originalStyleUrls() { return {
    "$": ["spinner.scss"]
  }; }
  static get styleUrls() { return {
    "$": ["spinner.css"]
  }; }
  static get properties() { return {
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
    "currentProvider": {
      "type": "string",
      "mutable": false,
      "complexType": {
        "original": "PlayerProps['currentProvider']",
        "resolved": "Provider.Audio | Provider.Dailymotion | Provider.Dash | Provider.FakeTube | Provider.HLS | Provider.Video | Provider.Vimeo | Provider.YouTube | undefined",
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
      },
      "attribute": "current-provider",
      "reflect": false
    },
    "buffering": {
      "type": "boolean",
      "mutable": false,
      "complexType": {
        "original": "PlayerProps['buffering']",
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
      "attribute": "buffering",
      "reflect": false,
      "defaultValue": "false"
    }
  }; }
  static get states() { return {
    "isHidden": {},
    "isActive": {}
  }; }
  static get events() { return [{
      "method": "vWillShow",
      "name": "vWillShow",
      "bubbles": false,
      "cancelable": true,
      "composed": true,
      "docs": {
        "tags": [],
        "text": "Emitted when the spinner will be shown."
      },
      "complexType": {
        "original": "void",
        "resolved": "void",
        "references": {}
      }
    }, {
      "method": "vWillHide",
      "name": "vWillHide",
      "bubbles": false,
      "cancelable": true,
      "composed": true,
      "docs": {
        "tags": [],
        "text": "Emitted when the spinner will be hidden."
      },
      "complexType": {
        "original": "void",
        "resolved": "void",
        "references": {}
      }
    }]; }
  static get watchers() { return [{
      "propName": "isVideoView",
      "methodName": "onVideoViewChange"
    }, {
      "propName": "buffering",
      "methodName": "onActiveChange"
    }]; }
}
