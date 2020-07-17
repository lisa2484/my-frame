<?php

namespace app\models;

class messages_main_dao
{
    private static $table_name = "messages_main";
    private static $user_table = "user";

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

    function getMsgDataForChatroom(int $id): array
    {
        return DB::select("SELECT `m`.`status`,`m`.`member_id`,`m`.`member_name`,`m`.`member_env`,`m`.`member_ip`,`m`.`member_loc`,`m`.`member_from`,`m`.`user_id`,`u`.`account`,`u`.`user_name` 
                           FROM `" . self::$table_name . "` AS `m`
                           LEFT JOIN `" . self::$user_table . "` AS `u`
                           ON `m`.`user_id` = `u`.`account`
                           AND `u`.`is_del` = 0
                           WHERE `m`.`id` = '" . $id . "'");
    }

    function getMsgForNotOverByAcount()
    {
        return DB::select("SELECT `id`,`member_id`,`member_name`,`status`,`circle_count`,`user_id`
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

    function setMsgLocal(int $id, string $loc)
    {
        return DB::DBCode("UPDATE `" . self::$table_name . "` SET `member_loc` = '" . $loc . "' WHERE `id` = '" . $id . "' AND `member_loc` = ''");
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

    /**
     * 儀錶板抓取 在線/離線 人數
     * @param int $status 要查詢的狀態值 (0:等待對話 1:處理中 2:處理完畢)
     * @param int $today_s 當天0時
     * @param int $today_e 現在時間
     * @return int 回傳數量
     */
    function getMsgStatus(int $status, int $today_s, int $today_e)
    {
        $count = DB::select("SELECT count(*) FROM `" . self::$table_name . "` WHERE `status` = '" . $status . "' AND `start_time` >= " . $today_s . " AND `end_time` <= " . $today_e);
        return $count[0]['count(*)'];
    }

    /**
     * 儀錶板抓取回合數、評價
     * @param int $today_s 當天0時
     * @param int $today_e 現在時間
     * @return array 總數、評價、訊息數量 MYSQLI_ASSOC
     */
    function getMsgInfo(int $today_s, int $today_e)
    {
        return DB::select("SELECT count(*) as c, SUM(`evaluation`) as s, SUM(`circle_count`) as r FROM `" . self::$table_name . "` WHERE (`status` = 1 OR `status` = 2) AND `evaluation` != 0 AND `circle_count` != 0 AND `start_time` >= " . $today_s . " AND `end_time` <= " . $today_e);
    }

    /**
     * 儀錶板抓取 在線/離線 人數
     * @return array 回傳狀態、數量 MYSQLI_ASSOC
     */
    function getMsgLength(int $today_s, int $today_e)
    {
        return DB::select("SELECT count(*) as c, SUM(`rep_len` - `start_time`) as fl, SUM(`end_time` - `start_time`) as ml FROM `" . self::$table_name . "` WHERE (`status` = 1 OR `status` = 2) AND `rep_len` != 0 AND `start_time` >= " . $today_s . " AND `end_time` <= " . $today_e);
    }

    /**
     * 儀錶板抓取 在線/離線 人數
     * @return array 回傳狀態、數量 MYSQLI_ASSOC
     */
    function getMsgChart(string $field, int $today_s, int $today_e)
    {
        return DB::select("SELECT `" . $field . "` as 'name', COUNT(*) as 'count' FROM `messages_main` WHERE (`status` = 1 OR `status` = 2) AND `start_time` >= " . $today_s . " AND `end_time` <= " . $today_e . " GROUP BY `" . $field . "`");
    }
}
