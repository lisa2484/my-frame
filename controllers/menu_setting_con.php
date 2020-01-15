<?php

namespace app\controllers;

use app\models\menu_setting_dao;

include_once "./sys/controller.php";
include_once "./models/menu_setting_dao.php";
include "./models/authority_dao.php";

use app\models\authority_dao;

class menu_setting_con
{
    function init()
    {
        // $dao = new menu_setting_dao;
        $menus = menu_setting_dao::getMenuSettingAll();
        $authorityTable = authority_dao::getAll();
        $mainMenus = array();
        $belongs = array();
        foreach ($menus as $menu) {
            if ($menu["belong"] == 0) {
                $mainMenus[] = $menu;
            } else {
                $belongs[$menu["belong"]][] = $menu;
            }
        }
        return view("settings/menu_setting", ["menus" => $mainMenus, "belongs" => $belongs, "authority" => $authorityTable]);
    }
    function menu_setting()
    {
        echo "menu";
    }

    function menu_edit()
    {
        if (menu_setting_dao::updateMenuSettingByID($_POST["id"], $_POST["name"], $_POST["url"], $_POST["icon"], $_POST["aut"])) {
            echo "ok";
            return;
        }
        echo "error";
    }

    function menu_add()
    {
        if (menu_setting_dao::insertMainMenuSetting($_POST["name"], $_POST["url"], $_POST["icon"], $_POST["aut"])) {
            echo "ok";
            return;
        }
        echo "error";
        return;
    }

    function menu_del()
    {
        if (menu_setting_dao::deleteMenuSetting($_POST["id"])) {
            echo "ok";
            return;
        }
        echo "error";
        return;
    }

    function menu_child_add()
    {
        if (menu_setting_dao::insertChildMenuSetting($_POST["id"], $_POST["name"], $_POST["url"], $_POST["icon"], $_POST["aut"])) {
            echo "ok";
            return;
        }
        echo "error";
        return;
    }

    function sortable()
    {
        $main = json_decode($_POST["main"]);
        $sub = json_decode($_POST["sub"]);
        foreach ($main as $key => $id) {
            menu_setting_dao::sortSetting($id, $key);
        }
        foreach ($sub as $midArr) {
            if (count($midArr) > 1) {
                foreach ($midArr as $key => $id) {
                    menu_setting_dao::sortSetting($id, $key);
                }
            }
        }
    }
}
