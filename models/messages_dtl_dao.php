<?php

namespace app\models;

class messages_dtl_dao
{
    private static $table = "messages_dtl";
    private static $main_table = "messages_main";
    private static $user_table = "user";

    function getMsgByNewMessage(int $mid, int $id)
    {
        return DB::select("SELECT * 
                           FROM `" . self::$table . "`
                           WHERE `main_id` = '" . $mid . "'
                           AND `id` > '" . $id . "'
                           ORDER BY `id` ASC");
    }

    function getMsgByMsgJsonUser(int $mid, int $id)
    {
        return DB::select("SELECT `d`.`id`,`d`.`msg_from`,`d`.`content`,`d`.`filename`,`d`.`time`,`d`.`service_name`,`d`.`service_img`,`u`.`user_name`,`d`.`type`
                           FROM `" . self::$table . "` AS `d`
                           LEFT JOIN `" . self::$user_table . "` AS `u`
                           ON `d`.`service_name` = `u`.`account`
                           AND `u`.`is_del` = 0
                           WHERE `d`.`main_id` = '" . $mid . "'
                           AND `d`.`id` > '" . $id . "' 
                           ORDER BY `d`.`id` ASC");
    }

    function setMsgInsert(array $insertArr, int &$id = 0)
    {
        if (!isset($insertArr["main_id"]) || !is_numeric($insertArr["main_id"])) return false;
        $success = DB::DBCode("INSERT INTO `" . self::$table . "` (`" . implode("`,`", array_keys($insertArr)) . "`)
                               VALUE ('" . implode("','", array_values($insertArr)) . "')");
        if ($success) {
            $id = mysqli_insert_id(DB::getDBCon());
            $this->setMainAddCircleCount($insertArr["main_id"]);
        }
        return $success;
    }

    private function setMainAddCircleCount(int $id)
    {
        DB::DBCode("UPDATE `" . self::$main_table . "` SET `circle_count` = (`circle_count` + 1) ,`end_time` = '" . time() . "' WHERE `id` = '" . $id . "';");
    }

    function getMessagesMainRecord(int $mainid): array
    {
        return DB::select("SELECT * FROM `" . self::$table . "` WHERE `main_id` = '" . $mainid . "'");
    }
}
