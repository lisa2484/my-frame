<?php

namespace app\controllers;

include "./models/user_commonmsg_dao.php";

use app\models\user_commonmsg_dao;

class user_commonmsg_con
{
    function init()
    {
        $commsgDao = new user_commonmsg_dao;

        $datas = $commsgDao->getCommonMsg($_SESSION["id"]);

        return json($datas);
    }

    function setUserCommonAdd()
    {
        if (!isset($_POST["tag"]) || $_POST["tag"] == "") return false;
        $tag = $_POST["tag"];
        if (!isset($_POST["msg"]) || $_POST["msg"] == "") return false;
        $msg = $_POST["msg"];
        if (!isset($_POST["sort"]) || $_POST["sort"] == "") return false;
        $sort = $_POST["sort"];

        $commsgDao = new user_commonmsg_dao;

        return $commsgDao->addCommonMsg($_SESSION["id"], $tag, $msg, $sort);
    }

    function setUserCommonUpd()
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

        $commsgDao = new user_commonmsg_dao;

        return $commsgDao->updCommonMsg($_SESSION["id"], $id, $tag, $msg, $sort);
    }

    function setUserCommonDel()
    {
        if (!isset($_POST["id"])) return false;
        if (empty($_POST["id"])) return false;
        $id = $_POST["id"];

        $commsgDao = new user_commonmsg_dao;

        return $commsgDao->delCommonMsg($id);
    }
}
