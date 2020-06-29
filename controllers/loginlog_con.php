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
        $time_s = empty($_POST["timestart"]) ? "" : $_POST["timestart"];
        $time_e = empty($_POST["timeend"]) ? "" : $_POST["timeend"];

        if (!isset($_POST["page"])) return returnAPI([], 1, "param_err");
        $page = $_POST["page"];
        if (!isset($_POST["limit"])) return returnAPI([], 1, "param_err");
        $limit = $_POST["limit"];

        $loginlogDao = new login_log_dao;
        $logtotal = $loginlogDao->getLoginLogTotal($adminname, $time_s, $time_e);
        $logdata = $loginlogDao->getLoginLog($adminname, $time_s, $time_e, $page, $limit);

        $data_arr = array(
            'total' => $logtotal,
            'totalpage' => ceil($logtotal/$limit),
            'page' => $page,
            'list' => $logdata
        );

        return returnAPI($data_arr);
    }
}
