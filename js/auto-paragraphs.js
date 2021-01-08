(function ($, Drupal, drupalSettings) {

  'use strict';

  Drupal.behaviors.bform = {
    attach : function(context, settings) {
      // Load the existing change event.
      var paragraphDetailEvents = $._data($("#field-paragraph-detail-add-more")[0], "events");
      var paragraphHeaderEvents = $._data($("#field-paragraph-header-add-more")[0], "events");

      $('#edit-field-type').change(function(event) {
        let changeValue = $(this).val();
        if (changeValue == 1) {
          $.each(paragraphDetailEvents.mousedown, function () {
            this.handler(event);
          });
        }
        if (changeValue == 2) {
          $.each(paragraphHeaderEvents.mousedown, function () {
            this.handler(event);
          });
        }
      });
    }
  };

})(jQuery, Drupal);

