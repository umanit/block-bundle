$(document).ready(function () {

  $('[data-behavior="add-block"]').each(function () {
    $addBlockSelect = $(this);
    $collectionHolder = $('#' + $addBlockSelect.data('panel-id'));

    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    $collectionHolder.data('index', $collectionHolder.find('.ublock').length);

    $addBlockSelect.on('change', $addBlockSelect, function (e) {
      $addBlockSelect = $(this);
      $collectionHolder = $('#' + $addBlockSelect.data('panel-id'));
      $collectionHolder.data('index', $collectionHolder.find('.ublock').length);

      e.preventDefault();

      // Get selected option value
      var selectedblockType = $addBlockSelect.val();
      var selectedblockName = $addBlockSelect.find(':selected').data('name');

      // Get the new index
      var index = $collectionHolder.data('index');

      // Get the prototype data
      var proto = $("div[data-type='" + selectedblockType + "']").data('prototype');

      if (typeof proto === 'undefined') {
        return;
      }

      var newForm = proto.replace(/__umanit_block__/g, index);

      // HTML template
      var $ublock = $('<div class="ublock-body"></div>').append(newForm);
      var $embededItem = $(
        '<div class="ublock ui-sortable-handle" data-block-type="' + selectedblockType + '" data-name="' + selectedblockName + '" data-order="' + index + '">' +
        '   <div class="ublock-header"><h3 class="ublock-title">' + NEW_BLOCK_STR + selectedblockName + '</h3>' +
        '       <div class="ublock-tools"><span title="' + NOT_SAVED_STR + '" class="ublock-label bg-yellow">' + selectedblockName + '</span>' +
        '           <button type="button" class="btn btn-ublock-tool" data-target="removeBoxItem"><i class="fa fa-trash"></i></button>' +
        '           <button type="button" class="btn btn-ublock-tool" data-collapse="ublock-body"><i class="fa fa-caret-up"></i></button>' +
        '       </div>' +
        '   </div>' +
        '</div>'
      );
      $embededItem.append($ublock);

      // increment the index with one for the next item
      $collectionHolder.data('index', index + 1);

      // Display html content
      $collectionHolder.append($embededItem);

      // add custom javascript event on the new panel
      var eventItem = new CustomEvent('ublock.after_added', { 'detail' : $embededItem });
      document.dispatchEvent(eventItem);

      // Scroll to newly created block
      $('html, body').animate({
        scrollTop: $embededItem.offset().top - 130
      }, 800);

      // Reset select
      if (typeof Select2 === "object") {
        // Using jQuerySelect2
        $addBlockSelect.select2('val', '');
      } else {
        // Using only jQuery
        $addBlockSelect.val('');
      }

      resetOrder($collectionHolder);
    });
  });

  // Update block order
  function resetOrder($collectionHolder) {
    var i = 0;
    $collectionHolder.find('[data-target="position"]').each(function () {
      $(this).val(i);
      i++;
    });
  }

  // Block sorting using jQueryUI's "Sortable"
  $(".sortable").sortable({
    handle: '.ublock-header',
    placeholder: "ui-state-highlight",
    forcePlaceholderSize: true,
    stop: function (event, ui) {
      var $collectionHolder = ui.item.closest('.panel-holder');
      resetOrder($collectionHolder);
    }
  });

  // Remove ublock item
  $(document).on("click", '[data-target="removeBoxItem"]', function (e) {
    e.preventDefault();
    if (window.confirm("You're about to remove this block, confirm?")) {
      $(this).closest('.ublock').fadeOut(400, "swing", function () {
        // get the new index
        var $collectionHolder = $(this).closest('.panel-holder');
        var index = $collectionHolder.data('index');
        $(this).remove();
        $collectionHolder.data('index', index - 1);
        resetOrder($collectionHolder);
      });
    }
  });

  $(document).on("click", '[data-collapse="ublock-body"]', function (e) {
    e.preventDefault();

    var $ublockBlody = $(this).closest('.ublock').find('.ublock-body');

    if ($ublockBlody.is(':visible')) {
      $(this).html('<i class="fa fa-caret-down"></i>')
    } else {
      $(this).html('<i class="fa fa-caret-up"></i>')
    }
    $ublockBlody.slideToggle()
  });

});

