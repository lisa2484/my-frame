<?php

namespace app;

use app\models\DB;

class serverset
{
    function setTimeZone()
    {
        $timezoneset = DB::select("SELECT `value` FROM `web_set` WHERE `set_key` = 'web_timezone'");
        if (empty($timezoneset) || empty($timezoneset[0]["value"])) {
            date_default_timezone_set("America/Blanc-Sablon");
        } else {
            date_default_timezone_set('Asia/Taipei');
        }
    }
}
