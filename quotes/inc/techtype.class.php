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
class TechType {
    public $TechTypeID = 0;
    public $Type = "";
    public $Replacement = "";
    public $Units = "";
    public $Input = "";
    public $Constant = "";
    public $FormulaType = 0;
    public $FormulaUsageHoursPerYear = "";
    public $FormulaCostSavings = "";
    public $FormulaOldHours = "";
    public $FormulaNewHours = "";
    public $OldTechMonthlyCost = 0;
    public $NewTechMonthlyCost = 0;
    public $ReplacementCost = 0;
    public $ReplacementTime = 0;
    
    public function __construct ($techtypeid = 0) {
        $this->BuildTables();
        $this->TechTypeID = $techtypeid;

        $sql = "SELECT * FROM `tech_types` WHERE `id` = '".mysqli_real_escape_string($GLOBALS["link"],$this->TechTypeID)."'";
        $sqlres = mysqli_query($GLOBALS["link"],$sql);
        while($row = mysqli_fetch_assoc($sqlres)) {
            $this->Type = $row["tech_type"];
            $this->FormulaType = $row["formula_type"];
            $this->ReplacementCost = $row["replacement_cost"];
            $this->ReplacementTime = $row["replacement_time"];
           
            //===Phase 1 static formulas===
            if ($this->FormulaType == 1) {
                //$this->Input = "Hourly,Daily,Weekly Hours";
                $this->Input = "Hours Per Day,Days Per Week,Weeks Per Year";
            }elseif($this->FormulaType == 2) {
                $this->Input = "Old Litre measurement,Number of uses per day each,Average Shower Time (in minutes)";
            }else{
                //===Unknown - Future Types===
                $this->Input = "";
            }
        }
    }
    
    public static function ListTechTypes() {
        $types = NULL;
        $sql = "SELECT `id`, `tech_type` FROM `tech_types` WHERE `active` = 1 ORDER BY `tech_type`";
        $sqlres = mysqli_query($GLOBALS["link"],$sql);
        while($row = mysqli_fetch_assoc($sqlres)) {
            $types[] = $row;
        }
        return $types;
    }
    
    private function BuildTables() {
        $sql = "CREATE TABLE `tech_types` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `tech_type` varchar(250) NOT NULL,
        `active` tinyint(1) NOT NULL DEFAULT '1',
        `formula_type` int(11) NOT NULL DEFAULT '0',
        `replacement_cost` float NOT NULL DEFAULT '0',
        `replacement_time` int(11) NOT NULL DEFAULT '0',
        UNIQUE KEY `id` (`id`),
        KEY `tech_type` (`tech_type`),
        KEY `active` (`active`),
        KEY `formula_type` (`formula_type`),
        KEY `replacement_cost` (`replacement_cost`),
        KEY `replacement_time` (`replacement_time`)
      ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1";
        mysqli_query($GLOBALS["link"],$sql);
    }
}
?>