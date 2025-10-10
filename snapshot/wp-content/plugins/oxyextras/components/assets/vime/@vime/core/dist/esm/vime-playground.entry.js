import { r as registerInstance, h, F as Fragment, H as Host } from './index-e4fee97f.js';
import { P as Provider } from './Provider-99c71269.js';

const playgroundCss = "vime-playground{width:100%;height:100%;display:flex;align-items:center;justify-content:center;flex-direction:column}vime-playground .container{width:100%;max-width:960px}vime-playground .buttons,vime-playground .checkboxes{margin-top:24px}vime-playground .buttons>*,vime-playground .checkboxes>*{margin-left:8px}vime-playground .buttons>*:first-child,vime-playground .checkboxes>*:first-child{margin-left:0px}vime-playground .inputs{width:100%;max-width:480px;display:flex;flex-direction:column;margin-top:12px}vime-playground .inputs>input[readonly]{background-color:#f0f0f0}vime-playground .inputs>label{margin-top:16px;margin-bottom:8px}";

const BASE_MEDIA_URL = 'https://media.vimejs.com';
const Playground = class {
  constructor(hostRef) {
    registerInstance(this, hostRef);
    /**
     * The current media provider.
     */
    this.provider = Provider.Audio;
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
      [Provider.Audio]: `${BASE_MEDIA_URL}/audio.mp3`,
      [Provider.Video]: `${BASE_MEDIA_URL}/720p.mp4`,
      [Provider.HLS]: `${BASE_MEDIA_URL}/hls/index.m3u8`,
    };
    const mediaType = {
      [Provider.Audio]: 'audio/mp3',
      [Provider.Video]: 'video/mp4',
      [Provider.HLS]: 'application/x-mpegURL',
    };
    return (h(Fragment, null, h("source", { "data-src": (_a = this.src) !== null && _a !== void 0 ? _a : (this.src = defaultSrc[this.provider]), type: mediaType[this.provider] }), (this.provider !== Provider.HLS) && (h(Fragment, null, h("track", { default: true, kind: "subtitles", src: `${BASE_MEDIA_URL}/subs/english.vtt`, srclang: "en", label: "English" }), h("track", { kind: "subtitles", src: `${BASE_MEDIA_URL}/subs/spanish.vtt`, srclang: "es", label: "Spanish" })))));
  }
  buildProvider() {
    var _a, _b, _c, _d;
    switch (this.provider) {
      case Provider.Audio:
        return (h("vime-audio", { crossOrigin: "" }, this.buildProviderChildren()));
      case Provider.Video:
        return (h("vime-video", { crossOrigin: "", poster: this.poster }, this.buildProviderChildren()));
      case Provider.HLS:
        return (h("vime-hls", { crossOrigin: "", poster: this.poster }, this.buildProviderChildren()));
      case Provider.Dash:
        return (h("vime-dash", { crossOrigin: "", poster: this.poster, src: (_a = this.src) !== null && _a !== void 0 ? _a : (this.src = `${BASE_MEDIA_URL}/mpd/manifest.mpd`) }));
      case Provider.YouTube:
        return (h("vime-youtube", { videoId: (_b = this.src) !== null && _b !== void 0 ? _b : (this.src = 'DyTCOwB0DVw') }));
      case Provider.Vimeo:
        return (h("vime-vimeo", { videoId: (_c = this.src) !== null && _c !== void 0 ? _c : (this.src = '411652396') }));
      case Provider.Dailymotion:
        return (h("vime-dailymotion", { videoId: (_d = this.src) !== null && _d !== void 0 ? _d : (this.src = 'k3b11PemcuTrmWvYe0q') }));
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
    const buttons = Object.values(Provider)
      .filter((provider) => provider !== 'faketube')
      .map((provider) => (h("button", { id: "audio", type: "button", onClick: () => this.changeProvider(provider) }, provider)));
    return (h(Host, null, h("div", { class: "container" }, h("vime-player", { controls: !this.showCustomUI, theme: this.theme }, this.buildProvider(), this.showCustomUI && h("vime-default-ui", null))), h("div", { class: "buttons" }, buttons), h("div", { class: "inputs" }, h("label", { htmlFor: "src" }, "Src"), h("input", { type: "text", id: "src", value: this.src, onChange: this.onSrcChange.bind(this) }), h("label", { htmlFor: "poster" }, "Poster"), h("input", { type: "text", id: "poster", value: this.poster, readonly: true }), h("div", { class: "checkboxes" }, h("label", { htmlFor: "ui" }, "Custom UI"), h("input", { type: "checkbox", id: "ui", checked: this.showCustomUI, onChange: this.onCustomUiChange.bind(this) }), h("label", { htmlFor: "theme" }, "Light Theme"), h("input", { type: "checkbox", id: "theme", checked: this.theme === 'light', onChange: this.onThemeChange.bind(this) })))));
  }
};
Playground.style = playgroundCss;

export { Playground as vime_playground };
