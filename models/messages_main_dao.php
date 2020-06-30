<?php

namespace app\models;

class messages_main_dao
{
    private static $table_name = "messages_main";

    function getMessagesMainTotal(string $member, string $name, string $device, string $ip, string $adminname, string $date_s, string $date_e): int
    {
        $where = [];
        if ($member != "") $where[] = "`member_id` = '" . $member . "'";
        if ($name != "") $where[] = "`member_name` = '" . $name . "'";
        if ($device != "") $where[] = "`member_env` = '" . $device . "'";
        if ($ip != "") $where[] = "`member_ip` = '" . $ip . "'";
        if ($adminname != "") $where[] = "`user_id` = '" . $adminname . "'";
        if (!empty($date_s) && !empty($date_e)) $where[] = "`start_time` BETWEEN '" . $date_s . "' AND '" . $date_e . "'";

        $str_sql = "";
        if (!empty($where)) $str_sql = "WHERE " . implode(" AND ", $where);

        $total = DB::select("SELECT count(*) AS c FROM `" . self::$table_name . "` " . $str_sql . ";");

        return $total[0]['c'];
    }

    function getMessagesMain(string $member, string $name, string $device, string $ip, string $adminname, string $date_s, string $date_e, int $page, int $limit): array
    {
        $where = [];
        if ($member != "") $where[] = "`member_id` = '" . $member . "'";
        if ($name != "") $where[] = "`member_name` = '" . $name . "'";
        if ($device != "") $where[] = "`member_env` = '" . $device . "'";
        if ($ip != "") $where[] = "`member_ip` = '" . $ip . "'";
        if ($adminname != "") $where[] = "`user_id` = '" . $adminname . "'";
        if (!empty($date_s) && !empty($date_e)) $where[] = "`start_time` BETWEEN '" . $date_s . "' AND '" . $date_e . "'";

        $str_sql = "";
        if (!empty($where)) $str_sql = "WHERE " . implode(" AND ", $where);
    
        $page = ($page - 1) * $limit;

        return DB::select("SELECT * FROM `" . self::$table_name . "` " . $str_sql . " ORDER BY `id` DESC LIMIT " . $page . "," . $limit . ";");
    }
}