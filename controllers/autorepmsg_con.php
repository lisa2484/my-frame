<?php

namespace app\controllers;

// include './models/autorepmsg_dao.php';

spl_autoload_register(function ($class) {
    include "./models/" . $class . ".php";
});

use app\models\autorepmsg_dao;

class autorepmsg_con
{
    function init()
    {
        return $this->get();
    }

    function get()
    {
        if (!key_exists("page", $_POST) || empty($_POST["page"])) return false;
        $page = $_POST["page"];
        if (!key_exists("limit", $_POST) || empty($_POST["limit"])) return false;
        $limit = $_POST["limit"];
        $where = [];
        if (key_exists("keyword", $_POST) && $_POST["keyword"] != "") $where["keyword"] = $_POST["keyword"];
        $amsgDao = new autorepmsg_dao;
        $datas = $amsgDao->getAllMsg($page, $limit, $where);
        $total = $amsgDao->getAllMsgTotal($where);
        return json(["total" => $total, "datas" => $datas]);
    }

    function onfSwitch()
    {
        if (!key_exists("id", $_POST) || !is_numeric($_POST["id"])) return false;
        $id = $_POST["id"];
        if (!key_exists("onf", $_POST) || !in_array($_POST["onf"], [0, 1])) return false;
        $onf = $_POST["onf"];
        $amsgDao = new autorepmsg_dao;
        return $amsgDao->setMsgOnf($id, $onf);
    }

    function edit()
    {
        if (!key_exists("id", $_POST) || !is_numeric($_POST["id"])) return false;
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
        if (empty($updateArr)) return false;
        $amsgDao = new autorepmsg_dao;
        return $amsgDao->setMsgUpdate($id, $updateArr);
    }

    function add()
    {
        if (!key_exists("title", $_POST) || $_POST["title"] == "") return false;
        $insertArr["title"] = $_POST["title"];
        if (!key_exists("keyword", $_POST) || $_POST["keyword"] == "") return false;
        $insertArr["keyword"] = $_POST["keyword"];
        if (!key_exists("msg", $_POST) || $_POST["msg"] == "") return false;
        $insertArr["msg"] = $_POST["msg"];
        if (!key_exists("time_limit", $_POST) || !in_array($_POST["time_limit"], [0, 1, 2])) return false;
        $insertArr["time_limit"] = $_POST["time_limit"];
        if (key_exists("start_d", $_POST) && !empty($_POST["start_d"])) $insertArr["start_d"] = $_POST["start_d"];
        if (key_exists("end_d", $_POST) && !empty($_POST["end_d"])) $insertArr["end_d"] = $_POST["end_d"];
        if (key_exists("start_t", $_POST) && !empty($_POST["start_t"])) $insertArr["start_t"] = $_POST["start_t"];
        if (key_exists("end_t", $_POST) && !empty($_POST["end_t"])) $insertArr["end_t"] = $_POST["end_t"];
        if (!empty($insertArr["time_limit"])) {
            if (!key_exists("start_t", $insertArr) || empty($insertArr["start_t"])) return false;
            if (!key_exists("end_t", $insertArr) || empty($insertArr["end_t"])) return false;
        }
        if ($insertArr["time_limit"] == 1) {
            if (!key_exists("start_d", $insertArr) || empty($insertArr["start_d"])) return false;
            if (!key_exists("end_d", $insertArr) || empty($insertArr["end_d"])) return false;
        }
        $amsgDao = new autorepmsg_dao;
        return $amsgDao->setMsgInsert($insertArr);
    }

    function delete()
    {
        if (!key_exists("id", $_POST) || !is_numeric($_POST["id"])) return false;
        $id = $_POST["id"];
        $amsgDao = new autorepmsg_dao;
        return $amsgDao->setMsgDelete($id);
    }
}
