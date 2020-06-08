<?php

namespace app\models;

include './sys/serverset.php';

class DB
{
    static function select($SQLCode)
    {
        $SQL = getServer();
        $data = $SQL->query($SQLCode);
        $dataArr = [];
        while ($reData = mysqli_fetch_assoc($data)) {
            $dataArr[] = $reData;
        }
        $SQL->close();
        return $dataArr;
    }

    static function DBCode($SQLCode)
    {
        $SQL= getServer();
        $request = false;
        if($SQL->query($SQLCode)){
            $request = !$request;
        }
        $SQL->close();
        return $request;
    }
}
