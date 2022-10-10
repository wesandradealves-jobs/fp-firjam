(function ($, Drupal) {
  Drupal.behaviors.brazilianIdsMask = {
    attach: function (context, settings) {
      // Adds mask to CPF/CNPJ field. They must have the
      // "brazilian-ids-cpf-cnpj-mask" CSS class.
      var cpfCnpjMask = function (val) {
        return val.replace(/\D/g, '').length <= 11 ? '000.000.000-000' : '00.000.000/0000-00';
      };
      var cpfCnpjMaskOptions = {
        onKeyPress: function (val, e, field, options) {
          field.mask(cpfCnpjMask.apply({}, arguments), options);
        }
      };
      $('.brazilian-ids-cpf-cnpj-mask', context)
        .once('brazilian-ids-cpf-cnpj-mask')
        .mask(cpfCnpjMask, cpfCnpjMaskOptions);
    }
  };
})(jQuery, Drupal);
