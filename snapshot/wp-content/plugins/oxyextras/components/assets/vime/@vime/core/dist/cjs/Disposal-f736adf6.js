'use strict';

class Disposal {
  constructor(dispose = []) {
    this.dispose = dispose;
  }
  add(callback) {
    this.dispose.push(callback);
  }
  empty() {
    this.dispose.forEach((fn) => fn());
    this.dispose = [];
  }
}

exports.Disposal = Disposal;
