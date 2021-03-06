<?php

namespace app\models;

use app\models\DB;

class user_dao
{
    private static $table_name = "user";

    /**
     * 抓取使用者資料
     * @return array 回傳table表單資料 MYSQLI_ASSOC
     */
    function getUser(): array
    {
        return DB::select("SELECT `id`,`user_name`,`authority`,`account`,`create_dt` FROM `" . self::$table_name . "` WHERE `is_del` = 0;");
    }

    /**
     * 搜尋要查詢的使用者資料
     * @param string $act 使用者帳號
     * @return array 回傳table表單資料
     */
    function selectUser(string $act): array
    {
        return DB::select("SELECT * FROM `" . self::$table_name . "` WHERE `account` = '" . $act . "' AND `is_del` = 0;");
    }

    /**
     * 寫入使用者資料
     * @param string $act 使用者帳號
     * @param string $pad 使用者密碼
     * @param string $name 使用者名稱
     * @param int $aut 使用者權限
     * @param int $time 建立時間
     * @return bool 回傳是否成功
     */
    function insertUser(string $act, string $pad, string $name, int $aut, int $time): bool
    {
        return DB::DBCode("INSERT INTO `" . self::$table_name . "` (`account`,`password`,`user_name`,`authority`,`creator`,`create_dt`,`create_ip`,`updater`,`update_dt`,`update_ip`,`chg_pw_time`) 
                           VALUE ('" . $act . "',
                                  '" . $pad . "',
                                  '" . $name . "',
                                  '" . $aut . "',
                                  '" . $_SESSION["act"] . "',
                                  '" . date("Y-m-d H:i:s", $time) . "',
                                  '" . getRemoteIP() . "',
                                  '" . $_SESSION["act"] . "',
                                  '" . date("Y-m-d H:i:s", $time) . "',
                                  '" . getRemoteIP() . "',
                                  '" . $time . "')");
    }

    /**
     * 更新客服暱稱
     * @param int $id id
     * @param string $name 暱稱
     * @return bool 回傳是否成功
     */
    function setUserName(int $id, string $name): bool
    {
        return DB::DBCode("UPDATE `" . self::$table_name . "`
                           SET `user_name` = '" . $name . "',
                               `updater` = '" . $_SESSION["act"] . "',
                               `update_dt` = '" . date("Y-m-d H:i:s") . "',
                               `update_ip` = '" . getRemoteIP() . "'
                           WHERE `id` = '" . $id . "' AND `is_del` = 0;");
    }

    /**
     * 抓取指定使用者的資料
     * @param int $id
     * @return array 回傳table表單資料 MYSQLI_ASSOC
     */
    function selectUserByID(int $id): array
    {
        return DB::select("SELECT * FROM `" . self::$table_name . "` WHERE `id` = '" . $id . "' AND `is_del` = 0;");
    }

    /**
     * 更新使用者密碼
     * @param int $id 使用者編號
     * @param string $pad 使用者密碼
     * @return bool 回傳是否成功
     */
    function updateUserForPad(int $id, string $pad): bool
    {
        return DB::DBCode("UPDATE `" . self::$table_name . "` 
                           SET `password` = '" . $pad . "',
                               `updater` = '" . $_SESSION["act"] . "',
                               `update_dt` = '" . date("Y-m-d H:i:s") . "',
                               `update_ip` = '" . getRemoteIP() . "',
                               `chg_pw_time` = '" . time() . "'
                           WHERE `id` = '" . $id . "'");
    }

    /**
     * 刪除使用者資料
     * @param int $id 使用者編號
     * @return bool 回傳是否成功
     */
    function setDelete(int $id): bool
    {
        return DB::DBCode("UPDATE `" . self::$table_name . "` 
                           SET `is_del` = 1 ,
                               `updater` = '" . $_SESSION["act"] . "',
                               `update_dt` = '" . date("Y-m-d H:i:s") . "',
                               `update_ip` = '" . getRemoteIP() . "'
                           WHERE `id` = '" . $id . "';");
    }
}
