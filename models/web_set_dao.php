<?php

namespace app\models;

class web_set_dao
{
    private static $table = "web_set";

    /**
     * 抓取配置各選項資料
     * @return array 回傳table表單資料
     */
    function getWebSetList()
    {
        return DB::select("SELECT * FROM `" . web_set_dao::$table . "`;");
    }

    /**
     * 抓取指定選項名稱的資料
     * @param mixed $setKey 指定的選項名稱
     * @return array 回傳table表單資料
     */
    function getWebSetListBySetKey($setKey)
    {
        return DB::select("SELECT * FROM `" . web_set_dao::$table . "` WHERE `set_key` = '" . $setKey . "' LIMIT 1;");
    }

    /**
     * 新增指定選項名稱的設定
     * @param mixed $setKey 指定的選項名稱
     * @param mixed $value 選取到的值
     * @return bool 回傳是否成功
     */
    function setWebSetAdd($setKey, $value)
    {
        return DB::DBCode("INSERT INTO `" . web_set_dao::$table . "` (`set_key`,`value`) VALUE ('" . $setKey . "','" . $value . "');");
    }

    /**
     * 更新指定選項名稱的設定
     * @param mixed $setKey 指定的選項名稱
     * @param mixed $value 選取到的值
     * @return bool 回傳是否成功
     */
    function setWebSetEdit($setKey, $value)
    {
        return DB::DBCode("UPDATE `" . web_set_dao::$table . "` SET `value` = '" . $value . "' WHERE `set_key` = '" . $setKey . "';");
    }
}
