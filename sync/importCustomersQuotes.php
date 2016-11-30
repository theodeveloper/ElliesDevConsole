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
// error_reporting(E_ALL);

//connection to the database
function db_connect()
{
	global $db_user, $db_pass, $db_host, $db_name, $db_handle;
	$db_handle = mysqli_connect($db_host, $db_user, $db_pass) 
	 or die("Unable to connect to MySQL");
	//echo "Connected to MySQL<br>";

	//select a database to work with
	$selected = mysqli_select_db($db_handle,$db_name)
	  or die("Could not open database");
}

function db_close()
{
	global $db_handle;
	//close the connection
	mysqli_close($db_handle);
}


/*
  Do a check to see what version of the app is running
  pull into the script that corelates to that version
*/
if(isset($_GET['v']))
{
  $version_number = $_GET['v'];
  // echo "is v set?:".$version_number;

  $target_version = substr($version_number,0,strrpos($version_number,'.'));
  
  if($target_version=='1.1')
  {
    include 'v1.1/importCustomersQuotes.php';
    die();
  }
  else{
    //nothing
  }
}

// echo "before connect";
db_connect();
// echo "after connect";
$toEmail=array();
//echo "test1";
$test = getallheaders();
//echo "test2";
// print_r($test);exit();
$myList=$test["Json"];
//echo "test3";
$output2 = json_decode($myList, true);
//echo "test4";
$customersJson=$output2["customers"];
$quotesJson=$output2["quotes"];
$quote_itemsJson=$output2["quote_items"];
$quote_checklist = $output2["quote_checklist"];

/*echo "testFile:\n";
print_r($test);
echo "\n";
echo "myList:\n";
print_r($myList);
echo "\n";
echo "output:\n";
print_r($output2);
echo "\n";
exit;*/
$customers = json_decode($customersJson, true);
$customermap=array();
//if(is_array($customers))
foreach ($customers as $key => $customer){
	$query="";
	if ($customer["dirty"]==1){
		$query = makeUpdateCustomer($customer, "customers");
		mysqli_query($db_handle,$query);
	} else {
		$query = makeInsertCustomer($customer, "customers");
		mysqli_query($db_handle,$query);
		$customermap[$customer["id"]]=mysqli_insert_id($db_handle);
	}
}

$quotes=json_decode($quotesJson, true);
$quotemap=array();
//if(is_array($quote))
foreach ($quotes as $key => $quote){
	//here is our new line
	//if the image_hashes is set, get it. if not, blank array
	global $db_handle;
	$quoteImageHashJson=isset($quote["image_hashes"])?$quote["image_hashes"]:array();
	$quoteImageTransfer=isset($quote["Transfers"])?$quote["Transfers"]:array();

	$oldId = $quote["customer_id"];
	if (isset($customermap[$oldId])){
     		$newId = $customermap[$oldId];
		$quote["customer_id"]=$newId;
	}
	$query = makeInsert($quote, "quotes");
	mysqli_query($db_handle,$query);
	$qid = mysqli_insert_id($db_handle);
	$quotemap[$quote["id"]]=$qid;
       $ref = "INQ".str_pad($qid, 6, "0", STR_PAD_LEFT);
	mysqli_query($db_handle,"Update quotes set ref='$ref' where id='$qid'");
	mysqli_query($db_handle,"insert into quote_settings select '$qid',rental.* from rental");
	$complete=$quote["complete"];
	$channel=$quote["channel"];
	if (($complete==1)&&($channel==0)){
		$toEmail[$qid]=$qid;
	}
	if(mysqli_affected_rows($db_handle)<1)
		echo 'Failed';
	//if not a blank array, go through the array.  
	if (sizeof($quoteImageHashJson)) {//array consits only of hash values associated with that quote
		//save to database
		foreach ($quoteImageHashJson AS $hash) {
			$tempval = (int)$qid;
			if((int)$tempval!=0)
			mysqli_query($db_handle,"INSERT INTO quote_images(quote_id,hash) VALUES('$qid','{$hash}')");
		}
	}
	if(sizeof($quoteImageTransfer))
	{
		foreach ($quoteImageTransfer as $key => $jsonObject) {
			$tranfer = $jsonObject;//used to be a json_decode here. aparently the first decode already split the arrays.
			$insertNew = "INSERT INTO quote_images(quote_id,hash,FileName,Room,Status)" .
							"Values ($qid,'".$tranfer["hash"]."','".$tranfer["FileName"].
								"','".$tranfer["Room"]."',0)";
			$tempval = (int)$qid;
			if((int)$tempval!=0)
			mysqli_query($db_handle,$insertNew);
		}
	}
}

$quoteItems=json_decode($quote_itemsJson, true);
//if(is_array($quoteItems))
foreach ($quoteItems as $key => $quoteItem){
	$oldId = $quoteItem["quote_id"];
	$newId = $quotemap[$oldId];
	$quoteItem["quote_id"]=$newId; 
	$query = makeInsert($quoteItem, "quote_items");
	mysqli_query($db_handle,$query);
}

$quoteChecklist=json_decode($quote_checklist,true);
//if(is_array($quoteChecklist))
foreach ($quoteChecklist as $key => $checklist) {//for each checklist in the set
	$oldId = $checklist["quote_id"];//get the currently stored ID
	$newId = $quotemap[$oldId];//match that with the newly generated ID (might be the same)
	$checklist["quote_id"] = $newId;//swap out the new ID
	$query = makeInsert($checklist, "quote_checklist");//make the insert
	mysqli_query($db_handle,$query);//Ding!
}


//function invokes emailer
//This function passes new quotes to the PHPmailer stored in the elliesAdmin section
//located under ElliesAdmin/quotes/emailquote.php
//the garbel of text acts as a private key authentication
//if(is_array($toEmail))
foreach ($toEmail as $key => $val){
		error_log("Quote no: $val is complete");
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
      if (in_array($column,array("id","dirty","image_hashes","Transfers"))) continue;

	  $columns = $columns.$column.",";
	  $values = $values."'$value',";
   }
   $columns = rtrim($columns, ",");
   $values = rtrim($values,",");
   $query = "$prefix($columns) VALUES ($values)";
   return $query;
   
}

function makeInsertCustomer($assocArray, $table){
   $prefix = "INSERT INTO $table";
   $columns = "";
   $values = "";

   foreach ($assocArray as $column => $value){
      if (in_array($column,array("id","dirty","image_hashes","Transfers","date_created","last_accessed"))) continue;

	  $columns = $columns.$column.",";
	  $values = $values."'$value',";
   }
   $columns = $columns."date_created,last_accessed";
   //$columns = rtrim($columns, ",");
   $values = $values."now(),now()";
   //$values = rtrim($values,",");
   $query = "$prefix($columns) VALUES ($values)";
   return $query;
   
}

function makeUpdateCustomer($assocArray, $table){
   $query = "UPDATE $table set ";
   foreach ($assocArray as $column => $value){
        if (in_array($column,array("id","dirty","last_accessed"))) continue;
        
	    $query=$query."$column='$value',";
   }
   //$query = rtrim($query, ",");
   $query = $query."last_accessed=now() ";
   $query = $query." where id='".$assocArray["id"]."'";
   return $query;
}

echo "success";
db_close();


?>