/* eslint-disable no-param-reassign */
import { h, Host, Component, Prop, Listen, Event, Element, State, Watch, Method, writeTask, } from '@stencil/core';
import { buildNoAncestorSelector } from '../../../../utils/dom';
import { isUndefined, isNull } from '../../../../utils/unit';
/**
 * @slot - Used to pass in the body of the menu which usually contains menu items, radio groups
 * and/or submenus.
 */
export class Menu {
  constructor() {
    this.shouldFocusOnOpen = false;
    this.currFocusedMenuItem = 0;
    /**
     * Whether the menu is open/visible.
     */
    this.active = false;
  }
  onMenuItemsChange() {
    this.vMenuItemsChange.emit(this.menuItems);
  }
  async onFocusedMenuItemChange() {
    const menuItem = await this.getFocusedMenuItem();
    this.vFocusMenuItemChange.emit(menuItem);
  }
  onActiveChange() {
    if (this.active) {
      this.findMenuItems();
      this.findSubmenus();
    }
    this.active ? this.vOpen.emit() : this.vClose.emit();
  }
  connectedCallback() {
    this.findMenuItems();
  }
  componentDidRender() {
    if (this.active && this.shouldFocusOnOpen) {
      writeTask(() => {
        this.el.focus();
        this.shouldFocusOnOpen = false;
      });
    }
  }
  /**
   * Returns the controller responsible for opening/closing this menu.
   */
  async getController() {
    return document.querySelector(`#${this.controller}`);
  }
  /**
   * Returns the currently focused menu item.
   */
  async getFocusedMenuItem() {
    return this.menuItems[this.currFocusedMenuItem];
  }
  /**
   * This should be called directly before opening the menu to set the keyboard focus on it. This
   * is a one-time operation and needs to be called everytime prior to opening the menu.
   */
  async focusOnOpen() {
    this.shouldFocusOnOpen = true;
  }
  findMenuItems() {
    this.menuItems = document.querySelectorAll(buildNoAncestorSelector(`#${this.identifier}`, 'vime-menu', 'vime-menu-item', 5));
  }
  async focusController() {
    const controller = await this.getController();
    controller === null || controller === void 0 ? void 0 : controller.focus();
  }
  focusMenuItem(index) {
    var _a;
    let boundIndex = (index >= 0) ? index : (this.menuItems.length - 1);
    if (boundIndex >= this.menuItems.length)
      boundIndex = 0;
    this.currFocusedMenuItem = boundIndex;
    (_a = this.menuItems[boundIndex]) === null || _a === void 0 ? void 0 : _a.focus();
  }
  openSubmenu() {
    const menuItem = this.menuItems[this.currFocusedMenuItem];
    if (isUndefined(menuItem))
      return;
    menuItem.click();
    writeTask(() => {
      const submenu = document.querySelector(`#${menuItem.menu}`);
      submenu === null || submenu === void 0 ? void 0 : submenu.focus();
    });
  }
  onOpen(event) {
    event.stopPropagation();
    this.findMenuItems();
    this.currFocusedMenuItem = 0;
    // Prevents forwarding click event that opened the menu to menu item.
    setTimeout(() => { var _a; (_a = this.menuItems[this.currFocusedMenuItem]) === null || _a === void 0 ? void 0 : _a.focus(); }, 10);
    this.active = true;
  }
  onClose() {
    this.currFocusedMenuItem = -1;
    this.active = false;
    this.focusController();
  }
  onClick(event) {
    event.stopPropagation();
  }
  onKeyDown(event) {
    if (!this.active || this.menuItems.length === 0)
      return;
    event.preventDefault();
    event.stopPropagation();
    switch (event.key) {
      case 'Escape':
        this.onClose();
        break;
      case 'ArrowDown':
      case 'Tab':
        this.focusMenuItem(this.currFocusedMenuItem + 1);
        break;
      case 'ArrowUp':
        this.focusMenuItem(this.currFocusedMenuItem - 1);
        break;
      case 'ArrowLeft':
        this.onClose();
        break;
      case 'ArrowRight':
      case 'Enter':
      case ' ':
        this.openSubmenu();
        break;
      case 'Home':
      case 'PageUp':
        this.focusMenuItem(0);
        break;
      case 'End':
      case 'PageDown':
        this.focusMenuItem(this.menuItems.length - 1);
        break;
    }
  }
  findSubmenus() {
    this.submenus = document.querySelectorAll(buildNoAncestorSelector(`#${this.identifier}`, 'vime-menu', 'vime-menu', 4));
  }
  isValidSubmenu(submenu) {
    if (isNull(submenu))
      return false;
    return !isUndefined(Array.from(this.submenus).find((menu) => menu.id === submenu.id));
  }
  toggleSubmenu(submenu, isActive) {
    if (!this.isValidSubmenu(submenu))
      return;
    Array.from(this.menuItems)
      .filter((menuItem) => menuItem.identifier !== submenu.controller)
      .forEach((menuItem) => { menuItem.hidden = isActive; });
    submenu.active = isActive;
  }
  onSubmenuOpen(event) {
    const submenu = event.target;
    this.toggleSubmenu(submenu, true);
  }
  onSubmenuClose(event) {
    const submenu = event.target;
    this.toggleSubmenu(submenu, false);
  }
  onWindowClick() {
    if (this.active)
      this.active = false;
  }
  onWindowKeyDown(event) {
    if (this.active && (event.key === 'Escape'))
      this.onClose();
  }
  render() {
    return (h(Host, { id: this.identifier, role: "menu", tabindex: "-1", "aria-labelledby": this.controller, "aria-hidden": !this.active ? 'true' : 'false', onFocus: this.onOpen.bind(this), onClick: this.onClick.bind(this), onKeyDown: this.onKeyDown.bind(this) },
      h("div", null,
        h("slot", null))));
  }
  static get is() { return "vime-menu"; }
  static get originalStyleUrls() { return {
    "$": ["menu.scss"]
  }; }
  static get styleUrls() { return {
    "$": ["menu.css"]
  }; }
  static get properties() { return {
    "active": {
      "type": "boolean",
      "mutable": true,
      "complexType": {
        "original": "boolean",
        "resolved": "boolean",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [],
        "text": "Whether the menu is open/visible."
      },
      "attribute": "active",
      "reflect": true,
      "defaultValue": "false"
    },
    "identifier": {
      "type": "string",
      "mutable": false,
      "complexType": {
        "original": "string",
        "resolved": "string",
        "references": {}
      },
      "required": true,
      "optional": false,
      "docs": {
        "tags": [],
        "text": "The `id` attribute of the menu."
      },
      "attribute": "identifier",
      "reflect": false
    },
    "controller": {
      "type": "string",
      "mutable": false,
      "complexType": {
        "original": "string",
        "resolved": "string",
        "references": {}
      },
      "required": true,
      "optional": false,
      "docs": {
        "tags": [],
        "text": "The `id` attribute value of the control responsible for opening/closing this menu."
      },
      "attribute": "controller",
      "reflect": false
    }
  }; }
  static get states() { return {
    "menuItems": {},
    "currFocusedMenuItem": {}
  }; }
  static get events() { return [{
      "method": "vOpen",
      "name": "vOpen",
      "bubbles": true,
      "cancelable": true,
      "composed": true,
      "docs": {
        "tags": [],
        "text": "Emitted when the menu is open/active."
      },
      "complexType": {
        "original": "void",
        "resolved": "void",
        "references": {}
      }
    }, {
      "method": "vClose",
      "name": "vClose",
      "bubbles": true,
      "cancelable": true,
      "composed": true,
      "docs": {
        "tags": [],
        "text": "Emitted when the menu has closed/is not active."
      },
      "complexType": {
        "original": "void",
        "resolved": "void",
        "references": {}
      }
    }, {
      "method": "vMenuItemsChange",
      "name": "vMenuItemsChange",
      "bubbles": true,
      "cancelable": true,
      "composed": true,
      "docs": {
        "tags": [],
        "text": "Emitted when the menu items present changes."
      },
      "complexType": {
        "original": "NodeListOf<HTMLVimeMenuItemElement> | undefined",
        "resolved": "NodeListOf<HTMLVimeMenuItemElement> | undefined",
        "references": {
          "NodeListOf": {
            "location": "global"
          },
          "HTMLVimeMenuItemElement": {
            "location": "global"
          }
        }
      }
    }, {
      "method": "vFocusMenuItemChange",
      "name": "vFocusMenuItemChange",
      "bubbles": true,
      "cancelable": true,
      "composed": true,
      "docs": {
        "tags": [],
        "text": "Emitted when the currently focused menu item changes."
      },
      "complexType": {
        "original": "HTMLVimeMenuItemElement | undefined",
        "resolved": "HTMLVimeMenuItemElement | undefined",
        "references": {
          "HTMLVimeMenuItemElement": {
            "location": "global"
          }
        }
      }
    }]; }
  static get methods() { return {
    "getController": {
      "complexType": {
        "signature": "() => Promise<HTMLElement>",
        "parameters": [],
        "references": {
          "Promise": {
            "location": "global"
          },
          "HTMLElement": {
            "location": "global"
          }
        },
        "return": "Promise<HTMLElement>"
      },
      "docs": {
        "text": "Returns the controller responsible for opening/closing this menu.",
        "tags": []
      }
    },
    "getFocusedMenuItem": {
      "complexType": {
        "signature": "() => Promise<HTMLVimeMenuItemElement>",
        "parameters": [],
        "references": {
          "Promise": {
            "location": "global"
          },
          "HTMLVimeMenuItemElement": {
            "location": "global"
          }
        },
        "return": "Promise<HTMLVimeMenuItemElement>"
      },
      "docs": {
        "text": "Returns the currently focused menu item.",
        "tags": []
      }
    },
    "focusOnOpen": {
      "complexType": {
        "signature": "() => Promise<void>",
        "parameters": [],
        "references": {
          "Promise": {
            "location": "global"
          }
        },
        "return": "Promise<void>"
      },
      "docs": {
        "text": "This should be called directly before opening the menu to set the keyboard focus on it. This\nis a one-time operation and needs to be called everytime prior to opening the menu.",
        "tags": []
      }
    }
  }; }
  static get elementRef() { return "el"; }
  static get watchers() { return [{
      "propName": "menuItems",
      "methodName": "onMenuItemsChange"
    }, {
      "propName": "currFocusedMenuItem",
      "methodName": "onFocusedMenuItemChange"
    }, {
      "propName": "active",
      "methodName": "onActiveChange"
    }]; }
  static get listeners() { return [{
      "name": "vOpen",
      "method": "onSubmenuOpen",
      "target": undefined,
      "capture": false,
      "passive": false
    }, {
      "name": "vClose",
      "method": "onSubmenuClose",
      "target": undefined,
      "capture": false,
      "passive": false
    }, {
      "name": "click",
      "method": "onWindowClick",
      "target": "window",
      "capture": false,
      "passive": false
    }, {
      "name": "keydown",
      "method": "onWindowKeyDown",
      "target": "window",
      "capture": false,
      "passive": false
    }]; }
}
