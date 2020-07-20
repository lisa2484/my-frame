<?php

namespace app\controllers;

include "./models/messages_main_dao.php";
include "./models/user_online_status_dao.php";

use app\models\messages_main_dao;
use app\models\user_online_status_dao;

class dashboard_con
{
    private $time_now;
    private $time_today;
    private $autoname = "智能客服";

    function __construct()
    {
        $this->time_now = time();
        $this->time_today = strtotime(date("Y-m-d"));
    }

    /**
     * 初始：儀表板各資訊、超級管理員切換客服上線狀態
     */
    function init()
    {
        //抓登入者權限
        $aut_status = [];

        $user_aut = $_SESSION["aut"];
        if ($user_aut == 1) {
            $uosDao = new user_online_status_dao;
            $aut_status = $uosDao->getAllUserOnlineType();
        }

        $gss_arr = $this->getServiceStatus();       //客服
        $gcs_arr = $this->getCustomerStatus();      //客戶
        $gti_arr = $this->getTodayInfo();           //今日實時統計
        $gipa_arr = $this->getIPArea(0);            //IP地區
        $gs_arr = $this->getSource(0);              //來源網址
        $gd_arr = $this->getDevice(0);              //使用環境

        $data_arr = array(
            'serviceOnline' => $aut_status,
            'service' => $gss_arr,
            'customer' => $gcs_arr,
            'todayinfo' => $gti_arr,
            'iparea' => $gipa_arr,
            'source' => $gs_arr,
            'device' => $gd_arr
        );

        return returnAPI($data_arr);
    }

    /**
     * 切換客服上線狀態
     */
    function setServiceSwitch()
    {
        if (!isset($_POST["userid"]) || !is_numeric($_POST["userid"])) return returnAPI([], 1, "param_err");

        $uosDao = new user_online_status_dao;

        if ($uosDao->setUserStatus($_POST["userid"])) return returnAPI([]);
        return returnAPI([], 1, "upd_err");
    }

    /**
     * 客服狀態：在線/離線
     */
    private function getServiceStatus()
    {
        $uosDao = new user_online_status_dao;

        $goc = $uosDao->getOnlineCount();

        $offcount = 0;
        $oncount = 0;

        foreach ($goc as $value) {
            if ($value['status'] == 0) {
                $offcount = $value['count(*)'];
            } else {
                $oncount = $value['count(*)'];
            }
        }

        $ss_arr = array(
            'off' => $offcount,
            'on' => $oncount
        );

        return $ss_arr;
    }

    /**
     * 客戶狀態：等待/處理中
     */
    private function getCustomerStatus()
    {
        $msgmainDao = new messages_main_dao;

        $today_s = $this->time_today;
        $today_e = $this->time_now;

        $waiting = $msgmainDao->getMsgStatus(0, $today_s, $today_e);                            //等待對話
        $processing = $msgmainDao->getMsgStatus(1, $today_s, $today_e);                         //處理中
        $autodatas = $msgmainDao->getMsgAutoStatus($this->autoname, 1, $today_s, $today_e);     //處理中(人工/智能)
        $autocount = 0;
        $servicecount = 0;
        foreach ($autodatas as $value) {
            if ($value['user_id'] == $this->autoname) {
                $autocount = $value['count(*)'];
            } else {
                $servicecount = $value['count(*)'];
            }
        }

        $cs_arr = array(
            'waiting' => $waiting,
            'processing' => $processing,
            'auto' => $autocount,
            'service' => $servicecount
        );

        return $cs_arr;
    }

    /**
     * 今日統計：評價/平均回合數/處理完畢/平均首次響應/平均對話
     */
    private function getTodayInfo()
    {
        $msgmainDao = new messages_main_dao;

        $today_s = $this->time_today;
        $today_e = $this->time_now;

        $gmi_data = $msgmainDao->getMsgInfo($today_s, $today_e);
        $gml_data = $msgmainDao->getMsgLength($today_s, $today_e);

        //評價
        if (!empty($gmi_data[0]['s']) && !empty($gmi_data[0]['c'])) {
            $evaluation = round($gmi_data[0]['s'] / $gmi_data[0]['c'], 1);
        } else {
            $evaluation = 0;
        }

        //平均回合數
        if (!empty($gmi_data[0]['r']) && !empty($gmi_data[0]['c'])) {
            $round = round($gmi_data[0]['r'] / $gmi_data[0]['c'], 1);
        } else {
            $round = 0;
        }

        //處理完畢
        $finish = $msgmainDao->getMsgStatus(2, $today_s, $today_e);

        //平均首次響應時長
        if (!empty($gml_data[0]['fl']) && !empty($gml_data[0]['c'])) {
            $first = $this->getMsgTime($gml_data[0]['fl'] / $gml_data[0]['c']);
        } else {
            $first = 0;
        }

        //平均對話時長
        if (!empty($gml_data[0]['ml']) && !empty($gml_data[0]['c'])) {
            $msg = $this->getMsgTime($gml_data[0]['ml'] / $gml_data[0]['c']);
        } else {
            $msg = 0;
        }

        $ti_arr = array(
            'evaluation' => $evaluation,
            'round' => $round,
            'finish' => $finish,
            'first' => $first,
            'msg' => $msg
        );

        return $ti_arr;
    }

    /**
     * IP地區數據
     */
    function getIPArea($init = 1)
    {
        if ($init == 1) {
            if (!isset($_POST["days"])) return returnAPI([], 1, "param_err");
            if (!is_numeric($_POST["days"])) return returnAPI([], 1, "param_err");
            $days = $_POST["days"];
        } else {
            $days = "7";
        }

        $msgmainDao = new messages_main_dao;

        $time_s = $this->getToTime($days);
        $time_e = $this->time_now;

        $area = $msgmainDao->getMsgChart("member_loc", $time_s, $time_e);        //IP地區

        if ($init == 1) return returnAPI($area);
        return $area;
    }

    /**
     * 來源網址數據
     */
    function getSource($init = 1)
    {
        if ($init == 1) {
            if (!isset($_POST["days"])) return returnAPI([], 1, "param_err");
            if (!is_numeric($_POST["days"])) return returnAPI([], 1, "param_err");
            $days = $_POST["days"];
        } else {
            $days = "7";
        }

        $msgmainDao = new messages_main_dao;

        $time_s = $this->getToTime($days);
        $time_e = $this->time_now;

        $source = $msgmainDao->getMsgChart("member_from", $time_s, $time_e);        //來源網址

        if ($init == 1) return returnAPI($source);
        return $source;
    }

    /**
     * 使用環境數據
     */
    function getDevice($init = 1)
    {
        if ($init == 1) {
            if (!isset($_POST["days"])) return returnAPI([], 1, "param_err");
            if (!is_numeric($_POST["days"])) return returnAPI([], 1, "param_err");
            $days = $_POST["days"];
        } else {
            $days = "7";
        }

        $msgmainDao = new messages_main_dao;

        $time_s = $this->getToTime($days);
        $time_e = $this->time_now;

        $device = $msgmainDao->getMsgChart("member_env", $time_s, $time_e);        //使用環境

        if ($init == 1) return returnAPI($device);
        return $device;
    }

    /**
     * 時間換算 00:00:00
     */
    private function getMsgTime($time)
    {
        $hour = str_pad(floor($time / 3600), 2, "0", STR_PAD_LEFT);
        $time = $time % 3600;
        $minute = str_pad(floor($time / 60), 2, "0", STR_PAD_LEFT);
        $second = str_pad(($time % 60), 2, "0", STR_PAD_LEFT);

        return $hour . ':' . $minute . ':' . $second;
    }

    /**
     * 計算時戳
     */
    private function getToTime($val)
    {
        switch ($val) {
            case '7':
                $time = "-1 week";
                break;

            case '15':
                $time = "-15 day";
                break;

            case '30':
                $time = "-1 month";
                break;

            case '90':
                $time = "-3 month";
                break;

            case '180':
                $time = "-6 month";
                break;
        }

        return strtotime($time, $this->time_today);
    }
}
