<?php
class ErrorMessage
{
    static function getErrMsg($key)
    {
        return key_exists($key, self::errmsgarr()) ? self::errmsgarr()[$key] : "non error message";
    }

    private static function errmsgarr()
    {
        $arr = [];
        $arr["user_empty"] = "该使用者帐号已不存在";
        $arr["aut_empty"] = "该权限群组已不存在";
        $arr["route_err"] = "请确认网址是否正确";
        $arr["class_err"] = "该功能不存在";
        $arr["function_err"] = "该方法不存在";
        $arr["login_logout"] = "您已登出";
        $arr["login_do"] = "请先登入";
        $arr["login_fail"] = "帐号或密码错误";
        $arr["login_timeout"] = "连线已逾时请重新登入";
        $arr["login_another"] = "此帐号已从其他电脑登入";
        $arr["ip_fail"] = "IP位置错误";
        $arr["aut_fail"] = "您没有权限浏览此页面";
        return $arr;
    }
}
