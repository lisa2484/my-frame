<?php

namespace app\sys;

class system
{
    static function start()
    {
        system_datetime::setTime('+0800');
    }
}
