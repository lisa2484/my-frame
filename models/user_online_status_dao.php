<?php

namespace app\models;

class user_online_status_dao
{
    private static $table = "user_online_status";
    private static $user_table = "user";

    function getUserIsOnline()
    {
        return DB::select("SELECT `u`.`account`,`u`.`user_name`,`u`.`id` 
                           FROM `" . self::$table . "` AS `s` 
                           LEFT JOIN `" . self::$user_table . "` AS `u` 
                           ON `s`.`user_id` = `u`.`id`
                           WHERE `u`.`is_del` = 0 AND `s`.`status` = 1");
    }
}
