import { h, Host, Component, Prop, } from '@stencil/core';
import { withPlayerContext } from '../../core/player/PlayerContext';
import { IS_IOS } from '../../../utils/support';
/**
 * @slot - Used to pass in UI components for the player.
 */
export class UI {
  constructor() {
    /**
     * @internal
     */
    this.isVideoView = false;
    /**
     * @internal
     */
    this.playsinline = false;
    /**
     * @internal
     */
    this.isFullscreenActive = false;
    withPlayerContext(this, [
      'isVideoView',
      'playsinline',
      'isFullscreenActive',
    ]);
  }
  render() {
    const canShowCustomUI = !IS_IOS
      || !this.isVideoView
      || (this.playsinline && !this.isFullscreenActive);
    return (h(Host, { class: {
        hidden: !canShowCustomUI,
        video: this.isVideoView,
      } },
      h("div", null, canShowCustomUI && h("slot", null))));
  }
  static get is() { return "vime-ui"; }
  static get originalStyleUrls() { return {
    "$": ["ui.scss"]
  }; }
  static get styleUrls() { return {
    "$": ["ui.css"]
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
    "playsinline": {
      "type": "boolean",
      "mutable": false,
      "complexType": {
        "original": "PlayerProps['playsinline']",
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
      "attribute": "playsinline",
      "reflect": false,
      "defaultValue": "false"
    },
    "isFullscreenActive": {
      "type": "boolean",
      "mutable": false,
      "complexType": {
        "original": "PlayerProps['isFullscreenActive']",
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
      "attribute": "is-fullscreen-active",
      "reflect": false,
      "defaultValue": "false"
    }
  }; }
}
