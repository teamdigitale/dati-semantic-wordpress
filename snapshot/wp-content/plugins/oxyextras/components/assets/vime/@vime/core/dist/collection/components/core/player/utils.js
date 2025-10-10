import { getElement } from '@stencil/core';
import { isInstanceOf, isNull } from '../../../utils/unit';
/**
 * Finds the closest ancestor player element.
 *
 * @param ref A HTMLElement that is within the player's subtree.
 */
export const findRootPlayer = (ref) => {
  const root = isInstanceOf(ref, HTMLElement) ? ref : getElement(ref);
  let player = root;
  while (!isNull(player) && !(/^VIME-PLAYER$/.test(player === null || player === void 0 ? void 0 : player.nodeName))) {
    player = player.parentElement;
  }
  if (isNull(player)) {
    throw Error(`Can't find root player given: ${root}`);
  }
  return player;
};
