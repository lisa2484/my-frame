<?php

namespace app\controllers;

include "./models/web_set_dao.php";

use app\models\web_set_dao;

class bot_basicset_con
{
    private $arr = [
        'welcome_val' => 'bot_welcome_switch',
        'automsg_val' => 'bot_automsg_switch',
        'keyword_val' => 'bot_keyword_switch',
        'autoservice_val' => 'bot_autoservice_switch'
    ];

    /**
     * 智能客服基本設置列表
     */
    function init()
    {
        $data_arr = [];
        $arr = $this->arr;
        $wsDao = new web_set_dao;
        foreach ($arr as $k => $d) {
            $fieldstatus = $wsDao->getWebSetListBySetKey($d);
            if (!empty($fieldstatus)) {
                $data_arr[$k] = $fieldstatus[0]['value'];
            } else {
                $data_arr[$k] = 0;
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
        $seting_arr = [];
        $request = $_POST;
        $arr = $this->arr;
        foreach ($arr as $k => $d) {
            if (!isset($request[$k])) return returnAPI([], 1, "param_err");
            $p = $request[$k];
            if (!in_array($p, [0, 1])) return returnAPI([], 1, "param_err");
            $seting_arr[$d] = $p;
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
