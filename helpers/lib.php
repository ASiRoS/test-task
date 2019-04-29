<?php

if(!function_exists('array_key_first')) {
    /**
     * @param array $array
     * @return string|int|null
    */
    function array_key_first(array $array)
    {
        return $array ? array_keys($array)[0] : null;
    }
}