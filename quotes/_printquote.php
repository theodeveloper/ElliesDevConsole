<?php 

require_once("inc/config.php");
require_once("inc/customer.class.php");
require_once("inc/quote.class.php");
require_once("inc/quoteitem.class.php");
require_once("inc/techtype.class.php");
require_once("inc/techitem.class.php");
require_once("inc/product.class.php");
require_once("inc/functions.php");
require_once("../inc/system_user.php");

$quoteid = $_GET["id"];

$booktitle = "Edit Quote";

//require_once("inc/header.php");

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



$quote = new Quote($quoteid);
$customerid = 0;

if ($quoteid == "0") {
    $quote->CustomerID = $_GET["customerid"];
    $quote->Save();
}
$customerid = $quote->CustomerID;


$isnew = false;
$customer = new Customer($quote->CustomerID);


$html .= '<table border="0">';
$html .= '<tr>';
$html .= '<td align="center" valign="middle" rowspan="4"><img src="images/details_icon.png" /></td>';
$html .= '<td align="center" valign="middle"><img src="images/ellies_icon.png" /></td>';
$html .= '<td align="left" class="heading">Electricity Escalation Rate:</td>';
$html .= '<td align="left" class="quote_vals" valign="middle">'.$quote->ElectricityEscalationRatePercentage.'</td>';
$html .= '<td align="left" class="customer_icon" valign="middle"><img src="images/name_icon.png" /></td>';
$html .= '<td align="left" class="customer_vals" valign="middle">'.$customer->Surname.", ".$customer->Name.'</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<td align="center" valign="middle"><img src="images/ellies_icon.png" /></td>';        
$html .= '<td align="left" class="heading">Quote Reference:</td>';
$html .= '<td align="left" class="quote_vals" valign="middle">'.$quote->QuoteReferenceNo.'</td>';
$html .= '<td align="left" class="customer_icon" valign="middle"><img src="images/phone_icon.png" /></td>';
$html .= '<td align="left" class="customer_vals" valign="middle">'.$customer->CellPhone.'</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<td align="center" valign="middle"><img src="images/ellies_icon.png" /></td>';        
$html .= '<td align="left" class="heading">Price/kWh R:</td>';
$html .= '<td align="left" class="quote_vals" valign="middle">'.$quote->KWhPrice.'</td>';
$html .= '<td align="left" class="customer_icon" valign="middle"><img src="images/email_icon.png" /></td>';
$html .= '<td align="left" class="customer_vals" valign="middle">'.$customer->Email.'</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<td align="center" valign="middle"><img src="images/ellies_icon.png" /></td>';        
$html .= '<td align="left" class="heading">Date:</td>';
$html .= '<td align="left" class="quote_vals" valign="middle">'.$quote->DateCreated.'</td>';
$html .= '<td align="left" class="customer_icon" valign="middle"><img src="images/property_icon.png" /></td>';
$html .= '<td align="left" class="customer_vals" valign="middle">'.$quote->Property.'</td>';
$html .= '</tr>';
$html .= '</table>';


$html .= '<br/><br/>';


$quote->CalculateCostSavingTotals();
$savings = $quote->Get5YearSavings();
$ttl_savings = end($savings["Cumulative Savings"]);

//end get year num

/*--------Total Overview------*/

$html .= '<h1 class="icon_heading">Overview</h1>';
$html .= '<div id="overview">';
$html .= '<div class="total_icon"><div class="inner_icon"><img src="images/calendar_icon.png" />';
$html .= '<div class="icon_description">Payback Period</div>';
$html .= '<h2>'.$quote->TotalPaybackPeriodFormatted.'</h2></div></div>';
$html .= '<div class="total_icon"><div class="inner_icon"><img src="images/plus_icon.png" />';
$html .= '<div class="icon_description">Monthly Saved</div>';
$html .= '</div><h2>R'.number_format($quote->MonthlyCostSaving, 2).'</h2></div>';
$html .= '<div class="total_icon"><div class="inner_icon"><img src="images/percentage_icon.png" />';
$html .= '<div class="icon_description">Percentage Saved</div>';
$html .= '<h2>'.number_format($quote->KWhSavedPerc, 0).'%</h2></div></div>';
$html .= '<div class="total_icon"><div class="inner_icon"><img src="images/years_icon.png" />';
$html .= '<div class="icon_description">Savings after 5 years</div>';
$html .= '<h2>'.$ttl_savings.'</h2></div></div>';
$html .= '<div class="total_icon"><div class="inner_icon"><img src="images/tick_icon.png" />';
$html .= '<div class="icon_description">Total Cost</div>';
$html .= '<h2>R'.number_format($quote->TotalPrice, 2).'</h2></div></div>';
$html .= '</div>';

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
$html .= '<div class="title_heading"><div class="inner_heading"><h1 class="prod_heading">Projected Savings</h1></div></div>';

$html .= '<div id="savings_cont" class="content_w"> ';
$html .= '<div class="content"> ';
$html .= '<div id="savings">';
$html .= '<div class="savings_icon">';
$html .= '<div class="inner_icon">';
$html .= '<div class="numberCircle"><h1>Year</h1></div>';
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
	$html .= '<div class="numberCircle" id = "savings_year'.($i+1).'"><h1>'.$keyYear[$i].'</h1></div>';
	
	foreach($headings as $key)
	{
		$curYear = $keyYear[$i];
		$val = $savings[$key][$curYear];
		$html .= '<p>'.$val.'</p>';
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
$html .= '<div class="title_heading"><div class="inner_heading" onclick="toggle_div(this,\'losses_cont\',\'losses_icon\');"><span class="title_span"><img id="losses_icon" src="images/prodmenu_plus_icon.png"/><span><h1 class="prod_heading">Projected Losses</h1></div><div id="losses_condition">If Ellies items are not chosen</div></div>';
$html .= '<div id="losses_cont" class="content_w"> ';
$html .= '<div class="content"> ';
$html .= '<div id="losses">';
$html .= '<div class="losses_icon">';
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
		$html .= '<p>'.$val.'</p>';
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
$html .= '<div class="title_heading"><div class="inner_heading" id="hahah" onclick="toggle_div(this,\'product_cont\', \'prod_icon\');"><span class="title_span"><img id="prod_icon" src="images/prodmenu_plus_icon.png"/><span><h1 class="prod_heading">Chosen Products Info</h1></div></div>';
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
$html .= '<td rowspan="6"> <img src="images/details_icon.png" width="150px" style="float:left;  padding-right:40px;"/></td>';

$html .= "<td colspan='3' id='item_number'>Product No. ".$itemcount."</td>";
$html .= '</tr>';
$html .= '<tr>';
$html .= '<td align="center" valign="middle"><img src="images/ellies_icon.png" /></td>';
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
$html .= '<td align="center" valign="middle"><img src="images/ellies_icon.png" /></td>';
$html .= '<td align="left" class="heading">Existing Product:</td>';
$html .= '<td align="left" class="prod_vals long_description" valign="middle">'.$quoteitem->OldProductName.'</td>';
$html .= '<td align="center" valign="middle"><img src="images/calendar_thumb.png" /></td>';        
$html .= '<td align="left" class="heading">Payback Period:</td>';
$html .= '<td align="left" class="prod_vals" valign="middle">'.$quoteitem->PaybackPeriodFormatted.'</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<td align="center" valign="middle"><img src="images/ellies_icon.png" /></td>';        
$html .= '<td align="left" class="heading">Replacement Product:</td>';
$html .= '<td align="left" class="prod_vals long_description" valign="middle">'.$quoteitem->ItemDescription.'</td>';
$html .= '<td align="center" valign="middle"><img src="images/plus_thumb.png" /></td>';        
$html .= '<td align="left" class="heading">Monthly Saved:</td>';
$html .= '<td align="left" class="prod_vals" valign="middle">'.number_format($quoteitem->MonthlyCostSaving, 2).'</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<td align="center" valign="middle"><img src="images/ellies_icon.png" /></td>';        
$html .= '<td align="left" class="heading">Quantity</td>';
$html .= '<td align="left" class="prod_vals long_description" valign="middle">'.$quoteitem->Qty.'</td>';
$html .= '<td align="center" valign="middle"><img src="images/percentage_thumb.png" /></td>';
$html .= '<td align="left" class="heading">Percentage Saved:</td>';
$html .= '<td align="left" class="prod_vals" valign="middle">'.number_format($quoteitem->KWhSaved, 2).' ('.number_format($quoteitem->KWhSavedPerc, 0).'%)</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<td align="center" valign="middle"><img src="images/ellies_icon.png" /></td>';        
$html .= '<td align="left" class="heading">Room:</td>';
$html .= '<td align="left" class="prod_vals long_description" valign="middle">'.$quoteitem->Room.'</td>';
$html .= '<td align="center" valign="middle"><img src="images/tick_thumb.png" /></td>';        
$html .= '<td align="left" class="heading">Replacement Price:</td>';
$html .= '<td align="left" class="prod_vals" valign="middle">R'.number_format($quoteitem->ItemTotal, 2).'</td>';
$html .= '</tr>';
$html .= "<tr><td colspan='7'><img  id ='product_divider'  src='images/horiz_divider.png'/></td></tr>";

    
    $totalqty += $quoteitem->Qty;
  

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

$html .= "</div>";

//echo $html;
//require_once("inc/footer.php");


include("inc/MPDF56/mpdf.php");
$mpdf=new mPDF('c','A4','','',10,10,10,10,6,6); 
$mpdf->SetDisplayMode('fullpage');
$mpdf->list_indent_first_level = 0;

$stylesheet = file_get_contents('jmobile/jquery.mobile-1.3.0.min.css');
$stylesheet .= file_get_contents('css/themes/el3.min.css');
$stylesheet .= file_get_contents('css/printsheet.css');
$mpdf->WriteHTML($stylesheet,1);
$mpdf->WriteHTML($html);    
$mpdf->Output('ellies.pdf', 'I');
exit;
?>