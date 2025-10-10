import { h, Component, Prop, State, Watch, Host, Event, Element, } from '@stencil/core';
import { withPlayerContext } from '../../core/player/PlayerContext';
import { isNull, isUndefined } from '../../../utils/unit';
import { LazyLoader } from '../../core/player/LazyLoader';
export class Poster {
  constructor() {
    this.isHidden = true;
    this.isActive = false;
    this.hasLoaded = false;
    /**
     * How the poster image should be resized to fit the container (sets the `object-fit` property).
     */
    this.fit = 'cover';
    /**
     * @internal
     */
    this.isVideoView = false;
    /**
     * @internal
     */
    this.playbackStarted = false;
    /**
     * @internal
     */
    this.currentTime = 0;
    withPlayerContext(this, [
      'mediaTitle',
      'currentPoster',
      'playbackStarted',
      'currentTime',
      'isVideoView',
    ]);
  }
  onCurrentPosterChange() {
    this.hasLoaded = false;
  }
  connectedCallback() {
    this.lazyLoader = new LazyLoader(this.el, ['data-src'], (el) => {
      const src = el.getAttribute('data-src');
      el.removeAttribute('src');
      if (!isNull(src))
        el.setAttribute('src', src);
    });
    this.onEnabledChange();
    this.onActiveChange();
  }
  disconnectedCallback() {
    this.lazyLoader.destroy();
  }
  onVisibilityChange() {
    (!this.isHidden && this.isActive) ? this.vWillShow.emit() : this.vWillHide.emit();
  }
  onEnabledChange() {
    this.isHidden = !this.isVideoView || isUndefined(this.currentPoster);
    this.onVisibilityChange();
  }
  onActiveChange() {
    this.isActive = !this.playbackStarted || this.currentTime <= 0.1;
    this.onVisibilityChange();
  }
  onPosterLoad() {
    this.vLoaded.emit();
    this.hasLoaded = true;
  }
  render() {
    return (h(Host, { class: {
        hidden: this.isHidden,
        active: this.isActive && this.hasLoaded,
      } },
      h("img", { class: "lazy", "data-src": this.currentPoster, alt: !isUndefined(this.mediaTitle) ? `${this.mediaTitle} Poster` : 'Media Poster', style: { objectFit: this.fit }, onLoad: this.onPosterLoad.bind(this) })));
  }
  static get is() { return "vime-poster"; }
  static get originalStyleUrls() { return {
    "$": ["poster.scss"]
  }; }
  static get styleUrls() { return {
    "$": ["poster.css"]
  }; }
  static get properties() { return {
    "fit": {
      "type": "string",
      "mutable": false,
      "complexType": {
        "original": "'fill' | 'contain' | 'cover' | 'scale-down' | 'none'",
        "resolved": "\"contain\" | \"cover\" | \"fill\" | \"none\" | \"scale-down\" | undefined",
        "references": {}
      },
      "required": false,
      "optional": true,
      "docs": {
        "tags": [],
        "text": "How the poster image should be resized to fit the container (sets the `object-fit` property)."
      },
      "attribute": "fit",
      "reflect": false,
      "defaultValue": "'cover'"
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
    "currentPoster": {
      "type": "string",
      "mutable": false,
      "complexType": {
        "original": "PlayerProps['currentPoster']",
        "resolved": "string | undefined",
        "references": {
          "PlayerProps": {
            "location": "import",
            "path": "../../core/player/PlayerProps"
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
      "attribute": "current-poster",
      "reflect": false
    },
    "mediaTitle": {
      "type": "string",
      "mutable": false,
      "complexType": {
        "original": "PlayerProps['mediaTitle']",
        "resolved": "string | undefined",
        "references": {
          "PlayerProps": {
            "location": "import",
            "path": "../../core/player/PlayerProps"
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
      "attribute": "media-title",
      "reflect": false
    },
    "playbackStarted": {
      "type": "boolean",
      "mutable": false,
      "complexType": {
        "original": "PlayerProps['playbackStarted']",
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
      "attribute": "playback-started",
      "reflect": false,
      "defaultValue": "false"
    },
    "currentTime": {
      "type": "number",
      "mutable": false,
      "complexType": {
        "original": "PlayerProps['currentTime']",
        "resolved": "number",
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
      "attribute": "current-time",
      "reflect": false,
      "defaultValue": "0"
    }
  }; }
  static get states() { return {
    "isHidden": {},
    "isActive": {},
    "hasLoaded": {}
  }; }
  static get events() { return [{
      "method": "vLoaded",
      "name": "vLoaded",
      "bubbles": false,
      "cancelable": true,
      "composed": true,
      "docs": {
        "tags": [],
        "text": "Emitted when the poster has loaded."
      },
      "complexType": {
        "original": "void",
        "resolved": "void",
        "references": {}
      }
    }, {
      "method": "vWillShow",
      "name": "vWillShow",
      "bubbles": false,
      "cancelable": true,
      "composed": true,
      "docs": {
        "tags": [],
        "text": "Emitted when the poster will be shown."
      },
      "complexType": {
        "original": "void",
        "resolved": "void",
        "references": {}
      }
    }, {
      "method": "vWillHide",
      "name": "vWillHide",
      "bubbles": false,
      "cancelable": true,
      "composed": true,
      "docs": {
        "tags": [],
        "text": "Emitted when the poster will be hidden."
      },
      "complexType": {
        "original": "void",
        "resolved": "void",
        "references": {}
      }
    }]; }
  static get elementRef() { return "el"; }
  static get watchers() { return [{
      "propName": "currentPoster",
      "methodName": "onCurrentPosterChange"
    }, {
      "propName": "isVideoView",
      "methodName": "onEnabledChange"
    }, {
      "propName": "currentPoster",
      "methodName": "onEnabledChange"
    }, {
      "propName": "currentTime",
      "methodName": "onActiveChange"
    }, {
      "propName": "playbackStarted",
      "methodName": "onActiveChange"
    }]; }
}
