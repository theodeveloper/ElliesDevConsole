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
    header("location: emailquote.php?id=".$_GET["id"]."&complete=1");
    exit();
}

//Rejected
if (GET("step") == "reject" && GET("id") > 0) {
    if (!$sysuser->hasPermission("complete_quotes")) {
        header("location: index.php?nopermission=Complete%20quotes");
        exit();
    } 
    $quote = new Quote($_GET["id"]);
    $customerid = $quote->CustomerID;
    $quote->Approved = -1;
    $quote->UpdatedBy = $sysuser->id;
    $quote->Save();
    //DebugPrint($quote);
    echo "<script>window.close();</script>";
    //header("location: emailquotecomm.php?id=".$_GET["id"]."&complete=1");
    exit();
}

//Approved
if (GET("step") == "approved" && GET("id") > 0) {
    if (!$sysuser->hasPermission("complete_quotes")) {
        header("location: index.php?nopermission=Complete%20quotes");
        exit();
    }

    $quote = new Quote($_GET["id"]);
    $customerid = $quote->CustomerID;
    $quote->Approved = 1;
    $quote->UpdatedBy = $sysuser->id;
    $quote->Save();
    //DebugPrint($quote);
    echo "<script>window.alert('Quote approved');</script>";
   // echo "<script>window.close();</script>";
    //header("location: emailquote.php?id=".$_GET["id"]."&complete=1");
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
    header("location: editquote.php?id=".$_GET["id"]);
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
//====================================================================WEBPAGE==================================================================================
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

print "><form method='Post' action='editquote.php?id=".$quoteid."'>";
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
//Status
if ($quote->Approved  == -1 ){
    print "<h1 data-role='label' data-theme='a' data-transition='fade' data-inline='true' data-icon='search' data-ajax='false' style='color:red'>Quote has been rejected</h1>";
}elseif ($quote->Approved  == 1 ){      
    print "<h1 data-role='label' data-theme='a' data-transition='fade' data-inline='true' data-icon='search' data-ajax='false' style='color:green'>Quote has been approved</h1>";
}

//Approved buttons
if ($quote->Approved != -1 ){
    print "<a href='emailquote.php?id=".$quoteid."&preview=1' data-role='button' data-theme='a' data-transition='fade' data-inline='true' data-icon='search' data-ajax='false' style='float:right'>Preview / Print Quote</a>";

    if ($quote->NumberOfItems > 0) {
        if ($customer->Email != "") {
            if (strpos($customer->Email, ".") > 0 && strpos($customer->Email, "@") > 0) {
                print "<a href='emailquote.php?id=".$quoteid."' data-role='button' data-theme='a' data-transition='fade' data-inline='true' data-icon='info' data-ajax='false'style='float:right'>Email Quote</a>";
            }
        }
    }
    if ($quote->Approved != 1 ){
        print "<a href=\"editquote.php?step=reject&id=".$quote->QuoteID."\" onclick=\"javascript:rejectQuote()\" data-role='button' data-theme='a' data-transition='fade' data-inline='true' data-icon='info' data-ajax='false'>Reject Quote</a>";
        print "<a href=\"editquote.php?step=approved&id=".$quote->QuoteID."\"  data-role='button' data-theme='a' data-transition='fade' data-inline='true' data-icon='info' data-ajax='false'>Approve Quote</a>";
    }
}

//View Images
print "<a href=\"viewimages.php?id=".$quote->QuoteID."\"  data-role='button' data-theme='a' data-transition='fade' data-inline='true' data-icon='info' data-ajax='false' style='float:right'>View Images</a>";


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
//print '<td align="left" class="customer_icon" valign="middle"><img src="images/property_icon.png" /></td>';
//$isInstall = ($quote->DoInstall==1)?"Includes Installation":"Excludes Installation";
//print '<td align="left" class="customer_vals" valign="middle"><div class="clickselect" id="do_install" style="display: inline">'.$isInstall.'</td>';
print '</tr>';
print '</table>';
print "</div>";
print '<br/><br/>';
print "<br><br>";
$quote->CalculateCostSavingTotals();
$savings = $quote->Get5YearSavings();
$term_savings=($quote->Get5Savings());
$ttl_savings = end($savings["Cumulative Savings"]);
$array_keys =array_keys($savings["Price per kWh"]);
$ttl_savings_time = end($array_keys);


if ($quote->RentalTerm==0){
    print '<h1 class="icon_heading">Cost Overview</h1>';
}else {
    print '<h1 class="icon_heading">Contract Overview</h1>';
}

print '<div id="overview">';
print '<div class="total_icon"><div class="inner_icon"><img src="images/calendar_icon2.png" />';
print '<div class="icon_description">Payback Period</div>';
print '<h2>'.$quote->TotalPaybackPeriodFormatted.'</h2></div></div>';
print '<div class="total_icon"><div class="inner_icon"><img src="images/plus_icon2.png" />';
print '<div class="icon_description">Monthly Saved</div>';
print '</div><h2>R'.number_format($quote->MonthlyCostSaving, 2).'</h2></div>';

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
	print '1: R'.number_format($quote->getRentalAmount("all",$quote->RentalTerm, $quote->MaintenancePercentage), 2).'<br/>';
	for ($terms = 1; $terms<$termyears; $terms++){
		print ($terms+1).': R'.number_format(($quote->getRentInMonth("all",$quote->RentalTerm,$terms*12,$quote->MaintenancePercentage)), 2).'<br/>';
	}
	print '</h2></div></div>';
    print '<div class="total_icon"><div class="inner_icon"><img src="images/percentage_icon2.png" />';
    print '<div class="icon_description">Return</div>';
    print '<h2><div class="click" id="target_return" style="display: inline">'.number_format((pow(1+$quote->getReturn($quote->RentalTerm, $quote->MaintenancePercentage),12)-1)*100, 2).'</div>%</h2></div></div>';
}
print '</div>';
/*--------Total Savings------*/
$irow = 0;
$keyYear = array_keys($savings["Price per kWh"]);
$headings = array_keys($savings);

/*get last year number */
$yearNumber = end($keyYear);

print '<div id="savings_menu">';
print '<div class="title_heading"><div class="inner_heading" onclick="toggle_div(this,\'savings_cont\',\'savings_icon\');"><span class="title_span"><img id="savings_icon" src="images/prodmenu_plus_icon.png"/><span><h1 class="prod_heading">Projected Savings</h1></div></div>';

print '<div id="savings_cont" class="content_w"> ';
print '<div class="content"> ';
print '<div id="savings">';
print '<div class="savings_icon">';
print '<div class="inner_icon">';
print '<div class="numberCircle"><h1>Year</h1></div>';
print '<p>Price per kWh</p>';
print '<p>Cumulative Savings</p>';
print '<p>Annual Savings</p>';
print '<p>Monthly Savings</p>';
print '</div>';
print '</div>';

for ($i = 0; $i < 5; $i ++)
{
    print '<div class="savings_icon">';
    print '<div class="inner_icon">';
    print '<div class="numberCircle" id = "savings_year'.($i+1).'"><h1>'.$keyYear[$i].'</h1></div>';

    foreach($headings as $key)
    {
        $curYear = $keyYear[$i];
        $val = $savings[$key][$curYear];
        print '<p>'.$val.'</p>';
    }
    print '</div>';
    print '</div>';
}
print'</div>';
print '</div>';
print '</div>';
print '</div>';
/*-------Total Losses------*/
print '<div id="losses_menu">';
print '<div class="title_heading"><div class="inner_heading" onclick="toggle_div(this,\'losses_cont\',\'losses_icon\');"><span class="title_span"><img id="losses_icon" src="images/prodmenu_plus_icon.png"/><span><h1 class="prod_heading">Projected Losses</h1></div><div id="losses_condition">if Ellies Customised Solution for you is not chosen</div></div>';
print '<div id="losses_cont" class="content_w"> ';
print '<div class="content"> ';
print '<div id="losses">';
print '<div class="losses_icon">';
print '<div class="inner_icon">';
print '<div class="numberCircle"><h1>Year</h1></div>';
print '<p>Price per kWh</p>';
print '<p>Cumulative losses</p>';
print '<p>Annual losses</p>';
print '<p>Monthly losses</p>';
print '</div>';
print '</div>';
for ($i = 0; $i < 5; $i ++)
{
    print '<div class="losses_icon">';
    print '<div class="inner_icon">';
    print '<div class="numberCircle" id = "losses_year'.($i+1).'"><h1>'.$keyYear[$i].'</h1></div>';

    foreach($headings as $key)
    {
        $curYear = $keyYear[$i];
        $val = $savings[$key][$curYear];
        print '<p>'.$val.'</p>';
    }
    print '</div>';
    print '</div>';
}
print '</div>';
print '</div>';
print '</div>';
print '</div>';
$sql = "SELECT `quote_items`.`id`";
$sql.= " FROM `quote_items`";
$sql.= " WHERE `quote_items`.`quote_id` = '".mysqli_real_escape_string($GLOBALS["link"],$quoteid)."' ORDER BY `id`";

/*--------Selected Products-----------------*/
print '<div id="product_menu">';
print '<div class="title_heading"><div class="inner_heading" onclick="toggle_div(this,\'product_cont\', \'prod_icon\');"><span class="title_span"><img id="prod_icon" src="images/prodmenu_plus_icon.png"/><span><h1 class="prod_heading">Chosen Products Info</h1></div></div>';
print '<div id="product_cont" class="content_w"> ';
print '<div class="content">';
print "<div id='product-details'>";
print '<table>';
$totalprice = 0;
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
        print '<td align="left" class="heading">Monthly Saved:</td>';
        print '<td align="left" class="prod_vals" valign="middle">R'.number_format($quoteitem->MonthlyCostSaving, 2).'</td>';
        print '</tr>';
        print '<tr>';
        print '<td align="center" valign="middle"><img src="images/ellies_icon.png" /></td>';
        print '<td align="left" class="heading">Quantity</td>';
        print '<td align="left" class="prod_vals long_description" valign="middle">'.$quoteitem->Qty.'</td>';
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
        print '<td align="left" class="prod_vals long_description" valign="middle">'.$quoteitem->Qty.'</td>';
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

//Remove additional charges for retail
/*--------ADDITIONAL CHARGES-----------------*/
/*if ($quote->TravelCostsReplacement!=-1){
  
    print '<div class="title_heading"><div class="inner_heading" onclick="toggle_div(this,\'addcharges_cont\',\'addcharge_icon\');"><span class="title_span"><img id="addcharge_icon" src="images/prodmenu_plus_icon.png"/><span><h1 class="prod_heading">Additional Charges</h1></div></div>';

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
}*/

print "<img  id ='product_divider'  src='images/horiz_divider.png'/>";
?>
    <script>
        function toggle_div(obj, targid, targimg)
        {
            $contentWrapper = $(obj);
            var divToShow = document.getElementById(targid);
            var imgIcon = document.getElementById(targimg);
        		if(divToShow.style.display == 'block')
            {
                $(imgIcon).attr('src','images/prodmenu_plus_icon.png');
                divToShow.style.display = 'none';

            }
            else
            {
                $(imgIcon).attr('src','images/prodmenu_minus_icon.png');
                divToShow.style.display = 'block';
                $('html, body').animate({ scrollTop: $contentWrapper.offset().top }, 'slow');

            }
        }
    </script>
<?php 
print "</div>";

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
?>
