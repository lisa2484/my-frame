<?php

namespace app\controllers;

include "./models/chatroom_menu_dao.php";

use app\models\chatroom_menu_dao;

class chatroom_menu_set_con
{
    /**
     * 聊天室menu設定列表
     */
    function init()
    {
        $cmDao = new chatroom_menu_dao;
        $datas = $cmDao->getMenuSet();
        $maxSort = 0;
        $menudata = [];
        foreach (array_keys($datas) as $keys) {
            foreach ($datas[$keys] as $key => $value) {
                switch ($key) {
                    case "filename":
                        $menudata[$keys][$key] = getImgUrl("chatroom_menu", $value);
                        break;
                    case "sort":
                        if ($maxSort < $value) $maxSort = $value;
                    default:
                        $menudata[$keys][$key] = $value;
                }
            }
        }

        return returnAPI(["list" => $menudata, "next_sort" => $maxSort + 1]);
    }

    /**
     * 新增
     */
    function add()
    {
        $cmDao = new chatroom_menu_dao;

        if (!isset($_POST["title"]) || $_POST["title"] == "") return returnAPI([], 1, "param_empty");
        if (!isset($_POST["url"]) || $_POST["url"] == "") return returnAPI([], 1, "param_empty");
        if (!isset($_POST["sort"]) || !is_numeric($_POST["sort"])) return returnAPI([], 1, "param_err");
        if (empty($_FILES)) return returnAPI([], 1, "param_empty");
        $sort = $_POST["sort"];
        $filename = "";
        if (!updateImg($filename, "chatroom_menu", "crmn_")) return returnAPI([], 1, "upload_err");
        if ($cmDao->getSortRepeat($sort)) return returnAPI([], 1, "sort_err");
        $insertArr = [
            "title" => $_POST["title"],
            "url" => $_POST["url"],
            "filename" => $filename,
            "sort" => $sort
        ];
        if ($cmDao->setMenuInsert($insertArr)) return returnAPI([]);
        return returnAPI([], 1, "add_err");
    }

    /**
     * 修改
     */
    function set()
    {
        if (!isset($_POST["id"]) || !is_numeric($_POST["id"])) return returnAPI([], 1, "param_err");
        if (!isset($_POST["sort"]) || !is_numeric($_POST["sort"]))  return returnAPI([], 1, "param_err");
        $id = $_POST["id"];
        $title = "";
        $url = "";
        $sort = $_POST["sort"];
        $cmDao = new chatroom_menu_dao;
        if ($cmDao->getSortRepeat($sort, $id)) return returnAPI([], 1, "sort_err");
        if (isset($_POST["title"])) $title = $_POST["title"];
        if (isset($_POST["url"])) $url = $_POST["url"];
        $filename = "";
        if (!empty($_FILES)) {
            if (!updateImg($filename, "chatroom_menu", "crmn_")) return returnAPI([], 1, "upload_err");
        }
        $updateArr["sort"] = $sort;
        if ($title != "") $updateArr["title"] = $title;
        if ($url != "") $updateArr["url"] = $url;
        if ($filename != "") $updateArr["filename"] = $filename;
        if ($cmDao->setMenuUpdate($id, $updateArr)) return returnAPI([]);
        return returnAPI([], 1, "upd_err");
    }

    /**
     * 刪除
     */
    function delete()
    {
        if (!isset($_POST["id"])) return returnAPI([], 1, "param_err");
        $ids = explode(",", $_POST["id"]);
        if (empty($ids)) return returnAPI([], 1, "param_err");
        foreach ($ids as $i) {
            if (!is_numeric($i)) return returnAPI([], 1, "param_err");
        }
        $cmDao = new chatroom_menu_dao;
        if ($cmDao->setDelete($ids)) return returnAPI([]);
        return returnAPI([], 1, "del_err");
    }
}
