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
            $pad = md5($account . $password . date("Y-m-d H:i:s", $time));
            if ($userDao->insertUser($account, $pad, $authority, $time)) return returnAPI([], 0, "userset_add_success");
            return returnAPI([], 1, "userset_add_fail");
        }
        return returnAPI([], 1, "userset_add_repeat");
    }

    function set()
    {
        if (!isset($_POST["id"]) || !is_numeric($_POST["id"])) return returnAPI([], 1, "param_err");
        $id = $_POST["id"];
        $aut = null;
        $pad = null;
        if (isset($_POST["aut"])) {
            if (!is_numeric($_POST["aut"])) return returnAPI([], 1, "param_err");
            if ($_POST["aut"] != "") $aut = $_POST["aut"];
        }
        if (isset($_POST["pad"]) && $_POST["pad"] != "") $pad = $_POST["pad"];
        if (!isset($aut) && !isset($pad)) return returnAPI([], 1, "param_err");
        $userDao = new user_dao;
        if (isset($pad)) {
            $user = $userDao->getUserByID($id);
            $pad = md5($user[0]["account"] . $pad . $user[0]["create_dt"]);
            if ($user[0]["password"] == $pad) return returnAPI([], 1, "userset_pwd_repeat");
        }
        if ($userDao->setUserSetting($id, $aut, $pad)) {
            return returnAPI([]);
        }
        return returnAPI([], 1, "upd_err");
    }

    function delUser()
    {
        if (!key_exists("id", $_POST)) return returnAPI([], 1, "param_err");
        $ids = explode(",", $_POST["id"]);
        foreach ($ids as $i) {
            if (!is_numeric($i)) return returnAPI([], 1, "param_err");
        }
        $uDao = new user_dao;
        foreach ($ids as $id) {
            if (!$uDao->setDelete($id)) return returnAPI([], 1, "del_err");
        }
        return returnAPI([]);
    }
}
