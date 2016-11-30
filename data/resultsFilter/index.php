<?php 
    
     session_start();
	 
if (isset($_SESSION['allowDataFilter']) && $_SESSION['allowDataFilter'] != '')
{
	header("location: filter.php");
}
else
{
    header("location: ../../login.php");
}
?>