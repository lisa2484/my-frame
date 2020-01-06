<?php

namespace app\controllers;

include_once './sys/contorller.php';
include './models/menu_setting_dao.php';

use app\models\menu_setting_dao;

class main_con
{
    function init()
    {
        $menus = menu_setting_dao::getMenuSettingAll();
        $mainMenus = array();
        $belongs = array();
        foreach ($menus as $menu) {
            if ($menu['belong'] == 0) {
                $mainMenus[] = $menu;
            } else {
                $belongs[$menu['belong']][] = $menu;
            }
        }
        return view('main', ['menus' => $mainMenus, 'belongs' => $belongs]);
    }
}
