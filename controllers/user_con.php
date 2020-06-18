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
        if (!isset($_POST["act"]) || $_POST["act"] == "") return false;
        $account = $_POST["act"];
        if (!isset($_POST["pad"]) || $_POST["pad"] == "") return false;
        $password = $_POST["pad"];
        if (empty($_POST["aut"])) return false;
        $authority = $_POST["aut"];
        isset($_POST["name"]) ? $name = $_POST["name"] : $name = "";
        $time = time();
        $userDao = new user_dao;
        $redata = $userDao->selectUser($account);
        if (empty($redata)) {
            $pad = md5($account . $password . $time);
            return $userDao->insertUser($account, $pad, $name, $authority, $time);
        } else {
            return json(["account-repeat"]);
        }
    }

    function setUserName()
    {
        if (!isset($_POST["name"]) || $_POST["name"] == "") return false;
        $name = $_POST["name"];
        $userDao = new user_dao;
        if ($userDao->setUserName($_SESSION["id"], $name)) {
            $_SESSION["name"] = $name;
            return true;
        }
        return false;
    }

    function setUserImg()
    {
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
