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
require('../quotes/fpdf/fpdf.php');

function DeFrac($text) {
    $text = str_replace("&frac12;", "%BD", $text);
    $text = str_replace("&frac14;", "%BC", $text);
    return urldecode($text);
}

class PDF extends FPDF
{
    public $MyPageCount = 0;
    public $IsDisclaimer = false;
    function RoundedRect($x, $y, $w, $h, $r, $style = '', $angle = '1234')
    {
        $k = $this->k;
        $hp = $this->h;
        if($style=='F')
            $op='f';
        elseif($style=='FD' or $style=='DF')
            $op='B';
        else
            $op='S';
        $MyArc = 4/3 * (sqrt(2) - 1);
        $this->_out(sprintf('%.2f %.2f m', ($x+$r)*$k, ($hp-$y)*$k ));

        $xc = $x+$w-$r;
        $yc = $y+$r;
        $this->_out(sprintf('%.2f %.2f l', $xc*$k, ($hp-$y)*$k ));
        if (strpos($angle, '2')===false)
            $this->_out(sprintf('%.2f %.2f l', ($x+$w)*$k, ($hp-$y)*$k ));
        else
            $this->_Arc($xc + $r*$MyArc, $yc - $r, $xc + $r, $yc - $r*$MyArc, $xc + $r, $yc);

        $xc = $x+$w-$r;
        $yc = $y+$h-$r;
        $this->_out(sprintf('%.2f %.2f l', ($x+$w)*$k, ($hp-$yc)*$k));
        if (strpos($angle, '3')===false)
            $this->_out(sprintf('%.2f %.2f l', ($x+$w)*$k, ($hp-($y+$h))*$k));
        else
            $this->_Arc($xc + $r, $yc + $r*$MyArc, $xc + $r*$MyArc, $yc + $r, $xc, $yc + $r);

        $xc = $x+$r;
        $yc = $y+$h-$r;
        $this->_out(sprintf('%.2f %.2f l', $xc*$k, ($hp-($y+$h))*$k));
        if (strpos($angle, '4')===false)
            $this->_out(sprintf('%.2f %.2f l', ($x)*$k, ($hp-($y+$h))*$k));
        else
            $this->_Arc($xc - $r*$MyArc, $yc + $r, $xc - $r, $yc + $r*$MyArc, $xc - $r, $yc);

        $xc = $x+$r ;
        $yc = $y+$r;
        $this->_out(sprintf('%.2f %.2f l', ($x)*$k, ($hp-$yc)*$k ));
        if (strpos($angle, '1')===false)
        {
            $this->_out(sprintf('%.2f %.2f l', ($x)*$k, ($hp-$y)*$k ));
            $this->_out(sprintf('%.2f %.2f l', ($x+$r)*$k, ($hp-$y)*$k ));
        }
        else
            $this->_Arc($xc - $r, $yc - $r*$MyArc, $xc - $r*$MyArc, $yc - $r, $xc, $yc - $r);
        $this->_out($op);
    }

    function _Arc($x1, $y1, $x2, $y2, $x3, $y3)
    {
        $h = $this->h;
        $this->_out(sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c ', $x1*$this->k, ($h-$y1)*$this->k,
            $x2*$this->k, ($h-$y2)*$this->k, $x3*$this->k, ($h-$y3)*$this->k));
    }
    
    function Footer()
    {
        // Go to 1.5 cm from bottom
        $this->SetY(-15);
        // Select Arial italic 8
        //$this->SetFont('Arial','I',10);
        // Print centered page number
        //$this->Cell(100,10,'Page '.$this->PageNo().' of '.$this->MyPageCount,0,0,'L');
	//if (!$this->IsDisclaimer) {
	    //$this->Cell(90,10,'Refer to disclaimers on Page '.($this->MyPageCount -1),0,0,'R');
	//}
	
    }


var $widths;
var $aligns;

function SetWidths($w)
{
    //Set the array of column widths
    $this->widths=$w;
}

function SetAligns($a)
{
    //Set the array of column alignments
    $this->aligns=$a;
}

function Row($data)
{
    //Calculate the height of the row
    $nb=0;
    for($i=0;$i<count($data);$i++)
        $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
    $h=5*$nb;
    //Issue a page break first if needed
    $this->CheckPageBreak($h);
    //Draw the cells of the row
    for($i=0;$i<count($data);$i++)
    {
        $w=$this->widths[$i];
        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
        //Save the current position
        $x=$this->GetX();
        $y=$this->GetY();
        //Draw the border
        $this->Rect($x,$y,$w,$h);
        //Print the text
        $this->MultiCell($w,5,$data[$i],0,$a);
        //Put the position to the right of the cell
        $this->SetXY($x+$w,$y);
    }
    //Go to the next line
    $this->Ln($h);
}

function CheckPageBreak($h)
{
    //If the height h would cause an overflow, add a new page immediately
    if($this->GetY()+$h>$this->PageBreakTrigger)
        $this->AddPage($this->CurOrientation);
}

function NbLines($w,$txt)
{
    //Computes the number of lines a MultiCell of width w will take
    $cw=&$this->CurrentFont['cw'];
    if($w==0)
        $w=$this->w-$this->rMargin-$this->x;
    $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
    $s=str_replace("\r",'',$txt);
    $nb=strlen($s);
    if($nb>0 and $s[$nb-1]=="\n")
        $nb--;
    $sep=-1;
    $i=0;
    $j=0;
    $l=0;
    $nl=1;
    while($i<$nb)
    {
        $c=$s[$i];
        if($c=="\n")
        {
            $i++;
            $sep=-1;
            $j=$i;
            $l=0;
            $nl++;
            continue;
        }
        if($c==' ')
            $sep=$i;
        $l+=$cw[$c];
        if($l>$wmax)
        {
            if($sep==-1)
            {
                if($i==$j)
                    $i++;
            }
            else
                $i=$sep+1;
            $sep=-1;
            $j=$i;
            $l=0;
            $nl++;
        }
        else
            $i++;
    }
    return $nl;
}


}


function PrintHeader($pdf, $title, $quote) {
    $pdf->AddPage();
    $pdf->SetTextColor(0);
    $pdf->Image("images/logo.png", 10, 5);
    $pdf->SetFillColor(255,255,255);
    $pdf->Rect(5, 22, 50, 15, "F");
    $pdf->SetFont('Arial','B',18);
    $pdf->Text(10, 30, $title);

    $pdf->SetFont('Arial','B',10);

    $fullbannerwidth = 190;
    $colw2 = 30;
    $colw3 = 50;
    $colw1 = $fullbannerwidth - ($colw2 + $colw3) - 2;

    $cols = null;
    $cols[] = array("text" => "", "width" => $colw1, "style" => 0);
    $cols[] = array("text" => "Reference", "width" => $colw2, "style" => 2);
    $cols[] = array("text" => $quote->QuoteReferenceNo, "width" => $colw3, "style" => 1);
    PrintTr($pdf, $cols);

    $cols = null;
    $cols[] = array("text" => "", "width" => $colw1, "style" => 0);
    $cols[] = array("text" => "Date", "width" => $colw2, "style" => 2);
    $cols[] = array("text" => $quote->DateCreated, "width" => $colw3, "style" => 1);
    PrintTr($pdf, $cols);

    $cols = null;
    $cols[] = array("text" => "", "width" => $colw1, "style" => 0);
    $cols[] = array("text" => "Price/KWh", "width" => $colw2, "style" => 2);
    $cols[] = array("text" => "R ".number_format($quote->KWhPrice, 2), "width" => $colw3, "style" => 1);
    PrintTr($pdf, $cols);

    $cols = null;
    $cols[] = array("text" => "", "width" => $colw1, "style" => 0);
    $cols[] = array("text" => "Escalation Rate", "width" => $colw2, "style" => 2);
    $cols[] = array("text" => $quote->ElectricityEscalationRatePercentage." %", "width" => $colw3, "style" => 1);
    PrintTr($pdf, $cols);

    //$pdf->pages
    $pdf->ln();
    $pdf->ln();
    $pdf->ln();
}


function PrintTr($pdfobj, $cols, $lineheight = 6, $cellspacing = 1) {
	$pdfobj->SetFont('Arial','',18);
    foreach($cols as $col) {
        $fill = false;
        switch ($col["style"]) {
            case "0":
                $borders = "";
                $fill = false;
                break;
            case "1":
                $pdfobj->SetFillColor(0,0,0);
                $pdfobj->SetFont('');
                $borders = "LTRB";
                $fill = false;
                break;
            case "2":
                $pdfobj->SetFillColor(233,233,233);
                $pdfobj->SetTextColor(0);
                //$pdfobj->SetFont('','B');
		$pdfobj->SetFont('Arial','B',18);
		//$lineheight = 6;
                $borders = "LTRB";
                $fill = true;
                break;
            case "3":
                $pdfobj->SetFillColor(87,88,90);
                $pdfobj->SetTextColor(255,255,255);
                $pdfobj->SetFont('','B');
                $borders = "LTRB";
                $fill = true;
                break;
            case "2b":
                $pdfobj->SetFillColor(233,233,233);
                $pdfobj->SetTextColor(0);
                $pdfobj->SetFont('Arial','B', 12);
                $borders = "LTRB";
                $fill = true;
                break;
            case "3b":
                $pdfobj->SetFillColor(87,88,90);
                $pdfobj->SetTextColor(255,255,255);
                $pdfobj->SetFont('Arial','B', 12);
                $borders = "LTRB";
                $fill = true;
                break;
            case "4":
                $pdfobj->SetFillColor(220,29,36);
                $pdfobj->SetTextColor(255,255,255);
                $pdfobj->SetFont('','B');
                $borders = "LTRB";
                $fill = true;
                break;
            case "5":
                $pdfobj->SetFillColor(127,203,42);
                $pdfobj->SetTextColor(255,255,255);
                $pdfobj->SetFont('','B');
                $borders = "LTRB";
                $fill = true;
                break;
            case "6":
                $pdfobj->SetFillColor(40,65,9);
                $pdfobj->SetTextColor(255,255,255);
                $pdfobj->SetFont('','B');
                $borders = "LTRB";
                $fill = true;
                break;
        }
	$multiline = false;
	if (strpos($col["text"], "\n") > 0) {
	    $coltexts = explode("\n", $col["text"]);
	    $col["text"] = "";
	    $multiline = true;
	}
        $pdfobj->Cell($col["width"], $lineheight, $col["text"], $borders, 0, $align = "", $fill);
	$mx = "";
	$my = "";
	if ($multiline) {
	    $mx = $pdfobj->GetX() + 1;
	    $mx -= $col["width"];
	    $my = $pdfobj->GetY() + 3;
	    $pdfobj->SetFont('Arial','',7);
	    $pdfobj->Text($mx, $my, $coltexts[0]);
	    $pdfobj->Text($mx, $my + 2, $coltexts[1]);
	    $pdfobj->SetFont('Arial','',8);
	}

        $pdfobj->Cell($cellspacing, $lineheight, "", "");

	$borders = 0;
	if (!empty($borders)) {
		$borders = 1;
	}
	//$pdfobj->MultiCell($col["width"], $lineheight, $col["text"], $borders, $align = "", $fill);
	//$pdfobj->MultiCell($cellspacing, $lineheight, "", $borders);
	//$pdf->Cell($w, $h, $txt, $border, $ln, $align, $fill, $link);
	//$pdf->MultiCell($w, $h, $txt, $border, $align, $fill);
        
        //$pdf->RoundedRect($x, $y, $w, $h, $r, 'DF', '12');
        //$pdf->RoundedRect($x + $w + $recspacing, $y, $w, $h, $r, 'DF', '12');
        //$pdf->Text($x + $paddingleft, $y + $paddingtop, "ello baldrick");
        //$pdf->Text($x + $paddingleft + $w + $recspacing, $y + $paddingtop, "ello baldrick");
    }
    $pdfobj->Ln();
    $pdfobj->Cell($cellspacing, $cellspacing, "", "");
    $pdfobj->Ln();
}


$pdf=new PDF();
$pdf->Open();
//===Page 1===
$pdf->AddPage();

//$pdf->Image("images/logo.png", 10, 5);
$pdf->SetFillColor(255,255,255);
$pdf->Rect(5, 22, 50, 15, "F");
$pdf->SetFont('Arial','B',20);
$pdf->Text(10, 30, "Checklist");

$pdf->SetFont('Arial','B',18);

$fullbannerwidth = 180;
$colw2 = 50;
$colw3 = 50;
$colw1 = $fullbannerwidth - ($colw2 + $colw3) - 2;

$customername = "John Doe";
$cellphone = "082 012 2312";
$projectcode = $_GET["projectcode"];
//$projectcode = "CLS000001";


$cols = null;
$cols[] = array("text" => "", "width" => $colw1, "style" => 0);
$cols[] = array("text" => "Customer", "width" => $colw2, "style" => 2);
$cols[] = array("text" => $customername, "width" => $colw3, "style" => 1);
PrintTr($pdf, $cols, 9);

$cols = null;
$cols[] = array("text" => "", "width" => $colw1, "style" => 0);
$cols[] = array("text" => "Cellphone", "width" => $colw2, "style" => 2);
$cols[] = array("text" => $cellphone, "width" => $colw3, "style" => 1);
PrintTr($pdf, $cols, 9);



$cols = null;
$cols[] = array("text" => "", "width" => $colw1, "style" => 0);
$cols[] = array("text" => "Project Code", "width" => $colw2, "style" => 2);
$cols[] = array("text" => $projectcode, "width" => $colw3, "style" => 1);
PrintTr($pdf, $cols, 9);

$cols = null;
$cols[] = array("text" => "", "width" => $colw1, "style" => 0);
PrintTr($pdf, $cols, 2);
$pdf->ln();
$pdf->MultiCell($fullbannerwidth, 6, "Dear ".$customername.", thank you for continued support. Please verify that the checked items in the checklist have been done to your satisfaction. Please sign this document to acknowledge you acceptance.");
$pdf->ln();

$pdf->SetFont('Arial','',16);

$sql = "SELECT * FROM `checklist_items` WHERE `project_code` = '".mysql_real_escape_string($projectcode)."' ORDER BY `id`";
$sqlres = mysql_query($sql);
$y = 64;
$lineheight = 9;
while($row = mysql_fetch_assoc($sqlres)) {
    $cols = null;
    $strchecked = "Not Checked";
    $strimage = "images/cross.png";
    if ($row["checked"]) {
        $strchecked = "Checked";
        $strimage = "images/tick.png";
    }
    $cols[] = array("text" => $row["description"], "width" => 175, "style" => 2);
    $cols[] = array("text" => "", "width" => 15, "style" => 1);
    PrintTr($pdf, $cols, $lineheight);
    $y += $lineheight + 1;

    $pdf->Image($strimage, 190, $y);
}
$pdf->ln();
$pdf->ln();
$pdf->ln();
$pdf->SetFont('Arial','B',16);
$pdf->MultiCell($fullbannerwidth, 6, "Signed On: ".date("Y-m-d"));

$outtofile = false;
if ($outtofile) {
    $filename = "../quotes/emailedquotes/JobCard-".$projectcode.".pdf";
    if (file_exists($filename)) {
        unlink($filename);
    }
    $pdf->Output($filename, "F");
}else{
    $pdf->Output();    
}


?> 