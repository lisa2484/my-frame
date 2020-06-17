<?php

namespace app\models;

class web_set_dao
{
    private static $table = "web_set";

    /**
     * 抓取配置各選項資料
     * @return array 回傳table表單資料 MYSQLI_ASSOC
     */
    function getWebSetList(): array
    {
        return DB::select("SELECT * FROM `" . self::$table . "`;");
    }

    /**
     * 抓取指定選項名稱的資料
     * @param string $setKey 指定的選項名稱
     * @return array 回傳table表單資料
     */
    function getWebSetListBySetKey(string $setKey): array
    {
        return DB::select("SELECT * FROM `" . self::$table . "` WHERE `set_key` = '" . $setKey . "' LIMIT 1;");
    }

    /**
     * 新增指定選項名稱的設定
     * @param string $setKey 指定的選項名稱
     * @param string $value 選取到的值
     * @return bool 回傳是否成功
     */
    function setWebSetAdd(string $setKey, string $value): bool
    {
        return DB::DBCode("INSERT INTO `" . self::$table . "` (`set_key`,`value`,`updater`,`update_dt`,`update_ip`) 
                           VALUE ('" . $setKey . "','" . $value . "','" . $_SESSION["act"] . "','" . date("Y-m-d H:i:s") . "','" . getRemoteIP() . "');");
    }

    /**
     * 更新指定選項名稱的設定
     * @param string $setKey 指定的選項名稱
     * @param string $value 選取到的值
     * @return bool 回傳是否成功
     */
    function setWebSetEdit(string $setKey, string $value): bool
    {
        return DB::DBCode("UPDATE `" . self::$table . "` 
                           SET `value` = '" . $value . "',
                               `updater` = '" . $_SESSION["act"] . "',
                               `update_dt` = '" . date("Y-m-d H:i:s") . "',
                               `update_ip` = '" . getRemoteIP() . "'
                           WHERE `set_key` = '" . $setKey . "';");
    }
}
