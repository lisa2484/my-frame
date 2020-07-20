<?php

namespace app\controllers;

include "./models/web_set_dao.php";

use app\models\web_set_dao;

class web_set_con
{
    /**
     * 網站設定列表
     */
    function init()
    {
        $wsDao = new web_set_dao;
        $timezone = $wsDao->getWebSetListBySetKey("web_timezone");
        return returnAPI(["web_tz" => (empty($timezone) ? 0 : $timezone[0]["value"])]);
    }

    /**
     * 修改時區
     */
    function setWebTimeZone()
    {
        if (!isset($_POST["value"]) || !in_array($_POST["value"], [0, 1])) return returnAPI([], 1, "param_err");
        $value = $_POST["value"];
        if ($this->setWebset("web_timezone", $value)) return returnAPI([]);
        return returnAPI([], 1, "upd_err");
    }

    /**
     * web_set設定功能
     */
    private function setWebset($setkey, $value)
    {
        $wsDao = new web_set_dao;
        if (empty($wsDao->getWebSetListBySetKey($setkey))) return $wsDao->setWebSetAdd($setkey, $value);
        return $wsDao->setWebSetEdit($setkey, $value);
    }
}
