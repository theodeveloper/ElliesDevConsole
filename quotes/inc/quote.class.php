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

class Quote
{
	public $Grade = 0;
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
    public $Channel = 0;
	public $MonthlyPrice = 0;
	public $MaintenancePercentage = 0;
	public $Row;
	public $OutHours=0;

    public $initRent=-1;
    public $initMaint=-1;
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
    public $RentalTerm = 0;
    public $Deposit = 0;
    public $RentalAmountAdditional = 0;
	public $MaintenanceAmountAdditional = 0;
    public $RentalAmountReplacement = 0;
	public $MaintenanceAmountReplacement = 0;
    public $RentalNominal = 0;
    public $Interest = 0;
	public $RentalEscalationRate = 0;
	
	public $TravelCostsReplacement = 0;
	public $LabourCostsReplacement = 0;
	public $CrushCostsReplacement = 0;
	public $MaterialsCostsReplacement = 0;

	public $TravelCostsAdditional = 0;
	public $LabourCostsAdditional = 0;
	public $CrushCostsAdditional = 0;
	public $MaterialsCostsAdditional = 0;
	
	public $TravelCostsReplacementCost = 0;
	public $LabourCostsReplacementCost = 0;
	public $CrushCostsReplacementCost = 0;
	public $MaterialsCostsReplacementCost = 0;

	public $TravelCostsAdditionalCost = 0;
	public $LabourCostsAdditionalCost = 0;
	public $CrushCostsAdditionalCost = 0;
	public $MaterialsCostsAdditionalCost = 0;
	
    public $NumberOfItems = 0;
    public $AllCosts = 0;
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

	public $OriginalPrice = 0;
    public $ReplacementPrice = 0;
	public $AdditionalPrice = 0;
	public $AllPrice = 0;
    public $TotalPriceExVat = 0;
    public $VAT = 0;
    public $Discount = 0;
	
	public $DoInstall = 1;
	public $OverridePrice = -1;
	public $OverridePriceAdditionalCapital = -1;
	public $OverridePriceAdditional = -1;
	public $OverridePriceReplacementCapital = -1;
	public $OverridePriceReplacement = -1;
	public $OverrideDeposit = -1;
	public $OverrideRental = -1;
	
	public $Settings;
	
    public $Distance = 0;
    public $Address = "";
    public $ElectricSupplier = "";
    public $PropertyID ="";
    public $ExpirateDate ="";
    public $KWhNewNoAdditional = 0;

    public $Items = NULL;

    public $yearsInit = array(1, 2, 3, 4, 5);

    public function __construct($QuoteID = 0)
    {
        $this->BuildTables();
        if ($QuoteID > 0) {
			$sql1 = "SELECT * FROM `quote_settings` WHERE `quoteid` = '" . mysqli_real_escape_string($GLOBALS["link"],$QuoteID) . "'";
			//print $sql1;
            $sqlres1 = mysqli_query($GLOBALS["link"],$sql1);
			$this->Settings = mysqli_fetch_assoc($sqlres1);
		
            $sql = "SELECT * FROM `quotes` WHERE `id` = '" . mysqli_real_escape_string($GLOBALS["link"],$QuoteID) . "'";
            $sqlres = mysqli_query($GLOBALS["link"],$sql);
            while ($row = mysqli_fetch_assoc($sqlres)) {
				$this->Row=$row;
                $this->Exists = true;
                $this->QuoteID = $row["id"];
                if (!empty($row["ref"])) {
                    $this->QuoteReferenceNo = $row["ref"];
                } else {
                    $this->QuoteReferenceNo = "INQ" . str_pad($this->QuoteID, 6, "0", STR_PAD_LEFT);
                }
                $this->CustomerID = $row["customer_id"];
                $this->Approved = $row["approved"];
				//die ("Here");
				$this->Grade = $row["grade"];
                $this->DateCreated = $row["date_created"];
                $this->LastUpdated = $row["last_updated"];
                $this->Address = $row["property"];
                $this->ElectricSupplier = $row["electrical_supplier_id"];
                $this->PropertyID = $row["property_id"];
                $this->ExpirateDate = $row["expiration_date"];
                
                $this->Discount = $row["discount"];
                $this->Channel = $row["channel"];
                $this->RentalTerm = $row["rental_term"];
				$this->OutHours = $row["numerator"];
                $this->Deposit = $row["deposit_amount"];
				$this->RentalEscalationRate = $row["rental_escalation_rate"];
                $this->Distance = $row["distance"];
				$this->LabourCostsReplacement = $row["labour_costs_replacement"];
				$this->LabourCostsAdditional = $row["labour_costs_additional"];
				
				$labourratio = 1;
				if ($row["do_install"]==2){
					$labourratio = $this->Settings["labour_per_minute_out_hours"]/$this->Settings["labour_per_minute_in_hours"];
				}
				
				$this->TravelCostsReplacement = (($row["override_travel_replacement"]==-1) ? $row["travel_costs_replacement"] : $row["override_travel_replacement"]);
				$this->LabourCostsReplacement = (($row["override_labour_replacement"]==-1) ? $row["labour_costs_replacement"]*$labourratio : $row["override_labour_replacement"]);
				$this->CrushCostsReplacement = (($row["override_crush_replacement"]==-1) ? $row["crush_costs_replacement"] : $row["override_crush_replacement"]);
				$this->MaterialsCostsReplacement = 0;

				$this->TravelCostsAdditional = (($row["override_travel_additional"]==-1) ? $row["travel_costs_additional"] : $row["override_travel_additional"]);
				$this->LabourCostsAdditional = (($row["override_labour_additional"]==-1) ? $row["labour_costs_additional"] * $labourratio : $row["override_labour_additional"]);
				$this->CrushCostsAdditional = (($row["override_crush_additional"]==-1) ? $row["crush_costs_additional"] : $row["override_crush_additional"]);
				$this->MaterialsCostsAdditional = (($row["override_materials_additional"]==-1) ? $row["materials_costs_additional"] : $row["override_materials_additional"]);


				$this->TravelCostsReplacementCost = $row["travel_costs_replacement"]/(1+$this->Settings["contractor_cost_per_km_markup"]);
				$this->LabourCostsReplacementCost = $row["labour_costs_replacement"]*$labourratio/(1+$this->Settings["labour_per_minute_markup"]);
				$this->CrushCostsReplacementCost = $row["crush_costs_replacement"]/(1+$this->Settings["disposal_charge_markup"]);
				$this->MaterialsCostsReplacementCost = $row["materials_costs_replacement"]/(1+$this->Settings["materials_markup"]);

				$this->TravelCostsAdditionalCost = $row["travel_costs_additional"]/(1+$this->Settings["contractor_cost_per_km_markup"]);
				$this->LabourCostsAdditionalCost = $row["labour_costs_additional"]*$labourratio/(1+$this->Settings["labour_per_minute_markup"]);
				$this->CrushCostsAdditionalCost = $row["crush_costs_additional"]/(1+$this->Settings["disposal_charge_markup"]);
				$this->MaterialsCostsAdditionalCost = $row["materials_costs_additional"]/(1+$this->Settings["materials_markup"]);
			
				
				$this->DoInstall = $row["do_install"];
				if (($row["do_install"]==0)||($row["channel"]==0)){
					$this->TravelCostsReplacement = 0;
					$this->LabourCostsReplacement=0;
					$this->MaterialsCostsReplacement=0;
					$this->CrushCostsReplacement=0;
					$this->CrushCostsAdditional=0;
					$this->TravelCostsAdditional = 0;
					$this->LabourCostsAdditional=0;
					$this->MaterialsCostsAdditional=0;

					$this->TravelCostsReplacementCost = 0;
					$this->LabourCostsReplacementCost=0;
					$this->MaterialsCostsReplacementCost=0;
					$this->CrushCostsReplacementCost=0;
					$this->CrushCostsAdditionalCost=0;
					$this->TravelCostsAdditionalCost = 0;
					$this->LabourCostsAdditionalCost=0;
					$this->MaterialsCostsAdditionalCost=0;
				}
				
				$this->OverridePrice = $row["override_price"];
				$this->OverridePriceReplacement = $row["override_price_replacement"];
				$this->OverridePriceReplacementCapital = $row["override_price_replacement_capital"];
				$this->OverridePriceAdditional = $row["override_price_additional"];
				$this->OverridePriceAdditionalCapital = $row["override_price_additional_capital"];
				$this->OverrideRental = $row["override_rental"];
				
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
	
                $sql = "SELECT `username` FROM `system_users` WHERE `id` = '" . mysqli_real_escape_string($GLOBALS["link"],$this->CreatedBy) . "' LIMIT 0, 1";
                $sqlres = mysqli_query($GLOBALS["link"],$sql);
                while ($row = mysqli_fetch_assoc($sqlres)) {
                    $this->CreatedByUserName = $row["username"];
                }
            }
            $result = mysqli_query($GLOBALS["link"],"Select interest_rate_annual_effective from rental");
            $rr = mysqli_fetch_assoc($result);
            $this->Interest = $rr["interest_rate_annual_effective"];

            $sql = "SELECT `id`, `newqty` FROM `quote_items` WHERE `quote_id` = '" . mysqli_real_escape_string($GLOBALS["link"],$QuoteID) . "' ORDER BY `id`";
            $sqlres = mysqli_query($GLOBALS["link"],$sql);
            while ($row = mysqli_fetch_assoc($sqlres)) {
                $this->Items[] = $row["id"];
                $this->NumberOfItems += $row["newqty"];
                $this->HasItems = true;
            }
        }
    }

    public function Save()
    {
        if (!$this->Exists) {
            $sql = "INSERT INTO `quotes` (`date_created`, `customer_id`, `created_by`) VALUES (NOW(), '" . mysqli_real_escape_string($GLOBALS["link"],$this->CustomerID) . "', '" . mysqli_real_escape_string($GLOBALS["link"],$this->CreatedBy) . "')";
            mysqli_query($GLOBALS["link"],$sql);
            $this->QuoteID = mysql_insert_id();
            $this->Exists = true;
        }
        if ($this->Exists) {
            $sql = "UPDATE `quotes` SET";
            $sql .= " `last_updated` = NOW()";
            $sql .= ", `approved` = '" . mysqli_real_escape_string($GLOBALS["link"],$this->Approved) . "'";
            $sql .= ", `complete` = '" . mysqli_real_escape_string($GLOBALS["link"],$this->Complete) . "'";
            $sql .= ", `escalation_rate` = '" . mysqli_real_escape_string($GLOBALS["link"],$this->ElectricityEscalationRatePercentage) . "'";
            $sql .= ", `discount` = '" . mysqli_real_escape_string($GLOBALS["link"],$this->Discount) . "'";
            $sql .= ", `kwh_price` = '" . mysqli_real_escape_string($GLOBALS["link"],$this->KWhPrice) . "'";
            $sql .= ", `property` = '" . mysqli_real_escape_string($GLOBALS["link"],$this->Property) . "'";
            $sql .= ", `ref` = '" . mysqli_real_escape_string($GLOBALS["link"],$this->QuoteReferenceNo) . "'";
            $sql .= ", `created_by` = '" . mysqli_real_escape_string($GLOBALS["link"],$this->CreatedBy) . "'";
            $sql .= ", `updated_by` = '" . mysqli_real_escape_string($GLOBALS["link"],$this->UpdatedBy) . "'";
            $sql .= " WHERE `id` = '" . mysqli_real_escape_string($GLOBALS["link"],$this->QuoteID) . "'";
            mysqli_query($GLOBALS["link"],$sql);

            $sql = "UPDATE `quote_items` SET `quote_id` = '" . mysqli_real_escape_string($GLOBALS["link"],$this->QuoteID) . "' WHERE `quote_id` = 0 AND `session_id` = '" . mysqli_real_escape_string($GLOBALS["link"],$this->SessionID) . "'";
            mysqli_query($GLOBALS["link"],$sql);
        }
    }

    public function Delete()
    {
        $sql = "DELETE FROM `quote_items_input` WHERE `quote_item_id` IN (SELECT `id` FROM `quote_items` WHERE `quote_id` = '" . mysqli_real_escape_string($GLOBALS["link"],$this->QuoteID) . "')";
        mysqli_query($GLOBALS["link"],$sql);

        $sql = "DELETE FROM `quote_items` WHERE `quote_id` = '" . mysqli_real_escape_string($GLOBALS["link"],$this->QuoteID) . "'";
        mysqli_query($GLOBALS["link"],$sql);

        $sql = "DELETE FROM `quotes` WHERE `id` = '" . mysqli_real_escape_string($GLOBALS["link"],$this->QuoteID) . "'";
        mysqli_query($GLOBALS["link"],$sql);
    }

    public function getN(){
        return 0;
    }

    public function GetNominalRent()
    {
        $total = $this->Deposit;
        for ($i = 0; $i < $this->RentalTerm; $i++) {
            $total = $total + $this->getRentInMonth("all",$this->RentalTerm,$i,$this->MaintenancePercentage);
        }
        
        return $total;
    }

    public function GetCumulativeRent($n)
    {
        $monthlimit = $n * 12;
        $total = $this->Deposit;
        $limit = min($monthlimit, $this->RentalTerm);
        for ($i = 0; $i < $limit; $i++) {
            $total = $total + $this->getRentInMonth("all",$this->RentalTerm,$i,$this->MaintenancePercentage);
        }
        return $total;
    }
	
    public function CalculateCostSavingTotals()
    {
        $sql = "SELECT `id` FROM `quote_items` WHERE `quote_id` = '" . mysqli_real_escape_string($GLOBALS["link"],$this->QuoteID) . "'";
        $sqlres = mysqli_query($GLOBALS["link"],$sql);
        while ($row = mysqli_fetch_assoc($sqlres)) {
            $quoteitem = new QuoteItem($row["id"], $this);
            $techitem = new TechItem($quoteitem->TechID);
            $quoteitem->CalculateCostSavings();
            $this->KWhOld += $quoteitem->KWhOld;
            $this->KWhNew += $quoteitem->KWhNew;
            $this->KWhSaved += $quoteitem->KWhSaved;

            $this->LitresOld += $quoteitem->LitresOld;
            $this->LitresNew += $quoteitem->LitresNew;

            $this->MonthlyCostOld += $quoteitem->MonthlyCostOld;
            $this->MonthlyCostNew += $quoteitem->MonthlyCostNew;

            $this->MonthlyCostSaving += $quoteitem->MonthlyCostSaving;
            
    	    if ($quoteitem->OldProductID!=-1){
    		  $this->ReplacementPrice += $quoteitem->ItemTotal;
              $this->KWhNewNoAdditional += $quoteitem->KWhNew;
    	    } else {
    		  $this->AdditionalPrice += $quoteitem->ItemTotal;
    	    }
		    $this->AllCosts += $quoteitem ->Cost;
	        $this->OriginalPrice += $quoteitem->OriginalPrice;
            $this->TotalPaybackPeriod += $quoteitem->PaybackPeriod;
        }
		
        $this->TotalPriceExVat = $this->AllPrice / 1.14;
	    $this->Deposit=$this->getDeposit("all");
		
		if ($this->OverridePriceAdditionalCapital != -1){$this->AdditionalPrice = $this->OverridePriceAdditionalCapital;}
		if ($this->OverridePriceReplacementCapital != -1){$this->ReplacementPrice = $this->OverridePriceReplacementCapital;};
		
		if ($this->TravelCostsReplacement != -1){
			$this->ReplacementPrice = $this->ReplacementPrice + $this->TravelCostsReplacement + $this->LabourCostsReplacement + $this->CrushCostsReplacement + $this->MaterialsCostsReplacement ;
			$this->AdditionalPrice  = $this->AdditionalPrice+ $this->TravelCostsAdditional + $this->LabourCostsAdditional + $this->CrushCostsAdditional + $this->MaterialsCostsAdditional;

			$this->AllCosts = $this->AllCosts + $this->TravelCostsReplacementCost+ $this->LabourCostsReplacementCost + $this->CrushCostsReplacementCost + $this->MaterialsCostsReplacementCost;
			$this->AllCosts = $this->AllCosts + $this->TravelCostsAdditionalCost+ $this->LabourCostsAdditionalCost + $this->CrushCostsAdditionalCost + $this->MaterialsCostsAdditionalCost;	
		}
		
		//print "Additionals : ".(($this->TravelCostsReplacement/(1+$this->Settings["contractor_cost_per_km_markup"])) + ($this->LabourCostsReplacement/(1+$this->Settings["labour_per_minute_markup"])) + ($this->CrushCostsReplacement/(1+$this->Settings["disposal_charge_markup"])) + ($this->MaterialsCostsReplacement/(1+$this->Settings["materials_markup"])));
		if ($this->OverridePriceAdditional!=-1){$this->AdditionalPrice=$this->OverridePriceAdditional;};
		if ($this->OverridePriceReplacement!=-1){$this->ReplacementPrice=$this->OverridePriceReplacement;};

		$this->AllPrice = $this->ReplacementPrice+$this->AdditionalPrice;
		
		
		if ($this->OverridePrice!=-1){
			$this->AllPrice = $this->OverridePrice;
		}

		$targetRentalReturn = $this->Row["override_target_return"];
		if ($targetRentalReturn==-1){
		//die ("Here");
		if ($this->Grade==0){
			$fld = "return_A";
		}
		if ($this->Grade==1){
			$fld = "return_B";
		}
		if ($this->Grade==2){
			$fld = "return_C";
		}
		$targetRentalReturn = $this->Settings[$fld];
		}
		
		$target = $this->getRate($targetRentalReturn,12);
        
		if ($this->RentalTerm != 0) {
		   if ($this->Row["override_deposit_additional"]==-1){
			$this->optimalProcedure($target);
            
			$this->RentalAmount = $this->getRentalAmount("all",$this->RentalTerm, $this->MaintenancePercentage);
			//print "Actual Return is ".$this->getReturn($this->RentalTerm, $this->MaintenancePercentage);
		   } else {
            
			$this->RentalTerm=$this->Row["override_deposit_additional"];
			$this->MaintenancePercentage=$this->getMaintenance($this->RentalTerm,$target);
            /*error_reporting(E_ALL);     
            print "test3";
            echo "<br/>";
            $return=$this->getReturn(12,0);
            echo "Return = $return <br/>";
            $var12 = ($this->getRentalAmount("replacement",$this->RentalTerm,$this->MaintenancePercentage))/$this->MonthlyCostSaving;
            $var13 = $this->getRentalAmount("replacement",$this->RentalTerm,$this->MaintenancePercentage);
            echo "get rental factor = $var12<br/>";
            echo "get rental amount = $var13<br/>";
            //echo "factor = $factor <br/>";
            echo "MonthlyCostSaving = $this->MonthlyCostSaving <br/>";*/
			$this->RentalAmount = $this->getRentalAmount("all",$this->RentalTerm, $this->MaintenancePercentage);
		   }
		}
		
		if ($this->OverrideRental!=-1){
			$this->RentalAmount=$this->OverrideRental;
		}
		
        if ($this->RentalTerm != 0) {
            $this->RentalNominal = $this->GetNominalRent();
        }
	
        $this->VAT = $this->AllPrice - $this->TotalPriceExVat;
        $this->LitresSaved = $this->LitresOld - $this->LitresNew;
        
        if ($this->RentalTerm == 0) {
            $this->TotalPaybackPeriod = CalculatePayback($this->KWhPrice, $this->ElectricityEscalationRatePercentage, $this->ReplacementPrice, $this->KWhSaved);
        } else {
            $this->TotalPaybackPeriod = CalculatePayback($this->KWhPrice, $this->ElectricityEscalationRatePercentage, $this->RentalNominal, $this->KWhSaved);
        }
        $this->TotalPaybackPeriodFormatted = number_format((double)$this->TotalPaybackPeriod, 2) . " Months";
        if($this->KWhOld == 0){
            $this->KWhSavedPerc = 0;
        }else{
           $this->KWhSavedPerc = number_format((double)($this->KWhSaved / $this->KWhOld) * 100, 0); 
        }
        
    }

    public function PrintCostSummary()
    {
        //$html .= "techid: ".$this->TechID."<br>";
        $this->CalculateCostSavingTotals();

        $html .= "<table width='100%' cellpadding='3'><tr><td width='50%'>";
        $html .= "<b>Old kWh/Month:</b> " . number_format($this->KWhOld, 2);
        $html .= "<br><b>New kWh/Month:</b> " . number_format($this->KWhNew, 2);
        $html .= "<br><b>Saved kWh/Month:</b> " . number_format($this->KWhSaved, 2);

        /*
        $html .= "</td><td width='33%'>";
        $html .= "<b>Old Litres/Year:</b> ".$this->LitresOld;
        $html .= "<br><b>New Litres/Year:</b> ".$this->LitresNew;
        $html .= "<br><b>Saved Litres/Year:</b> ".$this->LitresSaved;
        */

        $html .= "</td><td>";
        $html .= "<b>Old Monthly Cost:</b> R " . number_format($this->MonthlyCostOld, 2);
        $html .= "<br><b>New Monthly Cost:</b> R " . number_format($this->MonthlyCostNew, 2);
        $html .= "<br><b>Monthly Saving:</b> R " . number_format($this->MonthlyCostSaving, 2);
        $html .= "</td></tr></table>";
    }

    private function getYears()
    {
        $Total_payback_savings = number_format((double)$this->TotalPaybackPeriod, 2);

        $count = 0;
        $iyear = 1;
        $years = array();
        $years[1] = 1;
        $years[5] = 5 * (ceil($Total_payback_savings / 60));

        if ($years[5] != 0) {
            if ($years[5] % 2 == 0) //if fifth year is even
            {
                $years[3] = $years[5] / 2;
                $years[2] = floor($years[3] - ($years[5] / 4));
                $years[4] = ceil($years[3] + ($years[5] / 4));
            } else //if fifth year is odd
            {
                $years[3] = ($years[5] + 1) / 2;
                $years[2] = floor($years[3] - ($years[5] - $years[3]) / 2);
                $years[4] = ceil($years[3] + ($years[5] - $years[3]) / 2);
            }
            ksort($years);
        } else {
            $years = $this->yearsInit;
        }

        return $years;
    }

    public function MonthlyRental($n)
    {
        return $this->RentalAmount * pow((1 + $this->Interest), $n-1);
    }

    public function NormalisedMonthlyRental($n){
        $monthlimit = $n * 12;
        $price = $this->MonthlyRental($n);
		if ($n==0){
		  $total = $this->Deposit;
		} else {
            $total = 0;
		}
        $limit = min($monthlimit, $this->RentalTerm);
        for ($i = $monthlimit-12; $i < $limit; $i++) {
            $total = $total + $price;
        }
        return $total/12;
    }

    public function Get5YearSavings()
    {
        $Electricity_Price_Escalation_Rate = ($this->ElectricityEscalationRatePercentage / 100);
        $Water_Price_Escalation_Rate = ($this->WaterEscalationRatePercentage / 100);
        $Total_Saved_KWh_All_Annual = $this->KWhSaved * 12;
        $years = $this->getYears();

        foreach ($years as $yearNum) {

            if ($this->RentalTerm == 0) {
                $price_per_kwh = $this->PricePerKWh * pow((1 + $Electricity_Price_Escalation_Rate), ($yearNum - 1));
                $fiveyearsavings["Price per kWh"][$yearNum] = "R " . number_format($price_per_kwh, 2);

                //===Cumulative Savings===
                $cumulative_savings = ($this->PricePerKWh * $Total_Saved_KWh_All_Annual * ((pow(1 + $Electricity_Price_Escalation_Rate, $yearNum) - 1) / $Electricity_Price_Escalation_Rate)) - $this->ReplacementPrice;
                $fiveyearsavings["Cumulative Savings"][$yearNum] = "R " . number_format($cumulative_savings, 2);
                //$fiveyearsavings["term"][$yearNum] = $yearNum;
                //===Annual Savings===
                if ($yearNum == 1) {
                    $fiveyearsavings["Annual Savings"][$yearNum] = "R " . number_format(($Total_Saved_KWh_All_Annual * $price_per_kwh) - $this->ReplacementPrice, 2);
                } else {
                    $fiveyearsavings["Annual Savings"][$yearNum] = "R " . number_format(($Total_Saved_KWh_All_Annual * $price_per_kwh), 2);
                }
                //===Monthly Savings===
                if ($yearNum == 1) {
                    $fiveyearsavings["Monthly Savings"][$yearNum] = "R " . number_format(($Total_Saved_KWh_All_Annual / 12 * $price_per_kwh) - ($this->ReplacementPrice / 12), 2);
                } else {
                    $fiveyearsavings["Monthly Savings"][$yearNum] = "R " . number_format(($Total_Saved_KWh_All_Annual / 12 * $price_per_kwh), 2);
                }
            } else {
                //===Price Per kWh===
                $price_per_kwh = $this->PricePerKWh * pow((1 + $Electricity_Price_Escalation_Rate), ($yearNum - 1));
                $fiveyearsavings["Price per kWh"][$yearNum] = "R " . number_format($price_per_kwh, 2);
                //===Cumulative Savings===
                $cumulative_savings = ($this->PricePerKWh * $Total_Saved_KWh_All_Annual * ((pow(1 + $Electricity_Price_Escalation_Rate, $yearNum) - 1) / $Electricity_Price_Escalation_Rate)) - $this->GetCumulativeRent($yearNum);
                $fiveyearsavings["Cumulative Savings"][$yearNum] = "R " . number_format($cumulative_savings, 2);
                //===Annual Savings===
                $fiveyearsavings["Annual Savings"][$yearNum] = "R " . number_format(($Total_Saved_KWh_All_Annual * $price_per_kwh) - ($this->NormalisedMonthlyRental($yearNum) * 12), 2);
                //===Monthly Savings===
                $fiveyearsavings["Monthly Savings"][$yearNum] = "R " . number_format(($Total_Saved_KWh_All_Annual / 12 * $price_per_kwh) - $this->NormalisedMonthlyRental($yearNum), 2);
            }
        }
        return $fiveyearsavings;
    }

    public function Get5Savings()
    {
        $Electricity_Price_Escalation_Rate = ($this->ElectricityEscalationRatePercentage / 100);
        $Water_Price_Escalation_Rate = ($this->WaterEscalationRatePercentage / 100);
        $Total_Saved_KWh_All_Annual = $this->KWhSaved * 12;
        $years = $this->getYears();
        $yearNum = 5;

        //===Price Per kWh===
        $price_per_kwh = $this->PricePerKWh * pow((1 + $Electricity_Price_Escalation_Rate), ($yearNum - 1));

        //===Cumulative Savings===
        if ($this->RentalTerm == 0) {
            $cumulative_savings = ($this->PricePerKWh * $Total_Saved_KWh_All_Annual * ((pow(1 + $Electricity_Price_Escalation_Rate, $yearNum) - 1) / $Electricity_Price_Escalation_Rate)) - $this->ReplacementPrice;
        } else {
            $cumulative_savings = ($this->PricePerKWh * $Total_Saved_KWh_All_Annual * ((pow(1 + $Electricity_Price_Escalation_Rate, $yearNum) - 1) / $Electricity_Price_Escalation_Rate)) - $this->GetCumulativeRent($yearNum);

        }
        return number_format($cumulative_savings, 2);
    }

    public function Get5YearLosses()
    {
        $Electricity_Price_Escalation_Rate = ($this->ElectricityEscalationRatePercentage / 100);
        $Water_Price_Escalation_Rate = ($this->WaterEscalationRatePercentage / 100);
        $Total_Saved_KWh_All_Annual = $this->KWhSaved * 12;

        $years = $this->getYears();
        foreach ($years as $yearNum) {
            //===Price Per kWh===
            $price_per_kwh = $this->PricePerKWh * pow((1 + $Electricity_Price_Escalation_Rate), ($yearNum - 1));
            $fiveyearpenalties["Price per kWh"]["Year " . $yearNum] = "R " . number_format($price_per_kwh, 2);

            //===Cumulative Losses===
            $cumulative_penalties = $this->TotalPrice - ($this->PricePerKWh * $Total_Saved_KWh_All_Annual * ((pow(1 + $Electricity_Price_Escalation_Rate, $yearNum) - 1) / $Electricity_Price_Escalation_Rate));
            $fiveyearpenalties["Cumulative Losses"]["Year " . $yearNum] = "R " . number_format($cumulative_penalties, 2);

            //===Annual Losses==
            if ($yearNum == 1) {
                $fiveyearpenalties["Annual Losses"]["Year " . $yearNum] = "R " . number_format($this->TotalPrice - ($Total_Saved_KWh_All_Annual * $price_per_kwh), 2);
            } else {
                $fiveyearpenalties["Annual Losses"]["Year " . $yearNum] = "R " . number_format((($Total_Saved_KWh_All_Annual * $price_per_kwh) * -1), 2);
            }
            //===Monthly Losses==
            if ($yearNum == 1) {
                $fiveyearpenalties["Monthly Losses"]["Year " . $yearNum] = "R " . number_format($this->TotalPrice / 12 - ($Total_Saved_KWh_All_Annual / 12 * $price_per_kwh), 2);
            } else {
                $fiveyearpenalties["Monthly Losses"]["Year " . $yearNum] = "R " . number_format((($Total_Saved_KWh_All_Annual / 12 * $price_per_kwh) * -1), 2);
            }
        }
        return $fiveyearpenalties;
    }

	public function PV0($i, $n, $x) {
		$t1 = pow(1 + $i, -$n);
		$t2 = 1 - $t1;
		$numerator = -$x * $t2;
		if (($numerator==0) && ($i==0)){
			return -$x*$n;
		}
		return $numerator / $i;
	}

	public function PV1($i, $n, $x) {
		$t1 = pow(1 + $i, -$n);
		$t2 = 1 - $t1;
		$numerator = -$x * $t2;
		$denominator = $i * (pow(1 + $i, -1));
		if (($numerator==0) && ($denominator==0)){
			return -$x*$n;
		}
		return $numerator / $denominator;
	}

	public function getRate($i, $m) {
		$t1 = pow(1 + $i, 1 / $m);
		return $t1 - 1;
	}

	public function getFixedMaintenancePerProduct($quoteitem) {
		if ($quoteitem->NewProductID==-1){
			return 0;
		}
		return $quoteitem->FixedMaintenance;
	}

	public function PVFixedMaintenancePerProduct($quoteitem, $term, $rate) {
		if ($quoteitem->NewProductID==-1){
			return 0;
		}
		
		$t1 = (1 + $rate)
				/ (1 + $this->Settings["fixed_maintenance_per_product_escalation_rate"]);
		$result = $this->PV0($t1 - 1, $term / 12, -1
				* $this->getFixedMaintenancePerProduct($quoteitem));
		return $result;
	}

	public function getPVFixedMaintenancePerProductTotal($class, $term, $rate) {
		$total = 0;
        $sql = "SELECT `id` FROM `quote_items` WHERE `quote_id` = '" . mysqli_real_escape_string($GLOBALS["link"],$this->QuoteID) . "'";
        $sqlres = mysqli_query($GLOBALS["link"],$sql);
        while ($row = mysqli_fetch_assoc($sqlres)) {
            $quoteitem = new QuoteItem($row["id"], $this);
			if ((($quoteitem->OldProductID==-1)==($class=="additional"))||($class=="all")){	
				//print "prodid : ".$quoteitem->OldProductID." class = ".$class;
				$total = $total + $this->PVFixedMaintenancePerProduct($quoteitem, $term, $rate);
			}
		}
		return $total;
	}
	
	public function getPVFixedMaintenanceContract($class) {
		$t1 = (1 + $this->Settings["interest_rate_annual_effective"])/ (1 + $this->Settings["fixed_maintenance_per_contract_escalation_rate"]);
		//print "Term: ".$this->RentalTerm;
		$result = $this->getFixedMaintenancePerContract($class)*$this->PV0($t1 - 1, $this->RentalTerm / 12,-1);
		return $result;
	}

	/*
	 * public function PVPercentageMaintenanceContract(){ double
	 * t1=(1+interestRateAnnualEffective)/(1+rentalAmountEscalationRate); double
	 * t2=rent }
	 */
	 

	public function getDepositProportion(){
		if ($this->Row["override_deposit_replacement"]==-1){
			return $this->Settings["deposit_percentage"]*100;
		} else {
		  return $this->Row["override_deposit_replacement"]*100;
		}	
	}

	public function getDeposit($class){
		if ($class=="all"){
			return $this->getDeposit("additional")+$this->getDeposit("replacement");
		}

		if ($this->Row["override_deposit_replacement"]==-1){
			return $this->getPrice($class)*$this->Settings["deposit_percentage"];
		} else {
		  return $this->getPrice($class)*$this->Row["override_deposit_replacement"];
		}	
	}
	
	public function getFixedRatio($class){
		$diff=$this->getPrice("all")-$this->getPrice($class);
		if ($diff==$this->getPrice("all")){
			return 0;
		} else if ($diff!=0){
			return 0.5;
		} else {
			return 1;
		}
	}
	
	public function getFixedMaintenancePerContract($class){
		return $this->Settings["fixed_maintenance_per_contract"]*$this->getFixedRatio($class);
	}
	
	public function getPVInitialExpensesRental($class){
		return $this->Settings["initial_expenses_rental"]*$this->getFixedRatio($class);
	}
	
	public function getPrice($class){
		if ($class=="additional"){
			return $this->AdditionalPrice;
		} else  if ($class=="replacement"){
			return $this->ReplacementPrice;
		} else {
			return $this->AllPrice;
		}
	}
	 
	public function getRentalCapital($class,$RentalTerm){
		$InterestAnnualEffective = $this->Settings["interest_rate_annual_effective"];
		$CapitalEscalation = $this->Settings["rental_capital_escalation_rate"];

		$n1=$this->getPrice($class)-$this->getDeposit($class);
		$d1=$this->PV0($this->getRate($InterestAnnualEffective,12),12,-1);
		$d2=$this->PV1(((1+$InterestAnnualEffective)/(1+$CapitalEscalation))-1,$RentalTerm/12,-1);
		return $n1/($d1*$d2);
	}
	
	public function getPVPercentagePriceMaintenance($class, $RentalTerm, $MaintenancePercentage){
		$InterestAnnualEffective = $this->Settings["interest_rate_annual_effective"];
		$MaintenancePercentagePriceEscalation = $this->Settings["maintenance_percentage_price_escalation_rate"];
		$n1 = $MaintenancePercentage*$this->getPrice($class);
		$n2 = $this->PV0(((1+$InterestAnnualEffective)/(1+$MaintenancePercentagePriceEscalation))-1, $RentalTerm/12, -1);
		return $n1*$n2;
	}
	
	public function getRentalMaintenance($class, $RentalTerm, $MaintenancePercentage){
		$InterestAnnualEffective = $this->Settings["interest_rate_annual_effective"];
		$MaintenanceRentalAmountEscalationRate = $this->Settings["maintenance_rental_amount_escalation_rate"];
		
		$total = (float) $this->getPVInitialExpensesRental($class);
		//print "PVIER : ".$this->getPVInitialExpensesRental($class);
    
		$total += (float) $this->getPVFixedMaintenanceContract($class);
		//print "PVFMC:" .$class." : ".$this->getPVFixedMaintenanceContract($class);

		$total += (float) $this->getPVFixedMaintenancePerProductTotal($class,$RentalTerm, $InterestAnnualEffective);
		//print "PVFMPPT:" .$class." : ".$this->getPVFixedMaintenancePerProductTotal($class,$RentalTerm, $InterestAnnualEffective);;
		//print "Total without inverse maintenance = $total<br/>";
        
		$total += (float) $this->getPVPercentagePriceMaintenance($class, $RentalTerm, $MaintenancePercentage);//problem
		//print "PV percentage price maintenance :" .$class." : ".$this->getPVPercentagePriceMaintenance($class, $RentalTerm, $MaintenancePercentage);
       
		$rate = (float) $this->getRate($InterestAnnualEffective,12);
	
		$d1=(float) $this->PV0($rate,12,-1);
		$rate2 = (float) ((1+$InterestAnnualEffective)/(1+$MaintenanceRentalAmountEscalationRate))-1;
		
		$d2=(float)$this->PV1($rate2,$RentalTerm/12,-1);
		$denominator =(float) $d1*$d2;
		/*if ($class=="additional"){
		print "Total is $total";
		print "d1 = $d1, d2 = $d2, int = $InterestAnnualEffective, main = $MaintenanceRentalAmountEscalationRate<br/>";
		print "Denominator is $denominator <br/>";
		print "Maintenance is ".$total/$denominator."<br/>";
		}*/
		return $total/$denominator;
	}
	
	public function getRentalAmount($class, $RentalTerm,$MaintenancePercentage){
		$this->initRent=-1;
		return $this->getRentalCapital($class, $RentalTerm)+$this->getRentalMaintenance($class, $RentalTerm,$MaintenancePercentage);
	}

	//month is zero indexed, so 0 refers to the end of month 1
	public function getRentInMonth($class, $term, $month, $MaintenancePercentage){
		$CapitalEscalation = $this->Settings["rental_capital_escalation_rate"];
		$MaintenanceRentalAmountEscalationRate = $this->Settings["maintenance_rental_amount_escalation_rate"];
		if ($this->initRent==-1){
		  $this->initRent = $this->getRentalCapital($class, $term);
		  $this->initMaint = $this->getRentalMaintenance($class, $term, $MaintenancePercentage);
		}
		$growth = floor($month/12);
		$finalCapital=$this->initRent*pow(1+$CapitalEscalation,$growth);
		$finalMaintenance=$this->initMaint*pow(1+$MaintenanceRentalAmountEscalationRate,$growth);
		return $finalCapital+$finalMaintenance;
	}
	
	public function getPVRent($return, $term, $MaintenancePercentage){
		$this->initRent=-1;
		$total=$this->getDeposit("all");
		$discount=1/(1+$return);

		for ($i=0; $i<$term; $i++){
			$total=$total+($this->getRentInMonth("all",$term,$i,$MaintenancePercentage)*$discount);
			$discount=$discount/(1+$return);
		}
		return $total;
	}
	
	public function getPVTargetPercentagePriceMaintenance($return, $RentalTerm, $MaintenancePercentage){
		$InterestAnnualEffective = $return;
		$MaintenancePercentagePriceEscalation = $this->Settings["maintenance_percentage_price_escalation_rate"];
		$n1 = $MaintenancePercentage*$this->getPrice("all");
		$n2 = $this->PV0(((1+$InterestAnnualEffective)/(1+$MaintenancePercentagePriceEscalation))-1, $RentalTerm/12, -1);
		return $n1*$n2;
	}
	
	public function getPVFixedMaintenanceContractCost($class, $return, $term) {
		$t1 =  (1 + $this->Settings["fixed_maintenance_per_contract_escalation_rate"])/(1+$return);
		//print "Term: ".$this->RentalTerm;
		$c = $this->getFixedMaintenancePerContract($class);
		$result = ($c*(1-pow(1+($t1-1),($term/12)+1))/($t1-1))+$c;
		
		return -1*$result;
	}
	
	public function getPVFixedMaintenancePerProductCost($class, $return, $term) {
		$total = 0;
        $sql = "SELECT `id` FROM `quote_items` WHERE `quote_id` = '" . mysqli_real_escape_string($GLOBALS["link"],$this->QuoteID) . "'";
        $sqlres = mysqli_query($GLOBALS["link"],$sql);
        while ($row = mysqli_fetch_assoc($sqlres)) {
            $quoteitem = new QuoteItem($row["id"], $this);
			if ((($quoteitem->OldProductID==-1)==($class=="additional"))||($class=="all")){
				$total = $total + $this->getFixedMaintenancePerProduct($quoteitem);
			}
		}
		
		$t1 =  (1 + $this->Settings["fixed_maintenance_per_product_escalation_rate"])/(1+$return);
		//print "Term: ".$this->RentalTerm;
		$c = $total;
		$result = ($c*(1-pow(1+($t1-1),($term/12)+1))/($t1-1))+$c;
		//echo "<br/>1. $c 2. $result ; term : $term; t1 : $t1 ; return : $return<br/>";
		return -1*$result;
	}

	public function getPVCosts($return, $term, $MaintenancePercentage){
		$total = $this->AllCosts;
		//echo "Costs ".$this->AllCosts;
	    $total += $this->getPVInitialExpensesRental("all");
		//echo "Init ".$this->getPVInitialExpensesRental("all");
		$total += $this->getPVFixedMaintenanceContractCost("all",$return,$term);
		//echo "PVFMCC ".$this->getPVFixedMaintenanceContractCost("all",$return,$term);
		$total += $this->getPVFixedMaintenancePerProductCost("all",$return,$term);
		//echo "PVFMPPC ".$this->getPVFixedMaintenancePerProductCost("all",$return,$term);
		//echo "<br/>PC Costs is $total at return $return with $MaintenancePercentage <br/>";
		return $total;
	}
	
	public function getNPVRent($return, $term, $MaintenancePercentage){
		return $this->getPVRent($return, $term, $MaintenancePercentage) - $this->getPVCosts(pow(1+$return,12)-1, $term,$MaintenancePercentage);
	}
	
	public function getReturn($term, $MaintenancePercentage){
		$increment = 0.4;
		$perc =0.4;
		$count=0;
		$goingup=true;
		$this->initRent=-1;
		$val = $this->getNPVRent($perc,$term, $MaintenancePercentage);
		while ((abs($val)>0.001)&&($count<50)){
			$count++;
			$this->initRent=-1;
			if ($val<0){
				$newgoingup=false;
				$perc=$perc-$increment;
			} else {
				$newgoingup=true;
				$perc = $perc+$increment;
			}
			
			if ($goingup!=$newgoingup){
				$increment=$increment/2;
				$goingup = $newgoingup;
			}			
			$val = $this->getNPVRent($perc,$term, $MaintenancePercentage);
			//print "<br/>   icount is $count, NPV is $val, term is $term , maintenance is $MaintenancePercentage<br/>";
		}
		//print "<br/>Return for $term months and $MaintenancePercentage % is $perc and count is $count, NPV is $val<br/>";
		return $perc;
	}

	public function optimalProcedure($targetReturn){
        //print "Target Return is $targetReturn";
        $maintenance = 0;
        $term=12;
        $this->RentalTerm=12;
        $factor = $this->Settings["smaller_than_factor"];
        $this->initRent=-1;
        $return=$this->getReturn($term, $maintenance);
   
        /*error_reporting(E_ALL);     
        print "test3";
        echo "<br/>";
        $return=$this->getReturn(12,0);
        echo "Return = $return <br/>";
        $var12 = ($this->getRentalAmount("replacement",$term,$this->MaintenancePercentage))/$this->MonthlyCostSaving;
        $var13 = $this->getRentalAmount("replacement",$term,$this->MaintenancePercentage);
        echo "get rental factor = $var12<br/>";
        echo "get rental amount = $var13<br/>";
        echo "factor = $factor <br/>";
        echo "MonthlyCostSaving = $this->MonthlyCostSaving <br/>";*/
        
        if ($return>=$targetReturn){
            if ((($this->getRentalAmount("replacement",$term,$maintenance)/$this->MonthlyCostSaving)<=$factor)){
                $this->MaintenancePercentage=$maintenance;
                //print "cp1";
                $this->RentalTerm=$term;
                //print "test1a";
                
                return;
            } else {
                //print "test1b";
                $maintenance=0;
                $this->RentalTerm=24;
                $term = 24;
            };
        } else {
            $maintenance=$this->getMaintenance($term, $targetReturn);
            if ((($this->getRentalAmount("replacement",$term,$maintenance)/$this->MonthlyCostSaving)<=$factor)){
                $this->MaintenancePercentage=$maintenance;
                $this->RentalTerm=$term;
                //print "cp2";
                //print "test2a";
                
                return;
            } else {
                //print "test2b";
                
                $maintenance=0;
                $this->RentalTerm=24;
                $term = 24;
            };          
        }
        
        $this->initRent=-1;
        $return=$this->getReturn($term, $maintenance);
        if ($return>=$targetReturn){
            if ((($this->getRentalAmount("replacement",$term,$maintenance)/$this->MonthlyCostSaving)<=$factor)){
                $this->MaintenancePercentage=$maintenance;
                $this->RentalTerm=$term;
                //print "cp3";
                return;
            } else {
                $maintenance=0;
                $this->RentalTerm=36;
                $term = 36;
            };
        } else {
            $maintenance=$this->getMaintenance($term, $targetReturn);
            if (($this->getRentalAmount("replacement",$term,$maintenance)/$this->MonthlyCostSaving)<=$factor){
                $this->MaintenancePercentage=$maintenance;
                $this->RentalTerm=$term;
                //  print "cp4";
                return;
            } else {
                $maintenance=0;
                $this->RentalTerm=36;
                $term = 36;
            };          
        }
        $this->initRent=-1;     
        $return=$this->getReturn($term, $maintenance);
        if ($return>=$targetReturn){
            //  print "cp5";
            $this->MaintenancePercentage=$maintenance;
            $this->RentalTerm=$term;
            return;
        } else {
            //print "In last method<br/>";
            //  print "cp6";
            $this->MaintenancePercentage=$this->getMaintenance($term, $targetReturn);
            $this->RentalTerm = 36;
            //print "Actual Return in method is ".$this->getReturn($this->RentalTerm, $this->MaintenancePercentage."<br/>");
            //print "<br/>Perc is ".$this->MaintenancePercentage;
            //print "<br/>Term is ".$this->RentalTerm;
        };
        
    }
	
	public function getMaintenance($term,$targetReturn){
		//print "Getting maintenance";
		$perc = 0.5;
		$increment = 0.5;
		$count=0;
		$goingup=true;
		$val = $this->getReturn($term, $perc);
		while ((abs($val-$targetReturn)>0.000001)&&($count<40)){
			$count++;
			if ($val>$targetReturn){
				$newgoingup = false;
				$perc=$perc-$increment;
			} else {
				$newgoingup=true;
				$perc = $perc+$increment;
			}
			if ($goingup!=$newgoingup){
				$increment=$increment/2;
				$goingup = $newgoingup;
			}
			$val = $this->getReturn($term, $perc);
			//print "<br/>mCount : $count ; i : $val"; 
		}
		//print "<br/>Target: $targetReturn<br/>Actual ".$this->getReturn($term,$perc).": <br/>Diff :".(abs($this->getReturn($term,$perc)-$targetReturn." <br/>"));
		//print "<br/>Perc is $perc";
		//print "<br/>Term is $term";
        $perc = ($perc<0 ?  0 : $perc);//this line!!!! you forgot this line!
		return $perc;
	}
	
    private function BuildTables()
    {
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
