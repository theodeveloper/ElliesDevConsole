<!DOCTYPE html>
<html> 
  <head> 
  </head>
	<body>    
    <form action="ElliesPDF.php" name="reqform" method="post" enctype="multipart/form-data">

       <h2>Front Page</h2>
       <label>Company name:</label>
       <br/>
       <input type="text" id="company_name" name="company_name" value="" >
       <br/><br/>

       <h2>Second Page</h2>

       <h3>Project Details</h3>
       <label>Type of Premises:</label>
       <br/>
       <input type="checkbox" id="business" name="business" value="business">Business
       <input type="checkbox" id="home" name="home" value="home">Home
       <input type="checkbox" id="other" name="other" value="other">Other
       <br/>

      <label>Street Address:</label>
      <br/>
      <input type="text" id="street_address" name="street_address" value="" >
      <br/>

      <label>Telephone Number:</label>
      <br/>
      <input type="text" id="tel_number" name="tel_number" value="" >
      <br/>

      <label>Contact Person:</label>
      <br/>
      <input type="text" id="contact_name" name="contact_name" value="" >
      <br/>

      <label>Telephone Number:</label>
      <br/>
      <input type="text" id="tel_contact_number" name="tel_contact_number" value="" >
      <br/>  
      <label>Email:</label>
      <br/>
      <input type="text" id="email" name="email" value="" >
      <br/>

      <label>Date:</label>
      <br/>
      <input type="date" id="date" name="date" value="">
      <br/>

      <h3>Additional Information</h3>
      <label>Project Manager:</label>
      <br/>
      <input type="text" id="project_manager" name="project_manager" value="" >
      <br/>

      <label>Installer:</label>
      <br/>
      <input type="text" id="installer" name="installer" value="" >
      <br/>
      <label>Sales Rep:</label>
      <br/>
      <input type="text" id="sales_rep" name="sales_rep" value="" >
      <br/>

      <label>Electricity Supplier:</label>
      <br/>
      <input type="text" id="electricity_supplier" name="electricity_supplier" value="" >
      <br/>
      <label>Cost per kWh:</label>
      <br/>
      <input type="text" id="cost" name="cost" value="" >
      <br/>

      <label>Region:</label>
      <br/>
      <input type="text" id="region" name="region" value="" >
      <br/>
      <label>Type of Connection</label>
      <br/>
      <input type="checkbox" id="singe" name="single_phase" value="Single Phase">Single Phase
      <input type="checkbox" id="three_phase" name="three_phase" value="Three Phase">Three Phase
      <br/>

      <label>Meter Number:</label>
      <br/>
      <input type="text" id="meter_number" name="meter_number" value="" >
      <br/>

      <h3>Consumption Data and Installation Details</h3>
      <label>Electricity Accounts:</label>
      <br/>
      <input type="text" id="electricity_accounts" name="electricity_accounts" value="" >
      <br/>

       <label>Travel (km):</label>
      <br/>
      <input type="text" id="travel" name="travel" value="" >
      <br/>

      <label>Ceiling Height:</label>
      <br/>
      <input type="text" id="height" name="height" value="" >
      <br/>

      <label>Contingency:</label>
      <br/>
      <input type="text" id="contingency" name="contingency" value="">
      <br/>

      <label>Other Expenses:</label>
      <br/>
      <input type="checkbox" id="access_equipment" name="access_equipment" value="access_equipment">Access Equipment
      <input type="checkbox" id="coc" name="coc" value="coc">COC
      <br/>

      <label>Installation:</label>
      <br/>
      <input type="checkbox" id="installation" name="installation" value="installation">Installation
      <br/>

      <label>Notes:</label>
      <br/>
      <input type="text" id="notes" name="notes" value="">
      <br/>
      <!---end of second page-->
      <h2>Third Page</h2>
      <h3>Scope of Works</h3>
    
      <?php
            $records  = "";
            $type = 0;
            $area = "Ground Floor";
            for ($x = 0; $x < 17; $x++)
            {   
                  if($x  % 5 == 0)  
                  {
                     $type = 1;    
                  }else{
                    $type++;
                    //echo $type;
                     switch ($type) {
                       case 1:
                         $area = "Ground Floor";
                         break;
                       case 2:
                        $area = "Ground boardroom";
                         break;
                       case 3:
                         $area = "Ground bathroom";
                         break;
                       case 4:
                         $area = "Ground patio";
                         break;
                       case 5:
                         $area = "Ground office";
                         break;
                       
                       default:
                         $area = "Ground Floor";
                         break;
                     }                  
                  }
                  $records .= $area .$type .",Light bulbs" .$type. "," .$type. ",Lamps".$type.",".$type.",".$type.",".$type.";";
                  //echo $records .'<br/>';
            }
            //exit;
      ?>  
      <textarea id="records" name="records" rows="10" style="width:800px" ><?php echo $records ?></textarea>
      <!---end of third page-->
      <h2>Fourth Page</h2>
      <h3>Financial Analysis</h3>

      <label>Demand for Lighting per hour:</label>
      <br/>
      <label>Before:</label>
      <input type="text" id="lighting_before_value" name="lighting_before_value">
      <br/>
      <label>After:</label>
      <input type="text" id="lighting_after_value" name="lighting_after_value" value="">
      <br/>

      <label>Electricity Consumption per day:</label>
      <br/>
      <label>Before:</label>
      <input type="text" id="electric_before_value" name="electric_before_value" value="">
      <br/>
      <label>After:</label>
      <input type="text" id="electric_after_value" name="electric_after_value" value="">
      <br/>

      <label>Monthly Cost:</label>
      <br/>
      <label>Before:</label>
      <input type="text" id="cost_before_value" name="cost_before_value" value="">
      <br/>
      <label>After:</label>
      <input type="text" id="cost_after_value" name="cost_after_value" value="">
      <br/><br/>
      
      <label>Project Cost ex VAT:</label>
       <br/>
      <input type="text" id="project_cost" name="project_cost" value="">
      <br/>
    
      <label>Expected Monthly Saving:</label>
       <br/>
      <input type="text" id="month_saving" name="month_saving" value="">
      <br/>
      
      <label>% Saving:</label>
       <br/>
      <input type="text" id="saving" name="saving" value="">
      <br/><br/>
      <!---end of third page-->
      <h2>Fifth Page</h2>
      <h3>Quote Acceptance</h3>
      <label>Name:</label>
       <br/>
      <input type="text" id="name" name="name" value="">
      <br/>

      <label>Capacity:</label>
       <br/>
      <input type="text" id="capacity" name="capacity" value="">
      <br/>

      <label>Signed at:</label>
       <br/>
      <input type="text" id="signat" name="signat" value="">
      <br/>

      <label>On this:</label>
       <br/>
      <input type="text" id="onthis" name="onthis" value="">
      <br/>

      <label>day of:</label>
       <br/>
      <input type="text" id="dayof" name="dayof" value="">
      <br/>

      <label>For the Customer:</label>
       <br/>
      <input type="text" id="forcustomer" name="forcustomer" value="">
      <br/>
      <label>For the Company: Ellies :</label>
       <br/>
      <input type="text" id="forcompany" name="forcompany" value="">
      <br/><br/>
      <h2>Sixth Page</h2>
      <h3>Job Card</h3>
       <label>Job Card Items:</label>
       <br/>
      <?php
            $records  = "";
            for ($x = 0; $x < 12; $x++)
            {
                  $records .= "FFCCQWER" .$x .",Light bulbs" .$x. ",".$x.",".$x.",".$x.";";
            }
      ?>  
      <textarea id="jobcard" name="jobcard" rows="10" style="width:800px" ><?php echo $records ?></textarea>
      <br/>


      <label>Project signed off on:</label>
       <br/>
      <input type="date" id="signproject" name="signproject" value="">
      <br/>
      <label>For Installer:</label>
       <br/>
      <input type="text" id="forinstaller" name="forinstaller" value="">
      <br/>

       <label>Returned stock:</label>
       <br/>
       <?php
            $records  = "";
            for ($x = 0; $x < 12; $x++)
            {
                  if($x  % 2 == 0){
                     $used = "Yes";   
               }else{$used = "No";}
                  
                  $records .= "FFCCQWER" .$x .",Light bulbs" .$x. ",".$used.";";
            }
      ?>  
      <textarea id="returnedstock" name="returnedstock" rows="10" style="width:800px" ><?php echo $records ?></textarea>

      <p><input type="submit" name="commit" value="Submit"></p>
    </form>                  
      <!--End Form-->                   
	</body>
</html>