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
ini_set('display_errors', 'On');
error_reporting(E_ALL);
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

$quoteid = $_GET["id"];
$requested = false;

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
        //header("location: editquote.php?id=".$quoteid);
        //exit();
    }
}
//kwhprice:1.2
//elecesc:8
//property:asdfasdfasdf sadfasdf

//==========================================================================
$booktitle = "Mail Sent";
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
//====================================================================WEB PAGE===================================================================================================================================================
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
print '<td align="left" class="heading">Date:</td>';
print '<td align="left" class="quote_vals" valign="middle">'.$quote->DateCreated.'</td>';
print '<td align="left" class="customer_icon" valign="middle"><img src="images/property_icon.png" /></td>';
$isInstall = ($quote->DoInstall==1)?"Includes Installation":"Excludes Installation";
print '<td align="left" class="customer_vals" valign="middle"><div class="clickselect" id="do_install" style="display: inline">'.$isInstall.'</td>';
print '</tr>';
print '</table>';
print "</div>";
//print "<input type='button' value='Update Values' >";
print '<br/><br/>';
//print "<a href='selectproduct.php?id=0&quoteitemid=0&quoteid=".$quoteid."' data-role='button' data-rel='dialog' data-close-btn-text='close' data-inline='true' data-icon='plus' data-theme='a'>Add Item</a>";
//print '<a data-role="button" id="btneditquote" data-icon="grid" data-iconpos="left" class="ui-btn-left" data-inline="true" data-theme="a" href="#page1" onclick="ShowQuoteValues()">Edit Values</a>';
print "<br><br>";
$quote->CalculateCostSavingTotals();
//$qtyears = $quote->getYears();
$savings = $quote->Get5YearSavings();
$term_savings=($quote->Get5Savings());
$ttl_savings = end($savings["Cumulative Savings"]);
$ttl_savings_time = end(array_keys($savings["Price per kWh"]));

print "<h1 data-role='label' data-theme='a' data-transition='fade' data-inline='true' data-icon='search' data-ajax='false' style='color:red'>Quote has been sent for approval</h1>";
//======================================================================================================================================================================================================================
print "<div class=\"ui-popup-container ui-popup-hidden\" id=\"popupLogin-popup\"><div data-role=\"popup\" id=\"popupLogin\" data-theme=\"a\" class=\"ui-corner-all ui-popup ui-body-a ui-overlay-shadow\" aria-disabled=\"false\" data-disabled=\"false\" data-shadow=\"true\" data-corners=\"true\" data-transition=\"none\" data-position-to=\"origin\" data-dismissible=\"true\">
<form action = \"editquote.php?step=discount&id=".$quote->QuoteID."\" data-ajax=\"false\">
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