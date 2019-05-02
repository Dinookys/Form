<?php 

namespace Validators;


use Classes\Validator;

class Required extends Validator
{    

    protected $message = 'Este campo é obrigatório';

    public function validation($value, \Form\Form $instance, $id = null)
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