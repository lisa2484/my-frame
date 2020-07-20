<?php

namespace app\controllers;

include "./models/searchautorep_dao.php";

use app\models\searchautorep_dao;

class searchautorep_con
{
    /**
     * 取得智能客服無關鍵字回覆訊息功能列表
     */
    function init()
    {
        if (!isset($_POST["page"]) || !is_numeric($_POST["page"])) return returnAPI([], 1, "param_err");
        if (!isset($_POST["limit"]) || !is_numeric($_POST["limit"])) return returnAPI([], 1, "param_err");
        $page = $_POST["page"];
        $limit = $_POST["limit"];
        $keyword = "";
        if (isset($_POST["keyword"]) && $_POST["keyword"] != "") $keyword = $_POST["keyword"];
        $searchDao = new searchautorep_dao;
        $searchtotal = $searchDao->getSearchAutoRepTotal($keyword);
        $searchdata = $searchDao->getSearchAutoRep($keyword, $page, $limit);
        $totalpage = ceil($searchtotal / $limit);
        if ($page > $totalpage && $totalpage != 0) return returnAPI([], 1, "param_err");
        return returnAPI([
            'total' => $searchtotal,
            'totalpage' => $totalpage,
            'page' => $page,
            'list' => $searchdata
        ]);
    }

    /**
     * 新增
     */
    function setSearchAutoRepAdd()
    {
        if (!isset($_POST["msg"]) || $_POST["msg"] == "") return returnAPI([], 1, "param_err");
        $msg = $_POST["msg"];
        $searchDao = new searchautorep_dao;
        if ($searchDao->addSearchAutoRep($msg)) return returnAPI([]);
        return returnAPI([], 1, "add_err");
    }

    /**
     * 修改
     */
    function setSearchAutoRepUpd()
    {
        if (!isset($_POST["msg"]) || $_POST["msg"] == "") return returnAPI([], 1, "param_err");
        if (!isset($_POST["status"]) || $_POST["status"] == "") return returnAPI([], 1, "param_err");
        if (!isset($_POST["id"]) || !is_numeric($_POST["id"])) return returnAPI([], 1, "param_err");
        $id = $_POST["id"];
        $msg = $_POST["msg"];
        $status = $_POST["status"];
        $searchDao = new searchautorep_dao;
        if ($searchDao->updSearchAutoRep($id, $msg, $status)) return returnAPI([]);
        return returnAPI([], 1, "upd_err");
    }

    /**
     * 刪除
     */
    function setSearchAutoRepDel()
    {
        if (!isset($_POST["id"]) || empty($_POST["id"])) return returnAPI([], 1, "param_err");
        $ids = explode(",", $_POST["id"]);
        foreach ($ids as $id) {
            if (!is_numeric($id)) return returnAPI([], 1, "param_err");
        }
        $searchDao = new searchautorep_dao;
        if ($searchDao->deleteList($ids)) return returnAPI([]);
        return returnAPI([], 1, "del_err");
    }

    /**
     * 開關修改
     */
    function setSearchAutoRepOnf()
    {
        if (!isset($_POST["id"]) || !is_numeric($_POST["id"])) return returnAPI([], 1, "param_err");
        if (!isset($_POST["onf"]) || !in_array($_POST["onf"], [0, 1])) return returnAPI([], 1, "param_err");
        $id = $_POST["id"];
        $onf = $_POST["onf"];
        $searchDao = new searchautorep_dao;
        if ($searchDao->updOnfSwitch($id, $onf)) return returnAPI([]);
        return returnAPI([], 1, "upd_err");
    }
}
