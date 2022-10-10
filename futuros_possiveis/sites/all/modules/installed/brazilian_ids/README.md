# Brazilian IDs
Drupal 8 version of the [Brazilian IDs (Brazilian Tax Number Fields) module](https://www.drupal.org/project/brazilian_ids).

This version of the module provides:
* Text field widgets for CPF, CNPJ, and CPF/CNPJ. The provided values are properly validated.
* Text field formatters for CPF, CNPJ, and CPF/CNPJ.
* A field type for RG which can collect number, agency and State.
* Form API elements for CPF, CNPJ, CPF/CNPJ, and RG. Values for CPF and CNPJ are validated. The RG element can collect number, agency and State.

## Form API usage

The following form element types are available:
* *brazilian_ids_cpf* (for CPF numbers only)
* *brazilian_ids_cnpj* (for CNPJ numbers only)
* *brazilian_ids_cpf_cnpj* (for CPF or CNPJ numbers)
* *brazilian_ids_rg* (for RG number, agency and State)

The CPF, CNPJ  and CPF/CNPJ elements have the same options available for the Drupal's textfield element - see [Drupal\Core\Render\Element\Textfield](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Render%21Element%21Textfield.php/class/Textfield/8.2.x).

The RG element has the following options besides the usual (*title*, *description*, *required*, etc.):
* **default_value**: an associative array containing the *number*, *agency*, and *state* keys and their corresponding values.
* **number_only**: a boolean indicating whether only the number should be required - the agency and state fields are not shown. Defaults to *FALSE*.
* **clean_number**: a boolean indication whether the value should have any mask removed on submission. Defaults to *FALSE*.
* **state_options**: an associative array of options to be used in the "State" select. By default, contains all Brazilian States.

Examples:

```php
$form['cpf'] = [
  '#type' => 'brazilian_ids_cpf',
  '#title' => t('CPF'),
  '#description' => t('Your CPF number.'),
  '#required' => TRUE,
];

$form['cnpj'] = [
  '#type' => 'brazilian_ids_cnpj',
  '#title' => t('CNPJ'),
  '#description' => t('Your CNPJ number.'),
  '#required' => TRUE,
];

$form['cpf_cnpj'] = [
  '#type' => 'brazilian_ids_cpf_cnpj',
  '#title' => t('CPF or CNPJ'),
  '#description' => t('Provide a CPF or CNPJ number.'),
  '#required' => TRUE,
];

$form['rg'] = [
  '#type' => 'brazilian_ids_rg',
  '#title' => t('RG'),
  '#description' => t('Provide your RG.'),
  '#required' => TRUE,
  '#clean_number' => TRUE,
];
```

## Recommended modules

The CPF, CNPJ and CPF/CNPJ elements will be masked if you enable the [Mask Field module](https://www.drupal.org/project/mask).
