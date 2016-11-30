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

function db_connect(){
global $db_user, $db_pass, $db_host, $db_name, $db_handle;
$db_handle = mysql_connect($db_host, $db_user, $db_pass) 
 or die("Unable to connect to MySQL");
//echo "Connected to MySQL<br>";

//select a database to work with
$selected = mysql_select_db($db_name,$db_handle) 
  or die("Could not open database");
}

function db_close(){
global $db_handle;
//close the connection
mysql_close($db_handle);
}

db_connect();
$test = getallheaders();
$myList=$test["Json"];
$output2 = json_decode($myList, true);
$locationsJson=$output2["locations"];
//echo "string";
// print_r($test);
// exit();

$locations = json_decode($locationsJson, true);
foreach ($locations as $key=> $location){
    $query = makeInsert($location, "locations");
    mysql_query($query);
}

function makeInsert($assocArray, $table){
   $prefix = "INSERT INTO $table";
   $columns = "";
   $values = "";

   foreach ($assocArray as $column => $value){
        if (($column!="id")&&($column!="dirty")){
	  $columns = $columns.$column.",";
	  $values = $values."'$value',";
        }
   }
   $columns = rtrim($columns, ",");
   $values = rtrim($values,",");
   $query = "$prefix($columns) VALUES ($values)";
   return $query;
   
}
echo "success";
db_close();

?>