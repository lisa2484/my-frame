<?php

namespace app\models;

class action_log_dao
{
    private static $table_name = "action_log";

    /**
     * 抓取操作紀錄總筆數
     * @param mixed $str_sql 要查詢的sql語法字串
     * @return int 回傳總筆數
     */
    function getActionLogTotal($str_sql): int
    {
        $total = DB::select("SELECT count(*) FROM " . self::$table_name . $str_sql);
        return $total[0]['count(*)'];
    }

    /**
     * 抓取操作紀錄
     * @param mixed $str_sql 要查詢的sql語法字串
     * @param mixed $limit 要查詢的起始值
     * @param mixed $offset 要查詢的筆數
     * @return array 回傳table表單資料 MYSQLI_ASSOC
     */
    function getActionLog($str_sql, $limit, $offset): array
    {
        return DB::select("SELECT `id`, `ip`, `user`, `datetime`, `remark`, `fun` FROM " . self::$table_name . $str_sql . " ORDER BY `datetime` DESC LIMIT " . $limit . " OFFSET " . $offset);
    }
}
