<?php

namespace app\controllers;

use app\models\menu_setting_dao;

include "./models/menu_setting_dao.php";

class menu_setting_con
{
    function init()
    {
        $msDao = new menu_setting_dao;
        $menus = $msDao->getMenuSettingAll();
        $mainMenus = array();
        $belongs = array();
        foreach ($menus as $menu) {
            if ($menu["belong"] == 0) {
                $mainMenus[] = $menu;
            } else {
                $belongs[$menu["belong"]][] = $menu;
            }
        }
        return view("settings/menu_setting", ["menus" => $mainMenus, "belongs" => $belongs]);
    }
    function menu_setting()
    {
        return "menu";
    }

    function menu_edit()
    {
        $msDao = new menu_setting_dao;
        if ($msDao->updateMenuSettingByID($_POST["id"], $_POST["name"], $_POST["url"], $_POST["icon"])) {
            return "ok";
        }
        return "error";
    }

    function menu_add()
    {
        $msDao = new menu_setting_dao;
        if ($msDao->insertMainMenuSetting($_POST["name"], $_POST["url"], $_POST["icon"])) {
            return "ok";
        }
        return "error";
    }

    function menu_del()
    {
        $msDao = new menu_setting_dao;
        if ($msDao->deleteMenuSetting($_POST["id"])) {
            return "ok";
        }
        return "error";
    }

    function menu_child_add()
    {
        $msDao = new menu_setting_dao;
        if ($msDao->insertChildMenuSetting($_POST["id"], $_POST["name"], $_POST["url"], $_POST["icon"])) {
            return "ok";
        }
        return "error";
    }

    function sortable()
    {
        $msDao = new menu_setting_dao;
        $main = json_decode($_POST["main"]);
        $sub = json_decode($_POST["sub"]);
        foreach ($main as $key => $id) {
            $msDao->sortSetting($id, $key);
        }
        foreach ($sub as $midArr) {
            if (count($midArr) > 1) {
                foreach ($midArr as $key => $id) {
                    $msDao->sortSetting($id, $key);
                }
            }
        }
    }
}
