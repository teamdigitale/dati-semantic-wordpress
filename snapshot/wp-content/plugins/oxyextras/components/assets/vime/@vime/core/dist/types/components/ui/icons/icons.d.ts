export declare class Icons {
  el: HTMLVimeIconsElement;
  /**
   * The URL to an SVG sprite to load.
   */
  href: string;
  loadIcons(): void;
  componentWillLoad(): void;
  disconnectedCallback(): void;
  private findRoot;
  private findExistingSprite;
  private hasLoaded;
}
