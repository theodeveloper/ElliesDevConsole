<?php 
# This code is written by Tradepage Pty Ltd for Ellies Pty Ltd (the 'parties' mentioned below)
# The code is provided based on the the terms specified within the agreed NDA between both parties.
# Both parties have agreed the code is strictly confidential
# and only by mutal agreement of both parties may the code be exposed to outside parties.
#
# Any changes made to the code other than by Tradepage Pty Ltd during the NDA Agreement void support of the code
#
/* --------------------------------------------------------------------------
* This source code contains confidential information that is proprietary to
* CloudGroup (Pty) Ltd. No part of its contents may be used,
* copied, disclosed or conveyed to any party in any manner whatsoever
* without prior written permission from CloudGroup(Pty) Ltd.
* No part of this source code may be used, reproduced, stored in a retrieval system,
* or transmitted, in any form or by any means, electronic, mechanical,
* photocopying, recording or otherwise, without the prior written permission
* of the copyright owners.
* --------------------------------------------------------------------------
* Copyright CloudGroup (Pty) Ltd
*/
session_start();
$csvfields = "Tech_Type,Option,New_Product_Code,Old_Technology_Type,New_Technology_Type,Related_To,KW_YN,Old_KWh,New_KWh,Old_Litres,New_Litres,Price,HasStock";
require_once("inc/config.php");
require_once("inc/functions.php");
require_once("../inc/system_user.php");
require_once("../inc/functions.php");
//require_once("inc/quote.class.php");
//require_once("inc/quoteitem.class.php");
//require_once("inc/techitem.class.php");
//die($_SESSION["userid"]);
//LoginCheck("quotes.php");
get_session();
    // If not authenticated...
	if (!isset($_SESSION["userid"])){
	header("location:./login.php");
}
$sysuser = new userType($_SESSION["userid"]);
if (!$sysuser->hasPermission("import_tech_items")) {
    //header("location: index.php?nopermission=Edit%20Products");
	echo "No Permission";
    exit();
}
if (!is_authenticated()) {
        if (isset($_POST['aj']) && $_POST['aj'] == 1) {
            print "\n<!-- scripts , code below be eval()ed by javascript -->\nalert('You have been signed out! Please relogin');";
        } else {
            die("Not logged in");
            exit(1);
        }
    }
	error_reporting(E_ERROR);
ini_set('display_errors', 'On');

?>
    <style>
        body {
            font-family: arial;
            font-size: 12px;
        }
        td, th {
            font-family: arial;
            font-size: 12px;
        }
    </style>
<?php 
function CleanDodgyCharacters($instring) {
    //LL To Â½" Pr
    $instring = str_replace("Â½\"", "<sup>1/2</sup>", $instring);
    return $instring;
}

function SetAssocBlanks($assoc_array = NULL, $column_defaults = NULL) {
    //Old KWh	Old Litres
    if (!empty($assoc_array) && !empty($column_defaults)) {
        if (is_array($assoc_array) && is_array($column_defaults)) {
            foreach($assoc_array as $array_item) {
                foreach($column_defaults as $colkey=>$coldefault) {
                    if (empty($array_item[$colkey]) || $array_item[$colkey] == "#N/A") {
                        $array_item[$colkey] = $coldefault;
                    }
                }
                $new_array[] = $array_item;
            }
        }
    }
    return $new_array;
}

function InsertDBFromAssoc($assoc_array, $tablename = "temp_table",$retailChannel) {
   // print_r($assoc_array);
    if($tablename == 'new_products')
    {
        print "NEW PRODUCT INSERT";
        //print_r($assoc_array);
        print"<br/>";
    }
    if (!empty($assoc_array)) {
        foreach($assoc_array as $array_item) {
            $icol = 0;
            $fieldstr = "";
            $valuestr = "";
            foreach($array_item as $key=>$value) {
                $icol += 1;
                $tkey = trim($key);
                $tkey = str_replace(" ", "_", $tkey);
				$tkey = str_replace("_-_","_", $tkey);
                $tkey = strtolower($tkey);
				if (substr($tkey,0,5)=="price"){
					$value = trim(substr($value,1));
					$value = str_replace(" ","",$value);
				};
				if (substr($tkey,0,4)=="cost"){
					$value = trim(substr($value,1));
					$value = str_replace(" ","",$value);
				};
				if ($tkey=="materials_new"){
					$value = trim(substr($value,1));
					$value = str_replace(" ","",$value);
				};
				if ($tkey=="maintenance_cost"){
					//echo 
					$value = trim(substr($value,1));
					$value = str_replace(" ","",$value);
				};






                if ($icol > 1) {
                    $fieldstr .= ",";
                    $valuestr .= ",";
                }
                if($tkey != "")
                    $fieldstr .= "`".mysqli_real_escape_string($GLOBALS["link"],$tkey)."`";

                $valuestr .= "'".mysqli_real_escape_string($GLOBALS["link"],$value)."'";
            }
            $sqlinsert = "INSERT INTO `".$tablename."` (".$fieldstr.",deleted,channel) VALUES (".$valuestr.",0,$retailChannel)";
          //   print $sqlinsert."<br>";
           //  print $sqlinsert."</br>";
            mysqli_query($GLOBALS["link"],$sqlinsert);
        }
    }
}

function UpdateDBFromAssoc($assoc_array, $tablename = "temp_table", $wherefield = "code", $retailChannel)
{
    if($tablename == 'new_products')
    {
        //print "NEW PRODUCT UPDATE";
        //print_r($assoc_array);
    }
    if (!empty($assoc_array))
    {
        foreach($assoc_array as $array_item)
        {
            $sql = "UPDATE `".$tablename."` SET ";
            $icol = 0;
            foreach($array_item as $key=>$value)
            {
                $tkey = trim($key,"_");
		//		echo "*$key*$tkey*";
                $tkey = str_replace(" ", "_", $tkey);
				$tkey = str_replace("_-_","_", $tkey);
                $tkey = strtolower($tkey);
				if (substr($tkey,0,5)=="price"){
					$value = trim(substr($value,1));
					$value = str_replace(" ","",$value);
				};
if (substr($tkey,0,4)=="cost"){
					$value = trim(substr($value,1));
					$value = str_replace(" ","",$value);
				};
				if ($tkey=="materials_new"){
					$value = trim(substr($value,1));
					$value = str_replace(" ","",$value);
				};
				if ($tkey=="maintenance_cost"){
					//echo 
					$value = trim(substr($value,1));
					$value = str_replace(" ","",$value);
				};
                if ($tkey != $wherefield)
                {
                    $icol += 1;
                    if ($icol > 1)
                    {
                        $sql .= ",";
                    }
                    $sql .= "`".mysqli_real_escape_string($GLOBALS["link"],$tkey)."` = '".mysqli_real_escape_string($GLOBALS["link"],$value)."'";
                }
            }
            $sql.= ", deleted=0 WHERE channel=$retailChannel AND `".$wherefield."` = '".mysqli_real_escape_string($GLOBALS["link"],$array_item[$wherefield])."'";
          // print $sql."<br>";
            mysqli_query($GLOBALS["link"],$sql);
        }
    }
}

function ImportCSVProducts($suffix, $retailChannel) {
	//die($retailchannel);
    $oldproducts = csv_to_array("temp/Existing_Product_Tables.csv", ",", true);
    $newproducts = csv_to_array("temp/New_Product_Tables.csv", ",", true);
	//print "Update new_products_$suffix set deleted=1 where channel = $retailChannel";
	mysqli_query($GLOBALS["link"],"Update new_products_$suffix set deleted=1 where channel = $retailChannel");
	mysqli_query($GLOBALS["link"],"Update old_products_$suffix set deleted=1 where channel = $retailChannel");
	
    $oldexists = false;
    $newexists = false;

    //print_r($oldproducts);
    //===Old Products DB Update===
    $sql = "SELECT code, product FROM `old_products_$suffix` where channel = $retailChannel";
    $sqlres = mysqli_query($GLOBALS["link"],$sql);
    while($row = mysqli_fetch_assoc($sqlres)) {
        $dbprodsold[$row["code"]] = $row;
        $oldexists = true;
    }
	//print "$sql";
	//print "exists? $oldexists <br/>";
    if (!$oldexists) {
        InsertDBFromAssoc($oldproducts, "old_products_$suffix", $retailChannel);
    }else{

        foreach($oldproducts as $old) {
            if (empty($old["existing_kwh"])) { $old["existing_kwh"] = 0; }
            if (empty($old["existing_litres"])) { $old["existing_litres"] = 0; }
            $result = array_diff_assoc($old, $dbprodsold[$old["code"]]);

            if (sizeof($result) > 0) {
                //===Do Update Here===
                $result["code"] = $old["code"];
                $changed[] = $result;
            }else{
                if (!is_array($result)) {
                    //===Do Insert Here===
                    $tproducts[] = $old;
                }
            }
        }
    }
	if (isset($tproducts)){
    InsertDBFromAssoc($tproducts, "old_products_$suffix", $retailChannel);
	} else {
    UpdateDBFromAssoc($changed, "old_products_$suffix", "code", $retailChannel);
}
    //===New Products DB Update===
    //print_r($newproducts);
    $sql = "SELECT `code`,`product` FROM `new_products_$suffix` where channel = $retailChannel";
   // die ($sql);
    $sqlres = mysqli_query($GLOBALS["link"],$sql);
    while($row = mysqli_fetch_assoc($sqlres)) {
        $dbprodsnew[$row["code"]] = $row;
        $newexists = true;
    }

    $tproducts = NULL;
    $changed = NULL;

    $irow = -1;
    foreach($newproducts as $new) {
        $irow += 1;
        foreach ($new as $key=>$value) {
            if (substr($key, 0, 8) != "existing") {
                $tmpnewproducts[$irow][$key] = $value;
            }
        }
    }
    //print_r($tmpnewproducts);

    //var_dump($tmpnewproducts);
    if (!$newexists) {
        InsertDBFromAssoc($tmpnewproducts, "new_products_$suffix", $retailChannel);
    }else{

        //print_r($tmpnewproducts);
        $count =1;
        foreach($tmpnewproducts as $new) {
            if (empty($new["replacement_kwh"])) { $new["replacement_kwh"] = 0; }
            if (empty($new["replacement_litres"])) { $new["replacement_litres"] = 0; }
           // if ($new["price"] == "#N/A") { $new["price"] = 0; }

            $new["a"] = str_replace(",", "", $new["a"]);
			$new["b"] = str_replace(",", "", $new["b"]);
			$new["c"] = str_replace(",", "", $new["c"]);
		//	print($new["code"]." : ".isset($dbprodsnew[$new["code"]]));
			if (isset($dbprodsnew[$new["code"]])){
            $result = array_diff_assoc($new, $dbprodsnew[$new["code"]]);

            if (sizeof($result) > 0) {
                //===Do Update Here===
                $result["code"] = $new["code"];
                $changed[] = $result;

            }}else{
				//print("I AM HERE");
                //if (!is_array($result)) {
                    //===Do Insert Here===
                    // if($new["code"] == "FSFSH12")
                    //	print_r($new);
					//print("I AM HERE");
                    $tproducts[] = $new;
                //}
            }
        }
    }
	//var_dump($tproducts);
    //print_r($changed);
    InsertDBFromAssoc($tproducts, "new_products_$suffix", $retailChannel);
    UpdateDBFromAssoc($changed, "new_products_$suffix", "code", $retailChannel);

    $sql = "SELECT `id`, `code` FROM `new_products_$suffix`  where channel = $retailChannel UNION ALL SELECT `id`, `product` AS `code` FROM `old_products_$suffix`  where channel = $retailChannel";
    //print "$sql";  
  $sqlres = mysqli_query($GLOBALS["link"],$sql);
    while($row = mysqli_fetch_assoc($sqlres)) {
        $codelook[trim($row["code"])]["id"] = $row["id"];
      //  $codelook[trim($row["code"])]["type_for_mobi_site"] = $row["type_for_mobi_site"];
    }

    $sql = "DELETE FROM `products_mapping_$suffix`  where channel = $retailChannel";
    mysqli_query($GLOBALS["link"],$sql);

    foreach($newproducts as $new) {
        foreach ($new as $key=>$value) {
            if (substr($key, 0, 8) == "existing" && !empty($value)) {
                $newcode = trim($new["code"]);
                $oldcode = trim($value);
				//echo "Old code: ".mysqli_real_escape_string($GLOBALS["link"],$codelook[$newcode]["id"])."New code: ".;
                $sql = "INSERT INTO `products_mapping_$suffix` (`oldid`, `newid`, `tech_type`,`channel`) VALUES ('".mysqli_real_escape_string($GLOBALS["link"],$codelook[$oldcode]["id"])."', '".mysqli_real_escape_string($GLOBALS["link"],$codelook[$newcode]["id"])."', '"."','".$retailChannel."')";//mysqli_real_escape_string($GLOBALS["link"],$codelook[$oldcode]["type_for_mobi_site"])."')";
                //print "$sql <br/>";
                mysqli_query($GLOBALS["link"],$sql);
            }
        }
    }

    print "Products have been imported";
}

//print_r($_POST);
//exit();
if (!empty($_FILES))
{
    $allowedExtension = array("csv");
    foreach ($_FILES as $file)
    {
        if ($file['tmp_name'] > '')
        {
            if (!in_array(end(explode(".", strtolower($file['name']))), $allowedExtension))
            {
                die($file['name'].' is an invalid file type!<br/>'.'<a href="javascript:history.go(-1);">'.'<< Go Back</a>');
            }
        }
    }

    $bup1 = false;
    $bup2 = false;

    if (move_uploaded_file($_FILES["fileold"]["tmp_name"], "temp/Existing_Product_Tables.csv"))
    {
        $bup1 = true;
    }

    if (move_uploaded_file($_FILES["filenew"]["tmp_name"], "temp/New_Product_Tables.csv"))
    {
        $bup2 = true;
    }
   // var_dump($_FILES);
    if ($bup1 && $bup2)
    {
        //die($suffix);
        $suffix = $_POST["channel"];
		$retailchannel = $_POST["retailchannel"];
        ImportCSVProducts($suffix, $retailchannel);
    }
    else
    {
        die('Please upload both csv files!<br/>'.'<a href="javascript:history.go(-1);">'.'<< Go Back</a>');
    }
    exit();
}

print '<form action="importcsv.php" method="POST" enctype="multipart/form-data">';
print "<label for='fileold'>Existing Products CSV file</label><br>";
print "<input type='file' id='fileold' name='fileold'><br><br>";

print "<label for='filenew'>Replacement Products CSV file</label><br>";
print "<input type='file' id='filenew' name='filenew'><br><br>";
if ($sysuser->branchID == 37){
  print "<input type='hidden' name='channel' id='channel' value='1'>";
  print "<input type='hidden' name='retailchannel' id='channel' value='0'>";

} else {
  print "<input type='hidden' name='channel' id='channel' value='0'>";
  if (!$sysuser->isSuperAdmin){
    print "<input type='hidden' name='retailchannel' id='channel' value='".$sysuser->retailChannel."'>";
} else {
	print "<select name='retailchannel' id='retailchannel'>";
	$query = "select id, name from channels";
	$result = mysqli_query($GLOBALS["link"],$query);
	while ($row=mysqli_fetch_assoc($result)){
		print "<option value='".$row["id"]."'>".$row["name"]."</option>";
	}
	print "</select>";
	

}
}

print '<input data-theme="b" value="Submit" type="submit">';
print '</form>';
//ImportCSVProducts();
?>
