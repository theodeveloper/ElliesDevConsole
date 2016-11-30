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
    /*register_menu("errors", "submenu", "System Errors",
        Array(
            Array("title" => "Report a System Error", "location" => "report", "acl" => "report_error"),
            Array("title" => "View Reported Errors",  "location" => "view",   "acl" => "view_errors")
        )
    );*/
    
    register_permission("System Errors Permissions", "errors",        "Access to Errors module");
    register_permission("System Errors Permissions", "resolve_error", "Mark errors as resolved");
    register_permission("System Errors Permissions", "report_error",  "Report errors");
    register_permission("System Errors Permissions", "view_errors",   "View Reported errors");

    class Errors {
        
        private $items_per_page;
        
        public function __construct () {
            $this->items_per_page = Settings::getSetting(2);
        }
        
        public function report () {
            print "<div id='groups-container' style='width: 100%; height: 100%;'>";
            print "<table cellpadding='0' cellspacing='0' border='0' width='100%'>";
                print "<tr class='same-bg'>";
                    print "<td width='30%' style='padding-left: 10px;'><br/>";
                        print "<span id='create-group' style='font-weight: 900; font-size: 13px; color: #222222;'>Report a System Error</span>";
                    print "</td>";
                    print "<td width='70%' style='font-style: italic; color: #666666;'><br/>";
                        print "Please report only genuine System errors";
                    print "</td>";
                print "</tr>";
                print "<tr class='same-bg'>";
                    print "<td colspan='2'>";
                        print "<br/>&nbsp;";
                    print "</td>";
                print "</tr>";
            print "</table>";
            print "<table id='users-table' cellspacing='0' cellpadding='0' border='0' width='100%'>";
            print "<thead>";
                print "<tr>";
                    print "<th width='10%' style='text-align: right; font-weight: 900; color: #333333; padding-right: 10px; border-bottom: none;'>";
                        print "&nbsp;";
                    print "</th>";
                    print "<th colspan='4' style='font-weight: 900; color: #444444; padding-left: 10px; padding-right: 10px; border-bottom: none;'>";
                        print "<br/>Error description";
                        print "<br/><textarea id='description' maxlength='4999' style='width: 80%; height: 150px; resize: none; overflow: auto;'></textarea><br/>&nbsp;";
                    print "</th>";
                print "</tr>";
                print "<tr>";
                    print "<th style='border-bottom: none;'>&nbsp;</th>";
                    print "<th style='width: 25px; text-align: left; padding-left: 5px; border-bottom: none;'><br/>";
                        print "<input type='checkbox' id='confirm' />";
                    print "</th>";
                    print "<th style='width: 300px; border-bottom: none;'><br/>";
                        print "<label for='confirm' style='font-style: italic; font-family: arial;'>This is a genuine system error</label>";
                    print "</th>";
                    print "<th style='border-bottom: none; width: 170px;'><br/>";
                        print "<button class='ibutton' id='submit-error' disabled='disabled'>Submit Error</button>";
                    print "</th>";
                    print "<th style='border-bottom: none;'><br/>";
                        print "<span id='ok-message'></span><span id='error-message'></span>";
                    print "</th>";
                print "</tr>";
            print "</thead>";
            print "<tbody>";
                print "<tr>";
                    print "<th colspan='5' id='temp' style='text-align: left;'>&nbsp;</th>";
                print "</tr>";
            print "</tbody>";
            print "</table>";
            print "</div>";
            
            print "\n<!-- scripts , code below be eval()ed by javascript -->\n";
            
            print "
                jQuery('#description').focus();
                
                jQuery('#confirm').change(function () {
                    if (jQuery(this).is(':checked')) {
                        jQuery('#submit-error').removeAttr('disabled');
                    } else {
                        jQuery('#submit-error').attr('disabled', true);
                    }
                });
            ";
            
            print "
                jQuery('#submit-error').click(function () {
                    if(jQuery.trim(jQuery('#description').val()) == '') {
                        jQuery('#error-message').html('Please provide the error description...').show(0).delay(4000).hide(0);
                        jQuery('#description').val('').focus();
                    } else {
                        AJAXCallModuleJSOnly('" . __CLASS__ . "', 'sendError', 'description='+encodeURIComponent(jQuery('#description').val()));
                    }
                })
            ";
        }
        
        public function sendError ($array) {
            $description = urldecode($array['description']);
            
            if(!$GLOBALS['system_user']->hasPermission("installer_edit_company")) {
                print "jQuery(\"#error-message\").html(\"Cannot complete action! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
                return;
            } else {
                $query  = "INSERT INTO `system_errors` (`reporter_id`, `report_dts`, `description`, `resolved`, `deleted`) VALUES (";
                $query .= "'" . mysqli_real_escape_string($GLOBALS["link"],$GLOBALS['system_user']->id) . "',";
                $query .= "NOW(),";
                $query .= "'" . mysqli_real_escape_string($GLOBALS["link"],$description) . "',";
                $query .= "'0',";
                $query .= "'0'";
                $query .= ")";
                $this->sendErrorMail($description);
                if (mysqli_query($GLOBALS["link"],$query)) {
                    logAction("Reported a system error system_errors.id[".mysql_insert_id()."]");
                    print "jQuery('#description').val('').focus();\n";
                    print "jQuery('#submit-error').attr('disabled', true);\n";
                    print "jQuery('#confirm').removeAttr('checked');\n";
                    print "jQuery(\"#ok-message\").html(\"System error sent...\").show(0).delay(4000).hide(0);\n";
                } else {
                    print "jQuery(\"#error-message\").html(\"Failed to submit system error! Please try again...\").show(0).delay(4000).hide(0);\n";
                }
            }
        }
        
        private function sendErrorMail ($errorDescription) {
            $result       = mysqli_query($GLOBALS["link"],"SELECT `branch` FROM `branches` WHERE `id` = " . mysqli_real_escape_string($GLOBALS["link"],$GLOBALS['system_user']->branchID));
            $branch_array = mysqli_fetch_assoc($result);
            $branch_name  = $branch_array['branch'];
            $headers      = 'MIME-Version: 1.0' . "\r\n";
            $headers     .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
            $headers     .= "From: Ellies - " . SITE_TITLE . "<noreply@ellies.co.za>" . "\r\n";
            $description  = "<html>";
            $description .= "<body>";
            $description .= "Hi,<br/><br/>";
            $description .= "<b>This email was sent automatically by Ellies - " . SITE_TITLE . ":</b><br/><br/>";
            $description .= ucwords($GLOBALS['system_user']->first . " " . $GLOBALS['system_user']->last . " [$branch_name]") . " reported a system error on " . date("j M Y, H\hi") . ".<br/><br/>";
            $description .= "<span style='font-weight: 900; text-decoration: underline;'>Error Description:</span><br/>";
            $description .= htmlentities($errorDescription, ENT_QUOTES) . "<br/><br/>";
            $description .= "ttfn,<br/>";
            $description .= "&nbsp;&nbsp;&nbsp;&raquo;&nbsp;Ellies - " . SITE_TITLE;
            $description .= "</body>";
            $description .= "</html>";
            
            mail(Settings::getSetting(5), "Ellies Notification: " . SITE_TITLE . " System Error", $description, $headers);
        }
        
        public function view ($array) {
            $page         = isset($array['page']) && intval($array['page']) > 0 ? intval($array['page']) : 1;
            $searchString = isset($array['search']) ? urldecode($array['search']) : "";
            
            $can_add_new_suburb = $GLOBALS['system_user']->hasPermission("installer_add_suburb");
            $can_edit_suburb    = $GLOBALS['system_user']->hasPermission("installer_edit_suburb");
            
            $query  = "SELECT SQL_CALC_FOUND_ROWS";
            $query .= " `system_errors`.`id`,";
            $query .= " DATE_FORMAT(`system_errors`.`report_dts`, '%e %b %y, %H:%i') AS reported_on,";
            $query .= " CONCAT(`system_users`.`first`, ' ', `system_users`.`last`, ' [', `branches`.`branch`, ']') AS reported_by,";
            $query .= " `system_errors`.`description`,";
            $query .= " IF(`system_errors`.`resolved` = 1, DATE_FORMAT(`system_errors`.`resolve_dts`, '%e %b %y, %H:%i'), 'Not resolved') AS resolved_on,";
            $query .= " IF(`system_errors`.`resolved` = 1, CONCAT(`s`.`first`, ' ', `s`.`last`), 'Not resolved') AS resolved_by,";
            $query .= " `system_errors`.`resolved`";
            $query .= " FROM `system_errors`";
            $query .= " LEFT OUTER JOIN `system_users` LEFT OUTER JOIN `branches` ON `system_users`.`branch_id` = `branches`.`id`";
            $query .= " ON `system_errors`.`reporter_id` = `system_users`.`id`";
            $query .= " LEFT OUTER JOIN `system_users` s ON `system_errors`.`resolver_id` = `s`.`id`";
            $query .= " WHERE 1=1";
            if (!empty($searchString)) {
                $q = mysqli_real_escape_string($GLOBALS["link"],$searchString);
                $query .= " AND (`system_users`.`first` LIKE '%$q%' OR `system_users`.`last` LIKE '%$q%' OR `branches`.`branch` LIKE '%$q%' OR `system_errors`.`description` LIKE '%$q%' OR `s`.`first` LIKE '%$q%' OR `s`.`last` LIKE '%$q%')";
            }
            $query .= " ORDER BY `system_errors`.`report_dts` DESC, `system_errors`.`resolved` ASC, `system_users`.`first` ASC";
            $query .= " LIMIT " . ( $this->items_per_page * ($page - 1) ) . ", " . $this->items_per_page;
            
            $result  = mysqli_query($GLOBALS["link"],$query);
            $result2 = mysqli_query($GLOBALS["link"],"SELECT FOUND_ROWS()");
            $farr    = mysqli_fetch_row($result2);
            $total_items = $farr[0];
            
            $no_of_pages = ceil($total_items/$this->items_per_page);
            
            print "<input type='hidden' id='page-number' value='$page' />";
            print "<div id='groups-container' style='width: 100%;'>";// style='padding-left: 5%; padding-right: 5%;'>";
            print "<table cellpadding='0' cellspacing='0' border='0' width='100%'>";
                print "<tr class='same-bg'>";
                    print "<td colspan='2' style='font-style: italic; color: #666666; text-align: center;'><br/>";
                        print "<span id='ok-message'></span>";
                        print "<span id='error-message'></span>";
                    print "</td>";
                print "</tr>";
                print "<tr class='same-bg'>";
                    print "<td width='30%'>";
                        print "<br/>";
                        print "&nbsp;&nbsp;<span id='create-group' style='font-weight: 900; font-size: 13px; color: #222222;'>Reported System Errors</span>";
                        print "<br/>&nbsp;";
                    print "</td>";
                    print "<td width='70%' style='text-align: right;'>";
                        print "&nbsp;";
                    print "</td>";
                print "</tr>";
            print "</table>";
            print "<table id='users-table' cellspacing='0' cellpadding='0' border='0' width='100%'>";
            print "<thead>";
                print "<tr>";
                    print "<th class='tablecount' colspan='2' style='text-align: left; border: none; padding-left: 10px; font-style: italic; font-size: 11px;'>";
                        print "$total_items reported System errors";
                    print "</th>";
                    print "<th colspan='4' style='text-align: right; border: none; padding-bottom: 10px; padding-right: 10px;'>";
                        print "<input id='search-field' type='text' maxlength='35' title='Search' value='" . htmlentities($searchString, ENT_QUOTES) . "' />";
                        print "&nbsp;<a id='search-button' class='button-image' title='Search'><img src='images/magnifying_glass_16x16.png' /></a>";
                    print "</th>";
                print "</tr>";
                print "<tr>";
                    print "<th>Reported on</th>";
                    print "<th>Reported by</th>";
                    print "<th>Error description</th>";
                    print "<th>Resolved on</th>";
                    print "<th>Resolved by</th>";
                    print "<th>Resolved</th>";
                print "</tr>";
            print "</thead>";
            print "<tbody>";
                print "<tr>";
                    print "<th colspan='6'>&nbsp;</th>";
                print "</tr>";
                
                if ($total_items == 0 && empty($searchString)) {
                    print "<tr><td colspan='6' style='text-align: center; font-style: italic;'>No System errors have been reported</td></tr>";
                } else if($total_items == 0) {
                    print "<tr><td colspan='6' style='text-align: center; font-style: italic;'>No System errors found</td></tr>";
                } else;
                
                $disable_resolve_error = $GLOBALS['system_user']->hasPermission("installer_edit_company") ? "" : "disabled='disabled'";
                
                while ($arr = mysqli_fetch_assoc($result)) {
                    print "<tr>";
                        //print "<td><input type='checkbox' value='".$arr['id']."' id='suburb-checkbox-".$arr['id']."' class='group-checkbox' /></td>";
                        print "<td style='font-size: 11px;'>" . $arr['reported_on'] . "</td>";
                        print "<td style='font-size: 11px;'>" . $arr['reported_by'] . "</td>";
                        print "<td style='font-size: 11px;'>" . $this->trimNameView($arr['description'], $arr['id']) . "</td>";
                        print "<td style='font-size: 11px;'>" . $arr['resolved_on'] . "</td>";
                        print "<td style='font-size: 11px;'>" . $arr['resolved_by'] . "</td>";
                        print "<td style='font-size: 11px;'><input type='checkbox' class='resolve' id='res-" . $arr['id'] . "' $disable_resolve_error " . ($arr['resolved'] == 1 ? "checked='checked'" : "") . " /></td>";
                    print "</tr>";
                }
                    
                print "</tbody>";
                print "<tfoot>";
                    print "<tr>";
                        print "<th colspan='6'>&nbsp;</th>";
                    print "</tr>";
                    print "<tr>";
                        print "<td colspan='6' style='border-top: 1px solid #DDDDDD'>&nbsp;</td>";
                    print "</tr>";
                    print "<tr>";
                        print "<th colspan='6' align='left'>";
                            print $this->printPaging($page, $no_of_pages, __CLASS__, __FUNCTION__, urlencode($searchString));
                        print "</th>";
                    print "</tr>";
                print "</tfoot>";
            print "</table>";
            print "<br/>";
            
            $this->printErrorDescription();
            
            print "\n<!-- scripts , code below be eval()ed by javascript -->\n";
            
            print "
                jQuery('#search-field').focus().select();
            
                jQuery('.resolve').change(function () {
                    var r = jQuery(this).is(':checked') ? 1 : 0;
                    var jObjid = jQuery(this).attr('id');
                    var id = jObjid.substring(jObjid.lastIndexOf('-')+1);
                    var page = jQuery('#page-number').val();
                    var search = jQuery('#search-field').val();
                    AJAXCallModule('" . __CLASS__ . "', 'resolve', 'r='+r+'&id='+id+'&page=' + page + '&search=' + encodeURIComponent(search));
                });
            ";
            
            print "
                jQuery('#search-field').keydown(function (e) {
                    if (e.keyCode == 13) {
                        doSearch();
                    }
                });
                
                jQuery('#search-button').click(function () {
                    doSearch();
                });
                
                function doSearch () {
                    var search = jQuery('#search-field').val();
                    AJAXCallModule('" . __CLASS__ . "','" . __FUNCTION__ . "', 'search=' + encodeURIComponent(search) + '&page=1');
                }
            ";
            
            print "
                jQuery('.view-all').click(function (){
                    var jObjid = jQuery(this).attr('id');
                    var id = jObjid.substring(jObjid.lastIndexOf('-')+1);
                    AJAXCallModuleJSOnly('" . __CLASS__ . "', 'showFullError', 'id=' + id);
                });
            ";
            
            print "
                jQuery('.resolve').mouseover(function () { 
                    jQuery(this).parent('td').parent('tr').addClass('trhighlight');
                });
                
                jQuery('.resolve').mouseout(function(){
                    jQuery(this).parent('td').parent('tr').removeClass('trhighlight');
                });
            ";
        }
        
        public function showFullError ($array) {
            $id                = intval($array['id']);
            $result            = mysqli_query($GLOBALS["link"],"SELECT `description` FROM `system_errors` WHERE `id` = " . mysqli_real_escape_string($GLOBALS["link"],$id));
            $description_array = mysqli_fetch_assoc($result);
            $description       = $description_array['description'];
            $description       = htmlentities($description, ENT_QUOTES);
            $description       = str_replace("\n", "<br/>", $description);
            
            print "jQuery('#error-description-th').html(\"$description\");";
            print "blockDialog('#create-error-dialog', '500px');\n";
        }
        
        private function printErrorDescription(){
            print "<div id='create-error-dialog' class='dialog'>";
            print "<input type='hidden' id='suburbid' value='' />";
            print "<table cellpadding='0' cellspacing='0' border='0' width='100%'>";
                print "<thead style='background-color: #EEEEEE; border-bottom: 1px solid #CCCCCC;'>";
                    print "<tr>";
                        print "<th colspan='2'>";
                            print "<table cellpadding='0' cellspacing='0' border='0' width='100%'>";
                                print "<thead style='background-color: #EEEEEE; border-bottom: 1px solid #CCCCCC;'>";
                                    print "<tr>";
                                        print "<th id='create-error-title' style='text-align: left; font-weight: 900; font-family: trebuchet ms !important; font-size: 14px; padding-left: 20px; padding-top: 15px; padding-bottom: 10px;'>Error Description</th>";
                                        print "<th style='text-align: right; padding-right: 10px;'><img class='close-dialog' src='images/closepopup.png' style='cursor: pointer;' /></th>";
                                    print "</tr>";
                                print "</thead>";
                            print "</table>";
                        print "</th>";
                    print "</tr>";
                print "</thead>";
                print "<tbody>";
                    print "<tr>";
                        print "<th id='error-description-th' colspan='2' style='text-align: left; font-size: 12px; font-weight: 400; padding-top: 5px; padding-left: 10px; padding-right: 10px; padding-bottom: 10px;'>&nbsp;</th>";
                    print "</tr>";
                print "</tbody>";
                print "<tfoot style='background-color: #EEEEEE; border-top: 1px solid #CCCCCC;'>";
                    print "<tr>";
                        print "<th colspan='2' style='font-size: 12px; color: red; padding-top: 5px; padding-bottom: 5px;'>&nbsp;";
                        print "</th>";
                    print "</tr>";
                print "</tfoot>";
            print "</table>";
            print "</div>";
        }
        
        public function resolve ($array) {
            $search = $array['search'];
            $page   = $array['page'];
            $r      = intval($array['r']);
            $id     = intval($array['id']);
            if ($r == 0) {
                mysqli_query($GLOBALS["link"],"UPDATE `system_errors` SET `resolved` = 0 WHERE `id` = " . mysqli_real_escape_string($GLOBALS["link"],$id));
                logAction("Marked error as not resolved system_errors.id[$id]");
            } else {
                mysqli_query($GLOBALS["link"],"UPDATE `system_errors` SET `resolved` = 1, `resolver_id` = " . mysqli_real_escape_string($GLOBALS["link"],$GLOBALS['system_user']->id) . ", `resolve_dts` = NOW() WHERE `id` = " . mysqli_real_escape_string($GLOBALS["link"],$id));
                logAction("Marked error as resolved system_errors.id[$id]");
            }
            
            //print "AJAXCallModule('" . __CLASS__ . "', 'view', '&page=$page&search=$search');\n";
            $this->view(array("page" => $page, "search" => $search));
        }
        
        private function trimNameView ($branchName, $id, $maxSizeBeforeTrim = 65) {
            return strlen($branchName) > $maxSizeBeforeTrim ? htmlentities(substr($branchName, 0, $maxSizeBeforeTrim - 3), ENT_QUOTES) . "...&nbsp;&nbsp;&nbsp;<a class='view-all' title='View full error description' id='view-$id' style='font-size: 10px;'>View All</a>" : $branchName;
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