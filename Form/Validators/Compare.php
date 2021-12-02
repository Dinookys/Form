<?php

namespace Form\Validators;


use Form\Base\Validator;

/**
 * @param string $compare field name for comparation 
 * @var $message using method sprintf for replace %s for label or placholder of field comparation name
 */
class Compare extends Validator
{
  protected $compare;
  protected $message = 'O valor do campo deve ser igual ao campo: "%s"';

  public function __construct($compare = null)
  {
    if ($compare == null) {
      throw new \Exception('Need set compare field name');
    }

    $this->compare = $compare;
  }

  public function validation($value, \Form\Form $instance, $id = null)
  {
    $data_compare = $instance->getData()[$this->compare];

    $field_compare = $instance->getField($this->compare)[0];

    //Get Field Attrs;
    $this->setMessage(sprintf($this->message, $field_compare['label'] ?? $field_compare['placeholder']));

    if ($value != $data_compare) {
      return false;
    }

    return true;
  }
}