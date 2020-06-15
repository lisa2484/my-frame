<?php
    namespace app\controllers;

    include "./models/web_set_dao.php";

    use app\models\web_set_dao;

    class bot_basicset_con
    {
        public $field_arr = ['bot_welcome_switch', 'bot_automsg_switch', 'bot_keyword_switch', 'bot_autoservice_switch'];
        public $front_arr = ['welcome_val', 'automsg_val', 'keyword_val', 'autoservice_val'];

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

            return json($data_arr);
        }

        function setBotBasicSetting()
        {
            $seting_arr = array();

            for ($i=0; $i < count($this->front_arr); $i++) { 
                if (isset($_POST[$this->front_arr[$i]])) {
                    $seting_arr[$this->front_arr[$i]] = ($_POST[$this->front_arr[$i]] == 1) ? 1 : 0 ;
                } else {
                    return false;
                }
            }

            foreach ($seting_arr as $key => $value) {
                switch ($key) {
                    case $this->front_arr[0]:
                        $getstatus = $this->getWebSetStatus($this->field_arr[0], $value);
                        if (!$getstatus) return $getstatus;

                        break;
                    
                    case $this->front_arr[1]:
                        $getstatus = $this->getWebSetStatus($this->field_arr[1], $value);
                        if (!$getstatus) return $getstatus;

                        break;
                    
                    case $this->front_arr[2]:
                        $getstatus = $this->getWebSetStatus($this->field_arr[2], $value);
                        if (!$getstatus) return $getstatus;

                        break;
                    
                    case $this->front_arr[3]:
                        $getstatus = $this->getWebSetStatus($this->field_arr[3], $value);
                        if (!$getstatus) return $getstatus;

                        break;
                    
                    default:
                        return false;
                        break;
                }
                
            }

            return true;
        }

        private function getWebSetStatus($fieldname, $value)
        {
            $wsDao = new web_set_dao;

            if (empty($wsDao->getWebSetListBySetKey($fieldname))) return $wsDao->setWebSetAdd($fieldname, $value);
            return $wsDao->setWebSetEdit($fieldname, $value);
        }
    }
