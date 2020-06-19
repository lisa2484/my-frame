<?php

namespace app\models;

class autoservicerep_dao
{
    private static $table = "autoservicerep";

    function getWebData(): array
    {
        return DB::select("SELECT `id`,`parent_id`,`title`,`msg`,`onf`,`sort`
                           FROM `" . self::$table . "`
                           WHERE `is_del` = 0");
    }

    function getResponseForParentId(int $parentId)
    {
        return DB::select("SELECT `id`,`title`,`msg` 
                           FROM `" . self::$table . "` 
                           WHERE `is_del` = 0
                           AND `onf` = 1 
                           AND `parent_id` = '" . $parentId . "';");
    }

    function setMsgInsert(array $insertArr): bool
    {
        if (empty($insertArr)) return false;
        return DB::DBCode("INSERT INTO `" . self::$table . "` (`" . implode("`,`", array_keys($insertArr)) . "`)
                           VALUE ('" . implode("','", array_values($insertArr)) . "');");
    }

    function setMsgUpdate(int $id, array $updateArr): bool
    {
        if (empty($updateArr)) return false;
        $whereArr = [];
        foreach ($updateArr as $k => $d) {
            $whereArr[] = "`" . $k . "` = '" . $d . "'";
        }
        return DB::DBCode("UPDATE `" . self::$table . "` 
                           SET " . implode(",", $whereArr) . "
                           WHERE `id` = '" . $id . "' AND `is_del` = 0;");
    }
}
