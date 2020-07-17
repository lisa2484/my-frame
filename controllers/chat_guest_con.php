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
    private $bot_service_name = "智能客服";

    function __construct()
    {
        $this->autoservice_sw = $this->getWebSet("bot_autoservice_switch");
        $this->time = time();
    }

    /**取得初始 */
    function init()
    {
        $errmsg = "param_err";
        if (!isset($_SESSION["chatroomid"])) if (!$this->createChatRoom($name, $errmsg)) return returnAPI([], 1, $errmsg);
        if (!isset($name)) {
            $mmDao = new messages_main_dao;
            $data = $mmDao->getMsgDataForChatroom($_SESSION["chatroomid"]);
            if (empty($data)) return returnAPI([], 1, "chatroom_null");
            if (in_array($data[0]["status"], [2, 3])) {
                unset($_SESSION["chatroomid"]);
                if (!$this->createChatRoom($name, $errmsg)) return returnAPI([], 1, $errmsg);
            }
            $name = (empty($data[0]["user_name"]) ? $data[0]["user_id"] : $data[0]["user_name"]);
        }
        $web_data = $this->getWebData();
        $cmsDao = new chatroom_menu_dao;
        $menu_data = $cmsDao->getMenuSet();
        foreach ($menu_data as $k => $d) {
            $menu_data[$k]["filename"] = getImgUrl("chatroom_menu", $d["filename"]);
        }
        if ($name == $this->bot_service_name) {
            $asrDao = new autoservicerep_dao;
            $amsg = $asrDao->getResponseForParentId(0);
        } else {
            $wel_swi = $this->getWebSet("bot_welcome_switch");
            if (!empty($wel_swi)) $wel = $this->getWebSet("bot_welcome");
        }
        return returnAPI([
            'chatroom_id' => $_SESSION["chatroomid"],
            'chatroom_set' => $web_data,
            'chatroom_type' => ($name == $this->bot_service_name ? 'bot' : 'service'),
            'menu_set' => $menu_data,
            'service' => $name,
            'autoservice' => $this->autoservice_sw,
            'autoservice_msg' => (isset($amsg) ? $amsg : []),
            'welcome' => (isset($wel) ? html_entity_decode($wel) : ""),
            'ip' => getRemoteIP()
        ]);
    }

    function getAutoServicerep()
    {
        $asDao = new autoservicerep_dao;
        $datas = $asDao->getListForOnf();
        if (!empty($datas)) {
            $pIDs = [];
            foreach ($datas as $data) {
                $pIDs[$data["parent_id"]][] = $data["id"];
            }
            $sDatas = [];
            foreach ($datas as $data) {
                $sDatas[$data["id"]] = ["id" => $data["id"], "msg" => $data["msg"]];
            }
            $redata = [];
            $this->setDatas(0, $pIDs, $sDatas, $redata);
        }
        return returnAPI([$redata]);
    }

    private function setDatas(int $id, array &$pIDs, array &$datas, array &$rdata, array &$out = [])
    {
        if (in_array($id, array_keys($pIDs))) {
            foreach ($pIDs[$id] as $i) {
                $r = [];
                if (!in_array($id, $out)) {
                    $r = ["id" => $datas[$i]["id"], "msg" => $datas[$i]["msg"]];
                }
                $r["list"] = [];
                $this->setDatas($i, $pIDs, $datas, $r["list"], $out);
                if (empty($r["list"])) unset($r["list"]);
                $rdata[] = $r;
            }
        }
        $out[] = $id;
    }

    /**
     * 設定地區
     */
    function setLocal()
    {
        if (!isset($_SESSION["chatroomid"])) return returnAPI([], 1, "chatroom_empty");
        if (!isset($_POST["loc"])) return returnAPI([], 1, "param_err");
        $mmDao = new messages_main_dao;
        if ($mmDao->setMsgLocal($_SESSION["chatroomid"], $_POST["loc"])) return returnAPI([]);
        return returnAPI([], 1, "upd_err");
    }

    /**設定評價 */
    function setEvaluation()
    {
        if (!isset($_SESSION["chatroomid"])) return returnAPI([], 1, "chatroom_empty");
        if (!isset($_POST["eva"]) || !in_array($_POST["eva"], [1, 2, 3, 4, 5])) return returnAPI([], 1, "param_err");
        $mmDao = new messages_main_dao;
        if ($mmDao->setMsgUpdate($_SESSION["chatroomid"], ["evaluation" => $_POST["eva"]])) return returnAPI([]);
        return returnAPI([], 1, "upd_err");
    }

    /**取得新訊息 */
    function getNewMessages()
    {
        if (!isset($_POST["id"]) || !is_numeric($_POST["id"])) return returnAPI([], 1, "param_err");
        if (!isset($_SESSION["chatroomid"])) return returnAPI([], 1, "chatroom_empty");
        $mmDao = new messages_main_dao;
        $cdata = $mmDao->getMsgDataForChatroom($_SESSION["chatroomid"]);
        if (empty($cdata)) return returnAPI([], 1, "chatroom_null");
        if (in_array($cdata[0]["status"], [2, 3])) return returnAPI([], 1, "chatroom_over");
        $mdDao = new messages_dtl_dao;
        $datas = $mdDao->getMsgByMsgJsonUser($_SESSION["chatroomid"], $_POST["id"]);
        $returnArr = [];
        foreach ($datas as $data) {
            $arr = [
                "id" => $data["id"],
                "content" => (empty($data["type"]) ? $data["content"] : json_decode($data["content"], true)),
                "c_type" => (empty($data["type"]) ? "string" : "array"),
                "file" => (empty($data["filename"]) ? "" : getImgUrl('chatroom/' . $_SESSION["chatroomid"], $data["filename"])),
                "date" => date("Y-m-d", $data["time"]),
                "time" => date("H:i:s", $data["time"]),
                "service_img" => empty($data["service_img"]) ? "" : getImgUrl("", $data["service_img"])
            ];
            switch ($data["msg_from"]) {
                case 1:
                    $arr["type"] = "guest";
                    $arr["service_name"] = "";
                    break;
                case 2:
                    $arr["type"] = "service";
                    $arr["service_name"] = (empty($data["user_name"]) ? $data["service_name"] : $data["user_name"]);
                    break;
                case 3:
                    $arr["type"] = "bot";
                    $arr["service_name"] = $this->bot_service_name;
                    break;
                case 4:
                    $arr["type"] = "system";
                    $arr["service_name"] = "";
            }
            $returnArr[] = $arr;
        }
        return returnAPI([$returnArr]);
    }

    /**發話 */
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
        $id = $this->setMsgSaveByGuest($mdDao, 1, $_POST["say"], $filename);
        if (empty($id)) return returnAPI([], 1, "chatroom_insert_err");
        return returnAPI([
            "msg_id" => $id,
            "file" => (empty($filename) ? "" : getImgUrl("chatroom/" . $_SESSION["chatroomid"], $filename)),
            "date" => date("Y-m-d", $this->time),
            "time" => date("H:i:s", $this->time)
        ]);
    }

    /**智能客服 */
    function getAutoService()
    {
        if (!isset($_SESSION["chatroomid"])) return returnAPI([], 1, "chatroom_empty");
        $asrDao = new autoservicerep_dao;
        $sarDao = new searchautorep_dao;
        if (isset($_POST["id"])) {
            if (!is_numeric($_POST["id"])) return returnAPI([], 1, "param_err");
            $mdDao = new messages_dtl_dao;
            $data = $asrDao->getByMsgForID($_POST["id"]);
            if (empty($data)) return $this->repBotMsg($sarDao->getRepForBot());
            if (!$this->setMsgSave($mdDao, 1, $data[0]["msg"])) return returnAPI([], 1, "chatroom_insert_err");
            $msgs = $asrDao->getResponseForParentId($_POST["id"]);
            if (empty($msgs)) return $this->repBotMsg($sarDao->getRepForBot());
            return $this->repBotMsg($msgs);
        }
        if (isset($_POST["say"])) {
            if (strlen($_POST["say"]) < 1) return returnAPI([], 1, "chatroom_key_short");
            $mdDao = new messages_dtl_dao;
            if (!$this->setMsgSave($mdDao, 1, $_POST["say"])) return returnAPI([], 1, "chatroom_insert_err");
            $datas = $asrDao->getResponseForLink($_POST["say"]);
            if (empty($datas)) {
                $armDao = new autorepmsg_dao;
                $msg = $armDao->getMsgWhereTimeAndOnf($_POST["say"]);
                if (!empty($msg)) return $this->repBotMsg($msg[0]["msg"]);
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
        if ($mmDao->setMsgUpdate($_SESSION["chatroomid"], ["user_id" => $user[0]["account"]])) {
            $mdDao = new messages_dtl_dao;
            $this->setSystemMsg($mdDao, $_SESSION["chatroomid"], $this->bot_service_name . '已将聊天室转给其他客服');
            return returnAPI([]);
        }
        return returnAPI([], 1, "upd_err");
    }

    /**離開聊天室 */
    function setUnsetChatroom()
    {
        if (isset($_SESSION["chatroomid"]) && is_numeric($_SESSION["chatroomid"])) {
            $mmDao = new messages_main_dao;
            $mmDao->setMsgStatusOver($_SESSION["chatroomid"]);
            $mdDao = new messages_dtl_dao;
            $this->setSystemMsg($mdDao, $_SESSION["chatroomid"], '访客已离开聊天室');
            if (isset($_POST["eva"]) && in_array($_POST["eva"], [1, 2, 3, 4, 5])) {
                $mmDao->setMsgUpdate($_SESSION["chatroomid"], ["evaluation" => $_POST["eva"]]);
            }
        }
        unset($_SESSION["chatroomid"]);
        return returnAPI([]);
    }

    private function setSystemMsg(messages_dtl_dao &$mdDao, int $cid, string $msg)
    {
        $insert = [
            "main_id" => $cid,
            "content" => $msg,
            "msg_from" => 4,
            "time" => time()
        ];
        $mdDao->setMsgInsert($insert);
    }

    /**機器人回傳訊息與紀錄 */
    private function repBotMsg($say)
    {
        if (is_array($say)) {
            $mdDao = new messages_dtl_dao;
            $asrDao = new autoservicerep_dao;
            $last = true;
            foreach ($say as $s) {
                if (!empty($asrDao->getResponseForParentId($s["id"]))) {
                    $last = false;
                    break;
                }
            }
            $id = $this->setMsgSave($mdDao, 3, json_encode($say), "", 1);
            return returnAPI([
                "msg_id" => $id,
                "type" => "array",
                "msg" => $say,
                "date" => date("Y-m-d", $this->time),
                "time" => date("H:i:s", $this->time),
                "last" => ($last ? 1 : 0)
            ]);
        }
        if (is_string($say)) {
            $mdDao = new messages_dtl_dao;
            $id = $this->setMsgSave($mdDao, 3, $say);
            return returnAPI([
                "msg_id" => $id,
                "type" => "string",
                "msg" => $say,
                "date" => date("Y-m-d", $this->time),
                "time" => date("H:i:s", $this->time),
                "last" => 1
            ]);
        }
        return returnAPI([], 1, "autoservice_err");
    }

    /**儲存聊天紀錄 */
    private function setMsgSave(messages_dtl_dao &$mdDao, int $from, string $say, string $fileName = "", int $type = 0)
    {
        $id = 0;
        $insert = [
            "main_id" => $_SESSION["chatroomid"],
            "msg_from" => $from,
            "content" => (empty($type) ? $say : str_replace("\\", "\\\\", $say)),
            "type" => $type,
            "filename" => $fileName,
            "time" => $this->time
        ];
        $mdDao->setMsgInsert($insert, $id);
        return $id;
    }

    /**儲存聊天紀錄 */
    private function setMsgSaveByGuest(messages_dtl_dao &$mdDao, int $from, string $say, string $fileName = "", int $type = 0)
    {
        $id = 0;
        $insert = [
            "main_id" => $_SESSION["chatroomid"],
            "msg_from" => $from,
            "content" => (empty($type) ? $say : str_replace("\\", "\\\\", $say)),
            "type" => $type,
            "filename" => $fileName,
            "time" => $this->time
        ];
        $mdDao->setMsgInsertAddUnread($insert, $id);
        return $id;
    }

    /**建立聊天室 */
    private function createChatRoom(&$name, &$errmsg)
    {
        if (!isset($_POST["env"])) return false;
        if (!isset($_POST["from"])) return false;
        $user = "";
        $user_name = "";
        if (empty($this->autoservice_sw)) {
            $uosDao = new user_online_status_dao;
            $users = $uosDao->getUserIsOnline();
            if (empty($users)) {
                $errmsg = "chatroom_service_offline";
                return false;
            }
            $user = $users[0]["account"];
            $user_name = $users[0]["user_name"];
        } else {
            $user = $this->bot_service_name;
            $user_name = $this->bot_service_name;
        }
        $mmDao = new messages_main_dao;
        $id = 0;
        $time = $this->time;
        $insertArr = [
            "member_env" => $_POST["env"],
            "member_from" => $_POST["from"],
            "user_id" => $user,
            "start_time" => $time,
            "end_time" => $time,
            "member_ip" => getRemoteIP()
        ];
        if (isset($_POST["loc"]) && !empty($_POST["loc"])) $insertArr["member_loc"] = $_POST["loc"];
        $mmDao->setMsgMainForChatroom($insertArr, $id);
        if (empty($id)) {
            $errmsg = "chatroom_create_err";
            return false;
        }
        $_SESSION["chatroomid"] = $id;
        $name = (empty($user_name) ? $user : $user_name);
        return true;
    }

    /**取得設定 */
    private function getWebSet($key)
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
}
