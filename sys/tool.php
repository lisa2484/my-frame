<?php

function getRemoteIP()
{
    if (function_exists('apache_request_headers')) {
        $headers = apache_request_headers();
    } else {
        $headers = $_SERVER;
    }

    //Get the forwarded IP if it exists
    if (array_key_exists('X-Forwarded-For', $headers) && filter_var($headers['X-Forwarded-For'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
        $the_ip = $headers['X-Forwarded-For'];
    } elseif (array_key_exists('HTTP_X_FORWARDED_FOR', $headers) && filter_var($headers['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
        $the_ip = $headers['HTTP_X_FORWARDED_FOR'];
    } else {
        $the_ip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
    }
    return $the_ip;
}

function validateDate($date, $format = 'Y-m-d H:i:s')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

function updateImg(&$filename, string $url, string $name = "", $key = "file")
{
    if (empty($_FILES)) return false;
    $type = pathinfo($_FILES[$key]["name"]);
    if (!isset($type["extension"])) return false;
    if (!in_array(strtolower($type["extension"]), ["jpg", "gif", "jpeg", "png", "bmp"])) return false;
    $path = "./resources/img/" . $url;
    if (!is_dir($path)) {
        mkdir($path, 0777, true);
    }
    $filename = $name . date("YmdHis") . "." . $type["extension"];
    return move_uploaded_file($_FILES[$key]["tmp_name"], "$path/$filename");
}

/**
 * 取得圖片路徑
 * @param string $dir 資料夾名稱/資料夾名稱
 * @param string $fileName 檔案名稱
 */
function getImgUrl(string $dir, string $fileName): string
{
    return "/resources/img/" . $dir . "/" . $fileName;
}
