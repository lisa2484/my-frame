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
                if (!$this->chkWhitelist()) return false;
                if (!isset($_SESSION["act"]) || !isset($_SESSION["pad"])) {
                    if (isset($_POST["account"]) && isset($_POST["password"])) {
                        if (!empty($_POST["account"])) {
                            $user = DB::select("SELECT * FROM `bg_user` WHERE `user_name` = '" . $_POST["account"] . "' LIMIT 1");
                            if (!empty($user)) {
                                $user = $user[0];
                                $insert["account"] = $user["account"];
                                $insert["session_id"] = session_id();
                                $headers = apache_request_headers();
                                $headers["REMOTE_ADDR"] = $_SERVER["REMOTE_ADDR"];
                                $insert["headers"] = json_encode($headers);
                                if (md5($user["account"] . $_POST["password"] . strtotime($user["create_dt"])) == $user["password"]) {
                                    $this->loginLog($user["account"], true);
                                    $_SESSION["act"] = $user["account"];
                                    $_SESSION["pad"] = $user["password"];
                                    $_SESSION["name"] = $user["user_name"];
                                    $_SESSION["aut"] = $user["authority"];
                                    $_SESSION["time"] = time();
                                    return true;
                                }
                            }
                            $this->loginLog($_POST["account"], false);
                            return false;
                        }
                    }
                }
                if (isset($_SESSION["time"]) && (time() - $_SESSION["time"]) < 1800) { //逾時登出
                    $log = DB::select("SELECT `session_id` FROM `login_log` WHERE `account` = '" . $_SESSION["act"] . "' ORDER BY id DESC LIMIT 1");
                    if (!empty($log) && $log[0]["session_id"] == session_id()) {
                        $_SESSION["time"] = time();
                        return true;
                    }
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

    private function loginLog($user, $success)
    {
        $insert["account"] = $user;
        $insert["session_id"] = session_id();
        $headers = apache_request_headers();
        $headers["REMOTE_ADDR"] = $_SERVER["REMOTE_ADDR"];
        $insert["headers"] = json_encode($headers);
        $insert["ip"] = getRemoteIP();
        $insert["login_date"] = date("Y-m-d H:i:s");
        $success ? $insert["success"] = 1 : $insert["success"] = 0;
        DB::DBCode("INSERT INTO `login_log` (`" . implode("`,`", array_keys($insert)) . "`) VALUE ('" . implode("','", array_values($insert)) . "')");
    }

    private function chkWhitelist()
    {
        $set = DB::select("SELECT `value` FROM `web_set` WHERE `set_key` = 'whitelist_switch'");
        if (!empty($set)) {
            $ip = getRemoteIP();
            $req = DB::select("SELECT id FROM `whitelist` WHERE `ip` = '" . $ip . "' LIMIT 1");
            if (empty($req)) {
                return false;
            }
        }
        return true;
    }
}
