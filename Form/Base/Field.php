<?php

namespace Form\Base;

use Form\Traits\HelperHTML;

abstract class Field
{

  use HelperHTML;

  /**
   * @return string HTML string
   * @param array $attrs: id,class,name,value any field attrsx
   */
  abstract function render(array $attrs);

  protected function renderWithLabel(array $attrs, $input = '')
  {

    extract($attrs);

    if (isset($label)) {     

      $label = '<label for="' . $id . '" ' . $this->placeAttrs($labelAttrs) . '>' . $label . '</label>';

      return !$labelAfter ? ($label . $input) : ($input . $label);
    }

    return $input;
  }

  protected function extractLabelAttrs(&$attrs = array()) {

    if(!isset($attrs['label'])) return array();

    $id = $attrs['id'];
    $label = $attrs['label'];
    $labelAttrs = isset($attrs['labelAttrs']) ? $attrs['labelAttrs'] : array();
    $labelAfter = isset($attrs['labelAfter']) ? false : true;

    unset($attrs['label']);
    unset($attrs['labelAttrs']);

    return array(
      'id' => $id,
      'label' => $label,
      'labelAttrs' => $labelAttrs,
      'labelAfter' => $labelAfter,
    );
  }
}
