<?php

/*$db_user = "cltdrx_ellsdev";
$db_pass = "JzESNju8";
$db_host = "dedi48.jnb1.host-h.net";
$db_name = "cltdrx_ellsdev";
$db_user = "root";
$db_pass = "";
$db_host = "localhost";
$db_name = "ellies";
$db_handle;*/

include 'dbaccess.php';

error_reporting(0);
// error_reporting(E_ALL|E_NOTICE);

function db_connect(){
  global $db_user, $db_pass, $db_host, $db_name, $db_handle;
  $db_handle = mysqli_connect($db_host, $db_user, $db_pass) 
   or die("Unable to connect to MySQL");
  //echo "Connected to MySQL<br>";

  //select a database to work with
  $selected = mysqli_select_db($db_handle,$db_name) 
    or die("Could not open database");
}

function db_close(){
	global $db_handle;
	//close the connection
	mysqli_close($db_handle);
}

function makeInsert($assocArray, $table){
   $prefix = "INSERT INTO $table";
   $columns = "";
   $values = "";

  if (!empty($_SERVER['HTTP_CLIENT_IP'])) 
  {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
  } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
  } else {
    $ip = $_SERVER['REMOTE_ADDR'];
  }

  foreach ($assocArray as $column => $value){
    if (in_array($column,array("id","synced"))) continue;
    $columns = $columns.$column.",";
    $values = $values."'$value',";
  }

  $column="ip_address";
  $value=$ip;

  $columns = $columns.$column.",";
  $values = $values."'$value',";

  $columns = rtrim($columns, ",");
  $values = rtrim($values,",");
  $query = "$prefix($columns) VALUES ($values)";
  return $query;
}
//not working
db_connect();
$test = getallheaders();
$myList=$test["Json"];
$output2 = json_decode($myList, true);
$user_logs=$output2["user_log_in"];
// print_r($user_logs);
$logs_decoded = json_decode($user_logs, true);
// $var = 1;
foreach ($logs_decoded as $key=> $log){
    // echo $var.")\n";
    $sql = makeInsert($log, "user_log_in");
    // echo $sql."\n";
    // $var++;
    mysqli_query($db_handle,$sql);
}

echo "success";
db_close();