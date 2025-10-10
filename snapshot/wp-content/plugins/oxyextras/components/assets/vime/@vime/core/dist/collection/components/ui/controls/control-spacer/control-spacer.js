import { h, Host, Component } from '@stencil/core';
export class ControlSpacer {
  render() {
    return (h(Host, null));
  }
  static get is() { return "vime-control-spacer"; }
  static get originalStyleUrls() { return {
    "$": ["control-spacer.css"]
  }; }
  static get styleUrls() { return {
    "$": ["control-spacer.css"]
  }; }
}
