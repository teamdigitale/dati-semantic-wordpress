import { Provider } from '../../providers/Provider';
export declare class Playground {
  /**
   * The current media provider.
   */
  provider: Provider;
  /**
   * The current `src` to load into the provider.
   */
  src?: string;
  /**
   * Whether to show the custom Vime UI or not.
   */
  showCustomUI: boolean;
  /**
   * The current custom UI theme, won't work if custom UI is turned off.
   */
  theme: 'light' | 'dark';
  /**
   *  The current poster to load.
   */
  poster: string;
  private buildProviderChildren;
  private buildProvider;
  private changeProvider;
  private onCustomUiChange;
  private onThemeChange;
  private onSrcChange;
  render(): any;
}
