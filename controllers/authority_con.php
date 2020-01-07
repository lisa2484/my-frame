<?php

namespace app\controllers;

include_once "./sys/controller.php";
include "./models/authority_dao.php";

use app\models\authority_dao;

class authority_con
{
    function init()
    {
        $datas = authority_dao::getAll();
        return view("settings/authority", ["datas" => $datas]);
    }
    function add()
    {
        echo authority_dao::insert($_POST["name"]);
    }
    function edit()
    {
        echo authority_dao::update($_POST["id"], $_POST["name"]);
    }
    function del()
    {
        echo authority_dao::delete($_POST["id"]);
    }
}
