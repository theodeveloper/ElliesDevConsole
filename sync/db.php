<?php
ini_set('memory_limit','512M');

error_reporting(0);
// error_reporting(E_ALL);
/*
  Do a check to see what version of the app is running
  pull into the script that corelates to that version
*/
// print_r($_GET);exit;
if(isset($_GET['v']))
{
  $version_number = $_GET['v'];
  // echo "\nversion:{$version_number}\n";
  $target_version = substr($version_number,0,strrpos($version_number,'.'));
  
  if($target_version=='1.1')
  {
    include 'v1.1/db.php';
    die();
  }
  else{
    //nothing
  }
}
// echo "string";

//if we end up here, we are legacy
$exclude_array = array('parent_quote_item_id','propertyType','branch_owner');
include 'function_library.php';

$table=$_GET["table"];
db_connect();
if ($table=="all")
{
  $res = get_fake_tables();
} else if($table=="list")
{
  $res=get_table_names();
} else 
{
  $res = get_table($table);
}

echo json_encode($res);
db_close();





