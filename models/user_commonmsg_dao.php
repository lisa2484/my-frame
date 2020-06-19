<?php

namespace app\models;

class user_commonmsg_dao
{
    private static $table_name = "usermsg";

    function getCommonMsg($userid): array
    {
        return DB::select("SELECT `id`, `tag`, `msg`, `sort` FROM " . self::$table_name . " WHERE `user_id` = " . $userid . " AND `is_del` = 0 ");
    }

    function addCommonMsg($userid, string $settag, string $msg, int $sort): bool
    {
        return DB::DBCode("INSERT INTO `" . self::$table_name . "` (`user_id`, `tag`, `msg`, `sort`, `creator`, `create_dt`, `create_ip`) 
                           VALUE ('" . $userid . "','" . $settag . "','" . $msg . "','" . $sort . "','" . $_SESSION["act"] . "','" . date("Y-m-d H:i:s") . "','" . getRemoteIP() . "');");
    }

    function updCommonMsg($userid, int $id, string $settag, string $msg, int $sort): bool
    {
        return DB::DBCode("UPDATE `" . self::$table_name . "` 
                           SET `user_id` = '" . $userid . "',
                               `tag` = '" . $settag . "',
                               `msg` = '" . $msg . "',
                               `sort` = '" . $sort . "',
                               `updater` = '" . $_SESSION["act"] . "',
                               `update_dt` = '" . date("Y-m-d H:i:s") . "',
                               `update_ip` = '" . getRemoteIP() . "'
                           WHERE `id` = '" . $id . "';");
    }

    function delCommonMsg(int $id): bool
    {
        return DB::DBCode("UPDATE `" . self::$table_name . "` SET `is_del` = 1, `updater` = '" . $_SESSION["act"] . "', `update_dt` = '" . date("Y-m-d H:i:s") . "', `update_ip` = '" . getRemoteIP() . "' WHERE `id` = '" . $id . "'");
    }    
}
