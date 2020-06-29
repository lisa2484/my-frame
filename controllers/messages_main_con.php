<?php

namespace app\controllers;

include "./models/user_dao.php";
include "./models/messages_main_dao.php";

use app\models\user_dao;
use app\models\messages_main_dao;

class messages_main_con
{
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

        for ($i=0; $i < count($msgmaindata); $i++) { 
            foreach ($msgmaindata[$i] as $key => $value) {
                if ($key != "rep_len") {
                    $msgmain_arr[$i][$key] = $value;
                }
            }

            $st = $msgmaindata[$i]['start_time'];
            $et = $msgmaindata[$i]['end_time'];
            $rl = $msgmaindata[$i]['rep_len'];

            $msgmain_arr[$i]['start_time'] = date("Y-m-d H:i:s", $st);
            $msgmain_arr[$i]['end_time'] = date("Y-m-d H:i:s", $et);

            $msgmain_arr[$i]['time_length'] = $this->getDateTime(($et - $st));
            $msgmain_arr[$i]['first_time'] = $this->getDateTime(($rl - $st));
        }

        $data_arr = array(
            'total' => $msgmaintotal,
            'totalpage' => ceil($msgmaintotal/$limit),
            'page' => $page,
            'acclist' => $useracc,
            'list' => $msgmain_arr
        );

        return returnAPI($data_arr);
    }

    private function getDateTime($time){
        $hour = str_pad(floor($time / 3600), 2, "0", STR_PAD_LEFT);
        $time = $time % 3600;
        $minute = str_pad(floor($time / 60), 2, "0", STR_PAD_LEFT);
        $second = str_pad(($time % 60), 2, "0", STR_PAD_LEFT);
        
        return $hour . ':' . $minute . ':' . $second;
    }
}