<?php
namespace app\controllers;
include_once "./sys/controller.php";

class init_con
{
    function init()
    {
        return view("welcome");
    }
}