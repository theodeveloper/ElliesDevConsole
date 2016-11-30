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
	}
?>
<html>
<head>
	<title>Notifications</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
	<link href="css/site.css" rel="stylesheet" type="text/css" />
	<link href="css/jquery-ui-1.8.18.custom.css" rel="stylesheet" type="text/css" />
</head>
	<body>
		<form name="formchange" id="formchange" action="sendnotification.php" method="post" enctype="multipart/form-data">
			<table id='addnotificationsetup' name='addnotificationsetup' cellspacing='0' cellpadding='4' border='0' width='100%'>
				<thead>
					<tr>
						<td width='8%'  style='border: none;'>&nbsp;</td>
						<td width='36%' style='border: none;'>&nbsp;</td>
						<td width='12%' style='border: none;'>&nbsp;</td>
						<td width='36%' style='border: none;'>&nbsp;</td>
						<td width='8%' style='border: none;'>&nbsp;</td>
					</tr>	
				</thead>
				<tbody>
					<tr>
						<td colspan='4' style='text-align: left; padding: 0'>
						<div>
						<table cellpadding='10'>
                            <tr>
                                <td>Title</td>
                                <td><input type='text' id='notification_title' style='width: 200px' /></td>
                            </tr>
                            <tr>
                                <td>Description</td>
                                <td><textarea id='notification_description' rows='10' style='width: 200px'></textarea></td>
                            </tr>
                            <?php
	                            //Channel Type
	                            $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
	                            $sqlres = mysqli_query($GLOBALS["link"],$sql);
	                            $row = mysqli_fetch_assoc($sqlres);
	                            $channeltype = $row['type'];
	                            $sysuser = new userType($_SESSION["userid"]); 
	                            if($sysuser->isSuperAdmin){
	                                print "<tr>";
	                                    print "<td>";
	                                        print "Notify";
	                                    print "</td>";
	                                    print "<td>";
	                                        if($sysuser->isSuperAdmin && $channeltype !="Franchises"){
	                                            print "<select id='notifychanneltype'>";
	                                            print "<option value=''>[Please select channel type]</option>";
	                                           // print "<option id ='All' value='All' >All</option>"; 
	                                            if($channeltype =="Commercial"){//changed
	                                                print "<option id ='Commercial' value='Commercial' >Commercial</option>"; 
	                                            }

	                                            if($channeltype =="Retail"){
	                                                print "<option id='Retail' value='Retail'>Retail</option>";
	                                            }

	                                            if($channeltype =="Franchises"){
	                                                print "<option id='Franchises' value='Franchises'>Franchises</option>";
	                                            }
	                                                                           
	                                            print "</select>";
	                                        }else if($sysuser->isSuperAdmin && $channeltype =="Franchises"){
	                                            print "<select id='notifychanneltype'>";
	                                            print "<option value=''>[Please select channel type]</option>"; 
	                                            print "<option id='Franchises' value='Franchises'>Franchises</option>";    
	                                            print "</select>";
	                                        }
	                                        print "<br/><br/>";
	                                        print "<select id='notifyCommercial' style='display:none;' >";
	                                            print "<option value=''>[Please select commercial]</option>";
	                                            print "<option id ='All' value='All' >All</option>";               
	                                            $sql = "SELECT * FROM `channels` WHERE `type`='Commercial'";
	                                            $sqlres = mysqli_query($GLOBALS["link"],$sql);
	                                            while ($row=mysqli_fetch_assoc($sqlres)){
	                                                if ($channelname == $row["name"]) {
	                                                    print "<option id='".$row['id']."' value='".$row["name"]."' selected>".ucfirst($row["name"])."</option>";
	                                                }else{
	                                                    print "<option id='".$row['id']."' value='".$row["name"]."'>".ucfirst($row["name"])."</option>";                
	                                                }                       
	                                            }
	                                        print "</select>";

	                                        print "<select id='notifyFranchises' style='display:none;' >";
	                                            print "<option value=''>[Please select]</option>";
	                                            print "<option id='All' value='All'>All</option>";       
	                                            $sql = "SELECT * FROM `channels` WHERE `type`='Franchises'";
	                                            $sqlres = mysqli_query($GLOBALS["link"],$sql);
	                                            while ($row=mysqli_fetch_assoc($sqlres)){
	                                                if ($channelname == $row["name"]) {
	                                                    print "<option id='".$row['id']."' value='".$row["name"]."' selected>".ucfirst($row["name"])."</option>";
	                                                }else{
	                                                    print "<option id='".$row['id']."' value='".$row["name"]."'>".ucfirst($row["name"])."</option>";                
	                                                }
	                                            }   
	                                        print "</select>";

	                                        print "<select id='notifyRetail' style='display:none; >";
	                                            print "<option value=''>[Please select]</option>";  
	                                            print "<option id='All' value='All'>All</option>"; 
	                                            $sql = "SELECT * FROM `channels` WHERE `type`='Retail'";
	                                            $sqlres = mysqli_query($GLOBALS["link"],$sql);
	                                            while ($row=mysqli_fetch_assoc($sqlres)){
	                                               if ($channelname == $row["id"]) {
	                                                    print "<option id='".$row['id']."' value='".$row["name"]."' selected>".ucfirst($row["name"])."</option>";
	                                                }else{
	                                                    print "<option id='".$row['id']."' value='".$row["name"]."'>".ucfirst($row["name"])."</option>";                
	                                                }
	                                            }  
	                                        print "</select>";
	                                    print "</td>";
	                                print "</tr>";
	                            }else{
	                                print "<tr>";
	                                    print "<td>";
	                                        //print "Notify";
	                                    print "</td>";
	                                    print "<td>";
	                                $query = "select id, branch from branches where deleted='0' AND id='".$GLOBALS['system_user']->branchID."'";
	                                $result = mysqli_query($GLOBALS["link"],$query);
	                                $branch = "";
	                                while ($row=mysqli_fetch_assoc($result)){
	                                    $branch =  ucfirst($row["branch"]);
	                                }
	                                print "<input type='hidden' id='addnotification_branch' style='width: 200px' value='". $branch."' readonly/>";  
	                                    print "</td>";
	                                print "</tr>";
	                            }
                            ?>
                            <tr>
                                <td>Attachment</td>
                                <td><input type='file' id='notification_attachment' style='width: 200px' multiple/></td>
                            </tr>
                         </table>
						</div>
					</tr>
				</tbody>
			</table>
		</form>
	</body>
</html>