import { h, Component, Element, Prop, Watch, Event, Listen, } from '@stencil/core';
/**
 * @slot - Used to pass in radio buttons (`vime-menu-radio`).
 */
export class MenuRadioGroup {
  onValueChange() {
    this.findRadios().forEach((radio) => {
      // eslint-disable-next-line no-param-reassign
      radio.checked = (radio.value === this.value);
    });
  }
  connectedCallback() {
    this.onValueChange();
  }
  onSelectionChange(event) {
    const radio = event.target;
    this.value = radio.value;
  }
  findRadios() {
    return this.el.querySelectorAll('vime-menu-radio');
  }
  render() {
    return (h("slot", null));
  }
  static get is() { return "vime-menu-radio-group"; }
  static get properties() { return {
    "value": {
      "type": "string",
      "mutable": true,
      "complexType": {
        "original": "string",
        "resolved": "string | undefined",
        "references": {}
      },
      "required": false,
      "optional": true,
      "docs": {
        "tags": [],
        "text": "The current value selected for this group."
      },
      "attribute": "value",
      "reflect": false
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
        "text": "Emitted when a new radio button is selected for this group."
      },
      "complexType": {
        "original": "void",
        "resolved": "void",
        "references": {}
      }
    }]; }
  static get elementRef() { return "el"; }
  static get watchers() { return [{
      "propName": "value",
      "methodName": "onValueChange"
    }]; }
  static get listeners() { return [{
      "name": "vCheck",
      "method": "onSelectionChange",
      "target": undefined,
      "capture": false,
      "passive": false
    }]; }
}
