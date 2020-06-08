<?php

namespace app\controllers;

include "./models/authority_dao.php";
include "./models/menu_setting_dao.php";

use app\models\authority_dao;
use app\models\menu_setting_dao;

class authority_con
{
    function init()
    {
        $autDao = new authority_dao;
        $menuDao = new menu_setting_dao;
        $datas = $autDao->getAll();
        $menus = $menuDao->getMenuSettingAll();
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
        $autDao = new authority_dao;
        return $autDao->insert($_POST["name"]);
    }

    function edit()
    {
        $autDao = new authority_dao;
        return $autDao->update($_POST["id"], $_POST["name"], $_POST["aut"]);
    }

    function del()
    {
        $autDao = new authority_dao;
        return $autDao->delete($_POST["id"]);
    }
}
