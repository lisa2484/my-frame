<?php

namespace app\controllers;

include "./models/user_dao.php";
// include "./models/authority_dao.php";

use app\models\user_dao;
// use app\models\authority_dao;

class userpwd_con
{
    function init()
    {
        $data_arr = array(
            'id' => $_SESSION["id"],
            'account' => $_SESSION["act"],
            'name' => $_SESSION["name"]
        );

        return returnAPI($data_arr);
    }

    function editPassword()
    {
        if (!key_exists("id", $_POST)) return returnAPI([], 1, "param_err");
        $id = $_POST["id"];
        if (!is_numeric($id)) return returnAPI([], 1, "param_err");
        if (!key_exists("old_password", $_POST)) return returnAPI([], 1, "param_err");
        $fopad = $_POST["old_password"];
        if (!key_exists("new_password", $_POST)) return returnAPI([], 1, "param_err");
        $fpad = $_POST["new_password"];
        $userDao = new user_dao;
        $userData = $userDao->getUserByID($id);
        if (empty($userData)) return returnAPI([], 1, "param_err");
        $userData = $userData[0];
        $opad = md5($userData["account"] . $fopad . $userData["create_dt"]);
        if ($opad != $userData["password"]) {
            return returnAPI([], 1, "editpwd_oldpwd_err");
        } else {
            $npad = md5($userData["account"] . $fpad . $userData["create_dt"]);
            
            if ($userDao->updateUserForPad($id, $npad)) {
                return returnAPI([]);
            } else {
                return returnAPI([], 1, "upd_err");
            }
        }
    }
}
