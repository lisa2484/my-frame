<?php

namespace app\models;

use app\models\DB;

class whitelist_dao
{
    private static $table = "whitelist";

    function getList($page, $limit)
    {
        return DB::select("SELECT * FROM `" . whitelist_dao::$table . "` WHERE `is_del` = 0 LIMIT " . ($page - 1) * $limit . "," . $limit . ";");
    }

    function getIP($ip)
    {
        return DB::select("SELECT * FROM `" . whitelist_dao::$table . "` WHERE `ip` = '" . $ip . "' AND `is_del` = 0 LIMIT 1");
    }

    function insertIP($ip)
    {
        return DB::DBCode("INSERT INTO `" . whitelist_dao::$table . "` (`ip`,`creator`,`creator_name`,`creation_date`,`updater`,`updater_name`,`update_date`) 
                           VALUE ('" . $ip . "','" . $_SESSION["act"] . "','" . $_SESSION["name"] . "','" . date("Y-m-d H:i:s") . "','" . $_SESSION["act"] . "','" . $_SESSION["name"] . "','" . date("Y-m-d H:i:s") . "')");
    }

    function setIP($id, $ip)
    {
        return DB::DBCode("UPDATE `" . whitelist_dao::$table . "` 
                           SET `ip` = '" . $ip . "', `updater` = '" . $_SESSION["act"] . "',`updater_name` = '" . $_SESSION["name"] . "', `update_date` = '" . date("Y-m-d H:i:s") . "' 
                           WHERE `id` = '" . $id . "'");
    }

    function deleteIP($id)
    {
        return DB::DBCode("UPDATE `" . whitelist_dao::$table . "` 
                           SET `is_del` = 1 ,
                               `updater` = '" . $_SESSION["act"] . "',
                               `updater_name` = '" . $_SESSION["name"] . "',
                               `update_date` = '" . date("Y-m-d H:i:s") . "'
                           WHERE `id` = '" . $id . "'");
    }
}
