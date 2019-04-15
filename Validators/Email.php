<?php 

namespace Validators;


use Classes\Validator;

class Email extends Validator
{    

    protected $message = 'The email is not in a valid format';

    public function validation($value, \Form\Form $instance)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) ? true : false;
    }
}