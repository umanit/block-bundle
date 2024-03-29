import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
  static targets = ['panel', 'select', 'position', 'prototype'];
  static values = {
    index: Number,
    newBlock: String,
  };

  connect() {
    this.indexValue = this.positionTargets.length;

    this.computeOrderOnSubmit = this.computeOrderOnSubmit.bind(this);

    this.selectTarget.form.addEventListener('submit', this.computeOrderOnSubmit);
  }

  disconnect() {
    this.selectTarget.form.removeEventListener('submit', this.computeOrderOnSubmit);
  }

  add(e) {
    e.preventDefault();

    // Get selected option value
    const selectedblockType = e.currentTarget.value;
    const selectedblockName = e.currentTarget.selectedOptions[0].dataset.name;

    // Get the prototype data
    const proto = this.getPrototype(selectedblockType);

    if (null === proto) {
      return;
    }

    const blockItemProto = this.panelTarget.dataset.blockItemPrototype
      .replace(/__type__/g, selectedblockType)
      .replace(/__name__/g, selectedblockName)
      .replace(/__state_class__/g, 'yellow')
      .replace(/__position__/g, this.indexValue)
      .replace(/__header__/g, this.newBlockValue + ' ' + selectedblockName)
      .replace(/__body_attr__/g, '')
      .replace(/__body__/g, proto.dataset.blockPrototype.replace(/__umanit_block__/g, this.indexValue))
    ;

    // Increment the index by one for the next item
    ++this.indexValue;

    // Display html content (with <script> execution, necessary for WYSIWYG
    // fields managed by CKEditor, for example)
    const fragment = document.createRange().createContextualFragment(blockItemProto);

    this.panelTarget.appendChild(fragment);

    const insertedItem = this.panelTarget.lastElementChild;

    // Add custom javascript event on the new panel
    this.dispatch('after-add', {
      detail: {
        panel: this.panelTarget,
        item: insertedItem,
      },
    });

    ///////////////////////////////////////////////////////
    // COMPATIBILITY WITH EASYADMIN ///////////////////////
    ///////////////////////////////////////////////////////
    // Allows EasyAdmin JS to be bound to new blocks, without
    // the controller prefix (allows for EA types like
    // FileUploadType to be used within blocks)
    this.dispatch('ea.collection.item-added', { prefix: null });

    // Scroll to newly created block
    insertedItem.scrollIntoView({ behavior: 'smooth', block: 'start' });

    // Reset select
    e.currentTarget.value = '';
  }

  computeOrderOnSubmit() {
    let i = 0;

    for (const position of this.positionTargets) {
      ++i;

      position.value = i;
    }
  }

  getPrototype(blockType) {
    let prototype = null;

    for (const prototypeTarget of this.prototypeTargets) {
      if (blockType === prototypeTarget.dataset.blockType) {
        prototype = prototypeTarget;
        break;
      }
    }

    return prototype;
  }
}
