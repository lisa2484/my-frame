<?php

namespace app\models;

class user_online_status_dao
{
    private static $table = "user_online_status";
    private static $user_table = "user";

    function getAllUserOnlineType()
    {
        return DB::select("SELECT `u`.`account`,`s`.`status`,`u`.`id`
                           FROM `" . self::$table . "` AS `s` 
                           LEFT JOIN `" . self::$user_table . "` AS `u` 
                           ON `s`.`user_id` = `u`.`id`
                           WHERE `u`.`is_del` = 0;");
    }

    function getUserIsOnline()
    {
        return DB::select("SELECT `u`.`account`,`u`.`user_name`,`u`.`id` 
                           FROM `" . self::$table . "` AS `s` 
                           LEFT JOIN `" . self::$user_table . "` AS `u` 
                           ON `s`.`user_id` = `u`.`id`
                           WHERE `u`.`is_del` = 0 AND `s`.`status` = 1
                           ORDER BY RAND()
                           LIMIT 1;");
    }

    function getUserOnline(int $id)
    {
        return DB::select("SELECT `status`
                           FROM `" . self::$table . "`
                           WHERE `user_id` = '" . $id . "'
                           LIMIT 1;");
    }

    function getUserOnlineForTransfer(int $id)
    {
        return DB::select("SELECT `u`.`account`,`u`.`user_name` 
                           FROM `" . self::$table . "` AS `s` 
                           LEFT JOIN `" . self::$user_table . "` AS `u`
                           ON `s`.`user_id` = `u`.`id`
                           WHERE `u`.`id` = '" . $id . "'
                           AND `u`.`is_del` = 0
                           AND `s`.`status` = 1
                           LIMIT 1;");
    }

    function setUserOnlineType(int $id, int $switch)
    {
        $str = "";
        if (!empty($switch)) $str = ",`last_online_time` = " . time();
        $success = DB::DBCode("UPDATE `" . self::$table . "`
                               SET `status` = '" . $switch . "'" . $str . "
                               WHERE `user_id` = '" . $id . "';");
        if ($success && empty(mysqli_affected_rows(DB::getDBCon()))) {
            if (empty(DB::select("SELECT `id` FROM `" . self::$table . "` WHERE `user_id` = `" . $id . "` LIMIT 1")))
                return DB::DBCode("INSERT INTO `" . self::$table . "` (`user_id`,`status`,`last_online_time`)
                                   VALUE ('" . $id . "','" . $switch . "','" . time() . "');");
        }
        return $success;
    }
}
