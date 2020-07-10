<?php

namespace app\controllers;

include "./models/usermsg_dao.php";
include "./models/user_dao.php";

use app\models\usermsg_dao;
use app\models\user_dao;

class usermsg_con
{
    function init()
    {
        $tag = "";
        if (isset($_POST["tag"])) $tag = $_POST["tag"];
        if (!isset($_POST["page"])) return returnAPI([], 1, "param_err");
        $page = $_POST["page"];
        if (!isset($_POST["limit"])) return returnAPI([], 1, "param_err");
        $limit = $_POST["limit"];

        $userDao = new user_dao;
        $usermsgDao = new usermsg_dao;

        $ndatas = $userDao->getUserByID($_SESSION["id"]);

        $msgtotal = $usermsgDao->getUserMsgTotal($_SESSION["id"], $tag);

        $totalpage = ceil($msgtotal / $limit);
        if ($totalpage == 0) {
            if ($page != 1) {
                return returnAPI([], 1, "param_err");
            }
        } else {
            if ($page > $totalpage) return returnAPI([], 1, "param_err");
        }

        $datas = $usermsgDao->getUserMsg($_SESSION["id"], $tag, $limit, $page);

        $data_arr = array(
            'nickname' => $ndatas[0]['user_name'],
            'photo' => getImgUrl("", $ndatas[0]['img_name']),
            "total" => $msgtotal,
            "totalpage" => $totalpage,
            "page" => $page,
            'msglist' => $datas
        );

        return returnAPI($data_arr);
    }

    /**
     * 個人設置-常用語設置
     */
    function setUserMsgAdd()
    {
        if (!isset($_POST["tag"]) || $_POST["tag"] == "") return returnAPI([], 1, "param_err");
        $tag = $_POST["tag"];
        if (!isset($_POST["msg"]) || $_POST["msg"] == "") return returnAPI([], 1, "param_err");
        $msg = $_POST["msg"];
        if (!isset($_POST["sort"]) || $_POST["sort"] == "" || !is_numeric($_POST["sort"])) return returnAPI([], 1, "param_err");
        $sort = $_POST["sort"];

        $usermsgDao = new usermsg_dao;

        if ($usermsgDao->getSort($sort) != 0) return returnAPI([], 1, "sort_err");
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
        if (!isset($_POST["sort"]) || $_POST["sort"] == "" || !is_numeric($_POST["sort"])) return returnAPI([], 1, "param_err");
        $sort = $_POST["sort"];

        $usermsgDao = new usermsg_dao;

        if ($sort != $usermsgDao->getSortValue($id)) {
            if ($usermsgDao->getSort($sort) != 0) return returnAPI([], 1, "sort_err");
        }
        
        if ($usermsgDao->updUserMsg($_SESSION["id"], $id, $tag, $msg, $sort)) {
            return returnAPI([]);
        } else {
            return returnAPI([], 1, "upd_err");
        }
    }

    function setUserMsgDel()
    {
        if (!isset($_POST["id"])) return returnAPI([], 1, "param_err");
        if (empty($_POST["id"])) return returnAPI([], 1, "param_empty");

        $ids = explode(",", $_POST["id"]);

        foreach ($ids as $id) {
            if (!is_numeric($id)) return returnAPI([], 1, "param_err");
        }

        $usermsgDao = new usermsg_dao;

        if ($usermsgDao->delUserMsg($ids)) return returnAPI([]);
        return returnAPI([], 1, "del_err");
    }

    /**
     * 個人設置-基本設置
     */
    function setUserNickName()
    {
        if (!isset($_POST["nickname"]) || $_POST["nickname"] == "") return returnAPI([], 1, "param_err");
        $nickname = $_POST["nickname"];

        $userDao = new user_dao;

        if ($userDao->setUserName($_SESSION["id"], $nickname)) {
            return returnAPI([]);
        } else {
            return returnAPI([], 1, "upd_err");
        }
    }

    function setUserPhoto()
    {
        $filename = "";
        if (!empty($_FILES)) {
            if (!updateImg($filename, "", "userphoto_")) return returnAPI([], 1, "upload_err");
        }

        $userDao = new user_dao;

        if ($userDao->updUserPhoto($_SESSION["id"], $filename)) {
            return returnAPI([]);
        } else {
            return returnAPI([], 1, "upd_err");
        }
    }
}
