<?php

namespace app\controllers;

include_once "./sys/controller.php";
include "./models/user_dao.php";
include "./models/authority_dao.php";

use app\models\user_dao;
use app\models\authority_dao;

class user_con
{
    function init()
    {
        $datas = user_dao::getUser();
        $authority = authority_dao::getAll();
        return view('settings/user_setting', ["datas" => $datas, "authority" => $authority]);
    }

    function addUser()
    {
        $time = time();
        $redata = user_dao::selectUser($_POST["act"]);
        if (count($redata) > 0) {
            $pad = md5($_POST["act"] . $_POST["pad"] . $time);
            echo user_dao::insertUser($_POST["act"], $pad, $_POST["name"], $_POST["aut"], $time);
        } else {
            echo "account-repeat";
        }
    }

    function editUser()
    {
        echo user_dao::updateUserForEdit($_POST["id"], $_POST["name"], $_POST["aut"]);
    }

    function padEdit()
    {
        $userData = user_dao::selectUserByID($_POST["id"]);
        $userData = $userData[0];
        $opad = md5($userData["account"] . $_POST["opad"] . strtotime($userData["create_dt"]));
        if ($opad != $userData["password"]) {
            echo "opad-false";
        } else {
            $npad = md5($userData["account"] . $_POST["pad"] . strtotime($userData["create_dt"]));
            if (user_dao::updateUserForPad($_POST["id"], $npad)) {
                echo "true";
                return;
            }
            echo "false";
        }
    }

    function delUser()
    {
    }

    function logout()
    {
    }
}
