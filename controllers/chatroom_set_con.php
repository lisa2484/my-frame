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
        return returnAPI($repArr);
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
        if (!isset($key)) return returnAPI([], 1, "param_empty");
        if ($this->setWebset($keys[$key], $_POST[$key])) return returnAPI([]);
        return returnAPI([], 1, "upd_err");
    }

    function setList()
    {
        $keys = self::getChatroomSetKey();
        foreach ($keys as $k => $d) {
            if (!key_exists($k, $_POST) || $_POST[$k] == "") return returnAPI([], 1, "param_err");
            $datas[] = [$d, $_POST[$k]];
        }
        foreach ($datas as $data) {
            if (!$this->setWebset($data[0], $data[1])) return returnAPI([], 1, "upd_err");
        }
        return returnAPI([]);
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
