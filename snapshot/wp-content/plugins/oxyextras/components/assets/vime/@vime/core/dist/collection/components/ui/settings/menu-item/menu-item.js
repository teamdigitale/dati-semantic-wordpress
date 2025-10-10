/* eslint-disable jsx-a11y/no-noninteractive-tabindex */
/* eslint-disable jsx-a11y/role-supports-aria-props */
import { h, Host, Component, Prop, State, Element, } from '@stencil/core';
import { isUndefined, isNull } from '../../../../utils/unit';
import { withPlayerContext } from '../../../core/player/PlayerContext';
export class MenuItem {
  constructor() {
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
    withPlayerContext(this, ['isTouch']);
  }
  onClick() {
    if (isUndefined(this.menu))
      return;
    const submenu = document.querySelector(`#${this.menu}`);
    if (!isNull(submenu))
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
    const isCheckedDefined = !isUndefined(this.checked);
    const isMenuDefined = !isUndefined(this.menu);
    const hasExpanded = this.expanded ? 'true' : 'false';
    const isChecked = this.checked ? 'true' : 'false';
    const showCheckedIcon = isCheckedDefined && !isUndefined(this.checkedIcon);
    const showLeftNavArrow = isMenuDefined && this.expanded;
    const showRightNavArrow = isMenuDefined && !this.expanded;
    const showHint = !isUndefined(this.hint)
      && !isCheckedDefined
      && (!isMenuDefined || !this.expanded);
    const showBadge = !isUndefined(this.badge) && (!showHint && !showRightNavArrow);
    const hasSpacer = showHint || showBadge || showRightNavArrow;
    return (h(Host, { class: {
        notTouch: !this.isTouch,
        tapHighlight: this.showTapHighlight,
        showDivider: isMenuDefined && ((_a = this.expanded) !== null && _a !== void 0 ? _a : false),
      }, id: this.identifier, role: isCheckedDefined ? 'menuitemradio' : 'menuitem', tabindex: "0", "aria-label": this.label, "aria-hidden": this.hidden ? 'true' : 'false', "aria-haspopup": isMenuDefined ? 'true' : undefined, "aria-controls": this.menu, "aria-expanded": isMenuDefined ? hasExpanded : undefined, "aria-checked": isCheckedDefined ? isChecked : undefined, onClick: this.onClick.bind(this), onTouchStart: this.onTouchStart.bind(this), onMouseLeave: this.onMouseLeave.bind(this) },
      showCheckedIcon && h("vime-icon", { href: this.checkedIcon }),
      showLeftNavArrow && h("span", { class: "arrow left" }),
      this.label,
      hasSpacer && h("span", { class: "spacer" }),
      showHint && h("span", { class: "hint" }, this.hint),
      showBadge && h("span", { class: "badge" }, this.badge),
      showRightNavArrow && h("span", { class: "arrow right" })));
  }
  static get is() { return "vime-menu-item"; }
  static get originalStyleUrls() { return {
    "$": ["menu-item.scss"]
  }; }
  static get styleUrls() { return {
    "$": ["menu-item.css"]
  }; }
  static get properties() { return {
    "identifier": {
      "type": "string",
      "mutable": false,
      "complexType": {
        "original": "string",
        "resolved": "string | undefined",
        "references": {}
      },
      "required": false,
      "optional": true,
      "docs": {
        "tags": [],
        "text": "The `id` attribute of the item."
      },
      "attribute": "identifier",
      "reflect": false
    },
    "hidden": {
      "type": "boolean",
      "mutable": false,
      "complexType": {
        "original": "boolean",
        "resolved": "boolean",
        "references": {}
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [],
        "text": "Whether the item is displayed or not."
      },
      "attribute": "hidden",
      "reflect": false,
      "defaultValue": "false"
    },
    "label": {
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
        "text": "The label/title of the item."
      },
      "attribute": "label",
      "reflect": false
    },
    "menu": {
      "type": "string",
      "mutable": false,
      "complexType": {
        "original": "string",
        "resolved": "string | undefined",
        "references": {}
      },
      "required": false,
      "optional": true,
      "docs": {
        "tags": [],
        "text": "If the item has a popup menu, then this should be the `id` of said menu. Sets the\n`aria-controls` property."
      },
      "attribute": "menu",
      "reflect": false
    },
    "expanded": {
      "type": "boolean",
      "mutable": false,
      "complexType": {
        "original": "boolean",
        "resolved": "boolean | undefined",
        "references": {}
      },
      "required": false,
      "optional": true,
      "docs": {
        "tags": [],
        "text": "If the item has a popup menu, this indicates whether the menu is open or not. Sets the\n`aria-expanded` property."
      },
      "attribute": "expanded",
      "reflect": false
    },
    "checked": {
      "type": "boolean",
      "mutable": false,
      "complexType": {
        "original": "boolean",
        "resolved": "boolean | undefined",
        "references": {}
      },
      "required": false,
      "optional": true,
      "docs": {
        "tags": [],
        "text": "If this item is to behave as a radio button, then this property determines whether the\nradio is selected or not. Sets the `aria-checked` property."
      },
      "attribute": "checked",
      "reflect": false
    },
    "hint": {
      "type": "string",
      "mutable": false,
      "complexType": {
        "original": "string",
        "resolved": "string | undefined",
        "references": {}
      },
      "required": false,
      "optional": true,
      "docs": {
        "tags": [],
        "text": "This can provide additional context about some underlying state of the item. For example, if\nthe menu item opens/closes a submenu with options, the hint could be the currently selected\noption."
      },
      "attribute": "hint",
      "reflect": false
    },
    "badge": {
      "type": "string",
      "mutable": false,
      "complexType": {
        "original": "string",
        "resolved": "string | undefined",
        "references": {}
      },
      "required": false,
      "optional": true,
      "docs": {
        "tags": [],
        "text": "This can provide additional context about the value of a menu item. For example, if the item\nis a radio button for a set of video qualities, the badge could describe whether the quality\nis UHD, HD etc."
      },
      "attribute": "badge",
      "reflect": false
    },
    "checkedIcon": {
      "type": "string",
      "mutable": false,
      "complexType": {
        "original": "string",
        "resolved": "string | undefined",
        "references": {}
      },
      "required": false,
      "optional": true,
      "docs": {
        "tags": [],
        "text": "The URL to an SVG element or fragment to load."
      },
      "attribute": "checked-icon",
      "reflect": false,
      "defaultValue": "'#vime-checkmark'"
    },
    "isTouch": {
      "type": "boolean",
      "mutable": false,
      "complexType": {
        "original": "PlayerProps['isTouch']",
        "resolved": "boolean",
        "references": {
          "PlayerProps": {
            "location": "import",
            "path": "../../../core/player/PlayerProps"
          }
        }
      },
      "required": false,
      "optional": false,
      "docs": {
        "tags": [{
            "text": undefined,
            "name": "internal"
          }],
        "text": ""
      },
      "attribute": "is-touch",
      "reflect": false,
      "defaultValue": "false"
    }
  }; }
  static get states() { return {
    "showTapHighlight": {}
  }; }
  static get elementRef() { return "el"; }
}
