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

// Parameter $close of decorator accepts %s replace to message error if exists
$form->setFieldsDecorator('<div class="form-group my-3">', '</div>', 'initial,valid');
$form->setFieldsDecorator('<div class="form-group my-3">', '<div class="invalid-feedback">%s</div></div>', 'invalid');

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
        'class' => 'form-control'
    ),
    new \Form\Fields\Input
);

$form->setField(
    'email',
    array(
        'name' => 'email',
        'type' => 'email',
        'placeholder' => 'Email',
        'class' => 'form-control'
    ),
    new \Form\Fields\Input
);

$form->setField(
    'confirm_false',
    array(
        'type'  => 'radio',
        'name'  => 'confirm',
        'value' => 'false',
        'label' => 'Não'
    ),
    new \Form\Fields\Input
);

$form->setField(
    'confirm_true',
    array(
        'type'  => 'radio',
        'name'  => 'confirm',
        'value' => 'true',
        'checked' => 'checked',
        'label' => 'Sim',
        'labelAttrs' => array(
            'class' => 'label_class'
        )
    ),
    new \Form\Fields\Input
);

$form->setField(
    'select_example',
    array(
        'name' => 'select_example',
        'class' => 'form-control',
        'multiple' => '',
        'value' => array('','valor2'),
        'choices' => [
            ['', '--Select--'],
            ['valor', 'Valor'],
            ['valor2', 'Valor2']
        ]
    ),
    new \Form\Fields\Select
);

$form->setField(
    'submitButton',
    array(
        'type' => 'submit',
        'value' => 'Enviar',
        'name' => 'submit',
        'class' => 'btn btn-primary'
    ),
    new \Form\Fields\Input
)->notUseFieldDecorator();


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
