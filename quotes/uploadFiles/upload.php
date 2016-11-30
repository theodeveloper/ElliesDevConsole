<?php
session_start();

require_once("../inc/config.php");
require_once("../../inc/quoteimages.class.php");

//$output_dir = "../images/Images/";
$output_dir = "../../sync/files/images/";

if(isset($_SESSION["quoteID"]))
{
	if(isset($_FILES["myfile"]))
	{
		$ret = array();
		$_SESSION["Room"] = $_GET["Room"];
		$error =$_FILES["myfile"]["error"];
		//You need to handle  both cases
		//If Any browser does not support serializing of multiple files using FormData() 
		if(!is_array($_FILES["myfile"]["name"])) //single file
		{
	 	 	$fileName =  basename($_FILES["myfile"]["name"]);
	 	 	$target_file = $output_dir.$fileName;
	 	 	$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
	 	 	$fileName  = "Room_" . date('Ymd'). "_" . time('hms') ."_Upload" .$_SESSION["quoteID"] . ".". $imageFileType;
	 		move_uploaded_file($_FILES["myfile"]["tmp_name"],$output_dir.$fileName);
	    	$ret[]= $fileName;

	    	//Adds to database
	    	$images = new QuoteImages();
	    	$images->QuoteID = $_SESSION["quoteID"];
	    	$images->Hash = $images->GetHash($fileName);
	    	$images->FileName = $fileName;
	    	$images->Room = $_SESSION["Room"];
	    	$images->Status = 0;
	    	$images->Save();

	    	/*echo "ID:" .$images->QuoteID . "<br/>";
	    	echo "Hash:" .$images->Hash. "<br/>";
	    	echo "Filename:" .$images->FileName . "<br/>";
	    	echo "Room:" .$images->Room . "<br/>";
	    	echo "Status:" .$images->Status . "<br/>";*/

	    	//Creates a Thumbnail
	        $img = new SimpleImage($output_dir.$fileName);
	        $img->best_fit(105, 56)->save($output_dir  .'Thumbnails/' . $images->FileName);
		}
		else  //Multiple files, file[]
		{
		  $fileCount = count($_FILES["myfile"]["name"]);

		  for($i=0; $i < $fileCount; $i++)
		  {
		  	$fileName = $_FILES["myfile"]["name"][$i];
			$target_file = $output_dir.$fileName;
	 	 	$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
	 	 	$fileName  = "Room_" . date('Ymd'). "_" . time('hms') ."_Upload" .$_SESSION["quoteID"] . ".". $imageFileType;
	 		move_uploaded_file($_FILES["myfile"]["tmp_name"][$i],$output_dir.$fileName);
	    	$ret[]= $fileName;

	    	//Adds to database
	    	$images = new QuoteImages();
	    	$images->QuoteID = $_SESSION["quoteID"];
	    	$images->Hash = $images->GetHash($fileName);
	    	$images->FileName = $fileName;
	    	$images->Room = $_SESSION["Room"];
	    	$images->Status = 0;
	    	$images->Save();

	    	//Creates a Thumbnail
	        $img = new SimpleImage($output_dir.$fileName);
	        $img->best_fit(105, 56)->save($output_dir  .'Thumbnails/' . $images->FileName);
		  }
		}
	    echo json_encode($ret);
	 }
}
?>

