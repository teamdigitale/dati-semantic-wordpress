'use strict';

Object.defineProperty(exports, '__esModule', { value: true });

const index = require('./index-e8963331.js');
const dom = require('./dom-4b0c36e3.js');
require('./support-578168e8.js');
const network = require('./network-7d352591.js');
const LazyLoader = require('./LazyLoader-0f7f3135.js');

const embedCss = "vime-embed>iframe{position:absolute;top:0;left:0;border:0;width:100%;height:100%;user-select:none;z-index:var(--vm-media-z-index)}";

let idCount = 0;
const connected = new Set();
const Embed = class {
  constructor(hostRef) {
    index.registerInstance(this, hostRef);
    this.vEmbedSrcChange = index.createEvent(this, "vEmbedSrcChange", 3);
    this.vEmbedMessage = index.createEvent(this, "vEmbedMessage", 3);
    this.vEmbedLoaded = index.createEvent(this, "vEmbedLoaded", 3);
    this.srcWithParams = '';
    this.hasEnteredViewport = false;
    /**
     * A URL that will load the external player and media (Eg: https://www.youtube.com/embed/DyTCOwB0DVw).
     */
    this.embedSrc = '';
    /**
     * The title of the current media so it can be set on the inner `iframe` for screen readers.
     */
    this.mediaTitle = '';
    /**
     * The parameters to pass to the embedded player which are appended to the `embedSrc` prop. These
     * can be passed in as a query string or object.
     */
    this.params = '';
    /**
     * A collection of URLs to that the browser should immediately start establishing a connection
     * with.
     */
    this.preconnections = [];
  }
  srcChange() {
    this.srcWithParams = network.appendParamsToURL(this.embedSrc, this.params);
  }
  srcWithParamsChange() {
    if (!this.hasEnteredViewport && !connected.has(this.embedSrc)) {
      if (network.preconnect(this.srcWithParams))
        connected.add(this.embedSrc);
    }
    this.vEmbedSrcChange.emit(this.srcWithParams);
  }
  preconnectionsChange() {
    if (this.hasEnteredViewport) {
      return;
    }
    this.preconnections
      .filter((connection) => !connected.has(connection))
      .forEach((connection) => {
      if (network.preconnect(connection))
        connected.add(connection);
    });
  }
  connectedCallback() {
    this.lazyLoader = new LazyLoader.LazyLoader(this.el, ['data-src'], (el) => {
      const src = el.getAttribute('data-src');
      el.removeAttribute('src');
      if (!dom.isNull(src))
        el.setAttribute('src', src);
    });
    this.srcChange();
    this.genIframeId();
  }
  disconnectedCallback() {
    this.lazyLoader.destroy();
  }
  onWindowMessage(e) {
    var _a, _b, _c;
    const originMatches = (e.source === ((_a = this.iframe) === null || _a === void 0 ? void 0 : _a.contentWindow))
      && (!dom.isString(this.origin) || this.origin === e.origin);
    if (!originMatches)
      return;
    const message = (_c = (_b = this.decoder) === null || _b === void 0 ? void 0 : _b.call(this, e.data)) !== null && _c !== void 0 ? _c : e.data;
    if (message)
      this.vEmbedMessage.emit(message);
  }
  /**
   * Posts a message to the embedded media player.
   */
  async postMessage(message, target) {
    var _a, _b;
    (_b = (_a = this.iframe) === null || _a === void 0 ? void 0 : _a.contentWindow) === null || _b === void 0 ? void 0 : _b.postMessage(JSON.stringify(message), (target !== null && target !== void 0 ? target : '*'));
  }
  onLoad() {
    this.vEmbedLoaded.emit();
  }
  genIframeId() {
    idCount += 1;
    this.id = `vime-iframe-${idCount}`;
  }
  render() {
    return (index.h("iframe", { id: this.id, class: "lazy", title: this.mediaTitle, "data-src": this.srcWithParams,
      // @ts-ignore
      allowfullscreen: "1", allow: "autoplay; encrypted-media; picture-in-picture", onLoad: this.onLoad.bind(this), ref: (el) => { this.iframe = el; } }));
  }
  get el() { return index.getElement(this); }
  static get watchers() { return {
    "embedSrc": ["srcChange"],
    "params": ["srcChange"],
    "srcWithParams": ["srcWithParamsChange"],
    "preconnections": ["preconnectionsChange"]
  }; }
};
Embed.style = embedCss;

exports.vime_embed = Embed;
