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
        if (!isset($_POST["page"]) || !is_numeric($_POST["page"])) return returnAPI([], 1, "param_err");
        if (!isset($_POST["limit"]) || !is_numeric($_POST["limit"])) return returnAPI([], 1, "param_err");
        $page = $_POST["page"];
        $limit = $_POST["limit"];
        $member = "";
        $name = "";
        $device = "";
        $ip = "";
        $adminname = "";
        $date_s = "";
        $date_e = "";
        if (isset($_POST["member"]) && $_POST["member"] != "") $member = $_POST["member"];
        if (isset($_POST["name"]) && $_POST["name"] != "") $name = $_POST["name"];
        if (isset($_POST["device"]) && !empty($_POST["device"])) $device = $_POST["device"];
        if (isset($_POST["ip"]) && !empty($_POST["ip"])) $ip = $_POST["ip"];
        if (isset($_POST["adminname"]) && $_POST["adminname"] != "") $adminname = $_POST["adminname"];
        if (isset($_POST["date"]) && !empty($_POST["date"])) {
            $date_s = strtotime($_POST["date"]);
            $date_e = $date_s + 86400;
        }
        $msgmain_arr = [];
        $userdao = new user_dao;
        $msgmainDao = new messages_main_dao;
        $msgmaintotal = $msgmainDao->getMessagesMainTotal($member, $name, $device, $ip, $adminname, $date_s, $date_e);
        $msgmaindata = $msgmainDao->getMessagesMain($member, $name, $device, $ip, $adminname, $date_s, $date_e, $page, $limit);
        $useracc = $userdao->getUserAcc();
        if (!empty($msgmaindata)) {
            foreach ($msgmaindata as $i => $d) {
                foreach ($d as $key => $value) {
                    if ($key != "rep_len") {
                        $msgmain_arr[$i][$key] = $value;
                    }
                }
                $st = $d['start_time'];
                $et = $d['end_time'];
                $rl = $d['rep_len'];
                $rl_time = ($rl != 0) ? ($rl - $st) : "0";
                $msgmain_arr[$i]['start_time'] = date("Y-m-d H:i:s", $st);
                $msgmain_arr[$i]['end_time'] = date("Y-m-d H:i:s", $et);
                $msgmain_arr[$i]['time_length'] = $this->getDateTime(($et - $st));
                $msgmain_arr[$i]['first_time'] = $this->getDateTime($rl_time);
            }
        }
        $data_arr = [
            'total' => $msgmaintotal,
            'totalpage' => ceil($msgmaintotal / $limit),
            'page' => $page,
            'acclist' => $useracc,
            'list' => $msgmain_arr
        ];
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
            $arr = [
                $data["id"],
                $status[$data["status"]],
                $data["member_id"],
                $data["member_name"],
                $data["member_env"],
                $data["member_ip"],
                $data["member_loc"],
                $data["member_from"],
                $data["user_id"],
                date("Y-m-d H:i:s", $data["start_time"]),
                date("Y-m-d H:i:s", $data["end_time"]),
                $this->getDateTime($data["end_time"] - $data["start_time"]),
                $this->getDateTime($data["rep_len"] - $data["start_time"]),
                $data["circle_count"],
                $data["evaluation"]
            ];
            fputcsv($file, $arr);
        }
        fclose($file);
    }

    /**
     * 匯出功能_欄位標題名稱
     */
    private function getTitle()
    {
        return [
            "序号",
            "状态",
            "会员帐号",
            "真实姓名",
            "环境",
            "IP",
            "地区",
            "来源",
            "客服帐号",
            "开始时间",
            "结束时间",
            "对话时长",
            "首次响应时长",
            "讯息回合数",
            "评价"
        ];
    }

    /**
     * 匯出功能_狀態對應名稱
     */
    private function getStatus()
    {
        return [
            0 => "等待对话",
            1 => "处理中",
            2 => "处理完毕",
            3 => "垃圾讯息"
        ];
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
        if (!isset($_POST["mainid"]) || !is_numeric($_POST["mainid"])) return returnAPI([], 1, "param_err");
        $mainid = $_POST["mainid"];
        $msgdtl_arr = [];
        $msgdtlDao = new messages_dtl_dao;
        $msgdtldata = $msgdtlDao->getMessagesMainRecord($mainid);
        if (!empty($msgdtldata)) {
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
        }
        return returnAPI($msgdtl_arr);
    }
}
