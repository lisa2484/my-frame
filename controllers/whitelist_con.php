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
        if (!key_exists("page", $_POST)) return false;
        $page = $_POST["page"];
        if (!key_exists("limit", $_POST)) return false;
        $limit = $_POST["limit"];
        $wDao = new whitelist_dao;
        $datas = $wDao->getList($page, $limit);
        return json($datas);
    }

    function setWhitelistAdd()
    {
        if (!key_exists("ip", $_POST)) return false;
        $ip = $_POST["ip"];
        if (!filter_var($ip, FILTER_VALIDATE_IP)) return false;
        $wDao = new whitelist_dao;
        if (empty($wDao->getIP($ip))) return $wDao->insertIP($ip);
        return false;
    }

    function setWhitelistEdit()
    {
        if (!key_exists("ip", $_POST)) return false;
        if (!key_exists("id", $_POST)) return false;
        $ip = $_POST["ip"];
        if (!filter_var($ip, FILTER_VALIDATE_IP)) return false;
        $id = $_POST["id"];
        if (!is_numeric($id) || empty($id)) return false;
        $wDao = new whitelist_dao;
        return $wDao->setIP($id, $ip);
    }

    function setWhitelistSwitch()
    {
        if (!key_exists("value", $_POST)) return false;
        $value = $_POST["value"];
        if (strlen($value) > 1) return false;
        if (!in_array($value, [0, 1])) return false;
        $wsDao = new web_set_dao;
        if (empty($wsDao->getWebSetListBySetKey("whitelist_switch"))) return $wsDao->setWebSetAdd("whitelist_switch", $value);
        return $wsDao->setWebSetEdit("whitelist_switch", $value);
    }

    function setWhitelistDelete()
    {
        if (!key_exists("id", $_POST)) return false;
        $id = $_POST["id"];
        if (!is_numeric($id) || empty($id)) return false;
        $wDao = new whitelist_dao;
        return $wDao->deleteIP($id);
    }
}
