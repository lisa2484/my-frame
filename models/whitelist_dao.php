<?php

namespace app\models;

use app\models\DB;

class whitelist_dao
{
    private static $table = "whitelist";

    /**
     * 抓取IP白名單列表
     * @param mixed $page 當前頁碼
     * @param mixed $limit 要抓取的筆數
     * @return array 回傳table表單資料
     */
    function getList($page, $limit)
    {
        return DB::select("SELECT * FROM `" . whitelist_dao::$table . "` WHERE `is_del` = 0 LIMIT " . ($page - 1) * $limit . "," . $limit . ";");
    }

    /**
     * 抓取指定IP的資料
     * @param mixed $ip 指定的IP
     * @return array 回傳table表單資料
     */
    function getIP($ip)
    {
        return DB::select("SELECT * FROM `" . whitelist_dao::$table . "` WHERE `ip` = '" . $ip . "' AND `is_del` = 0 LIMIT 1");
    }

    /**
     * 新增IP
     * @param mixed $ip 要新增的IP
     * @return bool 回傳是否成功
     */
    function insertIP($ip)
    {
        return DB::DBCode("INSERT INTO `" . whitelist_dao::$table . "` (`ip`,`creator`,`creator_name`,`creation_date`,`updater`,`updater_name`,`update_date`) 
                           VALUE ('" . $ip . "','" . $_SESSION["act"] . "','" . $_SESSION["name"] . "','" . date("Y-m-d H:i:s") . "','" . $_SESSION["act"] . "','" . $_SESSION["name"] . "','" . date("Y-m-d H:i:s") . "')");
    }

    /**
     * 更新指定的IP
     * @param mixed $id 指定的IP編號
     * @param mixed $ip 更新的IP
     * @return bool 回傳是否成功
     */
    function setIP($id, $ip)
    {
        return DB::DBCode("UPDATE `" . whitelist_dao::$table . "` 
                           SET `ip` = '" . $ip . "', `updater` = '" . $_SESSION["act"] . "',`updater_name` = '" . $_SESSION["name"] . "', `update_date` = '" . date("Y-m-d H:i:s") . "' 
                           WHERE `id` = '" . $id . "'");
    }

    /**
     * 刪除指定的IP
     * @param mixed $id 指定的IP編號
     * @return bool 回傳是否成功
     */
    function deleteIP($id)
    {
        return DB::DBCode("UPDATE `" . whitelist_dao::$table . "` 
                           SET `is_del` = 1 ,
                               `updater` = '" . $_SESSION["act"] . "',
                               `updater_name` = '" . $_SESSION["name"] . "',
                               `update_date` = '" . date("Y-m-d H:i:s") . "'
                           WHERE `id` = '" . $id . "'");
    }
}
