<?php

namespace Form\Fields;

use Form\Base\Field;

/**
 * 
 */
class Input extends Field
{

    use \Form\Traits\HelperHTML;

    public function render(array $attrs)
    {
        $default_attrs = array(
            'type' => 'text',
            'name' => 'inputName',
            'value' => ''
        );

        $attrs = array_merge($default_attrs, $attrs);
        $labelAttrs = $this->extractLabelAttrs($attrs);

        if (isset($attrs['values']) && in_array($attrs['type'], array('radio', 'checkbox'))) {

            $values = $attrs['values'];
            unset($attrs['values']);
            unset($attrs['checked']);

            if ((is_array($values) && in_array($attrs['value'], $values)) ||
                $attrs['value'] == $values
            ) {
                $attrs['checked'] = 'checked';
            }
        }

        $input = '<input ' . $this->placeAttrs($attrs) . ' />';

        return $this->renderWithLabel($labelAttrs, $input);
    }
}
