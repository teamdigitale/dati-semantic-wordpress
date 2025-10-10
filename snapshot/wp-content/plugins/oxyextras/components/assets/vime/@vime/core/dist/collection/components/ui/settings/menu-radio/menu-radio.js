/* eslint-disable jsx-a11y/label-has-associated-control */
import { h, Component, Prop, Event, } from '@stencil/core';
export class MenuRadio {
  constructor() {
    /**
     * Whether the radio item is selected or not.
     */
    this.checked = false;
    /**
     * The URL to an SVG element or fragment to load.
     */
    this.checkedIcon = '#vime-checkmark';
  }
  onClick() {
    this.checked = true;
    this.vCheck.emit();
  }
  render() {
    return (h("vime-menu-item", { label: this.label, checked: this.checked, badge: this.badge, checkedIcon: this.checkedIcon, onClick: this.onClick.bind(this) }));
  }
  static get is() { return "vime-menu-radio"; }
  static get properties() { return {
    "label": {
      "type": "string",
      "mutable": false,
      "complexType": {
        "original": "string",
        "resolved": "string",
        "references": {}
      },
      "required": true,
      "optional": false,
      "docs": {
        "tags": [],
        "text": "The title of the radio item displayed to the user."
      },
      "attribute": "label",
      "reflect": false
    },
    "value": {
      "type": "string",
      "mutable": false,
      "complexType": {
        "original": "string",
        "resolved": "string",
        "references": {}
      },
      "required": true,
      "optional": false,
      "docs": {
        "tags": [],
        "text": "The value associated with this radio item."
      },
      "attribute": "value",
      "reflect": false
    },
    "checked": {
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
        "text": "Whether the radio item is selected or not."
      },
      "attribute": "checked",
      "reflect": false,
      "defaultValue": "false"
    },
    "badge": {
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
        "text": "This can provide additional context about the value. For example, if the option is for a set of\nvideo qualities, the badge could describe whether the quality is UHD, HD etc."
      },
      "attribute": "badge",
      "reflect": false
    },
    "checkedIcon": {
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
        "text": "The URL to an SVG element or fragment to load."
      },
      "attribute": "checked-icon",
      "reflect": false,
      "defaultValue": "'#vime-checkmark'"
    }
  }; }
  static get events() { return [{
      "method": "vCheck",
      "name": "vCheck",
      "bubbles": true,
      "cancelable": true,
      "composed": true,
      "docs": {
        "tags": [],
        "text": "Emitted when the radio button is selected."
      },
      "complexType": {
        "original": "void",
        "resolved": "void",
        "references": {}
      }
    }]; }
}
