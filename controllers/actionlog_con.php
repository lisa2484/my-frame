<?php

namespace app\controllers;

include "./models/action_log_dao.php";

use app\models\action_log_dao;

class actionlog_con
{
    /**
     * 抓取操作紀錄資料
     */
    function init()
    {
        if (!isset($_POST["page"]) || !is_numeric($_POST["page"]) || $_POST["page"] < 1) return returnAPI([], 1, "param_err");
        if (!isset($_POST["limit"]) || !is_numeric($_POST["limit"])) return returnAPI([], 1, "param_err");
        $page = $_POST["page"];
        $limit = $_POST["limit"];
        $adminname = isset($_POST["adminname"]) ? $_POST["adminname"] : "";
        $time_s = isset($_POST["timestart"]) ? $_POST["timestart"] : "";
        $time_e = isset($_POST["timeend"]) ? $_POST["timeend"] : "";

        if (($time_s == "" ^ $time_e == "") && ($time_s > $time_e)) return returnAPI([], 1, "param_err");
        $where = [];
        if (!empty($time_s) && !empty($time_e)) {
            $where["s_d"] = $time_s . " 00:00:00";
            $where["e_d"] = $time_e . " 23:59:59";
        }
        if ($adminname != "") $where["user"] = $adminname;

        $actionlogDao = new action_log_dao;
        $logtotal = $actionlogDao->getActionLogTotalByArrayWhere($where);
        $logdata = $actionlogDao->getActionLogJoinMenu($where, $page, $limit);

        $data_arr = [
            'total' => $logtotal,
            'totalpage' => ceil($logtotal / $limit),
            'page' => $page,
            'list' => $logdata
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
        header('Content-Disposition: attachment; filename=actionLog.csv');

        $adminname = "";
        $time_s = "";
        $time_e = "";

        if (isset($_POST["adminname"])) $adminname = $_POST["adminname"];
        if (isset($_POST["timestart"]) && !empty($_POST["timestart"])) $time_s = $_POST["timestart"];
        if (isset($_POST["timeend"]) && !empty($_POST["timeend"])) $time_e = $_POST["timeend"];
        if (($time_s == "" ^ $time_e == "") || ($time_s > $time_e)) return returnAPI([], 1, "param_err");
        $where = [];
        if ($adminname != "") $where["user"] = $adminname;
        if (!empty($time_s) && !empty($time_e)) {
            $where["s_d"] = $time_s . " 00:00:00";
            $where["e_d"] = $time_e . " 23:59:59";
        }
        $actionlogDao = new action_log_dao;
        $logdata = $actionlogDao->getActionLogJoinMenu($where, 1, 50000);
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
