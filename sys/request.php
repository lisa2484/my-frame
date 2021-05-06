<?php

namespace app\sys;

class request
{
    function __construct()
    {
        $post = $_POST;
        foreach ($post as $k => $d) {
            $this->$k = htmlentities($d);
        }
    }

    function Int(string $key): ?int
    {
        return $this->$key;
    }

    function Float(string $key): ?float
    {
        return $this->$key;
    }

    function String(string $key): ?string
    {
        return $this->$key;
    }

    function Json(string $key): array
    {
        $arr = [];
        if (isset($this->$key)) {
            $req = json_decode($this->$key, true);
            if (isset($req) && $req !== false)
                $arr = $req;
        }
        return $arr;
    }
}
