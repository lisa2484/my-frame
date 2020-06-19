<?php

namespace app\controllers;

include "./models/searchautorep_dao.php";

use app\models\searchautorep_dao;

class searchautorep_con
{
    function init()
    {
        if (!isset($_POST["page"])) return false;
        $page = $_POST["page"];
        if (!isset($_POST["limit"])) return false;
        $limit = $_POST["limit"];

        $searchDao = new searchautorep_dao;
        $msgtotal = $searchDao->getSearchAutoRepTotal();
        $msgdata = $searchDao->getSearchAutoRep($page, $limit);

        $data_arr = array(
            'total' => $msgtotal,
            'page' => $page,
            'data' => $msgdata
        );

        return json($data_arr);
    }

    function setSearchAutoRepAdd()
    {
        if (!isset($_POST["msg"]) || $_POST["msg"] == "") return false;
        $msg = $_POST["msg"];

        $searchDao = new searchautorep_dao;

        return $searchDao->addSearchAutoRep($msg);
    }

    function setSearchAutoRepUpd()
    {
        if (!isset($_POST["id"])) return false;
        if (empty($_POST["id"])) return false;
        $id = $_POST["id"];
        if (!isset($_POST["msg"]) || $_POST["msg"] == "") return false;
        $msg = $_POST["msg"];
        if (!isset($_POST["status"]) || $_POST["status"] == "") return false;
        $status = $_POST["status"];

        $searchDao = new searchautorep_dao;

        return $searchDao->updSearchAutoRep($id, $msg, $status);
    }

    function setSearchAutoRepDel()
    {
        if (!isset($_POST["id"])) return false;
        if (empty($_POST["id"])) return false;
        $id = $_POST["id"];

        $searchDao = new searchautorep_dao;

        return $searchDao->delSearchAutoRep($id);
    }
}
