<?php

namespace app\models;

use app\models\DB;

class whitelist_dao
{
    private static $table = "ipwhitelist";

    /**
     * 抓取IP白名單列表
     * @param int $page 當前頁碼
     * @param int $limit 要抓取的筆數
     * @return array 回傳table表單資料 MYSQLI_ASSOC
     */
    function getList(string $ip, int $page, int $limit): array
    {
        $where[] = "`is_del` = 0";
        if ($ip != "") $where[] = "`ip` = '" . $ip . "'";
        $str_sql = "";
        if (!empty($where)) $str_sql = "WHERE " . implode(" AND ", $where);

        return DB::select("SELECT `id`, `ip`, `creator`, `updater` FROM `" . self::$table . "` " . $str_sql . " LIMIT " . ($page - 1) * $limit . "," . $limit . ";");
    }

    function getTotalList(string $ip): int
    {
        $ipsql = "";
        if ($ip != "") $ipsql = "AND `ip` = '" . $ip . "'";

        $total = DB::select("SELECT count(*) FROM `" . self::$table . "` WHERE `is_del` = 0 " . $ipsql);
        return $total[0]['count(*)'];
    }

    /**
     * 抓取指定IP的資料
     * @param string $ip 指定的IP
     * @return array 回傳table表單單筆資料 MYSQLI_ASSOC
     */
    function getIP(string $ip): array
    {
        return DB::select("SELECT * FROM `" . self::$table . "` WHERE `ip` = '" . $ip . "' AND `is_del` = 0 LIMIT 1;");
    }

    /**
     * 新增IP
     * @param string $ip 要新增的IP
     * @return bool 回傳是否成功
     */
    function insertIP(string $ip): bool
    {
        return DB::DBCode("INSERT INTO `" . self::$table . "` (`ip`,`creator`,`create_dt`,`create_ip`,`updater`,`update_dt`,`update_ip`) 
                           VALUE ('" . $ip . "',
                                  '" . $_SESSION["act"] . "',
                                  '" . date("Y-m-d H:i:s") . "',
                                  '" . getRemoteIP() . "',
                                  '" . $_SESSION["act"] . "',
                                  '" . date("Y-m-d H:i:s") . "',
                                  '" . getRemoteIP() . "');");
    }

    /**
     * 更新指定的IP
     * @param int $id 指定的IP編號
     * @param string $ip 更新的IP
     * @return bool 回傳是否成功
     */
    function setIP(int $id, string $ip): bool
    {
        return DB::DBCode("UPDATE `" . self::$table . "` 
                           SET `ip` = '" . $ip . "',
                               `updater` = '" . $_SESSION["act"] . "',
                               `update_dt` = '" . date("Y-m-d H:i:s") . "',
                               `update_ip` = '" . getRemoteIP() . "'
                           WHERE `id` = '" . $id . "';");
    }

    /**
     * 開關指定的IP
     * @param int $id 指定的IP編號
     * @param int $switch 開關
     * @return bool 回傳是否成功
     */
    function setOnf(int $id, int $switch): bool
    {
        return DB::DBCode("UPDATE `" . self::$table . "`
                           SET `onf` = '" . $switch . "',
                               `updater` = '" . $_SESSION["act"] . "',
                               `update_dt` = '" . date("Y-m-d H:i:s") . "',
                               `update_ip` = '" . getRemoteIP() . "'
                           WHERE `id` = '" . $id . "';");
    }

    function setDeleteList(array $ids): bool
    {
        return DB::DBCode("UPDATE `" . self::$table . "` 
                           SET `is_del` = 1 ,
                               `updater` = '" . $_SESSION["act"] . "',
                               `update_dt` = '" . date("Y-m-d H:i:s") . "',
                               `update_ip` = '" . getRemoteIP() . "'
                           WHERE `id` IN (" . implode(",", $ids) . ")");
    }
}
