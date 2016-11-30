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
class TechItem {
    public $TechID = 0;
    /*
    public $Type = "";
    public $Replacement = "";
    public $Units = "";
    public $Input = "";
    public $Constant = "";
    */

    public $TechType = "";
    public $TechTypeID = 0;
    public $Option = "";
    public $OldProduct = "";
    public $NewProduct = "";
    public $NewProductCode = "";
    public $OldTechType = "";
    public $NewTechType = "";
    public $RelatedTo = "";
    public $UsesKW = false;
    public $OldKWh = 0;
    public $NewKWh = 0;
    public $OldLitres = 0;
    public $NewLitres = 0;
    public $Price = 0;
    public $HasStock = false;
    public $Active = false;
    public $Info = "";
    
    public $Input = "";
    public $FormulaUsageHoursPerYear = "";
    public $FormulaCostSavings = "";
    public $FormulaOldHours = "";
    public $FormulaNewHours = "";
    public $OldTechMonthlyCost = 0;
    public $NewTechMonthlyCost = 0;
    
    public function __construct ($techid = 0) {
        $this->BuildTables();
        $this->TechID = $techid;

        $sql = "SELECT * FROM `tech_items` WHERE `id` = '".mysqli_real_escape_string($GLOBALS["link"],$this->TechID)."'";
        $sqlres = mysqli_query($GLOBALS["link"],$sql);
        while($row = mysqli_fetch_assoc($sqlres)) {
            $this->TechType = $row["tech_type"];
            $this->TechTypeID = $row["tech_type_id"];
            $this->Option = $row["option"];
            $this->NewProductCode = $row["new_product_code"];
            $this->OldTechType = $row["old_tech_type"];
            $this->NewTechType = $row["new_tech_type"];
            $this->RelatedTo = $row["related_to"];
            $this->UsesKW = $row["uses_kw"];
            $this->OldKWh = $row["old_kwh"];
            $this->NewKWh = $row["new_kwh"];
            $this->OldLitres = $row["old_litres"];
            $this->NewLitres = $row["new_litres"];
            $this->Price = $row["price"];
            $this->HasStock = $row["has_stock"];
            $this->Active = $row["active"];
            $this->Info = $row["item_info"];
            $this->OldProduct = $row["old_product"];
            $this->NewProduct = $row["new_product"];
        }
    }
    
    public function Save() {
        if ($this->TechID == 0) {
            $sql = "INSERT INTO `tech_items` (`date_created`) VALUES (NOW())";
            mysqli_query($GLOBALS["link"],$sql);
            $this->TechID = mysql_insert_id();
        }
        
        if ($this->TechID > 0) {
            $sql = "UPDATE `tech_items` SET";
            $sql.= " `tech_type` = '".mysqli_real_escape_string($GLOBALS["link"],$this->TechType)."'";
            $sql.= ", `tech_type_id` = '".mysqli_real_escape_string($GLOBALS["link"],$this->TechTypeID)."'";
            $sql.= ", `option` = '".mysqli_real_escape_string($GLOBALS["link"],$this->Option)."'";
            $sql.= ", `old_product` = '".mysqli_real_escape_string($GLOBALS["link"],$this->OldProduct)."'";
            $sql.= ", `new_product` = '".mysqli_real_escape_string($GLOBALS["link"],$this->NewProduct)."'";
            $sql.= ", `new_product_code` = '".mysqli_real_escape_string($GLOBALS["link"],$this->NewProductCode)."'";
            $sql.= ", `old_tech_type` = '".mysqli_real_escape_string($GLOBALS["link"],$this->OldTechType)."'";
            $sql.= ", `new_tech_type` = '".mysqli_real_escape_string($GLOBALS["link"],$this->NewTechType)."'";
            $sql.= ", `related_to` = '".mysqli_real_escape_string($GLOBALS["link"],$this->RelatedTo)."'";
            $sql.= ", `uses_kw` = '".mysqli_real_escape_string($GLOBALS["link"],$this->UsesKW)."'";
            $sql.= ", `old_kwh` = '".mysqli_real_escape_string($GLOBALS["link"],$this->OldKWh)."'";
            $sql.= ", `new_kwh` = '".mysqli_real_escape_string($GLOBALS["link"],$this->NewKWh)."'";
            $sql.= ", `old_litres` = '".mysqli_real_escape_string($GLOBALS["link"],$this->OldLitres)."'";
            $sql.= ", `new_litres` = '".mysqli_real_escape_string($GLOBALS["link"],$this->NewLitres)."'";
            $sql.= ", `price` = '".mysqli_real_escape_string($GLOBALS["link"],$this->Price)."'";
            $sql.= ", `has_stock` = '".mysqli_real_escape_string($GLOBALS["link"],$this->HasStock)."'";
            $sql.= ", `active` = '".mysqli_real_escape_string($GLOBALS["link"],$this->Active)."'";
            $sql.= ", `item_info` = '".mysqli_real_escape_string($GLOBALS["link"],$this->Info)."'";
            $sql.= " WHERE `id` = '".mysqli_real_escape_string($GLOBALS["link"],$this->TechID)."'";
            //print $sql;
            //exit();
            mysqli_query($GLOBALS["link"],$sql);
        }
    }
    
    public function PrintItemInfo($medialocation = "") {
        $tempinfo = $this->Info;
        if (!empty($tempinfo)) {
            if (!empty($medialocation)) {
                $tempinfo = str_replace("quotes/media/", $medialocation, $tempinfo);                    
            }
            print $tempinfo;                
        }else{
            print "No information available for this product yet.";
        }
    }
    
    public function Delete() {
        //$sql = "DELETE FROM `tech_items` WHERE `id` = '".mysqli_real_escape_string($GLOBALS["link"],$this->TechID)."'";
        $sql = "UPDATE `tech_items` SET `deleted` = 1 WHERE `id` = '".mysqli_real_escape_string($GLOBALS["link"],$this->TechID)."'";
        mysqli_query($GLOBALS["link"],$sql);
    }
    
    private function BuildTables() {
        $sql = "CREATE TABLE `tech_items` (
            `id` int(11) NOT NULL AUTO_INCREMENT,        
            `tech_type_id` int(11) NOT NULL DEFAULT '0',
            `option` varchar(250) NOT NULL,
            `new_product_code` varchar(250) NOT NULL,
            `old_tech_type` varchar(250) NOT NULL,
            `new_tech_type` varchar(250) NOT NULL,
            `related_to` varchar(250) NOT NULL,
            `uses_kw` tinyint(1) NOT NULL DEFAULT '1',
            `old_kwh` float NOT NULL DEFAULT '0',
            `new_kwh` float NOT NULL DEFAULT '0',
            `old_litres` float NOT NULL DEFAULT '0',
            `new_litres` float NOT NULL DEFAULT '0',
            `price` float NOT NULL DEFAULT '0',
            `has_stock` tinyint(1) NOT NULL DEFAULT '1',
            `date_created` datetime NOT NULL,
            `active` tinyint(1) NOT NULL DEFAULT '1',
            `item_info` text DEFULT NULL,
            UNIQUE KEY `id` (`id`),
            KEY `tech_type_id` (`tech_type_id`),
            KEY `option` (`option`),
            KEY `new_product_code` (`new_product_code`),
            KEY `old_tech_type` (`old_tech_type`),
            KEY `new_tech_type` (`new_tech_type`),
            KEY `related_to` (`related_to`),
            KEY `uses_kw` (`uses_kw`),
            KEY `old_kwh` (`old_kwh`),
            KEY `new_kwh` (`new_kwh`),
            KEY `old_litres` (`old_litres`),
            KEY `new_litres` (`new_litres`),
            KEY `price` (`price`),
            KEY `has_stock` (`has_stock`),
            KEY `date_created` (`date_created`),
            KEY `active` (`active`)
          ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;";
        mysqli_query($GLOBALS["link"],$sql);
    }
    

}
?>