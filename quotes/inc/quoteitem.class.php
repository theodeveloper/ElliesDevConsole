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
class QuoteItem {
    public $ELECTRICITY = 1;
    public $WATER = 2;
    public $QuoteItemID = 0;
    public $QuoteID = 0;
    public $TechID = 0;
    public $TechTypeID = 0;
    public $Type = 0;
	public $OriginalPrice = 0;
    //public $TechType = "";
    public $Qty = 0;
	public $NewQty = 0;
    public $Room = "";
    public $SessionID = "";
    public $CostSavings = 0;
    public $Exists = false;
    public $InputValues = NULL;
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
	public $Cost = 0;
    public $KWhPrice = 1.20;
    public $ElectricityEscalationRate = 12.00;
    public $PaybackPeriod = 0;
    public $PaybackPeriodMonths = 0;
    public $PaybackPeriodFormatted = "";
    public $ReplacementTime = "";
    public $NewProductID = 0;
    public $OldProductID = 0;
    public $ItemDescription = "";
    public $ItemPrice = 0;
    public $ItemTotal = 0;
    public $NewTechnologyType = "";
    public $ProductCode = "";
    public $ClassOfProduct = "";	
    public $RelatedTo = "";
    public $UsesKW = false;
    public $NewKWh = 0;
    public $NewLitres = 0;
    public $OldKWh = 0;
    public $OldLitres = 0;
    public $TechType = "";
    public $OldProductName = "";
    public $TempPrice = 0;
    public $Channel = 0;
	public $FixedMaintenance = 0;
	public $MaterialsNew = 0;
    public $Quote;

    public $Old_Litre_measurement = 0;
    public $Average_Shower_Time = 0;
    public $Number_of_uses_per_day_each;
    
    public function __construct ($quoteitemid = 0, $Quote) {
        $this->QuoteItemID = $quoteitemid;
        $this->Channel = $Quote->Channel;
        $this->Quote = $Quote;
        $this->LoadData();
    }
	
	public function getOldRetailLoss(){
		//echo "STUFF".$this->Channel."~~~";
		//echo "Select `table` from old_products_".$this->Channel." where id=".$this->QuoteItemID;
		$result = mysqli_query($GLOBALS["link"],"Select `table` from old_products where id=".$this->QuoteItemID);
		$row = mysqli_fetch_array($result);
		if ($row[0]=="fluorescent"){
			return 0.15;
		}else {
			return 0;
		}
	}
	
	public function getLoss($ismagnetic){
		if ($ismagnetic==1){
		    return 0;
		} else if ($ismagnetic==0){
			return $this->getOldRetailLoss();
		} else if ($ismagnetic==2){
			$sql= "SELECT ballast_loss_electric from rental";
			$sqlres = mysqli_query($GLOBALS["link"],$sql);
			$row = mysqli_fetch_assoc($sqlres);
			return $row["ballast_loss_electric"];
		} else if ($ismagnetic==3){
			$sql= "SELECT ballast_loss_magnetic from rental";
			$sqlres = mysqli_query($GLOBALS["link"],$sql);
			$row = mysqli_fetch_assoc($sqlres);
			return $row["ballast_loss_magnetic"];
		}
		return 0;
	}
   
    public function LoadData() {
        $sql = "SELECT * FROM `quote_items` WHERE `id` = '".mysqli_real_escape_string($GLOBALS["link"],$this->QuoteItemID)."'";
        $sqlres = mysqli_query($GLOBALS["link"],$sql);
        while($row = mysqli_fetch_assoc($sqlres)) {
            $this->QuoteID = $row["quote_id"];
			$isMagnetic = $row["ismagnetic"];
			$loss = $this->getLoss($isMagnetic);
            $this->Qty = $row["qty"];
			$this->NewQty = $row["newqty"];
            $this->Room = $row["room"];
			
            $this->NewProductID = $row["new_product_id"];
            $this->OldProductID = $row["old_product_id"];
            $this->Exists = true;
            $this->Type = $row["type"];
            if ($this->Type == 2){
                //Electric
                $this->InputValues["Hours_Per_Day"] = $row["param1"];
                $this->InputValues["Days_Per_Week"] = $row["param2"];
                $this->InputValues["Weeks_Per_Year"] = $row["param3"];
            }
          
            if ($this->Type == 1){
                //"Water";
                //$this->InputValues["Old_Litre_measurement"] = $row["param1"];
                //$this->InputValues["Average_Shower_Time"] = $row["param2"];
                //$this->InputValues["Number_of_uses_per_day_each"] = $row["param3"];
                
                $this->Old_Litre_measurement= $row["param1"];
                $this->Average_Shower_Time= $row["param2"];
                $this->Number_of_uses_per_day_each= $row["param3"];
            }
            $this->TempPrice = $row["newprice"];
        }
       
        if ($this->NewProductID > 0) {
            $q = $this->Quote;
      
            $this->ItemPrice = ($this->TempPrice * (100-$q->Discount))/100;

            $sql = "SELECT * FROM `new_products` WHERE `id` = '".mysqli_real_escape_string($GLOBALS["link"],$this->NewProductID)."' LIMIT 0, 1";
            $sqlres = mysqli_query($GLOBALS["link"],$sql);
            //echo $sql."<br/>";
            while($row = mysqli_fetch_assoc($sqlres)) {
                $this->ProductCode =$row["code"];
                $this->ItemDescription = $row["product"];
                $this->NewTechnologyType = $row["class_of_product"];
                $this->ClassOfProduct = $row["class_of_product"];
                $this->RelatedTo = $row["related_to"];
                $this->UsesKW = $row["kw_yn"];
                $this->NewKWh = $row["replacement_kwh_actual"];
                $this->NewLitres = $row["replacement_litres"];
                $this->TechType = $row["class_of_product"];
                $this->FixedMaintenance = $row["maintenance_cost"] * $this->NewQty;
             
                if ($this->NewQty > 0) {
                    $this->ItemTotal = $this->ItemPrice * $this->NewQty;
                    $this->OriginalPrice = $this->TempPrice * $this->NewQty;
                    $this->Cost = $row["cost"] * $this->NewQty;
                }
            }
        }

        if ($this->OldProductID > 0) {
            $sql = "SELECT * FROM `old_products` WHERE `id` = '".mysqli_real_escape_string($GLOBALS["link"],$this->OldProductID)."' LIMIT 0, 1";
            $sqlres = mysqli_query($GLOBALS["link"],$sql);
            while($row = mysqli_fetch_assoc($sqlres)) {
                $this->OldKWh = $row["existing_kwh"]* (1+$loss);
                $this->OldLitres = $row["existing_litres"];
                $this->OldProductName = $row["product"];
            }
        }
        
        $sql = "SELECT `kwh_price`, `escalation_rate` FROM `quotes` WHERE `id` = '".mysqli_real_escape_string($GLOBALS["link"],$this->QuoteID)."' LIMIT 0, 1";
        $sqlres = mysqli_query($GLOBALS["link"],$sql);
        while($row = mysqli_fetch_assoc($sqlres)) {
            if ($row["kwh_price"] > 0) {
                $this->KWhPrice = $row["kwh_price"];
            }
            if ($row["escalation_rate"] > 0) {
                $this->ElectricityEscalationRate = $row["escalation_rate"];
            }
        }
        
        // echo "test3";
        if ($this->Exists) {
           // $this->GetInputValues();
            //$this->CalculateCostSavings(); need to be Fix urgent
        }
        //echo "test4";
    }
    
    public function GetInputValues() {
        return $this->InputValues;
    }
    
    public function Save() {
        if (!$this->Exists) {
            $this->SessionID = session_id();
            $sql = "INSERT INTO `quote_items` (`date_created`, `quote_id`, `session_id`) VALUES (NOW(), '".mysqli_real_escape_string($GLOBALS["link"],$this->QuoteID)."', '".mysqli_real_escape_string($GLOBALS["link"],$this->SessionID)."')";
            mysqli_query($GLOBALS["link"],$sql);
            $this->QuoteItemID = mysql_insert_id();
            $this->Exists = true;
        }
        if ($this->Exists) {
            $sql = "UPDATE `quote_items` SET";
            $sql.= " `qty` = '".mysqli_real_escape_string($GLOBALS["link"],$this->Qty)."'";
            $sql.= ", `room` = '".mysqli_real_escape_string($GLOBALS["link"],$this->Room)."'";
            $sql.= ", `quote_id` = '".mysqli_real_escape_string($GLOBALS["link"],$this->QuoteID)."'";
            $sql.= ", `tech_id` = '".mysqli_real_escape_string($GLOBALS["link"],$this->TechID)."'";
            $sql.= ", `new_product_id` = '".mysqli_real_escape_string($GLOBALS["link"],$this->NewProductID)."'";
            $sql.= ", `old_product_id` = '".mysqli_real_escape_string($GLOBALS["link"],$this->OldProductID)."'";
            $sql.= ", `cost_savings` = '".mysqli_real_escape_string($GLOBALS["link"],$this->CostSavings)."'";
            $sql.= " WHERE `id` = '".mysqli_real_escape_string($GLOBALS["link"],$this->QuoteItemID)."'";
            mysqli_query($GLOBALS["link"],$sql);

            $sql = "UPDATE `quotes` SET `complete` = 0 WHERE `id` = '".mysqli_real_escape_string($GLOBALS["link"],$this->QuoteID)."'";
            mysqli_query($GLOBALS["link"],$sql);
        }
        
        $sql = "DELETE FROM `quote_items_input` WHERE `quote_item_id` = '".mysqli_real_escape_string($GLOBALS["link"],$this->QuoteItemID)."'";
        mysqli_query($GLOBALS["link"],$sql);

        foreach($this->InputValues as $key=>$value) {
            $sql = "INSERT INTO `quote_items_input` (`quote_item_id`, `input_key`, `input_value`)";
            $sql.= " VALUES ('".mysqli_real_escape_string($GLOBALS["link"],$this->QuoteItemID)."', '".mysqli_real_escape_string($GLOBALS["link"],$key)."', '".mysqli_real_escape_string($GLOBALS["link"],$value)."')";
            mysqli_query($GLOBALS["link"],$sql);
        }
    }
    
    public function Delete() {
        $sql = "DELETE FROM `quote_items` WHERE `id` = '".mysqli_real_escape_string($GLOBALS["link"],$this->QuoteItemID)."'";
        mysqli_query($GLOBALS["link"],$sql);

        $sql = "DELETE FROM `quote_items_input` WHERE `quote_item_id` = '".mysqli_real_escape_string($GLOBALS["link"],$this->QuoteItemID)."'";
        mysqli_query($GLOBALS["link"],$sql);        
    }
    
    public function AddInputValue($key, $value) {
        $this->InputValues[$key] = $value;
    }
    
    public static function INT($value) {
        //===Return integer part of a fraction===
        $value.=".00";
        $segs = explode(".", $value);
        return $segs[0];
    }
    
    public function CalculateCostSavings($debug_print = false) {
        if($this->InputValues){
           foreach($this->InputValues as $key=>$value) {
                $inputs_array[$key] = $value;
                $evalstr = "\$".$key." = ".$value.";";
                eval($evalstr);
            } 
        }
        
        $Average_Shower_Time = $this->Average_Shower_Time;
        $Old_Litre_measurement = $this->Old_Litre_measurement;
        $Number_of_uses_per_day_each = $this->Number_of_uses_per_day_each;

        if (!isset($Average_Shower_Time)) $Average_Shower_Time=0;
        if (!isset($Old_Litre_measurement)) $Old_Litre_measurement=0;
        if (!isset($Number_of_uses_per_day_each)) $Number_of_uses_per_day_each=1;
        
        if ($this->RelatedTo == "Hours Per Year") {
            if ($this->UsesKW) {
                $this->KWhOld = $this->Qty * ($this->OldKWh * $Hours_Per_Day * $Days_Per_Week * ($Weeks_Per_Year/12));     //FormulaOldHours
                $this->KWhNew = $this->NewQty * ($this->NewKWh * $Hours_Per_Day * $Days_Per_Week * ($Weeks_Per_Year/12));     //FormulaNewHours
				if ($this->Qty != 0){
                $this->KWhSaved = $this->KWhOld - $this->KWhNew;
				} else {
				$this->KWhSaved = 0;
				}
            }
            if ($this->KWhSaved > 0 && $this->KWhOld > 0) {
                //$this->KWhSavedPerc = ($this->KWhSaved/$this->KWhOld) * 100;
                $this->KWhSavedPerc = number_format(($this->KWhSaved/$this->KWhOld) * 100, 0);
            }

            if ($debug_print) {
                print "<br><b>Item Old KWh</b> ".$this->OldKWh;
                print "<br><b>Item New KWh</b> ".$this->NewKWh;
    
                print "<br><b>Old KWh</b> ".$this->KWhOld;
                print "<br><b>New KWh</b> ".$this->KWhNew;
            }
            
            $this->MonthlyCostOld = ($this->KWhPrice * $this->KWhOld);
            $this->MonthlyCostNew = ($this->KWhPrice * $this->KWhNew);
			if ($this->Qty!=0){
            $this->MonthlyCostSaving = $this->MonthlyCostOld - $this->MonthlyCostNew;
				}else {
				$this->MonthlyCostSaving = 0;
				}
            //===Calculate Payback Period===
            if ($this->KWhSaved > 0) {
                $this->PaybackPeriod = CalculatePayback($this->KWhPrice, $this->ElectricityEscalationRate, $this->ItemTotal, $this->KWhSaved);
                $this->PaybackPeriodFormatted = number_format((double)$this->PaybackPeriod, 2)." Months";                
            }else{
                $this->PaybackPeriodFormatted = "No Payback";
            }
        }elseif ($this->RelatedTo == "Number Per Day") {
            if ($this->UsesKW) {
                $this->KWhOld = $this->Qty * ((365.25 / 12) * (50 * $Old_Litre_measurement * 0.5 * 1.16 * $Average_Shower_Time * $Number_of_uses_per_day_each) / 1000);
                $this->KWhNew = $this->NewQty * ($this->NewKWh * ($Number_of_uses_per_day_each * $Average_Shower_Time * (365.25/12)));
				if ($this->Qty!=0){
                $this->KWhSaved = $this->KWhOld - $this->KWhNew;
				}else {
				$this->KWhSaved = 0;
				}
            }
            if ($this->KWhSaved > 0 && $this->KWhOld > 0) {
                $this->KWhSavedPerc = number_format(($this->KWhSaved/$this->KWhOld) * 100, 0);
            }
            if ($debug_print) {
                print "<br><b>Item Old KWh</b> ".$this->OldKWh;
                print "<br><b>Item New KWh</b> ".$this->NewKWh;
    
                print "<br><b>Old KWh</b> ".$this->KWhOld;
                print "<br><b>New KWh</b> ".$this->KWhNew;
            }            
            
            $this->MonthlyCostOld = ($this->KWhPrice * $this->KWhOld);
            $this->MonthlyCostNew = ($this->KWhPrice * $this->KWhNew);
			if ($this->Qty!=0){
            $this->MonthlyCostSaving = $this->MonthlyCostOld - $this->MonthlyCostNew;
			}else {
				$this->MonthlyCostSaving = 0;
				}
            
            $this->LitresOld = ((365.25 / 12) * $Old_Litre_measurement * $Number_of_uses_per_day_each * $Average_Shower_Time) * $this->Qty;
            $this->LitresNew = ($this->NewLitres * $Number_of_uses_per_day_each * $Average_Shower_Time * (365.25/12)) * $this->NewQty;
            $this->LitresSaved = $this->LitresOld - $this->LitresNew;
               
            //===Calculate Payback Period===
            if ($this->KWhSaved > 0) {
                $this->PaybackPeriod = CalculatePayback($this->KWhPrice, $this->ElectricityEscalationRate, $this->ItemTotal, $this->KWhSaved);
                $this->PaybackPeriodFormatted = number_format($this->PaybackPeriod, 2)." Months";                
            }else{
                $this->PaybackPeriodFormatted = "No Payback";
            }
        }
    }
    
    public function PrintCostSummary() {
        $this->CalculateCostSavings(); 

        if ($this->UsesKW) {
            print "<table width='100%'><tr><td width='33%' class='ui-body-b' style='font-size:11px !important'>";
            print "<b>Old kWh/Month:</b> <span class='price-old'>".number_format($this->KWhOld, 2)."</span>";
            print "<br><b>New kWh/Month:</b> <span class='price-new'>".number_format($this->KWhNew, 2)."</span>";
            print "<br><b>Saved kWh/Month:</b> <span class='price-saving'>".number_format($this->KWhSaved, 2)." (".number_format($this->KWhSavedPerc, 0)."%)</span>";
            print "<br><b>Payback Period (months):</b> ".$this->PaybackPeriod."</span>";
            
            print "</td><td width='33%' class='ui-body-b' style='font-size:11px !important'>";

            print "<b>Old Monthly Cost:</b> <span class='price-old'>R ".number_format($this->MonthlyCostOld, 2)."</span>";
            print "<br><b>New Monthly Cost:</b> <span class='price-new'>R ".number_format($this->MonthlyCostNew, 2)."</span>";
            print "<br><b>Monthly Saving:</b> <span class='price-saving'>R ".number_format($this->MonthlyCostSaving, 2)."</span>";
            
            print "</td><td width='33%' class='ui-body-b' style='font-size:11px !important'>";
            
            foreach($this->GetInputValues() as $key=>$value) {
                $key = str_replace("_", " ", $key);
                print "<b>".$key.":</b> <span class=''>".$value."</span><br>";
            }
            
            print "</td></tr></table>";
        }else{
            
            print "<table width='100%'><tr><td width='33%' class='ui-body-b' style='font-size:11px !important'>";
            print "<b>Old kWh/Month:</b> <span class='price-old'>".number_format($this->KWhOld, 2)."</span>";
            print "<br><b>New kWh/Month:</b> <span class='price-new'>".number_format($this->KWhNew, 2)."</span>";
            print "<br><b>Saved kWh/Month:</b> <span class='price-saving'>".number_format($this->KWhSaved, 2)." (".number_format($this->KWhSavedPerc, 0)."%)</span>";
            print "<br><b>Payback Period (months):</b> ".$this->PaybackPeriod."</span>";

            print "</td><td width='33%' class='ui-body-b' style='font-size:11px !important'>";

            print "<b>Old Monthly Cost:</b> <span class='price-old'>R ".number_format($this->MonthlyCostOld, 2)."</span>";
            print "<br><b>New Monthly Cost:</b> <span class='price-new'>R ".number_format($this->MonthlyCostNew, 2)."</span>";
            print "<br><b>Monthly Saving:</b> <span class='price-saving'>R ".number_format($this->MonthlyCostSaving, 2)."</span>";
            
            print "</td><td width='33%' class='ui-body-b' style='font-size:11px !important'>";
            
            foreach($this->GetInputValues() as $key=>$value) {
                $key = str_replace("_", " ", $key);
                print "<b>".$key.":</b> <span class=''>".$value."</span><br>";
            }
            
            print "</td></tr></table>";
        }
    }
   
}
?>
