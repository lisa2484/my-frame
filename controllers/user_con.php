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
        $authority = $autDao->getUserType();
        $autArr = [];
        foreach ($authority as $a) {
            $autArr[$a["id"]] = $a["authority_name"];
        }
        foreach ($datas as $k => $d) {
            $datas[$k]["authority_name"] = $autArr[$d["authority"]];
        }
        return returnAPI(["list" => $datas, "authority_list" => $authority]);
    }

    function addUser()
    {
        if (!isset($_POST["act"]) || $_POST["act"] == "") return returnAPI([], 1, "userset_act_empty");
        $account = $_POST["act"];
        if (!isset($_POST["pad"]) || $_POST["pad"] == "") return returnAPI([], 1, "userset_pwd_empty");
        $password = $_POST["pad"];
        if (empty($_POST["aut"])) return returnAPI([], 1, "userset_aut_empty");
        $authority = $_POST["aut"];
        $time = time();
        $userDao = new user_dao;
        $redata = $userDao->getUserByAccount($account);
        if (empty($redata)) {
            $pad = md5($account . $password . $time);
            if ($userDao->insertUser($account, $pad, $authority, $time)) return returnAPI([], 0, "userset_add_success");
            return returnAPI([], 0, "userset_add_fail");
        } else {
            return returnAPI([], 1, "userset_add_repeat");
        }
    }

    function set()
    {
        if (empty($_POST["aut"])) return returnAPI([], 1, "");
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
        $opad = md5($userData["account"] . $fopad . $userData["create_dt"]);
        if ($opad != $userData["password"]) {
            return false;
        } else {
            $npad = md5($userData["account"] . $fpad . $userData["create_dt"]);
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
