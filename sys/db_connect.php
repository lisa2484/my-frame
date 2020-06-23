<?php

class DBConnect
{
    protected static function getServer()
    {
        $mysql = new mysqli('localhost', 'root', '', 'laravel');
        mysqli_query($mysql, "SET NAMES utf8");
        mysqli_query($mysql, "SET CHARACTER_SET_CLIENT=utf8");
        mysqli_query($mysql, "SET CHARACTER_SET_RESULTS=utf8");
        return $mysql;
    }
}
