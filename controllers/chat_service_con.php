<?php

namespace app\controllers;

include "./models/user_online_status_dao.php";
include "./models/messages_main_dao.php";
include "./models/usermsg_dao.php";
include "./models/messages_dtl_dao.php";
include "./models/user_dao.php";
include "./models/web_set_dao.php";

use app\models\messages_dtl_dao;
use app\models\messages_main_dao;
use app\models\user_dao;
use app\models\user_online_status_dao;
use app\models\usermsg_dao;
use app\models\web_set_dao;

class chat_service_con
{
    private $time;

    function __construct()
    {
        $this->time = time();
    }

    function init()
    {
        $uosDao = new user_online_status_dao;
        $mmDao = new messages_main_dao;
        $umDao = new usermsg_dao;
        $users = $this->getUserAllOnlineTyep($uosDao);
        $onlineType = $uosDao->getUserOnline($_SESSION["id"]);
        $chatroom = $this->getChatroomType($mmDao);
        $usermsg = $this->getOftenMsg($umDao);
        return returnAPI([
            "online" => $onlineType,
            "service" => $users,
            "chatroom" => $chatroom,
            "usermsg" => $usermsg,
            "chatroom_set" => $this->getWebData()
        ]);
    }

    function getChatroomAndServiceStatus()
    {
        $uosDao = new user_online_status_dao;
        $mmDao = new messages_main_dao;
        $users = $this->getUserAllOnlineTyep($uosDao);
        $chatroom = $this->getChatroomType($mmDao);
        return returnAPI(["service" => $users, "chatroom" => $chatroom]);
    }

    function getChatRooom()
    {
        if (!isset($_POST["chatroom_id"]) || !is_numeric($_POST["chatroom_id"])) return returnAPI([], 1, "param_err");
        $cid = $_POST["chatroom_id"];
        $id = 0;
        if (isset($_POST["msg_id"]) && is_numeric($_POST["msg_id"])) $id = $_POST["msg_id"];
        $mmDao = new messages_main_dao;
        $chatroom = $this->getChatroomData($mmDao, $cid);
        $mdDao = new messages_dtl_dao;
        $msgs = $this->getChatroomNewMessage($mdDao, $cid, $id);
        return returnAPI(["chatroom_data" => $chatroom, "message" => $msgs]);
    }

    /**
     * 刷新
     */
    function getNewMessages()
    {
        if (!isset($_POST["chatroom_id"]) || !is_numeric($_POST["chatroom_id"])) return returnAPI([], 1, "param_err");
        if (!isset($_POST["msg_id"]) || !is_numeric($_POST["msg_id"])) return returnAPI([], 1, "param_err");
        $cid = $_POST["chatroom_id"];
        $mid = $_POST["msg_id"];
        $mdDao = new messages_dtl_dao;
        $msgs = $this->getChatroomNewMessage($mdDao, $cid, $mid);
        return returnAPI([$msgs]);
    }

    /**
     * 發話
     */
    function setSpeak()
    {
        if (!isset($_POST["chatroom_id"]) || !is_numeric($_POST["chatroom_id"])) return returnAPI([], 1, "param_err");
        if (!isset($_POST["say"])) return returnAPI([], 1, "param_err");
        $cid = $_POST["chatroom_id"];
        $filename = "";
        $id = $this->setChatroomDtlInsert($cid, $_POST["say"], $filename);
        if (empty($id)) return returnAPI([], 1, "chatroom_insert_err");
        return returnAPI([
            "msg_id" => $id,
            "file" => (empty($filename) ? "" : getImgUrl("chatroom/" . $cid, $filename)),
            "date" => date("Y-m-d", $this->time),
            "time" => date("H:i:s", $this->time)
        ]);
    }

    /**
     * 設定聊天室狀態(處理中,處理完成,垃圾訊息)
     */
    function setChatRoomType()
    {
        if (!isset($_POST["chatroom_id"]) || !is_numeric($_POST["chatroom_id"])) return returnAPI([], 1, "param_err");
        if (!isset($_POST["status"]) || !in_array($_POST["status"], [1, 2, 3])) return returnAPI([], 1, "param_err");
        $cid = $_POST["chatroom_id"];
        $status = $_POST["status"];
        $mmDao = new messages_main_dao;
        if (!$mmDao->setMsgUpdate($cid, ["status" => $status])) return returnAPI([], 1, "upd_err");
        if ($status == 1) {
            $mdDao = new messages_dtl_dao;
            $this->setSystemMsg($mdDao, $cid, '客服"' . (empty($_SESSION["name"]) ? $_SESSION["act"] : $_SESSION["name"]) . '"已加入聊天');
        }
        return returnAPI([]);
    }

    /**
     * 轉交聊天室
     */
    function setTransfer()
    {
        if (!isset($_POST["chatroom_id"]) || !is_numeric($_POST["chatroom_id"])) return returnAPI([], 1, "param_err");
        if (!isset($_POST["id"]) || !is_numeric($_POST["id"])) return returnAPI([], 1, "param_err");
        $cid = $_POST["chatroom_id"];
        $uid = $_POST["id"];
        $uosDao = new user_online_status_dao;
        $user = $uosDao->getUserOnlineForTransfer($uid);
        if (empty($user)) return returnAPI([], 1, "service_empty");
        $mmDao = new messages_main_dao;
        if ($mmDao->setMsgUpdate($cid, ["status" => 0, "user_id" => $user[0]["account"]])) {
            $mdDao = new messages_dtl_dao;
            $this->setSystemMsg($mdDao, $cid, '客服"' . (empty($_SESSION["name"]) ? $_SESSION["act"] : $_SESSION["name"]) . '"已将聊天室转给其他客服');
            return returnAPI([]);
        }
        return returnAPI([], 1, "service_transfer_err");
    }

    /**
     * 設定上線狀態
     */
    function setOnlineType()
    {
        if (!isset($_POST["switch"]) || !in_array($_POST["switch"], [0, 1])) return returnAPI([], 1, "param_err");
        $uosDao = new user_online_status_dao;
        if ($uosDao->setUserOnlineType($_SESSION["id"], $_POST["switch"])) return returnAPI([]);
        return returnAPI([], 1, "upd_err");
    }

    /**
     * 設定訪客資料
     */
    function setGuestData()
    {
        if (!isset($_POST["chatroom_id"]) || !is_numeric($_POST["chatroom_id"])) return returnAPI([], 1, "param_err");
        $update = [];
        if (isset($_POST["member"])) $update["member_id"] = $_POST["member"];
        if (isset($_POST["name"])) $update["member_name"] = $_POST["name"];
        if (isset($_POST["local"])) $update["member_loc"] = $_POST["local"];
        if (empty($update)) return returnAPI([], 1, "param_empty");
        $mmDao = new messages_main_dao;
        if ($mmDao->setMsgUpdate($_POST["chatroom_id"], $update)) return returnAPI([]);
        return returnAPI([], 1, "upd_err");
    }

    /**取得所有客服的在線狀態 */
    private function getUserAllOnlineTyep(user_online_status_dao &$uosDao): array
    {
        return $uosDao->getAllUserOnlineType();
    }

    /**取得所有自己尚未完成的聊天室 */
    private function getChatroomType(messages_main_dao &$mmDao): array
    {
        return $mmDao->getMsgForNotOverByAcount();
    }

    /**取得自己的常用語 */
    private function getOftenMsg(usermsg_dao &$umDao): array
    {
        $reArr = [];
        $arr = [];
        $usermsgs = $umDao->getUserMsg($_SESSION["id"]);
        if (!empty($usermsgs)) {
            foreach ($usermsgs as $data) {
                $arr[$data["tag"]][] = $data["msg"];
            }
            $id = 1;
            foreach ($arr as $k => $d) {
                $tar = [];
                $tar["tag"] = $k;
                $tar["id"] = $id++;
                $t = [];
                foreach ($d as $c) {
                    $t["id"] = $id++;
                    $t["tag"] = $c;
                    $tar["content"][] = $t;
                }
                $reArr[] = $tar;
            }
        }
        return $reArr;
    }

    /**取得聊天室聊天訊息 */
    private function getChatroomNewMessage(messages_dtl_dao &$mdDao, int $mid, int $id): array
    {
        $reArr = [];
        $datas = $mdDao->getMsgByNew($mid, $id);
        if (!empty($datas)) {
            $mmDao = new messages_main_dao;
            $mmDao->setMsgUpdate($mid, ["unread" => 0]);
            foreach ($datas as $data) {
                $arr = [
                    "id" => $data["id"],
                    "content" => (empty($data["type"]) ? $data["content"] : json_decode($data["content"], true)),
                    "c_type" => (empty($data["type"]) ? "string" : "array"),
                    "file" => (empty($data["filename"]) ? "" : getImgUrl('chatroom/' . $mid, $data["filename"])),
                    "date" => date("Y-m-d", $data["time"]),
                    "time" => date("H:i:s", $data["time"]),
                    "service_name" => ($data["msg_from"] == 3 ? "智能客服" : (empty($data["service_name"]) ? $data["service_act"] : $data["service_name"])),
                    "service_img" => (empty($data["service_img"]) ? "" : getImgUrl("", $data["service_img"]))
                ];
                switch ($data["msg_from"]) {
                    case 1:
                        $arr["type"] = "guest";
                        break;
                    case 2:
                        $arr["type"] = "service";
                        break;
                    case 3:
                        $arr["type"] = "bot";
                        break;
                    case 4:
                        $arr["type"] = "system";
                }
                $reArr[] = $arr;
            }
        }
        return $reArr;
    }

    /**
     * 新增系統訊息
     */
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

    /**聊天室新增訊息 */
    private function setChatroomDtlInsert(int $cid, string $say, string &$filename = ""): int
    {
        $mmDao = new messages_main_dao;
        $mdDao = new messages_dtl_dao;
        $uDao = new user_dao;
        $user = $uDao->getUserByID($_SESSION["id"]);
        $uImg = isset($user[0]["img_name"]) ? $user[0]["img_name"] : "";
        $id = 0;
        $insert = [
            "main_id" => $cid,
            "content" => $say,
            "msg_from" => 2,
            "time" => time(),
            "service_act" => $_SESSION["act"],
            "service_name" => $_SESSION["name"],
            "service_img" => $uImg
        ];
        if (updateImg($filename, "chatroom/" . $cid, $_SESSION["act"])) $insert["filename"] = $filename;
        $uDao = new user_dao;
        $img = $uDao->getUserPhoto($_SESSION["id"]);
        $insert["service_img"] = $img;
        if (empty($insert["content"]) && $filename) return 0;
        $mdDao->setMsgInsert($insert, $id);
        $mmDao->setMsgUpdate($cid, ["rep_len" => time()]);
        return $id;
    }

    /**取得聊天室資訊 */
    private function getChatroomData(messages_main_dao &$mmDao, int $cid)
    {
        $datas = $mmDao->getMsgByID($cid);
        if (empty($datas)) return [];
        $data = $datas[0];
        $arr = [
            "member_id" => $data["member_id"],
            "member_name" => $data["member_name"],
            "ip" => $data["member_ip"],
            "local" => $data["member_loc"],
            "env" => $data["member_env"],
            "from" => $data["member_from"]
        ];
        return $arr;
    }

    /**取得聊天室設定 */
    private function getWebData()
    {
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
                    switch ($k) {
                        case "too_s":
                            $repArr[$k] = html_entity_decode($rDatas[$d]);
                            break;
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
            "win_c" => "window_color",
            "ser_i" => "service_img",
            "ser_c" => "service_color",
            "vis_i" => "visitor_img",
            "vis_c" => "visitor_color",
            "too_s" => "toolbar_set"
        ];
        return $arr;
    }
}
