import { h, Component, Prop } from '@stencil/core';
/**
 * @slot - Used to extend the default user interface with custom UI components.
 */
export class DefaultUI {
  constructor() {
    /**
     * Whether the default icons should not be loaded.
     */
    this.noIcons = false;
    /**
     * Whether clicking the player should not toggle playback.
     */
    this.noClickToPlay = false;
    /**
     * Whether double clicking the player should not toggle fullscreen mode.
     */
    this.noDblClickFullscreen = false;
    /**
     * Whether the custom captions UI should not be loaded.
     */
    this.noCaptions = false;
    /**
     * Whether the custom poster UI should not be loaded.
     */
    this.noPoster = false;
    /**
     * Whether the custom spinner UI should not be loaded.
     */
    this.noSpinner = false;
    /**
     * Whether the custom default controls should not be loaded.
     */
    this.noControls = false;
    /**
     * Whether the custom default settings menu should not be loaded.
     */
    this.noSettings = false;
    /**
     * Whether the skeleton loading animation should be shown while the player is loading.
     */
    this.noSkeleton = false;
  }
  render() {
    return (h("vime-ui", null,
      !this.noIcons && h("vime-icons", null),
      !this.noSkeleton && h("vime-skeleton", null),
      !this.noClickToPlay && h("vime-click-to-play", null),
      !this.noDblClickFullscreen && h("vime-dbl-click-fullscreen", null),
      !this.noCaptions && h("vime-captions", null),
      !this.noPoster && h("vime-poster", null),
      !this.noSpinner && h("vime-spinner", null),
      !this.noControls && h("vime-default-controls", null),
      !this.noSettings && h("vime-default-settings", null),
      h("slot", null)));
  }
  static get is() { return "vime-default-ui"; }
  static get properties() { return {
    "noIcons": {
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
        "text": "Whether the default icons should not be loaded."
      },
      "attribute": "no-icons",
      "reflect": false,
      "defaultValue": "false"
    },
    "noClickToPlay": {
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
        "text": "Whether clicking the player should not toggle playback."
      },
      "attribute": "no-click-to-play",
      "reflect": false,
      "defaultValue": "false"
    },
    "noDblClickFullscreen": {
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
        "text": "Whether double clicking the player should not toggle fullscreen mode."
      },
      "attribute": "no-dbl-click-fullscreen",
      "reflect": false,
      "defaultValue": "false"
    },
    "noCaptions": {
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
        "text": "Whether the custom captions UI should not be loaded."
      },
      "attribute": "no-captions",
      "reflect": false,
      "defaultValue": "false"
    },
    "noPoster": {
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
        "text": "Whether the custom poster UI should not be loaded."
      },
      "attribute": "no-poster",
      "reflect": false,
      "defaultValue": "false"
    },
    "noSpinner": {
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
        "text": "Whether the custom spinner UI should not be loaded."
      },
      "attribute": "no-spinner",
      "reflect": false,
      "defaultValue": "false"
    },
    "noControls": {
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
        "text": "Whether the custom default controls should not be loaded."
      },
      "attribute": "no-controls",
      "reflect": false,
      "defaultValue": "false"
    },
    "noSettings": {
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
        "text": "Whether the custom default settings menu should not be loaded."
      },
      "attribute": "no-settings",
      "reflect": false,
      "defaultValue": "false"
    },
    "noSkeleton": {
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
        "text": "Whether the skeleton loading animation should be shown while the player is loading."
      },
      "attribute": "no-skeleton",
      "reflect": false,
      "defaultValue": "false"
    }
  }; }
}
