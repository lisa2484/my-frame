<?php

namespace app\controllers;

include "./models/web_set_dao.php";
include "./models/messages_main_dao.php";
include "./models/user_online_status_dao.php";
include "./models/messages_dtl_dao.php";

use app\models\messages_main_dao;
use app\models\user_online_status_dao;
use app\models\web_set_dao;
use app\models\messages_dtl_dao;

class chat_guest_con
{
    private $autoservice_sw;
    private $wsDao;

    function init()
    {
        $this->autoservice_sw = $this->getWebSet("bot_autoservice_switch");
        if (isset($_SESSION["chatroomid"])) return returnAPI([], 1, "chatroom_exist");
        if (!$this->createChatRoom($name)) return returnAPI([], 1, "chatroom_create_err");
        $web_data = $this->getWebData();
        $data_arr = array(
            'webset' => $web_data,
            'service' => $name,
            'autoservice' => $this->autoservice_sw
        );
        return returnAPI($data_arr);
    }

    function getNewMessages()
    {
        if (isset($_POST["id"]) && !is_numeric($_POST["id"])) return returnAPI([], 1, "param_err");
        $mdDao = new messages_dtl_dao;
        if (!isset($_SESSION["chatroomid"])) return returnAPI([], 1, "chatroom_empty");
        $datas = $mdDao->getMsgByNewMessage($_SESSION["chatroomid"], $_POST["id"]);
        $returnArr = [];
        foreach ($datas as $data) {
            $arr["id"] = $data["id"];
            $arr["content"] = $data["content"];
            $arr["file"] = '/resources/img/chatroom/' . $_SESSION["chatroomid"] . '/' . $data["filename"];
            $arr["time"] = date("H:i:s", $data["time"]);
            $arr["service_name"] = $data["service_name"];
            $returnArr[] = $arr;
        }
        return returnAPI([$returnArr]);
    }

    function setGuestSpeak()
    {
    }

    function setGuestImg()
    {
    }

    private function createChatRoom(&$name)
    {
        if (!isset($_POST["env"])) {
            return false;
        }
        if (!isset($_POST["from"])) {
            return false;
        }
        $user = "";
        $user_name = "";
        if (empty($this->autoservice_sw)) {
            $uosDao = new user_online_status_dao;
            $users = $uosDao->getUserIsOnline();
            if (!empty($users)) {
                if (count($users) > 1) {
                    $i = random_int(0, (count($users) - 1));
                    $user = $users[$i]["account"];
                    $user_name = $users[$i]["user_name"];
                } else {
                    $user = $users[0]["account"];
                    $user_name = $users[0]["user_name"];
                }
            }
        } else {
            $user = "智能客服";
            $user_name = "智能客服";
        }
        $mmDao = new messages_main_dao;
        $id = 0;
        $insertArr = [];
        $insertArr["member_env"] = $_POST["env"];
        $insertArr["member_from"] = $_POST["env"];
        $insertArr["user_id"] = $user;
        $insertArr["start_time"] = time();
        $insertArr["end_time"] = time();
        $insertArr["member_ip"] = getRemoteIP();
        $mmDao->setMsgMainForChatroom($insertArr, $id);
        if (empty($id)) {
            return false;
        }
        $_SESSION["chatroomid"] = $id;
        $name = (empty($user_name) ? $user : $user_name);
        return true;
    }

    private function getWebSet($key)
    {
        if (!isset($this->wsDao)) $this->wsDao = new web_set_dao;
        $data = $this->wsDao->getWebSetListBySetKey($key);
        if (empty($data)) {
            return 0;
        } else {
            return $data[0]["value"];
        }
    }

    private function getWebData()
    {
        if (!isset($this->wsDao)) $this->wsDao = new web_set_dao;
        $datas = $this->wsDao->getWebSetList();
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
