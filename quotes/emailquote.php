<?php 

session_start();
error_reporting(E_ALL);
require_once("inc/config.php");
require_once("inc/customer.class.php");
require_once("inc/quote.class.php");
require_once("inc/quoteitem.class.php");
require_once("inc/techtype.class.php");
require_once("inc/printtechitem.class.php");
require_once("inc/printproduct.class.php");
require_once("inc/printfunctions.php");

require_once("inc/class.phpmailer.php");
require_once("../inc/system_user.php");
require_once("../inc/functions.php");

require_once("inc/MPDF56/mpdf.php");

if (!isset($_GET["app"])){
    if (isset($_GET["bp"])){
        if ($_GET["bp"]!="qwepoiasdlkjzxcmnb"){
            
            get_session();
            if (!is_authenticated()) {
                if (isset($_POST['aj']) && $_POST['aj'] == 1) {
                    print "\n<!-- scripts , code below be eval()ed by javascript -->\nalert('You have been signed out! Please relogin');";
                } else {
                    die("Not logged in");
                    exit(1);
                }
            }
        }
    }
}

$quoteid = $_GET["id"];

$booktitle = "Edit Quote";

function PrintNavBar($title, $showback = true, $backpage = "index.php", $reurl = "") {
    print "<table width='100%' height='265' border='0' cellpadding='0' cellspacing='0'>";
    print "<tr class='grassstrip'>";
    print "<td width='33%' valign='top'><img src='images/logo_m.jpg' border='0'></td>";
    print "<td align='center' class='headertexta'>Reduce Energy, Reduce Costs</td>";
    print "<td width='33%' valign='top' align='right'><img src='images/saving_icons_m.jpg' border='0'></td>";
    print "</tr>";
    print "<tr><td colspan='3' style='height:50px;'><div class='hbargreen'>";
    print $title."</div></td></tr>";
    print "<tr><td>";
    if ($showback) {
        print "<a data-role='button' href='".$backpage."' data-icon='arrow-l' data-iconpos='left' class='ui-btn-left' data-ajax='false' data-inline='true' data-theme='a'>Back</a>";
    }    
    print "</td><td>";
    print "&nbsp;";
    print "</td><td align='right'>";
    if (!empty($_SESSION["userid"])) {
        print "<a data-role='button' href='logoff.php' data-icon='gear' data-iconpos='right' class='ui-btn-left' data-ajax='false' data-inline='true' data-theme='a'>Logoff</a>";
    }
    print "</td></tr>";
    print "</table>";
}

function SendEmail($recipientemail, $recipientname, $subject, $message, $attachments = NULL) {
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
    $mail->SetFrom("support@tradepage.co.za","Do Not Reply");
    $mail->AddAddress($recipientemail, $recipientname);

    //$mail->AddBCC("ari.salkow@ellies.co.za", "Ari Salkow");
    //$mail->AddBCC("donavan.cook@tradepage.net", "Donavan Cook");

    $mail->Subject    = $subject;
    $mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
    $mail->MsgHTML($message);
    
    if (!empty($attachments)) {
        if (is_array($attachments)) {
            foreach($attachments as $attachment) {
                $mail->AddAttachment($attachment);
            }
        }else{
            $mail->AddAttachment($attachments);
        }
    }

    $mail->Send();
    
    if(!empty($mail->ErrorInfo)) {
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

$previewmode = false;
if (!empty($_GET["preview"])) {
	if ($_GET["preview"] == "1") {
		$previewmode = true;
	}
}

$quote = new Quote($quoteid);

$customerid = 0;

if ($quoteid == "0") {
    $quote->CustomerID = $_GET["customerid"];
    $quote->Save();
}
$customerid = $quote->CustomerID;

$isnew = false;
$customer = new Customer($quote->CustomerID);
//--------------------------------------------------------------------------------------
//Create new PDF document
$mpdf=new mPDF(); 
$mpdf->list_indent_first_level = 0;
$stylesheet .= file_get_contents('css/printsheet.css');
$mpdf->WriteHTML($stylesheet,1);

// set document information
$mpdf->SetCreator(PDF_CREATOR);
$mpdf->SetAuthor('Theo Malongete');
$mpdf->SetTitle('Ellies Renewable Energy.');
$mpdf->SetSubject('Ellies Renewable Energy.');
$mpdf->SetKeywords('Ellies, Renewable, Energy');

//set auto page breaks
$mpdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
// ------------------First Page----------------------------------------
// add a page
$mpdf->AddPage();
$title = "QUOTATION";
$mpdf->SetFont("Helvetica", 'B', 12);
$mpdf->SetTextColor(140, 197, 62);
$mpdf->MultiCell(180, 3,$title,'', 'R','', 0,'25', '5');


//Customer details
$customerid = 0;
if ($quoteid == "0") {
    $quote->CustomerID = $_GET["customerid"];
    $quote->Save();
}
$customerid = $quote->CustomerID;
$customer = new Customer($quote->CustomerID);
$companyName = $customer->CompanyName;
if(!empty($companyName)){
    $companyName = $customer->CompanyName;
}else{
    $companyName = $customer->Name." ".$customer->Surname; 
}

$mpdf->SetFont("Helvetica", 'B', 15);
$title = $companyName;
$mpdf->MultiCell(180, 3,$title,'', 'L','', 0,'25', '10');

//Front page
$image_file ='pdf/images/front.jpg';
$mpdf->Image($image_file, 25, 30, 150, '', 'JPG', '', 'T', '',false, false, false, false, false);
//-------------------------------Second Page-----------------------------------------------------------------
$mpdf->AddPage();
$mpdf->SetHTMLFooter('
<table width="100%" style="vertical-align: bottom; font-family: serif; font-size: 7pt; color: #000000; font-weight: bold; font-style: italic;">
    <tr>
        <td width="33%"><span style="font-weight: bold; font-style: italic;">Ellies Renewable Energy<br/>Lighting Solutions</span></td>
        <td width="33%" align="center" style="font-weight: bold; font-style: italic;"></td>
        <td width="33%" style="text-align: right; ">Page {PAGENO}/{nbpg}</td>
    </tr>
</table>');
$html = "";
//$html .= '<div style="float:left; width:200px;"><img src="images/logo.png" width="200px"/></div>';
$html .= '<div style="float:left; width:300px; border-radius:15px;	border:0.5px solid #70aa00;">
<table id="info_table">';
$html .= '<tr>';
    $html .= '<td align="center" valign="middle"><img src="images/ellies_icon_pdf.png" width="20px" /></td>';
    $html .= '<td align="left" class="heading">Electricity Escalation Rate:</td>';  
    $html .= '<td align="left" class="quote_vals" valign="middle">'.$quote->ElectricityEscalationRatePercentage.'</td>';
$html .= '</tr>';
$html .= '<tr>';
    $html .= '<td align="center" valign="middle"><img src="images/ellies_icon_pdf.png" width="20px"/></td>';        
    $html .= '<td align="left" class="heading">Quote Reference:</td>';
    $html .= '<td align="left" class="quote_vals" valign="middle">'.$quote->QuoteReferenceNo.'</td>';
$html .= '</tr>';
$html .= '<tr>';
    $html .= '<td align="center" valign="middle"><img src="images/ellies_icon_pdf.png" width="20px"/></td>';
    $html .= '<td align="left" class="heading">Price/kWh R:</td>';
    $html .= '<td align="left" class="quote_vals" valign="middle">'.$quote->KWhPrice.'</td>';
$html .= '</tr>';

if ($quote->Discount>0){
    $html .= '<tr>';
        $html .= '<td align="center" valign="middle"><img src="images/ellies_icon_pdf.png" width="20px"/></td>';
        $html .= '<td align="left" class="heading">Discount:</td>';
        $html .= '<td align="left" class="quote_vals" valign="middle">'.$quote->Discount.'%</td>';
    $html .= '</tr>';
}
$html .= '<tr>';
    $html .= '<td align="center" valign="middle"><img src="images/ellies_icon_pdf.png" width="20px"/></td>';        
    $html .= '<td align="left" class="heading">Date:</td>';
    $html .= '<td align="left" class="quote_vals" valign="middle">'.$quote->DateCreated.'</td>';
$html .= '</tr>';

$html .= '</table></div>';
$html .= '<div style="float:right; width:300px; border-radius:15px;	border:0.5px solid #70aa00; margin-top:0px;">
<table align="left" id="customer_table">';

$html.='
<tr>
<td  align="center" valign="middle" class="customer_icon" valign="middle"><img src="images/name_icon_pdf.png" width="20px"/></td>
<td align="left" class="heading">Customer:</td>
<td align="left" class="quote_vals" valign="middle">'.$customer->Name." ".$customer->Surname.'</td>
</tr>
<tr>
<td  align="center" valign="middle" class="customer_icon" valign="middle"><img src="images/phone_icon_pdf.png" width="20px"/></td>
<td align="left" class="heading">Cellphone:</td>
<td align="left" class="quote_vals" valign="middle">'.$customer->CellPhone.'</td>
</tr>
<tr>
<td  align="center" valign="middle" class="customer_icon" valign="middle"><img src="images/email_icon_pdf.png" width="20px"/></td>
<td align="left" class="heading">Email:</td>
<td align="left" class="quote_vals" valign="middle">'.$customer->Email.'</td>
</tr>';
$html .= '</table><br/><br/></div>';
$html .= '<div style="font-weight:bold; font-size:13px; clear:both"><br/>Dear '.$customer->Name.' '.$customer->Surname.',<br/><br/>
Thank you for giving Ellies the opportunity to give you a customised energy audit and therefore help you save money. Below are the estimated savings and details of our customised solution for you.'; 

//Note that the summary below ';
//$html .= $quote->DoInstall == 1 ? 'includes installation':'excludes installation';

$html .='</div>';  
//-------------------------------------------
$quote->CalculateCostSavingTotals();
$savings = $quote->Get5YearSavings();
$ttl_savings = end($savings["Cumulative Savings"]);
$ttl_savings_time = end(array_keys($savings["Price per kWh"]));
//end get year num
/*--------Total Overview------*/
if ($quote->RentalTerm==0){
    $html .= '<h2 class="icon_heading">Cost Overview</h2>';
}else {
    $html .= '<h2 class="icon_heading">Contract Overview</h2>';
}
$html .= '<table id="overview">';
$html .= '<tr><td class="total_icon"><div class="inner_icon"><img src="images/calendar_icon2.png" width="150px" style="padding-bottom:20px"/>';
$html .= '<div class="icon_description" >Payback Period</div>';
$html .= '<h2>'.$quote->TotalPaybackPeriodFormatted.'</h2></div></td>';
$html .= '<td class="total_icon"><div class="inner_icon"><img src="images/plus_icon2.png" width="150px" style="padding-bottom:20px" />';
$html .= '<div class="icon_description">Monthly Saved</div>';
$html .= '<h2>R'.number_format($quote->MonthlyCostSaving, 2).'</h2></div></td>';

if ($quote->RentalTerm==0){
    $html .= '<td class="total_icon"><div class="inner_icon"><img src="images/percentage_icon2.png" width="150px" style="padding-bottom:20px" />';
    $html .= '<div class="icon_description">Percentage Saved</div>';
    $html .= '<h2>'.number_format($quote->KWhSavedPerc, 0).'%</h2></div></td>';
    $html .= '<td class="total_icon"><div class="inner_icon"><img src="images/monthly_payment_icon.png" width="150px" style="padding-bottom:20px" />';
    $html .= '<div class="icon_description">Savings after '.$ttl_savings_time.' years</div>';
    $html .= '<h2>'.$ttl_savings.'</h2></div></div>';
    $html .= '<td class="total_icon"><div class="inner_icon"><img src="images/tick_icon2.png" width="150px" style="padding-bottom:20px" />';
    $html .= '<div class="icon_description">Cost</div>';
    $html .= '<h2>R'.number_format($quote->ReplacementPrice, 2).'</h2></div></td>';
} else {
    $html .= '<td class="total_icon"><div class="inner_icon"><img src="images/calendar_icon2.png" width="150px" style="padding-bottom:20px" />';
    $html .= '<div class="icon_description">Rental Term</div>';
    $html .= '<h2>'.number_format($quote->RentalTerm, 0).' Months</h2></div></td>';
    $html .= '<td class="total_icon"><div class="inner_icon"><img src="images/deposit_icon.png" width="150px" style="padding-bottom:20px" />';
    $html .= '<div class="icon_description">Deposit</div>';
    $html .= '<h2>R'.number_format($quote->getDeposit("all"), 2).'</h2></div></td>';
    $html .= '<td class="total_icon"><div class="inner_icon"><img src="images/monthly_payment_icon.png" width="150px" style="padding-bottom:20px" />';

    $html .= '<div class="icon_description">Monthly Rental by year</div>';
    $html .=  '<h2>';
		$termyears = ceil($quote->RentalTerm/12);
	$html .= '1: R'.number_format($quote->getRentalAmount("all",$quote->RentalTerm, $quote->MaintenancePercentage), 2).'<br/>';
	for ($terms = 1; $terms<$termyears; $terms++){
		$html .= ($terms+1).': R'.number_format(($quote->getRentInMonth("all",$quote->RentalTerm,$terms*12,$quote->MaintenancePercentage)), 2).'<br/>';
	}
	$html .=  '</h2></div></div>';
    $html .= '<td class="total_icon"><div class="inner_icon"><img src="images/percentage_icon2.png" width="150px" style="padding-bottom:20px" />';
    $html .= '<div class="icon_description">Return</div>';
    $html .= '<h2>'.number_format((pow(1+$quote->getReturn($quote->RentalTerm, $quote->MaintenancePercentage),12)-1)*100, 2).'%</h2></div></div>';
}
$html .= '</table>';
/*--------Total Savings------*/
$irow = 0;
$keyYear = array_keys($savings["Price per kWh"]);
$headings = array_keys($savings);

/*get last year number */
$yearNumber = end($keyYear);
$html .= '<div id="savings_menu">';
$html .= '<div class="title_heading" style="padding-bottom:15px"><div class="inner_heading" onclick="toggle_div(this,\'savings_cont\',\'savings_icon\');">Projected Savings</div></div>';
$html .= '<div id="savings_cont" class="content_w"> ';
$html .= '<div class="content"> ';
$html .= '<div id="savings">';
$html .= '<div id="savings_label" class="savings_icon">';
$html .= '<div class="inner_icon">';
$html .= '<div class="numberCircle" style="margin-bottom:17px;"><h2>Year</h2></div>';
$html .= '<p>Price per kWh</p>';
$html .= '<p>Cumulative Savings</p>';
$html .= '<p>Annual Savings</p>';
$html .= '<p>Monthly Savings</p>';
$html .= '</div>';
$html .= '</div>';

for ($i = 0; $i < 5; $i ++)
{
	$html .= '<div class="savings_icon">';
	$html .= '<div class="inner_icon">';
	$html .= '<div class="numberCircle" style="margin-bottom:17px; " id = "savings_year'.($i+1).'"><h2>'.$keyYear[$i].'</h2></div>';
	
	foreach($headings as $key)
	{
		$curYear = $keyYear[$i];
		$val = $savings[$key][$curYear];
		$html .= '<div style="font-size:10px; padding-bottom:5px;">'.$val.'</div>';
	}
	$html .= '</div>';
	$html .= '</div>';
} 
$html .= '</div>';
$html .= '</div>';
$html .= '</div>';
$html .= '</div>';
/*-------Total Losses------*/
$html .= '<div id="losses_menu">';
$html .= '<div class="title_heading"><div class="inner_heading" onclick="toggle_div(this,\'losses_cont\',\'losses_icon\');">Projected Losses</div><div id="losses_condition">If Ellies items are not chosen</div></div>';
$html .= '<div id="losses_cont" class="content_w"> ';
$html .= '<div class="content"> ';
$html .= '<div id="losses">';
$html .= '<div id="losses_label" class="losses_icon">';
$html .= '<div class="inner_icon">';
$html .= '<div class="numberCircle"><h2>Year</h2></div>';
$html .= '<p>Price per kWh</p>';
$html .= '<p>Cumulative losses</p>';
$html .= '<p>Annual losses</p>';
$html .= '<p>Monthly losses</p>';
$html .= '</div>';
$html .= '</div>';
for ($i = 0; $i < 5; $i ++)
{
	$html .= '<div class="losses_icon">';
	$html .= '<div class="inner_icon">';
	$html .= '<div class="numberCircle" id = "losses_year'.($i+1).'"><h2>'.$keyYear[$i].'</h2></div>';
	
	foreach($headings as $key)
	{
		$curYear = $keyYear[$i];
		$val = $savings[$key][$curYear];
		$html .= '<div style="font-size:10px; padding-bottom:5px;">'.$val.'</div>';
	}
	$html .= '</div>';
	$html .= '</div>';
} 
$html .= '</div>';
$html .= '</div>';
$html .= '</div>';
$html .= '</div>';
$mpdf->WriteHTML($html); 
//-------------------------------Third Page-----------------------------------------------------------------
$mpdf->SetTextColor(140, 197, 62);
$header = '
<table width="100%" style="border-bottom: 20px solid #8cc53e; border-top: 20px solid #8cc53e; vertical-align: bottom; font-family: serif; font-size: 9pt;">
    <tr>
        <td width="33%"><p style="color: #8cc53e;">JOHANNESBURG<br/>Cnr. Eloff and Wrighboag Street<br/> Village Deep<br/>JOHANNESBURG<br/>2001<br/>Tel: 011 493 0344<br/>Fax: 011 493 3027</p></td>
        <td width="33%" align="center"></td>
        <td width="33%" style="text-align: right;"><span style="color: #8cc53e;">VAT No: 4240171472<br/>Reg No: 2007/011513/07<br/><p>PO Box 57076<br/>SPRINGFIELD<br/>2137</p><br/>www.ellies.co.za</span></td>
    </tr>
</table>
';
$mpdf->SetHTMLHeader( $header); 
$mpdf->AddPage();
$mpdf->SetY(50);
$mpdf->SetTextColor(0, 0, 0);

$sql = "SELECT `quote_items`.`id`";
$sql.= " FROM `quote_items`";
$sql.= " WHERE `quote_items`.`quote_id` = '".mysqli_real_escape_string($GLOBALS["link"],$quoteid)."' ORDER BY `id`";
$html = "";
/*--------Selected Products-----------------*/
$html .= '<div id="product_menu">';
$html .= '<div class="title_heading"><div class="inner_heading">Chosen Product Info</div></div>';
$html .= '<div id="product_cont" class="content_w"> ';
$html .= '<div class="content">';
$html .= "<div id='product-details'>";
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
    if (($itemcount-1) % 4 == 0 && ($itemcount-1) >1)
	{
        $html .= "<pagebreak />";
        $mpdf->WriteHTML($html);
        $html ="";
        $mpdf->SetY(60);
	}
    $html .= '<table class="product_table">';
    $html .= "<tr>";
        $html .= "<td colspan='3' id='item_number'><h2>Product No. ".$itemcount."<h2></td>";
    $html .= '</tr>';
      $html .= "<tr><td colspan='3' id='item_number'></td></tr>";
      $html .= "<tr><td colspan='3' id='item_number'></td></tr>";

    $qty = $quoteitem->GetInputValues();
    $quoteitem->CalculateCostSavings();
    $qty_val = reset($qty); // First Element's Value
    $qty_key = key($qty); // First Element's Key
    $qty_key = str_replace("_", " ", $qty_key);
    if ($qty_key != "Hours Per Day")
    {
    	$qty_key = "Average Shower Time";
    	$qty_val = $qty["Average_Shower_Time"]." min";
    }
    if ($quoteitem->Qty>0){
        $html .= '<tr>';
        $html .= '<td align="center" valign="middle"><img src="images/ellies_icon_pdf.png" width="30px" /></td>';
        $html .= '<td align="left" class="heading">Technology Type:</td>';
        $html .= '<td align="left" class="prod_vals long_description" valign="middle">'.$quoteitem->TechType.'</td>';
        $html .= '<td align="center" valign="middle"><img src= "images/clock_thumb.png" /></td>';
        $html .= '<td align="left" class="heading">'.$qty_key.':</td>';
        $html .= '<td align="left" class="prod_vals" valign="middle">'.$qty_val.'</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td align="center" valign="middle"><img src="images/ellies_icon_pdf.png" width="30px" /></td>';
        $html .= '<td align="left" class="heading">Existing Product:</td>';
        $html .= '<td align="left" class="prod_vals long_description" valign="middle">'.$quoteitem->OldProductName.'</td>';
        $html .= '<td align="center" valign="middle"><img src="images/calendar_thumb_pdf.png" width="30px"/></td>';        
        $html .= '<td align="left" class="heading">Payback Period:</td>';
        $html .= '<td align="left" class="prod_vals" valign="middle">'.$quoteitem->PaybackPeriodFormatted.'</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td align="center" valign="middle"><img src="images/ellies_icon_pdf.png" width="30px" /></td>';
        $html .= '<td align="left" class="heading">Replacement Product:</td>';
        $html .= '<td align="left" class="prod_vals long_description" valign="middle">'.$quoteitem->ItemDescription.'</td>';
        $html .= '<td align="center" valign="middle"><img src="images/plus_thumb_pdf.png" width="30px"/></td>';        
        $html .= '<td align="left" class="heading">Monthly Saved:</td>';
        $html .= '<td align="left" class="prod_vals" valign="middle">'.number_format($quoteitem->MonthlyCostSaving, 2).'</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td align="center" valign="middle"><img src="images/ellies_icon_pdf.png" width="30px" /></td>';
        $html .= '<td align="left" class="heading">Quantity</td>';
        $html .= '<td align="left" class="prod_vals long_description" valign="middle">'.$quoteitem->Qty.'</td>';
        $html .= '<td align="center" valign="middle"><img src="images/percentage_thumb_pdf.png" width="30px" /></td>';
        $html .= '<td align="left" class="heading">Percentage Saved:</td>';
        $html .= '<td align="left" class="prod_vals" valign="middle">'.number_format($quoteitem->KWhSaved, 2).' ('.number_format($quoteitem->KWhSavedPerc, 0).'%)</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td align="center" valign="middle"><img src="images/ellies_icon_pdf.png" width="30px" /></td>';
        $html .= '<td align="left" class="heading">Room:</td>';
        $html .= '<td align="left" class="prod_vals long_description" valign="middle">'.str_replace("^"," - ",$quoteitem->Room).'</td>';
        $html .= '<td align="center" valign="middle"><img src="images/tick_thumb_pdf.png" width="30px"/></td>';        
        $html .= '<td align="left" class="heading">Replacement Price:</td>';
        $html .= '<td align="left" class="prod_vals" valign="middle">R'.number_format($quoteitem->ItemTotal, 2).'</td>';
        $html .= '</tr>';
        $html .= "<tr><td colspan='3' id='item_number'></td></tr>";
        $html .= "<tr><td colspan='3' id='item_number'></td></tr>";      
    } else {
        $html .= '<tr>';
        $html .= '<td align="center" valign="middle"><img src="images/ellies_icon_pdf.png" width="30px" /></td>';
        $html .= '<td align="left" class="heading">Technology Type:</td>';
        $html .= '<td align="left" class="prod_vals long_description" valign="middle">'.$quoteitem->TechType.'</td>';
        $html .= '<td align="center" valign="middle"></td>';
        $html .= '<td align="left" class="heading"></td>';
        $html .= '<td align="left" class="prod_vals" valign="middle"></td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td align="center" valign="middle"><img src="images/ellies_icon_pdf.png" width="30px" /></td>';
        $html .= '<td align="left" class="heading">Replacement Product:</td>';
        $html .= '<td align="left" class="prod_vals long_description" valign="middle">'.$quoteitem->ItemDescription.'</td>';
        $html .= '<td align="center" valign="middle"></td>';        
        $html .= '<td align="left" class="heading"></td>';
        $html .= '<td align="left" class="prod_vals" valign="middle"></td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td align="center" valign="middle"><img src="images/ellies_icon_pdf.png" width="30px" /></td>';
        $html .= '<td align="left" class="heading">Quantity</td>';
        $html .= '<td align="left" class="prod_vals long_description" valign="middle">'.$quoteitem->NewQty.'</td>';
        $html .= '<td align="center" valign="middle"></td>';
        $html .= '<td align="left" class="heading"></td>';
        $html .= '<td align="left" class="prod_vals" valign="middle"></td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td align="center" valign="middle"><img src="images/ellies_icon_pdf.png" width="30px" /></td>';
        $html .= '<td align="left" class="heading">Room:</td>';
        $html .= '<td align="left" class="prod_vals long_description" valign="middle">'.str_replace("^"," - ",$quoteitem->Room).'</td>';
        $html .= '<td align="center" valign="middle"><img src="images/tick_thumb_pdf.png" width="30px"/></td>';        
        $html .= '<td align="left" class="heading">Replacement Price:</td>';
        $html .= '<td align="left" class="prod_vals" valign="middle">R'.number_format($quoteitem->ItemTotal, 2).'</td>';
        $html .= '</tr>';
        $html .= "<tr><td colspan='3' id='item_number'></td></tr>";
        $html .= "<tr><td colspan='3' id='item_number'></td></tr>";
    }
    $totalqty += $quoteitem->NewQty;
    $html .= '<tr>';
 
    $html .= '</table>';
}

$html .= "</div>";
$html .= '</div>';
$html .= '</div>';
$html .= '</div>';

$html .="<img  id ='product_divider'  src='images/horiz_divider.png' width='100%'/>";
$mpdf->WriteHTML($html); 
//------------------------------Last Page--------------------------------------------------------
$mpdf->SetHTMLHeader(""); 
$mpdf->AddPage();
$html = "";

$channeltype = "";
if (!isset($_GET["app"])){
    $system_user = new userType($_SESSION["userid"]);
    $sql = "SELECT `type` FROM `channels` WHERE `id`=".$system_user->retailChannel;
    $sqlres = mysqli_query($GLOBALS["link"],$sql);
    $row = mysqli_fetch_assoc($sqlres);
    $channeltype = $row['type'];
    if ($channeltype !== "Commercial" && $channeltype !=="Franchises"){
        $channeltype = "Retail";  
    } 

    if($channeltype =="Franchises"){
         $channeltype = "Commercial";
    }
}else{
    $channeltype = "Retail";  
}

$sql = "SELECT `termsCond`, `additionalCond`";
$sql.= " FROM `pdf_settings`";
$sql .= " WHERE `pdf_type` ='$channeltype'"; 
$sql .= " ORDER BY `id` DESC LIMIT 1";
$sqlres = mysqli_query($GLOBALS["link"],$sql);
$termsCond ="";
$additonalCond = "";
while($row = mysqli_fetch_assoc($sqlres)) {
    $termsCond =  $row['termsCond'];
    $additonalCond =  $row['additionalCond'];
}
$mpdf->SetTextColor(0, 0, 0);
$disclaimertext = '
    <h2>Disclaimers</h2>
    <ol style="font-size:13px">'
        .$termsCond.
   '</ol>';
$html .= $disclaimertext;
$html .= "</div>";
$mpdf->WriteHTML($html);    

if ($previewmode)
{
    $mpdf->Output();
}
else{
    require_once("inc/header.php");
    $quoteid = $_GET["id"];
    $quote = new Quote($quoteid);
    if ($quoteid == "0") {
	$quote->CustomerID = SESSION("customerid");
	$quote->Save();
    }

    $filename = "/tmp/Quotation - ".$quote->QuoteReferenceNo.".pdf";
    if (file_exists($filename)) {
	   unlink($filename);
    }
    $mpdf->Output($filename, "F");

    $sql = "SELECT `type`";
    $sql.= " FROM `channels` INNER JOIN `quotes` ON `quotes`.channel =`channels`.id";
    $sql .= " WHERE `quotes`.id=".$quoteid." LIMIT 1";
    $sqlres = mysqli_query($GLOBALS["link"],$sql);
    $row = mysqli_fetch_assoc($sqlres);
    //$retail = strtolower($row['type']);
    $retail = $row['type'];

    $status = false;
    if (isset($_GET["app"])){
        //Automated Sending of quotes to retail only
        $app = (string)$_GET["app"];
        if($retail=="Retail" && $app == "true"){
            $status = SendEmail($customer->Email, $customer->Name." ".$customer->Surname, "Ellies Energy Saving - Quotation: ".$quote->QuoteReferenceNo, "Please find attached. Ellies Energy Saving - Quotation: ".$quote->QuoteReferenceNo, $attachments[] = $filename); 
        }
    }else{
       $status = SendEmail($customer->Email, $customer->Name." ".$customer->Surname, "Ellies Energy Saving - Quotation: ".$quote->QuoteReferenceNo, "Please find attached. Ellies Energy Saving - Quotation: ".$quote->QuoteReferenceNo, $attachments[] = $filename); 
    }  

    /*$status = SendEmail($customer->Email, $customer->Name." ".$customer->Surname, "Ellies Energy Saving - Quotation: ".$quote->QuoteReferenceNo, "Please find attached. Ellies Energy Saving - Quotation: ".$quote->QuoteReferenceNo, $attachments[] = $filename); */
   
	if ($status){
		$quote->Approved=1;
		$quote->Save();
	}

    if (!empty($_GET["complete"])) {

		if ($_GET["complete"] == "1") {
			
			PrintNavBar($booktitle, true, "newquote.php?step=newclick");
		}
		else{
			PrintNavBar($booktitle, true, "editquote.php?id=".$quoteid);
		}
    }else{
        if (!isset($_GET["app"])) PrintNavBar($booktitle, true, "editquote.php?id=".$quoteid);
    }
	
    if ($status) {
		if (!isset($_GET["app"]))print "<p>Thank you. Your quote has been emailed to the customer.</p>";
    }else{
		if (!isset($_GET["app"]))print "<p>".$status."</p>";	
    }
    //mail($customer->Email, "Ellies Energy Saving - Quotation", "");
    //===Put in alternate mailer here with attachment support===
    require_once("inc/footer.php");
}
?>