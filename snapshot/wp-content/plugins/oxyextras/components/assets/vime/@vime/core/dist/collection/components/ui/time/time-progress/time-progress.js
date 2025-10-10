import { h, Host, Component, Prop, } from '@stencil/core';
export class TimeProgress {
  constructor() {
    /**
     * The string used to separate the current time and end time.
     */
    this.separator = '/';
    /**
     * Whether the times should always show the hours unit, even if the time is less than
     * 1 hour (eg: `20:35` -> `00:20:35`).
     */
    this.alwaysShowHours = false;
  }
  render() {
    return (h(Host, null,
      h("vime-current-time", { alwaysShowHours: this.alwaysShowHours }),
      h("span", null, this.separator),
      h("vime-end-time", { alwaysShowHours: this.alwaysShowHours })));
  }
  static get is() { return "vime-time-progress"; }
  static get originalStyleUrls() { return {
    "$": ["time-progress.css"]
  }; }
  static get styleUrls() { return {
    "$": ["time-progress.css"]
  }; }
  static get properties() { return {
    "separator": {
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
        "text": "The string used to separate the current time and end time."
      },
      "attribute": "separator",
      "reflect": false,
      "defaultValue": "'/'"
    },
    "alwaysShowHours": {
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
        "text": "Whether the times should always show the hours unit, even if the time is less than\n1 hour (eg: `20:35` -> `00:20:35`)."
      },
      "attribute": "always-show-hours",
      "reflect": false,
      "defaultValue": "false"
    }
  }; }
}
