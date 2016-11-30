<?php
require_once('libraries/tcpdf/config/lang/eng.php');
require_once('libraries/tcpdf/tcpdf.php');
require_once('libraries/MYPDF.php');

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
//print_r($_REQUEST);exit;
$pdf->SetPrintHeader(false);
// add a page
$pdf->AddPage();

$title = 'Sales Representative';
$pdf->SetFont("Helvetica", 'B', 12);
$pdf->SetTextColor(140, 197, 62);
$pdf->MultiCell(180, 3,$title,'', 'R','', 0,'25', '5');
$pdf->SetFont("Helvetica", 'B', 15);
$title = 'Total Pellisier';
$pdf->MultiCell(180, 3,$title,'', 'L','', 0,'25', '10');

//Front page
$image_file ='images/front.jpg';
$pdf->Image($image_file, 25, 20, 900, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);

// ------------------Second Page----------------------------------------
$pdf->SetPrintHeader(true);
// add a page
$pdf->AddPage();

$fontname =  'arial';
$title = 'Project Details';
$pdf->SetFont("Helvetica", 'B', 15);
$pdf->SetTextColor(140, 197, 62);
$pdf->MultiCell(180, 3,$title,'', 'C','', 0,'10', '55');

$pdf->SetFont($fontname, '', 10);
$pdf->MultiCell(100, 3, "Type of Premises:  (Mark with x):",'', 'L','', 0,'13', '70');
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

$pdf->setFormDefaultProp(array('lineWidth'=>1, 'borderStyle'=>'solid', 'fillColor'=>array(255, 255, 255), 'strokeColor'=>array(255, 0, 0)));
$pdf->SetTextColor(0, 0,0);
if(isset($_REQUEST["business"])){
    $pdf->CheckBox('business', 5, true, array(), array(), 'business','88','70');
}else{
   $pdf->CheckBox('business', 5, false, array(), array(), 'business','88','70');
}

if(isset($_REQUEST["home"])){
   $pdf->CheckBox('home', 5, true, array(), array(), 'home','115','70');
}else{
   $pdf->CheckBox('home', 5, false, array(), array(), 'home','115','70');
}

if(isset($_REQUEST["other"])){
   $pdf->CheckBox('other', 5, true, array(), array(), 'other','138','70');
}else{
  $pdf->CheckBox('other', 5, false, array(), array(), 'other','138','70');
}

$pdf->TextField('companyName', 50, 6, array(), array('v'=>$_REQUEST["company_name"],'dv'=>$_REQUEST["company_name"]),'50','83');
$pdf->TextField('streetAddress', 50, 18, array('multiline'=>true), array('v'=>$_REQUEST["street_address"],'dv'=>$_REQUEST["street_address"]),'50','95');
$pdf->TextField('telephone', 50, 6, array(), array('v'=>$_REQUEST["tel_number"],'dv'=>$_REQUEST["tel_number"]),'50','115');
$pdf->TextField('contactPerson', 50, 6, array(), array('v'=>$_REQUEST["contact_name"],'dv'=>$_REQUEST["contact_name"]),'140','83');
$pdf->TextField('telephone', 50, 6, array(), array('v'=>$_REQUEST["tel_contact_number"],'dv'=>$_REQUEST["tel_contact_number"]),'140','95');
$pdf->TextField('email', 50, 6, array(), array('v'=>$_REQUEST["email"],'dv'=>$_REQUEST["email"]),'140','105');
$pdf->TextField('date', 50, 6, array(), array('v'=>$_REQUEST["date"],'dv'=>$_REQUEST["date"]),'140','115');

$title = 'Additional Information';
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
$pdf->TextField('projectManager', 50, 6, array(), array('v'=>$_REQUEST["project_manager"],'dv'=>$_REQUEST["project_manager"]),'50','139');
$pdf->TextField('installer', 50, 6, array(), array('v'=>$_REQUEST["installer"],'dv'=>$_REQUEST["installer"]),'50','149');
$pdf->TextField('salesRep', 50, 6, array(), array('v'=>$_REQUEST["sales_rep"],'dv'=>$_REQUEST["sales_rep"]),'50','159');
$pdf->TextField('electricitySupplier', 50, 6, array(), array('v'=>$_REQUEST["electricity_supplier"],'dv'=>$_REQUEST["electricity_supplier"]),'50','169');
$pdf->TextField('cost', 50, 6, array(), array('v'=>$_REQUEST["cost"],'dv'=>$_REQUEST["cost"]),'50','179');

$pdf->TextField('region', 50, 6, array(), array('v'=>$_REQUEST["region"],'dv'=>$_REQUEST["region"]),'140','139');

//Type of Connection
$pdf->SetFont($fontname, '', 8);
$pdf->MultiCell(100, 3, "Single Phase",'', 'L','', 0,'145', '170');
$pdf->MultiCell(100, 3, "Three Phase",'', 'L','', 0,'170', '170');
if(isset($_REQUEST["single_phase"])){
   $pdf->CheckBox('single', 5, true, array(), array(), 'single phase','165','169');
}else{
   $pdf->CheckBox('single', 5, false, array(), array(), 'single phase','165','169');
}
if(isset($_REQUEST["three_phase"])){
   $pdf->CheckBox('three', 5, false, array(), array(), 'three pahse','190','169');
}else{
   $pdf->CheckBox('three', 5, false, array(), array(), 'three pahse','190','169');;
}

$pdf->SetFont($fontname, '', 10);
$pdf->TextField('meter', 50, 6, array(), array('v'=>$_REQUEST["meter_number"],'dv'=>$_REQUEST["meter_number"]),'140','179');

$title = 'Consumption Data and Installation Details';
$pdf->SetTextColor(140, 197, 62);
$pdf->SetFont("Helvetica", 'B', 15);
$pdf->MultiCell(180, 3,$title,'', 'C','', 0,'10', '195');

$pdf->SetFont($fontname, '', 10);
$pdf->MultiCell(100, 3, "Electricity Accounts",'', 'L','', 0,'13', '210');
$pdf->MultiCell(100, 3, "Travel (km)",'', 'L','', 0,'70', '210');
$pdf->MultiCell(100, 3, "Ceiling Height",'', 'L','', 0,'120', '210');
$pdf->MultiCell(100, 3, "Contingency",'', 'L','', 0,'170', '210');

$pdf->SetTextColor(0, 0,0);
$pdf->TextField('accounts', 30, 6, array(), array('v'=>$_REQUEST["electricity_accounts"],'dv'=>$_REQUEST["electricity_accounts"]),'13','215');
$pdf->TextField('travel', 30, 6, array(), array('v'=>$_REQUEST["travel"],'dv'=>$_REQUEST["travel"]),'70','215');
$pdf->TextField('height', 30, 6, array(), array('v'=>$_REQUEST["height"],'dv'=>$_REQUEST["height"]),'120','215');
$pdf->TextField('contingency', 30, 6, array(), array('v'=>$_REQUEST["contingency"],'dv'=>$_REQUEST["contingency"]),'170','215');

$pdf->SetTextColor(140, 197, 62);
$pdf->MultiCell(100, 3, "Other Expenses",'', 'L','', 0,'130', '225');
$pdf->SetTextColor(0, 0,0);
$pdf->MultiCell(100, 3, "Access Equipment",'', 'L','', 0,'135', '230');
$pdf->MultiCell(100, 3, "COC",'', 'L','', 0,'135', '235');

if(isset($_REQUEST["access_equipment"])){
   $pdf->CheckBox('equipment', 5, true, array(), array(), 'access equipment','170','230');
}else{
  $pdf->CheckBox('equipment', 5, false, array(), array(), 'access equipment','170','230');
}
if(isset($_REQUEST["coc"])){
  $pdf->CheckBox('coc', 5, true, array(), array(), 'coc','170','235');
}else{
  $pdf->CheckBox('coc', 5, false, array(), array(), 'coc','170','235');
}

$pdf->SetTextColor(140, 197, 62);
$pdf->MultiCell(100, 3, "Installation",'', 'L','', 0,'70', '240');
$pdf->MultiCell(100, 3, "Notes:",'', 'L','', 0,'13', '250');
$pdf->SetTextColor(0, 0,0);
if(isset($_REQUEST["coc"])){
  $pdf->CheckBox('installation', 5, true, array(), array(), 'installation','90','240');
}else{
  $pdf->CheckBox('installation', 5, false, array(), array(), 'installation','90','240');
}


$notes= $_REQUEST["notes"];
$pdf->SetFont($fontname, 'B', 10);
$pdf->SetLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(192, 192, 192)));
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFillColor(255,255, 255);
$pdf->MultiCell(150,22, $notes  , 1, 'L', 1, 0,'30', '250');
// ------------------Third Page---------------------------------------
$pdf->SetPrintHeader(true);
// add a page
$pdf->AddPage();

$fontname =  'arial';
$title = 'Scope of Works';
$pdf->SetFont("Helvetica", 'B', 18);
$pdf->SetTextColor(140, 197, 62);
$pdf->MultiCell(180, 3,$title,'', 'C','', 0,'10', '55');

$val = 0;
$formCompany = "Total Pellisier";
$description= "This project entails the supply and installation of ".$val." efficient lighting technologies to retrofit or replace ".$val." inefficient lighting sources found at the premises of "
		.$formCompany. ". Measurements were done to show savings in consumption more accurately. No measurements were done, which means that all savings are based on estimated values. Work to be completed per specified location is described below.";
$pdf->SetFont($fontname, 'B', 10);
$pdf->SetLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(192, 192, 192)));
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFillColor(255,255, 255);
$pdf->MultiCell(180,27, $description  , 1, 'C', 1, 0,'14', '70');

//Generate the number of work
$records = $_REQUEST["records"];
$numberItems =  count(explode(";" , $records)) - 1 ;
$items = '';
if($numberItems > 0){
    for ($x = 0; $x < $numberItems; $x++) {
      $arrRecords = explode(";" , $records);
      $onerecord = explode("," , $arrRecords[$x]); 
         $items .=  '
            <tr>
                  <td>' .$onerecord[0] . '</td>
                  <td>' .$onerecord[1] . '</td>
                  <td>' .$onerecord[2] . '</td>
                  <td>' .$onerecord[3] . '</td>
                  <td>' .$onerecord[4] . '</td>
                  <td>' .$onerecord[5] . '</td>
                  <td>' .$onerecord[6] . '</td>
             </tr>';
    }
}else{
  for ($x = 0; $x < 17; $x++) {
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
      <table border = "1" cellpadding = "3">
        <tr>
            <th bgcolor ="#8CC53E">Area</th>
            <th bgcolor ="#8CC53E">Old Technology</th>
            <th bgcolor ="#8CC53E">Quantity</th>
            <th bgcolor ="#8CC53E">Replacement Technology</th>
            <th bgcolor ="#8CC53E">Quantity</th>
            <th bgcolor ="#8CC53E">Hours Operated</th>
            <th bgcolor ="#8CC53E">Replace Fitting</th>

        </tr> ' . $items . '
       
    </table>';
$pdf->writeHTMLCell(0, 0,13,105,$table, 0, 0, '','', 'L', true);
// ------------------Fourth Page---------------------------------------

// add a page
$pdf->AddPage();

$fontname =  'arial';
$title = 'Financial Analysis';
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
if(!strlen($_REQUEST["lighting_before_value"]) == 0)
{
	$lighting_before_value =$_REQUEST["lighting_before_value"] . " W";
}else{ $lighting_before_value ="0 W"; }
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
if(!strlen($_REQUEST["lighting_after_value"]) == 0)
{
	$lighting_after_value  =$_REQUEST["lighting_after_value"] . " W";
}else{ $lighting_after_value  ="0 W"; }
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
if(!strlen($_REQUEST["electric_before_value"]) == 0)
{
	$electric_before_value  =$_REQUEST["electric_before_value"] . " W";
}else{ $electric_before_value  ="0 W"; }
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
if(!strlen($_REQUEST["electric_after_value"]) == 0)
{
	$electric_after_value  =$_REQUEST["electric_after_value"] . " W";
}else{ $electric_after_value  ="0 W"; }
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
if(!strlen($_REQUEST["cost_before_value"]) == 0)
{
	$cost_before_value  = "R " . $_REQUEST["cost_before_value"];
}else{ $cost_before_value  ="R 0,00"; }
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
if(!strlen($_REQUEST["cost_after_value"]) == 0)
{
	$cost_after_value  = "R " . $_REQUEST["cost_after_value"];
}else{ $cost_after_value  ="R 0,00"; }
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

if(!strlen($_REQUEST["project_cost"]) == 0)
{
	$project_cost  = "R " . $_REQUEST["project_cost"];
}else{ $project_cost  ="R 0,00"; }
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

if(!strlen($_REQUEST["month_saving"]) == 0)
{
	$month_saving  = "R " . $_REQUEST["month_saving"];
}else{ $month_saving  ="R 0,00"; }
$pdf->SetFont($fontname, 'B', 12);
$pdf->SetLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(192, 192, 192)));
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFillColor(255,255, 255);
$pdf->MultiCell(54, 1,$month_saving, 1, 'C', 1, 0,'78', '104');

//% Saving
$pdf->SetFont($fontname, 'B', 12);
$pdf->SetLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(192, 192, 192)));
$pdf->SetFillColor(153, 153, 153);
$pdf->MultiCell(54, 1,'% Saving', 1, 'L', 1, 0,'140', '100');

if(!strlen($_REQUEST["saving"]) == 0)
{
	$saving  = "R " . $_REQUEST["saving"];
}else{ $saving  ="R 0,00"; }
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
$pdf->MultiCell(54, 1,'Breakeven', 1, 'L', 1, 0,'140', '120');

$text = "The breakeven point is calculated as the amount of time (in months) that the savings achieved from retrofitting old technologies will take to recover the initial project expense.";
$pdf->SetFont($fontname, 'B', 10);
$pdf->SetLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(192, 192, 192)));
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFillColor(255,255, 255);
$pdf->MultiCell(54, 10,$text, 1, 'C', 1, 0,'140', '125');

//Chart
$image_file ='images/graph.jpg';
$pdf->Image($image_file,15, 110, 110, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);

//Rent per Month
$pdf->SetFont($fontname, 'B', 12);
$pdf->SetLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(192, 192, 192)));
$pdf->SetFillColor(192, 192, 192);
$pdf->MultiCell(54, 1,'Rent per Month', 1, 'L', 1, 0,'', '170');


$text = "*Rental option subject to approval by a third party financier over a period of 36 Months";
$pdf->SetFont($fontname, 'B', 10);
$pdf->SetLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(192, 192, 192)));
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFillColor(255,255, 255);
$pdf->MultiCell(54, 10,$text, 1, 'C', 1, 0,'', '175');

$value =  'R 0.00';
$value2 = 'R 0.00';
$value3 = 'R 0.00';
$value4 = 'R 0.00';
$value5 = 'R 0.00';
$text = 'If the project were not completed,'. $value . ' would continue paying as much as '. $value2 .' per month for lighting. But by completing the project, this amount can decrease to as little as ' .$value3. 'each month. Even if you rent the equipment for ' . $value4 .' over 36 months, you end up paying only '. $value5 . ' monthly which, after the 36 months, becomes money in your pockets.';
$pdf->SetFont($fontname, 'B', 10);
$pdf->SetLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(192, 192, 192)));
$pdf->SetTextColor(255, 0, 0);
$pdf->SetFillColor(255,255, 255);
$pdf->MultiCell(120, 15,$text, 1, 'L', 1, 0,'74', '170');
// ---------------------------------------------------------------------------------------------------------------------
//How the calculations are done (unrelated example)
$pdf->SetFont($fontname , 'B', 15);
$pdf->SetTextColor(140, 197, 62);
$html = '<p>How the calculations are done (unrelated example)</p>';
$pdf->writeHTMLCell(178, 20,16,190,$html, 0, 0, '','', 'C', true);

//Images
$image_file ='images/36W.jpg';
$pdf->Image($image_file,50, 205, 20, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
$image_file ='images/20W.jpg';
$pdf->Image($image_file,75, 208, 20, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);

$image_file ='images/60W.jpg';
$pdf->Image($image_file,100,209, 20, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
$image_file ='images/7W.jpg';
$pdf->Image($image_file,120, 206,20, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);

$image_file ='images/50W.jpg';
$pdf->Image($image_file,150, 211, 20, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
$image_file ='images/6W.jpg';
$pdf->Image($image_file,170, 203, 20, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);

//Before value
$pdf->SetFont($fontname, 'B', 12);
$pdf->SetLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(140, 197, 62)));
$pdf->SetFillColor(140, 197, 62);
$pdf->SetTextColor(252, 247, 247);
$pdf->MultiCell(23, 5,'0.036 kWh' , 1, 'C', 1, 0,'47', '240');
//After value
$pdf->SetFont($fontname, 'B', 12);
$pdf->SetLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(253, 0, 0)));
$pdf->SetFillColor(253, 0, 0);
$pdf->SetTextColor(252, 247, 247);
$pdf->MultiCell(23, 1,'0.020 kWh', 1, 'C', 1, 0,'70', '240');

//Before value
$pdf->SetFont($fontname, 'B', 12);
$pdf->SetLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(140, 197, 62)));
$pdf->SetFillColor(140, 197, 62);
$pdf->SetTextColor(252, 247, 247);
$pdf->MultiCell(23, 5,'0.060 kWh' , 1, 'C', 1, 0,'97', '240');
//After value
$pdf->SetFont($fontname, 'B', 12);
$pdf->SetLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(253, 0, 0)));
$pdf->SetFillColor(253, 0, 0);
$pdf->SetTextColor(252, 247, 247);
$pdf->MultiCell(23, 1,'0.007 kWh', 1, 'C', 1, 0,'120', '240');

//Before value
$pdf->SetFont($fontname, 'B', 12);
$pdf->SetLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(140, 197, 62)));
$pdf->SetFillColor(140, 197, 62);
$pdf->SetTextColor(252, 247, 247);
$pdf->MultiCell(23, 5,'0.050 kWh' , 1, 'C', 1, 0,'148', '240');
//After value
$pdf->SetFont($fontname, 'B', 12);
$pdf->SetLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(253, 0, 0)));
$pdf->SetFillColor(253, 0, 0);
$pdf->SetTextColor(252, 247, 247);
$pdf->MultiCell(23, 1,'0.006 kWh', 1, 'C', 1, 0,'171', '240');

$pdf->SetFont($fontname, 'B', 11);
$pdf->SetTextColor(0, 0, 0);
$pdf->MultiCell(50, 3, 'Cost per Hour:','', 'L','', 0,'15', '250');
$pdf->MultiCell(50, 3, 'Cost per Day','', 'L','', 0,'15', '258');
$pdf->MultiCell(50, 3, 'Cost per Month','', 'L','', 0,'15', '266');
$pdf->SetFont($fontname, 'B', 8);
//Compare1
$pdf->MultiCell(50, 3, '≈ R0.04','', 'L','', 0,'52', '250');
$pdf->MultiCell(50, 3, '= R0.36','', 'L','', 0,'52', '258');
$pdf->MultiCell(50, 3, '= R9.00','', 'L','', 0,'52', '266');
$pdf->MultiCell(50, 3, '≈ R0.02','', 'L','', 0,'75', '250');
$pdf->MultiCell(50, 3, '= R0.20','', 'L','', 0,'75', '258');
$pdf->MultiCell(50, 3, '= R5.00','', 'L','', 0,'75', '266');

//Compare2
$pdf->MultiCell(50, 3, '≈ R0.06','', 'L','', 0,'100', '250');
$pdf->MultiCell(50, 3, '= R0.60','', 'L','', 0,'100', '258');
$pdf->MultiCell(50, 3, '= R15.00','', 'L','', 0,'100', '266');
$pdf->MultiCell(50, 3, '≈ R0.01','', 'L','', 0,'125', '250');
$pdf->MultiCell(50, 3, '= R0.07','', 'L','', 0,'125', '258');
$pdf->MultiCell(50, 3, '= R1.75','', 'L','', 0,'125', '266');

//Compare3
$pdf->MultiCell(50, 3, '≈ R0.05','', 'L','', 0,'155', '250');
$pdf->MultiCell(50, 3, '= R0.50','', 'L','', 0,'155', '258');
$pdf->MultiCell(50, 3, '= R12.50','', 'L','', 0,'155', '266');
$pdf->MultiCell(50, 3, '≈ R0.01','', 'L','', 0,'175', '250');
$pdf->MultiCell(50, 3, '= R0.06','', 'L','', 0,'175', '258');
$pdf->MultiCell(50, 3, '= R1.50','', 'L','', 0,'175', '266');
// ------------------Fifth Page---------------------------------------
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

$val = 0;
$responsibility = "I, ";
$responsibility2 = " in my Capacity as";
$responsibility3 = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;hereby accept the quotation submitted to me by Ellies for " .$val . " of energy efficient lighting to the amount of R" . $val  .".  I understand that a 10% deposit is payable before any work will commence and that the rest of the amount will be payable " . $val . " upon completion of the job and signing of the jobcard below once all equipment";

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
$pdf->TextField('name', 177, 6,array('multiline'=>true), array('v'=>$_REQUEST["name"],'dv'=>$_REQUEST["name"]),'18','68');
$pdf->TextField('capacity', 150, 6,array('multiline'=>true), array('v'=>$_REQUEST["capacity"],'dv'=>$_REQUEST["capacity"]),'46','73');

$pdf->TextField('signAt', 30, 6,array(), array('v'=>$_REQUEST["signat"],'dv'=>$_REQUEST["signat"]),'35','99');
$pdf->TextField('onThis', 30, 6,array(), array('v'=>$_REQUEST["onthis"],'dv'=>$_REQUEST["onthis"]),'78','99');
$pdf->TextField('day', 30, 6,array(), array('v'=>$_REQUEST["dayof"],'dv'=>$_REQUEST["dayof"]),'121','99');
$pdf->TextField('customer', 40, 6,array(), array('v'=>$_REQUEST["forcustomer"],'dv'=>$_REQUEST["forcustomer"]),'45','109');
$pdf->TextField('company', 45, 6,array(), array('v'=>$_REQUEST["forcompany"],'dv'=>$_REQUEST["forcompany"]),'101','109');

$pdf->MultiCell(30, 3, "Signed at:",'', 'C','', 0,'10', '100');
$pdf->MultiCell(30, 3, "on this",'', 'C','', 0,'56', '100');
$pdf->MultiCell(30, 3, "day of",'', 'C','', 0,'100', '100');
$pdf->MultiCell(30, 3, "for the Customer",'', 'C','', 0,'50', '115');
$pdf->MultiCell(50, 3, "for the Company: Ellies ",'', 'C','', 0,'100', '115');

$Content = '
    <p><u>Terms and conditions</u></p>
    <ul>
	    <li>This Quote is valid for a period of thirty (30) days from date hereof. Should we receive your order AFTER thirty (30) days from date hereof, we may need to adjust your pricing based on the ruling rates of exchange.</li>
	    <li>Our proposal is based on current exchange rates.</li>
	    <li>The scope of works may change while our contractors are on site due to changes in your requirements.  Any items exceeding the above quantities will be charged separately at the same rate as detailed above.</li>
	    <li>The estimated savings are based on operating hours given by customer and electricity bill provided.</li>
	    <li>Should the actual design differ from the information supplied then a revised variation quote will be submitted to you.</li>
	    <li>Our labour rates are based on normal working hours. Should you require work to be done after hours, labour will be charged at ruling overtime rates and a new quote will be supplied.</li>
	    <li>It is the sole responsibility of the business owner to ensure that all equipment and furniture is moved so as to allow unhindered access to all required areas to complete the installation.  Ellies and its representatives are fully indemnified against all responsibility arising from any damage in this regard.</li>
	    <li>Ellies warrants, subject to our normal condition of sale which are available on request, the equipment and services </li>
	    <li>E. & O.E.</li>
	    <li>The installation carries a warranty for a period of twelve (12) months from the date of installation</li>
	    <li>Lighting products carry a 3 year warranty from the date of installation..</li>
	    <li>Should payment not be made on due date, Ellies reserves the right to remove all equipment installed and claim such damages from the client.</li>
	    <li>Interest at 2% per month will be levied on all overdue accounts.</li>
	</ul>
	<p><u>Additional terms and conditions if the project is paid as a full maintenance rental contract</u></p>
    <ul class="details">
	    <li>Ellies will endeavour to investigate and replace where necessary any Ellies installed products indicated in this quote that may fail during the duration of the contract term, within 72 hours of the fault being reported subject to any circumstances beyond our control that may prevent service delivery within the stipulated time frame.  Callout operating hours Monday to Friday 08:00 - 17:00 excluding public holidays</li>
	    <li>Additional terms and conditions are stipulated in the rental contract which must be signed in addition to this quote acceptance document.</li>
	    <li>Contracts are subject to a 20% Deposit to be paid upfront</li>
    </ul>';

$pdf->writeHTMLCell(178, 20,14,121,$Content, 0, 0, '','', 'L', true);
// ------------------Sixth Page---------------------------------------
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

$val = 0;
if(!strlen($_REQUEST["company_name"]) == 0)
{
	$formCompany  = "Company: " . $_REQUEST["company_name"];
}else{ $formCompany  ="Company: None"; }

if(!strlen($_REQUEST["street_address"]) == 0)
{
	$address  = "Address: " . $_REQUEST["street_address"];
}else{ $address  ="Address: None"; }

if(!strlen($_REQUEST["contact_name"]) == 0)
{
	$contact  = "Contact: " . $_REQUEST["contact_name"];
}else{ $contact  ="Contact: None"; }

if(!strlen($_REQUEST["tel_contact_number"]) == 0)
{
	$cellphone  = "Cellphone: " . $_REQUEST["tel_contact_number"];
}else{ $cellphone  ="Cellphone: None";  }

$description =  $formCompany .'<br/>'.$address.'<br/>'.$contact .'<br/>'.$cellphone;
$Content = '
    <style>
       <link rel="stylesheet" href="css/layout.css"  type="text/css"/>
    </style>
    <p>' .$description . '</p>';

$pdf->writeHTMLCell(80, 25,14,71,$Content, 1, 0, '','', 'L', true);

$description= "This project entails the supply and installation of ".$val." efficient lighting technologies to retrofit or replace ".$val." inefficient lighting sources found at the premises of "
		.$formCompany. ". Work to be completed per specified location is described below.";

$pdf->MultiCell(80,25, $description  , 1, 'C', 1, 0,'114', '71');
$description= "Stock used and returned to be indicated below. At least 5 pictures to be taken on site. Customer and Installer to sign completed Job Card.";

$totalVAtExcl = 0;
$totalVAt = 0;
$totalVAtIncl = 0;
$table = '
    <style>
       <link rel="stylesheet" href="css/layout.css"  type="text/css"/>
    </style>

      <table>
        <tr>
            <td>Total Value excluding VAT</td>
            <td align="center">' .$totalVAtExcl . '</td>
        </tr>
        <tr>
            <td>VAT Calculated at 14%</td>
            <td align="center">' . $totalVAt . '</td>
        </tr>
        <tr>
            <td>Total Value Including VAT</td>
            <td align="center">' . $totalVAtIncl .'</td>
        </tr>
    </table>';
$pdf->writeHTMLCell(85, 15,110,200,$table, 0, 0, '','', 'L', true);

//Generate the number of items
$records = $_REQUEST["jobcard"];
$numberItems =  count(explode(";" , $records)) + 3 ;
$end = $numberItems - 4;
$items = '';
$val = 0;
//echo count(explode(";" , $records)). ":".$numberItems;exit;

if(count(explode(";" , $records)) == 1)
{
  for ($x = 0; $x < 16; $x++) {
       if($x == 12)
       {
         $pdf->SetTextColor(0,0,0);
         $items .=  '
          <tr>
              <td>Labour</td>
              <td></td>
              <td align="center">0</td>
              <td align="center">0</td>
              <td align="center">0</td>
         </tr>

         <tr>
              <td>Travel(Chargeg at R' . $val.' per km)</td>
              <td></td>
              <td align="center">0</td>
              <td align="center">0</td>
              <td align="center">0</td>
         </tr>

          <tr>
              <td>Sundry Expenses</td>
              <td></td>
              <td align="center">0</td>
              <td align="center">0</td>
              <td align="center">0</td>
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
                <td>Labour</td>
                <td></td>
                <td align="center">0</td>
                <td align="center">0</td>
                <td align="center">0</td>
           </tr>

           <tr>
                <td>Travel(Chargeg at R' . $val.' per km)</td>
                <td></td>
                <td align="center">0</td>
                <td align="center">0</td>
                <td align="center">0</td>
           </tr>

            <tr>
                <td>Sundry Expenses</td>
                <td></td>
                <td align="center">0</td>
                <td align="center">0</td>
                <td align="center">0</td>
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

$table = '
      <table border = "1" cellpadding = "2">
        <tr>
            <th bgcolor ="#8CC53E">Item Code </th>
            <th bgcolor ="#8CC53E">Description</th>
            <th bgcolor ="#8CC53E">Quantity</th>
            <th bgcolor ="#8CC53E">Unit Cost</th>
            <th bgcolor ="#8CC53E">Extended Cost</th>
        </tr> ' . $items . '
       
    </table>';
$pdf->writeHTMLCell(0, 0,13,105,$table, 0, 0, '','', 'L', true);


$Content = '
    <style>
       <link rel="stylesheet" href="css/layout.css"  type="text/css"/>
    </style>
    <p>Stock used and returned to be indicated above.<br/>At least 5 pictures to be taken on site.<br/>Customer and Installer to sign completed Job Card.</p>';
$pdf->writeHTMLCell(85, 15,14,255,$Content, 1, 0, '','', 'L', true);
//Generate the number of items
$records = $_REQUEST["returnedstock"];
$numberItems =  count(explode(";" , $records)) -1;
$items = '';
$val = 0;
if($numberItems > 0)
{
	for ($x = 0; $x < $numberItems; $x++) {
       	 $arrRecords = explode(";" , $records);
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

if(!strlen($_REQUEST["company_name"]) == 0)
{
	$formCompany  = $_REQUEST["company_name"];
}else{ $formCompany  ="None"; }
$pdf->MultiCell(180, 3,"Project signed off on:",'', 'L','', 0,'110', '230');
$pdf->MultiCell(180, 3,"For Customer ". $formCompany .":",'', 'L','', 0,'110', '240');
$pdf->MultiCell(180, 3,"For Installer:",'', 'l','', 0,'110', '250');

$pdf->TextField('signProject', 40, 6,array(), array('v'=>$_REQUEST["signproject"],'dv'=>$_REQUEST["signproject"]),'150','228');
$pdf->TextField('signCustomer', 40, 6,array(), array('v'=>$_REQUEST["forcustomer"],'dv'=>$_REQUEST["forcustomer"]),'160','238');
$pdf->TextField('signInstaller', 40, 6,array(), array('v'=>$_REQUEST["forinstaller"],'dv'=>$_REQUEST["forinstaller"]),'135','248');
// --------------------------------------------------------------

//Close and output PDF document
$pdf->Output('Ellies Renewable Energy.pdf', 'I');
?>