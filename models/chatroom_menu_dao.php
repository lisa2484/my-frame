<?php

namespace app\models;

class chatroom_menu_dao
{
    private static $table = "chatroom_menu";

    function getMenuSet(): array
    {
        return DB::select("SELECT `id`,`title`,`url`,`sort`,`filename`
                           FROM `" . self::$table . "`
                           WHERE `is_del` = 0;");
    }

    function getMaxSort(): int
    {
        $max = DB::select("SELECT max(`sort`) AS m
                           FROM `" . self::$table . "`
                           WHERE `is_del` = 0;");
        if (empty($max[0]["m"])) return 0;
        return $max[0]["m"];
    }

    function setMenuInsert(array $insertArr): bool
    {
        if (empty($insertArr)) return false;
        $nowDate = date("Y-m-d H:i:s");
        $ip = getRemoteIP();
        return DB::DBCode("INSERT INTO `" . self::$table . "`
                                    (`" . implode("`,`", array_keys($insertArr)) . "`,
                                    `creator`,`create_dt`,`create_ip`,`updater`,`update_dt`,`update_ip`)
                           VALUE ('" . implode("','", array_values($insertArr)) . "',
                                    '" . $_SESSION["act"] . "','" . $nowDate . "','" . $ip . "',
                                    '" . $_SESSION["act"] . "','" . $nowDate . "','" . $ip . "');");
    }

    function setMenuUpdate(int $id, array $updateArr): bool
    {
        if (empty($updateArr)) return false;
        $udStrArr = [];
        foreach ($updateArr as $k => $d) {
            $udStrArr[] = "`" . $k . "` = '" . $d . "'";
        }
        return DB::DBCode("UPDATE `" . self::$table . "`
                           SET `updater` = '" . $_SESSION["act"] . "',
                               `update_dt` = '" . date("Y-m-d H:i:s") . "',
                               `update_ip` = '" . getRemoteIP() . "',
                               " . implode(",", $udStrArr) . "
                           WHERE `id` = '" . $id . "' AND `is_del` = 0;");
    }

    function setDelete(int $id): bool
    {
        return DB::DBCode("UPDATE `" . self::$table . "`
                           SET `is_del` = 1,
                               `updater` = '" . $_SESSION["act"] . "'
                               `update_dt` = '" . date("Y-m-d H:i:s") . "'
                               `update_ip` = '" . getRemoteIP() . "'
                           WHERE `id` = '" . $id . "'");
    }
}
