<?php

namespace app\models;

include_once "./sys/mysqlDB.php";

use app\models\DB;

class user_dao
{
    private static $table_name = "bg_user";

    function getUser()
    {
        return DB::select("SELECT `id`,`user_name`,`authority`,`account`,`create_dt` FROM " . user_dao::$table_name);
    }

    function selectUser($act)
    {
        return DB::select("SELECT * FROM " . user_dao::$table_name . " WHERE account = '" . $act . "'");
    }

    function insertUser($act, $pad, $name, $aut, $time)
    {
        return DB::DBCode("INSERT INTO " . user_dao::$table_name . " (account,password,user_name,authority,create_dt) VALUES ('" . $act . "','" . $pad . "','" . $name . "','" . $aut . "','" . date("Y-m-d H:i:s", $time) . "')");
    }

    function updateUserForEdit($id, $name, $aut)
    {
        return DB::DBCode("UPDATE " . user_dao::$table_name . " SET user_name = '" . $name . "',authority = '" . $aut . "' WHERE id = '" . $id . "'");
    }

    function selectUserByID($id)
    {
        return DB::select("SELECT * FROM " . user_dao::$table_name . " WHERE id = '" . $id . "'");
    }

    function updateUserForPad($id, $pad)
    {
        return DB::DBCode("UPDATE " . user_dao::$table_name . " SET password ='" . $pad . "' WHERE id ='" . $id . "'");
    }
}
