import { h, Host, Component, Element, Prop, } from '@stencil/core';
import { isString } from '../../../utils/unit';
import { withPlayerContext } from '../../core/player/PlayerContext';
let tooltipIdCount = 0;
/**
 * @slot - Used to pass in the contents of the tooltip.
 */
export class Tooltip {
  constructor() {
    // Avoid tooltips flashing when player initializing.
    this.hasLoaded = false;
    /**
     * Whether the tooltip is displayed or not.
     */
    this.hidden = false;
    /**
     * Whether the tooltip is visible or not.
     */
    this.active = false;
    /**
     * Determines if the tooltip appears on top/bottom of it's parent.
     */
    this.position = 'top';
    /**
     * @internal
     */
    this.isTouch = false;
    withPlayerContext(this, ['isTouch']);
  }
  componentDidLoad() {
    this.hasLoaded = true;
  }
  getId() {
    // eslint-disable-next-line prefer-destructuring
    const id = this.el.id;
    if (isString(id) && id.length > 0)
      return id;
    tooltipIdCount += 1;
    return `vime-tooltip-${tooltipIdCount}`;
  }
  render() {
    return (h(Host, { id: this.getId(), role: "tooltip", "aria-hidden": (!this.active || this.isTouch) ? 'true' : 'false', class: {
        hidden: !this.hasLoaded || this.hidden,
        onTop: (this.position === 'top'),
        onBottom: (this.position === 'bottom'),
        growLeft: (this.direction === 'left'),
        growRight: (this.direction === 'right'),
      } },
      h("slot", null)));
  }
  static get is() { return "vime-tooltip"; }
  static get originalStyleUrls() { return {
    "$": ["tooltip.scss"]
  }; }
  static get styleUrls() { return {
    "$": ["tooltip.css"]
  }; }
  static get properties() { return {
    "hidden": {
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
        "text": "Whether the tooltip is displayed or not."
      },
      "attribute": "hidden",
      "reflect": false,
      "defaultValue": "false"
    },
    "active": {
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
        "text": "Whether the tooltip is visible or not."
      },
      "attribute": "active",
      "reflect": false,
      "defaultValue": "false"
    },
    "position": {
      "type": "string",
      "mutable": false,
      "complexType": {
        "original": "TooltipPosition",
        "resolved": "\"bottom\" | \"top\"",
        "references": {
          "TooltipPosition": {
            "location": "import",
            "path": "./types"
          }
        }
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [],
        "text": "Determines if the tooltip appears on top/bottom of it's parent."
      },
      "attribute": "position",
      "reflect": false,
      "defaultValue": "'top'"
    },
    "direction": {
      "type": "string",
      "mutable": false,
      "complexType": {
        "original": "TooltipDirection",
        "resolved": "\"left\" | \"right\" | undefined",
        "references": {
          "TooltipDirection": {
            "location": "import",
            "path": "./types"
          }
        }
      },
      "required": false,
      "optional": true,
      "docs": {
        "tags": [],
        "text": "Determines if the tooltip should grow according to its contents to the left/right. By default\ncontent grows outwards from the center."
      },
      "attribute": "direction",
      "reflect": false
    },
    "isTouch": {
      "type": "boolean",
      "mutable": false,
      "complexType": {
        "original": "PlayerProps['isTouch']",
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
      "attribute": "is-touch",
      "reflect": false,
      "defaultValue": "false"
    }
  }; }
  static get elementRef() { return "el"; }
}
