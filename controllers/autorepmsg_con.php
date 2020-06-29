<?php

namespace app\controllers;

include './models/autorepmsg_dao.php';

use app\models\autorepmsg_dao;

class autorepmsg_con
{
    function init()
    {
        return $this->get();
    }

    function get()
    {
        if (!key_exists("page", $_POST) || empty($_POST["page"])) return returnAPI([], 1, "param_err");
        $page = $_POST["page"];
        if (!key_exists("limit", $_POST) || empty($_POST["limit"])) return returnAPI([], 1, "param_err");
        $limit = $_POST["limit"];
        $where = [];
        if (key_exists("keyword", $_POST) && $_POST["keyword"] != "") $where["keyword"] = $_POST["keyword"];
        $amsgDao = new autorepmsg_dao;
        $datas = $amsgDao->getAllMsg($page, $limit, $where);
        $total = $amsgDao->getAllMsgTotal($where);
        return returnAPI(["total" => $total, "datas" => $datas]);
    }

    function onfSwitch()
    {
        if (!key_exists("id", $_POST) || !is_numeric($_POST["id"])) return returnAPI([], 1, "param_err");
        $id = $_POST["id"];
        if (!key_exists("onf", $_POST) || !in_array($_POST["onf"], [0, 1])) return returnAPI([], 1, "param_err");
        $onf = $_POST["onf"];
        $amsgDao = new autorepmsg_dao;
        if ($amsgDao->setMsgOnf($id, $onf)) return returnAPI([]);
        return returnAPI([], 1, "upd_err");
    }

    function edit()
    {
        if (!key_exists("id", $_POST) || !is_numeric($_POST["id"])) return returnAPI([], 1, "param_err");
        $id = $_POST["id"];
        $updateArr = [];
        if (key_exists("title", $_POST) && $_POST["title"] != "") $updateArr["title"] = $_POST["title"];
        if (key_exists("keyword", $_POST) && $_POST["keyword"] != "") $updateArr["keyword"] = $_POST["keyword"];
        if (key_exists("msg", $_POST) && $_POST["msg"] != "") $updateArr["msg"] = $_POST["msg"];
        if (key_exists("time_limit", $_POST) && in_array($_POST["time_limit"], [0, 1, 2])) $updateArr["time_limit"] = $_POST["time_limit"];
        if (key_exists("start_d", $_POST) && !empty($_POST["start_d"])) $updateArr["start_d"] = $_POST["start_d"];
        if (key_exists("end_d", $_POST) && !empty($_POST["end_d"])) $updateArr["end_d"] = $_POST["end_d"];
        if (key_exists("start_t", $_POST) && !empty($_POST["start_t"])) $updateArr["start_t"] = $_POST["start_t"];
        if (key_exists("end_t", $_POST) && !empty($_POST["end_t"])) $updateArr["end_t"] = $_POST["end_t"];
        if (empty($updateArr)) return returnAPI([], 1, "param_err");
        $amsgDao = new autorepmsg_dao;
        if ($amsgDao->setMsgUpdate($id, $updateArr)) return returnAPI([]);
        return returnAPI([], 1, "upd_err");
    }

    function add()
    {
        if (!key_exists("title", $_POST) || $_POST["title"] == "") return returnAPI([], 1, "param_empty");
        $insertArr["title"] = $_POST["title"];
        if (!key_exists("keyword", $_POST) || $_POST["keyword"] == "") return returnAPI([], 1, "param_empty");
        $insertArr["keyword"] = $_POST["keyword"];
        if (!key_exists("msg", $_POST) || $_POST["msg"] == "") return returnAPI([], 1, "param_empty");
        $insertArr["msg"] = $_POST["msg"];
        if (!key_exists("time_limit", $_POST) || !in_array($_POST["time_limit"], [0, 1, 2])) return returnAPI([], 1, "param_err");
        $insertArr["time_limit"] = $_POST["time_limit"];
        if (key_exists("start_d", $_POST) && !empty($_POST["start_d"])) $insertArr["start_d"] = $_POST["start_d"];
        if (key_exists("end_d", $_POST) && !empty($_POST["end_d"])) $insertArr["end_d"] = $_POST["end_d"];
        if (key_exists("start_t", $_POST) && !empty($_POST["start_t"])) $insertArr["start_t"] = $_POST["start_t"];
        if (key_exists("end_t", $_POST) && !empty($_POST["end_t"])) $insertArr["end_t"] = $_POST["end_t"];
        if (!empty($insertArr["time_limit"])) {
            if (!key_exists("start_t", $insertArr) || empty($insertArr["start_t"])) return returnAPI([], 1, "param_empty");
            if (!key_exists("end_t", $insertArr) || empty($insertArr["end_t"])) return returnAPI([], 1, "param_empty");
        }
        if ($insertArr["time_limit"] == 1) {
            if (!key_exists("start_d", $insertArr) || empty($insertArr["start_d"])) return returnAPI([], 1, "param_empty");
            if (!key_exists("end_d", $insertArr) || empty($insertArr["end_d"])) return returnAPI([], 1, "param_empty");
        }
        $amsgDao = new autorepmsg_dao;
        if ($amsgDao->setMsgInsert($insertArr)) return returnAPI([]);
        return returnAPI([], 1, "add_err");
    }

    function delete()
    {
        if (!key_exists("id", $_POST)) return returnAPI([], 1, "param_err");
        $ids = explode(",", $_POST["id"]);
        $amsgDao = new autorepmsg_dao;
        foreach ($ids as $id) {
            if (is_numeric($id)) {
                if (!$amsgDao->setMsgDelete($id)) return returnAPI([], 1, "del_err");
            }
        }
        return returnAPI([]);
    }
}
