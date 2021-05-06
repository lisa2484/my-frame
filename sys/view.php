<?php

namespace app\sys;

class view
{
    static function webView(string $key, array $value = [])
    {
        if (!empty($value))
            foreach ($value as $k => $d) {
                $$k = $d;
            }
        include './views/' . $key . '.php';
        die;
    }

    static function stringView(string $str)
    {
        die($str);
    }

    static function jsonView(array $arr)
    {
        die(json_encode($arr));
    }
}
