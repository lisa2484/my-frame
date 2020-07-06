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
}
