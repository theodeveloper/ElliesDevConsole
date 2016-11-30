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
$booktitle = "Viewing Images";
$_SESSION['quoteID'] = $_GET["id"];
$_SESSION['Room'] = "";

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
if (!$isnew) {
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
}else{
  print "<a href=\"editquote.php?step=back&id=".$quote->QuoteID."\"  data-role='button' data-theme='a' data-transition='fade' data-inline='true' data-icon='info' data-ajax='false'>Back</a>";  
}
///View images based on rooms of the checklist
$arrImages = new QuoteImages();
$arrRoomsTypes  = array();
$arrImages2 = $arrImages->ReadImages($quoteid);
$count = count($arrImages2);
for($r = 0;$r< $count; $r ++)
{
    //filename,room;
    $ImgDetails = $arrImages2[$r];
    $d = explode(",",$ImgDetails);
    $filename = $d[0];
    $room = $d[1];
    $roomDetails  = explode("^",$room);
    if(count($roomDetails) > 2){
        $roomtype = $roomDetails[1];
        $roomtype= str_replace(";", "", $roomtype);
        $room = $roomtype;
        if(!in_array($room, $arrRoomsTypes)){
            $arrRoomsTypes[] =  $room;
        }
    }else{
        $room= str_replace(";", "", $room);
        if(!in_array($room, $arrRoomsTypes)){
            $arrRoomsTypes[] =  $room;
        }
    }
} 

$numRooms = count($arrRoomsTypes);
$arr = '';    
for ($i = 0; $i < $numRooms; $i++)
{
      $arr .= '<option value="'.$arrRoomsTypes[$i].'">'.$arrRoomsTypes[$i].'</option>';
}
//Add images
print "<a id='addImage' name='addImage'  data-role='button' data-theme='a' data-transition='fade' data-inline='true' data-icon='info' data-ajax='false'>Add Image</a>";
print '<form id ="uploadfiles" enctype="multipart/form-data">
        Select Room:<select id="roomType"  name="roomType" data-theme="a" data-transition="fade" data-inline="true" data-icon="arrow-d" data-ajax="false">
                      <option value="NONE" selected>(NONE)</option>'
                      .$arr.'
                    </select>
        <div id="errormessage" style="color:red"></div>  
        <div id="mulitplefileuploader">Upload</div>
        <div id="status"></div>
        </form>';

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
$quote->CalculateCostSavingTotals();
$savings = $quote->Get5YearSavings();
$term_savings=($quote->Get5Savings());
$ttl_savings = end($savings["Cumulative Savings"]);
$array_keys = array_keys($savings["Price per kWh"]);
$ttl_savings_time = end($array_keys);
//=================================================================================IMAGES===============================================================
//Heading
if ($quote->RentalTerm==0){
    print '<h1 class="icon_heading">Images</h1>';
}

//<!-- FancyBox -->
print '<link rel="stylesheet" href="fancybox/jquery.fancybox-buttons.css">';
print '<link rel="stylesheet" href="fancybox/jquery.fancybox-thumbs.css">';
print '<link rel="stylesheet" href="fancybox/jquery.fancybox.css">';

//Upload Files
print '<link href="uploadFiles/uploadfile.css" rel="stylesheet">';
print '<script src="uploadFiles/jquery.uploadfile.min.js"></script>';
print '<script src="uploadFiles/jquerysession.js"></script>';

//Resizing Images
if (class_exists('SimpleImage'))
{
    $img = new SimpleImage("images/none.png");
    $img->best_fit(60, 60)->save('images/none2.png');

    $img = new SimpleImage("images/Images/light.jpg");
    $img->best_fit(250, 250)->save('images/Images/Thumbnails/light.jpg');

    $img = new SimpleImage("images/Images/light2.jpg");
    $img->best_fit(250, 250)->save('images/Images/Thumbnails/light2.jpg');

    $img = new SimpleImage("images/Images/light3.jpg");
    $img->best_fit(250, 250)->save('images/Images/Thumbnails/light3.jpg');

    $img = new SimpleImage("images/Images/light4.jpg");
    $img->best_fit(250, 250)->save('images/Images/Thumbnails/light4.jpg');

    $img = new SimpleImage("images/Images/car.jpg");
    //$img->best_fit(100, 100)->save('images/Images/Thumbnails/car.jpg'); 
    $img->best_fit(105, 56)->save('images/Images/Thumbnails/car.jpg'); 


    $img = new SimpleImage("images/Images/error.jpg");
    //$img->thumbnail(105, 56)->save('images/Images/Thumbnails/error.jpg');
    $img->best_fit(105, 56)->save('images/Images/Thumbnails/error.jpg');        
}else{
    echo "Class doesnt exist";
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

//Get the number of images based on rooms 
$arrImages = new QuoteImages();
$arrRoomsTypes  = array();
$arrImages2 = $arrImages->ReadImages($quoteid);
$count = count($arrImages2);
for($r = 0;$r< $count; $r ++)
{
    //filename,room;
    $ImgDetails = $arrImages2[$r];
    $d = explode(",",$ImgDetails);
    $filename = $d[0];
    $room = $d[1];
    $roomDetails  = explode("^",$room);
    if(count($roomDetails) > 2){
        $roomtype = $roomDetails[1];
        $roomtype= str_replace(";", "", $roomtype);
        $room = $roomtype;
        if(!in_array($room, $arrRoomsTypes)){
            $arrRoomsTypes[] =  $room;
        }
    }else{
        $room= str_replace(";", "", $room);
        if(!in_array($room, $arrRoomsTypes)){
            $arrRoomsTypes[] =  $room;
        }
    } 
}  

//Classifying Type of Premises
$homeArr = array("House","Big House","Public Service");
$businessArr = array("Business park","Restaurant");
$buildingArr = array("Public Service Building","Public Building");
$shopArr = array("Retail Shop","Big Shop");
$warehousepArr = array("Warehouse Style");
$hospitalArr = array("Hospital");
$clinicArr = array("Clinic");
$officeArr = array("Office");


$Property ='';
if($quote->PropertyID >0){
    $sql = "SELECT `Type`";
    $sql.= " FROM `property_types`";
    $sql.= " WHERE `property_types`.id = '".mysqli_real_escape_string($GLOBALS["link"],$quote->PropertyID)."' LIMIT 1";

    $sqlres = mysqli_query($GLOBALS["link"],$sql);
    while($row = mysqli_fetch_assoc($sqlres)) {
        $Property = $row['Type'];
    } 
}


if(in_array($Property , $homeArr)){
    $Property = "Home";
}else if(in_array($Property , $warehousepArr)){
    $ $Property = "Warehouse";
}else if(in_array($Property , $shopArr)){
    $Property = "Shop";
}else if(in_array($Property , $hospitalArr)){
    $ $Property = "Hospital";
}elseif(in_array($Property , $clinicArr)){
    $Property = "Clinic";
}else if(in_array($Property , $officeArr)){
    $Property = "Office";
}else if(in_array($Property , $buildingArr)){
    $Property = "Building";
}else if(in_array($Property , $businessArr)){
    $Property = "Business";
}else{
    $Property = "Retail";
}


print '<div id="savings_menu">';
print '<div class="title_heading"><div class="inner_heading" onclick="toggle_div(this,\''.$Property.'\',\''.$Property.'\');"><span class="title_span"><img id="savings_icon" src="images/prodmenu_plus_icon.png"/><span><h1 class="prod_heading">'.$Property.'</h1></div></div>';

print '<div id="'.$Property.'" class="content_w"> ';
    print '<div class="content"> ';
    //Row 1
        /*print '<div id="savings">';
            print '<div class="savings_icon">';
                print '<div class="inner_icon">';
                    print '<div><h1>Single</h1></div>';
                       // print '<p>Room</p>';
                    print '</div>';
                print '</div>';
            //Images
            $images = new QuoteImages(1508);
            $Room = $images->Room;      
            $Room = str_replace("^", " ", $Room);
            $typeofRoom = explode(" ", $Room);
            for ($i = 0; $i < 5; $i ++)
            {
                print '<div class="savings_icon">';
                print '<div class="inner_icon">'; 
                    if(!empty($images->Filename))
                    {     
                      $file = 'images/Images/' . $images->Filename;
                      print'<a class="fancybox" rel="group" href='.$file .' title='.$images->Filename. '><img style="overflow:hidden" src="images/Images/Thumbnails/'.$images->Filename.'"  ></a>';
       
                      //MYPATH
                      print '<div class="icon_description">'.$Room.'</div>';
                      print '<p> '. $typeofRoom[1] .($i+1).'</p>';
                    } else{
                        
                           print '<div class="icon_description">NONE</div>';
                           print '<p>NONE</p>';
                    } 
                print '</div>';
                print '</div>';
            }
        print'</div>';*/

        //$Room  = "";
        //Row 2
         print '<div id="savings">';
            print '<div class="savings_icon">';
                print '<div class="inner_icon">';
                    print '<div><h1>Group By</h1></div>';
              
                    print '</div>';
                print '</div>';
            //Array
            $arrImages = new QuoteImages();
            $arrImages2 = $arrImages->ReadImages($quoteid);
            $arrayRooms = $arrRoomsTypes;
            $count = count($arrImages2);
            $done = false; 
            $found = false; 
            for ($i = 0; $i < 5; $i ++)
            {
                print '<div class="savings_icon">';
                print '<div class="inner_icon">';

                //Type of Room -Reception etc.
                for($r = 0;$r< $count; $r ++)
                {
                    //filename,room;
                    $ImgDetails = $arrImages2[$r];
                    $d = explode(",",$ImgDetails);
                    $filename = $d[0];
                    $room = $d[1];
                    $roomDetails  = explode("^",$room);
                    if(count($roomDetails) > 2){
                        $roomtype = $roomDetails[1];
                        $roomtype= str_replace(";", "", $roomtype);
                        $room = $roomtype;
                    }else{
                        $room= str_replace(";", "", $room);
                    } 

                    if(!empty($arrayRooms[$i]))
                    {
                        if($room == $arrayRooms[$i]){
                            $file = '../sync/files/images/' .  $filename;
                            $img = new SimpleImage($file);
                            $img->best_fit(105, 56)->save('../sync/files/images/Thumbnails/' .  $filename);
                            print'<a class="fancybox" rel="group" href='.$file .' title='. $filename. '><img src="../sync/files/images/Thumbnails/'. $filename.'"></a>';
                            $done  = true;
                            $found  = true;
                            if( ($r +1) == $count){
                                break;
                            }
                        }else{
                         $done  = false;
                        }
                    }else{
                        $done  = false;
                    }                   
                }

               if($done){
                    print '<div class="icon_description"></div>';
                    print '<p> '. $room .'</p>';
               }else{
                    if($found == false)
                    {
                        //print'<a class="fancybox" rel="group" href="images/none.png" title="None"><img src="images/none2.png""></a>';
                        //print '<div class="icon_description">NONE</div>';
                    }
                    if(!empty($arrayRooms[$i]))
                    {
                         print '<div class="icon_description"></div>';
                        print '<p>' . $arrayRooms[$i] .'</p>';
                    }else{
                        print'<a class="fancybox" rel="group" href="images/none.png" title="None"><img src="images/none2.png""></a>';
                        print '<div class="icon_description"></div>';
                        print '<p>None</p>';
                    }  
               }                      
               print '</div>';
               print '</div>';
            }
        print'</div>';
        //Row 3 
        /* print '<div id="savings">';
            print '<div class="savings_icon">';
                print '<div class="inner_icon">';
                    print '<div><h1>Single Group</h1></div>';

                    print '</div>';
                print '</div>';

            //Array
            $arrImages = new QuoteImages();
            $arrImages3 = $arrImages->ReadImagesA($quoteid);
            //echo "size:" . count($arrImages3);
            $count = count($arrImages3);

            for ($i = 0; $i < 5; $i ++)
            {
                print '<div class="savings_icon">';
                print '<div class="inner_icon">';

                for($r = 0;$r< $count; $r ++)
                {

                  $file = '../sync/files/images/' . $arrImages3[$r];
                  print'<a class="fancybox" rel="group" href='.$file .' title='.$arrImages3[$r]. '><img src="../sync/files/images/Thumbnails/'.$arrImages3[$r].'"></a>';//MYPATH
                }
                    //print '<div class="icon_description">Light Bulbs</div>';
                print '<p>Quote Images</p>';
                print '</div>';
                print '</div>';
                break;
            }
        print'</div>';*/
        ///Row 4
         /*print '<div id="savings">';
            print '<div class="savings_icon">';
                print '<div class="inner_icon">';
                    print '<div><h1>Ground</h1></div>';

                    print '</div>';
                print '</div>';

            for ($i = 15; $i < 20; $i ++)
            {
                print '<div class="savings_icon">';
                print '<div class="inner_icon">';
                    print'<a class="fancybox" rel="group" href="images/Images/light4.jpg" title="Light Bulbs"><img src="images/Images/Thumbnails/light4.jpg""></a>';
                    print '<div class="icon_description">Light Bulbs</div>';

                print '<p>Reception '.($i+1).'</p>';
                print '</div>';
                print '</div>';
            }
        print'</div>';*/

print '</div>';
print '</div>';
print '</div>';
/*-------Hospital------*/
/*print '<div id="hosiptal_menu">';
print '<div class="title_heading"><div class="inner_heading" onclick="toggle_div(this,\'hosiptal_cont\',\'savings_icon\');"><span class="title_span"><img id="savings_icon" src="images/prodmenu_plus_icon.png"/><span><h1 class="prod_heading">Hospital</h1></div></div>';

print '<div id="hosiptal_cont" class="content_w"> ';
    print '<div class="content"> ';
        print '<div id="savings">';
            print '<div class="savings_icon">';
                print '<div class="inner_icon">';
                   print '<div><h1>Ground</h1></div>';
                       
                    print '</div>';
                print '</div>';

            for ($i = 0; $i < 5; $i ++)
            {
                print '<div class="savings_icon">';
                print '<div class="inner_icon">';                
                     print'<a class="fancybox" rel="group" href="images/Images/light.jpg" title="Light Bulbs"><img src="images/Images/Thumbnails/light.jpg""></a>';
                     print '<div class="icon_description">Light Bulbs</div>';

                print '<p>Reception '.($i+1).'</p>';
                print '</div>';
                print '</div>';
            }
        print'</div>';
        //Row 2
        print '<div id="savings">';
            print '<div class="savings_icon">';
                print '<div class="inner_icon">';
                     print '<div><h1>Ground</h1></div>';

                    print '</div>';
                print '</div>';

            for ($i = 5; $i < 10; $i ++)
            {
                print '<div class="savings_icon">';
                print '<div class="inner_icon">';
                      print'<a class="fancybox" rel="group" href="images/Images/light2.jpg" title="Light Bulbs"><img src="images/Images/Thumbnails/light2.jpg""></a>';
                      print '<div class="icon_description">Light Bulbs</div>';

                print '<p>Reception '.($i+1).'</p>';
                print '</div>';
                print '</div>';
            }
        print'</div>';
        //Row 3
         print '<div id="savings">';
            print '<div class="savings_icon">';
                print '<div class="inner_icon">';
                    print '<div><h1>Ground</h1></div>';

                    print '</div>';
                print '</div>';

           for ($i = 10; $i < 15; $i ++)
            {
                print '<div class="savings_icon">';
                print '<div class="inner_icon">';
                    print'<a class="fancybox" rel="group" href="images/Images/light3.jpg" title="Light Bulbs"><img src="images/Images/Thumbnails/light3.jpg""></a>';
                    print '<div class="icon_description">Light Bulbs</div>';

                print '<p>Reception '.($i+1).'</p>';
                print '</div>';
                print '</div>';
            }
        print'</div>';

print '</div>';
print '</div>';
print '</div>';
/*-------Clinic------*/
/*print '<div id="clinic_menu">';
print '<div class="title_heading"><div class="inner_heading" onclick="toggle_div(this,\'clinic_cont\',\'savings_icon\');"><span class="title_span"><img id="savings_icon" src="images/prodmenu_plus_icon.png"/><span><h1 class="prod_heading">Clinic</h1></div></div>';

print '<div id="clinic_cont" class="content_w"> ';
    print '<div class="content"> ';
        print '<div id="savings">';
            print '<div class="savings_icon">';
                print '<div class="inner_icon">';
                        print '<div><h1>Ground</h1></div>';
                       
                    print '</div>';
                print '</div>';

            for ($i = 0; $i < 5; $i ++)
            {
                print '<div class="savings_icon">';
                print '<div class="inner_icon">';                
                     print'<a class="fancybox" rel="group" href="images/Images/light.jpg" title="Light Bulbs"><img src="images/Images/Thumbnails/light.jpg""></a>';
                      print '<div class="icon_description">Light Bulbs</div>';

                print '<p>Reception '.($i+1).'</p>';
                print '</div>';
                print '</div>';
            }
        print'</div>';
        //Row 2
        print '<div id="savings">';
            print '<div class="savings_icon">';
                print '<div class="inner_icon">';
                     print '<div><h1>Ground</h1></div>';

                    print '</div>';
                print '</div>';

            for ($i = 5; $i < 10; $i ++)
            {
                print '<div class="savings_icon">';
                print '<div class="inner_icon">';
                      print'<a class="fancybox" rel="group" href="images/Images/light2.jpg" title="Light Bulbs"><img src="images/Images/Thumbnails/light2.jpg""></a>';
                      print '<div class="icon_description">Light Bulbs</div>';

                print '<p>Reception '.($i+1).'</p>';
                print '</div>';
                print '</div>';
            }
        print'</div>';
        //Row 3
         print '<div id="savings">';
            print '<div class="savings_icon">';
                print '<div class="inner_icon">';
                    print '<div><h1>Ground</h1></div>';

                    print '</div>';
                print '</div>';

           for ($i = 10; $i < 15; $i ++)
            {
                print '<div class="savings_icon">';
                print '<div class="inner_icon">';
                    print'<a class="fancybox" rel="group" href="images/Images/light3.jpg" title="Light Bulbs"><img src="images/Images/Thumbnails/light3.jpg""></a>';
                    print '<div class="icon_description">Light Bulbs</div>';

                print '<p>Reception '.($i+1).'</p>';
                print '</div>';
                print '</div>';
            }
        print'</div>';


print '</div>';
print '</div>';
print '</div>';*/
/*-------Office------*/
/*print '<div id="office_menu">';
print '<div class="title_heading"><div class="inner_heading" onclick="toggle_div(this,\'office_cont\',\'savings_icon\');"><span class="title_span"><img id="savings_icon" src="images/prodmenu_plus_icon.png"/><span><h1 class="prod_heading">Office</h1></div></div>';

print '<div id="office_cont" class="content_w"> ';
    print '<div class="content"> ';
        print '<div id="savings">';
            print '<div class="savings_icon">';
                print '<div class="inner_icon">';
                              print '<div><h1>Ground</h1></div>';
                       
                    print '</div>';
                print '</div>';

            for ($i = 0; $i < 5; $i ++)
            {
                print '<div class="savings_icon">';
                print '<div class="inner_icon">';                
                     print'<a class="fancybox" rel="group" href="images/Images/light.jpg" title="Light Bulbs"><img src="images/Images/Thumbnails/light.jpg""></a>';
                      print '<div class="icon_description">Light Bulbs</div>';

                print '<p>Reception '.($i+1).'</p>';
                print '</div>';
                print '</div>';
            }
        print'</div>';
        //Row 2
        print '<div id="savings">';
            print '<div class="savings_icon">';
                print '<div class="inner_icon">';
                     print '<div><h1>Ground</h1></div>';

                    print '</div>';
                print '</div>';

            for ($i = 5; $i < 10; $i ++)
            {
                print '<div class="savings_icon">';
                print '<div class="inner_icon">';
                      print'<a class="fancybox" rel="group" href="images/Images/light2.jpg" title="Light Bulbs"><img src="images/Images/Thumbnails/light2.jpg""></a>';
                      print '<div class="icon_description">Light Bulbs</div>';

                print '<p>Reception '.($i+1).'</p>';
                print '</div>';
                print '</div>';
            }
        print'</div>';
        //Row 3
         print '<div id="savings">';
            print '<div class="savings_icon">';
                print '<div class="inner_icon">';
                    print '<div><h1>Ground</h1></div>';

                    print '</div>';
                print '</div>';

           for ($i = 10; $i < 15; $i ++)
            {
                print '<div class="savings_icon">';
                print '<div class="inner_icon">';
                    print'<a class="fancybox" rel="group" href="images/Images/light3.jpg" title="Light Bulbs"><img src="images/Images/Thumbnails/light3.jpg""></a>';
                    print '<div class="icon_description">Light Bulbs</div>';

                print '<p>Reception '.($i+1).'</p>';
                print '</div>';
                print '</div>';
            }
        print'</div>';


print '</div>';
print '</div>';
print '</div>';*/
/*-------Total Losses------*/
/*print '<div id="losses_menu">';
print '<div class="title_heading"><div class="inner_heading" onclick="toggle_div(this,\'losses_cont\',\'losses_icon\');"><span class="title_span"><img id="losses_icon" src="images/prodmenu_plus_icon.png"/><span><h1 class="prod_heading">Warehouse</h1></div><div id="losses_condition">if Ellies Customised Solution for you is not chosen</div></div>';

print '<div id="losses_cont" class="content_w"> ';
    print '<div class="content"> ';
        print '<div id="losses">';
            print '<div class="losses_icon">';
                print '<div class="inner_icon">';
                    print '<div><h1>Ground</h1></div>';

                    print '</div>';
                print '</div>';
                for ($i = 0; $i < 5; $i ++)
                {
                    print '<div class="losses_icon">';
                    print '<div class="inner_icon">';
                        print'<a class="fancybox" rel="group" href="images/Images/light.jpg" title="Light Bulbs"><img src="images/Images/Thumbnails/light.jpg""></a>';
                        print '<div class="icon_description">Light Bulbs</div>';

                    print '<p>Reception '.($i+1).'</p>';
                    print '</div>';
                    print '</div>';
                }
        print '</div>';
        //Row 2
        print '<div id="losses">';
            print '<div class="losses_icon">';
                print '<div class="inner_icon">';
                     print '<div><h1>Ground</h1></div>';

                    print '</div>';
                print '</div>';
                for ($i = 5; $i < 10; $i ++)
                {
                    print '<div class="losses_icon">';
                    print '<div class="inner_icon">';
                    print'<a class="fancybox" rel="group" href="images/Images/light2.jpg" title="Light Bulbs"><img src="images/Images/Thumbnails/light2.jpg""></a>';
                      print '<div class="icon_description">Light Bulbs</div>';

                    print '<p>Reception '.($i+1).'</p>';
                    print '</div>';
                    print '</div>';
                }
        print '</div>';
        //Row 3
        print '<div id="losses">';
            print '<div class="losses_icon">';
                print '<div class="inner_icon">';
                     print '<div><h1>Ground</h1></div>';
                    print '</div>';
                print '</div>';
                for ($i = 10; $i < 15; $i ++)
                {
                    print '<div class="losses_icon">';
                    print '<div class="inner_icon">';
                     print'<a class="fancybox" rel="group" href="images/Images/light3.jpg" title="Light Bulbs"><img src="images/Images/Thumbnails/light3.jpg""></a>';
                    print '<div class="icon_description">Light Bulbs</div>';

                    print '<p>Reception '.($i+1).'</p>';
                    print '</div>';
                    print '</div>';
                }
        print '</div>';
print '</div>';
print '</div>';
print '</div>';*/
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

<!--FancyBox-->
<script src="fancybox/jquery.fancybox.js"></script>
<script src="fancybox/jquery.fancybox-buttons.js"></script>
<script src="fancybox/jquery.fancybox-thumbs.js"></script>
<script src="fancybox/jquery.mousewheel-3.0.6.pack.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
    $(".fancybox").fancybox();
    });
</script>

<script> 
    $('#uploadfiles').hide();
    $( "#addImage" ).click(function() {
        $('#uploadfiles').toggle();
    });
</script>

<script>
    $("select#roomType").change(function() {
            var room  = $(this).find("option:selected").text();
            //alert(room+' clicked!');
            var urlQuote = "uploadFiles/upload.php?Room=" + room;
            var settings = {

                url:urlQuote,
                dragDrop:true,
                //multiple:true,
                fileName: "myfile",
                allowedTypes:"jpg,png,gif,doc,pdf,zip",
                returnType:"json",
                onSubmit:function(files)
                {
                     //files : List of files to be uploaded
                    //return false; to stop upload
                    if($("select#roomType").val() == "NONE")
                    {
                        $("div#errormessage").text("Please select a room");
                        return false;
                    }
                },
                 onSuccess:function(files,data,xhr)
                {
                    var sess =<?php echo $_SESSION["quoteID"]; ?>;
                    //alert(sess);
                },
                showDelete:false,
                deleteCallback: function(data,pd)
                {
                    for(var i=0;i<data.length;i++)
                    {
                        $.post("uploadFiles/delete.php",{op:"delete",name:data[i]},
                        function(resp, textStatus, jqXHR)
                        {
                            //Show Message  
                            $("#status").append("<div style='color:red'>File Deleted</div>");      
                        });
                     }      
                    pd.statusbar.hide(); //You choice to hide/not.

                }
            }
            var uploadObj = $("div#mulitplefileuploader").uploadFile(settings);
    });
</script>