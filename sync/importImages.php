<?php
require_once("../inc/simpleImage.php");
$output_dir = "./files/images/";

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

$output2 = $_POST["json"];
$iArray = json_decode($output2,true);

$Image=$iArray["Images"];
$Imagedetails = json_decode($Image,true);
//print_r($Imagedetails);
$ImageHash = $Imagedetails["hash"];
$ImageFName = $Imagedetails["FileName"];
$ImageRoom = $Imagedetails["RoomName"];
$ImageEncoded = $Imagedetails["RawData"];

/*$textfile = fopen("rawdata.txt",'wb');
fwrite($textfile, $ImageEncoded);
fclose($textfile);*/
$qID = fetchID($ImageHash);
$imageStatus = (int)fetchStatus($ImageHash);
if($qID>0)
{
  $ImageFName = substr_replace($ImageFName, "_".$qID, strlen($ImageFName)-4) . ".jpg";
}
else
{
  $ImageFName = substr_replace($ImageFName, "_"."0000", strlen($ImageFName)-4) . ".jpg";
}
//write blob to disk
//$data = explode(',', $ImageEncoded);//base64 encode and decode adds a whole lot of meta data
//print_r($data);
//$ImageBlob = base64_decode($data[0]);//get the blob only
$comingIn = strlen($ImageEncoded);
$ImageBlob = base64_decode($ImageEncoded);
//print_r($ImageBlob);
$writtenFile = "./files/images/".$ImageFName;
$ifp = fopen($writtenFile, "wb");
$sizeof = strlen($ImageBlob);
for($written=0; $written<$sizeof; $written += $fwrite)
{
	$fwrite = fwrite($ifp, substr($ImageBlob, $written));
	if($fwrite==false)
		break;
		//done return $written;
}
fclose($ifp);
//done return $written;

//store the rest
$queryString = 'UPDATE quote_images SET FileName=\''.$ImageFName.'\', Room=\''.$ImageRoom.'\', Status=0 
	WHERE hash=\''.$ImageHash.'\'';
//mysql_query("UPDATE quote_images SET FileName='$ImageFName', Room='$ImageRoom', Status='0' WHERE hash='$ImageHash'");
mysql_query($queryString);
//echo $queryString;
$affectedRows = mysql_affected_rows();
if($affectedRows>0){
  //Creates a Thumbnail
  $img = new SimpleImage($output_dir.$ImageFName);
  $img->best_fit(105, 56)->save($output_dir  .'Thumbnails/' .$ImageFName);
	echo "success";
}else{
  if($imageStatus==0)
    echo "success";//could resolve a rare error
  else
	   echo "Falied";
}

db_close();

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

function fetchID($hash)
{
  $sql = "SELECT quote_id FROM quote_images WHERE hash='{$hash}'";
  $result = mysql_query($sql);
  if($result==false)
    return -1;
  $row = mysql_fetch_assoc($result);
  return $row["quote_id"];
}

function fetchStatus($hash)
{
  $sql = "SELECT Status FROM quote_images WHERE hash='{$hash}'";
  $result = mysql_query($sql);
  if($result==false)
    return -1;
  $row = mysql_fetch_assoc($result);
  return (int)$row["quote_id"];
}

?>