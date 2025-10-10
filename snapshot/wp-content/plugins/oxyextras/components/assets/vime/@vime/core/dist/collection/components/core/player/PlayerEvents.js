// Events that toggle state and the prop is named `is{PropName}...`.
const isToggleStateEvent = new Set([
  'isFullscreenActive',
  'isControlsActive',
  'isPiPActive',
  'isLive',
  'isTouch',
  'isAudio',
  'isVideo',
  'isAudioView',
  'isVideoView',
]);
// Events that are emitted without the 'Change' postfix.
const hasShortenedEventName = new Set([
  'ready',
  'playbackStarted',
  'playbackEnded',
  'playbackReady',
]);
export const getEventName = (prop) => {
  // Example: isFullscreenActive -> vFullscreenChange
  if (isToggleStateEvent.has(prop)) {
    return `v${prop.replace('is', '').replace('Active', '')}Change`;
  }
  // Example: playbackStarted -> vPlaybackStarted
  if (hasShortenedEventName.has(prop)) {
    return `v${prop.charAt(0).toUpperCase()}${prop.slice(1)}`;
  }
  // Example: currentTime -> vCurrentTimeChange
  return `v${prop.charAt(0).toUpperCase()}${prop.slice(1)}Change`;
};
