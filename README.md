# Form
Classes for constructing forms with validation

## Examples

### Example #1 using render method
```
<?php

$form = new \Form\Form();
$form->setFormAttr('method', 'post') //POST, GET, REQUEST (DEFAULT is GET)
    ->setFormAttr('action', 'main-action.php');

// Parameter $after of decorator accepts %s replace to message error if exists
$form->setFieldsDecorator('<div class="form-group">', '<span class="error">%s</span></div>');

$form->setFieldsCSSClass(array(
    'initial' => 'form-control'
));

// $form->setField($attrs, $field)
// $attrs form field
// $field class of the field

$form->setField(
    'name',
    array(
        'name' => 'name',
        'type' => 'text',
        'value' => '',
        'placeholder' => 'Nome'
    ),
    new \Fields\Input
)
    ->setFieldValidator(new \Validators\Required);

$form->setField(
    'email',
    array(
        'name' => 'email',
        'type' => 'email',
        'value' => '',
        'placeholder' => 'Email'
    ),
    new \Fields\Input
)
    ->setFieldValidator(new \Validators\Required)
    ->setFieldValidator(new \Validators\Email);

// Reset global class for prevent apply initial class on next fields after this point
$form->setFieldsCSSClass(array('initial' => ''));

$form->setField(
    'submitButton',
    array(
        'type' => 'submit',
        'value' => 'Enviar',
        'name' => 'submit',
        'class' => 'btn btn-primary'
    ),
    new \Fields\Input
)
    ->setFieldDecorator('<div>', '</div>');

if ($form->hasPost()) {
    !$form->isValid() ? $form->populate() : '';
}

$form->render();

```
