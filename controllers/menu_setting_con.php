<?php

namespace app\controllers;

use app\models\menu_dao;

include "./models/menu_dao.php";

class menu_setting_con
{
    function init()
    {
        $msDao = new menu_dao;
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
        if (!key_exists("id", $_POST)) return false;
        $id = $_POST["id"];
        if (!is_numeric($id)) return false;
        if (!key_exists("name", $_POST)) return false;
        $name = $_POST["name"];
        if (!key_exists("url", $_POST)) return false;
        $url = $_POST["url"];
        key_exists("icon", $_POST) ? $icon = $_POST["icon"] : $icon = "";
        $msDao = new menu_dao;
        return $msDao->updateMenuSettingByID($id, $name, $url, $icon);
    }

    function menu_add()
    {
        if (!key_exists("name", $_POST)) return false;
        $name = $_POST["name"];
        if (!key_exists("url", $_POST)) return false;
        $url = $_POST["url"];
        key_exists("icon", $_POST) ? $icon = $_POST["icon"] : $icon = "";
        $msDao = new menu_dao;
        return $msDao->insertMainMenuSetting($name, $url, $icon);
    }

    function menu_del()
    {
        if (!key_exists("id", $_POST)) return false;
        if (!is_numeric($_POST["id"])) return false;
        $msDao = new menu_dao;
        return $msDao->deleteMenuSetting($_POST["id"]);
    }

    function menu_child_add()
    {
        if (!key_exists("id", $_POST)) return false;
        $id = $_POST["id"];
        if (!is_numeric($id)) return false;
        if (!key_exists("name", $_POST)) return false;
        $name = $_POST["name"];
        if (!key_exists("url", $_POST)) return false;
        $url = $_POST["url"];
        key_exists("icon", $_POST) ? $icon = $_POST["icon"] : $icon = "";
        $msDao = new menu_dao;
        return $msDao->insertChildMenuSetting($id, $name, $url, $icon);
    }

    function sortable()
    {
        $msDao = new menu_dao;
        $main = json_decode($_POST["main"]);
        $sub = json_decode($_POST["sub"]);
        foreach ($main as $key => $id) {
            $msDao->sortSetting($id, $key);
        }
        foreach ($sub as $midArr) {
            if (count($midArr) > 1) {
                foreach ($midArr as $key => $id) {
                    if (!$msDao->sortSetting($id, $key)) return false;
                }
            }
        }
    }
}
