import { SettingsController } from './SettingsController';
import { PlayerProps } from '../../../core/player/PlayerProps';
/**
 * @slot - Used to pass in the body of the settings menu, which usually contains submenus.
 */
export declare class Settings {
  private id;
  private menu;
  private disposal;
  private controller?;
  private dispatch;
  el: HTMLVimeSettingsElement;
  controllerId?: string;
  /**
   * The height of any lower control bar in pixels so that the settings can re-position itself
   * accordingly.
   */
  controlsHeight: number;
  /**
   * Pins the settings to the defined position inside the video player. This has no effect when
   * the view is of type `audio` (always `bottomRight`) and on mobile devices (always bottom sheet).
   */
  pin: 'topLeft' | 'topRight' | 'bottomLeft' | 'bottomRight';
  /**
   * Whether the settings menu is opened/closed.
   */
  active: boolean;
  onActiveChange(): void;
  /**
   * @internal
   */
  isMobile: PlayerProps['isMobile'];
  /**
   * @internal
   */
  isAudioView: PlayerProps['isAudioView'];
  constructor();
  connectedCallback(): void;
  disconnectedCallback(): void;
  /**
   * Sets the controller responsible for opening/closing this settings.
   */
  setController(id: string, controller: SettingsController): Promise<void>;
  private getPosition;
  private onClose;
  render(): any;
}
