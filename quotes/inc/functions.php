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
//session_set_cookie_params(7200);
session_start();
setcookie(session_name(), session_id(), time() + 7200);

function SESSION($varname)
{
    if (!empty($_SESSION[$varname])) {
        return $_SESSION[$varname];
    } else {
        return "";
    }
}

function POST($varname)
{
    if (!empty($_POST[$varname])) {
        return $_POST[$varname];
    } else {
        return "";
    }
}

function GET($varname)
{
    if (!empty($_GET[$varname])) {
        return $_GET[$varname];
    } else {
        return "";
    }
}

function LoginCheck($redirect_url = "")
{
    if (empty($_SESSION["userid"])) {
        $reurl = "";
        if (!empty($redirect_url)) {
            $_SESSION["reurl"] = $redirect_url;
        }
        header("location: login.php");
    }
}

function PrintNavBar($title, $showback = true, $backpage = "index.php", $reurl = "")
{
    print "<table width='100%' height='265' border='0' cellpadding='0' cellspacing='0'>";
    print "<tr class='grassstrip'>";
    print "<td width='33%' valign='top'><img src='images/logo_m.jpg' border='0'></td>";
    print "<td align='center' class='headertexta'>Reduce Energy, Reduce Costs</td>";
    print "<td width='33%' valign='top' align='right'><img src='images/saving_icons_m.jpg' border='0'></td>";
    print "</tr>";
    print "<tr><td colspan='3' style='height:50px;'><div class='hbargreen'>";
    print $title . "</div></td></tr>";
    print "<tr><td>";
    if ($showback) {
        //  print "<a data-role='button' href='".$backpage."' data-icon='arrow-l' data-iconpos='left' class='ui-btn-left' data-ajax='false' data-inline='true' data-theme='a'>Back</a>";
    }
    print "</td><td>";
    print "&nbsp;";
    print "</td><td align='right'>";
    if (!empty($_SESSION["userid"])) {
        //print "<a data-role='button' href='logoff.php' data-icon='gear' data-iconpos='right' class='ui-btn-left' data-ajax='false' data-inline='true' data-theme='a'>Logoff</a>";
    }
    print "</td></tr>";
    print "</table>";
}


function PrintNavBar_old($title, $showback = true, $backpage = "index.php", $reurl = "")
{
    print "<div data-theme='a' data-role='header' data-position='fixed'>";
    // data-transition='slide'
    //data-transition='slide' data-direction='reverse'
    if ($showback) {
        //if (!empty($reurl)) {
        //print "<a data-role='button' href='../?module=Quotations&call=".$reurl."' data-icon='arrow-l' data-iconpos='left' class='ui-btn-left' data-ajax='false'>Back</a>";
        //}else{
        print "<a data-role='button' href='" . $backpage . "' data-icon='arrow-l' data-iconpos='left' class='ui-btn-left' data-ajax='false'>Back</a>";
        //}
    }
    print "<h3>";
    print $title;


    //print "</td></tr>";
    //print "</table>";
    print "</h3>";
    print "<div  class='ui-btn-right' style='padding-top:3px;'>";
    //print "<table border='0' cellpadding='0' cellspacing='0'><tr><td>";
    print "<a href='index.php'><img src='images/circle_e.png' border='0' width='30'></a>";
    //print "</td><td>";
    //print "&nbsp;&nbsp;</td><td style='font-size:18px;'>";
    //print "in-store";
    //print "</td></tr></table>";
    //print "<img src='images/circle_e.png' border='0'>";
    print "</div>";
    print "</div>";
}

function PrintTextBox($id, $label = "", $value = "", $placeholder = "", $readonly = false, $disabled = false, $additionalattributes = "")
{
    print "<div data-role='fieldcontain'>";
    print "<fieldset data-role='controlgroup' data-mini='true'>";
    print "<label for='" . $id . "'>";
    print "<noBr>" . $label . "</noBr>";
    print "</label><br>";
    $strreadonly = "";
    $strdisabled = "";
    if ($readonly) {
        $strreadonly = " readonly";
    }
    if ($disabled) {
        $strdisabled = " disabled";
    }
    if ($id == "inptfrm_Old_Litre_measurement") {
        print "<table border='0' cellspacing='0' cellpadding='0' id='inp-calc-form'><tr><td>";
        print "<input name='" . $id . "' id='" . $id . "' placeholder='" . $placeholder . "' value='" . $value . "' data-inline='true' data-mini='true' type='text'" . $strreadonly . $strdisabled . ">";
        print "</td><td>";
        print "<a href='#' onclick='ShowLitreCalc()' id='btnOpenCalculator' style='text-decoration:none;'>Open Calculator</a>";

        print "</td></tr></table>";
        print "<div id='inp-calculator' style='display:none;'>";
        print "<input name='inpcalc_container_ml' id='inpcalc_container_ml' placeholder='Size of Measuring Container (ml)' value='' data-inline='true' data-mini='true' type='text'>";
        print "<input name='inpcalc_filltime_sec' id='inpcalc_filltime_sec' placeholder='Time taken to fill (sec)' value='' data-inline='true' data-mini='true' type='text'>";
        print "<br><button data-inline='true' data-mini='true' data-theme='b' data-transition='none' onclick='CalculateLitreUsage()'>Calculate</button>";
        print "</div>";
    } else {
        print "<input name='" . $id . "' id='" . $id . "' placeholder='" . $placeholder . "' value='" . $value . "' data-inline='true' data-mini='true' type='text'" . $strreadonly . $strdisabled . ">";
    }
    print "</fieldset>";
    print "</div>";
}

function PrintRange($id, $label = "", $value = "", $min = "", $max = "", $step = "", $placeholder = "", $readonly = false, $disabled = false)
{
    print "<div data-role='fieldcontain'>";
    print "<fieldset data-role='controlgroup' data-mini='true'>";
    print "<label for='" . $id . "'>";
    print "<noBr>" . $label . "</noBr>";
    print "</label><br>";
    $strreadonly = "";
    $strdisabled = "";
    if ($readonly) {
        $strreadonly = " readonly";
    }
    if ($disabled) {
        $strdisabled = " disabled";
    }
    print "";
    print "<div class='rates' id='kwh'>
			
			<div class='rates_qty'>";
    if ($id == 'KWhPrice') {
        print"
				<ul>
					<div class= 'rates_input'>
						<input name='" . $id . "' id='" . $id . "' min='" . $min . "' max='" . $max . "' step='" . $step . "' placeholder='" . $placeholder . "' value='" . $value . "'
						  data-inline='true' data-mini='true' type='text' " . $strreadonly . $strdisabled . ">
					</div>
					<li>
						<img src='./images/rate07.png'/>
						<input type='hidden' value = '0.7' />
					</li>
					<li>
						<img src='./images/rate08.png' />
						<input type='hidden' value = '0.8' />
					</li>
					<li>
						<img src='./images/rate09.png' />
						<input type='hidden' value = '0.9' />
					</li>
					<li>
						<img src='./images/rate1.png' />
						<input type='hidden' value = '1.0' />
					</li>
					<li>
						<img src='./images/rate1_1.png' />
						<input type='hidden' value = '1.1' />
					</li>
					<li>
						<img src='./images/rate1_2.png' />
						<input type='hidden' value = '1.2' />
					</li>
					<li>
						<img src='./images/rate1_3.png' />
						<input type='hidden' value = '1.3' />
					</li>
					<li>
						<img src='./images/rate1_4.png' />
						<input type='hidden' value = '1.4' />
					</li>
					<li>
						<img src='./images/rate1_5.png' />
						<input type='hidden' value = '1.5' />
					</li>
				</ul>";
    } else if ($id == 'ElectricityEscalationRatePercentage') {
        print"
				<ul>
					<div class= 'rates_input'>
						<input name='" . $id . "' id='" . $id . "' min='" . $min . "' max='" . $max . "' step='" . $step . "' placeholder='" . $placeholder . "' value='" . $value . "'
						  data-inline='true' data-mini='true' type='text' " . $strreadonly . $strdisabled . ">
					</div>
					<li>
							<img src='./images/rate8.png' />
							<input type='hidden' value='8' />
						</li>
						<li>
							<img src='./images/rate9.png' />
							<input type='hidden' value='9' />
						</li>
						<li>
							<img src='./images/rate10.png' />
							<input type='hidden' value='10' />
						</li>
						<li>
							<img src='./images/rate11.png' />
							<input type='hidden' value='11' />
						</li>
						<li>
							<img src='./images/rate12.png' />
							<input type='hidden' value='12' />
						</li>
						<li>
							<img src='./images/rate13.png' />
							<input type='hidden' value='13' />
						</li>
						<li>
							<img src='./images/rate14.png' />
							<input type='hidden' value='14' />
						</li>
						<li>
							<img src='./images/rate15.png' />
							<input type='hidden' value='15' />
						</li>
						<li>
							<img src='./images/rate16.png' />
							<input type='hidden' value='16' />
						</li>
						<li>
							<img src='./images/rate17.png' />
							<input type='hidden' value='17' />
						</li>
						<li>
							<img src='./images/rate18.png' />
							<input type='hidden' value='18' />
						</li>
				</ul>";

    }

    print"</div>
		</div>";
    print "</fieldset>";
    print "</div>";
}


function PrintTextArea($id, $label = "", $value = "", $placeholder = "", $readonly = false, $disabled = false)
{
    print "<div data-role='fieldcontain'>";
    print "<fieldset data-role='controlgroup' data-mini='true'>";
    print "<label for='" . $id . "'>";
    print "<noBr>" . $label . "</noBr>";
    print "</label><br>";
    $strreadonly = "";
    $strdisabled = "";
    if ($readonly) {
        $strreadonly = " readonly";
    }
    if ($disabled) {
        $strdisabled = " disabled";
    }
    print "<textarea name='" . $id . "' id='" . $id . "' placeholder='" . $placeholder . "'" . $strreadonly . $strdisabled . ">" . $value . "</textarea>";
    //<textarea name="" id="property" placeholder="Enter property details" data-mini="true"></textarea>
    print "</fieldset>";
    print "</div>";
}

function PrintPasswordBox($id, $label = "")
{
    print "<div data-role='fieldcontain'>";
    print "<fieldset data-role='controlgroup' data-mini='true'>";
    print "<label for='" . $id . "'>";
    print "<noBr>" . $label . "</noBr>";
    print "</label><br>";
    print "<input name='" . $id . "' id='" . $id . "' type='password'>";
    print "</fieldset>";
    print "</div>";
}

function PrintLabel($id, $label = "", $value = "", $placeholder = "", $readonly = false, $disabled = false)
{
    print "<div data-role='fieldcontain'>";
    print "<fieldset data-role='controlgroup' data-mini='true'>";
    print "<label for='" . $id . "'>";
    print "<noBr>" . $label . "</noBr>";
    print "</label><br>";
    if (empty($value)) {
        $value = "N/A";
    }
    print $value;
    print "</fieldset>";
    print "</div>";
}

function PrintInfoField($id, $label = "", $value = "", $block1column = "a", $block2column = "b")
{
    print "<div class='ui-block-" . $block1column . "'><div class='ui-body ui-body-a'>";
    print $label;
    print "</div></div>";

    print "<div class='ui-block-" . $block2column . "'><div class='ui-body ui-body-c'>";
    //print "<span id='".$id."'>".$value."</span>";
    print $value;
    print "&nbsp;</div></div>";
}


function PrintDBResults($sql, $idfield = "", $editlink, $customcolumns = NULL, $extrarowfunction = "")
{
    $sqlres = mysqli_query($GLOBALS["link"],$sql);
    $icount = -1;
    print "<table border='1' cellpadding='3' cellspacing='0' width='100%'>";
    while ($row = mysqli_fetch_assoc($sqlres)) {
        $icount += 1;
        if ($icount == 0) {
            print "<tr>";
            if (!empty($editlink)) {
                print "<th class='ui-body-a' width='120'>&nbsp;</th>";
            }
            foreach ($row as $field => $value) {
                if ($field != $idfield && substr($field, 0, 7) != "hidden_") {
                    print "<th class='ui-body-a'>";
                    print $field;
                    print "</th>";
                }
            }
            if (!empty($customcolumns)) {
                if (is_array($customcolumns)) {
                    foreach ($customcolumns as $column) {
                        print "<th class='ui-body-a'>";
                        print $column["caption"];
                        print "</th>";
                    }
                }
            }
            print "</tr>";
        }
        $colspan = 0;
        print "<tr>";
        if (!empty($editlink)) {
            print "<td class='ui-body-c'>";
            eval($editlink . ";");
            print "</td>";
            $colspan += 1;
        }
        $icol = 0;
        foreach ($row as $field => $value) {
            if ($field != $idfield && substr($field, 0, 7) != "hidden_") {
                $icol += 1;
                print "<td class='ui-body-c'>";
                print $value;
                print "</td>";
                $colspan += 1;
            }
        }
        if (!empty($customcolumns)) {
            if (is_array($customcolumns)) {
                foreach ($customcolumns as $column) {
                    print "<td class='ui-body-c'>";
                    eval($column["command"] . ";");
                    print "</td>";
                    $colspan += 1;
                }
            }
        }
        print "</tr>";

        if (!empty($extrarowfunction)) {
            print "<tr><td colspan='" . $colspan . "'>";
            eval($extrarowfunction . ";");
            print "</td></tr>";
        }
    }
    print "</table>";
}

function PrintDBResultsGrid($sql)
{
    $sqlres = mysqli_query($GLOBALS["link"],$sql);
    $icount = -1;
    print "<div class='ui-grid-d ui-responsive'>";
    while ($row = mysqli_fetch_assoc($sqlres)) {
        $icount += 1;
        if ($icount == 0) {
            $icol = 0;
            foreach ($row as $field => $value) {
                $icol += 1;
                print "<div class='ui-block-" . GetLetter($icol) . "'><div class='ui-body ui-body-a'>" . $field . "</div></div>";
            }
        }
        $icol = 0;
        foreach ($row as $field => $value) {
            $icol += 1;
            print "<div class='ui-block-" . GetLetter($icol) . "'><div class='ui-body ui-body-c'>" . $value . "</div></div>";
        }
    }
    print "</div>";
}

function GetLetter($number)
{
    $arrdata[1] = "a";
    $arrdata[2] = "b";
    $arrdata[3] = "c";
    $arrdata[4] = "d";
    $arrdata[5] = "e";
    return $arrdata[$number];
}

function DebugPrint($obj)
{
    print "<pre>";
    print_r($obj);
    print "</pre>";
}

function PrintTableFromAssoc($assocarray)
{
    $icount = -1;
    print "<table border='1' cellpadding='3' cellspacing='0' width='100%'>";
    foreach ($assocarray as $row) {
        $icount += 1;
        if ($icount == 0) {
            print "<tr>";
            foreach ($row as $field => $value) {
                print "<th>";
                print $field;
                print "</th>";
            }
            print "</tr>";
        }
        print "<tr>";
        foreach ($row as $field => $value) {
            if (empty($value)) {
                $value = "&nbsp;";
            }
            print "<td>";
            print $value;
            print "</td>";
        }
        print "</tr>";
    }
    print "</table>";
}

function csv_to_array($filename = '', $delimiter = ',', $normalizeheaderrow = false)
{
    if (!file_exists($filename) || !is_readable($filename))
        return FALSE;

    $header = NULL;
    $data = array();
    if (($handle = fopen($filename, 'r')) !== FALSE) {
        while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
            //print_r($row);
            //print "<br/><br/>";
            if (!$header) {
                $header = $row;
                if ($normalizeheaderrow) {
                    foreach ($header as $col) {
                        $col = strtolower(str_replace(" ", "_", $col));
                        //print "header: ".$col."<br/>";
                        $theader[] = $col;
                    }
                    $header = $theader;
                }
            } else {
                foreach ($row as $key => $value) {
                    //if ($filename == "temp/New_Product_Tables.csv")
                    //print "key ".$key. " value: ".$value."<br/>";
                    //,Yes,
                    //$value = str_replace(",Yes,", "1", $value);
                    //$value = str_replace(",No,", "0", $value);
                    //$value = "orange";

                    if (strtolower($value) == "yes") {
                        $value = "1";
                    }
                    if (strtolower($value) == "no") {
                        $value = "0";
                    }
                    $trow[$key] = $value;
                }
                $data[] = array_combine($theader, $trow);
            }
        }
        fclose($handle);
    }
    if ($filename == "temp/New_Product_Tables.csv") {
        //print (count($header));
        //print_r($header);
        //print (count($data));
    }

    /*
        $rows = array_map('str_getcsv', file($filename));
        $header = array_shift($rows);
        $csv = array();
        foreach ($rows as $row)
        {
          $csv[] = array_combine($header, $row);
        }
        if($filename == "temp/New_Product_Tables.csv")
        {
        /*print "*****";
        print (count($header));
        print_r($header);
        print (count($data));
        print_r($csv);
        print "**";*/
    return $data;
}


function GetLoggedInUserID()
{
    $userid = 0;
    $sql = "SELECT * FROM `system_users` WHERE `username` = \"" . mysqli_real_escape_string($GLOBALS["link"],$_SESSION["userid"]) . "\"";
    $sqlres = mysqli_query($GLOBALS["link"],$sql);
    while ($row = mysqli_fetch_assoc($sqlres)) {
        $userid = $row["id"];
    }
    return $userid;
}

function PrintList($heading, $listitems, $openindialog = false)
{
    print "<ul data-role='listview' data-divider-theme='a'";
    if (!$openindialog) {
        print " data-inset='true'";
    } else {
        print " data-close-btn-text='Close'";
    }
    print ">";
    print "<li data-role='list-divider' role='heading'>" . $heading . "</li>";
    foreach ($listitems as $item) {
        if ($item["divider"]) {
            print "<li data-role='list-divider' role='heading'>" . $item["label"] . "</li>";
        } else {
            print "<li>";
            $tmpimage = strtolower($item["label"]) . "jpg";
            if (file_exists("../images/products/" . $tmpimage)) {
                print "<img src='" . $tmpimage . "' border='0'>";
            }
            print "<a href='" . $item["url"] . "'";
            if ($openindialog) {
                print " data-rel='dialog'";
                print " data-close-btn-text='Close'";
            } else {
                print " data-ajax='false'";
            }
            print ">" . $item["label"] . "</a>";
            print "</li>";
        }
    }
    print "</ul>";
}

function CalculatePayback($kwh_price, $escalaction_rate, $price_of_sales, $kwh_saved, $debug_print = false)
{
    /*
    See PaybackCalculator_v2.ods for details and testing of the formula/process for payback period
    */
    try{
        $B1 = $kwh_price;
        $B2 = $escalaction_rate;

        if ($B2 > 0) {
            $B2 = $B2 / 100; //Convert to a fraction Excel does this automatically with percentages
        }
        $B3 = $kwh_saved; //VLOOKUP(B37,Calculation_Table,MATCH("Kwh Saved",Calculation_Table1,0),0)
        $B4 = $price_of_sales;

           ///echo $B3 .":". $B4;exit();
         if($B4 <=0 || $B2 <=0 || $B3 <=0 || $B1 <=0){
             $A8 = 0;
        }else{
             $A8 = (($B4 * $B2) / (12 * $B1 * $B3));
        }

        $A8 = log10(1 + $A8) / log10(1 + $B2);//LOG(1+((B4 * B2)/(12 * B1 * B3 ))) / LOG(1+B2)
        $B8 = (12 * $B1 * $B3) * ((pow(1 + $B2, intval($A8)) - 1) / $B2); //=(12*B1*B3)*((POWER(1+B2,INT(A8))-1)/B2)
        $C8 = $B4;
        $D8 = $C8 - $B8;

        if($B1 <=0 || $B3 <=0){
             $E8 = 0;
        }else{
             $E8 = ($B1 * $B3 * pow(1 + $B2, intval($A8)));
        }
        if($E8 <=0){
           $E8 = 0;
       }else{
           $E8 = $D8 / $E8; //=D8/(B1*B3*POWER(1+B2,INT(A8)))
       }


        $F8 = number_format(12 * intval($A8) + $E8, 7); //=ROUND(12*INT(A8)+E8,7)
        if ($F8 < 0 || $F8 == 0) {
            $F8 = "No Payback";
        } else {
            $F8 = number_format($F8, 2);
        }
        if ($debug_print) {
            print "<br>B1: " . $B1;
            print "<br>B2: " . $B2;
            print "<br>B3: " . $B3;
            print "<br>B4: " . $B4;
            print "<br>A8: " . $A8;
            print "<br>B8: " . $B8;
            print "<br>C8: " . $C8;
            print "<br>D8: " . $D8;
            print "<br>E8: " . $E8;
            print "<br>F8: " . $F8;
        }
        return $F8;
    }catch (Exception $e){
    	print "No payback<br/>";
    	return 0;
    }
}

function LogDataChange($userid, $recordtype, $action, $oldrecorddata, $newrecorddata)
{
    /*
    CREATE TABLE `user_data_log` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `dts` datetime NOT NULL,
      `user_id` int(6) NOT NULL DEFAULT '0',
      `record_type` varchar(100) NOT NULL,
      `action` varchar(100) NOT NULL,
      `original_data` text DEFAULT NULL,
      `changed_data` text DEFAULT NULL,
      `description` varchar(255) NOT NULL,
      `ip_address` varchar(25) NOT NULL,
      PRIMARY KEY (`id`),
      KEY `user_id` (`user_id`),
      KEY `record_type` (`record_type`),
      KEY `action` (`action`)
    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1 COMMENT='user quote changes log';
    */
    $oldrecorddata = json_encode($oldrecorddata);
    $newrecorddata = json_encode($newrecorddata);
    $sql = "INSERT INTO `user_data_log` (`dts`, `user_id`, `record_type`, `action`, `original_data`, `changed_data`, `ip_address`)";
    $sql .= " VALUES (NOW(), '" . mysqli_real_escape_string($GLOBALS["link"],$userid) . "', '" . mysqli_real_escape_string($GLOBALS["link"],$recordtype) . "', '" . mysqli_real_escape_string($GLOBALS["link"],$action) . "', '" . mysqli_real_escape_string($GLOBALS["link"],$oldrecorddata) . "', '" . mysqli_real_escape_string($GLOBALS["link"],$newrecorddata) . "', '" . mysqli_real_escape_string($GLOBALS["link"],$_SERVER["REMOTE_ADDR"]) . "')";
    //print $sql;
    mysqli_query($GLOBALS["link"],$sql);
}

function SendEmail($recipientemail, $recipientname, $subject, $message, $attachments = NULL)
{
    $mail = new PHPMailer(); // defaults to using php "mail()"
    $mail->IsSMTP();
    //$mail->Host = "smtp.saix.net";
    $mail->Host = SMTP_Host;
    $mail->SMTPAuth = false;

    //$body             = file_get_contents('contents.html');
    //$body             = eregi_replace("[\]",'',$body);

    //===tradepage@pholacoaches.co.za is the firewall friendly address given to us by Phola Coaches IT/Mail hosting company===
    //$mail->AddReplyTo("noreply@tradepage.co.za","Do Not Reply");
    //
    //$mail->SetFrom("support@tradepage.co.za","Do Not Reply");
    $mail->Helo = "heyyyyyy";
    $mail->SetFrom("support@tradepage.co.za", "Do Not Reply");
    $mail->AddAddress($recipientemail, $recipientname);

    $mail->AddBCC("ari.salkow@ellies.co.za", "Ari Salkow");
    //$mail->AddBCC("donavan.cook@tradepage.net", "Donavan Cook");

    $mail->Subject = $subject;
    $mail->AltBody = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
    $mail->MsgHTML($message);

    if (!empty($attachments)) {
        if (is_array($attachments)) {
            foreach ($attachments as $attachment) {
                $mail->AddAttachment($attachment);
            }
        } else {
            $mail->AddAttachment($attachments);
        }
    }

    $mail->Send();

    if (!empty($mail->ErrorInfo)) {
        //echo "Mailer Error: " . $mail->ErrorInfo;
        $mailsent = $mail->ErrorInfo;
    } else {
        $mailsent = true;
    }

    //print "<pre>";
    //print_r($mail);
    //print "</pre>";
    return $mailsent;
}


//$_SESSION["quoteitemid"] = 3;
//$val = GetInputValue($_SESSION["quoteitemid"], "Daily");
//print $val;
//exit();
?>