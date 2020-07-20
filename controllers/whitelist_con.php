<?php

namespace app\controllers;

include "./models/whitelist_dao.php";
include "./models/web_set_dao.php";

use app\models\whitelist_dao;
use app\models\web_set_dao;

class whitelist_con
{
    /**
     * IP白名單列表
     */
    function init()
    {
        if (!isset($_POST["page"]) || !is_numeric($_POST["page"])) return returnAPI([], 1, "param_empty");
        if (!isset($_POST["limit"]) || !is_numeric($_POST["limit"])) return returnAPI([], 1, "param_empty");
        $page = $_POST["page"];
        $limit = $_POST["limit"];
        $ip = "";
        if (isset($_POST["ip"]) && $_POST["ip"] != "") {
            $ip = $_POST["ip"];
            if (!filter_var($ip, FILTER_VALIDATE_IP)) return returnAPI([], 1, "param_err");
        }
        $wDao = new whitelist_dao;
        $totaldata = $wDao->getTotalList($ip);
        $totalpage = ceil($totaldata / $limit);
        $datas = $wDao->getList($ip, $page, $limit);

        $wsDao = new web_set_dao;
        $data = $wsDao->getWebSetListBySetKey("whitelist_switch");
        if (empty($data)) {
            $switch = 0;
        } else {
            $switch = $data[0]["value"];
        }
        return returnAPI([
            'switch' => $switch,
            'total' => $totaldata,
            'totalpage' => $totalpage,
            'page' => $page,
            'list' => $datas
        ]);
    }

    /**
     * 新增
     */
    function setWhitelistAdd()
    {
        if (!isset($_POST["ip"]) || !filter_var($_POST["ip"], FILTER_VALIDATE_IP)) return returnAPI([], 1, "param_err");
        $ip = $_POST["ip"];
        $wDao = new whitelist_dao;
        if (!empty($wDao->getIP($ip))) return returnAPI([], 1, "whitelist_add_err");
        if ($wDao->insertIP($ip)) return returnAPI([]);
        return returnAPI([], 1, "add_err");
    }

    /**
     * 修改
     */
    function setWhitelistEdit()
    {
        if (!isset($_POST["ip"]) || !filter_var($_POST["ip"], FILTER_VALIDATE_IP)) return returnAPI([], 1, "param_err");
        if (!isset($_POST["id"]) || empty($_POST["id"]) || !is_numeric($_POST["id"])) return returnAPI([], 1, "param_err");
        $ip = $_POST["ip"];
        $id = $_POST["id"];
        $wDao = new whitelist_dao;
        if ($wDao->setIP($id, $ip)) return returnAPI([]);
        return returnAPI([], 1, "upd_err");
    }

    /**
     * 總開關
     */
    function setWhitelistSwitch()
    {
        if (!isset($_POST["value"]) || !in_array($_POST["value"], [0, 1])) return returnAPI([], 1, "param_err");
        $value = $_POST["value"];
        $wsDao = new web_set_dao;
        if (empty($wsDao->getWebSetListBySetKey("whitelist_switch"))) {
            if ($wsDao->setWebSetAdd("whitelist_switch", $value)) return returnAPI([]);
            return returnAPI([], 1, "whitelist_setswitch_err");
        } else {
            if ($wsDao->setWebSetEdit("whitelist_switch", $value)) return returnAPI([]);
            return returnAPI([], 1, "upd_err");
        }
    }

    /**
     * 刪除
     */
    function setWhitelistDeleteList()
    {
        if (!isset($_POST["id"])) return returnAPI([], 1, "param_empty");
        $ids = explode(",", $_POST["id"]);
        foreach ($ids as $id) {
            if (!is_numeric($id)) return returnAPI([], 1, "param_err");
        }
        $wDao = new whitelist_dao;
        if ($wDao->setDeleteList($ids)) return returnAPI([]);
        return returnAPI([], 1, "del_err");
    }
}
