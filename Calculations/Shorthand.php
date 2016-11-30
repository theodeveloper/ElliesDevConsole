<?php
function Procedure()
{
	$monthlyRental;
	$PercentageHolder;
	$Maintenance_Percentage_Price=0;
	$Rental_Term=12;

	
	if($Total_Saved_KWH_ALL<=0)
	{
		$PercentageHolder = $Maintenance_Percentage_Price;
		if($Maintenance_Percentage_Price<0)
			$Maintenance_Percentage_Price=0
		//end
	}
	for(int $i = 0; $i<2; $i++)
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
				$Rental_Term+=12;
			}
		}
		else
		{
			//target return calculation
			if($Total_Saved_KWH_ALL<=0)
			{
				$PercentageHolder = $Maintenance_Percentage_Price;
				if($Maintenance_Percentage_Price<0)
					$Maintenance_Percentage_Price=0
				//end
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
				$Rental_Term+=12;
			}
		}
	}
	//calculate Rental Amounts and returns
	if($Return_Rental_Contract>=$Target_Return_Percentage)
	{
		$monthlyRental=$Rental_Amount;
		//exit Procedure
	}
	else
	{
		//target return calculation
		if($Total_Saved_KWH_ALL<=0)
		{
			$PercentageHolder = $Maintenance_Percentage_Price;
			if($Maintenance_Percentage_Price<0)
				$Maintenance_Percentage_Price=0
			//end
		}
		//calculate Rental amounts and Returns
		$monthlyRental=$Rental_Amount;
		//exit procedure
	}
}
?>