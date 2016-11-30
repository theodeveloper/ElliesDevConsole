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
    register_menu("groups", "parentMenu", "Groups");
    
    register_permission("Group Permissions", "groups_add_remove_members",     "Add/Remove group members");
    register_permission("Group Permissions", "groups_create_new_group",       "Create a new group");
    register_permission("Group Permissions", "groups_delete_groups",          "Delete groups");
    register_permission("Group Permissions", "groups_edit_group_info",        "Edit existing group info");
    register_permission("Group Permissions", "groups_edit_group_permissions", "Edit group permissions");
    register_permission("Group Permissions", "groups",                        "Access to Groups module");
    
    class Groups {
        
        private $items_per_page;
        
        function __construct() {
            $this->items_per_page = Settings::getSetting(2);
        }
        
        function main () {
            $this->printGroupList(array("page" => 1));
        }
        
        function printGroupList($array) {
            
            $can_create_groups = $GLOBALS['system_user']->hasPermission("groups_create_new_group");
            $can_delete_groups = $GLOBALS['system_user']->hasPermission("groups_delete_groups");
            
            $page         = isset($array['page']) && intval($array['page']) > 0 ? intval($array['page']) : 1;
            $searchString = isset($array['search']) ? urldecode($array['search']) : "";
            
            $query  = "SELECT SQL_CALC_FOUND_ROWS `id`, `name`, (SELECT COUNT(`id`) FROM `user_groups` WHERE `user_groups`.`group_id` = `groups`.`id`) AS members, `description`";
            $query .= " FROM `groups`";
            $query .= " WHERE `deleted` = 0";
            if (!empty($searchString)) {
                $q = mysqli_real_escape_string($GLOBALS["link"],$searchString);
                $query .= " AND (`name` LIKE '%$q%' OR `description` LIKE '%$q%')";
            }
            $query .= " ORDER BY `name`";
            $query .= " LIMIT " . ( $this->items_per_page * ($page - 1) ) . ", " . $this->items_per_page;
            
            $result  = mysqli_query($GLOBALS["link"],$query);
            $result2 = mysqli_query($GLOBALS["link"],"SELECT FOUND_ROWS()");
            $farr    = mysqli_fetch_row($result2);
            $total_items = $farr[0];
            
            $no_of_pages = ceil($total_items/$this->items_per_page);

            
            print "<input type='hidden' id='page-number' value='$page' />";
            print "<div id='groups-container' style='width: 100%;'>";// style='padding-left: 5%; padding-right: 5%;'>";


            print "<div class='classy_table'>";
            print "&nbsp;&nbsp;&nbsp;<span style='font-size:14px;font-weight:bold;'>";
            if($can_create_groups) {
                print "<a id='create-group' style='font-weight: 900; font-size: 13px;'>Create a new group</a><br/><br/>&nbsp;&nbsp;";
            }
            print "<label style='float: right;''>Groups are a collection of permissions used to simplify the management of system permissions and users</label>";
            //print "<br/><br/>";

            if($can_delete_groups) {
               print "<button disabled='disabled' class='ibutton delete-groups' title='This will remove all group permission from all group members' style='width: 140px;'>Delete groups</button>";
               print"<br/><br/>";
               print "<span id='ok-message'></span>";
               print "<span id='error-message'></span>";
            }
            print "</span>";
            print "</div>";
            print "<br/><br/>";

            print "<label style='text-align: left; border: none; padding-left: 10px; font-weight:bold;font-style: italic; font-size: 12px;'>".$total_items ." System groups</label>";
            print "<br/><br/>";

            print '
                <head>
                    <style>
                       .group_color a:link {
                            color: green;
                            text-decoration: none;
                        }
                        a:hover {
                            color: red;
                            background-color: transparent;
                            text-decoration: underline;
                        }
                    </style>
                    <script type="text/javascript" language="javascript" class="init">
                    $(document).ready(function() {
                        $("#users-table").dataTable( {
                            columnDefs: [  {
                                targets: [ 1 ],
                                orderData: [ 1, 0 ]
                            }, {
                                targets: [ 2 ],
                                orderData: [ 2, 0 ]
                            } ],
                            "order": [[ 1, "asc" ]]
                        } );
                    } );
                    </script>
                </head>
                <body>
                    <table id="users-table" class="display" cellspacing="0" cellpadding="0" width="100%">
                        <thead>
                            <tr>
                                <th class="tablecount" colspan="3" style="text-align: left; border: none; padding-left: 10px; font-style: italic; font-size: 11px;"></th>
                            </tr>
                            <tr>
                                <th width="8%">Select All<input type="checkbox" id="check-all-groups"/></th>
                                <th>Name</th>
                                <th>Members To</th>
                                <th>Description</th>            
                            </tr>
                        </thead>
                        <tbody>';
                
                    if($total_items == 0 && empty($searchString)) {
                        print "<tr><td colspan='4' style='text-align: center; font-style: italic;'>No groups have been created</td></tr>";
                    } else if($total_items == 0) {
                        print "<tr><td colspan='4' style='text-align: center; font-style: italic;'>No groups found</td></tr>";
                    } else;
                    
                    while ($arr = mysqli_fetch_assoc($result)) {
                        print "<tr class='user-tr' id='user-tr-".$arr['id']."'>";
                            print "<td ><input type='checkbox' value='".$arr['id']."' id='group-checkbox-".$arr['id']."' class='group-checkbox'/></td>";
                            print "<td><a><span class='groupanchor' id='groupanchor-".$arr['id']."' ><u>" . ucwords($arr['name']) . "</u></span></a></td>";
                            print "<td>".$arr['members']."</td>";
                            print "<td style='font-style: italic;'>" . $arr['description'] . "</td>";
                        print "</tr>";
                    }
            print '</tbody>
                   <tfoot>
                        <tr>
                            <th colspan="4">&nbsp;</th>
                        </tr>';
                        if($can_delete_groups) {
                            print "<tr>";
                                print "<td colspan='4' style='border-top: 1px solid #DDDDDD'>";
                                    print "<br/>&nbsp;&nbsp;<button disabled='disabled' class='ibutton delete-groups' title='This will remove all group permission from all group members' style='width: 140px;'>Delete groups</button>";
                                print "</td>";
                            print "</tr>";
                        }
            print '</tfoot>
                    </table>
                </body>';
                    
            
            print "\n<!-- scripts , code below be eval()ed by javascript -->\n";
            
        }
        
        function deleteGroups ($array) {
            
            if (!$GLOBALS['system_user']->hasPermission("groups_delete_groups")) {
                print "jQuery(\"#error-message\").html(\"Cannot complete action! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
                return;
            }
            
            $groups = $array['groups'];
            
            $groups = preg_split("/[\s\|]+/", $groups, NULL, PREG_SPLIT_NO_EMPTY);
            $group_no = count($groups);
            $s = $group_no > 1 ? "s" : "";
            $query1 = "UPDATE `groups` SET `deleted` = 1 WHERE";
            $query2 = "DELETE FROM `user_groups` WHERE";
            foreach($groups as $g) {
                $query1 .= " `id` = " . mysqli_real_escape_string($GLOBALS["link"],$g) . " OR";
                $query2 .= " `group_id` = " . mysqli_real_escape_string($GLOBALS["link"],$g) . " OR";
            }
            $query1 = substr($query1, 0, -3);
            $query2 = substr($query2, 0, -3);
            $result1 = mysqli_query($GLOBALS["link"],$query1);
            $result2 = mysqli_query($GLOBALS["link"],$query2);
            if($result1 && $result2){
                print "jQuery(\"#ok-message\").html(\"$group_no group$s successfully deleted\").show(0).delay(4000).hide(0);\n";
                logAction("Deleted $group_no group$s");
            } else {
                print "jQuery(\"#error-message\").html(\"Unable to delete $group_no group$s! Please try again...\").show(0).delay(4000).hide(0);\n";
            }
            print "AJAXCallModule('Groups','printGroupList','page=' + jQuery('#page-number').val());\n";
            //print "jQuery(\"#groups\").trigger(\"click\");\n";
        }
        
        function editCreateGroup ($array) {
            
            $can_edit_group_info        = $GLOBALS['system_user']->hasPermission("groups_edit_group_info");
            $can_delete_groups          = $GLOBALS['system_user']->hasPermission("groups_delete_groups");
            $can_add_remove_members     = $GLOBALS['system_user']->hasPermission("groups_add_remove_members");
            $can_create_groups          = $GLOBALS['system_user']->hasPermission("groups_create_new_group");
            $can_edit_group_permissions = $GLOBALS['system_user']->hasPermission("groups_edit_group_permissions");
            
            $edit = intval($array['edit']);
            $groupid = $edit == 1 ? intval($array['groupid']) : 0;
            $groupname = "";
            $groupdescription = "";
            if($edit == 1) {
                $query = "SELECT `name`, `description` FROM `groups` WHERE `id` = " . mysqli_real_escape_string($GLOBALS["link"],$groupid);
                $result = mysqli_query($GLOBALS["link"],$query);
                $arr = mysqli_fetch_assoc($result);
                $groupname = $arr['name'];
                $groupdescription = $arr['description'];
            }
            
            print "<input type='hidden' id='groupid' value='$groupid' />";
            print "<div id='groups-container' style='width: 100%;'>";
                        print "<td valign='top'>";
                            print "<table id='third-table' cellpadding='0' cellspacing='0' border='0'>";
                                print "<tr class='same-bg'>";
                                    print "<td colspan='2'><br/>&nbsp;&nbsp;&laquo;&nbsp;<a id='back-to-groups'>Back to Groups<br/><br/></td>";
                                print "</tr>";
                                print "<tr class='same-bg'>";
                                    print "<td colspan='1' style='font-weight: 900;'>";
                                        print "<span style='padding-left: 20px; font-weight: 900; font-size: 11px;'>Group name</span>";
                                        print "<br/>";
                                        $can_edit = $can_edit_group_info ? "" : "disabled='disabled'";
                                        print "<span style='padding-left: 20px;'><input type='text' $can_edit id='group-name' value='" . htmlentities($groupname, ENT_QUOTES) . "' maxlength='19' style='width:250px;' /></span>";
                                    print "</td>";
                                    print "<td colspan='1' align='left'>";
                                        //print "&nbsp;";
                                        print "<span id='ok-message'></span>";
                                        print "<span id=error-message></span>";
                                    print "<td>";
                                print "</tr>";
                                print "<tr class='same-bg'>";
                                    print "<td style='font-weight: 900; width: 50%;'>";
                                        print "<br/>";
                                        print "<span style='padding-left: 20px; font-weight: 900; font-size: 11px;'>Group description</span>";
                                        print "<br/>";
                                        print "<span style='padding-left: 20px;'><textarea $can_edit id='group-description' maxlength='254' style='width: 250px; height: 50px; resize: none; overflow: auto;'>" . htmlentities($groupdescription, ENT_QUOTES) . "</textarea></span>";
                                    print "</td>";
                                    print "<td valign='bottom' style='text-align: left;'>";
                                            print $edit == 1 && $can_delete_groups? "<button id='delete-this-group' class='ibutton' style='width: 105px;'>Delete group</button>":"<button id='cancel-group' class='ibutton' style='width: 105px;'>Cancel</button>";
                                            print "<br/>&nbsp;";
                                    print "</td>";
                                print "</tr>";
                                print "<tr>";
                                    print "<td colspan='2'>";
                                        print "<div id='tabs'>";
                                            print "<ul>";
                                                print "<li><a href='#members-tab'>Members</a></li>";
                                                if($edit == 1) {
                                                    print "<li><a href='#gpermissions-tab'>Permissions</a></li>";
                                                }
                                            print "</ul>";
                                            print "<div id='members-tab'>";
                                                print "<table cellpadding='0' cellspacing='0' border='0' style='width: 80%;'>";
                                                    print "<tr>";
                                                        print "<td colspan='2'>&nbsp;</td>";
                                                        print "<td>";
                                                            print "<select id='all-branch-group'>";
                                                                print "<option value='0' selected='selected'>[Users in all branches]</option>";
                                                                $query  = "SELECT `id`, `branch` FROM `branches` WHERE `deleted` = 0 ORDER BY `branch`";
                                                                $result = mysqli_query($GLOBALS["link"],$query);
                                                                while ($arr = mysqli_fetch_assoc($result)) {
                                                                    print "<option value='" . $arr['id'] . "'>" . $arr['branch'] . "</option>";
                                                                }
                                                            print "</select>";
                                                            print "<br/><br/>";
                                                        print "</td>";
                                                    print "</tr>";
                                                    print "<tr>";
                                                        print "<td style='text-align: right;' width='41%'>";
                                                            print "<select id='user-branch-group-left' multiple='multiple' size='10' style='width: 240px; border: 1px solid #888888;'>";
                                                                if($edit == 1) {
                                                                    $query  = "SELECT `system_users`.`id`, `system_users`.`first`, `system_users`.`last`, `branches`.`branch`";
                                                                    $query .= " FROM `system_users`, `branches`";
                                                                    $query .= " WHERE `system_users`.`branch_id` = `branches`.`id`";
                                                                    $query .= " AND `system_users`.`active` = 1";
                                                                    $query .= " AND `system_users`.`super_admin` = 0";
                                                                    $query .= " AND `branches`.`deleted` = 0";
                                                                    $query .= " AND `system_users`.`id` IN (SELECT `user_id` FROM `user_groups` WHERE `group_id` = " . mysqli_real_escape_string($GLOBALS["link"],$groupid) . ")";
                                                                    $query .= " ORDER BY `system_users`.`first`";
                                                                    $result = mysqli_query($GLOBALS["link"],$query);
                                                                    while ($arr = mysqli_fetch_assoc($result)) {
                                                                        print "<option title='" . $arr['first'] . " " . $arr['last'] . " [" . $arr['branch'] . "]" . "' value='" . $arr['id'] . "'>" . $arr['first'] . " " . $arr['last'] . " [" . $arr['branch'] . "]" .( $GLOBALS['system_user']->id == $arr['id'] ? "*" : "" ). "</option>";
                                                                    }
                                                                }
                                                            print "</select>";
                                                        print "</td>";
                                                        print "<td style='text-align: center;' width='8%'>";
                                                            $can_move = $can_add_remove_members ? "" : "disabled='disabled'";
                                                            print "<button id='moveleft' $can_move title='Add to group' class='ibutton'>&laquo;</button><br/>";
                                                            print "<button id='moveright' $can_move title='Remove from group' class='ibutton'>&raquo;</button>";
                                                        print "</td>";
                                                        print "<td width='41%'>";
                                                            print "<select id='user-branch-group-right' multiple='multiple' size='10' style='width: 240px; border: 1px solid #888888;'>";
                                                                $query  = "SELECT `system_users`.`id`, `system_users`.`first`, `system_users`.`last`, `branches`.`branch`";
                                                                $query .= " FROM `system_users`, `branches`";
                                                                $query .= " WHERE `system_users`.`branch_id` = `branches`.`id`";
                                                                $query .= " AND `system_users`.`active` = 1";
                                                                $query .= " AND `system_users`.`super_admin` = 0";
                                                                if($edit == 1) {
                                                                    $query .= " AND `system_users`.`id` NOT IN (SELECT `user_id` FROM `user_groups` WHERE `group_id` = " . mysqli_real_escape_string($GLOBALS["link"],$groupid) . ")";
                                                                }
                                                                $query .= " AND `branches`.`deleted` = 0";
                                                                $query .= " ORDER BY `system_users`.`first`";
                                                                $result = mysqli_query($GLOBALS["link"],$query);
                                                                while ($arr = mysqli_fetch_assoc($result)) {
                                                                    print "<option title='" . $arr['first'] . " " . $arr['last'] . " [" . $arr['branch'] . "]" . "' value='" . $arr['id'] . "'>" . $arr['first'] . " " . $arr['last'] . " [" . $arr['branch'] . "]" .( $GLOBALS['system_user']->id == $arr['id'] ? "*" : "" ). "</option>";
                                                                }
                                                            print "</select>";
                                                        print "</td>";
                                                    print "</tr>";
                                                    print "<tr>";
                                                        print "<td colspan='1' style='text-align: right;'>";
                                                            print "<br/>";
                                                            $can_save = $can_create_groups ? "" : "disabled='disabled'";
                                                            print $edit == 1 ? "<button class='ibutton' $can_move id='save-group-button'>Save group info</button>":"<button class='ibutton' $can_save id='save-group-button'>Create group</button>";
                                                        print "</td>";
                                                        print "<td colspan='2'>&nbsp;</td>";
                                                    print "</tr>";
                                                print "</table>";
                                            print "</div>";
                                            if($edit == 1) {
                                                print "<div id='gpermissions-tab'>";
                                                $edit_permissions   = $can_edit_group_permissions ? "" : "disabled='disabled'";
                                                $groupPermissions   = $this->groupPermissions($groupid);
                                                $system_permissions = $GLOBALS['registered_permissions'];
                                                $other_permissions  = isset($system_permissions['']) ? $system_permissions[''] : NULL;
                                                if (isset($system_permissions[''])) {
                                                    unset($system_permissions['']);
                                                }
                                                print "<div style='height: 370px; overflow: auto; border-top: 1px solid #999999; border-bottom: 1px solid #999999;'>";
                                                ksort($system_permissions);
                                                if(!empty($other_permissions))
                                                    $system_permissions['Other Permissions'] = $other_permissions;
                                                //print_r($GLOBALS['registered_permissions']);
                                                foreach ($system_permissions as $key=>$value) {
                                                    print "<table class='group-tables' cellspacing='0' cellpadding='0' border='0' width='60%'>";
                                                        print "<thead>";
                                                            print "<tr>";
                                                                print "<th width='5%'>";
                                                                        print "<input type='checkbox' $edit_permissions class='group-permission-main-checkbox' />";
                                                                print "</th>";
                                                                print "<th width='5%'>&nbsp;</th>";
                                                                print "<th>";
                                                                    print ucwords($key);
                                                                print "</th>";
                                                            print "</tr>";
                                                        print "</thead>";
                                                        print "<tbody>";
                                                            foreach ($value as $v) {
                                                                $checked = in_array($v['acl_name'], $groupPermissions) ? " checked='checked'" : "";
                                                                print "<tr>";
                                                                    print "<td>&nbsp;</td>";
                                                                    print "<td colspan='1'><input type='checkbox'$checked $edit_permissions class='group-permission-sub-checkbox' value='".$v['acl_name']."' /></td>";
                                                                    print "<td>&nbsp;" . $v['description'] . "</td>";
                                                                print "</tr>";
                                                            }
                                                        print "</tbody>";
                                                    print "</table>";
                                                }
                                                //print "</table>";
                                                print "</div>";
                                                print "<br/>&nbsp;&nbsp;<button class='ibutton' $edit_permissions id='save-group-permissions-button'>Save group permissions</button>";
                                                print "</div>";
                                            }
                                        print "</div>";
                                    print "</td>";
                                print "</tr>";
                            print "</table>";
                        print "</td>";
                print "</tr>";
                print "</table>";
            print "</div>";
            
            print "\n<!-- scripts , code below be eval()ed by javascript -->\n";
            print "jQuery('#group-name').focus();\n";
            print "jQuery(\"#tabs\").tabs({ selected: 0 });";
            if (!empty($array['message'])) {
                print "jQuery(\"#ok-message\").html(\"" . $array['message'] . "\").show(0).delay(4000).hide(0);\n";
            }
        }
        
        function filterGroupBranch ($array) {
            $branchid = intval($array['branchid']);
            $groupid = intval($array['groupid']);
            $edit = intval($array['edit']);
            
            
            
            $query  = "SELECT `system_users`.`id`, `system_users`.`first`, `system_users`.`last`, `branches`.`branch`";
            $query .= " FROM `system_users`, `branches`";
            $query .= " WHERE `system_users`.`branch_id` = `branches`.`id`";
            $query .= " AND `system_users`.`active` = 1";
            $query .= " AND `system_users`.`super_admin` = 0";
            if($edit == 1) {
                $query .= " AND `system_users`.`id` NOT IN (SELECT `user_id` FROM `user_groups` WHERE `group_id` = " . mysqli_real_escape_string($GLOBALS["link"],$groupid) . ")";
            }
            if($branchid > 0) {
                $query .= " AND `branches`.`id` = " . mysqli_real_escape_string($GLOBALS["link"],$branchid);
            }
            $query .= " AND `branches`.`deleted` = 0";
            $query .= " ORDER BY `system_users`.`first`";
            
            $result = mysqli_query($GLOBALS["link"],$query);
            
            $html = "";
            while ($arr = mysqli_fetch_assoc($result)) {
                $html .= "<option title='" . $arr['first'] . " " . $arr['last'] . " [" . $arr['branch'] . "]" . "' value='" . $arr['id'] . "'>" . $arr['first'] . " " . $arr['last'] . " [" . $arr['branch'] . "]" . "</option>";
            }
            
            print "jQuery(\"#user-branch-group-right\").html(\"$html\");\n";
        }
        
        function saveGroup ($array) {
            $groupid          = intval($array['groupid']);
            $groupname        = urldecode($array['groupname']);
            $groupdescription = urldecode($array['groupdescription']);
            $members          = preg_split("/[\s\|]+/", $array['members'], NULL, PREG_SPLIT_NO_EMPTY);
            $edit             = intval($array['edit']);
            
            if ($edit == 1) {
                
                if (!$GLOBALS['system_user']->hasPermission("groups_edit_group_info") && !$GLOBALS['system_user']->hasPermission("groups_add_remove_members")) {
                    print "jQuery(\"#error-message\").html(\"Cannot complete action! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
                    return;
                }
                
                $query1 = "UPDATE `groups` SET `name` = \"".mysqli_real_escape_string($GLOBALS["link"],$groupname)."\", `description` = \"".mysqli_real_escape_string($GLOBALS["link"],$groupdescription)."\" WHERE `id` = " . mysqli_real_escape_string($GLOBALS["link"],$groupid);
                $result0 = mysqli_query($GLOBALS["link"],$query1);
                $result1 = mysqli_query($GLOBALS["link"],"DELETE FROM `user_groups` WHERE `group_id` = " . mysqli_real_escape_string($GLOBALS["link"],$groupid));
                $result1 = $result0 && $result1;
                logAction("Edited group groups.id[$groupid]");
            } else {
                
                if (!$GLOBALS['system_user']->hasPermission("groups_create_new_group") && !$GLOBALS['system_user']->hasPermission("groups_add_remove_members")) {
                    print "jQuery(\"#error-message\").html(\"Cannot complete action! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
                    return;
                }
                
                $query1 = "INSERT INTO `groups` (`name`, `description`, `deleted`) VALUES (\"".mysqli_real_escape_string($GLOBALS["link"],$groupname)."\",\"".mysqli_real_escape_string($GLOBALS["link"],$groupdescription)."\",\"0\")";
                $result1 = mysqli_query($GLOBALS["link"],$query1);
                $groupid = mysql_insert_id();
                logAction("Added new group groups.id[$groupid]");
            }
            
            $query2 = "INSERT INTO `user_groups` (`user_id`, `group_id`) VALUES";
            $result2 = TRUE;
            $hasMembers = FALSE;
            foreach ($members as $m) {
                $query2 .= "(\"" . mysqli_real_escape_string($GLOBALS["link"],$m) . "\",\"" . mysqli_real_escape_string($GLOBALS["link"],$groupid) . "\"),";
                $hasMembers = TRUE;
            }
            $query2 = substr($query2, 0, -1);
            if ($hasMembers) {
                $result2 = mysqli_query($GLOBALS["link"],$query2);
            }
            
            if ($edit == 1) {
                if ($result1 && $result2){
                    print "jQuery(\"#ok-message\").html(\"Changes to group successfully saved...\").show(0).delay(4000).hide(0);\n";
                } else {
                    print "jQuery(\"#error-message\").html(\"Unable to save changes to group! Please try again...\").show(0).delay(4000).hide(0);\n";
                }
            } else {
                if ($result1 && $result2){
                    print "AJAXCallModule(\"Groups\",\"editCreateGroup\", \"edit=1&groupid=$groupid&message=Group ".strtoupper($groupname)." succesfully created\");\n";
                } else {
                    print "jQuery(\"#error-message\").html(\"Failed to create new group! Please try again...\").show(0).delay(4000).hide(0);\n";
                }
            }
        }
        
        function saveGroupPermissions ($array) {
            
            if (!$GLOBALS['system_user']->hasPermission("groups_edit_group_permissions")) {
                print "jQuery(\"#error-message\").html(\"Cannot complete action! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
                return;
            }
            
            $groupid = $array['groupid'];
            $permissions = preg_split("/[\s\|]+/", $array['permissions'], NULL, PREG_SPLIT_NO_EMPTY);
            
            $query1 = "DELETE FROM `group_permissions` WHERE `group_id` = " . mysqli_real_escape_string($GLOBALS["link"],$groupid);
            $query2 = "INSERT INTO `group_permissions` (`group_id`, `permission`) VALUES ";
            
            foreach ($permissions as $p) {
                $query2 .= "(\"" . mysqli_real_escape_string($GLOBALS["link"],$groupid) . "\", \"" . mysqli_real_escape_string($GLOBALS["link"],$p) . "\"),";
            }
            $query2 = substr($query2, 0, -1);
            $result1 = mysqli_query($GLOBALS["link"],$query1);
            $result2 = mysqli_query($GLOBALS["link"],$query2);
            if ($result1 && $result2) {
                print "jQuery(\"#ok-message\").html(\"Group permissions saved...\").show(0).delay(4000).hide(0);\n";
            } else {
                print "jQuery(\"#error-message\").html(\"Failed to save group permissions! Please try again...\").show(0).delay(4000).hide(0);\n";
            }
            logAction("Updated group permissions groups.id[$groupid]");
        }
        
        function groupPermissions ($groupid) {
            $permissions = array();
            $query  = "SELECT `permission`";
            $query .= " FROM `group_permissions`";
            $query .= " WHERE `group_id` = " . mysqli_real_escape_string($GLOBALS["link"],$groupid);
            $result = mysqli_query($GLOBALS["link"],$query);
            while ($arr = mysqli_fetch_assoc($result)) {
                $permissions[] = $arr['permission'];
            }
            return $permissions;
        }
        
        function trimBranchName ($branchName, $maxSizeBeforeTrim = 22) {
            return strlen($branchName) > $maxSizeBeforeTrim ? substr($branchName, 0, $maxSizeBeforeTrim - 3) . "..." : $branchName;
        }
        
        private function printPaging ($current, $total_pages, $module, $function, $search, $index_limit = 3) {
            $start = max($current - intval($index_limit / 2), 1);
            $end = $start + $index_limit - 1;
            //$search = "encodeURIComponent('fish')";
            print "<div class='paging'>";
                if($current == 1) {
                    print "<span class='prn'>&laquo;&nbsp;Previous</span>";
                }
                else {
                    $i = $current - 1;
                    print "<a class='prn' title='go to page ".$i."' rel='nofollow' onclick=AJAXCallModule('$module','$function','page=$i&search=$search');>&laquo;&nbsp;Previous</a>";
                    print "<span class='prn'>...</span>";
                }
                
                if($start > 1) {
                    $i = 1;
                    print "<a title='go to page ".$i."' onclick=AJAXCallModule('$module','$function','page=$i&search=$search');>".$i."</a>";
                }
                
                for($i = $start; $i <= $end && $i <= $total_pages; $i++){
                    print $i == $current ? "<span>".$i."</span>" : "<a title='go to page ".$i."' onclick=AJAXCallModule('$module','$function','page=$i&search=$search');>".$i."</a>";
                }
                
                if($total_pages > $end){
                    $i = $total_pages;
                    print "<span class='prn'>...</span>";
                    print "<a title='go to page ".$i."' onclick=AJAXCallModule('$module','$function','page=$i&search=$search');>Last</a>";
                }
                
                if($current < $total_pages){
                    $i = $current+1;
                    print "<a class='prn' title='go to page ".$i."' rel='nofollow' onclick=AJAXCallModule('$module','$function','page=$i&search=$search');>Next&nbsp;&raquo;</a>";
                }
                else{
                    print "<span class='prn'>Next&nbsp;&raquo;</span>";
                }
            print "</div>";
        }
    }
?>