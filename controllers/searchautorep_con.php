<?php

namespace app\controllers;

include "./models/searchautorep_dao.php";

use app\models\searchautorep_dao;

class searchautorep_con
{
    function init()
    {
        $keyword = isset($_POST["keyword"]) ? $_POST["keyword"] : "";
        if (!isset($_POST["page"])) return returnAPI([], 1, "param_err");
        $page = $_POST["page"];
        if (!isset($_POST["limit"])) return returnAPI([], 1, "param_err");
        $limit = $_POST["limit"];

        $searchDao = new searchautorep_dao;
        $msgtotal = $searchDao->getSearchAutoRepTotal($keyword);
        $msgdata = $searchDao->getSearchAutoRep($keyword, $page, $limit);

        $totalpage = ceil($msgtotal / $limit);
        if ($totalpage > 0) {
            if ($page > $totalpage) return returnAPI([], 1, "param_err");
        }

        $data_arr = array(
            'total' => $msgtotal,
            'totalpage' => $totalpage,
            'page' => $page,
            'list' => $msgdata
        );

        return returnAPI($data_arr);
    }

    function setSearchAutoRepAdd()
    {
        if (!isset($_POST["msg"]) || $_POST["msg"] == "") return returnAPI([], 1, "param_err");
        $msg = $_POST["msg"];

        $searchDao = new searchautorep_dao;

        if ($searchDao->addSearchAutoRep($msg)) {
            return returnAPI([]);
        } else {
            return returnAPI([], 1, "add_err");
        }
    }

    function setSearchAutoRepUpd()
    {
        if (!isset($_POST["id"])) return returnAPI([], 1, "param_err");
        if (empty($_POST["id"])) return returnAPI([], 1, "param_err");
        $id = $_POST["id"];
        if (!isset($_POST["msg"]) || $_POST["msg"] == "") return returnAPI([], 1, "param_err");
        $msg = $_POST["msg"];
        if (!isset($_POST["status"]) || $_POST["status"] == "") return returnAPI([], 1, "param_err");
        $status = $_POST["status"];

        $searchDao = new searchautorep_dao;

        if ($searchDao->updSearchAutoRep($id, $msg, $status)) {
            return returnAPI([]);
        } else {
            return returnAPI([], 1, "upd_err");
        }
    }

    function setSearchAutoRepDel()
    {
        if (!isset($_POST["id"])) return returnAPI([], 1, "param_err");
        if (empty($_POST["id"])) return returnAPI([], 1, "param_err");
        $id = $_POST["id"];

        $searchDao = new searchautorep_dao;

        if ($searchDao->delSearchAutoRep($id)) {
            return returnAPI([]);
        } else {
            return returnAPI([], 1, "del_err");
        }
    }
}
