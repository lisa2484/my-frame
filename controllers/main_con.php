<?php

namespace app\controllers;

include './models/menu_setting_dao.php';
include './models/authority_dao.php';

use app\models\menu_setting_dao;
use app\models\authority_dao;

class main_con
{
    function init()
    {
        $msDao = new menu_setting_dao;
        $menus = $msDao->getMenuSettingAll();
        $autDao = new authority_dao;
        $autDatas = $autDao->getAuthorityByID($_SESSION["aut"]);
        $autData = json_decode($autDatas[0]["authority"], true);
        $mainMenus = array();
        $belongs = array();
        foreach ($menus as $menu) {
            if (in_array($menu["id"], $autData["r"])) {
                if ($menu['belong'] == 0) {
                    $mainMenus[] = $menu;
                } else {
                    $belongs[$menu['belong']][] = $menu;
                }
            }
        }
        return view('main', ['menus' => $mainMenus, 'belongs' => $belongs, 'autname' => $autDatas[0]["authority_name"]]);
    }
}
