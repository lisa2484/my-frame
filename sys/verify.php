<?php

namespace app;

use app\models\DB;

class verify
{
    function isVerfy($verify)
    {
        $verify = isset($verify) ? $verify : true;
        if ($verify) {
            if (!isset($_POST["logout"])) {
                if (!isset($_SESSION["act"]) || !isset($_SESSION["pad"])) {
                    if (isset($_POST["account"]) && isset($_POST["password"])) {
                        $user = DB::select("SELECT * FROM `bg_user` WHERE `user_name` = '" . $_POST["account"] . "' LIMIT 1");
                        if (!empty($user)) {
                            $user = $user[0];
                            if (md5($user["account"] . $_POST["password"] . strtotime($user["create_dt"])) == $user["password"]) {
                                $_SESSION["act"] = $user["account"];
                                $_SESSION["pad"] = $user["password"];
                                $_SESSION["name"] = $user["user_name"];
                                $_SESSION["aut"] = $user["authority"];
                                $_SESSION["time"] = time();
                                return true;
                            }
                        }
                    }
                    return false;
                }
                if (isset($_SESSION["time"]) && (time() - $_SESSION["time"]) < 1800) { //逾時登出
                    $_SESSION["time"] = time();
                    return true;
                }
            }
            unset($_SESSION["act"]);
            unset($_SESSION["pad"]);
            unset($_SESSION["aut"]);
            unset($_SESSION["name"]);
            unset($_SESSION["time"]);
            return false;
        }
    }
}
