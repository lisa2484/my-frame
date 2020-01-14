<?php

use app\models\user_dao;

include_once "./sys/controller.php";

function isVerfy($verify)
{
    $verify = isset($verify) ? $verify : true;
    if ($verify) {
        session_start();
        if (!isset($_POST["logout"])) {
            if (!isset($_SESSION["act"]) || !isset($_SESSION["pad"])) {
                if (!isset($_POST["account"]) || !isset($_POST["password"])) {
                    return view("login");
                } else {
                    include "./models/user_dao.php";
                    $user = user_dao::selectUser($_POST["account"]);
                    $user = $user[0];
                    if (count($user) > 0 && md5($user["account"] . $_POST["password"] . strtotime($user["create_dt"])) == $user["password"]) {
                        $_SESSION["act"] = $user["account"];
                        $_SESSION["pad"] = $user["password"];
                        $_SESSION["name"] = $user["user_name"];
                        $_SESSION["aut"] = $user["authority"];
                        $_SESSION["time"] = time();
                        echo "true";
                        return;
                    } else {
                        echo "false";
                        return;
                    }
                }
            }
        } else {
            unset($_SESSION["act"]);
            unset($_SESSION["pad"]);
            unset($_SESSION["aut"]);
            unset($_SESSION["name"]);
            unset($_SESSION["time"]);
            return view("login");
        }
        // if (isset($_SESSION["time"]) && time() - $_SESSION["time"] > 1800) { //逾時登出
        //     unset($_SESSION["act"]);
        //     unset($_SESSION["pad"]);
        //     unset($_SESSION["time"]);
        //     return view("login");
        // } else {
        //     $_SESSION["time"] = time();
        // }

    }
    include "./sys/route.php";
    $route = new app\route;
    return $route->init();
}
isVerfy($verify);
