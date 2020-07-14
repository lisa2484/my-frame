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

    function getMsgMainForNotLimit(array $where): array
    {
        if (empty($where)) return DB::select("SELECT * FROM `" . self::$table_name . "`");
        $arr = [];
        foreach ($where as $k => $d) {
            switch ($k) {
                case "date":
                    $arr[] = "`start_time` BETWEEN '" . $d . " 00:00:00' AND '" . $d . " 23:59:59'";
                    break;
                default:
                    $arr[] = "`" . $k . "` = '" . $d . "'";
            }
        }
        return DB::select("SELECT * FROM `" . self::$table_name . "` WHERE " . implode(" AND ", $arr));
    }

    function getMsgByID(int $id): array
    {
        return DB::select("SELECT * FROM `" . self::$table_name . "` WHERE `id` = '" . $id . "' LIMIT 1;");
    }

    function getMsgForNotOverByAcount()
    {
        return DB::select("SELECT `id`,`status`,`circle_count`,`user_id`
                           FROM `" . self::$table_name . "`
                           WHERE `user_id` = '" . $_SESSION["act"] . "' AND `status` IN (0,1);");
    }

    function setMsgMainForChatroom(array $insert, int &$id = 0): bool
    {
        $success = DB::DBCode("INSERT INTO `" . self::$table_name . "` (`" . implode("`,`", array_keys($insert)) . "`)
                               VALUE ('" . implode("','", array_values($insert)) . "')");
        if ($success) $id = mysqli_insert_id(DB::getDBCon());
        return $success;
    }

    function setMsgUpdate(int $id, array $update)
    {
        $upstr = [];
        foreach ($update as $k => $d) {
            switch ($k) {
                case 'circle_count':
                    break;
                case 'rep_len':
                    $upstr[] = "`" . $k . "` = IF(`" . $k . "` = 0," . $d . ",`" . $k . "`)";
                    break;
                default:
                    $upstr[] = "`" . $k . "` = '" . $d . "'";
            }
        }
        return DB::DBCode("UPDATE `" . self::$table_name . "` SET " . implode(",", $upstr) . " WHERE `id` = '" . $id . "';");
    }

    function setMsgStatusOver(int $id): bool
    {
        return DB::DBCode("UPDATE `" . self::$table_name . "` 
                           SET `status` = 2 
                           WHERE `id` = '" . $id . "' 
                           AND `status` IN (0,1);");
    }
}
