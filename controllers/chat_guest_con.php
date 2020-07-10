<?php

namespace app\controllers;

include "./models/web_set_dao.php";
include "./models/messages_main_dao.php";
include "./models/user_online_status_dao.php";
include "./models/messages_dtl_dao.php";
include "./models/autorepmsg_dao.php";
include "./models/searchautorep_dao.php";
include "./models/autoservicerep_dao.php";
include "./models/chatroom_menu_dao.php";

use app\models\messages_main_dao;
use app\models\user_online_status_dao;
use app\models\web_set_dao;
use app\models\messages_dtl_dao;
use app\models\autorepmsg_dao;
use app\models\autoservicerep_dao;
use app\models\searchautorep_dao;
use app\models\chatroom_menu_dao;

class chat_guest_con
{
    private $autoservice_sw;
    private $wsDao;
    private $time;

    function __construct()
    {
        $this->autoservice_sw = $this->getWebSet("bot_autoservice_switch");
        $this->time = time();
    }

    function init()
    {
        if (!isset($_SESSION["chatroomid"])) if (!$this->createChatRoom($name)) return returnAPI([], 1, "chatroom_create_err");
        if (!isset($name)) {
            $mmDao = new messages_main_dao;
            $data = $mmDao->getMsgByID($_SESSION["chatroomid"]);
            if (empty($data)) return returnAPI([], 1, "chatroom_null");
            $name = $data[0]["user_id"];
        }
        $web_data = $this->getWebData();
        $cmsDao = new chatroom_menu_dao;
        $menu_data = $cmsDao->getMenuSet();
        if (!empty($this->autoservice_sw)) {
            $wel_swi = $this->getWebSet("bot_welcome_switch");
            if (!empty($wel_swi)) $wel = $this->getWebSet("bot_welcome");
        }
        if ($name == "智能客服") {
            $asrDao = new autoservicerep_dao;
            $amsg = $asrDao->getResponseForParentId(0);
        }
        $data_arr = array(
            'chatroom_id' => $_SESSION["chatroomid"],
            'chatroom_set' => $web_data,
            'chatroom_type' => ($name == "智能客服" ? 'bot' : 'service'),
            'menu_set' => $menu_data,
            'service' => $name,
            'autoservice' => $this->autoservice_sw,
            'autoservice_msg' => (isset($amsg) ? $amsg : []),
            'welcome' => (isset($wel) ? $wel : "")
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
            $arr["file"] = (empty($data["filename"]) ? "" : getImgUrl('chatroom/' . $_SESSION["chatroomid"], $data["filename"]));
            $arr["date"] = date("Y-m-d", $data["time"]);
            $arr["time"] = date("H:i:s", $data["time"]);
            switch ($data["msg_from"]) {
                case 1:
                    $arr["type"] = "guest";
                    $arr["service_name"] = "";
                    break;
                case 2:
                    $arr["type"] = "service";
                    $arr["service_name"] = $data["service_name"];
                    break;
                case 3:
                    $arr["type"] = "bot";
                    $arr["service_name"] = "智能客服";
            }
            $returnArr[] = $arr;
        }
        return returnAPI([$returnArr]);
    }

    function setGuestSpeak()
    {
        if (!isset($_POST["say"]) || ($_POST["say"] == "" && empty($_FILES))) return returnAPI([], 1, "param_err");
        $mmDao = new messages_main_dao;
        if (!isset($_SESSION["chatroomid"])) return returnAPI([], 1, "chatroom_empty");
        $data = $mmDao->getMsgByID($_SESSION["chatroomid"]);
        if (empty($data)) return returnAPI([], 1, "chatroom_empty");
        if (in_array($data[0]["status"], [2, 3])) return returnAPI([], 1, "chatroom_over");
        $filename = "";
        if (isset($_FILES["file"])) if (!updateImg($filename, "chatroom/" . $_SESSION["chatroomid"], "guest" . $_SESSION["chatroomid"])) return returnAPI([], 1, "upload_err");
        $mdDao = new messages_dtl_dao;
        $id = $this->setMsgSave($mdDao, 1, $_POST["say"], $filename);
        if (empty($id)) return returnAPI([], 1, "chatroom_insert_err");
        $mmupdate["circle_count"] = 1;
        $mmupdate["end_time"] = $this->time;
        if (!$mmDao->setMsgUpdate($_SESSION["chatroomid"], $mmupdate)) return returnAPI([], 1, "chatroom_insert_err");
        return returnAPI(["msg_id" => $id]);
    }

    /**智能客服 */
    function getAutoService()
    {
        if (!isset($_SESSION["chatroomid"])) return returnAPI([], 1, "chatroom_empty");
        $asrDao = new autoservicerep_dao;
        $sarDao = new searchautorep_dao;
        if (isset($_POST["id"])) {
            if (!is_numeric($_POST["id"])) return returnAPI([], 1, "param_err");
            $msgs = $asrDao->getResponseForParentId($_POST["id"]);
            if (empty($msgs)) {
                return $this->repBotMsg($sarDao->getRepForBot());
            }
            return $this->repBotMsg($msgs);
        }
        if (isset($_POST["say"])) {
            if (strlen($_POST["say"]) < 1) return returnAPI([], 1, "chatroom_key_short");
            $datas = $asrDao->getResponseForLink($_POST["say"]);
            if (empty($datas)) {
                $armDao = new autorepmsg_dao;
                $msg = $armDao->getMsgByChatroom($_POST["say"]);
                if (!empty($msg)) return $this->repBotMsg($msg);
                return $this->repBotMsg($sarDao->getRepForBot());
            }
            foreach ($datas as $d) {
                $m = $asrDao->getResponseForParentId($d["id"]);
                if (!empty($m)) return $this->repBotMsg($m);
            }
            return $this->repBotMsg($sarDao->getRepForBot());
        }
        return returnAPI([], 1, "param_err");
    }

    /**轉交人工 */
    function setTransfer()
    {
        if (!isset($_SESSION["chatroomid"])) return returnAPI([], 1, "chatroom_empty");
        $uosDao = new user_online_status_dao;
        $user = $uosDao->getUserIsOnline();
        if (empty($user)) return returnAPI([], 1, "chatroom_service_offline");
        $mmDao = new messages_main_dao;
        if ($mmDao->setMsgUpdate($_SESSION["chatroomid"], ["user_id" => $user[0]["account"]])) return returnAPI([]);
        return returnAPI([], 1, "upd_err");
    }

    /**離開聊天室 */
    function setUnsetChatroom()
    {
        unset($_SESSION["chatroomid"]);
        return returnAPI([]);
    }

    /**機器人回傳訊息與紀錄 */
    private function repBotMsg($say)
    {
        if (is_array($say)) {
            foreach ($say as $d) {
                $arr[] = $d["msg"];
            }
            $id = $this->setMsgSave(new messages_dtl_dao, 3, implode("<br>", $arr));
            return returnAPI(["msg_id" => $id, "type" => "array", "msg" => $say]);
        }
        if (is_string($say)) {
            $id = $this->setMsgSave(new messages_dtl_dao, 3, $say);
            return returnAPI(["msg_id" => $id, "type" => "string", "msg" => $say]);
        }
        return returnAPI([], 1, "autoservice_err");
    }

    /**儲存聊天紀錄 */
    private function setMsgSave(messages_dtl_dao &$mdDao, int $from, string $say, string $fileName = "")
    {
        $id = 0;
        $insert["main_id"] = $_SESSION["chatroomid"];
        $insert["msg_from"] = $from;
        $insert["content"] = $say;
        $insert["filename"] = $fileName;
        $insert["time"] = $this->time;
        $mdDao->setMsgInsert($insert, $id);
        return $id;
    }

    /**建立聊天室 */
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
        $time = $this->time;
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

    /**取得設定 */
    private function getWebSet($key): int
    {
        if (!isset($this->wsDao)) $this->wsDao = new web_set_dao;
        $data = $this->wsDao->getWebSetListBySetKey($key);
        if (!isset($data[0]["value"]) || empty($data[0]["value"])) {
            return 0;
        } else {
            return $data[0]["value"];
        }
    }

    /**取得聊天室設定 */
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
                    switch ($k) {
                        case "too_s":
                            $repArr[$k] = html_entity_decode($rDatas[$d]);
                            break;
                        case "logo_i":
                        case "ser_i":
                        case "vis_i":
                            $repArr[$k] = getImgUrl("", $rDatas[$d]);
                            break;
                        default:
                            $repArr[$k] = $rDatas[$d];
                    }
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
