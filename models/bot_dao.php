<?php

namespace app\models;

class bot_dao
{
    static private $table = "bot_glossary";
    static private $keyTable = "";

    function getBotGreet()
    {
        $GreetArr = [];
        $GreetArr[] = "請說";
        $GreetArr[] = "你想問什麼?";
        $GreetArr[] = "你想知道什麼?";
        return $GreetArr[random_int(0, count($GreetArr) - 1)];
    }

    function setBotGreet()
    {
    }

    function getBotKeyWords()
    {
        $keys["知道"] = "或許我知道，或許我不知道";
        $keys["紅包"] = "給我紅包";
        // $keys[""] = "";
        return $keys;
    }

    function setBotKeyWords()
    {
    }

    function getBotNoResult()
    {
        $result[] = "呃....";
        $result[] = "嗯....?";
        $result[] = "我不懂你的意思";
        return $result[random_int(0, count($result) - 1)];
    }
}
