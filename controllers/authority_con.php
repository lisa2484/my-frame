<?php

namespace app\controllers;

include_once "./sys/controller.php";
include "./models/authority_dao.php";
include "./models/menu_setting_dao.php";

use app\models\authority_dao;
use app\models\menu_setting_dao;

class authority_con
{
    function init()
    {
        $datas = authority_dao::getAll();
        $menus = menu_setting_dao::getMenuSettingAll();
        $mainMenus = [];
        $belongs = [];
        foreach ($menus as $menu) {
            if ($menu["belong"] == 0) {
                $mainMenus[] = $menu;
            } else {
                $belongs[$menu["belong"]][] = $menu;
            }
        }
        return view("settings/authority", ["datas" => $datas, "menu" => $mainMenus, "bel" => $belongs]);
    }
    function add()
    {
        echo authority_dao::insert($_POST["name"]);
    }
    function edit()
    {
        echo authority_dao::update($_POST["id"], $_POST["name"], $_POST["aut"]);
    }
    function del()
    {
        echo authority_dao::delete($_POST["id"]);
    }
}
