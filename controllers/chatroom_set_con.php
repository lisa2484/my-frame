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
        $imgkey_arr = ['logo_img', 'service_img', 'visitor_img'];

        $wsDao = new web_set_dao;
        $datas = $wsDao->getWebSetList();
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
                    if ($d == "toolbar_set") {
                        $repArr[$k] = html_entity_decode($rDatas[$d]);
                    } else if (in_array($d, $imgkey_arr)) {
                        $repArr[$k] = getImgUrl("", $rDatas[$d]);
                    } else {
                        $repArr[$k] = $rDatas[$d];
                    }
                } else {
                    $repArr[$k] = "";
                }
            }
        }
        return returnAPI($repArr);
    }

    function set()
    {
        $key_arr = ['logo_i', 'ser_i', 'vis_i'];
        $keys = self::getChatroomSetKey();

        if (empty($_FILES)) {
            $keys = self::getChatroomSetKey();
            foreach (array_keys($_POST) as $p) {
                if (key_exists($p, $keys)) {
                    $key = $p;
                    break;
                }
            }
        } else {
            $f_key = array_keys($_FILES);

            if (in_array($f_key[0], $key_arr)) {
                $key = $f_key[0];
            } else {
                return returnAPI([], 1, "param_err");
            }
        }

        if (in_array($key, $key_arr)) {
            $upload_str = $this->setChatImg($key);

            if ($upload_str == "upload_err" || $upload_str == "file_err") {
                return returnAPI([], 1, $upload_str);
            } else {
                $val = $upload_str;
            }
        } else {
            $val = $_POST[$key];
        }

        if (!isset($key)) return returnAPI([], 1, "param_empty");
        if ($this->setWebset($keys[$key], $val)) return returnAPI([]);
        return returnAPI([], 1, "upd_err");
    }

    function setList()
    {
        $keys = self::getChatroomSetKey();
        foreach ($keys as $k => $d) {
            if (!key_exists($k, $_POST)) return returnAPI([], 1, "param_err");
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

    private function setChatImg($key)
    {
        $filename = "";
        if (!empty($_FILES)) {
            if (!updateImg($filename, "", "chatroom_" . $key . "_", $key)) return "upload_err";
            return $filename;
        } else {
            return "file_err";
        }
    }
}
