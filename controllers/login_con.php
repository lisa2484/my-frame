<?php

namespace app\controllers;

include "./models/user_dao.php";
include "./models/authority_dao.php";
include "./models/login_log_dao.php";

use app\models\user_dao;
use app\models\authority_dao;
use app\models\login_log_dao;

class login_con
{
    function init()
    {
        if (!key_exists("account", $_POST) || $_POST["account"] == "") return returnAPI([], 2, "userset_act_empty");
        if (!key_exists("password", $_POST) || $_POST["password"] == "") return returnAPI([], 2, "userset_pwd_empty");
        $userDao = new user_dao;
        $user = $userDao->getUserByAccount($_POST["account"]);
        if (!empty($user)) {
            $user = $user[0];
            if (!isset($_POST["password"])) {
                $this->loginLog($_POST["account"], false);
                return returnAPI([], 2, "login_fail");
            }
            if (md5($user["account"] . $_POST["password"] . $user["create_dt"]) == $user["password"]) {
                $_SESSION["id"] = $user["id"];
                $_SESSION["act"] = $user["account"];
                $_SESSION["name"] = $user["user_name"];
                $_SESSION["aut"] = $user["authority"];
                $autDao = new authority_dao;
                $autName = $autDao->getAuthorityByID($user["authority"]);
                $_SESSION["aut_name"] = isset($autName[0]["authority_name"]) ? $autName[0]["authority_name"] : "";
                $_SESSION["time"] = time();
                $this->loginLog($user["account"], true);
                return returnAPI([]);
            }
        }
        $this->loginLog($_POST["account"], false);
        return returnAPI([], 2, "login_fail");
    }

    function getLoginStatus()
    {
        if (!$this->chkSession()) return returnAPI(["ip" => getRemoteIP(), "login" => false]);
        return returnAPI(["ip" => getRemoteIP(), "login" => true]);
    }

    //登入紀錄
    private function loginLog(string $user, bool $success)
    {
        $insert["account"] = $user;
        $insert["session_id"] = session_id();
        $headers = apache_request_headers();
        $headers["REMOTE_ADDR"] = $_SERVER["REMOTE_ADDR"];
        $insert["headers"] = json_encode($headers);
        $insert["ip"] = getRemoteIP();
        $insert["login_date"] = date("Y-m-d H:i:s");
        $insert["user_name"] = ($success ? $_SESSION["name"] : "");
        if (isset($_SESSION["aut_name"])) {
            $insert["authority_name"] = $_SESSION["aut_name"];
        } else {
            $insert["authority_name"] = "";
        }
        $success ? $insert["success"] = 1 : $insert["success"] = 0;
        $logDao = new login_log_dao;
        $logDao->setLoginLogInsert($insert);
    }

    private function chkSession()
    {
        if (!isset($_SESSION["id"]) || !is_numeric($_SESSION["id"])) return false;
        if (!isset($_SESSION["name"])) return false;
        if (!isset($_SESSION["act"])) return false;
        if (!isset($_SESSION["aut"]) || !is_numeric($_SESSION["aut"])) return false;
        if (!isset($_SESSION["aut_name"])) return false;
        return true;
    }
}
