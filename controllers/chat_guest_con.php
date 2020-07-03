<?php

namespace app\controllers;

include "./models/web_set_dao.php";

use app\models\web_set_dao;

class chat_guest_con
{
    function init()
    {
        $web_data = $this->getWebData();
        $automsg_sw = $this->getWebSet("bot_automsg_switch");
        $autoservice_sw = $this->getWebSet("bot_autoservice_switch");        

        $data_arr = array(
            'webset' => $web_data,
            'automsg' => $automsg_sw,
            'autoservice' => $autoservice_sw
        );

        return returnAPI($data_arr);
    }

    private function getWebSet($key)
    {
        $wsDao = new web_set_dao;
        $data = $wsDao->getWebSetListBySetKey($key);
        if (empty($data)) {
            return 0;
        } else {
            return $data[0]["value"];
        }
    }

    private function getWebData()
    {
        $wsDao = new web_set_dao;
        $datas = $wsDao->getWebSetList();
        if (empty($datas)) return returnAPI([], 1, "sql_err");
        $keys = self::getChatroomSetKey();
        $repArr = [];
        $rDatas = [];
        if (!empty($datas)) {
            foreach ($datas as $data) {
                $rDatas[$data["set_key"]] = $data["value"];
            }
        }
        if (empty($rDatas)) {
            foreach (array_keys($keys) as $k) {
                $repArr[$k] = "";
            }
        } else {
            foreach ($keys as $k => $d) {
                if (key_exists($d, $rDatas)) {
                    $repArr[$k] = $rDatas[$d];
                } else {
                    $repArr[$k] = "";
                }
            }
        }

        return $repArr;
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
}
