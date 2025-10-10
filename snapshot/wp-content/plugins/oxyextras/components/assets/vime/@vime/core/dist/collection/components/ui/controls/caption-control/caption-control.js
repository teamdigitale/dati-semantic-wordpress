import { h, Host, Component, Prop, } from '@stencil/core';
import { isUndefined } from '../../../../utils/unit';
import { findRootPlayer } from '../../../core/player/utils';
import { withPlayerContext } from '../../../core/player/PlayerContext';
export class CaptionControl {
  constructor() {
    /**
     * The URL to an SVG element or fragment to load.
     */
    this.showIcon = '#vime-captions-on';
    /**
     * The URL to an SVG element or fragment to load.
     */
    this.hideIcon = '#vime-captions-off';
    /**
     * Whether the tooltip is positioned above/below the control.
     */
    this.tooltipPosition = 'top';
    /**
     * Whether the tooltip should not be displayed.
     */
    this.hideTooltip = false;
    /**
     * @inheritdoc
     */
    this.keys = 'c';
    /**
     * @internal
     */
    this.isCaptionsActive = false;
    /**
     * @internal
     */
    this.i18n = {};
    withPlayerContext(this, [
      'isCaptionsActive',
      'currentCaption',
      'i18n',
    ]);
  }
  onClick() {
    const player = findRootPlayer(this);
    player.toggleCaptionsVisibility();
  }
  render() {
    const tooltip = this.isCaptionsActive ? this.i18n.disableCaptions : this.i18n.enableCaptions;
    const tooltipWithHint = !isUndefined(this.keys) ? `${tooltip} (${this.keys})` : tooltip;
    return (h(Host, { class: {
        hidden: isUndefined(this.currentCaption),
      } },
      h("vime-control", { label: this.i18n.captions, keys: this.keys, hidden: isUndefined(this.currentCaption), pressed: this.isCaptionsActive, onClick: this.onClick.bind(this) },
        h("vime-icon", { href: this.isCaptionsActive ? this.showIcon : this.hideIcon }),
        h("vime-tooltip", { hidden: this.hideTooltip, position: this.tooltipPosition, direction: this.tooltipDirection }, tooltipWithHint))));
  }
  static get is() { return "vime-caption-control"; }
  static get originalStyleUrls() { return {
    "$": ["caption-control.css"]
  }; }
  static get styleUrls() { return {
    "$": ["caption-control.css"]
  }; }
  static get properties() { return {
    "showIcon": {
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
      "attribute": "show-icon",
      "reflect": false,
      "defaultValue": "'#vime-captions-on'"
    },
    "hideIcon": {
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
      "attribute": "hide-icon",
      "reflect": false,
      "defaultValue": "'#vime-captions-off'"
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
    "hideTooltip": {
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
        "text": "Whether the tooltip should not be displayed."
      },
      "attribute": "hide-tooltip",
      "reflect": false,
      "defaultValue": "false"
    },
    "keys": {
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
        "tags": [{
            "text": undefined,
            "name": "inheritdoc"
          }],
        "text": "A slash (`/`) separated string of JS keyboard keys (`KeyboardEvent.key`), that when caught in\na `keydown` event, will trigger a `click` event on the control."
      },
      "attribute": "keys",
      "reflect": false,
      "defaultValue": "'c'"
    },
    "currentCaption": {
      "type": "unknown",
      "mutable": false,
      "complexType": {
        "original": "PlayerProps['currentCaption']",
        "resolved": "TextTrack | undefined",
        "references": {
          "PlayerProps": {
            "location": "import",
            "path": "../../../core/player/PlayerProps"
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
    "isCaptionsActive": {
      "type": "boolean",
      "mutable": false,
      "complexType": {
        "original": "PlayerProps['isCaptionsActive']",
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
      "attribute": "is-captions-active",
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
}
