<?php
    namespace app\controllers;

    include "./models/web_set_dao.php";

    use app\models\web_set_dao;

    class bot_basicset_con
    {
        public $field_arr = ['bot_welcome_switch', 'bot_automsg_switch', 'bot_keyword_switch', 'bot_autoservice_switch'];
        public $front_arr = ['welcome_val', 'automsg_val', 'keyword_val', 'autoservice_val'];

        /**
         * 初始：智能客服基本設置
         * @return string json形式 {
         *                          "welcome_val": string 歡迎訊息的配置,
         *                          "automsg_val": string 自動回應訊息的配置,
         *                          "keyword_val": string 查無關鍵字訊息的配置,
         *                          "autoservice_val": string 智能客服訊息的配置
         *                         }
         */
        function init()
        {
            $data_arr = array();

            $wsDao = new web_set_dao;
            
            for ($i=0; $i < count($this->field_arr); $i++) { 
                $fieldstatus = $wsDao->getWebSetListBySetKey($this->field_arr[$i]);

                if ($fieldstatus) {
                    $data_arr[$this->front_arr[$i]] = $fieldstatus[0]['value'];
                } else {
                    $data_arr[$this->front_arr[$i]] = 0;
                }
            }

            return returnAPI($data_arr);
        }

        /**
         * 設定基本設置各選項
         * @return bool 回傳是否成功
         */
        function setBotBasicSetting()
        {
            $seting_arr = array();

            for ($i=0; $i < count($this->front_arr); $i++) { 
                if (array_key_exists($this->front_arr[$i], $_POST)) {
                    if (strlen($_POST[$this->front_arr[$i]]) != 1) return returnAPI([], 1, "botset_length_err");
                    if (!in_array($_POST[$this->front_arr[$i]], ['0','1'])) return returnAPI([], 1, "botset_val_err");

                    $seting_arr[$this->field_arr[$i]] = $_POST[$this->front_arr[$i]];

                } else {
                    return returnAPI([], 1, "param_err");
                }
            }

            foreach ($seting_arr as $key => $value) {
                $getstatus = $this->getWebSetStatus($key, $value);
                if (!$getstatus) return returnAPI([], 1, "botset_set_err");
            }

            return returnAPI([]);
        }

        /**
         * 抓取目前選項設置
         * 無資料：新增對應欄位名稱及設置
         * 有資料：修改該選項設置
         * @param mixed $fieldname 選取的選項名稱
         * @param mixed $value 選取到的值
         * @return bool 回傳是否成功
         */
        private function getWebSetStatus($fieldname, $value)
        {
            $wsDao = new web_set_dao;

            if (empty($wsDao->getWebSetListBySetKey($fieldname))) return $wsDao->setWebSetAdd($fieldname, $value);
            return $wsDao->setWebSetEdit($fieldname, $value);
        }
    }
