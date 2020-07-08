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
        $arr["userset_act_empty"] = "帐号不可为空";
        $arr["userset_act_spcr"] = "帐号不可使用特殊字元";
        $arr["userset_pwd_empty"] = "密码不可为空";
        $arr["userset_aut_empty"] = "权限设定不可为空";
        $arr["userset_add_repeat"] = "该帐号名称已经被使用";
        $arr["userset_add_fail"] = "使用者增加失败";
		$arr["userset_pwd_repeat"] = "新密码不可与旧密码相同";
		$arr["action_log_err"] = "功能记录失败，将不进行任何修改";
		$arr["chatroom_create_err"] = "聊天室建立失败";
		$arr["chatroom_exist"] = "聊天室已存在";
		$arr["chatroom_empty"] = "聊天室尚未建立";
		$arr["chatroom_insert_err"] = "聊天室留言失败";
		$arr["chatroom_over"] = "此聊天室已关闭";
		$arr["service_empty"] = "此客服已离线";
		$arr["service_transfer_err"] = "转交失败，请确认伺服器状态后在试一次";
		//mysql
		$arr["sql_err"] = "资料库错误";

        //參數
        $arr["param_err"] = "参数错误";
		$arr["param_empty"] = "参数不可为空";

        //執行動作
        $arr["add_err"] = "新增失败";
        $arr["upd_err"] = "修改失败";
        $arr["del_err"] = "删除失败";

        //修改密碼
        $arr["editpwd_oldpwd_err"] = "原密码错误";

        //智能客服訊息配置
        $arr["botset_length_err"] = "长度错误";
        $arr["botset_val_err"] = "值错误";
        $arr["botset_set_err"] = "设定错误";

        //IP白名單
        $arr["whitelist_add_err"] = "IP重复";
        $arr["whitelist_setswitch_err"] = "设定错误";

        //個人設置
        $arr["upload_err"] = "上传错误";
        $arr["sort_err"] = "排序重复";

        //客戶畫面配置
        $arr["file_err"] = "檔案错误";

        return $arr;
    }
}
