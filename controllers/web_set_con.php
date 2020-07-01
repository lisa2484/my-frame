<?php

namespace app\controllers;

include "./models/web_set_dao.php";

use app\models\web_set_dao;

class web_set_con
{
    function init()
    {
        return $this->getWebsetList();
    }

    function getWebsetList()
    {
        $wsDao = new web_set_dao;
        $timezone = $wsDao->getWebSetListBySetKey("web_timezone");
        (empty($timezone) ? $returnArr["web_tz"] = 0 : $returnArr["web_tz"] = $timezone[0]["value"]);
        return returnAPI($returnArr);
    }

    function setWebTimeZone()
    {
        if (!key_exists("value", $_POST)) return returnAPI([], 1, "param_err");
        $value = $_POST["value"];
        if (strlen($value) > 1) return returnAPI([], 1, "param_err");
        if (!in_array($value, [0, 1])) return returnAPI([], 1, "param_err");
        if ($this->setWebset("web_timezone", $value)) {
            return returnAPI([]);
        }
        return returnAPI([], 1, "upd_err");
    }

    private function setWebset($setkey, $value)
    {
        $wsDao = new web_set_dao;
        if (empty($wsDao->getWebSetListBySetKey($setkey))) return $wsDao->setWebSetAdd($setkey, $value);
        return $wsDao->setWebSetEdit($setkey, $value);
    }
}
