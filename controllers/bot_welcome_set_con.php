<?php

namespace app\controllers;

include "./models/web_set_dao.php";

use app\models\web_set_dao;

class bot_welcome_set_con
{
    function init()
    {
        $wsDao = new web_set_dao;
        $data = $wsDao->getWebSetListBySetKey("bot_welcome");
        if (empty($data)) {
            return returnAPI(["value" => ""]);
        }
        return returnAPI(["value" => $data["value"]]);
    }

    function set()
    {
        if (!key_exists("value", $_POST)) return returnAPI([], 1, "param_err");
        if ($this->setWebset("bot_welcome", $_POST["value"])) return returnAPI([]);
        return returnAPI([], 1, "upd_err");
    }

    private function setWebset($setkey, $value)
    {
        $wsDao = new web_set_dao;
        if (empty($wsDao->getWebSetListBySetKey($setkey))) return $wsDao->setWebSetAdd($setkey, $value);
        return $wsDao->setWebSetEdit($setkey, $value);
    }
}
