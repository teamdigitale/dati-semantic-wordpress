import { Component, Element, Prop, Watch, } from '@stencil/core';
import { findShadowRoot } from '../../../utils/dom';
import { loadSprite } from '../../../utils/network';
import { isNull } from '../../../utils/unit';
export class Icons {
  constructor() {
    /**
     * The URL to an SVG sprite to load.
     */
    this.href = 'https://cdn.jsdelivr.net/npm/@vime/core@latest/icons/sprite.svg';
  }
  loadIcons() {
    return this.hasLoaded() ? undefined : loadSprite(this.href, this.findRoot());
  }
  componentWillLoad() {
    return this.loadIcons();
  }
  disconnectedCallback() {
    var _a;
    (_a = this.findExistingSprite()) === null || _a === void 0 ? void 0 : _a.remove();
  }
  findRoot() {
    var _a;
    return (_a = findShadowRoot(this.el)) !== null && _a !== void 0 ? _a : document.head;
  }
  findExistingSprite() {
    return this.findRoot().querySelector(`div[data-sprite="${this.href}"]`);
  }
  hasLoaded() {
    return !isNull(this.findExistingSprite());
  }
  static get is() { return "vime-icons"; }
  static get properties() { return {
    "href": {
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
        "text": "The URL to an SVG sprite to load."
      },
      "attribute": "href",
      "reflect": false,
      "defaultValue": "'https://cdn.jsdelivr.net/npm/@vime/core@latest/icons/sprite.svg'"
    }
  }; }
  static get elementRef() { return "el"; }
  static get watchers() { return [{
      "propName": "href",
      "methodName": "loadIcons"
    }]; }
}
