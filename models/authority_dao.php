<?php

namespace app\models;

class authority_dao
{
    private static $table_name = "authority";

    function getAll()
    {
        return DB::select("SELECT `id`,`authority_name`,`authority` FROM `" . authority_dao::$table_name . "` WHERE `is_del` = 0");
    }

    function getAuthorityByID($aid)
    {
        return DB::select("SELECT `id`,`authority_name`,`authority` FROM `" . authority_dao::$table_name . "` WHERE `id` = '" . $aid . "' AND `is_del` = 0 LIMIT 1");
    }

    function update($id, $name, $aut)
    {
        return DB::DBCode("UPDATE `" . authority_dao::$table_name . "` SET `authority_name` = '" . $name . "', `authority` = '" . $aut . "' WHERE `id` = '" . $id . "' AND `is_del` = 0");
    }

    function insert($name)
    {
        return DB::DBCode("INSERT INTO `" . authority_dao::$table_name . "` (`authority_name`) VALUE ('" . $name . "')");
    }

    function delete($id)
    {
        return DB::DBCode("UPDATE `" . authority_dao::$table_name . "` SET `is_del` = 1 WHERE `id` = '" . $id . "'");
    }
}
