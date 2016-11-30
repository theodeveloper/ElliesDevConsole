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

    $newLink = new mysqli(MYSQL_SERVER, MYSQL_USERNAME, MYSQL_PASSWORD,MYSQL_DATABASE);
    if($newLink->connect_errno){
        print "DB connection error.";
        exit(-1);
    }

    $GLOBALS["link"]=$newLink;

    function register_menu($module, $menu_type, $menu_parenttitle, $menu_subitems = NULL, $menu_permission_acl = "") {
        global $registered_menus;
        try {
            $registered_menus[] = Array (
                                            "module"      => $module,
                                            "type"        => $menu_type,
                                            "parenttitle" => $menu_parenttitle,
                                            "subitems"    => $menu_subitems
                                        );
            return TRUE;
        } catch (Exception $e) {
            return FALSE;
        }
    }
    
    function register_permission($permission_group, $acl_name, $description) {
        global $registered_permissions;
        
        $registered_permissions[ $permission_group ][] = Array("acl_name" => $acl_name, "description" => $description );
    }
    
    function getUserPermissionACLs() {
        $permissions = array();
        foreach ($GLOBALS['registered_permissions'] as $rp) {
            foreach ($rp as $r) {
                if ($GLOBALS['system_user']->hasPermission($r['acl_name']))
                    $permissions[] = $r['acl_name'];
            }
        }
        return $permissions;
    }
    
    function get_session() {
        $query = "SELECT * FROM `system_sessions` WHERE username !='' AND `sessionid` = \"".mysqli_real_escape_string($GLOBALS["link"],session_id())."\"";
        $result = mysqli_query($GLOBALS["link"],$query);
        if (mysqli_num_rows($result)) {
            $GLOBALS['SESSION'] = mysqli_fetch_assoc($result);
        }
        $query = "UPDATE `system_sessions` SET `lastActivity` = NOW() WHERE `sessionid` = \"".mysqli_real_escape_string($GLOBALS["link"],session_id())."\"";
        $result = mysqli_query($GLOBALS["link"],$query);
    }
    
    function save_session() {
        $query = "INSERT INTO `system_sessions` (`sessionid`,`lastActivity`,";
        foreach ($GLOBALS['SESSION'] as $key=>$value) {
            if ($key == "sessionid") continue;
            $query .= "`".$key."`,";
        }
        $query = substr($query,0,-1).") values (\"".session_id()."\",NOW(),";
        foreach ($GLOBALS['SESSION'] as $key=>$value) {
            if ($key == "sessionid") continue;
            $query .= "\"".mysqli_real_escape_string($GLOBALS["link"],$value)."\",";
        }
        $query = substr($query,0,-1).") ON DUPLICATE KEY UPDATE `lastActivity` = NOW(),";
        foreach ($GLOBALS['SESSION'] as $key=>$value) {
            if ($key == "sessionid") continue;
            $query .= "`".$key."` = \"".mysqli_real_escape_string($GLOBALS["link"],$value)."\",";
        }
        $query = substr($query,0,-1);

        $result = mysqli_query($GLOBALS["link"],$query);
    }
    
    function delete_session() {
        $query = "DELETE FROM `system_sessions` WHERE `sessionid` = \"".mysqli_real_escape_string($GLOBALS["link"],session_id())."\"";
        $result = mysqli_query($GLOBALS["link"],$query);
        $GLOBALS['SESSION'] = NULL;
        $_SESSION['isInstaller'] = NULL;
		$_SESSION['allowDataFilter'] = NULL;

    }
    
    function register_new_system_user($username, $first, $last, $email, $md5_password, $branchid, $superadmin, $active, $storeid = 0) {
        $query  = "INSERT INTO `system_users` (`username`,`first`,`last`, `email`, `password`, `branch_id` ,`date_added`,`date_updated`,`super_admin`,`active`, `store_id`) VALUES ";
        $query .= " (\"".mysqli_real_escape_string($GLOBALS["link"],$username)."\",\"".mysqli_real_escape_string($GLOBALS["link"],$first)."\",\"".mysqli_real_escape_string($GLOBALS["link"],$last)."\",\"".mysqli_real_escape_string($GLOBALS["link"],$email)."\",\"".mysqli_real_escape_string($GLOBALS["link"],$md5_password)."\", \"".mysqli_real_escape_string($GLOBALS["link"],$branchid)."\", NOW(),NOW(),\"".mysqli_real_escape_string($GLOBALS["link"],$superadmin)."\",\"".mysqli_real_escape_string($GLOBALS["link"],$active)."\",\"".mysqli_real_escape_string($GLOBALS["link"],$storeid)."\")";
        return mysqli_query($GLOBALS["link"],$query);
    }
    
    function update_system_user_lastlogin($username) {
        $query = "UPDATE `system_users` SET `lastlogin` = NOW() WHERE `username` = \"" . mysqli_real_escape_string($GLOBALS["link"],$username) . "\"";
        mysqli_query($GLOBALS["link"],$query);
    }
    
    function add_include_path ($path) {
        foreach (func_get_args() AS $path) {
            if (!file_exists($path) OR (file_exists($path) && filetype($path) !== 'dir')) {
                trigger_error("Include path '{$path}' not exists", E_USER_WARNING);
                continue;
            }
            $paths = explode(PATH_SEPARATOR, get_include_path());
            if (array_search($path, $paths) === false)
                array_push($paths, $path);
            set_include_path(implode(PATH_SEPARATOR, $paths));
        }
    }
    
    function remove_include_path ($path) {
        foreach (func_get_args() AS $path) {
            $paths = explode(PATH_SEPARATOR, get_include_path());
            
            if (($k = array_search($path, $paths)) !== false)
                unset($paths[$k]);
            else
                continue;
            
            if (!count($paths)) {
                trigger_error("Include path '{$path}' can not be removed because it is the only", E_USER_NOTICE);
                continue;
            }
            set_include_path(implode(PATH_SEPARATOR, $paths));
        }
    }

    
    function is_authenticated() {
        return isset($GLOBALS['SESSION']['auth']) && $GLOBALS['SESSION']['auth'] == 1;
    }
    
    function sortRegisteredMenus($sortOrder = "ASC", $customArray = NULL) {
        $sortOrder = strtolower($sortOrder);
        global $registered_menus;
        $registered_menus_copy = $registered_menus;
	    $registered_menus = array();
        
        $modules = array();
        foreach ($registered_menus_copy as $r) {
            if(!in_array($r, $modules))$modules[] = $r['module'];
        }
        if(!empty($modules)) {
            if($sortOrder == "custom"){          
                $modules = $customArray;
            } else if ($sortOrder == "asc") {
                sort($modules);
            } else {
                rsort($modules);
            }
        }

        foreach ($modules as $m) {
            foreach ($registered_menus_copy as $r) {
                $count = 0;
                if (in_array($m, $r)){
                    if(in_array($r['module'], $customArray)){
                        $registered_menus[] = $r; 
                    }
                    break;
                }
                $count++;
            }
        }
    }
    
    function logAction($description_max_255chars = "", $is_installer = FALSE, $voucher_number = "") {
        $query  = "INSERT INTO `audit_log` (`dts`, `user_id`, `branch_id`, `is_installer`, `voucher_no`, `logvoucher`, `description`, `ip_address`) VALUES (";
        $query .= "NOW(), ";
        $query .= "\"" . mysqli_real_escape_string($GLOBALS["link"],$GLOBALS['system_user']->id) . "\", ";
        $query .= "\"" . mysqli_real_escape_string($GLOBALS["link"],$GLOBALS['system_user']->branchID) . "\", ";
        $query .= $is_installer ? "\"1\"," : "\"0\",";
        $query .= "\"" . mysqli_real_escape_string($GLOBALS["link"],$voucher_number) . "\", ";
        $query .= "\"" . ( empty($voucher_number) ? 0 : 1 ) . "\", ";
        $query .= "\"" . mysqli_real_escape_string($GLOBALS["link"],$description_max_255chars) . "\", ";
        $query .= "INET_ATON(\"" . mysqli_real_escape_string($GLOBALS["link"],$_SERVER['REMOTE_ADDR']) . "\")";
        $query .= ")";
        
        return mysqli_query($GLOBALS["link"],$query);
    }
    
    function idleTimeout() {
        $logoutTimeInSeconds = getSetting(3);
        if ($logoutTimeInSeconds > 0) {
            print "<div id='idletimeout'>";
            print "You will be logged out in <span><!-- countdown place holder --></span>&nbsp;seconds due to inactivity.";
            print " <a id='idletimeout-resume' href='#'>Click here to continue using this page</a>.</div>";
            print "<script type='text/javascript' language='javascript'>";
            print "
                jQuery(function(){
                    jQuery.idleTimeout('#idletimeout', '#idletimeout a', {
                    idleAfter: " . $logoutTimeInSeconds . " * 60, // minutes
                    pollingInterval: 1 * 60,    // 1 minute
                    keepAliveURL: 'keepalive.php',
                    serverResponseEquals: 'OK',
                    onTimeout: function() {
                        document.location.href='login.php?logout=1';
                    },
                    onIdle: function() {
                        jQuery(this).slideDown('slow'); // show the warning bar
                        jQuery('.overlay').show();
                    },
                    onCountdown: function( counter ) {
                        jQuery(this).find('span').html(counter); // update the counter
                    },
                    onResume: function() {
                        jQuery('.overlay').hide();
                        jQuery(this).slideUp('slow'); // hide the warning bar
                    }
                    });
                });
            ";
            print "</script>";
        }
    }
    
    function getSetting ($settingid) {
        $query  = "SELECT `value`";
        $query .= " FROM `settings`";
        $query .= " WHERE `id` = " . mysqli_real_escape_string($GLOBALS["link"],$settingid);
        $result = mysqli_query($GLOBALS["link"],$query);
        $array  = mysqli_fetch_assoc($result);
        
        return $array['value'];
    }

    function PrintDBResultz($sql, $hiddencolumns = array(""), $customcolumns = array("somecol" => ""), $extrarowfunction = "") {
        //PrintDBResults($sql, $hiddencolumns = array("id"), $customcolumns = array("id" => "printEditLink(\$row['id'])"));
        $sqlres = mysqli_query($sql);
        $icount = -1;
        print "<table border='0' cellpadding='2' cellspacing='0'>";
        while($row = mysqli_fetch_assoc($sqlres)) {
            $icount += 1;
            if ($icount == 0) {
                print "<tr>";
                foreach($row as $field=>$value) {
                    print "<th>";
                    if (!in_array($field, $hiddencolumns)) {
                        print $field;		    
                    }else{
                        print "&nbsp;";
                    }
                    print "</th>";
                }
                print "</tr>";
            }
            print "<tr>";
            foreach($row as $field=>$value) {
                print "<td>";
                if (!empty($customcolumns[$field])) {
                    //print htmlentities($customcolumns[$field]);
                    eval($customcolumns[$field].";");
                }else{
                    if (empty($value)) { $value = "&nbsp;"; }
                    print $value;
                }
                print "</td>";
            }
            print "</tr>";
        }
        if (!empty($extrarowfunction)) {
            print "<tr><td colspan='".$colspan."'>";
            eval($extrarowfunction.";");
            print "</td></tr>";
        }
        if ($icount < 0) {
            print "<tr><td>No Record(s) Found</td></tr>";
        }
        print "</table>";
        //$icount += 1;
        //print $icount." record(s) found";
    }

    //=============================================================================================================
?>