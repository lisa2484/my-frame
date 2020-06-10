<?php

namespace app\models;

use app\models\DB;

class whitelist_dao
{
    private static $table = "whitelist";

    function getList($page, $limit)
    {
        return DB::select("SELECT * FROM `" . whitelist_dao::$table . "` LIMIT " . ($page - 1) * $limit . "," . $limit . ";");
    }

    function getIP($ip)
    {
        return DB::select("SELECT * FROM `" . whitelist_dao::$table . "` WHERE `ip` = '" . $ip . "' LIMIT 1");
    }

    function insertIP($ip)
    {
        return DB::DBCode("INSERT INTO `" . whitelist_dao::$table . "` (`ip`,`creator`,`creation_date`,`updater`,`update_date`) 
                           VALUE ('" . $ip . "','" . $_SESSION["act"] . "','" . date("Y-m-d H:i:s") . "','" . $_SESSION["act"] . "','" . date("Y-m-d H:i:s") . "')");
    }

    function setIP($id, $ip)
    {
        return DB::DBCode("UPDATE `" . whitelist_dao::$table . "` 
                           SET `ip` = '" . $ip . "', `updater` = '" . $_SESSION["act"] . "', `update_date` = '" . date("Y-m-d H:i:s") . "' 
                           WHERE `id` = '" . $id . "'");
    }

    function deleteIP($id)
    {
    }
}
