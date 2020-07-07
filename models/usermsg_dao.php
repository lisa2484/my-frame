<?php

namespace app\models;

class usermsg_dao
{
    private static $table_name = "usermsg";

    function getUserMsg($userid): array
    {
        return DB::select("SELECT `id`, `tag`, `msg`, `sort` FROM " . self::$table_name . " WHERE `user_id` = " . $userid . " AND `is_del` = 0 ORDER BY `sort` ASC ");
    }

    function getSort($sort)
    {
        $sortsount = DB::select("SELECT count(*) FROM " . self::$table_name . " WHERE `sort` = " . $sort . " AND `is_del` = 0 limit 1 ");
        return $sortsount[0]['count(*)'];
    }

    function addUserMsg($userid, string $settag, string $msg, int $sort): bool
    {
        return DB::DBCode("INSERT INTO `" . self::$table_name . "` (`user_id`, `tag`, `msg`, `sort`, `creator`, `create_dt`, `create_ip`) 
                           VALUE ('" . $userid . "','" . $settag . "','" . $msg . "','" . $sort . "','" . $_SESSION["act"] . "','" . date("Y-m-d H:i:s") . "','" . getRemoteIP() . "');");
    }

    function updUserMsg($userid, int $id, string $settag, string $msg, int $sort): bool
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

    function delUserMsg(int $id): bool
    {
        return DB::DBCode("UPDATE `" . self::$table_name . "` SET `is_del` = 1, `updater` = '" . $_SESSION["act"] . "', `update_dt` = '" . date("Y-m-d H:i:s") . "', `update_ip` = '" . getRemoteIP() . "' WHERE `id` = '" . $id . "'");
    }    
}
