<?php
error_reporting(E_ALL);
date_default_timezone_set('Africa/Johannesburg');
ini_set('display_errors', TRUE);

require_once('phpexcel/PHPExcel.php');
require_once('phpexcel/PHPExcel/IOFactory.php');

ini_set('memory_limit', '-1');
ini_set('max_execution_time', 9048);
set_time_limit(9048);

$objPHPExcel = new PHPExcel();
$typeString = PHPExcel_Cell_DataType::TYPE_STRING;
$typeNumeric = PHPExcel_Cell_DataType::TYPE_NUMERIC;

// Set properties
$objPHPExcel->getProperties()->setCreator("Theo Malongete")
				                     ->setLastModifiedBy("Theo Malongete")
				                     ->setTitle("Customer Template")
				                     ->setSubject("Customer Template")
				                     ->setDescription("Customer Template Details")
				                     ->setKeywords("Customer Template")
				                    ->setCategory("Customer Template");

$green = new PHPExcel_Style();
$green->applyFromArray(
	array('fill' 	=> array(
								'type'		=> PHPExcel_Style_Fill::FILL_SOLID,
								'color'		=> array('argb' => '8cc53e')
							),
		    'borders' => array(
                'top'    => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom'    => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right'     => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'left'     => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'inside'     => array('style' => PHPExcel_Style_Border::BORDER_THIN)
              ),
		    'font'    => array(
								'bold'      => true
							),
		    'alignment' => array(
								'wrap' => true,
								'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
							)
		 ));


function getColIndex($Columns =array(),$col =""){
  $index = 0;
  $c = 0;
  foreach ($Columns AS $Column) {
        if(stristr($Column["field"],$col)) {
          $index = $c;
        }
        $c++;
  }
  return $index;
}

//SETTINGS
$Columns=array(
    array("title"=>"Date Created","field"=>"Date","width"=>50),
    array("title"=>"Name","field"=>"CustomerName","width"=>50),
    array("title"=>"Surname","field"=>"Surname","width"=>10),
    array("title"=>"Email Address","field"=>"Email","width"=>20),
    array("title"=>"Cellphone","field"=>"Cellphone","width"=>20),
    array("title"=>"Status","field"=>"Status","width"=>20)
);
$numCols = count($Columns);

//Creates a database connection
$db_host = "dedi48.jnb1.host-h.net";
$db_user = "cltdrx_ellsdev";
$db_pass = 'JzESNju8';
$db_name = "cltdrx_ellsdev";
$connection = mysqli_connect( $db_host,$db_user, $db_pass,$db_name);
//Test if connection occured
if(mysqli_connect_errno()){
    die("Database connection failed:".
        mysqli_connect_error().
        "(" . mysqli_connect_errno() . ")"    
    );
} 

$channelID = (int)isset($_REQUEST["channel"])?$_REQUEST["channel"]:0;
$branchID  = "";
$sheet  = $objPHPExcel->getSheet(0);
//Channel Type
$sql = "SELECT `id`,`type` FROM `channels` WHERE `id`=".$channelID;
$sqlres = mysqli_query($connection,$sql);
$row = mysqli_fetch_assoc($sqlres);
$channeltype = $row['type'];
if(isset($_REQUEST["branch"])){
  if($_REQUEST["branch"] =="All"){
    $branchID = "All";
    $query  = "SELECT *";
    $query .= " FROM `channels`";
    $query .= " WHERE `channels`.type='".$channeltype."'";
    $result = mysqli_query($connection,$query);
    $arrChannels = array();
    while ($arr = mysqli_fetch_assoc($result)) {
      $arrChannels[] = $arr;
    }
    for ($i=0; $i <count($arrChannels); $i++) {

      if($i > 0 && $i <count($arrChannels)){
       $sheet = $objPHPExcel->createSheet();
      } 

      $sheet = $objPHPExcel->getSheet($i);

      //Set title and Dimensions
      $colcounter=0;
      $rowStart = 3;
      foreach ($Columns as $Column) {
        $FieldSettings = $Column;
        $width = 0;
        if (isset($FieldSettings["width"])) {
          $title = $FieldSettings["title"];
          $width = (int)$FieldSettings["width"];
          $col = PHPExcel_Cell::stringFromColumnIndex($colcounter);
          if ($title)$sheet->setCellValue($col.$rowStart, $title);
          if ($width)$sheet->getColumnDimension($col)->setWidth($width);
        } 
        $colcounter++;
      }

      $sheet->getRowDimension('3')->setRowHeight(35);

      $sheet->freezePane('A'.($rowStart));

      //Auto Filter
      $sheet->setAutoFilter(PHPExcel_Cell::stringFromColumnIndex(0).($rowStart).":".PHPExcel_Cell::stringFromColumnIndex(sizeof($Columns)-1).($rowStart));

      $sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
      $sheet->getDefaultStyle()->getFont()->setName('Arial');
      $sheet->getDefaultStyle()->getFont()->setSize(10);

      $sheet->setSharedStyle($green,"A3:".PHPExcel_Cell::stringFromColumnIndex($numCols-1)."3");
    }
  }else{
  
    //Set title and Dimensions
    $colcounter=0;
    $rowStart = 3;
    foreach ($Columns as $Column) {
      $FieldSettings = $Column;
      $width = 0;
      if (isset($FieldSettings["width"])) {
        $title = $FieldSettings["title"];
        $width = (int)$FieldSettings["width"];
        $col = PHPExcel_Cell::stringFromColumnIndex($colcounter);
        if ($title)$sheet->setCellValue($col.$rowStart, $title);
        if ($width)$sheet->getColumnDimension($col)->setWidth($width);
      } 
      $colcounter++;
    }

    $sheet->getRowDimension('3')->setRowHeight(35);

    $sheet->freezePane('A'.($rowStart));

    //Auto Filter
    $sheet->setAutoFilter(PHPExcel_Cell::stringFromColumnIndex(0).($rowStart).":".PHPExcel_Cell::stringFromColumnIndex(sizeof($Columns)-1).($rowStart));

    $sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
    $sheet->getDefaultStyle()->getFont()->setName('Arial');
    $sheet->getDefaultStyle()->getFont()->setSize(10);

    $sheet->setSharedStyle($green,"A3:".PHPExcel_Cell::stringFromColumnIndex($numCols-1)."3");
  }
}    
//-------------------------------------------------
$rowStart = 3;
$rowStart +=1;

$channel_name  = "";
$channelID = (int)isset($_REQUEST["channel"])?$_REQUEST["channel"]:0;
$branchID  = "";
//Channel Type
$sql = "SELECT `id`,`type` FROM `channels` WHERE `id`=".$channelID;
$sqlres = mysqli_query($connection,$sql);
$row = mysqli_fetch_assoc($sqlres);
$channeltype = $row['type'];
if(isset($_REQUEST["branch"])){
  if($_REQUEST["branch"] =="All"){
    $branchID = "All";
    $query  = "SELECT *";
    $query .= " FROM `channels`";
    $query .= " WHERE `channels`.type='".$channeltype."'";
    $result = mysqli_query($connection,$query);
    $arrChannels = array();
    while ($arr = mysqli_fetch_assoc($result)) {
      $arrChannels[] = $arr;
    }
    
    for ($i=0; $i <count($arrChannels) ; $i++) {
      $rowStart =3;
      $rowStart +=1;

      $details = $arrChannels[$i];
      $channel_name = $details['name'];
      if($channel_name =="")$channel_name = "(NONE)";

      $sheet->setCellValueExplicit('A1',"Channel",$typeString);
      $sheet->setCellValueExplicit('B1',$channel_name,$typeString);
      //Sheet names
      $sheet->setTitle($channel_name); 

      //Customers
      $query  = "SELECT *,`customers`.name AS customers_name";
      $query .= " FROM `customers` INNER JOIN `channels` ON `channels`.id = `customers`.channel";
      $query .= " WHERE `customers`.channel=".$details['id'];
      $result = mysqli_query($connection,$query);
      $array = array();
      while ($arr = mysqli_fetch_assoc($result)) {
        $array[] = $arr;
      }

      for ($c=0; $c < count($array) ; $c++) { 
        $member = $array[$c];
        $col = PHPExcel_Cell::stringFromColumnIndex(getColIndex($Columns,'Date'));
        $date = date('Y-m-d',strtotime($member['date_created']));
        $sheet->setCellValueExplicit($col.$rowStart,$date,$typeString);
        $col = PHPExcel_Cell::stringFromColumnIndex(getColIndex($Columns,'CustomerName'));
        $sheet->setCellValueExplicit($col.$rowStart,$member['customers_name'],$typeString);
        $col = PHPExcel_Cell::stringFromColumnIndex(getColIndex($Columns,'Surname'));
        $sheet->setCellValueExplicit($col.$rowStart,$member['surname'],$typeString);
        $col = PHPExcel_Cell::stringFromColumnIndex(getColIndex($Columns,'Email'));
        $sheet->setCellValueExplicit($col.$rowStart,$member['email'],$typeString);                            
        $col = PHPExcel_Cell::stringFromColumnIndex(getColIndex($Columns,'Cellphone'));
        $sheet->setCellValueExplicit($col.$rowStart,$member['cellphone'],$typeString);
        $col = PHPExcel_Cell::stringFromColumnIndex(getColIndex($Columns,'Status'));
        $Status = $member['active'];
        if($Status =="1")  $Status = "Active";
        if($Status =="0")  $Status = "Inctive";
        $sheet->setCellValueExplicit($col.$rowStart,$Status,$typeString);
        $rowStart+=1;
      }

      //Sheet names
      $sheet = $objPHPExcel->getSheet($i);
    }
  }else{
    $branchID = (int)isset($_REQUEST["branch"])?$_REQUEST["branch"]:0;
    $query  = "SELECT *";
    $query .= " FROM `channels`";
    $query .= " WHERE `channels`.id='".$branchID."'";
    $result = mysqli_query($connection,$query);
    $row = mysqli_fetch_assoc($result);
    $channel_name = $row['name'];
    if($channel_name =="")$channel_name = "(NONE)";

    $sheet->setCellValueExplicit('A1',"Channel",$typeString);
    $sheet->setCellValueExplicit('B1',$channel_name,$typeString);

    //Customers
    $query  = "SELECT *,`customers`.name AS customers_name,`customers`.date_created AS customers_created";
    $query .= " FROM `customers` INNER JOIN `channels` ON `channels`.id = `customers`.channel";
    $query .= " WHERE `customers`.channel=".$branchID;
    $result = mysqli_query($connection,$query);
    $array = array();
    while ($arr = mysqli_fetch_assoc($result)) {
      $array[] = $arr;
    }

    for ($i=0; $i < count($array) ; $i++) { 
      $member = $array[$i];
      $col = PHPExcel_Cell::stringFromColumnIndex(getColIndex($Columns,'Date'));
      $date = date('Y-m-d',strtotime($member['customers_created']));
      $sheet->setCellValueExplicit($col.$rowStart,$date,$typeString);
      $col = PHPExcel_Cell::stringFromColumnIndex(getColIndex($Columns,'CustomerName'));
      $sheet->setCellValueExplicit($col.$rowStart,$member['customers_name'],$typeString);
      $col = PHPExcel_Cell::stringFromColumnIndex(getColIndex($Columns,'Surname'));
      $sheet->setCellValueExplicit($col.$rowStart,$member['surname'],$typeString);
      $col = PHPExcel_Cell::stringFromColumnIndex(getColIndex($Columns,'Email'));
      $sheet->setCellValueExplicit($col.$rowStart,$member['email'],$typeString);                            
      $col = PHPExcel_Cell::stringFromColumnIndex(getColIndex($Columns,'Cellphone'));
      $sheet->setCellValueExplicit($col.$rowStart,$member['cellphone'],$typeString);
      $col = PHPExcel_Cell::stringFromColumnIndex(getColIndex($Columns,'Status'));
      $Status = $member['active'];
      if($Status =="1")  $Status = "Active";
      if($Status =="0")  $Status = "Inctive";
      $sheet->setCellValueExplicit($col.$rowStart,$Status,$typeString);
      $rowStart+=1;
    }

    //Sheet names
    $sheet->setTitle($channel_name); 
  }
}
//-------------------------------------------------
//Protection details
$sheet->getProtection()->setPassword('theo');
$sheet->getProtection()->setSheet(true);
$sheet->getProtection()->setInsertRows(true);
$sheet->getProtection()->setFormatCells(true);
//======================================================================

//Create a new sheet with the Config
$sheet = $objPHPExcel->createSheet();
$sheet->setTitle('Config')->setSheetState( PHPExcel_Worksheet::SHEETSTATE_HIDDEN);
$sheet->getProtection()->setPassword('theo');
$sheet->getProtection()->setSheet(true);
$sheet->getProtection()->setInsertRows(true);
$sheet->getProtection()->setInsertColumns(true);
$sheet->getProtection()->setFormatCells(true);

$sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex(0))->setWidth(25);
$sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex(1))->setWidth(20);

$i=1;
$sheet->setCellValue("A{$i}","Generated");
$sheet->setCellValue("B{$i}",date("Y-m-d H:i:s"));
$i++;
$sheet->setCellValue("A{$i}","BranchID");
$sheet->setCellValue("B{$i}",$branchID);

//Setup the rest of the excel file
$pageMargins = $sheet->getPageMargins();

// margin is set in inches (0.5cm)
$margin = 0.5 / 2.54;

$pageMargins->setTop($margin*3.5);
$pageMargins->setBottom($margin*2);
$pageMargins->setLeft($margin);
$pageMargins->setRight($margin);

$sheet->getPageSetup()->setFitToPage(true);
$sheet->getPageSetup()->setFitToWidth(1);
$sheet->getPageSetup()->setFitToHeight(0);

$sheet->getHeaderFooter()->setOddHeader('&LPage &P of &N&C Customer Sheet Log&R&G');
$sheet->getHeaderFooter()->setOddFooter('&CGenerated by Ellies| Export date: &D');

$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 3);
//======================================================================
// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Customer_Export.xlsx"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objPHPExcel->setActiveSheetIndex(0);
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
?>