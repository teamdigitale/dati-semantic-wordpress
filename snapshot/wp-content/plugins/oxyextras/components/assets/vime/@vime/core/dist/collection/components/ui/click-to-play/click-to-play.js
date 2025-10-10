import { h, Component, Host, Prop, } from '@stencil/core';
import { createDispatcher } from '../../core/player/PlayerDispatcher';
import { withPlayerContext } from '../../core/player/PlayerContext';
import { IS_MOBILE } from '../../../utils/support';
export class ClickToPlay {
  constructor() {
    /**
     * By default this is disabled on mobile to not interfere with playback, set this to `true` to
     * enable it.
     */
    this.useOnMobile = false;
    /**
     * @internal
     */
    this.paused = true;
    /**
     * @internal
     */
    this.isVideoView = false;
    withPlayerContext(this, ['paused', 'isVideoView']);
  }
  connectedCallback() {
    this.dispatch = createDispatcher(this);
  }
  onClick() {
    this.dispatch('paused', !this.paused);
  }
  render() {
    return (h(Host, { class: {
        enabled: this.isVideoView && (!IS_MOBILE || this.useOnMobile),
      }, onClick: this.onClick.bind(this) }));
  }
  static get is() { return "vime-click-to-play"; }
  static get originalStyleUrls() { return {
    "$": ["click-to-play.scss"]
  }; }
  static get styleUrls() { return {
    "$": ["click-to-play.css"]
  }; }
  static get properties() { return {
    "useOnMobile": {
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
        "text": "By default this is disabled on mobile to not interfere with playback, set this to `true` to\nenable it."
      },
      "attribute": "use-on-mobile",
      "reflect": false,
      "defaultValue": "false"
    },
    "paused": {
      "type": "boolean",
      "mutable": false,
      "complexType": {
        "original": "PlayerProps['paused']",
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
      "attribute": "paused",
      "reflect": false,
      "defaultValue": "true"
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
    }
  }; }
}
