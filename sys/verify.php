<?php

namespace app;

use app\models\DB;

class verify extends serverset
{
    //驗證功能
    protected function isVerfy($verify, $routeGs)
    {
        $verify = isset($verify) ? $verify : true;
        if ($verify) {
            if (!isset($_POST["logout"])) {
                if (!$this->chkWhitelist()) return false;
                if (!$this->chkSession()) {
                    if (isset($_POST["account"]) && isset($_POST["password"])) {
                        if (!empty($_POST["account"])) {
                            //登入驗證
                            if ($this->LoginIn()) {
                                $this->loginLog($_POST["account"], true);
                                return true;
                            }
                            $this->loginLog($_POST["account"], false);
                            return false;
                        }
                    }
                } else {
                    if ($this->verifiedLogin()) return true;
                }
            }
            $this->unsetSession();
            return false;
        }
        return true;
    }

    //登入
    private function LoginIn()
    {
        $user = DB::select("SELECT * FROM `bg_user` WHERE `user_name` = '" . $_POST["account"] . "' LIMIT 1");
        if (!empty($user)) {
            $user = $user[0];
            if (!isset($_POST["password"])) return false;
            if (md5($user["account"] . $_POST["password"] . $user["create_dt"]) == $user["password"]) {
                $_SESSION["id"] = $user["id"];
                $_SESSION["act"] = $user["account"];
                $_SESSION["name"] = $user["user_name"];
                $_SESSION["aut"] = $user["authority"];
                $autName = DB::select("SELECT `authority_name` FROM authority WHERE `id` = '" . $user["id"] . "'");
                $_SESSION["aut_name"] = isset($autName[0]["authority_name"]) ? $autName[0]["authority_name"] : "";
                $_SESSION["time"] = time();
                return true;
            }
        }
        return false;
    }

    //確認session資料
    private function chkSession()
    {
        if (!isset($_SESSION["id"]) || !is_numeric($_SESSION["id"])) return false;
        if (!isset($_SESSION["name"])) return false;
        if (!isset($_SESSION["act"])) return false;
        if (!isset($_SESSION["aut"]) || !is_numeric($_SESSION["aut"])) return false;
        if (!isset($_SESSION["aut_name"])) return false;
        return true;
    }

    //驗證與權限功能補強
    private function verifiedLogin()
    {
        $user = DB::select("SELECT `id`,`authority` FROM `bg_user` WHERE `id` = '" . $_SESSION["id"] . "' LIMIT 1");
        if (empty($user)) {
            return false;
        }
        if ($user[0]["authority"] != $_SESSION["aut"]) {
            $_SESSION["aut"] = $user[0]["authority"];
        }
        $authority = DB::select("SELECT id FROM `authority` WHERE `id` = '" . $_SESSION["aut"] . "' LIMIT 1");
        if (empty($authority)) {
            return false;
        }
        if (!isset($_SESSION["time"]) || (time() - $_SESSION["time"]) > 1800) {
            return false;
        }
        $log = DB::select("SELECT `session_id` FROM `login_log` WHERE `account` = '" . $_SESSION["act"] . "' ORDER BY id DESC LIMIT 1");
        if (empty($log) || $log[0]["session_id"] != session_id()) {
            return false;
        }
        $_SESSION["time"] = time();
        return true;
    }

    //清除session資料
    private function unsetSession()
    {
        unset($_SESSION["id"]);
        unset($_SESSION["act"]);
        unset($_SESSION["aut"]);
        unset($_SESSION["name"]);
        unset($_SESSION["aut_name"]);
        unset($_SESSION["time"]);
    }

    //登入紀錄
    private function loginLog($user, $success)
    {
        $insert["account"] = $user;
        $insert["session_id"] = session_id();
        $headers = apache_request_headers();
        $headers["REMOTE_ADDR"] = $_SERVER["REMOTE_ADDR"];
        $insert["headers"] = json_encode($headers);
        $insert["ip"] = getRemoteIP();
        $insert["login_date"] = date("Y-m-d H:i:s");
        $insert["user_name"] = ($success ? $_SESSION["name"] : "");
        if (isset($_SESSION["id"])) {
            $autName = DB::select("SELECT `authority_name` FROM authority WHERE `id` = '" . $_SESSION["id"] . "'");
            $insert["authority_name"] = $autName[0]["authority_name"];
        } else {
            $insert["authority_name"] = "";
        }
        $success ? $insert["success"] = 1 : $insert["success"] = 0;
        DB::DBCode("INSERT INTO `login_log` (`" . implode("`,`", array_keys($insert)) . "`) VALUE ('" . implode("','", array_values($insert)) . "')");
    }

    //確認IP白名單
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
