<?php
// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {

    //Page header
    public function Header() {
        //Border header line
        $fontname =  'arial';
        $this->SetFont($fontname, 'B', 12);
        $this->SetLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(140, 197, 62)));
        $this->SetFillColor(140, 197, 62);
        $this->MultiCell(180, 1,'', 1, 'L', 1, 0,'', '5');

        //Logo
        //$target_file = isset($_REQUEST["image_header"])?$_REQUEST["image_header"]:null;
       // $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
       // if ($target_file) $this->Image($target_file, 15, 7, 45, '', $imageFileType, '', 'T', false, 300, '', false, false, 0, false, false, false);

        // Logo
       /* $image_file ='../images/ellies2.jpg';
        $this->Image($image_file, 80, 20, 40, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);*/

        $this->SetTextColor(140, 197, 62);

        //Area
         $area = 'JOHANNESBURG';
         $this->MultiCell(180, 1,  $area, '', 'L', 0, 0,'16', '11');
         $this->SetFont($fontname, 'B', 8);  

         //Registration
        $vat = 'VAT No: 4240171472';
        $reg = 'Reg No: 2007/011513/07';     
        $html = ' <p>'.$vat .'<br/> '. $reg .'</p>';
        $this->writeHTMLCell(178, 10,164,10,$html, 0, 0, '','', 'L', true);

        //Street Address
        $streetAdd = 'Cnr. Eloff and Wrighboag Street ,Village Deep,JOHANNESBURG,2001';
        $html = ' <p>Cnr. Eloff and Wrighboag Street<br/> Village Deep<br/>JOHANNESBURG<br/>2001</p>';
        $this->writeHTMLCell(178, 20,16,16,$html, 0, 0, '','', 'L', true);

        //Postal Address
        $postalAdd = 'PO Box 57076,SPRINGFIELD,2137';
        $html = ' <p>PO Box 57076<br/>SPRINGFIELD<br/>2137</p>';
        $this->writeHTMLCell(178, 20,175,11,$html, 0, 0, '','', 'L', true);

        //Contact details
        $tel = 'Tel: 011 493 0344';
        $fax = 'Fax: 011 493 3027';
        $html = ' <p>'. $tel . '<br/>' . $fax. '</p>';
        $this->writeHTMLCell(178, 20,16,25,$html, 0, 0, '','', 'L', true);

        //Email
        $email ='www.ellies.co.za';
        $this->MultiCell(180, 1,  $email, '', 'L', 0, 0,'173', '35');

        //Border header line
        $this->SetFont($fontname, 'B', 12);
        $this->SetLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(140, 197, 62)));
        $this->MultiCell(180, 1,  '', 1, 'L', 1, 0,'', '43');
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $fontname =  'arial';
        $this->SetY(-15);
        // Set font
         $this->SetFont($fontname, 'I', 8);
         // Title
        $title = 'Ellies Renewable Energy';
        $form = 'Lighting Solutions';
        // Page number
        $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
        $this->SetY(-20);
        $this->Cell(0, 5,   $title  , 0, false, 'L', 0, '', 0, false, 'T', 'M');
        $this->SetY(-15);
        $this->Cell(0, 5,   $form, 0, false, 'L', 0, '', 0, false, 'T', 'M');
    }
}
