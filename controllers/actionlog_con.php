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

    function getCsv()
    {
        set_time_limit(0);
        ini_set("memory_limit", "512M");
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename=actionLog.csv');

        $adminname = "";
        $time_s = "";
        $time_e = "";

        if (isset($_POST["adminname"]) && $_POST["adminname"] != "") $adminname = $_POST["adminname"];
        if (isset($_POST["timestart"]) && !empty($_POST["timestart"])) $time_s = $_POST["timestart"] . " 00:00:00";
        if (isset($_POST["timeend"]) && !empty($_POST["timeend"])) $time_e = $_POST["timeend"] . " 23:59:59";

        $actionlogDao = new action_log_dao;
        $logdata = $actionlogDao->getActionLogForExport($adminname, $time_s, $time_e);
        
        $title = $this->getTitle();
        
        $f = fopen("php://output", "w");        
        fputcsv($f, $title);

        foreach ($logdata as $data) {
            fputcsv($f, array_values($data));
        }
        fclose($f);
    }

    private function getTitle()
    {
        $arr[] = "编号";
        $arr[] = "操作时间";
        $arr[] = "帐号";
        $arr[] = "IP";
        $arr[] = "操作页面";
        $arr[] = "操作功能";

        return $arr;
    }
}
