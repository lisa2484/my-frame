<?php

namespace app\controllers;

include "./models/action_log_dao.php";

use app\models\action_log_dao;

class actionlog_con
{
    /**
     * 抓取操作紀錄資料
     * @return array(
     *              'total' => string 總數量,
     *              'page' => string 當前頁碼,
     *              'data' => array(
     *                              0 => array(
     *                                  'id' => string 編號
     *                                  'ip' => string IP
     *                                  'user' => string 使用者帳號
     *                                  'datetime' => string 操作時間
     *                                  'remark' => string 操作敘述
     *                                  'fun' => string 操作項目
     *                              ),
     *                              1 => array(....)
     *                        ) 
     *         )
     */
    function init()
    {
        $adminname = isset($_POST["adminname"]) ? $_POST["adminname"] : "";
        $time_s = isset($_POST["timestart"]) ? $_POST["timestart"] : "";
        $time_e = isset($_POST["timeend"]) ? $_POST["timeend"] : "";

        if (!isset($_POST["page"]) || !is_numeric($_POST["page"]) || $_POST["page"] < 1) return returnAPI([], 1, "param_err");
        if (!isset($_POST["limit"]) || !is_numeric($_POST["limit"])) return returnAPI([], 1, "param_err");
        $page = $_POST["page"];
        $limit = $_POST["limit"];

        if ($time_s == "" ^ $time_e == "") {
            return returnAPI([], 1, "param_empty");
        }

        if ($time_s > $time_e) {
            return returnAPI([], 1, "param_err");
        }
        $where = [];
        if (!empty($time_s) && !empty($time_e)) {
            $where["s_d"] = $time_s . " 00:00:00";
            $where["e_d"] = $time_e . " 23:59:59";
        }
        if ($adminname != "") $where["user"] = $adminname;

        $actionlogDao = new action_log_dao;
        $logtotal = $actionlogDao->getActionLogTotalByArrayWhere($where);
        $logdata = $actionlogDao->getActionLogJoinMenu($where, $page, $limit);

        $data_arr = array(
            'total' => $logtotal,
            'totalpage' => ceil($logtotal / $limit),
            'page' => $page,
            'list' => $logdata
        );

        return returnAPI($data_arr);
    }
}
