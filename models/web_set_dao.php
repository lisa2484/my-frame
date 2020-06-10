<?php

namespace app\models;

class web_set_dao
{
    private static $table = "web_set";

    function getWebSetList()
    {
        return DB::select("SELECT * FROM `" . web_set_dao::$table . "`;");
    }

    function getWebSetListBySetKey($setKey)
    {
        return DB::select("SELECT * FROM `" . web_set_dao::$table . "` WHERE `set_key` = '" . $setKey . "' LIMIT 1;");
    }

    function setWebSetAdd($setKey, $value)
    {
        return DB::DBCode("INSERT INTO `" . web_set_dao::$table . "` (`set_key`,`value`) VALUE ('" . $setKey . "','" . $value . "');");
    }

    function setWebSetEdit($setKey, $value)
    {
        return DB::DBCode("UPDATE `" . web_set_dao::$table . "` SET `value` = '" . $value . "' WHERE `set_key` = '" . $setKey . "';");
    }
}
