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
require_once("../inc/simpleImage.php");
require_once("../inc/quoteimages.class.php");

require_once("inc/checklist.class.php");
require_once("../inc/quotechecklist.class.php");


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
$booktitle = "Viewing Quote Checklist";
$_SESSION['quoteID'] = $_GET["id"];

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
//====================================================================WEBPAGE==========================================================================
$reurl = "";
if (!empty($_GET["reurl"])) {
    $_SESSION["reurl"] = $_GET["reurl"];
    $reurl = $_GET["reurl"];
}

//Gets customer ID
$customerid = 0;
if ($quoteid == "0") {
    $quote->CustomerID = $_GET["customerid"];
    $quote->Save();
}
$customerid = $quote->CustomerID;

//Prints the header
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

$sql = "SELECT `type` FROM `channels` WHERE `id`=".$sysuser->retailChannel;
$sqlres = mysqli_query($GLOBALS["link"],$sql);
$row = mysqli_fetch_assoc($sqlres);
$channeltype = $row['type'];
if ($channeltype == "Commercial" || $channeltype =="Franchises"){
    print "><form method='Post' action='editquotecomm.php?id=".$quoteid."'>";
}else{
    print "><form method='Post' action='editquote.php?id=".$quoteid."'>";
}

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
}else {
    print "<a href=\"editquote.php?step=back&id=".$quote->QuoteID."\"  data-role='button' data-theme='a' data-transition='fade' data-inline='true' data-icon='info' data-ajax='false'>Back</a>";
}
/*------*/
print "<div id='quote-container'";
if ($isnew) {
    print " style='display:none;'";
}else{

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
$quote->CalculateCostSavingTotals();
$savings = $quote->Get5YearSavings();
$term_savings=($quote->Get5Savings());
$ttl_savings = end($savings["Cumulative Savings"]);
$array_keys = array_keys($savings["Price per kWh"]);
$ttl_savings_time = end($array_keys);
//=================================================================================Checklist===============================================================
//Heading
if ($quote->RentalTerm==0){
    print '<h1 class="icon_heading">Quote Checklists</h1>';
}

//Types of Properties
    $typeofRoom = "Shop";
    print '<div id="overview">';
    print '<div class="total_icon"><div class="inner_icon"><img src="images/shop.png" />';
    print '<h2>' . $typeofRoom.'</h2></div></div>';

    $typeofRoom = "Warehouse";
    print '<div class="total_icon"><div class="inner_icon"><img src="images/warehouse.png" />';
    print '<h2>'. $typeofRoom .'</h2></div></div>';

    $typeofRoom = "Hospital";
    print '<div class="total_icon"><div class="inner_icon"><img src="images/hospital.png" />';
    print '<h2>'. $typeofRoom .'</h2></div></div>';

    $typeofRoom = "Clinic";
    print '<div class="total_icon"><div class="inner_icon"><img src="images/clinic.png" />';
    print '<h2>'. $typeofRoom .'</h2></div></div>';

    $typeofRoom = "Office";
    print '<div class="total_icon"><div class="inner_icon"><img src="images/office.png" />';
    print '<h2>'. $typeofRoom .'</h2></div></div>';

    $typeofRoom = "House";
    print '<div class="total_icon"><div class="inner_icon"><img src="images/house.png" />';
    print '<h2>'. $typeofRoom .'</h2></div></div>';

    $typeofRoom = "Building";
    print '<div class="total_icon"><div class="inner_icon"><img src="images/building.png" />';
    print '<h2>'. $typeofRoom .'</h2></div></div>';

print '</div>';
//Property Fields
/*--------Shop------*/
$irow = 0;
$keyYear = array_keys($savings["Price per kWh"]);
$headings = array_keys($savings);
/*get last year number */
$yearNumber = end($keyYear);

//Quote Checklist
//Answers
$quotechecklist = new QuoteChecklist();
$arrRoomsTypes  = $quotechecklist->RoomTypesExists($quoteid);
$numRooms = count($arrRoomsTypes);

//Questions
$checklist = new Checklist();

 if($_SESSION["approved"] == 1)
 {
    $readonly = true;
 }else{
    $readonly = false;
 }

 //Heading
$row = '';
if($numRooms == 0){
    print '<br><h1 style="color:red;">Quote has no checklist</h1>';
}
for($i=0;$i<$numRooms;$i++)
{
    print '<div id="savings_menu">';
    $room = str_replace("^", " ", $arrRoomsTypes[$i]);
    $row = '<thead>
            <tr>
                <td>
                    <div data-role="header" data-theme="a" data-transition="fade" data-inline="true"  data-ajax="false"  height="80%">
                        <h1>QUESTION</h1>
                    </div> 
                </td>
                <td>
                    <div data-role="header" data-theme="a" data-transition="fade" data-inline="true"  data-ajax="false" >
                    <h1>ANSWER</h1>
                </div> </td>
            </tr>  
        </thead>';
    print '<div class="title_heading"><div class="inner_heading" onclick="toggle_div(this,\''.$i.'\',\''.$i.'_icon\');"><span class="title_span"><img id="'.$i.'_icon" src="images/prodmenu_plus_icon.png"/><span><h1 class="prod_heading"> '. $room . '</h1></div></div>';
    //Answers for room
    $arrQuoteAnswers = $quotechecklist->ReadChecklist($quoteid,$arrRoomsTypes[$i]);
    $numAnswers = count($arrQuoteAnswers);
    print '<div id="'.$i.'" class="content_w"> ';
       for($q=0;$q<$numAnswers;$q++)
       {
            //title,answer;
             $quest = explode(':', $arrQuoteAnswers[$q]);
             $QuestID = $quest[0];
             $title = $checklist->GetQuestion($QuestID);
             $answer = trim(str_replace(';', "", $quest[1]));
             $row .= '<tr >
                        <td width="60%"><div style="word-break:break-all;">'.$title .'</div></td>
                        <td width="40%"style="text-align:center;"><div style="word-break:break-all;">'. $answer.'</div></td>
                    </tr>';
       } 

       print '<div>
                <table cellspacing="0" cellpadding="8" width="50%" >'.$row . '</table>
       </div>';
    print '</div>';
    print '</div>';
}
//====================================================================
//Divider
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