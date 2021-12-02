<?php

namespace Form\Validators;


use Form\Base\Validator;

class Max extends Validator
{

    protected $max = 10;
    protected $message = 'O campo dever conter no máximo %d caracteres';

    public function __construct($max = 10)
    {
        $this->max = $max;
        $this->setMessage($this->message);
    }

    public function validation($value, \Form\Form $instance, $id = null)
    {
        if ($value && strlen($value) > $this->max) {
            return false;
        }

        return true;
    }

    public function setMessage($message = null)
    {
        $this->message = sprintf($message, $this->max);
    }
}
