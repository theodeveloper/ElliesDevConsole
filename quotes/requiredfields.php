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
//exit();

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
require_once("../inc/simpleImage.php");
require_once("../inc/quoteimages.class.php");

//LoginCheck("quotes.php");
ini_set('display_errors', 'On');
//error_reporting(E_ALL);
//die("Here");  
get_session();
//echo "RRRR".$_SESSION["tpm_userid"];

if (!isset($_SESSION["userid"])){
  header("location:../login.php");
}

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
if(isset($_POST['quote_id']) && !empty($_POST["quote_id"]))
{
   $quoteid = $_POST['quote_id'];
}else{
   $quoteid = $_GET["id"];
}


if ($quoteid == 0) {
    if (isset($_GET["customerid"])) {
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
          ElectricityEscalationRate...  8
          KWhPrice  1.2
          Property
        */
        $quote->Complete = false;
        $origquote = new Quote($quote->QuoteID);
        $origquote->CalculateCostSavingTotals();
        $quote->Save();

        $quotenew = new Quote($quoteid);
        $quotenew->CalculateCostSavingTotals();
        LogDataChange($sysuser->id, "quotes", "Updated quote ".$quote->QuoteID, $origquote, $quote);
        //header("location: editquote.php?id=".$quoteid);
        //exit();
    }
}
//==========================================================================
$booktitle = "Required Fields for PDF Quote";
//Back
if (GET("step") == "back" && GET("id") > 0) {
//    die("viewing images");
    if (!$sysuser->hasPermission("complete_quotes")) {
        header("location: index.php?nopermission=Complete%20quotes");
        exit();
    }
    header("location: editquote.php?id=".$_GET["id"]);
    exit();
}

if(isset($_POST['quote_id']) && !empty($_POST["quote_id"]))
{
   $_SESSION["quoteID"] =  $_POST['quote_id'];
}else{
   $_SESSION["quoteID"] =  $_GET["id"];
}

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
    header("location: emailquote.php?id=".$_GET["id"]."&complete=1");
    exit();
}

//discount
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
    //DebugPrint($quote);
    header("location: editquotecomm.php?id=".$_GET["id"]);
    exit();
}
//=======================================================================
require_once("inc/header.php");

$quote = new Quote($quoteid);
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
            jQAJAXCall("editquote.php", data)
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
       data   : "{'0':'Excludes Installation','1':'Includes Installation','selected': '<?php  echo $quote->DoInstall; ?>'}", 
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
//====================================================================WEBPAGE===========================================================================================================================================

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
if ($isnew) {

}else{
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
//Back
$sql = "SELECT `type` FROM `channels` WHERE `id`=".$sysuser->retailChannel;
$sqlres = mysqli_query($GLOBALS["link"],$sql);
$row = mysqli_fetch_assoc($sqlres);
$channeltype = $row['type'];
if ($channeltype == "Commercial" || $channeltype =="Franchises"){
    print "<a href=\"editquotecomm.php?step=back&id=".$quote->QuoteID."\"  data-role='button' data-theme='a' data-transition='fade' data-inline='true' data-icon='info' data-ajax='false'>Back</a>";
}else{
  print "<a href=\"editquote.php?step=back&id=".$quote->QuoteID."\"  data-role='button' data-theme='a' data-transition='fade' data-inline='true' data-icon='info' data-ajax='false'>Back</a>";  
}
/*------*/
print "<div id='quote-container'";
if ($isnew) {
    print " style='display:none;'";
}
print ">";

//Customer Details
print '<br/>';
print "<div id='quote-details'>";
    print '<img src="images/details_icon.png" style="float:left; padding-right:100px;"/>';
    print '<table>';
    print '<tr>';
        print '<td align="center" valign="middle"><img src="images/ellies_icon.png" /></td>';
        print '<td align="left" class="heading">Electricity Escalation Rate (%):</td>';
        print '<td align="left" class="quote_vals" valign="middle">'.$quote->ElectricityEscalationRatePercentage.'</td>';
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
        print '<td align="left" class="quote_vals" valign="middle">'.$quote->KWhPrice.'</td>';
        print '<td align="left" class="customer_icon" valign="middle"><img src="images/email_icon.png" /></td>';
        print '<td align="left" class="customer_vals" valign="middle">'.$customer->Email.'</td>';
    print '</tr>';
    print '<tr>';
        print '<td align="center" valign="middle"><img src="images/ellies_icon.png" /></td>';
        print '<td align="left" class="heading">Discount:</td>';
        print '<td align="left" class="quote_vals" valign="middle">'.$quote->Discount.'</td>';
        print '<td align="left" class="customer_icon" valign="middle"><img src="images/property_icon.png" /></td>';
        print '<td align="left" class="customer_vals" valign="middle">'.$quote->Property.'</td>';
    print '</tr>';
    print '<tr>';
        print '<td align="center" valign="middle"><img src="images/ellies_icon.png" /></td>';
        print '<td align="left" class="heading">Date:</td>';
        print '<td align="left" class="quote_vals" valign="middle">'.$quote->DateCreated.'</td>';
        print '<td align="left" class="customer_icon" valign="middle"><img src="images/property_icon.png" /></td>';
        $isInstall = ($quote->DoInstall==1)?"Includes Installation":"Excludes Installation";
        print '<td align="left" class="customer_vals" valign="middle">'.$isInstall.'</td>';
    print '</tr>';
    print '</table>';
print "</div>";
print '<br/><br/>';
print "<br><br>";
//=========================================================================================================================================================
//Add required fields
if(isset($_POST['project_manager']) && !empty($_POST["project_manager"])){
    $quote_id =  $_POST['quote_id']; 
    $project_manager =  $_POST['project_manager']; 
    $installer =  $_POST['installer']; 
    $sales_rep =  $_POST['sales_rep']; 
    $type_connection =  $_POST['type_connection']; 
    $meter_number =  $_POST['meter_number']; 
    $height =  $_POST['ceiling_height']; 
    $contingency =  $_POST['contingency']; 
    $notes =  $_POST['notes']; 
    $last_updated = "NOW()";
    $date_created ="NOW()";

    $QuoteID = "";
    $query = "SELECT `quote_id` FROM `pdf_required_fields` WHERE `quote_id` = '" . $quote_id . "'";
    $result = mysqli_query($GLOBALS["link"],$query);
    while($row = mysqli_fetch_assoc($result)) {  
        $QuoteID = $row['quote_id'];
    } 

    if($QuoteID !== ""){
        $query = "UPDATE `pdf_required_fields` SET";
        $query .= " `quote_id` = '" . $QuoteID . "'";
        $query .= ", `project_manager` = '" . $project_manager . "'";
        $query .= ", `installer` = '" . $installer . "'";
        $query .= ", `sales_rep` = '" . $sales_rep . "'";
        $query .= ", `type_connection` = '" . $type_connection . "'";
        $query .= ", `meter_number` = '" . $meter_number . "'";
        $query .= ", `ceiling_height` = '" . $height . "'";
        $query .= ", `contingency` = '" . $contingency . "'";
        $query .= ", `notes` = '" . $notes . "'";
        $query .= ", `last_updated` = NOW()";
        $query .= " WHERE `quote_id` = '" . $QuoteID . "'";
        $result = mysqli_query($GLOBALS["link"],$query);
        if($result) {
            print "<h2 style='color:green;'>Updated PDF required fields</h2>";

            //Quoted Modified
            $query = "UPDATE `quotes` SET";
            $query .= " `last_updated` = NOW()";
            $query .= " WHERE `quote_id` = '" . $QuoteID . "'";
            $result = mysqli_query($GLOBALS["link"],$query);

            logAction("Edit Required PDF");
        } else {
            print "<h2 style='color:red;'>PDF required fields not Updated</h2>";
        } 
    }else{
        $query  = "INSERT INTO `pdf_required_fields` (quote_id,project_manager,installer,sales_rep,type_connection,meter_number,ceiling_height,contingency,notes,date_created,last_updated) VALUES (";
        $query  .= " '".$quote_id. "',";
        $query  .= " '".$project_manager."',";
        $query  .= " '".$installer."',";
        $query  .= " '".$sales_rep."',";
        $query  .= " '".$type_connection."',";
        $query  .= " '".$meter_number."',";
        $query  .= " '".$height."',";
        $query  .= " '".$contingency."',";
        $query  .= " '".$notes."',";
        $query  .= " ".$date_created. ",";
        $query  .= " ".$last_updated. "";
        $query  .= ") ";
        $result = mysqli_query($GLOBALS["link"],$query);
        if($result) {
            print "<h2 style='color:green;'>Saved PDF required fields</h2>";
            //Quoted Modified
            $query = "UPDATE `quotes` SET";
            $query .= " `last_updated` = NOW()";
            $query .= " WHERE `quote_id` = '" . $QuoteID . "'";
            $result = mysqli_query($GLOBALS["link"],$query);
            logAction("Edit Required PDF");
        } else {
            print "<h2 style='color:red;'>PDF required fields not saved/h2>";
        } 
    }     
}

//Checks if required fields exist
$QuoteID = "";
$project_manager =  ""; 
$installer =  "";
$sales_rep =  ""; 
$type_connection =  "";
$meter_number =  ""; 
$height =  "";
$contingency =  "";
$notes =  "";

$query = "SELECT * FROM `pdf_required_fields` WHERE `quote_id` = '" . $quoteid . "'";
$result = mysqli_query($GLOBALS["link"],$query);
while($row = mysqli_fetch_assoc($result)) {  
    $QuoteID = $row['quote_id'];
    $project_manager =  $row['project_manager']; 
    $installer =  $row['installer']; 
    $sales_rep =  $row['sales_rep']; 
    $type_connection =  $row['type_connection']; 
    $meter_number =  $row['meter_number']; 
    $height =  $row['ceiling_height']; 
    $contingency =  $row['contingency']; 
    $notes =  $row['notes'];

} 
if($project_manager !== ""){
 print "<a href='printquote.php?id=".$quoteid."&preview=1' data-role='button' data-theme='a' data-transition='fade' data-inline='true' data-icon='search' data-ajax='false' style='float:right'>Preview / Print Quote</a>";   
}else{
    print "<h2 style='color:green'>Please enter the following details:</h2>";
}
print '<form id="reqform"  name="reqform" action="" method="POST">
        <h3>Additional Information</h3>
        <input type="hidden" id="quote_id" name="quote_id" value="'.$quoteid.'">
        <table border="0">
            <tr>
                <td>Project Manager:</td>
                <td><input type="text" id="project_manager" name="project_manager" placeholder="Name" value="'.$project_manager. '" required ></td>
            </tr>
             <tr>
                <td>Installer:</td>
                <td><input type="text" id="installer" name="installer" placeholder="Name" value="'.$installer. '"  required></td>
            </tr>
            <tr>
                <td>Sales Rep:</td>
                <td><input type="text" id="sales_rep" name="sales_rep" placeholder="Name" value="'.$sales_rep. '" required></td>
            </tr>
            <tr>
                <td>Type of Connection:</td>
                <td width="50%">';
                print '<table width="100%">';
                if($type_connection == "Single Phase"){
                    print '<tr>     
                            <td width="25%"><input type="radio" id="type_connection" name="type_connection" value="Single Phase" checked></td>
                            <td>Single Phase</td>
                           </tr>
                           <tr>
                            <td><input type="radio" id="type_connection" name="type_connection" value="Three Phase"></td>
                            <td>Three Phase</td>            
                           </tr></table>';
                }else if($type_connection == "Three Phase"){
                    print '<tr>
                            <td width="25%"><input type="radio" id="type_connection" name="type_connection" value="Single Phase"></td>
                            <td>Single Phase:</td>     
                           </tr>
                           <tr>                   
                            <td><input type="radio" id="type_connection" name="type_connection" value="Three Phase" checked></td>
                            <td>Three Phase:</td>
                           </tr></table>';
                }else{
                     print '<tr>
                            <td width="25%"><input type="radio" id="type_connection" name="type_connection" value="Single Phase"></td>
                            <td>Single Phase:</td>   
                           </tr>
                           <tr>
                            <td><input type="radio" id="type_connection" name="type_connection" value="Three Phase"></td>
                            <td>Three Phase:</td>               
                           </tr></table>';
                }
                print '</td></tr>';
print '<tr>
            <td>Meter Number:</td>
            <td><input type="text" id="meter_number" name="meter_number" placeholder="Meter Number" value="'.$meter_number. '" required></td>
        </tr>
        </table>';

print '<h3>Consumption Data and Installation Details</h3>
      <table>
            <tr>
                <td>Ceiling Height:</td>
                <td><input type="text" id="ceiling_height" name="ceiling_height"  placeholder="Height" value="'.$height. '"  required></td>
            </tr>
            <tr>
                <td>Contingency:</td>
                <td><input type="text" id="contingency" name="contingency" placeholder="Contingency"  value="'.$contingency. '" required></td>
            </tr>
      </table>
      <label>Notes:</label>
      <textarea id="notes" name="notes" rows="20" cols="10" placeholder="Notes" >'.$notes. '</textarea>
      <p style="width:10%;float:right;"><input type="submit" id="savepdf" name="savepdf" value="Save"></p>
    </form>  ';

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
 <script>
    $(document).ready(function() {
        // catch submit event for your form
        $('#savepdf').submit(function() {
           // serialize data from form
           var id  = $("input#quote_id").val();
           var data = $('form#reqform').serialize();
           $.ajax({
             type:"POST",
             //dataType: "json",
             url:"requiredfields.php?id="+id,
             data:data,
             succes :function(data) {
                // output response
                //$('.ui-icon ui-icon-loading').hide();
               //$('.ui-loader ui-corner-all ui-body-a ui-loader-default').hide();
                alert(data);
             }
           });
        });
    });
</script>