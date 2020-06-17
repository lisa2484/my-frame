<?php

namespace app\models;

use app\models\DB;

class user_dao
{
    private static $table_name = "user";

    function getUser()
    {
        return DB::select("SELECT `id`,`user_name`,`authority`,`account`,`create_dt` FROM `" . self::$table_name . "`");
    }

    function selectUser($act)
    {
        return DB::select("SELECT * FROM `" . self::$table_name . "` WHERE `account` = '" . $act . "'");
    }

    function insertUser($act, $pad, $name, $aut, $time)
    {
        return DB::DBCode("INSERT INTO `" . self::$table_name . "` (`account`,`password`,`user_name`,`authority`,`creator`,`create_dt`,`create_ip`,`updater`,`update_dt`,`update_ip`,`chg_pw_time`) 
                           VALUE ('" . $act . "',
                                  '" . $pad . "',
                                  '" . $name . "',
                                  '" . $aut . "',
                                  '" . $_SESSION["act"] . "',
                                  '" . date("Y-m-d H:i:s", $time) . "',
                                  '" . getRemoteIP() . "',
                                  '" . $_SESSION["act"] . "',
                                  '" . date("Y-m-d H:i:s") . "',
                                  '" . getRemoteIP() . "',
                                  '" . time() . "')");
    }

    // function updateUserForEdit(int $id, $name, int $aut)
    // {
    //     return DB::DBCode("UPDATE `" . self::$table_name . "` 
    //                        SET `user_name` = '" . $name . "',
    //                            `authority` = '" . $aut . "' 
    //                        WHERE `id` = '" . $id . "'");
    // }

    function selectUserByID($id)
    {
        return DB::select("SELECT * FROM `" . self::$table_name . "` WHERE `id` = '" . $id . "'");
    }

    function updateUserForPad($id, $pad)
    {
        return DB::DBCode("UPDATE `" . self::$table_name . "` 
                           SET `password` = '" . $pad . "',
                               `updater` = '" . $_SESSION["act"] . "',
                               `update_dt` = '" . date("Y-m-d H:i:s") . "',
                               `update_ip` = '" . getRemoteIP() . "',
                               `chg_pw_time` = '" . time() . "'
                           WHERE `id` = '" . $id . "'");
    }

    function setDelete($id)
    {
        return DB::DBCode("UPDATE `" . self::$table_name . "` 
                           SET `is_del` = 1 ,
                               `updater` = '" . $_SESSION["act"] . "',
                               `update_dt` = '" . date("Y-m-d H:i:s") . "',
                               `update_ip` = '" . getRemoteIP() . "'
                           WHERE `id` = '" . $id . "';");
    }
}
