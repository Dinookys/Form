<?php
namespace Fields;

use Classes\Field;

/**
 * Special Class for render empty, Use to create extra html elements
 * @todo Overwrite this and create native html elements inside of Class Form
 */
class _Empty extends Field
{
    public function render(array $attrs)
    { }
}
