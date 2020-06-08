<?php

namespace app\controllers;

include "./models/user_dao.php";
include "./models/authority_dao.php";

use app\models\user_dao;
use app\models\authority_dao;

class user_con
{
    function init()
    {
        $userDao = new user_dao;
        $autDao = new authority_dao;
        $datas = $userDao->getUser();
        $authority = $autDao->getAll();
        return view('settings/user_setting', ["datas" => $datas, "authority" => $authority]);
    }

    function addUser()
    {
        $time = time();
        $userDao = new user_dao;
        $redata = $userDao->selectUser($_POST["act"]);
        if (empty($redata)) {
            $pad = md5($_POST["act"] . $_POST["pad"] . $time);
            return $userDao->insertUser($_POST["act"], $pad, $_POST["name"], $_POST["aut"], $time);
        } else {
            return "account-repeat";
        }
    }

    function editUser()
    {
        $userDao = new user_dao;
        return $userDao->updateUserForEdit($_POST["id"], $_POST["name"], $_POST["aut"]);
    }

    function padEdit()
    {
        $userDao = new user_dao;
        $userData = $userDao->selectUserByID($_POST["id"]);
        $userData = $userData[0];
        $opad = md5($userData["account"] . $_POST["opad"] . strtotime($userData["create_dt"]));
        if ($opad != $userData["password"]) {
            return "opad-false";
        } else {
            $npad = md5($userData["account"] . $_POST["pad"] . strtotime($userData["create_dt"]));
            if ($userDao->updateUserForPad($_POST["id"], $npad)) {
                return "true";
            }
            return "false";
        }
    }

    function delUser()
    {
    }

    function logout()
    {
    }
}
