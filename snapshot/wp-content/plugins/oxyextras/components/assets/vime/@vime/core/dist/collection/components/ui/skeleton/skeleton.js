import { h, Host, Component, Prop, State, Watch, } from '@stencil/core';
import { withPlayerContext } from '../../core/player/PlayerContext';
export class Skeleton {
  constructor() {
    this.hidden = false;
    /**
     * Determines which effect the skeleton will use.
     * */
    this.effect = 'sheen';
    /**
     * @internal
     */
    this.ready = false;
    withPlayerContext(this, ['ready']);
  }
  onReadyChange() {
    if (!this.ready) {
      this.hidden = false;
    }
    else {
      setTimeout(() => {
        this.hidden = true;
      }, 500);
    }
  }
  render() {
    return (h(Host, { class: {
        hidden: this.hidden,
        sheen: (this.effect === 'sheen'),
      } },
      h("div", { class: "indicator" })));
  }
  static get is() { return "vime-skeleton"; }
  static get originalStyleUrls() { return {
    "$": ["skeleton.scss"]
  }; }
  static get styleUrls() { return {
    "$": ["skeleton.css"]
  }; }
  static get properties() { return {
    "effect": {
      "type": "string",
      "mutable": false,
      "complexType": {
        "original": "'sheen' | 'none'",
        "resolved": "\"none\" | \"sheen\"",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [],
        "text": "Determines which effect the skeleton will use."
      },
      "attribute": "effect",
      "reflect": false,
      "defaultValue": "'sheen'"
    },
    "ready": {
      "type": "boolean",
      "mutable": false,
      "complexType": {
        "original": "PlayerProps['ready']",
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
      "attribute": "ready",
      "reflect": false,
      "defaultValue": "false"
    }
  }; }
  static get states() { return {
    "hidden": {}
  }; }
  static get watchers() { return [{
      "propName": "ready",
      "methodName": "onReadyChange"
    }]; }
}
