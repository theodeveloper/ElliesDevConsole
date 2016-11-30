<?php
# This code is written by Tradepage Pty Ltd for Ellies Pty Ltd (the 'parties' mentioned below)
# The code is provided based on the the terms specified within the agreed NDA between both parties.
# Both parties have agreed the code is strictly confidential
# and only by mutal agreement of both parties may the code be exposed to outside parties.
#
# Any changes made to the code other than by Tradepage Pty Ltd during the NDA Agreement void support of the code
#
/* ------ --------------------------------------------------------------------
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
//error_reporting(E_ALL);
session_start();

require_once("inc/config.php");
require_once("inc/customer.class.php");
require_once("inc/quote.class.php");
require_once("inc/quoteitem.class.php");
require_once("inc/techtype.class.php");
require_once("inc/techitem.class.php");
require_once("inc/product.class.php");
require_once("inc/functions.php");

require_once("../inc/system_user.php");
require_once("../inc/functions.php");


ini_set('display_errors', 1);
error_reporting(E_All);
//die("Here");  
get_session();
if (!isset($_SESSION["userid"])){
	header("location:../login.php");
}

$sysuser = new userType($_SESSION["userid"]);

//die("****$sysuser***");
if (!is_authenticated()) {
    if (isset($_POST['aj']) && $_POST['aj'] == 1) {
        print "\n<!-- scripts , code below be eval()ed by javascript -->\nalert('You have been signed out! Please relogin');";
    } else {
       if(isset($_GET["id"])){            
            if($_GET["id"] !==""){
                  header("location: ../login.php?id=".$_GET["id"]); 
            }
        }
        else{
            die("Not logged in");
            exit(1); 
        } 
    }
}

if (!$sysuser->hasPermission("edit_quotes")) {
    header("location: index.php?nopermission=Edit%20quotes");
    exit();
}

$quoteid = $_GET["id"];
//================================================================================
//Document Plans
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	//Add Document plan fields
	$uploadmessage ="";
	$uploadmessageErr ="";
	header('Content-type: application/json');
	if(!empty($_FILES)){
	   $floor_id = $_POST['doc_category'];
	   if($floor_id  !== ""){
	    $output_dir = "floorplans/";
	    $allowedExtension = array('pdf','png','jpg','gif','jpeg');
	    foreach ($_FILES as $file){
	      if ($file['tmp_name'] > ''){
	        if (!in_array(end(explode(".", strtolower($file['tmp_name']))), $allowedExtension)){
	            $uploadmessageErr ="Invalid file type!";
	        }
	      }
	    }

	    if($uploadmessageErr !==""){
	    	$upload = false;
		    $fileName =  basename($_FILES["floorfile"]["name"]);
		    $target_file = $output_dir.$fileName;
		    $FileType = pathinfo($target_file,PATHINFO_EXTENSION);
		    $fileNameNew =  date('Ymd'). "_" . time('hms'). ".".$FileType;

		    if (!is_dir($output_dir)) {
		        mkdir("floorplans/", 0777);
		    }

		    if (move_uploaded_file($_FILES["floorfile"]["tmp_name"], $output_dir.$fileNameNew)){
		        $upload = true;
		    }

		    if(!$upload){
		     	$uploadmessageErr = "Unable to save! Please try again...";
		    }else{
				$images = array('png','jpg','gif');
				$filegroup = "";
				if (in_array($FileType,  $images)){
					$filegroup = "Image";
				}else{
					$filegroup = "PDF";
				}

				$query  = "INSERT INTO `quote_floor_plans` (date_created,quote_id,floor_id,floor_plan,file_group) VALUES (";
				$query  .= "NOW(),";
				$query  .= " '".$quoteid. "',";
				$query  .= " '".$floor_id. "',";
				$query  .= " '".$fileNameNew. "',";
				$query  .= " '".$filegroup. "'";
				$query  .= ") ";
				$result = mysqli_query($GLOBALS["link"],$query);
				//echo$query; 
				if($result) {
					$uploadmessage = "Document Plans details saved...";
					//Quoted Modified
					$query = "UPDATE `quotes` SET";
					$query .= " `last_updated` = NOW()";
					$query .= " WHERE `id` = '" . $quoteid . "'";
					mysql_query($query);
					echo json_encode($uploadmessage);
					logAction("Added Document Plans details");
				} else {
					$uploadmessageErr = "Unable to save document plans details! Please try again...";
					echo json_encode($uploadmessageErr);
				} 
		    }
	    }else{
	    	echo json_encode($uploadmessageErr);
	    }    
	   }else{
	    $uploadmessageErr = "Unable to save! Please select category...";
	    echo json_encode($uploadmessageErr);
	   } 
	}  
}
?>