<?php
	require_once("inc/config.php");
    require_once("inc/system_user.php");
	require_once("inc/functions.php");
	error_reporting(E_ERROR);
	ini_set('display_errors', 'On');
	session_start();

	$errorMess = "";
	$Mess = "";

	if(isset($_POST["addnewsletter"])){
		$Channel = trim($_POST["addnewslettertypechannel"]);
		$NewsletterType = trim($_POST["addnewslettertype"]);
		$Subject = trim($_POST["addnewslettersubject"]);

        $newsletter_layout = str_replace("–", "-", $_POST["newslettertypelayout"]);
        $newsletter_layout = str_replace("“", "\"", $newsletter_layout);
        $newsletter_layout = str_replace("”", "\"", $newsletter_layout);
        $newsletter_layout = str_replace("'", "\"", $newsletter_layout);
        $newsletter_layout = str_replace("’", "'", $newsletter_layout);
        $newsletter_layout = str_replace("-\n", "-\n\n", $newsletter_layout);
        $newsletter_layout = str_replace(".\n", ".\n\n", $newsletter_layout);

        if($NewsletterType !="" && $Subject !="" && $Channel !="" ){
	        $query  = "INSERT INTO `news_letter_types` (date_created,newsletter_type,subject,newsletter_layout,channel) VALUES (";
	        $query  .= "NOW(),";
	        $query  .= " '".$NewsletterType."',";
	        $query  .= " '".$Subject. "',";
	        $query  .= " '".$newsletter_layout. "',";
	        $query  .= " '".$Channel. "'";
	        $query  .= ") ";
	        $result = mysqli_query($GLOBALS["link"],$query); 
	        $id =$GLOBALS["link"]->insert_id;    
	        if($result) {
	            $Mess =  "Newsletter Type saved...";
	            //Uploaded files
			    if (!empty($_FILES)){
			      	$output_dir = "modules/newsletter/attachments/";
			        $fileName =  basename($_FILES["addnewsletterattachment"]["name"]);
			        $target_file = $output_dir.$fileName;
			        $FileType = pathinfo($target_file,PATHINFO_EXTENSION);
			        $fileName  = "newsletter_attachment_".date('Ymd')."_".time('hms').".".$FileType;
			        $upload = false;
			        if (move_uploaded_file($_FILES["addnewsletterattachment"]["tmp_name"], $output_dir.$fileName)){
			            $upload = true;
			        }
			   
			        if ($upload ==false){
			        	$errorMess = "Unable to add Newsletter Attachment!";
			        }else{
			        	$Image = $output_dir.$fileName;
			        	$query = "UPDATE news_letter_types SET";
				        $query .= " `last_updated` =NOW(),";
				        $query  .= "`attachment` = '".$fileName. "'";
				 		$query .= " WHERE id=".$id." LIMIT 1";
				        $result = mysqli_query($GLOBALS["link"],$query); 
				        if(!$result){
				        	$errorMess = "Unable to update Newsletter Attachment!";
					    }
			        }  
			    }
	            $title = $NewsletterType;
	            logAction("Added Newsletter Type:$title");
	        } else {
	            $errorMess =  "Unable to save Newsletter Type saved Inforamtion! Please try again...";
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
	<form name="formchange" id="formchange" action="addnewslettertype.php" method="post" enctype="multipart/form-data">
		<table cellspacing='0' cellpadding='4' border='0' width='100%'>
			<tr>
				<tr>
					<td>
						<?php
							$sysuser = new userType($_SESSION["userid"]);
						    if ($sysuser->isSuperAdmin){
								$sql = "SELECT `type` FROM `channels` WHERE `id`=".$sysuser->retailChannel;
	                            $sqlres = mysqli_query($GLOBALS["link"],$sql);
	                            $row = mysqli_fetch_assoc($sqlres);
	                            $channeltype = $row['type'];  
								if($channeltype =="Franchises")print "Franchisee";  
	                            if($channeltype =="Retail")print "Channel";                             
	                            if($channeltype =="Commercial")print "Channel";
	                        }
						?>
					</td>
					<td>		
						<?php
							$sysuser = new userType($_SESSION["userid"]);
						    if ($sysuser->isSuperAdmin){	

			                    $sql = "SELECT `type` FROM `channels` WHERE `id`=".$sysuser->retailChannel;
	                            $sqlres = mysqli_query($GLOBALS["link"],$sql);
	                            $row = mysqli_fetch_assoc($sqlres);
	                            $channeltype = $row['type'];                  	   
			                    $query = "select id, name from channels WHERE `channels`.type='".$channeltype."'";
			                    $result = mysqli_query($GLOBALS["link"],$query);
			                    print "<select id='addnewslettertypechannel' name ='addnewslettertypechannel'>";
			                        print "<option value='' selected='selected'>[Please select]</option>";
			                        while ($row=mysqli_fetch_assoc($result)){
			                            print "<option id='".$row['id']."' value='".$row["id"]."'>";
			                                print ucfirst($row["name"]);
			                            print "</option>";
			                        }
			                    print "</select>";
			                }else{
			                    print "<input type='hidden' id='addnewslettertypechannel'  name ='addnewslettertypechannel' style='width: 200px;' value='".$sysuser->retailChannel."' readonly/>";   
			                }
						?>	
						<br/><br/>
					</td>
				</tr>
				<tr>
					<td>Newsletter Template</td>
					<td><input type='text' id='addnewslettertype' name ='addnewslettertype' style='width: 215px' /><br/><br/></td>
				</tr>
				<tr>
					<td>Newsletter Subject</td>
					<td><input type='text' id='addnewslettersubject' name ='addnewslettersubject' style='width: 215px' /><br/><br/></td>
				</tr>
				<tr>
					<td>Newsletter Attachment</td>
					<td><input type='file' id='addnewsletterattachment' name ='addnewsletterattachment' style='width: 215px' /></td>
				</tr>
			</tr>
			<tr>
				<td colspan='3' valign='middle' style='text-align: center;'>
					<br/>
	    			<input type='submit' id='addnewsletter' name='addnewsletter' value="Save Newsletter Template" />
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
		
		<table id='addnewslettertypessetup' name='addnewslettertypessetup' cellspacing='0' cellpadding='4' border='0' width='100%'>
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
									    <section style='top: 204px;'>
									    	<br/><br/><br/><br/><br/><br/><br/>
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
	<script type="text/javascript">
	 	var editor = new wysihtml5.Editor("newslettertypelayout", {
                toolbar:     "wysihtml5-editor-toolbar",
                stylesheets: ["wysihtml5/css//reset-min.css", "wysihtml5/css/editor.css"],
                parserRules: wysihtml5ParserRules
              });
              
	    editor.on("load", function() {
	       var composer = editor.composer;
	        composer.selection.selectNode(editor.composer.element.querySelector("h1"));
	    });
    </script>
    <script type="text/javascript">
	   /* $("input#addnewsletter").click(function(){
		    alert("The paragraph was clicked.");
		    var form=$('form#formchange');
        	//form.attr('action',form.attr('action')).trigger('submit');
		});
*/
     	
    </script>
	</body>
</html>