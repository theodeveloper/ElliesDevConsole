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
class Quote {
    public $Exists = false;
    public $QuoteID = 0;
    public $CustomerID = 0;
    public $Approved = false;
    public $DateCreated = NULL;
    public $LastUpdated = NULL;
    public $QuoteReferenceNo = "";
    public $SessionID = "";
    public $Complete = false;
    public $HasItems = false;
    
    public $KWhOld = 0;
    public $KWhNew = 0;
    public $KWhSaved = 0;
    public $KWhSavedPerc = 0;
    public $LitresOld = 0;
    public $LitresNew = 0;
    public $LitresSaved = 0;
    public $MonthlyCostOld = 0;
    public $MonthlyCostNew = 0;
    public $MonthlyCostSaving = 0;
    
    public $NumberOfItems = 0;
    public $MaintenanceTerm = 60;
    public $PricePerKWh = 1.2;
    public $PricePerKiloLitre = 1;
    public $ElectricityEscalationRatePercentage = 8;
    public $WaterEscalationRatePercentage = 12;
    public $KWhPrice = 1.20;
    public $TotalPaybackPeriod = 0;
    public $TotalPaybackPeriodFormatted = "";
    public $Property = "";
    public $CreatedBy = 0;
    public $UpdatedBy = 0;
    public $CreatedByUserName = "N/A";
    
    public $TotalPrice = 0;
    public $TotalPriceExVat = 0;
    public $VAT = 0;
    
    public $Items = NULL;
    
	public $yearsInit = array(1,2,3,4,5);
	
    public function __construct ($QuoteID = 0) {
        $this->BuildTables();
        if ($QuoteID > 0) {
            $sql = "SELECT * FROM `quotes` WHERE `id` = '".mysqli_real_escape_string($GLOBALS["link"],$QuoteID)."'";                
            $sqlres = mysqli_query($GLOBALS["link"],$sql);
            while($row = mysqli_fetch_assoc($sqlres)) {
                $this->Exists = true;
                $this->QuoteID = $row["id"];
                if (!empty($row["ref"])) {
                    $this->QuoteReferenceNo = $row["ref"];
                }else{
                    $this->QuoteReferenceNo = "INQ".str_pad($this->QuoteID, 6, "0", STR_PAD_LEFT);
                }
                $this->CustomerID = $row["customer_id"];
                $this->Approved = $row["approved"];
                $this->DateCreated = $row["date_created"];
                $this->LastUpdated = $row["last_updated"];
                $this->Complete = $row["complete"];
                $this->SessionID = session_id();
                $this->Property = $row["property"];
                $this->CreatedBy = $row["created_by"];
                $this->UpdatedBy = $row["updated_by"];
                if ($row["kwh_price"] > 0) {
                    $this->KWhPrice = $row["kwh_price"];
                    $this->PricePerKWh = $row["kwh_price"];
                }
                if ($row["escalation_rate"] > 0) {
                    $this->ElectricityEscalationRatePercentage = $row["escalation_rate"];
                }
                
                $sql = "SELECT `username` FROM `system_users` WHERE `id` = '".mysqli_real_escape_string($GLOBALS["link"],$this->CreatedBy)."' LIMIT 0, 1";
                $sqlres = mysqli_query($GLOBALS["link"],$sql);
                while($row = mysqli_fetch_assoc($sqlres)) {
                    $this->CreatedByUserName = $row["username"];
                }
            }
            $sql = "SELECT `id`, `qty` FROM `quote_items` WHERE `quote_id` = '".mysqli_real_escape_string($GLOBALS["link"],$QuoteID)."' ORDER BY `id`";                
            $sqlres = mysqli_query($GLOBALS["link"],$sql);
            while($row = mysqli_fetch_assoc($sqlres)) {
                $this->Items[] = $row["id"];
                $this->NumberOfItems += $row["qty"];
                $this->HasItems = true;
            }
        }
    }
    
    public function Save() {
        if (!$this->Exists) {
            $sql = "INSERT INTO `quotes` (`date_created`, `customer_id`, `created_by`) VALUES (NOW(), '".mysqli_real_escape_string($GLOBALS["link"],$this->CustomerID)."', '".mysqli_real_escape_string($GLOBALS["link"],$this->CreatedBy)."')";
            mysqli_query($GLOBALS["link"],$sql);
            $this->QuoteID = mysql_insert_id();
            $this->Exists = true;
        }
        if ($this->Exists) {
            $sql = "UPDATE `quotes` SET";
            $sql.= " `last_updated` = NOW()";
            $sql.= ", `approved` = '".mysqli_real_escape_string($GLOBALS["link"],$this->Approved)."'";
            $sql.= ", `complete` = '".mysqli_real_escape_string($GLOBALS["link"],$this->Complete)."'";
            $sql.= ", `escalation_rate` = '".mysqli_real_escape_string($GLOBALS["link"],$this->ElectricityEscalationRatePercentage)."'";
            $sql.= ", `kwh_price` = '".mysqli_real_escape_string($GLOBALS["link"],$this->KWhPrice)."'";
            $sql.= ", `property` = '".mysqli_real_escape_string($GLOBALS["link"],$this->Property)."'";
            $sql.= ", `ref` = '".mysqli_real_escape_string($GLOBALS["link"],$this->QuoteReferenceNo)."'";
            $sql.= ", `created_by` = '".mysqli_real_escape_string($GLOBALS["link"],$this->CreatedBy)."'";
            $sql.= ", `updated_by` = '".mysqli_real_escape_string($GLOBALS["link"],$this->UpdatedBy)."'";
            $sql.= " WHERE `id` = '".mysqli_real_escape_string($GLOBALS["link"],$this->QuoteID)."'";
            //print $sql;
            //exit();
            mysqli_query($GLOBALS["link"],$sql);
            
            $sql = "UPDATE `quote_items` SET `quote_id` = '".mysqli_real_escape_string($GLOBALS["link"],$this->QuoteID)."' WHERE `quote_id` = 0 AND `session_id` = '".mysqli_real_escape_string($GLOBALS["link"],$this->SessionID)."'";
            mysqli_query($GLOBALS["link"],$sql);
        }
    }
    
    public function Delete() {
        $sql = "DELETE FROM `quote_items_input` WHERE `quote_item_id` IN (SELECT `id` FROM `quote_items` WHERE `quote_id` = '".mysqli_real_escape_string($GLOBALS["link"],$this->QuoteID)."')";
        mysqli_query($GLOBALS["link"],$sql);

        $sql = "DELETE FROM `quote_items` WHERE `quote_id` = '".mysqli_real_escape_string($GLOBALS["link"],$this->QuoteID)."'";
        mysqli_query($GLOBALS["link"],$sql);

        $sql = "DELETE FROM `quotes` WHERE `id` = '".mysqli_real_escape_string($GLOBALS["link"],$this->QuoteID)."'";
        mysqli_query($GLOBALS["link"],$sql);
    }
    
    public function CalculateCostSavingTotals() {
        $sql = "SELECT `id` FROM `quote_items` WHERE `quote_id` = '".mysqli_real_escape_string($GLOBALS["link"],$this->QuoteID)."'";                
        $sqlres = mysqli_query($GLOBALS["link"],$sql);
        while($row = mysqli_fetch_assoc($sqlres)) {
            $quoteitem = new QuoteItem($row["id"]);
            $techitem = new TechItem($quoteitem->TechID);
            $quoteitem->CalculateCostSavings();
            $this->KWhOld += $quoteitem->KWhOld;
            $this->KWhNew += $quoteitem->KWhNew;
            //$this->KWhSaved += $quoteitem->KWhSaved;
            
            $this->LitresOld += $quoteitem->LitresOld;
            $this->LitresNew += $quoteitem->LitresNew;
            //$this->LitresSaved += $quoteitem->LitresSaved;
            
            $this->MonthlyCostOld += $quoteitem->MonthlyCostOld;
            $this->MonthlyCostNew += $quoteitem->MonthlyCostNew;
            //$this->MonthlyCostSaving += $quoteitem->MonthlyCostSaving;
            $this->TotalPrice += $quoteitem->ItemTotal;
            $this->TotalPaybackPeriod += $quoteitem->PaybackPeriod;
        }
        $this->TotalPriceExVat = $this->TotalPrice / 1.14;
        $this->VAT = $this->TotalPrice - $this->TotalPriceExVat;
        $this->KWhSaved = $this->KWhOld - $this->KWhNew;
        $this->LitresSaved = $this->LitresOld - $this->LitresNew;
        $this->MonthlyCostSaving = $this->MonthlyCostOld - $this->MonthlyCostNew;
        //$this->TotalPaybackPeriod = $this->CalculatePayback($this->KWhPrice, $this->ElectricityEscalationRatePercentage, $this->TotalPrice, $this->KWhSaved);
        $this->TotalPaybackPeriod = CalculatePayback($this->KWhPrice, $this->ElectricityEscalationRatePercentage, $this->TotalPrice, $this->KWhSaved);
        $this->TotalPaybackPeriodFormatted = number_format($this->TotalPaybackPeriod, 2)." Months";
        $this->KWhSavedPerc = number_format(($this->KWhSaved/$this->KWhOld) * 100, 0);
    }
    
    public function PrintCostSummary() {
        //print "techid: ".$this->TechID."<br>";
        $this->CalculateCostSavingTotals();

        print "<table width='100%' cellpadding='3'><tr><td width='50%'>";
        print "<b>Old kWh/Month:</b> ".number_format($this->KWhOld, 2);
        print "<br><b>New kWh/Month:</b> ".number_format($this->KWhNew, 2);
        print "<br><b>Saved kWh/Month:</b> ".number_format($this->KWhSaved, 2);
        
        /*
        print "</td><td width='33%'>";

        print "<b>Old Litres/Year:</b> ".$this->LitresOld;
        print "<br><b>New Litres/Year:</b> ".$this->LitresNew;
        print "<br><b>Saved Litres/Year:</b> ".$this->LitresSaved;
        */
        
        print "</td><td>";

        print "<b>Old Monthly Cost:</b> R ".number_format($this->MonthlyCostOld, 2);
        print "<br><b>New Monthly Cost:</b> R ".number_format($this->MonthlyCostNew, 2);
        print "<br><b>Monthly Saving:</b> R ".number_format($this->MonthlyCostSaving, 2);
        print "</td></tr></table>";
    }
    
	private function getYears()
	{
		$Total_payback_savings = number_format($this->TotalPaybackPeriod, 2);

		$count = 0;
		$iyear = 1;
		$years= array();
		$years[1] = 1;
		$years[5] = 5* (ceil($Total_payback_savings/60));
		
		if($years[5] != 0)
		{
			if($years[5] %2 == 0) //if fifth year is even
			{
				$years[3] = $years[5]/2;
				$years[2] = floor($years[3] - ($years[5]/4));
				$years[4] = ceil($years[3] + ($years[5]/4));
			}
			
			else //if fifth year is odd
			{
				$years[3] = ($years[5]+1)/2;
				$years[2] = floor($years[3] - ($years[5]-$years[3])/2);
				$years[4] = ceil($years[3] + ($years[5] - $years[3])/2);
			}
			ksort($years);
		}
		else
		{
			$years = $this->yearsInit;	
		}
			
		return $years;
	}
	
	
    public function Get5YearSavings() {
		
    $Electricity_Price_Escalation_Rate = ($this->ElectricityEscalationRatePercentage/100);
    $Water_Price_Escalation_Rate = ($this->WaterEscalationRatePercentage/100);
	$Total_Saved_KWh_All_Annual = $this->KWhSaved * 12;
	//$Total_payback_savings = number_format($this->TotalPaybackPeriod, 2);
	
	$years = $this->getYears();
	
	foreach ($years as $yearNum)
	{        
	    //===Price Per kWh===
		$price_per_kwh = $this->PricePerKWh * pow((1 + $Electricity_Price_Escalation_Rate), ($yearNum - 1));
        $fiveyearsavings["Price per kWh"][$yearNum] = "R ".number_format($price_per_kwh, 2);
	
	   //===Cumulative Savings===
	    $cumulative_savings = ($this->PricePerKWh * $Total_Saved_KWh_All_Annual * ((pow(1+$Electricity_Price_Escalation_Rate,$yearNum)-1)/				 			 				 		$Electricity_Price_Escalation_Rate)) - $this->TotalPrice;
        $fiveyearsavings["Cumulative Savings"][$yearNum] = "R ".number_format($cumulative_savings, 2);
            
		 //===Annual Savings===
		if ($yearNum == 1) 
		{
			$fiveyearsavings["Annual Savings"][$yearNum] = "R ".number_format(($Total_Saved_KWh_All_Annual * $price_per_kwh) - $this->TotalPrice,2);
		}
		else
		{
			$fiveyearsavings["Annual Savings"][$yearNum] =  "R ".number_format(($Total_Saved_KWh_All_Annual * $price_per_kwh),2);
		}
		
		
		//===Monthly Savings===

		if ($yearNum == 1) 
		{
			$fiveyearsavings["Monthly Savings"][$yearNum] = "R ".number_format(($Total_Saved_KWh_All_Annual/12 * $price_per_kwh) - ($this->TotalPrice/12), 2);
		}
		else
		{
		     $fiveyearsavings["Monthly Savings"][$yearNum] =  "R ".number_format(($Total_Saved_KWh_All_Annual/12 * $price_per_kwh),2);
		}	
		
		
	} 
	//print_r($fiveyearsavings);
	return $fiveyearsavings;

	
	/*
	
	
	for($iyear = 1; $iyear < 6; $iyear++) {
            //===Price Per kWh===
            $price_per_kwh = $this->PricePerKWh * pow((1 + $Electricity_Price_Escalation_Rate), ($iyear - 1));
            $fiveyearsavings["Price per kWh"]["Year ".$iyear] = "R ".number_format($price_per_kwh, 2);

            //===Cumulative Savings===
            $cumulative_savings = ($this->PricePerKWh * $Total_Saved_KWh_All_Annual * ((pow(1+$Electricity_Price_Escalation_Rate,$iyear)-1)/$Electricity_Price_Escalation_Rate)) - $this->TotalPrice;
            $fiveyearsavings["Cumulative Savings"]["Year ".$iyear] = "R ".number_format($cumulative_savings, 2);
            
            //===Annual Savings===
            if ($iyear == 1) {
                $fiveyearsavings["Annual Savings"]["Year ".$iyear] = ($Total_Saved_KWh_All_Annual * $price_per_kwh) - $this->TotalPrice;
            }else{
                $fiveyearsavings["Annual Savings"]["Year ".$iyear] = ($Total_Saved_KWh_All_Annual * $price_per_kwh);
            }
            
            //===Monthly Savings===
            $fiveyearsavings["Monthly Savings"]["Year ".$iyear] = "R ".number_format(($fiveyearsavings["Annual Savings"]["Year ".$iyear] / 12), 2);
            
            $fiveyearsavings["Annual Savings"]["Year ".$iyear] = "R ".number_format($fiveyearsavings["Annual Savings"]["Year ".$iyear], 2);
	}
        return $fiveyearsavings;*/
    }
    
    public function Get5YearLosses() {
        $Electricity_Price_Escalation_Rate = ($this->ElectricityEscalationRatePercentage/100);
        $Water_Price_Escalation_Rate = ($this->WaterEscalationRatePercentage/100);
		$Total_Saved_KWh_All_Annual = $this->KWhSaved * 12;
        
		
		$years = $this->getYears();
		foreach ($years as $yearNum)
		{
			 //===Price Per kWh===
            $price_per_kwh = $this->PricePerKWh * pow((1 + $Electricity_Price_Escalation_Rate), ($yearNum - 1));
            $fiveyearpenalties["Price per kWh"]["Year ".$yearNum] = "R ".number_format($price_per_kwh, 2);
            
			//===Cumulative Losses===
            $cumulative_penalties = $this->TotalPrice - ($this->PricePerKWh * $Total_Saved_KWh_All_Annual * ((pow(1+$Electricity_Price_Escalation_Rate,$yearNum)-1)/ 	 	 	  			$Electricity_Price_Escalation_Rate));
            $fiveyearpenalties["Cumulative Losses"]["Year ".$yearNum] = "R ".number_format($cumulative_penalties, 2);
            
			 //===Annual Losses==
            if ($yearNum == 1) 
			{
                $fiveyearpenalties["Annual Losses"]["Year ".$yearNum] = "R ".number_format($this->TotalPrice - ($Total_Saved_KWh_All_Annual * $price_per_kwh),2);
            }
			else
			{
                $fiveyearpenalties["Annual Losses"]["Year ".$yearNum] = "R ".number_format((($Total_Saved_KWh_All_Annual * $price_per_kwh) * -1),2);
            }
			 //===Monthly Losses==
            if ($yearNum == 1)
			{

				$fiveyearpenalties["Monthly Losses"]["Year ".$yearNum] = "R ".number_format($this->TotalPrice/12 - ($Total_Saved_KWh_All_Annual/12 * $price_per_kwh), 2);
			}
			else
			{
				$fiveyearpenalties["Monthly Losses"]["Year ".$yearNum] = "R ".number_format((($Total_Saved_KWh_All_Annual/12 * $price_per_kwh) * -1),2);
			}
		}
		
		return $fiveyearpenalties;

		
	/*for($iyear = 1; $iyear < 6; $iyear++) {
            //===Price Per kWh===
            $price_per_kwh = $this->PricePerKWh * pow((1 + $Electricity_Price_Escalation_Rate), ($iyear - 1));
            $fiveyearpenalties["Price per kWh"]["Year ".$iyear] = "R ".number_format($price_per_kwh, 2);
            
            //===Cumulative Penalties===
            $cumulative_penalties = $this->TotalPrice - ($this->PricePerKWh * $Total_Saved_KWh_All_Annual * ((pow(1+$Electricity_Price_Escalation_Rate,$iyear)-1)/ 	 	 	  			$Electricity_Price_Escalation_Rate));
            $fiveyearpenalties["Cumulative Losses"]["Year ".$iyear] = "R ".number_format($cumulative_penalties, 2);
            
            //===Annual Penalties==
            if ($iyear == 1) {
                $fiveyearpenalties["Annual Losses"]["Year ".$iyear] = $this->TotalPrice - ($Total_Saved_KWh_All_Annual * $price_per_kwh);
            }else{
                $fiveyearpenalties["Annual Losses"]["Year ".$iyear] = ($Total_Saved_KWh_All_Annual * $price_per_kwh) * -1;
            }
            
            //===Monthly Penalties==
            $fiveyearpenalties["Monthly Losses"]["Year ".$iyear] = "R ".number_format($fiveyearpenalties["Annual Losses"]["Year ".$iyear] / 12, 2);
            
            $fiveyearpenalties["Annual Losses"]["Year ".$iyear] = "R ".number_format($fiveyearpenalties["Annual Losses"]["Year ".$iyear], 2);
	}*/
    }
    
    private function BuildTables() {
        $sql = "CREATE TABLE IF NOT EXISTS `quotes` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `customer_id` int(11) NOT NULL,
          `date_created` datetime NOT NULL,
          `last_updated` datetime NOT NULL,
          `approved` tinyint(1) NOT NULL DEFAULT '0',
          `complete` tinyint(1) NOT NULL DEFAULT '0',
          UNIQUE KEY `id` (`id`),
          KEY `customer_id` (`customer_id`),
          KEY `date_created` (`date_created`),
          KEY `last_updated` (`last_updated`),
          KEY `approved` (`approved`),
          KEY `complete` (`complete`)
        ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;";
        mysqli_query($GLOBALS["link"],$sql);
    }
}
?>