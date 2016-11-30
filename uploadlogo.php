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
   
    session_start();

    include_once("inc/config.php");
    include_once("inc/functions.php");
    include_once("inc/sql.php");
    include_once("inc/system_user.php");
    get_session();
    
    // If not authenticated...
    if (!is_authenticated()) {
        if ($_POST['aj'] == 1) {
            print "\n<!-- scripts , code below be eval()ed by javascript -->\nalert('You have been signed out! Please relogin');";
        } else {
            header("location: login.php");
            exit(1);
        }
    }

    // Get user object
    $system_user = new userType($GLOBALS['SESSION']['username']);
    // If not valid, we have a non continuing error...
    if ($system_user->valid != 1) {
        print "Unable to find system user!";
        exit(1);
    }

	error_reporting(E_ERROR);
	ini_set('display_errors', 'On');

	$Image = "images/company.png";
	$errorMess = "";
	$Mess = "";

	//Uploaded files
    if (!empty($_FILES)){
      $output_dir = "modules/companyprofiles/logos/";
      $allowedExtension = array('png','jpg','gif','jpeg');

      foreach ($_FILES as $file){
        if ($file['tmp_name'] > ''){
            $name = $file['name'];
            $name = explode(".", strtolower($name));
            if (!in_array(end($name), $allowedExtension)){
              $errors= $file['name'].' is an invalid file type!<br/>';
            }
        }
      }
      $errors = "";

      if($errors ==""){
        $fileName =  basename($_FILES["upload"]["name"]);
        $target_file = $output_dir.$fileName;
        $FileType = pathinfo($target_file,PATHINFO_EXTENSION);
        $fileName  = "company_logo_" . date('Ymd'). "_" . time('hms'). ".".$FileType;
        $upload = false;
        if (move_uploaded_file($_FILES["upload"]["tmp_name"], $output_dir.$fileName)){
            $upload = true;
        }
   
        if ($upload ==false){
        	$errorMess = "Unable to update Company Profile Logo!";
        }else{
        	$Image = $output_dir.$fileName;
        	$query = "UPDATE branches SET";
	        $query .= " `last_updated` =NOW(),";
	        $query  .= "`logo` = '".$fileName. "'";
	 		$query .= " WHERE id=".$_POST["company"]." LIMIT 1";
	        $result = mysqli_query($GLOBALS["link"],$query); 
	        if($result) {
	        	$Mess = "Company Profile Logo updated";
		    } else {
	        	$errorMess = "Unable to update Company Profile Logo!";
		    }
        }
      }
    }else{
  		$Image = "images/company.png";
	}
?>
<html>
<head>
<title>Logo</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="js/bootstrap.min.css" rel="stylesheet" type="text/css" />  
</head>
<body>
	<form name="formchange" id="formchange" action="uploadlogo.php" method="post" enctype="multipart/form-data" style="display: none;">
        <?php
            $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
            $sqlres = mysqli_query($GLOBALS["link"],$sql);
            $row = mysqli_fetch_assoc($sqlres);
            $channeltype = $row['type'];
            $sysuser = new userType($_SESSION["userid"]);
            if ($sysuser->isSuperAdmin){ 
		      print "<h3 style='font-size:14px;font-weight:bold;'>Select Company:</h3>";
		      print "<select id='company' name='company'>";
	          print "<option value='' selected='selected'>[Please select]</option>";
    	        $query  = "SELECT `branches`.id,`branch`,`name`";
    	        $query .= " FROM `branches` INNER JOIN channels ON channels.id = branches.channel AND channels.type='".$channeltype."'";
    	        $query .= " ORDER BY `branches`.id";
    	        $result = mysqli_query($GLOBALS["link"],$query);
    	        while ($arr = mysqli_fetch_assoc($result)) {
    	            print "<option id='".$arr['id']."' value='".$arr['id']."'>";
    	                    print ucfirst($arr['name'])."-".ucfirst($arr['branch']);
    	            print "</option>";
    	        }
                print "</select>";
            }else{
                print "<h3 style='font-size:14px;font-weight:bold;display:none;'>Select Company:</h3>";
                print "<select id='company' name='company' style='display:none;'>";
                print "<option id='".$GLOBALS['system_user']->branchID."' value='".$GLOBALS['system_user']->branchID."' selected></option>";
                print "</select>";
            }
	    ?>
	    <br/><br/><br/>
		<div align="center">
			<input name="upload" id="upload"  type="file" />
			<br /><br />
				<img src="<?php echo htmlspecialchars($Image); ?>" name="ProfileImage" id="ProfileImage"  alt="Img" height="200" width="200" align="middle">
			<br />
			<input  style="float:right" type="submit"  id="save" name="save" class="btn btn-primary" value="Save">
            <br /><br /><br />
        </div>
	</form>

    <div style='margin-top:76px'>
    	<h3 style='font-size:14px;font-weight:bold;'>Change Logo:</h3>
    	<img src="<?php echo htmlspecialchars($Image); ?>" name="ChangeProfileImage" id="ChangeProfileImage"  alt="Img" height="200" width="200" align="middle" title="Click here to change image">
    	 <br /><br />
        <span id='ok-message' style='color:green'><?php echo $Mess;?></span>
        <span id='error-message'style='color:red'><?php echo $errorMess;?></span>
    </div>

<script src="js/jQuery-2.1.3.min.js"></script>
<script src="js/formValidation.min.js" type="text/javascript"></script> 
<script src="js/framework/bootstrap.min.js" type="text/javascript"></script> 
<script src="js/bootstrap.js" type="text/javascript"></script> 
<script src="js/bootstrap.min.js" type="text/javascript"></script>   
<script src="js/bootbox.min.js" type="text/javascript"></script> 

<script>
    $('img#ChangeProfileImage').on('click', function() {
        bootbox.dialog({
                title: 'Change Company Logo ',
                message: $('#formchange'),
                show: false // We will show it manually later
            })
            .on('shown.bs.modal', function() {
                $('#formchange')
                    .show()                             // Show the login form
                    .formValidation('resetForm', true); // Reset form
            })
            .on('hide.bs.modal', function(e) {
                // Bootbox will remove the modal (including the body which contains the login form)
                // after hiding the modal
                // Therefor, we need to backup the form
                $('#formchange').hide().appendTo('body');
            })
            .modal('show');
    });
</script>

<script type="text/javascript">
	$('input#upload').change( function(event) {
		var src = URL.createObjectURL(event.target.files[0]); 
	    $("img#ProfileImage").attr('src',src);	    
	});
</script>
</body>
</html>