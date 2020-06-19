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
        return json($asDao->getWebData());
    }

    function add()
    {
        if (!key_exists("parent_id", $_POST) || !is_numeric($_POST["parent_id"])) return false;
        if (!key_exists("title", $_POST) || $_POST["title"] == "") return false;
        if (!key_exists("msg", $_POST) || $_POST["msg"] == "") return false;
        $insertArr = [];
        $insertArr["parent_id"] = $_POST["parent_id"];
        $insertArr["title"] = $_POST["title"];
        $insertArr["msg"] = $_POST["msg"];
        if (key_exists("onf", $_POST) && in_array($_POST["onf"], [0, 1])) $insertArr["onf"] = $_POST["onf"];
        if (key_exists("sort", $_POST) && is_numeric($_POST["sort"])) $insertArr["sort"] = $_POST["sort"];
        $asDao = new autoservicerep_dao;
        return $asDao->setMsgInsert($insertArr);
    }

    function edit()
    {
        if (!key_exists("id", $_POST) || !is_numeric($_POST["id"])) return false;
        if (!key_exists("parent_id", $_POST) || !is_numeric($_POST["parent_id"])) return false;
        if (!key_exists("title", $_POST) || $_POST["title"] == "") return false;
        if (!key_exists("msg", $_POST) || $_POST["msg"] == "") return false;
        $id = $_POST["id"];
        $updateArr = [];
        $updateArr["parent_id"] = $_POST["parent_id"];
        $updateArr["title"] = $_POST["title"];
        $updateArr["msg"] = $_POST["msg"];
        if (key_exists("onf", $_POST) && in_array($_POST["onf"], [0, 1])) $updateArr["onf"] = $_POST["onf"];
        if (key_exists("sort", $_POST) && is_numeric($_POST["sort"])) $updateArr["sort"] = $_POST["sort"];
        $asDao = new autoservicerep_dao;
        return $asDao->setMsgUpdate($id, $updateArr);
    }

    function delete()
    {
        if (!key_exists("id", $_POST) || !is_numeric($_POST["id"])) return false;
        $id = $_POST["id"];
        $asDao = new autoservicerep_dao;
        return $asDao->setMsgDelete($id);
    }
}
