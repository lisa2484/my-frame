<?php

namespace app\controllers;

include "./models/authority_dao.php";
include "./models/menu_dao.php";

use app\models\authority_dao;
use app\models\menu_dao;

class authority_con
{
    /**
     * 權限表顯示
     */
    function init()
    {
        $autDao = new authority_dao;
        $datas = $autDao->getAuthorityByID(2);
        if (empty($datas)) return returnAPI([], 1, "sql_err");
        $menuDao = new menu_dao;
        $menus = $menuDao->getMenuByIDNotIn([14, 15]);
        if (empty($menus)) return returnAPI([], 1, "sql_err");
        $datas = json_decode($datas[0]["authority"], true)["r"];
        return returnAPI(["menus" => $menus, "authority" => $datas]);
    }

    /**
     * 權限表編輯
     */
    function edit()
    {
        if (!isset($_POST["ids"]) || empty($_POST["ids"])) return returnAPI([], 1, "param_err");
        $ids = explode(",", $_POST["ids"]);
        foreach ($ids as $id) {
            if (!is_numeric($id) || in_array($id, [14, 15])) return returnAPI([], 1, "param_err");
        }
        $arr["r"] = $ids;
        $autDao = new authority_dao;
        if ($autDao->setUpdateForAuthority(2, json_encode($arr))) return returnAPI([]);
        return returnAPI([], 1, "upd_err");
    }
}
