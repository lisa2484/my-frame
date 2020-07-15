<?php

namespace app\models;

class action_log_dao
{
    private static $table_name = "action_log";
    private static $menu_t = "menu";

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

    function getActionLogTotalByArrayWhere(array $where)
    {
        if (empty($where)) return DB::select("SELECT count(`id`) AS `c` FROM `" . self::$table_name . "`;")[0]["c"];
        $arr = [];
        if (isset($where["s_d"]) && isset($where["e_d"])) $arr[] = "`datetime` BETWEEN '" . $where["s_d"] . "' AND '" . $where["e_d"] . "'";
        if (isset($where["user"])) $arr[] = "`user` = '" . $where["user"] . "'";
        return DB::select("SELECT count(`id`) AS `c` FROM `" . self::$table_name . "` WHERE " . implode(" AND ", $arr) . ";")[0]["c"];
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

    function getActionLogJoinMenu(array $where, int $page, int $limit)
    {
        if (empty($where)) return DB::select("SELECT `a`.`id`,`a`.`datetime`,`a`.`user`,`a`.`ip`,`m`.`name`,`a`.`fun` 
                                              FROM `" . self::$table_name . "` AS `a` 
                                              LEFT JOIN `" . self::$menu_t . "` AS `m` 
                                              ON `a`.`remark` = `m`.`url` 
                                              ORDER BY `a`.`datetime` DESC
                                              LIMIT " . (($page - 1) * $limit) . "," . $limit);
        $arr = [];
        if (isset($where["s_d"]) && isset($where["e_d"])) $arr[] = "`a`.`datetime` BETWEEN '" . $where["s_d"] . "' AND '" . $where["e_d"] . "'";
        if (isset($where["user"])) $arr[] = "`a`.`user` = '" . $where["user"] . "'";
        return DB::select("SELECT `a`.`id`,`a`.`datetime`,`a`.`user`,`a`.`ip`,`m`.`name`,`a`.`fun` 
                           FROM `" . self::$table_name . "` AS `a` 
                           LEFT JOIN `" . self::$menu_t . "` AS `m` 
                           ON `a`.`remark` = `m`.`url` 
                           WHERE " . implode(" AND ", $arr) . "
                           ORDER BY `a`.`datetime` DESC
                           LIMIT " . (($page - 1) * $limit) . "," . $limit);
    }
}
