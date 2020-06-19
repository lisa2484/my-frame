<?php

namespace app\controllers;

include "./models/web_set_dao.php";

use app\models\web_set_dao;

class chatroom_set_con
{
    function init()
    {
        return $this->getWebData();
    }

    function getWebData()
    {
        $wsDao = new web_set_dao;
        $datas = $wsDao->getWebSetList();
        $keys = self::getChatroomSetKey();
        $repArr = [];
        foreach ($datas as $data) {
            $k = array_search($data["set_key"], $keys);
            if ($k !== false) $repArr[$k] = $data["value"];
        }
        return json($repArr);
    }

    function set()
    {
        $keys = self::getChatroomSetKey();
        foreach (array_keys($_POST) as $p) {
            if (key_exists($p, $keys)) {
                $key = $p;
                break;
            }
        }
        if (!isset($key)) return false;
        return $this->setWebset($keys[$key], $_POST[$key]);
    }

    private static function getChatroomSetKey(): array
    {
        $arr["win_t"] = "window_title";
        $arr["logo_i"] = "logo_img";
        $arr["logo_u"] = "logo_url";
        $arr["win_c"] = "window_color";
        $arr["news"] = "news";
        $arr["ser_i"] = "service_img";
        $arr["ser_c"] = "service_color";
        $arr["vis_i"] = "visitor_img";
        $arr["vis_c"] = "visitor_color";
        $arr["too_s"] = "toolbar_set";
        $arr["back_u"] = "back_url";
        return $arr;
    }

    private function setWebset($setkey, $value)
    {
        $wsDao = new web_set_dao;
        if (empty($wsDao->getWebSetListBySetKey($setkey))) return $wsDao->setWebSetAdd($setkey, $value);
        return $wsDao->setWebSetEdit($setkey, $value);
    }
}
