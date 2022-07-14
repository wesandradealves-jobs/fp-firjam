/**
 * @file
 * Global utilities.
 *
 */
(function ($, Drupal) {

  'use strict';

  Drupal.behaviors.futurospossiveis2022 = {
    attach: function (context, settings) {
      $('main', context).once('futurospossiveis2022').each(function () {
        console.log(true)
      })
    }
  };

})(jQuery, Drupal);
