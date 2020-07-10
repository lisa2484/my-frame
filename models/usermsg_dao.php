<?php

namespace app\models;

class usermsg_dao
{
    private static $table_name = "usermsg";

    function getUserMsg($userid, string $tag = "", int $limit = 0, int $page = 0): array
    {
        $where[] = "`user_id` = " . $userid;
        $where[] = "`is_del` = 0";
        if ($tag != "") $where[] = "`tag` = '" . $tag . "'";
        if ($limit != 0 && $page != 0) {
            $page = ($page - 1) * $limit;
            $limit = " LIMIT " . $page . "," . $limit . ";";
        } else {
            $limit = "";
        }
        return DB::select("SELECT `id`, `tag`, `msg`, `sort` FROM `" . self::$table_name . "` WHERE " . implode(" AND ", $where) . " ORDER BY `sort` ASC " . $limit);
    }

    function getUserMsgTotal($userid, string $tag): int
    {
        $where[] = "`user_id` = " . $userid;
        $where[] = "`is_del` = 0";
        if ($tag != "") $where[] = "`tag` = '" . $tag . "'";
        $str_sql = "";
        if (!empty($where)) $str_sql = " WHERE " . implode(" AND ", $where);

        $total = DB::select("SELECT count(*) FROM " . self::$table_name . $str_sql);
        return $total[0]['count(*)'];
    }

    function getSort($sort)
    {
        $sortsount = DB::select("SELECT count(*) FROM " . self::$table_name . " WHERE `sort` = " . $sort . " AND `is_del` = 0 limit 1 ");
        return $sortsount[0]['count(*)'];
    }

    function getSortValue($id)
    {
        $sortsount = DB::select("SELECT `sort` FROM " . self::$table_name . " WHERE `id` = " . $id . " AND `is_del` = 0 limit 1 ");
        return $sortsount[0]['sort'];
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

    function delUserMsg(array $id): bool
    {
        return DB::DBCode("UPDATE `" . self::$table_name . "`
                           SET `is_del` = 1,
                               `updater` = '" . $_SESSION["act"] . "',
                               `update_dt` = '" . date("Y-m-d H:i:s") . "',
                               `update_ip` = '" . getRemoteIP() . "'
                           WHERE `id` IN (" . implode(",", $id) . ")");
    }
}
