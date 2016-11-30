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
	//session_start();
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

	//Updates values in the database
	function UpdateDBFromAssocDoc($assoc_array, $tablename = "temp_table", $wherefield = "code", $typechannel,$channeltype)
	{
		$channeltype = "'".$channeltype."'";
	    if($tablename == 'new_products')
	    {
	        print "Existing products have been updated<br/>";
	        //print_r($assoc_array);
	    }
	    //print "Test";
	    if (!empty($assoc_array))
	    {
	        foreach($assoc_array as $array_item)
	        {
	            $sql = "UPDATE `".$tablename."` SET ";
	            $icol = 0;
	            foreach($array_item as $key=>$value)
	            {
	                $tkey = trim($key,"_");
					//echo "*$key*$tkey*";
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
	            $sql.= ", deleted=0,last_updated=NOW() WHERE channel=$typechannel AND channel_type=$channeltype AND `".$wherefield."` = '".mysqli_real_escape_string($GLOBALS["link"],$array_item[$wherefield])."'";
	            //print $sql."<br>";
	            mysqli_query($GLOBALS["link"],$sql);
	        }
	    }
	}
	//Inserts values in the database
	function InsertDBFromAssocDoc($assoc_array, $tablename = "temp_table",$typechannel,$channeltype) {
	   // print_r($assoc_array);
		$count =0;
	    if (!empty($assoc_array)) {
	    	   $channeltype = "'".$channeltype."'";
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

	                if ($icol > 1 && $tkey != "") {
	                    $fieldstr .= ",";
	                    $valuestr .= ",";
	                }
	                if($tkey != ""){
	                    $fieldstr .= "`".mysqli_real_escape_string($GLOBALS["link"],$tkey)."`";
	                    $valuestr .= "'".mysqli_real_escape_string($GLOBALS["link"],$value)."'";
	                }
	            }
	            //comment channneltype
	            //print $sqlinsert."<br>";
	            $sqlinsert = "INSERT INTO `".$tablename."` (".$fieldstr.",deleted,channel,channel_type,date_created,last_updated) VALUES (".$valuestr.",0,$typechannel,$channeltype,NOW(),NOW())";
	              if($tablename == 'new_products'){
	              	 $count += 1;
	              }
	           
	            ///print $sqlinsert."<br>";
	            mysqli_query($GLOBALS["link"],$sqlinsert);
	        }
	    }
	    return $count;
	}
	//Converts file to array
	function csv_to_arrayDoc($filename = '', $delimiter = ',', $normalizeheaderrow = false)
	{
	    if (!file_exists($filename) || !is_readable($filename))
	        return FALSE;

	    $header = NULL;
	    $data = array();
	    if (($handle = fopen($filename, 'r')) !== FALSE) {
	        while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
	            //print_r($row);
	            //print "<br/><br/>";
	            if (!$header) {
	                $header = $row;
	                if ($normalizeheaderrow) {
	                    foreach ($header as $col) {
	                        $col = strtolower(str_replace(" ", "_", $col));
	                        //print "header: ".$col."<br/>";
	                        $theader[] = $col;
	                    }
	                    $header = $theader;
	                }
	            } else {
	                foreach ($row as $key => $value) {
	                    //if ($filename == "temp/New_Product_Tables.csv")
	                    //print "key ".$key. " value: ".$value."<br/>";
	                    if (strtolower($value) == "yes") {
	                        $value = "1";
	                    }
	                    if (strtolower($value) == "no") {
	                        $value = "0";
	                    }
	                    $trow[$key] = $value;
	                }
	                $data[] = array_combine($theader, $trow);
	            }
	        }
	        fclose($handle);
	    }
	    return $data;
	}

	//Find duplicates products
	function Duplicates($fileNameOld=array(),$fileNameNew=array()){
		$num = count($fileNameOld);
		$duplicateOld = array();
		$row = "There are duplicates products in the following files:<br/><br/>";
		$row .="<h2>Existing Products</h2><br/>";
		$duplicates = array();
		for($r = 0;$r< $num; $r ++){
        	$value = $fileNameOld[$r];
        	if(!in_array($value["product"], $duplicateOld)){
        		$duplicateOld[] = $value["product"];
        	}else{
        		$row .= $value["product"] ." (" .$value["product_type"] .")<br/>";
        		$duplicates[] = $value["product"] ." (" .$value["code"] .")";
        	}
        }
        if(count($duplicates) ==0){
        	$row .= "No issues found<br/>";
        }

        $num = count($fileNameNew);
		$duplicateNewCode = array();
		$duplicateNewProduct = array();
		$found = false;
		$countRep = 0;

		//Checks in terms of code and product
		$row .="<h2>Replacement Products</h2><br/>";
		for($r = 0;$r< $num; $r ++){
        	$value = $fileNameNew[$r];
        	$found = false;
        	if(!in_array($value["code"], $duplicateNewCode)){
        		if(!in_array($value["product"], $duplicateNewProduct)){
        			$duplicateNewCode[] = $value["code"];
        			$duplicateNewProduct[] = $value["product"];
        		}else{
        			$found = true;
        		}
        	}else{
        		$found = true;
        	}
        	if(!empty($value)){
        	   if($found){
        		$row .= $value["product"] ." (" .$value["code"] .")<br/>";
        		$duplicates[] = $value["product"] ." (" .$value["code"] .")";
        		$countRep +=1;
        	   }
        	}     	
        }
         if($countRep ==0){
        	$row .= "No issues found<br/>";
        }
        if(empty($duplicates)){
        	$row = "";
        }
        return $row;
	}

	//Check if Replacement Products have existing Products
	function Replacements($fileNameNew=array()){
		$num = count($fileNameNew);
		$replacements = array();
		$row = "<h2>There following products don't have replacement products:</h2><br/>";
		$irow = -1;
	    foreach($fileNameNew as $new) {
	    	$irow += 1;
        	foreach ($new as $key=>$value) { 
	           //if (substr_count($key, 'existing_1') > 0) {
	           	if ($key == 'existing_1') {
	               if($value ==""){
	               		$replacements[$irow][$key] = $value;
	                	$row .= $new["product"] ." (" .$new["code"] .")<br/>";
	                	 break;
	                }
	            }
	        }
        }
        if(empty($replacements)){
        	$row = "";
        }
        return $row;
	}

	function ImportCSVProducts($typechannel,$fileNameOld,$fileNameNew,$channeltype) {
	    $oldproducts = csv_to_arrayDoc($fileNameOld, ",", true);
	    $newproducts = csv_to_arrayDoc($fileNameNew, ",", true);

	    //Find duplicates
	    $duplicates = Duplicates($oldproducts,$newproducts);

	    //Find Replacements
	    //$replacements = Replacements($newproducts);

	    if($duplicates !==""){
	    	echo $duplicates;	
	    }
	    //if($duplicates ==""){
	    //	echo $replacements;
	    //}

	    if($duplicates !==""){	
	    	echo "<br/><p style='color:red'>Unable to import products</p>";
	    	echo "<br/>";
	    	echo "<br/>";
	    	echo "<br/>";
	    }//elseif($replacements ==""  && $duplicates ==""){
	    	//echo "<br/><p style='color:green'>Import products</p>";
	   // }

	    if($duplicates ==""){
	    	//echo "No duplicates and all products have replacements";	
			//print "Update new_products_$suffix set deleted=1 where channel = $retailChannel";
			mysqli_query($GLOBALS["link"],"Update new_products set deleted=1 where channel = $typechannel");
			mysqli_query($GLOBALS["link"],"Update old_products set deleted=1 where channel = $typechannel");

			$count=0;
			$new_add = false;
			
		    $oldexists = false;
		    $newexists = false;

		    //print_r($oldproducts);
		    //===Old Products DB Update===
		    $sql = "SELECT code, product FROM `old_products` where channel = $typechannel and deleted=0";
		    $sqlres = mysqli_query($GLOBALS["link"],$sql);
		    while($row = mysqli_fetch_assoc($sqlres)) {
		        $dbprodsold[$row["code"]] = $row;
		        $oldexists = true;
		    }
			//print "$channeltype";
			//print "exists?:" . $oldexists ."<br/>";
		    if (!$oldexists) {
		        InsertDBFromAssocDoc($oldproducts, "old_products", $typechannel,$channeltype);
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
		    	InsertDBFromAssocDoc($tproducts, "old_products", $typechannel,$channeltype);
			} else {
		    	UpdateDBFromAssocDoc($changed, "old_products", "code", $typechannel,$channeltype);
			    //===New Products DB Update===
			    //print_r($newproducts);
			    $sql = "SELECT `code`,`product` FROM `new_products` where channel = $typechannel and deleted=0";
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
			    if (!$newexists) {
			        $count+=InsertDBFromAssocDoc($tmpnewproducts, "new_products", $typechannel,$channeltype);
			    }else{

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
			            	}
			        	}else{
		                  	$tproducts[] = $new;
			            }
			        }
			    }
				//var_dump($tproducts);
			    //print_r($changed);

			    //New Products
			    $count+=InsertDBFromAssocDoc($tproducts, "new_products", $typechannel,$channeltype);
			    print "New Products have been added<br/>";
			    UpdateDBFromAssocDoc($changed, "new_products", "code", $typechannel,$channeltype);

			    $codelook_old = array();
			    $codelook_new = array();
			    //new products
			    $sql = "SELECT `id`, `code` FROM `new_products`  where channel = $typechannel and deleted=0"; 
			    $sqlres = mysqli_query($GLOBALS["link"],$sql);
			    while($row = mysqli_fetch_assoc($sqlres)) {
			        $codelook_new[trim($row["code"])]["id"] = $row["id"];
			    }
			    //old products
			    $sql = "SELECT `id`, `product` AS `code` FROM `old_products`  where channel = $typechannel and deleted=0"; 
			    $sqlres = mysqli_query($GLOBALS["link"],$sql);
			    while($row = mysqli_fetch_assoc($sqlres)) {
			        $codelook_old[trim($row["code"])]["id"] = $row["id"];
			    }

			    $sql = "DELETE FROM `products_mapping`  where channel = $typechannel";
			    mysqli_query($GLOBALS["link"],$sql);
			    foreach($newproducts as $new) {
			        foreach ($new as $key=>$value) {
			            if (substr($key, 0, 8) == "existing" && !empty($value)) {
			                $newcode = trim($new["code"]);
			                $oldcode = trim($value);
			    			
			                $sql = "INSERT INTO `products_mapping` (`oldid`, `newid`, `tech_type`,`channel`) VALUES ('".mysqli_real_escape_string($GLOBALS["link"],$codelook_old[$oldcode]["id"])."', '".mysqli_real_escape_string($GLOBALS["link"],$codelook_new[$newcode]["id"])."', '"."','".$typechannel."')";
			                //print "$sql <br/>";
			                mysqli_query($GLOBALS["link"],$sql);
			            }
			        }
			    }
			    print "<br/>".$count." Products have been successfully imported";
			}
		}
	}

	//==================================================================================================================
	//Upload Files
	if (!empty($_FILES))
	{
		$output_dir = "productfiles/New_Products/";
		$output_dir2 = "productfiles/Old_Products/";

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

	    $old = false;
	    $new = false;

	 	$fileNameOld =  basename($_FILES["fileold"]["name"]);
	 	$target_file = $output_dir2.$fileNameOld;
	 	$FileType = pathinfo($target_file,PATHINFO_EXTENSION);
	 	$fileNameOld  = "Old_Products_" . date('Ymd'). "_" . time('hms'). ".".$FileType;
	    if (move_uploaded_file($_FILES["fileold"]["tmp_name"], $output_dir2.$fileNameOld)){
	        $old = true;
	    }

	    $fileNameNew =  basename($_FILES["filenew"]["name"]);
	 	$target_file = $output_dir.$fileNameNew;
	 	$FileType = pathinfo($target_file,PATHINFO_EXTENSION);
	 	$fileNameNew  = "New_Products_" . date('Ymd'). "_" . time('hms'). ".".$FileType;
	    if (move_uploaded_file($_FILES["filenew"]["tmp_name"], $output_dir.$fileNameNew)){
	        $new = true;
	    }
	    //var_dump($_FILES);
	    if ($old && $new){
		    $sql = "SELECT `type` FROM `channels` WHERE `id`=".$sysuser->retailChannel;
			$sqlres = mysqli_query($GLOBALS["link"],$sql);
			$row = mysqli_fetch_assoc($sqlres);
			$channeltype = $row['type'];
		    //print $channeltype."</br>";
	        //die($suffix);
	        //$suffix = $_POST["channel"];
			$typechannel = $_POST["typechannel"];
			$fileNameNew = "productfiles/New_Products/".$fileNameNew;
			$fileNameOld = "productfiles/Old_Products/".$fileNameOld;
	        ImportCSVProducts($typechannel,$fileNameOld,$fileNameNew,$channeltype);
	    }
	    else{
	        die('Please upload both csv files!<br/>'.'<a href="javascript:history.go(-1);">'.'<< Go Back</a>');
	    }
	    exit();
	}
	//==================================================================================================================
	//Form
	print '<form action="importcsv2.php" method="POST" enctype="multipart/form-data">';
	print "<label for='fileold'>Existing Products CSV file</label><br>";
	print "<input type='file' id='fileold' name='fileold'><br><br>";

	print "<label for='filenew'>Replacement Products CSV file</label><br>";
	print "<input type='file' id='filenew' name='filenew'><br><br>";

	/*$sql = "SELECT `type` FROM `channels` WHERE `id`=".$sysuser->retailChannel;
	$sqlres = mysqli_query($GLOBALS["link"],$sql);
	$row = mysqli_fetch_assoc($sqlres);
	$channeltype = $row['type'];

	if ($channeltype == "Commercial" || $channeltype =="Franchise"){
	  print "<input type='hidden' name='channel' id='channel' value='1'>";
	  print "<input type='hidden' name='typechannel' id='channel' value='0'>";
	} else {*/

	  print "<input type='hidden' name='channel' id='channel' value='0'>";
	  	if (!$sysuser->isSuperAdmin){
	    	print "<input type='hidden' name='typechannel' id='channel' value='".$sysuser->retailChannel."'>";
		} else {
			$sql = "SELECT `type` FROM `channels` WHERE `id`=".$sysuser->retailChannel;
            $sqlres = mysqli_query($GLOBALS["link"],$sql);
            $row = mysqli_fetch_assoc($sqlres);
            $channeltype = $row['type'];
     
            $query = "select id, name from channels where channels.type='".$channeltype."'";
			print "<select name='typechannel' id='typechannel'>";
			$result = mysqli_query($GLOBALS["link"],$query);
			while ($row=mysqli_fetch_assoc($result)){
				print "<option value='".$row["id"]."'>".$row["name"]."</option>";
			}
			print "</select>";
		}
	//}
	print '<input data-theme="b" value="Submit" type="submit">';
	print '</form>';
?>