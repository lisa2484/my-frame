<?php

namespace app\models;

include './sys/serverset.php';

class DB
{
    private static $dbcon;

    static function select($SQLCode)
    {
        self::dbCon();
        $req = mysqli_fetch_all(mysqli_query(self::$dbcon, $SQLCode), MYSQLI_ASSOC);
        return $req;
    }

    static function DBCode($SQLCode)
    {
        self::dbCon();
        $request = true;
        if (!self::$dbcon->query($SQLCode)) {
            $request = false;
        }
        return $request;
    }

    private static function dbCon()
    {
        if (!isset(self::$dbcon)) self::$dbcon = getServer();
    }
}
