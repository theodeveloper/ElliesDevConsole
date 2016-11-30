<?php 
session_start();
error_reporting(E_ALL);

require_once("inc/config.php");
require_once("../inc/system_user.php");
require_once("../inc/functions.php");

require_once("inc/customer.class.php");
require_once("inc/quote.class.php");
require_once("inc/quoteitem.class.php");
require_once("inc/techtype.class.php");
require_once("inc/techitem.class.php");
require_once("inc/product.class.php");
require_once("inc/functions.php");

require_once('pdf/libraries/tcpdf/config/lang/eng.php');
require_once('pdf/libraries/tcpdf/tcpdf.php');
require_once('pdf/libraries/MYPDF.php');

$quoteid = (int)$_GET["id"];
$quote = new Quote($quoteid);

//Checks if pdf has the required fields
$QuoteID ="";
$query = "SELECT * FROM `pdf_required_fields` WHERE `quote_id` = '" . $quoteid . "'";
$result = mysqli_query($GLOBALS["link"],$query);
while($row = mysqli_fetch_assoc($result)) {  
    $QuoteID = $row['quote_id'];
}

if($QuoteID ==""){
  header("Location: requiredfields.php?id=".$quoteid."&preview=1&approved=1"); 
}

//Cash Overviews
$quote->RentalTerm=0;
$quote->CalculateCostSavingTotals();
$savings = $quote->Get5YearSavings();
$term_savings=($quote->Get5Savings());
$ttl_savings = end($savings["Cumulative Savings"]);
$ttl_savings_time = end(array_keys($savings["Price per kWh"]));

$tableCash = '
    <style>
       <link rel="stylesheet" href="pdf/css/layout.css"  type="text/css"/>
    </style>
      <table border="1" cellpadding = "2">
        <tr>
            <th align="center" bgcolor ="#8CC53E">Category</th>
            <th align="center" bgcolor ="#8CC53E">Product Price</th>
            <th align="center" bgcolor ="#8CC53E">Installation</th>
            <th align="center" bgcolor ="#8CC53E">Total Price</th>
        </tr>
        <tr>
            <td>Replacement Products</td>
            <td align="center">R'.number_format($quote->ReplacementPrice-$quote->TravelCostsReplacement-$quote->CrushCostsReplacement-$quote->LabourCostsReplacement-$quote->MaterialsCostsReplacement,2).'</td>
            <td align="center">R'.number_format($quote->TravelCostsReplacement+$quote->CrushCostsReplacement+$quote->LabourCostsReplacement+$quote->MaterialsCostsReplacement,2).'</td>
            <td align="center">R'.number_format($quote->ReplacementPrice,2).'</td>
        </tr>
        <tr>
            <td>Additional Products</td>
            <td align="center">R'.number_format($quote->AdditionalPrice-$quote->TravelCostsAdditional-$quote->CrushCostsAdditional-$quote->LabourCostsAdditional-$quote->MaterialsCostsAdditional,2).'</td>
            <td align="center">R'.number_format($quote->TravelCostsAdditional+$quote->CrushCostsAdditional+$quote->LabourCostsAdditional+$quote->MaterialsCostsAdditional,2).'</td>
            <td align="center">R'.number_format($quote->AdditionalPrice,2).'</td>
        </tr>

        <tr>
            <td>Total</td>
            <td align="center">R'.number_format($quote->AllPrice-$quote->TravelCostsAdditional-$quote->CrushCostsAdditional-$quote->LabourCostsAdditional-$quote->MaterialsCostsAdditional-$quote->TravelCostsReplacement-$quote->CrushCostsReplacement-$quote->LabourCostsReplacement-$quote->MaterialsCostsReplacement,2).'</td>
            <td align="center">R'.number_format($quote->TravelCostsAdditional+$quote->CrushCostsAdditional+$quote->LabourCostsAdditional+$quote->MaterialsCostsAdditional+$quote->TravelCostsReplacement+$quote->CrushCostsReplacement+$quote->LabourCostsReplacement+$quote->MaterialsCostsReplacement,2).'</td>
            <td align="center">R'.number_format($quote->AllPrice,2).'</td>
        </tr>
    </table>';

$tableCash2 = '
    <style>
       <link rel="stylesheet" href="pdf/css/layout.css"  type="text/css"/>
    </style>
      <table border="1" cellpadding = "2">
        <tr>
            <th align="center" bgcolor ="#8CC53E">Payback Period</th>
            <th align="center" bgcolor ="#8CC53E">Monthly Savings</th>
            <th align="center" bgcolor ="#8CC53E">Percentage Saved(%)</th>
            <th align="center" bgcolor ="#8CC53E">Savings after '.$ttl_savings_time.' years</th>
        </tr>
        <tr>
            <td align="center">'.$quote->TotalPaybackPeriodFormatted.'</td>
            <td align="center">R'.number_format($quote->MonthlyCostSaving, 2).'</td>
            <td align="center">'.number_format($quote->KWhSavedPerc, 0).'%</td>
            <td align="center">'.$ttl_savings.'</td>
        </tr>
    </table>';

//Rental Overviews
$quote2 = new Quote($quoteid);
$quote2->RentalTerm=1;
$quote2->CalculateCostSavingTotals();
$savings = $quote2->Get5YearSavings();
$term_savings=($quote2->Get5Savings());
$ttl_savings = end($savings["Cumulative Savings"]);
$array_keys = array_keys($savings["Price per kWh"]);
$ttl_savings_time = end($array_keys);

//Override rental capital/maintenance
$sql = "SELECT `rental_capital`,`rental_maintenance`,`additional_capital`,`additional_maintenance`";
$sql.= " FROM `quote_extras`";
$sql.= " WHERE `quote_id` = '".mysqli_real_escape_string($GLOBALS["link"],$quoteid)."' LIMIT 1";
$sqlres = mysqli_query($GLOBALS["link"],$sql);
$rental_capital=0;
$rental_maintenance=0;
$additional_capital=0;
$additional_maintenance=0;
while($row = mysqli_fetch_assoc($sqlres)) {
    $rental_capital = $row['rental_capital'];
    $rental_maintenance = $row['rental_maintenance'];
    $additional_capital = $row['additional_capital'];
    $additional_maintenance = $row['additional_maintenance'];
}

$tableRental = '
    <style>
       <link rel="stylesheet" href="pdf/css/layout.css"  type="text/css"/>
    </style>
      <table border="1" cellpadding = "2">
        <tr>
            <th align="center" bgcolor ="#8CC53E">Category</th>
            <th align="center" bgcolor ="#8CC53E">Capital</th>
            <th align="center" bgcolor ="#8CC53E">Maintenance</th>
            <th align="center" bgcolor ="#8CC53E">Total</th>
        </tr>
        <tr>
            <td>Replacement Products</td>';
            if($rental_capital ==0){
               $tableRental .= '<td align="center">R'.sprintf("%.2f",$quote2->getRentalCapital("replacement",$quote2->RentalTerm)). '</td>';
            }else{
               $tableRental .= '<td align="center">R'.sprintf("%.2f",$rental_capital). '</td>';
            }

            if($rental_maintenance ==0){
                $tableRental .= '<td align="center">R'.sprintf("%.2f",$quote2->getRentalMaintenance("replacement",$quote2->RentalTerm,$quote2->MaintenancePercentage)). '</td>';
            }else{
                $tableRental .= '<td align="center">R'.sprintf("%.2f",$rental_maintenance). '</td>';
            }

            if($rental_maintenance == 0 && $rental_capital ==0){
               $tableRental .= '<td align="center">R'.sprintf("%.2f",$quote2->getRentalAmount("replacement",$quote2->RentalTerm, $quote2->MaintenancePercentage)).'</td>';
            }else{
                $total= $rental_maintenance + $rental_capital;
                $tableRental .= '<td align="center">R'.sprintf("%.2f",$total).'</td>';
            }
    $tableRental .= '
        </tr>
        <tr>
            <td>Additional Products</td>';
            if($additional_capital ==0){
                $tableRental .= '<td align="center">R'.sprintf("%.2f",$quote2->getRentalCapital("additional",$quote2->RentalTerm)).'</td>';
            }else{
               $tableRental .= '<td align="center">R'.sprintf("%.2f",$additional_capital). '</td>';
            }

           if($additional_maintenance ==0){
                $tableRental .= '<td align="center">R'.sprintf("%.2f",$quote2->getRentalMaintenance("additional",$quote2->RentalTerm, $quote2->MaintenancePercentage)).'</td>';
            }else{
                $tableRental .= '<td align="center">R'.sprintf("%.2f",$additional_maintenance). '</td>';
            }

            if($additional_maintenance == 0 && $additional_capital ==0){
                $tableRental .= '<td align="center">R'.sprintf("%.2f",$quote2->getRentalAmount("additional",$quote2->RentalTerm, $quote2->MaintenancePercentage)). '</td>';
            }else{
                $total= $additional_maintenance + $additional_capital;
                $tableRental .= '<td align="center">R'.sprintf("%.2f",$total).'</td>';
            }
    $tableRental .= '
        </tr>
        <tr>
            <td>Total</td>';
            if($rental_maintenance == 0 && $rental_capital ==0 && $additional_maintenance == 0 && $additional_capital ==0){
                $tableRental .= '<td align="center">R'.sprintf("%.2f",$quote2->getRentalCapital("all",$quote2->RentalTerm)).'</td>
                <td align="center">R'.sprintf("%.2f",$quote2->getRentalMaintenance("all",$quote2->RentalTerm, $quote2->MaintenancePercentage)).'</td>
                <td align="center">R'.sprintf("%.2f",$quote2->getRentalAmount("all",$quote2->RentalTerm, $quote2->MaintenancePercentage)).'</td>';
            }else{
               $total_capital = $additional_capital + $rental_capital;
               $total_maintenance = $additional_maintenance + $rental_maintenance;
               $total= $total_maintenance + $total_capital;

               $tableRental .= '<td align="center">R'.sprintf("%.2f",$total_capital).'</td>
                <td align="center">R'.sprintf("%.2f",$total_maintenance).'</td>
                <td align="center">R'.sprintf("%.2f",$total).'</td>';
            }
    $tableRental .= '</tr>
    </table>';

$monthly = '';
$termyears = ceil($quote2->RentalTerm/12);
$monthly .= '1: R'.sprintf("%.2f",$quote2->getRentalAmount("all",$quote2->RentalTerm, $quote2->MaintenancePercentage)).'<br/>';
for ($terms = 1; $terms<$termyears; $terms++){
    $monthly .= ($terms+1).': R'.number_format(($quote2->getRentInMonth("all",$quote2->RentalTerm,$terms*12,$quote2->MaintenancePercentage)), 2).'<br/>';
}

$tableRental2 = '
    <style>
       <link rel="stylesheet" href="pdf/css/layout.css"  type="text/css"/>
    </style>
      <table border="1" cellpadding = "2">
        <tr>
            <th align="center" bgcolor ="#8CC53E">Rental Term</th>
            <th align="center" bgcolor ="#8CC53E">Deposit</th>
            <th align="center" bgcolor ="#8CC53E">Monthly Rental by year</th>
        </tr>
        <tr>
            <td align="center">'.number_format($quote2->RentalTerm, 0). ' Months</td>
            <td align="center">R'.number_format($quote->getDeposit("all"), 2).' ('.$quote->getDepositProportion().'%)</td>
            <td>'.$monthly.'</td>
        </tr>
    </table>';
//--------------------------------------------------------------------------------------
// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Theo Malongete');
$pdf->SetTitle('Ellies Renewable Energy.');
$pdf->SetSubject('Ellies Renewable Energy.');
$pdf->SetKeywords('Ellies, Renewable, Energy');

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
$pdf->setLanguageArray($l);

// ------------------First Page----------------------------------------
$pdf->SetPrintHeader(false);
// add a page
$pdf->AddPage();

if($quote->Approved == 1){
    $title = "QUOTATION";
}else{
    $title = "ESTIMATION";
}

$pdf->SetFont("Helvetica", 'B', 12);
$pdf->SetTextColor(140, 197, 62);
$pdf->MultiCell(180, 3,$title,'', 'R','', 0,'25', '5');

$pdf->SetFont("Helvetica", 'B', 10);

if(date('Y-m-d',strtotime('1970-01-01')) == date('Y-m-d',strtotime($quote->ExpirateDate))){
   $expirationDate = "Valid until: Unknown"; 
}else{
    $expirationDate = "Valid until: " .date('Y-m-d',strtotime($quote->ExpirateDate));
}
$QuoteReferenceNo = "Quote Reference No: ".$quote->QuoteReferenceNo;

$pdf->MultiCell(180, 3,$expirationDate,'', 'R','', 0,'25', '10');
$pdf->MultiCell(180, 3,$QuoteReferenceNo,'', 'R','', 0,'25', '15');

$sysuser = new userType($_SESSION["userid"]);
$branchID = "";
$sql = "SELECT  * FROM `system_users` WHERE `id`=".$sysuser->id." LIMIT 1";       
$sqlres = mysqli_query($GLOBALS["link"],$sql); 
$row = mysqli_fetch_assoc($sqlres);
$branchID = $row['branch_id'];

//Branch
$sql = "SELECT * FROM branches WHERE id ='".$branchID."' LIMIT 1";
$sqlres = mysqli_query($GLOBALS["link"],$sql);
$logo = "";
$companydetails ="";
while($row = mysqli_fetch_assoc($sqlres)) {
    $logo  = $row['logo'];
    if($logo !=""){
        $logo = "../modules/companyprofiles/logos/".$logo;
    }else{
        $logo = "pdf/images/front.jpg";
    }
}
if($logo !="") $pdf->MultiCell(180, 3,'powered by Ellies','', 'R','', 0,'25', '20');

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

$pdf->SetFont("Helvetica", 'B', 15);
$title = ucfirst($companyName);
$pdf->MultiCell(180, 3,$title,'', 'L','', 0,'25', '10');

//Front page
$image_file ='pdf/images/front.jpg';
if($logo !="") {
    $image_file = $logo;
    $pdf->Image($image_file, 10, 100, 200, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
}else{
   $pdf->Image($image_file, 25, 20, 900, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false); 
}
// ------------------Second Page---------------------------------------
//For replacement products
$pdf->SetPrintHeader(true);
// add a page
$pdf->AddPage();

$fontname =  'arial';
$title = 'Cash Financial Analysis';
$pdf->SetFont("Helvetica", 'B', 18);
$pdf->SetTextColor(140, 197, 62);
$pdf->MultiCell(180, 3,$title,'', 'C','', 0,'10', '55');

$fontname =  'arial';
$pdf->SetFont($fontname , 'B', 10);
$pdf->SetTextColor(0,0,0);
$html = '<p>After the lighting survey conducted on site, the following calculations and estimates can be made:</p>';
$pdf->writeHTMLCell(178, 20,16,65,$html, 0, 0, '','', 'L', true);

//Demand for Lighting per hour
$pdf->SetFont($fontname, 'B', 10);
$pdf->SetLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(192, 192, 192)));
$pdf->SetFillColor(153, 153, 153);
$pdf->MultiCell(54, 1,'Demand for Lighting per hour', 1, 'L', 1, 0,'', '80');

$pdf->SetFont($fontname, 'B', 10);
$pdf->SetLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(253, 0, 0)));
$pdf->SetFillColor(253, 0, 0);
$pdf->SetTextColor(252, 247, 247);
$pdf->MultiCell(27, 1,'Before', 1, 'C', 1, 0,'', '84.5');
 //Before value
$demandBefore = $quote->KWhOld*(12/(24*365));
$lighting_before_value = number_format($demandBefore, 3) . " kWh";
$pdf->SetFont($fontname, 'B', 12);
$pdf->SetLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(192, 192, 192)));
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFillColor(255,255, 255);
$pdf->MultiCell(27, 5, $lighting_before_value , 1, 'C', 1, 0,'', '89');

$pdf->SetFont($fontname, 'B', 10);
$pdf->SetLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(140, 197, 62)));
$pdf->SetFillColor(140, 197, 62);
$pdf->SetTextColor(252, 247, 247);
$pdf->MultiCell(27, 1,'After', 1, 'C', 1, 0,'42', '84.5');
 //After value
$demandAfter = $quote->KWhNew*(12/(24*365));
$lighting_after_value = number_format($demandAfter, 3). " kWh";
$pdf->SetFont($fontname, 'B', 12);
$pdf->SetLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(192, 192, 192)));
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFillColor(255,255, 255);
$pdf->MultiCell(27, 5, $lighting_after_value, 1, 'C', 1, 0,'42', '89');

//Electricity Consumption per day
$pdf->SetFont($fontname, 'B', 10);
$pdf->SetLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(192, 192, 192)));
$pdf->SetFillColor(153, 153, 153);
$pdf->SetTextColor(0, 0, 0);
$pdf->MultiCell(54, 1,'Electricity Consumption per day', 1, 'L', 1, 0,'78', '80');

$pdf->SetFont($fontname, 'B', 10);
$pdf->SetLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(253, 0, 0)));
$pdf->SetFillColor(253, 0, 0);
$pdf->SetTextColor(252, 247, 247);
$pdf->MultiCell(27, 1,'Before', 1, 'C', 1, 0,'78', '84.5');
//Before value
$consumptionBefore = $quote->KWhOld*(12/(365));
$electric_before_value = number_format($consumptionBefore, 3). " kWh";
$pdf->SetFont($fontname, 'B', 12);
$pdf->SetLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(192, 192, 192)));
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFillColor(255,255, 255);
$pdf->MultiCell(27, 5, $electric_before_value  , 1, 'C', 1, 0,'78', '89');

$pdf->SetFont($fontname, 'B', 10);
$pdf->SetLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(140, 197, 62)));
$pdf->SetFillColor(140, 197, 62);
$pdf->SetTextColor(252, 247, 247);
$pdf->MultiCell(27, 1,'After', 1, 'C', 1, 0,'105', '84.5');

//After value
$consumptionAfter =$quote->KWhNew*(12/(365));
$electric_after_value = number_format($consumptionAfter , 3). " kWh";
$pdf->SetFont($fontname, 'B', 12);
$pdf->SetLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(192, 192, 192)));
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFillColor(255,255, 255);
$pdf->MultiCell(27, 5, $electric_after_value, 1, 'C', 1, 0,'105', '89');

//Monthly Cost
$pdf->SetFont($fontname, 'B', 10);
$pdf->SetLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(192, 192, 192)));
$pdf->SetFillColor(153, 153, 153);
$pdf->MultiCell(54, 1,'Monthly Cost', 1, 'L', 1, 0,'140', '80');

$pdf->SetFont($fontname, 'B', 10);
$pdf->SetLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(253, 0, 0)));
$pdf->SetFillColor(253, 0, 0);
$pdf->SetTextColor(252, 247, 247);
$pdf->MultiCell(27, 1,'Before', 1, 'C', 1, 0,'140', '84.5');
//Before value
$cost_before_value = "R "  . number_format($quote->MonthlyCostOld, 2);
$pdf->SetFont($fontname, 'B', 12);
$pdf->SetLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(192, 192, 192)));
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFillColor(255,255, 255);
$pdf->MultiCell(27, 5,$cost_before_value, 1, 'C', 1, 0,'140', '89');

$pdf->SetFont($fontname, 'B', 10);
$pdf->SetLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(140, 197, 62)));
$pdf->SetFillColor(140, 197, 62);
$pdf->SetTextColor(252, 247, 247);
$pdf->MultiCell(27, 1,'After', 1, 'C', 1, 0,'167', '84.5');
//After value
$MonthlyCostNew = number_format($quote->MonthlyCostNew, 2);
$cost_after_value = "R "  . number_format($quote->MonthlyCostNew, 2);
$pdf->SetFont($fontname, 'B', 12);
$pdf->SetLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(192, 192, 192)));
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFillColor(255,255, 255);
$pdf->MultiCell(27, 5, $cost_after_value, 1, 'C', 1, 0,'167', '89');
// ---------------------------------------------------------------------------------------------------------------------

//Project Cost ex VAT
$pdf->SetFont($fontname, 'B', 10);
$pdf->SetLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(192, 192, 192)));
$pdf->SetFillColor(153, 153, 153);
$pdf->MultiCell(54, 1,'Project Cost ex VAT', 1, 'L', 1, 0,'', '100');

$project_cost = "R ".number_format($quote->ReplacementPrice, 2);
$pdf->SetFont($fontname, 'B', 12);
$pdf->SetLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(192, 192, 192)));
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFillColor(255,255, 255);
$pdf->MultiCell(54, 1,$project_cost, 1, 'C', 1, 0,'', '104');

//Expected Monthly Saving
$pdf->SetFont($fontname, 'B', 10);
$pdf->SetLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(192, 192, 192)));
$pdf->SetFillColor(153, 153, 153);
$pdf->MultiCell(54, 1,'Expected Monthly Saving', 1, 'L', 1, 0,'78', '100');

$month_saving = "R ".number_format($quote->MonthlyCostSaving, 2);
$pdf->SetFont($fontname, 'B', 12);
$pdf->SetLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(192, 192, 192)));
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFillColor(255,255, 255);
$pdf->MultiCell(54, 1,$month_saving, 1, 'C', 1, 0,'78', '104');

//% Saving
$pdf->SetFont($fontname, 'B', 10);
$pdf->SetLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(192, 192, 192)));
$pdf->SetFillColor(153, 153, 153);
$pdf->MultiCell(54, 1,'% Saving', 1, 'L', 1, 0,'140', '100');

$saving = number_format($quote->KWhSavedPerc, 0) . "%";
$pdf->SetFont($fontname, 'B', 12);
$pdf->SetLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(192, 192, 192)));
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFillColor(255,255, 255);
$pdf->MultiCell(54, 1,$saving, 1, 'C', 1, 0,'140', '104');
// ---------------------------------------------------------------------------------------------------------------------
//Breakeven
$pdf->SetFont($fontname, 'B', 12);
$pdf->SetLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(49, 195, 231)));
$pdf->SetFillColor(49, 195, 231);
$pdf->SetTextColor(252, 247, 247);
//$pdf->MultiCell(180, 1,'Breakeven', 1, 'C', 1, 0,'', '174');

$text = "The breakeven point is calculated as the amount of time (in months) that the savings achieved from retrofitting old technologies will take to recover the initial project expense.";
$pdf->SetFont($fontname, 'B', 10);
$pdf->SetLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(49, 195, 231)));
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFillColor(255,255, 255);
$pdf->MultiCell(180, 10,$text, 1, 'C', 1, 0,'', '190');

//$pdf->MultiCell(120, 15,$text, 1, 'L', 1, 0,'74', '170');

//================================================================
//Graph
require_once('jpgraph/src/jpgraph.php');;
require_once ('jpgraph/src/jpgraph_bar.php');
require_once ('jpgraph/src/jpgraph_line.php');
require_once ('jpgraph/src/jpgraph_utils.inc.php');

$path =dirname($_SERVER["SCRIPT_FILENAME"]);
//Graph: Ellies Solution
//==================================================
// Create the graph. These two calls are always required
$graph = new Graph(1000,1500);  
$graph->SetScale("intlin");
$graph->SetShadow();
$graph->xaxis->SetTextTickInterval(2,0);
$graph->img->SetMargin(40,30,40,40);
$graph->SetFrame(false);

//=Total_Existing_KWh_All*$KWhPrice*POWER(1+Electricity_Price_Escalation_Rate,ROUNDDOWN((A5-1)/12,0))
//=Total_Replacement_KWh_All*$KWhPrice*POWER(1+Electricity_Price_Escalation_Rate,ROUNDDOWN((A5-1)/12,0))
$do_nothing = array();
$$do_ellies = array();
$Total_Existing_KWh_All = $quote->KWhOld;
$Total_Replacement_KWh_All = $quote2->KWhNewNoAdditional;
$ElectricityEscalationRatePercentage  = $quote2->ElectricityEscalationRatePercentage / 100;
$KWhPrice =  $quote2->KWhPrice;

//echo "Total_Existing_KWh_All:" .$Total_Existing_KWh_All  ."<br>";
//echo "Total_Replacement_KWh_All:" .$Total_Replacement_KWh_All ."<br>";
//echo "ElectricityEscalationRatePercentage:" .$ElectricityEscalationRatePercentage ."<br>";
//echo "KWhPrice:" .$KWhPrice ."<br>";

$labourCosts = 0;
$labourTotal = 0;
if($quote->DoInstall == 1){
    $qtyLabour = 1;
    $labourCosts = $quote->LabourCostsReplacement;
    $labourTotal = $qtyLabour * $labourCosts;
}
$travelTotal = number_format($quote->TravelCostsReplacement, 2);
$crushTotal = number_format($quote->CrushCostsReplacement, 2);
$materialTotal = number_format($quote->MaterialsCostsReplacementCost, 2);
$Total_Price_Replacement_With_All_Additional_Charges = number_format($quote->ReplacementPrice, 2,".","");

/*echo"<br>";
echo "travelTotal:" .$travelTotal  ."<br>";
echo "crushTotal:" .$crushTotal ."<br>";
echo "materialTotal:" .$materialTotal ."<br>";
echo "labourTotal:" .$labourTotal ."<br>";
echo"<br>";
echo "Total_Price_Replacement_With_All_Additional_Charges:" .$Total_Price_Replacement_With_All_Additional_Charges."<br>";*/
$ellies = $Total_Price_Replacement_With_All_Additional_Charges;
//Get months
$str = trim(str_replace("Months", "", $quote->TotalPaybackPeriodFormatted));
if($str !==""){
  $value = (double)$str;
  $months  = number_format($value,0);
  $months = ceil($months);
  $months = ($months+5);
  //echo "months:". $months ;
  //exit();
  $do_ellies[] = number_format($ellies,0,'.','');
  $do_nothing[] =0;

 // echo "starting point:".$ellies."<br>";
 // echo "starting point:".$do_ellies[0]."<br>";

  for($i=1;$i<=$months;$i++){
    $num = ($i-1)/12;
    $num =  floor($num);
    $pow= pow(1+$ElectricityEscalationRatePercentage,$num);

    $value = $Total_Existing_KWh_All*$KWhPrice*$pow;
    $value  = number_format($value,0,'.','');

    $do_nothing[] = $do_nothing[$i-1] + $value;

    $ellies = $Total_Replacement_KWh_All*$KWhPrice*$pow;
    $ellies  = number_format($ellies,0,'.','');
    $do_ellies[] = $do_ellies[$i-1] + $ellies;
  }
}else{
    $do_nothing[] =0;
    $do_ellies[] =0;   
}

/*print "<h1>Graph: Ellies Solution</h1>";
for($i=0;$i<=count($do_nothing);$i++){
    print   $do_nothing[$i];
    print "<br>";
}
    print "<br>";

for($i=0;$i<=count($do_ellies);$i++){
    print   $do_ellies[$i];
    print "<br>";
}*/
//exit();

// Create the linear plot
$lineplot = new LinePlot($do_nothing);
$lineplot2 = new LinePlot($do_ellies);

//Add the plot to the graph
$graph->Add($lineplot);
$graph->Add($lineplot2);

$lineplot->SetLegend("Do nothing");
$lineplot->SetColor("red");
$lineplot->SetWeight(3);
$lineplot->SetStyle("solid");
$lineplot2->SetLegend("Do Ellies Solution");
$lineplot2->SetColor("blue");
$lineplot2->SetWeight(3);
$lineplot2->SetStyle("solid");

$graph->img->SetAntiAliasing(false); 

$graph->legend->SetLayout(LEGEND_HOR);
//$graph->legend->Pos(0.4,0.95,"center","bottom");
$graph->legend->SetPos(0.5,0.90,'center','bottom');

$graph->xaxis->title->Set("Months");
$graph->yaxis->title->Set("Rands");

$graph->title->SetFont(FF_ARIAL,FS_NORMAL,20);
$graph->yaxis->title->SetFont(FF_ARIAL,FS_NORMAL,12);
$graph->xaxis->title->SetFont(FF_ARIAL,FS_NORMAL,12);

$graph->legend->SetFrameWeight(1);

$graph->img->SetImgFormat('jpeg');
$graph->img->SetQuality(75);
// Display the graph
$file = ($path.'/printquotes/graphs/Solution_'.$quoteid.'.jpg');
if(file_exists($file)){
    unlink($file);
    $graph->Stroke($file);
}else{
    $graph->Stroke($file);
}
$image_file = $file;
$pdf->Image($image_file,20, 110, 170, 79, 'JPG', '', 'T', true, 300, '', false, false, 0, false, false, false);

// ---------------------------------------------------------------------------------------------------------------------
//How the calculations are done (unrelated example)
$pdf->SetFont($fontname , 'B', 15);
$pdf->SetTextColor(140, 197, 62);
$html = '<p>How the calculations are done (unrelated example)</p>';
$pdf->writeHTMLCell(178, 20,16,201,$html, 0, 0, '','', 'C', true);

$pdf->SetFont($fontname , 'B', 7);
$pdf->SetTextColor(0, 0, 0);
$html = '<p>The running costs indicated below is based on 10 burning hours per day (07:00 until 17:00) for 25 days per month.  A cost of R1.00 per kWh is assumed.</p>';
$pdf->writeHTMLCell(178, 20,16,202,$html, 0, 0, '','', 'C', true);

//Images
$image_file ='pdf/images/36W.jpg';
$pdf->Image($image_file,50, 215, 20, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
$image_file ='pdf/images/20W.jpg';
$pdf->Image($image_file,75, 218, 20, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);

$image_file ='pdf/images/60W.jpg';
$pdf->Image($image_file,100,218, 20, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
$image_file ='pdf/images/7W.jpg';
$pdf->Image($image_file,120, 217,20, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);

$image_file ='pdf/images/50W.jpg';
$pdf->Image($image_file,150, 220, 20, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
$image_file ='pdf/images/6W.jpg';
$pdf->Image($image_file,170, 213, 20, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);

//Before value
$pdf->SetFont($fontname, 'B', 12);
$pdf->SetLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(140, 197, 62)));
$pdf->SetFillColor(140, 197, 62);
$pdf->SetTextColor(252, 247, 247);
$pdf->MultiCell(23, 5,'0.036 kWh' , 1, 'C', 1, 0,'47', '248');
//After value
$pdf->SetFont($fontname, 'B', 12);
$pdf->SetLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(253, 0, 0)));
$pdf->SetFillColor(253, 0, 0);
$pdf->SetTextColor(252, 247, 247);
$pdf->MultiCell(23, 1,'0.020 kWh', 1, 'C', 1, 0,'70', '248');

//Before value
$pdf->SetFont($fontname, 'B', 12);
$pdf->SetLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(140, 197, 62)));
$pdf->SetFillColor(140, 197, 62);
$pdf->SetTextColor(252, 247, 247);
$pdf->MultiCell(23, 5,'0.060 kWh' , 1, 'C', 1, 0,'97', '248');
//After value
$pdf->SetFont($fontname, 'B', 12);
$pdf->SetLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(253, 0, 0)));
$pdf->SetFillColor(253, 0, 0);
$pdf->SetTextColor(252, 247, 247);
$pdf->MultiCell(23, 1,'0.007 kWh', 1, 'C', 1, 0,'120', '248');

//Before value
$pdf->SetFont($fontname, 'B', 12);
$pdf->SetLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(140, 197, 62)));
$pdf->SetFillColor(140, 197, 62);
$pdf->SetTextColor(252, 247, 247);
$pdf->MultiCell(23, 5,'0.050 kWh' , 1, 'C', 1, 0,'148', '248');
//After value
$pdf->SetFont($fontname, 'B', 12);
$pdf->SetLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(253, 0, 0)));
$pdf->SetFillColor(253, 0, 0);
$pdf->SetTextColor(252, 247, 247);
$pdf->MultiCell(23, 1,'0.006 kWh', 1, 'C', 1, 0,'171', '248');

$pdf->SetFont($fontname, 'B', 8);
$pdf->SetTextColor(0, 0, 0);
$pdf->MultiCell(50, 3, 'Cost per Hour:','', 'L','', 0,'15', '256');
$pdf->MultiCell(50, 3, 'Cost per Day','', 'L','', 0,'15', '262');
$pdf->MultiCell(50, 3, 'Cost per Month','', 'L','', 0,'15', '268');
$pdf->SetFont($fontname, 'B', 8);
//Compare1
$pdf->MultiCell(50, 3, '≈ R0.04','', 'L','', 0,'52', '256');
$pdf->MultiCell(50, 3, '= R0.36','', 'L','', 0,'52', '262');
$pdf->MultiCell(50, 3, '= R9.00','', 'L','', 0,'52', '268');
$pdf->MultiCell(50, 3, '≈ R0.02','', 'L','', 0,'75', '256');
$pdf->MultiCell(50, 3, '= R0.20','', 'L','', 0,'75', '262');
$pdf->MultiCell(50, 3, '= R5.00','', 'L','', 0,'75', '268');

//Compare2
$pdf->MultiCell(50, 3, '≈ R0.06','', 'L','', 0,'100', '256');
$pdf->MultiCell(50, 3, '= R0.60','', 'L','', 0,'100', '262');
$pdf->MultiCell(50, 3, '= R15.00','', 'L','', 0,'100', '268');
$pdf->MultiCell(50, 3, '≈ R0.01','', 'L','', 0,'125', '256');
$pdf->MultiCell(50, 3, '= R0.07','', 'L','', 0,'125', '262');
$pdf->MultiCell(50, 3, '= R1.75','', 'L','', 0,'125', '268');

//Compare3
$pdf->MultiCell(50, 3, '≈ R0.05','', 'L','', 0,'155', '256');
$pdf->MultiCell(50, 3, '= R0.50','', 'L','', 0,'155', '262');
$pdf->MultiCell(50, 3, '= R12.50','', 'L','', 0,'155', '268');
$pdf->MultiCell(50, 3, '≈ R0.01','', 'L','', 0,'175', '256');
$pdf->MultiCell(50, 3, '= R0.06','', 'L','', 0,'175', '262');
$pdf->MultiCell(50, 3, '= R1.50','', 'L','', 0,'175', '268');

// ------------------Third Page----------------------------------------
$pdf->SetPrintHeader(true);
// add a page
$pdf->AddPage();
$fontname =  'arial';
$title = 'Cash Graphs';
$pdf->SetFont("Helvetica", 'B', 15);
$pdf->SetTextColor(140, 197, 62);

//Replacement Items Overview
$pdf->SetFont("Helvetica", 'B', 15);
$pdf->SetTextColor(140, 197, 62);
$pdf->MultiCell(180, 3,'Replacement Items Overview','', 'L','', 0,'15', '55'); 
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont("Helvetica", 'B', 14);
$pdf->writeHTMLCell(0, 0,13,65,$tableCash2, 0, 0, '','', 'L', true);

//Cost Overview - Cash Option
$pdf->SetFont("Helvetica", 'B', 15);
$pdf->SetTextColor(140, 197, 62);
$pdf->MultiCell(180, 3,'Cost Overview - Cash Option','', 'L','', 0,'15', '90'); 
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont("Helvetica", 'B', 14);
$pdf->writeHTMLCell(0, 0,13,100,$tableCash, 0, 0, '','', 'L', true);

//Graph: Project Savings
//==================================================
// Create the graph. These two calls are always required
$graph = new Graph(1000,1000);  
$graph->SetScale("intlin");
$graph->SetShadow();
$graph->xaxis->SetTextTickInterval(2,0);
$graph->img->SetMargin(40,30,20,40);


$str = trim(str_replace("Months", "", $quote->TotalPaybackPeriodFormatted));
$Total_Saved_KWh_All_Annual = $quote2->KWhSaved * $quote2->KWhPrice;
$ElectricityEscalationRatePercentage  = $quote2->ElectricityEscalationRatePercentage / 100;
$values = array();
$values[] =number_format(-$Total_Price_Replacement_With_All_Additional_Charges,0,'.','');
if($str !==""){
  $value = (double)$str;
  $months  = number_format($value,0);
  for($i=1;$i<=$months;$i++){
    $num = ($i-1)/12;
    $pow= pow(1+$ElectricityEscalationRatePercentage,$num);

    $value = number_format($Total_Saved_KWh_All_Annual*$pow,0,'.','');
    $values[] = $values[$i-1] + $value;
  }
}else{
    $values[] =0;
}

// Create the linear plot
$lineplot = new LinePlot($values);

// Add the plot to the graph
$graph->Add($lineplot);

$lineplot->SetLegend("Project Savings");
$lineplot->SetColor("gray4");
$lineplot->SetWeight(2);
$lineplot->SetStyle("solid");

$graph->img->SetAntiAliasing(false); 
$graph->title->Set("Project Savings");
$graph->xaxis->title->Set("Months");
$graph->yaxis->title->Set("Rands");
$graph->xaxis->SetPos("min"); 

$graph->title->SetFont(FF_ARIAL,FS_NORMAL,20);
$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);

$graph->img->SetImgFormat('jpeg');
$graph->legend->SetFont(FF_ARIAL,FS_NORMAL,15);
$graph->legend->SetFrameWeight(1);

// Display the graph
$file = ($path.'/printquotes/graphs/Savings_'.$quoteid.'.jpg');
if(file_exists($file)){
    unlink($file);
    $graph->Stroke($file);
}else{
    $graph->Stroke($file);
}
$image_file = $file;
$pdf->Image($image_file,30, 130, 150, 140, 'JPG', '', 'T', true, 300, '', false, false, 0, false, false, false);
// ------------------Third Page----------------------------------------
$pdf->SetPrintHeader(true);

//Cost Overview - Rental Option
$sql = "SELECT `rental_option`";
$sql.= " FROM `quote_extras`";
$sql.= " WHERE `quote_id` = '".mysqli_real_escape_string($GLOBALS["link"],$quoteid)."' LIMIT 1";
$sqlres = mysqli_query($GLOBALS["link"],$sql);
$rental_option="";
while($row = mysqli_fetch_assoc($sqlres)) {
    $rental_option = $row['rental_option'];
}

if($rental_option == "0"){
    $sysuser = new userType($_SESSION["userid"]);
    $sql = "SELECT `type` FROM `channels` WHERE `id`=".$sysuser->retailChannel;
    $sqlres = mysqli_query($GLOBALS["link"],$sql);
    $row = mysqli_fetch_assoc($sqlres);
    $channeltype = $row['type'];

    if ($channeltype != "Franchises"){
        // add a page
        $pdf->AddPage();

        $fontname =  'arial';
        $title = 'Rental Financial Analysis';
        $pdf->SetFont("Helvetica", 'B', 15);
        $pdf->SetTextColor(140, 197, 62);
        $pdf->MultiCell(180, 3,$title,'', 'C','', 0,'10', '50'); 


        $pdf->SetFont("Helvetica", 'B', 15);
        $pdf->SetTextColor(140, 197, 62);
        $pdf->MultiCell(180, 3,'Cost Overview - Rental Option','', 'L','', 0,'10', '60'); 
        $pdf->SetTextColor(0, 0, 0);

        $pdf->writeHTMLCell(0, 0,13,70,$tableRental, 0, 0, '','', 'L', true);

        $pdf->SetFont("Helvetica", 'B', 10);
        $pdf->SetTextColor(140, 197, 62);
        $pdf->MultiCell(180, 3,'Contract Overview','', 'L','', 0,'15', '95'); 
        $pdf->SetTextColor(0, 0, 0);

        $pdf->writeHTMLCell(0, 0,13,105,$tableRental2, 0, 0, '','', 'L', true);  
    }
}
//========================================================================
//Graph
if($rental_option == "0"){
    if ($channeltype != "Franchises"){

        //Graph:Monthly Savings versus Monthly Rental Amount
        //==================================================
        //Monthly savings
        $data1y=array();
        $Total_Saved_KWh_All_Annual = $quote2->KWhSaved * 12;
        $ElectricityEscalationRatePercentage  = $quote2->ElectricityEscalationRatePercentage / 100;
        for($i=0;$i<$termyears;$i++){
          $pow= pow(1+$ElectricityEscalationRatePercentage ,($i+1));
          $value = $Total_Saved_KWh_All_Annual*$pow;
          $value  = number_format($value,2,'.','');
          //$value  = str_replace(',', '.',   $value );
          $data1y[] =$value;
        }

        //Monthly rental
        $data2y=array();
        for($i=0;$i<$termyears;$i++){
          $data2y[] = number_format($quote2->getRentalAmount("all",$quote2->RentalTerm, $quote2->MaintenancePercentage),2,'.','');  
        }

        list($tickPositions,$minTickPositions) =  DateScaleUtils::GetTicks($data1y);
        $n = count($data1y);
        $grace = 400000;
        $xmin = $data1y[0] - $grace ;
        $xmax = $data1y[$n-1] + $grace ;


        // Create the graph. These two calls are always required
        $graph = new Graph(800,800);    
        $graph->SetScale("intlin");
        $graph->SetShadow();
        $graph->img->SetMargin(40,30,20,40);

        // Create the bar plots
        $b1plot = new BarPlot($data1y);
        $b2plot = new BarPlot($data2y);

        // Create the grouped bar plot
        $gbplot = new GroupBarPlot(array($b1plot,$b2plot));
        //Add graph
        $graph->Add($gbplot);

        $b1plot->SetFillColor("#9ACD32");
        $b2plot->SetFillColor("gray");


        $graph->title->Set("Monthly Savings versus Monthly Rental Amount");
        $graph->xaxis->title->Set("Years");
        $graph->yaxis->title->Set("Rands");

        $graph->title->SetFont(FF_ARIAL,FS_NORMAL,20);
        $graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
        $graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);


        $b1plot->SetLegend("Monthly Savings");
        $b2plot->SetLegend("Monthly Rental");
        //$graph->legend->Pos(0.05,0.5,"right","right");
        $graph->legend->SetFrameWeight(1);

        $graph->img->SetQuality(5);
        $graph->img->SetImgFormat('jpeg');

        // Display the graph
        $file = ($path.'/printquotes/graphs/Month_'.$quoteid.'.jpg');
        if(file_exists($file)){
            unlink($file);
            $graph->Stroke($file);
        }else{
            $graph->Stroke($file);
        }
        $image_file = $file;

         $pdf->Image($image_file,30, 135, 150, 108, 'JPG', '', 'T', true, 300, '', false, false, 0, false, false, false);
        //======================================================================
        //Rent per Month
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont($fontname, 'B', 12);
        $pdf->SetLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(192, 192, 192)));
        $pdf->SetFillColor(153, 153, 153);

        $pdf->MultiCell(54, 1,'Rent per Month', 1, 'L', 1, 0,'', '245');

        $text ='R'.number_format($quote2->getRentalAmount("all",$quote2->RentalTerm, $quote2->MaintenancePercentage),2) ." is the Rental option subject to approval by a third party financier over a period of 36 Months";
        $pdf->SetFont($fontname, 'B', 10);
        $pdf->SetLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(192, 192, 192)));
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFillColor(255,255, 255);

        // $pdf->MultiCell(54, 10,$text, 1, 'C', 1, 0,'', '230.5');
        $pdf->MultiCell(54, 10,$text, 1, 'C', 1, 0,'', '250');

        $customerid = 0;

        if ($quoteid == "0") {
            $quote->CustomerID = $_GET["customerid"];
            $quote->Save();
        }
        $customerid = $quote->CustomerID;

        $customer = new Customer($quote->CustomerID);
        $companyName = $customer->CompanyName;

        $value4 = sprintf("%.2f",$quote2->getRentalAmount("all",$quote2->RentalTerm, $quote2->MaintenancePercentage));
        $value3 = $quote2->getRentalAmount("all",$quote2->RentalTerm, $quote2->MaintenancePercentage)+$MonthlyCostNew;
        $value5 = 'R'.number_format($value3, 2);
        $pdf->SetFont($fontname, '', 10);
        $text = 'If the project were not completed,'. $companyName . ' would continue paying as much as '. $cost_before_value .' per month for lighting. But by completing the project, this amount can decrease to as little as ' .$cost_after_value. ' each month. Even if you rent the equipment for R' . $value4 .' over 36 months, you end up paying only '. $value5 . ' monthly which, after the 36 months, becomes money in your pockets.';
        $pdf->SetLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(192, 192, 192)));
        $pdf->SetTextColor(255, 0, 0);
        $pdf->SetFillColor(255,255, 255);
        //  $pdf->MultiCell(120, 16,$text, 1, 'L', 1, 0,'74', '225');
         $pdf->MultiCell(120, 16,$text, 1, 'L', 1, 0,'74', '245');
     }
}
//----------------------------------------------------------------------------------------------------
//Customer details
$booktitle = "Edit Quote";

$customerid = 0;

$expirationDate = $quote->ExpirateDate;

if ($quoteid == "0") {
    $quote->CustomerID = $_GET["customerid"];
    $quote->Save();
}
$customerid = $quote->CustomerID;

$isnew = false;
$customer = new Customer($quote->CustomerID);
$companyName = $customer->CompanyName;

//Region
$sql = "SELECT `branch_id`";
$sql.= " FROM `system_users`";
$sql.= " WHERE `system_users`.id = '".mysqli_real_escape_string($GLOBALS["link"],$quote->CreatedBy)."' LIMIT 1";
$sqlres = mysqli_query($GLOBALS["link"],$sql);
$branch_id='';
while($row = mysqli_fetch_assoc($sqlres)) {
    $branch_id = $row['branch_id'];
}  
$Region = "NONE";
$sql = "SELECT `branch`";
$sql.= " FROM `branches`";
$sql.= " WHERE `branches`.id = '".mysqli_real_escape_string($GLOBALS["link"],$branch_id)."' LIMIT 1";
$sqlres = mysqli_query($GLOBALS["link"],$sql);
 while($row = mysqli_fetch_assoc($sqlres)) {
    $Region = $row['branch'];
}
//Electricity Supplier
$ElectricSupplier = "NONE";
$sql = "SELECT `Name`";
$sql.= " FROM `electrical_supplier`";
$sql.= " WHERE `electrical_supplier`.id = '".mysqli_real_escape_string($GLOBALS["link"],$quote->ElectricSupplier)."' LIMIT 1";

$sqlres = mysqli_query($GLOBALS["link"],$sql);
while($row = mysqli_fetch_assoc($sqlres)) {
    $ElectricSupplier = $row['Name'];
} 
//===================================================
$pdf->SetPrintHeader(true);
// add a page
$pdf->AddPage();

$fontname =  'arial';
$title = 'Project Details';
$pdf->SetFont("Helvetica", 'B', 15);
$pdf->SetTextColor(140, 197, 62);
$pdf->MultiCell(180, 3,$title,'', 'C','', 0,'10', '55');

$pdf->SetFont($fontname, '', 10);
$pdf->MultiCell(100, 3, "Type of Premises   (Mark with x):",'', 'L','', 0,'13', '70');
$pdf->MultiCell(100, 3, "Business",'', 'L','', 0,'70', '70');
$pdf->MultiCell(100, 3, "Home",'', 'L','', 0,'100', '70');
$pdf->MultiCell(100, 3, "Other",'', 'L','', 0,'125', '70');

$pdf->MultiCell(100, 3, "Company Name",'', 'L','', 0,'13', '85');
$pdf->MultiCell(100, 3, "Physical Address",'', 'L','', 0,'13', '95');
$pdf->MultiCell(100, 3, "Telephone",'', 'L','', 0,'13', '115');

$pdf->MultiCell(100, 3, "Contact Person",'', 'L','', 0,'110', '85');
$pdf->MultiCell(100, 3, "Telephone",'', 'L','', 0,'110', '95');
$pdf->MultiCell(100, 3, "E-mail",'', 'L','', 0,'110', '105');
$pdf->MultiCell(100, 3, "Date",'', 'L','', 0,'110', '115');

$pdf->SetTextColor(0, 0,0);

//Classifying Type of Premises
$homeArr = array("House","Big House","Public Service");
$businessArr = array("Business park","Retail Shop","Big Shop","Restaurant","Office");
$otherArr = array("Public Service Building","Public Building","Warehouse Style","Hospital","Clinic","Other");

$sql = "SELECT `Type`";
$sql.= " FROM `property_types`";
$sql.= " WHERE `property_types`.id = '".mysqli_real_escape_string($GLOBALS["link"],$quote->PropertyID)."' LIMIT 1";
$sqlres = mysqli_query($GLOBALS["link"],$sql);
$Property ='';
while($row = mysqli_fetch_assoc($sqlres)) {
    $Property = $row['Type'];
} 

if(in_array($Property , $homeArr)){
    $home = true;
}else{
    $home = false;
}

if(in_array($Property , $businessArr)){
    $business = true;
}else{
    $business = false;
}

if(in_array($Property , $otherArr)){
    $other = true;
}else{
    $other = false;
}

if($business == true){
    $pdf->MultiCell(100, 3, "x",'', 'L','', 0,'88','70');
}else{
   $pdf->MultiCell(100, 3, "",'', 'L','', 0,'88','70');
}

if($home == true){
   $pdf->MultiCell(100, 3, "x",'', 'L','', 0,'115','70');
}else{
   $pdf->MultiCell(100, 3, "",'', 'L','', 0,'115','70');
}

if($other == true){
   $pdf->MultiCell(100, 3, "x",'', 'L','', 0,'138','70');
}else{
   $pdf->MultiCell(100, 3, "",'', 'L','', 0,'138','70');
}

$companyName = $customer->CompanyName;
$companyTelephone = $customer->Company_Telephone;
if(empty($companyTelephone)){
   $companyTelephone="NONE"; 
}
$streetAddress = $quote->Address;
$contactPerson = $customer->Name." ".$customer->Surname;
$telephone = $customer->CellPhone;
$email = $customer->Email;
$html = '<input type="checkbox" name="agree" value="1" checked="checked" />';

$pdf->MultiCell(100, 3, $companyName,'', 'L','', 0,'50','83');
$pdf->MultiCell(100, 3, $streetAddress,'', 'L','', 0,'50','95');
$pdf->MultiCell(100, 3, $companyTelephone,'', 'L','', 0,'50','115');
$pdf->MultiCell(100, 3, $contactPerson,'', 'L','', 0,'140','85');
$pdf->MultiCell(100, 3, $telephone,'', 'L','', 0,'140','95');
$pdf->MultiCell(100, 3, $email,'', 'L','', 0,'140','105');
$pdf->MultiCell(100, 3, date('Y-m-d'),'', 'L','', 0,'140','115');

$projectManager = "";
$installer = "";
$salesRep = "";
$type_connection =  ""; 
$meter_number = "";
$height = 0;
$contingency =0;
$notes = "";
$query = "SELECT * FROM `pdf_required_fields` WHERE `quote_id` = '" . $quoteid . "'";
$result = mysqli_query($GLOBALS["link"],$query);
while($row = mysqli_fetch_assoc($result)) {  
    $QuoteID = $row['quote_id'];
    $projectManager =  $row['project_manager']; 
    $installer =  $row['installer']; 
    $salesRep =  $row['sales_rep']; 
    $type_connection =  $row['type_connection']; 
    $meter_number =  $row['meter_number']; 
    $height =  $row['ceiling_height']; 
    $contingency =  $row['contingency']; 
    $notes =  $row['notes'];

}

$title = 'Additional Information';
if($projectManager ==""){
    $projectManager = "None";
}
if($installer ==""){
    $installer = "None";
}
if($salesRep ==""){
    $salesRep = "None";
}

$electricitySupplier =  $ElectricSupplier;
$cost = "R " . $quote->KWhPrice;
$region = $Region;

$pdf->SetTextColor(140, 197, 62);
$pdf->SetFont("Helvetica", 'B', 15);
$pdf->MultiCell(180, 3,$title,'', 'C','', 0,'10', '125');

$pdf->SetFont($fontname, '', 10);
$pdf->MultiCell(100, 3, "Project Manager",'', 'L','', 0,'13', '140');
$pdf->MultiCell(100, 3, "Installer",'', 'L','', 0,'13', '150');
$pdf->MultiCell(100, 3, "Sales Rep",'', 'L','', 0,'13', '160');
$pdf->MultiCell(100, 3, "Electricity Supplier",'', 'L','', 0,'13', '170');
$pdf->MultiCell(100, 3, "Cost per kWh",'', 'L','', 0,'13', '180');

$pdf->MultiCell(100, 3, "Region",'', 'L','', 0,'110', '140');
$pdf->MultiCell(100, 3, "Type of Connection",'', 'L','', 0,'110', '170');
$pdf->MultiCell(100, 3, "Meter Number",'', 'L','', 0,'110', '180');

$pdf->SetTextColor(0, 0,0);
$pdf->MultiCell(100, 3, $projectManager,'', 'L','', 0,'50','139');
$pdf->MultiCell(100, 3, $installer,'', 'L','', 0,'50','149');
$pdf->MultiCell(100, 3, $salesRep,'', 'L','', 0,'50','159');
$pdf->MultiCell(100, 3, $electricitySupplier,'', 'L','', 0,'50','169');
$pdf->MultiCell(100, 3, $cost,'', 'L','', 0,'50','179');
$pdf->MultiCell(100, 3, $region,'', 'L','', 0,'140','139');

//Type of Connection
$single_phase = false;
$three_phase = false;
if($type_connection == "Single Phase"){
       $single_phase = true;
}else if($type_connection == "Three Phase"){
    $three_phase = true;
}
$pdf->SetFont($fontname, '', 8);
$pdf->MultiCell(100, 3, "Single Phase",'', 'L','', 0,'145', '170');
$pdf->MultiCell(100, 3, "Three Phase",'', 'L','', 0,'170', '170');
if($single_phase == true){
   $pdf->MultiCell(100, 3, "x",'', 'L','', 0,'165','169');
}else{
   $pdf->MultiCell(100, 3, "",'', 'L','', 0,'165','169');
}
if($three_phase == true){
   $pdf->MultiCell(100, 3, "x",'', 'L','', 0,'190','169');

}else{
   $pdf->MultiCell(100, 3, "",'', 'L','', 0,'190','169');
}

if($meter_number ==""){
    $meter_number = "None";
}
$pdf->SetFont($fontname, '', 10);
$pdf->MultiCell(100, 3, $meter_number,'', 'L','', 0,'140','179');

$title = 'Consumption Data and Installation Details';

$electricity_accounts = "NONE";
$travel =$quote->Distance . " ";

$height = number_format($height,2);
$contingency = number_format($contingency,2);

$pdf->SetTextColor(140, 197, 62);
$pdf->SetFont("Helvetica", 'B', 15);
$pdf->MultiCell(180, 3,$title,'', 'C','', 0,'10', '195');

$pdf->SetFont($fontname, '', 10);
$pdf->MultiCell(100, 3, "Electricity Accounts",'', 'L','', 0,'13', '210');
$pdf->MultiCell(100, 3, "Travel (km)",'', 'L','', 0,'70', '210');
$pdf->MultiCell(100, 3, "Ceiling Height",'', 'L','', 0,'120', '210');
$pdf->MultiCell(100, 3, "Contingency",'', 'L','', 0,'170', '210');

$pdf->SetTextColor(0, 0,0);
//$pdf->TextField('accounts', 30, 6, array(), array('v'=>$electricity_accounts,'dv'=>$electricity_accounts),'13','215');

$pdf->MultiCell(100, 3, $travel,'', 'L','', 0,'70','215');
$pdf->MultiCell(100, 3, $height,'', 'L','', 0,'120','215');
$pdf->MultiCell(100, 3, $contingency,'', 'L','', 0,'170','215');

/*Removed 
$pdf->SetTextColor(140, 197, 62);
$pdf->MultiCell(100, 3, "Other Expenses",'', 'L','', 0,'130', '225');
$pdf->SetTextColor(0, 0,0);
$pdf->MultiCell(100, 3, "Access Equipment",'', 'L','', 0,'135', '230');
$pdf->MultiCell(100, 3, "COC",'', 'L','', 0,'135', '235');
$pdf->MultiCell(100, 3, "Site Survey",'', 'L','', 0,'135', '240');

$access_equipment = 0;
$coc = 0;
$sitesurvey = 0;
$pdf->TextField('equipment', 20, 5, array('readonly'=>true), array('v'=>$access_equipment. " ",'dv'=>$access_equipment. " "),'170','230');
$pdf->TextField('coc', 20, 5, array('readonly'=>true), array('v'=>$coc. " ",'dv'=>$coc. " "),'170','235');
$pdf->TextField('sitesurvey', 20, 5, array('readonly'=>true), array('v'=>$sitesurvey. " ",'dv'=>$sitesurvey. " "),'170','240');*/

$pdf->SetTextColor(140, 197, 62);
$pdf->MultiCell(100, 3, "Installation",'', 'L','', 0,'70', '230');
$pdf->MultiCell(100, 3, "Notes:",'', 'L','', 0,'13', '240');
$pdf->SetTextColor(0, 0,0);

if($quote->DoInstall == 1){
   $installation = true; 
}else{$installation = false;}

if($installation == true){
   $pdf->MultiCell(100, 3, "x",'', 'L','', 0,'90','230');

}else{
  $pdf->MultiCell(100, 3, "x",'', 'L','', 0,'90','230');
}

if($notes ==""){
    $notes = "None";
}
$pdf->SetFont($fontname, 'B', 10);
$pdf->SetLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(192, 192, 192)));
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFillColor(255,255, 255);
$pdf->MultiCell(150,30, $notes  , 1, 'L', 1, 0,'30', '240');
//----------------------------------------------------------------
//Additional products
$sql = "SELECT *";
$sql.= " FROM `quote_items`";
$sql.= " WHERE `quote_id` = '".mysqli_real_escape_string($GLOBALS["link"],$quoteid)."' ORDER BY `id`";
$sqlres = mysqli_query($GLOBALS["link"],$sql);
$records = "";
$sumTotal = 0;
$quantity = 0;
$arrAdditonal = array();
while($row = mysqli_fetch_assoc($sqlres)) {
    $ID = (int) $row["id"];
    $old_product_id = $row["old_product_id"]."<br/>";
    $new_product_id = $row["new_product_id"]."<br/>";
    if($old_product_id == -1){
        $sql = "SELECT `code`,`product`,`cost`";
        $sql.= " FROM `new_products`";
        $sql.= " WHERE `id` = '".mysqli_real_escape_string($GLOBALS["link"],$new_product_id)."' ORDER BY `id`";
        $sqlres2 = mysqli_query($GLOBALS["link"],$sql);
        $row2 = mysqli_fetch_assoc($sqlres2);
        $codeAdditional = $row2["code"];
        $productAdditional =  $row2["product"];
        $costAdditional =  $row2["cost"];
        $extend_cost = 1 * $costAdditional;
        $sumTotal +=$extend_cost;
        if(!empty($codeAdditional)){
            $arrAdditonal[] = $codeAdditional;
            $records .= $codeAdditional.",".$productAdditional.",1,".$costAdditional.",".$extend_cost .";";
        }
    } 
}

//Generate the number of items
$numberItems =  count(explode(";" , $records)) + 3 ;
$end = $numberItems - 4;
$items = '';
if(count(explode(";" , $records)) == 1)
{
  for ($x = 0; $x < 16; $x++) {
       if($x == 12)
       {
         $pdf->SetTextColor(0,0,0);
         break;
       }else{
         $items .=  '
          <tr>
                <td align="center"></td>
                <td align="center"></td>
                <td align="center"></td>
                <td align="center"></td>
                <td align="center"></td>
           </tr>';

       }
  }
}else{
      if($numberItems > 0)
      {
        for ($x = 0; $x < $numberItems; $x++) {
             if($x == $end)
             {
               $pdf->SetTextColor(0,0,0);
               break;
             }else{
               $arrRecords = explode(";" , $records);
               $onerecord = explode("," , $arrRecords[$x]); 
               $items .=  '
                <tr>
                      <td align="center">' .$onerecord[0] . '</td>
                      <td align="center">' .$onerecord[1] . '</td>
                      <td align="center">' .$onerecord[2] . '</td>
                      <td align="center">' .$onerecord[3] . '</td>
                      <td align="center">' .$onerecord[4] . '</td>
                 </tr>';
             }
        }
    }
}
$totalVAtExcl = $sumTotal;
$totalVAt = $totalVAtExcl * 0.14;
$totalVAtIncl = $totalVAtExcl + $totalVAt;

$table2 = '
      <table border = "1" cellpadding = "2">
        <tr>
            <th bgcolor ="#8CC53E" align="center">Item Code </th>
            <th bgcolor ="#8CC53E" width="40%" align="center">Description</th>
            <th bgcolor ="#8CC53E" width="10%" align="center">Quantity</th>
            <th bgcolor ="#8CC53E" width="10%" align="center">Unit Cost</th>
            <th bgcolor ="#8CC53E" align="center">Extended Cost</th>
        </tr> ' . $items . '
       
    </table>';

$table = '
    <style>
       <link rel="stylesheet" href="pdf/css/layout.css"  type="text/css"/>
    </style>
      <table>
        <tr>
            <td>Total Value excluding VAT</td>
            <td align="center">'.number_format($totalVAtExcl,2). '</td>
        </tr>
        <tr>
            <td>VAT Calculated at 14%</td>
            <td align="center">'. number_format($totalVAt,2). '</td>
        </tr>
        <tr>
            <td>Total Value Including VAT</td>
            <td align="center">'. number_format($totalVAtIncl,2).'</td>
        </tr>
    </table>';

if($records !== ""){
    // add a page
    $pdf->AddPage();

    $fontname =  'arial';
    $title = 'Additional Products';
    $pdf->SetFont("Helvetica", 'B', 18);
    $pdf->SetTextColor(140, 197, 62);
    $pdf->MultiCell(180, 3,$title,'', 'C','', 0,'10', '55');

    $fontname =  'arial';
    $pdf->SetFont($fontname , 'B', 10);
    $pdf->SetTextColor(0,0,0);

    $pdf->writeHTMLCell(0, 0,13,70,$table2, 0, 0, '','', 'L', true);

    $pdf->writeHTMLCell(85, 15,110,250,$table, 0, 0, '','', 'L', true);
}
// ------------------Fourth Page---------------------------------------
$pdf->SetPrintHeader(true);
// add a page
$pdf->AddPage();

$fontname =  'arial';
$title = 'Scope of Works';
$pdf->SetFont("Helvetica", 'B', 18);
$pdf->SetTextColor(140, 197, 62);
$pdf->MultiCell(180, 3,$title,'', 'C','', 0,'10', '55');

$title = 'Replacement Products';
$pdf->SetFont("Helvetica", 'B', 14);
$pdf->MultiCell(180, 3,$title,'', 'L','', 0,'15', '100');

//Quote Items
$sql = "SELECT *";
$sql.= " FROM `quote_items`";
$sql.= " WHERE `quote_id` = '".mysqli_real_escape_string($GLOBALS["link"],$quoteid)."' ORDER BY `id`";
$sqlres = mysqli_query($GLOBALS["link"],$sql);
$itemcount = 0;
$records = "";
$recordsAdditonal  = ""; 
while($row = mysqli_fetch_assoc($sqlres)) {  
    $itemcount += 1;
    $replaceFitting = "None";
    $ID = (int) $row["id"];
    $quoteitem = new QuoteItem($ID, $quote);
    $sql = "SELECT `fitting_type`";
    $sql.= " FROM `new_products`";
    $sql.= " WHERE `id` = '".mysqli_real_escape_string($GLOBALS["link"],$quoteitem->NewProductID)."' LIMIT 1";
    $sqlres2 = mysqli_query($GLOBALS["link"],$sql);
     while($row2 = mysqli_fetch_assoc($sqlres2)) {
        $replaceFitting = $row2['fitting_type'];
    }
    if( $replaceFitting == ""){
         $replaceFitting = "None";
    }
    $Hours_Per_Day = 0;
    if(!empty($quoteitem->InputValues["Hours_Per_Day"])){
        $Hours_Per_Day = $quoteitem->InputValues["Hours_Per_Day"];
        if($Hours_Per_Day < 0){
            $Hours_Per_Day = 0;
        }
    }
    if(!in_array($quoteitem->ProductCode, $arrAdditonal)){      
         $records  .= str_replace("^"," - ",$quoteitem->Room).",".$quoteitem->OldProductName.",".$quoteitem->Qty.",".$quoteitem->ItemDescription.",".$quoteitem->Qty.",". $Hours_Per_Day.",".$replaceFitting.";";
    }else{
         $recordsAdditonal  .= str_replace("^"," - ",$quoteitem->Room).",".$quoteitem->OldProductName.",".$quoteitem->Qty.",".$quoteitem->ItemDescription.",".$quoteitem->Qty.",". $Hours_Per_Day.",".$replaceFitting.";";     
    }  
}

//Generate the number of work
//$records = "Ground Floor1,Light bulbs1,1,Lamps1,1,1,1;Ground boardroom2,Light bulbs2,2,Lamps2,2,2,2;Ground bathroom3,Light bulbs3,3,Lamps3,3,3,3;Ground patio4,Light bulbs4,4,Lamps4,4,4,4;Ground office5,Light bulbs5,5,Lamps5,5,5,5;Ground office1,Light bulbs1,1,Lamps1,1,1,1;Ground boardroom2,Light bulbs2,2,Lamps2,2,2,2;Ground bathroom3,Light bulbs3,3,Lamps3,3,3,3;Ground patio4,Light bulbs4,4,Lamps4,4,4,4;Ground office5,Light bulbs5,5,Lamps5,5,5,5;Ground office1,Light bulbs1,1,Lamps1,1,1,1;Ground boardroom2,Light bulbs2,2,Lamps2,2,2,2;Ground bathroom3,Light bulbs3,3,Lamps3,3,3,3;Ground patio4,Light bulbs4,4,Lamps4,4,4,4;Ground office5,Light bulbs5,5,Lamps5,5,5,5;Ground office1,Light bulbs1,1,Lamps1,1,1,1;Ground boardroom2,Light bulbs2,2,Lamps2,2,2,2;";
$numberItems =  count(explode(";" , $records)) - 1 ;
$items = '';
$items2 = '';
$arrItems = array();
$count = 0;
if($numberItems > 0){
    for ($x = 0; $x < $numberItems; $x++) {
        $arrRecords = explode(";" , $records);
        $onerecord = explode("," , $arrRecords[$x]); 

        if($count > 9){
            $arrItems[] =  $items;
            $items = '';
            $count = 0;
        }

        $items .=  '
        <tr>
              <td>' .$onerecord[0] . '</td>
              <td>' .$onerecord[1] . '</td>
              <td align="center">' .$onerecord[2] . '</td>
              <td>' .$onerecord[3] . '</td>
              <td align="center">' .$onerecord[4] . '</td>
              <td align="center">' .$onerecord[5] . '</td>
              <td align="center">' .$onerecord[6] . '</td>
         </tr>';  
         $count++;   
    }
}else{
    for ($x = 0; $x < 24; $x++) {
         $items .=  '
            <tr>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
             </tr>';
    }
}

$val = $itemcount;
$formCompany = $companyName;
$description= "This project entails the supply and installation of ".$val." efficient lighting technologies to retrofit or replace ".$val." inefficient lighting sources found at the premises of "
        .$formCompany. ". Measurements were done to show savings in consumption more accurately. No measurements were done, which means that all savings are based on estimated values. Work to be completed per specified location is described below.";
$pdf->SetFont($fontname, 'B', 10);
$pdf->SetLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(192, 192, 192)));
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFillColor(255,255, 255);
$pdf->MultiCell(180,27, $description  , 1, 'C', 1, 0,'14', '70');

//Display Products
for ($x = 0; $x < count($arrItems); $x++) {
    if($x == 0){
        $table = '
          <table border = "1" cellpadding = "2">
            <tr>
                <th bgcolor ="#8CC53E" width="10%" align="center">Area</th>
                <th bgcolor ="#8CC53E" width="25%" align="center">Old Technology</th>
                <th bgcolor ="#8CC53E" width="10%" align="center">Quantity</th>
                <th bgcolor ="#8CC53E" width="25%" align="center">Replacement Technology</th>
                <th bgcolor ="#8CC53E" width="10%" align="center">Quantity</th>
                <th bgcolor ="#8CC53E" width="10%" >Hours Operated</th>
                <th bgcolor ="#8CC53E" width="10%" >Replace Fitting</th>

            </tr> ' . $arrItems[$x] . '  
        </table>';

        $pdf->writeHTMLCell(0, 0,13,110,$table, 0, 0, '','', 'L', true);
    }else{
        //More Products
        $pdf->SetPrintHeader(false);
        // add a page
        $pdf->AddPage();
        $pdf->SetY(10);

        $table = '
          <table border = "1" cellpadding = "1">
           ' . $arrItems[$x] . '  
        </table>';

        $pdf->writeHTMLCell(0, 0,13,20,$table, 0, 0, '','', 'L', true);
    }
 }

//Scope:Additional Products
if($recordsAdditonal !== ""){
    $pdf->SetPrintHeader(false);
    // add a page
    $pdf->AddPage();

    $title = 'Additional Products';
    $pdf->SetFont("Helvetica", 'B', 14);
    $pdf->SetTextColor(140, 197, 62);
    $pdf->MultiCell(180, 3,$title,'', 'L','', 0,'15', '20');

    $fontname =  'arial';
    $pdf->SetFont($fontname , 'B', 10);
    $pdf->SetTextColor(0,0,0);
    $numberItems =  count(explode(";" , $recordsAdditonal)) - 1 ;
    $items = '';
    if($numberItems > 0){
        for ($x = 0; $x < $numberItems; $x++) {
          $arrRecords = explode(";" , $recordsAdditonal);
          $onerecord = explode("," , $arrRecords[$x]); 
             $items .=  '
                <tr>
                      <td>' .$onerecord[0] . '</td>
                      <td>' .$onerecord[1] . '</td>
                      <td align="center">' .$onerecord[2] . '</td>
                      <td>' .$onerecord[3] . '</td>
                      <td align="center">' .$onerecord[4] . '</td>
                      <td align="center">' .$onerecord[5] . '</td>
                      <td align="center">' .$onerecord[6] . '</td>
                 </tr>';
        }
    }else{
        for ($x = 0; $x < 24; $x++) {
             $items .=  '
                <tr>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                 </tr>';
        }
    }
    $table = '
          <table border = "1" cellpadding = "2">
            <tr>
                <th bgcolor ="#8CC53E" width="10%" align="center">Area</th>
                <th bgcolor ="#8CC53E" width="25%" align="center">Old Technology</th>
                <th bgcolor ="#8CC53E" width="10%" align="center">Quantity</th>
                <th bgcolor ="#8CC53E" width="25%" align="center">Replacement Technology</th>
                <th bgcolor ="#8CC53E" width="10%" align="center">Quantity</th>
                <th bgcolor ="#8CC53E" width="10%" >Hours Operated</th>
                <th bgcolor ="#8CC53E" width="10%" >Replace Fitting</th>
            </tr> ' . $items . '  
        </table>';

    $pdf->writeHTMLCell(0, 0,13,35,$table, 0, 0, '','', 'L', true);
}
// ------------------Sixth Page---------------------------------------
$pdf->SetPrintHeader(true);

//add a page
$pdf->AddPage();
$pdf->SetY(20);

$fontname =  'arial';
$title = 'Quote Acceptance';
$pdf->SetFont("Helvetica", 'B', 18);
$pdf->SetTextColor(140, 197, 62);
$pdf->MultiCell(180, 3,$title,'', 'C','', 0,'10', '55');
$pdf->SetY(70);
$pdf->setFormDefaultProp(array('lineWidth'=>1, 'borderStyle'=>'solid', 'fillColor'=>array(255, 255, 255), 'strokeColor'=>array(255, 0, 0)));

$val =$itemcount;
$val2 =0;

$responsibility = "I, ";
$responsibility2 = " in my Capacity as";
$responsibility3 = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;hereby accept the quotation submitted to me by Ellies for " .$val. " of energy efficient lighting to the amount of R" . $val2.".  I understand that a 10% deposit is payable before any work will commence and that the rest of the amount will be payable upon completion of the job and signing of the jobcard below once all equipment";

$pdf->SetFont($fontname, 'B', 10);
$pdf->SetTextColor(0, 0, 0);
$html = '
    <p> ' .$responsibility . '
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

    '  .$responsibility2. 
    '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'
    .$responsibility3. '</p>';
$pdf->writeHTML($html, true, false, true, false, '');
//$pdf->writeHTMLCell(178, 20,14,70,$html, 0, 0, '','', 'L', true);

$name = "NONE";
$capacity = "NONE";
$signat = "NONE";

$dayof = "NONE";
$onthis = "NONE";
$forcustomer = "NONE";
$forcompany = "NONE";

/*$pdf->TextField('name', 177, 6,array('multiline'=>true), array('v'=>$name,'dv'=>$name),'18','68');
$pdf->TextField('capacity', 150, 6,array('multiline'=>true), array('v'=>$capacity,'dv'=>$capacity),'46','73');
$pdf->TextField('signAt', 30, 6,array(), array('v'=>$signat,'dv'=>$signat),'35','99');
$pdf->TextField('onThis', 30, 6,array(), array('v'=>$onthis,'dv'=>$onthis),'78','99');
$pdf->TextField('day', 30, 6,array(), array('v'=>$dayof,'dv'=>$dayof),'121','99');
$pdf->TextField('customer', 40, 6,array(), array('v'=>$forcustomer,'dv'=>$forcustomer),'45','109');
$pdf->TextField('company', 45, 6,array(), array('v'=>$forcompany,'dv'=>$forcompany),'101','109');*/

$pdf->MultiCell(30, 3, "Signed at:",'', 'C','', 0,'10', '100');
$pdf->MultiCell(30, 3, "on this",'', 'C','', 0,'56', '100');
$pdf->MultiCell(30, 3, "day of",'', 'C','', 0,'100', '100');
$pdf->MultiCell(30, 3, "for the Customer",'', 'C','', 0,'50', '115');
$pdf->MultiCell(50, 3, "for the Company: Ellies ",'', 'C','', 0,'100', '115');

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
$Content = '
    <p><u>Terms and conditions</u></p>
    <ul>'
        .$termsCond.
   '</ul>
    <p><u>Additional terms and conditions if the project is paid as a full maintenance rental contract</u></p>
    <ul class="details">
        '.$additonalCond.'
    </ul>';

$pdf->writeHTMLCell(178, 20,14,121,$Content, 0, 0, '','', 'L', true);
// ------------------Seventh Page---------------------------------------
$pdf->SetPrintHeader(true);

//add a page
$pdf->AddPage();

$fontname =  'arial';
$title = 'Job Card';
$pdf->SetFont("Helvetica", 'B', 18);
$pdf->SetTextColor(140, 197, 62);
$pdf->MultiCell(180, 3,$title,'', 'C','', 0,'10', '55');

$pdf->SetFont("Helvetica", 'B', 11);
$pdf->SetTextColor(0,0,0);
$pdf->MultiCell(180, 3,"Ship To:",'', 'L','', 0,'13', '65');
$pdf->MultiCell(180, 3,"Date:",'', 'L','', 0,'120', '65');

$pdf->SetFont($fontname, 'B', 9);
$pdf->SetLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFillColor(255,255, 255);

$company_name = $formCompany;
$street_address = $quote->Address;
$contact_name = $customer->Name." ".$customer->Surname;
$tel_contact_number = $customer->CellPhone;
if(!strlen($company_name) == 0)
{
    $formCompany  = "Company: " . $company_name;
}else{ 
    $formCompany  ="Company: None";
    $company_name = "None";
 }

if(!strlen($street_address) == 0)
{
    $address  = "Address: " . $street_address;
}else{ $address  ="Address: None"; }

if(!strlen($contact_name) == 0)
{
    $contact  = "Contact: " . $contact_name;
}else{ $contact  ="Contact: None"; }

if(!strlen($tel_contact_number) == 0)
{
    $cellphone  = "Cellphone: " . $tel_contact_number;
}else{ $cellphone  ="Cellphone: None";  }

$description =  $formCompany .'<br/>'.$address.'<br/>'.$contact .'<br/>'.$cellphone;
$Content = '
    <style>
       <link rel="stylesheet" href="pdf/css/layout.css"  type="text/css"/>
    </style>
    <p>' .$description . '</p>';

$pdf->writeHTMLCell(80, 25,14,71,$Content, 1, 0, '','', 'L', true);


$text= " supply and installation ";
if($installation){
    $text = " supply "; 
}

$description= "This project entails the".$text."of ".$itemcount." efficient lighting technologies to retrofit or replace ".$itemcount." inefficient lighting sources found at the premises of "
        .$company_name. ". Work to be completed per specified location is described below.";

$pdf->MultiCell(80,25, $description  , 1, 'C', 1, 0,'114', '71');
//====================================================
//Quote Items
$sql = "SELECT *";
$sql.= " FROM `quote_items`";
$sql.= " WHERE `quote_id` = '".mysqli_real_escape_string($GLOBALS["link"],$quoteid)."' ORDER BY `id`";
$sqlres = mysqli_query($GLOBALS["link"],$sql);
$itemcount = 0;
$records = "";
$records2 = "";
$sumTotal = 0;
$quantity = 0;
$count = 0;
$arrRecords = array();
$arrProducts = array();
$arrQuantity = array();
$arrDescription = array();
$arrPrice = array();

function GetProductPos($Code='',$arr=array()){
    $pos = 0;
    for ($x = 0; $x < count($arr); $x++) {
        if($arr[$x] == $Code){
            $pos = $x;
            return $pos;
        }
        $pos++;
    }
    return $pos;
}

while($row = mysqli_fetch_assoc($sqlres)) {
    $ID = (int) $row["id"];
    $quoteitem = new QuoteItem($ID, $quote);

    if(!empty($quoteitem->ProductCode) && $quoteitem->ProductCode !=="" )
    {
         //Grouping
         if(!in_array($quoteitem->ProductCode, $arrProducts)){
            $arrProducts[] = $quoteitem->ProductCode;
            $arrDescription[] = $quoteitem->ItemDescription;
            $arrPrice[] = $quoteitem->TempPrice;       
            $arrQuantity[] = $quoteitem->Qty;   
         }else{
            $getPos = GetProductPos($quoteitem->ProductCode,$arrProducts);
            $quantity =$arrQuantity[$getPos];
            $quantity+= $quoteitem->Qty;
            $arrQuantity[$getPos] = $quantity;
         }  
    } 
}

for ($x = 0; $x < count($arrProducts); $x++) {
    
    if($arrQuantity[$x] == 0 ){
        $extend_cost = $arrPrice[$x];
    }else{
        $extend_cost = $arrQuantity[$x] * $arrPrice[$x];
    }
    $sumTotal += $extend_cost;
    $records .= $arrProducts[$x].",".$arrDescription[$x].",".$arrQuantity[$x].",".$arrPrice[$x].",". number_format($extend_cost,2,".","") .";";
    $records2 .= $arrProducts[$x].",N/A,N/A;";
 }


//Generate the number of items
$numberItems =  count(explode(";" , $records)) + 3 ;
$end = $numberItems - 4;
$items = '';
$qtyLabour = 0;
$labourText = 'Labour';
$labourCosts = 0;
$labourTotal = 0;
if($installation){
    $qtyLabour = 1;
    $labourCosts = $quote->LabourCostsReplacement;
    $labourTotal = $qtyLabour * $labourCosts;
}
$travelperkm = $quote->Settings["contractor_cost_per_km_markup"];
//Totals
$travelTotal = number_format($quote->TravelCostsReplacement, 2);
$crushTotal = number_format($quote->CrushCostsReplacement, 2);
$materialTotal = number_format($quote->MaterialsCostsReplacementCost, 2);

if(count(explode(";" , $records)) == 1)
{
    for ($x = 0; $x < 16; $x++) {
       if($x == 12)
       {
         $pdf->SetTextColor(0,0,0);
         $items .=  '
          <tr>
              <td>'. $labourText.'</td>
              <td></td>
              <td align="center">'.$qtyLabour.'</td>
              <td align="center">'.$labourCosts.'</td>
              <td align="center">'.$labourTotal.'</td>
         </tr>

         <tr>
              <td>Travel</td>
              <td align="center">'.($travelperkm?'Charged at R'.$travelperkm.' per km':'').'</td>
              <td align="center">'.$quote->Distance.'</td>
              <td align="center">-</td>
              <td align="center">'.$travelTotal.'</td>
         </tr>

          <tr>
              <td>Crush</td>
              <td></td>
              <td align="center">-</td>
              <td align="center">-</td>
              <td align="center">'.$crushTotal.'</td>
         </tr>
         <tr>
              <td>Materials</td>
              <td></td>
              <td align="center">-</td>
              <td align="center">-</td>
              <td align="center">'.$materialTotal.'</td>
        </tr>';
         break;
       }else{
         $items .=  '
          <tr>
                <td align="center"></td>
                <td align="center"></td>
                <td align="center"></td>
                <td align="center"></td>
                <td align="center"></td>
           </tr>';

       }
  }
}else{
    if($numberItems > 0)
    {
        for ($x = 0; $x < $numberItems; $x++) {
            if($x == $end)
            {
               $pdf->SetTextColor(0,0,0);
               $items .=  '
                <tr>
                    <td>'.$labourText.'</td>
                    <td></td>
                    <td align="center">'.$qtyLabour.'</td>
                    <td align="center">'.$labourCosts.'</td>
                    <td align="center">'.$labourTotal.'</td>
               </tr>

               <tr>
                  <td>Travel</td>
                  <td align="center">'.($travelperkm?'Charged at R'.$travelperkm.' per km':'').'</td>
                  <td align="center">'.$quote->Distance.'</td>
                  <td align="center">-</td>
                  <td align="center">'.$travelTotal.'</td>
               </tr>

                <tr>
                    <td>Crush</td>
                    <td></td>
                    <td align="center">-</td>
                    <td align="center">-</td>
                    <td align="center">'.$crushTotal.'</td>
               </tr>
               <tr>
                    <td>Materials</td>
                    <td></td>
                    <td align="center">-</td>
                    <td align="center">-</td>
                    <td align="center">'.$materialTotal.'</td>
               </tr>';
               break;
            }else{
               $arrRecords = explode(";" , $records);
               $onerecord = explode("," , $arrRecords[$x]); 
               $items .=  '
                <tr>
                      <td align="center">' .$onerecord[0] . '</td>
                      <td align="center">' .$onerecord[1] . '</td>
                      <td align="center">' .$onerecord[2] . '</td>
                      <td align="center">' .$onerecord[3] . '</td>
                      <td align="center">' .$onerecord[4] . '</td>
                 </tr>';
            }
        }
    }
}

$totalVAtExcl = $labourTotal + $travelTotal  + $crushTotal + $materialTotal + $sumTotal;
$totalVAt = $totalVAtExcl * 0.14;
$totalVAtIncl = $totalVAtExcl + $totalVAt;
$table = '
    <style>
       <link rel="stylesheet" href="pdf/css/layout.css"  type="text/css"/>
    </style>

      <table>
        <tr>
            <td>Total Value excluding VAT</td>
            <td align="center">' .number_format($totalVAtExcl,2) . '</td>
        </tr>
        <tr>
            <td>VAT Calculated at 14%</td>
            <td align="center">' . number_format($totalVAt,2) . '</td>
        </tr>
        <tr>
            <td>Total Value Including VAT</td>
            <td align="center">' . number_format($totalVAtIncl,2) .'</td>
        </tr>
    </table>';
$pdf->writeHTMLCell(85, 15,110,200,$table, 0, 0, '','', 'L', true);

$table = '
      <table border = "1" cellpadding = "2">
        <tr>
            <th bgcolor ="#8CC53E" align="center">Item Code </th>
            <th bgcolor ="#8CC53E" width="40%" align="center">Description</th>
            <th bgcolor ="#8CC53E" width="10%" align="center">Quantity</th>
            <th bgcolor ="#8CC53E" width="10%" align="center">Unit Cost</th>
            <th bgcolor ="#8CC53E" align="center">Extended Cost</th>
        </tr> ' . $items . '     
    </table>';
$pdf->writeHTMLCell(0, 0,13,105,$table, 0, 0, '','', 'L', true);

$Content = '
    <style>
       <link rel="stylesheet" href="pdf/css/layout.css"  type="text/css"/>
    </style>
    <p style="font-size:8px;">Stock used and returned to be indicated above.<br/>At least 5 pictures to be taken on site.<br/>Customer and Installer to sign completed Job Card.</p>';
$pdf->writeHTMLCell(85, 10,14,260,$Content, 1, 0, '','', 'L', true);

//Generate the number of items
//$records = "FFCCQWER0,Light bulbs0,Yes;FFCCQWER1,Light bulbs1,No;FFCCQWER2,Light bulbs2,Yes;FFCCQWER3,Light bulbs3,No;FFCCQWER4,Light bulbs4,Yes;FFCCQWER5,Light bulbs5,No;FFCCQWER6,Light bulbs6,Yes;FFCCQWER7,Light bulbs7,No;FFCCQWER8,Light bulbs8,Yes;FFCCQWER9,Light bulbs9,No;FFCCQWER10,Light bulbs10,Yes;FFCCQWER11,Light bulbs11,No;";
$numberItems =  count(explode(";" , $records2)) -1;
$items = '';
$val = 0;
if($numberItems > 0)
{
    for ($x = 0; $x < $numberItems; $x++) {
         $arrRecords = explode(";" , $records2);
         $onerecord = explode("," , $arrRecords[$x]); 
         $items .=  '
                <tr>
                    <td align="center">' .$onerecord[0] . '</td>
                    <td align="center">' .$onerecord[1] . '</td>
                    <td align="center">' .$onerecord[2] . '</td>
               </tr>';
    }
}else{
    for ($x = 0; $x < 12; $x++) {
         $items .=  '
                <tr>
                    <td align="center"></td>
                    <td align="center"></td>
                    <td align="center"></td>
               </tr>';
     }
}

$table = '
      <table border = "1" style="align:center">
        <tr>
            <th bgcolor ="#8CC53E">Code</th>
            <th bgcolor ="#8CC53E">Supplied</th>
            <th bgcolor ="#8CC53E">Used</th>
        </tr> '. $items . '
    </table>';
$pdf->writeHTMLCell(80, 0,13,200,$table, 0, 0, '','', 'L', true);

$pdf->SetFont("Helvetica", 'B', 10);
$pdf->SetTextColor(0,0,0);

if(strlen($contactPerson ) == 0){
    $contactPerson   = "None";
}
$pdf->MultiCell(180, 3,"Project signed off on:",'', 'L','', 0,'110', '230');
$pdf->MultiCell(180, 3,"For Customer ". $contactPerson .":",'', 'L','', 0,'110', '240');
$pdf->MultiCell(180, 3,"For Installer:",'', 'l','', 0,'110', '250');

//$signproject = "NONE";
//$forcustomer = $contactPerson;
//$forinstaller = "NONE";
//$pdf->TextField('signProject', 40, 6,array(), array('v'=>$signproject,'dv'=>$signproject),'150','228');
//$pdf->TextField('signCustomer', 40, 6,array('readonly'=>true), array('v'=>$forcustomer,'dv'=>$forcustomer),'160','238');
//$pdf->TextField('signInstaller', 40, 6,array(), array('v'=>$forinstaller,'dv'=>$forinstaller),'135','248');
// --------------------------------------------------------------
$num =count($arrProducts);
$path =dirname($_SERVER["SCRIPT_FILENAME"]);
if($num > 0){
    $path =dirname($_SERVER["SCRIPT_FILENAME"]);
    error_reporting(E_ALL);
    $pdf->Output($path.'/printquotes/Ellies Renewable Energy_'.$quoteid.'.pdf','F');
    require_once('fpdf/fpdi/fpdi.php');
    class PDF extends FPDI
    {
        //Draw an imported PDF logo on every page 
        function Header()
        {
            // emtpy method body 
        } 
        function Footer()
        {
            // emtpy method body
        }

        var $files = array();
        function setFiles($files) {
              $this->files = $files;
        }
        function concat() {
          foreach($this->files AS $file) {
               $pagecount = $this->setSourceFile($file);
               for ($i = 1; $i <= $pagecount; $i++) {
                    $tplidx = $this->ImportPage($i);
                    $s = $this->getTemplatesize($tplidx);
                    $this->AddPage();
                    $this->useTemplate($tplidx);
               }
           }
        }
    }

    $fdpf = new PDF();
    $fdpf->SetMargins(PDF_MARGIN_LEFT, 40, PDF_MARGIN_RIGHT);
    $fdpf->SetAutoPageBreak(true, 40);
    $fdpf->setFontSubsetting(false);
    //set margins
    $fdpf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    //set auto page breaks
    $fdpf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    //set image scale factor
    $fdpf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    $fdpf->setFiles(array($path.'/printquotes/Ellies Renewable Energy_'.$quoteid.'.pdf'));
    $fdpf->concat();

    //Reference:Replacement product
    $files =array();
    for ($x = 0; $x < $num; $x++){
        if(file_exists($path."/productdescriptions/". $arrProducts[$x].".pdf")){
            $file = trim($arrProducts[$x].".pdf");
            $files[] = $path."/productdescriptions/".$file;
        }
    }
    if(count($files)!=0){
        $fdpf->setFiles($files);
        $fdpf->concat(); 
    }
    $fdpf->Output($path.'/emailedquotes/Ellies Renewable Energy_'.$quoteid.'.pdf','F');
    //$fdpf->Output('Ellies Renewable Energy_'.$quoteid.'.pdf', 'I');
}else{
    //Close and output PDF document
    //$pdf->Output('Ellies Renewable Energy_'.$quoteid.'.pdf', 'I');
    $pdf->Output($path.'/emailedquotes/Ellies Renewable Energy_'.$quoteid.'.pdf','F');
}
//====================================================================================
function SendEmail($recipientemail, $recipientname, $subject, $message, $attachments = NULL) {
    $mail = new PHPMailer(); 
    $mail->Mailer = 'smtp';

    $mail->SetFrom("support@tradepage.co.za","Do Not Reply");
    $mail->AddAddress($recipientemail, $recipientname);
    //$mail->AddBCC("ari.salkow@ellies.co.za", "Ari Salkow");

    $mail->Subject  = $subject;
    $mail->AltBody  = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and 
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
    $mailsent = false;
    if(!empty($mail->ErrorInfo)) {
      $mailsent = $mail->ErrorInfo;
    } else {
      $mailsent = true;
    }

    return $mailsent;
}

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

$quoteid = $_GET["id"];
$quote = new Quote($quoteid);
if ($quoteid == "0") {
    $quote->CustomerID = SESSION("customerid");
    $quote->Save();
}

$filename = $path.'/emailedquotes/Ellies Renewable Energy_'.$quoteid.'.pdf';
if (file_exists($filename)) {
   unlink($filename);
}

//$email = "gary@uthgroup.co.za";
$email = "ari.salkow@ellies.co.za";
$status = SendEmail($email, $customer->Name." ".$customer->Surname, "Ellies Energy Saving - Quotation: ".$quote->QuoteReferenceNo, "Please find attached. Ellies Energy Saving - Quotation: ".$quote->QuoteReferenceNo, $attachments[] = $filename);

if ($status){
	$quote->Approved=1;
	$quote->Save();
}
if ($status){
    PrintNavBar("Emailed Quote", true, "editquotecomm.php?id=".$quoteid);
    print "<p>Thank you. Your quote has been emailed to the customer.</p>";
}else{
   print "<p>".$status."</p>";   
}
//===Put in alternate mailer here with attachment support===
require_once("inc/footer.php");
?>
