<?php
namespace Fields;

use Classes\Field;

/**
 * 
 */
class Textarea extends Field 
{
    public function render(array $attrs)
    {
        $default_attrs = array(
            'name' => 'inputName',
            'value' => ''
        );

        $attrs = array_merge($default_attrs, $attrs);
        $value = $attrs['value'];
        unset($attrs['value']);

        return '<textarea '. $this->placeAttrs($attrs) .' >'. $value .'</textarea>';
    }
}
