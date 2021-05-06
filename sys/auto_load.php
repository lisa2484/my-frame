<?php

namespace app\sys;

class auto_load
{
    static function run(int $dbset)
    {
        error_reporting(E_ERROR);
        self::setAutoload();
        system::start();
        DB::dbCon($dbset);
    }

    static function setAutoload()
    {
        spl_autoload_register(function ($class) {
            $ra = explode('\\', $class);
            unset($ra[0]);
            $class = implode('\\', $ra);
            $r = __DIR__ . '/../' . $class . '.php';
            if (!file_exists($r)) die('include fail.' . $r);
            include $r;
        });
    }
}
