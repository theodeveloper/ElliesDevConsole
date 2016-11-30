<?php 
session_start();
require_once("../inc/config.php");
require_once("../inc/customer.class.php");
require_once("../inc/quote.class.php");
require_once("../inc/quoteitem.class.php");
require_once("../inc/techtype.class.php");
require_once("../inc/techitem.class.php");
require_once("../inc/product.class.php");
require_once("../inc/functions.php");
require_once("../../inc/system_user.php");
require_once("../../inc/functions.php");

get_session();
//var_dump($_SESSION);
//echo "RRRR".$_SESSION["tpm_userid"];
$sysuser = new userType($_SESSION["userid"]);
//die("****$sysuser***");
    if (!is_authenticated()) {
        if (isset($_POST['aj']) && $_POST['aj'] == 1) {
            print "\n<!-- scripts , code below be eval()ed by javascript -->\nalert('You have been signed out! Please relogin');";
        } else {
            die("Not logged in");
            exit(1);
        }
    }

if (!$sysuser->hasPermission("edit_quotes")) {
    header("location: index.php?nopermission=Edit%20quotes");
    exit();
}

if (($_POST['id']=="override_target_return")||($_POST['id']=="override_deposit_replacement")){
  $_POST['value']=$_POST['value']/100;
}

if($_POST['id']=="override_rental_maintenance"){
    $str = str_replace("R", "", $_POST['value']);
    $_POST['value'] = $str;
    $sql = "Update quote_extras set ".mysql_real_escape_string($_POST['id'])."='1' where quote_id = '".mysql_real_escape_string($_POST['quoteid'])."'";
    mysql_query($sql);
    //Quoted Modified
    $query = "UPDATE `quotes` SET";
    $query .= " `last_updated` = NOW()";
    $query .= " WHERE `id` = '" . $_POST['quoteid']. "'";
     mysql_query($query);
}
if($_POST['id']=="override_rental_capital"){
    $str = str_replace("R", "", $_POST['value']);
    $_POST['value'] = $str;
    $sql = "Update quote_extras set ".mysql_real_escape_string($_POST['id'])."='".$_POST['value']."' where quote_id = '".mysql_real_escape_string($_POST['quoteid'])."'";
    mysql_query($sql);
    //Quoted Modified
    $query = "UPDATE `quotes` SET";
    $query .= " `last_updated` = NOW()";
    $query .= " WHERE `id` = '" .$_POST['quoteid']. "'";
     mysql_query($query);
}

if($_POST['id']=="override_additional_maintenance"){
    $str = str_replace("R", "", $_POST['value']);
    $_POST['value'] = $str;
    $sql = "Update quote_extras set ".mysql_real_escape_string($_POST['id'])."='".$_POST['value']."' where quote_id = '".mysql_real_escape_string($_POST['quoteid'])."'";
    mysql_query($sql);
    //Quoted Modified
    $query = "UPDATE `quotes` SET";
    $query .= " `last_updated` = NOW()";
    $query .= " WHERE `id` = '" .$_POST['quoteid']. "'";
     mysql_query($query);
}

if($_POST['id']=="override_additional_capital"){
    $str = str_replace("R", "", $_POST['value']);
    $_POST['value'] = $str;
    $sql = "Update quote_extras set ".mysql_real_escape_string($_POST['id'])."='".$_POST['value']."' where quote_id = '".mysql_real_escape_string($_POST['quoteid'])."'";
    mysql_query($sql);
    //Quoted Modified
    $query = "UPDATE `quotes` SET";
    $query .= " `last_updated` = NOW()";
    $query .= " WHERE `id` = '" .$_POST['quoteid']. "'";
     mysql_query($query);
}

if($_POST['id']=="gross_profit"){
    $str =  $_POST['value'];
    $sql = "Update quote_extras set ".mysql_real_escape_string($_POST['id'])."='".$_POST['value']."' where quote_id = '".mysql_real_escape_string($_POST['quoteid'])."'";
    mysql_query($sql);
    //Quoted Modified
    $query = "UPDATE `quotes` SET";
    $query .= " `last_updated` = NOW()";
    $query .= " WHERE `id` = '" .$_POST['quoteid']. "'";
     mysql_query($query);
}

if($_POST['id'] !=="override_rental_maintenance" && $_POST['id'] !=="override_rental_capital" && $_POST['id'] !=="override_additional_capital" && $_POST['id'] !=="override_additional_maintenance"){
    /* Does not save anything. Just echoes back for demonstration purposes. */
    $sql = "Update quotes set ".mysql_real_escape_string($_POST['id'])."='".mysql_real_escape_string($_POST['value'])."' where id = '".mysql_real_escape_string($_POST['quoteid'])."'";
    mysql_query($sql);
    //Quoted Modified
    $query = "UPDATE `quotes` SET";
    $query .= " `last_updated` = NOW()";
    $query .= " WHERE `id` = '" .$_POST['quoteid']. "'";
    mysql_query($query);
    //echo   $query;
}