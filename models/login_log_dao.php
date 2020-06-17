<?php

namespace app\models;

class login_log_dao
{
    private static $table_name = "login_log";

    /**
     * 抓取登入紀錄總筆數
     * @param mixed $str_sql 要查詢的sql語法字串
     * @return int 回傳總筆數
     */
    function getLoginLogTotal($str_sql)
    {
        $total = DB::select("SELECT count(*) FROM " . login_log_dao::$table_name . $str_sql);
        return $total[0]['count(*)'];
    }

    /**
     * 抓取登入紀錄
     * @param mixed $str_sql 要查詢的sql語法字串
     * @param mixed $limit 要查詢的起始值
     * @param mixed $offset 要查詢的筆數
     * @return array 回傳table表單資料
     */
    function getLoginLog($str_sql, $limit, $offset)
    {
        return DB::select("SELECT `id`, `account`, `ip`, `user_name`, `authority_name`, `login_date` FROM " . login_log_dao::$table_name . $str_sql . " ORDER BY `login_date` DESC LIMIT " . $limit . " OFFSET " . $offset);
    }
}