<?php

namespace app\controllers;

include "./models/chatroom_menu_dao.php";

use app\models\chatroom_menu_dao;

class chatroom_menu_set_con
{
    function init()
    {
        return $this->get();
    }

    function get()
    {
        $cmDao = new chatroom_menu_dao;
        $datas = $cmDao->getMenuSet();
        return json($datas);
    }

    function add()
    {
        if (!key_exists("title", $_POST) || $_POST["title"] = "") return false;
        if (!key_exists("url", $_POST) || $_POST["url"] = "") return false;
        if (empty($_FILES)) return false;
        $cmDao = new chatroom_menu_dao;
        if (!key_exists("sort", $_POST)) {
            $sort = $cmDao->getMaxSort() + 1;
        } else {
            $sort = $_POST["sort"];
        }
        if (!is_numeric($sort)) return false;
        $filename = "";
        if (!$this->updateFile($filename)) return false;
        $insertArr["title"] = $_POST["title"];
        $insertArr["url"] = $_POST["url"];
        $insertArr["filename"] = $filename;
        $insertArr["sort"] = $sort;
        return $cmDao->setMenuInsert($insertArr);
    }

    function set()
    {
        if (key_exists("title", $_POST)) $title = $_POST["title"];
        if (key_exists("url", $_POST)) $url = $_POST["url"];
    }

    function delete()
    {
        if (!key_exists("id", $_POST) || !is_numeric($_POST["id"])) return false;
        $cmDao = new chatroom_menu_dao;
        return $cmDao->setDelete($_POST["id"]);
    }

    private function updateFile(&$filename)
    {
        if (empty($_FILES)) return false;
        $type = pathinfo($_FILES["file"]["name"]);
        if (!isset($type["extension"])) return false;
        if (!in_array($type["extension"], ["jpg", "gif", "jpeg", "png", "bmp"])) return false;
        $path = "./resources/chatroom_menu";
        if (!is_dir($path)) {
            mkdir($path);
        }
        $crmn = "crmn" . date("YmdHis") . "." . $type["extension"];
        $filename = $crmn;
        return move_uploaded_file($_FILES["file"]["tmp_name"], "$path/$crmn");
    }
}
