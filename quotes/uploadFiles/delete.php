<?php

//$output_dir = "../images/Images/";
$output_dir = "../../sync/files/images/";
if(isset($_POST["op"]) && $_POST["op"] == "delete" && isset($_POST['name']))
{
	$fileName =$_POST['name'];
	$filePath = $output_dir. $fileName;

	if (file_exists($filePath)) 
	{
        unlink($filePath);
        delete($filePath);
    }
	print  "Deleted File ".$fileName."<br>";
}

?>