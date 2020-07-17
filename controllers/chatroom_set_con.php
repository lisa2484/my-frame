<?php

namespace app\controllers;

include "./models/web_set_dao.php";

use app\models\web_set_dao;

class chatroom_set_con
{
    /**
     * 聊天室設定列表
     */
    function init()
    {
        $imgkey_arr = ['logo_img', 'service_img', 'visitor_img'];

        $wsDao = new web_set_dao;
        $keys = self::getChatroomSetKey();
        $datas = $wsDao->getWebSetListByArraySetKey(array_values($keys));
        $repArr = [];
        $rDatas = [];
        if (!empty($datas)) {
            foreach ($datas as $data) {
                $rDatas[$data["set_key"]] = $data["value"];
            }
        }
        foreach ($keys as $k => $d) {
            if (empty($rDatas) || !isset($rDatas[$d])) {
                $repArr[$k] = "";
            } elseif (in_array($d, $imgkey_arr)) {
                $repArr[$k] = getImgUrl("", $rDatas[$d]);
            } else if ($d == "toolbar_set") {
                $repArr[$k] = html_entity_decode($rDatas[$d]);
            } else {
                $repArr[$k] = $rDatas[$d];
            }
        }
        return returnAPI($repArr);
    }

    /**
     * 修改
     */
    function set()
    {
        $keys = self::getChatroomSetKey();
        $request = $_POST;
        if (empty($request) && empty($_FILES)) return returnAPI([], 1, "param_err");
        if (empty($_FILES)) {
            $p_key = array_keys($request)[0];
            if (!in_array($p_key, array_keys($keys)) || in_array($p_key, ["logo_i", "ser_i", "vis_i"])) return returnAPI([], 1, "param_err");
            $key = $p_key;
            $val = $request[$key];
        } else {
            $f_key = array_keys($_FILES)[0];
            switch ($f_key) {
                case 'logo_i':
                case 'ser_i':
                case 'vis_i':
                    $key = $f_key;
                    $upload_str = $this->setChatImg($key);
                    if ($upload_str == "upload_err" || $upload_str == "file_err") {
                        return returnAPI([], 1, $upload_str);
                    } else {
                        $val = $upload_str;
                    }
                    break;
                default:
                    return returnAPI([], 1, "param_err");
            }
        }
        if (!isset($key)) return returnAPI([], 1, "param_err");
        if ($this->setWebset($keys[$key], $val)) return returnAPI([]);
        return returnAPI([], 1, "upd_err");
    }

    /**
     * 批次修改
     */
    function setList()
    {
        $key_arr = ['logo_i', 'ser_i', 'vis_i'];
        $keys = self::getChatroomSetKey();
        $request = $_POST;
        foreach ($keys as $k => $d) {
            if (in_array($k, $key_arr)) {
                if (isset($_FILES[$k]) && $_FILES[$k]['name'] != "") {
                    $upload_str = $this->setChatImg($k);
                    if ($upload_str == "upload_err" || $upload_str == "file_err") {
                        return returnAPI([], 1, $upload_str);
                    } else {
                        $datas[] = [$d, $upload_str];
                    }
                }
            } else {
                if (!isset($request[$k])) return returnAPI([], 1, "param_err");
                $datas[] = [$d, $request[$k]];
            }
        }
        if (empty($datas)) return returnAPI([], 1, "param_err");
        foreach ($datas as $data) {
            if (!$this->setWebset($data[0], $data[1])) return returnAPI([], 1, "upd_err");
        }
        return returnAPI([]);
    }

    private static function getChatroomSetKey(): array
    {
        $arr = [
            "win_t" => "window_title",
            "logo_i" => "logo_img",
            "logo_u" => "logo_url",
            "win_c" => "window_color",
            "news" => "news",
            "ser_i" => "service_img",
            "ser_c" => "service_color",
            "vis_i" => "visitor_img",
            "vis_c" => "visitor_color",
            "too_s" => "toolbar_set",
            "back_u" => "back_url"
        ];
        return $arr;
    }

    /**
     * update
     */
    private function setWebset($setkey, $value)
    {
        $wsDao = new web_set_dao;
        if (empty($wsDao->getWebSetListBySetKey($setkey))) return $wsDao->setWebSetAdd($setkey, $value);
        return $wsDao->setWebSetEdit($setkey, $value);
    }

    /**
     * 圖片上傳功能
     */
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
