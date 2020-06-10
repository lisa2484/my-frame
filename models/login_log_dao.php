<?php

namespace app\models;

class login_log_dao
{
    private static $table_name = "login_log";

    /**
     * 抓取登入紀錄總筆數
     */
    function getLoginLogTotal($str_sql)
    {
        return DB::select("SELECT count(*) FROM " . login_log_dao::$table_name . $str_sql);
    }

    /**
     * 抓取登入紀錄
     */
    function getLoginLog($str_sql, $limit, $offset)
    {
        return DB::select("SELECT `id`, `account`, `ip`, `user_name`, `authority_name`, `login_date` FROM " . login_log_dao::$table_name . $str_sql . " ORDER BY `login_date` DESC LIMIT " . $limit . " OFFSET " . $offset);
    }
}