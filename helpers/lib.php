<?php

if(!function_exists('array_key_first')) {
    function array_key_first(array $array): string {
        return $array ? array_keys($array)[0] : null;
    }
}
