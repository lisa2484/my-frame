<?php

namespace app;

class routes
{
    function init()
    {
        $redirect_url = substr($_SERVER["REDIRECT_URL"], 1);
        $script_name = substr($_SERVER["SCRIPT_NAME"], 1);
        // var_dump($_SERVER);
        $script_name = str_replace("/index.php", "", $script_name);
        $url = str_replace($script_name, "", $redirect_url);
        $url = substr($url, 0, 1) == "/" ? substr($url, 1) : $url;
        if (!empty($url) && $url != "/") {
            $urlArr = preg_split("/\//", $url);
            $routes = $this->Routes(isset($urlArr[1]) ? $urlArr[0] : null);
            $get = isset($urlArr[1]) ? $urlArr[1] : $urlArr[0];
            $route = key_exists($get, $routes) ? $routes[$get] : null;      //取得路由位置
            if ($route != null) {
                $routeArr = preg_split("/\//", $route);
                $con = $routeArr[0];                                        //controller
                $fun =  key_exists(1, $routeArr) ? $routeArr[1] : null;     //function
                $classStr = "app\controllers\\" . $con;
                include_once "./controllers/" . $con . ".php";
                $class = new $classStr();
                if ($fun != null) {
                    return $class->$fun();
                } else {
                    return $class->init();                                  //無function時的進入點init
                }
            } else {
                echo "<div>error</div>";
            }
        } else {
            $classStr = "app\controllers\main_con";
            include_once "./controllers/main_con.php";
            $class = new $classStr();
            return $class->init();
        }
    }

    /**
     * 路由位置設定
     * @return array[fun] value controller/menu_setting
     */
    function Routes($route = null)
    {
        if (!empty($route) && is_file("./routes/" . $route . ".php")) {
            include "./routes/" . $route . ".php";
            return $routes;
        } else {
            include "./routes/main.php";
            return $routes;
        }
    }
}
