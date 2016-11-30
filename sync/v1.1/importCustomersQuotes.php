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

include './dbaccess.php';

$hash_pairs = array();
$to_hash_pairs = array();

error_reporting(0);
//error_reporting(E_ALL);

//connection to the database
/*function db_connect()
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
}*/

db_connect();
$toEmail=array();
//echo "test1";
$test = getallheaders();
//echo "test2";
//print_r($test);exit();
$myList=$test["Json"];
//echo "test3";
$output2 = json_decode($myList, true);
//echo "test4";
$customersJson=$output2["customers"];
$quotesJson=$output2["quotes"];
$quote_itemsJson=$output2["quote_items"];
$quote_checklist = $output2["quote_checklist"];


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
	if ($complete==1){
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
include 'quoteItemMatch.php';
$itemMatcher = new quoteItemMatch();

foreach ($quoteItems as $key => $quoteItem){
	$oldId = $quoteItem["quote_id"];
	$newId = $quotemap[$oldId];
	$quoteItem["quote_id"]=$newId; 
	// $quoteItem["parent_quote_item_id"] = $itemMatcher->match($quoteItem['id']);
	//match parent IDs if needed
	$item_hash = $quoteItem['HashTag'];
	unset($quoteItem['HashTag']);//destroy the hashtag, no longer needed once it maps, cannot be inserted into the database
	$quoteItem['parent_quote_item_id'] = -1;
	$query = makeInsert($quoteItem, "quote_items");
	mysqli_query($db_handle,$query);

	$qid = mysqli_insert_id($db_handle);
	if($item_hash!="-1" && $quoteItem['old_product_id']!="-1")
	{//must be a parent item
		add_parent($item_hash,$qid);
	}
	elseif ($item_hash!="-1" && $quoteItem['old_product_id']=="-1") {
		//must be child
		add_decendant($item_hash,$qid);
	}
	//else nothing
	// $itemMatcher->add($quoteItem['id'],$qid);
}
pair();

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
	//Automated Sending of quotes to retail only
	/*$sql = "SELECT `type`";
	$sql.= " FROM `channels` INNNER JOIN `quotes` ON `quotes`.channel =`channels`.id";
	$sql .= "WHERE `quotes`.id=".$val." LIMIT 1";
	$sqlres = mysqli_query($db_handle,$sql);
	$row = mysqli_fetch_assoc($sqlres);
	$retail = $row['type'];
	$retail = strtolower($retail);*/

	//Use CURL to send quote emails
	//if($retail=="retail"){
		$url = 'http://elliesdev.clientassist.co.za/quotes/emailquote.php?app=true&id='.$val;
		
		$ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $url); 
        curl_setopt($ch, CURLOPT_HEADER, TRUE); 
        curl_setopt($ch, CURLOPT_NOBODY, TRUE); // remove body 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
        $head = curl_exec($ch); 
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
        curl_close($ch); 
	//}
}

function add_parent($parentHash, $ParentID)
{
	global $hash_pairs;
	$hash_pairs["{$parentHash}"]=$ParentID;
}

function add_decendant($decendant_hash,$decendant_id)
{
	global $to_hash_pairs;
	$to_hash_pairs["{$decendant_id}"] = $decendant_hash;
}

function pair()
{
	global $hash_pairs;
	global $to_hash_pairs;
	global $db_handle;
	foreach ($to_hash_pairs as $k_id => $hash) {
		//we need to find this child a parent
		$parentID = $hash_pairs[$hash];
		$sql = "update quote_items set parent_quote_item_id={$parentID} where id={$k_id}";
		mysqli_query($db_handle,$sql);
	}
}


/*function makeUpdate($assocArray, $table){
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
}*/

echo "success";
db_close();


?>