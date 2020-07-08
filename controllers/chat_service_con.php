<?php

namespace app\controllers;

include "./models/user_online_status_dao.php";
include "./models/messages_main_dao.php";
include "./models/usermsg_dao.php";
include "./models/messages_dtl_dao.php";

use app\models\messages_dtl_dao;
use app\models\messages_main_dao;
use app\models\user_online_status_dao;
use app\models\usermsg_dao;

class chat_service_con
{
    function init()
    {
        $uosDao = new user_online_status_dao;
        $mmDao = new messages_main_dao;
        $umDao = new usermsg_dao;
        $users = $this->getUserAllOnlineTyep($uosDao);
        $onlineType = $this->getUserOnlineType($users);
        $chatroom = $this->getChatroomType($mmDao);
        $usermsg = $this->getOftenMsg($umDao);
        return returnAPI(["online" => $onlineType, "service" => $users, "chatroom" => $chatroom, "usermsg" => $usermsg]);
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

    function setSpeak()
    {
        if (!isset($_POST["chatroom_id"]) || !is_numeric($_POST["chatroom_id"])) return returnAPI([], 1, "param_err");
        if (!isset($_POST["say"])) return returnAPI([], 1, "param_err");
        $cid = $_POST["chatroom_id"];
        $mdDao = new messages_dtl_dao;
        $id = $this->setChatroomDtlInsert($mdDao, $cid, $_POST["say"]);
        if (empty($id)) return returnAPI([], 1, "chatroom_insert_err");
        $mmDao = new messages_main_dao;
        $this->setChatroomAddCircleCount($mmDao, $cid);
        return returnAPI(["msg_id" => $id]);
    }

    function setChatRoomType()
    {
        if (!isset($_POST["chatroom_id"]) || !is_numeric($_POST["chatroom_id"])) return returnAPI([], 1, "param_err");
        if (!isset($_POST["status"]) || !in_array($_POST["status"], [1, 2, 3])) return returnAPI([], 1, "param_err");
        $cid = $_POST["chatroom_id"];
        $status = $_POST["status"];
        $mmDao = new messages_main_dao;
        if (!$mmDao->setMsgUpdate($cid, ["status" => $status])) return returnAPI([], 1, "upd_err");
        return returnAPI([]);
    }

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
        if ($mmDao->setMsgUpdate($cid, ["user_id" => $user[0]["account"]])) return returnAPI([]);
        return returnAPI([], 1, "service_transfer_err");
    }

    function setOnlineType()
    {
        if (!isset($_POST["switch"]) || !in_array($_POST["switch"], [0, 1])) return returnAPI([], 1, "param_err");
        $uosDao = new user_online_status_dao;
        if ($uosDao->setUserOnlineType($_SESSION["id"], $_POST["switch"])) return returnAPI([]);
        return returnAPI([], 1, "upd_err");
    }

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

    /**取得自己的在線狀態 */
    private function getUserOnlineType(array &$users): int
    {
        foreach ($users as $u) {
            if ($u["id"] == $_SESSION["id"]) return (empty($u["status"]) ? 0 : 1);
        }
        return 0;
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
        $usermsgs = $umDao->getUserMsg($_SESSION["id"]);
        if (!empty($usermsgs)) {
            foreach ($usermsgs as $data) {
                $arr["tag"] = $data["tag"];
                $arr["msg"] = $data["msg"];
                $reArr[] = $arr;
            }
        }
        return $reArr;
    }

    /**取得聊天室聊天訊息 */
    private function getChatroomNewMessage(messages_dtl_dao &$mdDao, int $id, int $mid): array
    {
        $datas = $mdDao->getMsgByNewMessage($id, $mid);
        $reArr = [];
        if (!empty($datas)) {
            foreach ($datas as $data) {
                $arr["id"] = $data["id"];
                $arr["content"] = $data["content"];
                $arr["file"] = (empty($data["filename"]) ? "" : '/resources/img/chatroom/' . $id . '/' . $data["filename"]);
                $arr["date"] = date("Y-m-d", $data["time"]);
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
                $reArr[] = $arr;
            }
        }
        return $reArr;
    }

    /**設定聊天室訊息數量++ */
    private function setChatroomAddCircleCount(messages_main_dao &$mmDao, int $id): bool
    {
        return $mmDao->setMsgUpdate($id, ["circle_count" => 1, "end_time" => time(), "rep_len" => time()]);
    }

    /**聊天室新增訊息 */
    private function setChatroomDtlInsert(messages_dtl_dao &$mdDao, int $cid, string $say): int
    {
        $id = 0;
        $insert["main_id"] = $cid;
        $insert["content"] = $say;
        $insert["msg_from"] = 2;
        $filename = "";
        if (updateImg($filename, "chatroom/" . $cid, $_SESSION["act"])) $insert["filename"] = $filename;
        $insert["time"] = time();
        $insert["service_name"] = ($_SESSION["name"] == "" ? $_SESSION["act"] : $_SESSION["name"]);
        if (empty($insert["content"]) && $filename) return 0;
        $mdDao->setMsgInsert($insert, $id);
        return $id;
    }

    /**取得聊天室資訊 */
    private function getChatroomData(messages_main_dao &$mmDao, int $cid)
    {
        $datas = $mmDao->getMsgByID($cid);
        if (empty($datas)) return [];
        $data = $datas[0];
        $arr["member_id"] = $data["member_id"];
        $arr["member_name"] = $data["member_name"];
        $arr["ip"] = $data["member_ip"];
        $arr["local"] = $data["member_loc"];
        $arr["env"] = $data["member_env"];
        $arr["from"] = $data["member_from"];
        return $arr;
    }
}
