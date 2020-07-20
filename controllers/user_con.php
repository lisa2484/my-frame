<?php

namespace app\controllers;

include "./models/user_dao.php";
include "./models/authority_dao.php";

use app\models\user_dao;
use app\models\authority_dao;

class user_con
{
    /**
     * 後台使用者列表
     */
    function init()
    {
        if (!isset($_POST["page"]) || !is_numeric($_POST["page"])) return returnAPI([], 1, "param_err");
        if (!isset($_POST["limit"]) || !is_numeric($_POST["limit"])) return returnAPI([], 1, "param_err");
        $page = $_POST["page"];
        $limit = $_POST["limit"];
        $adminname = "";
        if (isset($_POST["adminname"]) && $_POST["adminname"] != "") $adminname = $_POST["adminname"];
        $userDao = new user_dao;
        $autDao = new authority_dao;
        $usertotal = $userDao->getUserTotal($adminname);
        $totalpage = ceil($usertotal / $limit);
        if ($page > $totalpage && $totalpage != 0) return returnAPI([], 1, "param_err");
        $datas = $userDao->getUser($adminname, $limit, $page);
        $authority = $autDao->getUserType();
        $autArr = [];
        foreach ($authority as $a) {
            $autArr[$a["id"]] = $a["authority_name"];
        }
        foreach ($datas as $k => $d) {
            $datas[$k]["authority_name"] = $autArr[$d["authority"]];
        }
        return returnAPI([
            "total" => $usertotal,
            "totalpage" => $totalpage,
            "page" => $page,
            "list" => $datas,
            "authority_list" => $authority
        ]);
    }

    /**
     * 新增
     */
    function addUser()
    {
        if (!isset($_POST["act"]) || $_POST["act"] == "") return returnAPI([], 1, "userset_act_empty");
        if (!$this->chkAccount($_POST["act"])) return returnAPI([], 1, "userset_act_spcr");
        if (!isset($_POST["pad"]) || $_POST["pad"] == "") return returnAPI([], 1, "userset_pwd_empty");
        if (!isset($_POST["aut"]) || empty($_POST["aut"]) || !is_numeric($_POST["aut"])) return returnAPI([], 1, "userset_aut_empty");
        $account = $_POST["act"];
        $password = $_POST["pad"];
        $authority = $_POST["aut"];
        $time = time();
        $userDao = new user_dao;
        $redata = $userDao->getUserByAccount($account);
        if (!empty($redata)) return returnAPI([], 1, "userset_add_repeat");
        $pad = md5($account . $password . date("Y-m-d H:i:s", $time));
        if ($userDao->insertUser($account, $pad, $authority, $time)) return returnAPI([]);
        return returnAPI([], 1, "userset_add_fail");
    }

    /**
     * 修改
     */
    function set()
    {
        if (!isset($_POST["id"]) || empty($_POST["id"]) || !is_numeric($_POST["id"])) return returnAPI([], 1, "param_err");
        $id = $_POST["id"];
        $aut = null;
        $pad = null;
        if (isset($_POST["aut"]) && (!empty($_POST["aut"]) && is_numeric($_POST["aut"]))) $aut = $_POST["aut"];
        if (isset($_POST["pad"]) && $_POST["pad"] != "") $pad = $_POST["pad"];
        if (!isset($aut) && !isset($pad)) return returnAPI([], 1, "param_err");
        $userDao = new user_dao;
        if (isset($pad)) {
            $user = $userDao->getUserByID($id);
            $pad = md5($user[0]["account"] . $pad . $user[0]["create_dt"]);
            if ($user[0]["password"] == $pad) return returnAPI([], 1, "userset_pwd_repeat");
        }
        if ($userDao->setUserSetting($id, $aut, $pad)) return returnAPI([]);
        return returnAPI([], 1, "upd_err");
    }

    /**
     * 刪除
     */
    function delUser()
    {
        if (!isset($_POST["id"])) return returnAPI([], 1, "param_err");
        $ids = explode(",", $_POST["id"]);
        foreach ($ids as $i) {
            if (!is_numeric($i)) return returnAPI([], 1, "param_err");
        }
        $uDao = new user_dao;
        if ($uDao->setDelete($ids)) return returnAPI([]);
        return returnAPI([], 1, "del_err");
    }

    /**
     * 字元塞選
     */
    private function chkAccount($str): bool
    {
        return preg_match("/^[0-9a-zA-Z]+$/", $str);
    }
}
