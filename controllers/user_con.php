<?php

namespace app\controllers;

include_once "./sys/controller.php";
include "./models/user_dao.php";

use app\models\user_dao;

class user_con
{
    function init()
    {
        $datas = user_dao::getUser();
        return view('settings/user_setting', ["datas" => $datas]);
    }

    function addUser()
    {
        
    }

    function editUser()
    {
    }

    function delUser()
    {
    }
}
