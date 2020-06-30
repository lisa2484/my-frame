<?php

namespace app\models;

class authority_dao
{
    private static $table_name = "authority";

    function getAll()
    {
        return DB::select("SELECT `id`,`authority_name`,`authority` FROM `" . self::$table_name . "` WHERE `is_del` = 0");
    }

    function getUserType()
    {
        return DB::select("SELECT `id`,`authority_name` FROM `" . self::$table_name . "` WHERE `is_del` = 0");
    }

    function getAuthorityByID($aid)
    {
        return DB::select("SELECT `id`,`authority_name`,`authority` FROM `" . self::$table_name . "` WHERE `id` = '" . $aid . "' AND `is_del` = 0 LIMIT 1");
    }

    function setUpdateForAuthority($id, $aut)
    {
        return DB::DBCode("UPDATE `" . self::$table_name . "` SET `authority` = '" . $aut . "' WHERE `id` = '" . $id . "';");
    }

    function update($id, $name, $aut)
    {
        return DB::DBCode("UPDATE `" . self::$table_name . "` SET `authority_name` = '" . $name . "', `authority` = '" . $aut . "' WHERE `id` = '" . $id . "' AND `is_del` = 0");
    }

    function insert($name)
    {
        return DB::DBCode("INSERT INTO `" . self::$table_name . "` (`authority_name`) VALUE ('" . $name . "')");
    }

    function delete($id)
    {
        return DB::DBCode("UPDATE `" . self::$table_name . "` SET `is_del` = 1 WHERE `id` = '" . $id . "'");
    }
}
