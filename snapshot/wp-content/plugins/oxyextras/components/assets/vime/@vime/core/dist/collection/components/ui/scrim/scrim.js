import { h, Component, Host, Prop, } from '@stencil/core';
import { withPlayerContext } from '../../core/player/PlayerContext';
import { isUndefined } from '../../../utils/unit';
export class Scrim {
  constructor() {
    /**
     * @internal
     */
    this.isVideoView = false;
    /**
     * @internal
     */
    this.isControlsActive = false;
    withPlayerContext(this, ['isVideoView', 'isControlsActive']);
  }
  render() {
    return (h(Host, { class: {
        gradient: !isUndefined(this.gradient),
        gradientUp: this.gradient === 'up',
        gradientDown: this.gradient === 'down',
        hidden: !this.isVideoView,
        active: this.isControlsActive,
      } },
      h("div", null)));
  }
  static get is() { return "vime-scrim"; }
  static get originalStyleUrls() { return {
    "$": ["scrim.scss"]
  }; }
  static get styleUrls() { return {
    "$": ["scrim.css"]
  }; }
  static get properties() { return {
    "gradient": {
      "type": "string",
      "mutable": false,
      "complexType": {
        "original": "'up' | 'down'",
        "resolved": "\"down\" | \"up\" | undefined",
        "references": {}
      },
      "required": false,
      "optional": true,
      "docs": {
        "tags": [],
        "text": "If this prop is defined, a dark gradient that smoothly fades out without being noticed will be\nused instead of a set color. This prop also sets the direction in which the dark end of the\ngradient should start. If the direction is set to `up`, the dark end of the gradient will\nstart at the bottom of the player and fade out to the center. If the direction is set to\n`down`, the gradient will start at the top of the player and fade out to the center."
      },
      "attribute": "gradient",
      "reflect": false
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
    }
  }; }
}
