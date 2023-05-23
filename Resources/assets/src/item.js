import { Controller } from '@hotwired/stimulus';
import * as fx from './fx';

export default class extends Controller {
  static targets = ['content'];
  static values = {
    removeBlock: String,
    closedIcon: String,
    openIcon: String
  };

  remove(e) {
    e.preventDefault();

    if (window.confirm(this.removeBlockValue)) {
      fx.fadeOut(this.element, () => {
        this.element.remove();
      });
    }
  }

  toggle(e) {
    e.preventDefault();

    if (e.currentTarget.classList.contains('accordion-button')) {
      e.currentTarget.classList.toggle('collapsed');
    }

    if ('none' !== window.getComputedStyle(this.contentTarget).display) {
      e.currentTarget.innerHTML = this.closedIconValue;
    } else {
      e.currentTarget.innerHTML = this.openIconValue;
    }

    fx.slideToggle(this.contentTarget);
  }
}
