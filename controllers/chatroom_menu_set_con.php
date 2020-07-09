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

        $menudata = [];
        foreach ($datas as $keys => $values) {
            foreach ($datas[$keys] as $key => $value) {
                if ($key == "filename") {
                    $menudata[$keys][$key] = getImgUrl("chatroom_menu", $value);
                } else {
                    $menudata[$keys][$key] = $value;
                }
            }
        }

        return returnAPI($menudata);
    }

    function add()
    {
        $cmDao = new chatroom_menu_dao;

        if (!key_exists("title", $_POST) || $_POST["title"] == "") return returnAPI([], 1, "param_empty");
        if (!key_exists("url", $_POST) || $_POST["url"] == "") return returnAPI([], 1, "param_empty");
        if (key_exists("sort", $_POST)) {
            if ($_POST["sort"] == "" || !is_numeric($_POST["sort"])) return returnAPI([], 1, "param_err");
            $sort = $_POST["sort"];
        } else {
            $sort = $cmDao->getMaxSort() + 1;
        }

        if (empty($_FILES)) return returnAPI([], 1, "param_empty");
        if (!is_numeric($sort)) return returnAPI([], 1, "add_err");
        $filename = "";
        if (!updateImg($filename, "/chatroom_menu", "crmn_")) return returnAPI([], 1, "upload_err");
        if ($cmDao->getSort($sort) != 0) return returnAPI([], 1, "sort_err");
        $insertArr["title"] = $_POST["title"];
        $insertArr["url"] = $_POST["url"];
        $insertArr["filename"] = $filename;
        $insertArr["sort"] = $sort;
        if ($cmDao->setMenuInsert($insertArr)) return returnAPI([]);
        return returnAPI([], 1, "add_err");
    }

    function set()
    {
        $cmDao = new chatroom_menu_dao;

        if (!key_exists("id", $_POST) || !is_numeric($_POST["id"])) return returnAPI([], 1, "param_err");
        $id = $_POST["id"];
        $title = "";
        $url = "";
        $sort = "";
        if (key_exists("title", $_POST)) $title = $_POST["title"];
        if (key_exists("url", $_POST)) $url = $_POST["url"];
        if (key_exists("sort", $_POST)) {
            if ($_POST["sort"] == "" || !is_numeric($_POST["sort"])) return returnAPI([], 1, "param_err");
            $sort = $_POST["sort"];
            if ($cmDao->getSort($sort) != 0) return returnAPI([], 1, "sort_err");
        }
        $updateArr = [];
        if ($title != "") $updateArr["title"] = $title;
        if ($url != "") $updateArr["url"] = $url;
        if ($sort != "") $updateArr["sort"] = $sort;
        if (empty($updateArr) && empty($_FILES)) return returnAPI([], 1, "param_empty");
        $filename = "";
        if (!empty($_FILES)) {
            if (!updateImg($filename, "/chatroom_menu", "crmn_")) return returnAPI([], 1, "upload_err");
        }
        if ($filename != "") $updateArr["filename"] = $filename;

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
}
