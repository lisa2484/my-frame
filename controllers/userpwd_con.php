<?php

namespace app\controllers;

include "./models/user_dao.php";

use app\models\user_dao;

class userpwd_con
{
    /**
     * 基本資料
     */
    function init()
    {
        return returnAPI([
            'id' => $_SESSION["id"],
            'account' => $_SESSION["act"],
            'name' => $_SESSION["name"]
        ]);
    }

    /**
     * 修改密碼
     */
    function editPassword()
    {
        if (!isset($_POST["id"]) || empty($_POST["id"]) || !is_numeric($_POST["id"])) return returnAPI([], 1, "param_err");
        if (!isset($_POST["old_password"])) return returnAPI([], 1, "param_err");
        if (!isset($_POST["new_password"])) return returnAPI([], 1, "param_err");
        $id = $_POST["id"];
        $fopad = $_POST["old_password"];
        $fpad = $_POST["new_password"];
        $userDao = new user_dao;
        $userData = $userDao->getUserByID($id);
        if (empty($userData)) return returnAPI([], 1, "param_err");
        $userData = $userData[0];
        $opad = md5($userData["account"] . $fopad . $userData["create_dt"]);
        if ($opad != $userData["password"]) return returnAPI([], 1, "editpwd_oldpwd_err");
        $npad = md5($userData["account"] . $fpad . $userData["create_dt"]);
        if ($userDao->updateUserForPad($id, $npad)) return returnAPI([]);
        return returnAPI([], 1, "upd_err");
    }
}
