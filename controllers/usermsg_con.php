<?php

namespace app\controllers;

include "./models/usermsg_dao.php";

use app\models\usermsg_dao;

class usermsg_con
{
    function init()
    {
        $usermsgDao = new usermsg_dao;

        $datas = $usermsgDao->getUserMsg($_SESSION["id"]);

        return returnAPI($datas);
    }

    function setUserMsgAdd()
    {
        if (!isset($_POST["tag"]) || $_POST["tag"] == "") return returnAPI([], 1, "param_err");
        $tag = $_POST["tag"];
        if (!isset($_POST["msg"]) || $_POST["msg"] == "") return returnAPI([], 1, "param_err");
        $msg = $_POST["msg"];
        if (!isset($_POST["sort"]) || $_POST["sort"] == "") return returnAPI([], 1, "param_err");
        $sort = $_POST["sort"];

        $usermsgDao = new usermsg_dao;

        if ($usermsgDao->addUserMsg($_SESSION["id"], $tag, $msg, $sort)) {
            return returnAPI([]);
        } else {
            return returnAPI([], 1, "add_err");
        }
    }

    function setUserMsgUpd()
    {
        if (!isset($_POST["id"])) return returnAPI([], 1, "param_err");
        if (empty($_POST["id"])) return returnAPI([], 1, "param_err");
        $id = $_POST["id"];
        if (!isset($_POST["tag"]) || $_POST["tag"] == "") return returnAPI([], 1, "param_err");
        $tag = $_POST["tag"];
        if (!isset($_POST["msg"]) || $_POST["msg"] == "") return returnAPI([], 1, "param_err");
        $msg = $_POST["msg"];
        if (!isset($_POST["sort"]) || $_POST["sort"] == "") return returnAPI([], 1, "param_err");
        $sort = $_POST["sort"];

        $usermsgDao = new usermsg_dao;

        if ($usermsgDao->updUserMsg($_SESSION["id"], $id, $tag, $msg, $sort)) {
            return returnAPI([]);
        } else {
            return returnAPI([], 1, "upd_err");
        }
    }

    function setUserMsgDel()
    {
        if (!isset($_POST["id"])) return returnAPI([], 1, "param_err");
        if (empty($_POST["id"])) return returnAPI([], 1, "param_err");
        $id = $_POST["id"];

        $usermsgDao = new usermsg_dao;

        if ($usermsgDao->delUserMsg($id)) {
            return returnAPI([]);
        } else {
            return returnAPI([], 1, "del_err");
        }
    }
}
