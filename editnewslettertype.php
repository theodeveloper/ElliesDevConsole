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
    get_session();

    include_once("inc/html.php");
    include_once("inc/system_user.php");
    
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
	session_start();

	$errorMess = "";
	$Mess = "";

	if(isset($_POST["deletenewsletter"]) || isset($_POST["updatenewsletter"])){
		$Channel = trim($_POST["editnewslettertypechannel"]);
		$NewsletterType = trim($_POST["newslettertype_edit"]);
		$Subject = trim($_POST["newslettersubject_edit"]);
		$NewsletterTypeID = trim($_POST["newslettertype_id"]);

        $newsletter_layout = str_replace("–", "-", $_POST["newslettertypelayout"]);
        $newsletter_layout = str_replace("“", "\"", $newsletter_layout);
        $newsletter_layout = str_replace("”", "\"", $newsletter_layout);
        $newsletter_layout = str_replace("'", "\"", $newsletter_layout);
        $newsletter_layout = str_replace("’", "'", $newsletter_layout);
        $newsletter_layout = str_replace("-\n", "-\n\n", $newsletter_layout);
        $newsletter_layout = str_replace(".\n", ".\n\n", $newsletter_layout);

        if($Channel !="" && $NewsletterType !="" && $Subject !="" ){
        	if(isset($_POST["updatenewsletter"])){

        		$query = "UPDATE news_letter_types SET";
	            $query .= " `last_updated` =NOW(),";
	            $query  .= " `newsletter_type` =  '".$NewsletterType. "',";
	            $query  .= " `subject` =  '".$Subject. "',";
	            $query  .= " `newsletter_layout` =  '".$newsletter_layout. "',";
	            $query  .= " `channel` =  '".$Channel. "'";
	            $query .= " WHERE id='".$NewsletterTypeID."' LIMIT 1";
	            $result = mysqli_query($GLOBALS["link"],$query);
	           
	            if($result) {
	                $Mess =  "NewsLetter Type updated...";
	                 //Uploaded files
				    if (!empty($_FILES)){
				      	$output_dir = "modules/newsletter/attachments/";
				        $fileName =  basename($_FILES["newsletterattachment_edit"]["name"]);
				        $target_file = $output_dir.$fileName;
				        $FileType = pathinfo($target_file,PATHINFO_EXTENSION);
				        $fileName  = "newsletter_attachment_".date('Ymd')."_".time('hms').".".$FileType;
				        $upload = false;
				        if (move_uploaded_file($_FILES["newsletterattachment_edit"]["tmp_name"], $output_dir.$fileName)){
				            $upload = true;
				        }
				   
				        if ($upload ==false){
				        	$errorMess = "Unable to add Newsletter Attachment!";
				        }else{
				        	$Image = $output_dir.$fileName;
				        	$query = "UPDATE news_letter_types SET";
					        $query .= " `last_updated` =NOW(),";
					        $query  .= "`attachment` = '".$fileName. "'";
					 		$query .= " WHERE id=".$NewsletterTypeID." LIMIT 1";
					        $result = mysqli_query($GLOBALS["link"],$query); 
					        if($result){
					        	$Mess =  "NewsLetter Type updated...";
						    }else{
						    	$errorMess = "Unable to update Newsletter Attachment!";
						    }
				        }  
				    }
	                $title = $NewsletterType;
	                logAction("Updated Newsletter Type:$title");
	            } else {
	               $errorMess =  "Unable to update Newsletter Type Inforamtion! Please try again.....";
	            } 
        	}elseif(isset($_POST["deletenewsletter"])){
        		$query = "UPDATE news_letter_types SET";
	            $query .= " `last_updated` =NOW(),";
	            $query .= " `deleted` = '1'";
	            $query .= " WHERE id=".$NewsletterTypeID." LIMIT 1";
	            $result = mysqli_query($GLOBALS["link"],$query);
	            if($result) {
	                $title = $NewsletterType;
	                $Mess =  "NewsLetter Type deleted...";
	                logAction("Deleted Newsltter Type:$title");
	            } else {
	                $errorMess =  "Unable to delete Newsletter Type Inforamtion! Please try again.....";
	            } 
        	}
	    }else{
	    	$errorMess =  "Please fill in the required fields...";
	    }
	}
?>
<html>
<head>
	<title>Newsletter Templates</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
	<link href="wysihtml5/css/stylesheet.css" rel="stylesheet" type="text/css" />
	<link href="wysihtml5/css/reset-min.css" rel="stylesheet" type="text/css" />
	 <!--wysihtml5 parser rules-->
	<script type="text/javascript" src="wysihtml5/js/advanced.js"></script>
	<script type="text/javascript" src="wysihtml5/js/wysihtml5-0.3.0.min.js"></script>
</head>
	<body>
	<form name="formchange" id="formchange" action="editnewslettertype.php" method="post" enctype="multipart/form-data">
		<table cellspacing='0' cellpadding='4' border='0' width='100%'>
			<tr>
				<td>
					<h3 style='font-weight: bold;'>Select Newsletter Template</h3>
					<input type='hidden' id='newslettertype_id' name='newslettertype_id' style='width: 200px' />
					<br/><br/>
				</td>
				<td>
				<?php
					    $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
                        $sqlres = mysqli_query($GLOBALS["link"],$sql);
                        $row = mysqli_fetch_assoc($sqlres);
                        $channeltype = $row['type'];
                        
                        $query  = "SELECT `news_letter_types`.id,`news_letter_types`.newsletter_type";
                        $query .= " FROM `news_letter_types`";
                        $sysuser = new userType($_SESSION["userid"]);
                        if ($sysuser->isSuperAdmin){
                             $query .= " INNER JOIN channels ON channels.id = news_letter_types.channel AND channels.type='".$channeltype."' WHERE `deleted` = '0'";
                        }else{
                            $query .= " WHERE `channel` ='".$GLOBALS['system_user']->retailChannel."' AND `deleted` = '0'";     
                        }
                        $query .= " ORDER BY `news_letter_types`.id";
                        //echo $query;?>	
					<select id='newslettertype_select_edit'>
					
					<option id=''value='' selected='selected'>[Please select]</option>	
					<?php
					    $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
                        $sqlres = mysqli_query($GLOBALS["link"],$sql);
                        $row = mysqli_fetch_assoc($sqlres);
                        $channeltype = $row['type'];
                        
                        $query  = "SELECT `news_letter_types`.id,`news_letter_types`.newsletter_type";
                        $query .= " FROM `news_letter_types`";
                        $sysuser = new userType($_SESSION["userid"]);
                        if ($sysuser->isSuperAdmin){
                             $query .= " INNER JOIN channels ON channels.id = news_letter_types.channel AND channels.type='".$channeltype."' WHERE `deleted` = '0'";
                        }else{
                            $query .= " WHERE `channel` ='".$GLOBALS['system_user']->retailChannel."' AND `deleted` = '0'";     
                        }
                        $query .= " ORDER BY `news_letter_types`.id";
                        $result = mysqli_query($GLOBALS["link"],$query);
                        while ($arr = mysqli_fetch_assoc($result)) {
                            print "<option id='".$arr['id']."' value='".htmlentities($arr['id'])."'>";
                                print ucfirst($arr['newsletter_type']);
                            print "</option>";
                        }
					?>	
				</td>		
			</tr>
			<tr>
				<?php
					$sysuser = new userType($_SESSION["userid"]);
				    if ($sysuser->isSuperAdmin){
					    $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
	                    $sqlres = mysqli_query($GLOBALS["link"],$sql);
	                    $row = mysqli_fetch_assoc($sqlres);
	                    $channeltype = $row['type'];
	                    if($channeltype=="Franchises"){
	                    	print "<td>Franchisee</td>";
	                    }else{
	                    	print "<td>Channel</td>";
	                    }
	                }
				?>
		
				<?php
					print "<td>";
					$sysuser = new userType($_SESSION["userid"]);
				    if ($sysuser->isSuperAdmin){	
				    	$sql = "SELECT `type` FROM `channels` WHERE `id`=".$sysuser->retailChannel;
                        $sqlres = mysqli_query($GLOBALS["link"],$sql);
                        $row = mysqli_fetch_assoc($sqlres);
                        $channeltype = $row['type'];                  	   
	                    $query = "select id, name from channels WHERE `channels`.type='".$channeltype."'";
	                    $result = mysqli_query($GLOBALS["link"],$query);
	                    print "<select id='editnewslettertypechannel' name ='editnewslettertypechannel'>";
	                        print "<option value='' selected='selected'>[Please select]</option>";
	                        while ($row=mysqli_fetch_assoc($result)){
	                            print "<option id='".$row['id']."' value='".$row["id"]."'>";
	                                print ucfirst($row["name"]);
	                            print "</option>";
	                        }
	                    print "</select>";
	                }else{
	                    print "<input type='hidden' id='editnewslettertypechannel'  name ='editnewslettertypechannel' style='width: 215px' value='".$GLOBALS['system_user']->retailChannel."' readonly />";   
	                }
					print "<br/><br/>";
					print "</td>";
				?>	
			</tr>
			<tr>
				<td>Newsletter Template</td>
				<td><input type='text' id='newslettertype_edit' name='newslettertype_edit' style='width: 215px' /><br/><br/></td>
			</tr>
			<tr>
				<td>Newsletter Subject</td>
				<td><input type='text' id='newslettersubject_edit' name ='newslettersubject_edit' style='width: 215px' /><br/><br/></td>
			</tr>
			<tr>
				<td>Change Newsletter Attachment</td>
				<td><input type='file' id='newsletterattachment_edit' name ='newsletterattachment_edit' style='width: 215px' /></td>
			</tr>
			<tr>
				<td colspan='3' valign='middle' style='text-align: center;'>
					<br/>
	    			<input type="submit" id='deletenewsletter' name='deletenewsletter' style='color:red;font-weight: bold;'  value="Delete Newsletter Template" />
	    			<input type="submit" id='updatenewsletter' name='updatenewsletter'  value="Update Newsletter Template" />
	    			<br/><br/>
				</td>
	    	</tr>
	    	<tr>
				<td colspan='3' valign='middle' style='text-align: center;'>
					<span id='ok-message' style='color:green'><?php echo $Mess;?></span>
	    			<span id='error-message'style='color:red'><?php echo $errorMess;?></span>
				</td>
	    	</tr>
		</table>
		
		<table id='editnewslettertypessetup' name='editnewslettertypessetup' cellspacing='0' cellpadding='4' border='0' width='100%'>
			<thead>
				<tr>
					<td width='8%'  style='border: none;'>&nbsp;</td>
					<td width='12%' style='border: none;'>&nbsp;</td>
					<td width='36%' style='border: none;'>&nbsp;</td>
					<td width='8%' style='border: none;'>&nbsp;</td>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td style="width: 20%;">Newsletter Layout</td>
					<td><br/><br/></td>
				</tr>
				<tr>
					<td colspan='4' style='text-align: left; padding: 0'>
						<div>
							<table cellpadding='10'>
								<tr>
									<td>
									    <div id="wysihtml5-editor-toolbar">
									      <header>
									        <ul class="commands">
									          <li data-wysihtml5-command="bold" title="Make text bold (CTRL + B)" class="command"></li>
									          <li data-wysihtml5-command="italic" title="Make text italic (CTRL + I)" class="command"></li>
									          <li data-wysihtml5-command="insertUnorderedList" title="Insert an unordered list" class="command"></li>
									          <li data-wysihtml5-command="insertOrderedList" title="Insert an ordered list" class="command"></li>
									          <li data-wysihtml5-command="createLink" title="Insert a link" class="command"></li>
									          <li data-wysihtml5-command="insertImage" title="Insert an image" class="command"></li>
									          <li data-wysihtml5-command="formatBlock" data-wysihtml5-command-value="h1" title="Insert headline 1" class="command"></li>
									          <li data-wysihtml5-command="formatBlock" data-wysihtml5-command-value="h2" title="Insert headline 2" class="command"></li>
									          <li data-wysihtml5-command-group="foreColor" class="fore-color" title="Color the selected text" class="command">
									            <ul>
									              <li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="silver"></li>
									              <li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="gray"></li>
									              <li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="maroon"></li>
									              <li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="red"></li>
									              <li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="purple"></li>
									              <li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="green"></li>
									              <li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="olive"></li>
									              <li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="navy"></li>
									              <li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="blue"></li>
									            </ul>
									          </li>
									          <li data-wysihtml5-command="insertSpeech" title="Insert speech" class="command"></li>
									          <li data-wysihtml5-action="change_view" title="Show HTML" class="action"></li>
									        </ul>
									      </header>
									      <div data-wysihtml5-dialog="createLink" style="display: none;">
									        <label>
									          Link:
									          <input data-wysihtml5-dialog-field="href" value="http://">
									        </label>
									        <a data-wysihtml5-dialog-action="save">OK</a>&nbsp;<a data-wysihtml5-dialog-action="cancel">Cancel</a>
									      </div>

									      <div data-wysihtml5-dialog="insertImage" style="display: none;">
									        <label>
									          Image:
									          <input data-wysihtml5-dialog-field="src" value="http://">
									        </label>
									        <a data-wysihtml5-dialog-action="save">OK</a>&nbsp;<a data-wysihtml5-dialog-action="cancel">Cancel</a>
									      </div>
									    </div>
	
									    <section style='top:247px;'>
									    	<br/><br/><br/><br/><br/><br/><br/><br/>
											<textarea id='newslettertypelayout' name ='newslettertypelayout' wrap="on" placeholder="Enter something ...">
											</textarea>
									    </section>
									</td>
									<td></td>
								</tr>
		                    </table>
						</div>
					</td>
					<td>&nbsp;</td>
				</tr>
			</tbody>
	    </table>
	</form>
	<script src="js/jQuery-2.1.3.min.js"></script>	
	<script src="modules/newsletter/js/functions.js"></script>
	<script type="text/javascript">
	 	var editor = new wysihtml5.Editor("newslettertypelayout", {
                toolbar:     "wysihtml5-editor-toolbar",
                stylesheets: ["wysihtml5/css/reset-min.css", "wysihtml5/css/editor.css"],
                parserRules: wysihtml5ParserRules
              });
              
	    editor.on("load", function() {
	       var composer = editor.composer;
	        composer.selection.selectNode(editor.composer.element.querySelector("h1"));
	    });
    </script>
    <script type="text/javascript">
    	jQuery('select#newslettertype_select_edit').change(function () { 
	        var id = $('select#newslettertype_select_edit').children(':selected').attr('id'); 
	        if(id !=""){
	        	$('input#newslettertype_id').val(id); 
	        	AJAXCallNewsLetterTypeInfo('getNewsLetterTypeInfo', 'newslettertypeID='+id);  	
	        }               
	    });
    </script>
	</body>
</html>