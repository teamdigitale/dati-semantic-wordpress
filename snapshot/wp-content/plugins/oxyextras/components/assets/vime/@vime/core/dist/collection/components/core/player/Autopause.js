let players = [];
export class Autopause {
  constructor(player) {
    this.player = player;
    players.push(player);
  }
  willPlay() {
    players.forEach((p) => {
      try {
        // eslint-disable-next-line no-param-reassign
        if (p !== this.player && p.autopause)
          p.paused = true;
      }
      catch (e) {
        // Might throw when testing because `disconnectCallback` isn't called.
      }
    });
  }
  destroy() {
    players = players.filter((p) => p !== this.player);
  }
}
