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

require_once("inc/submissions.class.php");
require_once("inc/approval.class.php");


//LoginCheck("quotes.php");
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

if ($quoteid == 0) {
    if ($_GET["customerid"] > 0) {
        //===Alternate Start New Quote==
        //===This is usually called from the quotes listing page quotes.php==
        $quote = new Quote();
        $quote->CustomerID = $_GET["customerid"];
        $quote->CreatedBy = $sysuser->id;
        $quote->Save();
        $quoteid = $quote->QuoteID;
        $_GET["id"] = $quoteid;
    }
}

if (!empty($_POST["step"])) {
    if ($_POST["step"] == "updatequotevalues") {
        $quoteid = $_POST["id"];
        $quote = new Quote($quoteid);
        $quote->KWhPrice = $_POST["KWhPrice"];
        $quote->ElectricityEscalationRatePercentage = $_POST["ElectricityEscalationRatePercentage"];
        $quote->Property = $_POST["Property"];
        $quote->UpdatedBy = $sysuser->id; 
    
        /*
            ElectricityEscalationRate...	8
            KWhPrice	1.2
            Property
        */
        $quote->Complete = false;
        $origquote = new Quote($quote->QuoteID);
        $origquote->CalculateCostSavingTotals();
        $quote->Save();

        $quotenew = new Quote($quoteid);
        $quotenew->CalculateCostSavingTotals();
        LogDataChange($sysuser->id, "quotes", "Updated quote ".$quote->QuoteID, $origquote, $quote);
        //header("location: editquotecomm.php?id=".$quoteid);
        //exit();

        //Quoted Modified
        $query = "UPDATE `quotes` SET";
        $query .= " `last_updated` = NOW()";
        $query .= " WHERE `id` = '".$_GET["id"]."'";
        mysql_query($query);
    }
}
//==========================================================================
$booktitle = "Edit Quote";
$_SESSION["quoteID"] =  $_GET["id"];
$_SESSION["approved"] =0;

//Complete
if (GET("step") == "complete" && GET("id") > 0) {
    if (!$sysuser->hasPermission("complete_quotes")) {
        header("location: index.php?nopermission=Complete%20quotes");
        exit();
    }
    $quote = new Quote($_GET["id"]);
    $customerid = $quote->CustomerID;
    $quote->Complete = true;
	  $quote->Approved = 1;
    $quote->UpdatedBy = $sysuser->id;
    $quote->Save();
    //DebugPrint($quote);
    header("location: emailquotecomm.php?id=".$_GET["id"]."&complete=1");
    exit();
}

//Get Status
function GetStatus($ID){
    switch ($ID) {
        case 0:
             $status = "Pending";
            break;
        case 1:
             $status = "Approved";
            break;
        case 2:
             $status = "Open";
            break;
        case 3:
             $status = "To be confirmed";
            break;
         case 4:
             $status = "Print";
            break;
         case -1:
             $status = "Reject";
            break;
        default:
            $status = "To be confirmed";
            break;
    }
    return $status;
}

//Submit
if (GET("step") == "submit" && GET("id") > 0) {
    if (!$sysuser->hasPermission("complete_quotes")) {
        header("location: index.php?nopermission=Complete%20quotes");
        exit();
    }
    $quote = new Quote($_GET["id"]);
    $ref = $quote->QuoteReferenceNo;
    $quote_submission = new Submissions($ref);
    //Set Quote_submission values
    $quote_submission->Date_submitted = date('Y-m-d H:i:s');
    $quote_submission->Ref = $ref;
    $quote_submission->Created_by = $sysuser->id;
    $quote_submission->Status = GetStatus(5);
    $quote_submission->Comments = "Yes Submit";
    $quote_submission->Printed = "No"; 
    $quote_submission->Save();
    $_SESSION['quoteLink'] = "http://elliesdev.clientassist.co.za/quotes/editquotecomm.php?id=" . $_GET["id"]. "&reurl=completed_quotations";
    //Sends the quote to be approved
    $quote_submission->SendApprovalSubmissions($quote_submission->Ref);
    $date = date('Y-m-d H:i:s');
    $date = strtotime($date);
    $date = strtotime("+30 day", $date);
    $query  = "UPDATE `quotes` SET ";
    $query  .= " `expiration_date` = '".date("Y-m-d H:i:s", $date). "'";
    $query  .= " WHERE `id` = ". $_GET["id"];
    $result = mysqli_query($GLOBALS["link"],$query);
    //DebugPrint($quote);
    //echo "<script>window.alert('Quote submitted succesfully');</script>";
    //echo "<script>window.close();</script>";
    //header("location: emailquote.php?id=".$_GET["id"]."&complete=1");
    //header("Location: ../../inc/submission.php");
    //die();
    exit();
}

//Approved
if (GET("step") == "approved" && GET("id") > 0) {
    if (!$sysuser->hasPermission("complete_quotes")) {
        header("location: index.php?nopermission=Complete%20quotes");
        exit();
    }

    $quote = new Quote($_GET["id"]);
    $ref = $quote->QuoteReferenceNo;
    $quote_approval= new Approval($ref,$sysuser->id);
    //Set Quote_approval values
    $quote_approval->Approved_by = $sysuser->id;
    $quote_approval->Date_approved = date('Y-m-d H:i:s');
    $quote_approval->Ref = $ref;
    $quote_approval->Approved = 1;
    $quote_approval->Status = GetStatus(1);
    $quote_approval->Comments = "Yes Approved";  
    $quote_approval->Save();
    $quote_approval->sendApprovedMail($ref,$sysuser->id,"approved");
   // $quote_approval->sendApprovedMail("cassandra.b@uthgroup.co.za","christiaan.b@uthgroup.co.za",$ref,$sysuser->id,"approved");

    //DebugPrint($quote);
    //echo "<script>window.alert('Quote approved succesfully by " . $sysuser->id . "');</script>";
   // echo "<script>window.close();</script>";
    //header("location: emailquote.php?id=".$_GET["id"]."&complete=1");
    exit();   
}

//Rejected
if (GET("step") == "reject" && GET("id") > 0) {
    if (!$sysuser->hasPermission("complete_quotes")) {
        header("location: index.php?nopermission=Complete%20quotes");
        exit();
    }

    $quote = new Quote($_GET["id"]);
    $ref = $quote->QuoteReferenceNo;
    //Set Quote_approval values
    $quote_approval= new Approval($ref,$sysuser->id);
    $quote_approval->Approved_by = $sysuser->id;
    $quote_approval->Date_approved = date('Y-m-d H:i:s');
    $quote_approval->Ref = $ref;
    $quote_approval->Approved = -1;
    $quote_approval->Status = GetStatus(-1);
    $quote_approval->Comments = "Yes Rejected";  
    $quote_approval->Save();
    $quote_approval->sendApprovedMail($ref,$sysuser->id,"rejected");
    //$quote_approval->sendApprovedMail("cassandra.b@uthgroup.co.za","christiaan.b@uthgroup.co.za",$ref,$sysuser->id,"rejected");

    //DebugPrint($quote);
    //echo "<script>window.alert('Quote rejected succesfully by " . $sysuser->id . "');</script>";
    //echo "<script>window.close();</script>";
    //header("location: emailquote.php?id=".$_GET["id"]."&complete=1");
    exit();
}

//Send an email for prinintg
function sendCustomerMail($CustomerMail = "",$quoteID = "")
{
      $to =  $CustomerMail;
      $subject = "Approval Ellies Quote";

         // message
      $message = '
      <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
      <html xmlns="http://www.w3.org/1999/xhtml">
      <head>
          <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
          <title>Ellies Quote</title>
          <style type="text/css">
          <!--
              body,td,th {
              font-family:Helvetica,Verdana, Arial, Helvetica, sans-serif;
              font-size: 13pt;
              }
          -->
         </style>
      </head>

      <body>

      <p>Your quote has been approved:</p>
      <p>Ouote Reference: ' .$quoteID.'</p>
      </body>
      </html>
      ';

        // To send HTML mail, the Content-type header must be set
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

        // Mail it
        $mail_sent = mail($to, $subject, $message, $headers);
        if($mail_sent){
            echo "Mail Sent";
             //header("Location: ../contactus/submission.php");
        }else{
          echo "<style>html{background:#ededed;color:#A71110;font-family:calibri;font-size: 14px}</style>";
          echo "The email sent was unsuccessful.<a href=javascript:history.go(-1)> << Back</a>" ;
        }    
}

//Discount
if (GET("step") == "discount" && GET("id") > 0) {
//    die("discounting");
    if (!$sysuser->hasPermission("complete_quotes")) {
        header("location: index.php?nopermission=Complete%20quotes");
        exit();
    }
    $quote = new Quote($_GET["id"]);
    $discount = $_GET["discount"];
    $customerid = $quote->CustomerID;
    $quote->Discount=$discount;
    $quote->UpdatedBy = $sysuser->id;
    $quote->Save();

    //Quoted Modified
    $query = "UPDATE `quotes` SET";
    $query .= " `last_updated` = NOW()";
    $query .= " WHERE `id` = '".$_GET["id"]."'";
    mysql_query($query);
    //DebugPrint($quote);
    header("location: editquotecomm.php?id=".$_GET["id"]);
    exit();
}

//Viewing Images
if (GET("step") == "images" && GET("id") > 0) {
//    die("viewing images");
    if (!$sysuser->hasPermission("complete_quotes")) {
        header("location: index.php?nopermission=Complete%20quotes");
        exit();
    }
    header("location: images.php?id=".$_GET["id"]);
    exit();
}
//=======================================================================
require_once("inc/header.php");

$quote = new Quote($quoteid);
$quote->RentalTerm=0;
$quote2 = new Quote($quoteid);
$quote2->RentalTerm=1;
?>
    <script language="javascript">
        function EditKWhPrice() {
            var price = $('#sp_kwhprice').html();
            price = "R " + 120;
            $('#sp_kwhprice').html(price);
        }

        function EditEscalationPerc() {
            $('#sp_escalation').html('Ello');
        }

        function ShowQuoteValues() {
            $('#quote-container').hide();
            $('#quote-edit-details').show();
        }

        function showPopup(){
            $("#popupLogin").popup("open").trigger("create");
        }

        function setDiscount(val){
            $("#popupLogin").popup("close");
        }

        function UpdateQuoteValues() {
            var data = "step=updatequotevalues&id=" + encodeURIComponent(<?php  print $quoteid; ?>) + "&kwhprice=" + encodeURIComponent($("#KWhPrice").val()) + "&elecesc=" + encodeURIComponent($("#ElectricityEscalationRatePercentage").val()) + "&property=" + encodeURIComponent($("#Property").val());
            jQAJAXCall("editquotecomm.php", data)
            $('#quote-edit-details').hide();
            $('#quote-container').show();
        }

        function UpdateCancel() {
            $('#quote-edit-details').hide();
            $('#quote-container').show();
        }
	
        $(function() {
                
          $(".click").editable("./inline/echo.php", { 
              indicator : "<img src='img/indicator.gif'>",
              tooltip   : "Click to edit...",
        	  width : "100px",
              style  : "inherit",
        	      submitdata : function() {
              return {quoteid : <?php  print $quoteid; ?>};
            }

          });
          $(".clickselect").editable("./inline/echo.php", { 
              indicator : "<img src='img/indicator.gif'>",
              tooltip   : "Click to edit...",
        	  width : "100px",
        	 data   : "{'0':'Excludes Installation','1':'In Hours Installation','2':'Out Hours Installation','selected': '<?php  echo $quote->DoInstall; ?>'}", 
             type    : 'select',
             submit  : 'OK',
              style  : "inherit",
        	      submitdata : function() {
              return {quoteid : <?php  print $quoteid; ?>};
            }

          });
              $(".clickselecthours").editable("./inline/echo.php", { 
              indicator : "<img src='img/indicator.gif'>",
              tooltip   : "Click to edit...",
        	  width : "100px",
        	 data   : "{'0':'In Hours','1':'Out Hours','selected': '<?php  echo $quote->OutHours; ?>'}", 
             type    : 'select',
             submit  : 'OK',
              style  : "inherit",
        	      submitdata : function() {
              return {quoteid : <?php  print $quoteid; ?>};
            }

          });

        });
    </script>

<?php 
    if (!empty($_GET["startdiv"])){
    print "
    <script language='javascript'>

    $(function() { 
    $(window).load(function() {
     $('html, body').animate({
        scrollTop: $('#".$_GET["startdiv"]."').offset().top-($(window).height()/2)}, 300);
    }); 
    });
    </script>

    ";
    }
?>

<?php 

$reurl = "";
if (!empty($_GET["reurl"])) {
    $_SESSION["reurl"] = $_GET["reurl"];
    $reurl = $_GET["reurl"];
}

$customerid = 0;

if ($quoteid == "0") {
    $quote->CustomerID = $_GET["customerid"];
    $quote->Save();
}
$customerid = $quote->CustomerID;

PrintNavBar($booktitle, true, "quotes.php?customerid=".$customerid, $reurl);

$isnew = false;
$customer = new Customer($quote->CustomerID);
if (!empty($_GET["isnew"])) {
    $isnew = $_GET["isnew"];
}

print "<div class='ui-body ui-body-b' id='quote-edit-details'";
if (!$isnew) {
    print " style='display:none;'";
}

print "><form method='Post' action='editquotecomm.php?id=".$quoteid."'>";
PrintRange("KWhPrice", "Price/KWh (R)", $quote->KWhPrice, 0.7, 1.5, 0.1);
PrintRange("ElectricityEscalationRatePercentage", "Electricity Escalation Rate (%)", $quote->ElectricityEscalationRatePercentage, 7, 20, 1);
PrintTextArea("Property", "Property", $quote->Property, "Enter property details");
print "<input type='hidden' id='step' name='step' value='updatequotevalues'>";
print "<input type='hidden' id='id' name='id' value='".$quoteid."'>";
print "</form></div>";
?>
    <script>
        $( ".rates_qty ul li" ).click(function() {
            var hiddenValue = $(this).find( ":hidden" ).val();
            var par = $(this).parent().attr('id');
            var changeInput = $(this).parent().find(':text');
            $(changeInput).val(hiddenValue);
        });
    </script>
<?php 
/* add buttons to top ------*/
function GetApprovalMembers(){
    $user ="";
    $sql = "SELECT * FROM `approval_users` INNER JOIN `system_users` ON `system_users`.id = `approval_users`.member WHERE `status` = 1 AND `system_users`.branch_id='".$GLOBALS['sysuser']->branchID."'";
    $sqlres = mysqli_query($GLOBALS["link"],$sql);
    while($row = mysqli_fetch_assoc($sqlres)){
        $user .= $row["member"].";";
    }
    return $user;
}

function GetApprovalSetting(){
    $setting ="";
    $sql = "SELECT `approval_setting` FROM `branches` WHERE `id` ='".$GLOBALS['sysuser']->branchID."'";
    $sqlres = mysqli_query($GLOBALS["link"],$sql);
    while($row = mysqli_fetch_assoc($sqlres)){
        $setting .= $row["approval_setting"];
    }
    return $setting;
}

$approvalMembers= GetApprovalMembers();
$users = explode(";",$approvalMembers);
$member = $users[0];
$member2 = $users[1];
$member3 = $users[2];

$approval_setting = GetApprovalSetting();

if(date('Y-m-d') ==date('Y-m-d',strtotime($quote->ExpirateDate)) ){
    print "<h1 data-role='label' data-theme='a' data-transition='fade' data-inline='true' data-icon='alert' data-ajax='false' style='color:red'>Quote has expired</h1>";
}else{
    if($approval_setting == "Yes"){
        if ($quote->Approved != -1 ){
            //Status
            if ($quote->Approved  == -1 ){
                print "<h1 data-role='label' data-theme='a' data-transition='fade' data-inline='true' data-icon='search' data-ajax='false' style='color:red'>Quote has been rejected</h1>";
            }elseif ($quote->Approved  == 1 ){      
                print "<h1 data-role='label' data-theme='a' data-transition='fade' data-inline='true' data-icon='search' data-ajax='false' style='color:green'>Quote has been approved</h1>";      
               //print "<a href='printquote.php?id=".$quoteid."&preview=1' data-role='button' data-theme='a' data-transition='fade' data-inline='true' data-icon='search' data-ajax='false' style='float:right'>Preview / Print Quote</a>";
               //print "<a href='requiredfields.php?id=".$quoteid."' data-role='button' data-theme='a' data-transition='fade' data-inline='true' data-icon='search' data-ajax='false' style='float:right'>Required fields</a>";

                if ($quote->NumberOfItems > 0) {
                    if ($customer->Email != "") {
                        if (strpos($customer->Email, ".") > 0 && strpos($customer->Email, "@") > 0) {
                            print "<a href='emailquotecomm.php?id=".$quoteid."' data-role='button' data-theme='a' data-transition='fade' data-inline='true' data-icon='info' data-ajax='false' style='float:right'>Email Quote</a>";
                        }
                    }
                }
            }

            //Approval Members
            if($sysuser->id == $member || $sysuser->id == $member2 || $sysuser->id == $member3)
            {
                if ($quote->Approved  != 1 ){
                    print "<a href=\"editquotecomm.php?step=approved&id=".$quote->QuoteID."\"  data-role='button' data-theme='a' data-transition='fade' data-inline='true' data-icon='info' data-ajax='false'>Approve Quote</a>";
                    print "<a href=\"editquotecomm.php?step=reject&id=".$quote->QuoteID."\" onclick=\"javascript:rejectQuote()\" data-role='button' data-theme='a' data-transition='fade' data-inline='true' data-icon='info' data-ajax='false'>Reject Quote</a>";
                } 
            }else{
                 //Checks if quote has been request for approval
                $quote = new Quote($_GET["id"]);
                $ref = $quote->QuoteReferenceNo;
                $quote_submission = new Submissions($ref);
                if(empty($quote_submission->Ref)){
                    if ($quote->Approved  != 1 ){
                        print "<a href=\"editquotecomm.php?step=submit&id=".$quote->QuoteID."\"  data-role='button' data-theme='a' data-transition='fade' data-inline='true' data-icon='info' data-ajax='false'>Request Quote Approval</a>";
                    }
                } 
          
                //Checks if quote has been approved/rejected
                if(!empty($quote_submission->Ref))
                {
                    $quote = new Quote($_GET["id"]);
                    $ref = $quote->QuoteReferenceNo;
                    $quote_approval= new Approval($ref,$member);
                    $quote_approval2= new Approval($ref,$member2);
                    $quote_approval3= new Approval($ref,$member3);
                   
                    //Chheck if the status is approved by all memebers
                    if($quote_approval->Status == "Approved" && $quote_approval2->Status  =="Approved"  && $quote_approval3->Status =="Approved")
                    {
                        $_SESSION["approved"] =1;
                        $quote = new Quote($_GET["id"]);
                        $customerid = $quote->CustomerID;
                        $quote->Approved = 1;
                        $quote->UpdatedBy = $sysuser->id;
                        $quote->Save();
                        echo "<script>window.alert('Quote is approved succesfully by all members');</script>";

                        //Set Quote_submission values
                        $ref = $quote->QuoteReferenceNo;
                        $quote_submission = new Submissions($ref);
                        $quote_submission->Date_submitted = date('Y-m-d H:i:s');
                        $quote_submission->Ref = $ref;
                        $quote_submission->Created_by = $sysuser->id;
                        $quote_submission->Status = GetStatus(1);
                        $quote_submission->Comments = "Yes approved";  
                        $quote_submission->Save();

                        //Printed and sent to the Customer
                        /*$customerid = $quote->CustomerID;
                        $customer = new Customer($customerid);
                        $CustomerMail =  $customer->Email;
                        sendCustomerMail($CustomerMail); */
                    }else if($quote_approval->Status == "Reject" && $quote_approval2->Status  =="Reject"  && $quote_approval3->Status =="Reject")
                    {

                        $quote = new Quote($_GET["id"]);
                        $customerid = $quote->CustomerID;
                        $quote->Approved = -1;
                        $quote->UpdatedBy = $sysuser->id;
                        $quote->Save();
                        echo "<script>window.alert('Quote is Reject succesfully by all members');</script>";

                        //Set Quote_submission values
                        $quote_submission->Date_submitted = date('Y-m-d H:i:s');
                        $quote_submission->Ref = $ref;
                        $quote_submission->Created_by = $sysuser->id;
                        $quote_submission->Status = GetStatus(-1);
                        $quote_submission->Comments = "Yes Rejected";  
                        $quote_submission->Save();
                    }else{
                        //Status
                        print "<h1 data-role='label' data-theme='a' data-transition='fade' data-inline='true' data-icon='search' data-ajax='false' style='color:orange'>Quote is still waiting for approval</h1>";
                        print "<p><b style='float:right'>Valid Until:".date('Y-m-d',strtotime($quote->ExpirateDate))."</b><p><br/><br/><br/>";  
                    }
                }
            }
        }
    }else{
        if ($customer->Email != "") {
            if (strpos($customer->Email, ".") > 0 && strpos($customer->Email, "@") > 0) {
                print "<a href='emailquotecomm.php?id=".$quoteid."' data-role='button' data-theme='a' data-transition='fade' data-inline='true' data-icon='info' data-ajax='false' style='float:right'>Email Quote</a>";
            }
        }
    }  
}


print "<a href='printquote.php?id=".$quoteid."&preview=1&".time()."' data-role='button' data-theme='a' data-transition='fade' data-inline='true' data-icon='search' data-ajax='false' style='float:right'>Preview / Print Quote</a>";
print "<a href='requiredfields.php?id=".$quoteid."&preview=1' data-role='button' data-theme='a' data-transition='fade' data-inline='true' data-icon='search' data-ajax='false' style='float:right'>Required fields</a>";

//View Images
print "<a href=\"viewimages.php?id=".$quote->QuoteID."\"  data-role='button' data-theme='a' data-transition='fade' data-inline='true' data-icon='info' data-ajax='false' style='float:right'>View Images</a>";
//View Checklist
print "<a href=\"quotechecklist.php?id=".$quote->QuoteID."\"  data-role='button' data-theme='a' data-transition='fade' data-inline='true' data-icon='info' data-ajax='false' style='float:right'>View Quote Checklist</a>";

//Add Rental_option
$QuoteID= "";
$query = "SELECT `quote_id` FROM `quote_extras` WHERE `quote_id` = '" .$quote->QuoteID. "'";
$result = mysqli_query($GLOBALS["link"],$query);
while($row = mysqli_fetch_assoc($result)) {  
    $QuoteID = $row['quote_id'];
} 
if($QuoteID == ""){
    $query  = "INSERT INTO `quote_extras` (quote_id,rental_option) VALUES (";
    $query  .= " '".$quote->QuoteID. "',";
    $query  .= " '0'";
    $query  .= ") ";
    $result = mysqli_query($GLOBALS["link"],$query);
    if($result) {
       //Quoted Modified
        $query = "UPDATE `quotes` SET";
        $query .= " `last_updated` = NOW()";
        $query .= " WHERE `id` = '".$QuoteID."'";
        mysql_query($query);
        logAction("Edit Rental Option");
    }
} 
//Rental_option
if(isset($_POST['rental_option'])){
    $quote_id =  $_POST['quote_id']; 
    $rental_option =  $_POST['rental_option']; 
    //echo "rental_option" . $rental_option;
    $QuoteID = "";
    $query = "SELECT `quote_id` FROM `quote_extras` WHERE `quote_id` = '" . $quote_id . "'";
    $result = mysqli_query($GLOBALS["link"],$query);
    while($row = mysqli_fetch_assoc($result)) {  
        $QuoteID = $row['quote_id'];
    } 

    if($QuoteID !== ""){
        $query = "UPDATE `quote_extras` SET";
        $query .= " `quote_id` = '" . $QuoteID . "'";
        $query .= ", `rental_option` = '" . $rental_option . "'";
        $query .= " WHERE `quote_id` = '" . $QuoteID. "'";
        mysql_query($query);
        if($result) {
          //Quoted Modified
          $query = "UPDATE `quotes` SET";
          $query .= " `last_updated` = NOW()";
          $query .= " WHERE `id` = '" . $QuoteID. "'";
          mysql_query($query);
          logAction("Edit Rental Option");
        } 
    }else{
        $query  = "INSERT INTO `quote_extras` (quote_id,rental_option) VALUES (";
        $query  .= " '".$quote_id. "',";
        $query  .= " '".$rental_option."'";
        $query  .= ") ";
        $result = mysqli_query($GLOBALS["link"],$query);
        if($result) {
            header('Content-type: text/html');
            print "<p>Rental Option saved</p>";
            //Quoted Modified
            $query = "UPDATE `quotes` SET";
            $query .= " `last_updated` = NOW()";
            $query .= " WHERE `id` = '" . $QuoteID . "'";
            mysql_query($query);
            logAction("Edit Rental Option");
        } else {
             header('Content-type: text/html');
            print "<p>Rental Option not saved</p>";
        } 
    }     
}
/*------*/
print "<div id='quote-container'";
if ($isnew) {
    print " style='display:none;'";
}
print ">";

print '<br/>';
print "<div id='quote-details'>";
print '<img src="images/details_icon.png" style="float:left; padding-right:100px;"/>';
print '<table>';
print '<tr>';
print '<td align="center" valign="middle"><img src="images/ellies_icon.png" /></td>';
print '<td align="left" class="heading">Electricity Escalation Rate (%):</td>';
print '<td align="left" class="quote_vals" valign="middle"><div class="click" id="escalation_rate" style="display: inline">'.$quote->ElectricityEscalationRatePercentage.'</div></td>';
print '<td align="left" class="customer_icon" valign="middle"><img src="images/name_icon.png" /></td>';
print '<td align="left" class="customer_vals" valign="middle">'.$customer->Surname.", ".$customer->Name.'</td>';
print '</tr>';
print '<tr>';
print '<td align="center" valign="middle"><img src="images/ellies_icon.png" /></td>';
print '<td align="left" class="heading">Quote Reference:</td>';
print '<td align="left" class="quote_vals" valign="middle">'.$quote->QuoteReferenceNo.'</td>';
print '<td align="left" class="customer_icon" valign="middle"><img src="images/phone_icon.png" /></td>';
print '<td align="left" class="customer_vals" valign="middle">'.$customer->CellPhone.'</td>';
print '</tr>';
print '<tr>';
print '<td align="center" valign="middle"><img src="images/ellies_icon.png" /></td>';
print '<td align="left" class="heading">Price/kWh (R):</td>';
print '<td align="left" class="quote_vals" valign="middle"><div class="click" id="kwh_price" style="display: inline">'.$quote->KWhPrice.'</div></td>';
print '<td align="left" class="customer_icon" valign="middle"><img src="images/email_icon.png" /></td>';
print '<td align="left" class="customer_vals" valign="middle">'.$customer->Email.'</td>';
print '</tr>';
print '<tr>';
print '<td align="center" valign="middle"><img src="images/ellies_icon.png" /></td>';
print '<td align="left" class="heading">Discount:</td>';
print '<td align="left" class="quote_vals" valign="middle"><div class="click" id="discount" style="display: inline">'.$quote->Discount.'</div></td>';
print '<td align="left" class="customer_icon" valign="middle"><img src="images/property_icon.png" /></td>';
print '<td align="left" class="customer_vals" valign="middle">'.$quote->Property.'</td>';
print '</tr>';
print '<tr>';
print '<td align="center" valign="middle"><img src="images/ellies_icon.png" /></td>';
print '<td align="left" class="heading">Distance:</td>';
print '<td align="left" class="quote_vals" valign="middle">'.$quote->Distance.'</td>';
print '</tr>';
print '<tr>';
print '<td align="center" valign="middle"><img src="images/ellies_icon.png" /></td>';
print '<td align="left" class="heading">Date:</td>';
print '<td align="left" class="quote_vals" valign="middle">'.$quote->DateCreated.'</td>';
print '<td align="left" class="customer_icon" valign="middle"><img src="images/property_icon.png" /></td>';

if ($quote->DoInstall==0)
	$isInstall = "Excludes Installation";
else if ($quote->DoInstall==1)
	$isInstall = "In Hours Installation";
else if ($quote->DoInstall==2)
	$isInstall = "Out Hours Installation";	
$isHours = ($quote->OutHours==0)?"In Hours":"Out Hours";
print '<td align="left" class="customer_vals" valign="middle"><div class="clickselect" id="do_install" style="display: inline">'.$isInstall.'</div></td>';
print '</tr>';


//Cost Overview - Rental Option
$sql = "SELECT `rental_option`";
$sql.= " FROM `quote_extras`";
$sql.= " WHERE `quote_id` = '".mysqli_real_escape_string($GLOBALS["link"],$quoteid)."' LIMIT 1";
$sqlres = mysqli_query($GLOBALS["link"],$sql);
$rental_option="";
while($row = mysqli_fetch_assoc($sqlres)) {
    $rental_option = $row['rental_option'];
} 

$sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['sysuser']->retailChannel;
$sqlres = mysqli_query($GLOBALS["link"],$sql);
$row = mysqli_fetch_assoc($sqlres);
$channeltype = $row['type'];
if ($channeltype == "Commercial"){
    print '<tr>';
    print '<td align="center" valign="middle"><img src="images/ellies_icon.png" /></td>';
    print '<td align="left" class="heading">Show Rental Option(PDF):</td>';
    print '<td align="left" class="quote_vals" valign="middle">';
    print "<select id='rental_option' name='rental_option' style='width='10%'>";
        if($rental_option == "0"){
            print "<option id='Yes' value='Yes' selected >Yes</option>";
            print "<option id='No' value='No'>No</option>";
        }else{
            print "<option id='Yes' value='Yes' >Yes</option>";
            print "<option id='No' value='No' selected>No</option>";
        }
    print "</select></td>";
    print '</tr>';  
}

$sql = "SELECT `gross_profit`";
$sql.= " FROM `quote_extras`";
$sql.= " WHERE `quote_id` = '".mysqli_real_escape_string($GLOBALS["link"],$quoteid)."' LIMIT 1";
$sqlres = mysqli_query($GLOBALS["link"],$sql);
$gross_profit=0;
while($row = mysqli_fetch_assoc($sqlres)) {
    $gross_profit = $row['gross_profit'];
}

print '<tr>';
    print '<td align="center" valign="middle"><img src="images/ellies_icon.png" /></td>';
    print '<td align="left" class="heading">Gross Profit:</td>';
    print '<td align="left" class="quote_vals" valign="middle"><div class="click" id="gross_profit" style="display: inline">'.$gross_profit.'</div></td>';
print '</tr>';

print '</table>';
print "</div>";
print '<br/><br/>';

if ($channeltype == "Commercial"){
    //View rental_option
    print "<div style='float:right;width='150%'><input type='hidden' id='quoteID' name='quoteID' value='".$quote->QuoteID."'/></div>";
}
print '<br/><br/>';

//=========================================
//Technical Product Information
print "<a href='#' id='viewtechnicalinfo' data-role='button' data-theme='a' data-transition='fade' data-inline='true' data-icon='info' data-ajax='false' style='float:left'>Technical Product Information</a><br/><br/><br/><br/><br/>";
$sql = "SELECT `quote_items`.`id`";
$sql.= " FROM `quote_items`";
$sql.= " WHERE `quote_items`.`quote_id` = '".mysqli_real_escape_string($GLOBALS["link"],$quoteid)."' ORDER BY `id`";
$sqlres = mysqli_query($GLOBALS["link"],$sql);

print "<table id='technicalinfo' border='1' cellpadding='10' style='float:left;' width='50%'>
      <tr>
          <th width='150'>Name</th>
          <th width='30'>LUX</th>
          <th width='20'>Lumens</th>
          <th width='20'>Qty</th>     
          <th width='30'>Hours'operated</th>
          <th width='30'>Expected lifetime</th>     
      </tr>";

      while($row = mysqli_fetch_assoc($sqlres)) {
        $quoteitem = new QuoteItem($row["id"], $quote);
        $qty = $quoteitem->GetInputValues();
        $quoteitem->CalculateCostSavings();

        $qty_val = reset($qty); // First Element's Value
        $qty_key = key($qty); // First Element's Key
        $qty_key = str_replace("_", " ", $qty_key);
        if ($qty_key != "Hours Per Day"){
            $qty_key = "Average Shower Time";
            $qty_val = $qty["Average_Shower_Time"]." min";
        }

        if ($quoteitem->Qty>0){

          $sql2 = "SELECT `lumen_output`";
          $sql2.= " FROM `new_products`";
          $sql2.= " WHERE `new_products`.`id` = '".mysqli_real_escape_string($GLOBALS["link"],$quoteitem->NewProductID)."'";
          $sqlres2 = mysqli_query($GLOBALS["link"],$sql2);
          $row2 = mysqli_fetch_assoc($sqlres2);

          print "<tr>
                <td>".$quoteitem->ItemDescription."</td>
                <td>(none)</td>
                <td>".$row2['lumen_output']."</td>
                <td>".$quoteitem->NewQty."</td>
                <td>".$qty_val."</td>
                <td>(none)</td>
            </tr>";
        }
      }
print "</table>";
//=========================================
//Document Plans
//Add Document plan fields
$uploadmessage ="";
$uploadmessageErr ="";
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
        $query .= " WHERE `quote_id` = '" . $quoteid . "'";
        $result = mysqli_query($GLOBALS["link"],$query);

        logAction("Added Document Plans details");
      } else {
        $uploadmessageErr = "Unable to save document plans details! Please try again...";
      } 
    }
   }else{
    $uploadmessageErr = "Unable to save! Please select category...";
   } 
}  

$query  = "SELECT *";
$query .= " FROM `floor_plan_settings`";
$result = mysqli_query($GLOBALS["link"],$query);
if ($result->num_rows > 0) {
  print "<a href='#' id='addFloorplans' data-role='button' data-theme='a' data-transition='fade' data-inline='true' data-icon='info' data-ajax='false' style='float:right'>Add Document Plans</a><br/><br/><br/><br/>";

    print "<div id='showfloorplansetup' name='showfloorplansetup' cellspacing='0' cellpadding='4' border='0' width='100%'>";
        print "<iframe src='../uploadfloorplans.php?id=".$quoteid."'  width='600' height='600' border='0' frameborder='0' style='position:absolute;right:10px'></iframe>"; 
    print "</div>";

  /*$documenttable = "<form id='formfloor'  name='formfloor' action='editquotecomm.php?id=".$quoteid."' method='post' enctype='multipart/form-data' style='float:right'>  
        <input type='hidden' id='quote_id_floor' name='quote_id_floor' value='".$quoteid."' > 
        <input type='hidden' id='doc_category' name='doc_category'> 

        <table id='floorplans' cellpadding='10'>
        <tr>
            <td>Select Category</td>
            <td>
            <select id='floorplan_user'>
              <option value='' selected='selected'>[Please select]</option>";
              $query  = "SELECT *";
              $query .= " FROM `floor_plan_settings`";
              $result = mysqli_query($GLOBALS["link"],$query);
              while ($arr = mysqli_fetch_assoc($result)) {
                  $FloorPlan = $arr['name'];
                  $documenttable .= "<option id='".$arr['id']."' value='".$FloorPlan."'>".ucfirst($FloorPlan) ."</option>";
              }
  $documenttable .="</select></td>
       </tr>
        <tr>
            <td>Document Plan</td>
            <td></a><input name='floorfile' id='floorfile' type='file' /></td>
        </tr>
        <tr>
            <td></td>
            <td><input type='button' id='savefloorplan' name='Submit' value='Save'/>    
            </td>
        </tr>
        <tr>
            <td><label class='goodmessage' id='goodmessage'  style='color:green'>".$uploadmessage."</label></td>
            <td><label class='badmessage'  id='badmessage'  style='color:red'>".$uploadmessageErr."</label></td>  
            </td>
        </tr>
        </table>
        </form>";
  echo $documenttable; */
}
//-------------------OVERVIEWS
$quote->CalculateCostSavingTotals();
$savings = $quote->Get5YearSavings();
$term_savings=($quote->Get5Savings());
$ttl_savings = end($savings["Cumulative Savings"]);
$array_keys = array_keys($savings["Price per kWh"]);
$ttl_savings_time = end($array_keys);
//end get year num
/*--------Total Overview------*/
//Cash Option
print "<img  id ='product_divider'  src='images/horiz_divider.png'/>";
if ($quote->RentalTerm==0){
    print '<h1 class="icon_heading">Cost Overview - Cash Option</h1>';
} else {
    print '<h1 class="icon_heading">Cost Overview - Rental Option</h1>';
}
print '<div id="cost_menu">';
print '<div class="total_icon">';
print '<div class="inner_icon">';
print '<div ><h1>Category</h1></div>';
print '<div>Replacement Products</div><p/>';
print '<div>Additional Products</div><p/>';
print '<div><b>Total</b></div><p/>';
print '</div>';
print '</div>';
if ($quote->RentalTerm==0){
    print '<div class="total_icon">';
    print '<div class="inner_icon">';
    print '<div><h1>Product Price</h1></div>';

    print '<div>R<div class="click" id="override_price_replacement_capital" style="display: inline-block">'.number_format($quote->ReplacementPrice-$quote->TravelCostsReplacement-$quote->CrushCostsReplacement-$quote->LabourCostsReplacement-$quote->MaterialsCostsReplacement,2).'</div></div><p/>';
	  print '<div>R<div class="click" id="override_price_additional_capital" style="display: inline-block">'.number_format($quote->AdditionalPrice-$quote->TravelCostsAdditional-$quote->CrushCostsAdditional-$quote->LabourCostsAdditional-$quote->MaterialsCostsAdditional,2).'</div></div><p/>';
	  print '<div><b>R'.number_format($quote->AllPrice-$quote->TravelCostsAdditional-$quote->CrushCostsAdditional-$quote->LabourCostsAdditional-$quote->MaterialsCostsAdditional-$quote->TravelCostsReplacement-$quote->CrushCostsReplacement-$quote->LabourCostsReplacement-$quote->MaterialsCostsReplacement,2).'</b></div><p/>';
    print '</div>';
    print '</div>';

    print '<div class="total_icon">';
    print '<div class="inner_icon">';
    print '<div><h1>Installation</h1></div>';

    print '<div>R'.number_format($quote->TravelCostsReplacement+$quote->CrushCostsReplacement+$quote->LabourCostsReplacement+$quote->MaterialsCostsReplacement,2).'</div><p/>';
	  print '<div>R'.number_format($quote->TravelCostsAdditional+$quote->CrushCostsAdditional+$quote->LabourCostsAdditional+$quote->MaterialsCostsAdditional,2).'</div><p/>';
	  print '<div><b>R'.number_format($quote->TravelCostsAdditional+$quote->CrushCostsAdditional+$quote->LabourCostsAdditional+$quote->MaterialsCostsAdditional+$quote->TravelCostsReplacement+$quote->CrushCostsReplacement+$quote->LabourCostsReplacement+$quote->MaterialsCostsReplacement,2).'</b></div><p/>';
    print '</div>';
    print '</div>';

    print '<div class="total_icon">';
    print '<div class="inner_icon">';
    print '<div><h1>Total Price</h1></div>';

    print '<div>R'.number_format($quote->ReplacementPrice,2).'</div><p/>';
	  print '<div>R'.number_format($quote->AdditionalPrice,2).'</div><p/>';
	  print '<div>R'.number_format($quote->AllPrice,2).'</div><p/>';
    print '</div>';
    print '</div>';
} else {
	print '<div class="total_icon">';
    print '<div class="inner_icon">';
    print '<div><h1>Capital</h1></div>';
        print '<div>R'.number_format($quote->getRentalCapital("replacement",$quote->RentalTerm), 2).'</div><p/>';
	    print '<div>R'.number_format($quote->getRentalCapital("additional",$quote->RentalTerm), 2).'</div><p/>';
	    print '<div><b>R'.number_format($quote->getRentalCapital("all",$quote->RentalTerm), 2).'</b></div><p/>';
    print '</div>';
    print '</div>';

	print '<div class="total_icon">';
    print '<div class="inner_icon">';
    print '<div><h1>Maintenance</h1></div>';
        print '<div>R'.number_format($quote->getRentalMaintenance("replacement",$quote->RentalTerm, $quote->MaintenancePercentage), 2).'</div><p/>';
        print '<div>R'.number_format($quote->getRentalMaintenance("additional",$quote->RentalTerm, $quote->MaintenancePercentage), 2).'</div><p/>';
        print '<div><b>R'.number_format($quote->getRentalMaintenance("all",$quote->RentalTerm, $quote->MaintenancePercentage), 2).'</b></div><p/>';
    print '</div>';
    print '</div>';

	print '<div class="total_icon">';
    print '<div class="inner_icon">';
    print '<div><h1>Total</h1></div>';
        print '<div>R'.number_format($quote->getRentalAmount("replacement",$quote->RentalTerm, $quote->MaintenancePercentage), 2).'</div><p/>';
	    print '<div>R'.number_format($quote->getRentalAmount("additional",$quote->RentalTerm, $quote->MaintenancePercentage), 2).'</div><p/>';
	    print '<div><b>R'.number_format($quote->getRentalAmount("all",$quote->RentalTerm, $quote->MaintenancePercentage), 2).'</b></div><p/>';
    print '</div>';
    print '</div>';
}
print '</div>';
if ($quote->RentalTerm==0){
    print '<h2 class="icon_heading">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Replacement Items Overview</h1>';
}else {
    print '<h2 class="icon_heading">Contract Overview</h1>';
}
print '<div id="overview">';
if ($quote->RentalTerm==0){
    print '<div class="total_icon"><div class="inner_icon"><img src="images/calendar_icon2.png" />';
    print '<div class="icon_description">Payback Period</div>';
    print '<h2>'.$quote->TotalPaybackPeriodFormatted.'</h2></div></div>';
    print '<div class="total_icon"><div class="inner_icon"><img src="images/plus_icon2.png" />';
    print '<div class="icon_description">Monthly Savings</div>';
    print '</div><h2>R'.number_format($quote->MonthlyCostSaving, 2).'</h2></div>';
}
if ($quote->RentalTerm==0){
    print '<div class="total_icon"><div class="inner_icon"><img src="images/percentage_icon2.png" />';
    print '<div class="icon_description">Percentage Saved</div>';
    print '<h2>'.number_format($quote->KWhSavedPerc, 0).'%</h2></div></div>';

    print '<div class="total_icon"><div class="inner_icon"><img src="images/monthly_payment_icon.png" />';
    print '<div class="icon_description">Savings after '.$ttl_savings_time.' years</div>';
    print '<h2>'.$ttl_savings.'</h2></div></div>';
    print '<div class="total_icon"><div class="inner_icon"><img src="images/tick_icon2.png" />';
    print '<div class="icon_description">Cost</div>';
    print '<h2>R<div class="click" id="override_price_replacement" style="display: inline">'.number_format($quote->ReplacementPrice, 2).'</div></h2></div></div>';
} else {
    print '<div class="total_icon"><div class="inner_icon"><img src="images/calendar_icon2.png" />';
    print '<div class="icon_description">Rental Term</div>';
    print '<h2><div class="click" id="rental_term" style="display: inline">'.number_format($quote->RentalTerm, 0).'</div> Months</h2></div></div>';

    print '<div class="total_icon"><div class="inner_icon"><img src="images/deposit_icon.png" />';
    print '<div class="icon_description">Deposit</div>';
    print '<h2>R'.number_format($quote->getDeposit("all"), 2).' (<div class="click" id="override_deposit_replacement" style="display: inline">'.$quote->getDepositProportion().'%)</div></h2></div></div>';
    print '<div class="total_icon"><div class="inner_icon"><img src="images/monthly_payment_icon.png" />';

    print '<div class="icon_description">Monthly Rental by year</div>';
    print '<h2>';
	  $termyears = ceil($quote->RentalTerm/12);
	  print '1: R'.sprintf("%2f",$quote->getRentalAmount("all",$quote->RentalTerm, $quote->MaintenancePercentage)).'<br/>';
      	for ($terms = 1; $terms<$termyears; $terms++){
      		print ($terms+1).': R'.number_format(($quote->getRentInMonth("all",$quote->RentalTerm,$terms*12,$quote->MaintenancePercentage)), 2).'<br/>';
      	}
	  print '</h2></div></div>';
    print '<div class="total_icon"><div class="inner_icon"><img src="images/percentage_icon2.png" />';
    print '<div class="icon_description">Return</div>';
    print '<h2><div class="click" id="override_target_return" style="display: inline">'.number_format((pow(1+$quote->getReturn($quote->RentalTerm, $quote->MaintenancePercentage),12)-1)*100, 2).'</div>%</h2></div></div>';
}
print '</div>';
//-------------------OVERVIEWS
$sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['sysuser']->retailChannel;
$sqlres = mysqli_query($GLOBALS["link"],$sql);
$row = mysqli_fetch_assoc($sqlres);
$channeltype = $row['type'];
if ($channeltype != "Franchises"){

    $quote2->CalculateCostSavingTotals();
    $savings = $quote2->Get5YearSavings();
    $term_savings=($quote2->Get5Savings());
    $ttl_savings = end($savings["Cumulative Savings"]);
    $array_keys = array_keys($savings["Price per kWh"]);
    $ttl_savings_time = end($array_keys);
    /*--------Total Overview------*/
    //Rental Option
    print "<img  id ='product_divider'  src='images/horiz_divider.png'/>";
    if ($quote2->RentalTerm==0){
        print '<h1 class="icon_heading">Cost Overview - Cash Option</h1>';
    } else {
        print '<h1 class="icon_heading">Cost Overview - Rental Option</h1>';
    }
    print '<div id="cost_menu">';
    print '<div class="total_icon">';
    print '<div class="inner_icon">';
    print '<div ><h1>Category</h1></div>';
    print '<div>Replacement Products</div><p/>';
    print '<div>Additional Products</div><p/>';
    print '<div><b>Total</b></div><p/>';
    print '</div>';
    print '</div>';
    if ($quote2->RentalTerm==0){
        print '<div class="total_icon">';
        print '<div class="inner_icon">';
        print '<div><h1>Product Price</h1></div>';

        print '<div>R<div class="click" id="override_price_replacement_capital" style="display: inline-block">'.number_format($quote2->ReplacementPrice-$quote2->TravelCostsReplacement-$quote2->CrushCostsReplacement-$quote2->LabourCostsReplacement-$quote2->MaterialsCostsReplacement,2).'</div></div><p/>';
    	  print '<div>R<div class="click" id="override_price_additional_capital" style="display: inline-block">'.number_format($quote2->AdditionalPrice-$quote2->TravelCostsAdditional-$quote2->CrushCostsAdditional-$quote2->LabourCostsAdditional-$quote2->MaterialsCostsAdditional,2).'</div></div><p/>';
    	  print '<div><b>R'.number_format($quote2->AllPrice-$quote2->TravelCostsAdditional-$quote2->CrushCostsAdditional-$quote2->LabourCostsAdditional-$quote2->MaterialsCostsAdditional-$quote2->TravelCostsReplacement-$quote2->CrushCostsReplacement-$quote2->LabourCostsReplacement-$quote2->MaterialsCostsReplacement,2).'</b></div><p/>';
        print '</div>';
        print '</div>';

        print '<div class="total_icon">';
        print '<div class="inner_icon">';
        print '<div><h1>Installation</h1></div>';

        print '<div>R'.number_format($quote2->TravelCostsReplacement+$quote2->CrushCostsReplacement+$quote2->LabourCostsReplacement+$quote2->MaterialsCostsReplacement,2).'</div><p/>';
    	  print '<div>R'.number_format($quote2->TravelCostsAdditional+$quote2->CrushCostsAdditional+$quote2->LabourCostsAdditional+$quote2->MaterialsCostsAdditional,2).'</div><p/>';
    	  print '<div><b>R'.number_format($quote2->TravelCostsAdditional+$quote2->CrushCostsAdditional+$quote2->LabourCostsAdditional+$quote2->MaterialsCostsAdditional+$quote2->TravelCostsReplacement+$quote2->CrushCostsReplacement+$quote2->LabourCostsReplacement+$quote2->MaterialsCostsReplacement,2).'</b></div><p/>';
        print '</div>';
        print '</div>';

        print '<div class="total_icon">';
        print '<div class="inner_icon">';
        print '<div><h1>Total Price</h1></div>';

        print '<div>R'.number_format($quote2->ReplacementPrice,2).'</div><p/>';
    	  print '<div>R'.number_format($quote2->AdditionalPrice,2).'</div><p/>';
    	  print '<div>R'.number_format($quote2->AllPrice,2).'</div><p/>';
        print '</div>';
        print '</div>';
    } else {//default view

        $sql = "SELECT `override_rental_capital`,`override_rental_maintenance`,`override_additional_capital`,`override_additional_maintenance`";
        $sql.= " FROM `quote_extras`";
        $sql.= " WHERE `quote_id` = '".mysqli_real_escape_string($GLOBALS["link"],$quoteid)."' LIMIT 1";
        $sqlres = mysqli_query($GLOBALS["link"],$sql);
        $rental_capital=0;
        $rental_maintenance=0;
        $additional_capital=0;
        $additional_maintenance=0;
        while($row = mysqli_fetch_assoc($sqlres)) {
            $rental_capital = $row['override_rental_capital'];
            $rental_maintenance = $row['override_rental_maintenance'];
            $additional_capital = $row['override_additional_capital'];
            $additional_maintenance = $row['override_additional_maintenance'];
        }
      	print '<div class="total_icon">';
          print '<div class="inner_icon">';
          print '<div><h1>Capital</h1></div>';
          if($rental_capital ==0){
                print '<div  class="click" id="override_rental_capital" style="display: inline">R'.sprintf("%.2f",$quote2->getRentalCapital("replacement",$quote2->RentalTerm)).'</div><p/>';
          }else{
                print '<div  class="click" id="override_rental_capital" style="display: inline">R'.number_format($rental_capital,2).'</div><p/>';
          }
          if($additional_capital ==0){
                print '<div class="click" id="override_additional_capital" style="display: inline">R'.sprintf("%.2f",$quote2->getRentalCapital("additional",$quote2->RentalTerm)).'</div><p/>';
          }else{
                print '<div class="click" id="override_additional_capital" style="display: inline">R'.number_format($additional_capital,2).'</div><p/>';
          }
      	
          if($additional_capital == 0 && $rental_capital ==0){
              print '<div><b>R'.sprintf("%.2f",$quote2->getRentalCapital("all",$quote2->RentalTerm)).'</b></div><p/>'; 
          }else{
              $total= $additional_capital + $rental_capital;
              $total=number_format($total,2);
              print '<div><b>R'. $total.'</b></div><p/>';
          }
          print '</div>';
          print '</div>';

      	print '<div class="total_icon">';
          print '<div class="inner_icon">';
          print '<div><h1>Maintenance</h1></div>';
          if($rental_maintenance ==0){
              print '<div class="click" id="override_rental_maintenance" style="display: inline">R'.sprintf("%.2f",$quote2->getRentalMaintenance("replacement",$quote2->RentalTerm, $quote2->MaintenancePercentage)).'</div><p/>';
          }else{
              print '<div class="click" id="override_rental_maintenance" style="display: inline">R'.number_format($rental_maintenance,2).'</div><p/>';
          }

          if($additional_maintenance ==0){
              print '<div class="click" id="override_additional_maintenance" style="display: inline">R'.sprintf("%.2f",$quote2->getRentalMaintenance("additional",$quote2->RentalTerm, $quote2->MaintenancePercentage)).'</div><p/>';
          }else{
              print '<div class="click" id="override_additional_maintenance" style="display: inline">R'.number_format($additional_maintenance,2).'</div><p/>';
          }

          if($rental_maintenance == 0 && $additional_maintenance ==0){
              print '<div><b>R'.sprintf("%.2f",$quote2->getRentalMaintenance("all",$quote2->RentalTerm, $quote2->MaintenancePercentage)).'</b></div><p/>';
          }else{
              $total= $rental_maintenance + $additional_maintenance;
              $total=number_format($total,2);
              print '<div><b>R'. $total.'</b></div><p/>';
          }
          print '</div>';
          print '</div>';

      	print '<div class="total_icon">';
          print '<div class="inner_icon">';
          print '<div><h1>Total</h1></div>';
          if($rental_maintenance == 0 && $rental_capital ==0){
             print '<div>R'.sprintf("%.2f",$quote2->getRentalAmount("replacement",$quote2->RentalTerm, $quote2->MaintenancePercentage)).'</div><p/>';
      	   print '<div>R'.sprintf("%.2f",$quote2->getRentalAmount("additional",$quote2->RentalTerm, $quote2->MaintenancePercentage)).'</div><p/>';
      	   print '<div><b>R'.sprintf("%.2f",$quote2->getRentalAmount("all",$quote2->RentalTerm, $quote2->MaintenancePercentage)).'</b></div><p/>';
          }else{
             $total_rental = $rental_maintenance + $rental_capital;
             $total_rental = number_format($total_rental,2);

             $total_addtional = $additional_maintenance + $additional_capital;
             $total_addtional = number_format($total_addtional,2);

             $total= $total_rental + $total_addtional;
             $total=number_format($total,2);
             print '<div>R'. $total_rental.'</div><p/>';
             print '<div>R'.$total_addtional.'</div><p/>';
             print '<div><b>R'.$total.'</b></div><p/>';
          }
          print '</div>';
          print '</div>';
    }
    print '</div>';

    if ($quote2->RentalTerm==0){
        print '<h2 class="icon_heading">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Replacement Items Overview</h1>';
    }else {
        print '<h2 class="icon_heading">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Contract Overview</h1>';
    }
    print '<div id="overview">';
    if ($quote2->RentalTerm==0){
        print '<div class="total_icon"><div class="inner_icon"><img src="images/calendar_icon2.png" />';
        print '<div class="icon_description">Payback Period</div>';
        print '<h2>'.$quote2->TotalPaybackPeriodFormatted.'</h2></div></div>';
        print '<div class="total_icon"><div class="inner_icon"><img src="images/plus_icon2.png" />';
        print '<div class="icon_description">Monthly Savings</div>';
        print '</div><h2>R'.number_format($quote2->MonthlyCostSaving, 2).'</h2></div>';
    }
    if ($quote2->RentalTerm==0){
        print '<div class="total_icon"><div class="inner_icon"><img src="images/percentage_icon2.png" />';
        print '<div class="icon_description">Percentage Saved</div>';
        print '<h2>'.number_format($quote2->KWhSavedPerc, 0).'%</h2></div></div>';

        print '<div class="total_icon"><div class="inner_icon"><img src="images/monthly_payment_icon.png" />';
        print '<div class="icon_description">Savings after '.$ttl_savings_time.' years</div>';
        print '<h2>'.$ttl_savings.'</h2></div></div>';
        print '<div class="total_icon"><div class="inner_icon"><img src="images/tick_icon2.png" />';
        print '<div class="icon_description">Cost</div>';
        print '<h2>R<div class="click" id="override_price_replacement" style="display: inline">'.number_format($quote2->ReplacementPrice, 2).'</div></h2></div></div>';
    } else {
        print '<div class="total_icon"><div class="inner_icon"><img src="images/calendar_icon2.png" />';
        print '<div class="icon_description">Rental Term</div>';
        print '<h2><div class="click" id="override_deposit_additional" style="display: inline">'.number_format($quote2->RentalTerm, 0).'</div> Months</h2></div></div>';

        print '<div class="total_icon"><div class="inner_icon"><img src="images/deposit_icon.png" />';
        print '<div class="icon_description">Deposit</div>';
        print '<h2>R'.number_format($quote->getDeposit("all"), 2).' (<div class="click" id="override_deposit_replacement" style="display: inline">'.$quote->getDepositProportion().'</div>%)</h2></div></div>';
        print '<div class="total_icon"><div class="inner_icon"><img src="images/monthly_payment_icon.png" />';

        print '<div class="icon_description">Monthly Rental by year</div>';//default view
        print '<h2>';
    	$termyears = ceil($quote2->RentalTerm/12);
    	print '1: R'.sprintf("%.2f",$quote2->getRentalAmount("all",$quote2->RentalTerm, $quote2->MaintenancePercentage)).'<br/>';
    	for ($terms = 1; $terms<$termyears; $terms++){
    		print ($terms+1).': R'.number_format(($quote2->getRentInMonth("all",$quote2->RentalTerm,$terms*12,$quote2->MaintenancePercentage)), 2).'<br/>';
    	}
    	print '</h2></div></div>';
        print '<div class="total_icon"><div class="inner_icon"><img src="images/percentage_icon2.png" />';
        print '<div class="icon_description">Return</div>';
        print '<h2><div class="click" id="override_target_return" style="display: inline">'.number_format((pow(1+$quote2->getReturn($quote2->RentalTerm, $quote2->MaintenancePercentage),12)-1)*100, 2).'</div>%</h2></div></div>';
    }
    print '</div>';
    print "<img  id ='product_divider'  src='images/horiz_divider.png'/>";
}
/*--------ADDITIONAL CHARGES------*/
if ($quote->TravelCostsReplacement!=-1){
    print '<div class="title_heading"><div class="inner_heading" onclick="toggle_div(this,\'addcharges_cont\',\'addcharge_icon\');"><span class="title_span"><img id="addcharge_icon" src="images/prodmenu_minus_icon.png"/><span><h1 class="prod_heading">&nbsp;Additional Charges</h1></div></div>';

    print '<div id="addcharges_cont" class="content_w" style="display:block">';
    print '<div class="content">';
    print '<div id="product-details">';
    print '<table>';
    print "<tr>";
    print '<td rowspan="6"> <img src="images/product_icon.png" width="100px" style="float:left;  padding-right:40px;"/></td>';
    print '<td> <table> <tr>';
    print "<td colspan='3' id='item_number'>The following additional charges are included in your quote</td>";
    print '</tr>';

    if ($quote->TravelCostsReplacement!=-1){
        print '<tr>';
        print '<td align="center" valign="middle"></td>';
        print '<td align="left" class="heading"></td>';
        print '<td align="left" class="prod_vals long_description" valign="middle"><b>Replacement Items</b></td>';
    	  print '<td align="left" class="prod_vals long_description" valign="middle"><b>Additional Items</b></td>';
        print '</tr>';

        print '<tr>';
        print '<td align="center" valign="middle"><img src="images/ellies_icon.png" /></td>';
        print '<td align="left" class="heading">Travel Costs:</td>';
        print '<td align="left" class="prod_vals long_description" valign="middle">R<div class="click" id="override_travel_replacement" style="display: inline">'.number_format($quote->TravelCostsReplacement, 2).'</div></td>';
    	  print '<td align="left" class="prod_vals long_description" valign="middle">R<div class="click" id="override_travel_additional" style="display: inline">'.number_format($quote->TravelCostsAdditional, 2).'</div></td>';
        print '</tr>';
        print '<tr>';
        print '<td align="center" valign="middle"><img src="images/ellies_icon.png" /></td>';
        print '<td align="left" class="heading">Labour Costs:</td>';
        print '<td align="left" class="prod_vals long_description" valign="middle">R<div class="click" id="override_labour_replacement" style="display: inline">'.number_format($quote->LabourCostsReplacement, 2).'</div></td>';
        print '<td align="left" class="prod_vals long_description" valign="middle">R<div class="click" id="override_labour_additional" style="display: inline">'.number_format($quote->LabourCostsAdditional, 2).'</div></td>';
        print '</tr>';
        print '<tr>';
        print '<td align="center" valign="middle"><img src="images/ellies_icon.png" /></td>';
        print '<td align="left" class="heading">Crush Costs:</td>';
        print '<td align="left" class="prod_vals long_description" valign="middle">R<div class="click" id="override_crush_replacement" style="display: inline">'.number_format($quote->CrushCostsReplacement, 2).'</div></td>';
        print '<td align="left" class="prod_vals long_description" valign="middle">R<div class="click" id="override_crush_additional" style="display: inline">'.number_format($quote->CrushCostsAdditional, 2).'</div></td>';
        print '</tr>';
        print '<tr>';
        print '<td align="center" valign="middle"><img src="images/ellies_icon.png" /></td>';
        print '<td align="left" class="heading">Materials Costs:</td>';
        print '<td align="left" class="prod_vals long_description" valign="middle">R<div class="click" id="override_materials_replacement" style="display: inline">'.number_format($quote->MaterialsCostsReplacement, 2).'</div></td>';
        print '<td align="left" class="prod_vals long_description" valign="middle">R<div class="click" id="override_materials_additional" style="display: inline">'.number_format($quote->MaterialsCostsAdditional, 2).'</div></td>';
        print '</tr>';
    }
    print '</table></td></tr>';
    print '</table>';
    print '</div>';
    print '</div>';
    print '</div>';

    $sql = "SELECT `quote_items`.`id`";
    $sql.= " FROM `quote_items`";
    $sql.= " WHERE `quote_items`.`quote_id` = '".mysqli_real_escape_string($GLOBALS["link"],$quoteid)."' ORDER BY `id`";

    /*--------Selected Products-----------------*/
    print '<div id="product_menu">';
    print '<div class="title_heading"><div class="inner_heading" onclick="toggle_div(this,\'product_cont\', \'prod_icon\');"><span class="title_span"><img id="prod_icon" src="images/prodmenu_plus_icon.png"/><span><h1 class="prod_heading">Chosen Products</h1></div></div>';
    print '<div id="product_cont" class="content_w"> ';
    print '<div class="content">';
    print "<div id='product-details'>";
    $totalprice=$quote->AllPrice;
    print '<table>';$totalprice = 0;
    $sqlres = mysqli_query($GLOBALS["link"],$sql);
    $itemcount = 0;
    $totalqty = 0;

    $totalkwhexisting = 0;
    $totalcostexisting = 0;
    $totalkwhreplacement = 0;
    $totalcostreplacement = 0;

    while($row = mysqli_fetch_assoc($sqlres)) {
        $quoteitem = new QuoteItem($row["id"], $quote);
        $itemcount += 1;

        print "<tr>";
        print '<td rowspan="6"> <img src="images/product_icon.png" width="100px" style="float:left;  padding-right:40px;"/></td>';

        print "<td colspan='3' id='item_number'>Product No. ".$itemcount."</td>";
        print '</tr>';
    	  $qty = $quoteitem->GetInputValues();
        $quoteitem->CalculateCostSavings();

        $qty_key = "";
        $qty_val  = "";
        if(is_array($qty)){
            $qty_val = reset($qty); // First Element's Value
            $qty_key = key($qty); // First Element's Key
            $qty_key = str_replace("_", " ", $qty_key);
            
        }
        if ($qty_key != "Hours Per Day"){
            $qty_key = "Average Shower Time";
            $qty_val = $qty["Average_Shower_Time"]." min";
        }

        if(trim($qty_val) =="min")$qty_val ="0 min";

        if ($quoteitem->Qty>0){
            print '<tr>';
            print '<td align="center" valign="middle"><img src="images/ellies_icon.png" /></td>';
            print '<td align="left" class="heading">Technology Type:</td>';
            print '<td align="left" class="prod_vals long_description" valign="middle">'.$quoteitem->TechType.'</td>';

            print '<td align="center" valign="middle"><img src= "images/clock_thumb.png" /></td>';
            print '<td align="left" class="heading">'.$qty_key.':</td>';
            print '<td align="left" class="prod_vals" valign="middle">'.$qty_val.'</td>';
            print '</tr>';

            print '<tr>';
            print '<td align="center" valign="middle"><img src="images/ellies_icon.png" /></td>';
            print '<td align="left" class="heading">Existing Product:</td>';
            print '<td align="left" class="prod_vals long_description" valign="middle">'.$quoteitem->OldProductName.'</td>';
            print '<td align="center" valign="middle"><img src="images/calendar_thumb.png" /></td>';
            print '<td align="left" class="heading">Payback Period:</td>';
            print '<td align="left" class="prod_vals" valign="middle">'.$quoteitem->PaybackPeriodFormatted.'</td>';
            print '</tr>';
        	
            print '<tr>';
            print '<td align="center" valign="middle"><img src="images/ellies_icon.png" /></td>';
            print '<td align="left" class="heading">Replacement Product:</td>';
            print '<td align="left" class="prod_vals long_description" valign="middle">'.$quoteitem->ItemDescription.'</td>';
            print '<td align="center" valign="middle"><img src="images/plus_thumb.png" /></td>';
            print '<td align="left" class="heading">Monthly Savings:</td>';
            print '<td align="left" class="prod_vals" valign="middle">R'.number_format($quoteitem->MonthlyCostSaving, 2).'</td>';
            print '</tr>';
            print '<tr>';
            print '<td align="center" valign="middle"><img src="images/ellies_icon.png" /></td>';
            print '<td align="left" class="heading">Quantity</td>';
            print '<td align="left" class="prod_vals long_description" valign="middle">Existing:'.$quoteitem->Qty.' / Replacement:'.$quoteitem->NewQty.'</td>';
            print '<td align="center" valign="middle"><img src="images/percentage_thumb.png" /></td>';
            print '<td align="left" class="heading">Percentage Saved:</td>';
            print '<td align="left" class="prod_vals" valign="middle">'.number_format($quoteitem->KWhSaved, 2).'KWh ('.number_format($quoteitem->KWhSavedPerc, 0).'%)</td>';
            print '</tr>';
            print '<tr>';
            print '<td align="center" valign="middle"><img src="images/ellies_icon.png" /></td>';
            print '<td align="left" class="heading">Room:</td>';
            print '<td align="left" class="prod_vals long_description" valign="middle">'.str_replace("^"," - ",$quoteitem->Room).'</td>';
            print '<td align="center" valign="middle"><img src="images/tick_thumb.png" /></td>';
            print '<td align="left" class="heading">Replacement Price:</td>';
            print '<td align="left" class="prod_vals" valign="middle">R'.number_format($quoteitem->ItemTotal, 2).'</td>';
            print '</tr>';
        } else {
            print '<tr>';
            print '<td align="center" valign="middle"><img src="images/ellies_icon.png" /></td>';
            print '<td align="left" class="heading">Technology Type:</td>';
            print '<td align="left" class="prod_vals long_description" valign="middle">'.$quoteitem->TechType.'</td>';
            print '</tr>';

            print '<tr>';
            print '<td align="center" valign="middle"><img src="images/ellies_icon.png" /></td>';
            print '<td align="left" class="heading">Replacement Price:</td>';
            print '<td align="left" class="prod_vals" valign="middle">R'.number_format($quoteitem->ItemTotal, 2).'</td>';
            print '</tr>';
        	
            print '<tr>';
            print '<td align="center" valign="middle"><img src="images/ellies_icon.png" /></td>';
            print '<td align="left" class="heading">Replacement Product:</td>';
            print '<td align="left" class="prod_vals long_description" valign="middle">'.$quoteitem->ItemDescription.'</td>';
          
            print '</tr>';
            print '<tr>';
            print '<td align="center" valign="middle"><img src="images/ellies_icon.png" /></td>';
            print '<td align="left" class="heading">Quantity</td>';
            print '<td align="left" class="prod_vals long_description" valign="middle">Existing:'.$quoteitem->Qty.' / Replacement:'.$quoteitem->NewQty.'</td>';
            print '</tr>';
            print '<tr>';
            print '<td align="center" valign="middle"><img src="images/ellies_icon.png" /></td>';
            print '<td align="left" class="heading">Room:</td>';
            print '<td align="left" class="prod_vals long_description" valign="middle">'.str_replace("^"," - ",$quoteitem->Room).'</td>';

            print '</tr>';
        }

        $totalqty += $quoteitem->NewQty;
    }
    print '</table>';
    print "</div>";
    print '</div>';
    print '</div>';
    print '</div>';
}
print "<img  id ='product_divider'  src='images/horiz_divider.png'/>";
?>
    <script>
        function toggle_div(obj, targid, targimg)
        {
            $contentWrapper = $(obj);
            var divToShow = document.getElementById(targid);
            var imgIcon = document.getElementById(targimg);
            if(divToShow.style.display == 'block'){
                $(imgIcon).attr('src','images/prodmenu_plus_icon.png');
                divToShow.style.display = 'none';

            }else{
                $(imgIcon).attr('src','images/prodmenu_minus_icon.png');
                divToShow.style.display = 'block';
                $('html, body').animate({ scrollTop: $contentWrapper.offset().top }, 'slow');

            }
        }

        //Technical Product Information
        $('#technicalinfo').hide();
        $( "a#viewtechnicalinfo" ).click(function() {
            $('#technicalinfo').toggle();
        });

        //Document plans
        $('#floorplans').hide();
        $('div#showfloorplansetup').hide();
        $( "a#addFloorplans" ).click(function() {
            //$('#floorplans').toggle();
            $('div#showfloorplansetup').toggle();
        });

        $('select#floorplan_user').change(function () {
            var id = $(floorplan_user).children(':selected').attr('id'); 
            $('input#doc_category').val(id);            
        });

        $('select#rental_option').change(function () {
            var id = jQuery('input#quoteID').val();
            var option = $(rental_option).children(':selected').attr('id'); 
            alert(option);
            var user_option = 0;
            if(option == "Yes"){
                user_option = 0;
            }else if(option == "No"){
                user_option = 1;
            }
            
            $.ajax({
             type:"POST",
             url:"editquotecomm.php",
             data: { quote_id: id, rental_option: user_option },
             succes :function(resp) {
                // output response
                $('.ui-loader ui-corner-all ui-body-a ui-loader-default').hide();
             }
            });           
        });

        $('#savefloorplan').click(function() {  
           // serialize data from form
           var id  = $("input#quote_id_floor").val();
           var data = new FormData($('form#formfloor')[0]);
            $.each($('form#formfloor #floorfile')[0].files, function(i, file) {
              data.append('file[]', file);
            });

           $.ajax({
             type:"POST",
             //dataType: "json",
             url:"editquotecomm.php?id="+id,
             //url:"uploaddocuments.php?id="+id,
             data: data,
             processData: false,
             contentType: false,
             succes :function(data) {
              // $(that).parent().find("#goodmessage").text("Successfully uploaded!");
              //$("label#goodmessage").text("Successfully uploaded!");
               // $(that).parent().find("#badmessage").text("Could not upload.");
            ///$("label#badmessage").text("Could not upload."); 
             },
             fail :function(data) {
              
             }
           });
        }); 
    </script>

<?php 
print "</div>";


print "<div class=\"ui-popup-container ui-popup-hidden\" id=\"popupLogin-popup\"><div data-role=\"popup\" id=\"popupLogin\" data-theme=\"a\" class=\"ui-corner-all ui-popup ui-body-a ui-overlay-shadow\" aria-disabled=\"false\" data-disabled=\"false\" data-shadow=\"true\" data-corners=\"true\" data-transition=\"none\" data-position-to=\"origin\" data-dismissible=\"true\">
<form action = \"editquotecomm.php?step=discount&id=".$quote->QuoteID."\" data-ajax=\"false\">
                <input type=\"hidden\" name=\"id\" value=\"$quote->QuoteID\">
                <input type=\"hidden\" name=\"step\" value=\"discount\">
				<div style=\"padding:10px 20px;\">
				  <h3>Please enter discount percentage (0-100)</h3>
		          <label for=\"un\" class=\"ui-hidden-accessible ui-input-text\">0</label>
		          <div class=\"ui-input-text ui-shadow-inset ui-corner-all ui-btn-shadow ui-body-a\"><input type=\"text\" name=\"discount\" id=\"discount\" value=\"$quote->Discount\" placeholder=\"0\" data-theme=\"a\" class=\"ui-input-text ui-body-a\"></div>
		    	  <button type=\"submit\" data-theme=\"b\" class=\"ui-btn-hidden\" data-disabled=\"false\">Set Discount</button>
				</div>
				</form>
		</div></div>";
require_once("inc/footer.php");
?>