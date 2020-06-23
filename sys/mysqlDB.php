<?php

namespace app\models;

use DBConnect;

include './sys/serverset.php';

class DB extends DBConnect
{
    private static $dbcon;

    static function select($SQLCode)
    {
        return mysqli_fetch_all(mysqli_query(self::$dbcon, $SQLCode), MYSQLI_ASSOC);
    }

    static function DBCode($SQLCode)
    {
        $request = true;
        if (!self::$dbcon->query($SQLCode)) {
            $request = false;
        }
        return $request;
    }

    static function dbCon()
    {
        if (!isset(self::$dbcon)) self::$dbcon = self::getServer();
    }

    static function getDBCon()
    {
        return self::$dbcon;
    }
}
