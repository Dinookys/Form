# Form
Classes for constructing forms with validation

## Examples

### Basic build

```
//POST, GET, REQUEST (DEFAULT is GET)
$form = new \Form\Form(array(
    'method' => 'post',
    'action' => ''
));

// Parameter $after of decorator accepts %s replace to message error if exists
$form->setFieldsDecorator('<div class="form-group my-3">', '</div>', 'initial,valid');
$form->setFieldsDecorator('<div class="form-group my-3">', '<div class="invalid-feedback">%s</div></div>', 'invalid');

$form->setFieldsCSSClass(array(
    'initial' => 'form-control'
));

// $form->setField($id, $attrs, $field)
// $id used the same field name
// $attrs form field
// $field class of the field

$form->setField(
    'name',
    array(
        'name' => 'name',
        'type' => 'text',
        'placeholder' => 'Nome',
    ),
    new \Form\Fields\Input
);

$form->setField(
    'email',
    array(
        'name' => 'email',
        'type' => 'email',
        'placeholder' => 'Email'
    ),
    new \Form\Fields\Input
);

// Reset global class for prevent apply initial class on next fields after this point
$form->resetFieldsCSSClass();

$form->setField(
    'submitButton',
    array(
        'type' => 'submit',
        'value' => 'Enviar',
        'name' => 'submit',
        'class' => 'btn btn-primary'
    ),
    new \Form\Fields\Input
)->setFieldDecorator('<div>', '</div>');


// Set validations
$form->setFieldValidator(new \Form\Validators\Required, 'name');
$form->setFieldValidator(new \Form\Validators\Required, 'email')
    ->setFieldValidator(new \Form\Validators\Email, 'email');

if ($form->hasPost()) {
    //Populate data for fields if as invalid fields
    !$form->isValid() ? $form->populate() : '';
}
```

### Output Example #1 using render method
```
$form->render();
```

### Output Example #2 use foreach loop with $form->renderDecorator()
```
<form action="" method="post" class="form container border mt-3 mb-5 p-5" >
    <?php foreach (array_keys($form->getFields()) as $id) {
        echo $form->renderDecorator($id);
        echo $form->renderField($id);
        echo $form->renderDecorator($id, true);
    } ?>
</form>
```

### Output Example #3 use foreach loop without $form->renderDecorator()
```
<form action="" method="post" class="form container border mt-3 mb-5 p-5" >
    <?php foreach (array_keys($form->getFields()) as $id) { ?>
    <div class="form-group border p-2 text-center" >
        <?php echo $form->renderField($id); ?>
        <?php echo $form->getFieldErrorMessage($id); ?>
    </div>
    <?php } ?>
</form>
```
