import { h, Host, Component, Prop, } from '@stencil/core';
import { withPlayerContext } from '../../core/player/PlayerContext';
export class LiveIndicator {
  constructor() {
    /**
     * @internal
     */
    this.isLive = false;
    /**
     * @internal
     */
    this.i18n = {};
    withPlayerContext(this, ['isLive', 'i18n']);
  }
  render() {
    return (h(Host, { class: {
        hidden: !this.isLive,
      } },
      h("div", { class: "indicator" }),
      this.i18n.live));
  }
  static get is() { return "vime-live-indicator"; }
  static get originalStyleUrls() { return {
    "$": ["live-indicator.scss"]
  }; }
  static get styleUrls() { return {
    "$": ["live-indicator.css"]
  }; }
  static get properties() { return {
    "isLive": {
      "type": "boolean",
      "mutable": false,
      "complexType": {
        "original": "PlayerProps['isLive']",
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
      "attribute": "is-live",
      "reflect": false,
      "defaultValue": "false"
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
      "defaultValue": "{}"
    }
  }; }
}
