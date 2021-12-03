<?php

namespace Form\Fields;

use Form\Base\Field;

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
        array('', 'Select')
      )
    );

    $attrs = array_merge($default_attrs, $attrs);

    $choices = $attrs['choices'];
    $value = $attrs['value'];
    unset($attrs['choices']);
    unset($attrs['value']);

    $html = '<select ' . $this->placeAttrs($attrs) . ' >';
    $html .= $this->loopOption($choices, $value);
    $html .= "</select>";

    return $html;
  }

  protected function loopOption($choices, $value)
  {
    $html = '';
    foreach ($choices as $_choice) {

      if (is_array($_choice)) {
        list($choice, $label) = $_choice;
      } else {
        $label = $_choice;
        $choice = $_choice;
      }

      $html .= '<option value="' . $choice . '" ' . $this->isSelected($value, $choice) . '>' . $label . '</option>' . PHP_EOL;
    }

    return $html;
  }

  protected function isSelected($value, $option_value)
  {

    if (is_array($value) && in_array($option_value, $value)) {
      return 'selected';
    }

    if ($value === $option_value) {
      return ' selected';
    }
  }
}
