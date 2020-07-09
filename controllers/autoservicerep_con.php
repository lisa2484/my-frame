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
        $datas = $asDao->getList();
        $layers = 0;
        if (!empty($datas)) {
            $pIDs = [];
            foreach ($datas as $data) {
                $pIDs[$data["parent_id"]][] = $data["id"];
            }
            $layers = $this->getLayser(0, $pIDs);
        }
        return returnAPI(["max_layers" => $layers, "list" => $datas]);
    }

    private function getLayser(int $id, array &$pIDs, int $layers = 0): int
    {
        if (in_array($id, array_keys($pIDs))) {
            $layer = 0;
            foreach ($pIDs[$id] as $i) {
                $l = $this->getLayser($i, $pIDs, $layers + 1);
                if ($l > $layer) $layer = $l;
            }
            if ($layer > $layers) $layers = $layer;
        }
        return $layers;
    }

    function add()
    {
        if (!isset($_POST["parent_id"]) || !is_numeric($_POST["parent_id"])) return returnAPI([], 1, "param_err");
        if (!isset($_POST["msg"]) || $_POST["msg"] == "") return returnAPI([], 1, "param_empty");
        if (!isset($_POST["sort"]) || !is_numeric($_POST["sort"])) return returnAPI([], 1, "param_err");
        $asDao = new autoservicerep_dao;
        if ($asDao->getSortRepeat($_POST["sort"])) return returnAPI([], 1, "sort_err");
        $insertArr["sort"] = $_POST["sort"];
        $insertArr["parent_id"] = $_POST["parent_id"];
        $insertArr["msg"] = $_POST["msg"];
        $insertArr["onf"] = 0;
        if ($asDao->setMsgInsert($insertArr)) return returnAPI([]);
        return returnAPI([], 1, "add_err");
    }

    function edit()
    {
        if (!isset($_POST["id"]) || !is_numeric($_POST["id"])) return returnAPI([], 1, "param_err");
        if (!isset($_POST["parent_id"]) || !is_numeric($_POST["parent_id"])) return returnAPI([], 1, "param_err");
        if (!isset($_POST["msg"]) || $_POST["msg"] == "") return returnAPI([], 1, "param_empty");
        $id = $_POST["id"];
        $updateArr["parent_id"] = $_POST["parent_id"];
        $updateArr["msg"] = $_POST["msg"];
        if (isset($_POST["sort"]) && is_numeric($_POST["sort"])) $updateArr["sort"] = $_POST["sort"];
        $asDao = new autoservicerep_dao;
        if ($asDao->setMsgUpdate($id, $updateArr)) return returnAPI([]);
        return returnAPI([], 1, "upd_err");
    }

    function editOnf()
    {
        if (!isset($_POST["id"])) return returnAPI([], 1, "param_err");
        $ids = explode(",", $_POST["id"]);
        if (empty($ids)) return returnAPI([], 1, "param_err");
        foreach ($ids as $i) {
            if (!is_numeric($i)) return returnAPI([], 1, "param_err");
        }
        $asDao = new autoservicerep_dao;
        if ($asDao->setOnf($ids)) return returnAPI([]);
        return returnAPI([], 1, "upd_err");
    }

    function delete()
    {
        if (!isset($_POST["id"])) return returnAPI([], 1, "param_err");
        $ids = explode(",", $_POST["id"]);
        if (empty($ids)) return returnAPI([], 1, "param_err");
        foreach ($ids as $i) {
            if (!is_numeric($i)) return returnAPI([], 1, "param_err");
        }
        $asDao = new autoservicerep_dao;
        if ($asDao->setMsgDelete($ids)) return returnAPI([]);
        return returnAPI([], 1, "del_err");
    }
}
