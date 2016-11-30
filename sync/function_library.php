<?php
ini_set('memory_limit','128M');

/*$db_user = "clieng_ellies";
$db_pass = "S5d453g8";
$db_host = "dedi48.jnb1.host-h.net";
$db_name = "clieng_ellies";
$db_user = "cltdrx_ellsdev";
$db_pass = "JzESNju8";
$db_host = "dedi48.jnb1.host-h.net";
$db_name = "cltdrx_ellsdev";
$db_user = "root";
$db_pass = "";
$db_host = "localhost";
$db_name = "ellies";
$db_handle;*/

include 'dbaccess.php';


function get_column_names_exclude($table)
{
  global $db_handle;
  global $exclude_array;
  global $db_name;

  $query = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '{$db_name}' AND TABLE_NAME = '{$table}'";
  // $query = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'cltdrx_ellsdev'AND TABLE_NAME = '{$table}'";
  // $query = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'clieng_ellies'AND TABLE_NAME = '{$table}'";
  // $query = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'ellies'AND TABLE_NAME = '{$table}'";
  $alternative = "SHOW COLUMNS FROM {$table}";

  /*
    $t_array = array("t","y");
    print_r($t_array);
  */
  // echo "query: {$query}";
  $cursor = mysqli_query($db_handle,$query);
  $arr = array();
  add_Extra($table);
  while ($row = mysqli_fetch_array($cursor)) {
    if(in_array($row[0], $exclude_array))
    {
      continue;
    }
    $arr[]="{$table}.{$row[0]}";
  }
  return implode(',', $arr);
  // print_r($arr);
  // echo "\n";
  // echo "result: \n".implode(',', $arr)."\n";
}

function add_Extra($table)
{
  switch ($table) {
    case 'branches':
      add_branch_exclude();
      break;
    
    default:
      # code...
      break;
  }
}

function add_branch_exclude()
{
  global $exclude_array;
  $exclude_array[]='theme_colour';
  $exclude_array[]='logo';
  $exclude_array[]='contact_number';
  $exclude_array[]='email';
  $exclude_array[]='notes';
}


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

  // Change character set to utf8
  mysqli_set_charset($db_handle,"utf8");
}

function get_table($table)
{
  $tables = array();
  $tables[$table]=dump_table($table);
  return base64_encode(gzdeflate(json_encode($tables)));
}

function get_table_names()
{
  $tableList = array();
  $tableList[]="quotes";
  $tableList[]="rental";
  $tableList[]="quote_items";
  $tableList[]="system_users";
  $tableList[]="new_products";
  $tableList[]="old_products";
  $tableList[]="products_mapping";
  $tableList[]="tech_types";
  $tableList[]="customers";
  $tableList[]="regions";
  $tableList[]="branches";
  $tableList[]="stores";
  $tableList[]="channels";
  $tableList[]="property_types";
  $tableList[]="checklist";
  $tableList[]="quote_checklist";
  $tableList[]="quote_images";
  $tableList[]="electrical_supplier";
  $tableList[]="Intervals";
  $tableList[]="quote_room_settings";
  // $var1 = json_encode($tableList);
  // print_r($var1);
  // exit();
  // $var2 = gzdeflate($var1);
  // $var3 = base64_encode($var2);
  return $tableList;
}

function get_fake_tables(){
  $tableList = array();
  $tableList[]="quotes";
  $tableList[]="rental";
  $tableList[]="quote_items";
  $tableList[]="system_users";
  $tableList[]="`new_products`";
  // $tableList[]="new_products_0";
  // $tableList[]="new_products_1";
  $tableList[]="`old_products`";
  // $tableList[]="old_products_0";
  // $tableList[]="old_products_1";
  $tableList[]="`products_mapping`";
  // $tableList[]="products_mapping_0";
  // $tableList[]="products_mapping_1";
  $tableList[]="tech_types";
  $tableList[]="customers";
  $tableList[]="regions";
  $tableList[]="branches";
  $tableList[]="stores";
  $tableList[]="channels";
  $tableList[]="property_types";
  $tableList[]="checklist";
  $tableList[]="quote_checklist";
  $tableList[]="quote_images";
  $tableList[]="electrical_supplier";
  $tableList[]="Intervals";
  $tableList[]="quote_room_settings";
  $tables = array();
  foreach ($tableList as $table) 
  {
  //  echo "Tablename is $table";
     $tables[$table] = dump_table($table);
  }
  $var1 = json_encode($tables);
  $var2 = gzdeflate($var1);
  $var3 = base64_encode($var2);
  // print_r($tables);
  return $var3;
  //return json_encode($tables);
}

function get_tables()
{
  global $db_handle;
  $result = mysqli_query($db_handle,"show tables");
  $tables = array();
  while ($row = mysqli_fetch_array($result)) {
     $table = $row{0};
     $tables[$table] = base64_encode(gzdeflate(json_encode(dump_table($table))));
  }
  return $tables;
}

function dump_table($tablename){
  //echo "".$tablename."<br/>";
  $select_columns = get_column_names_exclude($tablename);//apply filter
  global $db_handle;
  if (!$tablename) return array();
  // $Query = "SELECT * FROM {$tablename} ";//old code
  $Query = "SELECT {$select_columns} FROM {$tablename} ";
  if(isset($_GET["b"])&&isset($_GET["c"]))
    $Query .= filter($tablename);
  $result=mysqli_query($db_handle,$Query);
  $rows = array();
  while($r = mysqli_fetch_assoc($result)) {
    $rows[] = $r;
  }
  if (!sizeof($rows)) {
    switch ($tablename) {
      case 'quote_checklist':
        $rows[]=gen_empty_json_quote_checklist();
        break;

      case 'quote_images':
        $rows[]=gen_empty_json_quote_images();
        break;

        case 'checklist':
        $rows[]=gen_empty_json_checklist();
        break;

        case 'customers':
        $rows[]=gen_empty_json_customer();
        break;

        case 'quote_items':
        $rows[]=gen_empty_json_quote_items();
        break;

        case 'quotes':
        $rows[]=gen_empty_json_quotes();
        break;
      
      default:
        return $rows;
        break;
    }
    return $rows;
  }
  // print_r($rows);
  // echo "\n\n";
  return $rows;
}

function gen_empty_json_quote_checklist()
{
  return array('quote_id' => '0', 'question_id' => '0','room' => 'none','Value' => 'blank');
}

function gen_empty_json_quote_images()
{
  return array('quote_id' => '0', 'hash' => 'abc123','FileName' => '-1','Room' => '-1', 'Status' => '0');
}

function gen_empty_json_checklist()
{
  return array('id' => '0', 'Title' => 'abc123','Description' => 'abc123','Type' => 'Numeric', 'active' => '0','deleted' => '1',
    'Sort_order' => '0','Mandatory' => '0','date_created' => 'abc123','last_updated' => 'abc123');
}

function gen_empty_json_customer()
{
  return array('id' => '0', 'name' => 'abc123','surname' => 'abc123','cellphone' => '0000', 'company' => 'abc','c_telephone' => '000',
    'date_created' => '0','last_accessed' => '0','active' => '0','channel' => '0','created_by' => '0','updated_by' => '0');
}

function gen_empty_json_quote_items()
{
  return array('ismagnetic' => '-1', 'newqty' => '-1','type' => '-1','newprice' => '-1', 'param1' => '-1','param2' => '-1',
    'param3' => '-1','id' => '0','quote_id' => '0','qty' => '0','room' => 'abc','old_product_id' => '0',
    'new_product_id' => '0','HashTag' => '-1','parent_quote_item_id' => '-1');
}

function gen_empty_json_quotes()
{
  return array('override_target_return' => '-1', 'override_deposit_additional' => '-1','override_price_replacement' => '-1','override_price_additional' => '-1',
    'override_price_additional_capital' => '-1','override_price_replacement_capital' => '-1','override_travel_additional' => '-1,',
    'override_labour_additional' => '-1','override_crush_additional' => '-1','override_materials_additional' => '-1',
    'materials_costs_additional' => '0','labour_costs_additional' => '0','crush_costs_additional' => '0','travel_costs_additional' => '0',
    'do_install' => '0','override_materials_replacement' => '0','override_crush_replacement' => '-1','override_labour_replacement' => '0',
    'override_travel_replacement' => '0','override_rental' => '0','override_deposit_replacement' => '0','override_price' => '0','materials_costs_replacement' => '0',
    'teams' => '0','grade' => '0','rental_escalation_rate' => '0','denominator' => '0','numerator' => '0','rental_amount' => '0','deposit_amount' => '0',
    'rental_term' => '0','lng' => '0','lat' => '0','channel' => '0','discount' => '0','id' => '-1','customer_id' => '-1','date_created' => '0','last_updated' => '0',
    'approved' => '0','complete' => '-1','kwh_price' => '-1','escalation_rate' => '0','property' => 'abc','ref' => 'abc123',
    'created_by' => '-1','updated_by' => '-1','dirty' => '0','distance' => '0','travel_costs_replacement' => '0','labour_costs_replacement' => '0',
    'crush_costs_replacement' => '0','Review' => '-1','expiration_date' => '-1','property_id' => '-1','electrical_supplier_id' => '-1');
}

//execute the SQL query and return records
function db_show_query_result($query)
{
  global $db_handle;
  $result = mysqli_query($db_handle,$query);

//fetch tha data from the database 
  while ($row = mysqli_fetch_array($result)) 
  {
    echo "ID:".$row{0}."<br /> Name:".$row{1}."<br/> IP: ". //display the results
    $row{2}."<br>";
  }
}

function db_close()
{
  global $db_handle;
  //close the connection
  mysqli_close($db_handle);
}

function checkEncoding()
{
  echo "error no: ".json_last_error() . '<br/>';
  switch (json_last_error()) {
        case JSON_ERROR_NONE:
            echo ' - No errors';
        break;
        case JSON_ERROR_DEPTH:
            echo ' - Maximum stack depth exceeded';
        break;
        case JSON_ERROR_STATE_MISMATCH:
            echo ' - Underflow or the modes mismatch';
        break;
        case JSON_ERROR_CTRL_CHAR:
            echo ' - Unexpected control character found';
        break;
        case JSON_ERROR_SYNTAX:
            echo ' - Syntax error, malformed JSON';
        break;
        case JSON_ERROR_UTF8:
            echo ' - Malformed UTF-8 characters, possibly incorrectly encoded';
        break;
        default:
            echo ' - Unknown error';
        break;
    }
}

/*
*In this function we look at the table we are in, and apply a filter
*This filter can only be applied if and only if the URL paramters exist
*_GET["c"] for channel number and then _GET["b"] for branch
*/
function filter($tablename)
{
  $BranchID = (int)$_GET["b"];
  $ChannelNumber = (int)$_GET["c"];
  if($tablename=="quotes")
  {
    $sql = "select id from system_users WHERE branch_id='{$BranchID}'";
    return "WHERE created_by IN ({$sql})";
  }
  else if($tablename=="new_products")
  {
    return "WHERE channel=$ChannelNumber";
  }
  else if($tablename=="old_products")
  {
    return "WHERE channel=$ChannelNumber";
  }
  else if($tablename=="products_mapping")
  {
    return "WHERE channel=$ChannelNumber";
  }
  else if($tablename=="customers")
  {
    $sql = "select id from system_users WHERE branch_id='{$BranchID}'";
    return "WHERE created_by IN ({$sql})";
  }
  else if($tablename=="quote_items") 
  {
    $sql = "Select id from quotes WHERE created_by IN (Select id from system_users WHERE branch_id='{$BranchID}')";
    return "WHERE quote_id IN ({$sql})";
  }
  else if($tablename=="quote_checklist")
  {
    $sql = "Select id from quotes WHERE created_by IN (Select id from system_users WHERE branch_id='{$BranchID}')";
    return "WHERE quote_id IN ({$sql})";
  }
  else if($tablename=="quote_images")
  {
    $sql = "Select id from quotes WHERE created_by IN (Select id from system_users WHERE branch_id='{$BranchID}')";
    return "WHERE quote_id IN ({$sql})";
  }
}