<?php

namespace app\controllers;

include "./models/autoservicerep_dao.php";

use app\models\autoservicerep_dao;

class autoservicerep_con
{
    /**
     * 智能客服訊息設置列表
     */
    function init()
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
                $sDatas[$data["id"]] = [
                    "id" => $data["id"],
                    "parent_id" => $data["parent_id"],
                    "msg" => $data["msg"],
                    "sort" => $data["sort"],
                    "onf" => $data["onf"]
                ];
            }
            $this->setDatas(0, $pIDs, $sDatas, $redata);
            $this->getDatasBySData($redata, $tdatas);
        }
        return returnAPI(["list" => $redata, "datas" => $tdatas]);
    }

    /**
     * 產生多維陣列資料用
     */
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

    /**
     * 解多維陣列用
     */
    private function getDatasBySData(array &$rdata, array &$redata)
    {
        foreach ($rdata as $d) {
            $redata[] = [
                "id" => $d["id"],
                "msg" => $d["msg"],
                "onf" =>  $d["onf"],
                "sort" => $d["sort"],
                "parent_id" => $d["parent_id"]
            ];
            if (isset($d["list"])) $this->getDatasBySData($d["list"], $redata);
        }
    }

    /**
     * 新增
     */
    function add()
    {
        $request = $_POST;
        if (!isset($request["parent_id"]) || !is_numeric($request["parent_id"])) return returnAPI([], 1, "param_err");
        if (!isset($request["msg"]) || $request["msg"] == "") return returnAPI([], 1, "param_empty");
        if (!isset($request["sort"]) || !is_numeric($request["sort"])) return returnAPI([], 1, "param_err");
        if (!isset($request["onf"]) || !in_array($request["onf"], [0, 1])) return returnAPI([], 1, "param_err");
        $insertArr = [
            "sort" => $request["sort"],
            "parent_id" => $request["parent_id"],
            "msg" => $request["msg"],
            "onf" => $request["onf"]
        ];
        $asDao = new autoservicerep_dao;
        if ($asDao->setMsgInsert($insertArr)) return returnAPI([]);
        return returnAPI([], 1, "add_err");
    }

    /**
     * 修改
     */
    function edit()
    {
        $request = $_POST;
        if (!isset($request["id"]) || !is_numeric($request["id"])) return returnAPI([], 1, "param_err");
        if (!isset($request["parent_id"]) || !is_numeric($request["parent_id"])) return returnAPI([], 1, "param_err");
        if (!isset($request["msg"]) || $request["msg"] == "") return returnAPI([], 1, "param_empty");
        if (!isset($request["onf"]) || !in_array($request["onf"], [0, 1])) return returnAPI([], 1, "param_err");
        $id = $request["id"];
        $updateArr = [
            "parent_id" => $request["parent_id"],
            "msg" => $request["msg"],
            "onf" => $request["onf"]
        ];
        if (isset($request["sort"]) && is_numeric($request["sort"])) $updateArr["sort"] = $request["sort"];
        $asDao = new autoservicerep_dao;
        if ($asDao->setMsgUpdate($id, $updateArr)) return returnAPI([]);
        return returnAPI([], 1, "upd_err");
    }

    /**
     * 修改開關
     */
    function editOnf()
    {
        $request = $_POST;
        if (!isset($request["id"])) return returnAPI([], 1, "param_err");
        if (!isset($request["onf"]) || !in_array($request["onf"], [0, 1])) return returnAPI([], 1, "param_err");
        $ids = explode(",", $request["id"]);
        if (empty($ids)) return returnAPI([], 1, "param_err");
        foreach ($ids as $i) {
            if (!is_numeric($i)) return returnAPI([], 1, "param_err");
        }
        $asDao = new autoservicerep_dao;
        if ($asDao->setOnf($ids, $request["onf"])) return returnAPI([]);
        return returnAPI([], 1, "upd_err");
    }

    /**
     * 刪除
     */
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
