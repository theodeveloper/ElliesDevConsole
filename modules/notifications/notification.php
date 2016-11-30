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

register_menu("Notification", "submenu", "Notifications",
    Array(
        Array("title" => "View Sent Notifications",  "location" => "view_notifications",   "acl" => "view_notifications"),
        Array("title" => "Manage Notifications",  "location" => "manage_notifications",   "acl" => "manage_notifications")
    )
);

register_permission("Notification Permissions", "view_notifications", "View Sent Notifications");
register_permission("Notification Permissions", "manage_notifications", "Manage Notifications");

class Notification {
    private $items_per_page;
    
    public function __construct () {
        $this->items_per_page = Settings::getSetting(2);
    }

    public function getChannelType($branch=""){
        $channeltype="";
        if($branch !==""){
            $sql = "SELECT `type` FROM `channels` INNER JOIN `branches` ON `branches`.channel = `channels`.id WHERE `branches`.id=".$branch;
            $sqlres = mysqli_query($GLOBALS["link"],$sql);
            $row = mysqli_fetch_assoc($sqlres);
            $channeltype = $row['type'];
        }
        return $channeltype;
    }

    public function getBranch($branch=""){
        $branchname="";
        if($branch !==""){
            $sql = "SELECT `branch` FROM `branches` WHERE `id`=".$branch;
            $sqlres = mysqli_query($GLOBALS["link"],$sql);
            $row = mysqli_fetch_assoc($sqlres);
            $branchname = $row['branch'];
        }
        return $branchname;
    }

    public function view_notifications ($array) {

        print "<div class='classy_table'>";
        print "&nbsp;&nbsp;&nbsp;<span style='font-size:14px;font-weight:bold;'>View Notifications</span>";
        print "</div>";
        print "<br/><br/>";
        print '
                <body>
                    <table id="viewnotificationsetup" name="viewnotificationsetup" class="display" cellspacing="2" width="100%">
                        <thead>
                            <tr>
                                <th>Date Created</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Branch</th>           
                            </tr>
                        </thead>
                        <tbody>';
                            //Channel Type
                            $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
                            $sqlres = mysqli_query($GLOBALS["link"],$sql);
                            $row = mysqli_fetch_assoc($sqlres);
                            $channeltype = $row['type'];

                            $sysuser = new userType($_SESSION["userid"]); 
                            $query  ="";
                            if($sysuser->isSuperAdmin){  
                                $query  = "SELECT `notifications`.id,`notifications`.date_created,`notifications`.title, `notifications`.description, `notifications`.branch";
                                $query .= " FROM `notifications`";
                                $query .= " INNER JOIN `branches` ON `branches`.id = `notifications`.branch";
                                $query .= " INNER JOIN `channels` ON `channels`.id = `branches`.channel AND `channels`.type ='".$channeltype."' AND `channels`.id =".$GLOBALS['system_user']->retailChannel;
                                $query .= " WHERE `notifications`.deleted = '0'";
                                $query .= " ORDER BY `notifications`.id"; 
                            }else{
                                $query  = "SELECT `notifications`.id,`notifications`.date_created,`notifications`.title, `notifications`.description, `notifications`.branch";
                                $query .= " FROM `notifications` INNER JOIN `branches` ON `branches`.id = `notifications`.branch AND `notifications`.branch =".$GLOBALS['system_user']->branchID;
                                $query .= " WHERE `notifications`.deleted = '0'";
                                $query .= " ORDER BY `notifications`.id"; 
                            }
                            $result = mysqli_query($GLOBALS["link"],$query);
                            while ($arr = mysqli_fetch_assoc($result)) {
                               print "<tr>";
                                    print '<td align="center">'.date("Y-m-d", strtotime($arr['date_created'])).'</td>';
                                    print '<td align="center">'.$arr['title'].'</td>';
                                    print '<td align="center">'.$arr['description'] .'</td>';
                                    $branchname = $this->getBranch($arr['branch']);
                                    print '<td align="center">'.$branchname.'</td>';                 
                                print "</tr>";
                            }

                    print "</tbody>";
                    print "</table>";
                    print '<script type="text/javascript" language="javascript" class="init">
                        $(document).ready(function() {
                            $("#viewnotificationsetup").dataTable( {
                                columnDefs: [ {
                                    targets: [ 0 ],
                                    orderData: [ 0, 1 ]
                                }, {
                                    targets: [ 1 ],
                                    orderData: [ 1, 0 ]
                                }, {
                                    targets: [ 3 ],
                                    orderData: [ 3, 0 ]
                                } ],
                                "order": [[ 0, "desc" ]]
                            } );
                        } );
                    </script>';
        print "</body>";
    }

    public function manage_notifications ($array) {

        print "<div class='classy_table'>";
        print "&nbsp;&nbsp;&nbsp;<span style='font-size:14px;font-weight:bold;'>Manage Notifications</span>";
        print "</div>";
        print "<p style='float:right'><a href='#' id='editNotifications' name='editNotifications'><u>Edit</u></a> | <a href='#' id='addNotifications' name='addNotifications'><u>Send</u></a></p>";
        print "<table id='editnotificationsetup' name='editnotificationsetup' cellspacing='0' cellpadding='4' border='0' width='100%'>";
                    print "<thead>";
                        print "<tr>";
                            print "<td width='8%'  style='border: none;'>&nbsp;</td>";
                            print "<td width='36%' style='border: none;'>&nbsp;</td>";
                            print "<td width='12%' style='border: none;'>&nbsp;</td>";
                            print "<td width='36%' style='border: none;'>&nbsp;</td>";
                            print "<td width='8%' style='border: none;'>&nbsp;</td>";
                        print "</tr>";
                    print "</thead>";
                    print "<tbody>";

                        $sysuser = new userType($_SESSION["userid"]);
                        if ($sysuser->isSuperAdmin){
                            $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
                            $sqlres = mysqli_query($GLOBALS["link"],$sql);
                            $row = mysqli_fetch_assoc($sqlres);
                            $channeltype = $row['type'];
                            print "<tr>";
                                print "<td colspan='2' style='text-align: right; font-weight: 400; color: #111111; padding-right: 20px;'>";                                 
                                    print "Select Branch:";
                                    print "<input type='hidden' id='branch_id_edit' style='width: 200px' />"; 
                                print "</td>";
                                 print "<td>";   
                                    $query  = "SELECT `branches`.id AS branch_id ,`branch`";
                                    $query .= " FROM `branches` INNER JOIN `channels` ON `channels`.id = `branches`.channel WHERE `channels`.type='".$channeltype."'";
                                    $query .= " ORDER BY `branches`.id";  
                                    print "<select id='branchnotification_edit'>";
                                        print "<option value='' selected='selected'>[Please select]</option>";

                                        $result = mysqli_query($GLOBALS["link"],$query);
                                        while ($arr = mysqli_fetch_assoc($result)) {
                                            $Branch = $arr['branch'];
                                            print "<option id='".$arr['branch_id']."' value='".$arr['branch_id']."'>";
                                                print ucfirst($Branch);
                                            print "</option>";
                                        }
                                    print "</select>";
                                print "</td>";
                            print "</tr>";
                        }else{
                            print "<tr>";
                                print "<td colspan='2' style='text-align: right; font-weight: 400; color: #111111; padding-right: 20px;'>";                                 
                                    print "Branch:";
                                print "</td>";
                                 print "<td>";     
                                    print "<input type='hidden' id='branch_id_edit' style='width: 200px' value='".$GLOBALS['system_user']->branchID."'/>";
                                    $query  = "SELECT  `branch`";
                                    $query .= " FROM `branches` WHERE `id`='".$GLOBALS['system_user']->branchID."'";
                                    $query .= " ORDER BY `id`";
                                    $result = mysqli_query($GLOBALS["link"],$query);
                                    $branch_name = "";
                                    while ($arr = mysqli_fetch_assoc($result)) {
                                        $branch_name = $arr['branch'];
                                    }
                                    print "<label id='branch_name' style='width: 200px;font-weight: bold;'>".$branch_name."</label>";
                                print "</td>";
                            print "</tr>";
                        }

                        print "<tr>";
                        print "<td colspan='2' style='text-align: right; font-weight: 400; color: #111111; padding-right: 20px;'>";                                 
                            print "Select Notification";
                            print "<input type='hidden' id='notification_id' style='width: 200px' />"; 
                        print "</td>"; 
                        print "<td>";     

                             //Member permission
                            $sql = "SELECT `type` FROM `branches_profile_permissions` WHERE `user_id`=".$GLOBALS['system_user']->id." AND `branch`=".$GLOBALS['system_user']->branchID;
                            $sqlres = mysqli_query($GLOBALS["link"],$sql);
                            $row = mysqli_fetch_assoc($sqlres);
                            $member_permission = $row['type'];
                            $sysuser = new userType($_SESSION["userid"]);

                            if($member_permission == "Owner"){
                                $query  = "SELECT `notifications`.id AS notifications_id,`notifications`.date_created,`notifications`.title, `notifications`.description, `notifications`.branch";
                                $query .= " FROM `notifications` INNER JOIN `branches` ON `branches`.id = `notifications`.branch AND `notifications`.branch =".$GLOBALS['system_user']->branchID;
                                $query .= " WHERE `notifications`.deleted = '0'";
                                $query .= " ORDER BY `notifications`.id"; 
                            }
                   
                                print "<select id='notification_edit'>";
                                print "<option value='' selected='selected'>[Please select]</option>";  
                                if($member_permission == "Owner"){
                                    $result = mysqli_query($GLOBALS["link"],$query);
                                    while ($arr = mysqli_fetch_assoc($result)) {
                                        print "<option id='".$arr['notifications_id']."' value='".$arr["notifications_id"]."'>";
                                           print ucfirst($arr["title"]);
                                        print "</option>";
                                    }
                                }                  
                            print "</select>";
                        print "</td>";
                    print "</tr>";
                    print "<tr>";
                        print "<td colspan='4' style='text-align: left; padding: 0'>";
                         print "<div>";
                             print "<table cellpadding='10'>";
                                print "<tr>";
                                     print "<td>";
                                        print "Ttitle";
                                     print "</td>";
                                     print "<td>";
                                        print "<input type='text' id='title_edit' style='width: 200px' readonly/>";        
                                     print "</td>";
                                print "</tr>";

                                print "<tr>";
                                     print "<td>";
                                        print "Description";
                                     print "</td>";
                                     print "<td>";
                                        print "<textarea id='description_edit' rows='10' style='width: 200px' readonly></textarea>";        
                                     print "</td>";
                                print "</tr>";

                                print "<tr>";
                                 print "<td>";
                                    print "Branch";
                                 print "</td>";
                                 print "<td>";
                                    print "<input type='text' id='branch_edit' style='width: 200px' readonly/>"; 
                                 print "</td>";
                                print "</tr>"; 
                             print "</table>";
                         print "</div>";
                        print "</td>";
                        print "<td>&nbsp;</td>";
                    print "</tr>";
                    print "</tbody>";
                    print "<tfoot>";
                        print "<tr>";
                            print "<td colspan='3' valign='middle' style='text-align: right;'>";
                                print "<br/>";
                                print "<span id='ok-message2'></span><span id='error-message2'style='color:red'></span>";
                                print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button class='ibutton' id='delete-notification-button' style='color:red;font-weight: bold;font-size: 12px;' >Delete Notification</button>";
                                print "<br/><br/>";
                            print "</td>";
                        print "</tr>";
                    print "</tfoot>";
        print "</table>";
        print "<table id='addnotificationsetup' name='addnotificationsetup' cellspacing='0' cellpadding='4' border='0' width='100%'>";
                print "<thead>";
                    print "<tr>";
                        print "<td width='8%'  style='border: none;'>&nbsp;</td>";
                        print "<td width='36%' style='border: none;'>&nbsp;</td>";
                        print "<td width='12%' style='border: none;'>&nbsp;</td>";
                        print "<td width='36%' style='border: none;'>&nbsp;</td>";
                        print "<td width='8%' style='border: none;'>&nbsp;</td>";
                    print "</tr>";
                print "</thead>";
                print "<tbody>";
                print "<tr>";
                    print "<td colspan='4' style='text-align: left; padding: 0'>";
                     print "<div>";
                         print "<table cellpadding='10'>";
                             print "<tr>";
                                 print "<td>";
                                    print "Title";
                                 print "</td>";
                                 print "<td>";
                                    print "<input type='text' id='notification_title' style='width: 200px' />";    
                                print "</td>";
                            print "</tr>";

                            print "<tr>";
                                 print "<td>";
                                    print "Description";
                                 print "</td>";
                                 print "<td>";
                                    print "<textarea id='notification_description' rows='10' style='width: 200px'></textarea>";        
                                 print "</td>";
                            print "</tr>";

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
                                                    print "<option id='".$row['id']."' value='".$row["id"]."' selected>".ucfirst($row["name"])."</option>";
                                                }else{
                                                    print "<option id='".$row['id']."' value='".$row["id"]."'>".ucfirst($row["name"])."</option>";                
                                                }                       
                                            }
                                        print "</select>";

                                        print "<select id='notifyFranchises' name='notifyFranchises' style='display:none;' >";
                                            print "<option value=''>[Please select franchise]</option>";
                                            print "<option id='All' value='All'>All</option>";       
                                            $sql = "SELECT * FROM `channels` WHERE `type`='Franchises'";
                                            $sqlres = mysqli_query($GLOBALS["link"],$sql);
                                            while ($row=mysqli_fetch_assoc($sqlres)){
                                                if ($channelname == $row["name"]) {
                                                    print "<option id='".$row['id']."' value='".$row["id"]."' selected>".ucfirst($row["name"])."</option>";
                                                }else{
                                                    print "<option id='".$row['id']."' value='".$row["id"]."'>".ucfirst($row["name"])."</option>";                
                                                }
                                            }   
                                        print "</select>";

                                        print "<select id='notifyRetail' style='display:none; >";
                                            print "<option value=''>[Please select retail]</option>";  
                                            print "<option id='All' value='All'>All</option>"; 
                                            $sql = "SELECT * FROM `channels` WHERE `type`='Retail'";
                                            $sqlres = mysqli_query($GLOBALS["link"],$sql);
                                            while ($row=mysqli_fetch_assoc($sqlres)){
                                               if ($channelname == $row["id"]) {
                                                    print "<option id='".$row['id']."' value='".$row["id"]."' selected>".ucfirst($row["name"])."</option>";
                                                }else{
                                                    print "<option id='".$row['id']."' value='".$row["id"]."'>".ucfirst($row["name"])."</option>";                
                                                }
                                            }  
                                        print "</select>";

                                    print "<input type='hidden' id='notification_channeltype'  name='notification_channeltype' style='width: 200px' />";    
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
                                    $branch =  ucfirst($row["id"]);
                                }
                                print "<input type='hidden' id='addnotification_branch' style='width: 200px' value='". $branch."' readonly/>";  
                                    print "</td>";
                                print "</tr>";
                            }


                            /*print "<tr>";
                                 print "<td>";
                                    print "Attachment";
                                 print "</td>";
                                 print "<td>";
                                    print "<input type='file' id='notification_attachment style='width: 200px' multiple/>";    
                                print "</td>";
                            print "</tr>";*/
                
                         print "</table>";
                     print "</div>";
                    print "</td>";
                    print "<td>&nbsp;</td>";
                print "</tr>";
                print "</tbody>";
                print "<tfoot>";
                    print "<tr>";
                        print "<td colspan='3' valign='middle' style='text-align: right;'>";
                            print "<br/>";
                            print "<span id='ok-message'></span><span id='error-message'style='color:red'></span>";
                            print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button class='ibutton' id='add-notification-button'>Send Notification</button>";
                            print "<br/><br/>";
                        print "</td>";
                    print "</tr>";
                print "</tfoot>";
        print "</table>";
        //==========================================
        print '<script type="text/javascript" language="javascript" class="init">';
        print "jQuery('a#addNotifications').click(function () {
                $('table#editnotificationsetup').hide();
                $('table#addnotificationsetup').show(); 
            });\n
        ";
        
        print "$('table#editnotificationsetup').hide();
            jQuery('a#editNotifications').click(function () {
                $('table#addnotificationsetup').hide();
                $('table#editnotificationsetup').show(); 
            });\n
        ";

        print "jQuery('select#branchnotification_edit').change(function () { 
                    var id = $('select#branchnotification_edit').children(':selected').attr('id');               
                    $('input#branch_id_edit').val(id); 
                    AJAXCallNotification('" . __CLASS__ . "', 'getNotifications', 'branchID='+id);  
                });\n
        ";

        print "jQuery('select#notification_edit').change(function () { 
                    var id = $('select#notification_edit').children(':selected').attr('id');               
                    $('input#notification_id').val(id); 
                    AJAXCallNotificationInfo('" . __CLASS__ . "', 'getNotificationInfo', 'notificationID='+id);  
                });\n
            ";

        print "jQuery('select#notifychanneltype').change(function () { 
                  var type = $('select#notifychanneltype').val(); 
                  $('input#notification_channeltype').val(type); 
                  if(type == 'Commercial'){
                    $('select#notifyFranchises').hide();
                    $('select#notifyRetail').hide(); 
                    $('select#notifyCommercial').val('');  
                    $('select#notifyCommercial').toggle();
                  }else if(type == 'Franchises'){
                    $('select#notifyCommercial').hide();
                    $('select#notifyRetail').hide(); 
                    $('select#notifyFranchises').val('');  
                    $('select#notifyFranchises').toggle();
                  }else if(type == 'Retail'){
                    $('select#notifyCommercial').hide();
                    $('select#notifyFranchises').hide(); 
                    $('select#notifyRetail').val('');  
                    $('select#notifyRetail').toggle();
                  }else if(type == 'All'){
                    $('select#notifyCommercial').val('');  
                    $('select#notifyRetail').val('');  
                    $('select#notifyFranchises').val('');  

                    $('select#notifyCommercial').hide();
                    $('select#notifyFranchises').hide(); 
                    $('select#notifyRetail').hide();
                  }       
                });\n";

        print "jQuery('#add-notification-button').click(function () {  
                    var title  = jQuery('input#notification_title').val(); 
                    var description  = jQuery('textarea#notification_description').val(); 
                    var type = $('input#notification_channeltype').val(); 
                    var channeltype = $('select#notifychanneltype').val(); 
                    var branch ='';

                    if(channeltype == 'Commercial'){
                        branch = $('select#notifyCommercial').attr('id');
                    }else if(channeltype == 'Retail'){
                        branch = $('select#notifyRetail').attr('id');
                    }else if(channeltype == 'Franchises'){
                        branch = $('select#notifyFranchises').val();
                    }else if(channeltype == 'All'){
                        branch = 'All';
                    }else if(branch == 'All'){
                        branch = 'All';
                    }else{
                        branch =  $('input#addnotification_branch').val();  
                    }
                    //alert('branch:'+branch);
                    AJAXCallModuleJSOnly('" . __CLASS__ . "', 'save_notification', 'title='+title+'&description='+description+'&branch='+branch+'&channel='+channeltype);

                });\n
            "; 
        print "jQuery('#delete-notification-button').click(function () {
                    var notification  = jQuery('input#notification_id').val(); 
                    AJAXCallModuleJSOnly('" . __CLASS__ . "', 'delete_notification', 'notification='+notification);
                });\n
            "; 
        print '</script>';
        print "<div style='margin: 0 auto; width: 95%; border-bottom: 2px solid #BBBBBB;'>&nbsp;</div>";
        print '</div>';
    }
    //=======================================
    //Notification
    public function save_notification($array) {

        if ($GLOBALS['system_user']->hasPermission('manage_notifications')) {
            $branch =$array["branch"];
            $channel =$array["channel"];

            if($array["branch"] =="") $branch = $GLOBALS['system_user']->branchID;
            $title =$array["title"];
            if($title == "")$title ="(NONE)";

            if($channel =="Commercial" && $branch !=""){
                $this->getNotifyAllBranches("Commercial",$branch,$title,$array["description"]);
                exit();  
            }elseif($channel =="Retail" && $branch !=""){
                $this->getNotifyAllBranches("Retail",$branch,$title,$array["description"]);
                exit();  
            }elseif($channel =="Franchises" && $branch !=""){
                $this->getNotifyAllBranches("Franchises",$branch,$title,$array["description"]);
                exit();  
            }elseif($branch =="All"){
                if($channel =="Commercial")$this->getNotifyAllBranches("Commercial","",$title,$array["description"]);
                if($channel =="Retail")$this->getNotifyAllBranches("Retail","",$title,$array["description"]); 
                if($channel =="Franchises")$this->getNotifyAllBranches("Franchises","",$title,$array["description"]); 
                exit();
            }else{
                $query  = "INSERT INTO `notifications` (date_created,title,description,branch,status) VALUES (";
                $query  .= "NOW(),";
                $query  .= " '".$title. "',";
                $query  .= " '".$array["description"]."',";
                $query  .= " '".$branch."',";
                $query  .= " 'View'";
                $query  .= ") ";
                $result = mysqli_query($GLOBALS["link"],$query);
                if($result) {
                    print "jQuery(\"#ok-message\").html(\"Notification saved... \").show(0).delay(4000).hide(0);\n";
                    logAction("Added Notification:$title");
                    $array["branch_type"] = $this->getChannelType($branch);

                    //Save Notification Log
                    $this->save_notification_summary_log($array,1);
                } else {
                    print "jQuery(\"#error-message\").html(\"Unable to save Notification saved Inforamtion! Please try again...\").show(0).delay(4000).hide(0);\n";
                }  
                exit();  
            }

            /*if($branch =="Commercial"){
                $this->getNotifyAllBranches("Commercial",$title,$array["description"]);
                exit();  
            }elseif($branch =="Retail"){
                $this->getNotifyAllBranches("Retail",$title,$array["description"]); 
                exit();
            }elseif($branch =="Franchises"){
                $this->getNotifyAllBranches("Franchises",$title,$array["description"]); 
                exit();
            }elseif($branch =="All"){
                $this->getNotifyAllBranches("Commercial",$title,$array["description"]);
                $this->getNotifyAllBranches("Retail",$title,$array["description"]); 
                $this->getNotifyAllBranches("Franchises",$title,$array["description"]); 
                exit();
            }else{
                $query  = "INSERT INTO `notifications` (date_created,title,description,branch,status) VALUES (";
                $query  .= "NOW(),";
                $query  .= " '".$title. "',";
                $query  .= " '".$array["description"]."',";
                $query  .= " '".$branch."',";
                $query  .= " 'View'";
                $query  .= ") ";
                $result = mysqli_query($GLOBALS["link"],$query);
                if($result) {
                    print "jQuery(\"#ok-message\").html(\"Notification saved... \").show(0).delay(4000).hide(0);\n";
                    logAction("Added Notification:$title");
                    $array["branch_type"] = $this->getChannelType($branch);

                    //Save Notification Log
                    $this->save_notification_summary_log($array,1);
                } else {
                    print "jQuery(\"#error-message\").html(\"Unable to save Notification saved Inforamtion! Please try again...\").show(0).delay(4000).hide(0);\n";
                }  
                exit();  
            }*/
        }else{
            print "You do not have permission to perform this action.";
            exit();            
        }
    }

    public function delete_notification  ($array) {
        if ($GLOBALS['system_user']->hasPermission('manage_notifications')) {
            $sql = "UPDATE notifications SET";
            $sql .= " `last_updated` =NOW(),";
            $sql .= " `deleted` = '1'";
            $sql .= " WHERE id=".$array["notification"]." LIMIT 1";
            $result = mysqli_query($GLOBALS["link"],$sql);
            if($result) {
                print "jQuery(\"#ok-message2\").html(\"Notification deleted...  \").show(0).delay(4000).hide(0);\n";
                $title = $array["notification"];
                logAction("Deleted Notification:$title");
            } else {
               print "jQuery(\"#error-message2\").html(\"Unable to delete Notification Inforamtion! Please try again...\").show(0).delay(4000).hide(0);\n";
            }  
            exit();
        }else{
            print "You do not have permission to perform this action.";
            exit();            
        }
    }

    public function getNotifications  ($array) {
        $branchID = (int)isset($_POST["branchID"])?$_POST["branchID"]:0;
        $query  = "SELECT *";
        $query .= " FROM `notifications`";
        $query .= " WHERE `notifications`.branch= ".$branchID;

        if($GLOBALS['system_user']->hasPermission('manage_notifications')) {                    
            $result = mysqli_query($GLOBALS["link"],$query);
            $array = array();
            while ($arr = mysqli_fetch_assoc($result)){
                $array[] = $arr; 
            }
            print json_encode($array);
        } else {
            print "jQuery(\"#error-message2\").html(\"Unable to get Company Notifications information! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
        }
    }

    public function getNotificationInfo(){
        $notificationID = (int)isset($_POST["notificationID"])?$_POST["notificationID"]:0;
        $query  = "SELECT *, `branches`.branch AS branch_name";
        $query .= " FROM `notifications` INNER JOIN `branches` ON `branches`.id =  `notifications`.branch";
        $query .= " WHERE `notifications`.id= ".$notificationID;

        if($GLOBALS['system_user']->hasPermission('manage_notifications')) {                    
            $result = mysqli_query($GLOBALS["link"],$query);
            $arr = mysqli_fetch_assoc($result);
            print json_encode($arr);
         } else {
            print "jQuery(\"#error-message2\").html(\"Unable to get Notification information! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
         }
    }

    public function getSpecificBranches($channel=0){
        $sql = "SELECT `id` FROM `branches` WHERE `channel`=".$channel;
        $result = mysqli_query($GLOBALS["link"],$sql);
        $arrBranches = array();
        while ($arr = mysqli_fetch_assoc($result)){
            $arrBranches[] = $arr['id']; 
        }
        return $arrBranches;
    }

    public function getAllBranches($channel=""){
        //Channel Type
        $sql = "SELECT * FROM `channels` WHERE `type`='".$channel."'";
        $result = mysqli_query($GLOBALS["link"],$sql);
        $array = array();
        while ($arr = mysqli_fetch_assoc($result)){
            $array[] = $arr['id']; 
        }
        $arrBranches = array();
        for ($i=0; $i <count($array) ; $i++) { 
            $sql = "SELECT `id` FROM `branches` WHERE `channel`=".$array[$i];
            $result = mysqli_query($GLOBALS["link"],$sql);
            while ($arr = mysqli_fetch_assoc($result)){
                $arrBranches[] = $arr['id']; 
            }
        }
        return $arrBranches;
    }

    public function getNotifyAllBranches($channel="",$branchID="",$title="",$description=""){
        $branch = array();
        if($branchID =="")$branch = $this->getAllBranches($channel);
        if($channel !="" && $branchID !="")$branch = $this->getSpecificBranches($branchID);
        $count = 0;

        for ($i=0; $i < count($branch) ; $i++) { 
            $query  = "INSERT INTO `notifications` (date_created,title,description,branch,status) VALUES (";
            $query  .= "NOW(),";
            $query  .= " '".$title. "',";
            $query  .= " '".$description."',";
            $query  .= " '".$branch[$i]."',";
            $query  .= " 'View'";
            $query  .= ") ";
            $result =mysqli_query($GLOBALS["link"],$query);
            if($result) {
                $count++;
            } 
        }
        if($count == count($branch)) {
            print "jQuery(\"#ok-message\").html(\"Notification saved... \").show(0).delay(4000).hide(0);\n";
            logAction("Added Notification:$title");

            //Save Notification Log
            $this->save_notification_summary_log($array,$count);
        } else if(count($branch) ==0){
            print "jQuery(\"#error-message\").html(\"There are no branches available! Please try again...\").show(0).delay(4000).hide(0);\n";
        } else {
            print "jQuery(\"#error-message\").html(\"Unable to save Notification saved Inforamtion! Please try again...\").show(0).delay(4000).hide(0);\n";
        } 
    }

    public function save_notification_summary_log($array,$total_sent=0) {

        if ($GLOBALS['system_user']->hasPermission('manage_notifications')) {

            $Branch = $array["branch"];
            $branch_type ="";
            if($array["branch_type"] !="")$branch_type = $array["branch_type"];
            $Title = $array["title"];
            $Description = $array["description"];

            if($Branch =="Commercial"){
                $query  = "INSERT INTO `notification_summary_log` (date_created,title,description,branch_type,total_sent) VALUES (";
                $query  .= "NOW(),";
                $query  .= " '".$Title. "',";
                $query  .= " '".$Description. "',";
                $query  .= " '".$branch. "',";
                $query  .= " '".$total_sent. "'";
                $query  .= ") ";
                $result = mysqli_query($GLOBALS["link"],$query);     
                if($result) {
                    logAction("Added Notification Summary Log:$Title");
                    return true;
                } else {
                    return false;
                }  
                exit();  
            }elseif($Branch =="Retail"){
                $query  = "INSERT INTO `notification_summary_log` (date_created,title,description,branch_type,total_sent) VALUES (";
                $query  .= "NOW(),";
                $query  .= " '".$Title. "',";
                $query  .= " '".$Description. "',";
                $query  .= " '".$branch. "',";
                $query  .= " '".$total_sent. "'";
                $query  .= ") ";
                $result = mysqli_query($GLOBALS["link"],$query);     
                if($result) {
                    logAction("Added Notification Summary Log:$Title");
                    return true;
                } else {
                    return false;
                }  
                exit(); 
            }elseif($Branch =="Franchises"){
                $query  = "INSERT INTO `notification_summary_log` (date_created,title,description,branch_type,total_sent) VALUES (";
                $query  .= "NOW(),";
                $query  .= " '".$Title. "',";
                $query  .= " '".$Description. "',";
                $query  .= " '".$branch. "',";
                $query  .= " '".$total_sent. "'";
                $query  .= ") ";
                $result = mysqli_query($GLOBALS["link"],$query);     
                if($result) {
                    logAction("Added Notification Summary Log:$Title");
                    return true;
                } else {
                    return false;
                }  
                exit();
            }else{
                $query  = "INSERT INTO `notification_summary_log` (date_created,title,description,branch_type,total_sent) VALUES (";
                $query  .= "NOW(),";
                $query  .= " '".$Title. "',";
                $query  .= " '".$Description. "',";
                $query  .= " '".$branch_type. "',";
                $query  .= " '".$total_sent. "'";
                $query  .= ") ";
                $result = mysqli_query($GLOBALS["link"],$query);     
                if($result) {
                    logAction("Added Notification Summary Log:$Title");
                    return true;
                } else {
                    return false;
                }
                exit();
            }
        }
    }
}