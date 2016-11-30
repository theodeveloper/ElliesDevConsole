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
  mysql_close($db_handle);
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

function getRidOfOld($checklist)
{
	$qID = $checklist["quote_id"];
	$question = $checklist["question_id"];
	$room = $checklist["room"];
	$sql = "DELETE FROM quote_checklist WHERE quote_id = $qID AND question_id = $question AND room = '$room'";
	//print_r($sql);
	mysqli_query($db_handle,$sql);
}

db_connect();
$test = getallheaders();
$myList=$test["json"];
$output2 = json_decode($myList, true);
$checklist=$output2["checklist_answers"];

$checklist_answers = json_decode($checklist, true);
foreach ($checklist_answers as $key=> $answer){
	getRidOfOld($answer);
    $query = makeInsert($answer, "quote_checklist");
    mysqli_query($db_handle,$sql);
}

echo "success";
db_close();

?>