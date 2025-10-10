import { h, Component, Host, Prop, Watch, State, } from '@stencil/core';
import { withPlayerContext } from '../../core/player/PlayerContext';
import { IS_MOBILE } from '../../../utils/support';
import { findRootPlayer } from '../../core/player/utils';
import { findUIRoot } from '../ui/utils';
export class DblClickFullscreen {
  constructor() {
    this.canSetFullscreen = false;
    /**
     * By default this is disabled on mobile to not interfere with playback, set this to `true` to
     * enable it.
     */
    this.useOnMobile = false;
    /**
     * @internal
     */
    this.isFullscreenActive = true;
    /**
     * @internal
     */
    this.isVideoView = false;
    /**
     * @internal
     */
    this.playbackReady = false;
    this.clicks = 0;
    withPlayerContext(this, [
      'playbackReady',
      'isFullscreenActive',
      'isVideoView',
    ]);
  }
  async onPlaybackReadyChange() {
    const player = findRootPlayer(this);
    this.canSetFullscreen = await player.canSetFullscreen();
  }
  onTriggerClickToPlay() {
    const ui = findUIRoot(this);
    const clickToPlay = ui === null || ui === void 0 ? void 0 : ui.querySelector('vime-click-to-play');
    clickToPlay === null || clickToPlay === void 0 ? void 0 : clickToPlay.dispatchEvent(new Event('click'));
  }
  onToggleFullscreen() {
    const player = findRootPlayer(this);
    this.isFullscreenActive ? player.exitFullscreen() : player.enterFullscreen();
  }
  onClick() {
    this.clicks += 1;
    if (this.clicks === 1) {
      setTimeout(() => {
        if (this.clicks === 1) {
          this.onTriggerClickToPlay();
        }
        else {
          this.onToggleFullscreen();
        }
        this.clicks = 0;
      }, 300);
    }
  }
  render() {
    return (h(Host, { class: {
        enabled: this.playbackReady
          && this.canSetFullscreen
          && this.isVideoView
          && (!IS_MOBILE || this.useOnMobile),
      }, onClick: this.onClick.bind(this) }));
  }
  static get is() { return "vime-dbl-click-fullscreen"; }
  static get originalStyleUrls() { return {
    "$": ["dbl-click-fullscreen.scss"]
  }; }
  static get styleUrls() { return {
    "$": ["dbl-click-fullscreen.css"]
  }; }
  static get properties() { return {
    "useOnMobile": {
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
        "text": "By default this is disabled on mobile to not interfere with playback, set this to `true` to\nenable it."
      },
      "attribute": "use-on-mobile",
      "reflect": false,
      "defaultValue": "false"
    },
    "isFullscreenActive": {
      "type": "boolean",
      "mutable": false,
      "complexType": {
        "original": "PlayerProps['isFullscreenActive']",
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
      "attribute": "is-fullscreen-active",
      "reflect": false,
      "defaultValue": "true"
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
      "attribute": "is-video-view",
      "reflect": false,
      "defaultValue": "false"
    },
    "playbackReady": {
      "type": "boolean",
      "mutable": false,
      "complexType": {
        "original": "PlayerProps['playbackReady']",
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
      "attribute": "playback-ready",
      "reflect": false,
      "defaultValue": "false"
    }
  }; }
  static get states() { return {
    "canSetFullscreen": {}
  }; }
  static get watchers() { return [{
      "propName": "playbackReady",
      "methodName": "onPlaybackReadyChange"
    }]; }
}
