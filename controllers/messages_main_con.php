<?php

namespace app\controllers;

include "./models/user_dao.php";
include "./models/messages_dtl_dao.php";
include "./models/messages_main_dao.php";

use app\models\user_dao;
use app\models\messages_dtl_dao;
use app\models\messages_main_dao;

class messages_main_con
{
    /**
     * 初始：客戶對話查詢_列表
     */
    function init()
    {
        $member = "";
        if (isset($_POST["member"])) $member = $_POST["member"];
        $name = "";
        if (isset($_POST["name"])) $name = $_POST["name"];
        $device = empty($_POST["device"]) ? "" : $_POST["device"];
        $ip = empty($_POST["ip"]) ? "" : $_POST["ip"];
        $adminname = empty($_POST["adminname"]) ? "" : $_POST["adminname"];

        if (empty($_POST["date"])) {
            $date_s = "";
            $date_e = "";
        } else {
            $date_s = strtotime($_POST["date"]);
            $date_e = $date_s + 86400;
        }

        if (!isset($_POST["page"])) return returnAPI([], 1, "param_err");
        if (!is_numeric($_POST["page"])) return returnAPI([], 1, "param_err");
        $page = $_POST["page"];
        if (!isset($_POST["limit"])) return returnAPI([], 1, "param_err");
        if (!is_numeric($_POST["limit"])) return returnAPI([], 1, "param_err");
        $limit = $_POST["limit"];

        $msgmain_arr = array();

        $userdao = new user_dao;
        $msgmainDao = new messages_main_dao;
        $msgmaintotal = $msgmainDao->getMessagesMainTotal($member, $name, $device, $ip, $adminname, $date_s, $date_e);
        $msgmaindata = $msgmainDao->getMessagesMain($member, $name, $device, $ip, $adminname, $date_s, $date_e, $page, $limit);

        $useracc = $userdao->getUserAcc();

        for ($i = 0; $i < count($msgmaindata); $i++) {
            foreach ($msgmaindata[$i] as $key => $value) {
                if ($key != "rep_len") {
                    $msgmain_arr[$i][$key] = $value;
                }
            }

            $st = $msgmaindata[$i]['start_time'];
            $et = $msgmaindata[$i]['end_time'];
            $rl = $msgmaindata[$i]['rep_len'];
            $rl_time = ($rl != 0) ? ($rl - $st) : "0";

            $msgmain_arr[$i]['start_time'] = date("Y-m-d H:i:s", $st);
            $msgmain_arr[$i]['end_time'] = date("Y-m-d H:i:s", $et);

            $msgmain_arr[$i]['time_length'] = $this->getDateTime(($et - $st));
            $msgmain_arr[$i]['first_time'] = $this->getDateTime($rl_time);
        }

        $data_arr = array(
            'total' => $msgmaintotal,
            'totalpage' => ceil($msgmaintotal / $limit),
            'page' => $page,
            'acclist' => $useracc,
            'list' => $msgmain_arr
        );

        return returnAPI($data_arr);
    }

    /**
     * 匯出功能
     */
    function getCsv()
    {
        set_time_limit(0);
        ini_set("memory_limit", "512M");
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename=messages_main.csv');
        $msgmDao = new messages_main_dao;
        $where = [];
        if (isset($_POST["member"]) && $_POST["member"] != "") $where["member_id"] = $_POST["member"];
        if (isset($_POST["name"]) && $_POST["name"] != "") $where["member_name"] = $_POST["name"];
        if (isset($_POST["device"]) && $_POST["device"] != "") $where["member_env"] = $_POST["device"];
        if (isset($_POST["ip"]) && !empty($_POST["ip"])) $where["member_ip"] = $_POST["ip"];
        if (isset($_POST["adminname"]) && $_POST["adminname"] != "") $where["user_id"] = $_POST["adminname"];
        if (isset($_POST["date"]) && !empty($_POST["date"])) $where["date"] = $_POST["date"];
        $datas = $msgmDao->getMsgMainForNotLimit($where);
        $file = fopen("php://output", "w");
        $title = $this->getTitle();
        $status = $this->getStatus();
        fputcsv($file, $title);
        foreach ($datas as $data) {
            $arr = [];
            $arr[] = $data["id"];
            $arr[] = $status[$data["status"]];
            $arr[] = $data["member_id"];
            $arr[] = $data["member_name"];
            $arr[] = $data["member_env"];
            $arr[] = $data["member_ip"];
            $arr[] = $data["member_loc"];
            $arr[] = $data["member_from"];
            $arr[] = $data["user_id"];
            $arr[] = date("Y-m-d H:i:s", $data["start_time"]);
            $arr[] = date("Y-m-d H:i:s", $data["end_time"]);
            $arr[] = $this->getDateTime($data["end_time"] - $data["start_time"]);
            $arr[] = $this->getDateTime($data["rep_len"] - $data["start_time"]);
            $arr[] = $data["circle_count"];
            $arr[] = $data["evaluation"];
            fputcsv($file, $arr);
        }
        fclose($file);
    }

    /**
     * 匯出功能_欄位標題名稱
     */
    private function getTitle()
    {
        $arr[] = "序号";
        $arr[] = "状态";
        $arr[] = "会员帐号";
        $arr[] = "真实姓名";
        $arr[] = "环境";
        $arr[] = "IP";
        $arr[] = "地区";
        $arr[] = "来源";
        $arr[] = "客服帐号";
        $arr[] = "开始时间";
        $arr[] = "结束时间";
        $arr[] = "对话时长";
        $arr[] = "首次响应时长";
        $arr[] = "讯息回合数";
        $arr[] = "评价";
        return $arr;
    }

    /**
     * 匯出功能_狀態對應名稱
     */
    private function getStatus()
    {
        $arr[0] = "等待对话";
        $arr[1] = "处理中";
        $arr[2] = "处理完毕";
        $arr[3] = "垃圾讯息";
        return $arr;
    }

    /**
     * 時間換算 00:00:00
     */
    private function getDateTime($time)
    {
        $hour = str_pad(floor($time / 3600), 2, "0", STR_PAD_LEFT);
        $time = $time % 3600;
        $minute = str_pad(floor($time / 60), 2, "0", STR_PAD_LEFT);
        $second = str_pad(($time % 60), 2, "0", STR_PAD_LEFT);

        return $hour . ':' . $minute . ':' . $second;
    }

    /**
     * 客戶對話查詢_單筆查詢
     */
    function getMsgRecord()
    {
        if (!isset($_POST["mainid"])) return returnAPI([], 1, "param_err");
        if (!is_numeric($_POST["mainid"])) return returnAPI([], 1, "param_err");
        $mainid = $_POST["mainid"];

        $msgdtl_arr = array();

        $msgdtlDao = new messages_dtl_dao;

        $msgdtldata = $msgdtlDao->getMessagesMainRecord($mainid);

        foreach ($msgdtldata as $data) {
            $arr = [
                'id' => $data['id'],
                'content' => empty($data["type"]) ? $data['content'] : json_decode($data['content'], true),
                'c_type' => empty($data["type"]) ? 'string' : 'array',
                'file' => (empty($data['filename']) ? "" : getImgUrl('chatroom/' . $data["main_id"], $data['filename'])),
                'date' => date("Y-m-d", $data['time']),
                'time' => date("H:i:s", $data['time']),
                'service_name' => empty($data['service_name']) ? $data["service_act"] : $data['service_name'],
                'service_img' => (empty($data['service_img']) ? "" : getImgUrl('', $data['service_img']))
            ];
            switch ($data['msg_from']) {
                case '1':
                    $arr["type"] = "guest";
                    break;
                case '2':
                    $arr["type"] = "service";
                    break;
                case '3':
                    $arr["type"] = "bot";
                    break;
                case '4':
                    $arr["type"] = "system";
            }
            $msgdtl_arr[] = $arr;
        }

        return returnAPI($msgdtl_arr);
    }
}
