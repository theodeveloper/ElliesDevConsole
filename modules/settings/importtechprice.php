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
	$csvfields = "Tech_Type,Option,New_Product_Code,Old_Technology_Type,New_Technology_Type,Related_To,KW_YN,Old_KWh,New_KWh,Old_Litres,New_Litres,Price,HasStock";
	require_once(dirname(__FILE__)."/../../inc/config.php");
	require_once(dirname(__FILE__)."/../../inc/functions.php");
	require_once(dirname(__FILE__)."/../../inc/system_user.php");
		
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
	function UpdateDBFromAssocDoc($update_products,$update_prices,$update_class, $tablename = "temp_table", $typechannel){
		$count=0;
	    if($tablename == 'new_products'){
	        print "Existing product prices have been updated<br/>";
	    }

	    if (!empty($update_products)){
	    	for($i=0;$i<count($update_products);$i++){
	    		$sql = "UPDATE $tablename SET";
               	$sql .= " `last_updated` =NOW(),";
               	$sql .= " `price_a` = '".$update_prices[$i]."'";
               	$sql .= " WHERE channel=".$typechannel." AND `code` ='".$update_products[$i]."' AND deleted=0 LIMIT 1";
              	$result = mysqli_query($GLOBALS["link"],$sql);
      
              	if($result){
              		$class_of_product = "GLS";
              		$query  = "INSERT INTO `product_price_list_log` (date_created,product_code,class_of_product,price,channel,edited_by) VALUES (";
	                $query  .= "NOW(),";
	                $query  .= " '".$update_products[$i]. "',";
	                $query  .= " '".$update_class[$i]. "',";
	                $query  .= " '".$update_prices[$i]. "',";
	                $query  .= " '".$GLOBALS['system_user']->retailChannel. "',";
	                $query  .= " '".$GLOBALS['system_user']->id. "'";
	                $query  .= ") ";
	                $result = mysqli_query($GLOBALS["link"],$query);        
	                if($result) {
	                    $count +=1;
                	}
              	}
	    	}
	    }
	    return $count;
	}

	//Converts file to array
	function csv_to_arrayDoc($filename = '', $delimiter = ',', $normalizeheaderrow = false){
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
	function Duplicates($fileNameOld=array()){
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

        if(empty($duplicates)){
        	$row = "";
        }
        return $row;
	}

	function ImportCSVProducts($typechannel,$fileNameNew) {
	    $newproducts = csv_to_arrayDoc($fileNameNew, ",", true);
	    
	    //Find duplicates
	    $duplicates = Duplicates($newproducts);

	    if($duplicates !==""){
	    	echo $duplicates;	
	    }

	    if($duplicates !==""){	
	    	echo "<br/><p style='color:red'>Unable to import products</p>";
	    	echo "<br/>";
	    	echo "<br/>";
	    	echo "<br/>";
	    }

	    if($duplicates ==""){
	    	//echo "No duplicates and all products have replacements";	
			$count=0;	
		    $newexists = false;

		    $existing_products = array();
		    $update_products = array();
		    $update_prices = array();
		    $update_class = array();
		    //print_r($newproducts);
		    //===New Products DB===
		    $sql = "SELECT code, product FROM `new_products` where channel = $typechannel and deleted=0";
		    echo $sql;
		    $sqlres = mysqli_query($GLOBALS["link"],$sql);
		    while($row = mysqli_fetch_assoc($sqlres)) {
		        $existing_products[] = $row["code"];
		    }
		    foreach($newproducts as $new) {
		    	if(in_array($new['code'], $existing_products)){
		    		$update_products[] = $new['code'];
		    		$price = trim(str_replace("R", "", $new["price_-_a"]));
		    		$update_prices[] = $price;
		    		$update_class[] = $new['class_of_product'];
		    	}
		    }

		    if(count($update_prices) >0){
		    	$count =UpdateDBFromAssocDoc($update_products,$update_prices,$update_class,"new_products",$typechannel);
		    }
		    print "<br/>".$count." Product Prices have been successfully Updated";
		}
	}
	//==================================================================================================================
	//Upload Files
	if (!empty($_FILES)){
		$output_dir = "productprice/";

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

	    $new = false;

	 	$fileNameNew =  basename($_FILES["filenew"]["name"]);
	 	$target_file = $output_dir.$fileNameNew;
	 	$FileType = pathinfo($target_file,PATHINFO_EXTENSION);
	 	$fileNameNew  = "New_Products_" . date('Ymd'). "_" . time('hms'). ".".$FileType;
	    if (move_uploaded_file($_FILES["filenew"]["tmp_name"], $output_dir.$fileNameNew)){
	        $new = true;
	    }

	    //var_dump($_FILES);
	    if ($new){
			$typechannel = $_POST["typechannel"];
			$fileNameNew = $output_dir.$fileNameNew;
	        ImportCSVProducts($typechannel,$fileNameNew);
	    }
	    else{
	        die('Please upload csv files!<br/>'.'<a href="javascript:history.go(-1);">'.'<< Go Back</a>');
	    }
	    exit();
	}
?>