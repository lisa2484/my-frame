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
        $userDao = new user_dao;
        $usermsgDao = new usermsg_dao;

        $ndatas = $userDao->getUserNickName($_SESSION["id"]);
        $datas = $usermsgDao->getUserMsg($_SESSION["id"]);

        $data_arr = array(
            'nickname' => $ndatas[0]['user_name'],
            'photo' => "resources/img/" . $ndatas[0]['img_name'],
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

        if ($usermsgDao->getSort($sort) != 0) return returnAPI([], 1, "sort_err");
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
            if (!$this->updateFile($filename)) return returnAPI([], 1, "upload_err");
        }

        $userDao = new user_dao;

        if ($userDao->updUserPhoto($_SESSION["id"], $filename)) {
            return returnAPI([]);
        } else {
            return returnAPI([], 1, "upd_err");
        }
    }

    private function updateFile(&$filename)
    {
        if (empty($_FILES)) return false;
        $type = pathinfo($_FILES["file"]["name"]);
        if (!isset($type["extension"])) return false;
        if (!in_array($type["extension"], ["jpg", "gif", "jpeg", "png", "bmp"])) return false;
        $path = "./resources/img";
        if (!is_dir($path)) {
            mkdir($path);
        }
        $crmn = "userphoto" . date("YmdHis") . "." . $type["extension"];
        $filename = $crmn;
        return move_uploaded_file($_FILES["file"]["tmp_name"], "$path/$crmn");
    }
}
