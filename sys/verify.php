<?php

namespace app;

use app\models\DB;

class verify extends serverset
{
    private $errmsg = "";
    private $status = 2;
    //驗證功能
    protected function isVerfy(array $route = [])
    {
        empty($route) ? $routeGs = "" : $routeGs = trim($route[0]);
        if ($this->except($routeGs)) return true;
        if (isset($_POST["logout"])) {
            $this->unsetSession();
            $this->errmsg = "logout";
            return false;
        }
        if (!$this->chkWhitelist()) {
            $this->unsetSession();
            $this->errmsg = "ip_fail";
            return false;
        }
        if (!$this->chkSession()) {
            $this->errmsg = "login_do";
            return false;
        }
        if ($this->verifiedLogin()) {
            if ($this->chkAuthority($routeGs)) {
                if ($this->actionLogAdd($route)) return true;
                $this->errmsg = "action_log_err";
                $this->status = 1;
                return false;
            }
            $this->errmsg = "aut_fail";
            $this->status = 1;
            return false;
        }
        return false;
    }

    protected function getErrMsg()
    {
        return ["status" => $this->status, "msg" => $this->errmsg];
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
            $this->errmsg = "user_empty";
            return false;
        }
        if ($user[0]["authority"] != $_SESSION["aut"]) {
            $_SESSION["aut"] = $user[0]["authority"];
        }
        $authority = DB::select("SELECT id FROM `authority` WHERE `id` = '" . $_SESSION["aut"] . "' LIMIT 1");
        if (empty($authority)) {
            $this->unsetSession();
            $this->errmsg = "aut_empty";
            return false;
        }
        if (!isset($_SESSION["time"]) || (time() - $_SESSION["time"]) > 1800) {
            $this->unsetSession();
            $this->errmsg = "login_timeout";
            return false;
        }
        $log = DB::select("SELECT `session_id` FROM `login_log` WHERE `account` = '" . $_SESSION["act"] . "' ORDER BY id DESC LIMIT 1");
        if (empty($log) || $log[0]["session_id"] != session_id()) {
            $this->unsetSession();
            $this->errmsg = "login_another";
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

    //確認IP白名單
    private function chkWhitelist()
    {
        $set = DB::select("SELECT `value` FROM `web_set` WHERE `set_key` = 'whitelist_switch'");
        if (!empty($set[0]['value'])) {
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
        $exceptArr[] = "login";
        return in_array($routeName, $exceptArr);
    }

    private function actionLogAdd(array $route)
    {
        if (!isset($route[1])) return true;
        $fun = "";
        if (preg_match("/^(add)/", $route[1])) $fun = "新增";
        if (empty($fun) && preg_match("/^(edit)/", $route[1])) $fun = "修改";
        if (empty($fun) && preg_match("/^(del)/", $route[1])) $fun = "删除";
        if (empty($fun) && preg_match("/^(switch)/", $route[1])) $fun = "开关修改";
        if (empty($fun)) return true;
        return DB::DBCode("INSERT INTO `action_log` (`ip`,`user`,`action`,`remark`,`fun`) 
                           VALUE ('" . getRemoteIP() . "',
                                  '" . $_SESSION["act"] . "',
                                  '" . json_encode($_POST) . "',
                                  '" . $route[0] . "',
                                  '" . $fun . "')");
    }
}
