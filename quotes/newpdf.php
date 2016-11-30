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
require_once("inc/customer.class.php");
require_once("inc/quote.class.php");
require_once("inc/quoteitem.class.php");
require_once("inc/techtype.class.php");
require_once("inc/techitem.class.php");
require_once("inc/product.class.php");

require_once("inc/class.phpmailer.php");

require('fpdf/fpdf.php');

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
        $this->SetFont('Arial','I',10);
        // Print centered page number
        $this->Cell(100,10,'Page '.$this->PageNo().' of '.$this->MyPageCount,0,0,'L');
	if (!$this->IsDisclaimer) {
	    $this->Cell(90,10,'Refer to disclaimers on Page '.($this->MyPageCount -1),0,0,'R');
	}
	
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
	$pdfobj->SetFont('Arial','',8);
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
		$pdfobj->SetFont('Arial','B',8);
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

function PrintDashedLine($pdf) {
    $pdf->ln();
    $pdf->ln();
    $pdf->ln();
    
    $cols = null;
    for ($il = 0; $il < 24; $il++) {
	$cols[] = array("text" => "", "width" => 4, "style" => 2);
	$cols[] = array("text" => "", "width" => 2, "style" => 0);
    }
    PrintTr($pdf, $cols, .2);    
    $pdf->ln();
    $pdf->ln();
    $pdf->ln();
}



$_GET["id"] = 77;
$quote = new Quote($_GET["id"]);
$previewmode = false;
if (!empty($_GET["preview"])) {
	if ($_GET["preview"] == "1") {
		$previewmode = true;
	}
}
$productcount = 0;
$productpagecount = 1;
$icount = 0;
foreach($quote->Items as $item) {
    $productcount += 1;
    $icount += 1;
    if ($icount == 3) {
	$icount = 0;
	$productpagecount += 1;
    }
}
if ($icount > 0 && $icount < 3) {
    $productpagecount += 1;
}
$customer = new Customer($quote->CustomerID);

$pdf=new PDF();
$pdf->Open();
//$productcount = $productcount/3;
$pdf->MyPageCount += ($productpagecount + 2);
//$pdf->MyPageCount = 6;

//===Page 1===
$pdf->AddPage();

$pdf->Image("images/logo.png", 10, 5);
$pdf->SetFillColor(255,255,255);
$pdf->Rect(5, 22, 50, 15, "F");
$pdf->SetFont('Arial','B',18);
$pdf->Text(10, 30, "Quotation");

$pdf->SetFont('Arial','B',10);

$fullbannerwidth = 190;
$colw2 = 30;
$colw3 = 50;
$colw1 = $fullbannerwidth - ($colw2 + $colw3) - 2;
$cols = null;
$cols[] = array("text" => "", "width" => $colw1, "style" => 0);
$cols[] = array("text" => "Customer", "width" => $colw2, "style" => 2);
$cols[] = array("text" => $customer->Name." ".$customer->Surname, "width" => $colw3, "style" => 1);
PrintTr($pdf, $cols);

$cols = null;
$cols[] = array("text" => "", "width" => $colw1, "style" => 0);
$cols[] = array("text" => "Cellphone", "width" => $colw2, "style" => 2);
$cols[] = array("text" => $customer->CellPhone, "width" => $colw3, "style" => 1);
PrintTr($pdf, $cols);

$cols = null;
$cols[] = array("text" => "", "width" => $colw1, "style" => 0);
$cols[] = array("text" => "Email", "width" => $colw2, "style" => 2);
$cols[] = array("text" => $customer->Email, "width" => $colw3, "style" => 1);
PrintTr($pdf, $cols);


$cols = null;
$cols[] = array("text" => "", "width" => $colw1, "style" => 0);
PrintTr($pdf, $cols, 1);


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

$cols = null;
$cols[] = array("text" => "", "width" => $colw1, "style" => 0);
PrintTr($pdf, $cols, 2);
$pdf->ln();
$pdf->MultiCell($fullbannerwidth, 6, "Dear ".$customer->Name." ".$customer->Surname.", thank you for giving Ellies the opportunity to give you a customised energy audit and therefore help you save money. Below are the estimated savings and details of our customised solution for you.");
$pdf->ln();

$cols = null;
$cols[] = array("text" => "Consumption Information", "width" => $fullbannerwidth, "style" => "3b");
PrintTr($pdf, $cols);

$colw2 = 30;
$colw3 = 50;
$colw1 = $fullbannerwidth - ($colw2 + $colw3) - 2;

$colwidths[] = 46;
$colwidths[] = 2;
$colwidths[] = 46;
$colwidths[] = 46;
$colwidths[] = 46;
$colwidths[] = 46;

$pdf->SetFont('Arial','',9);

$quote->CalculateCostSavingTotals();

$cols = null;
$cols[] = array("text" => "", "width" => $colwidths[0], "style" => 0);
$cols[] = array("text" => "", "width" => $colwidths[1], "style" => 0);
$cols[] = array("text" => "Existing", "width" => $colwidths[2], "style" => 2);
$cols[] = array("text" => "Replacement", "width" => $colwidths[3], "style" => 2);
$cols[] = array("text" => "Saved", "width" => $colwidths[4], "style" => 2);
PrintTr($pdf, $cols);

$cols = null;
$cols[] = array("text" => "Total kWh Per Month", "width" => $colwidths[0], "style" => 2);
$cols[] = array("text" => "", "width" => $colwidths[1], "style" => 0);
$cols[] = array("text" => number_format($quote->KWhOld, 0)." kWh", "width" => $colwidths[2], "style" => 4);
$cols[] = array("text" => number_format($quote->KWhNew, 0)." kWh", "width" => $colwidths[3], "style" => 5);
$cols[] = array("text" => number_format($quote->KWhSaved, 0)." kWh (".number_format($quote->KWhSavedPerc, 0)."%)", "width" => $colwidths[4], "style" => 5);
PrintTr($pdf, $cols);

$cols = null;
$cols[] = array("text" => "Total Monthly Cost (Year 1)", "width" => $colwidths[0], "style" => 2);
$cols[] = array("text" => "", "width" => $colwidths[1], "style" => 0);
$cols[] = array("text" => "R ".number_format($quote->MonthlyCostOld, 2), "width" => $colwidths[2], "style" => 4);
$cols[] = array("text" => "R ".number_format($quote->MonthlyCostNew, 2), "width" => $colwidths[3], "style" => 5);
$cols[] = array("text" => "R ".number_format($quote->MonthlyCostSaving, 2)." (".number_format($quote->KWhSavedPerc, 0)."%)", "width" => $colwidths[4], "style" => 5);
PrintTr($pdf, $cols);

$pdf->ln();
$pdf->ln();
$pdf->ln();

$cols = null;
$cols[] = array("text" => "Ellies Customised Solution for Your Needs (see Page 2 for Itemised Details)", "width" => $fullbannerwidth, "style" => "3b");
PrintTr($pdf, $cols);

$colwidths = NULL;
$colwidths[] = 46;
$colwidths[] = 38;

$cols = null;
$cols[] = array("text" => "Number of items to change", "width" => $colwidths[0], "style" => 2);
$cols[] = array("text" => $quote->NumberOfItems, "width" => $colwidths[1], "style" => 1);
PrintTr($pdf, $cols);

$cols = null;
$cols[] = array("text" => "Total (excl) VAT", "width" => $colwidths[0], "style" => 2);
$cols[] = array("text" => "R ".number_format($quote->TotalPriceExVat, 2), "width" => $colwidths[1], "style" => 1);
PrintTr($pdf, $cols);

$cols = null;
$cols[] = array("text" => "VAT", "width" => $colwidths[0], "style" => 2);
$cols[] = array("text" => "R ".number_format($quote->VAT, 2), "width" => $colwidths[1], "style" => 1);
PrintTr($pdf, $cols);

$cols = null;
$cols[] = array("text" => "Total (incl) VAT", "width" => $colwidths[0], "style" => 2);
$cols[] = array("text" => "R ".number_format($quote->TotalPrice, 2), "width" => $colwidths[1], "style" => 1);
PrintTr($pdf, $cols);

$cols = null;
$cols[] = array("text" => "Total Payback Period", "width" => $colwidths[0], "style" => 2);
$cols[] = array("text" => $quote->TotalPaybackPeriodFormatted, "width" => $colwidths[1], "style" => 5);
PrintTr($pdf, $cols);

$pdf->ln();
$pdf->ln();
$pdf->ln();

//===Projected Savings===
$irow = 0;

$cols = null;
$cols[] = array("text" => "Projected Savings from Ellies Customised Solution for You", "width" => $fullbannerwidth, "style" => "3b");
PrintTr($pdf, $cols);

$savings = $quote->Get5YearSavings();
foreach($savings as $key=>$value) {
    $irow += 1;
    if ($irow == 1) {
	$cols = null;
	$cols[] = array("text" => "", "width" => 45, "style" => 0);
        foreach($value as $col=>$val) {
	    $cols[] = array("text" => $col, "width" => 28, "style" => 2);
        }
	PrintTr($pdf, $cols);
    }

    $cols = null;
    $cols[] = array("text" => $key, "width" => 45, "style" => 2);
    foreach($value as $col=>$val) {
        if ($irow == 1) {
	    $cols[] = array("text" => $val, "width" => 28, "style" => 1);
        }else{
	    $cols[] = array("text" => $val, "width" => 28, "style" => 5);
        }
    }
    PrintTr($pdf, $cols);
}
$cols = null;
$cols[] = array("text" => "", "width" => 103, "style" => 0);
$cols[] = array("text" => "Projected Savings After 5 Years", "width" => 57, "style" => 3);
$cols[] = array("text" => $savings["Cumulative Savings"]["Year 5"], "width" => 28, "style" => 5);
PrintTr($pdf, $cols);


$pdf->ln();
$pdf->ln();
$pdf->ln();

//===Projected Losses===
$irow = 0;

$cols = null;
$cols[] = array("text" => "Projected Losses if Ellies Customised Solution for You is Not Chosen", "width" => $fullbannerwidth, "style" => "3b");
PrintTr($pdf, $cols);

$losses = $quote->Get5YearLosses();
foreach($losses as $key=>$value) {
    $irow += 1;
    if ($irow == 1) {
	$cols = null;
	$cols[] = array("text" => "", "width" => 45, "style" => 0);
        foreach($value as $col=>$val) {
	    $cols[] = array("text" => $col, "width" => 28, "style" => 2);
        }
	PrintTr($pdf, $cols);
    }

    $cols = null;
    $cols[] = array("text" => $key, "width" => 45, "style" => 2);
    foreach($value as $col=>$val) {
        if ($irow == 1) {
	    $cols[] = array("text" => $val, "width" => 28, "style" => 1);
        }else{
	    $cols[] = array("text" => $val, "width" => 28, "style" => 4);
        }
    }
    PrintTr($pdf, $cols);
}
$cols = null;
$cols[] = array("text" => "", "width" => 103, "style" => 0);
$cols[] = array("text" => "Projected Losses After 5 Years", "width" => 57, "style" => 3);
$cols[] = array("text" => $losses["Cumulative Losses"]["Year 5"], "width" => 28, "style" => 4);
PrintTr($pdf, $cols);

$pdf->SetTextColor(0);
//$pdf->Footer();

//===Page 2===
//PrintFooter($pdf, "Page 1 of 12million", "Refer to Disclaimers");
PrintHeader($pdf, "Itemised Product Information", $quote);

//$fullbannerwidth = 190;
$pdf->SetFont('Arial','',8);
$iprod = 0;
$irow = 0;
foreach($quote->Items as $itemid) {
	$irow += 1;
    $iprod += 1;
    $quoteitem = new QuoteItem($itemid);
    $quoteitem->CalculateCostSavings();
    $cols = null;
    $cols[] = array("text" => "Product No. ".$iprod, "width" => 94, "style" => "3b");
    $cols[] = array("text" => "Usage Information", "width" => 95, "style" => 2);
    PrintTr($pdf, $cols);
    
    if ($quoteitem->RelatedTo == "Hours Per Year") {
	$cols = null;
	$cols[] = array("text" => "Technology Type", "width" => 30, "style" => 2);
	$cols[] = array("text" => "Quantity", "width" => 31, "style" => 2);
	$cols[] = array("text" => "Room", "width" => 31, "style" => 2);
	foreach ($quoteitem->GetInputValues() as $inputkey=>$inputvalue) {
	    $tkey = $inputkey;
	    $tkey = str_replace("_", " ", $tkey);
	    $cols[] = array("text" => $tkey, "width" => 31, "style" => 2);	
	}
	PrintTr($pdf, $cols);
    }else{
	$cols = null;
	$cols[] = array("text" => "Technology Type", "width" => 30, "style" => 2);
	$cols[] = array("text" => "Quantity", "width" => 31, "style" => 2);
	$cols[] = array("text" => "Room", "width" => 31, "style" => 2);
	$pass = 0;
	foreach ($quoteitem->GetInputValues() as $inputkey=>$inputvalue) {
	    $tkey = $inputkey;
	    if ($tkey == "Number_of_uses_per_day_each") {
		$tkey = "Number of uses\nper day each";
	    }
	    if ($tkey == "Old_Litre_measurement") {
		$tkey = "Old Litre\nmeasurement";
	    }
	    if ($tkey == "Average_Shower_Time") {
		$tkey = "Average Shower\nTime";
	    }
	    $cols[] = array("text" => $tkey, "width" => 31, "style" => 2);	
	}
	PrintTr($pdf, $cols, 6);	
    }
    $pdf->SetFont('Arial','B',8);
    $cols = null;
    $cols[] = array("text" => $quoteitem->TechType, "width" => 30, "style" => 1);
    $cols[] = array("text" => $quoteitem->Qty, "width" => 31, "style" => 1);
    $cols[] = array("text" => $quoteitem->Room, "width" => 31, "style" => 1);
    foreach ($quoteitem->GetInputValues() as $inputkey=>$inputvalue) {
	$cols[] = array("text" => $inputvalue, "width" => 31, "style" => 1);
    }
    PrintTr($pdf, $cols);

    $cols = null;
    $cols[] = array("text" => "", "width" => $colw1, "style" => 0);
    PrintTr($pdf, $cols, 1);

    $cols = null;
    $cols[] = array("text" => "Item", "width" => 126, "style" => 2);
    $cols[] = array("text" => "kWh Per Month", "width" => 31, "style" => 2);
    $cols[] = array("text" => "Monthly Cost (Year 1)", "width" => 31, "style" => 2);
    PrintTr($pdf, $cols, 6);

    $cols = null;
    $cols[] = array("text" => "Existing", "width" => 30, "style" => 4);
    $cols[] = array("text" => $quoteitem->OldProductName, "width" => 95, "style" => 4);
    $cols[] = array("text" => number_format($quoteitem->KWhOld, 0)." kWh", "width" => 31, "style" => 4);
    $cols[] = array("text" => "R ".number_format($quoteitem->MonthlyCostOld, 2), "width" => 31, "style" => 4);
    PrintTr($pdf, $cols, 6);

    $cols = null;
    $cols[] = array("text" => "Replacement", "width" => 30, "style" => 5);
    $cols[] = array("text" => $quoteitem->ItemDescription, "width" => 95, "style" => 5);
    $cols[] = array("text" => number_format($quoteitem->KWhNew, 0)." kWh", "width" => 31, "style" => 5);
    $cols[] = array("text" => "R ".number_format($quoteitem->MonthlyCostNew, 2), "width" => 31, "style" => 5);
    PrintTr($pdf, $cols, 6);

    $cols = null;
    $cols[] = array("text" => "", "width" => $colw1, "style" => 0);
    //PrintTr($pdf, $cols, 1);

    $cols = null;
    $cols[] = array("text" => "Savings", "width" => 94, "style" => "2b");
    $cols[] = array("text" => "Payback Information", "width" => 95, "style" => "2b");
    PrintTr($pdf, $cols, 6);

    $cols = null;
    $cols[] = array("text" => "Saved kWh Month", "width" => 58, "style" => 6);
    $cols[] = array("text" => number_format($quoteitem->KWhSaved, 0)." kWh (".$quoteitem->KWhSavedPerc."%)", "width" => 35, "style" => 6);
    $cols[] = array("text" => "Replacement Price", "width" => 59, "style" => 6);
    $cols[] = array("text" => "R ".number_format($quoteitem->ItemTotal, 2), "width" => 35, "style" => 6);
    PrintTr($pdf, $cols, 6);

    $cols = null;
    $cols[] = array("text" => "Amount Saved Month (Y 1)", "width" => 58, "style" => 6);
    $cols[] = array("text" => "R ".number_format($quoteitem->MonthlyCostSaving, 2)." (".$quoteitem->KWhSavedPerc."%)", "width" => 35, "style" => 6);
    $cols[] = array("text" => "Payback Period", "width" => 59, "style" => 6);
    $cols[] = array("text" => $quoteitem->PaybackPeriodFormatted, "width" => 35, "style" => 6);
    PrintTr($pdf, $cols, 6);

   
    if ($irow % 3 == 0 && $irow < $productcount) {
	PrintHeader($pdf, "Itemised Product Information", $quote);
    }else{
	if ($irow < $productcount) {
		PrintDashedLine($pdf);		
	}
    }
    /*
    $cols = null;
    $cols[] = array("text" => "Replacement", "width" => 30, "style" => 5);
    $cols[] = array("text" => $quoteitem->ItemDescription, "width" => 95, "style" => 5);
    $cols[] = array("text" => number_format($quoteitem->KWhNew, 0)." kWh", "width" => 31, "style" => 5);
    $cols[] = array("text" => "R ".number_format($quoteitem->MonthlyCostNew, 2), "width" => 31, "style" => 5);
    PrintTr($pdf, $cols, 6);
    */
}
//$pdf->Cell($w, $h, $txt, $border, $ln, $align, $fill, $link);
//$pdf->MultiCell($w, $h, $txt, $border, $align, $fill);
//$pdf->MultiCell(58,5,"a line  \n Nr. ZW04-270602 \n Format A1",1,'C');



$pdf->SetTextColor(0);
//$pdf->Footer();

//===Page 3 or 4 Disclaimer===
/*
$pdf->AddPage();
$pdf->SetTextColor(0);
$pdf->Image("images/logo.png", 10, 5);
$pdf->SetFillColor(255,255,255);
$pdf->Rect(5, 22, 50, 15, "F");
$pdf->SetFont('Arial','B',18);
$pdf->Text(10, 30, "Disclaimer");
*/
PrintHeader($pdf, "Disclaimers", $quote);
$pdf->IsDisclaimer = true;

//$fullbannerwidth = 190;
$pdf->SetFont('Arial','',10);

$disclaimertext = "In providing the customer with energy savings advice, use shall be made of the Ellies Customised Energy Audit to determine the indicative estimated savings that the customer may enjoy.  However, the customer should be alerted to and have specific regard to the following important assumptions, limitations and qualifications which relate to any advice which may be provided to the customer –

1.	The estimates generated from the Ellies Customised Energy Audit are indicative estimates, are based solely on information provided by the customer and should not be considered to be warranted as accurate. This type of information includes information on existing technologies and usage profiles (which includes existing technology durations used, daily frequency of usage and litre measurements for water products). The onus to ensure that the correctness of the information entered into the Ellies Customised Energy Audit rests entirely with the customer.
2.	Manufacturer's stated specifications in respect of proposed replacement products may vary.
3.	The quality experience of the replacement product suggested may not be exactly like-for-like. For example, the lumen output may be reduced and other factors such as beam angle, light temperature/colour and perceived light quality may be different.
4.	Lamp lifetime of the replacement product, as stated on the packaging, will be affected by variations in mains voltage and frequency.
5.	Excessive repetitive use (e.g. frequent repetitive on/off cycles) of the replacement product will adversely affect the performance and lifespan of the product.
6.	Certain lighting products are not suitable for use in circuits with dimmers.
7.	Ellies is not responsible for product failures/reduced performance on replacement products caused by any external factors including faulty wiring, poor installation, faulty ancillary equipment or incorrect application of product. 
8.	All electrical installations and modifications must be carried out by a qualified electrician - in accordance with South African regulations.
9.	By using all replacement technologies you agree to all exclusions and limitations of liability stated herein and accept them as reasonable.
10.	This disclaimer notice shall be interpreted and governed by South African law, and any disputes in relation to it are subject to the jurisdiction of the South African Courts.
11.	The products provided by Ellies are on an “As Is” basis and disclaim any warranty of any kind imputed by the laws of any jurisdiction, whether express or implied, as to any matter whatsoever relating to the service and sale of any of Ellies products including, without limitation the implied warranties of merchantability, fitness for a   particular purpose and non-infringement.
12.	Use of the product is at the customer’s own risk. The data and information provided is not advice, professional or otherwise, and should not be relied upon as such.


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
$pdf->Write(5, $disclaimertext);


//===Output and Email PDF / Preview PDF===
if ($previewmode) {
    $pdf->Output();
}else{
    require_once("inc/header.php");
    $quoteid = $_GET["id"];
    $quote = new Quote($quoteid);
    if ($quoteid == "0") {
	$quote->CustomerID = SESSION("customerid");
	$quote->Save();
    }

    $filename = "emailedquotes/Quotation - ".$quote->QuoteReferenceNo.".pdf";
    if (file_exists($filename)) {
	unlink($filename);
    }
    $pdf->Output($filename, "F");
    
    $status = SendEmail($customer->Email, $customer->Name." ".$customer->Surname, "Ellies Energy Saving - Quotation: ".$quote->QuoteReferenceNo, "Please find attached. Ellies Energy Saving - Quotation: ".$quote->QuoteReferenceNo, $attachments[] = $filename);
    $status = true;
    if (!empty($_GET["complete"])) {
	if ($_GET["complete"] == "1") {
	    PrintNavBar($booktitle, true, "newquote.php?step=newclick");
	}else{
	    PrintNavBar($booktitle, true, "editquote.php?id=".$quoteid);
	}
    }else{
        PrintNavBar($booktitle, true, "editquote.php?id=".$quoteid);
    }
	
    if ($status === true) {
	print "<p>Thank you. Your quote has been emailed to the customer.</p>";
    }else{
	print "<p>".$status."</p>";	
    }
    //mail($customer->Email, "Ellies Energy Saving - Quotation", "");
    //===Put in alternate mailer here with attachment support===
    require_once("inc/footer.php");
}
?> 