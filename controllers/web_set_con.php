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
        return json_encode($wsDao->getWebSetList());
    }

    function setWebset()
    {
        if (!key_exists("setkey", $_POST)) return false;
        if (!key_exists("value", $_POST)) return false;
        $setkey = $_POST["setkey"];
        $value = $_POST["value"];
        $wsDao = new web_set_dao;
        if (empty($wsDao->getWebSetListBySetKey($setkey))) return $wsDao->setWebSetAdd($setkey, $value);
        return $wsDao->setWebSetEdit($setkey, $value);
    }

    function setWebsetList()
    {
    }
}
