<?php

namespace app\models;

include_once './sys/mysqlDB.php';

class menu_setting_dao
{
    private static $table_name = 'menu';

    function getMenuSettingAll()
    {
        $dataArr = DB::select("SELECT * FROM " . menu_setting_dao::$table_name);
        return $dataArr;
    }

    function insertMainMenuSetting($name, $url, $icon, $aut)
    {
        return DB::DBCode("INSERT INTO " . menu_setting_dao::$table_name . " (name,url,icon,authority) VALUES ('" . $name . "','" . $url . "','" . $icon . "','" . $aut . "')");
    }

    function insertChildMenuSetting($id, $name, $url, $icon, $aut)
    {
        return DB::DBCode("INSERT INTO " . menu_setting_dao::$table_name . " (belong,name,url,icon,authority) VALUES ('" . $id . "','" . $name . "','" . $url . "','" . $icon . "','" . $aut . "')");
    }

    function updateMenuSettingByID($id, $name, $url, $icon, $aut)
    {
        return DB::DBCode("UPDATE " . menu_setting_dao::$table_name . " SET name = '" . $name . "',url = '" . $url . "',icon = '" . $icon . "' ,authority = '" . $aut . "' WHERE id = " . $id);
    }

    function deleteMenuSetting($id)
    {
        return DB::DBCode("DELETE FROM " . menu_setting_dao::$table_name . " WHERE id =" . $id);
    }
}
