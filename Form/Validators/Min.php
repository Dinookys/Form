<?php

namespace Form\Validators;


use Form\Base\Validator;

class Min extends Validator
{

    protected $min = 10;
    protected $message = 'Este campo deve ter no mínimo %d caracteres';

    public function __construct($min = 10)
    {
        $this->min = $min;
        $this->setMessage($this->message);
    }

    public function validation($value, \Form\Form $instance, $id = null)
    {
        if (!$value || strlen($value) < $this->min) {
            return false;
        }
        
        return true;
    }

    public function setMessage($message = null)
    {
        $this->message = sprintf($message, $this->min);
    }
}
