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
         *              'total' => string 總數量,
         *              'page' => string 當前頁碼,
         *              'data' => array(
         *                              0 => array(
         *                                  'id' => string 編號
         *                                  'account' => string 使用者帳號
         *                                  'ip' => string IP
         *                                  'user_name' => string 使用者名稱
         *                                  'authority_name' => string 使用者權限名稱
         *                                  'login_date' => string 登入時間
         *                              ),
         *                              1 => array(....)
         *                        ) 
         *         )
         */
        function getLogList()
        {
            $str_sql_arr = array();

            $adminname = isset($_POST["adminname"]) ? $_POST["adminname"] : "" ;
            $time_s = isset($_POST["timestart"]) ? $_POST["timestart"] : "" ;
            $time_e = isset($_POST["timeend"]) ? $_POST["timeend"] : "" ;

            if (isset($_POST["page"])) {
                $page = $_POST["page"];
            } else {
                return false;
            }

            if (isset($_POST["pagenum"])) {
                $limit = $_POST["pagenum"];
            } else {
                return false;
            }
            
            if (!empty($adminname) || !empty($time_s) || !empty($time_e)) {
                $str_sql = " WHERE";
            } else {
                $str_sql = "";
            }

            if (!empty($time_s) || !empty($time_e)) {
                $time_s = empty($time_s) ? date("Y-m-01 00:00:00") : $time_s;
                $time_e = empty($time_e) ? date("Y-m-d H:i:s") : $time_e;

                $time_sql = " `login_date` BETWEEN '$time_s' AND '$time_e' ";
                array_push($str_sql_arr, $time_sql);
            }
            
            if (!empty($adminname)) {
                $username_sql = " `account` = '$adminname' ";
                array_push($str_sql_arr, $username_sql);
            }

            $str_sql_im = implode(" AND ", $str_sql_arr);
            $str_sql .= $str_sql_im;

            $loginlogDao = new login_log_dao;

            $logtotal = $loginlogDao->getLoginLogTotal($str_sql);

            if ($logtotal == 0) {
                $page = 1;
            } else {
                $page = empty($page) ? 1 : $page;
                if ($page < 1) $page = 1;

                $page_max = ceil($logtotal/$limit);
                if ($page > $page_max) $page = $page_max;
            }

            $offset = $limit * ($page - 1);

            $logdata = $loginlogDao->getLoginLog($str_sql, $limit, $offset);

            $data_arr = array(
                'total' => $logtotal,
                'page' => $page,
                'data' => $logdata
            );

            return json($data_arr);
        }
    }
