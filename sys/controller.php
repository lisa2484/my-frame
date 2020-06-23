<?php
function view($view, $push = array())
{
    if ($view != 'login') {
        foreach (array_keys($push) as $key) {
            $$key = $push[$key];
        }
        include './views/' . $view . '.php';
    } else {
        include './login.php';
    }
}

function json(array $array)
{
    return json_encode($array);
}

function returnAPI(array $data, int $status = 0, string $errmsg = "")
{
    $arr["Status"] = $status;
    $arr["ip"] = getRemoteIP();
    $arr["ErrorMessage"] = (empty($errmsg) ? "" : ErrorMessage::getErrMsg($errmsg));
    $arr["Data"] = $data;
    return json_encode($arr);
}
