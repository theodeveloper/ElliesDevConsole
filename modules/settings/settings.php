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
    //Single
    /*register_menu("settings", "parentMenu", "Settings");

    
    register_permission("Settings Permissions", "settings_edit_settings", "Edit System settings");
    register_permission("Settings Permissions", "settings", "View System settings");*/


    register_menu("Settings", "submenu", "Settings",
        Array(
            Array("title" => "System Settings",  "location" => "system_settings",   "acl" => "settings"),
            Array("title" => "Quote Settings",  "location" => "quote_settings",   "acl" => "settings")  
        )
    );
    register_menu("Settings", "parentMenu", "Settings");

    register_permission("Settings Permissions", "settings_edit_settings", "Edit System settings");
    register_permission("Settings Permissions", "settings", "View System settings");


    $newLink = new mysqli(MYSQL_SERVER, MYSQL_USERNAME, MYSQL_PASSWORD,MYSQL_DATABASE);
    if($newLink->connect_errno){
        print "DB connection error.";
        exit(-1);
    }

    $link = $newLink;
    $GLOBALS["link"]=$newLink;

    class Settings {

        public function system_settings() {

           // print "<div style='padding: 10px; background-color: #C0C0C0;font-size: 16px;font-weight: bold;font-family: inherit;'>&nbsp;System Settings</div>";
            print "<div class='classy_table'>";
            print "&nbsp;&nbsp;&nbsp;<span style='font-size:14px;font-weight:bold;'>System Settings</span>";
            print "</div>";
            print "<div id='settings-container'>";
                print "<ul>";
                    print "<li><a href='#systemdiv'>General</a></li>";      
                    $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
                    $sqlres = mysqli_query($GLOBALS["link"],$sql);
                    $row = mysqli_fetch_assoc($sqlres);
                    $channeltype = $row['type'];
                    $sysuser = new userType($_SESSION["userid"]);
                    if ($channeltype == "Commercial" || $channeltype =="Franchises"){
                        if ($sysuser->isSuperAdmin)print "<li><a href='#rentaldiv'>Financials</a></li>"; 
                    } 
                    print "<li><a href='#chartdiv'>Dashboard Charts</a></li>";
                    print "<li><a href='#importexportdiv'>Import/Export</a></li>";        
                print "</ul>";
                // = == = = ==== == == ===== == = == == ==== == == = = ==  == = = ==== = ==
                print "<div id='systemdiv'>";         
                print "<table class='questionsetup' cellspacing='0' cellpadding='4' border='0' width='100%'>";
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
                                print "Site Title";
                            print "</td>";
                            print "<td colspan='3'>";
                                print "<input type='text' id='sitetitle' maxlength='30' value='".htmlentities(self::getSetting(1), ENT_QUOTES)."' style='width: 200px' />";
                            print "</td>";
                        print "</tr>";
                        print "<tr>";
                            print "<td colspan='2' style='text-align: right; font-weight: 400; color: #111111; padding-right: 20px;'>";
                                print "Number of table rows per page";
                            print "</td>";
                            print "<td colspan='1' style='text-align: left;'>";
                                print "<select id='tablerows'>";
                                    $rows = self::getSetting(2);
                                    print $rows == 5    ? "<option value='5'    selected='selected'>5</option>"    : "<option value='5'>5</option>";
                                    print $rows == 10   ? "<option value='10'   selected='selected'>10</option>"   : "<option value='10'>10</option>";
                                    print $rows == 20   ? "<option value='20'   selected='selected'>20</option>"   : "<option value='20'>20</option>";
                                    print $rows == 50   ? "<option value='50'   selected='selected'>50</option>"   : "<option value='50'>50</option>";
                                    print $rows == 100  ? "<option value='100'  selected='selected'>100</option>"  : "<option value='100'>100</option>";
                                    print $rows == 200  ? "<option value='200'  selected='selected'>200</option>"  : "<option value='200'>200</option>";
                                    print $rows == 500  ? "<option value='500'  selected='selected'>500</option>"  : "<option value='500'>500</option>";
                                    print $rows == 1000 ? "<option value='1000' selected='selected'>1000</option>" : "<option value='1000'>1000</option>";
                                print "</select>";
                            print "</td>";
                            print "<td colspan='2'>&nbsp;</td>";
                        print "</tr>";
                        print "<tr>";
                            print "<td colspan='2' style='text-align: right; font-weight: 400; color: #111111; padding-right: 10px;'>";
                                print "Automatically log out user after";
                            print "</td>";
                            print "<td colspan='3' style='text-align: left; font-weight: 400; color: #111111;'>";
                                print "<select id='autologoutinterval'>";
                                    $autologout = self::getSetting(3);
                                    print $autologout == 0   ? "<option value='0'   selected='selected'>Disabled</option>" : "<option value='0'>Disable</option>";
                                    print $autologout == 1   ? "<option value='1'   selected='selected'>1</option>"        : "<option value='1'>1</option>";
                                    print $autologout == 5   ? "<option value='5'   selected='selected'>5</option>"        : "<option value='5'>5</option>";
                                    print $autologout == 10  ? "<option value='10'  selected='selected'>10</option>"       : "<option value='10'>10</option>";
                                    print $autologout == 15  ? "<option value='15'  selected='selected'>15</option>"       : "<option value='15'>15</option>";
                                    print $autologout == 20  ? "<option value='20'  selected='selected'>20</option>"       : "<option value='20'>20</option>";
                                    print $autologout == 60  ? "<option value='60'  selected='selected'>60</option>"       : "<option value='60'>60</option>";
                                    print $autologout == 120 ? "<option value='120' selected='selected'>120</option>"      : "<option value='120'>120</option>";
                                print "</select>";
                                print "&nbsp;&nbsp;&nbsp;minute(s) of inactivity";
                            print "</td>";
                        print "</tr>";
                        print "<tr>";
                            print "<td colspan='2' style='text-align: right; font-weight: 900; color: #333333; padding-right: 20px;'>";
                                $showadmininbranch = self::getSetting(4) == 0 ? "" : "checked='checked'";
                                print "<input id='showadmin' type='checkbox' $showadmininbranch />";
                            print "</td>";
                            print "<td colspan='3' style='text-align: left; font-weight: 400; color: #111111; padding-right: 20px;'>";
                                print "&nbsp;";
                                print "<label for='showadmin' style='width: 90%; background-color: transparent; font-weight: 400; color: #111111; font-size: 11px;'>Show system super administrators in listings of their branches</label>";
                            print "</td>";
                        print "</tr>";
                        print "<tr>";
                            print "<td colspan='2' style='text-align: right; font-weight: 400; color: #111111; padding-right: 20px;'>";
                                print "Send system emails to these super administrators";
                            print "</td>";
                            print "<td colspan='2' style='text-align: left; font-weight: 900; color: #333333; padding-right: 20px;'>";
                                print "<select id='superadminemaillist'>";
                                    print "<option value='' selected='selected'>[Please select]</option>";
                                    $query  = "SELECT `id`, `first`, `last`, `email`";
                                    $query .= " FROM `system_users`";
                                    $query .= " WHERE `super_admin` = 1";
                                    $query .= " AND `active` = 1";
                                    $query .= " ORDER BY `first`";
                                    $result = mysqli_query($GLOBALS["link"],$query);
                                    while ($arr = mysqli_fetch_assoc($result)) {
                                        print "<option value='".htmlentities($arr['email'])."'>";
                                            print ucfirst($arr['first']." ".$arr['last']);
                                            if ($GLOBALS['system_user']->id == $arr['id']) print "*";
                                        print "</option>";
                                    }
                                print "</select>";
                            print "</td>";
                            print "<td style='text-align: left; font-weight: 900; color: #333333;'>";
                                print "<button id='add-admin-email'><img id='add-email-button' src='images/return.png' width='10' height='15' /></button>";
                            print "</td>";
                        print "</tr>";
                        print "<tr>";
                            print "<td colspan='4' style='text-align: right; padding: 0;'>";
                                print "<textarea id='adminemails' style='width: 90%; height: 50px; resize: none; overflow: auto;' maxlength='254'>".htmlentities(self::getSetting(5), ENT_QUOTES)."</textarea>";
                            print "</td>";
                            print "<td>&nbsp;</td>";
                        print "</tr>";
                        print "<tr>";
                            print "<td colspan='2' style='text-align: right; font-weight: 400; color: #111111; padding-right: 20px;'>";
                                print "All Installer cellphone numbers should obey this Regular Expression";
                            print "</td>";
                            print "<td colspan='2' style='text-align: right; padding: 0;'>";
                                print "<textarea id='regex' style='width: 100%; height: 45px; resize: none; overflow: auto;' maxlength='254'>".htmlentities(self::getSetting(7), ENT_QUOTES)."</textarea>";
                            print "</td>";
                            print "<td>&nbsp;</td>";
                        print "</tr>";
                        print "<tr>";
                            print "<td colspan='2' style='text-align: right; font-weight: 400; color: #111111; padding-right: 20px;'>";
                                print "Installer Ethnic groups";
                            print "</td>";
                            print "<td colspan='2' style='text-align: right; padding: 0;'>";
                                print "<textarea id='ethnic' style='width: 100%; height: 45px; resize: none; overflow: auto;' maxlength='254'>".htmlentities(self::getSetting(13), ENT_QUOTES)."</textarea>";
                            print "</td>";
                            print "<td>&nbsp;</td>";
                        print "</tr>";
                        print "<tr>";
                            print "<td colspan='2' style='text-align: right; font-weight: 900; color: #333333; padding-right: 20px;'>";
                                $disableuserlogin = self::getSetting(6) == 0 ? "" : "checked='checked'";
                                print "<input type='checkbox' id='disableduserlogin' $disableuserlogin />";
                            print "</td>";
                            print "<td colspan='3' style='text-align: left; font-weight: 400; color: #111111; padding-right: 20px;'>";
                                print "<label for='disableduserlogin' style='width: 90%; background-color: transparent; font-weight: 400; color: #111111; font-size: 11px;'>Disable user login (Site Maintenance)</label>";
                            print "</td>";
                        print "</tr>";
                    print "</tbody>";
                    print "<tfoot>";
                        print "<tr>";
                            print "<td colspan='3' valign='middle' style='text-align: right;'>";
                                print "<br/>";
                                print "<span id='ok-message'></span><span id='error-message'></span>";
                                print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button class='ibutton' id='save-settings-button'>Save settings</button>";
                                print "<br/><br/>";
                            print "</td>";
                            print "<td colspan='2' align='right' valign='middle' style='padding-right: 20px;'>";
                                print "<a id='restore-default-settings'>Restore default settings</a>";
                            print "</td>";
                        print "</tr>";
                    print "</tfoot>";
                print "</table>";
                print "</div>";
                // = == = = ==== == == ===== == = == == ==== == == = = ==  == = = ==== = ==
                $oldVals = self::getRentals();
                $sysuser = new userType($_SESSION["userid"]);
                if ($channeltype == "Commercial" || $channeltype =="Franchises"){
                    if ($sysuser->isSuperAdmin){
                        print "<div id='rentaldiv'>";
                            print "<table class='questionsetup' cellspacing='0' cellpadding='4' border='0' width='100%'>";
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
                                        print "<td colspan='2' style='text-align: right; font-weight: 400; color: #111111;'>";
                                            print "Annual Effective Interest Rate";
                                        print "</td>";
                                        print "<td colspan='3' style='padding-left: 3.7%; padding-right: 0; text-align: left;'>";
                                           print "<input type='text' id='interest_rate_annual_effective' maxlength='30' value='".$oldVals['interest_rate_annual_effective']."' style='width: 200px' />";
                                        print "</td>";
                                    print "</tr>";
                                    
                                    print "<tr>";                       
                                        print "<td colspan='2' style='text-align: right; font-weight: 400; color: #111111;'>";
                                            print "Rental Capital Escalation Rate";
                                        print "</td>";
                                        print "<td colspan='3' style='padding-left: 3.7%; padding-right: 0; text-align: left;'>";
                                           print "<input type='text' id='rental_capital_escalation_rate' maxlength='30' value='".$oldVals['rental_capital_escalation_rate']."' style='width: 200px' />";
                                        print "</td>";
                                    print "</tr>";
                                    
                                    print "<tr>";   
                                    print "<tr>";                       
                                        print "<td colspan='2' style='text-align: right; font-weight: 400; color: #111111;'>";
                                            print "Rental Maintenance Escalation Rate";
                                        print "</td>";
                                        print "<td colspan='3' style='padding-left: 3.7%; padding-right: 0; text-align: left;'>";
                                           print "<input type='text' id='maintenance_rental_amount_escalation_rate' maxlength='30' value='".$oldVals['maintenance_rental_amount_escalation_rate']."' style='width: 200px' />";
                                        print "</td>";
                                    print "</tr>";
                                    
                                    print "<tr>";   
                                    print "<tr>";                       
                                        print "<td colspan='2' style='text-align: right; font-weight: 400; color: #111111;'>";
                                            print "Smaller Than Factor";
                                        print "</td>";
                                        print "<td colspan='3' style='padding-left: 3.7%; padding-right: 0; text-align: left;'>";
                                           print "<input type='text' id='smaller_than_factor' maxlength='30' value='".$oldVals['smaller_than_factor']."' style='width: 200px' />";
                                        print "</td>";
                                    print "</tr>";

                                    print "<tr>";                       
                                        print "<td colspan='2' style='text-align: right; font-weight: 400; color: #111111;'>";
                                            print "Target Return - A";
                                        print "</td>";
                                        print "<td colspan='3' style='padding-left: 3.7%; padding-right: 0; text-align: left;'>";
                                           print "<input type='text' id='return_A' maxlength='30' value='".$oldVals['return_A']."' style='width: 200px' />";
                                        print "</td>";
                                    print "</tr>";

                                    print "<tr>";                       
                                        print "<td colspan='2' style='text-align: right; font-weight: 400; color: #111111;'>";
                                            print "Target Return - B";
                                        print "</td>";
                                        print "<td colspan='3' style='padding-left: 3.7%; padding-right: 0; text-align: left;'>";
                                           print "<input type='text' id='return_B' maxlength='30' value='".$oldVals['return_B']."' style='width: 200px' />";
                                        print "</td>";
                                    print "</tr>";

                                    print "<tr>";                       
                                        print "<td colspan='2' style='text-align: right; font-weight: 400; color: #111111;'>";
                                            print "Target Return - C";
                                        print "</td>";
                                        print "<td colspan='3' style='padding-left: 3.7%; padding-right: 0; text-align: left;'>";
                                           print "<input type='text' id='return_C' maxlength='30' value='".$oldVals['return_C']."' style='width: 200px' />";
                                        print "</td>";
                                    print "</tr>";

                                    print "<tr>";                       
                                        print "<td colspan='2' style='text-align: right; font-weight: 400; color: #111111;'>";
                                            print "Deposit Proportion";
                                        print "</td>";
                                        print "<td colspan='3' style='padding-left: 3.7%; padding-right: 0; text-align: left;'>";
                                           print "<input type='text' id='deposit_percentage' maxlength='30' value='".$oldVals['deposit_percentage']."' style='width: 200px' />";
                                        print "</td>";
                                    print "</tr>";
                                    
                                    print "<tr>";                       
                                        print "<td colspan='2' style='text-align: right; font-weight: 400; color: #111111;'>";
                                            print "Initial Rental Expenses";
                                        print "</td>";
                                        print "<td colspan='3' style='padding-left: 3.7%; padding-right: 0; text-align: left;'>";
                                           print "<input type='text' id='initial_expenses_rental' maxlength='30' value='".$oldVals['initial_expenses_rental']."' style='width: 200px' />";
                                        print "</td>";
                                    print "</tr>";

                                    print "<tr>";                       
                                        print "<td colspan='2' style='text-align: right; font-weight: 400; color: #111111;'>";
                                            print "Maintenance Percentage per Rental";
                                        print "</td>";
                                        print "<td colspan='3' style='padding-left: 3.7%; padding-right: 0; text-align: left;'>";
                                           print "<input type='text' id='maintenance_percentage_per_rental' maxlength='30' value='".$oldVals['maintenance_percentage_per_rental']."' style='width: 200px' />";
                                        print "</td>";
                                    print "</tr>";
                                       print "<tr>";                        
                                        print "<td colspan='2' style='text-align: right; font-weight: 400; color: #111111;'>";
                                            print "Maintenance Percentage Price Escalation Rate";
                                        print "</td>";
                                        print "<td colspan='3' style='padding-left: 3.7%; padding-right: 0; text-align: left;'>";
                                           print "<input type='text' id='maintenance_percentage_price_escalation_rate' maxlength='30' value='".$oldVals['maintenance_percentage_price_escalation_rate']."' style='width: 200px' />";
                                        print "</td>";
                                    print "</tr>";
                                    
                                    print "<tr>";                       
                                        print "<td colspan='2' style='text-align: right; font-weight: 400; color: #111111;'>";
                                            print "Fixed Maintenance per Contract";
                                        print "</td>";
                                        print "<td colspan='3' style='padding-left: 3.7%; padding-right: 0; text-align: left;'>";
                                           print "<input type='text' id='fixed_maintenance_per_contract' maxlength='30' value='".$oldVals['fixed_maintenance_per_contract']."' style='width: 200px' />";
                                        print "</td>";
                                    print "</tr>";
                                    
                                    print "<tr>";                       
                                        print "<td colspan='2' style='text-align: right; font-weight: 400; color: #111111;'>";
                                            print "Fixed Maintenance per Contract Escalation Rate";
                                        print "</td>";
                                        print "<td colspan='3' style='padding-left: 3.7%; padding-right: 0; text-align: left;'>";
                                           print "<input type='text' id='fixed_maintenance_per_contract_escalation_rate' maxlength='30' value='".$oldVals['fixed_maintenance_per_contract_escalation_rate']."' style='width: 200px' />";
                                        print "</td>";
                                    print "</tr>";
                                    
                                    print "<tr>";                       
                                        print "<td colspan='2' style='text-align: right; font-weight: 400; color: #111111;'>";
                                            print "Fixed Maintenance per Product Escalation Rate";
                                        print "</td>";
                                        print "<td colspan='3' style='padding-left: 3.7%; padding-right: 0; text-align: left;'>";
                                           print "<input type='text' id='fixed_maintenance_per_product_escalation_rate' maxlength='30' value='".$oldVals['fixed_maintenance_per_product_escalation_rate']."' style='width: 200px' />";
                                        print "</td>";
                                    print "</tr>";
                                    //-----------
                                    print "<tr>";                       
                                        print "<td colspan='2' style='text-align: right; font-weight: 400; color: #111111;'>";
                                            print "Labour per minute - In hours";
                                        print "</td>";
                                        print "<td colspan='3' style='padding-left: 3.7%; padding-right: 0; text-align: left;'>";
                                           print "<input type='text' id='labour_per_minute_in_hours' maxlength='30' value='".$oldVals['labour_per_minute_in_hours']."' style='width: 200px' />";
                                        print "</td>";
                                    print "</tr>";
                                        
                                    print "<tr>";                       
                                        print "<td colspan='2' style='text-align: right; font-weight: 400; color: #111111;'>";
                                            print "Labour per minute - Out hours";
                                        print "</td>";
                                        print "<td colspan='3' style='padding-left: 3.7%; padding-right: 0; text-align: left;'>";
                                           print "<input type='text' id='labour_per_minute_out_hours' maxlength='30' value='".$oldVals['labour_per_minute_out_hours']."' style='width: 200px' />";
                                        print "</td>";
                                    print "</tr>";
                                    
                                    print "<tr>";                       
                                        print "<td colspan='2' style='text-align: right; font-weight: 400; color: #111111;'>";
                                            print "Labour per minute - Markup";
                                        print "</td>";
                                        print "<td colspan='3' style='padding-left: 3.7%; padding-right: 0; text-align: left;'>";
                                           print "<input type='text' id='labour_per_minute_markup' maxlength='30' value='".$oldVals['labour_per_minute_markup']."' style='width: 200px' />";
                                        print "</td>";
                                    print "</tr>";
                                    
                                    print "<tr>";                       
                                        print "<td colspan='2' style='text-align: right; font-weight: 400; color: #111111;'>";
                                            print "Contractor Hours per Day";
                                        print "</td>";
                                        print "<td colspan='3' style='padding-left: 3.7%; padding-right: 0; text-align: left;'>";
                                           print "<input type='text' id='contractor_hours_per_day' maxlength='30' value='".$oldVals['contractor_hours_per_day']."' style='width: 200px' />";
                                        print "</td>";
                                    print "</tr>";
                                    
                                    print "<tr>";                       
                                        print "<td colspan='2' style='text-align: right; font-weight: 400; color: #111111;'>";
                                            print "Contractor Cost per KM";
                                        print "</td>";
                                        print "<td colspan='3' style='padding-left: 3.7%; padding-right: 0; text-align: left;'>";
                                           print "<input type='text' id='contractor_cost_per_km' maxlength='30' value='".$oldVals['contractor_cost_per_km']."' style='width: 200px' />";
                                        print "</td>";
                                    print "</tr>";
                                    
                                    print "<tr>";                       
                                        print "<td colspan='2' style='text-align: right; font-weight: 400; color: #111111;'>";
                                            print "Contractor Cost per KM - Markup";
                                        print "</td>";
                                        print "<td colspan='3' style='padding-left: 3.7%; padding-right: 0; text-align: left;'>";
                                           print "<input type='text' id='contractor_cost_per_km_markup' maxlength='30' value='".$oldVals['contractor_cost_per_km_markup']."' style='width: 200px' />";
                                        print "</td>";
                                    print "</tr>";
                                    
                                    print "<tr>";                       
                                        print "<td colspan='2' style='text-align: right; font-weight: 400; color: #111111;'>";
                                            print "Disposal Charge - Markup";
                                        print "</td>";
                                        print "<td colspan='3' style='padding-left: 3.7%; padding-right: 0; text-align: left;'>";
                                           print "<input type='text' id='disposal_charge_markup' maxlength='30' value='".$oldVals['disposal_charge_markup']."' style='width: 200px' />";
                                        print "</td>";
                                    print "</tr>";
                                    
                                    print "<tr>";                       
                                        print "<td colspan='2' style='text-align: right; font-weight: 400; color: #111111;'>";
                                            print "Materials Charge - Markup";
                                        print "</td>";
                                        print "<td colspan='3' style='padding-left: 3.7%; padding-right: 0; text-align: left;'>";
                                           print "<input type='text' id='materials_markup' maxlength='30' value='".$oldVals['materials_markup']."' style='width: 200px' />";
                                        print "</td>";
                                    print "</tr>";
                                    
                                    print "<tr>";                       
                                        print "<td colspan='2' style='text-align: right; font-weight: 400; color: #111111;'>";
                                            print "Ballast Loss - Electric";
                                        print "</td>";
                                        print "<td colspan='3' style='padding-left: 3.7%; padding-right: 0; text-align: left;'>";
                                           print "<input type='text' id='ballast_loss_electric' maxlength='30' value='".$oldVals['ballast_loss_electric']."' style='width: 200px' />";
                                        print "</td>";
                                    print "</tr>";
                                    
                                    print "<tr>";                       
                                        print "<td colspan='2' style='text-align: right; font-weight: 400; color: #111111;'>";
                                            print "Ballast Loss - Magnetic/Mechanical";
                                        print "</td>";
                                        print "<td colspan='3' style='padding-left: 3.7%; padding-right: 0; text-align: left;'>";
                                           print "<input type='text' id='ballast_loss_magnetic' maxlength='30' value='".$oldVals['ballast_loss_magnetic']."' style='width: 200px' />";
                                        print "</td>";
                                    print "</tr>";                            
                                    //-----------     
                                print "</tbody>";
                                print "<tfoot>";
                                    print "<tr>";
                                        print "<td colspan='3' valign='middle' style='text-align: right;'>";
                                            print "<br/>";
                                            print "<span id='ok-message3'></span><span id='error-message3'></span>";
                                            print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button class='ibutton' id='save-settings-button3'>Save settings</button>";
                                            print "<br/><br/>";
                                        print "</td>";
                                        
                                        print "<td colspan='2' align='right' valign='middle' style='padding-right: 20px;'>";
                                            print "<a id='restore-default-settings3'>Restore default settings</a>";
                                        print "</td>";
                                    print "</tr>";
                                print "</tfoot>";
                            print "</table>";
                        print "</div>";
                    }
                }
                //===============================================================
                print "<div id='chartdiv'>";
                print "<p style='float:right'><a href='#' id='viewCharts' name='viewCharts'><u>View</u></a> | <a  href='#' id='editCharts' name='editCharts'><u>Edit</u></a> | <a id='addCharts' name='addCharts'><u>Add</u></a></p>";
                print '
                        <body>
                            <table id="viewchartsetup" name="viewchartsetup" class="display" cellspacing="2" width="100%">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Description</th>
                                        <th>Show</th>
                                        <th>Channel</th>               
                                    </tr>
                                </thead>
                                <tbody>';
                                              
                                    $query  = "SELECT `title`, `description`, `show_chart`, `channel_type`";
                                    $query .= " FROM `dashboardchart_settings`";
                                    $query .= " WHERE `deleted` = '0'";
                                    $query .= " ORDER BY `id`";
                                    $result = mysqli_query($GLOBALS["link"],$query);
                                    while ($arr = mysqli_fetch_assoc($result)) {
                                        print "<tr>";
                                            print '<td>' . $arr['title'] . '</td>';
                                            print '<td>' . $arr['description'] .'</td>';
                                            print '<td align="center">' . $arr['show_chart'] .'</td>';
                                            print '<td align="center">'.$arr['channel_type'].'</td>';
                                        print "</tr>";
                                    }  

                            print "</tbody>";
                            print "</table>";
                            print '<script type="text/javascript" language="javascript" class="init">
                                $(document).ready(function() {
                                    $("#viewchartsetup").dataTable( {
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
                print "<table id='editchartsetup' name='editchartsetup' cellspacing='0' cellpadding='4' border='0' width='100%'>";
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
                                print "Select Chart";
                                print "<input type='hidden' id='chart_id' style='width: 200px' />"; 
                             print "</td>";
                             print "<td>";
                               print "<select id='chart'>";
                               print "<option value='' selected='selected'>[Please select]</option>";
                               $arrChannels = array("Commercial","Retail","Franchises");
                               for($i=0;$i<count($arrChannels);$i++){
                                    print "<optgroup id='".$arrChannels[$i]."' label='".$arrChannels[$i]."'>";
                                    $query  = "SELECT `id`,`title`";
                                    $query .= " FROM `dashboardchart_settings`";
                                    $query .= " WHERE `deleted` = '0' AND `channel_type`='".$arrChannels[$i]."'";
                                    $query .= " ORDER BY `id`";
                                    $result = mysqli_query($GLOBALS["link"],$query);
                                    while ($arr = mysqli_fetch_assoc($result)) {
                                        print "<option id='".$arr['id']."' value='".htmlentities($arr['id'])."'>";
                                            print ucfirst($arr['title']);
                                        print "</option>";
                                    }
                                    print "</optgroup>";
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
                                            print "<input type='text' id='charttitle_edit' name='charttitle_edit' style='width: 200px' />";        
                                         print "</td>";
                                     print "</tr>";

                                    print "<tr>";
                                         print "<td>";
                                            print "Description";
                                         print "</td>";
                                         print "<td>";
                                            print "<textarea id='chartdescription_edit' name='chartdescription_edit' style='width: 98%; height: 50px; resize: none; overflow: auto;' maxlength='254'></textarea>";           
                                         print "</td>";
                                    print "</tr>";

                                    print "<tr>";
                                         print "<td>";
                                            print "Show";
                                         print "</td>";
                                         print "<td>";
                                         print "<select id='chartshow_edit' name='chartshow_edit'>";
                                            print "<option value='' selected='selected'>[Please select]</option>";
                                            print "<option id='Yes' value='Yes'>Yes</option>";
                                            print "<option id='No' value='No'>No</option>";
                                         print "</select>";
                                         print "</td>";
                                    print "</tr>";

                                    print "<tr>";
                                         print "<td>";
                                            print "Channel Type";
                                         print "</td>";
                                         print "<td>";
                                         print "<select id='chartchanneltype_edit' name='chartchanneltype_edit'>";
                                            print "<option value='' selected='selected'>[Please select]</option>";
                                            print "<option id='Commercial' value='Commercial'>Commercial</option>";
                                            print "<option id='Retail' value='Retail'>Retail</option>";
                                            print "<option id='Franchises' value='Franchises'>Franchises</option>";
                                         print "</select>";
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
                                    print "<span id='ok-message2-chart'style='color:green'></span><span id='error-message2-chart'style='color:red'></span>";
                                    print "<span id='ok-message2-chart-save'style='color:green'></span><span id='error-message2-chart-save'style='color:red'></span>";
                                    print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button class='ibutton' id='save-chart-settings-button'>Save Chart</button>";
                                    print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button class='ibutton' id='delete-chart-settings-button' style='color:red'>Delete Chart</button>";

                                    print "<br/><br/>";
                                print "</td>";
                            print "</tr>";
                        print "</tfoot>";
                print "</table>";
                print "<table id='addchartsetup' name='addchartsetup' cellspacing='0' cellpadding='4' border='0' width='100%'>";
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
                                            print "<input type='text' id='addcharttitle' style='width: 200px' />";        
                                         print "</td>";
                                     print "</tr>";

                                    print "<tr>";
                                         print "<td>";
                                            print "Description";
                                         print "</td>";
                                         print "<td>";
                                            print "<textarea id='addchartdescription' style='width: 98%; height: 50px; resize: none; overflow: auto;' maxlength='254'></textarea>";           
                                         print "</td>";
                                    print "</tr>";

                                    print "<tr>";
                                         print "<td>";
                                            print "Show";
                                         print "</td>";
                                         print "<td>";
                                         print "<select id='addchartshow'>";
                                            print "<option value='' selected='selected'>[Please select]</option>";
                                            print "<option id='Yes' value='Yes'>Yes</option>";
                                            print "<option id='No' value='No'>No</option>";
                                         print "</select>";
                                         print "</td>";
                                    print "</tr>";


                                    print "<tr>";
                                         print "<td>";
                                            print "Channel Type";
                                         print "</td>";
                                         print "<td>";
                                         print "<select id='addchartchanneltype'>";
                                            print "<option value='' selected='selected'>[Please select]</option>";
                                            print "<option id='Commercial' value='Commercial'>Commercial</option>";
                                            print "<option id='Retail' value='Retail'>Retail</option>";
                                            print "<option id='Franchises' value='Franchises'>Franchises</option>";
                                         print "</select>";
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
                                    print "<span id='ok-message-chart'style='color:green'></span><span id='error-message-chart'style='color:red'></span>";
                                    print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button class='ibutton' id='add-chart-settings-button'>Add Chart</button>";
                                    print "<br/><br/>";
                                print "</td>";
                            print "</tr>";
                        print "</tfoot>";
                print "</table>";
                print "</div>"; 
                //===============================================================
                print "<div id='importexportdiv'>";
                print "<table  cellspacing='10' cellpadding='20' border='0' width='100%'>";
                    print "<tbody>";
                        print "<tr>";
                            print "<td><button class='ibutton' id='db-export-button' style='height: 80px;width: 134px;font-size: 16px;'>DB Export</button></td>";
                            print "<td><button class='ibutton' id='tech-import-button' style='height: 80px;width: 134px;font-size: 16px;'>Product Import</button></td>";
                            print "<td><button class='ibutton' id='tech-prices-import-button' style='height: 80px;width: 134px;font-size: 16px;'>Update Product Prices</button></td>";
                            print "<td><button class='ibutton' id='customer-export-button' style='height: 80px;width: 134px;font-size: 16px;'>Customer Export</button></td>";
                        print "</tr>";
                    print "</tbody>";
                print "</table>";
                print "<br/><br/>";

                print "<div id='dbexportdiv'>";
                    print "<div class='classy_table'>";
                    print "&nbsp;&nbsp;&nbsp;<span style='font-size:14px;font-weight:bold;'>DB Export</span>";
                    print "</div>";
                    print "<br/>";
                    print "<a href='downloaddb.php'><u>Download CSV</u></a>";
                print "</div>"; 

                print "<div id='techimportdiv'>";
                    print "<div class='classy_table'>";
                    print "&nbsp;&nbsp;&nbsp;<span style='font-size:14px;font-weight:bold;'>Product Types</span>";
                    print "</div>";
                    print "<br/>";
                    print "<iframe src='quotes/importcsv2.php' width='600' height='600' border='0' frameborder='0'></iframe>";
                print "</div>"; 

                print "<div id='techpricesimportdiv'>";
                    print "<div class='classy_table'>";
                    print "&nbsp;&nbsp;&nbsp;<span style='font-size:14px;font-weight:bold;'>Product Price List</span>";
                    print "</div>";
                    print "<br/>";
                    
                    //Form
                    print '<form action="modules/settings/importtechprice.php" method="POST" enctype="multipart/form-data">';
                    print "<label for='fileold'>Existing Products Prices CSV file</label><br>";
                    print "<input type='file' id='filenew' name='filenew'><br><br>";

                    print "<input type='hidden' name='channel' id='channel' value='0'>";
                    $sysuser = new userType($_SESSION["userid"]);
                    if (!$sysuser->isSuperAdmin){

                        print "<input type='hidden' name='typechannel' id='channel' value='".$sysuser->retailChannel."'>";
                    } else {
                        $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
                        $sqlres = mysqli_query($GLOBALS["link"],$sql);
                        $row = mysqli_fetch_assoc($sqlres);
                        $channeltype = $row['type'];
                        print "<input type='hidden' name='userid' id='userid' value='".$sysuser->id."'>";
                        print "<select name='typechannel' id='typechannel'>";
                        $query = "select id, name from channels where channels.type='".$channeltype."'";
                        $result = mysqli_query($GLOBALS["link"],$query);
                        while ($row=mysqli_fetch_assoc($result)){
                            print "<option value='".$row["id"]."'>".$row["name"]."</option>";
                        }
                        print "</select>";
                    }
             
                    print '<input data-theme="b" value="Submit" type="submit">';
                    print '</form>'; 
                print "</div>"; 

                print "<div id='customerexportdiv'>";
                    print "<div class='classy_table'>";
                    print "&nbsp;&nbsp;&nbsp;<span style='font-size:14px;font-weight:bold;'>Customer Export</span>";
                    print "</div>";
                    print "<br/>";
                    //Channel Type
                    $sql = "SELECT `id`,`type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
                    $sqlres = mysqli_query($GLOBALS["link"],$sql);
                    $row = mysqli_fetch_assoc($sqlres);
                    $channeltype = $row['type'];
                    $channelID = $row['id'];

                    $sysuser = new userType($_SESSION["userid"]);
                     if ($sysuser->isSuperAdmin){
                        $query = "select `name`,`id` from `channels` where`channels`.type='".$channeltype."'";
                        $result = mysqli_query($GLOBALS["link"],$query);
                        if($channeltype =="Commercial")print "<label for='branch'>Select Commercial</label><br/><br/>";
                        if($channeltype =="Retail")print "<label for='branch'>Select Retail</label><br/><br/>";
                        if($channeltype =="Franchises")print "<label for='branch'>Select Franchise</label><br/><br/>";

                        print "<select id='branch'>";
                            print "<option value=''>[Please select]</option>";
                            print "<option id='All' value='All' selected='selected'>All</option>";
                            while ($row=mysqli_fetch_assoc($result)){
                                print "<option id='".$row['id']."' value='".$row["id"]."'>";
                                    print ucfirst($row["name"]);
                                print "</option>";
                            }
                        print "</select><br/><br/>"; 

                        print "<input type='hidden' id='customer_channel' style='width: 200px' value='". $channelID."' readonly/><br/>";
                        print "<input type='hidden' id='customer_branch' style='width: 200px' value='All' readonly/><br/>";

                        print "<a id='customerdb' name='customerdb' href='#'><u>Download spreadsheet</u></a>";

                    }else{
                        $query = "select `name`,`id` from `channels`where id='".$GLOBALS['system_user']->retailChannel."'";
                        $result = mysqli_query($GLOBALS["link"],$query);
                        $branch = "";
                        $id = "";
                        $channel = "";
                        while ($row=mysqli_fetch_assoc($result)){
                            $branch =  ucfirst($row["name"]);
                            $id =  $row["id"];
                            $channel =  $row["id"];
                        }      

                        print "<label for='branch'>Branch</label><br/><br/>";
                        print "<label style='font-size: 12px;font-style: normal;font-weight: bold;'>". $branch." </label><br/><br/>";
                        print "<a href='customerdb.php?branch=".$id."&channel=".$channel."'><u>Download spreadsheet</u></a>"; 
                    }
                    
                print "</div>";

                print "</div>"; 
                //===============================================================
                print "<div style='margin: 0 auto; width: 95%; border-bottom: 2px solid #BBBBBB;'>&nbsp;</div>";
                print "</div>";
            
                print "\n<!-- scripts , code below be eval()ed by javascript -->\n";

                print "jQuery(\"#settings-container\").tabs({ selected: 0 });\n";
                //===============================================================
                //System Settings
                print "jQuery('#save-settings-button').click(function () {
                        var title      = jQuery('#sitetitle').val();
                        var rows       = jQuery('#tablerows').val();
                        var autologout = jQuery('#autologoutinterval').val();
                        var showadmin  = jQuery('#showadmin').is(':checked') ? 1 : 0;
                        var emails     = jQuery('#adminemails').val();
                        var loginoff   = jQuery('#disableduserlogin').is(':checked') ? 1 : 0;
                        var telregex   = jQuery('#regex').val();
                        var ethnic     = jQuery('#ethnic').val();
                        AJAXCallModuleJSOnly('" . __CLASS__ . "', 'saveSystemSettings', 'title='+encodeURIComponent(title)+'&rows='+rows+'&autologout='+autologout+'&showadmin='+showadmin+'&emails='+encodeURIComponent(emails)+'&loginoff='+loginoff+'&regex='+encodeURIComponent(telregex)+'&ethnic='+encodeURIComponent(ethnic));
                    });\n";
                print "jQuery('#add-admin-email').click(function () {
                        var email = jQuery('#superadminemaillist').val();
                        if (email != ''){
                            var allEmails = jQuery('#adminemails').val();
                            if (allEmails == '') {
                                allEmails += email;
                            } else {
                                allEmails += ', ' + email;
                            }
                            
                            if (allEmails.length <= jQuery('#adminemails').attr('maxlength')){
                                jQuery('#adminemails').val(allEmails);
                            } else;
                        }
                    });\n";
                //===============================================================
                //Financial Settings
                print "jQuery('#save-settings-button3').click(function () {
                    var interest_rate_annual_effective = jQuery('#interest_rate_annual_effective').val();
                    var rental_capital_escalation_rate = jQuery('#rental_capital_escalation_rate').val();
                    var maintenance_rental_amount_escalation_rate = jQuery('#maintenance_rental_amount_escalation_rate').val();
                    var deposit_percentage = jQuery('#deposit_percentage').val();
                    var smaller_than_factor = jQuery('#smaller_than_factor').val();
                    var return_A = jQuery('#return_A').val();
                    var return_B = jQuery('#return_B').val();
                    var return_C = jQuery('#return_C').val();
                    var initial_expenses_rental = jQuery('#initial_expenses_rental').val();
                    var maintenance_percentage_per_rental = jQuery('#maintenance_percentage_per_rental').val();
                    var maintenance_percentage_price_escalation_rate = jQuery('#maintenance_percentage_price_escalation_rate').val();
                    var fixed_maintenance_per_contract = jQuery('#fixed_maintenance_per_contract').val();
                    var fixed_maintenance_per_contract_escalation_rate = jQuery('#fixed_maintenance_per_contract_escalation_rate').val();
                    var fixed_maintenance_per_product_escalation_rate = jQuery('#fixed_maintenance_per_product_escalation_rate').val();
                    
                    var labour_per_minute_in_hours = jQuery('#labour_per_minute_in_hours').val();
                    var labour_per_minute_out_hours = jQuery('#labour_per_minute_out_hours').val();
                    var labour_per_minute_markup = jQuery('#labour_per_minute_markup').val();
                    var contractor_hours_per_day = jQuery('#contractor_hours_per_day').val();
                    var contractor_cost_per_km = jQuery('#contractor_cost_per_km').val();
                    var contractor_cost_per_km_markup = jQuery('#contractor_cost_per_km_markup').val();
                    var disposal_charge_markup = jQuery('#disposal_charge_markup').val();
                    var materials_markup = jQuery('#materials_markup').val();
                    var ballast_loss_magnetic = jQuery('#ballast_loss_magnetic').val();
                    var ballast_loss_electric = jQuery('#ballast_loss_electric').val();
                    
                    var url = 'interest_rate_annual_effective='+interest_rate_annual_effective+'&rental_capital_escalation_rate='+rental_capital_escalation_rate+'&maintenance_rental_amount_escalation_rate='+maintenance_rental_amount_escalation_rate+'&deposit_percentage='+deposit_percentage+'&smaller_than_factor='+smaller_than_factor+'&return_A='+return_A+'&return_B='+return_B+'&return_C='+return_C+'&initial_expenses_rental='+initial_expenses_rental+'&maintenance_percentage_per_rental='+maintenance_percentage_per_rental+'&maintenance_percentage_price_escalation_rate='+maintenance_percentage_price_escalation_rate+'&fixed_maintenance_per_contract='+fixed_maintenance_per_contract+'&fixed_maintenance_per_contract_escalation_rate='+fixed_maintenance_per_contract_escalation_rate+'&fixed_maintenance_per_product_escalation_rate='+fixed_maintenance_per_product_escalation_rate+'&labour_per_minute_in_hours='+labour_per_minute_in_hours+'&labour_per_minute_out_hours='+labour_per_minute_out_hours+'&labour_per_minute_markup='+labour_per_minute_markup+'&contractor_hours_per_day='+contractor_hours_per_day+'&contractor_cost_per_km='+contractor_cost_per_km+'&contractor_cost_per_km_markup='+contractor_cost_per_km_markup+'&disposal_charge_markup='+disposal_charge_markup+'&materials_markup='+materials_markup+'&ballast_loss_electric='+ballast_loss_electric+'&ballast_loss_magnetic='+ballast_loss_magnetic;
                    AJAXCallModuleJSOnly('" . __CLASS__ . "', 'saveRentalSettings', url);
                    });\n
                ";

                print "jQuery('#restore-default-settings3').click(function () {
                        if (confirm('Are you sure you want to load default general settings? Your current general settings will be lost.')) {
                            AJAXCallModuleJSOnly('" . __CLASS__ . "', 'restoreDefaultRentalSettings', 'restore=1');
                        }
                    });\n";
                //===============================================================
                //Chart Settings
                print " $('table#editchartsetup').hide();
                    jQuery('a#editCharts').click(function () {
                        $('table#addchartsetup').hide(); 
                        $('div#viewchartsetup_wrapper').hide();
                        $('table#editchartsetup').show(); 
                    });\n";

                print "jQuery('a#viewCharts').click(function () {
                         $('table#editchartsetup').hide();
                         $('table#addchartsetup').hide(); 
                         $('div#viewchartsetup_wrapper').show(); 
                    });\n ";

                print " $('table#addchartsetup').hide();
                    jQuery('a#addCharts').click(function () {
                        $('div#viewchartsetup_wrapper').hide();
                        $('table#editchartsetup').hide();
                        $('table#addchartsetup').show(); 
                    });\n
                ";

                print "jQuery('select#chart').change(function () {
                        var select = $('select#chart');
                        var selectedItem= select.find(':selected');
                        var id = selectedItem.attr('id');               
                        $('input#chart_id').val(id); 
                        AJAXCallChartInfo('" . __CLASS__ . "', 'getChartInfo', 'chartID='+id);  
                    });\n";

                print "jQuery('#add-chart-settings-button').click(function () {
                        var title  = jQuery('input#addcharttitle').val();
                        var description = jQuery('textarea#addchartdescription').val();
                        var show = jQuery('select#addchartshow').val();
                        var channel = jQuery('select#addchartchanneltype').val();
                        AJAXCallModuleJSOnly('" . __CLASS__ . "', 'addChartSettings', 'title='+title+'&description='+description+'&show='+show+'&channel='+channel);
                    });\n";


                print "jQuery('#save-chart-settings-button').click(function () {
                    var title  = jQuery('input#charttitle_edit').val();
                    var description = jQuery('textarea#chartdescription_edit').val();
                    var show = jQuery('select#chartshow_edit').val();
                    var channel = jQuery('select#chartchanneltype_edit').val();
                    var id  = jQuery('input#chart_id').val(); 
                    AJAXCallModuleJSOnly('" . __CLASS__ . "', 'saveChartSettings', 'id='+id+'&title='+title+'&description='+description+'&show='+show+'&channel='+channel);
                    });\n";

                print "jQuery('#delete-chart-settings-button').click(function () {
                        var title  = jQuery('input#chart_id').val(); 
                        AJAXCallModuleJSOnly('" . __CLASS__ . "', 'deleteChartSettings', 'id='+title);
                    });\n";
                //===============================================================
                //Import and Export
                print " $('div#dbexportdiv').hide();";
                print " $('div#techimportdiv').hide();";
                print " $('div#techpricesimportdiv').hide();";
                print " $('div#customerexportdiv').hide();";

            
                print "$('button#db-export-button').on('click', function (e) {
                        $('div#techimportdiv').hide();
                        $('div#techpricesimportdiv').hide(); 
                        $('div#customerexportdiv').hide(); 
                        $('div#dbexportdiv').toggle(); 
                    });\n";
                print "$('button#tech-import-button').on('click', function (e) {
                        $('div#dbexportdiv').hide();
                        $('div#techpricesimportdiv').hide(); 
                        $('div#customerexportdiv').hide(); 
                        $('div#techimportdiv').toggle(); 
                    });\n";

                print "$('button#tech-prices-import-button').on('click', function (e) {
                        $('div#dbexportdiv').hide();
                        $('div#techimportdiv').hide(); 
                        $('div#customerexportdiv').hide(); 
                        $('div#techpricesimportdiv').toggle(); 
                    });\n";

                print "$('button#customer-export-button').on('click', function (e) {
                        $('div#dbexportdiv').hide();
                        $('div#techimportdiv').hide(); 
                        $('div#techpricesimportdiv').hide(); 
                        $('div#customerexportdiv').toggle(); 
                    });\n";

                print " var channel = 'customerdb.php?channel='+$('input#customer_channel').val();
                        var branch =$('input#customer_branch').val();  
                        if(branch !=''){
                            channel2 = channel + '&branch=' + branch;
                            $('a#customerdb').attr('href', channel2) ;  
                        }
                        jQuery('select#branch').change(function () { 
                        var id = $('select#branch').children(':selected').attr('id');
                        $('input#customer_branch').val(''); 
                        var url = channel;
                        url += '&branch=' +id;
                        $('a#customerdb').attr('href', url) ;              
                    });\n
                ";

            //print "</div>";
           // print "</div>";
        }

        public function quote_settings() {

           // print "<div style='padding: 10px; background-color: #C0C0C0;font-size: 16px;font-weight: bold;font-family: inherit;'>&nbsp;Quote Settings</div>";
            print "<div class='classy_table'>";
            print "&nbsp;&nbsp;&nbsp;<span style='font-size:14px;font-weight:bold;'>Quote Settings</span>";
            print "</div>";
            $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
                    $sqlres = mysqli_query($GLOBALS["link"],$sql);
                    $row = mysqli_fetch_assoc($sqlres);
                    $channeltype = $row['type'];
            print "<div id='settings-container'>";
                print "<ul>";
                    print "<li><a href='#checklistdiv'>Checklist Questions</a></li>";
                    print "<li><a href='#pdfdiv'>PDF</a></li>";
                    print "<li><a href='#approvaldiv'>Approval Users</a></li>";
                    print "<li><a href='#quoteroomdiv'>Rooms</a></li>";
                    print "<li><a href='#quoteescalatediv'>Escalation Users</a></li>";
                    if ($channeltype !== "Retail")print "<li><a href='#propertydiv'>Property types</a></li>";
                    print "<li><a href='#floorplansdiv'>Document categories</a></li>";   
                print "</ul>";
                // = == = = ==== == == ===== == = == == ==== == == = = ==  == = = ==== = == 
                print "<div id='checklistdiv'>";
                print "<p style='float:right'><a href='#' id='viewChecklists' name='viewChecklists'><u>View</u></a> | <a href='#' id='editChecklists' name='editChecklists'><u>Edit</u></a> | <a  href='#' id='addchecklists' name='addchecklists'><u>Add</u></p></a>";
                print '
                        <body>
                            <table id="viewchecklistsetup" name="viewchecklistsetup" class="display" cellspacing="2" width="100%">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Description</th>
                                        <th>Type</th>
                                        <th>Mandatory</th>               
                                    </tr>
                                </thead>
                                <tbody>';
                                              
                             $query  = "SELECT `Title`, `Description`, `Type`, `Mandatory`";
                                    $query .= " FROM `checklist`";
                                    $query .= " WHERE `active` = 1";
                                    $query .= " ORDER BY `id`";
                                    $result = mysqli_query($GLOBALS["link"],$query);
                                    while ($arr = mysqli_fetch_assoc($result)) {
                                        print "<tr>";
                                            print '<td>' . $arr['Title'] . '</td>';
                                            print '<td>' . $arr['Description'] .'</td>';
                                            print '<td align="center">' . $arr['Type'] .'</td>';
                                            if( $arr['Mandatory']  == '1'){
                                                 print '<td>Yes</td>';
                                            }else{
                                                 print '<td>No</td>';
                                            }                                  
                                        print "</tr>";
                                    }  

                            print "</tbody>";
                            print "</table>";
                            print '<script type="text/javascript" language="javascript" class="init">
                                $(document).ready(function() {
                                    $("#viewchecklistsetup").dataTable( {
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
                print "<table id='editchecklistsetup' name='editchecklistsetup' cellspacing='0' cellpadding='4' border='0' width='100%'>";
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
                                print "Select Checklist Question:";
                                print "<input type='hidden' id='checklistid' style='width: 200px' />"; 
                            print "</td>"; 
                            print "<td>";     
                                print "<select id='checklist'>";
                                    print "<option value='' selected='selected'>[Please select]</option>";
                                    $query  = "SELECT `id`,`Title`, `Description`, `Type`, `Mandatory`";
                                    $query .= " FROM `checklist`";
                                    $query .= " ORDER BY `id`";
                                    $result = mysqli_query($GLOBALS["link"],$query);
                                    while ($arr = mysqli_fetch_assoc($result)) {
                                        print "<option id='".$arr['id']."' value='".htmlentities($arr['Title'])."'>";
                                         $Description = $arr['Description'];
                                         $Type = $arr['Type'];
                                         $Mandatory = $arr['Mandatory'];
                                            print ucfirst($arr['Title']);
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
                                            print "<input type='text' id='checklistitle' style='width: 400px' />";        
                                         print "</td>";
                                     print "</tr>";

                                    print "<tr>";
                                         print "<td>";
                                            print "Description";
                                         print "</td>";
                                         print "<td>";
                                            print "<textarea id='checklistdescription' style='width: 98%; height: 50px; resize: none; overflow: auto;' maxlength='254'></textarea>";           
                                         print "</td>";
                                    print "</tr>";
                                    print "<tr>";
                                         print "<td>";
                                            print "Type";
                                         print "</td>";
                                         print "<td>";
                                         print "<select id='checklisttype'>";
                                            print "<option value='' selected='selected'>[Please select]</option>";
                                            print "<option id='Yes/No' value='Yes/No'>Yes/No</option>";
                                            print "<option id='Numeric' value='Numeric'>Numeric</option>";
                                            print "<option id='SingleLineText' value='SingleLineText'>SingleLineText</option>";
                                            print "<option id='MultiLineText' value='MultiLineText'>MultiLineText</option>";
                                         print "</select>";      
                                         print "</td>";
                                    print "</tr>";

                                      print "<tr>";
                                         print "<td>";
                                            print "Mandatory";
                                         print "</td>";
                                         print "<td>";
                                         print "<select id='checklistmandatory'>";
                                            print "<option value='' selected='selected'>[Please select]</option>";
                                            print "<option id='Yes' value='Yes'>Yes</option>";
                                            print "<option id='No' value='No'>No</option>";
                                         print "</select>";
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
                                    print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button class='ibutton' id='save-checklist-settings-button'>Save Question</button>";
                                    print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button class='ibutton' id='delete-checklist-settings-button' style='color:red'>Delete Question</button>";
                                    print "<br/><br/>";
                                print "</td>";
                            print "</tr>";
                        print "</tfoot>";
                print "</table>";
                print "<table id='addchecklistsetup' name='addchecklistsetup' cellspacing='0' cellpadding='4' border='0' width='100%'>";
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
                                            print "<input type='text' id='addchecklistitle' style='width: 400px' />";        
                                         print "</td>";
                                     print "</tr>";

                                    print "<tr>";
                                         print "<td>";
                                            print "Description";
                                         print "</td>";
                                         print "<td>";
                                            print "<textarea id='addchecklistdescription' style='width: 98%; height: 50px; resize: none; overflow: auto;' maxlength='254'></textarea>";           
                                         print "</td>";
                                    print "</tr>";
                                    print "<tr>";
                                         print "<td>";
                                            print "Type";
                                         print "</td>";
                                         print "<td>";
                                         print "<select id='addchecklisttype'>";
                                            print "<option value='' selected='selected'>[Please select]</option>";
                                            print "<option id='Yes/No' value='Yes/No'>Yes/No</option>";
                                            print "<option id='Numeric' value='Numeric'>Numeric</option>";
                                            print "<option id='SingleLineText' value='SingleLineText'>SingleLineText</option>";
                                            print "<option id='MultiLineText' value='MultiLineText'>MultiLineText</option>";
                                         print "</select>";      
                                         print "</td>";
                                    print "</tr>";

                                      print "<tr>";
                                         print "<td>";
                                            print "Mandatory";
                                         print "</td>";
                                         print "<td>";
                                         print "<select id='addchecklistmandatory'>";
                                            print "<option value='' selected='selected'>[Please select]</option>";
                                            print "<option id='Yes' value='Yes'>Yes</option>";
                                            print "<option id='No' value='No'>No</option>";
                                         print "</select>";
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
                                    print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button class='ibutton' id='add-checklist-settings-button'>Save Question</button>";
                                    print "<br/><br/>";
                                print "</td>";
                            print "</tr>";
                        print "</tfoot>";
                print "</table>";
                print "</div>"; 
                // = == = = ==== == == ===== == = == == ==== == == = = ==  == = = ==== = == 
                print "<div id='pdfdiv'>";
                    $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
                    $sqlres = mysqli_query($GLOBALS["link"],$sql);
                    $row = mysqli_fetch_assoc($sqlres);
                    $channeltype = $row['type'];
                    if ($channeltype !== "Commercial" && $channeltype !=="Franchises"){
                        $channeltype = "Retail";  
                    } 

                    if ($channeltype == "Commercial" || $channeltype =="Franchises"){
                        print "<p style='float:right'><a href='#' id='viewPDFSetttings' name='viewPDFSetttings'><u>View</u></a> | <a href='#' id='editPDFSetttings' name='editPDFSetttings'><u>Edit</u></a></p>";
                          print "<div id='divViewPDFSetttings'>";
                                print "<p><a id='termsCondView' name='termsCondView'><u>Terms and Conditions</u></a></p>";
                                print "<ul id='termsCondSetupView' name='termsCondSetupView'width='100%'>";
                                        $query  = "SELECT `termsCond`, `additionalCond`";
                                        $query .= " FROM `pdf_settings`"; 
                                        $query .= " WHERE `pdf_type` ='$channeltype'"; 
                                        $query .= " ORDER BY `id` DESC LIMIT 1";
                                        $result = mysqli_query($GLOBALS["link"],$query);
                                        $termsCond ="";
                                        $additonalCond = "";
                                        while ($arr = mysqli_fetch_assoc($result)) {
                                            $termsCond =  $arr['termsCond'];
                                            $additonalCond =  $arr['additionalCond'];
                                        } 
                                    print "$termsCond";
                                print "</ul>";
                                print "<br/>";
                                print "<p><a id='additonalCondView' name='additonalCondView'><u>Additional terms and conditions if the project is paid as a full maintenance rental contract</u></a></p>";
                                print "<ul id='additionalCondSetupView' name='additionalCondSetupView'width='100%'>";
                                    print "$additonalCond";
                                print "</ul>";
                          print "</div>";
                          print "<div id='divEditPDFSetttings'>";
                                print "<p><a id='termsCondEdit' name='termsCondEdit'><u>Terms and Conditions</u></a></p>";
                                print "<input type='hidden' id='pdfUserID' style='width: 200px' value='".$GLOBALS['system_user']->id."'/>";
                                    $query  = "SELECT `termsCond`, `additionalCond`";
                                    $query .= " FROM `pdf_settings`"; 
                                    $query .= " WHERE `pdf_type` ='$channeltype'"; 
                                    $query .= " ORDER BY `id` DESC LIMIT 1";
                                    $result = mysqli_query($GLOBALS["link"],$query);
                                    $termsCond ="";
                                    $additonalCond = "";
                                    while ($arr = mysqli_fetch_assoc($result)) {
                                        $termsCond =  $arr['termsCond'];
                                        $additonalCond =  $arr['additionalCond'];
                                    } 
                                print "<textarea id='termsCondSetupEdit' name='termsCondSetupEdit'width='100%' rows='10'cols='100'>".$termsCond."</textarea> ";
                                print "<br/>";
                                print "<p><a id='additonalCondEdit' name='additonalCondEdit'><u>Additional terms and conditions if the project is paid as a full maintenance rental contract</u></a></p>";
                                print "<textarea  id='additionalCondSetupEdit' name='additionalCondSetupEdit'width='100%' rows='10'cols='100'>".$additonalCond."</textarea>";
                                print "<br/>";
                                print "<br/>";
                                print "<span id='ok-message-pdf'style='color:green'></span><span id='error-message-pdf'style='color:red'></span>";
                                print "<br/>"; 
                                print "<button class='ibutton' id='save-pdf-settings-button' style='float: left;'>Save PDF</button>";
                                print "<br/><br/>";
                        print "</div>";
                    }else{
                         print "<p style='float:right'><a href='#' id='viewDisclaimerSetttings' name='viewDisclaimerSetttings'><u>View</u></a> | <a href='#' id='editDisclaimerSetttings' name='editDisclaimerSetttings'><u>Edit</u></a></p>";
                         print "<div id='divViewDisclaimerSetttings'>";
                                print "<p><a id='disclaimersView' name='disclaimersView'><u>Disclaimers</u></a></p>";
                                print "<ol id='disclaimersSetupView' name='disclaimersSetupView'width='100%'>";
                                        $query  = "SELECT `termsCond`, `additionalCond`";
                                        $query .= " FROM `pdf_settings`"; 
                                        $query .= " WHERE `pdf_type` ='$channeltype'"; 
                                        $query .= " ORDER BY `id` DESC LIMIT 1";
                                        $result = mysqli_query($GLOBALS["link"],$query);
                                        $termsCond ="";
                                        $additonalCond = "";
                                        while ($arr = mysqli_fetch_assoc($result)) {
                                            $termsCond =  $arr['termsCond'];
                                            $additonalCond =  $arr['additionalCond'];
                                        } 
                                    print "$termsCond";
                                print "</ol>";
                                print "<br/>";
                        print "</div>";
                        print "<div id='divEditDisclaimerSetttings'>";
                                print "<p><a id='disclaimersEdit' name='disclaimersEdit'><u>Disclaimers</u></a></p>";
                                print "<input type='hidden' id='pdfUserID' style='width: 200px' value='".$GLOBALS['system_user']->id."'/>";
                                    $query  = "SELECT `termsCond`, `additionalCond`";
                                    $query .= " FROM `pdf_settings`"; 
                                    $query .= " WHERE `pdf_type` ='$channeltype'"; 
                                    $query .= " ORDER BY `id` DESC LIMIT 1";
                                    $result = mysqli_query($GLOBALS["link"],$query);
                                    $termsCond ="";
                                    $additonalCond = "";
                                    while ($arr = mysqli_fetch_assoc($result)) {
                                        $termsCond =  $arr['termsCond'];
                                        $additonalCond =  $arr['additionalCond'];
                                    } 
                                print "<textarea id='termsCondSetupEdit' name='termsCondSetupEdit'width='100%' rows='40'cols='100'>".$termsCond."</textarea> ";
                                print "<textarea  id='additionalCondSetupEdit' name='additionalCondSetupEdit'width='100%' rows='10'cols='100' style='display:none;'>".$additonalCond."</textarea>";
                                print "<br/>";
                                print "<span id='ok-message-pdf'style='color:green'></span><span id='error-message-pdf'style='color:red'></span>";
                                print "<br/>"; 
                                print "<button class='ibutton' id='save-pdf-settings-button' style='float: left;'>Save Disclaimer</button>";
                                print "<br/><br/>";
                        print "</div>";
                    }      
                print "</div>";
                // = == = = ==== == == ===== == = == == ==== == == = = ==  == = = ==== = == 
                print "<div id='approvaldiv'>";
                print "<p style='float:right'><a href='#' id='viewApprovalusers' name='viewApprovalusers'><u>View</u></a> | <a href='#' id='editApprovalusers' name='editApprovalusers'><u>Edit</u></a> | <a href='#' id='addApprovalusers' name='addApprovalusers'><u>Add</u></a>";
                print "
                        <body>
                            <table id='viewApprovalusersetup' name='viewApprovalusersetup' cellspacing='0' cellpadding='4'  width='100%'>";
                            print "<thead>";
                                print "<tr>";
                                    print "<th align='left'>Name</th>";
                                    print "<th align='left'>Surname</th>";
                                    print "<th align='left'>Email</th>";
                                    print "<th align='left'>Branch</th> ";
                                    print "<th align='left'>Address</th> ";
                                print "</tr>";
                            print "</thead>";
                            print "<tbody>";  
                                    $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
                                    $sqlres = mysqli_query($GLOBALS["link"],$sql);
                                    $row = mysqli_fetch_assoc($sqlres);
                                    $channeltype = $row['type']; 

                                    $query  = "SELECT `first`, `last`, `system_users`.email, `branches`.branch, `branches`.address";
                                    $query .= " FROM `system_users`";
                                    $query .= " INNER JOIN `approval_users` ON `system_users`.id = `approval_users`.member";
                                    $query .= " INNER JOIN `branches` ON `branches`.id = `system_users`.branch_id";
                                    $query .= " INNER JOIN `channels` ON `channels`.id = `branches`.channel";
                                    $query .= " WHERE  `approval_users`.status='1' AND `channels`.type='".$channeltype."'";
                                    $result = mysqli_query($GLOBALS["link"],$query);
                                    while ($arr = mysqli_fetch_assoc($result)) {
                                        print "<tr>";
                                            print '<td>' . $arr['first'] . '</td>';
                                            print '<td>' . $arr['last'] .'</td>';
                                            print '<td>' . $arr['email'] .'</td>';
                                            print '<td>' . $arr['branch'] .'</td>';
                                            print '<td>' . $arr['address'] .'</td>';                                 
                                        print "</tr>";
                                    }                  
                            print "</tbody>";
                            print "</table>";
                            print '<script type="text/javascript" language="javascript" class="init">
                                $(document).ready(function() {
                                    $("#viewApprovalusersetup").dataTable( {
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
                            </script>';
                print "</body>";
                print "<table id='addApprovaluserssetup' name='addApprovaluserssetup' cellspacing='0' cellpadding='4' border='0' width='100%'>";
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
                                print "Select Branch:";
                                print "<input type='hidden' id='branch_id' style='width: 200px' />"; 
                            print "</td>";
                             print "<td>";   
                                $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
                                $sqlres = mysqli_query($GLOBALS["link"],$sql);
                                $row = mysqli_fetch_assoc($sqlres);
                                $channeltype = $row['type'];  
                                print "<select id='branchapproval'>";
                                    print "<option value='' selected='selected'>[Please select]</option>";
                                    $query  = "SELECT  `branches`.id,`branch`";
                                    $query .= " FROM `branches` INNER JOIN channels ON channels.id = branches.channel WHERE `channels`.type='".$channeltype."'";
                                    $query .= " ORDER BY `branches`.id";
                                    $result = mysqli_query($GLOBALS["link"],$query);
                                    while ($arr = mysqli_fetch_assoc($result)) {
                                        $Branch = $arr['branch'];
                                        print "<option id='".$arr['id']."' value='".htmlentities($Branch)."'>";
                                            print ucfirst($Branch);
                                        print "</option>";
                                    }
                                print "</select>";
                            print "</td>";
                         print "</tr>"; 
                        print "<tr>";
                            print "<td colspan='2' style='text-align: right; font-weight: 400; color: #111111; padding-right: 20px;'>";                                 
                                print "Select Approval Member:";
                                print "<input type='hidden' id='system_users_id' style='width: 200px' />"; 
                            print "</td>"; 
                            print "<td>";     
                                print "<select id='systemapproval'>";
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
                                            print "<input type='text' id='systemFirst' style='width: 200px' readonly/>";        
                                         print "</td>";
                                     print "</tr>";

                                      print "<tr>";
                                         print "<td>";
                                            print "Surname";
                                         print "</td>";
                                         print "<td>";
                                            print "<input type='text' id='systemLast' style='width: 200px' readonly/>";        
                                         print "</td>";
                                     print "</tr>";

                                      print "<tr>";
                                         print "<td>";
                                            print "Email";
                                         print "</td>";
                                         print "<td>";
                                            print "<input type='text' id='systemEmail' style='width: 200px' readonly />";        
                                         print "</td>";
                                     print "</tr>";

                                       print "<tr>";
                                         print "<td>";
                                            print "Branch";
                                         print "</td>";
                                         print "<td>";
                                            print "<input type='text' id='systemBranch' style='width: 200px' readonly/>";        
                                         print "</td>";
                                     print "</tr>";

                                    print "<tr>";
                                         print "<td>";
                                            print "Address";
                                         print "</td>";
                                         print "<td>";
                                            print "<textarea id='systemAddress' style='width: 98%; height: 50px; resize: none; overflow: auto;' maxlength='254' readonly></textarea>";           
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
                                    print "<span id='ok-message-approval' style='color:green'></span><span id='error-message-approval'style='color:red'></span>";
                                    print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button class='ibutton' id='add-approval-settings-button'>Add Approval User</button>";
                                    print "<br/><br/>";
                                print "</td>";
                            print "</tr>";
                        print "</tfoot>";
                print "</table>";
                print "<table id='editApprovaluserssetup' name='editApprovaluserssetup' cellspacing='0' cellpadding='4' border='0' width='100%'>";
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
                                print "Select Branch:";
                                print "<input type='hidden' id='branch_id_edit' style='width: 200px' />"; 
                            print "</td>";
                             print "<td>"; 
                                $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
                                $sqlres = mysqli_query($GLOBALS["link"],$sql);
                                $row = mysqli_fetch_assoc($sqlres);
                                $channeltype = $row['type'];     
                                print "<select id='branchapproval_edit'>";
                                    print "<option value='' selected='selected'>[Please select]</option>";
                                    $query  = "SELECT  `branches`.id,`branch`";
                                    $query .= " FROM `branches` INNER JOIN channels ON channels.id = branches.channel WHERE `channels`.type='".$channeltype."'";
                                    $query .= " ORDER BY `branches`.id";
                                    $result = mysqli_query($GLOBALS["link"],$query);
                                    while ($arr = mysqli_fetch_assoc($result)) {
                                        $Branch = $arr['branch'];
                                        print "<option id='".$arr['id']."' value='".htmlentities($Branch)."'>";
                                            print ucfirst($Branch);
                                        print "</option>";
                                    }
                                print "</select>";
                            print "</td>";
                         print "</tr>"; 
                            print "<tr>";
                            print "<td colspan='2' style='text-align: right; font-weight: 400; color: #111111; padding-right: 20px;'>";                                 
                                print "Select Approval Member:";
                                print "<input type='hidden' id='system_users_id_edit' style='width: 200px' />"; 
                            print "</td>"; 
                            print "<td>";     
                                print "<select id='systemapproval_edit'>";
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
                                            print "<input type='text' id='systemFirst_edit' style='width: 200px' readonly/>";        
                                         print "</td>";
                                     print "</tr>";

                                      print "<tr>";
                                         print "<td>";
                                            print "Surname";
                                         print "</td>";
                                         print "<td>";
                                            print "<input type='text' id='systemLast_edit' style='width: 200px' readonly/>";        
                                         print "</td>";
                                     print "</tr>";

                                      print "<tr>";
                                         print "<td>";
                                            print "Email";
                                         print "</td>";
                                         print "<td>";
                                            print "<input type='text' id='systemEmail_edit' style='width: 200px' readonly />";        
                                         print "</td>";
                                     print "</tr>";

                                       print "<tr>";
                                         print "<td>";
                                            print "Branch";
                                         print "</td>";
                                         print "<td>";
                                            print "<input type='text' id='systemBranch_edit' style='width: 200px' readonly/>";        
                                         print "</td>";
                                     print "</tr>";

                                    print "<tr>";
                                         print "<td>";
                                            print "Address";
                                         print "</td>";
                                         print "<td>";
                                            print "<textarea id='systemAddress_edit' style='width: 98%; height: 50px; resize: none; overflow: auto;' maxlength='254' readonly></textarea>";           
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
                                    print "<span id='ok-message-approval-delete' style='color:green'></span><span id='error-message-approval-delete'style='color:red'></span>";
                                    print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button class='ibutton' id='delete-approval-settings-button' style='color:red'>Delete Approval User</button>";
                                    print "<br/><br/>";
                                print "</td>";
                            print "</tr>";
                        print "</tfoot>";
                print "</table>";
                print "</div>";  
                // = == = = ==== == == ===== == = == == ==== == == = = ==  == = = ==== = == 
                print "<div id='quoteroomdiv'>";
                print "<p style='float:right'><a href='#' id='viewRooms' name='viewRooms'><u>View</u></a> | <a href='#' id='editRooms' name='editRooms'><u>Edit</u></a> | <a href='#' id='addRooms' name='addRooms'><u>Add</u></a>";
                print "
                        <body>
                            <table id='viewRoomsetup' name='viewRoomsetup' cellspacing='0' cellpadding='4'  width='100%'>";
                            print "<thead>";
                                print "<tr>";
                                    print "<th align='left'>Room</th>";
                                    print "<th align='left'>Quote Type</th>";
                                print "</tr>";
                            print "</thead>";
                            print "<tbody>";  
                                    $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
                                    $sqlres = mysqli_query($GLOBALS["link"],$sql);
                                    $row = mysqli_fetch_assoc($sqlres);
                                    $channeltype = $row['type'];
        
                                    $query  = "SELECT `room_type`, `quote_type`";
                                    $query .= " FROM `quote_room_settings`";
                                    $query .= " WHERE  `deleted`= 0 AND quote_type='".  $channeltype."'";
                                    $result = mysqli_query($GLOBALS["link"],$query);
                                    while ($arr = mysqli_fetch_assoc($result)) {
                                        print "<tr>";
                                            print '<td width="50%">' . $arr['room_type'] . '</td>';
                                            print '<td width="50%">' . $arr['quote_type'] .'</td>';                                
                                        print "</tr>";
                                    }                  
                            print "</tbody>";
                            print "</table>";
                            print '<script type="text/javascript" language="javascript" class="init">
                                $(document).ready(function() {
                                    $("#viewRoomsetup").dataTable( {
                                        columnDefs: [ {
                                            targets: [ 0 ],
                                            orderData: [ 0, 1 ]
                                        }, {
                                            targets: [ 1 ],
                                            orderData: [ 1, 0 ]
                                        } ],
                                        "order": [[ 1, "desc" ]]
                                    } );
                                } );
                            </script>';
                print "</body>";

                $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
                $sqlres = mysqli_query($GLOBALS["link"],$sql);
                $row = mysqli_fetch_assoc($sqlres);
                $channeltype = $row['type'];
                print "<table id='addRoomsetup' name='addRoomsetup' cellspacing='0' cellpadding='4' border='0' width='100%'>";
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
                                 if ($channeltype != "Retail"){
                                    print "<tr>";
                                         print "<td>";
                                            print "Floor Type";
                                         print "</td>";
                                         print "<td>";
                                            print "<input type='text' id='systemFloor' style='width: 200px' />";        
                                         print "</td>";
                                    print "</tr>";
                                }else{
                                    print "<tr>";
                                         print "<td>";
                                         print "</td>";
                                         print "<td>";
                                            print "<input type='hidden' id='systemFloor' style='width: 200px'/>";        
                                         print "</td>";
                                    print "</tr>";
                                }

                                    print "<tr>";
                                         print "<td>";
                                            print "Room";
                                         print "</td>";
                                         print "<td>";
                                            print "<input type='text' id='systemRoom' style='width: 200px' />";        
                                         print "</td>";
                                    print "</tr>";

                                    if ($channeltype != "Retail"){
                                        print "<tr>";
                                             print "<td>";
                                                print "Property Type";
                                                print "<input type='hidden' id='property_id_room' style='width: 200px' />"; 
                                             print "</td>";
                                             print "<td>";
                                                print "<select id='property_type_room' >";
                                                    print "<option value='' selected='selected'>[Please select]</option>";
          
                                                    $query  = "SELECT *";
                                                    $query .= " FROM `property_types`";
                                                    $result = mysqli_query($GLOBALS["link"],$query);
                                                    while ($arr = mysqli_fetch_assoc($result)) {
                                                        $Property = $arr['Type'];
                                                        print "<option id='".$arr['id']."' value='".$arr['id']."'>";
                                                            print ucfirst($Property);
                                                        print "</option>";
                                                    }
                                                print "</select>";       
                                             print "</td>";
                                        print "</tr>";
                                    }else{
                                        print "<tr>";
                                             print "<td>";
                                                //print "Property Type";
                                             print "</td>";
                                             print "<td>";
                                                print "<input type='hidden' id='property_id_room' style='width: 200px' value ='0'/>"; 
                                            print "</td>";
                                        print "</tr>";
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
                                    print "<span id='ok-message-room' style='color:green'></span><span id='error-message-room'style='color:red'></span>";
                                    print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button class='ibutton' id='add-room-settings-button'>Add Room</button>";
                                    print "<br/><br/>";
                                print "</td>";
                            print "</tr>";
                        print "</tfoot>";
                print "</table>";
                print "<table id='editRoomsetup' name='editRoomsetup' cellspacing='0' cellpadding='4' border='0' width='100%'>";
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
                                print "Select Room:";
                                print "<input type='hidden' id='room_id_edit' style='width: 200px' />"; 
                            print "</td>";
                             print "<td>";     
                                print "<select id='roomtype_edit'>";
                                    print "<option value='' selected='selected'>[Please select]</option>";

                                    $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
                                    $sqlres = mysqli_query($GLOBALS["link"],$sql);
                                    $row = mysqli_fetch_assoc($sqlres);
                                    $channeltype = $row['type'];
        
                                    $query  = "SELECT `id`,`room_type`";
                                    $query .= " FROM `quote_room_settings`";
                                        $query .= " WHERE  `deleted`= 0 AND quote_type='".  $channeltype."'";
                                    $result = mysqli_query($GLOBALS["link"],$query);
                                    while ($arr = mysqli_fetch_assoc($result)) {
                                        $Room = $arr['room_type'];
                                        print "<option id='".$arr['id']."' value='".$Room."'>";
                                            print ucfirst($Room);
                                        print "</option>";
                                    }
                                print "</select>";
                            print "</td>";
                        print "</tr>"; 
                        print "<tr>";
                            print "<td colspan='4' style='text-align: left; padding: 0'>";
                             print "<div>";
                                 print "<table cellpadding='10'>";

                                    if ($channeltype !== "Retail"){
                                         print "<tr>";
                                             print "<td>";
                                                print "Floor Type";
                                             print "</td>";
                                             print "<td>";
                                                print "<input type='text' id='systemFloor_edit' style='width: 200px' readonly/>";        
                                             print "</td>";
                                         print "</tr>";
                                    }else{
                                        print "<tr>";
                                             print "<td>";
                                             print "</td>";
                                             print "<td>";
                                                print "<input type='hidden' id='systemFloor_edit' style='width: 200px'/>";        
                                             print "</td>";
                                        print "</tr>";
                                    }

                                     print "<tr>";
                                         print "<td>";
                                            print "Room";
                                         print "</td>";
                                         print "<td>";
                                            print "<input type='text' id='systemRoom_edit' style='width: 200px' />";        
                                         print "</td>";
                                     print "</tr>";

                                    if ($channeltype != "Retail"){
                                        print "<tr>";
                                            print "<td>";
                                                print "Property Type";
                                            print "</td>";
                                            print "<td>";
                                                print "<select id='systemProperty_edit'>";
                                                    print "<option value='' selected='selected'>[Please select]</option>";
          
                                                    $query  = "SELECT *";
                                                    $query .= " FROM `property_types`";
                                                    $result = mysqli_query($GLOBALS["link"],$query);
                                                    while ($arr = mysqli_fetch_assoc($result)) {
                                                        $Property = $arr['Type'];
                                                        print "<option id='".$arr['id']."' value='".$arr['id']."'>";
                                                            print ucfirst($Property);
                                                        print "</option>";
                                                    }
                                                print "</select>";         
                                            print "</td>";
                                        print "</tr>";
                                    }else{
                                        print "<tr>";
                                             print "<td>";
                                                //print "Property Type";
                                             print "</td>";
                                             print "<td>";
                                                print "<input type='hidden' id='systemProperty_edit' style='width: 200px' value ='0'/>"; 
                                            print "</td>";
                                        print "</tr>";

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
                                    print "<span id='ok-message-room-delete' style='color:green'></span><span id='error-message-room-delete'style='color:red'></span>";
                                    print "<span id='ok-message-room-save' style='color:green'></span><span id='error-message-room-save'style='color:red'></span>";                 
                                    print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button class='ibutton' id='save-room-settings-button'>Save Room</button>";
                                    print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button class='ibutton' id='delete-room-settings-button' style='color:red'>Delete Room</button>";
                                    print "<br/><br/>";
                                print "</td>";
                            print "</tr>";
                        print "</tfoot>";
                print "</table>";
                print "</div>";
                // = == = = ==== == == ===== == = == == ==== == == = = ==  == = = ==== = ==
                print "<div id='quoteescalatediv'>";
                print "<p style='float:right'><a href='#' id='viewEscalationusers' name='viewEscalationusers'><u>View</u></a> | <a href='#' id='editEscalationusers' name='editEscalationusers'><u>Edit</u></a> | <a href='#' id='addEscalationusers' name='addEscalationusers'><u>Add</u></a>";     
                print "
                        <body>
                            <table id='viewEscalationusersetup' name='viewEscalationusersetup' cellspacing='0' cellpadding='4'  width='100%'>";
                            print "<thead>";
                                print "<tr>";
                                    print "<th align='left'>Name</th>";
                                    print "<th align='left'>Surname</th>";
                                    print "<th align='left'>Email</th>";
                                    print "<th align='left'>Branch</th> ";
                                    print "<th align='left'>Address</th> ";
                                    print "<th align='left'>Time Escalation</th>";
                                print "</tr>";
                            print "</thead>";
                            print "<tbody>";  
                                    $query  = "SELECT `first`, `last`, `system_users`.email, `branches`.branch, `branches`.address, `time_escalation`";
                                    $query .= " FROM `system_users`";
                                    $query .= " INNER JOIN `escalation_users` ON `system_users`.id = `escalation_users`.member";
                                    $query .= " INNER JOIN `branches` ON `branches`.id = `system_users`.branch_id ";
                                    $query .= " WHERE  `status`= 1";
                                    $result = mysqli_query($GLOBALS["link"],$query);
                                    while ($arr = mysqli_fetch_assoc($result)) {
                                        print "<tr>";
                                            print '<td>' . $arr['first'] . '</td>';
                                            print '<td>' . $arr['last'] .'</td>';
                                            print '<td>' . $arr['email'] .'</td>';
                                            print '<td>' . $arr['branch'] .'</td>';
                                            print '<td>' . $arr['address'] .'</td>';
                                            print '<td>' . $arr['time_escalation'] .'</td>';                                                                  
                                        print "</tr>";
                                    }                  
                            print "</tbody>";
                            print "</table>";
                            print '<script type="text/javascript" language="javascript" class="init">
                                $(document).ready(function() {
                                    $("#viewEscalationusersetup").dataTable( {
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
                                        "order": [[ 4, "desc" ]]
                                    } );
                                } );
                            </script>';
                print "</body>";
                print "<table id='addEscalationusersetup' name='addEscalationusersetup' cellspacing='0' cellpadding='4' border='0' width='100%'>";
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
                            print "Select Branch:";
                            print "<input type='hidden' id='branch_escalation_id' style='width: 200px' />"; 
                        print "</td>";
                         print "<td>";     
                            print "<select id='branchescalation'>";
                                print "<option value='' selected='selected'>[Please select]</option>";
                                $query  = "SELECT  `id`,`branch`";
                                $query .= " FROM `branches`";
                                $query .= " ORDER BY `id`";
                                $result = mysqli_query($GLOBALS["link"],$query);
                                while ($arr = mysqli_fetch_assoc($result)) {
                                    $Branch = $arr['branch'];
                                    print "<option id='".$arr['id']."' value='".htmlentities($Branch)."'>";
                                        print ucfirst($Branch);
                                    print "</option>";
                                }
                            print "</select>";
                        print "</td>";
                     print "</tr>"; 
                    print "<tr>";
                        print "<td colspan='2' style='text-align: right; font-weight: 400; color: #111111; padding-right: 20px;'>";                                 
                            print "Select Escalation Member:";
                            print "<input type='hidden' id='systemescalation_users_id' style='width: 200px' />";
                        print "</td>"; 
                        print "<td>";     
                            print "<select id='systemescalation'>";
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
                                        print "<input type='text' id='systemescalationFirst' style='width: 200px' readonly/>";        
                                     print "</td>";
                                 print "</tr>";

                                  print "<tr>";
                                     print "<td>";
                                        print "Surname";
                                     print "</td>";
                                     print "<td>";
                                        print "<input type='text' id='systemescalationLast' style='width: 200px' readonly/>";        
                                     print "</td>";
                                 print "</tr>";

                                  print "<tr>";
                                     print "<td>";
                                        print "Email";
                                     print "</td>";
                                     print "<td>";
                                        print "<input type='text' id='systemescalationEmail' style='width: 200px' readonly />";        
                                     print "</td>";
                                 print "</tr>";

                                   print "<tr>";
                                     print "<td>";
                                        print "Branch";
                                     print "</td>";
                                     print "<td>";
                                        print "<input type='text' id='systemescalationBranch' style='width: 200px' readonly/>";        
                                     print "</td>";
                                 print "</tr>";

                                print "<tr>";
                                     print "<td>";
                                        print "Address";
                                     print "</td>";
                                     print "<td>";
                                        print "<textarea id='systemescalationAddress' style='width: 98%; height: 50px; resize: none; overflow: auto;' maxlength='254' readonly></textarea>";           
                                     print "</td>";
                                print "</tr>";

                                print "<tr>";
                                         print "<td>";
                                            print "Hours to Escalate";
                                         print "</td>";
                                         print "<td>";
                                            print "<input type='text' id='timescalation' style='width: 200px' />";        
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
                                print "<span id='ok-message-escalation' style='color:green'></span><span id='error-message-escalation'style='color:red'></span>";
                                print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button class='ibutton' id='add-escalation-settings-button'>Add Escalation User</button>";
                                print "<br/><br/>";
                            print "</td>";
                        print "</tr>";
                    print "</tfoot>";
                print "</table>";
                print "<table id='editEscalationusersetup' name='editEscalationusersetup' cellspacing='0' cellpadding='4' border='0' width='100%'>";
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
                                print "Select Branch:";
                                print "<input type='hidden' id='branchescalation_id_edit' style='width: 200px' />"; 
                            print "</td>";
                             print "<td>";   

                                print "<select id='branchescalation_edit'>";
                                    print "<option value='' selected='selected'>[Please select]</option>";
                                    $query  = "SELECT  `id`,`branch`";
                                    $query .= " FROM `branches`";
                                    $query .= " ORDER BY `id`";
                                    $result = mysqli_query($GLOBALS["link"],$query);
                                    while ($arr = mysqli_fetch_assoc($result)) {
                                        $Branch = $arr['branch'];
                                        print "<option id='".$arr['id']."' value='".htmlentities($Branch)."'>";
                                            print ucfirst($Branch);
                                        print "</option>";
                                    }
                                print "</select>";
                            print "</td>";
                         print "</tr>"; 
                            print "<tr>";
                            print "<td colspan='2' style='text-align: right; font-weight: 400; color: #111111; padding-right: 20px;'>";                                 
                                print "Select Escalation Member:";
                                print "<input type='hidden' id='systemescalation_users_id_edit' style='width: 200px' />"; 
                            print "</td>"; 
                            print "<td>";     
                                print "<select id='systemescalation_edit'>";
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
                                            print "<input type='text' id='systemescalationFirst_edit' style='width: 200px' readonly/>";        
                                         print "</td>";
                                     print "</tr>";

                                      print "<tr>";
                                         print "<td>";
                                            print "Surname";
                                         print "</td>";
                                         print "<td>";
                                            print "<input type='text' id='systemescalationLast_edit' style='width: 200px' readonly/>";        
                                         print "</td>";
                                     print "</tr>";

                                      print "<tr>";
                                         print "<td>";
                                            print "Email";
                                         print "</td>";
                                         print "<td>";
                                            print "<input type='text' id='systemescalationEmail_edit' style='width: 200px' readonly />";        
                                         print "</td>";
                                     print "</tr>";

                                       print "<tr>";
                                         print "<td>";
                                            print "Branch";
                                         print "</td>";
                                         print "<td>";
                                            print "<input type='text' id='systemescalationranch_edit' style='width: 200px' readonly/>";        
                                         print "</td>";
                                     print "</tr>";

                                    print "<tr>";
                                         print "<td>";
                                            print "Address";
                                         print "</td>";
                                         print "<td>";
                                            print "<textarea id='systemescalationAddress_edit' style='width: 98%; height: 50px; resize: none; overflow: auto;' maxlength='254' readonly></textarea>";           
                                         print "</td>";
                                    print "</tr>";


                                    print "<tr>";
                                         print "<td>";
                                            print "Hours to Escalate";
                                         print "</td>";
                                         print "<td>";
                                            print "<input type='text' id='timescalation_edit' style='width: 200px' readonly/>";        
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
                                    print "<span id='ok-message-escalation-delete' style='color:green'></span><span id='error-message-escalation-delete'style='color:red'></span>";
                                    print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button class='ibutton' id='delete-escalation-settings-button' style='color:red'>Delete Escalation User</button>";
                                    print "<br/><br/>";
                                print "</td>";
                            print "</tr>";
                        print "</tfoot>";
                print "</table>";
                print "</div>";
                // = == = = ==== == == ===== == = == == ==== == == = = ==  == = = ==== = ==
                if ($channeltype !== "Retail"){
                    print "<div id='propertydiv'>";
                    print "<p style='float:right'><a href='#' id='viewProperty' name='viewProperty'><u>View</u></a> | <a href='#' id='editProperties' name='editProperties'><u>Edit</u></a> | <a href='#' id='addProperty' name='addProperty'><u>Add</u></a>";
                    print "
                            <body>
                                <table id='viewPropertysetup' name='viewPropertysetup' cellspacing='0' cellpadding='4'  width='100%'>";
                                print "<thead>";
                                    print "<tr>";
                                        print "<th align='left'>Date created</th>";
                                        print "<th align='left'>Property</th>";
                                    print "</tr>";
                                print "</thead>";
                                print "<tbody>";  
                                        $query  = "SELECT *";
                                        $query .= " FROM `property_types`";
                                        $result = mysqli_query($GLOBALS["link"],$query);
                                        while ($arr = mysqli_fetch_assoc($result)) {
                                            print "<tr>";
                                                print '<td width="50%">' . date("Y-m-d", strtotime($arr['date_created'])). '</td>';
                                                print '<td width="50%">' . $arr['Type'] .'</td>';                                
                                            print "</tr>";
                                        }                  
                                print "</tbody>";
                                print "</table>";
                                print '<script type="text/javascript" language="javascript" class="init">
                                    $(document).ready(function() {
                                        $("#viewPropertysetup").dataTable( {
                                            columnDefs: [ {
                                                targets: [ 0 ],
                                                orderData: [ 0, 1 ]
                                            }, {
                                                targets: [ 1 ],
                                                orderData: [ 1, 0 ]
                                            } ],
                                            "order": [[ 0, "desc" ]]
                                        } );
                                    } );
                                </script>';
                    print "</body>";
                    print "<table id='addPropertysetup' name='addPropertysetup' cellspacing='0' cellpadding='4' border='0' width='100%'>";
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
                                                print "Property Type";
                                             print "</td>";
                                             print "<td>";
                                                print "<input type='text' id='systemProperty' style='width: 200px' />";        
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
                                        print "<span id='ok-message-property' style='color:green'></span><span id='error-message-property'style='color:red'></span>";
                                        print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button class='ibutton' id='add-property-settings-button'>Add Property</button>";
                                        print "<br/><br/>";
                                    print "</td>";
                                print "</tr>";
                            print "</tfoot>";
                    print "</table>";
                    print "<table id='editPropertysetup' name='editPropertysetup' cellspacing='0' cellpadding='4' border='0' width='100%'>";
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
                                    print "Select Property:";
                                    print "<input type='hidden' id='property_id_edit' style='width: 200px' />"; 
                                print "</td>";
                                 print "<td>";     
                                    print "<select id='property_edit'>";
                                        print "<option value='' selected='selected'>[Please select]</option>";
            
                                        $query  = "SELECT *";
                                        $query .= " FROM `property_types`";
                                        $result = mysqli_query($GLOBALS["link"],$query);
                                        while ($arr = mysqli_fetch_assoc($result)) {
                                            $Property = $arr['Type'];
                                            print "<option id='".$arr['id']."' value='".$Property."'>";
                                                print ucfirst($Property);
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
                                                print "Property Type";
                                             print "</td>";
                                             print "<td>";
                                                print "<input type='text' id='systemProperty_edit' style='width: 200px' readonly/>";        
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
                                        print "<span id='ok-message-property-delete' style='color:green'></span><span id='error-message-property-delete'style='color:red'></span>";
                                        print "<span id='ok-message-property-save' style='color:green'></span><span id='error-message-property-save'style='color:red'></span>";      
                                        print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button class='ibutton' id='save-property-settings-button'>Save Property</button>";
                                        print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button class='ibutton' id='delete-property-settings-button' style='color:red'>Delete Property</button>";
                                        print "<br/><br/>";
                                    print "</td>";
                                print "</tr>";
                            print "</tfoot>";
                    print "</table>";
                    print "</div>";
                }
                // = == = = ==== == == ===== == = == == ==== == == = = ==  == = = ==== = ==
                print "<div id='floorplansdiv'>";
                print "<p style='float:right'><a href='#' id='viewFloorPlan' name='viewFloorPlan'><u>View</u></a> | <a  href='#' id='editFloorPlan' name='editFloorPlan'><u>Edit</u></a> | <a href='#' id='addFloorPlan' name='addFloorPlan'><u>Add</u></a>";
                print "
                        <body>
                            <table id='viewFloorPlansetup' name='viewFloorPlansetup' cellspacing='0' cellpadding='4'  width='100%'>";
                            print "<thead>";
                                print "<tr>";
                                    print "<th align='left'>Date created</th>";
                                    print "<th align='left'>Name</th>";
                                    print "<th align='left'>Description</th>";
                                print "</tr>";
                            print "</thead>";
                            print "<tbody>";  
                                    $query  = "SELECT *";
                                    $query .= " FROM `floor_plan_settings`";
                                    $result = mysqli_query($GLOBALS["link"],$query);
                                    while ($arr = mysqli_fetch_assoc($result)) {
                                        print "<tr>";
                                            print '<td width="50%">' . date("Y-m-d", strtotime($arr['date_created'])). '</td>';
                                            print '<td width="50%">' . $arr['name'] .'</td>';  
                                            print '<td width="50%">' . $arr['description'] .'</td>';                                                                
                                        print "</tr>";
                                    }                  
                            print "</tbody>";
                            print "</table>";
                            print '<script type="text/javascript" language="javascript" class="init">
                                $(document).ready(function() {
                                    $("#viewFloorPlansetup").dataTable( {
                                        columnDefs: [ {
                                            targets: [ 0 ],
                                            orderData: [ 0, 1 ]
                                        }, {
                                            targets: [ 1 ],
                                            orderData: [ 1, 0 ]
                                        } ],
                                        "order": [[ 0, "desc" ]]
                                    } );
                                } );
                            </script>';
                print "<table id='addFloorPlansetup' name='addFloorPlansetup' cellspacing='0' cellpadding='4' border='0' width='100%'>";
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
                                            print "<input type='text' id='systemfloorplanName' style='width: 200px' />";
                                         print "</td>";
                                    print "</tr>";
                                    print "<tr>";
                                         print "<td>";
                                            print "Description";
                                         print "</td>";
                                         print "<td>";
                                            print "<textarea id='systemfloorplanDescription' style='width: 98%; height: 50px; resize: none; overflow: auto;' maxlength='254'></textarea>";  
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
                                    print "<span id='ok-message-floorplan' style='color:green'></span><span id='error-message-floorplan'style='color:red'></span>";
                                    print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button class='ibutton' id='add-floorplan-settings-button'>Add Document Category</button>";
                                    print "<br/><br/>";
                                print "</td>";
                            print "</tr>";
                        print "</tfoot>";
                print "</table>";
                print "<table id='editFloorPlansetup' name='editFloorPlansetup' cellspacing='0' cellpadding='4' border='0' width='100%'>";
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
                                print "Select Floor Plan:";
                                print "<input type='hidden' id='floor_plan_id_edit' style='width: 200px' />"; 
                            print "</td>";
                             print "<td>";     
                                print "<select id='floorplan_edit'>";
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
                                            print "<input type='text' id='systemFloorPlanName_edit' style='width: 200px' readonly/>";        
                                         print "</td>";
                                     print "</tr>";

                                     print "<tr>";
                                         print "<td>";
                                            print "Description";
                                         print "</td>";
                                         print "<td>";
                                            print "<textarea id='systemFloorPlanDescription_edit' style='width: 98%; height: 50px; resize: none; overflow: auto;' maxlength='254' readonly></textarea>";           
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
                                    print "<span id='ok-message-floorplan-delete' style='color:green'></span><span id='error-message-floorplan-delete'style='color:red'></span>";
                                    print "<span id='ok-message-floorplan-save' style='color:green'></span><span id='error-message-floorplan-save'style='color:red'></span>";
                                    print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button class='ibutton' id='save-floorplan-settings-button'>Save Document Category</button>";
                                    print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button class='ibutton' id='delete-floorplan-settings-button' style='color:red'>Delete Document Category</button>";
                                    print "<br/><br/>";
                                print "</td>";
                            print "</tr>";
                        print "</tfoot>";
                print "</table>";
                print "</div>";
                // = == = = ==== == == ===== == = == == ==== == == = = ==  == = = ==== = ==
                print "<div style='margin: 0 auto; width: 95%; border-bottom: 2px solid #BBBBBB;'>&nbsp;</div>";
                print "</div>";
            
                print "\n<!-- scripts , code below be eval()ed by javascript -->\n";

                print "jQuery(\"#settings-container\").tabs({ selected: 0 });\n";
                //===============================================================
                //Checklist Questions
                print " $('table#editchecklistsetup').hide();
                    jQuery('a#editChecklists').click(function () {
                        $('table#addchecklistsetup').hide(); 
                        $('div#viewchecklistsetup_wrapper').hide();
                        $('table#editchecklistsetup').show(); 
                    });\n";

                print "jQuery('a#viewChecklists').click(function () {
                         $('table#editchecklistsetup').hide();
                         $('table#addchecklistsetup').hide(); 
                         $('div#viewchecklistsetup_wrapper').show(); 
                    });\n";

                print " $('table#addchecklistsetup').hide();
                    jQuery('a#addchecklists').click(function () {
                        $('div#viewchecklistsetup_wrapper').hide();
                        $('table#editchecklistsetup').hide();
                        $('table#addchecklistsetup').show(); 
                    });\n";
                
                print "jQuery('select#checklist').change(function () {
                        var question = $(checklist).val(); 
                        var id = $(checklist).children(':selected').attr('id');               
                        $('input#checklistid').val(id); 
                        AJAXCall('" . __CLASS__ . "', 'getAjaxChecklistInfo', 'questionID='+id);  
                    });\n";

                print "jQuery('#save-checklist-settings-button').click(function () {
                        var id = jQuery('input#checklistid').val();
                        var title  = jQuery('input#checklistitle').val();
                        var description = jQuery('textarea#checklistdescription').val();
                        var type = jQuery('select#checklisttype').val();
                        var mandatory = jQuery('select#checklistmandatory').val();
                        AJAXCallModuleJSOnly('" . __CLASS__ . "', 'saveChecklistSettings', 'id='+id+'&title='+title+'&description='+description+'&type='+type+'&mandatory='+mandatory);
                    });\n";

                print "jQuery('#delete-checklist-settings-button').click(function () {
                        var id = jQuery('input#checklistid').val();
                        var title  = jQuery('input#checklistitle').val();
                        var description = jQuery('textarea#checklistdescription').val();
                        var type = jQuery('input#checklisttype').val();
                        var mandatory = jQuery('input#checklistmandatory').val();
                        AJAXCallModuleJSOnly('" . __CLASS__ . "', 'deleteChecklistSettings', 'id='+id+'&title='+title+'&description='+description+'&type='+type+'&mandatory='+mandatory);
                    });\n";

                print "jQuery('#add-checklist-settings-button').click(function () {
                        var title  = jQuery('input#addchecklistitle').val();
                        var description = jQuery('textarea#addchecklistdescription').val();
                        var type = jQuery('select#addchecklisttype').val();
                        var mandatory = jQuery('select#addchecklistmandatory').val();
                        AJAXCallModuleJSOnly('" . __CLASS__ . "', 'addChecklistSettings', 'title='+title+'&description='+description+'&type='+type+'&mandatory='+mandatory);
                    });\n";
                //===============================================================
                //PDF Settings
                print "jQuery('a#viewPDFSetttings').click(function () {
                        $('div#divEditPDFSetttings').hide();
                        $('div#divViewPDFSetttings').show(); 
                    });\n";

                print " $('div#divEditPDFSetttings').hide();
                    jQuery('a#editPDFSetttings').click(function () {
                         $('div#divViewPDFSetttings').hide();
                        $('div#divEditPDFSetttings').show(); 
                    });\n";

                print "jQuery('#save-pdf-settings-button').click(function () {
                        var id = jQuery('input#pdfUserID').val();
                        var termsCond  = jQuery('textarea#termsCondSetupEdit').val();
                        var additonalCond = jQuery('textarea#additionalCondSetupEdit').val();

                        AJAXCallModuleJSOnly('" . __CLASS__ . "', 'savePDFSettings', 'id='+id+'&termsCond='+termsCond+'&additonalCond='+additonalCond);
                    });\n";
                //===============================================================
                //Disclaimer Settings
                print "jQuery('a#viewDisclaimerSetttings').click(function () {
                        $('div#divEditDisclaimerSetttings').hide();
                        $('div#divViewDisclaimerSetttings').show(); 
                    });\n";

                print " $('div#divEditDisclaimerSetttings').hide();
                    jQuery('a#editDisclaimerSetttings').click(function () {
                         $('div#divViewDisclaimerSetttings').hide();
                        $('div#divEditDisclaimerSetttings').show(); 
                    });\n";
                //===============================================================
                //Approval users
                print "$('table#addApprovaluserssetup').hide();
                    jQuery('a#addApprovalusers').click(function () {
                         $('div#viewApprovalusersetup_wrapper').hide();
                         $('table#editApprovaluserssetup').hide();
                        $('table#addApprovaluserssetup').show(); 
                    });\n
                ";
                print "$('table#editApprovaluserssetup').hide();
                    jQuery('a#editApprovalusers').click(function () {
                         $('div#viewApprovalusersetup_wrapper').hide();
                         $('table#addApprovaluserssetup').hide();
                        $('table#editApprovaluserssetup').show(); 
                    });\n";

                print "jQuery('a#viewApprovalusers').click(function () {
                        $('table#editApprovaluserssetup').hide();
                        $('table#addApprovaluserssetup').hide();
                        $('div#viewApprovalusersetup_wrapper').show(); 
                    });\n";

                print "jQuery('#add-approval-settings-button').click(function () {
                        var member  = jQuery('input#system_users_id').val(); 
                        var branch =  $(branchapproval).children(':selected').attr('id');  
                        AJAXCallModuleJSOnly('" . __CLASS__ . "', 'saveApprovalUserSettings', 'member='+member+'&branch='+branch);
                    });\n";

                print "jQuery('#delete-approval-settings-button').click(function () {
                        var member  = jQuery('input#system_users_id_edit').val(); 
                        AJAXCallModuleJSOnly('" . __CLASS__ . "', 'deleteApprovalUserSettings', 'member='+member);
                    });\n";

                print "jQuery('select#branchapproval').change(function () { 
                        var id = $(branchapproval).children(':selected').attr('id');               
                        $('input#branch_id').val(id); 
                        AJAXCallApprovalBranch('" . __CLASS__ . "', 'getApprovalBranchUserInfo', 'branchID='+id);  
                    });\n";

                print "jQuery('select#branchapproval_edit').change(function () { 
                        var id = $(branchapproval_edit).children(':selected').attr('id');               
                        $('input#branch_id_edit').val(id); 
                        AJAXCallApprovalMembers('" . __CLASS__ . "', 'getBranchApprovalMembers', 'branchID='+id);  
                    });\n";

                print "jQuery('select#systemapproval').change(function () { 
                        var id = $(systemapproval).children(':selected').attr('id');               
                        $('input#system_users_id').val(id); 
                        AJAXCallApproval('" . __CLASS__ . "', 'getApprovalUserInfo', 'systemID='+id);  
                    });\n";

                print "jQuery('select#systemapproval_edit').change(function () {
                        var id = $(systemapproval_edit).children(':selected').attr('id');               
                        $('input#system_users_id_edit').val(id); 
                        AJAXCallApprovalDelete('" . __CLASS__ . "', 'getApprovalUserInfoEdit', 'systemID='+id);  
                    });\n";
                //===============================================================
                //Quote Room Settings
                print "$('table#addRoomsetup').hide();
                    jQuery('a#addRooms').click(function () {
                         $('div#viewRoomsetup_wrapper').hide();
                         $('table#editRoomsetup').hide();
                        $('table#addRoomsetup').show(); 
                    });\n";
                
                print "$('table#editRoomsetup').hide();
                    jQuery('a#editRooms').click(function () {
                         $('div#viewRoomsetup_wrapper').hide();
                         $('table#addRoomsetup').hide();
                        $('table#editRoomsetup').show(); 
                    });\n";

                print "jQuery('a#viewRooms').click(function () {
                         $('table#editRoomsetup').hide();
                         $('table#addRoomsetup').hide();
                        $('div#viewRoomsetup_wrapper').show(); 
                    });\n";

                print "jQuery('#add-room-settings-button').click(function () {
                        String.prototype.capitalize = function() {
                            return this.charAt(0).toUpperCase() + this.slice(1);
                        }
                        var Floor  = jQuery('input#systemFloor').val().capitalize();
                        var Room = jQuery('input#systemRoom').val().capitalize();
                        var property = $('select#property_type_room').children(':selected').attr('id'); 
                        var roomtype = Floor + '\^' + Room;
                        AJAXCallModuleJSOnly('" . __CLASS__ . "', 'addRoomSettings', 'roomtype='+roomtype+'&property='+property);
                    });\n";

                print "jQuery('select#roomtype_edit').change(function () { 
                        var id = $(roomtype_edit).children(':selected').attr('id');               
                        $('input#room_id_edit').val(id); 
                        AJAXCallRoom('" . __CLASS__ . "', 'getRoomInfo', 'roomID='+id);  
                    });\n ";
                
                print "jQuery('#save-room-settings-button').click(function () {
                        String.prototype.capitalize = function() {
                            return this.charAt(0).toUpperCase() + this.slice(1);
                        }
                        var Floor  = jQuery('input#systemFloor_edit').val().capitalize();
                        var Room = jQuery('input#systemRoom_edit').val().capitalize();

                        var property ='';
                        if(Floor =='')property = jQuery('input#systemProperty_edit').val(); 
                        if(Floor !='')property = jQuery('select#systemProperty_edit').children(':selected').attr('id'); 
                    

                        //var property = $('select#systemProperty_edit').children(':selected').attr('id'); 
                        var roomtype = Floor + '\^' + Room;
                        var id  = jQuery('input#room_id_edit').val();
                        AJAXCallModuleJSOnly('" . __CLASS__ . "', 'saveRoomSettings', 'id='+id+'&roomtype='+roomtype+'&property='+property);
                    });\n";
                print "jQuery('#delete-room-settings-button').click(function () {
                        var room  = jQuery('input#room_id_edit').val(); 
                        AJAXCallModuleJSOnly('" . __CLASS__ . "', 'deleteRoomSettings', 'room='+room);
                    });\n";
                //===============================================================
                //Escalation users
                print "$('table#addEscalationusersetup').hide();
                    jQuery('a#addEscalationusers').click(function () {
                         $('div#viewEscalationusersetup_wrapper').hide();
                         $('table#editEscalationusersetup').hide();
                        $('table#addEscalationusersetup').show(); 
                    });\n";

                print "$('table#editEscalationusersetup').hide();
                    jQuery('a#editEscalationusers').click(function () {
                         $('div#viewEscalationusersetup_wrapper').hide();
                         $('table#addEscalationusersetup').hide();
                        $('table#editEscalationusersetup').show(); 
                    });\n";

                print "jQuery('a#viewEscalationusers').click(function () {
                         $('table#editEscalationusersetup').hide();
                         $('table#addEscalationusersetup').hide();
                        $('div#viewEscalationusersetup_wrapper').show(); 
                    });\n";

                print "jQuery('select#branchescalation').change(function () { 
                        var id = $(branchescalation).children(':selected').attr('id');               
                        $('input#branch_escalation_id').val(id); 
                        AJAXCallEscalationBranch('" . __CLASS__ . "', 'getApprovalBranchUserInfo', 'branchID='+id);  
                    });\n";

                print "jQuery('select#systemescalation').change(function () { 
                        var id = $(systemescalation).children(':selected').attr('id');               
                        $('input#systemescalation_users_id').val(id); 
                        AJAXCallEscalation('" . __CLASS__ . "', 'getApprovalUserInfo', 'systemID='+id);  
                    });\n";

                print "jQuery('#add-escalation-settings-button').click(function () {
                        var member  = jQuery('input#systemescalation_users_id').val(); 
                        var branch =  $(branchescalation).children(':selected').attr('id');
                        var time = jQuery('input#timescalation').val(); 
                        AJAXCallModuleJSOnly('" . __CLASS__ . "', 'saveEscalationUserSettings', 'member='+member+'&branch='+branch+'&time='+time);
                    });\n";

                print "jQuery('select#branchescalation_edit').change(function () { 
                        var id = $(branchescalation_edit).children(':selected').attr('id');               
                        $('input#branchescalation_id_edit').val(id); 
                        AJAXCallEscalationMembers('" . __CLASS__ . "', 'getBranchEscalationMembers', 'branchID='+id);  
                    });\n";

                print "jQuery('select#systemescalation_edit').change(function () {
                        var id = $(systemescalation_edit).children(':selected').attr('id');               
                        $('input#systemescalation_users_id_edit').val(id); 
                        AJAXCallEscalationDelete('" . __CLASS__ . "', 'getEscalationUserInfoEdit', 'systemID='+id);  
                    });\n";

                print "jQuery('#delete-escalation-settings-button').click(function () {
                        var member  = jQuery('input#systemescalation_users_id_edit').val(); 
                        AJAXCallModuleJSOnly('" . __CLASS__ . "', 'deleteEscalationUserSettings', 'member='+member);
                    });\n";
                //===============================================================
                //Quote Property Settings
                print "$('table#addPropertysetup').hide();
                    jQuery('a#addProperty').click(function () {
                         $('div#viewPropertysetup_wrapper').hide();
                         $('table#editPropertysetup').hide();
                        $('table#addPropertysetup').show(); 
                    });\n ";
                
                print "$('table#editPropertysetup').hide();
                    jQuery('a#editProperties').click(function () {
                         $('div#viewPropertysetup_wrapper').hide();
                         $('table#addPropertysetup').hide();
                        $('table#editPropertysetup').show(); 
                    });\n ";

                print "jQuery('a#viewProperty').click(function () {
                         $('table#editPropertysetup').hide();
                         $('table#addPropertysetup').hide();
                        $('div#viewPropertysetup_wrapper').show(); 
                    });\n";

                print "jQuery('#add-property-settings-button').click(function () {
                        String.prototype.capitalize = function() {
                            return this.charAt(0).toUpperCase() + this.slice(1);
                        }
                        var property  = jQuery('input#systemProperty').val().capitalize();
                        AJAXCallModuleJSOnly('" . __CLASS__ . "', 'addPropertySettings', 'property='+property);
                    });\n ";

                print "jQuery('select#property_edit').change(function () { 
                        var id = $(property_edit).children(':selected').attr('id');               
                        $('input#property_id_edit').val(id); 
                        AJAXCallProperty('" . __CLASS__ . "', 'getPropertyInfo', 'propertyID='+id);  
                    });\n ";

                print "jQuery('#save-property-settings-button').click(function () {
                        String.prototype.capitalize = function() {
                            return this.charAt(0).toUpperCase() + this.slice(1);
                        }
                        var property  = jQuery('input#systemProperty_edit').val().capitalize();
                        var id  = jQuery('input#property_id_edit').val(); 
                        AJAXCallModuleJSOnly('" . __CLASS__ . "', 'savePropertySettings', 'id='+id+'&property='+property);
                    });\n ";

                print "jQuery('#delete-property-settings-button').click(function () {
                        var property  = jQuery('input#property_id_edit').val(); 
                        AJAXCallModuleJSOnly('" . __CLASS__ . "', 'deletePropertySettings', 'property='+property);
                    });\n ";
                  //===============================================================
                //Quote Floor Plans Settings
                print "$('table#addFloorPlansetup').hide();
                    jQuery('a#addFloorPlan').click(function () {
                         $('div#viewFloorPlansetup_wrapper').hide();
                         $('table#editFloorPlansetup').hide();
                        $('table#addFloorPlansetup').show(); 
                    });\n";
                
                print "$('table#editFloorPlansetup').hide();
                    jQuery('a#editFloorPlan').click(function () {
                         $('div#viewFloorPlansetup_wrapper').hide();
                         $('table#addFloorPlansetup').hide();
                        $('table#editFloorPlansetup').show(); 
                    });\n ";

                print "jQuery('a#viewFloorPlan').click(function () {
                         $('table#editFloorPlansetup').hide();
                         $('table#addFloorPlansetup').hide();
                        $('div#viewFloorPlansetup_wrapper').show(); 
                    });\n";

                print "jQuery('#add-floorplan-settings-button').click(function () {                           
                        var name  = jQuery('input#systemFloorPlanName_edit').val();
                        var description  = jQuery('textarea#systemfloorplanDescription').val(); 
                        AJAXCallModuleJSOnly('" . __CLASS__ . "', 'addFloorPlanSettings', 'name='+name+'&description='+description);      
                    });\n";

                print "jQuery('select#floorplan_edit').change(function () { 
                        var id = $(floorplan_edit).children(':selected').attr('id');               
                        $('input#floor_plan_id_edit').val(id); 
                        AJAXCallFloorPlans('" . __CLASS__ . "', 'getFloorPlan', 'floorplanID='+id);  
                    });\n ";
                
                print "jQuery('#save-floorplan-settings-button').click(function () {
                        var name  = jQuery('input#systemFloorPlanName_edit').val();
                        var description  = jQuery('textarea#systemFloorPlanDescription_edit').val(); 
                        var id  = jQuery('input#floor_plan_id_edit').val(); 
                        AJAXCallModuleJSOnly('" . __CLASS__ . "', 'saveFloorPlanSettings', 'id='+id+'&name='+name+'&description='+description); 
                    });\n ";

                print "jQuery('#delete-floorplan-settings-button').click(function () {
                        var floorplan  = jQuery('input#floor_plan_id_edit').val(); 
                        AJAXCallModuleJSOnly('" . __CLASS__ . "', 'deleteFloorPlanSettings', 'floorplan='+floorplan);
                    });\n ";   
                
                print '$(document).ready(function() {
                    $(".fancybox").fancybox();
                    })';
                //===============================================================
            //print "</div>";
            // print "</div>";
        }

        //======General Settings===============================================
        /*print "<div id='generaldiv'>";
        print "<table class='questionsetup' cellspacing='0' cellpadding='4' border='0' width='100%'>";
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
                    print "<td colspan='2' style='text-align: right; font-weight: 400; color: #111111;'>";
                        print "Length of SMS verification code";
                    print "</td>";
                    print "<td colspan='3' style='padding-left: 3.7%; padding-right: 0; text-align: left;'>";
                        print "<select id='vcode-length'>";
                            $codelen        = self::getSetting(8);
                            print $codelen == 3  ? "<option value='3'  selected='selected'>3</option>"  : "<option value='3'>3</option>";
                            print $codelen == 4  ? "<option value='4'  selected='selected'>4</option>"  : "<option value='4'>4</option>";
                            print $codelen == 5  ? "<option value='5'  selected='selected'>5</option>"  : "<option value='5'>5</option>";
                            print $codelen == 6  ? "<option value='6'  selected='selected'>6</option>"  : "<option value='6'>6</option>";
                            print $codelen == 7  ? "<option value='7'  selected='selected'>7</option>"  : "<option value='7'>7</option>";
                            print $codelen == 8  ? "<option value='8'  selected='selected'>8</option>"  : "<option value='8'>8</option>";
                            print $codelen == 9  ? "<option value='9'  selected='selected'>9</option>"  : "<option value='9'>9</option>";
                            print $codelen == 10 ? "<option value='10' selected='selected'>10</option>" : "<option value='10'>10</option>";
                        print "</select>";
                    print "</td>";
                print "</tr>";
                print "<tr>";
                    print "<td colspan='2' style='text-align: right; font-weight: 400; color: #111111;'>";
                        print "The installer can only send one SMS per voucher type in ";
                    print "</td>";
                    print "<td style='padding-left: 0; padding-right: 0; text-align: center;'>";
                        print "<select id='sms-interval'>";
                            $smsinterval        = self::getSetting(9);
                            print $smsinterval == 1   ? "<option value='1'   selected='selected'>1</option>"  : "<option value='1'>1</option>";
                            print $smsinterval == 5   ? "<option value='5'   selected='selected'>5</option>"  : "<option value='5'>5</option>";
                            print $smsinterval == 10  ? "<option value='10'  selected='selected'>10</option>" : "<option value='10'>10</option>";
                            print $smsinterval == 15  ? "<option value='15'  selected='selected'>15</option>" : "<option value='15'>15</option>";
                            print $smsinterval == 20  ? "<option value='20'  selected='selected'>20</option>" : "<option value='20'>20</option>";
                            print $smsinterval == 30  ? "<option value='30'  selected='selected'>30</option>" : "<option value='30'>30</option>";
                            print $smsinterval == 60  ? "<option value='60'  selected='selected'>60</option>" : "<option value='60'>60</option>";
                        print "</select>";
                    print "</td>";
                    print "<td colspan='2' style='text-align: left; font-weight: 400; color: #111111;'>";
                        print "&nbsp; minute intervals";
                    print "</td>";
                print "</tr>";
                print "<tr>";
                    print "<td colspan='2' style='text-align: right; font-weight: 400; color: #111111;'>";
                        print "The installer can only send a maximum of ";
                    print "</td>";
                    print "<td style='padding-left: 0; padding-right: 0; text-align: center;'>";
                        print "<select id='sms-day'>";
                            $smsday        = self::getSetting(10);
                            print $smsday == 1   ? "<option value='1'   selected='selected'>1</option>"  : "<option value='1'>1</option>";
                            print $smsday == 5   ? "<option value='5'   selected='selected'>5</option>"  : "<option value='5'>5</option>";
                            print $smsday == 10  ? "<option value='10'  selected='selected'>10</option>" : "<option value='10'>10</option>";
                            print $smsday == 15  ? "<option value='15'  selected='selected'>15</option>" : "<option value='15'>15</option>";
                            print $smsday == 20  ? "<option value='20'  selected='selected'>20</option>" : "<option value='20'>20</option>";
                            print $smsday == 30  ? "<option value='30'  selected='selected'>30</option>" : "<option value='30'>30</option>";
                            print $smsday == 60  ? "<option value='60'  selected='selected'>60</option>" : "<option value='60'>60</option>";
                        print "</select>";
                    print "</td>";
                    print "<td colspan='2' style='text-align: left; font-weight: 400; color: #111111;'>";
                        print "&nbsp; SMS's during a 24 hour period";
                    print "</td>";
                print "</tr>";
                print "<tr>";
                    print "<td colspan='2' style='text-align: right; font-weight: 400; color: #111111;'>";
                        print "Greenslip numbers should be ";
                    print "</td>";
                    print "<td style='padding-left: 0; padding-right: 0; text-align: center;'>";
                        print "<select id='gslen'>";
                            $gslen        = self::getSetting(11);
                            print $gslen == 6  ? "<option value='6'  selected='selected'>6</option>"  : "<option value='6'>6</option>";
                            print $gslen == 7  ? "<option value='7'  selected='selected'>7</option>"  : "<option value='7'>7</option>";
                            print $gslen == 8  ? "<option value='8'  selected='selected'>8</option>"  : "<option value='8'>8</option>";
                            print $gslen == 9  ? "<option value='9'  selected='selected'>9</option>"  : "<option value='9'>9</option>";
                            print $gslen == 10 ? "<option value='10' selected='selected'>10</option>" : "<option value='10'>10</option>";
                        print "</select>";
                    print "</td>";
                    print "<td colspan='2' style='text-align: left; font-weight: 400; color: #111111;'>";
                        print "&nbsp; characters long";
                    print "</td>";
                print "</tr>";
                print "<tr>";
                    print "<td colspan='2' style='text-align: right; font-weight: 400; color: #111111;'>";
                        $disablesms = self::getSetting(12) == 0 ? "" : "checked='checked'";
                        print "<input type='checkbox' id='disabledsms' $disablesms />";
                    print "</td>";
                    print "<td colspan='3' class='same-bg' style='text-align: left; padding-left: 35px;'>";
                        print "<label for='disabledsms' style='width: 90%; background-color: transparent; font-weight: 400; color: #111111; font-size: 11px;'>Disable SMS Sending and Receiving</label>";
                    print "</td>";
                print "</tr>";
            print "</tbody>";
            print "<tfoot>";
                print "<tr>";
                    print "<td colspan='3' valign='middle' style='text-align: right;'>";
                        print "<br/>";
                        print "<span id='ok-message2'></span><span id='error-message2'></span>";
                        print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button class='ibutton' id='save-settings-button2'>Save settings</button>";
                        print "<br/><br/>";
                    print "</td>";
                    
                    print "<td colspan='2' align='right' valign='middle' style='padding-right: 20px;'>";
                        print "<a id='restore-default-settings2'>Restore default settings</a>";
                    print "</td>";
                print "</tr>";
            print "</tfoot>";
        print "</table>";
        print "</div>";
        // = == = = ==== == == ===== == = == == ==== == == = = ==  == = = ==== = ==
        print "<div style='margin: 0 auto; width: 95%; border-bottom: 2px solid #BBBBBB;'>&nbsp;</div>";
        print "</div>";
        
        print "\n<!-- scripts , code below be eval()ed by javascript -->\n";
        
        print "jQuery(\"#settings-container\").tabs({ selected: 0 });\n";
        //================================================================
        //General
        print "jQuery('#save-settings-button2').click(function () {
                var codelen     = jQuery('#vcode-length').val();
                var smsinterval = jQuery('#sms-interval').val();
                var smsday      = jQuery('#sms-day').val();
                var gslen       = jQuery('#gslen').val();
                var disableSMS  = jQuery('#disabledsms').is(':checked') ? 1 : 0;
                AJAXCallModuleJSOnly('" . __CLASS__ . "', 'saveGeneralSettings', 'codelen='+codelen+'&smsinterval='+smsinterval+'&smsday='+smsday+'&gslen='+gslen+'&disableSMS='+disableSMS);
            });\n
        "; 
        print "jQuery('#restore-default-settings2').click(function () {
                if (confirm('Are you sure you want to load default general settings? Your current general settings will be lost.')) {
                    AJAXCallModuleJSOnly('" . __CLASS__ . "', 'restoreDefaultGeneralSettings', 'restore=1');
                }
            });\n
        ";  */
//==================================================================================================
        //Checklist
        public function getAjaxChecklistInfo(){
            $questionID = (int)isset($_POST["questionID"])?$_POST["questionID"]:0;
            $query  = "SELECT `id`, `Title`, `Description`, `Type`, `Mandatory`";
            $query .= " FROM `checklist`";
            $query .= " WHERE `id`= ".$questionID;

            if($GLOBALS['system_user']->hasPermission('settings_edit_settings')) {                    
                $result = mysqli_query($GLOBALS["link"],$query);
                $arr = mysqli_fetch_assoc($result);
                print json_encode($arr);
             } else {
                 print "jQuery(\"#error-message\").html(\"Unable to get Checklist information! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
             }        
        }
        public function deleteChecklistSettings ($array) {
            $id = $array['id'];
            $title = $array['title'];
            $description = $array['description'];
            $type = $array['type'];
            if($array['mandatory'] == 'Yes'){
                $mandatory  = 1;
            }else{
                $mandatory  = 0;
            }
            
            if($GLOBALS['system_user']->hasPermission('settings_edit_settings')) {
                $query  = "UPDATE `checklist` SET ";
                $query  .= " `Title` = '".$title. "',";
                $query  .= " `Description` = '".$description. "',";
                $query  .= " `Type` = '".$type."',";
                $query  .= " `active` = 0,";
                $query  .= " `deleted` = 1,";
                $query  .= " `Mandatory` = " . $mandatory;
                $query  .= " WHERE `id` = ". $id;

                $result = mysqli_query($GLOBALS["link"],$query);
                if($result) {
                    print "jQuery(\"#ok-message2\").html(\"Checklist settings deleted...\").show(0).delay(4000).hide(0);\n";
                    logAction("Deleted Checklist settings");
                } else {
                    print "jQuery(\"#error-message2\").html(\"Unable to delete! Please try again...\").show(0).delay(4000).hide(0);\n";
                }    
            } else {
                print "jQuery(\"#error-message2\").html(\"Unable to delete! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
            }
        }
        public function saveChecklistSettings ($array) {
            $id = $array['id'];
            $title = $array['title'];
            $description = $array['description'];
            $type = $array['type'];
            if($array['mandatory'] == 'Yes'){
                $mandatory  = 1;
            }else{
                $mandatory  = 0;
            }
                  
            if($GLOBALS['system_user']->hasPermission('settings_edit_settings')) {
                $query  = "UPDATE `checklist` SET ";
                $query  .= " `Title` = '" .$title. "',";
                $query  .= " `Description` = '".$description. "',";
                $query  .= " `Type` = '".$type."',";
                $query  .= " `active` = 1,";
                $query  .= " `deleted` = 0,";
                $query  .= " `Mandatory` = " . $mandatory;
                $query  .= " WHERE `id` = ". $id;

                $result = mysqli_query($GLOBALS["link"],$query);
                    print "jQuery(\"#ok-message2\").html(\"Checklist settings saved...  \").show(0).delay(4000).hide(0);\n";

                if($result) {
                    print "jQuery(\"#ok-message2\").html(\"Checklist settings saved...\").show(0).delay(4000).hide(0);\n";
                   logAction("Edited Checklist settings");
                } else {
                    print "jQuery(\"#error-message2\").html(\"Unable to save! Please try again...\").show(0).delay(4000).hide(0);\n";
                }    
            } else {
                print "jQuery(\"#error-message2\").html(\"Unable to save! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
            }
        }
        public function addChecklistSettings ($array) {
            $title = $array['title'];
            $description = $array['description'];
            $type = $array['type'];
            if($array['mandatory'] == 'Yes'){
                $mandatory  = 1;
            }else{
                $mandatory  = 0;
            }
            $active = 1;
            $deleted = 0;
            $Sort_order = 0;
                  
            if($GLOBALS['system_user']->hasPermission('settings_edit_settings')) {
                $query  = "INSERT INTO `checklist` (Title,Description,Type,active,deleted,Sort_order,Mandatory) VALUES (";
                $query  .= " '" .$title. "',";
                $query  .= " '".$description. "',";
                $query  .= " '".$type."',";
                $query  .= " '".$active."',";
                $query  .= " '".$deleted."',";
                $query  .= " '".$Sort_order."',";
                $query  .= " '" .$mandatory. "'";
                $query  .= ") ";

                $result = mysqli_query($GLOBALS["link"],$query);
                if($result) {
                    print "jQuery(\"#ok-message2\").html(\"Checklist settings added...\").show(0).delay(4000).hide(0);\n";
                   logAction("Added Checklist settings");
                } else {
                    print "jQuery(\"#error-message2\").html(\"Unable to add question! Please try again...\").show(0).delay(4000).hide(0);\n";
                }    
            } else {
                print "jQuery(\"#error-message2\").html(\"Unable to add question! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
            }
        }
        //======================================================
        //Approval memebers
        public function getApprovalBranchUserInfo(){
            $branchID = (int)isset($_POST["branchID"])?$_POST["branchID"]:0;

            if($GLOBALS['system_user']->hasPermission('settings_edit_settings')) {
                $query  = "SELECT `id`,`first`, `last`";
                $query .= " FROM `system_users`";
                $query .= " WHERE `branch_id` =". $branchID;
                $query .= " ORDER BY `system_users`.branch_id";                    
                $result = mysqli_query($GLOBALS["link"],$query);
                $array = array();
                while ($arr = mysqli_fetch_assoc($result)){
                    $array[] = $arr; 
                }
                print json_encode($array);
             } else {
                 print "jQuery(\"#error-message\").html(\"Unable to get Branch System User information! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
             }
        }
        public function getApprovalUserInfo(){
            $systemID = (int)isset($_POST["systemID"])?$_POST["systemID"]:0;
            $query  = "SELECT `first`, `last`, `system_users`.email, `branch`, `address`";
            $query .= " FROM `system_users` INNER JOIN `branches` ON `branches`.id = `system_users`.branch_id";
            $query .= " WHERE `system_users`.id= ".$systemID;
            if($GLOBALS['system_user']->hasPermission('settings_edit_settings')) {                    
                $result = mysqli_query($GLOBALS["link"],$query);
                $arr = mysqli_fetch_assoc($result);
                print json_encode($arr);
             } else {
                 print "jQuery(\"#error-message\").html(\"Unable to get Approval User information! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
             } 
        }
        public function getApprovalUserInfoEdit(){
            $systemID = (int)isset($_POST["systemID"])?$_POST["systemID"]:0;
            $query  = "SELECT `first`, `last`, `email`, `branch`, `address`";
            $query .= " FROM `system_users` INNER JOIN `branches` ON `branches`.id = `system_users`.branch_id";
            $query .= " WHERE `system_users`.id= ".$systemID;

            if($GLOBALS['system_user']->hasPermission('settings_edit_settings')) {                    
                $result = mysqli_query($GLOBALS["link"],$query);
                $arr = mysqli_fetch_assoc($result);
                print json_encode($arr);
             } else {
                 print "jQuery(\"#error-message\").html(\"Unable to get Approval User information! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
             } 
        }
        public function getBranchApprovalMembers($array){
            $branch = $array['branchID'];

            if($GLOBALS['system_user']->hasPermission('settings_edit_settings')) { 
                $query  = "SELECT `id`,`first`, `last`";
                $query .= " FROM `system_users`,`approval_users`";
                $query .= " WHERE `system_users`.id = `approval_users`.member AND `system_users`.branch_id =".$branch;
                $query .= " ORDER BY `id`";
                $result = mysqli_query($GLOBALS["link"],$query);
                $array = array();
                while ($arr = mysqli_fetch_assoc($result)) {
                     $array[] = $arr;
                }
                print json_encode($array);
            }else {
                 print "jQuery(\"#error-message\").html(\"Unable to get Approval User information! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
            }
        }
        public function saveApprovalUserSettings ($array) {
            $member = $array['member'];
            $branch = $array['branch'];
            $status = "1";
            $date = "NOW()";
            $count = 0;
        
            if($GLOBALS['system_user']->hasPermission('settings_edit_settings')) {
                $query  = "SELECT *";
                $query .= " FROM `approval_users`";
                $query .= " INNER JOIN `system_users` ON `system_users`.id = `approval_users`.member WHERE `status` = 1 AND `system_users`.branch_id=".$branch;
                $result = mysqli_query($GLOBALS["link"],$query);
                while($row = mysqli_fetch_assoc($result))
                {     $count +=1;   
                } 
 
                if($count >= 3){    
                   print "jQuery(\"#error-message-approval\").html(\"Unable to save! Number of approval members for branch exceeded...\").show(0).delay(4000).hide(0);\n";  
                }else{
                    $query  = "INSERT INTO `approval_users` (member,status,date_added) VALUES (";
                    $query  .= " '".$member. "',";
                    $query  .= " '".$status. "',";
                    $query  .= "" .$date. "";
                    $query  .= ") ";
                    $result = mysqli_query($GLOBALS["link"],$query);
                    if($result) {
                        print "jQuery(\"#ok-message-approval\").html(\"Approval User settings saved...\").show(0).delay(4000).hide(0);\n";
                       logAction("Edited Approval Member settings");
                    } else {
                        print "jQuery(\"#error-message-approval\").html(\"Unable to save! Please try again...\").show(0).delay(4000).hide(0);\n";
                    }  
                }  
            } else {
                print "jQuery(\"#error-message-approval\").html(\"Unable to save! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
            }
        }
        public function deleteApprovalUserSettings ($array) {
            $member = $array['member'];
            $status = "0";
            $date_update = "NOW()";
            
            if($GLOBALS['system_user']->hasPermission('settings_edit_settings')) {
                $query = "UPDATE `approval_users` SET";
                $query .= " `member` = '" . $member . "'";
                $query .= ", `status` = '" . $status . "'";
                $query .= ", `last_updated` = " . $date_update;
                $query .= " WHERE `member` = '" . $member . "'";
                $result = mysqli_query($GLOBALS["link"],$query);

                if($result) {
                    print "jQuery(\"#ok-message-approval-delete\").html(\"Delete Approval User settings...\").show(0).delay(4000).hide(0);\n";
                   logAction("Edited Approval Member settings");
                } else {
                    print "jQuery(\"#error-message-approval-delete\").html(\"Unable to save! Please try again...\").show(0).delay(4000).hide(0);\n";
                }   
            } else {
                print "jQuery(\"#error-message-approval-delete\").html(\"Unable to save! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
            }
        }
        //======================================================
        //PDF
        public function savePDFSettings ($array) {
            $userID = $array['id'];
            $disclaimertext = $array['termsCond'];
            $additonalCond = $array['additonalCond'];
            $date = "NOW()";
            $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
            $sqlres = mysqli_query($GLOBALS["link"],$sql);
            $row = mysqli_fetch_assoc($sqlres);
            $channeltype = $row['type'];
            if ($channeltype !== "Commercial" && $channeltype !=="Franchises"){
                $channeltype = "Retail";  
            } 

            $disclaimertext = str_replace("–", "-", $disclaimertext);
            $disclaimertext = str_replace("“", "\"", $disclaimertext);
            $disclaimertext = str_replace("”", "\"", $disclaimertext);
            $disclaimertext = str_replace("'", "\"", $disclaimertext);
            $disclaimertext = str_replace("’", "'", $disclaimertext);
            $disclaimertext = str_replace("-\n", "-\n\n", $disclaimertext);
            $disclaimertext = str_replace(".\n", ".\n\n", $disclaimertext);

            if($GLOBALS['system_user']->hasPermission('settings_edit_settings')) {
                $query  = "INSERT INTO `pdf_settings` (last_updated,last_updated_by,termsCond,additionalCond,pdf_type) VALUES (";
                $query  .= " ".$date. ",";
                $query  .= " '".$userID. "',";
                $query  .= " '".$disclaimertext."',";
                $query  .= " '" .$additonalCond. "',";
                $query  .= " '" .$channeltype. "'";
                $query  .= ") ";
                $result = mysqli_query($GLOBALS["link"],$query);
                
                if($result) {
                    print "jQuery(\"#ok-message-pdf\").html(\"PDF settings saved...\").show(0).delay(4000).hide(0);\n";
                   logAction("Edited PDF settings");
                } else {
                    print "jQuery(\"#error-message-pdf\").html(\"Unable to save! Please try again...\").show(0).delay(4000).hide(0);\n";
                }   
            } else {
                print "jQuery(\"#error-message-pdf\").html(\"Unable to save! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
            }
        }
        //======================================================
        //Dashboard Chart Settings
        public function getChannelCharts(){
            $channelID = (int)isset($_POST["channelID"])?$_POST["channelID"]:0;
            $query  = "SELECT `id`, `title`";
            $query .= " FROM `dashboardchart_settings`";
            $query .= " WHERE `channel_type`= '".$channelID."'";

            if($GLOBALS['system_user']->hasPermission('settings_edit_settings')) {                    
                $result = mysqli_query($GLOBALS["link"],$query);
                $arr = mysqli_fetch_assoc($result);
                print json_encode($arr);
             } else {
                 print "jQuery(\"#error-message2-chart\").html(\"Unable to get Charts information! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
             }        
        }
        public function getChartInfo(){
            $chartID = (int)isset($_POST["chartID"])?$_POST["chartID"]:0;
            $query  = "SELECT `id`, `title`, `description`, `show_chart`, `channel_type`";
            $query .= " FROM `dashboardchart_settings`";
            $query .= " WHERE `id`= ".$chartID;

            if($GLOBALS['system_user']->hasPermission('settings_edit_settings')) {                    
                $result = mysqli_query($GLOBALS["link"],$query);
                $arr = mysqli_fetch_assoc($result);
                print json_encode($arr);
             } else {
                 print "jQuery(\"#error-message2-chart\").html(\"Unable to get Chart information! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
             }        
        }

        public function addChartSettings ($array) {      
            if($GLOBALS['system_user']->hasPermission('settings_edit_settings')) {
                $query  = "INSERT INTO `dashboardchart_settings` (date_created,title,description,show_chart,channel_type,deleted) VALUES (";
                $query  .= "NOW(),";
                $query  .= " '" .$array['title']. "',";
                $query  .= " '".$array['description']. "',";
                $query  .= " '".$array['show']."',";
                $query  .= " '".$array['channel']."',";
                $query  .= " '0'";
                $query  .= ") ";
                $result = mysqli_query($GLOBALS["link"],$query);
                if($result) {
                   print "jQuery(\"#ok-message-chart\").html(\"Chart settings added...\").show(0).delay(4000).hide(0);\n";
                   logAction("Added Chart settings");
                } else {
                    print "jQuery(\"#error-message-chart\").html(\"Unable to add chart! Please try again...\").show(0).delay(4000).hide(0);\n";
                }    
            } else {
                print "jQuery(\"#error-message-chart\").html(\"Unable to add chart! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
            }
        }
        public function saveChartSettings ($array) {
            $id = $array['id'];
    
            if($GLOBALS['system_user']->hasPermission('settings_edit_settings')) {
                $query  = "UPDATE `dashboardchart_settings` SET ";
                $query  .= " `last_updated` = NOW(),";
                $query  .= " `title` = '" .$array['title']. "',";
                $query  .= " `description` = '".$array['description']. "',";
                $query  .= " `show_chart` = '".$array['show']. "',";     
                $query  .= " `channel_type` = '".$array['channel']."'";
                $query  .= " WHERE `id` = ".$id;
                $result = mysqli_query($GLOBALS["link"],$query);
                if($result) {
                    print "jQuery(\"#ok-message2-chart-save\").html(\"Chart settings saved...\").show(0).delay(4000).hide(0);\n";
                    logAction("Deleted Checklist settings");
                } else {
                    print "jQuery(\"#error-message2-chart-save\").html(\"Unable to saved chart! Please try again...\").show(0).delay(4000).hide(0);\n";
                }    
            } else {
                print "jQuery(\"#error-message2-chart-save\").html(\"Unable to saved chart! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
            }
        }
        public function deleteChartSettings ($array) {
            $id = $array['id'];
            
            if($GLOBALS['system_user']->hasPermission('settings_edit_settings')) {
                $query  = "UPDATE `dashboardchart_settings` SET ";
                $query  .= " `last_updated` = NOW(),";
                $query  .= " `deleted` = '1'";
                $query  .= " WHERE `id` = ". $id;
                $result = mysqli_query($GLOBALS["link"],$query);
                if($result) {
                    print "jQuery(\"#ok-message2-chart\").html(\"Chart settings deleted...\").show(0).delay(4000).hide(0);\n";
                    logAction("Deleted Checklist settings");
                } else {
                    print "jQuery(\"#error-message2-chart\").html(\"Unable to delete chart! Please try again...\").show(0).delay(4000).hide(0);\n";
                }    
            } else {
                print "jQuery(\"#error-message2-chart\").html(\"Unable to delete chart! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
            }
        }
        //======================================================
        //Room
        public function getRoomInfo(){
            $roomID = (int)isset($_POST["roomID"])?$_POST["roomID"]:0;
            $query  = "SELECT `room_type`,`property_type`";
            $query .= " FROM `quote_room_settings`";
            $query .= " WHERE `quote_room_settings`.id= ".$roomID;

            if($GLOBALS['system_user']->hasPermission('settings_edit_settings')) {                    
                $result = mysqli_query($GLOBALS["link"],$query);
                $arr = mysqli_fetch_assoc($result);
                print json_encode($arr);
             } else {
                 print "jQuery(\"#error-message-room\").html(\"Unable to get Room information! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
             } 
        }
        public function addRoomSettings ($array) {

            $room_type = $array['roomtype'];
            $property = $array['property'];

            $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
            $sqlres = mysqli_query($GLOBALS["link"],$sql);
            $row = mysqli_fetch_assoc($sqlres);
            $channeltype = $row['type'];
            $quote_type = $channeltype;

            if ($channeltype == "Retail")$room_type = str_replace("^", "", $room_type);
        
            if($GLOBALS['system_user']->hasPermission('settings_edit_settings')) {
                $query  = "INSERT INTO `quote_room_settings` (room_type,quote_type,property_type) VALUES (";
                $query  .= " '".$room_type. "',";
                $query  .= " '".$quote_type. "',";        
                $query  .= "'" .$property. "'";
                $query  .= ") ";
                $result = mysqli_query($GLOBALS["link"],$query);
                if($result) {
                    print "jQuery(\"#ok-message-room\").html(\"Room settings saved...\").show(0).delay(4000).hide(0);\n";
                   logAction("Edited Room settings");
                } else {
                    print "jQuery(\"#error-message-room\").html(\"Unable to save! Please try again...\").show(0).delay(4000).hide(0);\n";
                }    
            } else {
                print "jQuery(\"#error-message-room\").html(\"Unable to save! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
            }
        }
        public function saveRoomSettings ($array) {

            $room_type = $array['roomtype'];
            $property = $array['property'];
            $id = $array['id'];

            $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
            $sqlres = mysqli_query($GLOBALS["link"],$sql);
            $row = mysqli_fetch_assoc($sqlres);
            $channeltype = $row['type'];
            $quote_type = $channeltype;

            if ($channeltype == "Retail")$room_type = str_replace("^", "", $room_type);

        
            if($GLOBALS['system_user']->hasPermission('settings_edit_settings')) {
  
                $query  = "UPDATE `quote_room_settings` SET ";
                $query  .= " `room_type` = '" .$room_type. "',";
                $query  .= " `quote_type` = '".$quote_type. "',";
                $query  .= " `property_type` = ".$property;
                $query  .= " WHERE `id` = ". $id;
                $result = mysqli_query($GLOBALS["link"],$query);
                if($result) {
                    print "jQuery(\"#ok-message-room-save\").html(\"Room settings saved...\").show(0).delay(4000).hide(0);\n";
                   logAction("Edited Room settings");
                } else {
                    print "jQuery(\"#error-message-room-save\").html(\"Unable to save! Please try again...\").show(0).delay(4000).hide(0);\n";
                }    
            } else {
                print "jQuery(\"#error-message-room-save\").html(\"Unable to save! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
            }
        }
        public function deleteRoomSettings ($array) {
            $id = $array['room'];

            if($GLOBALS['system_user']->hasPermission('settings_edit_settings')) {
                $query  = "UPDATE `quote_room_settings` SET ";
                $query  .= " `deleted` = 1";
                $query  .= " WHERE `id` = ". $id;
                $result = mysqli_query($GLOBALS["link"],$query);
                if($result) {
                    print "jQuery(\"#ok-message-room-delete\").html(\"Room settings deleted...\").show(0).delay(4000).hide(0);\n";
                   logAction("Edited Room settings");
                } else {
                    print "jQuery(\"#error-message-room-delete\").html(\"Unable to save! Please try again...\").show(0).delay(4000).hide(0);\n";
                    }  
            } else {
                print "jQuery(\"#error-message-room-delete\").html(\"Unable to save! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
            }
        }
        //======================================================
        //Property
        public function getPropertyInfo(){
            $propertyID = (int)isset($_POST["propertyID"])?$_POST["propertyID"]:0;
            $query  = "SELECT `Type`";
            $query .= " FROM `property_types`";
            $query .= " WHERE `property_types`.id= ".$propertyID;

            if($GLOBALS['system_user']->hasPermission('settings_edit_settings')) {                    
                $result = mysqli_query($GLOBALS["link"],$query);
                $arr = mysqli_fetch_assoc($result);
                print json_encode($arr);
             } else {
                 print "jQuery(\"#error-message-property\").html(\"Unable to get Property information! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
             } 
        }
        public function addPropertySettings ($array) {

            $property = $array['property'];
        
            if($GLOBALS['system_user']->hasPermission('settings_edit_settings')) {
                $query  = "INSERT INTO `property_types` (Type,date_created) VALUES (";
                $query  .= " '".$property. "',";
                $query  .= "NOW()";
                $query  .= ") ";
                $result = mysqli_query($GLOBALS["link"],$query);
                if($result) {
                    print "jQuery(\"#ok-message-property\").html(\"Property settings saved...\").show(0).delay(4000).hide(0);\n";
                   logAction("Edited Property settings");
                } else {
                    print "jQuery(\"#error-message-property\").html(\"Unable to save! Please try again...\").show(0).delay(4000).hide(0);\n";
                }    
            } else {
                print "jQuery(\"#error-message-property\").html(\"Unable to save! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
            }
        }
        public function savePropertySettings ($array) {

            $property = $array['property'];
            $id = $array['id'];    
        
            if($GLOBALS['system_user']->hasPermission('settings_edit_settings')) {
                $query  = "UPDATE `property_types` SET ";
                $query  .= " `last_updated` = NOW(),";
                $query  .= " `Type` = '".$property."'";
                $query  .= " WHERE `id` = ". $id;
                $result = mysqli_query($GLOBALS["link"],$query);
                if($result) {
                    print "jQuery(\"#ok-message-property-save\").html(\"Property settings saved...\").show(0).delay(4000).hide(0);\n";
                   logAction("Edited Property settings");
                } else {
                    print "jQuery(\"#error-message-property-save\").html(\"Unable to save! Please try again...\").show(0).delay(4000).hide(0);\n";
                }    
            } else {
                print "jQuery(\"#error-message-property-save\").html(\"Unable to save! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
            }
        }
        public function deletePropertySettings ($array) {
            $id = $array['property'];

            if($GLOBALS['system_user']->hasPermission('settings_edit_settings')) {
                $query  = "UPDATE `property_types` SET ";
                $query  .= " `deleted` = 1";
                $query  .= " WHERE `id` = ". $id;
                $result = mysqli_query($GLOBALS["link"],$query);
                if($result) {
                    print "jQuery(\"#ok-message-property-delete\").html(\"Property settings deleted...\").show(0).delay(4000).hide(0);\n";
                   logAction("Edited Property settings");
                } else {
                    print "jQuery(\"#error-message-property-delete\").html(\"Unable to save! Please try again...\").show(0).delay(4000).hide(0);\n";
                    }  
            } else {
                print "jQuery(\"#error-message-property-delete\").html(\"Unable to save! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
            }
        }
        //======================================================
        //Escalation users
        public function saveEscalationUserSettings ($array) {
            $member = $array['member'];
            $branch = $array['branch'];
            $time = $array['time'];
            $status = "1";
            $date = "NOW()";
        
            if($GLOBALS['system_user']->hasPermission('settings_edit_settings')) {
                $query  = "INSERT INTO `escalation_users` (member,time_escalation,status,date_added) VALUES (";
                $query  .= " '".$member. "',";
                $query  .= " '".$time. "',";   
                $query  .= " '".$status. "',";
                $query  .= "" .$date. "";
                $query  .= ") ";
                $result = mysqli_query($GLOBALS["link"],$query);
                if($result) {
                    print "jQuery(\"#ok-message-escalation\").html(\"Escalation User settings saved...\").show(0).delay(4000).hide(0);\n";
                   logAction("Edited Escalation Member settings");
                } else {
                    print "jQuery(\"#error-message-escalation\").html(\"Unable to save! Please try again...\").show(0).delay(4000).hide(0);\n";
                }  
            }else {
                print "jQuery(\"#error-message-escalation\").html(\"Unable to save! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
            }
        }
        public function getBranchEscalationMembers($array){
            $branch = $array['branchID'];

            if($GLOBALS['system_user']->hasPermission('settings_edit_settings')) { 
                $query  = "SELECT `id`,`first`, `last`";
                $query .= " FROM `system_users`,`escalation_users`";
                $query .= " WHERE `system_users`.id = `escalation_users`.member AND `system_users`.branch_id =".$branch;
                $query .= " ORDER BY `id`";
                $result = mysqli_query($GLOBALS["link"],$query);
                $array = array();
                while ($arr = mysqli_fetch_assoc($result)) {
                     $array[] = $arr;
                }
                print json_encode($array);
            }else {
                 print "jQuery(\"#error-message\").html(\"Unable to get Escalation User information! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
            }
        }
        public function getEscalationUserInfoEdit(){
            $systemID = (int)isset($_POST["systemID"])?$_POST["systemID"]:0;
            $query  = "SELECT `first`, `last`, `system_users`.email, `branch`, `address`,`time_escalation`";
            $query .= " FROM `system_users` INNER JOIN `branches` ON `branches`.id = `system_users`.branch_id";
            $query .= " INNER JOIN `escalation_users` ON `system_users`.id = `escalation_users`.member";
            $query .= " WHERE `system_users`.id= ".$systemID;

            if($GLOBALS['system_user']->hasPermission('settings_edit_settings')) {                    
                $result = mysqli_query($GLOBALS["link"],$query);
                $arr = mysqli_fetch_assoc($result);
                print json_encode($arr);
             } else {
                 print "jQuery(\"#error-message\").html(\"Unable to get Approval User information! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
             } 
        }
        public function deleteEscalationUserSettings ($array) {
            $member = $array['member'];
            $status = "0";
            $date_update = "NOW()";
            
            if($GLOBALS['system_user']->hasPermission('settings_edit_settings')) {
                $query = "UPDATE `escalation_users` SET";
                $query .= " `member` = '" . $member . "'";
                $query .= ", `status` = '" . $status . "'";
                $query .= ", `last_updated` = " . $date_update;
                $query .= " WHERE `member` = '" . $member . "'";
                $result = mysqli_query($GLOBALS["link"],$query);

                if($result) {
                    print "jQuery(\"#ok-message-escalation-delete\").html(\"Delete Escalation User settings...\").show(0).delay(4000).hide(0);\n";
                   logAction("Edited Escalation Member settings");
                } else {
                    print "jQuery(\"#error-message-escalation-delete\").html(\"Unable to save! Please try again...\").show(0).delay(4000).hide(0);\n";
                }   
            } else {
                print "jQuery(\"#error-message-escalation-delete\").html(\"Unable to save! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
            }
        }
        //======================================================
        //Floor Plans
        public function getFloorPlan(){
            $floorplanID = (int)isset($_POST["floorplanID"])?$_POST["floorplanID"]:0;
            $query  = "SELECT *";
            $query .= " FROM `floor_plan_settings`";
            $query .= " WHERE `floor_plan_settings`.id= ".$floorplanID;

            if($GLOBALS['system_user']->hasPermission('settings_edit_settings')) {                    
                $result = mysqli_query($GLOBALS["link"],$query);
                $arr = mysqli_fetch_assoc($result);
                print json_encode($arr);
             } else {
                 print "jQuery(\"#error-message-floorplan\").html(\"Unable to get Floor Plan information! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
             } 
        }
        public function addFloorPlanSettings ($array) {

            $name = $array['name'];
            $description = $array['description'];

            if($GLOBALS['system_user']->hasPermission('settings_edit_settings')) {
                $query  = "INSERT INTO `floor_plan_settings` (date_created,name,description) VALUES (";
                $query  .= "NOW(),";
                $query  .= " '".$name. "',";
                $query  .= " '".$description. "'";
                $query  .= ") ";
                $result = mysqli_query($GLOBALS["link"],$query);
                if($result) {
                    print "jQuery(\"#ok-message-floorplan\").html(\"Document Floor Plans  settings saved...\").show(0).delay(4000).hide(0);\n";
                   logAction("Edited Document Plan settings");
                } else {
                    print "jQuery(\"#error-message-floorplan\").html(\"Unable to save! Please try again...\").show(0).delay(4000).hide(0);\n";
                } 
            } else {
                print "jQuery(\"#error-message-floorplan\").html(\"Unable to save! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
            }
        }
        public function saveFloorPlanSettings ($array) {
            $name = $array['name'];
            $description = $array['description'];
            $id = $array['id'];

            if($GLOBALS['system_user']->hasPermission('settings_edit_settings')) {
                $query  = "UPDATE `floor_plan_settings` SET ";
                $query  .= " `last_updated` = NOW(),";
                $query  .= " `name` = '".$name."',";
                $query  .= " `description` = '".$description."'";
                $query  .= " WHERE `id` = ". $id;//Floor Plans
                $result = mysqli_query($GLOBALS["link"],$query);
                if($result) {
                    print "jQuery(\"#ok-message-floorplan-save\").html(\"Document Floor Plans settings saved...\").show(0).delay(4000).hide(0);\n";
                   logAction("Edited Document Plan settings");
                } else {
                    print "jQuery(\"#error-message-floorplan-save\").html(\"Unable to save! Please try again...\").show(0).delay(4000).hide(0);\n";
                    }  
            } else {
                print "jQuery(\"#error-message-floorplan-save\").html(\"Unable to save! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
            }
        }
        public function deleteFloorPlanSettings ($array) {
            $id = $array['floorplan'];

            if($GLOBALS['system_user']->hasPermission('settings_edit_settings')) {
                $query  = "UPDATE `floor_plan_settings` SET ";
                $query  .= " `deleted` = '1'";
                $query  .= " WHERE `id` = ". $id;
                $result = mysqli_query($GLOBALS["link"],$query);
                if($result) {
                    print "jQuery(\"#ok-message-floorplan-delete\").html(\"Document Floor Plans  settings deleted...\").show(0).delay(4000).hide(0);\n";
                   logAction("Edited Document Plan settings");
                } else {
                    print "jQuery(\"#error-message-floorplan-delete\").html(\"Unable to delete! Please try again...\").show(0).delay(4000).hide(0);\n";
                    }  
            } else {
                print "jQuery(\"#error-message-floorplan-delete\").html(\"Unable to save! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
            }
        }
        //======================================================
        //Rental
		public function saveRentalSettings ($array){
			$interest_rate_annual_effective = $array['interest_rate_annual_effective'];
            $rental_capital_escalation_rate = $array['rental_capital_escalation_rate'];
            $maintenance_rental_amount_escalation_rate = $array['maintenance_rental_amount_escalation_rate'];
            $deposit_percentage = $array['deposit_percentage'];
            $smaller_than_factor = $array['smaller_than_factor'];
            $return_A = $array['return_A'];
            $return_B = $array['return_B'];
            $return_C = $array['return_C'];

            $initial_expenses_rental = $array['initial_expenses_rental'];
            $maintenance_percentage_per_rental = $array['maintenance_percentage_per_rental'];
			$maintenance_percentage_price_escalation_rate = $array['maintenance_percentage_price_escalation_rate'];
            $fixed_maintenance_per_contract = $array['fixed_maintenance_per_contract'];
			$fixed_maintenance_per_contract_escalation_rate = $array['fixed_maintenance_per_contract_escalation_rate'];
            $fixed_maintenance_per_product_escalation_rate = $array['fixed_maintenance_per_product_escalation_rate'];

			$labour_per_minute_in_hours = $array['labour_per_minute_in_hours'];
			$labour_per_minute_out_hours = $array['labour_per_minute_out_hours'];
			$labour_per_minute_markup = $array['labour_per_minute_markup'];
			$contractor_hours_per_day = $array['contractor_hours_per_day'];
			$contractor_cost_per_km = $array['contractor_cost_per_km'];
			$contractor_cost_per_km_markup = $array['contractor_cost_per_km_markup'];
			$disposal_charge_markup = $array['disposal_charge_markup'];
			
			$materials_markup = $array['materials_markup'];
			$ballast_loss_electric = $array['ballast_loss_electric'];
			$ballast_loss_magnetic = $array['ballast_loss_magnetic'];


			
			$q="Update rental set ".
				"interest_rate_annual_effective = ".$interest_rate_annual_effective.", ".
				"rental_capital_escalation_rate = ".$rental_capital_escalation_rate.", ".
				"maintenance_rental_amount_escalation_rate = ".$maintenance_rental_amount_escalation_rate.", ".
				"deposit_percentage = ".$deposit_percentage.", ".
				"smaller_than_factor = ".$smaller_than_factor.", ".
				"return_A = ".$return_A.", ".
				"return_B = ".$return_B.", ".
				"return_C = ".$return_C.", ".
				"initial_expenses_rental = ".$initial_expenses_rental.", ".
				"maintenance_percentage_per_rental = ".$maintenance_percentage_per_rental.", ".
				"maintenance_percentage_price_escalation_rate = ".$maintenance_percentage_price_escalation_rate.", ".
				"fixed_maintenance_per_contract = ".$fixed_maintenance_per_contract.", ".
				"fixed_maintenance_per_contract_escalation_rate = ".$fixed_maintenance_per_contract_escalation_rate.", ".
				"labour_per_minute_in_hours = ".$labour_per_minute_in_hours.", ".
				"labour_per_minute_out_hours = ".$labour_per_minute_out_hours.", ".
				"labour_per_minute_markup = ".$labour_per_minute_markup.", ".
				"contractor_hours_per_day = ".$contractor_hours_per_day.", ".
				"contractor_cost_per_km = ".$contractor_cost_per_km.", ".
				"contractor_cost_per_km_markup = ".$contractor_cost_per_km_markup.", ".
				"disposal_charge_markup = ".$disposal_charge_markup.", ".
				"materials_markup = ".$materials_markup.", ". 
				"ballast_loss_electric = ".$ballast_loss_electric.", ".
				"ballast_loss_magnetic = ".$ballast_loss_magnetic.", ".
				"fixed_maintenance_per_product_escalation_rate = ".$fixed_maintenance_per_product_escalation_rate;
			//print q;
			if($GLOBALS['system_user']->hasPermission('settings_edit_settings')) {
				$result = mysqli_query($GLOBALS["link"],$q);
				if($result) {
                    print "jQuery(\"#ok-message3\").html(\"Rental settings saved...\").show(0).delay(4000).hide(0);\n";
                    logAction("Edited rental settings");
                } else {
                    print "jQuery(\"#error-message3\").html(\"Unable to save! Please try again...\").show(0).delay(4000).hide(0);\n";
                }
            } else {
                print "jQuery(\"#error-message3\").html(\"Unable to save! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
            }
		}

        public function restoreDefaultRentalSettings () {
            $q = "Update rental set ".
                "interest_rate_annual_effective = default, ".
                "rental_capital_escalation_rate = default, ".
                "maintenance_rental_amount_escalation_rate = default, ".
                "deposit_percentage = default, ".
                "smaller_than_factor = default, ".
                "return_A = default, ".
                "return_B = default, ".
                "return_C = default, ".
                "initial_expenses_rental = default, ".
                "maintenance_percentage_per_rental = default, ".
                "maintenance_percentage_price_escalation_rate = default, ".
                "fixed_maintenance_per_contract = default, ".
                "fixed_maintenance_per_contract_escalation_rate = default, ".
                "labour_per_minute_in_hours = default, ".
                "labour_per_minute_out_hours = default, ".
                "labour_per_minute_markup = default, ". 
                "contractor_hours_per_day = default, ".
                "contractor_cost_per_km = default, ".
                "contractor_cost_per_km_markup = default, ".
                "disposal_charge_markup = default, ".
                "materials_markup = default, ".
                "ballast_loss_electric = default, ".
                "ballast_loss_magnetic = default, ".
                "fixed_maintenance_per_product_escalation_rate = default";
            //print $q;
            //$somestring="Soem";
            //print $somestring;
            if($GLOBALS['system_user']->hasPermission('settings_edit_settings')) {
                if(mysqli_query($GLOBALS["link"],$q)) {
                    logAction ("Restored default rental settings");
                    print "AJAXCallModule('Settings', 'main', 'a=1');\n";
                } else {
                    print "jQuery(\"#error-message3\").html(\"Unable to save! Please try again... \").show(0).delay(4000).hide(0);\n";
                }
            } else {
                print "jQuery(\"#error-message3\").html(\"Failed! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
            }
        }
        //======================================================
        //System
        public function saveSystemSettings ($array) {
            $title      = urldecode($array['title']);
            $rows       = $array['rows'];
            $autologout = $array['autologout'];
            $showadmin  = $array['showadmin'];
            $emails     = urldecode($array['emails']);
            $loginoff   = $array['loginoff'];
            $regex      = urldecode($array['regex']);
            $ethnic     = urldecode($array['ethnic']);
            
            if($GLOBALS['system_user']->hasPermission('settings_edit_settings')) {
                $result1 = mysqli_query($GLOBALS["link"],"UPDATE `settings` SET `value` = \"" . $this->escape($title)      . "\" WHERE `id` = 1");
                $result2 = mysqli_query($GLOBALS["link"],"UPDATE `settings` SET `value` = \"" . $this->escape($rows)       . "\" WHERE `id` = 2");
                $result3 = mysqli_query($GLOBALS["link"],"UPDATE `settings` SET `value` = \"" . $this->escape($autologout) . "\" WHERE `id` = 3");
                $result4 = mysqli_query($GLOBALS["link"],"UPDATE `settings` SET `value` = \"" . $this->escape($showadmin)  . "\" WHERE `id` = 4");
                $result5 = mysqli_query($GLOBALS["link"],"UPDATE `settings` SET `value` = \"" . $this->escape($emails)     . "\" WHERE `id` = 5");
                $result6 = mysqli_query($GLOBALS["link"],"UPDATE `settings` SET `value` = \"" . $this->escape($loginoff)   . "\" WHERE `id` = 6");
                $result7 = mysqli_query($GLOBALS["link"],"UPDATE `settings` SET `value` = \"" . $this->escape($regex)      . "\" WHERE `id` = 7");
                $result8 = mysqli_query($GLOBALS["link"],"UPDATE `settings` SET `value` = \"" . $this->escape($ethnic)     . "\" WHERE `id` = 13");
				$result9 = mysqli_query($GLOBALS["link"],"UPDATE `settings` SET `value` = \"" . $this->escape($ethnic)     . "\" WHERE `id` = 13");
				
                $allResults = $result1 && $result2 && $result3 && $result4 && $result5 && $result6 && $result7 && $result8;
                if($allResults) {
                    print "jQuery(\"#ok-message\").html(\"System settings saved...\").show(0).delay(4000).hide(0);\n";
                    logAction("Edited system settings");
                } else {
                    print "jQuery(\"#error-message\").html(\"Unable to save! Please try again...\").show(0).delay(4000).hide(0);\n";
                }
                
            } else {
                print "jQuery(\"#error-message\").html(\"Unable to save! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
            }
        }

        public function restoreDefaultSystemSettings () {
            if($GLOBALS['system_user']->hasPermission('settings_edit_settings')) {
                if(mysqli_query($GLOBALS["link"],"UPDATE `settings` SET `value` = `default` WHERE `id` <= 7")) {
                    //print "jQuery(\"#ok-message\").html(\"Default system settings restored..\").show(0).delay(4000).hide(0);\n";
                    logAction ("Restored default system settings");
                    print "AJAXCallModule('Settings', 'main', 'a=1');\n";
                } else {
                    print "jQuery(\"#error-message\").html(\"Unable to save! Please try again...\").show(0).delay(4000).hide(0);\n";
                }
            } else {
                print "jQuery(\"#error-message\").html(\"Failed! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
            }
        }
        //======================================================
        //General
        public function saveGeneralSettings ($array) {
            $codelen      = $array['codelen'];
            $smsinterval  = $array['smsinterval'];
            $smsday       = $array['smsday'];
            $gslen        = $array['gslen'];
            $disableSMS   = $array['disableSMS'];
            
            if($GLOBALS['system_user']->hasPermission('settings_edit_settings')) {
                $result1 = mysqli_query($GLOBALS["link"],"UPDATE `settings` SET `value` = \"" . $this->escape($codelen)     . "\" WHERE `id` = 8");
                $result2 = mysqli_query($GLOBALS["link"],"UPDATE `settings` SET `value` = \"" . $this->escape($smsinterval) . "\" WHERE `id` = 9");
                $result3 = mysqli_query($GLOBALS["link"],"UPDATE `settings` SET `value` = \"" . $this->escape($smsday)      . "\" WHERE `id` = 10");
                $result3 = mysqli_query($GLOBALS["link"],"UPDATE `settings` SET `value` = \"" . $this->escape($gslen)       . "\" WHERE `id` = 11");
                $result4 = mysqli_query($GLOBALS["link"],"UPDATE `settings` SET `value` = \"" . $this->escape($disableSMS)  . "\" WHERE `id` = 12");
                $allResults = $result1 && $result2 && $result3 && $result4;
                if($allResults) {
                    print "jQuery(\"#ok-message2\").html(\"General settings saved...\").show(0).delay(4000).hide(0);\n";
                    logAction("Edited general settings");
                } else {
                    print "jQuery(\"#error-message2\").html(\"Unable to save! Please try again...\").show(0).delay(4000).hide(0);\n";
                }
                
            } else {
                print "jQuery(\"#error-message2\").html(\"Unable to save! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
            }
        }
             
        public function restoreDefaultGeneralSettings () {
            if($GLOBALS['system_user']->hasPermission('settings_edit_settings')) {
                if(mysqli_query($GLOBALS["link"],"UPDATE `settings` SET `value` = `default` WHERE `id` >= 8")) {
                    logAction ("Restored default general settings");
                    print "AJAXCallModule('Settings', 'main', 'a=1');\n";
                } else {
                    print "jQuery(\"#error-message\").html(\"Unable to save! Please try again...\").show(0).delay(4000).hide(0);\n";
                }
            } else {
                print "jQuery(\"#error-message\").html(\"Failed! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
            }
        }
        //======================================================
        public static function getSetting ($settingid) {
            $query  = "SELECT `value`";
            $query .= " FROM `settings`";
            $query .= " WHERE `id` = " . mysqli_real_escape_string($GLOBALS["link"],$settingid);
            $result = mysqli_query($GLOBALS["link"],$query);
            $array  = mysqli_fetch_assoc($result);
            
            return $array['value'];
        }
		
		public static function getRentals(){
		   $query = "SELECT * from rental";
		   $result = mysqli_query($GLOBALS["link"],$query);
		   $array = mysqli_fetch_assoc($result);
		   return $array;
		}
     
        public function escape($str){
	       return mysqli_real_escape_string($GLOBALS["link"],get_magic_quotes_gpc() ? stripslashes($str) : $str);
	    }
    }
?>