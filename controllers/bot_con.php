<?php

namespace app\controllers;

class bot_con
{
    function init()
    {
        return "";
    }

    private function proceess()
    {
        if (!key_exists("say", $_POST) || empty($_POST["say"])) return $this->getGreet();
        $say = $_POST["say"];
        $reply = "";
        if ($this->getQuestion($say, $reply)) return $reply;
        if ($this->getKeyWords($say, $reply)) return $reply;
        return $this->getNoResult();
        var_dump($_SERVER);
    }

    private function XXS()
    {
        if (!key_exists("a", $_SESSION)) $_SESSION["a"] = 0;
        $_SESSION["a"] = $_SESSION["a"] + 1;
        $script = '<script>
                    $("button").click();
                   </script>';
        if ($_SESSION["a"] >= 10) {
            $_SESSION["a"] = 0;
            $script = "";
        }
        $html = '<img src="..' . dirname($_SERVER["SCRIPT_NAME"]) . '/resources/img/16319866' . $_SESSION["a"] . '.png">';
        return $html . $script;
    }

    private function getGreet(): string
    {
        return "";
    }

    private function getKeyWords($say, &$reply): bool
    {
        return true;
    }

    private function getQuestion($say, &$reply): bool
    {
        return true;
    }

    private function getNoResult(): string
    {
        return "";
    }
}
