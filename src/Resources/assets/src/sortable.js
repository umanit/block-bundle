/*
 * Greatly inspired by stimulus-sortable
 *
 * @see https://github.com/stimulus-components/stimulus-sortable
 */

import { Controller } from 'stimulus';
import Sortable from 'sortablejs';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
  static values = {
    animation: Number,
    handle: String,
  };

  initialize() {
    this.start = this.start.bind(this);
    this.end = this.end.bind(this);
  }

  connect() {
    this.sortable = new Sortable(this.element, {
      ...this.defaultOptions,
      ...this.options,
    });
  }

  disconnect() {
    this.sortable.destroy();
    this.sortable = undefined;
  }

  start() {
    const panel = this.element;
    const eventItem = new CustomEvent('umanit-block-bundle.onSortStart', { detail: { panel } });
    document.dispatchEvent(eventItem);
  }

  end() {
    const panel = this.element;
    const eventItem = new CustomEvent('umanit-block-bundle.onSortEnd', { detail: { panel } });
    document.dispatchEvent(eventItem);
  }

  get options() {
    return {
      animation: this.animationValue || this.defaultOptions.animation || 150,
      handle: this.handleValue || this.defaultOptions.handle || undefined,
      onStart: this.start,
      onEnd: this.end,
    };
  }

  get defaultOptions() {
    return {
      handle: '.js-panel-sortable-handler',
    };
  }
}
