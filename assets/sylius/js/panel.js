import Sortable from 'sortablejs';
import * as fx from '../../fx';

const resetOrder = panel => {
  let i = 0;

  panel.querySelectorAll('.js-panel-position').forEach(
    positionInput => positionInput.value = ++i,
  );
};

const initSortable = panel => {
  new Sortable(panel, {
    draggable: '.js-panel-container',
    handle: '.js-panel-sortable-handler',
    animation: 150,
    onEnd: () => resetOrder(panel),
  });
};

window.addEventListener('load', () => {
  document.querySelectorAll('.js-panel-add-block').forEach(blockSelector => {
    blockSelector.addEventListener('change', e => {
        e.preventDefault();

        const parentId = blockSelector.getAttribute('data-panel-id');
        const panelHolder = document.querySelector(`#${parentId}`);
        panelHolder.setAttribute(
          'data-index',
          panelHolder.querySelectorAll('.js-panel-container').length.toString(),
        );

        // Get selected option value
        const selectedblockType = blockSelector.value;
        const selectedblockName = blockSelector.selectedOptions[0].getAttribute('data-name');

        // Get the new index
        const index = parseInt(panelHolder.getAttribute('data-index'), 10);

        // Get the prototype data
        const proto = panelHolder.querySelector(`[data-block-type="${selectedblockType}"]`);

        if (null === proto) {
          return;
        }

        const blockItemProto = panelHolder.getAttribute('data-block-item-prototype')
          .replace(/__type__/g, selectedblockType)
          .replace(/__name__/g, selectedblockName)
          .replace(/__state_class__/g, 'yellow')
          .replace(/__position__/g, index)
          .replace(/__header__/g, panelHolder.getAttribute('data-str-new-block') + ' ' + selectedblockName)
          .replace(/__body_attr__/g, 'style="display:none;"')
          .replace(/__body__/g, proto.getAttribute('data-block-prototype').replace(/__umanit_block__/g, index))
        ;

        // Increment the index with one for the next item
        panelHolder.setAttribute('data-index', (index + 1).toString());

        // Display html content
        panelHolder.insertAdjacentHTML('beforeend', blockItemProto);

        const insertedItem = panelHolder.lastElementChild;

        // Add custom javascript event on the new panel
        const eventItem = new CustomEvent('ublock.after_added', { detail: { panel: panelHolder, item: insertedItem } });
        document.dispatchEvent(eventItem);

        // Scroll to newly created block
        window.scrollTo({
          behavior: 'smooth',
          left: 0,
          top: insertedItem.offsetTop,
        });

        // Reset select
        blockSelector.value = '';

        resetOrder(panelHolder);
        initSortable(panelHolder);
      },
    );
  });

  document.querySelectorAll('.js-panel-holder').forEach(panel => {
    // Block sorting using SortableJS
    initSortable(panel);

    panel.addEventListener('click', e => {
      // Remove panel item
      if (
        e.target.classList.contains('js-panel-remove') ||
        (e.target.parentElement && e.target.parentElement.classList.contains('js-panel-remove'))
      ) {
        e.preventDefault();

        if (window.confirm(panel.getAttribute('data-str-remove-block'))) {
          panel.setAttribute(
            'data-index',
            (parseInt(panel.getAttribute('data-index'), 10) - 1).toString(),
          );

          const container = e.target.closest('.js-panel-container');

          fx.fadeOut(container, () => {
            container.remove();

            resetOrder(panel);
          });
        }
        // Toggle panel item
      } else if (
        e.target.classList.contains('js-panel-toggle') ||
        (e.target.parentElement && e.target.parentElement.classList.contains('js-panel-toggle'))
      ) {
        e.preventDefault();

        const container = e.target.closest('.js-panel-container');
        const itemBody = container.querySelector('.js-panel-body');

        if ('none' !== window.getComputedStyle(itemBody).display) {
          container.querySelector('.js-panel-toggle').innerHTML = '<i class="caret right icon"></i>';
        } else {
          container.querySelector('.js-panel-toggle').innerHTML = '<i class="caret down icon"></i>';
        }

        fx.slideToggle(itemBody);
      }
    });
  });
});
