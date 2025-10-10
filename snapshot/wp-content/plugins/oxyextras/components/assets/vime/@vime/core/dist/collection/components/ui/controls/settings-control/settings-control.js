import { h, Host, Component, Prop, Element, } from '@stencil/core';
import { withPlayerContext } from '../../../core/player/PlayerContext';
import { isUndefined } from '../../../../utils/unit';
import { findUIRoot } from '../../ui/utils';
let idCount = 0;
export class SettingsControl {
  constructor() {
    /**
     * The URL to an SVG element or fragment to load.
     */
    this.icon = '#vime-settings';
    /**
     * Whether the tooltip is positioned above/below the control.
     */
    this.tooltipPosition = 'top';
    /**
     * Whether the settings menu this control manages is open.
     */
    this.expanded = false;
    /**
     * @internal
     */
    this.i18n = {};
    withPlayerContext(this, ['i18n']);
  }
  connectedCallback() {
    idCount += 1;
    this.id = `vime-settings-control-${idCount}`;
    this.findSettings();
  }
  componentDidLoad() {
    this.findSettings();
  }
  findSettings() {
    var _a;
    const settings = (_a = findUIRoot(this)) === null || _a === void 0 ? void 0 : _a.querySelector('vime-settings');
    settings === null || settings === void 0 ? void 0 : settings.setController(this.id, this.el);
  }
  render() {
    const hasSettings = !isUndefined(this.menu);
    return (h(Host, { class: {
        hidden: !hasSettings,
        active: hasSettings && this.expanded,
      } },
      h("vime-control", { identifier: this.id, menu: this.menu, hidden: !hasSettings, expanded: this.expanded, label: this.i18n.settings },
        h("vime-icon", { href: this.icon }),
        h("vime-tooltip", { hidden: this.expanded, position: this.tooltipPosition, direction: this.tooltipDirection }, this.i18n.settings))));
  }
  static get is() { return "vime-settings-control"; }
  static get originalStyleUrls() { return {
    "$": ["settings-control.scss"]
  }; }
  static get styleUrls() { return {
    "$": ["settings-control.css"]
  }; }
  static get properties() { return {
    "icon": {
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
        "tags": [],
        "text": "The URL to an SVG element or fragment to load."
      },
      "attribute": "icon",
      "reflect": false,
      "defaultValue": "'#vime-settings'"
    },
    "tooltipPosition": {
      "type": "string",
      "mutable": false,
      "complexType": {
        "original": "TooltipPosition",
        "resolved": "\"bottom\" | \"top\"",
        "references": {
          "TooltipPosition": {
            "location": "import",
            "path": "../../tooltip/types"
          }
        }
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [],
        "text": "Whether the tooltip is positioned above/below the control."
      },
      "attribute": "tooltip-position",
      "reflect": false,
      "defaultValue": "'top'"
    },
    "tooltipDirection": {
      "type": "string",
      "mutable": false,
      "complexType": {
        "original": "TooltipDirection",
        "resolved": "\"left\" | \"right\" | undefined",
        "references": {
          "TooltipDirection": {
            "location": "import",
            "path": "../../tooltip/types"
          }
        }
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [],
        "text": "The direction in which the tooltip should grow."
      },
      "attribute": "tooltip-direction",
      "reflect": false
    },
    "menu": {
      "type": "string",
      "mutable": false,
      "complexType": {
        "original": "string",
        "resolved": "string | undefined",
        "references": {}
      },
      "required": false,
      "optional": true,
      "docs": {
        "tags": [],
        "text": "The DOM `id` of the settings menu this control is responsible for opening/closing."
      },
      "attribute": "menu",
      "reflect": false
    },
    "expanded": {
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
        "text": "Whether the settings menu this control manages is open."
      },
      "attribute": "expanded",
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
    }
  }; }
  static get elementRef() { return "el"; }
}
