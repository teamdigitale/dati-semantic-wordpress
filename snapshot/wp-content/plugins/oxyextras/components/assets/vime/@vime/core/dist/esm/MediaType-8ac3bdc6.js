var ViewType;
(function (ViewType) {
  ViewType["Audio"] = "audio";
  ViewType["Video"] = "video";
})(ViewType || (ViewType = {}));

var MediaType;
(function (MediaType) {
  MediaType["Audio"] = "audio";
  MediaType["Video"] = "video";
})(MediaType || (MediaType = {}));

export { MediaType as M, ViewType as V };
