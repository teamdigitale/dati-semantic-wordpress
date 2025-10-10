'use strict';

Object.defineProperty(exports, '__esModule', { value: true });

const index = require('./index-e8963331.js');
const Provider = require('./Provider-4c382d32.js');

const playgroundCss = "vime-playground{width:100%;height:100%;display:flex;align-items:center;justify-content:center;flex-direction:column}vime-playground .container{width:100%;max-width:960px}vime-playground .buttons,vime-playground .checkboxes{margin-top:24px}vime-playground .buttons>*,vime-playground .checkboxes>*{margin-left:8px}vime-playground .buttons>*:first-child,vime-playground .checkboxes>*:first-child{margin-left:0px}vime-playground .inputs{width:100%;max-width:480px;display:flex;flex-direction:column;margin-top:12px}vime-playground .inputs>input[readonly]{background-color:#f0f0f0}vime-playground .inputs>label{margin-top:16px;margin-bottom:8px}";

const BASE_MEDIA_URL = 'https://media.vimejs.com';
const Playground = class {
  constructor(hostRef) {
    index.registerInstance(this, hostRef);
    /**
     * The current media provider.
     */
    this.provider = Provider.Provider.Audio;
    /**
     * Whether to show the custom Vime UI or not.
     */
    this.showCustomUI = false;
    /**
     * The current custom UI theme, won't work if custom UI is turned off.
     */
    this.theme = 'dark';
    /**
     *  The current poster to load.
     */
    this.poster = `${BASE_MEDIA_URL}/poster.png`;
  }
  buildProviderChildren() {
    var _a;
    const defaultSrc = {
      [Provider.Provider.Audio]: `${BASE_MEDIA_URL}/audio.mp3`,
      [Provider.Provider.Video]: `${BASE_MEDIA_URL}/720p.mp4`,
      [Provider.Provider.HLS]: `${BASE_MEDIA_URL}/hls/index.m3u8`,
    };
    const mediaType = {
      [Provider.Provider.Audio]: 'audio/mp3',
      [Provider.Provider.Video]: 'video/mp4',
      [Provider.Provider.HLS]: 'application/x-mpegURL',
    };
    return (index.h(index.Fragment, null, index.h("source", { "data-src": (_a = this.src) !== null && _a !== void 0 ? _a : (this.src = defaultSrc[this.provider]), type: mediaType[this.provider] }), (this.provider !== Provider.Provider.HLS) && (index.h(index.Fragment, null, index.h("track", { default: true, kind: "subtitles", src: `${BASE_MEDIA_URL}/subs/english.vtt`, srclang: "en", label: "English" }), index.h("track", { kind: "subtitles", src: `${BASE_MEDIA_URL}/subs/spanish.vtt`, srclang: "es", label: "Spanish" })))));
  }
  buildProvider() {
    var _a, _b, _c, _d;
    switch (this.provider) {
      case Provider.Provider.Audio:
        return (index.h("vime-audio", { crossOrigin: "" }, this.buildProviderChildren()));
      case Provider.Provider.Video:
        return (index.h("vime-video", { crossOrigin: "", poster: this.poster }, this.buildProviderChildren()));
      case Provider.Provider.HLS:
        return (index.h("vime-hls", { crossOrigin: "", poster: this.poster }, this.buildProviderChildren()));
      case Provider.Provider.Dash:
        return (index.h("vime-dash", { crossOrigin: "", poster: this.poster, src: (_a = this.src) !== null && _a !== void 0 ? _a : (this.src = `${BASE_MEDIA_URL}/mpd/manifest.mpd`) }));
      case Provider.Provider.YouTube:
        return (index.h("vime-youtube", { videoId: (_b = this.src) !== null && _b !== void 0 ? _b : (this.src = 'DyTCOwB0DVw') }));
      case Provider.Provider.Vimeo:
        return (index.h("vime-vimeo", { videoId: (_c = this.src) !== null && _c !== void 0 ? _c : (this.src = '411652396') }));
      case Provider.Provider.Dailymotion:
        return (index.h("vime-dailymotion", { videoId: (_d = this.src) !== null && _d !== void 0 ? _d : (this.src = 'k3b11PemcuTrmWvYe0q') }));
      default:
        return undefined;
    }
  }
  changeProvider(newProvider) {
    this.src = undefined;
    this.provider = newProvider;
  }
  onCustomUiChange(e) {
    this.showCustomUI = e.target.checked;
  }
  onThemeChange(e) {
    this.theme = e.target.checked ? 'light' : 'dark';
  }
  onSrcChange(e) {
    this.src = e.target.value;
  }
  render() {
    const buttons = Object.values(Provider.Provider)
      .filter((provider) => provider !== 'faketube')
      .map((provider) => (index.h("button", { id: "audio", type: "button", onClick: () => this.changeProvider(provider) }, provider)));
    return (index.h(index.Host, null, index.h("div", { class: "container" }, index.h("vime-player", { controls: !this.showCustomUI, theme: this.theme }, this.buildProvider(), this.showCustomUI && index.h("vime-default-ui", null))), index.h("div", { class: "buttons" }, buttons), index.h("div", { class: "inputs" }, index.h("label", { htmlFor: "src" }, "Src"), index.h("input", { type: "text", id: "src", value: this.src, onChange: this.onSrcChange.bind(this) }), index.h("label", { htmlFor: "poster" }, "Poster"), index.h("input", { type: "text", id: "poster", value: this.poster, readonly: true }), index.h("div", { class: "checkboxes" }, index.h("label", { htmlFor: "ui" }, "Custom UI"), index.h("input", { type: "checkbox", id: "ui", checked: this.showCustomUI, onChange: this.onCustomUiChange.bind(this) }), index.h("label", { htmlFor: "theme" }, "Light Theme"), index.h("input", { type: "checkbox", id: "theme", checked: this.theme === 'light', onChange: this.onThemeChange.bind(this) })))));
  }
};
Playground.style = playgroundCss;

exports.vime_playground = Playground;
