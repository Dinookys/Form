<?php 

namespace Validators;


use Classes\Validator;

class Required extends Validator
{    

    protected $message = 'This field is required';

    public function validation($value, \Form\Form $instance)
    {
        if(is_string($value)) {
            $value = trim($value);
            return strlen($value) ? true : false;
        }

        if(is_array($value)) {
            return true;
        }

        return false;
    }
}