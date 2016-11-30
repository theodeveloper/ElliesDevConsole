<?php

ini_set('memory_limit','512M');

error_reporting(0);
// error_reporting(E_ALL);
$dir = dirname(__FILE__).'/../function_library.php';
$exclude_array = array('branch_owner');
include $dir;
// include '../function_library.php';


$table=$_GET["table"];
// echo "$table<br/>";
db_connect();
if ($table=="all"){
$res = get_fake_tables();
} else if($table=="list"){
  $res=get_table_names();
} else {
$res = get_table($table);
}

echo json_encode($res);
db_close();



