<?php

namespace app\models;

use app\models\DB;

class whitelist_dao
{
    private static $table = "ipwhitelist";

    function getList(int $page, int $limit)
    {
        return DB::select("SELECT * FROM `" . self::$table . "` WHERE `is_del` = 0 LIMIT " . ($page - 1) * $limit . "," . $limit . ";");
    }

    function getIP($ip)
    {
        return DB::select("SELECT * FROM `" . self::$table . "` WHERE `ip` = '" . $ip . "' AND `is_del` = 0 LIMIT 1");
    }

    function insertIP($ip)
    {
        return DB::DBCode("INSERT INTO `" . self::$table . "` (`ip`,`creator`,`create_dt`,`create_ip`,`updater`,`update_dt`,`update_ip`) 
                           VALUE (
                               '" . $ip . "',
                               '" . $_SESSION["act"] . "',
                               '" . date("Y-m-d H:i:s") . "',
                               '" . getRemoteIP() . "',
                               '" . $_SESSION["act"] . "',
                               '" . date("Y-m-d H:i:s") . "'
                               '" . getRemoteIP() . "')");
    }

    function setIP(int $id, $ip)
    {
        return DB::DBCode("UPDATE `" . self::$table . "` 
                           SET `ip` = '" . $ip . "', `updater` = '" . $_SESSION["act"] . "',
                               `update_dt` = '" . date("Y-m-d H:i:s") . "',
                               `update_ip` = '" . getRemoteIP() . "'
                           WHERE `id` = '" . $id . "'");
    }

    function setOnf($id, int $switch)
    {
    }

    function deleteIP(int $id)
    {
        return DB::DBCode("UPDATE `" . self::$table . "` 
                           SET `is_del` = 1 ,
                               `updater` = '" . $_SESSION["act"] . "',
                               `update_dt` = '" . date("Y-m-d H:i:s") . "',
                               `update_ip` = '" . getRemoteIP() . "'
                           WHERE `id` = '" . $id . "'");
    }
}
