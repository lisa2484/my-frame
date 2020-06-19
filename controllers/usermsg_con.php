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

        return json($datas);
    }

    function setUserMsgAdd()
    {
        if (!isset($_POST["tag"]) || $_POST["tag"] == "") return false;
        $tag = $_POST["tag"];
        if (!isset($_POST["msg"]) || $_POST["msg"] == "") return false;
        $msg = $_POST["msg"];
        if (!isset($_POST["sort"]) || $_POST["sort"] == "") return false;
        $sort = $_POST["sort"];

        $usermsgDao = new usermsg_dao;

        return $usermsgDao->addUserMsg($_SESSION["id"], $tag, $msg, $sort);
    }

    function setUserMsgUpd()
    {
        if (!isset($_POST["id"])) return false;
        if (empty($_POST["id"])) return false;
        $id = $_POST["id"];
        if (!isset($_POST["tag"]) || $_POST["tag"] == "") return false;
        $tag = $_POST["tag"];
        if (!isset($_POST["msg"]) || $_POST["msg"] == "") return false;
        $msg = $_POST["msg"];
        if (!isset($_POST["sort"]) || $_POST["sort"] == "") return false;
        $sort = $_POST["sort"];

        $usermsgDao = new usermsg_dao;

        return $usermsgDao->updUserMsg($_SESSION["id"], $id, $tag, $msg, $sort);
    }

    function setUserMsgDel()
    {
        if (!isset($_POST["id"])) return false;
        if (empty($_POST["id"])) return false;
        $id = $_POST["id"];

        $usermsgDao = new usermsg_dao;

        return $usermsgDao->delUserMsg($id);
    }
}
