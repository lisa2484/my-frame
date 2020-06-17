<?php

namespace app\models;

use app\models\DB;

class user_dao
{
    private static $table_name = "bg_user";

    /**
     * 抓取使用者資料
     * @return array 回傳table表單資料
     */
    function getUser()
    {
        return DB::select("SELECT `id`,`user_name`,`authority`,`account`,`create_dt` FROM " . user_dao::$table_name);
    }

    /**
     * 搜尋要查詢的使用者資料
     * @param mixed $act 使用者帳號
     * @return array 回傳table表單資料
     */
    function selectUser($act)
    {
        return DB::select("SELECT * FROM " . user_dao::$table_name . " WHERE account = '" . $act . "'");
    }

    /**
     * 寫入使用者資料
     * @param mixed $act 使用者帳號
     * @param mixed $pad 使用者密碼
     * @param mixed $name 使用者名稱
     * @param mixed $aut 使用者權限
     * @param mixed $time 建立時間
     * @return bool 回傳是否成功
     */
    function insertUser($act, $pad, $name, $aut, $time)
    {
        return DB::DBCode("INSERT INTO " . user_dao::$table_name . " (account,password,user_name,authority,create_dt) VALUES ('" . $act . "','" . $pad . "','" . $name . "','" . $aut . "','" . date("Y-m-d H:i:s", $time) . "')");
    }

    /**
     * 更新使用者資料
     * @param mixed $id 使用者編號
     * @param mixed $name 使用者名稱
     * @param mixed $aut 使用者權限
     * @return bool 回傳是否成功
     */
    function updateUserForEdit($id, $name, $aut)
    {
        return DB::DBCode("UPDATE " . user_dao::$table_name . " SET user_name = '" . $name . "',authority = '" . $aut . "' WHERE id = '" . $id . "'");
    }

    /**
     * 抓取指定使用者的資料
     * @param mixed $id
     * @return array 回傳table表單資料
     */
    function selectUserByID($id)
    {
        return DB::select("SELECT * FROM " . user_dao::$table_name . " WHERE id = '" . $id . "'");
    }

    /**
     * 更新使用者密碼
     * @param mixed $id 使用者編號
     * @param mixed $pad 使用者密碼
     * @return bool 回傳是否成功
     */
    function updateUserForPad($id, $pad)
    {
        return DB::DBCode("UPDATE " . user_dao::$table_name . " SET password ='" . $pad . "' WHERE id ='" . $id . "'");
    }

    /**
     * 刪除使用者資料
     * @param mixed $id 使用者編號
     * @return bool 回傳是否成功
     */
    function setDelete($id)
    {
        return DB::DBCode("UPDATE `" . user_dao::$table_name . "` SET `is_del` = 1 WHERE `id` = '" . $id . "';");
    }
}
