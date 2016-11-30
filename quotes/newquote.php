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
require_once("inc/config.php");
require_once("inc/customer.class.php");
require_once("inc/quote.class.php");
require_once("inc/techitem.class.php");
require_once("inc/functions.php");
require_once("../inc/system_user.php");

if (!empty($_GET["step"])) {
	if ($_GET["step"] == "newclick") {
		header("location: logoff.php");
		exit();
	}
}

LoginCheck("customers.php");

function ValidateFormFind() {
	$haserrors = false;
	$strmessage = "";
	if (empty($_POST["CellPhone"])) {
		$haserrors = true;
		print "$('#chkCellPhoneFind').attr('placeholder', 'Cell Phone is required!');\n";
		if (empty($_POST["Email"])) {
			$haserrors = true;
			$strmessage.= "Either a Cell Phone Number or an Email Address is required!\\n";
			print "$('#chkEmailFind').attr('placeholder', 'Email is required!');\n";
		}elseif(strpos($_POST["Email"], ".") < 1 && strpos($_POST["Email"], "@") < 1) {
			$haserrors = true;
			$strmessage.= "Email address is invalid.\\n";
		}
	} else {
		if(strlen($_POST["CellPhone"])!=10){
			$haserrors = true;
			$strmessage.= "The Cell Phone number must be 10 numbers beggining with a 0 [0123456789].\\n";
		}
	}
	if ($haserrors) {
		print "alert('".$strmessage."');\n";
		exit();
	}else{
		return true;
	}
}

function ValidateForm() {
	$haserrors = false;
	$strmessage = "";
	if (empty($_POST["Name"])) {
		$haserrors = true;
		//$strmessage.= "`Name` is required!\\n";
		print "$('#Name').attr('placeholder', 'Name is required!');\n";
	}
	if (empty($_POST["Surname"])) {
		$haserrors = true;
		//$strmessage.= "`Surname` is required!\\n";
		print "$('#Surname').attr('placeholder', 'Surname is required!');\n";
	}	
	if (empty($_POST["Email"])) {
		$haserrors = true;
		//$strmessage.= "`Email` is required!\\n";
		print "$('#Email').attr('placeholder', 'Email is required!');\n";
	}elseif(strpos($_POST["Email"], ".") < 1 && strpos($_POST["Email"], "@") < 1) {
		$haserrors = true;
		//$strmessage.= "`Email` is required!\\n";
		//print "$('#Email').attr('placeholder', 'Email is invalid!');\n";
		$strmessage.= "Email address is invalid.\\n";
	}	
	if (empty($_POST["CellPhone"])) {
		$haserrors = true;
		//$strmessage.= "`Cell Phone` is required!\\n";
		print "$('#CellPhone').attr('placeholder', 'Cell Phone is required!');\n";
	} else {
		if(strlen($_POST["CellPhone"])!=10){
			$haserrors = true;
			$strmessage.= "The Cell Phone number must be 10 numbers beggining with a 0 [0123456789].\\n";
		}
	}
	if ($haserrors) {
		if($strmessage==""){ $strmessage = "All fields are required"; }
		print "alert('".$strmessage."');\n";
		exit();
	}else{
		return true;
	}
}

$booktitle = "New Quote";
$sysuser = new userType($_SESSION["userid"]);
if (!$sysuser->hasPermission("edit_quotes")) {
	header("location: index.php?nopermission=Add%20quotes");
	exit();
}

if (POST("step") == "CheckCellPhone") {
	ValidateFormFind();
	$cellphone = POST("CellPhone");
	$email = POST("Email");
	$customer = new Customer(0, $cellphone, $email);
	if ($customer->Exists) {
		//===Comparison Fields: Used for comparison purposes===
		print "$('#chkName').val('".$customer->Name."');\n";
		print "$('#chkSurname').val('".$customer->Surname."');\n";
		print "$('#chkEmail').val('".$customer->Email."');\n";
		print "$('#chkCellPhone').val('".$customer->CellPhone."');\n"; // added by alec not sure???
		print "$('#CustomerID').val('".$customer->CustomerID."');\n";
		
		//===Input Fields===
		print "$('#Name').val('".$customer->Name."');\n";
		print "$('#Surname').val('".$customer->Surname."');\n";
		print "$('#CellPhone').val('".$customer->CellPhone."');\n";
		print "$('#Email').val('".$customer->Email."');\n";
		print "$('#CustomerID').val('".$customer->CustomerID."');\n";
		print "$('#cellinput').hide();\n";
		print "$('#customerinput').show();\n";
	}else{
		if (!empty($cellphone)) {
			print "$('#CellPhone').val('".$cellphone."');\n";	
		}else{
			//print "$('#CellPhone').removeAttr('readonly');\n";
			//print "$('#CellPhone').removeAttr('disabled');\n";
		}
		//print "$('#CellPhone').val($('#chkCellPhone').val());\n";
		print "$('#Email').val('".$email."');\n";
		print "$('#cellinput').hide();\n";
		print "$('#customerinput').show();\n";
		print "$('#CustomerID').val('0');\n";
		print "$('#btnNewQuote').val('Create New Customer');\n";
	}
	exit();
}

if (POST("step") == "StartNewQuote") {
	ValidateForm();
	$customer = new Customer(POST("CustomerID"),"");
	if (!$customer->Exists) {
		$customer->Name = ucfirst(POST("Name"));
		$customer->Surname = ucfirst(POST("Surname"));
		$customer->Email = POST("Email");
		$customer->CellPhone = POST("CellPhone");
		$customer->CustomerID = POST("CustomerID");
		$customer->CreatedBy = $sysuser->id;
		$customer->Save();
	}
	$_SESSION["customerid"] = $customer->CustomerID;
	//print "document.location.href='editquote.php?id=0&customerid=".$customer->CustomerID."&isnew=1';\n";
	print "document.location.href='quotes.php?customerid=".POST("CustomerID")."';\n";
	exit();
}

if (POST("step") == "ConfirmSaveCustomer") {
	ValidateForm();
	$customer = new Customer(POST("CustomerID"),"");
	if ($customer->Exists) {
		$haschanged = false;
		if ($customer->Name != POST("Name")) { $haschanged = true; }
		if ($customer->Surname != POST("Surname")) { $haschanged = true; }
		if ($customer->Email != POST("Email")) { $haschanged = true; }
		if ($customer->CellPhone != POST("CellPhone")) { $haschanged = true; }
		if ($haschanged) {
			//===Confirm Customer Details have changed===
			$customer->Name = ucfirst(POST("Name"));
			$customer->Surname = ucfirst(POST("Surname"));
			$customer->Email = POST("Email");
			$customer->CellPhone = POST("CellPhone");
			$customer->UpdatedBy = $sysuser->id;
			$customer->Save();
			$_SESSION["customerid"] = $customer->CustomerID;
		}
	}else{
		//===Create New Customer===
		$customer->Name = ucfirst(POST("Name"));
		$customer->Surname = ucfirst(POST("Surname"));
		$customer->Email = POST("Email");
		$customer->CellPhone = POST("CellPhone");
		$customer->CreatedBy = $sysuser->id;
		$customer->Save();
		$_SESSION["customerid"] = $customer->CustomerID;
	}
	/*
	if ($customer->HasOpenQuotes) {
		//===Show Existing Quotes===
		print "document.location.href='quotes.php?customerid=".$customer->CustomerID."';\n";
	}else{
		//===Start New Quote Form===
		print "document.location.href='editquote.php?id=0&customerid=".$customer->CustomerID."&isnew=1';\n";
	}
	*/
	print "document.location.href='quotes.php?customerid=".$customer->CustomerID."';\n";
	exit();
}

if (POST("step") == "SaveNewCustomer") {
	ValidateForm();
	$customer = new Customer(0, POST("CellPhone"));
	$customer->Name = ucfirst(POST("Name"));
	$customer->Surname = ucfirst(POST("Surname"));
	$customer->Email = POST("Email");
	$customer->CellPhone = POST("CellPhone");
	if (!$customer->Exists) {
		$customer->CreatedBy = $sysuser->id;
	}else{
		$customer->UpdatedBy = $sysuser->id;
	}
	$customer->Save();
	//print "document.location.href='editquote.php?id=0&customerid=".$customer->CustomerID."&isnew=1';\n";
	print "document.location.href='quotes.php?customerid=".$customer->CustomerID."';\n";
	exit();		
}

require_once("inc/header.php");

?>
<script language='javascript'>
var EmailCheck = '';
var SurnameCheck = '';
var EmailCheck = '';

function CheckCellPhone() {
	var urldata = 'step=CheckCellPhone&CellPhone=' + encodeURIComponent($('#chkCellPhoneFind').val());
	urldata += '&Email=' + encodeURIComponent($('#chkEmailFind').val());
	jQAJAXCall('newquote.php', urldata);
}

function CheckHasChanged() {
	var clientdetailschanged = false;
	if ($('#Name').val() != $('#chkName').val()) { clientdetailschanged = true };
	if ($('#Surname').val() != $('#chkSurname').val()) { clientdetailschanged = true };
	if ($('#Email').val() != $('#chkEmail').val()) { clientdetailschanged = true };
	if ($('#CellPhone').val() != $('#chkCellPhone').val()) { clientdetailschanged = true };
	if (clientdetailschanged && $('#CustomerID').val() > 0) {
		$('#btnSaveCustomer').show();
		$('#btnNewQuote').hide();
	}else{
		$('#btnSaveCustomer').hide();
		$('#btnNewQuote').show();
	}
}

function ConfirmSaveCustomer() {
	var clientdetailschanged = false;
	if ($('#Name').val() != $('#chkName').val()) { clientdetailschanged = true };
	if ($('#Surname').val() != $('#chkSurname').val()) { clientdetailschanged = true };
	if ($('#Email').val() != $('#chkEmail').val()) { clientdetailschanged = true };
	if ($('#CellPhone').val() != $('#chkCellPhone').val()) { clientdetailschanged = true };
	var urldata = '';
	
	if (clientdetailschanged && $('#CustomerID').val() > 0) {
		if (window.confirm('Customer details have changed. Would you like to save the new details?')) {
			var urldata = 'step=ConfirmSaveCustomer';
			$('#customerinput').find('input[type=text],select,input[type=hidden]').each(function() {
				if ($(this).val() != '') {
					urldata += '&' + $(this).attr('id') + '=' + encodeURIComponent($(this).val());
				}
			});
			jQAJAXCall('newquote.php', urldata);
		}else{
			CreateNewQuote();
		}
	}else{
		CreateNewQuote();
	}
}

function SaveNewCustomer() {
	var urldata = 'step=SaveNewCustomer';
	$('#customerinput').find('input[type=text],select,input[type=hidden]').each(function() {
		if ($(this).val() != '') {
			urldata += '&' + $(this).attr('id') + '=' + encodeURIComponent($(this).val());
		}
	});
	jQAJAXCall('newquote.php', urldata);
}

function CreateNewQuote() {
	var urldata = 'step=StartNewQuote';
	$('#customerinput').find('input[type=text],select,input[type=hidden]').each(function() {
		if ($(this).val() != '') {
			urldata += '&' + $(this).attr('id') + '=' + encodeURIComponent($(this).val());
		}
	});
	jQAJAXCall('newquote.php', urldata);
}
</script>
<?php 
PrintNavBar($booktitle, true);

print "<div id='cellinput'>";
PrintTextBox("chkCellPhoneFind", "Cell Phone", "");
PrintTextBox("chkEmailFind", "Email Address", "");
print '<a href="#" onclick="CheckCellPhone()" data-role="button" data-theme="a" data-transition="fade" data-inline="true" data-icon="forward" data-ajax="false">Find Customer</a>';
print "</div>";

print "<div id='customerinput' style='display:none;'>";
PrintTextBox("Name", "Name", "");
PrintTextBox("Surname", "Surname", "");
//PrintTextBox("CellPhone", "Cell Phone", "", "", true, true);
PrintTextBox("CellPhone", "Cell Phone", "");
PrintTextBox("Email", "Email", "");

print "<input type='hidden' id='chkName' name='chkName' value=''>";
print "<input type='hidden' id='chkSurname' name='chkSurname' value=''>";
print "<input type='hidden' id='chkEmail' name='chkEmail' value=''>";
print "<input type='hidden' id='chkCellPhone' name='chkCellPhone' value=''>";
print "<input type='hidden' id='CustomerID' name='CustomerID' value='0'>";

//PrintTextBox("EscalationRate", "Escalation Rate", "8");
//PrintTextBox("KWhPrice", "KWh Price", "1.12");
//PrintTextArea("Property", "Property", "", "Enter property details.");
print '<a href="#" id="btnSaveCustomer" onclick="ConfirmSaveCustomer()" style="display:none;" data-role="button" data-theme="a" data-transition="fade" data-inline="true" data-icon="forward" data-ajax="false">Update Customer Details</a>';
print '<a href="#" id="btnNewQuote" onclick="CreateNewQuote()" data-role="button" data-theme="a" data-transition="fade" data-inline="true" data-icon="forward" data-ajax="false">Find / Create New Quote</a>';
print "</div>";
?>
<script>
jQuery("#chkCellPhoneFind").keypress(function(){
	var value = jQuery(this).val();
	value = value.replace(/[^0-9]+/g, '');
	jQuery(this).val(value);
});
$('#customerinput').find('input[type=text]').keyup(function() {
	CheckHasChanged()
});
</script>

<?php 
require_once("inc/footer.php");
?>