<?php
// echo var_dump($data);
// foreach($datas as $data){
// echo var_dump($datas);
// }
function arrayPush($j)
{
    $Arr = [];
    for ($i = 0; $i < $j; $i++) {
        // array_push($Arr, $i);
        $k = '12123132ssssssssfasfsawerwetqdfdbfbdfd'.$i.'gg132132121'.$i;
    }
}
function arrayNewPush($j)
{
    $Arr = [];
    for ($i = 0; $i < $j; $i++) {
        // $Arr[] = $i;
        $k = "12123132ssssssssfasfsawerwetqdfdbfbd".$i."fdgg132132121".$i;
    }
}
$ti = 10000000;
$ts = microtime(true);
arrayPush($ti);
$te = microtime(true);
echo $t = $te - $ts;
echo '<br>';
$ts = microtime(true);
arrayNewPush($ti);
$te = microtime(true);
echo $t = $te - $ts;