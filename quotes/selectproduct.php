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
require_once("inc/functions.php");
require_once("inc/quote.class.php");
require_once("inc/quoteitem.class.php");
require_once("inc/product.class.php");
require_once("../inc/system_user.php");

$sysuser = new userType($_SESSION["userid"]);

function ReIndexProducts() {
    $sql = "DELETE FROM `products_mapping`";
    $sqlres = mysqli_query($GLOBALS["link"],$sql);

    $sql = "SELECT `id`, `tech_type`, `product_type` FROM `old_products`";
    $sqlres = mysqli_query($GLOBALS["link"],$sql);
    while ($old = mysqli_fetch_assoc($sqlres)) {

        $sql = "SELECT `id` FROM `new_products` WHERE `product` LIKE '%%%".mysqli_real_escape_string($GLOBALS["link"],$old["product_type"])."%%%'";
        $sql.= " OR `new_technology_type` = '".mysqli_real_escape_string($GLOBALS["link"],$old["product_type"])."'";
        $sql.= " ORDER BY `new_technology_type`, `product`";
        $sqlresb = mysqli_query($GLOBALS["link"],$sql);
        while ($new = mysqli_fetch_assoc($sqlresb)) {
            $sql = "INSERT INTO `products_mapping` (`oldid`, `newid`, `tech_type`)";
            $sql .= " VALUES ('".$old["id"]."', '".$new["id"]."', '".$old["tech_type"]."')";
            mysqli_query($GLOBALS["link"],$sql);
            //print $old["tech_type"]." - ".$old["id"]." - ".$new["id"]."<br>";
            //break;
        }
    }
}
//print "<li>";
//ReIndexProducts();
//print "</li>";
//exit();
function LoadValueOrSetDefault($value, $default) {
    if (!empty($value)) {
        return $value;
    }else{
        return $default;
    }
}

function PrintCalculator($id, $placeholder = "", $value="") {
    print "<table border='0' cellspacing='0' cellpadding='0' id='inp-calc-form'><tr><td>";
    print "<input name='".$id."' id='".$id."' placeholder='".$placeholder."' value='".$value."' data-inline='true' data-mini='true' type='text' class='calctarget' onkeyup='NumOnlyCheck(this, 20)'>";
    print "</td><td>";
    print "&nbsp;&nbsp;&nbsp;<a href='#' onclick='ShowLitreCalc()' id='btnOpenCalculator' style='text-decoration:none;'>Open Calculator</a>";

    print "</td></tr></table>";
    print "<div id='inp-calculator' style='display:none;'>";
    print "<label for='inpcalc_container_ml'>Size of Measuring Container (ml)</lable><input name='inpcalc_container_ml' id='inpcalc_container_ml' data-inline='true' data-mini='true' type='range' placeholder='1000' value='1000'min='1' max='10000' step='1' >";
    print "<label for='inpcalc_filltime_sec'>Time taken to fill (sec)</lable><input name='inpcalc_filltime_sec' id='inpcalc_filltime_sec' placeholder='20' value='20' data-inline='true' data-mini='true' type='range' min='1' max='600' step='1'>";
    print "<br><button data-inline='true' data-mini='true' data-theme='b' data-transition='none' onclick='CalculateLitreUsage()'>Calculate</button>";
    print "</div>";
}

function PrintForm1($quoteid = 0, $quoteitemid = 0, $newid = 0, $oldid = 0, $inputvalues = NULL, $qty = 1) {
    $backlink = "selectproduct.php?oldid=".$oldid;
    //$inputvalues["inp_room"] = "bedroom";
    //$inputvalues["inp_qty"] = 1;
    $_SESSION["quoteid"] = $quoteid;
    $inputvalues["inp_qty"] = LoadValueOrSetDefault($qty, 1);
    
    $product = new Product($newid);
    if ($quoteitemid > 0) {
        $quoteitem = new QuoteItem($quoteitemid);
        $inputvalues = $quoteitem->GetInputValues();
        $inputvalues["inp_room"] = $quoteitem->Room;
        $inputvalues["inp_qty"] = $quoteitem->Qty;
    }
    $sql = "SELECT `type_for_mobi_site` FROM `old_products` WHERE `id` = '".mysqli_real_escape_string($GLOBALS["link"],$oldid)."' LIMIT 1";
    $sqlres = mysqli_query($GLOBALS["link"],$sql);
    while ($row = mysqli_fetch_assoc($sqlres)) {
        $tech_type = $row["type_for_mobi_site"];
    }
	
	require_once("inc/rooms.php");
    
    print "<li data-role='list-divider' role='heading'>".$product->Product."</li>";

    //print "<li>";
    //print "<label for='inp_existing'>Existing Product</label>";
    //print "<select onclick=\"document.location.href='http://www.google.com'\" data-rel='dialog'></select>";
    print "<a href='selectproduct.php?quoteitemid=".$quoteitemid."&quoteid=".$quoteid."' data-rel='dialog' data-role='button' data-inline='true' data-theme='b' data-icon='arrow-d' data-iconpos='right' style='width:97%;'>Please Select Product Type</a>";
    print "<a href='selectproduct.php?tech_type=".$tech_type."&quoteitemid=".$quoteitemid."&quoteid=".$quoteid."' data-rel='dialog' data-role='button' data-inline='true' data-theme='d' data-icon='arrow-d' data-iconpos='right' style='width:97%;'>Please Select Existing Product</a>";
    print "<a href='selectproduct.php?oldid=".$oldid."&quoteitemid=".$quoteitemid."&quoteid=".$quoteid."' data-rel='dialog' data-role='button' data-inline='true' data-theme='e' data-icon='arrow-d' data-iconpos='right' style='width:97%;'>Please Select Replacement Product</a>";
    //oldid=388
//quoteid=1
//quoteitemid=53
    //print "<input type='text' id='inp_existing' name='inp_existing' placeholder='Bedroom, Bathroom etc' class='iteminput' value='".$inputvalues["inp_room"]."'>";
    //print "</li>";
    
    if ($product->RelatedTo == "Hours Per Year") {
        $inputvalues["inp_room"] = LoadValueOrSetDefault($inputvalues["inp_room"], "Bedroom 1");
        $inputvalues["Hours_Per_Day"] = LoadValueOrSetDefault($inputvalues["Hours_Per_Day"], 8);
        $inputvalues["Days_Per_Week"] = LoadValueOrSetDefault($inputvalues["Days_Per_Week"], 7);
        $inputvalues["Weeks_Per_Year"] = LoadValueOrSetDefault($inputvalues["Weeks_Per_Year"], 52);

        print "<li id='withPopUp'>
				<label for='inp_room'>Room</label>
				<input type='text' id='inp_room' name='inp_room' placeholder='Bedroom, Bathroom etc' class='iteminput' value='".$inputvalues["inp_room"]."'>
				<div class='ui-btn ui-shadow ui-btn-corner-all ui-btn-inline ui-btn-icon-right ui-btn-up-b' onClick=\"PopupDiv('inp_room','".$inputvalues["inp_room"]."','LightingRoom')\">
					<span class='ui-btn-inner'>
						<span class='ui-btn-text'>Please Select Room</span>
						<span class='ui-icon ui-icon-arrow-r ui-icon-shadow'>&nbsp;</span>
					</span>
				</div>
			</li>";
        print "<li id='qtyAmount'><label for='inp_qty'>Qty</label>
					<div class= 'change_input'>
					<ul id='qty_ul'>
						<input type='text' id='inp_qty' name='inp_qty' value='".$inputvalues["inp_qty"]."'>
						<li>
							<img src='./images/qty1.png' />
							<input type='hidden' value='1' />
						</li>
						<li>
							<img src='./images/qty2.png' />
							<input type='hidden' value='2' />
						</li>
						<li>
							<img src='./images/qty3.png' />
							<input type='hidden' value='3' />
						</li>
						<li>
							<img src='./images/qty4.png' />
							<input type='hidden' value='4' />
						</li>
						<li>
							<img src='./images/qty5.png' />
							<input type='hidden' value='5' />
						</li>
						<li>
							<img src='./images/qty6.png' />
							<input type='hidden' value='6' />
						</li>
						<li>
							<img src='./images/qty7.png' />
							<input type='hidden' value='7' />
						</li>
						<li>
							<img src='./images/qty8.png' />
							<input type='hidden' value='8' />
						</li>
						<li>
							<img src='./images/qty9.png' />
							<input type='hidden' value='9' />
						</li>
						<li>
							<img src='./images/qty10.png' />
							<input type='hidden' value='10' />
						</li>
					</ul/>
				</div>
			</li>";
	
		/*<div  class='slider'>
			<input type='range' min='1' max='100' step='1' id='inp_qty' name='inp_qty' value='".$inputvalues["inp_qty"]."'>
			</div>
		*/
		
		print "<li><label for='inp_Hours_Per_Day'>Hours per day</label>
				<div class= 'change_input'>
					<ul id='days_ul'>
							<input type='text' id='inp_Hours_Per_Day' name='inp_Hours_Per_Day' value='".$inputvalues["Hours_Per_Day"]."' onkeyup='MaxNumCheck(this, 24)'>
						<li>
							<img src='./images/qty1.png' />
							<input type='hidden' value='1' />
						</li>
						<li>
							<img src='./images/qty2.png' />
							<input type='hidden' value='2' />
						</li>
						<li>
							<img src='./images/qty3.png' />
							<input type='hidden' value='3' />
						</li>
						<li>
							<img src='./images/qty4.png' />
							<input type='hidden' value='4' />
						</li>
						<li>
							<img src='./images/qty5.png' />
							<input type='hidden' value='5' />
						</li>
						<li>
							<img src='./images/qty6.png' />
							<input type='hidden' value='6' />
						</li>
						<li>
							<img src='./images/qty7.png' />
							<input type='hidden' value='7' />
						</li>
						<li>
							<img src='./images/qty8.png' />
							<input type='hidden' value='8' />
						</li>
						<li>
							<img src='./images/qty9.png' />
							<input type='hidden' value='9' />
						</li>
						<li>
							<img src='./images/qty10.png' />
							<input type='hidden' value='10' />
						</li>
						<li>
							<img src='./images/qty11.png' />
							<input type='hidden' value='11' />
						</li>
						<li>
							<img src='./images/qty12.png' />
							<input type='hidden' value='12' />
						</li>
					</ul/>
				</div>		
			</li>";
        print "<li><label for='inp_Days_Per_Week'>Days per week: <span id='days_val'>".$inputvalues["Days_Per_Week"]."</span></label>
				<div class='ui-btn ui-showDefault ui-shadow ui-btn-corner-all ui-btn-inline ui-btn-up-b' id='days_div'>
					<span class='ui-btn-inner' onClick=\"toggle_visibility('days')\">
						<span class='ui-btn-text'>Change</span>
					</span>
				</div>
				<div id='days_qty'>
					<div class= 'change_input'>
						<ul id='days_ul'>
							<input type='text' id='inp_Days_Per_Week' name='inp_Days_Per_Week' value='".$inputvalues["Days_Per_Week"]."' onkeyup='MaxNumCheck(this, 7)'>												 							<li>
								<img src='./images/qty1.png' />
								<input type='hidden' value='1' />
							</li>
							<li>
								<img src='./images/qty2.png' />
								<input type='hidden' value='2' />
							</li>
							<li>
								<img src='./images/qty3.png' />
								<input type='hidden' value='3' />
							</li>
							<li>
								<img src='./images/qty4.png' />
								<input type='hidden' value='4' />
							</li>
							<li>
								<img src='./images/qty5.png' />
								<input type='hidden' value='5' />
							</li>
							<li>
								<img src='./images/qty6.png' />
								<input type='hidden' value='6' />
							</li>
							<li>
								<img src='./images/qty7.png' />
								<input type='hidden' value='7' />
							</li>
						</ul/>
					</div>		
				</div>	
		</li>";
	
		
        print "<li><label for='inp_Weeks_Per_Year'>Weeks per year: <span id='weeks_val'>".$inputvalues["Weeks_Per_Year"]."</span></label>
		<div class='ui-btn ui-showDefault ui-shadow ui-btn-corner-all ui-btn-inline ui-btn-up-b' id='weeks_div'>
					<span class='ui-btn-inner' onClick=\"toggle_visibility('weeks')\">
						<span class='ui-btn-text'>Change</span>
					</span>
				</div>
				<div id='weeks_qty'>
					<div class= 'change_input'>
						<ul id='days_ul'>
							<input type='text' id='inp_Weeks_Per_Year' name='inp_Weeks_Per_Year' value='".$inputvalues["Weeks_Per_Year"]."' onkeyup='MaxNumCheck(this, 52)'>
							<li>
								<img src='./images/qty42.png' />
								<input type='hidden' value='42' />
							</li>
							<li>
								<img src='./images/qty43.png' />
								<input type='hidden' value='43' />
							</li>
							<li>
								<img src='./images/qty44.png' />
								<input type='hidden' value='44' />
							</li>
							<li>
								<img src='./images/qty45.png' />
								<input type='hidden' value='45' />
							</li>
							<li>
								<img src='./images/qty46.png' />
								<input type='hidden' value='46' />
							</li>
							<li>
								<img src='./images/qty47.png' />
								<input type='hidden' value='47' />
							</li>
							<li>
								<img src='./images/qty48.png' />
								<input type='hidden' value='48' />
							</li>
							<li>
								<img src='./images/qty49.png' />
								<input type='hidden' value='49' />
							</li>
							<li>
								<img src='./images/qty50.png' />
								<input type='hidden' value='50' />
							</li>
							<li>
								<img src='./images/qty51.png' />
								<input type='hidden' value='51' />
							</li>
							<li>
								<img src='./images/qty52.png' />
								<input type='hidden' value='52' />
							</li>
						</ul/>
					</div>
			</div>
		</li>";
		
    }elseif ($product->RelatedTo == "Number Per Day") {
        $inputvalues["inp_room"] = LoadValueOrSetDefault($inputvalues["inp_room"], "Bathroom 1");
        $inputvalues["Old_Litre_measurement"] = LoadValueOrSetDefault($inputvalues["Old_Litre_measurement"], 20);
        $inputvalues["Average_Shower_Time"] = LoadValueOrSetDefault($inputvalues["Average_Shower_Time"], 15);
        $inputvalues["Number_of_uses_per_day_each"] = LoadValueOrSetDefault($inputvalues["Number_of_uses_per_day_each"], 1);

        print "<li id='withPopUp'>
				<label for='inp_room'>Room</label>
				<input type='text' id='inp_room' name='inp_room' placeholder='Bedroom, Bathroom etc' class='iteminput' value='".$inputvalues["inp_room"]."'>
				<div class='ui-btn ui-shadow ui-btn-corner-all ui-btn-inline ui-btn-icon-right ui-btn-up-b' onClick=\"PopupDiv('inp_room','".$inputvalues["inp_room"]."','WaterRoom')\">
					<span class='ui-btn-inner'>
						<span class='ui-btn-text'>Please Select Room</span>
						<span class='ui-icon ui-icon-arrow-r ui-icon-shadow'>&nbsp;</span>
					</span>
				</div>
			</li>";
		print "<li><label for='inp_qty'>Qty</label>
				<div class='slider'>
					<div class= 'change_input'>
						<ul id='water_qty_ul'>
							<input type='text' id='inp_qty' name='inp_qty' value='".$inputvalues["inp_qty"]."'>							
							<li>
								<img src='./images/qty1.png' />
								<input type='hidden' value='1' />
							</li>
							<li>
								<img src='./images/qty2.png' />
								<input type='hidden' value='2' />
							</li>
							<li>
								<img src='./images/qty3.png' />
								<input type='hidden' value='3' />
							</li>
							<li>
								<img src='./images/qty4.png' />
								<input type='hidden' value='4' />
							</li>
							<li>
								<img src='./images/qty5.png' />
								<input type='hidden' value='5' />
							</li>
							<li>
								<img src='./images/qty6.png' />
								<input type='hidden' value='6' />
							</li>
							<li>
								<img src='./images/qty7.png' />
								<input type='hidden' value='7' />
							</li>
							<li>
								<img src='./images/qty8.png' />
								<input type='hidden' value='8' />
							</li>
							<li>
								<img src='./images/qty9.png' />
								<input type='hidden' value='9' />
							</li>
							<li>
								<img src='./images/qty10.png' />
								<input type='hidden' value='10' />
							</li>
						</ul/>
				</div>			
			</div>		
		</li>";       
		
		print "<li><label for='inp_Old_Litre_measurement'>Old Litre measurement</label>";
        //print "<input type='text' id='inp_Old_Litre_measurement' name='inp_Old_Litre_measurement' value='".$inputvalues["Old_Litre_measurement"]."'>";
        PrintCalculator("inp_Old_Litre_measurement", "Enter litres/min", $inputvalues["Old_Litre_measurement"]);
        print "</li>";
		
        print "<li><label for='inp_Average_Shower_Time'>Average Shower Time: <span id='time_val'>".$inputvalues["Average_Shower_Time"]." min</span></label>
		
		<div class='ui-btn ui-showDefault ui-shadow ui-btn-corner-all ui-btn-inline ui-btn-up-b' id='time_div'>
					<span class='ui-btn-inner' onClick=\"toggle_visibility('time')\">
						<span class='ui-btn-text'>Change</span>
					</span>
				</div>
				<div id='time_qty'>					
					<div class= 'change_input'>
					<ul id='water_qty_ul'>
						<input type='text' id='inp_Average_Shower_Time' name='inp_Average_Shower_Time' value='".$inputvalues["Average_Shower_Time"]."' onkeyup='NumOnlyCheck(this, 15)'>
						<li>
							<img src='./images/qty5.png' />
							<input type='hidden' value='5' />
						</li>
						<li>
							<img src='./images/qty6.png' />
							<input type='hidden' value='6' />
						</li>
						<li>
							<img src='./images/qty7.png' />
							<input type='hidden' value='7' />
						</li>
						<li>
							<img src='./images/qty8.png' />
							<input type='hidden' value='8' />
						</li>
						<li>
							<img src='./images/qty9.png' />
							<input type='hidden' value='9' />
						</li>
						<li>
							<img src='./images/qty10.png' />
							<input type='hidden' value='10' />
						</li>
						<li>
							<img src='./images/qty11.png' />
							<input type='hidden' value='11' />
						</li>
						<li>
							<img src='./images/qty12.png' />
							<input type='hidden' value='12' />
						</li>
						<li>
							<img src='./images/qty13.png' />
							<input type='hidden' value='13' />
						</li>
						<li>
							<img src='./images/qty14.png' />
							<input type='hidden' value='14' />
						</li>
						<li>
							<img src='./images/qty15.png' />
							<input type='hidden' value='15' />
						</li>
					</ul/>
				</div>		
		</div>		
		</li>";
		
        print "<li>
		
		<label for='inp_Number_of_uses_per_day_each'>Number of uses per day each: <span id='uses_val'>".$inputvalues["Number_of_uses_per_day_each"]."</span></label>
				
		<div class='ui-btn ui-showDefault ui-shadow ui-btn-corner-all ui-btn-inline ui-btn-up-b' id='uses_div'>
					<span class='ui-btn-inner' onClick=\"toggle_visibility('uses')\">
						<span class='ui-btn-text'>Change</span>
					</span>
				</div>
				<div id='uses_qty'>
					<div class= 'change_input'>
					<ul id='water_qty_ul'>
						<input type='text' id='inp_Number_of_uses_per_day_each' name='inp_Number_of_uses_per_day_each' value='".$inputvalues["Number_of_uses_per_day_each"]."' onkeyup='NumOnlyCheck(this, 1)'>
							<li>
								<img src='./images/qty1.png' />
								<input type='hidden' value='1' />
							</li>
							<li>
								<img src='./images/qty2.png' />
								<input type='hidden' value='2' />
							</li>
							<li>
								<img src='./images/qty3.png' />
								<input type='hidden' value='3' />
							</li>
							<li>
								<img src='./images/qty4.png' />
								<input type='hidden' value='4' />
							</li>
							<li>
								<img src='./images/qty5.png' />
								<input type='hidden' value='5' />
							</li>
							<li>
								<img src='./images/qty6.png' />
								<input type='hidden' value='6' />
							</li>
							<li>
								<img src='./images/qty7.png' />
								<input type='hidden' value='7' />
							</li>
							<li>
								<img src='./images/qty8.png' />
								<input type='hidden' value='8' />
							</li>
							<li>
								<img src='./images/qty9.png' />
								<input type='hidden' value='9' />
							</li>
							<li>
								<img src='./images/qty10.png' />
								<input type='hidden' value='10' />
							</li>
					</ul/>
				</div>		
		</div>		
		</li>";        
    }
	
			?>
<script>
	$( ".change_input ul li" ).click(function() {
		var hiddenValue = $(this).find( ":hidden" ).val();
		var par = $(this).parent().attr('id');
		var changeInput = $(this).parent().find(':text');
		$(changeInput).val(hiddenValue);
		//alert(changeInput);
		//alert(par);
		
	});
</script>
			<?php 	
    //print "<li>";
    //print $product->RelatedTo;
    //print "</li>";
    print "<a href='selectproduct.php?oldid=".$oldid."&quoteitemid=".$quoteitemid."&quoteid=".$quoteid."' data-role='button' data-rel='dialog' data-inline='true' data-icon='back' data-iconpos='left' data-theme='a'>Back</a>";
    print "<a href='editquote.php?id=".$_SESSION["quoteid"]."' data-role='button' data-inline='true' data-icon='back' data-iconpos='left' data-theme='a'>Close</a>";
    print "<input type='button' value='save' data-inline='true' data-theme='a' data-icon='plus' data-iconpos='left' onclick='SaveProductInput(".$quoteid.", ".$quoteitemid.", ".$newid.", ".$oldid.")'>";
}

if (POST("step") == "SaveProductInput") {
    $quoteid = $_POST["quoteid"];
    $quoteitemid = $_POST["quoteitemid"];

    $quoteitem = new QuoteItem($quoteitemid);
    $quoteitem->QuoteID = $quoteid;
    $quoteitem->Room = POST("inp_room");
    $quoteitem->Qty = POST("inp_qty");
    //$quoteitem->TechID = POST("id");
    $quoteitem->NewProductID = POST("newid");
    $quoteitem->OldProductID = POST("oldid");
    $quoteitem->InputValues = NULL;
    foreach($_POST as $field=>$value) {
        if (substr($field, 0, strlen("inp_")) == "inp_") {
            $field = substr($field, strlen("inp_"));
            if ($field != "qty" && $field != "room") {
                $quoteitem->AddInputValue($field, $value);
            }
        }
    }
    if ($quoteitemid == 0) {
        $quoteitem->Save();
        $quoteitem->LoadData();
        $quoteitem->CalculateCostSavings();
        $quoteitemid = $quoteitem->QuoteItemID;
        LogDataChange($sysuser->id, "quote_items", "Added item to quote ".$quoteitem->QuoteID, NULL, $quoteitem);
    }else{
        $origquoteitems = new QuoteItem($quoteitem->QuoteItemID);
        $origquoteitems->CalculateCostSavings();
        $quoteitem->CalculateCostSavings();
        $quoteitem->Save();
        LogDataChange($sysuser->id, "quote_items", "Item updated on quote ".$quoteitem->QuoteID, $origquoteitems, $quoteitem);
    }
    $_SESSION["quoteitemid"] = $quoteitemid;
    //print "$('.ui-dialog').dialog('close');\n";
    print "document.location.href = 'editquote.php?id=".$quoteid."';\n";
    exit();
}

?>
<!DOCTYPE html> 
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable = no">
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<title>Ellies In-Store</title>
<link rel="shortcut icon" href="images/favicon.ico" />
<link rel="stylesheet" href="jmobile/jquery.mobile-1.3.0.min.css" />
<script src="js/jquery-1.8.2.min.js"></script>
<script src="js/jmobile.customize.js"></script>
<script src="jmobile/jquery.mobile-1.3.0.js"></script>
<script src="js/quotes.js"></script>
</head> 
<body>

<div data-role="page" id="page1">
<div data-role="content">
<?php 
print '<ul data-role="listview" data-divider-theme="a" data-ajax="false" data-rel="dialog" data-close-btn-text="close" data-inline="false">';
$datarelstr = " data-rel='dialog'";

if ($_GET["step"] == "EditItem") {
    $quoteitemid = $_GET["id"];
    $quoteitem = new QuoteItem($quoteitemid);
    PrintForm1($quoteitem->QuoteID, $quoteitem->QuoteItemID, $quoteitem->NewProductID, $quoteitem->OldProductID, $quoteitem->GetInputValues, $quoteitem->Qty);
    //exit();
}else{
    if (!empty($_GET["newid"])) {
        //===Input Form===
        PrintForm1($_GET["quoteid"], $_GET["quoteitemid"], $_GET["newid"], $_GET["oldid"]);
        //$backlink = "<br><a href='selectproduct.php?oldid=".$_GET["oldid"]."' data-role='button' data-rel='dialog' data-inline='true' data-icon='back' data-iconpos='left'>Back</a>";
        $backlink = "";
    }elseif (!empty($_GET["oldid"])) {
        //===New Products===
        $product_type = "";
        $newtechtype = "";
        $sql = "SELECT `product_type`, `tech_type` FROM `old_products` WHERE `id` = '".mysqli_real_escape_string($GLOBALS["link"],$_GET["oldid"])."' ORDER BY type_for_mobi_site, product_type, old_kwh";
        $sqlres = mysqli_query($GLOBALS["link"],$sql);
        while($row = mysqli_fetch_assoc($sqlres)) {
            $product_type = $row["product_type"];
            //$tech_type = $row["tech_type"];
        }
        
        $sql = "SELECT `new_products`.`id`, `new_products`.`product`, `new_products`.`new_technology_type`, `new_products`.`code` FROM `products_mapping`";
        $sql.= " INNER JOIN `new_products` ON `products_mapping`.`newid` = `new_products`.`id`";
        $sql.= " WHERE `products_mapping`.`oldid` = '".mysqli_real_escape_string($GLOBALS["link"],$_GET["oldid"])."'";
        $sql.= " AND NOT (`new_products`.`related_to` IS NULL)";
        
        
        $sql.= " ORDER BY `new_products`.`new_technology_type`, `new_products`.`product`";
        $sqlres = mysqli_query($GLOBALS["link"],$sql);
        //print "<li>".$sql."</li>";
        print "<li data-role='list-divider' role='heading'>Select Replacement Product</li>";
        while($row = mysqli_fetch_assoc($sqlres)) {
            if ($newtechtype != $row["new_technology_type"]) {
                $newtechtype = $row["new_technology_type"];
                //print "<li data-role='list-divider' role='heading'>".$newtechtype."</li>";
            }
            print "<li>";
            print "<a href='selectproduct.php?quoteitemid=".$_GET["quoteitemid"]."&quoteid=".$_GET["quoteid"]."&oldid=".$_GET["oldid"]."&newid=".$row["id"]."' data-rel='dialog'>";
            /*
            $tmpimage = "images/products/".strtolower($row["code"]).".jpg";
            if (file_exists($tmpimage)) {
                print "<img src='".$tmpimage."' border='0' class='ui-li-thumb'>";
            }else{
                print "<img src='images/products/noimage.jpg' border='0' class='ui-li-thumb'>";
            }
            */
            print $row["product"]."</a>";
            print "</li>";
        }
        $backlink = "<br><a href='selectproduct.php?tech_type=".$tech_type."&quoteitemid=".$_GET["quoteitemid"]."&quoteid=".$_GET["quoteid"]."' data-role='button' data-rel='dialog' data-inline='true' data-icon='back' data-iconpos='left' data-theme='a'>Back</a>";
        $backlink.= "<a href='editquote.php?id=".$_SESSION["quoteid"]."' data-role='button' data-inline='true' data-icon='back' data-iconpos='left' data-theme='a'>Close</a>";
    }elseif (!empty($_GET["tech_type"])) {
        //===Old Products===
        //$sql = "SELECT `id`, `product`, `product_type` FROM `old_products` WHERE `tech_type` = '".mysqli_real_escape_string($GLOBALS["link"],$_GET["tech_type"])."' ORDER BY `tech_type`";
        $sql = "SELECT `id`, `product`, `product_type` FROM `old_products` WHERE `id` IN (SELECT `oldid` FROM `products_mapping` WHERE `tech_type` = '".mysqli_real_escape_string($GLOBALS["link"],$_GET["tech_type"])."') ORDER BY `product`";
        
        $prevsub = "";
        //$sql = "SELECT `id`, `product`, `product_type` FROM `old_products` WHERE `tech_type` = '".mysqli_real_escape_string($GLOBALS["link"],$_GET["tech_type"])."' ORDER BY `tech_type`";
        $sql = "SELECT `id`, `product`, `product_type` FROM `old_products` WHERE `id` IN (SELECT `oldid` FROM `products_mapping` WHERE `tech_type` = '".mysqli_real_escape_string($GLOBALS["link"],$_GET["tech_type"])."') ORDER BY type_for_mobi_site, product_type, substring(product,1,3), old_kwh";
        
        
        $sqlres = mysqli_query($GLOBALS["link"],$sql);
        print "<li data-role='list-divider' role='heading'>Select Existing Product</li>";
        while($row = mysqli_fetch_assoc($sqlres)) {
            $subs = explode("-", $row["product"]);
            $icount = sizeof($subs);
            $subs = $subs[0];
            $subs = trim($subs);
            $product = $row["product"];
            if ($prevsub != $subs) {
                $prevsub = $subs;
                if ($icount > 1) {
                    //$items[] = array("url" => "", "label" => $subs, "jsfunc" => "", "divider" => true);
                    print "<li data-role='list-divider' role='heading'>".$subs."</li>";
                }
            }
            if ($icount > 1) {
                $product = substr($product, strpos($product, "-") + 1);
                $product = trim($product);
            }
            
            print "<li>";
            print "<a href='selectproduct.php?quoteitemid=".$_GET["quoteitemid"]."&quoteid=".$_GET["quoteid"]."&oldid=".$row["id"]."' data-rel='dialog'>".$product."</a>";
            print "</li>";
        }
        $backlink = "<br><a href='selectproduct.php?quoteitemid=".$_GET["quoteitemid"]."&quoteid=".$_GET["quoteid"]."' data-role='button' data-rel='dialog' data-inline='true' data-icon='back' data-iconpos='left' data-theme='a'>Back</a>";
        $backlink.= "<a href='editquote.php?id=".$_SESSION["quoteid"]."' data-role='button' data-inline='true' data-icon='back' data-iconpos='left' data-theme='a'>Close</a>";
    }else{
        //===Tech Types===
        $sql = "SELECT DISTINCT `tech_type` FROM `products_mapping` ORDER BY `tech_type`";
        $sqlres = mysqli_query($GLOBALS["link"],$sql);
        print "<li data-role='list-divider' role='heading'>Select Types</li>";
        while($row = mysqli_fetch_assoc($sqlres)) {
            print "<li>";
            print "<a href='selectproduct.php?quoteitemid=".$_GET["quoteitemid"]."&quoteid=".$_GET["quoteid"]."&tech_type=".$row["tech_type"]."' data-rel='dialog'>";
            /*
            $tmpimage = "images/products/".strtolower($row["tech_type"]).".jpg";
            if (file_exists($tmpimage)) {
                print "<img src='".$tmpimage."' border='0' class='ui-li-thumb'>";
            }            
            */
            print $row["tech_type"]."</a>";
            print "</li>";
        }
        if ($_GET["id"] == 0 && $_GET["quoteid"] > 0) {
            $_SESSION["quoteid"] = $_GET["quoteid"];
        }
        $backlink = "<br><a href='editquote.php?id=".$_SESSION["quoteid"]."' data-role='button' data-inline='true' data-icon='back' data-iconpos='left' data-theme='a'>Close</a>";
    }
}

print "</ul>";

print $backlink
?>
</div>
</div>


<!-- /page one -->
</body>
</html>