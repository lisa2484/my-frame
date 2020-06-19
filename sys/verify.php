<?php

namespace app;

use app\models\DB;

class verify extends serverset
{
    //驗證功能
    protected function isVerfy(string $routeGs = "")
    {
        $routeGs = trim($routeGs);
        if ($this->except($routeGs)) return true;
        if (isset($_POST["logout"])) {
            $this->unsetSession();
            return false;
        }
        if (!$this->chkWhitelist()) return false;
        if (!$this->chkSession()) {
            if (isset($_POST["account"]) && isset($_POST["password"])) {
                if ($_POST["account"] != "") {
                    //登入驗證
                    if ($this->LoginIn()) {
                        $this->loginLog($_POST["account"], true);
                        return $this->chkAuthority($routeGs);
                    }
                    $this->loginLog($_POST["account"], false);
                    return false;
                }
            }
            return false;
        } else {
            if ($this->verifiedLogin()) return $this->chkAuthority($routeGs);
        }
    }

    //登入
    private function LoginIn()
    {
        $user = DB::select("SELECT * FROM `user` WHERE `user_name` = '" . $_POST["account"] . "' AND `is_del` = 0 LIMIT 1");
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

    /**
     * 驗證與權限功能補強
     */
    private function verifiedLogin()
    {
        $user = DB::select("SELECT `id`,`authority` FROM `user` WHERE `id` = '" . $_SESSION["id"] . "' AND `is_del` = 0 LIMIT 1");
        if (empty($user)) {
            $this->unsetSession();
            return false;
        }
        if ($user[0]["authority"] != $_SESSION["aut"]) {
            $_SESSION["aut"] = $user[0]["authority"];
        }
        $authority = DB::select("SELECT id FROM `authority` WHERE `id` = '" . $_SESSION["aut"] . "' LIMIT 1");
        if (empty($authority)) {
            $this->unsetSession();
            return false;
        }
        if (!isset($_SESSION["time"]) || (time() - $_SESSION["time"]) > 1800) {
            $this->unsetSession();
            return false;
        }
        $log = DB::select("SELECT `session_id` FROM `login_log` WHERE `account` = '" . $_SESSION["act"] . "' ORDER BY id DESC LIMIT 1");
        if (empty($log) || $log[0]["session_id"] != session_id()) {
            $this->unsetSession();
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
            $req = DB::select("SELECT id FROM `ipwhitelist` WHERE `ip` = '" . $ip . "' AND `is_del` = 0 AND `onf` = 1 LIMIT 1");
            if (empty($req)) {
                return false;
            }
        }
        return true;
    }

    /**
     * 頁面權限驗證
     */
    private function chkAuthority(string $routeName): bool
    {
        if ($routeName == "") return true;
        $req = DB::select("SELECT `authority` FROM `authority` WHERE `id` = '" . $_SESSION["aut"] . "' AND `is_del` = 0 LIMIT 1;");
        if (empty($req)) return false;
        $authority = json_decode($req[0]["authority"], true)["r"];
        $req = DB::select("SELECT `id` FROM `menu` WHERE `url` = '" . $routeName . "' LIMIT 1;");
        if (empty($req)) return false;
        $menuId = $req[0]["id"];
        return in_array($menuId, $authority);
    }

    /**
     * 排除
     */
    private function except(string $routeName): bool
    {
        if ($routeName == "") return false;
        $exceptArr[] = "csbot";
        return in_array($routeName, $exceptArr);
    }
}
