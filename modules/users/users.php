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
  //  register_menu("users", "parentMenu", "Branches and Users");
    register_menu("users", "submenu", "Management",
        Array(
            //Array("title" => "New Quotation", "location" => "edit_quotation", "acl" => "edit_quotation"),
            //Array("title" => "Quotations",  "location" => "quotations_entry",   "acl" => "quotations_entry"),
            Array("title" => "View Team Member Profile(s)",  "location" => "view_team_profiles",   "acl" => "view_team_profiles"),
            Array("title" => "Manage Team Member Profile(s)",  "location" => "manage_team_member_profile",   "acl" => "manage_team_member_profile"),
            Array("title" => "Manage Teams",  "location" => "manage_teams",   "acl" => "manage_teams"),
            Array("title" => "Users / Staff",  "location" => "manage_users",   "acl" => "users_edit_user"),
            Array("title" => "Locations",  "location" => "location_list",   "acl" => "users_edit_user")
        )
    );
    
    register_menu("users", "parentMenu", "Branches and Users");
    register_permission("User Permissions", "users_create_new_superadmin", "Create a new Super administrator/Make normal user Super admin");
    register_permission("User Permissions", "users_create_new_user",       "Create a new User");
    register_permission("User Permissions", "users_deactivate_users",      "Deactivate Users");
    register_permission("User Permissions", "users_edit_user",             "Edit existing User");
    register_permission("User Permissions", "users_modify_permissions",    "Modify user permissions");
    register_permission("User Permissions", "users_move_user",             "Move users from one branch to another");
    register_permission("User Permissions", "users_view_all_users",        "View all system users (default: View only users at own branch)");
    register_permission("User Permissions", "users",                       "Access to Users module");
    register_permission("User Permissions", "users_view_own_permissions",  "View own permissions only");
    register_permission("User Permissions", "users_view_all_permissions",  "View permissions of all users (takes precedence over above permission)");
    
    register_permission("Branch Permissions", "users_add_new_branch",   "Add a new branch");
    register_permission("Branch Permissions", "users_delete_branches",  "Delete branches");
    register_permission("Branch Permissions", "users_edit_branch_info", "Edit existing branch info");

    register_permission("Registration Permissions", "view_team_profiles", "View Team Member Profiles");
    register_permission("Registration Permissions", "manage_team_member_profile", "Manage Team Member Profiles");
    register_permission("Registration Permissions", "manage_teams",  "Manage Teams");

    class Users {
        
        private $items_per_page;
        
        function __construct () {
            $this->items_per_page = Settings::getSetting(2);
        }
        
        function manage_users () {
            $can_delete_branches   = $GLOBALS['system_user']->hasPermission("users_delete_branches");
            $can_edit_branch_info  = $GLOBALS['system_user']->hasPermission("users_edit_branch_info");
            $can_view_all_users    = $GLOBALS['system_user']->hasPermission("users_view_all_users");
            $can_view_online_users = $can_view_all_users;// && $GLOBALS['system_user']->id == 1;
            
            print "<input type='hidden' id='chosen-branch' value='0' />";
            
            print "<div id='users-container' class='same-bg'>";
                print "<table id='main-table' cellpadding='0' cellspacing='0' border='0'>";
                    print "<tr>";
                        $view_all_display = $can_view_all_users ? "" : "style='display: none;'";
                        print "<td id='branches-td' $view_all_display valign='top' width='20%'>";
                            print "<br />";
                            print "<div id='site-name' class='same-bg'>";
                                print "<img src='images/treeOpen.gif' class='toggle-branch-list' align='top' />&nbsp;&nbsp;";
                                print "<span class='main-branch' style='color: #333333; font: 900 13px Arial,sans-serif;'>";
                                    $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
                                    $sqlres = mysqli_query($GLOBALS["link"],$sql);
                                    $row = mysqli_fetch_assoc($sqlres);
                                    $channeltype = $row['type'];
                                    if ($channeltype =="Commercial")print "Ellies Commercial";
                                    if ($channeltype =="Retail")print "Ellies Retail";
                                    if ($channeltype =="Franchises")print "Ellies Franchises";
                                    //print SITE_TITLE;
                                print "</span>";
                            print "</div>";
                            $usesstores = true;
                            if ($usesstores) {    
                                print "<div id='branch-list'>";
                                     ////Theo changed
                                    $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
                                    $sqlres = mysqli_query($GLOBALS["link"],$sql);
                                    $row = mysqli_fetch_assoc($sqlres);
                                    $channeltype = $row['type'];

                                    $query = "SELECT branches.id, branches.branch, branches.address, channels.name FROM `branches`,channels WHERE `deleted` = 0 and branches.channel=channels.id and channels.type ='".$channeltype."'";

                                    $sysuser = new userType($_SESSION["userid"]);
                                    if (!$sysuser->isSuperAdmin)$query .= " AND `branches`.`id` =".$GLOBALS['system_user']->branchID;
                                    /*
                                        if ($GLOBALS['system_user']->branchID == 37){
                                          $query .= " AND `branches`.`id`=37";
                                        } else {
                                            $query .= " AND `branches`.`id`!=37";
                                        }
                                    */
                                    $query .= " ORDER by name,branch";
                                    $result = mysqli_query($GLOBALS["link"],$query);
                                    while ($arr = mysqli_fetch_assoc($result)) {
                                        print "<div class='branch-divider'>";
                                        print "<a href='javascript:void()' style='color:#6dad1f' onclick='ListStores(".$arr['id'].");' id='branch-divider-".$arr['id']."' class='branch-divider-item'>".$arr['name']." - ".$this->trimBranchName($arr['branch'], strlen(SITE_TITLE))."</a><br>";          
                                            print "<div class='branch-stores' id='branch-stores-".$arr['id']."'>";
                                            print "</div>";
                                        print "</div>";
                                    }
                               print "</div>";                                
                                print "<div id='branch-list' style='display:none'>";
                                    print "<input type='hidden' id='page-number' value='1' />";
                                    $query = "SELECT `id`, `branch`, `address`, `channel` FROM `branches` WHERE `deleted` = 0 ORDER by `branch`";
                                    $result = mysqli_query($GLOBALS["link"],$query);
                                    while ($arr = mysqli_fetch_assoc($result)) {
                                        print "<div class='all-branches' id='all-branches-".$arr['id']."' " . (strlen($arr['branch']) > strlen(SITE_TITLE) ? "title='".$arr['branch']."'" : "") .">";
                                            print "<input type='hidden' class='branch-ids' value='" . $arr['id'] . "'>";
                                            print "<input type='hidden' class='branch-channels' value='" . $arr['channel'] . "'>";
                                            print "<input type='hidden' class='branch-names' value='" . htmlentities($arr['branch'], ENT_QUOTES) . "'>";
                                            print "<input type='hidden' class='branch-addresses' value='" . htmlentities($arr['address'], ENT_QUOTES) . "'>";
                                        print "</div>";
                                    }
                                print "</div>";
                            }else{
                                print "<div id='branch-list'>";
                                    print "<input type='hidden' id='page-number' value='1' />";
                                    $query = "SELECT `id`, `branch`, `address`, `channel` FROM `branches` WHERE `deleted` = 0 ORDER by `branch`";
                                    $result = mysqli_query($GLOBALS["link"],$query);
                                    while ($arr = mysqli_fetch_assoc($result)) {
                                        print "<div class='all-branches' id='all-branches-".$arr['id']."' " . (strlen($arr['branch']) > strlen(SITE_TITLE) ? "title='".$arr['branch']."'" : "") .">";
                                            print "<input type='hidden' class='branch-ids' value='" . $arr['id'] . "'>";
                                            print "<input type='hidden' class='branch-channels' value='" . $arr['channel'] . "'>";
                                            print "<input type='hidden' class='branch-names' value='" . htmlentities($arr['branch'], ENT_QUOTES) . "'>";
                                            print "<input type='hidden' class='branch-descriptions' value='" . htmlentities($arr['description'], ENT_QUOTES) . "'>";
                                            print "<input type='hidden' class='branch-addresses' value='" . htmlentities($arr['address'], ENT_QUOTES) . "'>";
                                            print $this->trimBranchName($arr['branch'], strlen(SITE_TITLE));
                                        print "</div>";
                                    }
                                print "</div>";
                            }
                            if ($can_view_online_users) {
                                print "<span class='ou-head'>Online users</span>";
                                print "<div id='online-user' class='online-users'>";
                                    $query  = "SELECT `system_sessions`.first, `system_sessions`.last, `system_sessions`.username, `system_sessions`.ip_address";
                                    $query .= " FROM `system_sessions`";
                                    $query .= " INNER JOIN `system_users` ON `system_users`.username = `system_sessions`.username AND `system_users`.branch_id ='".$GLOBALS['system_user']->branchID."'";
                                    $query .= " WHERE `lastActivity` > NOW() - INTERVAL 15 MINUTE";
                                    //$query .= " AND `username` != \"" . mysqli_real_escape_string($GLOBALS["link"],$GLOBALS['system_user']->username) . "\"";
                                    $query .= " GROUP BY `username`";
                                    $query .= " ORDER BY `first`, `last`";
                                    $query .= " LIMIT 100";
                                    $result = mysqli_query($GLOBALS["link"],$query);
                                    if (mysqli_num_rows($result) == 0) {
                                        print "No users online...";
                                    } else {
                                        print "<table cellspacing='0' cellpadding='0' border='0' width='100%'>";
                                        while ($arr = mysqli_fetch_assoc($result)) {
                                            print "<tr>";
                                                print "<th>";
                                                    print $arr['first'] . " " . $arr['last'];
                                                print "</th>";
                                                print "<td>";
                                                    print $arr['ip_address'];
                                                print "</td>";
                                            print "</tr>";
                                        }
                                        print "</table>";
                                    }
                                print "</div>";
                            }
                            
                                $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
                                $sqlres = mysqli_query($GLOBALS["link"],$sql);
                                $row = mysqli_fetch_assoc($sqlres);
                                $channeltype = $row['type'];   
                                if($channeltype =="Commercial")print "<span class='ou-head'>Commercial</span>";
                                if($channeltype =="Retail")print "<span class='ou-head'>Retail</span>";
                                if($channeltype =="Franchises")print "<span class='ou-head'>Franchises</span>";
                                print "<div id='online-user2' class='online-users'>";
                                    $query  = "SELECT * from channels where id='".$GLOBALS['system_user']->retailChannel."'";
                                    $sysuser = new userType($_SESSION["userid"]);        
                                    if ($sysuser->isSuperAdmin)$query  = "SELECT * from channels where type='".$channeltype."'";
                                    $query .= " ORDER BY `name`";
                                    $query .= " LIMIT 100";
                                    $result = mysqli_query($GLOBALS["link"],$query);
                                    if (mysqli_num_rows($result) == 0) {
                                        print "No channels";
                                    } else {
                                        print "<table cellspacing='0' cellpadding='0' border='0' width='100%'>";
                                        while ($arr = mysqli_fetch_assoc($result)) {
                                            print "<tr>";
                                                print "<th>";
                                                    print $arr['name'];
                                                print "</th>";
                                                print "<td>";
                                                    print $arr['id'];
                                                print "</td>";
                                            print "</tr>";
                                        }
                                        print "</table>";
                                    }
                                print "</div>";
                            print "</td>";
                        print "<td valign='top' class='same-bg' width='1%'>";
                            print "&nbsp";
                        print "</td>";
                        print "<td valign='top' width='79%'>";
                            print "<table id='second-table' cellpadding='0' cellspacing='0' border='0' width='100%'>";
                                print "<tr class='same-bg'>";
                                    print "<td style='font-weight: 900;'>";
                                        print "<br/>";
                                        print "<span id='main-branch-right' class='main-branch' style='color: #333333; font: 900 13px Arial,sans-serif;'>";
                                            print SITE_TITLE;
                                        print "</span>";
                                        print "<span id='main-branch-raquo'></span>";
                                    print "</td>";
                                    print "<td style='text-align: right;'>";
                                    print "<br/>";
                                        $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
                                        $sqlres = mysqli_query($GLOBALS["link"],$sql);
                                        $row = mysqli_fetch_assoc($sqlres);
                                        $channeltype = $row['type'];

                                        // if ($GLOBALS['system_user']->hasPermission("users_add_new_store") &&  ($channeltype != "Commercial" || $channeltype !="Franchises")) {
                                        if ($GLOBALS['system_user']->hasPermission("users_add_new_store")){
                                            if($channeltype =="Franchises"){
                                                print "<button id='add-channel' class='ibutton' style='width: 111px;'>Add franchisee</button>&nbsp;";
                                            }else{
                                                print "<button id='add-channel' class='ibutton' style='width: 105px;'>Add channel</button>&nbsp;";
                                            }
                                        }
  
                                        //if (($GLOBALS['system_user']->hasPermission("users_add_new_branch")) &&  ($channeltype != "Commercial" || $channeltype !="Franchises")) {
                                        if ($GLOBALS['system_user']->hasPermission("users_add_new_branch")) {
                                            print "<button id='add-branch' class='ibutton' style='width: 105px;'>Add area</button>&nbsp;";
                                            if($channeltype =="Franchises"){
                                                print "<button id='add-store' class='ibutton' style='width: 105px;'>Add outlet</button>&nbsp;";
                                            }else{
                                                print "<button id='add-store' class='ibutton' style='width: 105px;'>Add store</button>&nbsp;";
                                            }
                                        } else {
                                            print "<br/>";
                                        }                                       

                                        $can_view_more_branch_actions = $can_delete_branches || $can_edit_branch_info;
                                        //if ($can_view_more_branch_actions &&  ($GLOBALS['system_user']->branchID != 37)) {
                                        //if ($can_view_more_branch_actions &&  ($channeltype != "Commercial" || $channeltype !="Franchises")) {
                                        if ($can_view_more_branch_actions) {
                                            print "<div style='display: inline; width: auto;'>";
                                                print "<div style='display: inline-block;'>";
                                                    print "<button id='branch-more-actions' style='display: none; width: 115px;'>More actions&nbsp;<img src='images/downbutton.png' align='baseline'></button>&nbsp;";
                                                print "</div>";
                                                print "<div></div>";
                                                print "<div id='branch-more-actions-div' class='branch-more-actions-div branch-open' style='color: #333333; font-size: 11px; text-align: left; padding-left: 16px;margin-top: -1px; display: none; background-color: #CDCDCD; border: 1px solid #999999; margin-left: -118px; width: 99px; z-index: 200; position: absolute'>";
                                                    if ($can_edit_branch_info) {
                                                        print "<div class='branch-open' style='cursor: pointer; padding-top: 5px;' id='edit-branch'>Edit area</div>";
                                                    }
                                                    if ($can_delete_branches) {
                                                        print "<div class='branch-open' style='cursor: pointer; padding-top: 5px; padding-bottom: 5px;' id='delete-branch'>Delete area</div>";
                                                    }
                                                print "</div>";
                                            print "</div>";
                                        }
                                    print "</td>";
                                print "</tr>";
                                print "<tr class='same-bg'>";
                                    print "<td colspan='2' id='branch-description' style='padding-left: 10px;'>";
                                        print "Super administrators";
                                    print "<td>";
                                print "</tr>";
                                print "<tr class='same-bg'>";
                                    print "<td colspan='2' align='center'>";
                                        print "&nbsp;";
                                        print "<span id='ok-message'></span>";
                                        print "<span id=error-message></span>";
                                    print "<td>";
                                print "</tr>";
                                print "<tr>";
                                    print "<td colspan='2'>";
                                        print "<div id='tabs'>";
                                            print "<ul>";
                                                print "<li><a href='#users-tab'>Users</a></li>";
                                                print "<li id='permissions-li' style='display: none;'><a href='#upermissions-tab'>Permissions</a></li>";
                                            print "</ul>";
                                            print "<div id='users-tab'>";
                                            print "</div>";
                                            print "<div id='upermissions-tab' style='display: none;'>";
                                            print "</div>";
                                        print "</div>";
                                    print "</td>";
                                print "</tr>";
                            print "</table>";
                        print "</td>";
                print "</tr>";
                print "</table>";
            print "</div>";
            
            $this->printAddBranchDialog();
            $this->printAddStoreDialog();
            $this->printAddChannelDialog();
            $this->printCreateUserDialog();
            $this->addUserToGroup();
            
            print "\n<!-- scripts , code below be eval()ed by javascript -->\n";
            
            print "jQuery(\"#tabs\").tabs({ selected: 0 });";
            if ($can_view_all_users) {
                print "AJAXCallModuleJSOnly('Users','printTabContent', 'branchid=0&page=1');\n";
            } else {
                print "AJAXCallModuleJSOnly('Users','printTabContent', 'branchid=" . $GLOBALS['system_user']->branchID . "&page=1');\n";
            }
            
            if ($can_view_online_users) {
                print "intvl = window.setInterval(\"getActiveUsers('" . __CLASS__ . "', 'getActiveUsers', '')\", 45000);";
            }
            
             if ($can_view_online_users) {
                print "intvl2 = window.setInterval(\"getActiveUsers('" . __CLASS__ . "', 'getActiveUsers', '')\", 45000);";
            }
        }
        function getActiveUsers ($array) {
            $query  = "SELECT `first`, `last`, `ip_address`";
            $query .= " FROM `system_sessions`";
            $query .= " WHERE `lastActivity` > NOW() - INTERVAL 15 MINUTE";
            //$query .= " AND `username` != \"" . mysqli_real_escape_string($GLOBALS["link"],$GLOBALS['system_user']->username) . "\"";
            $query .= " GROUP BY `username`";
            $query .= " ORDER BY `first`, `last`";
            $query .= " LIMIT 100";
            $result = mysqli_query($GLOBALS["link"],$query);
            print "jQuery('#online-user').html(\"";
            if (mysqli_num_rows($result) == 0) {
                print "No users online...";
            } else {
                print "<table cellspacing='0' cellpadding='0' border='0' width='100%'>";
                while ($arr = mysqli_fetch_assoc($result)) {
                    print "<tr>";
                        print "<th>";
                            print $arr['first'] . " " . $arr['last'];
                        print "</th>";
                        print "<td>";
                            print $arr['ip_address'];
                        print "</td>";
                    print "</tr>";
                }
                print "</table>";
            }
            print "\");\n";
        }
        
        function printTabContent ($array) {
            
            $branchid = intval($array['branchid']);
            $page     = intval($array['page']);
            
            $my_branch         = $this->getBranch($branchid);
            $branchName        = $my_branch['branch'];
            $branchAddress = $my_branch['address'];
            
            $query  = "SELECT SQL_CALC_FOUND_ROWS `system_users`.`id`, `system_users`.`super_admin`, `system_users`.`first`, `system_users`.`last`,  `system_users`.`username`, `system_users`.`email`, IFNULL(`branches`.`branch`, '-') AS branch, `system_users`.`date_added`, IF(`system_users`.`active` = 1, 'Yes', 'No') AS active, `system_users`.`lastlogin`";
            $query .= ", (SELECT `store` FROM `stores` WHERE `id` = `system_users`.`store_id`) AS `store_name`";
            $query .= " FROM `system_users`";
            $query .= " LEFT OUTER JOIN `branches` ON `system_users`.`branch_id` = `branches`.`id`";
            $query .= " WHERE 1=1";
            if ($branchid == 0) {
                $query .= " AND `system_users`.`super_admin` = 1";
                $query .= " AND `branches`.`id` =".$GLOBALS['system_user']->branchID;
            } else {
                if (Settings::getSetting(4) == 0) {
                   $query .= " AND `system_users`.`super_admin` = 0";
                }
                $query .= " AND `branches`.`id` = " . mysqli_real_escape_string($GLOBALS["link"],$branchid);
            }

            /*
            if ($GLOBALS['system_user']->branchID == 37){
              $query .= " AND `branches`.`id`=37";
            } else {
                $query .= " AND `branches`.`id`!=37";
            }
            */
            //$query .= " AND `branches`.`id` =".$GLOBALS['system_user']->branchID;
            //echo  $query;

            
            if (!empty($array["storeid"])) {
                $query.= " AND `system_users`.`store_id` = '".mysqli_real_escape_string($GLOBALS["link"],$array["storeid"])."'";
                $_SESSION["storeid"] = $array["storeid"];
            }
            $_SESSION["branchid"] = $array["branchid"];
            $query .= " ORDER BY `system_users`.`first`";
            $query .= " LIMIT " . ( $this->items_per_page * ($page - 1) ) . ", " . $this->items_per_page;

            $result = mysqli_query($GLOBALS["link"],$query);
            $result2 = mysqli_query($GLOBALS["link"],"SELECT FOUND_ROWS()");
            $farr = mysqli_fetch_row($result2);
            $total_items = $farr[0];
            $my_s = $total_items == 1 ? "" : "s";
            
            $no_of_pages = ceil($total_items/$this->items_per_page);

            $branchName = str_ireplace("\n", "", trim($branchName));
            $branchName = str_ireplace("\r", "", $branchName);
            $branchName = str_ireplace("\r", "", $branchName);
            $branchName=str_replace('"','\"',$branchName);
            $branchName=str_replace(" \ ",' ',$branchName);
            $branchName=str_replace("'",'&#039;',$branchName);
            $branchName=preg_replace('/[\t\n\r\0\x0B]/', '', $branchName);
            
            if ($branchid == 0) {
                $sysno = 0;
                if ($GLOBALS['system_user']->hasPermission("users_view_all_users")) {
                    $r = mysqli_query($GLOBALS["link"],"SELECT FORMAT(COUNT(`id`),0) AS c FROM `system_users`");
                    if ($arr   = mysqli_fetch_assoc($r)) $sysno = $arr['c'];
                }
                $sysno_s = $sysno == 1 ? "" : "s";
                
                print "jQuery(\"#main-branch-raquo\").html(\"<span style='color: #555; font-size: 11px; font-style: italic;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;($sysno system user$sysno_s)</span>\");";
                //print "jQuery(\"#branch-description\").html(\"Super administrators\");";
            }else {
                print "jQuery(\"#main-branch-raquo\").html(\"&nbsp;&nbsp;&nbsp;&raquo;&nbsp;&nbsp;&nbsp;$branchName\");";
                //print "jQuery(\"#branch-description\").html(\"$branchDescription\");";
            }
            print "jQuery(\"#chosen-branch\").val('$branchid');";
             
            $can_view_my_permissions = $GLOBALS['system_user']->hasPermission("users_view_own_permissions");
            $can_view_every_permission = $GLOBALS['system_user']->hasPermission("users_view_all_permissions");
            
            $can_view_permissions_tab = $can_view_my_permissions || $can_view_every_permission;
            
            if ($can_view_permissions_tab && $branchid != 0) {
                print "jQuery(\"#permissions-li, #upermissions-tab\").show(0);";
            } else {
                print "jQuery(\"#permissions-li, #upermissions-tab\").hide(0);";
            }
            print "jQuery(\"#users-tab\").html(\"";
            if ($branchid == 0) {
                if ($GLOBALS['system_user']->hasPermission("users_create_new_superadmin")) {
                    print "<button id='create-user' class='ibutton'>Create a new Super administrator</button>";
                    print "<span style='float: right; padding-right: 15px; font-style: italic; font-size: 11px; color: #444444;'>$total_items System super administrator$my_s</span>";
                    print "<br />";
                }
            } else {
                print "<table cellpadding='0' cellspacing='0' border='0' style='display: inline-block;'>";
                print "<tr>";
                if ($GLOBALS['system_user']->hasPermission("users_create_new_user")) {
                    print "<td>";
                        print "<button id='create-user' class='ibutton' style='width: 140px;'>Create a new user</button>";
                    print "</td>";
                }
                if ($GLOBALS['system_user']->hasPermission("users_move_user")) {
                    print "<td>";
                    print "<div style='display: inline; width: auto;margin-left: 5px;'>";
                        print "<div style='display: inline-block;'>";
                            print "<button id='move-user' disabled='disabled' style='display: inline-block; width: 115px;'>Move to&nbsp;<img src='images/downbutton.png' align='baseline'></button>";
                        print "</div>";
                        print "<div></div>";
                        print "<div id='branch-move-to' class='branch-more-actions-div branch-open' style='color: #333333; font-size: 11px; text-align: left;margin-top: -1px; display: none; background-color: #CDCDCD; border: 1px solid #999999; margin-left: 5px; width: 230px; max-height: 180px; overflow: auto; overflow-x: hidden !important; z-index: 200; position: absolute'>";
                            //$res = mysqli_query($GLOBALS["link"],"SELECT `id`, `branch` FROM `branches` WHERE `id` != \"".mysqli_real_escape_string($GLOBALS["link"],$branchid)."\" AND `deleted` = 0 ORDER BY `branch`");
                            //SELECT `branches`.`id`, `branches`.`branch`, `stores`.`id` AS `storeid`, `stores`.`name` AS `storename` FROM `branches` INNER JOIN `stores` ON `branches`.`id` = `stores`.`branch_id` WHERE `branches`.`deleted` = 0 ORDER BY `branches`.`branch`
                            $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
                            $sqlres = mysqli_query($GLOBALS["link"],$sql);
                            $row = mysqli_fetch_assoc($sqlres);
                            $channeltype = $row['type'];                    
                            $sql = "SELECT `branches`.`id`, `branches`.`branch`, `stores`.`id` AS `storeid`, `stores`.`store` AS `storename`";
                            $sql.= " FROM `branches`";
                            $sql.= " INNER JOIN `stores` ON `branches`.`id` = `stores`.`branch_id` INNER JOIN channels ON channels.id =`branches`.channel AND channels.type ='".$channeltype."'";
                            $sql.= " WHERE `branches`.`deleted` = 0";
                            //$sql.= " AND `branches`.`id`==37";
                            $sql.= " AND `stores`.`id` != '".mysqli_real_escape_string($GLOBALS["link"],$_SESSION["storeid"])."'";
                            $sql.= " ORDER BY `branches`.`branch`";
                        
                            $res = mysqli_query($GLOBALS["link"],$sql);
                            $first = TRUE;
                            $selected_move_to_branch = 0;
                            $tbranch = "";
                            while($arr = mysqli_fetch_assoc($res)){
                                if ($tbranch != $arr['branch']) {
                                    $tbranch = $arr['branch'];
                                    print "<div class='moveto-branch-divider'>".$this->trimBranchName($arr['branch'], 30)."</div>";
                                }
                                $trimStoreName=$this->trimBranchName($arr['storename'], 30);
                                $trimStoreName = str_ireplace("\n", "", trim($trimStoreName));
                                $trimStoreName = str_ireplace("\r", "", $trimStoreName);
                                $trimStoreName = str_ireplace("\r", "", $trimStoreName);
                                $trimStoreName=str_replace('"','\"',$trimStoreName);
                                $trimStoreName=str_replace(" \ ",' ',$trimStoreName);
                                $trimStoreName=str_replace("'",'&#039;',$trimStoreName);
                                $trimStoreName=preg_replace('/[\t\n\r\0\x0B]/', '', $trimStoreName);

                                print "<div class='branch-move".($first ? " selected-move-to-branch first-move-to-branch-div":"")."' style='padding-left: 10px; cursor: pointer; padding-top: 5px;' id='move-branch-div-".$arr['id']."' " . (strlen($arr['branch']) > 30 ? "title='".$arr['branch']."'" : "") ."><input type='hidden' class='selected-branch-move". ($first ? " first-move-to-branch-hidden" : "") . "' value='".$arr['storeid']."' storeid='".$arr['storeid']."' />".$trimStoreName."</div>";
                                if($first) {
                                    $selected_move_to_branch = $arr['id'];
                                    $first = FALSE;
                                }
                            }
                            print "<div style='cursor: pointer; padding-top: 5px; padding-bottom: 5px;'>";
                                print "<input type='hidden' id='selected-move-to-branch' value='$selected_move_to_branch' />";
                                print "&nbsp;<button class='ibutton' id='move-user-button'>Move to store</button>&nbsp;";
                                print "<button>Cancel</button>";
                            print "</div>";
                        print "</div>";
                    print "</div>";
                    print "</td>";
                }
                $can_add_user_to_group = $GLOBALS['system_user']->hasPermission("groups_add_remove_members");
                $can_make_superadmin   = $GLOBALS['system_user']->hasPermission("users_create_new_superadmin");
                $can_deactive_users    = $GLOBALS['system_user']->hasPermission("users_deactivate_users");
                $can_see_more_actions  = $can_add_user_to_group || $can_make_superadmin || $can_deactive_users;
                if ($can_see_more_actions) {
                    print "<td>";
                    print "<div style='display: inline; width: auto;margin-left: 5px;'>";
                        print "<div style='display: inline-block;'>";
                            print "<button id='user-more-actions' disabled='disabled' style='display: inline-block; width: 115px;'>More actions&nbsp;<img src='images/downbutton.png' align='baseline'></button>";
                        print "</div>";
                        print "<div></div>";
                        print "<div id='user-actions' class='branch-more-actions-div branch-open' style='color: #333333; font-size: 11px; text-align: left;margin-top: -1px; display: none; background-color: #CDCDCD; border: 1px solid #999999; margin-left: 5px; width: 180px; max-height: 180px; overflow: auto; overflow-x: hidden !important; z-index: 200; position: absolute;'>";
                            if ($can_add_user_to_group) {
                                print "<div class='user-more' style='padding-left: 10px; cursor: pointer; padding-top: 5px;' id='user-more-group'>Add to group</div>";
                            }
                            if ($can_make_superadmin) {
                                print "<div class='user-more' style='padding-left: 10px; cursor: pointer; padding-top: 5px;' id='user-more-admin'>Make Super administrator</div>";
                            }
                            if ($can_deactive_users) {
                                print "<div class='user-more' style='padding-left: 10px; cursor: pointer; padding-top: 5px;padding-bottom: 5px;' id='user-more-deactivate'>Deactivate users</div>";
                            }
                        print "</div>";
                    print "</div>";
                    print "</td>";
                }
                print "</table>";
                print "<span style='float: right; padding-right: 15px; font-style: italic; font-size: 11px; color: #444444;'>$total_items user$my_s in branch</span>";
            }
            //print "storeid: ".$_SESSION["storeid"];
            print "<table id='users-table' cellspacing='0' cellpadding='0' border='0' width='100%'>";
                print "<thead>";
                    print "<tr>";
                        print "<th>";
                                print $branchid == 0 ? "&nbsp;" : "<input type='checkbox' id='check-all-users'/>";
                        print "</th>";
                        print "<th>";
                            print "Name";
                        print "</th>";
                        print "<th>";
                            print "Username";
                        print "</th>";
                        print "<th>";
                            print "Email";
                        print "</th>";
                        print "<th>";
                            print "Area";
                        print "</th>";
                        print "<th>";
                            print "Store";
                        print "</th>";
                        print "<th>";
                            print "Added";
                        print "</th>";
                        print "<th>";
                            print "Active";
                        print "</th>";
                        print "<th>";
                            print "Last signed in";
                        print "</th>";
                    print "</tr>";
                print "</thead>";
                print "<tbody>";
                    print "<tr>";
                        print "<th colspan='9'>&nbsp;</th>";
                    print "</tr>";
                    
                    //$page = $page > $no_of_pages ? $no_of_pages : $page;
                    if($total_items == 0 && $branchid == 0) {
                        print "<tr><td colspan='9' style='text-align: center; font-style: italic;'>There are no system super administrators created</td></tr>";
                    }
                    else if($total_items == 0) {
                        print "<tr><td colspan='9' style='text-align: center; font-style: italic;'>This branch currently has no users</td></tr>";
                    }
                    $can_edit_users = $GLOBALS['system_user']->hasPermission("users_edit_user");
                    while ($arr = mysqli_fetch_assoc($result)) {
                        print "<tr class='user-tr' id='user-tr-".$arr['id']."'>";
                            print "<td>" . ( $branchid == 0 ? "&nbsp;" : "<input type='checkbox' value='".$arr['id']."' id='user-checkbox-".$arr['id']."' class='user-checkboxes' ".($arr['super_admin'] == 1?"disabled='disabled'":"")." />" ) . "</td>";
                            if ($can_edit_users || $GLOBALS['system_user']->id ==$arr['id']) {
                                print "<td><a class='useranchor' style='color:#6dad1f;' id='useranchor-".$arr['id']."'>" . $arr['first']." ".$arr['last'] . "</a>";
                            } else {
                                print "<td><a class='useranchor-no-edit' style='text-decoration: none; cursor: text;color:#6dad1f;'>" . $arr['first'] . " " . $arr['last'] . "</a>";
                            }
                            if ($branchid == 0 && $GLOBALS['system_user']->id ==$arr['id']) {
                                print "*";
                            } else if($GLOBALS['system_user']->id ==$arr['id']) {
                                print "**";
                            }  else if($branchid != 0 && $arr['super_admin'] == 1) {
                                print "*";
                            } else;
                            
                            print "</td>";
                            print "<td>" . $arr['username'] . "</td>";
                            print "<td>" . $arr['email'] . "</td>";
                            $str_size = 22;
                            print "<td ".( strlen($arr['branch']) > $str_size ? "title='".$arr['branch']."'" : "" ).">" . ucwords( strlen($arr['branch']) > $str_size ? $this->trimBranchName($arr['branch'], $str_size) : $arr['branch'] ) . "</td>";
                            print "<td>" . $arr['store_name'] . "</td>";
                            print "<td>" . $this->formatPastTime($arr['date_added']) . "</td>";
                            print "<td>" . $arr['active'] . "</td>";
                            print "<td>" . $this->formatPastTime($arr['lastlogin']) . "</td>";
                        print "</tr>";
                    }
                    
                print "</tbody>";
                print "<tfoot>";
                    print "<tr>";
                        print "<th colspan='9'>&nbsp;</th>";
                    print "</tr>";
                    print "<tr>";
                        print "<th colspan='9' align='center' style='border-top: 1px solid #DDDDDD'>";
                            print $this->printPaging($page, $no_of_pages, $branchid);
                        print "</th>";
                    print "</tr>";
                print "</tfoot>";
            print "</table>";
            print "\");\n";
            print "jQuery(\"#page-number\").val(\"$page\");\n";
            if (isset($can_see_more_actions) && $can_see_more_actions) { print "jQuery(\"#branch-more-actions\").show(0);\n"; }
            
            $can_modify_permissions    = $GLOBALS['system_user']->hasPermission("users_modify_permissions");
            $can_view_own_permissions  = $GLOBALS['system_user']->hasPermission("users_view_own_permissions");
            $can_view_all_permissions  = $GLOBALS['system_user']->hasPermission("users_view_all_permissions");
            $can_view_some_permissions = $can_view_own_permissions || $can_view_all_permissions;
            
            if($branchid > 0) {
                print "jQuery(\"#upermissions-tab\").html(\"";
                if($can_view_some_permissions) {
                    $query = "SELECT `system_users`.`id`, `system_users`.`first`, `system_users`.`last`, `branches`.`branch`";
                    $query .= " FROM `system_users`, `branches`";
                    $query .= " WHERE `system_users`.`branch_id` = `branches`.`id`";
                    $query .= " AND `system_users`.`active` = 1";
                    $query .= " AND `system_users`.`super_admin` = 0";
                    $query .= !$can_view_all_permissions ? " AND `system_users`.`id` = " . mysqli_real_escape_string($GLOBALS["link"],$GLOBALS['system_user']->id) : "";
                    $query .= " AND `branches`.`id` = " . mysqli_real_escape_string($GLOBALS["link"],$branchid);
                    $query .= " ORDER BY `first`, `last`";
                    $result = mysqli_query($GLOBALS["link"],$query);
                    print "<span style='font-style: italic;'>Edit permissions of</span>&nbsp;&nbsp;";
                    print "<select id='branch-users-permissions' style='font-style: italic;'>";
                        print "<option value='0' selected='selected'> [Please select user]</option>";
                        while ($arr = mysqli_fetch_assoc($result)) {
                            print "<option value='".$arr['id']."'>". ucwords($arr['first']." ".$arr['last']." [".$arr['branch']."]").($GLOBALS['system_user']->id == $arr['id'] ? " *" : "")."</option>";
                        }
                    print "</select>";
                    print "<br/><br/>";
                }
                print "<br/>";
                
                print "<div style='height: 400px; overflow: auto; border-top: 1px solid #999999; border-bottom: 1px solid #999999;'>";
                
                $temp_permissions = $GLOBALS['registered_permissions'];
                $other_permissions = isset($temp_permissions['']) ? $temp_permissions[''] : NULL;
                if (isset($temp_permissions[''])) {
                    unset($temp_permissions['']);
                }
                ksort($temp_permissions);
                if (!empty($other_permissions)) { $temp_permissions['Other Permissions'] = $other_permissions; }
                foreach ($temp_permissions as $key=>$value) {
                    print "<table class='user-tables' cellspacing='0' cellpadding='0' border='0' width='90%'>";
                        print "<thead>";
                            print "<tr>";
                                print "<th width='3%'>";
                                        print "<input type='checkbox' disabled='disabled' class='group-permission-main-checkbox' />";
                                print "</th>";
                                print "<th width='3%'>&nbsp;</th>";
                                print "<th colspan='2'>";
                                    print ucwords($key);
                                print "</th>";
                            print "</tr>";
                        print "</thead>";
                        print "<tbody>";
                            foreach ($value as $v) {
                                //$checked = in_array($v['acl_name'], $groupPermissions) ? " checked='checked'" : "";
                                print "<tr>";
                                    print "<td>&nbsp;</td>";
                                    print "<td colspan='1'><input type='checkbox' disabled='disabled' class='group-permission-sub-checkbox' value='".$v['acl_name']."' /></td>";
                                    print "<td width='70%'>&nbsp;" . $v['description'] . "</td>";
                                    print "<td id='td-".$v['acl_name']."' class='group-permissions-td' style='text-align: right;color: #666666; font-style: italic;'></td>";
                                print "</tr>";
                            }
                        print "</tbody>";
                    print "</table>";
                }
                
                print "</div>";
                print $can_modify_permissions ? "<br/>&nbsp;&nbsp;<button class='ibutton' disabled='disabled' id='save-user-permissions-button'>Save user permissions</button><br/><br/>" : "<br/>";
                print "\");\n";
            }
        }
        
        function printCreateUserDialog () {
            print "<div id='create-user-dialog' class='dialog'>";
            print "<table cellpadding='0' cellspacing='0' border='0' width='100%'>";
                print "<thead style='background-color: #EEEEEE; border-bottom: 1px solid #CCCCCC;'>";
                    print "<tr>";
                        print "<th id='create-user-title' style='text-align: left; font-family: trebuchet ms !important; font-weight: 900; font-size: 14px; padding-left: 20px; padding-top: 15px; padding-bottom: 10px;'>Create a new user</th>";
                        print "<th style='text-align: right; padding-right: 10px;'><img class='close-dialog' src='images/closepopup.png' style='cursor: pointer;' /></th>";
                    print "</tr>";
                print "</thead>";
                print "<tbody>";
                    print "<tr>";
                        print "<th style='text-align: left; font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 11px; font-weight: 900; padding-top: 5px; padding-left: 10px;'>First name<br /><input type='text' id='new-firstname' maxlength='24' class='disable-create-user' style='width: 150px;' /></th>";
                        print "<th style='text-align: left; font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 11px; font-weight: 900; padding-top: 5px;'>Last name<br /><input type='text' id='new-lastname' maxlength='24' class='disable-create-user' style='width: 150px;' /></th>";
                    print "</tr>";
                    print "<tr>";
                        print "<th colspan='2' style='text-align: left; font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 11px; font-weight: 900; padding-left: 10px; padding-top: 5px;'>Email address<br /><input type='text' id='new-email' maxlength='254' class='disable-create-user' style='width: 250px;' /></th>";
                    print "</tr>";
                    print "<tr>";
                        print "<th style='text-align: left;font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 11px; font-weight: 900; padding-left: 10px; padding-top: 5px;'>Username<br /><input type='text' id='new-username' maxlength='24' class='disable-create-user' style='width: 150px;' /></th>";
                        print "<th style='text-align: left; font-size: 11px; font-weight: 400; font-style: italic;'><br/>*Must be unique</th>";
                    print "</tr>";
                    print "<tr id='show-branch-tr' style='display: none;'>";
                        print "<th colspan='2' style='text-align: left; font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 11px; font-weight: 900; padding-left: 10px; padding-top: 5px;'>Branch<br />";
                            print "<select id='new-branch' class='disable-create-user'>";
                            //print "<option value='0'>--None--</option>";
                            $query = "SELECT `id`, `branch` FROM `branches` WHERE `deleted` = 0 ORDER by `branch`";
                            $result = mysqli_query($GLOBALS["link"],$query);
                            while ($arr = mysqli_fetch_assoc($result)) {
                                print "<option value='" . $arr['id'] . "'>" . $this->trimBranchName($arr['branch'], 30) . "</option>";
                            }
                            print "</select>";
                        print "</th>";
                    print "</tr>";
                    print "<tr id='show-create-user-password' style='display: none;'>";
                        print "<th colspan='2' style='text-align: left; padding-left: 15px;'>";
                            print "<br/><a id='show-create-user-anchor' style='cursor: pointer; color: #2200CC; text-decoration: underline;'>Change Password</a>";
                            print "<input type='hidden' id='edit-user-password' value='0' />";
                            print "<input type='hidden' id='edit-user-id' value='0' />";
                        print "</th>";
                    print "</tr>";
                    print "<tr id='create-user-password-tr'>";
                        print "<th style='text-align: left; font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 11px; font-weight: 900; padding-top: 5px; padding-left: 10px;'><span id='create-user-password' style='font-family: Verdana,Arial,Helvetica,sans-serif !important; font-size: 11px; font-weight: 900;'>Password</span><br /><input type='password' id='new-password1' maxlength='254' style='width: 150px;' /></th>";
                        print "<th style='text-align: left; font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 11px; font-weight: 900; padding-top: 5px;'>Re-enter Password<br /><input type='password' id='new-password2' maxlength='254' style='width: 150px;' /></th>";
                    print "</tr>";
                    print "<tr>";
                        print "<th style='text-align: left; font-size: 12px; font-weight: 900; padding-top: 10px; padding-bottom: 10px; padding-left: 20px;'><button id='save-user' style='font-size: 11px; font-weight: 900; cursor: pointer;'>Create user</button>&nbsp;<button class='close-dialog' style='font-size: 11px; cursor: pointer;'>Cancel</button></th>";
                        print "<th valign='top' style='text-align: right; font-size: 11px; font-weight: 400; padding-top: 10px; padding-bottom: 10px; padding-right: 15px;'><label for='new-user-active'><input style='vertical-align: middle;' type='checkbox' checked='checked' id='new-user-active' value='1' /><span id='activate-user-span' style='vertical-align: middle;'>&nbsp;Activate now</span></label></th>";
                    print "</tr>";
                print "</tbody>";
                print "<tfoot style='background-color: #EEEEEE; border-top: 1px solid #CCCCCC;'>";
                    print "<tr>";
                        print "<th colspan='2' id='create-user-error' style='font-size: 12px; color: red; padding-top: 5px; padding-bottom: 5px;'>";
                        print "</th>";
                    print "</tr>";
                print "</tfoot>";
            print "</table>";
            print "</div>";
        }
        
        function printAddBranchDialog () {
            print "<div id='create-branch-dialog' class='dialog'>";
            print "<table cellpadding='0' cellspacing='0' border='0' width='100%'>";
                print "<thead style='background-color: #EEEEEE; border-bottom: 1px solid #CCCCCC;'>";
                    print "<tr>";
                        print "<th id='create-branch-title' style='font-family: trebuchet ms !important; text-align: left; font-weight: 900; font-size: 14px; padding-left: 20px; padding-top: 15px; padding-bottom: 10px;'>Add a Area</th>";
                        print "<th style='text-align: right; padding-right: 10px;'><img class='close-dialog' src='images/closepopup.png' style='cursor: pointer;' /></th>";
                    print "</tr>";
                print "</thead>";
                print "<tbody>";
                    print "<tr>";
                        print "<th colspan='2' style='text-align: left; font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 11px; font-weight: 900; padding-top: 5px; padding-left: 10px;'>Area name<br /><input type='text' id='new-branchname' maxlength='49' style='width: 250px;' /></th>";        
                    print "</tr>";
                    print "<tr>";
                        print "<th colspan='2' style='text-align: left; font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 11px; font-weight: 900; padding-top: 5px; padding-left: 10px;'>Area address<br /><textarea id='new-branchaddress' style='width: 250px; height: 50px; resize: none; overflow: auto;' maxlength='254'></textarea></th>";
                    print "</tr>";
                    print "<tr>";
                        print "<th colspan='2' style='text-align: left; font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 11px; font-weight: 900; padding-top: 5px; padding-left: 10px;'>Area Code<br /><input type='text' id='new-branchcode' maxlength='254' style='width: 250px;' /></th>";
                    print "</tr>";
                      print "<tr>";
                        print "<th colspan='2' style='text-align: left; font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 11px; font-weight: 900; padding-top: 5px; padding-left: 10px;'>Approval Setting<br /><select id='new-approvalsetting'><option value='No'>No</option><option value='Yes'>Yes</option></select></th>";
                    print "</tr>";
                    
                    $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
                    $sqlres = mysqli_query($GLOBALS["link"],$sql);
                    $row = mysqli_fetch_assoc($sqlres);
                    $channeltype = $row['type'];
                    //if ($channeltype == "Commercial" || $channeltype =="Franchises"){
                        //print "<input type='hidden' id='new-branchchannel' value='0'/>";
                    //} else {
                        print "<tr>";
                            if($channeltype != "Franchises"){
                               print "<th colspan='2' style='text-align: left; font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 11px; font-weight: 900; padding-top: 5px; padding-left: 10px;'>Channel<br />
                                    <Select id='new-branchchannel'>";
                            }else{
                                print "<th colspan='2' style='text-align: left; font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 11px; font-weight: 900; padding-top: 5px; padding-left: 10px;'>Franchisee<br />
                                    <Select id='new-branchchannel'>";

                            }
                            
                            $query = "Select * from channels where type='".$channeltype."' order by name";
                            $res = mysqli_query($GLOBALS["link"],$query);
                            while ($arr=mysqli_fetch_assoc($res)){
                                print "<option value='".$arr["id"]."'>".$arr["name"]."</option>";
                            }   
                            print "</th>";
                         print "</tr>";     
                    //}
                    print "<tr>";
                        print "<th colspan='2' style='text-align: left; font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 11px; font-weight: 900; padding-top: 10px; padding-bottom: 10px; padding-left: 20px;'><button id='save-branch' style='font-size: 11px; font-weight: 900; cursor: pointer;'>Add area</button>&nbsp;<button class='close-dialog' style='font-size: 11px; cursor: pointer;'>Cancel</button></th>";
                    print "</tr>";
                print "</tbody>";
                print "<tfoot style='background-color: #EEEEEE; border-top: 1px solid #CCCCCC;'>";
                    print "<tr>";
                        print "<th colspan='2' id='create-branch-error' style='font-size: 12px; color: red; padding-top: 5px; padding-bottom: 5px;'>";
                        print "</th>";
                    print "</tr>";
                print "</tfoot>";
            print "</table>";
            print "</div>";
        }
        
        function printAddChannelDialog () {
            print "<div id='create-channel-dialog' class='dialog'>";
            print "<table cellpadding='0' cellspacing='0' border='0' width='100%'>";
                print "<thead style='background-color: #EEEEEE; border-bottom: 1px solid #CCCCCC;'>";
                    print "<tr>";
                        $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
                        $sqlres = mysqli_query($GLOBALS["link"],$sql);
                        $row = mysqli_fetch_assoc($sqlres);
                        $channeltype = $row['type'];
                        $title = "<th id='create-channel-title2' style='font-family: trebuchet ms !important; text-align: left; font-weight: 900; font-size: 14px; padding-left: 20px; padding-top: 15px; padding-bottom: 10px;'>Add a channel</th>"; 
                        if($channeltype =="Franchises")$title = "<th id='create-channel-title2' style='font-family: trebuchet ms !important; text-align: left; font-weight: 900; font-size: 14px; padding-left: 20px; padding-top: 15px; padding-bottom: 10px;'>Add a franchisee</th>"; 
                        if($channeltype =="Commercial")$title = "<th id='create-channel-title2' style='font-family: trebuchet ms !important; text-align: left; font-weight: 900; font-size: 14px; padding-left: 20px; padding-top: 15px; padding-bottom: 10px;'>Add a commercial</th>"; 
                        if($channeltype =="Retail") $title = "<th id='create-channel-title2' style='font-family: trebuchet ms !important; text-align: left; font-weight: 900; font-size: 14px; padding-left: 20px; padding-top: 15px; padding-bottom: 10px;'>Add a retail</th>"; 
                        print $title;
                       print "<th style='text-align: right; padding-right: 10px;'><img class='close-dialog' src='images/closepopup.png' style='cursor: pointer;' /></th>";
                    print "</tr>";
                print "</thead>";
                print "<tbody>";
                    print "<tr>";
                        print "<th colspan='2' style='text-align: left; font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 11px; font-weight: 900; padding-top: 5px; padding-left: 10px;display:none;'>Type of Channel<br />";
                        if($channeltype =="Retail"){
                            print "<input type='radio' id='new-channeltype' name='new-channeltype' style='width: 20px;' value='Retail'  readonly checked/>Retail<br />";
                        }
                       
                        if($channeltype =="Franchises"){
                            print "<input type='radio' id='new-channeltype' name='new-channeltype' style='width: 20px;' value='Franchises' readonly checked/>Franchise<br/>";
                        }
                        if($channeltype =="Commercial"){
                            print "<input type='radio' id='new-channeltype' name='new-channeltype' style='width: 20px;' value='Commercial' readonly checked/>Commercial</th>";
                        }
                    print "</tr>";

                    print "<tr>";
                        print "<th colspan='2' style='text-align: left; font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 11px; font-weight: 900; padding-top: 5px; padding-left: 10px;'>Name<br /><input type='text' id='new-channelname' maxlength='49' style='width: 250px;' /></th>";
                    print "</tr>";

                    $button = "<th colspan='2' style='text-align: left; font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 11px; font-weight: 900; padding-top: 10px; padding-bottom: 10px; padding-left: 20px;'><button id='save-channel' style='font-size: 11px; font-weight: 900; cursor: pointer;'>Add Channel</button>&nbsp;<button class='close-dialog' style='font-size: 11px; cursor: pointer;'>Cancel</button></th>";
                    if($channeltype =="Franchises")$button = "<th colspan='2' style='text-align: left; font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 11px; font-weight: 900; padding-top: 10px; padding-bottom: 10px; padding-left: 20px;'><button id='save-channel' style='font-size: 11px; font-weight: 900; cursor: pointer;'>Add Franchisee</button>&nbsp;<button class='close-dialog' style='font-size: 11px; cursor: pointer;'>Cancel</button></th>";
                    print "<tr>";
                        print $button;
                    print "</tr>";
                  

                print "</tbody>";
                print "<tfoot style='background-color: #EEEEEE; border-top: 1px solid #CCCCCC;'>";
                    print "<tr>";
                        print "<th colspan='2' id='create-channel-error' style='font-size: 12px; color: red; padding-top: 5px; padding-bottom: 5px;'>";
                        print "</th>";
                    print "</tr>";
                print "</tfoot>";
            print "</table>";
            print "</div>";
        }

        function printAddStoreDialog () {
            print "<div id='create-store-dialog' class='dialog'>";
            print "<table cellpadding='0' cellspacing='0' border='0' width='100%'>";
                print "<thead style='background-color: #EEEEEE; border-bottom: 1px solid #CCCCCC;'>";
                    print "<tr>";
                        $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
                        $sqlres = mysqli_query($GLOBALS["link"],$sql);
                        $row = mysqli_fetch_assoc($sqlres);
                        $channeltype = $row['type'];
                        if($channeltype =="Franchises"){
                            print "<th id='create-store-title' style='font-family: trebuchet ms !important; text-align: left; font-weight: 900; font-size: 14px; padding-left: 20px; padding-top: 15px; padding-bottom: 10px;'>Add a outlet</th>";
                        }else{
                          print "<th id='create-store-title' style='font-family: trebuchet ms !important; text-align: left; font-weight: 900; font-size: 14px; padding-left: 20px; padding-top: 15px; padding-bottom: 10px;'>Add a store</th>"; 
                        }
                        print "<th style='text-align: right; padding-right: 10px;'><img class='close-dialog' src='images/closepopup.png' style='cursor: pointer;' /></th>";
                    print "</tr>";
                print "</thead>";
                print "<tbody>";
                    if($channeltype =="Franchises"){
                        print "<tr>";
                        print "<th colspan='2' style='text-align: left; font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 11px; font-weight: 900; padding-top: 5px; padding-left: 10px;'>Outlet name<br /><input type='text' id='new-storename' maxlength='49' style='width: 250px;' /></th>";
                        print "</tr>";
                        print "<tr>";
                            print "<th colspan='2' style='text-align: left; font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 11px; font-weight: 900; padding-top: 5px; padding-left: 10px;'>GPS Coordinates<br /><input type='text' id='new-gpscoords' maxlength='49' style='width: 250px;' /></th>";
                        print "</tr>";
                        print "<tr>";
                            print "<th colspan='2' style='text-align: left; font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 11px; font-weight: 900; padding-top: 5px; padding-left: 10px;'>Outlet address<br /><textarea id='new-storeaddress' style='width: 250px; height: 50px; resize: none; overflow: auto;' maxlength='254'></textarea></th>";
                        print "</tr>";
                        print "<tr>";
                            print "<th colspan='2' style='text-align: left; font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 11px; font-weight: 900; padding-top: 5px; padding-left: 10px;'>Outlet Code<br /><input type='text' id='new-storecode' maxlength='49' style='width: 250px;' /></th>";
                        print "</tr>";
                        print "<tr>";
                            print "<th colspan='2' style='text-align: left; font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 11px; font-weight: 900; padding-top: 5px; padding-left: 10px;'>Contact Number<br /><input type='text' id='new-contactnumber' maxlength='49' style='width: 250px;'/></th>";
                        print "</tr>";
                         print "<tr>";
                            print "<th colspan='2' style='text-align: left; font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 11px; font-weight: 900; padding-top: 5px; padding-left: 10px;'>Manager<br /><input type='text' id='new-manager' maxlength='49' style='width: 250px;'/></th>";
                        print "</tr>";
                        print "<tr>";
                            print "<th colspan='2' style='text-align: left; font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 11px; font-weight: 900; padding-top: 5px; padding-left: 10px;'>Region<br />
                            <Select id='new-regionid'>";
                                $query = "SELECT `title`, `prefix` FROM regions ORDER by prefix";
                                $res = mysqli_query($GLOBALS["link"],$query);
                                while ($arr=mysqli_fetch_assoc($res)){
                                    print "<option value='".$arr["prefix"]."'>".$arr["title"]."</option>";
                                }
                            print "</th>";
                        print "</tr>";
                        print "<tr>";
                        $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
                        $sqlres = mysqli_query($GLOBALS["link"],$sql);
                        $row = mysqli_fetch_assoc($sqlres);
                        $channeltype = $row['type'];
                       // if ($channeltype == "Commercial" || $channeltype =="Franchises"){
                         //   print "<input type='hidden' id='new-branchid' name='new-branchid' value='1'/>";
                       // } else {
                        print "<tr>";
                        print "<th colspan='2' style='text-align: left; font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 11px; font-weight: 900; padding-top: 5px; padding-left: 10px;'>Area<br />
                        <Select id='new-branchid'>";
                            $query = "select branches.id, concat(channels.name,' - ',branches.branch) as tag from branches, channels where branches.channel=channels.id and channels.type ='".$channeltype."' order by tag";
                            $res = mysqli_query($GLOBALS["link"],$query);
                            while ($arr=mysqli_fetch_assoc($res)){
                                print "<option value='".$arr["id"]."'>".$arr["tag"]."</option>";
                            }
                        print "</th>";
                        print "</tr>";
                       // }
                        print "<tr>";
                            print "<th colspan='2' style='text-align: left; font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 11px; font-weight: 900; padding-top: 10px; padding-bottom: 10px; padding-left: 20px;'><button id='save-store' style='font-size: 11px; font-weight: 900; cursor: pointer;'>Add store</button>&nbsp;<button class='close-dialog' style='font-size: 11px; cursor: pointer;'>Cancel</button></th>";
                        print "</tr>";
                    }else{
                        print "<tr>";
                        print "<th colspan='2' style='text-align: left; font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 11px; font-weight: 900; padding-top: 5px; padding-left: 10px;'>Store name<br /><input type='text' id='new-storename' maxlength='49' style='width: 250px;' /></th>";
                        print "</tr>";
                        print "<tr>";
                            print "<th colspan='2' style='text-align: left; font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 11px; font-weight: 900; padding-top: 5px; padding-left: 10px;'>GPS Coordinates<br /><input type='text' id='new-gpscoords' maxlength='49' style='width: 250px;' /></th>";
                        print "</tr>";
                        print "<tr>";
                            print "<th colspan='2' style='text-align: left; font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 11px; font-weight: 900; padding-top: 5px; padding-left: 10px;'>Store address<br /><textarea id='new-storeaddress' style='width: 250px; height: 50px; resize: none; overflow: auto;' maxlength='254'></textarea></th>";
                        print "</tr>";
                        print "<tr>";
                            print "<th colspan='2' style='text-align: left; font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 11px; font-weight: 900; padding-top: 5px; padding-left: 10px;'>Store Code<br /><input type='text' id='new-storecode' maxlength='49' style='width: 250px;' /></th>";
                        print "</tr>";
                        print "<tr>";
                            print "<th colspan='2' style='text-align: left; font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 11px; font-weight: 900; padding-top: 5px; padding-left: 10px;'>Contact Number<br /><input type='text' id='new-contactnumber' maxlength='49' style='width: 250px;'/></th>";
                        print "</tr>";
                         print "<tr>";
                            print "<th colspan='2' style='text-align: left; font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 11px; font-weight: 900; padding-top: 5px; padding-left: 10px;'>Manager<br /><input type='text' id='new-manager' maxlength='49' style='width: 250px;'/></th>";
                        print "</tr>";
                        print "<tr>";
                            print "<th colspan='2' style='text-align: left; font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 11px; font-weight: 900; padding-top: 5px; padding-left: 10px;'>Region<br />
                            <Select id='new-regionid'>";
                                $query = "SELECT `title`, `prefix` FROM regions ORDER by prefix";
                                $res = mysqli_query($GLOBALS["link"],$query);
                                while ($arr=mysqli_fetch_assoc($res)){
                                    print "<option value='".$arr["prefix"]."'>".$arr["title"]."</option>";
                                }
                            print "</th>";
                        print "</tr>";
                        print "<tr>";
                        $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
                        $sqlres = mysqli_query($GLOBALS["link"],$sql);
                        $row = mysqli_fetch_assoc($sqlres);
                        $channeltype = $row['type'];
                       // if ($channeltype == "Commercial" || $channeltype =="Franchises"){
                         //   print "<input type='hidden' id='new-branchid' name='new-branchid' value='1'/>";
                       // } else {
                        print "<tr>";
                        print "<th colspan='2' style='text-align: left; font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 11px; font-weight: 900; padding-top: 5px; padding-left: 10px;'>Area<br />
                        <Select id='new-branchid'>";
                            $query = "select branches.id, concat(channels.name,' - ',branches.branch) as tag from branches, channels where branches.channel=channels.id and channels.type ='".$channeltype."' order by tag";
                            $res = mysqli_query($GLOBALS["link"],$query);
                            while ($arr=mysqli_fetch_assoc($res)){
                                print "<option value='".$arr["id"]."'>".$arr["tag"]."</option>";
                            }
                        print "</th>";
                        print "</tr>";
                       // }

                        $button = "<th colspan='2' style='text-align: left; font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 11px; font-weight: 900; padding-top: 10px; padding-bottom: 10px; padding-left: 20px;'><button id='save-store' style='font-size: 11px; font-weight: 900; cursor: pointer;'>Add store</button>&nbsp;<button class='close-dialog' style='font-size: 11px; cursor: pointer;'>Cancel</button></th>";
                        if($channeltype =="Franchises")$button = "<th colspan='2' style='text-align: left; font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 11px; font-weight: 900; padding-top: 10px; padding-bottom: 10px; padding-left: 20px;'><button id='save-store' style='font-size: 11px; font-weight: 900; cursor: pointer;'>Add outlet</button>&nbsp;<button class='close-dialog' style='font-size: 11px; cursor: pointer;'>Cancel</button></th>";
                        print "<tr>";
                            print $button;
                        print "</tr>";
                    }
                print "</tbody>";
                print "<tfoot style='background-color: #EEEEEE; border-top: 1px solid #CCCCCC;'>";
                    print "<tr>";
                        print "<th colspan='2' id='create-store-error' style='font-size: 12px; color: red; padding-top: 5px; padding-bottom: 5px;'>";
                        print "</th>";
                    print "</tr>";
                print "</tfoot>";
            print "</table>";
            print "</div>";
        }
        
        function addUserToGroup() {
            print "<div id='user-group-dialog' class='dialog'>";
            print "<table cellpadding='0' cellspacing='0' border='0' width='100%'>";
                print "<thead style='background-color: #EEEEEE; border-bottom: 1px solid #CCCCCC;'>";
                    print "<tr>";
                        print "<th id='user-group-title' style='text-align: left; font-weight: 900; font-size: 14px; font-family: trebuchet ms !important; padding-left: 20px; padding-top: 15px; padding-bottom: 10px;'>Add to group</th>";
                        print "<th style='text-align: right; padding-right: 10px;'><img class='close-dialog' src='images/closepopup.png' style='cursor: pointer;' /></th>";
                    print "</tr>";
                print "</thead>";
                print "<tbody>";
                    print "<tr>";
                        print "<th colspan='2' style='text-align: left; font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 11px;  font-weight: 900; padding-top: 5px; padding-left: 10px;'>User groups<br />";
                            print "<select id='add-to-group-select' multiple='multiple' size='10' style='width: 300px;'>";
                            $query  = "SELECT `id`, `name`";
                            $query .= " FROM `groups`";
                            $query .= " WHERE `deleted` =  0";
                            $query .=   $GLOBALS['system_user']->isSuperAdmin ? "" : " AND `id` IN (SELECT `group_id` FROM `user_groups` WHERE `user_id` = " . mysqli_real_escape_string($GLOBALS["link"],$GLOBALS['system_user']->id) . ")";
                            $query .= " ORDER by `name`";
                            $result = mysqli_query($GLOBALS["link"],$query);
                            //$toSelect = TRUE;
                            while ($arr = mysqli_fetch_assoc($result)) {
                                print "<option title='" . $arr['name'] . "' value='" . $arr['id'] . "'>" . $arr['name'] . "</option>";
                            }
                            print "</select>";
                        print "</th>";
                    print "</tr>";
                    print "<tr>";
                        print "<th colspan='2' style='text-align: left; font-size: 12px; font-weight: 900; padding-top: 10px; padding-bottom: 10px; padding-left: 20px;'><button id='add-user-to-group' style='font-size: 11px; font-weight: 900; cursor: pointer;'>Add to group</button>&nbsp;<button class='close-dialog' style='font-size: 11px; cursor: pointer;'>Cancel</button></th>";
                    print "</tr>";
                print "</tbody>";
                print "<tfoot style='background-color: #EEEEEE; border-top: 1px solid #CCCCCC;'>";
                    print "<tr>";
                        print "<th colspan='2' id='create-usergroup-error' style='font-size: 12px; color: red; padding-top: 5px; padding-bottom: 5px;'>";
                        print "</th>";
                    print "</tr>";
                print "</tfoot>";
            print "</table>";
            print "</div>";
        }
        
        function saveInGroup ($array) {
            
            if (!$GLOBALS['system_user']->hasPermission("groups_add_remove_members")) {
                print "jQuery(\"#error-message\").html(\"Cannot complete action! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
                return;
            }
            
            $users = $array['users'];
            $groups = $array['groups'];
            $users = preg_split("/[\s\|]+/", $users, NULL, PREG_SPLIT_NO_EMPTY);
            $groups = preg_split("/[\s\|]+/", $groups, NULL, PREG_SPLIT_NO_EMPTY);
            $nousers = count($users);
            $nogroups = count($groups);
            $user_s = $nousers > 1 ? "s" : "";
            $group_s = $nogroups > 1 ? "s" : "";
            
            $query1 = "DELETE FROM `user_groups` WHERE";
            $query2 = "INSERT INTO `user_groups` (`user_id`, `group_id`) VALUES";
            
            foreach ($users as $u) {
                foreach ($groups as $g) {
                    $query1 .= " (`user_id` = " . mysqli_real_escape_string($GLOBALS["link"],$u) . " AND `group_id` = " . mysqli_real_escape_string($GLOBALS["link"],$g) . ") OR";
                    $query2 .= " (\"" . mysqli_real_escape_string($GLOBALS["link"],$u) . "\", \"" . mysqli_real_escape_string($GLOBALS["link"],$g) . "\"),";
                }
            }
            
            $query1 = substr($query1, 0, -3);
            $query2 = substr($query2, 0, -1);
            
            
            $query1success = mysqli_query($GLOBALS["link"],$query1);
            $query2success = mysqli_query($GLOBALS["link"],$query2);
            if($query1success && $query2success) {
                print "jQuery(\"#ok-message\").html(\"$nousers user$user_s successfully added to $nogroups group$group_s\").show(0).delay(4000).hide(0);\n";
                logAction("Added $nousers user$user_s to $nogroups group$group_s");
            } else {
                print "jQuery(\"#error-message\").html(\"Unable to add $nousers user$user_s to $nogroups group$group_s! Please try again...\").show(0).delay(4000).hide(0);\n";
            }
            print "jQuery.unblockUI({ fadeOut: 0 });\n";
            print "jQuery(\".user-checkboxes\").removeAttr(\"checked\");\n";
        }
        
        function createNewUser ($array) {
            $firstname      = urldecode($array['firstname']);
            $lastname       = urldecode($array['lastname']);
            $username       = urldecode($array['username']);
            $email          = urldecode($array['email']);
            $password       = urldecode($array['password']);
            $branchid       = intval($array['branchid']);
            $newbranchid    = intval($array['newbranchid']);
            $active         = intval($array['active']);
            $edituser       = intval($array['edit']);
            $userid         = intval($array['userid']);
            $changepassword = intval($array['cp']);
            
            if (!$GLOBALS['system_user']->hasPermission("users_create_new_superadmin") && $branchid == 0) {
                print "jQuery(\"#error-message\").html(\"Cannot complete action! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
                return;
            }
            
            if (!$GLOBALS['system_user']->hasPermission("users_create_new_user") && $branchid != 0) {
                print "jQuery(\"#error-message\").html(\"Cannot complete action! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
                return;
            }

            
            $query  = "SELECT `email`";
            $query .= " FROM `system_users`";
            $query .= " WHERE `deleted` = '0'";
            $result2 = mysqli_query($GLOBALS["link"],$query);
            
            $query = "SELECT `id` FROM `system_users` WHERE `username` = \"" . mysqli_real_escape_string($GLOBALS["link"],$username)."\"";
            $query .= $edituser == 1 ? " AND `id` != \"".mysqli_real_escape_string($GLOBALS["link"],$userid)."\"" : "";
            $result = mysqli_query($GLOBALS["link"],$query);
            if (mysqli_num_rows($result) > 0) {
                print "jQuery(\"#create-user-error\").html(\"The username has already been taken\");\n";
                print "blockDialog(\"#create-user-dialog\");\n";
            }elseif (mysqli_num_rows($result2) > 0) {
                print "jQuery(\"#create-user-error\").html(\"The email has already been taken\");\n";
                print "blockDialog(\"#create-user-dialog\");\n";
            }  else {
                $md5_password = sha1($password);
                
                if($edituser == 1) {
                    $query  = "UPDATE `system_users` SET";
                    $query .= " `first` = \"" . mysqli_real_escape_string($GLOBALS["link"],$firstname) . "\",";
                    $query .= " `last` = \"" . mysqli_real_escape_string($GLOBALS["link"],$lastname) . "\",";
                    $query .= " `email` = \"" . mysqli_real_escape_string($GLOBALS["link"],$email) . "\",";
                    $query .= " `username` = \"" . mysqli_real_escape_string($GLOBALS["link"],$username) . "\",";
                    $query .= " `store_id` = \"" . mysqli_real_escape_string($GLOBALS["link"],$_SESSION["storeid"]) . "\",";
                    $query .= $GLOBALS['system_user']->id == $userid && $GLOBALS['system_user']->isSuperAdmin ? " `branch_id` = \"" . mysqli_real_escape_string($GLOBALS["link"],$newbranchid) . "\"," : "";
                    $query .= $changepassword == 1 ? " `password` = \"".mysqli_real_escape_string($GLOBALS["link"],sha1($password))."\"," : "";
                    $query .= " `date_updated` = NOW(),";
                    $query .= " `active` = \"" . mysqli_real_escape_string($GLOBALS["link"],$active) . "\"";
                    $query .= " WHERE `id` = " . mysqli_real_escape_string($GLOBALS["link"],$userid);
                    
                    if(mysqli_query($GLOBALS["link"],$query)) {
                        logAction($GLOBALS['system_user']->id == $userid? "Updated own user details users.id[$userid]" : "Updated system user users.id[$userid]");
                        print "jQuery(\"#ok-message\").html(\"Changes to user successfully saved...\").show(0).delay(4000).hide(0);\n";
                        if($GLOBALS['system_user']->id == $userid && $GLOBALS['system_user']->username != $username) {
                            print "
                                jQuery.blockUI({
                                    message: \"<img src='images/acloader.gif' align='absmiddle' style='width: 14px;' />&nbsp;&nbsp;&nbsp;&nbsp;<span style='font:14px; color: #AAAAAA; font-weight: 900;'>You will be logged out in a few seconds. Please relogin with your new username and password...</span>\",
                                    css: { border: 'none', padding: '15px', backgroundColor: '#000', '-webkit-border-radius': '10px', '-moz-border-radius': '10px', opacity: .7, color: '#fff' }
                                });
                                setTimeout(function() {
                                    jQuery.unblockUI();
                                    document.location.href='login.php?logout=1';
                                }, 6000);
                            ";
                            return;
                        }
                    } else {
                        print "jQuery(\"#error-message\").html(\"Unable to save changes to user! Please try again...\").show(0).delay(4000).hide(0);\n";
                    }
                    print "AJAXCallModuleJSOnly('Users','printTabContent', 'branchid=$branchid&page='+jQuery('#page-number').val());\n";
                } else {
                    if(register_new_system_user($username, $firstname, $lastname, $email, $md5_password, $branchid == 0 ? $newbranchid : $branchid, $branchid == 0 ? 1: 0, $active, $_SESSION["storeid"])) {
                        

                       // require_once(dirname(__FILE__)."/../../inc/class.phpmailer.php");
                        //Sends user their credentials
                        //Sends email to the respective user
                        $mail = new PHPMailer(); 
                        $mail->Mailer = 'smtp';

                        $mail->setFrom("noreply@clientassist.co.za","Theo Developer");
                        $mail->Subject = "Ellies User credentials:";

                        $fullname = $firstname." ".$lastname;

                        //message
                        $emailmessage = '
                        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                        <html xmlns="http://www.w3.org/1999/xhtml">
                        <head>
                            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                            <title>Ellies User credentials</title>
                        </head>
                        <body>
                            <p>Hi '.$fullname.',<br/><br/>
                            Your Ellies account has been created.<br/>
                            Please keep the details below safe and secure.<br/>
                            Username:'.$username.'<br/>
                            Email:'.$email.'<br/>
                            Password: '.$password.'</p>
                        </body>
                        </html>';

                        //Set who the message is to be sent to
                        $mail->addAddress($email,"Ellies Member");

                        //convert HTML into a basic plain-text alternative body
                        $mail->msgHTML($emailmessage); 
                        // Mail it
                        if($mail->send()){
                             logAction("Created new system user users.id[".mysqli_insert_id($GLOBALS["link"])."]");
                             print "jQuery(\"#ok-message\").html(\"New user successfully created and credentials sent...\").show(0).delay(4000).hide(0);\n";
                        }else{
                            print "jQuery(\"#error-message\").html(\"Unable to send new user credentials! Please try again...\").show(0).delay(4000).hide(0);\n";
                        }
                    } else {
                        print "jQuery(\"#error-message\").html(\"Unable to create new user! Please try again...\").show(0).delay(4000).hide(0);\n";
                    }
                    //print "AJAXCallModuleJSOnly('Users','printTabContent', 'branchid=$branchid&storeid=".$_SESSION["storeid"]."&page=1'+jQuery('#page-number').val());\n";
                    print "AJAXCallModuleJSOnly('Users','printTabContent', 'branchid=$branchid&storeid=".$_SESSION["storeid"]."&page=1');\n";
                }
            }
        }
        
        function addNewBranch ($array) {
            if (!$GLOBALS['system_user']->hasPermission("users_add_new_branch")) {
                print "jQuery(\"#error-message\").html(\"Cannot complete action! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
                return;
            }
            $branchid           = intval($array['branchid']);
            $branchname         = urldecode($array['branchname']);
            $branchaddress      = urldecode($array['branchaddress']);
            $approvalsetting  = urldecode($array['approvalsetting']);
            $branchcode  = urldecode($array['branchcode']);
            $branchchannel  = urldecode($array['branchchannel']);
            $editbranch         = intval($array['edit']);
              
            if($editbranch == 1){
                $query = "UPDATE `branches` SET `branch` = \"".mysqli_real_escape_string($GLOBALS["link"],$branchname)."\", `channel` = \"".mysqli_real_escape_string($GLOBALS["link"],$branchchannel)."\", `approval_setting` = \"".mysqli_real_escape_string($GLOBALS["link"],$approvalsetting)."\", `prefix` = \"".mysqli_real_escape_string($GLOBALS["link"],$branchcode)."\", `address` = \"".mysqli_real_escape_string($GLOBALS["link"],$branchaddress)."\" ,`last_updated`= NOW() WHERE `id` = " . mysqli_real_escape_string($GLOBALS["link"],$branchid);
                if(mysqli_query($GLOBALS["link"],$query)) {
                    print "jQuery(\"#ok-message\").html(\"Branch successfully updated...\").show(0).delay(4000).hide(0);\n";
                    logAction("Edited branch [branch.id=$branchid]");
                } else {
                    print "jQuery(\"#error-message\").html(\"Unable to save changes to the branch! Please try again...\").show(0).delay(4000).hide(0);\n";
                }
                $str = "<input type='hidden' class='branch-ids' value='" . $branchid . "'>";
                $str .= "<input type='hidden' class='branch-channels' value='" . $branchchannel . "'>";
                $str .= "<input type='hidden' class='branch-names' value='" . htmlentities($branchname, ENT_QUOTES) . "'>";
                $str .= "<input type='hidden' class='branch-addresses' value='" . htmlentities(str_replace("\n", "\\n", $branchaddress), ENT_QUOTES) . "'>";
                $str .= "<input type='hidden' class='branch-code' value='" . htmlentities($branchcode) . "'>";
                $str .= "<input type='hidden' class='branch-approvalsetting' value='" . htmlspecialchars($approvalsetting) . "'>";
                $str .= htmlentities($this->trimBranchName($branchname, strlen(SITE_TITLE)), ENT_QUOTES);
                print "jQuery.unblockUI({ fadeOut: 0 });\n";
                if ($GLOBALS['system_user']->hasPermission("users_view_all_users")) {
                    print "jQuery(\"#all-branches-$branchid\").html(\"$str\");\n";
                    print "jQuery(\"#all-branches-$branchid\").trigger(\"click\");\n";
                } else {
                    print "jQuery(\"#users\").trigger(\"click\");\n";
                }
            } else {
                 $query = "INSERT INTO `branches` (`branch`, `address`,`approval_setting`, `channel`, `prefix`, `deleted`,`date_created`,`last_updated`) VALUES (\"".mysqli_real_escape_string($GLOBALS["link"],$branchname)."\",\"".mysqli_real_escape_string($GLOBALS["link"],$branchaddress)."\",\"".mysqli_real_escape_string($GLOBALS["link"],$approvalsetting)."\",\"".mysqli_real_escape_string($GLOBALS["link"],$branchchannel)."\",\"".mysqli_real_escape_string($GLOBALS["link"],$branchcode)."\", \"0\",NOW(),NOW())";
                if(mysqli_query($GLOBALS["link"],$query)) {
                    print "jQuery(\"#ok-message\").html(\"New branch successfully added...\").show(0).delay(4000).hide(0);\n";
                    logAction("Added new branch branch.id[".mysqli_insert_id($GLOBALS["link"])."]");
                } else {
                    print "jQuery(\"#error-message\").html(\"Unable to add new branch! Please try again...\").show(0).delay(4000).hide(0);\n";
                }
                $str = "<div class='all-branches' id='all-branches-".mysqli_insert_id($GLOBALS["link"])."'>";
                    $str .= "<input type='hidden' class='branch-ids' value='" . mysqli_insert_id($GLOBALS["link"]) . "'>";
                    $str .= "<input type='hidden' class='branch-channels' value='" . $branchchannel . "'>";
                    $str .= "<input type='hidden' class='branch-names' value='" . htmlentities($branchname, ENT_QUOTES) . "'>";
                    $str .= "<input type='hidden' class='branch-addresses' value='" . htmlentities(str_replace("\n", "\\n", $branchaddress), ENT_QUOTES) . "'>";
                    $str .= "<input type='hidden' class='branch-code' value='" . htmlentities($branchcode) . "'>";
                     $str .= "<input type='hidden' class='branch-approvalsetting' value='" . htmlspecialchars($approvalsetting) . "'>";
                    $str .= htmlentities($this->trimBranchName($branchname, strlen(SITE_TITLE)), ENT_QUOTES);
                $str .= "</div>";
                print "jQuery.unblockUI({ fadeOut: 0 });\n";
                print "jQuery(\"#branch-list\").append(\"$str\");\n";
            }
        }

        function addNewChannel ($array) {
            if (!$GLOBALS['system_user']->hasPermission("users_add_new_store")) {
                print "jQuery(\"#error-message\").html(\"Cannot complete action! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
                return;
            }
        
            $name  = urldecode($array['name']);
            $channeltype  = urldecode($array['channeltype']);

            $query = "INSERT INTO `channels` (`name`,`type`,`date_created`,`last_updated`) VALUES ('".mysqli_real_escape_string($GLOBALS["link"],$name)."','".mysqli_real_escape_string($GLOBALS["link"],$channeltype)."',NOW(),NOW())";
            if(mysqli_query($GLOBALS["link"],$query)) {
                print "jQuery(\"#ok-message\").html(\"New channel successfully added...\").show(0).delay(4000).hide(0);\n";
                logAction("Added new store store.id[".mysqli_insert_id($GLOBALS["link"])."]");
            } else {
                print "jQuery(\"#error-message\").html(\"Unable to add new channel! Please try again...\").show(0).delay(4000).hide(0);\n";
            }
            print "jQuery.unblockUI({ fadeOut: 0 });\n";
        }

        function addNewStore ($array) {
            if (!$GLOBALS['system_user']->hasPermission("users_add_new_store")) {
                print "jQuery(\"#error-message\").html(\"Cannot complete action! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
                return;
            }
           //$storeid           = intval($_SESSION["storeid"]);
            $branchid           = intval($array['branchid']);
            $storename         = urldecode($array['storename']);
            $storecode         = urldecode($array['storecode']);
            $storeaddress         = urldecode($array['storeaddress']);
            $contactnumber         = urldecode($array['contactnumber']);
            $manager         = urldecode($array['manager']);  
            $regionid         = urldecode($array['regionid']);
            $gpscoords      = urldecode($array['gpscoords']);
            $editstore         = intval($array['edit']);
              
            if($editstore == 1){
                $query = "UPDATE `stores` SET `store` = \"".mysqli_real_escape_string($GLOBALS["link"],$storename)."\", `store_code` = \"".mysqli_real_escape_string($GLOBALS["link"],$storecode)."\", `address` = \"".mysqli_real_escape_string($GLOBALS["link"],$storeaddress)."\", `contact_number` = \"".mysqli_real_escape_string($GLOBALS["link"],$contactnumber)."\", `branch_id` = \"".mysqli_real_escape_string($GLOBALS["link"],$branchid)."\", `manager` = \"".mysqli_real_escape_string($GLOBALS["link"],$manager)."\", `region` = \"".mysqli_real_escape_string($GLOBALS["link"],$regionid)."\", `gps_coords` = \"".mysqli_real_escape_string($GLOBALS["link"],$gpscoords)."\" `last_updated`= NOW() WHERE `id` = " . mysqli_real_escape_string($GLOBALS["link"],$storeid);
                if(mysqli_query($GLOBALS["link"],$query)) {
                    print "jQuery(\"#ok-message\").html(\"Store successfully updated...\").show(0).delay(4000).hide(0);\n";
                    logAction("Edited store [store.id=$storeid]");
                } else {
                    print "jQuery(\"#error-message\").html(\"Unable to save changes to the store! Please try again...\").show(0).delay(4000).hide(0);\n";
                }
                if ($GLOBALS['system_user']->hasPermission("users_view_all_users")) {
                    print "jQuery(\"#all-branches-$branchid\").trigger(\"click\");\n";
                } else {
                    print "jQuery(\"#users\").trigger(\"click\");\n";
                }
                print "jQuery.unblockUI({ fadeOut: 0 });\n";
            } else {
                $query = "INSERT INTO `stores` (`store`, `gps_coords`,`branch_id`,`address`,`contact_number`,`manager`,`region`,`store_code`,`date_created`,`last_updated`) VALUES (\"".mysqli_real_escape_string($GLOBALS["link"],$storename)."\",\"".mysqli_real_escape_string($GLOBALS["link"],$gpscoords)."\",\"".mysqli_real_escape_string($GLOBALS["link"],$branchid)."\",\"".mysqli_real_escape_string($GLOBALS["link"],$storeaddress)."\",\"".mysqli_real_escape_string($GLOBALS["link"],$contactnumber)."\",\"".mysqli_real_escape_string($GLOBALS["link"],$manager)."\",\"".mysqli_real_escape_string($GLOBALS["link"],$regionid)."\",\"".mysqli_real_escape_string($GLOBALS["link"],$storecode)."\",NOW(),NOW())";
                if(mysqli_query($GLOBALS["link"],$query)) {
                    print "jQuery(\"#ok-message\").html(\"New store successfully added...\").show(0).delay(4000).hide(0);\n";
                    logAction("Added new store store.id[".mysqli_insert_id($GLOBALS["link"])."]");
                } else {
                    print "jQuery(\"#error-message\").html(\"Unable to add new store! Please try again...\").show(0).delay(4000).hide(0);\n";
                } 
                print "jQuery.unblockUI({ fadeOut: 0 });\n";   
            }      
        }
        
        function deleteBranch ($array) {
            
            if (!$GLOBALS['system_user']->hasPermission("users_delete_branches")) {
                print "jQuery(\"#error-message\").html(\"Cannot complete action! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
                return;
            }
            
            $branchid = $array['branchid'];
            
            $result = mysqli_query($GLOBALS["link"],"SELECT `id` FROM `system_users` WHERE `branch_id` = " . mysqli_real_escape_string($GLOBALS["link"],$branchid));
            if (mysqli_num_rows($result) > 0) {
                print "jQuery(\"#error-message\").html(\"You cannot delete this branch because it has users\").show(0).delay(4000).hide(0);\n";
            } else if ($branchid != 0){
                if(mysqli_query($GLOBALS["link"],"UPDATE `branches` SET `deleted` = 1 WHERE `id` = " . mysqli_real_escape_string($GLOBALS["link"],$branchid))) {
                    logAction("Deleted branch branch.id['$branchid']");
                    print "jQuery(\"#ok-message\").html(\"Branch successfully deleted...\").show(0).delay(4000).hide(0);\n";
                    print "jQuery(\"#all-branches-$branchid\").remove();\n";
                    print "jQuery(\".main-branch\").trigger(\"click\");\n";
                } else {
                    print "jQuery(\"#error-message\").html(\"Unable to delete branch! Please try again...\").show(0).delay(4000).hide(0);\n";
                }
            } else {
                print "jQuery(\"#error-message\").html(\"Unable to delete branch! Branch not found\").show(0).delay(4000).hide(0);\n";
            }
        }
        
        function moveUsers ($array) {
            
            if (!$GLOBALS['system_user']->hasPermission("users_move_user")) {
                print "jQuery(\"#error-message\").html(\"Cannot complete action! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
                return;
            }
            
            $branchid = intval($array['branchid']);
            $storeid = intval($array['storeid']);
            $users = $array['users'];
            
            if ($branchid == 0 && $storeid > 0) {
                $sql = "SELECT `branch_id` FROM `stores` WHERE `id` = '".mysqli_real_escape_string($GLOBALS["link"],$storeid)."'";
                $sqlres = mysqli_query($GLOBALS["link"],$sql);
                while ($row = mysqli_fetch_assoc($sqlres)) {
                    $branchid = $row["branch_id"];
                }
            }
            
            $users = preg_split("/[\s\|]+/", $users, NULL, PREG_SPLIT_NO_EMPTY);
            $user_no = count($users);
            $s = $user_no > 1 ? "s" : "";
            $query = "UPDATE `system_users` SET `branch_id` = \"".mysqli_real_escape_string($GLOBALS["link"],$branchid)."\", `store_id` = \"".mysqli_real_escape_string($GLOBALS["link"],$storeid)."\", date_updated = NOW() WHERE";
            foreach($users as $u) {
                $query .= " `id` = " . mysqli_real_escape_string($GLOBALS["link"],$u) . " OR";
                print "jQuery(\"#user-tr-$u\").remove();\n";
            }
            $query = substr($query, 0, -3);
            mysqli_query($GLOBALS["link"],$query);
            print "if(jQuery(\".user-tr\").length == 0){\n";
                print "jQuery(\"#users-table tbody\").append(\"<tr><td colspan='8' style='text-align: center; font-style: italic;'>This branch xxxx currently has no users</td></tr>\");\n";
            print "}\n";
            print "jQuery(\"#ok-message\").html(\"$user_no user$s successfully moved\").show(0).delay(4000).hide(0);\n";
            logAction("Moved $user_no user$s to branch.id[$branchid]");
        }
        
        function deactivateUser ($array) {
            
            if (!$GLOBALS['system_user']->hasPermission("users_deactivate_users")) {
                print "jQuery(\"#error-message\").html(\"Cannot complete action! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
                return;
            }
            
            $users = $array['users'];
            
            $users = preg_split("/[\s\|]+/", $users, NULL, PREG_SPLIT_NO_EMPTY);
            $user_no = count($users);
            $s = $user_no > 1 ? "s" : "";
            $query = "UPDATE `system_users` SET `active` = 0, date_updated = NOW() WHERE";
            foreach($users as $u) {
                $query .= " `id` = " . mysqli_real_escape_string($GLOBALS["link"],$u) . " OR";
            }
            $query = substr($query, 0, -3);
            if(mysqli_query($GLOBALS["link"],$query)){
                print "jQuery(\"#ok-message\").html(\"$user_no user$s successfully deactivated\").show(0).delay(4000).hide(0);\n";
                logAction("Deactivated $user_no user$s");
            } else {
                print "jQuery(\"#error-message\").html(\"Unable to deactivate $user_no user$s! Please try again...\").show(0).delay(4000).hide(0);\n";
            }
            //print "jQuery(\"#all-branches-\" + jQuery(\"#chosen-branch\").val()).trigger(\"click\");\n";
            print "AJAXCallModuleJSOnly('Users','printTabContent', 'branchid='+jQuery('#chosen-branch').val()+'&page='+jQuery('#page-number').val());\n";
        }
        
        function makeSuperadmin ($array) {
            
            if (!$GLOBALS['system_user']->hasPermission("users_create_new_superadmin")) {
                print "jQuery(\"#error-message\").html(\"Cannot complete action! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
                return;
            }
            
            $users = $array['users'];
            
            $users = preg_split("/[\s\|]+/", $users, NULL, PREG_SPLIT_NO_EMPTY);
            $user_no = count($users);
            $s = $user_no > 1 ? "s" : "";
            $query = "UPDATE `system_users` SET `super_admin` = 1, active = 1, date_updated = NOW() WHERE";
            foreach($users as $u) {
                $query .= " `id` = " . mysqli_real_escape_string($GLOBALS["link"],$u) . " OR";
            }
            $query = substr($query, 0, -3);
            if(mysqli_query($GLOBALS["link"],$query)){
                print "jQuery(\"#ok-message\").html(\"$user_no user$s successfully made Super administrator$s\").show(0).delay(4000).hide(0);\n";
                logAction("Made $user_no user$s Super administrator$s");
            } else {
                print "jQuery(\"#error-message\").html(\"Unable to make $user_no users$s Super administrator$s! Please try again...\").show(0).delay(4000).hide(0);\n";
            }
            print "AJAXCallModuleJSOnly('Users','printTabContent', 'branchid='+jQuery('#chosen-branch').val()+'&page='+jQuery('#page-number').val());\n";
            //print "jQuery(\"#all-branches-\" + jQuery(\"#chosen-branch\").val()).trigger(\"click\");\n";
        }
        
        function editUser ($array) {
            $userid = intval($array['userid']);
            
            $query  = "SELECT `super_admin`, `first`, `last`, `username`, `email`, `branch_id`, `active`";
            $query .= " FROM `system_users`";
            $query .= " WHERE `id` = " . mysqli_real_escape_string($GLOBALS["link"],$userid);
            
            $result = mysqli_query($GLOBALS["link"],$query);
            $arr = mysqli_fetch_assoc($result);
            
            
            print "jQuery(\"#create-user-title\").html(\"Edit user\");\n";
            print "jQuery(\"#create-user-error\").html(\"&nbsp;\");\n";
            print "jQuery(\"#show-create-user-anchor\").html(\"Change Password\");\n";
            print "jQuery(\"#new-firstname\").val(\"".htmlentities($arr['first'], ENT_QUOTES)."\");\n";
            print "jQuery(\"#new-lastname\").val(\"".htmlentities($arr['last'], ENT_QUOTES)."\");\n";
            print "jQuery(\"#new-email\").val(\"".htmlentities($arr['email'], ENT_QUOTES)."\");\n";
            print "jQuery(\"#new-username\").val(\"".htmlentities($arr['username'], ENT_QUOTES)."\");\n";
            
            if($arr['active'] == 1) {
                print "jQuery(\"#new-user-active\").attr(\"checked\", true);\n";
            } else {
                print "jQuery(\"#new-user-active\").removeAttr(\"checked\");\n";
            }
            
            print "jQuery(\"#new-password1\").val('');\n";
            print "jQuery(\"#new-password2\").val('');\n";
            print "jQuery(\"#create-user-password-tr\").css(\"display\", \"none\");\n";
            print "jQuery(\".disable-create-user\").removeAttr(\"disabled\");\n";
            print "jQuery(\"#new-user-active\").removeAttr(\"disabled\");\n";
            print "jQuery(\"#save-user\").removeAttr(\"disabled\");\n";
            print "jQuery(\"#edit-user-password\").val(\"0\");\n";
            
            if($GLOBALS['system_user']->id == $userid && $arr['super_admin'] == 1) {
                print "jQuery(\"#show-create-user-password\").css(\"display\", \"table-row\");\n";
                print "jQuery(\"#show-branch-tr\").css(\"display\", \"table-row\");\n";
                print "jQuery(\"#new-branch option[value=" . $arr['branch_id'] . "]\").attr(\"selected\", true);\n";
                print "jQuery(\"#new-user-active\").attr(\"disabled\", true);\n";
                //print "jQuery(\"#show-create-user-anchor\").click(function(){if(jQuery(\"#create-user-password-tr\").is(\":visible\")) jQuery(\"#create-user-password-tr\").css(\"display\", \"none\"); else jQuery(\"#create-user-password-tr\").css(\"display\", \"table-row\");});\n";
            } else if($GLOBALS['system_user']->id == $userid) {
                print "jQuery(\"#show-create-user-password\").css(\"display\", \"table-row\");\n";
                print "jQuery(\"#show-branch-tr\").css(\"display\", \"none\");\n";
                //print "jQuery(\"#show-create-user-anchor\").click(function(){if(jQuery(\"#create-user-password-tr\").is(\":visible\")) jQuery(\"#create-user-password-tr\").css(\"display\", \"none\"); else jQuery(\"#create-user-password-tr\").css(\"display\", \"table-row\");});\n";
            } else if($arr['super_admin'] == 1) {
                print "jQuery(\"#show-create-user-password\").css(\"display\", \"none\");\n";
                print "jQuery(\"#show-branch-tr\").css(\"display\", \"table-row\");\n";
                print "jQuery(\"#new-branch option[value=" . $arr['branch_id'] . "]\").attr(\"selected\", true);\n";
                print "jQuery(\".disable-create-user\").attr(\"disabled\", true);\n";
                print "jQuery(\"#new-user-active\").attr(\"disabled\", true);\n";
                print "jQuery(\"#save-user\").attr(\"disabled\", true);\n";
            } else {
                print "jQuery(\"#show-create-user-password\").css(\"display\", \"table-row\");\n";
                print "jQuery(\"#show-branch-tr\").css(\"display\", \"none\");\n";
                //print "jQuery(\"#show-create-user-anchor\").click(function(){if(jQuery(\"#create-user-password-tr\").is(\":visible\")) jQuery(\"#create-user-password-tr\").css(\"display\", \"none\"); else jQuery(\"#create-user-password-tr\").css(\"display\", \"table-row\");});\n";
            }
            
            print "jQuery(\"#activate-user-span\").html(\"&nbsp;Active\");\n";
            print "jQuery(\"#create-user-password\").html(\"New Password\");\n";
            print "jQuery(\"#save-user\").html(\"Save changes\");\n";
  
            print "blockDialog(\"#create-user-dialog\");\n";
        }
        
        function getUserPermissions ($array) {
            $userid = intval($array['userid']);
            $user_permissions = array();
            $group_permissions = array();
            
            if ($GLOBALS['system_user']->hasPermission("users_modify_permissions")) {
                print "jQuery(\".group-permission-main-checkbox, .group-permission-sub-checkbox, #save-user-permissions-button\").removeAttr(\"disabled\");\n";
            }
            
            $query = "SELECT `permission` FROM `user_permissions` WHERE `user_id` = " . mysqli_real_escape_string($GLOBALS["link"],$userid);
            $result = mysqli_query($GLOBALS["link"],$query);
            while ($arr = mysqli_fetch_assoc($result)) {
                $user_permissions[] = $arr['permission'];
                print "jQuery(\"input[type='checkbox'][value='" . $arr['permission'] . "'].group-permission-sub-checkbox\").attr(\"checked\", true);\n";
            }
            $query = "SELECT `permission` FROM `group_permissions` WHERE `group_id` IN (SELECT `group_id` FROM `user_groups` WHERE `user_id` = " . mysqli_real_escape_string($GLOBALS["link"],$userid) . ")";
            $result = mysqli_query($GLOBALS["link"],$query);
            while ($arr = mysqli_fetch_assoc($result)) {
                $group_permissions[] = $arr['permission'];
                print "jQuery(\"input[type='checkbox'][value='" . $arr['permission'] . "'].group-permission-sub-checkbox\").attr(\"checked\", true).attr(\"disabled\", true);\n";
                print "jQuery(\"#td-" . $arr['permission'] . "\").html(\"inherited group permission\");\n";
            }
        }
        
        function saveUserPermissions ($array) {
            
            if (!$GLOBALS['system_user']->hasPermission("users_modify_permissions")) {
                print "jQuery(\"#error-message\").html(\"Cannot complete action! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
                return;
            }
            
            $userid = intval($array['userid']);
            $permissions = preg_split("/[\s\|]+/", $array['permissions'], NULL, PREG_SPLIT_NO_EMPTY);
            
            $query1 = "DELETE FROM `user_permissions` WHERE `user_id` = " . mysqli_real_escape_string($GLOBALS["link"],$userid);
            $query2 = "INSERT INTO `user_permissions` (`user_id`, `permission`) VALUES ";
            $i=0;
            foreach ($permissions as $p) {
                $query2 .= "(\"".mysqli_real_escape_string($GLOBALS["link"],$userid)."\", \"".mysqli_real_escape_string($GLOBALS["link"],$p)."\"),";
                $i++;
            }
            $query2 = substr($query2, 0, -1);
            if ($i > 0) {
                $result1 = mysqli_query($GLOBALS["link"],$query1);
                $result2 = mysqli_query($GLOBALS["link"],$query2);
            } else {
                $result1 = 1;
                $result2 = 1;
            }
            if($result1 && $result2) {
                print "jQuery(\"#ok-message\").html(\"User permissions successfully updated\").show(0).delay(4000).hide(0);\n";
                logAction("Updated system user permissions users.id[$userid]");
            } else {
                print "jQuery(\"#error-message\").html(\"Unable to update user permissions! Please try again...\").show(0).delay(4000).hide(0);\n";
            }
        }
        
        function getBranch ($branchid) {
            $branch = array();
            $query  = "SELECT `branch`, `prefix`, `address`, `deleted`";
            $query .= " FROM `branches`";
            $query .= " WHERE `id` = " . mysqli_real_escape_string($GLOBALS["link"],$branchid);
            $result = mysqli_query($GLOBALS["link"],$query);
            $arr    = mysqli_fetch_assoc($result);
            
            $branch['branch']      = $arr['branch'];
            $branch['prefix'] = $arr['prefix'];
            $branch['address']     = $arr['address'];
            $branch['deleted']     = $arr['deleted'];
            
            return $branch;
        }
        
        function trimBranchName ($branchName, $maxSizeBeforeTrim = 22) {
            return strlen($branchName) > $maxSizeBeforeTrim ? substr($branchName, 0, $maxSizeBeforeTrim - 3) . "..." : $branchName;
        }
        
        function printPaging ($current, $total_pages, $branchid, $index_limit = 3) {
            $start = max($current - intval($index_limit / 2), 1);
            $end = $start + $index_limit - 1;
            
            $pagestr = "<div class='paging'>";
                if($current == 1) {
                    $pagestr .= "<span class='prn'>&laquo;&nbsp;Previous</span>";
                }
                else {
                    $i = $current - 1;
                    $pagestr .= "<a class='prn' title='go to page ".$i."' rel='nofollow' onclick=AJAXCallModuleJSOnly(\'Users\',\'printTabContent\',\'branchid=$branchid&page=$i\');>&laquo;&nbsp;Previous</a>";
                    $pagestr .= "<span class='prn'>...</span>";
                }
                
                if($start > 1) {
                    $i = 1;
                    $pagestr .= "<a title='go to page ".$i."' onclick=AJAXCallModuleJSOnly(\'Users\',\'printTabContent\',\'branchid=$branchid&page=$i\');>".$i."</a>";
                }
                
                for($i = $start; $i <= $end && $i <= $total_pages; $i++){
                    $pagestr .= $i == $current ? "<span>".$i."</span>" : "<a title='go to page ".$i."' onclick=AJAXCallModuleJSOnly(\'Users\',\'printTabContent\',\'branchid=$branchid&page=$i\');>".$i."</a>";
                }
                
                if($total_pages > $end){
                    $i = $total_pages;
                    $pagestr .= "<span class='prn'>...</span>";
                    $pagestr .= "<a title='go to page ".$i."' onclick=AJAXCallModuleJSOnly(\'Users\',\'printTabContent\',\'branchid=$branchid&page=$i\');>Last</a>";
                }
                
                if($current < $total_pages){
                    $i = $current+1;
                    $pagestr .= "<a class='prn' title='go to page ".$i."' rel='nofollow' onclick=AJAXCallModuleJSOnly(\'Users\',\'printTabContent\',\'branchid=$branchid&page=$i\');>Next&nbsp;&raquo;</a>";
                }
                else{
                    $pagestr .= "<span class='prn'>Next&nbsp;&raquo;</span>";
                }
            $pagestr .= "</div>";
            
            return $pagestr;
        }
        
        function formatPastTime ($datetime) {
            $givenTimestamp = strtotime($datetime);
            $currentTimestamp = strtotime("NOW");
            $timestampDifference = $currentTimestamp - $givenTimestamp;
            
            if($givenTimestamp < 0) {
                return "Never";
            } else if (date("Ymd", $currentTimestamp) == date("Ymd", $givenTimestamp)) {
                if ($timestampDifference < 59) {
                    return $timestampDifference . " second" . ($timestampDifference == 1 ? "" : "s") . " ago";
                } else{
                    return "Today, " . date("H:i", $givenTimestamp);
                }
            } else if (date("Y", $currentTimestamp) == date("Y", $givenTimestamp)) {
                return date("M jS, H:i", $givenTimestamp);
            } else {
                return date("M jS, Y, H:i", $givenTimestamp);
            }
            return strtotime($datetime);
        }
        
        public function getCriteria(){

            $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
            $sqlres = mysqli_query($GLOBALS["link"],$sql);
            $row = mysqli_fetch_assoc($sqlres);
            $channeltype = $row['type'];

            if ($channeltype == "Commercial" || $channeltype =="Franchises"){
                  if ($GLOBALS['system_user']->isSuperAdmin == FALSE){
                    $cond =" AND system_users.store_id = ".$GLOBALS['system_user']->storeID."";
                  } else {
                    $cond = " AND system_users.branch_id = ".$GLOBALS['system_user']->branchID."";
                  }
            } else {
                  if ($GLOBALS['system_user']->isSuperAdmin == FALSE){
                    $cond .=" AND system_users.store_id = ".$GLOBALS['system_user']->storeID."";
                  } else {
                    $cond = " AND system_users.branch_id != ".$GLOBALS['system_user']->branchID."";
                  }
            }
            return $cond;
        }
        
        public function location_list($array) {
            $first="";
            $last="";
            if (!empty($array["first"])) {
            $first = $array['first'];
            }
            if (!empty($array["last"])) {
            $last = $array['last'];
            }

            print "<div class='classy_table'>";
            print "&nbsp;&nbsp;&nbsp;<span style='font-size:14px;font-weight:bold;'>User locations</span>";
            print "</div>";
            print "<br/><br/>";

            if ($GLOBALS['system_user']->hasPermission('users_edit_user')) {
                print '
                    <head>
                            <script type="text/javascript" language="javascript" class="init">
                            $(document).ready(function() {
                                $("#locations").dataTable( {
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
                                    "order": [[ 3, "desc" ]]
                                } );
                            } );
                            </script>
                        </head>
                        <body>
                            <table id="locations" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th width="15%">Date Created</th> 
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th width="15%">Location</th>               
                                    </tr>
                                </thead>
                                <tbody>';

                $sql = "SELECT locations . * , system_users.First, system_users.last FROM system_users, locations WHERE system_users.id = locations.userid";
                $sql .= $this->getCriteria();
                $sql .=" and system_users.First like '".$first."%' and system_users.last like '".$last."%' ORDER BY `time` DESC";
                $sqlres = mysqli_query($GLOBALS["link"],$sql);
                while($row = mysqli_fetch_assoc($sqlres)) {
                    print "<tr>";
                    print "<td align='center'>".$row["time"]."</td>";
                    print "<td align='center'>".$row["First"]."</td>";
                    print "<td align='center'>".$row["last"]."</td>";
                    print "<td align='center'>";
                    print "<a href='http://maps.google.com?q=".$row["latitude"].",".$row["longitude"]."' target='_blank'><span class='ui-button-text'><img  width='20' height='20' title='View Details'  src='modules/users/images/search.ico' /></span>";
                    print "</td>";
                    print "</tr>";
                }
                print '</tbody>
                            </table>
                        </body>
                        ';

                print "\n<!-- scripts , code below be eval()ed by javascript -->\n";
                            print "
                jQuery('#filter-locations-button').click(function () {
                    var first    = jQuery('#first').val();
                    var last = jQuery('#last').val();
                    AJAXCallModule('" . __CLASS__ . "', 'location_list', 'first='+first+'&last='+last);
                });\n
            ";    
            }else{
                print "<p>You do not have permission to view locations.</p>";
            }
        }

        function listStores($arr) {
            print "\$('#branch-stores-".$arr['branchid']."').html(\"";
            $_SESSION["branchid"] = $arr['branchid'];
            $subsql = "SELECT * FROM `stores` WHERE `branch_id` = '".mysqli_real_escape_string($GLOBALS["link"],$arr['branchid'])."'";
            $res = mysqli_query($GLOBALS["link"],$subsql);
            while ($subrow = mysqli_fetch_assoc($res)) {
                print "<div class='branch-store-item' id='branch-store-item-".$subrow["id"]."' onclick='GetStore(".$arr['branchid'].", ".$subrow["id"].")'>".$subrow["store"]."</div>";
            }               
            print "\");\n";
            exit();
        }
        //===========================================================================
        //Registration
        public function getChannel($channel=""){
            $channelname="";
            if($channel !==""){
                $sql = "SELECT `name` FROM `channels` WHERE `id`=".$channel;
                $sqlres = mysqli_query($GLOBALS["link"],$sql);
                $row = mysqli_fetch_assoc($sqlres);
                $channelname = $row['name'];
            }
            return $channelname;
        }

        public function getTeamName($team=""){
            $title="";
            if($team !==""){
                $sql = "SELECT `title` FROM `teams` WHERE `id`=".$team;
                $sqlres = mysqli_query($GLOBALS["link"],$sql);
                $row = mysqli_fetch_assoc($sqlres);
                $title = $row['title'];
            }
            return $title;
        }

        public function manage_team_member_profile ($array) {

            print "<div class='classy_table'>";
            print "&nbsp;&nbsp;&nbsp;<span style='font-size:14px;font-weight:bold;'>Manage Team Member Profiles</span>";
            print "</div>";
            print "<p style='float:right'><a href='#' id='editTeamProfile' name='editTeamProfile'><u>Edit</u></a> | <a href='#' id='addTeamProfile' name='addTeamProfile'><u>Add</u></p></a> ";
            print "<table id='editteamprofilesetup' name='editteamprofilesetup' cellspacing='0' cellpadding='4' border='0' width='100%'>";
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
                            print "<td colspan='2' style='text-align: right; font-weight: 400; color: #111111; padding-right: 20px;'>";                                 
                                print "Select Team";
                                print "<input type='hidden' id='profile_team_id' style='width: 200px' />"; 
                            print "</td>"; 
                            print "<td>";     
                                print "<select id='profile_team_edit'>";
                                    print "<option value='' selected='selected'>[Please select]</option>";
                                    $query  = "SELECT `id`,`title`";
                                    $query .= " FROM `teams`";
                                    $sysuser = new userType($_SESSION["userid"]);
                                    if ($sysuser->isSuperAdmin){
                                        $query .= " WHERE `deleted` = '0'";
                                    }else{
                                        $query .= " WHERE `deleted` = '0' AND channel=".$GLOBALS['system_user']->retailChannel;
                                    } 
                                    $query .= " ORDER BY `id`";
                                    $result = mysqli_query($GLOBALS["link"],$query);
                                    while ($arr = mysqli_fetch_assoc($result)) {
                                        print "<option id='".$arr['id']."' value='".$arr['title']."'>";
                                            print ucfirst($arr['title']);
                                        print "</option>";
                                    }
                                print "</select>";
                            print "</td>";
                        print "</tr>";

                         print "<tr>";
                            print "<td colspan='2' style='text-align: right; font-weight: 400; color: #111111; padding-right: 20px;'>";                                 
                                print "Select Member";
                                print "<input type='hidden' id='profile_member_id' style='width: 200px' />"; 
                            print "</td>"; 
                            print "<td>";     
                                print "<select id='profile_member_edit'>";
                                    print "<option value='' selected='selected'>[Please select]</option>";
                                print "</select>";
                            print "</td>";
                        print "</tr>";
                        print "<tr>";
                            print "<td colspan='4' style='text-align: left; padding: 0'>";
                             print "<div>";
                                 print "<table cellpadding='10'>";
                                     print "<tr>";
                                         print "<td>";
                                            print "Name";
                                         print "</td>";
                                         print "<td>";
                                            print "<input type='text' id='profilename_edit' readonly style='width: 200px' />";        
                                         print "</td>";
                                     print "</tr>";

                                     print "<tr>";
                                         print "<td>";
                                            print "Surname";
                                         print "</td>";
                                         print "<td>";
                                            print "<input type='text' id='profilesurname_edit'  readonly style='width: 200px' />";        
                                         print "</td>";
                                     print "</tr>";

                                     print "<tr>";
                                         print "<td>";
                                            print "Cellphone";
                                         print "</td>";
                                         print "<td>";
                                            print "<input type='text' id='profilecellphone_edit'  readonly style='width: 200px' />";        
                                         print "</td>";
                                     print "</tr>";

                                     print "<tr>";
                                         print "<td>";
                                            print "Email";
                                         print "</td>";
                                         print "<td>";
                                            print "<input type='text' id='profileemail_edit' readonly style='width: 200px' />";        
                                         print "</td>";
                                     print "</tr>";

                                     print "<tr>";
                                     print "<td>";
                                        print "Address";
                                     print "</td>";
                                     print "<td>";
                                        print "<textarea id='profileaddress_edit' readonly style='width: 98%; height: 50px; resize: none; overflow: auto;' maxlength='254'></textarea>";           
                                     print "</td>";
                                    print "</tr>";

                                    print "<tr>";
                                         print "<td>";
                                            print "Team";
                                         print "</td>";
                                         print "<td>";
                                            print "<input type='text' id='profileteamchannel_edit' readonly style='width: 200px' />";           
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
                                    print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button class='ibutton' id='delete-team-member-button' style='color:red;font-weight: bold;font-size: 12px;'>Delete/Unlink Team Member</button>";
                                    print "<br/><br/>";
                                print "</td>";
                            print "</tr>";
                        print "</tfoot>";
            print "</table>";
            print "<table id='addteamprofilesetup' name='addteamprofilesetup' cellspacing='0' cellpadding='4' border='0' width='100%'>";
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
                                        print "Name";
                                     print "</td>";
                                     print "<td>";
                                        print "<input type='text' id='profilename' style='width: 200px' />";        
                                     print "</td>";
                                 print "</tr>";

                                 print "<tr>";
                                     print "<td>";
                                        print "Surname";
                                     print "</td>";
                                     print "<td>";
                                        print "<input type='text' id='profilesurname' style='width: 200px' />";        
                                     print "</td>";
                                 print "</tr>";

                                 print "<tr>";
                                     print "<td>";
                                        print "Cellphone";
                                     print "</td>";
                                     print "<td>";
                                        print "<input type='text' id='profilecellphone' style='width: 200px' />";        
                                     print "</td>";
                                 print "</tr>";


                                 print "<tr>";
                                     print "<td>";
                                        print "Email";
                                     print "</td>";
                                     print "<td>";
                                        print "<input type='text' id='profileemail' style='width: 200px' />";        
                                     print "</td>";
                                 print "</tr>";

                                print "<tr>";
                                     print "<td>";
                                        print "Address";
                                     print "</td>";
                                     print "<td>";
                                        print "<textarea id='profileaddress' style='width: 98%; height: 50px; resize: none; overflow: auto;' maxlength='254'></textarea>";           
                                     print "</td>";
                                print "</tr>";

                                print "<tr>";
                                     print "<td>";
                                        print "Location";
                                     print "</td>";
                                     print "<td>";
                                       print "<select id='profilestore'>";
                                       print "<option value='' selected='selected'>[Please select]</option>";
                                       $query = "select id,branch from branches where deleted='0' AND channel =".$GLOBALS['system_user']->retailChannel;
                                       $result = mysqli_query($GLOBALS["link"],$query);
                                       $arrBranches = array();
                                       $arrBranchesID = array();
                                       while ($row=mysqli_fetch_assoc($result)){
                                            $arrBranches[] =$row["branch"];
                                            $arrBranchesID[] =$row["id"];
                                        }
                                        for($i=0;$i<count($arrBranches);$i++){
                                            print "<optgroup id='".$arrBranchesID[$i]."' label='".$arrBranches[$i]."'>";
                                            $query = "select id,store from stores where deleted='0' AND branch_id ='".$arrBranchesID[$i]."'";
                                            $result = mysqli_query($GLOBALS["link"],$query);
                                            while ($row=mysqli_fetch_assoc($result)){
                                                print "<option id='".$row['id']."' value='".$row["store"]."'>";
                                                    print ucfirst($row["store"]);
                                                print "</option>";
                                            }
                                            print "</optgroup>";
                                        }
                                        print "</select>";         
                                     print "</td>";
                                print "</tr>";


                                $sysuser = new userType($_SESSION["userid"]);
                                if ($sysuser->isSuperAdmin){
                                    print "<tr>";
                                     print "<td>";
                                        print "Team";
                                     print "</td>";
                                     print "<td>";
                                     $query = "select id,title from teams where deleted='0'";
                                     $result = mysqli_query($GLOBALS["link"],$query);
                                     print "<select id='profileteamchannel'>";
                                        print "<option value='' selected='selected'>[Please select]</option>";
                                        while ($row=mysqli_fetch_assoc($result)){
                                            print "<option id='".$row['id']."' value='".$row["title"]."'>";
                                                print ucfirst($row["title"]);
                                            print "</option>";
                                        }
                                     print "</select>";
                                print "</td>";
                                print "</tr>";
                                }else{
                                    print "<tr>";
                                         print "<td>";
                                            print "Team";
                                         print "</td>";
                                         print "<td>";
                                         $query = "select id,title from teams where deleted='0' AND channel =".$GLOBALS['system_user']->retailChannel;
                                         $result = mysqli_query($GLOBALS["link"],$query);
                                         print "<select id='profileteamchannel'>";
                                            print "<option value='' selected='selected'>[Please select]</option>";
                                            while ($row=mysqli_fetch_assoc($result)){
                                                print "<option id='".$row['id']."' value='".$row["title"]."'>";
                                                    print ucfirst($row["title"]);
                                                print "</option>";
                                            }
                                         print "</select>";
                                    print "</td>";
                                    print "</tr>"; 
                                }   
                             print "</table>";
                             //User creditionals
                             print "<h2>User Creditionals</h2>";
                             print "<table cellpadding='10'>";
                                 print "<tr>";
                                         print "<td>";
                                            print "Username";
                                         print "</td>";
                                         print "<td>";
                                            print "<input type='text' id='profileusername' style='width: 200px' />";        
                                         print "</td>";
                                 print "</tr>";
                                 print "<tr>";
                                         print "<td>";
                                            print "Choose one:";
                                         print "</td>";
                                         print "<td>";
                                          print "<label>
                                                  <input type='radio' name='GeneratedPassword' id='CreatedPassword'  value='GeneratedPassword' checked>
                                                  Generated Password 
                                                </label>";    
                                         print "</td>";
                                 print "</tr>";
                                  print "<tr>";
                                         print "<td>";
                                         print "</td>";
                                         print "<td>";
                                          print "<label>
                                                  <input type='radio' name='GeneratedPassword' id='CreatedPassword' value='CreatedPassword'>
                                                  Created Password 
                                                </label>"; 
                                         print "</td>";
                                 print "</tr>";
                             print "</table>";

                            print "<table  id='userpasswords' name='userpasswords' cellpadding='10'>";
                                 print "<tr>";
                                         print "<td>";
                                            print "Password";
                                         print "</td>";
                                         print "<td>";
                                            print "<input type='text' id='profilepassword' style='width: 200px' />";        
                                         print "</td>";
                                 print "</tr>";
                                 print "<tr>";
                                         print "<td>";
                                            print "Confirm Password";
                                         print "</td>";
                                         print "<td>";
                                            print "<input type='text' id='profileconfirmpassword' style='width: 200px' />";        
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
                                print "<span id='ok-message'></span><span id='error-message'style='color:red'></span>";
                                print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button class='ibutton' id='add-team-member-profile-button'>Save/Link Team Member Profile</button>";
                                print "<br/><br/>";
                            print "</td>";
                        print "</tr>";
                    print "</tfoot>";
            print "</table>"; 
            //=================================
            print '<script type="text/javascript" language="javascript" class="init">';
            print "jQuery('a#addTeamProfile').click(function () {
                    $('table#editteamprofilesetup').hide();
                    $('table#addteamprofilesetup').show(); 
                });\n
            ";
            
            print "$('table#editteamprofilesetup').hide();
                jQuery('a#editTeamProfile').click(function () {
                    $('table#addteamprofilesetup').hide();
                    $('table#editteamprofilesetup').show(); 
                });\n
            ";

            print "jQuery('select#profile_team_edit').change(function () { 
                    var id = $(profile_team_edit).children(':selected').attr('id');               
                    $('input#profile_team_id').val(id); 
                    AJAXCallTypeTeam('" . __CLASS__ . "', 'getTeamMembersInfo', 'teamID='+id);  
                });\n
            ";

            print "jQuery('select#profile_member_edit').change(function () {
                    var id = $(profile_member_edit).children(':selected').attr('id');               
                    $('input#profile_member_id').val(id); 
                    AJAXCallTeamMember('" . __CLASS__ . "', 'getTeamMembersInfoEdit', 'memberID='+id);  
                });\n
            ";

            print "jQuery('#add-team-member-profile-button').click(function () {
                        var name  = jQuery('input#profilename').val(); 
                        var surname  = jQuery('input#profilesurname').val(); 
                        var cellphone  = jQuery('input#profilecellphone').val(); 
                        var email  = jQuery('input#profileemail').val(); 
                        var address  = jQuery('textarea#profileaddress').val(); 

                        var select = $('select#profilestore');
                        var selectedItem= select.find(':selected');
                        var store = selectedItem.attr('id');

                        var team = $(profileteamchannel).children(':selected').attr('id'); 
                        var username  = jQuery('input#profileusername').val();

                        var createdPassword = jQuery(CreatedPassword).is(':checked');
                        var password = '';
                        var confirmpassword = '';

                        if(CreatedPassword){
                            password= $('input#profilepassword').val();
                            confirmpassword  = $('input#profileconfirmpassword').val(); 
                            AJAXCallModuleJSOnly('" . __CLASS__ . "', 'save_profile', 'name='+name+'&surname='+surname+'&cellphone='+cellphone+'&email='+email+'&address='+address+'&store='+store+'&team='+team+'&username='+username+'&password='+password+'&confirmpassword='+confirmpassword); 
                        }else{
                            AJAXCallModuleJSOnly('" . __CLASS__ . "', 'save_profile', 'name='+name+'&surname='+surname+'&cellphone='+cellphone+'&email='+email+'&address='+address+'&store='+store+'&team='+team+'&username='+username);  
                        }      
                   });\n
                "; 

            print "jQuery('#delete-team-member-button').click(function () {
                    var member  = jQuery('input#profile_member_id').val(); 
                    AJAXCallModuleJSOnly('" . __CLASS__ . "', 'delete_profile', 'member='+member);
                });\n
            ";

            print "$('table#userpasswords').hide();
                    jQuery('input#CreatedPassword').change(function() {
                        var CreatedPassword = $(this).is(':checked');
                        if(CreatedPassword){
                            $('table#userpasswords').toggle(); 
                        }else if(CreatedPassword == false){
                            $('input#CreatedPassword').checked = false;
                            $('table#userpasswords').hide(); 
                        }  
                    });

                    jQuery('input#GeneratedPassword').change(function() {
                        var GeneratedPassword = $(this).is(':checked');
                        if(GeneratedPassword){
                            $('input#CreatedPassword').checked = false;
                            $('table#userpasswords').hide(); 
                        }  
                    });";
            print '</script>';
            print "<div style='margin: 0 auto; width: 95%; border-bottom: 2px solid #BBBBBB;'>&nbsp;</div>";
        }

        public function manage_teams ($array) {

            print "<div class='classy_table'>";
            print "&nbsp;&nbsp;&nbsp;<span style='font-size:14px;font-weight:bold;'>Manage Teams</span>";
            print "</div>";
            print "<p style='float:right'><a href='#' id='viewTeams' name='viewTeams'><u>View</u></a> | <a href='#' id='editTeams' name='editTeams'><u>Edit</u></a> | <a href='#' id='addTeams' name='addTeams'><u>Add</u></p></a>";
            print '
                    <body>
                        <table id="viewteamsetup" name="viewteamsetup" class="display" cellspacing="2" width="100%">
                            <thead>
                                <tr>
                                    <th>Date Created</th>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Channel</th>            
                                </tr>
                            </thead>
                            <tbody>';
                                    
                                $query  = "SELECT `date_created`,`title`, `description`, `channel`";
                                $query .= " FROM `teams`";
                                $query .= " WHERE `deleted` = '0'";
                                $query .= " ORDER BY `id`";
                                $result = mysqli_query($GLOBALS["link"],$query);
                                while ($arr = mysqli_fetch_assoc($result)) {
                                   print "<tr>";
                                        print '<td align="center">'.date("Y-m-d", strtotime($arr['date_created'])).'</td>';
                                        print '<td align="center">'.$arr['title'].'</td>';
                                        print '<td align="center">'.$arr['description'] .'</td>';
                                        $channelname = $this->getChannel($arr['channel']);
                                        print '<td align="center">'.$channelname.'</td>';                            
                                    print "</tr>";
                                }

                        print "</tbody>";
                        print "</table>";
                        print '<script type="text/javascript" language="javascript" class="init">
                            $(document).ready(function() {
                                $("#viewteamsetup").dataTable( {
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
            print "<table id='editteamsetup' name='editteamsetup' cellspacing='0' cellpadding='4' border='0' width='100%'>";
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
                            print "<td colspan='2' style='text-align: right; font-weight: 400; color: #111111; padding-right: 20px;'>";                                 
                                print "Select Team";
                                print "<input type='hidden' id='team_id' style='width: 200px' />"; 
                            print "</td>"; 
                            print "<td>";     
                                print "<select id='team_edit'>";
                                    print "<option value='' selected='selected'>[Please select]</option>";
                                    $query  = "SELECT `id`,`date_created`, `title`, `description`, `channel`";
                                    $query .= " FROM `teams`";
                                    $query .= " WHERE `deleted` = '0'";
                                    $query .= " ORDER BY `id`";
                                    $result = mysqli_query($GLOBALS["link"],$query);
                                    while ($arr = mysqli_fetch_assoc($result)) {
                                        print "<option id='".$arr['id']."' value='".htmlentities($arr['title'])."'>";
                                            print ucfirst($arr['title']);
                                        print "</option>";
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
                                            print "Title";
                                         print "</td>";
                                         print "<td>";
                                            print "<input type='text' id='teamtitle_edit' style='width: 200px' />";        
                                         print "</td>";
                                     print "</tr>";

                                    print "<tr>";
                                         print "<td>";
                                            print "Description";
                                         print "</td>";
                                         print "<td>";
                                            print "<textarea id='teamdescription_edit' style='width: 98%; height: 50px; resize: none; overflow: auto;' maxlength='254'></textarea>";           
                                         print "</td>";
                                    print "</tr>";

                                    print "<tr style='display:none;'>";
                                         print "<td>";
                                            $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
                                            $sqlres = mysqli_query($GLOBALS["link"],$sql);
                                            $row = mysqli_fetch_assoc($sqlres);
                                            $channeltype = $row['type'];
                                            if($channeltype =="Commercial")print "Channel";
                                            if($channeltype =="Retail")print "Channel";
                                            if($channeltype =="Franchises")print "Franchisee";
                                         print "</td>";
                                         print "<td>";
                                            print "<input type='text' id='teamchannel_edit' style='width: 200px' readonly/>";        
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
                                    print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button class='ibutton' id='save-team-button'>Save Team</button>";
                                    print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button class='ibutton' id='delete-team-button' style='color:red;font-weight: bold;font-size: 12px;'>Delete Team</button>";
                                    print "<br/><br/>";
                                print "</td>";
                            print "</tr>";
                        print "</tfoot>";
            print "</table>";
            print "<table id='addteamsetup' name='addteamsetup' cellspacing='0' cellpadding='4' border='0' width='100%'>";
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
                                        print "<input type='text' id='addteamtitle' style='width: 200px' />";        
                                     print "</td>";
                                 print "</tr>";

                                print "<tr>";
                                     print "<td>";
                                        print "Description";
                                     print "</td>";
                                     print "<td>";
                                        print "<textarea id='addteamdescription' style='width: 98%; height: 50px; resize: none; overflow: auto;' maxlength='254'></textarea>";           
                                     print "</td>";
                                print "</tr>";
                                $sysuser = new userType($_SESSION["userid"]);
                                if ($sysuser->isSuperAdmin){
                                    print "<tr>";
                                     print "<td>";
                                            $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
                                            $sqlres = mysqli_query($GLOBALS["link"],$sql);
                                            $row = mysqli_fetch_assoc($sqlres);
                                            $channeltype = $row['type'];
                                            if($channeltype =="Commercial")print "Channel";
                                            if($channeltype =="Retail")print "Channel";
                                            if($channeltype =="Franchises")print "Franchisee";
                                     print "</td>";
                                     print "<td>";
                                     $query = "select id, name from channels";
                                     $result = mysqli_query($GLOBALS["link"],$query);
                                     print "<select id='addteamchannel'>";
                                        print "<option value='' selected='selected'>[Please select]</option>";
                                        while ($row=mysqli_fetch_assoc($result)){
                                            print "<option id='".$row['id']."' value='".$row["name"]."'>";
                                                print ucfirst($row["name"]);
                                            print "</option>";
                                        }


                                     print "</select>";
                                    print "</td>";
                                    print "</tr>";
                                }else{
                                    print "<input type='hidden' id='addteamchannel' style='width: 200px' value='".$GLOBALS['system_user']->retailChannel."' />";   
                                }   
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
                                print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button class='ibutton' id='add-team-button'>Save Team</button>";
                                print "<br/><br/>";
                            print "</td>";
                        print "</tr>";
                    print "</tfoot>";
            print "</table>"; 
            //==========================================
            print '<script type="text/javascript" language="javascript" class="init">';
            print "$('table#addteamsetup').hide();
                jQuery('a#addTeams').click(function () {
                     $('div#viewteamsetup_wrapper').hide();
                     $('table#editteamsetup').hide();
                    $('table#addteamsetup').show(); 
                });\n
            ";
            
            print "$('table#editteamsetup').hide();
                jQuery('a#editTeams').click(function () {
                     $('div#viewteamsetup_wrapper').hide();
                     $('table#addteamsetup').hide();
                    $('table#editteamsetup').show(); 
                });\n
            ";

            print "jQuery('a#viewTeams').click(function () {
                     $('table#editteamsetup').hide();
                     $('table#addteamsetup').hide();
                    $('div#viewteamsetup_wrapper').show(); 
                });\n
            "; 

            print "jQuery('select#team_edit').change(function () { 
                        var id = $(team_edit).children(':selected').attr('id');               
                        $('input#team_id').val(id); 
                        AJAXCallTeam('" . __CLASS__ . "', 'getTeamInfo', 'teamID='+id);  
                    });\n
                ";

            print "jQuery('#add-team-button').click(function () {
                        var title  = jQuery('input#addteamtitle').val(); 
                        var description  = jQuery('textarea#addteamdescription').val(); 
                        var channel =  $('addteamchannel').children(':selected').attr('id');
                        AJAXCallModuleJSOnly('" . __CLASS__ . "', 'add_team', 'title='+title+'&description='+description+'&channel='+channel);
                    });\n
                "; 
            print "jQuery('#save-team-button').click(function () {
                        var title  = jQuery('input#teamtitle_edit').val(); 
                        var description  = jQuery('textarea#teamdescription_edit').val(); 
                        var channel =  $('input#teamchannel_edit').val();
                        var id  = jQuery('input#team_id').val(); 
                        AJAXCallModuleJSOnly('" . __CLASS__ . "', 'save_team', 'id='+id+'&title='+title+'&description='+description+'&channel='+channel);
                    });\n
                "; 
            print "jQuery('#delete-team-button').click(function () {
                        var team  = jQuery('input#team_id').val(); 
                        AJAXCallModuleJSOnly('" . __CLASS__ . "', 'delete_team', 'team='+team);
                    });\n
                "; 
            print '</script>';
            print "<div style='margin: 0 auto; width: 95%; border-bottom: 2px solid #BBBBBB;'>&nbsp;</div>";
            print '</div>';
        }

        //Teams
        public function add_team ($array) {

            if ($GLOBALS['system_user']->hasPermission('manage_teams')) {
              
                $query  = "INSERT INTO `teams` (date_created,title,description,channel) VALUES (";
                $query  .= "NOW(),";
                $query  .= " '".$array["title"]. "',";
                $query  .= " '".$array["description"]. "',";
                $query  .= " '".$array["channel"]. "'";
                $query  .= ") ";
                $result = mysqli_query($GLOBALS["link"],$query);     
                if($result) {
                    print "jQuery(\"#ok-message\").html(\"Team saved...  \").show(0).delay(4000).hide(0);\n";
                    logAction("Added Team:$title");
                } else {
                    print "jQuery(\"#error-message\").html(\"Unable to save Team Inforamtion! Please try again...\").show(0).delay(4000).hide(0);\n";
                }  
                exit();
            }else{
                print "You do not have permission to perform this action.";
                exit();            
            }
        }

        public function save_team ($array) {

            if ($GLOBALS['system_user']->hasPermission('manage_teams')) {
                $query = "UPDATE teams SET";
                $query .= " `last_updated` =NOW(),";
                $query  .= " `title` = '" .$array["title"]."',";
                $query  .= " `description` = '".$array["description"]."',";
                $query  .= " `channel` = '".$array["channel"]."'";
                $query .= " WHERE id=".$array["id"]." LIMIT 1";
                $result = mysqli_query($GLOBALS["link"],$query);      
                if($result) {
                    print "jQuery(\"#ok-message2\").html(\"Team saved...  \").show(0).delay(4000).hide(0);\n";
                    logAction("Added Team:$title");
                } else {
                    print "jQuery(\"#error-message2\").html(\"Unable to save Team Inforamtion! Please try again...\").show(0).delay(4000).hide(0);\n";
                }  
                exit();
            }else{
                print "You do not have permission to perform this action.";
                exit();            
            }
        }

        public function delete_team ($array) {
            if ($GLOBALS['system_user']->hasPermission('manage_teams')) {
                $sql = "UPDATE teams SET";
                $sql .= " `last_updated` =NOW(),";
                $sql .= " `deleted` = '1'";
                $sql .= " WHERE id=".$array["team"]." LIMIT 1";
                $result = mysqli_query($GLOBALS["link"],$sql);
               
                if($result) {
                    print "jQuery(\"#ok-message2\").html(\"Team deleted...  \").show(0).delay(4000).hide(0);\n";
                    logAction("Deleted Team:$title");
                } else {
                   print "jQuery(\"#error-message2\").html(\"Unable to delete Team Inforamtion! Please try again...\").show(0).delay(4000).hide(0);\n";
                }  
                exit();
            }else{
                print "You do not have permission to perform this action.";
                exit();            
            }
        }

        public function getTeamInfo(){
            $teamID = (int)isset($_POST["teamID"])?$_POST["teamID"]:0;
            $query  = "SELECT *";
            $query .= " FROM `teams`";
            $query .= " WHERE `teams`.id= ".$teamID;

            if($GLOBALS['system_user']->hasPermission('manage_teams')) {                    
                $result = mysqli_query($GLOBALS["link"],$query);
                $arr = mysqli_fetch_assoc($result);
                print json_encode($arr);
             } else {
                print "jQuery(\"#error-message-room\").html(\"Unable to get Team information! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
             }
        }
        //=======================================
        function generateStrongPassword($length = 9, $add_dashes = false, $available_sets = 'luds') {
            $sets = array();
            if(strpos($available_sets, 'l') !== false)
            $sets[] = 'abcdefghjkmnpqrstuvwxyz';
            if(strpos($available_sets, 'u') !== false)
            $sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
            if(strpos($available_sets, 'd') !== false)
            $sets[] = '23456789';
            if(strpos($available_sets, 's') !== false)
            $sets[] = '!@#$%&*?';
             
            $all = '';
            $password = '';
            foreach($sets as $set)
            {
            $password .= $set[array_rand(str_split($set))];
            $all .= $set;
            }
             
            $all = str_split($all);
            for($i = 0; $i < $length - count($sets); $i++)
            $password .= $all[array_rand($all)];
             
            $password = str_shuffle($password);
             
            if(!$add_dashes)
            return $password;
             
            $dash_len = floor(sqrt($length));
            $dash_str = '';
            while(strlen($password) > $dash_len)
            {
            $dash_str .= substr($password, 0, $dash_len) . '-';
            $password = substr($password, $dash_len);
            }
            $dash_str .= $password;
            return $dash_str;
        }
        //Team Member Profiles
        public function save_profile ($array) {
            if ($GLOBALS['system_user']->hasPermission('manage_teams')) {
               $created = false;
                if(isset($array["confirmpassword"])){
                    $created = true;
                }
     
                $password = "";
                $userpassword = "";

                $error = "";
                
                if($created){
                    if($array["password"] == $array["confirmpassword"]){
                        $password = sha1($array["password"]);
                        $userpassword = $array["password"];
                    }else{
                        $error = "jQuery(\"#error-message\").html(\"Invalid Confirm Password! Please try again...\").show(0).delay(4000).hide(0);\n";
                    }
                }else{
                    $userpass = $this->generateStrongPassword();
                    $password = sha1($userpass);
                    $userpassword = $userpass;
                }
                
                if($error==""){
                    $query  = "INSERT INTO `team_member_profiles` (date_created,last_updated,name,surname,cellphone,email,address,team,username,password,store_id) VALUES (";
                    $query  .= "NOW(),";
                    $query  .= "NOW(),";
                    $query  .= " '".$array["name"]. "',";
                    $query  .= " '".$array["surname"]. "',";
                    $query  .= " '".$array["cellphone"]. "',";
                    $query  .= " '".$array["email"]. "',";
                    $query  .= " '".$array["address"]. "',"; 
                    $query  .= " '".$array["team"]. "',";  
                    $query  .= " '".$array["username"]. "',";     
                    $query  .= " '".$password. "',";
                    $query  .= " '".$array["store"]. "'";
                    $query  .= ") ";
                    $result = mysqli_query($GLOBALS["link"],$query);     
                   if($result) {
                        //Add to system users
                        $query  = "INSERT INTO `system_users` (date_added,date_updated,username,password,email,first,last,branch_id,super_admin,active,store_id) VALUES (";
                        $query  .= "NOW(),";
                        $query  .= "NOW(),";
                        $query  .= " '".$array["username"]. "',";  
                        $query  .= " '".$password. "',";     
                        $query  .= " '".$array["email"]. "',";
                        $query  .= " '".$array["name"]. "',";
                        $query  .= " '".$array["surname"]. "',";  
                        $query  .= " '".$this->getBranchID($array["store"]). "',";              
                        $query  .= " '0',"; 
                        $query  .= " '1',"; 
                        $query  .= " '".$array["store"]. "'";
                        $query  .= ") ";

                        if($this->getBranchID($array["store"]) !="0")$result = mysqli_query($GLOBALS["link"],$query);


                        //require_once(dirname(__FILE__)."/../../inc/class.phpmailer.php");
                        //Sends user their credentials
                        //Sends email to the respective user
                        $mail = new PHPMailer(); 
                        $mail->Mailer = 'smtp';

                        $mail->setFrom("noreply@clientassist.co.za","Theo Developer");
                        $mail->Subject = "Ellies User credentials:";

                        $fullname = $array["name"]." ".$array["surname"];

                        //message
                        $emailmessage = '
                        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                        <html xmlns="http://www.w3.org/1999/xhtml">
                        <head>
                            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                            <title>Ellies User credentials</title>
                        </head>
                        <body>
                            <p>Hi '.$fullname.',</p><br/>
                            <p>Your Ellies account has been created.</p><br/>
                            <p>Please keep the details below safe and secure.</p><br/>
                            <p>Username:'.$array["username"].'</p><br/>
                            <p>Email:'.$array["email"].'</p><br/>
                            <p>Password: '.$userpassword.'</p><br/>
                        </html>';

                        //Set who the message is to be sent to
                        $mail->addAddress($array["email"],"Ellies Member");

                        //convert HTML into a basic plain-text alternative body
                        $mail->msgHTML($emailmessage); 

                        if($this->getBranchID($array["store"]) !="0"){
                            // Mail it
                             if($mail->send()){
                                print "jQuery(\"#ok-message\").html(\"Team Member Profile saved...\").show(0).delay(4000).hide(0);\n";
                                logAction("Added Team Member Profile:".$array["name"]);
                            }else{
                                print "jQuery(\"#error-message\").html(\"Unable to save Team Member Profile Inforamtion! Please try again...\").show(0).delay(4000).hide(0);\n";
                            }
                        }else{
                            print "jQuery(\"#error-message\").html(\"Unable to save Team Member Profile Inforamtion! Please try again...\").show(0).delay(4000).hide(0);\n";
                        }
                    } else {
                        print "jQuery(\"#error-message\").html(\"Unable to save Team Member Profile Inforamtion! Please try again...\").show(0).delay(4000).hide(0);\n";
                    } 
                }else{
                    print $error;
                }  
                exit();
            }else{
                print "You do not have permission to perform this action.";
                exit();            
            }
        }

        public function getTeamMembersInfo(){
            $teamID = (int)isset($_POST["teamID"])?$_POST["teamID"]:0;

            if($GLOBALS['system_user']->hasPermission('manage_team_member_profile')) {
                $query  = "SELECT `id`,`name`, `surname`";
                $query .= " FROM `team_member_profiles`";
                $query .= " WHERE `team` =". $teamID;
                $query .= " ORDER BY `team_member_profiles`.id";                    
                $result = mysqli_query($GLOBALS["link"],$query);
                $array = array();
                while ($arr = mysqli_fetch_assoc($result)){
                    $array[] = $arr; 
                }
                print json_encode($array);
             } else {
                 print "jQuery(\"#error-message\").html(\"Unable to get Team Members information! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
             }
        }

        function getBranchID($Store=0){
            $sql = "SELECT `branch_id` FROM `stores` where id='".$Store."'";
            $sqlres = mysqli_query($GLOBALS["link"],$sql);
            $row = mysqli_fetch_assoc($sqlres);
            return $row['branch_id'];

        }

        public function getTeamMembersInfoEdit(){
            $memberID = (int)isset($_POST["memberID"])?$_POST["memberID"]:0;
            $query  = "SELECT `name`, `surname`, `cellphone`, `email`, `address`, `team`";
            $query .= " FROM `team_member_profiles`";
            $query .= " WHERE `team_member_profiles`.id= ".$memberID;

            if($GLOBALS['system_user']->hasPermission('manage_team_member_profile')) {                    
                $result = mysqli_query($GLOBALS["link"],$query);
                $arr = mysqli_fetch_assoc($result);
                print json_encode($arr);
             } else {
                 print "jQuery(\"#error-message\").html(\"Unable to get Team Members information! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
             } 
        }

        public function delete_profile ($array) {
            $member = $array['member'];
            if ($GLOBALS['system_user']->hasPermission('manage_teams')) {
                $sql = "UPDATE team_member_profiles SET";
                $sql .= " `last_updated` =NOW(),";
                $sql .= " `deleted` = '1'";
                $sql .= " WHERE id=".$member." LIMIT 1";
                $result = mysqli_query($GLOBALS["link"],$sql);
               
                if($result) {
                    print "jQuery(\"#ok-message2\").html(\"Team Member Profile deleted...  \").show(0).delay(4000).hide(0);\n";
                    logAction("Deleted Team:$title");
                } else {
                   print "jQuery(\"#error-message2\").html(\"Unable to delete Team Member Profile Inforamtion! Please try again...\").show(0).delay(4000).hide(0);\n";
                }  
                exit();
            }else{
                print "You do not have permission to perform this action.";
                exit();            
            }
        }

        public function view_team_profiles ($array) {
            $selected = "";
            if (!empty($array["Team"])) {
                $selected = $array["Team"];
            }

            print "<div class='classy_table'>";
            print "&nbsp;&nbsp;&nbsp;<span style='font-size:14px;font-weight:bold;'>View Team Member Profiles</span>";
            print "</div>";
            print "<br/><br/>";
            print "<label style='font-size:12px;font-weight:bold;'>Select Team:</label>";
            print "&nbsp;&nbsp;&nbsp;<select onchange='ChangeTeamSelectionProfiles(this)'>";
            print "<option value='' selected='selected'>[Please select]</option>";
            $table="teams";
                $sysuser = new userType($_SESSION["userid"]);
                $sql = "SELECT * FROM $table WHERE channel=".$GLOBALS['system_user']->retailChannel." and deleted='0' ORDER BY `id`";
                if ($sysuser->isSuperAdmin){
                    $sql = "SELECT * FROM $table WHERE deleted='0' ORDER BY `id`";
                }

                $sqlres = mysqli_query($GLOBALS["link"],$sql);
                while($row = mysqli_fetch_assoc($sqlres)) {
                    if ($selected == $row["title"]) {
                        print "<option id='".$row["id"]."' value='".$row["id"]."' selected>".$row["title"]."</option>";
                    }else{
                        print "<option  id='".$row["id"]."' value='".$row["id"]."'>".$row["title"]."</option>";                
                    }
                }
                print "</select>";
            print "<br/><br/><br/>";
            print '
                    <head>
                        <script type="text/javascript" language="javascript" class="init">
                        $(document).ready(function() {
                            $("#products").dataTable( {
                                columnDefs: [ {
                                    targets: [ 0 ],
                                    orderData: [ 0, 1 ]
                                }, {
                                    targets: [ 1 ],
                                    orderData: [ 1, 0 ]
                                }, {
                                    targets: [ 4 ],
                                    orderData: [ 4, 0 ]
                                } ],
                                "order": [[ 0, "asc" ]]
                            } );
                        } );
                        </script>
                    </head>
                    <body>
                        <table id="products" class="display" cellspacing="3" width="100%">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Surname</th>
                                    <th>Cellphone To</th>
                                    <th>Email</th>
                                    <th>Address</th>
                                    <th>Team</th>
                                </tr>
                            </thead>
                            <tbody>';
                        $sql = "SELECT * FROM team_member_profiles WHERE team=".mysqli_real_escape_string($GLOBALS["link"],$selected)." ORDER BY `id`";
                        $sqlres = mysqli_query($GLOBALS["link"],$sql);
                        while($row = mysqli_fetch_assoc($sqlres)) {
                            print "<tr>";
                            print "<td align='center'>".$row["name"]."</td>";
                            print "<td align='center'>".$row["surname"]."</td>";
                            print "<td align='center'>".$row["cellphone"]."</td>";
                            print "<td align='center'>".$row["email"]."</td>";
                            print "<td align='center'>".$row["address"]."</td>";
                            $team =$this->getTeamName($row["team"]);
                            print "<td align='center'>".$team."</td>";
                            print "</tr>";
                        }
                print '</tbody>
                    </table>
                </body>';
        }

    }
?>