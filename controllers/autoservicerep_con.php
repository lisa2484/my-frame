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
        $redata = [];
        $tdatas = [];
        if (!empty($datas)) {
            $pIDs = [];
            foreach ($datas as $data) {
                $pIDs[$data["parent_id"]][] = $data["id"];
            }
            $sDatas = [];
            foreach ($datas as $data) {
                $sDatas[$data["id"]] = ["id" => $data["id"], "parent_id" => $data["parent_id"], "msg" => $data["msg"], "sort" => $data["sort"], "onf" => $data["onf"]];
            }
            $this->setDatas(0, $pIDs, $sDatas, $redata);
            $this->getDatasBySData($redata, $tdatas);
        }
        return returnAPI(["list" => $redata, "datas" => $tdatas]);
    }

    private function setDatas(int $id, array &$pIDs, array &$datas, array &$rdata, array &$out = [])
    {
        if (in_array($id, array_keys($pIDs))) {
            foreach ($pIDs[$id] as $i) {
                $r = [];
                if (!in_array($id, $out)) {
                    $r["id"] = $datas[$i]["id"];
                    $r["msg"] = $datas[$i]["msg"];
                    $r["sort"] = $datas[$i]["sort"];
                    $r["onf"] = $datas[$i]["onf"];
                    $r["parent_id"] = $datas[$i]["parent_id"];
                }
                $r["list"] = [];
                $this->setDatas($i, $pIDs, $datas, $r["list"], $out);
                if (empty($r["list"])) unset($r["list"]);
                $rdata[] = $r;
            }
        }
        $out[] = $id;
    }

    private function getDatasBySData(array &$rdata, array &$redata)
    {
        foreach ($rdata as $k => $d) {
            $redata[] = ["id" => $d["id"], "msg" => $d["msg"], "onf" =>  $d["onf"], "sort" => $d["sort"], "parent_id" => $d["parent_id"]];
            if (isset($d["list"])) $this->getDatasBySData($d["list"], $redata);
        }
    }

    function add()
    {
        if (!isset($_POST["parent_id"]) || !is_numeric($_POST["parent_id"])) return returnAPI([], 1, "param_err");
        if (!isset($_POST["msg"]) || $_POST["msg"] == "") return returnAPI([], 1, "param_empty");
        if (!isset($_POST["sort"]) || !is_numeric($_POST["sort"])) return returnAPI([], 1, "param_err");
        if (!isset($_POST["onf"]) || !in_array($_POST["onf"], [0, 1])) return returnAPI([], 1, "param_err");
        $asDao = new autoservicerep_dao;
        $insertArr["sort"] = $_POST["sort"];
        $insertArr["parent_id"] = $_POST["parent_id"];
        $insertArr["msg"] = $_POST["msg"];
        $insertArr["onf"] = $_POST["onf"];
        if ($asDao->setMsgInsert($insertArr)) return returnAPI([]);
        return returnAPI([], 1, "add_err");
    }

    function edit()
    {
        if (!isset($_POST["id"]) || !is_numeric($_POST["id"])) return returnAPI([], 1, "param_err");
        if (!isset($_POST["parent_id"]) || !is_numeric($_POST["parent_id"])) return returnAPI([], 1, "param_err");
        if (!isset($_POST["msg"]) || $_POST["msg"] == "") return returnAPI([], 1, "param_empty");
        if (!isset($_POST["onf"]) || !in_array($_POST["onf"], [0, 1])) return returnAPI([], 1, "param_err");
        $id = $_POST["id"];
        $updateArr["parent_id"] = $_POST["parent_id"];
        $updateArr["msg"] = $_POST["msg"];
        $updateArr["onf"] = $_POST["onf"];
        if (isset($_POST["sort"]) && is_numeric($_POST["sort"])) $updateArr["sort"] = $_POST["sort"];
        $asDao = new autoservicerep_dao;
        if ($asDao->setMsgUpdate($id, $updateArr)) return returnAPI([]);
        return returnAPI([], 1, "upd_err");
    }

    function editOnf()
    {
        if (!isset($_POST["id"])) return returnAPI([], 1, "param_err");
        if (!isset($_POST["onf"]) || !in_array($_POST["onf"], [0, 1])) return returnAPI([], 1, "param_err");
        $ids = explode(",", $_POST["id"]);
        if (empty($ids)) return returnAPI([], 1, "param_err");
        foreach ($ids as $i) {
            if (!is_numeric($i)) return returnAPI([], 1, "param_err");
        }
        $asDao = new autoservicerep_dao;
        if ($asDao->setOnf($ids, $_POST["onf"])) return returnAPI([]);
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
