<?php

namespace Classes;

/**
 * Inteface for create validation
 */
abstract class Validator {    

    protected $message = 'This field is required';

    /**
     * Method for create validation logic
     * @param string|array|object $value 
     * @return bool
     */
    abstract function validation($value, \Form\Form $instance);

    /**
     * Method for invoking the error message
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Method for set custom error message
     */
    public function setMessage($message = null)
    {
        if($message) {
            $this->message = $message;
        }
    }
}
