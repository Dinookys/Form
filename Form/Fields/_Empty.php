<?php

namespace Form\Fields;

use Form\Base\Field;

/**
 * Special Class for render empty, Use to create extra html elements
 * @todo Overwrite this and create native html elements inside of Class Form
 */
class _Empty extends Field
{
  public function __construct($html = '')
  {
    $this->html = $html;
  }

  public function render(array $attrs)
  {
    return $this->html;
  }
}