<?php

$db_user = "cltdrx_ellsdev";
$db_pass = "JzESNju8";
$db_host = "dedi48.jnb1.host-h.net";
$db_name = "cltdrx_ellsdev";
$db_handle;

error_reporting(0);

//connection to the database
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
$toEmail=array();
$test = getallheaders();
$myList=$test["json"];
$output2 = json_decode($myList, true);

$customersJson=$output2["customers"];
$quotesJson=$output2["quotes"];
$quote_itemsJson=$output2["quote_items"];
$locationsJson=$output2["locations"];

$locations = json_decode($locationsJson, true);
foreach ($locations as $key=> $location){
     $query = makeInsert($location, "locations");
     mysql_query($query);
}


$customers = json_decode($customersJson, true);
$customermap=array();
foreach ($customers as $key => $customer){
	$query="";
	if ($customer["dirty"]===1){
		$query = makeUpdate($customer, "customers");
		mysql_query($query);
	} else {
		$query = makeInsert($customer, "customers");
		mysql_query($query);
		$customermap[$customer["id"]]=mysql_insert_id();
	}
}

$quotes=json_decode($quotesJson, true);
$quotemap=array();
foreach ($quotes as $key => $quote){
	$oldId = $quote["customer_id"];
	if (isset($customermap[$oldId])){
     		$newId = $customermap[$oldId];
		$quote["customer_id"]=$newId;
	}
	$query = makeInsert($quote, "quotes");
	mysql_query($query);
	$qid = mysql_insert_id();
	$quotemap[$quote["id"]]=$qid;
       $ref = "INQ".str_pad($qid, 6, "0", STR_PAD_LEFT);
	mysql_query("Update quotes set ref='$ref' where id='$qid'");
	mysql_query("insert into quote_settings select '$qid',rental.* from rental");
	$complete=$quote["complete"];
	$channel=$quote["channel"];
	if (($complete==1)&&($channel==0)){
		$toEmail[$qid]=$qid;
	}
}

$quoteItems=json_decode($quote_itemsJson, true);
foreach ($quoteItems as $key => $quoteItem){
	$oldId = $quoteItem["quote_id"];
	$newId = $quotemap[$oldId];
	$quoteItem["quote_id"]=$newId; 
	$query = makeInsert($quoteItem, "quote_items");
	mysql_query($query);
}
//function invokes emailer
//This function passes new quotes to the PHPmailer stored in the elliesAdmin section
//located under ElliesAdmin/quotes/emailquote.php
//the garbel of text acts as a private key authentication
foreach ($toEmail as $key => $val){
		error_log("Quote no: $val is complete");
		//file_get_contents('http://elliesdev.clientassist.co.za/quotes/emailquote.php?bp=qwepoiasdlkjzxcmnb&id='.$val);
}

function makeUpdate($assocArray, $table){
   $query = "UPDATE $table set ";
   foreach ($assocArray as $column => $value){
        if (($column!="id")&&($column!="dirty")){
	    $query=$query."$column='$value',";
        }
   }
   $query = rtrim($query, ",");
   $query = $query." where id='".$assocArray["id"]."'";
   return $query;
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
