<?php
error_reporting(0);
$o = $_GET["o"];
$d = $_GET["d"];
//$json = "http://maps.googleapis.com/maps/api/distancematrix/json?origins=-26.325237,27.86641&destinations=-26.2436006,27.8876854";
$json = "http://maps.googleapis.com/maps/api/distancematrix/json?origins=$o&destinations=$d";
$jsonfile = file_get_contents($json);

$thing=(json_decode($jsonfile, true));
//var_dump($thing);
//$res = $thing["elements"];
//$output["result"]=$thing["elements"][0]["distance"];


//echo $output;
$val["result"] = ($thing["rows"][0]["elements"][0]["distance"]["value"]);
if ($val["result"]==null){
$val["result"]=-1;
}
//var_dump($val);
echo $val["result"];
?>