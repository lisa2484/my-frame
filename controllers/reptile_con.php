<?php

namespace app\controllers;


class reptile_con
{
    function init()
    {
        // $c = curl_init();
        // curl_setopt($c, CURLOPT_URL,'https://www.google.com/search?biw=1201&bih=891&sxsrf=ACYBGNSK8XA_dKvU1lXtbpWT38AiMGMo-Q%3A1577420331652&ei=K4YFXtu2J8qsmAXa0ZT4AQ&q=curl_exec+406&oq=curl_exec+406&gs_l=psy-ab.3..35i39.0.0..709788...0.0..0.119.189.1j1......0......gws-wiz.6MYtVvMpMmQ&ved=0ahUKEwib-J3U_NTmAhVKFqYKHdooBR8Q4dUDCAs&uact=5');
        // curl_setopt($c,CURLOPT_RETURNTRANSFER,1);
        // curl_setopt($c,CURLOPT_USERAGENT,"Mozilla/4.0");
        // curl_setopt($c,CURLOPT_HTTPHEADER,array('Content-Type: text/xml'));
        // $data = curl_exec($c);
        // if(curl_errno($c)){
        //     echo 'false';
        // }
        // curl_close($c);
        // $komica = '';
        // $data = $this->getUrlContent('https://www.google.com/search?biw=1201&bih=891&sxsrf=ACYBGNSK8XA_dKvU1lXtbpWT38AiMGMo-Q%3A1577420331652&ei=K4YFXtu2J8qsmAXa0ZT4AQ&q=curl_exec+406&oq=curl_exec+406&gs_l=psy-ab.3..35i39.0.0..709788...0.0..0.119.189.1j1......0......gws-wiz.6MYtVvMpMmQ&ved=0ahUKEwib-J3U_NTmAhVKFqYKHdooBR8Q4dUDCAs&uact=5');
        // $xmlpc = xml_parser_create();
        // xml_parse_into_struct($xmlpc, $data, $datas);
        // xml_parser_free($xmlpc);
        return view('reptile_view');
    }


    function getUrlContent($url)
    { // 初始化一個curl會話
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_REFERER, 'http://www.phpxs.com');
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
        // curl_setopt($ch, CURLOPT_POST, 1); //設定為POST方式
        // curl_setopt($ch, CURLOPT_POSTFIELDS, array()); //資料傳輸
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); //解決重定向問題
        curl_setopt($ch, CURLOPT_COOKIE, 'redirectLogin=3;t=1766da7fa03df9fdb66af1ebaa160ecc;'); // 執行一個curl會話
        $contents = curl_exec($ch); // 返回一個保護當前會話最近一次錯誤的字串
        $error = curl_error($ch);
        curl_close($ch);
        if ($error) {
            return 'Error: ' . $error;
        } // 關閉一個curl會話
        return $contents;
    }
}
