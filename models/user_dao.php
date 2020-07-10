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
    function getUser(string $account, int $limit, int $page): array
    {
        $where[] = "`is_del` = 0";
        if ($account != "") $where[] = "`account` = '" . $account . "'";
        $str_sql = "";
        if (!empty($where)) $str_sql = " WHERE " . implode(" AND ", $where);
        $page = ($page - 1) * $limit;

        return DB::select("SELECT `id`,`user_name`,`authority`,`account`,`create_dt` FROM `" . self::$table_name . "`" . $str_sql . " ORDER BY `id` DESC LIMIT " . $page . "," . $limit . ";");
    }

    /**
     * 抓取使用者所有資料
     * @return array 回傳table表單資料 MYSQLI_ASSOC
     */
    function getUserTotal(string $account): int
    {
        $where[] = "`is_del` = 0";
        if ($account != "") $where[] = "`account` = '" . $account . "'";
        $str_sql = "";
        if (!empty($where)) $str_sql = " WHERE " . implode(" AND ", $where);

        $total = DB::select("SELECT count(*) FROM " . self::$table_name . $str_sql);
        return $total[0]['count(*)'];
    }

    /**
     * 搜尋要查詢的使用者資料
     * @param string $act 使用者帳號
     * @return array 回傳table表單資料
     */
    function getUserByAccount(string $act): array
    {
        return DB::select("SELECT * FROM `" . self::$table_name . "` WHERE `account` = '" . $act . "' AND `is_del` = 0 LIMIT 1;");
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
    function insertUser(string $act, string $pad, int $aut, int $time): bool
    {
        return DB::DBCode("INSERT INTO `" . self::$table_name . "` (`account`,`password`,`authority`,`creator`,`create_dt`,`create_ip`,`updater`,`update_dt`,`update_ip`,`chg_pw_time`) 
                           VALUE ('" . $act . "',
                                  '" . $pad . "',
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

    function setUserSetting(int $id, ?int $aut, ?string $pad): bool
    {
        if (!isset($aut) && !isset($pad)) return false;
        $arr = [];
        if (isset($aut)) $arr[] = "`authority` = '" . $aut . "'";
        if (isset($pad)) {
            $arr[] = "`password` = '" . $pad . "'";
            $arr[] = "`chg_pw_time` = '" . time() . "'";
        }
        $arr[] = "`updater` = '" . $_SESSION["act"] . "'";
        $arr[] = "`update_dt` = '" . date("Y-m-d H:i:s") . "'";
        $arr[] = "`update_ip` = '" . getRemoteIP() . "'";
        $setstr = "UPDATE `" . self::$table_name . "`
                   SET " . implode(",", $arr) . " 
                   WHERE `id` = '" . $id . "' AND `is_del` = 0;";
        return DB::DBCode($setstr);
    }

    /**
     * 抓取指定使用者的資料
     * @param int $id
     * @return array 回傳table表單資料 MYSQLI_ASSOC
     */
    function getUserByID(int $id): array
    {
        return DB::select("SELECT * FROM `" . self::$table_name . "` WHERE `id` = '" . $id . "' AND `is_del` = 0 LIMIT 1;");
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

    /**
     * 抓取使用者帳號
     * @return array 回傳table表單資料 MYSQLI_ASSOC
     */
    function getUserAcc(): array
    {
        return DB::select("SELECT `id`,`account` FROM `" . self::$table_name . "` WHERE `is_del` = 0;");
    }

    function updUserPhoto(int $id, string $imgname): bool
    {
        return DB::DBCode("UPDATE `" . self::$table_name . "` SET `img_name` = '" . $imgname . "', `updater` = '" . $_SESSION["act"] . "', `update_dt` = '" . date("Y-m-d H:i:s") . "', `update_ip` = '" . getRemoteIP() . "' WHERE `id` = '" . $id . "'");
    }
}
