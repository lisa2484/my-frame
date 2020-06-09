<?php

namespace app;

include "./sys/controller.php";
include "./sys/mysqlDB.php";
include "./sys/verify.php";

use app\verify;

class route
{
    function init()
    {
        session_start();
        //注入防禦
        $this->unInjection(getServer());
        $script_name = $_SERVER["SCRIPT_NAME"];
        $script_name = str_replace("index.php", "", $script_name);
        $request_url = $_SERVER["REQUEST_URI"];
        $url = str_replace($script_name, "", $request_url);
        $verify = true;
        $versys = new verify;
        if (!$versys->isVerfy($verify)) {
            return false;
        }
        if (!empty($url) && $url != "/") {
            $urlArr = preg_split("/\//", $url);
            $routes = $this->Routes($urlArr[0]);
            $get = key_exists(1, $urlArr) ? $urlArr[1] : $urlArr[0];
            $route = key_exists($get, $routes) ? $routes[$get] : $get;      //取得路由位置
            if ($route != null) {
                $routeArr = preg_split("/\//", $route);
                $con = $routeArr[0];                                        //controller
                $fun =  key_exists(1, $routeArr) ? $routeArr[1] : null;     //function
                $classStr = "app\controllers\\" . $con;
                include "./controllers/" . $con . ".php";
                $class = new $classStr();
                if ($fun != null) {
                    return $class->$fun();
                } else {
                    return $class->init();                                  //無function時的進入點init
                }
            } else {
                return "error";
            }
        } else {
            $classStr = "app\controllers\main_con";
            include "./controllers/main_con.php";
            $class = new $classStr();
            return $class->init();
        }
    }

    /**
     * 路由群組設定
     * @return array[GroupName] routesPHPName
     */
    function RouteGroups()
    {
        include "./routes/route_groups.php";

        return $routeGroups;
    }

    function Routes($route)
    {
        $routeGroups = $this->RouteGroups();
        if (isset($route) && isset($routeGroups[$route])) {
            include "./routes/" . $routeGroups[$route] . ".php";
        } else {
            include "./routes/main.php";
        }
        return $routes;
    }

    function unInjection($dbcon)
    {
        foreach ($_POST as $k => $d) {
            $d = mysqli_real_escape_string($dbcon, htmlentities(trim($d)));
            $_POST[$k] = $d;
        }
        $dbcon->close();
    }
}
