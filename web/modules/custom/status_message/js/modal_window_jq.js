(function ($, Drupal, settings) {

  "use strict";
  Drupal.behaviors.status_message_modal_window = {
    attach: function (context, settings) {
      var modal = $('#modal_window');
      modal.css('background', drupalSettings.statusMessage.modalWindow.background);
      modal.css('width',drupalSettings.statusMessage.modalWindow.width);
      modal.css('height',drupalSettings.statusMessage.modalWindow.height)
      console.log(drupalSettings.statusMessage.modalWindow.height);
    }
  }

    })(jQuery, Drupal, drupalSettings);