<?php

namespace app\models;

class searchautorep_dao
{
    private static $table_name = "searchautorep";

    /**
     * 抓取關鍵字訊息總筆數
     */
    function getSearchAutoRepTotal(string $keyword): int
    {
        if ($keyword == "") return DB::select("SELECT count(*) FROM `" . self::$table_name . "` WHERE `is_del` = 0 ;")[0]['count(*)'];
        return DB::select("SELECT count(*) FROM `" . self::$table_name . "` WHERE `is_del` = 0 AND `msg` LIKE '%$keyword%';")[0]['count(*)'];
    }

    /**
     * 抓取關鍵字訊息紀錄
     */
    function getSearchAutoRep(string $keyword, $page, $limit): array
    {
        $offset = $limit * ($page - 1);
        if ($keyword == "") return DB::select("SELECT `id`, `msg`, `onf` FROM `" . self::$table_name . "` WHERE `is_del` = 0 ORDER BY `create_dt` DESC LIMIT " . $limit . " OFFSET " . $offset);
        return DB::select("SELECT `id`, `msg`, `onf` FROM `" . self::$table_name . "` WHERE `is_del` = 0 AND `msg` LIKE '%$keyword%' ORDER BY `create_dt` DESC LIMIT " . $limit . " OFFSET " . $offset);
    }

    function addSearchAutoRep(string $msg): bool
    {
        return DB::DBCode("INSERT INTO `" . self::$table_name . "` (`msg`, `onf`, `creator`, `create_dt`, `create_ip`) 
                           VALUE ('" . $msg . "', 1,'" . $_SESSION["act"] . "','" . date("Y-m-d H:i:s") . "','" . getRemoteIP() . "');");
    }

    function updSearchAutoRep(int $id, string $msg, int $status): bool
    {
        return DB::DBCode("UPDATE `" . self::$table_name . "` 
                           SET `msg` = '" . $msg . "',
                               `onf` = '" . $status . "',
                               `updater` = '" . $_SESSION["act"] . "',
                               `update_dt` = '" . date("Y-m-d H:i:s") . "',
                               `update_ip` = '" . getRemoteIP() . "'
                           WHERE `id` = '" . $id . "';");
    }

    function delSearchAutoRep(int $id): bool
    {
        return DB::DBCode("UPDATE `" . self::$table_name . "` SET `is_del` = 1, `updater` = '" . $_SESSION["act"] . "', `update_dt` = '" . date("Y-m-d H:i:s") . "', `update_ip` = '" . getRemoteIP() . "' WHERE `id` = '" . $id . "'");
    }

    function deleteList(array $ids): bool
    {
        return DB::DBCode("UPDATE `" . self::$table_name . "` 
                           SET `is_del` = 1,
                               `updater` = '" . $_SESSION["act"] . "',
                               `update_dt` = '" . date("Y-m-d H:i:s") . "',
                               `update_ip` = '" . getRemoteIP() . "'
                           WHERE `id` IN (" . implode(",", $ids) . ")");
    }
}
