'use strict';

Object.defineProperty(exports, '__esModule', { value: true });

const index = require('./index-e8963331.js');
const dom = require('./dom-4b0c36e3.js');
const PlayerContext = require('./PlayerContext-14c5c012.js');

const menuCss = "vime-menu{display:flex;flex-direction:column;position:relative;overflow:hidden;width:100%;height:auto;pointer-events:auto;text-align:left;color:var(--vm-menu-color);background:var(--vm-menu-bg);font-size:var(--vm-menu-font-size);font-weight:var(--vm-menu-font-weight);z-index:var(--vm-menu-z-index)}vime-menu[aria-hidden=true]{display:none}vime-menu:focus{outline:0}";

const Menu = class {
  constructor(hostRef) {
    index.registerInstance(this, hostRef);
    this.vOpen = index.createEvent(this, "vOpen", 7);
    this.vClose = index.createEvent(this, "vClose", 7);
    this.vMenuItemsChange = index.createEvent(this, "vMenuItemsChange", 7);
    this.vFocusMenuItemChange = index.createEvent(this, "vFocusMenuItemChange", 7);
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
      index.writeTask(() => {
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
    this.menuItems = document.querySelectorAll(dom.buildNoAncestorSelector(`#${this.identifier}`, 'vime-menu', 'vime-menu-item', 5));
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
    if (dom.isUndefined(menuItem))
      return;
    menuItem.click();
    index.writeTask(() => {
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
    this.submenus = document.querySelectorAll(dom.buildNoAncestorSelector(`#${this.identifier}`, 'vime-menu', 'vime-menu', 4));
  }
  isValidSubmenu(submenu) {
    if (dom.isNull(submenu))
      return false;
    return !dom.isUndefined(Array.from(this.submenus).find((menu) => menu.id === submenu.id));
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
    return (index.h(index.Host, { id: this.identifier, role: "menu", tabindex: "-1", "aria-labelledby": this.controller, "aria-hidden": !this.active ? 'true' : 'false', onFocus: this.onOpen.bind(this), onClick: this.onClick.bind(this), onKeyDown: this.onKeyDown.bind(this) }, index.h("div", null, index.h("slot", null))));
  }
  get el() { return index.getElement(this); }
  static get watchers() { return {
    "menuItems": ["onMenuItemsChange"],
    "currFocusedMenuItem": ["onFocusedMenuItemChange"],
    "active": ["onActiveChange"]
  }; }
};
Menu.style = menuCss;

const menuItemCss = "vime-menu-item{display:flex;align-items:center;flex-direction:row;width:100%;cursor:pointer;color:var(--vm-menu-color);background:var(--vm-menu-bg);font-size:var(--vm-menu-font-size);font-weight:var(--vm-menu-font-weight);padding:var(--vm-menu-item-padding)}vime-menu-item:focus{outline:0}vime-menu-item.hidden{display:none}vime-menu-item.tapHighlight{background:var(--vm-menu-item-tap-highlight)}vime-menu-item.showDivider{border-bottom:0.5px solid var(--vm-menu-item-divider-color)}vime-menu-item.notTouch:hover,vime-menu-item.notTouch:focus{outline:0;color:var(--vm-menu-item-focus-color);background-color:var(--vm-menu-item-focus-bg)}vime-menu-item[aria-hidden=true]{display:none}vime-menu-item[aria-checked=true] svg{opacity:1;visibility:visible}vime-menu-item vime-icon{display:inline-block}vime-menu-item svg{fill:currentColor;pointer-events:none;width:var(--vm-menu-item-check-icon-width, 10px) !important;height:var(--vm-menu-item-check-icon-height, 10px) !important;margin-right:10px;opacity:0;visibility:hidden;transition:var(--vm-fade-transition)}vime-menu-item .hint{display:inline-block;margin-left:auto;overflow:hidden;pointer-events:none;margin-right:6px;font-size:var(--vm-menu-item-hint-font-size);opacity:var(--vm-menu-item-hint-opacity);color:var(--vm-menu-item-hint-color)}vime-menu-item .badge{display:inline-block;line-height:1;overflow:hidden;pointer-events:none;color:var(--vm-menu-item-badge-color);background:var(--vm-menu-item-badge-bg);font-size:var(--vm-menu-item-badge-font-size)}vime-menu-item .spacer{flex:1}vime-menu-item .arrow{color:var(--vm-menu-item-arrow-color);border:2px solid;padding:2px;display:inline-block;border-width:0 2px 2px 0}vime-menu-item .arrow.left{margin-right:6px;transform:rotate(135deg)}vime-menu-item .arrow.right{transform:rotate(-45deg);opacity:0.38}";

const MenuItem = class {
  constructor(hostRef) {
    index.registerInstance(this, hostRef);
    this.showTapHighlight = false;
    /**
     * Whether the item is displayed or not.
     */
    this.hidden = false;
    /**
     * The URL to an SVG element or fragment to load.
     */
    this.checkedIcon = '#vime-checkmark';
    /**
     * @internal
     */
    this.isTouch = false;
    PlayerContext.withPlayerContext(this, ['isTouch']);
  }
  onClick() {
    if (dom.isUndefined(this.menu))
      return;
    const submenu = document.querySelector(`#${this.menu}`);
    if (!dom.isNull(submenu))
      submenu.active = !this.expanded;
  }
  onTouchStart() {
    this.showTapHighlight = true;
    setTimeout(() => { this.showTapHighlight = false; }, 100);
  }
  onMouseLeave() {
    this.el.blur();
  }
  render() {
    var _a;
    const isCheckedDefined = !dom.isUndefined(this.checked);
    const isMenuDefined = !dom.isUndefined(this.menu);
    const hasExpanded = this.expanded ? 'true' : 'false';
    const isChecked = this.checked ? 'true' : 'false';
    const showCheckedIcon = isCheckedDefined && !dom.isUndefined(this.checkedIcon);
    const showLeftNavArrow = isMenuDefined && this.expanded;
    const showRightNavArrow = isMenuDefined && !this.expanded;
    const showHint = !dom.isUndefined(this.hint)
      && !isCheckedDefined
      && (!isMenuDefined || !this.expanded);
    const showBadge = !dom.isUndefined(this.badge) && (!showHint && !showRightNavArrow);
    const hasSpacer = showHint || showBadge || showRightNavArrow;
    return (index.h(index.Host, { class: {
        notTouch: !this.isTouch,
        tapHighlight: this.showTapHighlight,
        showDivider: isMenuDefined && ((_a = this.expanded) !== null && _a !== void 0 ? _a : false),
      }, id: this.identifier, role: isCheckedDefined ? 'menuitemradio' : 'menuitem', tabindex: "0", "aria-label": this.label, "aria-hidden": this.hidden ? 'true' : 'false', "aria-haspopup": isMenuDefined ? 'true' : undefined, "aria-controls": this.menu, "aria-expanded": isMenuDefined ? hasExpanded : undefined, "aria-checked": isCheckedDefined ? isChecked : undefined, onClick: this.onClick.bind(this), onTouchStart: this.onTouchStart.bind(this), onMouseLeave: this.onMouseLeave.bind(this) }, showCheckedIcon && index.h("vime-icon", { href: this.checkedIcon }), showLeftNavArrow && index.h("span", { class: "arrow left" }), this.label, hasSpacer && index.h("span", { class: "spacer" }), showHint && index.h("span", { class: "hint" }, this.hint), showBadge && index.h("span", { class: "badge" }, this.badge), showRightNavArrow && index.h("span", { class: "arrow right" })));
  }
  get el() { return index.getElement(this); }
};
MenuItem.style = menuItemCss;

exports.vime_menu = Menu;
exports.vime_menu_item = MenuItem;
