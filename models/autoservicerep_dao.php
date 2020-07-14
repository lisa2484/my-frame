<?php

namespace app\models;

class autoservicerep_dao
{
    private static $table = "autoservicerep";

    function getList(): array
    {
        return DB::select("SELECT `id`,`parent_id`,`msg`,`onf`,`sort`
                           FROM `" . self::$table . "`
                           WHERE `is_del` = 0 
                           ORDER BY `parent_id` ASC,`sort` ASC;");
    }

    function getListForOnf(): array
    {
        return DB::select("SELECT `id`,`parent_id`,`msg`,`onf`,`sort`
                           FROM `" . self::$table . "`
                           WHERE `onf` = 1
                           AND `is_del` = 0
                           ORDER BY `parent_id` ASC,`sort` ASC;");
    }

    function getResponseForParentId(int $parentId): array
    {
        return DB::select("SELECT `id`,`msg` 
                           FROM `" . self::$table . "` 
                           WHERE `is_del` = 0
                           AND `onf` = 1 
                           AND `parent_id` = '" . $parentId . "';");
    }

    function getResponseForLink(string $say): array
    {
        return DB::select("SELECT `id`
                           FROM `" . self::$table . "`
                           WHERE `is_del` = 0
                           AND `onf` = 1
                           AND `msg` LIKE '%" . $say . "%'");
    }

    function getSortRepeat(int $sort): bool
    {
        return empty(DB::select("SELECT `id` FROM `" . self::$table . "` WHERE `sort` = '" . $sort . "' AND `is_del` = 0 LIMIT 1"));
    }

    function setOnf(array $ids): bool
    {
        return DB::DBCode("UPDATE `" . self::$table . "` SET `onf` = IF(`id` IN (" . implode(",", $ids) . "),1,0) WHERE `is_del` = 0;");
    }

    function setMsgInsert(array $insertArr): bool
    {
        if (empty($insertArr)) return false;
        return DB::DBCode("INSERT INTO `" . self::$table . "` (`" . implode("`,`", array_keys($insertArr)) . "`,`creator`,`create_dt`,`create_ip`,`updater`,`update_dt`,`update_ip`)
                           VALUE ('" . implode("','", array_values($insertArr)) . "','" . $_SESSION["act"] . "','" . date("Y-m-d H:i:s") . "','" . getRemoteIP() . "','" . $_SESSION["act"] . "','" . date("Y-m-d H:i:s") . "','" . getRemoteIP() . "');");
    }

    function setMsgUpdate(int $id, array $updateArr): bool
    {
        if (empty($updateArr)) return false;
        $whereArr = [];
        foreach ($updateArr as $k => $d) {
            $whereArr[] = "`" . $k . "` = '" . $d . "'";
        }
        return DB::DBCode("UPDATE `" . self::$table . "` 
                           SET " . implode(",", $whereArr) . ",
                                `updater` = '" . $_SESSION["act"] . "',
                                `update_dt` = '" . date("Y-m-d H:i:s") . "',
                                `update_ip` = '" . getRemoteIP() . "'
                           WHERE `id` = '" . $id . "' AND `is_del` = 0;");
    }

    function setMsgDelete(array $ids): bool
    {
        return DB::DBCode("UPDATE `" . self::$table . "`
                           SET `is_del` = 1,
                               `updater` = '" . $_SESSION["act"] . "',
                               `update_dt` = '" . date("Y-m-d H:i:s") . "',
                               `update_ip` = '" . getRemoteIP() . "'
                           WHERE `id` IN (" . implode(",", $ids) . ");");
    }
}
