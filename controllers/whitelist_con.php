<?php

namespace app\controllers;

include "./models/whitelist_dao.php";
include "./models/web_set_dao.php";

use app\models\whitelist_dao;
use app\models\web_set_dao;

class whitelist_con
{
    function init()
    {
        return $this->getWhitelistList();
    }

    function getWhitelistList()
    {
        if (!key_exists("page", $_POST)) return returnAPI([], 1, "param_empty");
        $page = $_POST["page"];
        if (!key_exists("limit", $_POST)) return returnAPI([], 1, "param_empty");
        $limit = $_POST["limit"];
        $wDao = new whitelist_dao;
        $datas = $wDao->getList($page, $limit);
        return returnAPI($datas);
    }

    function setWhitelistAdd()
    {
        if (!key_exists("ip", $_POST)) return returnAPI([], 1, "param_empty");
        $ip = $_POST["ip"];
        if (!filter_var($ip, FILTER_VALIDATE_IP)) return returnAPI([], 1, "param_err");
        $wDao = new whitelist_dao;

        if (empty($wDao->getIP($ip))){
            if ($wDao->insertIP($ip)) {
                return returnAPI([]);
            } else {
                return returnAPI([], 1, "add_err");
            }
        } else {
            return returnAPI([], 1, "whitelist_add_err");
        }
    }

    function setWhitelistEdit()
    {
        if (!key_exists("ip", $_POST)) return returnAPI([], 1, "param_empty");
        if (!key_exists("id", $_POST)) return returnAPI([], 1, "param_empty");
        $ip = $_POST["ip"];
        if (!filter_var($ip, FILTER_VALIDATE_IP)) return returnAPI([], 1, "param_err");
        $id = $_POST["id"];
        if (!is_numeric($id) || empty($id)) return returnAPI([], 1, "param_err");
        $wDao = new whitelist_dao;

        if ($wDao->setIP($id, $ip)) {
            return returnAPI([]);
        } else {
            return returnAPI([], 1, "upd_err");
        }
    }

    function setWhitelistSwitch()
    {
        if (!key_exists("value", $_POST)) return returnAPI([], 1, "param_empty");
        $value = $_POST["value"];
        if (strlen($value) > 1) return returnAPI([], 1, "param_err");
        if (!in_array($value, [0, 1])) return returnAPI([], 1, "param_err");
        $wsDao = new web_set_dao;

        if (empty($wsDao->getWebSetListBySetKey("whitelist_switch"))){
            if ($wsDao->setWebSetAdd("whitelist_switch", $value)) {
                return returnAPI([]);
            } else {
                return returnAPI([], 1, "whitelist_setswitch_err");
            }
        } else {
            if ($wsDao->setWebSetEdit("whitelist_switch", $value)) {
                return returnAPI([]);
            } else {
                return returnAPI([], 1, "upd_err");
            }
        }
    }

    function setWhitelistDelete()
    {
        if (!key_exists("id", $_POST)) return returnAPI([], 1, "param_empty");
        $id = $_POST["id"];
        if (!is_numeric($id) || empty($id)) return returnAPI([], 1, "param_err");
        $wDao = new whitelist_dao;

        if ($wDao->deleteIP($id)) {
            return returnAPI([]);
        } else {
            return returnAPI([], 1, "del_err");
        }
    }
}
