<?php

namespace app\models;

include_once "./sys/mysqlDB.php";

class authority_dao
{
    private static $table_name = "authority";

    function getAll()
    {
        return DB::select("SELECT * FROM " . authority_dao::$table_name);
    }

    function update($id, $name)
    {
        return DB::DBCode("UPDATE " . authority_dao::$table_name . " SET authority_name = '" . $name . "' WHERE id = '" . $id . "'");
    }

    function insert($name)
    {
        return DB::DBCode("INSERT INTO " . authority_dao::$table_name . " (authority_name) VALUE ('" . $name . "')");
    }

    function delete($id)
    {
        return DB::DBCode("DELETE FROM " . authority_dao::$table_name . " WHERE id = '" . $id . "'");
    }
}
