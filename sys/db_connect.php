<?php

namespace app\sys;

class db_connect
{
    private static $db_connect;

    protected static function getConnectData(int $Set)
    {
        self::setConnectSet();
        return self::$db_connect[$Set];
    }

    private static function setConnectSet()
    {
        $db_connect[0] = ['localhost', 'root', '', 'test'];
        self::$db_connect = $db_connect;
    }
}
