<?php 
    # This code is written by Tradepage Pty Ltd for Ellies Pty Ltd (the 'parties' mentioned below)
    # The code is provided based on the the terms specified within the agreed NDA between both parties.
    # Both parties have agreed the code is strictly confidential
    # and only by mutal agreement of both parties may the code be exposed to outside parties.
    #
    # Any changes made to the code other than by Tradepage Pty Ltd during the NDA Agreement void support of the code
    #
    /* --------------------------------------------------------------------------
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
require_once("inc/config.php");
require_once("inc/customer.class.php");
require_once("inc/quote.class.php");
require_once("inc/quoteitem.class.php");
require_once("inc/techtype.class.php");
require_once("inc/techitem.class.php");
require_once("inc/product.class.php");
require_once("inc/functions.php");
require_once("../inc/system_user.php");

LoginCheck("quotes.php");

$sysuser = new userType($_SESSION["userid"]);
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
        //header("location: editquote.php?id=".$quoteid);
        //exit();
    }
}
//kwhprice:1.2
//elecesc:8
//property:asdfasdfasdf sadfasdf


$booktitle = "Edit Quote";

if (GET("step") == "complete" && GET("id") > 0) {
    if (!$sysuser->hasPermission("complete_quotes")) {
        header("location: index.php?nopermission=Complete%20quotes");
        exit();
    }
    $quote = new Quote($_GET["id"]);
    $customerid = $quote->CustomerID;
    $quote->Complete = true;
    $quote->UpdatedBy = $sysuser->id;
    $quote->Save();
    //DebugPrint($quote);
    header("location: emailquote.php?id=".$_GET["id"]."&complete=1");
    exit();
}

require_once("inc/_header.php");

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
</script>
<?php 

$reurl = "";
if (!empty($_GET["reurl"])) {
    $_SESSION["reurl"] = $_GET["reurl"];
    $reurl = $_GET["reurl"];
}

$quote = new Quote($quoteid);
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
//PrintTextBox("KWhPrice", "Price/KWh (R)", $quote->KWhPrice);
PrintRange("KWhPrice", "Price/KWh (R)", $quote->KWhPrice, 0.7, 1.5, 0.1);
//PrintTextBox("ElectricityEscalationRatePercentage", "Electricity Escalation Rate (%)", $quote->ElectricityEscalationRatePercentage);
PrintRange("ElectricityEscalationRatePercentage", "Electricity Escalation Rate (%)", $quote->ElectricityEscalationRatePercentage, 7, 20, 1);
PrintTextArea("Property", "Property", $quote->Property, "Enter property details");
print "<input type='hidden' id='step' name='step' value='updatequotevalues'>";
print "<input type='hidden' id='id' name='id' value='".$quoteid."'>";
print "<input type='button' value='Cancel' onclick='UpdateCancel()' data-inline='true'>";
print "<input type='submit' value='Save' data-inline='true' data-ajax='false'>";
print "</form></div>";

/* add buttons to top ------*/
print "<a href='emailquote.php?id=".$quoteid."&preview=1' data-role='button' data-theme='a' data-transition='fade' data-inline='true' data-icon='search' data-ajax='false'>Preview / Print Quote</a>";
if ($quote->NumberOfItems > 0) {
    if ($customer->Email != "") {
        if (strpos($customer->Email, ".") > 0 && strpos($customer->Email, "@") > 0) {
            print "<a href='emailquote.php?id=".$quoteid."' data-role='button' data-theme='a' data-transition='fade' data-inline='true' data-icon='info' data-ajax='false'>Email Quote</a>";
        }
    }
}
    
    if (!$quote->Complete) {
        if ($sysuser->hasPermission("complete_quotes") && $quote->HasItems) {
            print "<a href='editquote.php?id=".$quoteid."&step=complete' data-role='button' data-inline='true' data-icon='check' data-theme='a' data-ajax='false'>Complete Quote</a>";
        }
    }else{
        print "<a href='newquote.php?step=newclick' data-role='button' data-inline='true' data-icon='edit' data-theme='a' data-ajax='false'>New Quote</a>";
    }


/*------*/
print "<div id='quote-container'";
if ($isnew) {
    print " style='display:none;'";    
}else{

}

print ">";



print "<div id='quote-details'>";
print '<img src="images/details_icon.png" style="float:left; padding-right:100px;"/>';
print '    <table>';
print '      <tr>';
print '        <td align="center" valign="middle"><img src="images/ellies_icon.png" /></td>';
print '        <td align="left" class="heading">Electricity Escalation Rate:</td>';
print '        <td align="left" class="quote_vals" valign="middle">'.$quote->ElectricityEscalationRatePercentage.'</td>';
print '        <td align="left" class="customer_icon" valign="middle"><img src="images/name_icon.png" /></td>';
print '        <td align="left" class="customer_vals" valign="middle">'.$customer->Surname.", ".$customer->Name.'</td>';
print '      </tr>';
print '      <tr>';
print '<td align="center" valign="middle"><img src="images/ellies_icon.png" /></td>';        
print '<td align="left" class="heading">Quote Reference:</td>';
print '        <td align="left" class="quote_vals" valign="middle">'.$quote->QuoteReferenceNo.'</td>';
print '      <td align="left" class="customer_icon" valign="middle"><img src="images/phone_icon.png" /></td>';
print '       <td align="left" class="customer_vals" valign="middle">'.$customer->CellPhone.'</td>';
print '      </tr>';
print '      <tr>';
print '<td align="center" valign="middle"><img src="images/ellies_icon.png" /></td>';        
print '<td align="left" class="heading">Price/kWh R:</td>';
print '        <td align="left" class="quote_vals" valign="middle">'.$quote->KWhPrice.'</td>';
print '        <td align="left" class="customer_icon" valign="middle"><img src="images/email_icon.png" /></td>';
print '        <td align="left" class="customer_vals" valign="middle">'.$customer->Email.'</td>';
print '      </tr>';
print '      <tr>';
print '<td align="center" valign="middle"><img src="images/ellies_icon.png" /></td>';        
print '<td align="left" class="heading">Date:</td>';
print '       <td align="left" class="quote_vals" valign="middle">'.$quote->DateCreated.'</td>';
print '        <td align="left" class="customer_icon" valign="middle"><img src="images/property_icon.png" /></td>';
print '        <td align="left" class="customer_vals" valign="middle">'.$quote->Property.'</td>';
print '      </tr>';
print '    </table>';
print "</div>";
//print "<input type='button' value='Update Values' >";
print "<a href='selectproduct.php?id=0&quoteitemid=0&quoteid=".$quoteid."' data-role='button' data-rel='dialog' data-close-btn-text='close' data-inline='true' data-icon='plus' data-theme='a'>Add Item</a>";
print '<a data-role="button" id="btneditquote" data-icon="grid" data-iconpos="left" class="ui-btn-left" data-inline="true" data-theme="a" href="#page1" onclick="ShowQuoteValues()">Edit Values</a>';
print "<br><br>";

$quote->CalculateCostSavingTotals();
$savings = $quote->Get5YearSavings();
$ttl_savings = end($savings["Cumulative Savings"]);

//end get year num

/*--------Total Overview------*/

print '<h1 class="icon_heading">Overview</h1>';
print '<div id="overview">';
print '<div class="total_icon"><div class="inner_icon"><img src="images/clock_icon.png" />';
print '<h2>'.$quote->TotalPaybackPeriodFormatted.'</h2>';
print '<span class="icon_description">Payback Period</span></div></div>';
print '<div class="total_icon"><div class="inner_icon"><img src="images/plus_icon.png" />';
print '<h2>R'.number_format($quote->MonthlyCostSaving, 2).'</h2>';
print '<span class="icon_description">Monthly Saved</span></div></div>';
print '<div class="total_icon"><div class="inner_icon"><img src="images/percentage_icon.png" />';
print '<h2>'.number_format($quote->KWhSavedPerc, 0).'%</h2>';
print '<span class="icon_description">Percentage Saved</span></div></div>';
print '<div class="total_icon"><div class="inner_icon"><img src="images/years_icon.png" />';
print '<h2>'.$ttl_savings.'</h2>';
print '<span class="icon_description">Savings after 5 years</span></div></div>';
print '<div class="total_icon"><div class="inner_icon"><img src="images/tick_icon.png" />';
print '<h2>R'.number_format($quote->TotalPrice, 2).'</h2>';
print '<span class="icon_description">Total Cost</span></div></div>';
print '</div>';

/*--------Total Savings------*/
$irow = 0;
$keyYear = array_keys($savings["Price per kWh"]);
$headings = array_keys($savings);

/*get last year number */
$yearNumber = end($keyYear);
//$newKeyYear = end($keyYear);
/*preg_match_all("/[0-9]/", $newKeyYear, $matches);
foreach($matches[0] as $value)
{
	$yearNumber .=  $value;
}*/
print '<div id="savings_menu">';
print '<div class="title_heading"><div class="inner_heading"><span class="title_span"><img id="prod_icon" src="images/prodmenu_plus_icon.png"/><span><h1 class="icon_heading" id="prod_heading">Projected Savings</h1></div><div id="losses_condition">If Ellies items are not chosen</div></div>';
print '    <div class="content_w"> ';
print '        <div class="content"> ';
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
print '        </div>';
print '    </div>';
print '</div>';
/*-------Total Losses------*/
print '<div id="losses_menu">';
print '<div class="title_heading"><div class="inner_heading"><span class="title_span"><img id="prod_icon" src="images/prodmenu_plus_icon.png"/><span><h1 class="icon_heading" id="prod_heading">Projected losses</h1></div></div>';
print '    <div class="content_w"> ';
print '        <div class="content"> ';
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
print '        </div>';
print '    </div>';
print '</div>';
$sql = "SELECT `quote_items`.`id`";
//$sql.= ", `quote_items`.`qty`";
//$sql.= ", `quote_items`.`room`";
//$sql.= ", `tech_items`.`tech_type`";
//$sql.= ", `tech_items`.`option`";
//$sql.= ", `tech_items`.`new_product`";
//$sql.= ", `tech_items`.`price`";
//$sql.= ", 0 AS `Units`";
$sql.= " FROM `quote_items`";
//$sql.= " INNER JOIN `tech_items` ON `quote_items`.`tech_id` = `tech_items`.`id`";
$sql.= " WHERE `quote_items`.`quote_id` = '".mysqli_real_escape_string($GLOBALS["link"],$quoteid)."' ORDER BY `id`";
print '<div id="product_menu">';
print '<div class="title_heading"><div class="inner_heading"><span class="title_span"><img id="prod_icon" src="images/prodmenu_plus_icon.png"/><span><h1 class="icon_heading" id="prod_heading">Chosen Products Info</h1></div></div>';
print '    <div class="content_w"> ';
print '        <div class="content"> ';
print "<table width='100%' cellspacing='4' cellpadding='0' border='0'>";
$totalprice = 0;
$sqlres = mysqli_query($GLOBALS["link"],$sql);
$itemcount = 0;
$totalqty = 0;

$totalkwhexisting = 0;
$totalcostexisting = 0;
$totalkwhreplacement = 0;
$totalcostreplacement = 0;
//$quote->CalculateCostSavingTotals();


while($row = mysqli_fetch_assoc($sqlres)) {
    $quoteitem = new QuoteItem($row["id"]);
    $itemcount += 1;
    print "<tr>";
    print "<td colspan='4' class='quotegridtitle borad-AB'>Product No. ".$itemcount."</td>";
    print "<td width='16%' style='padding:0px;margin:0px;'><a href='selectproduct.php?id=".$quoteitem->QuoteItemID."&step=EditItem' data-role='button' data-theme='a' data-rel='dialog' data-close-btn-text='close' data-icon='grid' data-inline='false'>Edit</a></td>";
    print "<td width='16%' style='padding:0px;margin:0px;'><a href='quoteitem.php?id=".$quoteitem->QuoteItemID."&quoteid=".$quoteitem->QuoteID."&step=remove' data-role='button' data-theme='a' data-icon='delete' data-inline='false'>Remove</a></td>";
    print "</tr>";
    
    print "<tr>";
    print "<td class='borad-AB color-A' align='center' width='16%' height='30'>Technology Type</td>";
    print "<td class='borad-AB color-A' align='center' width='16%'>Quantity</td>";
    print "<td class='borad-AB color-A' align='center' width='16%'>Room</td>";
    print "<td class='borad-AB color-A' align='center' colspan='3'>Usage Information</td>";
    print "</tr>";

    print "<tr>";
    print "<td class='borad-CD color-B' align='center' height='30'>".$quoteitem->TechType."</td>";
    print "<td class='borad-CD color-B' align='center'>".$quoteitem->Qty."</td>";
    print "<td class='borad-CD color-B' align='center'>".$quoteitem->Room."</td>";
    foreach($quoteitem->GetInputValues() as $key=>$value) {
        $key = str_replace("_", " ", $key);
        print "<td class='color-A' align='center'>".$key."</td>";    
    }
    print "</tr>";

    print "<tr>";
    print "<td colspan='3' height='30'>&nbsp;</td>";
    foreach($quoteitem->GetInputValues() as $key=>$value) {
        print "<td class='borad-CD color-B' align='center'>".$value."</td>";    
    }
    print "</tr>";

    $quoteitem->CalculateCostSavings();
    
    print "<tr>";
    print "<td colspan='4' class='borad-AB color-A' align='center' height='30'>Item</td>";
    print "<td class='borad-AB color-A' align='center'>kWh Per Month</td>";
    print "<td class='borad-AB color-A' align='center'>Monthly Cost (Year 1)</td>";
    print "</tr>";

    print "<tr>";
    print "<td class='color-C' align='center'>Existing</td>";
    print "<td colspan='3' class='color-C' height='30'>".$quoteitem->OldProductName."</td>";
    print "<td class='color-C' align='center'>".number_format($quoteitem->KWhOld, 2)."</td>";
    print "<td class='color-C' align='center'>R ".number_format($quoteitem->MonthlyCostOld, 2)."</td>";
    print "</tr>";
    
    
    print "<tr>";
    print "<td class='borad-C color-D' align='center'>Replacement</td>";
    print "<td colspan='3' class='color-D' height='30'>".$quoteitem->ItemDescription."</td>";
    print "<td class='color-D' align='center'>".number_format($quoteitem->KWhNew, 2)."</td>";
    print "<td class='borad-D color-D' align='center'>R ".number_format($quoteitem->MonthlyCostNew, 2)."</td>";
    print "</tr>";

    print "<tr>";
    print "<td colspan='3' class='borad-AB color-A' align='left' height='30'>Savings</td>";
    print "<td colspan='3' class='borad-AB color-A' align='left'>Payback Information</td>";
    print "</tr>";

    print "<tr>";
    print "<td colspan='2' class='color-E' align='left' height='30'>Saved kWh/Month</td>";
    print "<td class='color-E' align='left'>".number_format($quoteitem->KWhSaved, 2)." (".number_format($quoteitem->KWhSavedPerc, 0)."%)</td>";
    print "<td colspan='2' class='color-E' align='left'>Replacement Price</td>";
    print "<td class='color-E' align='left'>R ".number_format($quoteitem->ItemTotal, 2)."</td>";
    print "</tr>";
    $payback = CalculatePayback($quote->KWhPrice, $quote->ElectricityEscalationRatePercentage, $quoteitem->ItemTotal, $quoteitem->KWhSaved);
    print "<tr>";
    print "<td colspan='2' class='borad-C color-E' align='left' height='30'>Amount Saved Monthly (Y1)</td>";
    print "<td class='color-E' align='left'>R ".number_format($quoteitem->MonthlyCostSaving, 2)."</td>";
    print "<td colspan='2' class='color-E' align='left'>Payback Period</td>";
    print "<td class='borad-D color-E' align='left'>".$quoteitem->PaybackPeriodFormatted."</td>";
    print "</tr>";
    
    //===HR===
    print "<tr><td colspan='6' style='height:3px;font-size:6px;'>&nbsp;</td></tr>";
    print "<tr><td colspan='6' style='border-top:3px dashed black;margin-top:5px;'></td></tr>";
    print "<tr><td colspan='6' style='height:3px;font-size:6px;'>&nbsp;</td></tr>";
    
    $totalqty += $quoteitem->Qty;


}
print '        </div>';
print '    </div>';
print '</div>';
?>
<script>
  function toggleContent($cont) {
		$contentID = '#' + $cont;
	  $contentWrapper = $($contentID + ' .content_w');
//alert($cont);
    // Get the computed height of the content
    var contentHeight = $('.content', $contentWrapper).outerHeight(true);
    // Add or remove class "open"
    $contentWrapper.toggleClass('open');
 	
    // Set max-height
    if ($contentWrapper.hasClass('open'))
	{
      $contentWrapper.css('max-height', contentHeight); 
	  $('html, body').animate({ scrollTop: $contentWrapper.offset().top }, 'slow');
	  $($contentID + ' #prod_icon').attr('src','images/prodmenu_minus_icon.png');
    }
    else
	{
      $contentWrapper.css('max-height', 0);
	  $($contentID + ' #prod_icon').attr('src','images/prodmenu_plus_icon.png');
    }
  }

$( ".inner_heading" ).click(function() {
 	var par = $(this).parent().parent().attr("id");
	//alert (par);
    toggleContent(par); 
    
  //toggleContent($('#content_w')); 
});
</script>
<?php 
/*//===Totals===
print "<tr><td colspan='6' class='borad-AB quotegridtitle' align='center' height='30'>Total Information</td></tr>";
print "<tr><td colspan='6' class='borad-AB color-A' align='left' height='30'>Total Quantity of Items to Replace <div style='background-color:white;width:100px;display:inline-table;margin-left:50px;margin-top:2px;margin-bottom:2px'>".$totalqty."</div></td></tr>";

print "<tr>";
print "<td colspan='2' class='borad-AB color-A' align='left' height='30'>Total Items</td>";
print "<td colspan='2' class='borad-AB color-A' align='left'>Total kWh Per Month</td>";
print "<td colspan='2' class='borad-AB color-A' align='left'>Total Monthly Cost (Year 1)</td>";
print "</tr>";

print "<tr>";
print "<td colspan='2' class='color-C' align='left' height='30'>Existing</td>";
print "<td colspan='2' class='color-C' align='left'>".number_format($quote->KWhOld, 2)."</td>";
print "<td colspan='2' class='color-C' align='left'>R ".number_format($quote->MonthlyCostOld, 2)."</td>";
print "</tr>";

print "<tr>";
print "<td colspan='2' class='color-D' align='left' height='30'>Replacement</td>";
print "<td colspan='2' class='color-D' align='left'>".number_format($quote->KWhNew, 2)."</td>";
print "<td colspan='2' class='color-D' align='left'>R ".number_format($quote->MonthlyCostNew, 2)."</td>";
print "</tr>";

//===Savings Totals===
print "<tr>";
print "<td colspan='3' class='borad-AB color-A' align='left' height='30'>Savings</td>";
print "<td colspan='3' class='borad-AB color-A' align='left'>Payback Information</td>";
print "</tr>";

print "<tr>";
print "<td colspan='2' class='color-E' align='left' height='30'>Total Saved kWh/Month</td>";
print "<td class='color-E' align='left'>".number_format($quote->KWhSaved, 2)." (".number_format($quote->KWhSavedPerc, 0)."%)</td>";
print "<td colspan='2' class='color-E' align='left'>Total Replacement Price</td>";
print "<td class='color-E' align='left'>R ".number_format($quote->TotalPrice, 2)."</td>";
print "</tr>";

print "<tr>";
print "<td colspan='2' class='borad-C color-E' align='left' height='30'>Total Amount Saved Monthly (Y1)</td>";
print "<td class='color-E' align='left'>R ".number_format($quote->MonthlyCostSaving, 2)."</td>";
print "<td colspan='2' class='color-E' align='left'>Total Payback Period</td>";
print "<td class='borad-D color-E' align='left'>".$quote->TotalPaybackPeriodFormatted."</td>";
print "</tr>";
print "</table>";

//===Projected Savings===

print "<br><table width='100%' cellspacing='4' cellpadding='0' border='0'>";
print "<tr><td colspan='6' class='borad-AB quotegridtitle' align='center' height='30'>Projected Savings from Ellies Customised Solution for You</td></tr>";
//$savings = $quote->Get5YearSavings();
$irow = 0;
$yearNumber = "";
//get last year num
$keyYear = array_keys($savings["Price per kWh"]);
$newKeyYear = end($keyYear);
preg_match_all("/[0-9]/", $newKeyYear, $matches);
foreach($matches[0] as $value)
{
	$yearNumber .=  $value;
}
//end get year num

//$ttl_savings = end($savings["Cumulative Savings"]);
foreach($savings as $key=>$value) {
    $irow += 1;
    if ($irow == 1) {
        print "<tr>";
        print "<td></td>";
        foreach($value as $col=>$val) {
            print "<td class='borad-AB color-A' align='left' height='30' width='15%'>".$col."</td>";
        }
        print "</tr>";
    }

    print "<tr>";
    print "<td class='color-A' align='left' height='30'>".$key."</td>";
    foreach($value as $col=>$val) {
        if ($irow == 1) {
            print "<td class='color-B' align='left'>".$val."</td>"; 
		
        }else{
            print "<td class='color-D' align='left'>".$val."</td>";
        }
		
    }    
    print "</tr>";
	
   
}
print "<tr>";
print "<td colspan='3' height='40'></td>";
print "<td colspan='2' class='borad-C quotegridtitle'>Projected Savings After ".$yearNumber. " Years</td>";
print "<td class='color-D borad-D' align='left'>".$ttl_savings."</td>";
print "</tr>";
print "</table>";

//===Projected Losses===
$irow = 0;
print "<br><table width='100%' cellspacing='4' cellpadding='0' border='0'>";
print "<tr><td colspan='6' class='borad-AB quotegridtitle' align='center' height='30'>Projected Losses if Ellies Customised Solution for You is Not Chosen</td></tr>";
$losses = $quote->Get5YearLosses();
$ttl_losses = end($losses["Cumulative Losses"]);

foreach($losses as $key=>$value) {
    $irow += 1;
    if ($irow == 1) {
        print "<tr>";
        print "<td></td>";
        foreach($value as $col=>$val) {
            print "<td class='borad-AB color-A' align='left' height='30' width='15%'>".$col."</td>";
        }
        print "</tr>";
    }

    print "<tr>";
    print "<td class='color-A' align='left' height='30'>".$key."</td>";
    foreach($value as $col=>$val) {
        if ($irow == 1) {
            print "<td class='color-B' align='left'>".$val."</td>";    
        }else{
            print "<td class='color-C' align='left'>".$val."</td>";            
        }
    }    
    print "</tr>";
}
print "<tr>";
print "<td colspan='3' height='40'></td>";
print "<td colspan='2' class='borad-C quotegridtitle'>Projected Losses After ".$yearNumber. " Years</td>";
print "<td class='color-C borad-D' align='left'>".$ttl_losses."</td>";
print "</tr>";

    //===HR===
    print "<tr><td colspan='6' style='height:3px;font-size:6px;'>&nbsp;</td></tr>";
    print "<tr><td colspan='6' style='border-top:3px dashed black;margin-top:5px;'></td></tr>";
    print "<tr><td colspan='6' style='height:3px;font-size:6px;'>&nbsp;</td></tr>";

print "</table>";
*/




$exvat = $totalprice / 1.14;
$vat = $totalprice - $exvat;

$colspan = 3;
//if (!$quote->Complete) {
    $colspan = 5;
//}
/*
print "<tr>";
print "<th class='ui-body-e' colspan='".$colspan."' style='text-align:right;padding-right:5px;'>Total (ex vat)</th>";
print "<td class='ui-body-c'>R ".number_format($exvat, 2)."</td>";
print "</tr>";

print "<tr>";
print "<th class='ui-body-e' colspan='".$colspan."' style='text-align:right;padding-right:5px;'>Vat</th>";
print "<td class='ui-body-c'>R ".number_format($vat, 2)."</td>";
print "</tr>";

print "<tr>";
print "<th class='ui-body-a' colspan='".$colspan."' style='text-align:right;padding-right:5px;'>Total</th>";
print "<td class='ui-body-c'>R ".number_format($totalprice, 2)."</td>";
print "</tr>";
*/
//print "</table>";


//print "<br><a href='techinfo.php?id=0&quoteid=".$quoteid."' data-role='button' data-rel='dialog' data-close-btn-text='close' data-inline='true' data-icon='plus'>Add Item</a>";

//http://localhost/ellies_instore/quotes/techinfo.php

print "</div>";
require_once("inc/footer.php");
?>