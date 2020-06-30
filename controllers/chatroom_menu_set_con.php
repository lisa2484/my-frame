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
        return returnAPI($datas);
    }

    function add()
    {
        if (!key_exists("title", $_POST) || $_POST["title"] = "") return returnAPI([], 1, "param_empty");
        if (!key_exists("url", $_POST) || $_POST["url"] = "") return returnAPI([], 1, "param_empty");
        if (empty($_FILES)) return returnAPI([], 1, "param_empty");
        $cmDao = new chatroom_menu_dao;
        $sort = $cmDao->getMaxSort() + 1;
        if (!is_numeric($sort)) return returnAPI([], 1, "add_err");
        $filename = "";
        if (!$this->updateFile($filename)) return returnAPI([], 1, "add_err");
        $insertArr["title"] = $_POST["title"];
        $insertArr["url"] = $_POST["url"];
        $insertArr["filename"] = $filename;
        $insertArr["sort"] = $sort;
        if ($cmDao->setMenuInsert($insertArr)) return returnAPI([]);
        return returnAPI([], 1, "add_err");
    }

    function set()
    {
        if (!key_exists("id", $_POST) || !is_numeric($_POST["id"])) return returnAPI([], 1, "param_err");
        $id = $_POST["id"];
        if (key_exists("title", $_POST)) $title = $_POST["title"];
        if (key_exists("url", $_POST)) $url = $_POST["url"];
        $updateArr = [];
        if ($title != "") $updateArr["title"] = $title;
        if ($url != "") $updateArr["url"] = $url;
        if (empty($updateArr) && empty($_FILES)) return returnAPI([], 1, "param_empty");
        $filename = "";
        if (!empty($_FILES)) {
            if (!$this->updateFile($filename)) return returnAPI([], 1, "upd_err");
        }
        if ($filename != "") $updateArr["filename"] = $filename;
        $cmDao = new chatroom_menu_dao;
        if ($cmDao->setMenuUpdate($id, $updateArr)) return returnAPI([]);
        return returnAPI([], 1, "upd_err");
    }

    function setSort()
    {
        if (!key_exists("sort", $_POST) || empty($_POST["sort"])) return returnAPI([], 1, "param_err");
        $ids = explode(",", $_POST["sort"]);
        foreach ($ids as $id) {
            if (!is_numeric($id)) return returnAPI([], 1, "param_err");
        }
        $sort = 0;
        $cmDao = new chatroom_menu_dao;
        foreach ($ids as $id) {
            if (!$cmDao->setMenuUpdate($id, ["sort" => $sort])) return returnAPI([], 1, "upd_err");
            $sort++;
        }
        return returnAPI([]);
    }

    function delete()
    {
        if (!key_exists("id", $_POST) || !is_numeric($_POST["id"])) return returnAPI([], 1, "param_err");
        $cmDao = new chatroom_menu_dao;
        if ($cmDao->setDelete($_POST["id"])) return returnAPI([]);
        return returnAPI([], 1, "del_err");
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
