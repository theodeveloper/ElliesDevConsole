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
class TechItemBak {
    public $TechID = 0;
    public $Type = "";
    public $Replacement = "";
    public $Units = "";
    public $Input = "";
    public $Constant = "";
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
            $this->Type = $row["type"];
            $this->Replacement = $row["replacement"];
            $this->Units = $row["units"];
            $this->Input = $row["input"];
            $this->Constant = $row["constant"];
            //$this->Formula = $row["formula"]; // To implement in next phase
            
            //===Phase 1 static formulas===
            if ($this->TechID < 6 && $this->TechID > 0) {
                //$evalstr = "\$this->".$permission." = false;";
                //eval($evalstr);

                /*
                ===Lighting Types===
                Units: kWh
                Input: Hourly,Daily,Weekly Hours
                Constant: Old Tech kWh,New Tech kWh,New Tech Lifespan,Unit Cost
                Constant: Old_Tech_kWh = 0.1,New_Tech_kWh = 0.02,New_Tech_Lifespan = 8000,Unit_Cost = 42.99
                */
                $this->FormulaUsageHoursPerYear = "\$Usage_Hours_Per_Year = (\$Hourly * \$Daily) * \$Weekly_Hours";
                $this->FormulaOldHours = "\$Old_kWh_Year = (0.1 * \$Qty) * \$Usage_Hours_Per_Year";
                $this->FormulaNewHours = "\$New_kWh_Year = (0.02 * \$Qty) * \$Usage_Hours_Per_Year";
                $this->OldTechMonthlyCost = "(1.20 * \$Old_kWh_Year) / 12";
                $this->NewTechMonthlyCost = "(1.20 * \$New_kWh_Year) / 12";
            }elseif($this->TechID > 5) {
                /*
                ===Flow Regulator Types===
                Units: L / min
                Input: Usage Litre Measurement,Usage per day
                Constant: New Tech L/Min,Unit Cost
                */
                $this->FormulaUsageHoursPerYear = "N/A";
                $this->FormulaOldHours = "N/A";
                $this->FormulaNewHours = "N/A";
                $this->OldTechMonthlyCost = "N/A";
                $this->NewTechMonthlyCost = "N/A";
            }else{
                //===Unknown - Future Types===
                $this->FormulaUsageHoursPerYear = "N/A";
                $this->FormulaOldHours = "N/A";
                $this->FormulaNewHours = "N/A";
                $this->OldTechMonthlyCost = "N/A";
                $this->NewTechMonthlyCost = "N/A";
            }
        }
    }
    
    private function BuildTables() {
        $sql = "CREATE TABLE `tech_items` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `type` varchar(250) NOT NULL,
            `replacement` varchar(250) DEFAULT NULL,
            `units` varchar(250) NOT NULL,
            `input` varchar(250) NOT NULL,
            `constant` varchar(250) NOT NULL,
            `active` tinyint(1) NOT NULL DEFAULT '1',
            `formula` varchar(500) DEFAULT NULL,
            UNIQUE KEY `id` (`id`),
            KEY `type` (`type`),
            KEY `replacement` (`replacement`),
            KEY `units` (`units`),
            KEY `input` (`input`),
            KEY `constant` (`constant`),
            KEY `active` (`active`),
            KEY `formula` (`formula`)
          ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;";
        mysqli_query($GLOBALS["link"],$sql);
    }
}
?>