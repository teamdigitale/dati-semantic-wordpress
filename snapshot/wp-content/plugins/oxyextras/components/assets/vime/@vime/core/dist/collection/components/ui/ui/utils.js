import { getElement } from '@stencil/core';
import { isNull } from '../../../utils/unit';
export const findUIRoot = (ref) => {
  let ui = getElement(ref);
  while (!isNull(ui) && !(/^VIME-UI$/.test(ui.nodeName))) {
    ui = ui.parentElement;
  }
  return ui;
};
