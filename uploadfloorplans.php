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

	$Image = "images/floorplans.png";
	$errorMess = "";
	$Mess = "";
    $quoteid = (int)isset($_REQUEST["id"])?$_REQUEST["id"]:0;

	//Uploaded files
    if (!empty($_FILES)){
      $floor_id = $_POST['doc_category'];
      $quote_id_floor = $_POST['quote_id_floor'];
      $output_dir = "quotes/floorplans/";
      $allowedExtension = array('pdf','png','jpg','gif','jpeg');

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
        $fileName = date('Ymd')."_".time('hms').".".$FileType;
        $upload = false;
        if (move_uploaded_file($_FILES["upload"]["tmp_name"], $output_dir.$fileName)){
            $upload = true;
        }
   
        if ($upload ==false){
        	$errorMess = "Unable to add Quote Document Plan!";
        }else{
            $images = array('png','jpg','gif');
            $filegroup = "";
            if (in_array($FileType,  $images)){
                $filegroup = "Image";
            }else{
                $filegroup = "PDF";
            }

            $query  = "INSERT INTO `quote_floor_plans` (date_created,quote_id,floor_id,floor_plan,file_group) VALUES (";
            $query  .= "NOW(),";
            $query  .= " '".$quote_id_floor. "',";
            $query  .= " '".$floor_id. "',";
            $query  .= " '".$fileName. "',";
            $query  .= " '".$filegroup. "'";
            $query  .= ") ";
            $result = mysqli_query($GLOBALS["link"],$query);
            if($result) {
                $Mess = "Quote Document Plans details saved...";
                //Quoted Modified
                $query = "UPDATE `quotes` SET";
                $query .= " `last_updated` = NOW()";
                $query .= " WHERE `quote_id` = '" . $quoteid . "'";
                $result = mysqli_query($GLOBALS["link"],$query);

                logAction("Added Quote Document Plans details");
            } else {
                $errorMess = "Unable to save document plans details! Please try again...";
            } 
        }
      }
    }else{
  		$Image = "images/floorplans.png";
	}
?>
<html>
<head>
<title>Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="js/bootstrap.min.css" rel="stylesheet" type="text/css" />  
</head>
<body>
	<form name="formchange" id="formchange" width='600' height='600'  action="uploadfloorplans.php?" method="post" enctype="multipart/form-data" style="display: none;">
        <?php
          print "<input type='hidden' id='quote_id_floor' name='quote_id_floor' value='".$quoteid."' >";
          print "<input type='hidden' id='doc_category' name='doc_category'>";
	      print "<h3 style='font-size:14px;font-weight:bold;'>Select Category:</h3>";
	      print "<select id='floorplan_user' name='floorplan_user'>";
          print "<option value='' selected='selected'>[Please select]</option>";
	        $query  = "SELECT *";
            $query .= " FROM `floor_plan_settings`";
            $result = mysqli_query($GLOBALS["link"],$query);
	        while ($arr = mysqli_fetch_assoc($result)) {
                $FloorPlan = $arr['name'];
	            print "<option id='".$arr['id']."' value='".$FloorPlan."'>";
	                print ucfirst($FloorPlan);
	            print "</option>";
	        }
            print "</select>";
	    ?>
	    <br/><br/>
        <label>Document Plan:</label><br/>
        <input name="upload" id="upload"  type="file" />
		<div align="center">
			<br/><br/>
			<img src="<?php echo htmlspecialchars($Image); ?>" name="FloorPlanImage" id="FloorPlanImage"  alt="Img" height="200" width="200" align="middle">
			<br />
			<input  style="float:right" type="submit"  id="save" name="save" class="btn btn-primary" value="Add Document">
            <br /><br /><br />
        </div>
	</form>

	<h3 style='font-size:14px;font-weight:bold;'>Add Document to Quote Floor Plan:</h3>
    <br/><br/>
	<img src="<?php echo htmlspecialchars($Image); ?>" name="AddFloorPlanImage" id="AddFloorPlanImage"  alt="Img" height="350" width="350" style="margin-left:137px" align="middle" title="Click here to change image">
	 <br/><br/>
    <span id='ok-message' style='color:green'><?php echo $Mess;?></span>
    <span id='error-message'style='color:red'><?php echo $errorMess;?></span>

    <script src="js/jQuery-2.1.3.min.js"></script>
    <script src="js/formValidation.min.js" type="text/javascript"></script> 
    <script src="js/framework/bootstrap.min.js" type="text/javascript"></script> 
    <script src="js/bootstrap.js" type="text/javascript"></script> 
    <script src="js/bootstrap.min.js" type="text/javascript"></script>   
    <script src="js/bootbox.min.js" type="text/javascript"></script> 
    <script>
    $(document).ready(function(){
        $('img#AddFloorPlanImage').on('click', function() {
            bootbox.dialog({
                    title: 'Quote Document ',
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
        $("img#AddFloorPlanImage").click();

        $('select#floorplan_user').change(function () {
            var id = $(floorplan_user).children(':selected').attr('id'); 
            $('input#doc_category').val(id);            
        });

    });
    </script>
</body>
</html>