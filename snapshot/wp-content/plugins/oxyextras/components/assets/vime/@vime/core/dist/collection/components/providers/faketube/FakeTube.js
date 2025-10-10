import { Prop, Method, Component, Event, } from '@stencil/core';
import { withProviderContext, } from '../MediaProvider';
import { createProviderDispatcher } from '../ProviderDispatcher';
import { findRootPlayer } from '../../core/player/utils';
export class FakeTube {
  constructor() {
    /**
     * @internal
     */
    this.language = 'en';
    /**
     * @internal
     */
    this.autoplay = false;
    /**
     * @internal
     */
    this.controls = false;
    /**
     * @internal
     */
    this.loop = false;
    /**
     * @internal
     */
    this.muted = false;
    /**
     * @internal
     */
    this.playsinline = false;
    withProviderContext(this);
  }
  connectedCallback() {
    this.dispatch = createProviderDispatcher(this);
  }
  componentWillLoad() {
    const player = findRootPlayer(this);
    player.setProvider(this);
  }
  /**
   * Returns a mock adapter.
   */
  async getAdapter() {
    return {
      getInternalPlayer: jest.fn(),
      play: jest.fn(),
      pause: jest.fn(),
      canPlay: jest.fn(),
      setCurrentTime: jest.fn(),
      setMuted: jest.fn(),
      setVolume: jest.fn(),
      canSetPlaybackRate: jest.fn(),
      setPlaybackRate: jest.fn(),
      canSetPlaybackQuality: jest.fn(),
      setPlaybackQuality: jest.fn(),
      canSetFullscreen: jest.fn(),
      enterFullscreen: jest.fn(),
      exitFullscreen: jest.fn(),
      canSetPiP: jest.fn(),
      enterPiP: jest.fn(),
      exitPiP: jest.fn(),
    };
  }
  /**
   * Dispatches the `vLoadStart` event.
   */
  async dispatchLoadStart() {
    this.vLoadStart.emit();
  }
  /**
   * Dispatches a state change event.
   */
  async dispatchChange(prop, value) {
    this.dispatch(prop, value);
  }
  static get is() { return "vime-faketube"; }
  static get originalStyleUrls() { return {
    "$": ["faketube.css"]
  }; }
  static get styleUrls() { return {
    "$": ["faketube.css"]
  }; }
  static get properties() { return {
    "language": {
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
        "tags": [{
            "text": undefined,
            "name": "internal"
          }],
        "text": ""
      },
      "attribute": "language",
      "reflect": false,
      "defaultValue": "'en'"
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
    "controls": {
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
      "attribute": "controls",
      "reflect": false,
      "defaultValue": "false"
    },
    "logger": {
      "type": "unknown",
      "mutable": false,
      "complexType": {
        "original": "Logger",
        "resolved": "Logger | undefined",
        "references": {
          "Logger": {
            "location": "import",
            "path": "../../core/player/PlayerLogger"
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
    "loop": {
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
      "attribute": "loop",
      "reflect": false,
      "defaultValue": "false"
    },
    "muted": {
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
      "attribute": "muted",
      "reflect": false,
      "defaultValue": "false"
    },
    "playsinline": {
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
      "attribute": "playsinline",
      "reflect": false,
      "defaultValue": "false"
    }
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
        "signature": "() => Promise<MockMediaProviderAdapter>",
        "parameters": [],
        "references": {
          "Promise": {
            "location": "global"
          },
          "MockMediaProviderAdapter": {
            "location": "import",
            "path": "../MediaProvider"
          }
        },
        "return": "Promise<MockMediaProviderAdapter>"
      },
      "docs": {
        "text": "Returns a mock adapter.",
        "tags": []
      }
    },
    "dispatchLoadStart": {
      "complexType": {
        "signature": "() => Promise<void>",
        "parameters": [],
        "references": {
          "Promise": {
            "location": "global"
          }
        },
        "return": "Promise<void>"
      },
      "docs": {
        "text": "Dispatches the `vLoadStart` event.",
        "tags": []
      }
    },
    "dispatchChange": {
      "complexType": {
        "signature": "(prop: PlayerProp, value: any) => Promise<void>",
        "parameters": [{
            "tags": [],
            "text": ""
          }, {
            "tags": [],
            "text": ""
          }],
        "references": {
          "Promise": {
            "location": "global"
          },
          "PlayerProp": {
            "location": "import",
            "path": "../../core/player/PlayerProps"
          }
        },
        "return": "Promise<void>"
      },
      "docs": {
        "text": "Dispatches a state change event.",
        "tags": []
      }
    }
  }; }
}
