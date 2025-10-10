import { h, Host, Component, Prop, } from '@stencil/core';
import { isNull } from '../../../../utils/unit';
let idCount = 0;
/**
 * @slot - Used to pass in the body of the submenu which is usually a set of choices in the form
 * of a radio group (`vime-menu-radio-group`).
 */
export class Submenu {
  constructor() {
    /**
     * Whether the submenu should be displayed or not.
     */
    this.hidden = false;
    /**
     * Whether the submenu is open/closed.
     */
    this.active = false;
  }
  connectedCallback() {
    this.genId();
  }
  invalidEventTarget(event) {
    return isNull(event.target) || event.target.id !== this.id;
  }
  onOpen(event) {
    if (this.invalidEventTarget(event))
      return;
    this.active = true;
  }
  onClose(event) {
    if (this.invalidEventTarget(event))
      return;
    this.active = false;
  }
  genId() {
    idCount += 1;
    this.id = `vime-submenu-${idCount}`;
  }
  getControllerId() {
    return `${this.id}-controller`;
  }
  render() {
    return (h(Host, null,
      h("vime-menu-item", { identifier: this.getControllerId(), hidden: this.hidden, menu: this.id, label: this.label, hint: this.hint, expanded: this.active }),
      h("vime-menu", { identifier: this.id, controller: this.getControllerId(), active: this.active, onVOpen: this.onOpen.bind(this), onVClose: this.onClose.bind(this) },
        h("slot", null))));
  }
  static get is() { return "vime-submenu"; }
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
        "text": "The title of the submenu."
      },
      "attribute": "label",
      "reflect": false
    },
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
        "text": "Whether the submenu should be displayed or not."
      },
      "attribute": "hidden",
      "reflect": false,
      "defaultValue": "false"
    },
    "hint": {
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
        "text": "This can provide additional context about the current state of the submenu. For example, the\nhint could be the currently selected option if the submenu contains a radio group."
      },
      "attribute": "hint",
      "reflect": false
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
        "text": "Whether the submenu is open/closed."
      },
      "attribute": "active",
      "reflect": true,
      "defaultValue": "false"
    }
  }; }
}
