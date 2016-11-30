<?php 
session_start();
require_once("inc/system_user.php");
    include_once("inc/config.php");
    include_once("inc/functions.php");
    include_once("inc/sql.php");

require_once("quotes/inc/class.phpmailer.php");
require_once("quotes/inc/exportfunctions.php");
//$file_name = $_GET["filename"];

get_session();
    if (!is_authenticated()) {
        if (isset($_POST['aj']) && $_POST['aj'] == 1) {
            print "\n<!-- scripts , code below be eval()ed by javascript -->\nalert('You have been signed out! Please relogin');";
        } else {
            die("Not logged in");
            exit(1);
        }
    }
  header("Content-Type: application/zip");
  header("Content-Disposition: attachment; filename=db.zip");
ExportDBToCSV2();
print "\n";
?>