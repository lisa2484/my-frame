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
        return json($wsDao->getWebSetListBySetKey("web_timezone"));
    }

    function setWebTimeZone()
    {
        if (!key_exists("value", $_POST)) return false;
        $value = $_POST["value"];
        if (strlen($value) > 1) return false;
        if (!in_array($value, [0, 1])) return false;
        return $this->setWebset("web_timezone", $value);
    }

    private function setWebset($setkey, $value)
    {
        $wsDao = new web_set_dao;
        if (empty($wsDao->getWebSetListBySetKey($setkey))) return $wsDao->setWebSetAdd($setkey, $value);
        return $wsDao->setWebSetEdit($setkey, $value);
    }
}
