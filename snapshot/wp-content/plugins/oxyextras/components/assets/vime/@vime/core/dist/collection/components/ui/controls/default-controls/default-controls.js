import { h, Host, Component, Prop, } from '@stencil/core';
import { withPlayerContext } from '../../../core/player/PlayerContext';
export class DefaultControls {
  constructor() {
    /**
     * The length in milliseconds that the controls are active for before fading out. Audio players
     * are not effected by this prop.
     */
    this.activeDuration = 2750;
    /**
     * Whether the controls should wait for playback to start before being shown. Audio players
     * are not effected by this prop.
     */
    this.waitForPlaybackStart = false;
    /**
     * Whether the controls should show/hide when paused. Audio players are not effected by this prop.
     */
    this.hideWhenPaused = false;
    /**
     * Whether the controls should hide when the mouse leaves the player. Audio players are not
     * effected by this prop.
     */
    this.hideOnMouseLeave = false;
    /**
     * @internal
     */
    this.isMobile = false;
    /**
     * @internal
     */
    this.isLive = false;
    /**
     * @internal
     */
    this.isAudioView = false;
    /**
     * @internal
     */
    this.isVideoView = false;
    withPlayerContext(this, [
      'theme',
      'isMobile',
      'isAudioView',
      'isVideoView',
      'isLive',
    ]);
  }
  buildAudioControls() {
    return (h("vime-controls", { fullWidth: true },
      h("vime-playback-control", { tooltipDirection: "right" }),
      h("vime-volume-control", null),
      !this.isLive && h("vime-current-time", null),
      this.isLive && h("vime-control-spacer", null),
      !this.isLive && h("vime-scrubber-control", null),
      this.isLive && h("vime-live-indicator", null),
      !this.isLive && h("vime-end-time", null),
      !this.isLive && h("vime-settings-control", { tooltipDirection: "left" }),
      h("div", { style: { marginLeft: '0', paddingRight: '2px' } })));
  }
  buildMobileVideoControls() {
    const lowerControls = (h("vime-controls", { pin: "bottomLeft", fullWidth: true, activeDuration: this.activeDuration, waitForPlaybackStart: this.waitForPlaybackStart, hideWhenPaused: this.hideWhenPaused },
      h("vime-control-group", null,
        h("vime-current-time", null),
        h("vime-control-spacer", null),
        h("vime-end-time", null),
        h("vime-fullscreen-control", null)),
      h("vime-control-group", { space: "top" },
        h("vime-scrubber-control", null))));
    return (h(Host, null,
      h("vime-scrim", null),
      h("vime-controls", { pin: "topLeft", fullWidth: true, activeDuration: this.activeDuration, waitForPlaybackStart: this.waitForPlaybackStart, hideWhenPaused: this.hideWhenPaused },
        h("vime-control-spacer", null),
        h("vime-volume-control", null),
        !this.isLive && h("vime-caption-control", null),
        !this.isLive && h("vime-settings-control", null),
        this.isLive && h("vime-fullscreen-control", null)),
      h("vime-controls", { pin: "center", activeDuration: this.activeDuration, waitForPlaybackStart: this.waitForPlaybackStart, hideWhenPaused: this.hideWhenPaused },
        h("vime-playback-control", { style: { '--vm-control-scale': '1.5' } })),
      !this.isLive && lowerControls));
  }
  buildDesktopVideoControls() {
    const scrubberControlGroup = (h("vime-control-group", null,
      h("vime-scrubber-control", null)));
    return (h(Host, null,
      (this.theme !== 'light') && h("vime-scrim", { gradient: "up" }),
      h("vime-controls", { activeDuration: this.activeDuration, waitForPlaybackStart: this.waitForPlaybackStart, hideWhenPaused: this.hideWhenPaused, hideOnMouseLeave: this.hideOnMouseLeave, fullWidth: true },
        !this.isLive && scrubberControlGroup,
        h("vime-control-group", { space: this.isLive ? 'none' : 'top' },
          h("vime-playback-control", { tooltipDirection: "right" }),
          h("vime-volume-control", null),
          !this.isLive && h("vime-time-progress", null),
          h("vime-control-spacer", null),
          !this.isLive && h("vime-caption-control", null),
          this.isLive && h("vime-live-indicator", null),
          h("vime-pip-control", null),
          !this.isLive && h("vime-settings-control", null),
          h("vime-fullscreen-control", { tooltipDirection: "left" })))));
  }
  render() {
    if (this.isAudioView)
      return this.buildAudioControls();
    if (this.isVideoView && this.isMobile)
      return this.buildMobileVideoControls();
    if (this.isVideoView)
      return this.buildDesktopVideoControls();
    return undefined;
  }
  static get is() { return "vime-default-controls"; }
  static get originalStyleUrls() { return {
    "$": ["default-controls.css"]
  }; }
  static get styleUrls() { return {
    "$": ["default-controls.css"]
  }; }
  static get properties() { return {
    "activeDuration": {
      "type": "number",
      "mutable": false,
      "complexType": {
        "original": "number",
        "resolved": "number",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [],
        "text": "The length in milliseconds that the controls are active for before fading out. Audio players\nare not effected by this prop."
      },
      "attribute": "active-duration",
      "reflect": false,
      "defaultValue": "2750"
    },
    "waitForPlaybackStart": {
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
        "text": "Whether the controls should wait for playback to start before being shown. Audio players\nare not effected by this prop."
      },
      "attribute": "wait-for-playback-start",
      "reflect": false,
      "defaultValue": "false"
    },
    "hideWhenPaused": {
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
        "text": "Whether the controls should show/hide when paused. Audio players are not effected by this prop."
      },
      "attribute": "hide-when-paused",
      "reflect": false,
      "defaultValue": "false"
    },
    "hideOnMouseLeave": {
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
        "text": "Whether the controls should hide when the mouse leaves the player. Audio players are not\neffected by this prop."
      },
      "attribute": "hide-on-mouse-leave",
      "reflect": false,
      "defaultValue": "false"
    },
    "theme": {
      "type": "string",
      "mutable": false,
      "complexType": {
        "original": "PlayerProps['theme']",
        "resolved": "string | undefined",
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
      },
      "attribute": "theme",
      "reflect": false
    },
    "isMobile": {
      "type": "boolean",
      "mutable": false,
      "complexType": {
        "original": "PlayerProps['isMobile']",
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
      "attribute": "is-mobile",
      "reflect": false,
      "defaultValue": "false"
    },
    "isLive": {
      "type": "boolean",
      "mutable": false,
      "complexType": {
        "original": "PlayerProps['isLive']",
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
      "attribute": "is-live",
      "reflect": false,
      "defaultValue": "false"
    },
    "isAudioView": {
      "type": "boolean",
      "mutable": false,
      "complexType": {
        "original": "PlayerProps['isAudioView']",
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
      "attribute": "is-audio-view",
      "reflect": false,
      "defaultValue": "false"
    },
    "isVideoView": {
      "type": "boolean",
      "mutable": false,
      "complexType": {
        "original": "PlayerProps['isVideoView']",
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
      "attribute": "is-video-view",
      "reflect": false,
      "defaultValue": "false"
    }
  }; }
}
