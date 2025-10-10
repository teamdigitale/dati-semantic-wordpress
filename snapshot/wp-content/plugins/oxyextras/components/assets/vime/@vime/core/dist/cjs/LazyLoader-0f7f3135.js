'use strict';

const dom = require('./dom-4b0c36e3.js');
const support = require('./support-578168e8.js');

/* eslint-disable no-param-reassign */
class LazyLoader {
  constructor(el, attributes, onLoad) {
    var _a;
    this.el = el;
    this.attributes = attributes;
    this.onLoad = onLoad;
    this.hasLoaded = false;
    if (dom.isNullOrUndefined(this.el))
      return;
    this.intersectionObs = this.canObserveIntersection()
      ? (new IntersectionObserver(this.onIntersection.bind(this)))
      : undefined;
    this.mutationObs = this.canObserveMutations()
      ? (new MutationObserver(this.onMutation.bind(this)))
      : undefined;
    (_a = this.mutationObs) === null || _a === void 0 ? void 0 : _a.observe(this.el, {
      childList: true,
      subtree: true,
      attributeFilter: this.attributes,
    });
    this.lazyLoad();
  }
  didLoad() {
    return this.hasLoaded;
  }
  destroy() {
    var _a, _b;
    (_a = this.intersectionObs) === null || _a === void 0 ? void 0 : _a.disconnect();
    (_b = this.mutationObs) === null || _b === void 0 ? void 0 : _b.disconnect();
  }
  canObserveIntersection() {
    return support.IS_CLIENT && window.IntersectionObserver;
  }
  canObserveMutations() {
    return support.IS_CLIENT && window.MutationObserver;
  }
  lazyLoad() {
    var _a;
    if (this.canObserveIntersection()) {
      (_a = this.intersectionObs) === null || _a === void 0 ? void 0 : _a.observe(this.el);
    }
    else {
      this.load();
    }
  }
  onIntersection(entries) {
    entries.forEach((entry) => {
      if (entry.intersectionRatio > 0 || entry.isIntersecting) {
        this.load();
        this.intersectionObs.unobserve(entry.target);
      }
    });
  }
  onMutation() {
    if (this.hasLoaded)
      this.load();
  }
  getLazyElements() {
    return this.el.querySelectorAll('.lazy');
  }
  load() {
    window.requestAnimationFrame(() => {
      this.getLazyElements().forEach(this.loadEl.bind(this));
    });
  }
  loadEl(el) {
    var _a, _b;
    (_a = this.intersectionObs) === null || _a === void 0 ? void 0 : _a.unobserve(el);
    this.hasLoaded = true;
    (_b = this.onLoad) === null || _b === void 0 ? void 0 : _b.call(this, el);
  }
}

exports.LazyLoader = LazyLoader;
