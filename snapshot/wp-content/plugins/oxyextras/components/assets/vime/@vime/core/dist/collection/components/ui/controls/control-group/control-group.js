import { h, Host, Component, Element, Prop, } from '@stencil/core';
export class ControlNewLine {
  constructor() {
    /**
     * Determines where to add spacing/margin. The amount of spacing is determined by the CSS variable
     * `--control-group-spacing`.
     */
    this.space = 'none';
  }
  render() {
    return (h(Host, { class: {
        spaceTop: (this.space !== 'none' && this.space !== 'bottom'),
        spaceBottom: (this.space !== 'none' && this.space !== 'top'),
      } },
      h("slot", null)));
  }
  static get is() { return "vime-control-group"; }
  static get originalStyleUrls() { return {
    "$": ["control-group.scss"]
  }; }
  static get styleUrls() { return {
    "$": ["control-group.css"]
  }; }
  static get properties() { return {
    "space": {
      "type": "string",
      "mutable": false,
      "complexType": {
        "original": "'top' | 'bottom' | 'both' | 'none'",
        "resolved": "\"both\" | \"bottom\" | \"none\" | \"top\"",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [],
        "text": "Determines where to add spacing/margin. The amount of spacing is determined by the CSS variable\n`--control-group-spacing`."
      },
      "attribute": "space",
      "reflect": false,
      "defaultValue": "'none'"
    }
  }; }
  static get elementRef() { return "el"; }
}
