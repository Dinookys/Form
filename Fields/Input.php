<?php
namespace Fields;

use Classes\Field;

/**
 * 
 */
class Input extends Field 
{
    public function render(array $attrs)
    {
        $default_attrs = array(
            'type' => 'text',
            'name' => 'inputName',
            'value' => ''
        );

        $attrs = array_merge($default_attrs, $attrs);

        return '<input '. $this->placeAttrs($attrs) .' />';
    }
}
