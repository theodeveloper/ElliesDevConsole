<?php
$db_user = "cltdrx_ellsdev";
$db_pass = "JzESNju8";
$db_host = "dedi48.jnb1.host-h.net";
$db_name = "cltdrx_ellsdev";
$db_handle;

ini_set('memory_limit','512M');

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

function get_tables(){
$result = mysql_query("show tables");
$tables = array();
while ($row = mysql_fetch_array($result)) {
   $table = $row{0};
   $tables[$table] = dump_table($table);
}
return $tables;
}

function dump_table($tablename){
  $result=mysql_query("SELECT * from $tablename");
$rows = array();
while($r = mysql_fetch_assoc($result)) {
    $rows[] = $r;
}
return $rows;
}
//execute the SQL query and return records
function db_show_query_result($query){
$result = mysql_query($query);

//fetch tha data from the database 
while ($row = mysql_fetch_array($result)) {
   echo "ID:".$row{0}."<br /> Name:".$row{1}."<br/> IP: ". //display the results
   $row{2}."<br>";
}
}

function db_close(){
global $db_handle;
//close the connection
mysql_close($db_handle);
}
//$table=$_GET["table"];
db_connect();

$res = get_tables();
echo json_encode($res);
db_close();

?>