<?php

namespace app\controllers;

include "./models/bot_dao.php";
include "./resources/tool/SCtable.php";

use app\models\bot_dao;
use SCtable;

class bot_con
{
    function init()
    {
        return SCtable::translate($this->proceess(), "CN");
    }

    private function proceess()
    {
        if (!key_exists("say", $_POST) || empty($_POST["say"])) return $this->getGreet();
        $say = $_POST["say"];
        $reply = "";
        if ($this->getQuestion($say, $reply)) return $reply;
        if ($this->getKeyWords($say, $reply)) return $reply;
        return $this->getNoResult();
    }

    private function img()
    {
        if (!key_exists("a", $_SESSION)) $_SESSION["a"] = 0;
        $_SESSION["a"] = $_SESSION["a"] + 1;
        if ($_SESSION["a"] >= 10) {
            $_SESSION["a"] = 0;
        }
        $html = '<img src="..' . dirname($_SERVER["SCRIPT_NAME"]) . '/resources/img/16319866' . $_SESSION["a"] . '.png">';
        return $html;
    }

    private function getGreet(): string
    {
        $btd = new bot_dao;
        return $btd->getBotGreet();
    }

    private function getKeyWords($say, &$reply): bool
    {
        $btd = new bot_dao;
        $keys = $btd->getBotKeyWords();
        foreach ($keys as $k => $d) {
            if (mb_strstr($say, $k)) {
                $reply = $d;
                return true;
            }
        }
        return false;
    }

    private function getQuestion($say, &$reply): bool
    {
        return false;
    }

    private function getNoResult(): string
    {
        $btd = new bot_dao;
        return $btd->getBotNoResult();
    }
}
