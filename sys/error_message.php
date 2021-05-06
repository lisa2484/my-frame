<?php

namespace app\sys;

class error_message
{
    static function getErrMsg($key)
    {
        if (!is_string($key)) return $key;
        $str = self::errmsgarr($key);
        return empty($str) ? $key : $str;
    }

    private static function errmsgarr(string $key): string
    {
        $arr = [
            'fail_add' => '新增失败',
            'fail_update' => '修改失败',
            'fail_delete' => '删除失败',
            'error_param' => '参数错误',
            'empty_param' => '参数不可为空',
            'fail_authority' => '功能权限验证失败',
        ];
        return $arr[$key];
    }
}
