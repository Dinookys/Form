<?php
namespace Fields;

use Classes\Field;

/**
 * @obs for add options use  attr "choices"  in array format, for value diff of text display use array with 'value' and 'title' key
 * ex.: array('value' => 'bar', 'title' => 'Diff title')
 */
class Select extends Field
{
    public function render(array $attrs)
    {
        $default_attrs = array(
            'type' => 'text',
            'name' => 'inputName',
            'value' => '',
            'choices' => array(
                array('value' => 'Select', 'title' => 'Select')
            )
        );

        $attrs = array_merge($default_attrs, $attrs);

        $choices = $attrs['choices'];
        $value = $attrs['value'];
        unset($attrs['choices']);
        unset($attrs['value']);

        $html = '<select '. $this->placeAttrs($attrs) . ' >';
        foreach ($choices as $choice) {
            if(is_array($choice)) {
                $html .= '<option value="'. $choice['value'] .'" '. $this->isSelected($value, $choice['value']) .'>'. $choice['title'] .'</option>' . PHP_EOL;
            } else {
                $html .= '<option value="'. $choice .'" '. $this->isSelected($value, $choice) .'>'. $choice .'</option>' . PHP_EOL;
            }
        }
        $html .= '</select>';

        return $html;
    }

    protected function isSelected($value1, $value2)
    {
        if($value1 == $value2)
        {
            return ' selected';
        }
    }
}
