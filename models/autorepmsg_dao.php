<?php

namespace app\models;

class autorepmsg_dao
{
    private static $table = "autorepmsg";

    /**
     * 取得資料
     */
    function getAllMsg(int $page, int $limit, array $whereArr = []): array
    {
        $where = "`is_del` = 0";
        if (!empty($whereArr)) {
            foreach ($whereArr as $k => $d) {
                $where .= " AND `" . $k . "` = '" . $d . "'";
            }
        }
        return DB::select("SELECT `id`,`title`,`keyword`,`msg`,`start_d`,`end_d`,`start_t`,`end_t`,`time_limit`,`onf`
                           FROM `" . self::$table . "`
                           WHERE " . $where . "
                           LIMIT " . ($page - 1) * $limit . "," . $limit . ";");
    }

    /**
     * 取得資料總筆數
     */
    function getAllMsgTotal(array $whereArr = []): int
    {
        $where = "`is_del` = 0";
        if (!empty($whereArr)) {
            foreach ($whereArr as $k => $d) {
                $where .= " AND `" . $k . "` = '" . $d . "'";
            }
        }
        $req = DB::select("SELECT count(`id`) AS i
                           FROM `" . self::$table . "`
                           WHERE " . $where . ";");
        return $req[0]["i"];
    }

    /**
     * 取得資料 依時間與開啟狀況
     */
    function getMsgWhereTimeAndOnf(): array
    {
        $date = date("Y-m-d");
        $time = date("H:i:s");
        return DB::select("SELECT `keyword`,`msg`
                           FROM `" . self::$table . "`
                           WHERE `onf` = 1
                           AND (`time_limit` = 0 
                                OR (`start_d` <= '" . $date . "' 
                                    AND `end_d` >= '" . $date . "' 
                                    AND `start_t` <= '" . $time . "' 
                                    AND `end_t` >= '" . $time . "'
                                    AND `time_limit` = 1) 
                                OR (`time_limit` = 2
                                    AND `start_t` <= '" . $time . "' 
                                    AND `end_t` >= '" . $time . "'))
                           AND `is_del` = 0");
    }

    function setMsgInsert(array $insertArr)
    {
        return DB::DBCode("INSERT INTO `" . self::$table . "` (`" . implode("`,`", array_keys($insertArr)) . "`,`creator`,`create_dt`,`create_ip`,`updater`,`update_dt`,`update_ip`)
                           VALUE ('" . implode("','", array_values($insertArr)) . "','" . $_SESSION["act"] . "','" . date("Y-m-d H:i:s") . "','" . getRemoteIP() . "','" . $_SESSION["act"] . "','" . date("Y-m-d H:i:s") . "','" . getRemoteIP() . "');");
    }

    /**
     * 更新資料
     */
    function setMsgUpdate(int $id, array $updateArr)
    {
        if (empty($updateArr)) return false;
        $whereArr = [];
        foreach ($updateArr as $k => $d) {
            $whereArr[] = "`" . $k . "` = '" . $d . "'";
        }
        return DB::DBCode("UPDATE `" . self::$table . "` 
                           SET " . implode(",", $whereArr) . ",
                           `updater` = '" . $_SESSION["act"] . "',
                           `update_dt` = '" . date("Y-m-d H:i:s") . "',
                           `update_ip` = '" . getRemoteIP() . "'
                           WHERE `id` = '" . $id . "' AND `is_del` = 0;");
    }

    /**
     * 狀態開關
     */
    function setMsgOnf(int $id, int $onf)
    {
        return DB::DBCode("UPDATE `" . self::$table . "`
                           SET `onf` = '" . $onf . "',
                               `updater` = '" . $_SESSION["act"] . "',
                               `update_dt` = '" . date("Y-m-d H:i:s") . "',
                               `update_ip` = '" . getRemoteIP() . "'
                           WHERE `id` = '" . $id . "' AND `is_del` = 0;");
    }

    /**
     * 刪除資料
     */
    function setMsgDelete(int $id)
    {
        return DB::DBCode("UPDATE `" . self::$table . "`
                           SET `is_del` = 1,
                               `updater` = '" . $_SESSION["act"] . "',
                               `update_dt` = '" . date("Y-m-d H:i:s") . "',
                               `update_ip` = '" . getRemoteIP() . "'
                           WHERE `id` = '" . $id . "';");
    }
}
