<?php
date_default_timezone_set('Asia/Taipei');
include './sys/route.php';

use app\route;

$route = new route;
$echo = $route->init();
if (is_bool($echo)) {
    echo $echo ? "true" : "false";
} else {
    echo $echo;
}
