/* eslint-disable no-param-reassign */
import { h, Host, Component, Prop, Watch, Method, Element, State, } from '@stencil/core';
import { Disposal } from '../../../core/player/Disposal';
import { listen } from '../../../../utils/dom';
import { isUndefined, isNull } from '../../../../utils/unit';
import { createDispatcher } from '../../../core/player/PlayerDispatcher';
import { withPlayerContext } from '../../../core/player/PlayerContext';
let idCount = 0;
/**
 * @slot - Used to pass in the body of the settings menu, which usually contains submenus.
 */
export class Settings {
  constructor() {
    this.disposal = new Disposal();
    /**
     * The height of any lower control bar in pixels so that the settings can re-position itself
     * accordingly.
     */
    this.controlsHeight = 0;
    /**
     * Pins the settings to the defined position inside the video player. This has no effect when
     * the view is of type `audio` (always `bottomRight`) and on mobile devices (always bottom sheet).
     */
    this.pin = 'bottomRight';
    /**
     * Whether the settings menu is opened/closed.
     */
    this.active = false;
    /**
     * @internal
     */
    this.isMobile = false;
    /**
     * @internal
     */
    this.isAudioView = false;
    withPlayerContext(this, ['isMobile', 'isAudioView']);
  }
  onActiveChange() {
    this.dispatch('isSettingsActive', this.active);
    if (isUndefined(this.controller))
      return;
    this.controller.expanded = this.active;
  }
  connectedCallback() {
    this.dispatch = createDispatcher(this);
    idCount += 1;
    this.id = `vime-settings-${idCount}`;
  }
  disconnectedCallback() {
    this.disposal.empty();
  }
  /**
   * Sets the controller responsible for opening/closing this settings.
   */
  async setController(id, controller) {
    this.controllerId = id;
    this.controller = controller;
    this.controller.menu = this.id;
    this.disposal.empty();
    this.disposal.add(listen(this.controller, 'click', () => { this.active = !this.active; }));
    this.disposal.add(listen(this.controller, 'keydown', (event) => {
      if (event.key !== 'Enter')
        return;
      // We're looking for !active because the `click` event above will toggle it to active.
      if (!this.active)
        this.menu.focusOnOpen();
    }));
  }
  getPosition() {
    if (this.isAudioView) {
      return {
        right: '0',
        bottom: `${this.controlsHeight}px`,
      };
    }
    // topLeft => { top: 0, left: 0 }
    const pos = this.pin.split(/(?=[L|R])/).map((s) => s.toLowerCase());
    return {
      [pos.includes('top') ? 'top' : 'bottom']: `${this.controlsHeight}px`,
      [pos.includes('left') ? 'left' : 'right']: '8px',
    };
  }
  onClose(event) {
    if (isNull(event.target) || event.target.id !== this.id)
      return;
    this.active = false;
  }
  render() {
    var _a;
    return (h(Host, { style: Object.assign({}, this.getPosition()), class: {
        active: this.active,
        mobile: this.isMobile,
      } },
      h("vime-menu", { identifier: this.id, active: this.active, controller: (_a = this.controllerId) !== null && _a !== void 0 ? _a : '', onVClose: this.onClose.bind(this), ref: (el) => { this.menu = el; } },
        h("slot", null))));
  }
  static get is() { return "vime-settings"; }
  static get originalStyleUrls() { return {
    "$": ["settings.scss"]
  }; }
  static get styleUrls() { return {
    "$": ["settings.css"]
  }; }
  static get properties() { return {
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
        "text": "The height of any lower control bar in pixels so that the settings can re-position itself\naccordingly."
      },
      "attribute": "controls-height",
      "reflect": false,
      "defaultValue": "0"
    },
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
        "text": "Pins the settings to the defined position inside the video player. This has no effect when\nthe view is of type `audio` (always `bottomRight`) and on mobile devices (always bottom sheet)."
      },
      "attribute": "pin",
      "reflect": true,
      "defaultValue": "'bottomRight'"
    },
    "active": {
      "type": "boolean",
      "mutable": true,
      "complexType": {
        "original": "boolean",
        "resolved": "boolean",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [],
        "text": "Whether the settings menu is opened/closed."
      },
      "attribute": "active",
      "reflect": true,
      "defaultValue": "false"
    },
    "isMobile": {
      "type": "boolean",
      "mutable": false,
      "complexType": {
        "original": "PlayerProps['isMobile']",
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
      "attribute": "is-mobile",
      "reflect": false,
      "defaultValue": "false"
    },
    "isAudioView": {
      "type": "boolean",
      "mutable": false,
      "complexType": {
        "original": "PlayerProps['isAudioView']",
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
      "attribute": "is-audio-view",
      "reflect": false,
      "defaultValue": "false"
    }
  }; }
  static get states() { return {
    "controllerId": {}
  }; }
  static get methods() { return {
    "setController": {
      "complexType": {
        "signature": "(id: string, controller: SettingsController) => Promise<void>",
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
          "SettingsController": {
            "location": "import",
            "path": "./SettingsController"
          },
          "KeyboardEvent": {
            "location": "global"
          }
        },
        "return": "Promise<void>"
      },
      "docs": {
        "text": "Sets the controller responsible for opening/closing this settings.",
        "tags": []
      }
    }
  }; }
  static get elementRef() { return "el"; }
  static get watchers() { return [{
      "propName": "active",
      "methodName": "onActiveChange"
    }]; }
}
