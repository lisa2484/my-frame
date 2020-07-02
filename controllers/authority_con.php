<?php

namespace app\controllers;

include "./models/authority_dao.php";
include "./models/menu_dao.php";

use app\models\authority_dao;
use app\models\menu_dao;

class authority_con
{
    function init()
    {
        $autDao = new authority_dao;
        $menuDao = new menu_dao;
        $datas = $autDao->getAuthorityByID(2);
        if (empty($datas)) return returnAPI([], 1, "sql_err");
        $menus = $menuDao->getMenuByIDNotIn([14]);
        if (empty($menus)) return returnAPI([], 1, "sql_err");
        $datas = json_decode($datas[0]["authority"], true)["r"];
        return returnAPI(["menus" => $menus, "authority" => $datas]);
    }

    function edit()
    {
        if (!key_exists("ids", $_POST) || empty($_POST["ids"])) return returnAPI([], 1, "param_err");
        $ids = explode(",", $_POST["ids"]);
        foreach ($ids as $id) {
            if (!is_numeric($id) || $id == 14) return returnAPI([], 1, "param_err");
        }
        $arr["r"] = $ids;
        $autDao = new authority_dao;
        if ($autDao->setUpdateForAuthority(2, json_encode($arr))) return returnAPI([]);
        return returnAPI([], 1, "upd_err");
    }
}
