<?php

namespace app\models;

class login_log_dao
{
    private static $table_name = "login_log";

    /**
     * 抓取登入紀錄總筆數
     * @param string $account 使用者帳號
     * @param string $strdt 搜尋時間範圍 開始時間
     * @param string $enddt 搜尋時間範圍 結束時間
     * @return int 回傳總筆數
     */
    function getLoginLogTotal(string $account, string $strdt, string $enddt): int
    {
        $where[] = "`success` = 1";
        if (!empty($strdt) && !empty($enddt)) $where[] = "`login_date` BETWEEN '" . $strdt . "' AND '" . $enddt . "'";
        if ($account != "") $where[] = "`account` = '" . $account . "'";
        $str_sql = "";
        if (!empty($where)) $str_sql = "WHERE " . implode(" AND ", $where);
        $total = DB::select("SELECT count(`id`) AS i FROM `" . self::$table_name . "` " . $str_sql . ";");
        return $total[0]['i'];
    }

    /**
     * 抓取登入紀錄
     * @param mixed $str_sql 要查詢的sql語法字串
     * @param mixed $limit 要查詢的起始值
     * @param mixed $offset 要查詢的筆數
     * @return array 回傳table表單資料 MYSQLI_ASSOC
     */
    function getLoginLog(string $account, string $strdt, string $enddt, int $page, int $limit): array
    {
        $where[] = "`success` = 1";
        if (!empty($strdt) && !empty($enddt)) $where[] = "`login_date` BETWEEN '" . $strdt . "' AND '" . $enddt . "'";
        if ($account != "") $where[] = "`account` = '" . $account . "'";
        $str_sql = "WHERE " . implode(" AND ", $where);
        $page = ($page - 1) * $limit;
        return DB::select("SELECT `id`, `account`, `ip`, `user_name`, `authority_name`, `login_date` FROM `" . self::$table_name . "` " . $str_sql . " ORDER BY `login_date` DESC LIMIT " . $page . "," . $limit . ";");
    }

    function getLoginLogForExport(string $account, string $strdt, string $enddt): array
    {
        $where[] = "`success` = 1";
        if (!empty($strdt) && !empty($enddt)) $where[] = "`login_date` BETWEEN '" . $strdt . "' AND '" . $enddt . "'";
        if ($account != "") $where[] = "`account` = '" . $account . "'";
        $str_sql = "WHERE " . implode(" AND ", $where);
        return DB::select("SELECT `account`, `user_name`, `authority_name`, `ip` FROM `" . self::$table_name . "` " . $str_sql . " ORDER BY `login_date` DESC;");
    }

    function setLoginLogInsert(array $insertArr)
    {
        return DB::DBCode("INSERT INTO `" . self::$table_name . "` (`" . implode("`,`", array_keys($insertArr)) . "`) VALUE ('" . implode("','", array_values($insertArr)) . "')");
    }
}
