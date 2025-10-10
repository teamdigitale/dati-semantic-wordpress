import { Component, h, Prop } from '@stencil/core';
import { isString } from '../../../utils/unit';
/**
 * @slot - Used to pass in SVG markup to be drawn by the browser.
 */
export class Icon {
  render() {
    return (h("svg", { xmlns: "http://www.w3.org/2000/svg", role: "presentation", focusable: "false" }, isString(this.href) ? h("use", { href: this.href }) : h("slot", null)));
  }
  static get is() { return "vime-icon"; }
  static get originalStyleUrls() { return {
    "$": ["icon.css"]
  }; }
  static get styleUrls() { return {
    "$": ["icon.css"]
  }; }
  static get properties() { return {
    "href": {
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
      "attribute": "href",
      "reflect": false
    }
  }; }
}
