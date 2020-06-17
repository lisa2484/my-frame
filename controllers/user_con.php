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
        return json(["datas" => $datas, "authority" => $authority]);
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
            return json(["account-repeat"]);
        }
    }

    // function setUser()
    // {
    //     if (!key_exists("id", $_POST)) return false;
    //     $id = $_POST["id"];
    //     if (!is_numeric($id)) return false;
    //     if (!key_exists("name", $_POST)) return false;
    //     $name = $_POST["name"];
    //     if (!key_exists("aut", $_POST)) return false;
    //     $aut = $_POST["aut"];
    //     if (!is_numeric($aut)) return false;
    //     $uDao = new user_dao;
    //     return $uDao->updateUserForEdit($id, $name, $aut);
    // }

    function setUserName()
    {
        $_POST[""];
    }

    function editPassword()
    {
        if (!key_exists("id", $_POST)) return false;
        $id = $_POST["id"];
        if (!is_numeric($id)) return false;
        if (!key_exists("old_password", $_POST)) return false;
        $fopad = $_POST["old_password"];
        if (!key_exists("new_password", $_POST)) return false;
        $fpad = $_POST["new_password"];
        $userDao = new user_dao;
        $userData = $userDao->selectUserByID($id);
        if (empty($userData)) return false;
        $userData = $userData[0];
        $opad = md5($userData["account"] . $fopad . strtotime($userData["create_dt"]));
        if ($opad != $userData["password"]) {
            return false;
        } else {
            $npad = md5($userData["account"] . $fpad . strtotime($userData["create_dt"]));
            return $userDao->updateUserForPad($id, $npad);
        }
    }

    function delUser()
    {
        if (!key_exists("id", $_POST)) return false;
        $id = $_POST["id"];
        if (!is_numeric($id)) return false;
        $uDao = new user_dao;
        return $uDao->setDelete($id);
    }
}
