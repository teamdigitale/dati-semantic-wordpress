export declare class LazyLoader {
  private el;
  private attributes;
  private onLoad?;
  private intersectionObs?;
  private mutationObs?;
  private hasLoaded;
  constructor(el: HTMLElement, attributes: string[], onLoad?: (<T extends HTMLElement>(el: T) => void) | undefined);
  didLoad(): boolean;
  destroy(): void;
  private canObserveIntersection;
  private canObserveMutations;
  private lazyLoad;
  private onIntersection;
  private onMutation;
  private getLazyElements;
  private load;
  private loadEl;
}
