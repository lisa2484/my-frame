<?php

namespace app;
// error_reporting(0);
include "./sys/db_connect.php";
include "./sys/controller.php";
include "./sys/mysqlDB.php";
include "./sys/verify.php";
include "./sys/tool.php";
include "./sys/errmsg.php";

use app\models\DB;

class route extends verify
{
    function init()
    {
        session_start();
        DB::dbCon();
        $this->setTimeZone();
        $this->unInjection(DB::getDBCon());
        $script_name = $_SERVER["SCRIPT_NAME"];
        $script_name = str_replace("index.php", "", $script_name);
        $request_url = $_SERVER["REQUEST_URI"];
        $url = str_replace($script_name, "", $request_url);
        if (!empty($url) && $url != "/") {
            $urlArr = preg_split("/\//", $url);
            if (!$this->isVerfy($urlArr[0])) {
                $rep = $this->getErrMsg();
                return returnAPI([], $rep["status"], $rep["msg"]);
            }
            $routes = $this->Routes($urlArr[0]);
            if (empty($routes) || !isset($routes["init"])) return returnAPI([], 1, "route_err");
            $get = key_exists(1, $urlArr) ? $urlArr[1] : $urlArr[0];
            if (empty($get) || ($get != $urlArr[0] && !key_exists($get, $routes))) return returnAPI([], 1, "route_err");
            $route = key_exists($get, $routes) ? $routes[$get] : $routes["init"];      //取得路由位置
            if (empty($route)) return returnAPI([], 1, "route_err");
            $routeArr = preg_split("/\//", $route);
            $con = $routeArr[0];                                        //controller
            $fun =  key_exists(1, $routeArr) ? $routeArr[1] : null;     //function
            $classStr = "app\controllers\\" . $con;
            $classfile = "./controllers/" . $con . ".php";
            if (!is_file($classfile)) return returnAPI([], 1, "class_err");
            include($classfile);
            if (!class_exists($classStr)) return returnAPI([], 1, "class_err");
            $class = new $classStr();
            if ($fun != null) {
                if (!method_exists($class, $fun)) return returnAPI([], 1, "function_err");
                return $class->$fun();
            }
            if (!method_exists($class, "init")) return returnAPI([], 1, "function_err");
            return $class->init();                                  //無function時的進入點init
        } else {
            if (!$this->isVerfy()) {
                $rep = $this->getErrMsg();
                return returnAPI([], $rep["status"], $rep["msg"]);
            }
            $classStr = "app\controllers\main_con";
            $classfile = "./controllers/main_con.php";
            if (!is_file($classfile)) return returnAPI([], 1, "class_err");
            include $classfile;
            if (!class_exists($classStr)) return returnAPI([], 1, "class_err");
            $class = new $classStr();
            if (!method_exists($class, "init")) return returnAPI([], 1, "function_err");
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
    }
}
