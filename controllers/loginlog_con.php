<?php

namespace app\controllers;

include "./models/login_log_dao.php";

use app\models\login_log_dao;

class loginlog_con
{
    /**
     * 初始：顯示登入紀錄資料
     */
    function init()
    {
        return $this->getLogList();
    }

    /**
     * 抓取登入紀錄資料
     * @return array(
     *         'total' => string 總數量,
     *         'totalpage' => string 總頁碼,
     *         'page' => string 當前頁碼,
     *         'data' => array(
     *                   0 => array(
     *                   'id' => string 編號
     *                   'account' => string 使用者帳號
     *                   'ip' => string IP
     *                   'user_name' => string 使用者名稱
     *                   'authority_name' => string 使用者權限名稱
     *                   'login_date' => string 登入時間
     *                   ),
     *                   1 => array(....)
     *                   ) 
     *         )
     */
    function getLogList()
    {
        $adminname = "";
        if (isset($_POST["adminname"])) $adminname = $_POST["adminname"];
        $time_s = empty($_POST["timestart"]) ? "" : $_POST["timestart"] . " 00:00:00";
        $time_e = empty($_POST["timeend"]) ? "" : $_POST["timeend"] . " 23:59:59";

        if ($time_s == "" ^ $time_e == "") {
            return returnAPI([], 1, "param_empty");
        }

        if ($time_s > $time_e) {
            return returnAPI([], 1, "param_err");
        }

        if (!isset($_POST["page"])) return returnAPI([], 1, "param_err");
        $page = $_POST["page"];
        if (!isset($_POST["limit"])) return returnAPI([], 1, "param_err");
        $limit = $_POST["limit"];

        $loginlogDao = new login_log_dao;
        $logtotal = $loginlogDao->getLoginLogTotal($adminname, $time_s, $time_e);

        $totalpage = ceil($logtotal / $limit);
        if ($totalpage == 0) {
            if ($page != 1) {
                return returnAPI([], 1, "param_err");
            }
        } else {
            if ($page > $totalpage) return returnAPI([], 1, "param_err");
        }

        $logdata = $loginlogDao->getLoginLog($adminname, $time_s, $time_e, $page, $limit);

        $data_arr = array(
            'total' => $logtotal,
            'totalpage' => $totalpage,
            'page' => $page,
            'list' => $logdata
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
        header('Content-Disposition: attachment; filename=loginLog.csv');
        $adminname = "";
        $time_s = "";
        $time_e = "";
        if (isset($_POST["adminname"]) && $_POST["adminname"] != "") $adminname = $_POST["adminname"];
        if (isset($_POST["timestart"]) && !empty($_POST["timestart"])) $time_s = $_POST["timestart"] . " 00:00:00";
        if (isset($_POST["timeend"]) && !empty($_POST["timeend"])) $time_e = $_POST["timeend"] . " 23:59:59";
        $loginlogDao = new login_log_dao;
        $logdata = $loginlogDao->getLoginLogForExport($adminname, $time_s, $time_e);
        $title = $this->getTitle();
        $f = fopen("php://output", "w");
        fputcsv($f, $title);
        foreach ($logdata as $data) {
            fputcsv($f, array_values($data));
        }
        fclose($f);
    }

    /**
     * 匯出功能_欄位標題名稱
     */
    private function getTitle()
    {
        $arr[] = "帐号";
        $arr[] = "昵称";
        $arr[] = "权限";
        $arr[] = "登入IP";
        return $arr;
    }
}
