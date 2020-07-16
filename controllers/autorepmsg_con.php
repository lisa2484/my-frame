<?php

namespace app\controllers;

include './models/autorepmsg_dao.php';

use app\models\autorepmsg_dao;

class autorepmsg_con
{
    /**
     * 自動回覆訊息列表
     */
    function init()
    {
        if (!isset($_POST["page"]) || empty($_POST["page"])) return returnAPI([], 1, "param_err");
        if (!isset($_POST["limit"]) || empty($_POST["limit"])) return returnAPI([], 1, "param_err");
        $page = $_POST["page"];
        $limit = $_POST["limit"];
        $where = [];
        if (isset($_POST["title"]) && $_POST["title"] != "") $where["title"] = $_POST["title"];
        if (isset($_POST["keyword"]) && $_POST["keyword"] != "") $where["keyword"] = $_POST["keyword"];
        if (isset($_POST["msg"]) && $_POST["msg"] != "") $where["msg"] = $_POST["msg"];

        $amsgDao = new autorepmsg_dao;
        $datas = $amsgDao->getAllMsg($page, $limit, $where);
        $total = $amsgDao->getAllMsgTotal($where);
        return returnAPI(["total" => $total, "totalpage" => ceil($total / $limit), "page" => $page, "datas" => $datas]);
    }

    /**
     * 設定開關
     */
    function onfSwitch()
    {
        if (!isset($_POST["id"]) || !is_numeric($_POST["id"])) return returnAPI([], 1, "param_err");
        if (!isset($_POST["onf"]) || !in_array($_POST["onf"], [0, 1])) return returnAPI([], 1, "param_err");
        $id = $_POST["id"];
        $onf = $_POST["onf"];
        $amsgDao = new autorepmsg_dao;
        if ($amsgDao->setMsgOnf($id, $onf)) return returnAPI([]);
        return returnAPI([], 1, "upd_err");
    }

    /**
     * 編輯
     */
    function edit()
    {
        $request = $_POST;
        if (!isset($request["id"]) || !is_numeric($request["id"])) return returnAPI([], 1, "param_err");
        $id = $request["id"];
        $updateArr = [];
        if (isset($request["title"]) && $request["title"] != "") $updateArr["title"] = $request["title"];
        if (isset($request["keyword"]) && $request["keyword"] != "") $updateArr["keyword"] = $request["keyword"];
        if (isset($request["msg"]) && $request["msg"] != "") $updateArr["msg"] = $request["msg"];
        if ((isset($request["start_d"]) && !empty($request["start_d"])) && (isset($request["end_d"]) && !empty($request["end_d"]))) {
            $updateArr["start_d"] = $request["start_d"];
            $updateArr["end_d"] = $request["end_d"];
        }
        if ((isset($request["start_t"]) && !empty($request["start_t"])) && (isset($request["end_t"]) && !empty($request["end_t"]))) {
            $updateArr["start_t"] = $request["start_t"];
            $updateArr["end_t"] = $request["end_t"];
        }
        if (isset($request["onf"]) && $request["onf"] != "") $updateArr["onf"] = $request["onf"];
        if (isset($request["time_limit"]) && in_array($request["time_limit"], [0, 1, 2])) $updateArr["time_limit"] = $request["time_limit"];
        if (empty($updateArr)) return returnAPI([], 1, "param_err");
        $amsgDao = new autorepmsg_dao;
        if ($amsgDao->setMsgUpdate($id, $updateArr)) return returnAPI([]);
        return returnAPI([], 1, "upd_err");
    }

    /**
     * 新增
     */
    function add()
    {
        $request = $_POST;
        if (!isset($request["title"]) || $request["title"] == "") return returnAPI([], 1, "param_empty");
        if (!isset($request["keyword"]) || $request["keyword"] == "") return returnAPI([], 1, "param_empty");
        if (!isset($request["msg"]) || $request["msg"] == "") return returnAPI([], 1, "param_empty");
        if (!isset($request["time_limit"]) || !in_array($request["time_limit"], [0, 1, 2])) return returnAPI([], 1, "param_err");
        $insertArr["title"] = $request["title"];
        $insertArr["keyword"] = $request["keyword"];
        $insertArr["msg"] = $request["msg"];
        $insertArr["time_limit"] = $request["time_limit"];
        if (isset($request["start_d"]) && !empty($request["start_d"])) $insertArr["start_d"] = $request["start_d"];
        if (isset($request["end_d"]) && !empty($request["end_d"])) $insertArr["end_d"] = $request["end_d"];
        if (isset($request["start_t"]) && !empty($request["start_t"])) $insertArr["start_t"] = $request["start_t"];
        if (isset($request["end_t"]) && !empty($request["end_t"])) $insertArr["end_t"] = $request["end_t"];
        if (!empty($insertArr["time_limit"])) {
            if (!isset($insertArr["start_t"]) || empty($insertArr["start_t"])) return returnAPI([], 1, "param_empty");
            if (!isset($insertArr["end_t"]) || empty($insertArr["end_t"])) return returnAPI([], 1, "param_empty");
        }
        if ($insertArr["time_limit"] == 1) {
            if (!isset($insertArr["start_d"]) || empty($insertArr["start_d"])) return returnAPI([], 1, "param_empty");
            if (!isset($insertArr["end_d"]) || empty($insertArr["end_d"])) return returnAPI([], 1, "param_empty");
        }
        $amsgDao = new autorepmsg_dao;
        if ($amsgDao->setMsgInsert($insertArr)) return returnAPI([]);
        return returnAPI([], 1, "add_err");
    }

    /**
     * 刪除
     */
    function delete()
    {
        if (!isset($_POST["id"])) return returnAPI([], 1, "param_err");
        $ids = explode(",", $_POST["id"]);
        $amsgDao = new autorepmsg_dao;
        foreach ($ids as $id) {
            if (!is_numeric($id)) return returnAPI([], 1, "param_err");
        }
        if ($amsgDao->setMsgDelete($ids)) return returnAPI([]);
        return returnAPI([], 1, "del_err");
    }
}
