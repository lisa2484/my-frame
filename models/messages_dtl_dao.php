<?php

namespace app\models;

class messages_dtl_dao
{
    private static $table = "messages_dtl";

    function getMsgByNewMessage(int $mid, int $id)
    {
        return DB::select("SELECT * 
                           FROM `" . self::$table . "`
                           WHERE `main_id` = '" . $mid . "'
                           AND `id` > '" . $id . "'
                           ORDER BY `id` ASC");
    }

    function setMsgInsert(array $insertArr, int &$id = 0)
    {
        $success = DB::DBCode("INSERT INTO `" . self::$table . "` (`" . implode("`,`", array_keys($insertArr)) . "`)
                           VALUE ('" . implode("','", array_values($insertArr)) . "')");
        if ($success) $id = mysqli_insert_id(DB::getDBCon());
        return $success;
    }
}
