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
}
