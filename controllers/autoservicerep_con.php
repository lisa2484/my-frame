<?php

namespace app\controllers;

include "./models/autoservicerep_dao.php";

use app\models\autoservicerep_dao;

class autoservicerep_con
{
    function init()
    {
        return $this->get();
    }

    function get()
    {
        $asDao = new autoservicerep_dao;
        $datas = $asDao->getWebData();
        $layers = 0;
        if (!empty($datas)) {
            $ids = [];
            $pids = [];

            // $layers = $this->getLaysers($datas);
        }
        return returnAPI(["layers" => $layers, "list" => $datas]);
    }

    // private function getLaysers(int $id, array &$datas): int
    // {
    //     $layer = 0;
    //     foreach ($datas as $d) {
    //         if ($d["parent_id"] == $id)return $this->getLaysers($d["parent_id"],$id);
    //     }
    //     return 0;
    // }

    function add()
    {
        if (!key_exists("parent_id", $_POST) || !is_numeric($_POST["parent_id"])) return returnAPI([], 1, "param_err");
        if (!key_exists("title", $_POST) || $_POST["title"] == "") return returnAPI([], 1, "param_empty");
        if (!key_exists("msg", $_POST) || $_POST["msg"] == "") return returnAPI([], 1, "param_empty");
        $insertArr = [];
        $insertArr["parent_id"] = $_POST["parent_id"];
        $insertArr["title"] = $_POST["title"];
        $insertArr["msg"] = $_POST["msg"];
        if (key_exists("onf", $_POST) && in_array($_POST["onf"], [0, 1])) $insertArr["onf"] = $_POST["onf"];
        if (key_exists("sort", $_POST) && is_numeric($_POST["sort"])) $insertArr["sort"] = $_POST["sort"];
        $asDao = new autoservicerep_dao;
        if ($asDao->setMsgInsert($insertArr)) return returnAPI([]);
        return returnAPI([], 1, "add_err");
    }

    function edit()
    {
        if (!key_exists("id", $_POST) || !is_numeric($_POST["id"])) return returnAPI([], 1, "param_err");
        if (!key_exists("parent_id", $_POST) || !is_numeric($_POST["parent_id"])) return returnAPI([], 1, "param_err");
        if (!key_exists("title", $_POST) || $_POST["title"] == "") return returnAPI([], 1, "param_empty");
        if (!key_exists("msg", $_POST) || $_POST["msg"] == "") return returnAPI([], 1, "param_empty");
        $id = $_POST["id"];
        $updateArr = [];
        $updateArr["parent_id"] = $_POST["parent_id"];
        $updateArr["title"] = $_POST["title"];
        $updateArr["msg"] = $_POST["msg"];
        if (key_exists("onf", $_POST) && in_array($_POST["onf"], [0, 1])) $updateArr["onf"] = $_POST["onf"];
        if (key_exists("sort", $_POST) && is_numeric($_POST["sort"])) $updateArr["sort"] = $_POST["sort"];
        $asDao = new autoservicerep_dao;
        if ($asDao->setMsgUpdate($id, $updateArr)) return returnAPI([]);
        return returnAPI([], 1, "upd_err");
    }

    function delete()
    {
        if (!key_exists("id", $_POST) || !is_numeric($_POST["id"])) return returnAPI([], 1, "param_err");
        $id = $_POST["id"];
        $asDao = new autoservicerep_dao;
        if ($asDao->setMsgDelete($id)) return returnAPI([]);
        return returnAPI([], 1, "del_err");
    }
}
