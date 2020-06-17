<?php

namespace app\models;

include './sys/serverset.php';

class DB
{
    static function select($SQLCode)
    {
        return mysqli_fetch_all(mysqli_query(getServer(), $SQLCode), MYSQLI_ASSOC);
    }

    static function DBCode($SQLCode)
    {
        $SQL = getServer();
        $request = true;
        if (!$SQL->query($SQLCode)) {
            $request = false;
        }
        $SQL->close();
        return $request;
    }
}
