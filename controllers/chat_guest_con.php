<?php

namespace app\controllers;

include "./models/web_set_dao.php";
include "./models/messages_main_dao.php";
include "./models/user_online_status_dao.php";
include "./models/messages_dtl_dao.php";
include "./models/autorepmsg_dao.php";
include "./models/searchautorep_dao.php";

use app\models\messages_main_dao;
use app\models\user_online_status_dao;
use app\models\web_set_dao;
use app\models\messages_dtl_dao;
use app\models\autorepmsg_dao;
use app\models\searchautorep_dao;

class chat_guest_con
{
    private $autoservice_sw;
    private $wsDao;

    function __construct()
    {
        $this->autoservice_sw = $this->getWebSet("bot_autoservice_switch");
    }

    function init()
    {
        if (isset($_SESSION["chatroomid"])) return returnAPI([], 1, "chatroom_exist");
        if (!$this->createChatRoom($name)) return returnAPI([], 1, "chatroom_create_err");
        $web_data = $this->getWebData();
        $data_arr = array(
            'chatroom_set' => $web_data,
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
            $arr["file"] = (empty($data["filename"]) ? "" : '/resources/img/chatroom/' . $_SESSION["chatroomid"] . '/' . $data["filename"]);
            $arr["time"] = date("H:i:s", $data["time"]);
            switch ($data["msg_from"]) {
                case 1:
                    $arr["type"] = "guest";
                    break;
                case 2:
                    $arr["type"] = "service";
                    break;
                case 3:
                    $arr["type"] = "bot";
            }
            $arr["service_name"] = ($data["msg_from"] == 3 ? "智能客服" : $data["service_name"]);
            $returnArr[] = $arr;
        }
        return returnAPI([$returnArr]);
    }

    function setGuestSpeak()
    {
        if (!isset($_POST["say"]) || ($_POST["say"] == "" && empty($_FILES))) return returnAPI([], 1, "param_err");
        $mmDao = new messages_main_dao;
        $data = $mmDao->getMsgByID($_SESSION["chatroomid"]);
        if (empty($data)) return returnAPI([], 1, "chatroom_empty");
        if (in_array($data[0]["status"], [2, 3])) return returnAPI([], 1, "chatroom_over");
        if (isset($_FILES["file"])) if (!updateImg($filename, "chatroom/" . $_SESSION["chatroomid"], "guest" . $_SESSION["chatroomid"])) return returnAPI([], 1, "upload_err");
        $mdDao = new messages_dtl_dao;
        $time = time();
        $mdinsert["main_id"] = $_SESSION["chatroomid"];
        $mdinsert["msg_from"] = 1;
        $mdinsert["content"] = $_POST["say"];
        $mdinsert["time"] = $time;
        if (isset($filename)) $mdinsert["filename"] = $filename;
        $id = 0;
        if (!$mdDao->setMsgInsert($mdinsert, $id)) return returnAPI([], 1, "chatroom_insert_err");
        $mmupdate["circle_count"] = 1;
        $mmupdate["end_time"] = $time;
        if (!$mmDao->setMsgUpdate($_SESSION["chatroomid"], $mmupdate)) return returnAPI([], 1, "chatroom_insert_err");
        $rep["reply"] = "";
        $rep["id"] = $id;
        if ($data[0]["user_id"] == "智能客服" && !empty($this->autoservice_sw)) {
            $bid = 0;
            $rep["reply"] = $this->botResponse($mdDao, $_POST["say"], $bid);
            if (!empty($bid)) $rep["id"] = $bid;
        }
        return returnAPI($rep);
    }

    // function getBotResponse()
    // {
    // }

    function setUnsetChatroom()
    {
        unset($_SESSION["chatroomid"]);
        return returnAPI([]);
    }

    private function botResponse(messages_dtl_dao $mdDao, string $say, int &$id = 0): string
    {
        $automsg = $this->getAutorepmsg($say);
        if ($automsg == "") {
            $sarDao = new searchautorep_dao;
            $req = $sarDao->getRepForBot();
            if (!empty($req)) $automsg = $req[0]["msg"];
        }
        if ($automsg != "") {
            $mdinsert["main_id"] = $_SESSION["chatroomid"];
            $mdinsert["msg_from"] = 3;
            $mdinsert["content"] = $automsg;
            $mdinsert["time"] = time();
            if (!$mdDao->setMsgInsert($mdinsert, $id)) return "";
        }
        return $automsg;
    }

    private function getAutorepmsg($say): string
    {
        $armDao = new autorepmsg_dao;
        $keys = $armDao->getMsgWhereTimeAndOnf();
        foreach ($keys as $d) {
            if (mb_strstr($say, $d["keyword"])) {
                return $d["msg"];
            }
        }
        return "";
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
        $time = time();
        $insertArr["member_env"] = $_POST["env"];
        $insertArr["member_from"] = $_POST["env"];
        $insertArr["user_id"] = $user;
        $insertArr["start_time"] = $time;
        $insertArr["end_time"] = $time;
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
