<?php

namespace Traits;

trait HelperHTML
{
    /**
     * Convert array of attr to attrs tag html
     * @param array $attrs
     * @return string
     */
    protected function placeAttrs(array $attrs)
    {
        $result = ' ';

        foreach ($attrs as $attr => $value) {
            $result .= $attr . '="' . $value . '" ';
        }

        return $result;
    }
}
