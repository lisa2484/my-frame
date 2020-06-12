<?php

namespace app\models;

class menu_dao
{
    private static $table_name = 'menu';

    function getMenuSettingAll()
    {
        $dataArr = DB::select("SELECT * FROM `" . menu_dao::$table_name . "` ORDER BY `seq`");
        return $dataArr;
    }

    function insertMainMenuSetting($name, $url, $icon)
    {
        return DB::DBCode("INSERT INTO `" . menu_dao::$table_name . "` (`name`,`url`,`icon`) VALUES ('" . $name . "','" . $url . "','" . $icon . "')");
    }

    function insertChildMenuSetting($id, $name, $url, $icon)
    {
        return DB::DBCode("INSERT INTO `" . menu_dao::$table_name . "` (`belong`,`name`,`url`,`icon`) VALUES ('" . $id . "','" . $name . "','" . $url . "','" . $icon . "')");
    }

    function updateMenuSettingByID($id, $name, $url, $icon)
    {
        return DB::DBCode("UPDATE `" . menu_dao::$table_name . "` SET `name` = '" . $name . "',`url` = '" . $url . "',`icon` = '" . $icon . "' WHERE `id` = '" . $id . "'");
    }

    function deleteMenuSetting($id)
    {
        return DB::DBCode("DELETE FROM `" . menu_dao::$table_name . "` WHERE `id` ='" . $id . "'");
    }

    function sortSetting($id, $seq)
    {
        return DB::DBCode("UPDATE `" . menu_dao::$table_name . "` SET `seq` = '" . $seq . "' WHERE `id` = '" . $id . "'");
    }
}
