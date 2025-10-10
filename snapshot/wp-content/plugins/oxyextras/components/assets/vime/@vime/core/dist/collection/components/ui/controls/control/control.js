import { h, Host, Component, Prop, State, Element, Event, Watch, } from '@stencil/core';
import { isNull, isUndefined } from '../../../../utils/unit';
import { Disposal } from '../../../core/player/Disposal';
import { listen } from '../../../../utils/dom';
import { findRootPlayer } from '../../../core/player/utils';
import { withPlayerContext } from '../../../core/player/PlayerContext';
/**
 * @slot - Used to pass in the content of the control (text/icon/tooltip).
 */
export class Control {
  constructor() {
    this.keyboardDisposal = new Disposal();
    this.showTapHighlight = false;
    /**
     * Whether the control should be displayed or not.
     */
    this.hidden = false;
    /**
     * @internal
     */
    this.isTouch = false;
    withPlayerContext(this, ['isTouch']);
  }
  onKeysChange() {
    this.keyboardDisposal.empty();
    if (isUndefined(this.keys))
      return;
    const player = findRootPlayer(this);
    const codes = this.keys.split('/');
    this.keyboardDisposal.add(listen(player, 'keydown', (event) => {
      if (codes.includes(event.key)) {
        this.button.click();
      }
    }));
  }
  connectedCallback() {
    this.findTooltip();
    this.onKeysChange();
  }
  componentWillLoad() {
    this.findTooltip();
  }
  disconnectedCallback() {
    this.keyboardDisposal.empty();
  }
  onTouchStart() {
    this.showTapHighlight = true;
    setTimeout(() => { this.showTapHighlight = false; }, 100);
  }
  findTooltip() {
    const tooltip = this.el.querySelector('vime-tooltip');
    if (!isNull(tooltip))
      this.describedBy = tooltip.id;
    return tooltip;
  }
  onShowTooltip() {
    const tooltip = this.findTooltip();
    if (!isNull(tooltip))
      tooltip.active = true;
    this.vInteractionChange.emit(true);
  }
  onHideTooltip() {
    const tooltip = this.findTooltip();
    if (!isNull(tooltip))
      tooltip.active = false;
    this.button.blur();
    this.vInteractionChange.emit(false);
  }
  onFocus() {
    this.el.dispatchEvent(new window.Event('focus', { bubbles: true }));
    this.onShowTooltip();
  }
  onBlur() {
    this.el.dispatchEvent(new window.Event('blur', { bubbles: true }));
    this.onHideTooltip();
  }
  onMouseEnter() {
    this.onShowTooltip();
  }
  onMouseLeave() {
    this.onHideTooltip();
  }
  render() {
    return (h(Host, { class: {
        hidden: this.hidden,
      } },
      h("button", { class: {
          notTouch: !this.isTouch,
          tapHighlight: this.showTapHighlight,
        }, id: this.identifier, type: "button", "aria-label": this.label, "aria-haspopup": !isUndefined(this.menu) ? 'true' : undefined, "aria-controls": this.menu, "aria-expanded": !isUndefined(this.menu) ? (this.expanded ? 'true' : 'false') : undefined, "aria-pressed": !isUndefined(this.pressed) ? (this.pressed ? 'true' : 'false') : undefined, "aria-hidden": this.hidden ? 'true' : 'false', "aria-describedby": this.describedBy, onTouchStart: this.onTouchStart.bind(this), onFocus: this.onFocus.bind(this), onBlur: this.onBlur.bind(this), onMouseEnter: this.onMouseEnter.bind(this), onMouseLeave: this.onMouseLeave.bind(this), ref: (el) => { this.button = el; } },
        h("slot", null))));
  }
  static get is() { return "vime-control"; }
  static get originalStyleUrls() { return {
    "$": ["control.scss"]
  }; }
  static get styleUrls() { return {
    "$": ["control.css"]
  }; }
  static get properties() { return {
    "keys": {
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
        "tags": [{
            "text": undefined,
            "name": "inheritdoc"
          }],
        "text": "A slash (`/`) separated string of JS keyboard keys (`KeyboardEvent.key`), that when caught in\na `keydown` event, will trigger a `click` event on the control."
      },
      "attribute": "keys",
      "reflect": false
    },
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
        "text": "The `id` attribute of the control."
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
        "text": "Whether the control should be displayed or not."
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
        "text": "The `aria-label` property of the control."
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
        "text": "If the control has a popup menu, then this should be the `id` of said menu. Sets the\n`aria-controls` property."
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
        "text": "If the control has a popup menu, this indicates whether the menu is open or not. Sets the\n`aria-expanded` property."
      },
      "attribute": "expanded",
      "reflect": false
    },
    "pressed": {
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
        "text": "If the control is a toggle, this indicated whether the control is in a \"pressed\" state or not.\nSets the `aria-pressed` property."
      },
      "attribute": "pressed",
      "reflect": false
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
    "describedBy": {},
    "showTapHighlight": {}
  }; }
  static get events() { return [{
      "method": "vInteractionChange",
      "name": "vInteractionChange",
      "bubbles": true,
      "cancelable": true,
      "composed": true,
      "docs": {
        "tags": [],
        "text": "Emitted when the user is interacting with the control by focusing, touching or hovering on it."
      },
      "complexType": {
        "original": "boolean",
        "resolved": "boolean",
        "references": {}
      }
    }]; }
  static get elementRef() { return "el"; }
  static get watchers() { return [{
      "propName": "keys",
      "methodName": "onKeysChange"
    }]; }
}
