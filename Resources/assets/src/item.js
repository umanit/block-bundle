import { Controller } from 'stimulus';
import * as fx from './fx';

export default class extends Controller {
  static targets = ['content'];
  static values = {
    removeBlock: String,
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

    if ('none' !== window.getComputedStyle(this.contentTarget).display) {
      e.currentTarget.innerHTML = '<i class="caret right icon"></i>';
    } else {
      e.currentTarget.innerHTML = '<i class="caret down icon"></i>';
    }

    fx.slideToggle(this.contentTarget);
  }
}
