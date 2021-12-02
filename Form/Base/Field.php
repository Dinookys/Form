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
}