<?php

namespace app;

include "./sys/controller.php";
include "./sys/mysqlDB.php";
include "./sys/verify.php";
include "./sys/tool.php";
include "./sys/db_connect.php";

class route extends verify
{
    function init()
    {
        session_start();
        $this->setTimeZone();
        //注入防禦
        $this->unInjection(getServer());
        $script_name = $_SERVER["SCRIPT_NAME"];
        $script_name = str_replace("index.php", "", $script_name);
        $request_url = $_SERVER["REQUEST_URI"];
        $url = str_replace($script_name, "", $request_url);
        if (!empty($url) && $url != "/") {
            $urlArr = preg_split("/\//", $url);
            $verify = true;
            if (!$this->isVerfy($verify, $urlArr[0])) {
                return false;
            }
            $routes = $this->Routes($urlArr[0]);
            if (empty($routes) || !isset($routes["init"])) return "init or routes error";
            $get = key_exists(1, $urlArr) ? $urlArr[1] : $urlArr[0];
            if (empty($get) || ($get != $urlArr[0] && !key_exists($get, $routes))) return "route error";
            $route = key_exists($get, $routes) ? $routes[$get] : $routes["init"];      //取得路由位置
            if (empty($route)) return "error";
            $routeArr = preg_split("/\//", $route);
            $con = $routeArr[0];                                        //controller
            $fun =  key_exists(1, $routeArr) ? $routeArr[1] : null;     //function
            $classStr = "app\controllers\\" . $con;
            $classfile = "./controllers/" . $con . ".php";
            if (!is_file($classfile)) return "classfile error";
            include($classfile);
            if (!class_exists($classStr)) return "class error";
            $class = new $classStr();
            if ($fun != null) {
                if (!method_exists($class, $fun)) return "function error";
                return $class->$fun();
            }
            if (!method_exists($class, "init")) return "function error";
            return $class->init();                                  //無function時的進入點init
        } else {
            if (!$this->isVerfy(true, "")) {
                return false;
            }
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
        $routes = [];
        $routeGroups = $this->RouteGroups();
        if (isset($route) && isset($routeGroups[$route])) {
            include "./routes/" . $routeGroups[$route] . ".php";
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

    // function setTimeZone()
    // {
    //     DB::select("SELECT * FROM `web_set` ");
    // }

    function chkAuthority()
    {
    }
}
