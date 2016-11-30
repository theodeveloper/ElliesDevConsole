<?php
echo "Begin calculations";

function Return()
{
	
}


function TargetReturn()
{

}

//Calculation of the Optimal procedure 
function ReturnCalculation()
{
	$monthlyRental;
	$PercentageHolder;
	$Maintenance_Percentage_Price=0;
	$Rental_Term=12;

	//Calculation of the Optimal procedure
	if($Total_Saved_KWH_ALL<=0)
	{
		//Target return calculation


		
		$PercentageHolder=$Maintenance_Percentage_Price;
		if($Maintenance_Percentage_Price<0)
		{
			$Maintenance_Percentage_Price=0;
		}
		else
		{
			$Maintenance_Percentage_Price = $PercentageHolder;
		}
		//calculate Rental Amounts and Returns
	}
	else
	{
		//calculate Rental Amounts and Returns
		if($Return_Rental_Contract>=$Target_Return_Percentage)
		{
			if($Rental_Amount_replacement/($Total_Saved_KWH_ALL*$KWh_Price)<=$Smaller_Than_Factor)
			{
				$monthlyRental=$Rental_Amount;
				//exit procedure
			}
			else
			{
				$Maintenance_Percentage_Price=0;
				$Rental_Term=24;
			}
		}
		else
		{
			//target return calculation
			$PercentageHolder=$Maintenance_Percentage_Price;
			if($Maintenance_Percentage_Price<0)
			{
				$Maintenance_Percentage_Price=0;
			}
			else
			{
				$Maintenance_Percentage_Price = $PercentageHolder;
			}
			//calculate rental amounts and returns
			if($Rental_Amount_replacement/($Total_Saved_KWH_ALL*$KWh_Price)<=$Smaller_Than_Factor)
			{
				$monthlyRental=$Rental_Amount;
				//exit process
			}
			else
			{
				$Maintenance_Percentage_Price=0;
				$Rental_Term=24;
			}
		}
		//// at this stage I just did a copy past and an edit ////
		//calculate rental amounts and returns
		if($Return_Rental_Contract>=$Target_Return_Percentage)
		{
			if($Rental_Amount_replacement/($Total_Saved_KWH_ALL*$KWh_Price)<=$Smaller_Than_Factor)
			{
				$monthlyRental=$Rental_Amount;
				//exit procedure
			}
			else
			{
				$Maintenance_Percentage_Price=0;
				$Rental_Term=36;
			}
		}
		else
		{
			//target return calculation
			$PercentageHolder=$Maintenance_Percentage_Price;
			if($Maintenance_Percentage_Price<0)
			{
				$Maintenance_Percentage_Price=0;
			}
			else
			{
				$Maintenance_Percentage_Price = $PercentageHolder;
			}
			//calculate rental amounts and returns
			if($Rental_Amount_replacement/($Total_Saved_KWH_ALL*$KWh_Price)<=$Smaller_Than_Factor)
			{
				$monthlyRental=$Rental_Amount;
				//exit process
			}
			else
			{
				$Maintenance_Percentage_Price=0;
				$Rental_Term=36;
			}
		}
		/// slightly different ///
		//calculate Rental Amounts and returns
		if($Return_Rental_Contract>=$Target_Return_Percentage)
		{
			$monthlyRental=$Rental_Amount;
			//exit Procedure
		}
		else
		{
			//Target Return Calculations
			$PercentageHolder=$Maintenance_Percentage_Price;
			if($Maintenance_Percentage_Price<0)
			{
				$Maintenance_Percentage_Price=0;
			}
			else
			{
				$Maintenance_Percentage_Price = $PercentageHolder;
			}
			//calculate Rental amounts and Returns
			$monthlyRental=$Rental_Amount;
			//exit procedure
		}
	}
}

public function optimalProcedure($targetReturn){
		//print "Target Return is $targetReturn";
		$maintenance = 0;
		$term=12;
		$this->RentalTerm=12;
		$factor = $this->Settings["smaller_than_factor"];
		$this->initRent=-1;
		$return=$this->getReturn($term, $maintenance);
		//	$this->MaintenancePercentage=$maintenance;
	   	//				$this->RentalTerm=$term;
		//				return;
				//print "cp0";
		//echo "mcs : ".$this->MonthlyCostSaving;
		if ($return>=$targetReturn){
			if ((($this->getRentalAmount("replacement",$term,$maintenance)/$this->MonthlyCostSaving)<=$factor)){
				$this->MaintenancePercentage=$maintenance;
				//print "cp1";
				$this->RentalTerm=$term;
				return;
			} else {
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
				return;
			} else {
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
				//	print "cp4";
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
			//	print "cp5";
			$this->MaintenancePercentage=$maintenance;
			$this->RentalTerm=$term;
			return;
		} else {
			//print "In last method<br/>";
			//print "cp6";
			$this->MaintenancePercentage=$this->getMaintenance($term, $targetReturn);
			$this->RentalTerm = 36;
			//print "Actual Return in method is ".$this->getReturn($this->RentalTerm, $this->MaintenancePercentage."<br/>");
			//print "<br/>Perc is ".$this->MaintenancePercentage;
			//print "<br/>Term is ".$this->RentalTerm;
		};	
	}

?>