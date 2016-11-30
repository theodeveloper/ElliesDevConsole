<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" href = "style.css" />
<title>New Audit</title>
<style>
body {
	padding: 0px;
	margin: 0px;
}

.icon_heading {
}
h2 {
	
}
#container {
	margin: 10px;
}
td {
	padding-top: 4px;
	padding-bottom: 4px;
}
.heading {
	padding-left: 25px;
}
.quote_vals, .customer_vals {
	padding-left: 30px;
}
.customer_icon {
	padding-left: 150px;
}

.icon_heading {
	color: rgb(51, 51, 51);
}

.icon_description {
	color: #989899;
	font-size:120%;
}
#overview {
	width: 100%;
	display: table;
	border-spacing: 5px;
	margin-bottom:60px;
}
.total_icon {
	display: table-cell;
	text-align: center;
	border-right: 1px solid #959f83;
	margin-top: 80px;
	width: 20%;
}
.total_icon h2{
	font-size:190%;
}
.total_icon:last-child{ 
	border:none;
}

.inner_icon{
	/*margin: 5px 0px 5px 0px;*/
}

#savings, #losses{
	width: 100%;
	display: table;
	border-spacing: 5px;
	margin-bottom:60px;
}

.savings_icon, .losses_icon{
	display: table-cell;
	text-align: center;
	border-right: 1px solid #959f83;
	width: 16.6%;
	vertical-align:top;    

}

.savings_icon:last-child, .losses_icon:last-child{ 
	border:none;
}

.savings_icon p, .losses_icon p{
	color: #989899;
	margin-top:0;
    margin-bottom:5px;
	color:black;
	text-align:center;
}
.savings_icon:first-child p, .losses_icon:first-child p{
	color: #989899;
}

.numberCircle {
    -webkit-border-radius: 999px;
    -moz-border-radius: 999px;
    border-radius: 999px;
    behavior: url(PIE.htc);

    width: 80px;
    height: 80px;
    border: 5px solid #989899;
    text-align: center;
	margin: 0 auto;
	margin-bottom:10px;
    
}

.numberCircle h1{
	font-weight:normal;
	font-size:200%;
	color: #989899;	
}

.savings_icon:nth-child(n+2) .numberCircle h1, .losses_icon:nth-child(n+2) .numberCircle h1{ 
	font-size:400%;
	color: #989899;
	margin:0;
	padding:0;
	height:80px;
	width:80px;
	text-align:center;
}

#savings_year1, #savings_year1 h1{
	color:#70AA00;
	border-color:#70AA00;
}

#savings_year2, #savings_year2 h1{
	color:#629400;
	border-color:#629400;
}

#savings_year3, #savings_year3 h1{
	color:#527D00;
	border-color:#527D00;
}

#savings_year4, #savings_year4 h1{
	color:#4A6C01;
	border-color:#4A6C01;
}

#savings_year5, #savings_year5 h1{
	color:#416500;
	border-color:#416500;
}

</style>
</head>

<body>
<div id="container">
  <div> <img src="images/details_icon.png" style="float:left; padding-right:20px;" width="160px"/>
    <table>
      <tr>
        <td align="center" valign="middle"><img src="images/ellies_icon.png"  width="30"/></td>
        <td align="left" class="heading">Electricity Escalation Rate:</td>
        <td align="left" class="quote_vals" valign="middle">8%</td>
        <td align="left" class="customer_icon" valign="middle"><img src="images/name_icon.png"  width="30"/></td>
        <td align="left" class="customer_vals" valign="middle">Daniel Widmonte</td>
      </tr>
      <tr>
        <td align="center" valign="middle"><img src="images/ellies_icon.png" /></td>
        <td align="left" class="heading">Quote Reference:</td>
        <td align="left" class="quote_vals" valign="middle">INQWE234</td>
        <td align="left" class="customer_icon" valign="middle"><img src="images/phone_icon.png" width="30"/></td>
        <td align="left" class="customer_vals" valign="middle">0836262827</td>
      </tr>
      <tr>
        <td align="center" valign="middle"><img src="images/ellies_icon.png" /></td>
        <td align="left" class="heading">Price/kWh R:</td>
        <td align="left" class="quote_vals" valign="middle">R1.2</td>
        <td align="left" class="customer_icon" valign="middle"><img src="images/email_icon.png" /></td>
        <td align="left" class="customer_vals" valign="middle">woodydaniel@asfsd.com</td>
      </tr>
      <tr>
        <td align="center" valign="middle"><img src="images/ellies_icon.png"  width="30"/></td>
        <td align="left" class="heading">Date:</td>
        <td align="left" class="quote_vals" valign="middle">2013-11-20  15:20:35</td>
        <td align="left" class="customer_icon" valign="middle"><img src="images/property_icon.png" width="30"/></td>
        <td align="left" class="customer_vals" valign="middle">private residence</td>
      </tr>
    </table>
  </div>
  <h1 class="icon_heading">Overview</h1>
  <div id="overview">
    <div class="total_icon"><div class="inner_icon"><img src="images/clock_icon.png" width="100"/>
      <h2>34 Months</h2>
      <span class="icon_description">Payback Period</span></div></div>
    <div class="total_icon"><div class="inner_icon"><img src="images/plus_icon.png"  width="100"/>
      <h2>R100</h2>
      <span class="icon_description">Monthly Saved</span></div></div>
    <div class="total_icon"><div class="inner_icon"><img src="images/percentage_icon.png" width="100" />
      <h2>80%</h2>
      <span class="icon_description">Percentage Saved</span></div></div>
    <div class="total_icon"><div class="inner_icon"><img src="images/years_icon.png"  width="100"/>
      <h2>R2545</h2>
      <span class="icon_description">Savings after 5 years</span></div></div>
    <div class="total_icon"><div class="inner_icon"><img src="images/tick_icon.png" width="100"/>
      <h2>R7119</h2>
      <span class="icon_description">Total Cost</span></div></div>
  </div>
  <h1 class="icon_heading">Projected Savings</h1>
  <div id="savings">
  	<div class="savings_icon">
    	<div class="inner_icon">
        	<div class="numberCircle"><h1>Year</h1></div>
            <p>Price per kWh</p>
            <p>Cumulative Savings</p>
            <p>Annual Savings</p>
            <p>Monthly Savings</p>
        </div>
    </div>
  	<div class="savings_icon">
    	<div class="inner_icon">
        	<div id="savings_year1" class="numberCircle"><h1>1</h1></div>
            <p>R0.00</p>
            <p>R0.00</p>
         	<p>R0.00</p>
            <p>R0.00</p>
        </div>
    </div>
  	<div class="savings_icon">
    	<div class="inner_icon">
        	<div id="savings_year2" class="numberCircle"><h1>2</h1></div>
               <p>R0.00</p>
            <p>R0.00</p>
         	<p>R0.00</p>
            <p>R0.00</p>
        </div>
    </div>
  	<div class="savings_icon">
    	<div class="inner_icon">
        	<div id="savings_year3" class="numberCircle"><h1>5</h1></div>
               <p>R0.00</p>
            <p>R0.00</p>
         	<p>R0.00</p>
            <p>R0.00</p>
        </div>
    </div>
  	<div class="savings_icon">
    	<div class="inner_icon">
        	<div id="savings_year4" class="numberCircle"><h1>8</h1></div>
               <p>R0.00</p>
            <p>R0.00</p>
         	<p>R0.00</p>
            <p>R0.00</p>
        </div>
    </div>
  	<div class="savings_icon">
    	<div class="inner_icon">
        	<div id="savings_year5" class="numberCircle"><h1>10</h1></div>
            <p>R0.00</p>
            <p>R0.00</p>
         	<p>R0.00</p>
            <p>R0.00</p>
        </div>
    </div>
  </div>
  <h1 class="icon_heading">Projected Losses</h1>
  <div id="losses">
  	<div class="losses_icon">
    	<div class="inner_icon">
        	<div class="numberCircle"><h1>Year</h1></div>
            <p>Price per kWh</p>
            <p>Cumulative losses</p>
            <p>Annual losses</p>
            <p>Monthly losses</p>
        </div>
    </div>
  	<div class="losses_icon">
    	<div class="inner_icon">
        	<div id="year1" class="numberCircle"><h1>1</h1></div>
            <p>R0.00</p>
            <p>R0.00</p>
         	<p>R0.00</p>
            <p>R0.00</p>
        </div>
    </div>
  	<div class="losses_icon">
    	<div class="inner_icon">
        	<div id="year2" class="numberCircle"><h1>2</h1></div>
               <p>R0.00</p>
            <p>R0.00</p>
         	<p>R0.00</p>
            <p>R0.00</p>
        </div>
    </div>
  	<div class="losses_icon">
    	<div class="inner_icon">
        	<div id="year3" class="numberCircle"><h1>5</h1></div>
               <p>R0.00</p>
            <p>R0.00</p>
         	<p>R0.00</p>
            <p>R0.00</p>
        </div>
    </div>
  	<div class="losses_icon">
    	<div class="inner_icon">
        	<div id="year4" class="numberCircle"><h1>8</h1></div>
               <p>R0.00</p>
            <p>R0.00</p>
         	<p>R0.00</p>
            <p>R0.00</p>
        </div>
    </div>
  	<div class="losses_icon">
    	<div class="inner_icon">
        	<div id="year5" class="numberCircle"><h1>10</h1></div>
            <p>R0.00</p>
            <p>R0.00</p>
         	<p>R0.00</p>
            <p>R0.00</p>
        </div>
    </div>
  </div>
</div>
</body>
</html>