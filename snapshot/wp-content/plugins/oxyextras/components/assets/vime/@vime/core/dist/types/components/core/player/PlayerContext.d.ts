import { PlayerProp, PlayerProps } from './PlayerProps';
/**
 * Binds props between an instance of a given component class and it's closest ancestor player.
 *
 * @param Component A Stencil component class or instance.
 * @param props A set of props to watch and update on the given component instance.
 */
export declare const withPlayerContext: (Component: any, props: PlayerProp[]) => void;
/**
 * Finds the closest ancestor player to the given `ref` and watches the given props for changes. On
 * a prop change the given `updater` fn is called.
 *
 * @param ref A element within any player's subtree.
 * @param props A set of props to watch and call the `updater` fn with.
 * @param updater This function is called with the prop/value of any watched properties.
 */
export declare const usePlayerContext: (ref: HTMLElement, props: PlayerProp[], updater: <P extends "debug" | "attached" | "theme" | "paused" | "playing" | "duration" | "mediaTitle" | "currentSrc" | "currentPoster" | "currentTime" | "autoplay" | "ready" | "playbackReady" | "loop" | "muted" | "buffered" | "playbackRate" | "playbackRates" | "playbackQuality" | "playbackQualities" | "seeking" | "playbackStarted" | "playbackEnded" | "buffering" | "controls" | "isControlsActive" | "errors" | "textTracks" | "volume" | "isFullscreenActive" | "aspectRatio" | "viewType" | "isAudioView" | "isVideoView" | "mediaType" | "isAudio" | "isVideo" | "isMobile" | "isTouch" | "isCaptionsActive" | "isSettingsActive" | "currentProvider" | "currentCaption" | "isLive" | "isPiPActive" | "autopause" | "playsinline" | "language" | "languages" | "translations" | "i18n" | "logger">(prop: P, value: PlayerProps[P]) => void, playerRef?: HTMLVimePlayerElement | undefined) => () => void;
