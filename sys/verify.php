<?php

use app\models\user_dao;

include_once "./sys/contorller.php";

function isVerfy($verify)
{
    $verify = isset($verify) ? $verify : true;
    if ($verify) {
        session_start();
        // var_dump($_SESSION);
        if (!isset($_SESSION["act"]) || !isset($_SESSION["pad"])) {
            if (!isset($_POST["account"]) || !isset($_POST["password"])) {
                return view("login");
            } else {
                include "./models/user_dao.php";
                $user = user_dao::selectUser($_POST["account"]);
                if (count($user) > 0 && md5($_POST["password"]) == $user[0]["password"]) {
                    $_SESSION["act"] = $user[0]["account"];
                    $_SESSION["pad"] = $user[0]["password"];
                    $_SESSION["time"] = time();
                    echo "true";
                    return;
                } else {
                    echo "false";
                    return;
                }
            }
        }
        if (isset($_SESSION["time"]) && time() - $_SESSION["time"] > 1800) { //逾時登出
            // session_unset();
            unset($_SESSION["act"]);
            unset($_SESSION["pad"]);
            unset($_SESSION["time"]);
            return view("login");
        } else {
            $_SESSION["time"] = time();
        }
    }
    include "./sys/route.php";
    $routes = new app\route;
    return $routes->init();
}
isVerfy($verify);
