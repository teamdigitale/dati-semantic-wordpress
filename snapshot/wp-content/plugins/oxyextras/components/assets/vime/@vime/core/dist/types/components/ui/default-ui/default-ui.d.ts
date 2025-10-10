/**
 * @slot - Used to extend the default user interface with custom UI components.
 */
export declare class DefaultUI {
  /**
   * Whether the default icons should not be loaded.
   */
  noIcons: boolean;
  /**
   * Whether clicking the player should not toggle playback.
   */
  noClickToPlay: boolean;
  /**
   * Whether double clicking the player should not toggle fullscreen mode.
   */
  noDblClickFullscreen: boolean;
  /**
   * Whether the custom captions UI should not be loaded.
   */
  noCaptions: boolean;
  /**
   * Whether the custom poster UI should not be loaded.
   */
  noPoster: boolean;
  /**
   * Whether the custom spinner UI should not be loaded.
   */
  noSpinner: boolean;
  /**
   * Whether the custom default controls should not be loaded.
   */
  noControls: boolean;
  /**
   * Whether the custom default settings menu should not be loaded.
   */
  noSettings: boolean;
  /**
   * Whether the skeleton loading animation should be shown while the player is loading.
   */
  noSkeleton: boolean;
  render(): any;
}
