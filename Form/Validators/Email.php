<?php

namespace Form\Validators;


use Form\Base\Validator;

class Email extends Validator
{

  protected $message = 'O email não está em um formato válido';

  public function validation($value, \Form\Form $instance, $id = null)
  {
    return filter_var($value, FILTER_VALIDATE_EMAIL) ? true : false;
  }
}