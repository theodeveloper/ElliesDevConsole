<?php 

require_once("inc/config.php");
require_once("inc/customer.class.php");
require_once("inc/quote.class.php");
require_once("inc/quoteitem.class.php");
require_once("inc/techtype.class.php");
require_once("inc/printtechitem.class.php");
require_once("inc/printproduct.class.php");
require_once("inc/printfunctions.php");

$quoteid = $_GET["id"];

$booktitle = "Edit Quote";

//require_once("inc/header.php");



$quote = new Quote($quoteid);
$customerid = 0;

if ($quoteid == "0") {
    $quote->CustomerID = $_GET["customerid"];
    $quote->Save();
}
$customerid = $quote->CustomerID;


$isnew = false;
$customer = new Customer($quote->CustomerID);

$html .= '<div style="float:left; width:200px;"><img src="images/logo.png" width="200px"/></div>';
$html .= '<div style="float:right; width:380px; border-radius:15px;	border:0.5px solid #70aa00;">
<table id="info_table">';
$html .= '<tr>';
/*$html .= '<td align="center" valign="middle" rowspan="8"><img src="images/details_icon.png"  width="120px"/></td>';*/
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
$html .= '<tr>';
$html .= '<td align="center" valign="middle"><img src="images/ellies_icon_pdf.png" width="20px"/></td>';        
$html .= '<td align="left" class="heading">Date:</td>';
$html .= '<td align="left" class="quote_vals" valign="middle">'.$quote->DateCreated.'</td>';
$html .= '</tr>';

/*<tr>';

<td  align="center" valign="middle" class="customer_icon" valign="middle"><img src="images/property_icon_pdf.png" width="20px"/></td>

<td align="left" class="quote_vals" valign="middle">'.$quote->Property.'</td>
</tr>';*/
$html .= '</table>
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
$html .= '</table></div>';
$html.='<div style="clear:both;">&nbsp;</div>';

$html .= '<br/><br/>';
$html .= '<div style="font-weight:bold; font-size:13px;">Dear '.$customer->Name.' '.$customer->Surname.',<br/><br/>
Thank you for giving Ellies the opportunity to give you a customised energy audit and therefore help you save money. Below are the estimated savings and details of our customised solution for you.</div>';
$html .= '<br/>';

$quote->CalculateCostSavingTotals();
$savings = $quote->Get5YearSavings();
$ttl_savings = end($savings["Cumulative Savings"]);

//end get year num

/*--------Total Overview------*/

$html .= '<h2 class="icon_heading">Overview</h2>';
$html .= '<table id="overview">';
$html .= '<tr><td class="total_icon"><div class="inner_icon"><img src="images/calendar_icon2.png" width="150px" style="padding-bottom:20px"/>';
$html .= '<div class="icon_description" >Payback Period</div>';
$html .= '<h2>'.$quote->TotalPaybackPeriodFormatted.'</h2></div></td>';
$html .= '<td class="total_icon"><div class="inner_icon"><img src="images/plus_icon2.png" width="150px" style="padding-bottom:20px" />';
$html .= '<div class="icon_description">Monthly Saved</div>';
$html .= '<h2>R'.number_format($quote->MonthlyCostSaving, 2).'</h2></div></td>';
$html .= '<td class="total_icon"><div class="inner_icon"><img src="images/percentage_icon2.png" width="150px" style="padding-bottom:20px" />';
$html .= '<div class="icon_description">Percentage Saved</div>';
$html .= '<h2>'.number_format($quote->KWhSavedPerc, 0).'%</h2></div></td>';
$html .= '<td class="total_icon"><div class="inner_icon"><img src="images/years_icon2.png" width="150px" style="padding-bottom:20px" />';
$html .= '<div class="icon_description">Savings after 5 years</div>';
$html .= '<h2>'.$ttl_savings.'</h2></div></div>';
$html .= '<td class="total_icon"><div class="inner_icon"><img src="images/tick_icon2.png" width="150px" style="padding-bottom:20px" />';
$html .= '<div class="icon_description">Total Cost</div>';
$html .= '<h2>R'.number_format($quote->TotalPrice, 2).'</h2></div></td>';
$html .= '</table>';

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
$html .= '<div id="savings_menu">';
$html .= '<div class="title_heading"><div class="inner_heading" onclick="toggle_div(this,\'savings_cont\',\'savings_icon\');">Projected Savings</div></div>';

$html .= '<div id="savings_cont" class="content_w"> ';
$html .= '<div class="content"> ';
$html .= '<div id="savings">';
$html .= '<div id="savings_label" class="savings_icon">';
$html .= '<div class="inner_icon">';
$html .= '<div class="numberCircle" style="margin-bottom:17px;"><h1>Year</h1></div>';
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
	$html .= '<div class="numberCircle" style="margin-bottom:17px; " id = "savings_year'.($i+1).'"><h1>'.$keyYear[$i].'</h1></div>';
	
	foreach($headings as $key)
	{
		$curYear = $keyYear[$i];
		$val = $savings[$key][$curYear];
		$html .= '<div>'.$val.'</div>';
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
$html .= '<div class="numberCircle"><h1>Year</h1></div>';
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
	$html .= '<div class="numberCircle" id = "losses_year'.($i+1).'"><h1>'.$keyYear[$i].'</h1></div>';
	
	foreach($headings as $key)
	{
		$curYear = $keyYear[$i];
		$val = $savings[$key][$curYear];
		$html .= '<div>'.$val.'</div>';
	}
	$html .= '</div>';
	$html .= '</div>';
} 
$html .= '</div>';
$html .= '</div>';
$html .= '</div>';
$html .= '</div>';
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

/*--------Selected Products-----------------*/
$html .= '<div id="product_menu">';
$html .= '<div class="title_heading"><div class="inner_heading">Chosen Product Info</div></div>';
$html .= '<div id="product_cont" class="content_w"> ';
$html .= '<div class="content">';
$html .= "<div id='product-details'>";
$html .= '<table>';$totalprice = 0;
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

$html .= "<tr>";
//$html .= '<td rowspan="6"> <img src="images/details_icon.png" width="150px" style="float:left;  padding-right:40px;"/></td>';

$html .= "<td colspan='3' id='item_number'><h2>Product No. ".$itemcount."<h2></td>";
$html .= '</tr>';
$html .= '<tr>';
$html .= '<td align="center" valign="middle"><img src="images/ellies_icon_pdf.png" width="30px" /></td>';
$html .= '<td align="left" class="heading">Technology Type:</td>';
$html .= '<td align="left" class="prod_vals long_description" valign="middle">'.$quoteitem->TechType.'</td>';
$qty = $quoteitem->GetInputValues();
$qty_val = reset($qty); // First Element's Value
$qty_key = key($qty); // First Element's Key
$qty_key = str_replace("_", " ", $qty_key);
if ($qty_key != "Hours Per Day")
{
	$qty_key = "Average Shower Time";
	$qty_val = $qty["Average_Shower_Time"]." min";
}
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
$html .= '<td align="left" class="prod_vals long_description" valign="middle">'.$quoteitem->Room.'</td>';
$html .= '<td align="center" valign="middle"><img src="images/tick_thumb_pdf.png" width="30px"/></td>';        
$html .= '<td align="left" class="heading">Replacement Price:</td>';
$html .= '<td align="left" class="prod_vals" valign="middle">R'.number_format($quoteitem->ItemTotal, 2).'</td>';
$html .= '</tr>';
    
    $totalqty += $quoteitem->Qty;
$html .= '<tr>';
$html .= '<td width="100%" colspan="6" align="center">';
$html .= '<img id ="product_divider" src="images/horiz_divider2.png"/>';
$html .= '</td>';

}
$html .= '</table>';

$html .= "</div>";
$html .= '</div>';
$html .= '</div>';
$html .= '</div>';

$exvat = $totalprice / 1.14;
$vat = $totalprice - $exvat;

$colspan = 3;
    $colspan = 5;

$html .= "<pagebreak />";
$disclaimertext = 
"<h2>Disclaimers</h2>
In providing the customer with energy savings advice, use shall be made of the Ellies Customised Energy Audit to determine the indicative estimated savings that the customer may enjoy.  However, the customer should be alerted to and have specific regard to the following important assumptions, limitations and qualifications which relate to any advice which may be provided to the customer –
<br/><br/>
1.	The estimates generated from the Ellies Customised Energy Audit are indicative estimates, are based solely on information provided by the customer and should not be considered to be warranted as accurate. This type of information includes information on existing technologies and usage profiles (which includes existing technology durations used, daily frequency of usage and litre measurements for water products). The onus to ensure that the correctness of the information entered into the Ellies Customised Energy Audit rests entirely with the customer.<br/>
2.	Manufacturer's stated specifications in respect of proposed replacement products may vary.<br/>
3.	The quality experience of the replacement product suggested may not be exactly like-for-like. For example, the lumen output may be reduced and other factors such as beam angle, light temperature/colour and perceived light quality may be different.<br/>
4.	Lamp lifetime of the replacement product, as stated on the packaging, will be affected by variations in mains voltage and frequency.<br/>
5.	Excessive repetitive use (e.g. frequent repetitive on/off cycles) of the replacement product will adversely affect the performance and lifespan of the product.<br/>
6.	Certain lighting products are not suitable for use in circuits with dimmers.<br/>
7.	Ellies is not responsible for product failures/reduced performance on replacement products caused by any external factors including faulty wiring, poor installation, faulty ancillary equipment or incorrect application of product.<br/>
8.	All electrical installations and modifications must be carried out by a qualified electrician - in accordance with South African regulations.<br/>
9.	By using all replacement technologies you agree to all exclusions and limitations of liability stated herein and accept them as reasonable.<br/>
10.	This disclaimer notice shall be interpreted and governed by South African law, and any disputes in relation to it are subject to the jurisdiction of the South African Courts.<br/>
11.	The products provided by Ellies are on an “As Is” basis and disclaim any warranty of any kind imputed by the laws of any jurisdiction, whether express or implied, as to any matter whatsoever relating to the service and sale of any of Ellies products including, without limitation the implied warranties of merchantability, fitness for a   particular purpose and non-infringement.<br/>
12.	Use of the product is at the customer’s own risk. The data and information provided is not advice, professional or otherwise, and should not be relied upon as such.<br/>


13.	The replacement technologies (products) discussed or recommended by Ellies, directly or indirectly to customers may not be suitable to all customers. Customers must make their own decisions based on their own needs and requirements, notwithstanding.
14.	The information contained herein relating to Ellies replacement technologies   (products) is believed to be reliable but Ellies do not warrant its completeness or accuracy.
15.	Customers are advised to ultimately rely on their own judgment when making purchasing decisions.
16.	Ellies along with its directors, employees, associates or other representatives and its affiliates shall not be liable for damages or injury arising out of or in connection with the use of the replacement technologies (products referred to herein,) including non-availability, direct, indirect or consequential damages, work stoppage, or interruption of business, arising out of or relating in any way to the product, from or relating to any of the products offered by Ellies.
17.	Ellies makes no guarantees or representations as to, and shall have no liability for    any replacement technology products delivered by any third party, including, without limitation, the accuracy, subject matter, quality or timeliness of any of the Ellies products offered to the customer.
18.	Ellies shall not be liable for any misrepresentations, falsification, and deception or for any lack of availability of replacement technology products offered to the customer, even if same is advertised accordingly.
19.	Any display, description or references to any of Ellies replacement technology   products offered to customers shall not constitute an endorsement by Ellies. 

20.	Ellies does not warrant the accuracy or completeness of the products or the reliability of any advice, opinion or other information stated by or through Ellies. The customer acknowledges that any reliance on any such opinion, advice, statement or information shall be at the customer’s own risk.
21.	Ellies reserves the right to effect price changes to all replacement technology products at any time it’s deemed appropriate and such price changes may vary from store to store.";

$disclaimertext = str_replace(chr(9), "  ", $disclaimertext);
$disclaimertext = str_replace("–", "-", $disclaimertext);
$disclaimertext = str_replace("“", "\"", $disclaimertext);
$disclaimertext = str_replace("”", "\"", $disclaimertext);
$disclaimertext = str_replace("’", "'", $disclaimertext);
$disclaimertext = str_replace("-\n", "-\n\n", $disclaimertext);
$disclaimertext = str_replace(".\n", ".\n\n", $disclaimertext);
$html .= $disclaimertext;
$html .= "</div>";

//echo $html;
//require_once("inc/footer.php");


include("inc/MPDF56/mpdf.php");
$mpdf=new mPDF('c','A4','','',10,10,10,10,6,6); 
$mpdf->SetDisplayMode('fullpage');
$mpdf->list_indent_first_level = 0;

/*$mpdf->fontdata = array(
    "neueLight" => array(
    'R' => 'HelveticaNeueLight.ttf'
    ),
"neueBold" => array(
    'R' => 'HelveticaNeueCondensedBold.ttf'
    ),
"neueBlack" => array(
    'R' => 'HelveticaNeueCondensedBlack.ttf'
    )
);*/
//$stylesheet = file_get_contents('jmobile/jquery.mobile-1.3.0.min.css');
//$stylesheet .= file_get_contents('css/themes/el3.min.css');
$stylesheet .= file_get_contents('css/printsheet.css');
$mpdf->WriteHTML($stylesheet,1);
$mpdf->WriteHTML($html);    
$mpdf->SetDisplayMode('fullwidth');
$content = $mpdf->Output('', 'S');

$content = chunk_split(base64_encode($content));
$mailto = 'woodydaniel@gmail.com'; //Mailto here
$from_name = 'Ellies Electrical'; //Name of sender mail
$from_mail = 'mailfrom@mailfrom.com'; //Mailfrom here
$subject = 'Ellies renewable energy'; 
$message = 'mailmessage';
$filename = "Ellies-".date("d-m-Y_H-i",time()); //Your Filename whit local date and time

//Headers of PDF and e-mail
$boundary = "XYZ-" . date("dmYis") . "-ZYX"; 

$header = "--$boundary\r\n"; 
$header .= "Content-Transfer-Encoding: 8bits\r\n"; 
$header .= "Content-Type: text/html; charset=ISO-8859-1\r\n\r\n"; //plain 
$header .= "$message\r\n";
$header .= "--$boundary\r\n";
$header .= "Content-Type: application/pdf; name=\"".$filename."\"\r\n";
$header .= "Content-Disposition: attachment; filename=\"".$filename."\"\r\n";
$header .= "Content-Transfer-Encoding: base64\r\n\r\n";
$header .= "$content\r\n"; 
$header .= "--$boundary--\r\n";

$header2 = "MIME-Version: 1.0\r\n";
$header2 .= "From: ".$from_name." \r\n"; 
$header2 .= "Return-Path: $from_mail\r\n";
$header2 .= "Content-type: multipart/mixed; boundary=\"$boundary\"\r\n";
$header2 .= "$boundary\r\n";

mail($mailto,$subject,$header,$header2, "-r".$from_mail);

$mpdf->Output($filename ,'I');
exit;
?>