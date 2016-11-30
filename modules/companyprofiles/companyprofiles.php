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
    register_menu("CompanyProfiles", "submenu", "Franchisee Profiles",
        Array(
            Array("title" => "View Company Profile(s)",  "location" => "view_company_profile",   "acl" => "view_company_profile"),
            Array("title" => "Manage Company Profiles",  "location" => "manage_company_profile",   "acl" => "manage_company_profile")
        )
    );

    register_permission("CompanyProfiles Permissions", "view_company_profile", "View Company Profile(s)");
    register_permission("CompanyProfiles Permissions", "manage_company_profile", "Manage Company Profile");


    class CompanyProfiles {
        private $items_per_page;
    
        public function __construct () {
            $this->items_per_page = Settings::getSetting(2);
        }

        public function manage_company_profile ($array) {
            $ColourList = array(
                        "aliceblue" => "#F0F8FF", "antiquewhite" => "#FAEBD7", "aqua" => "#00FFFF", "aquamarine" => "#7FFFD4", "azure" => "#F0FFFF", "beige" => "#F5F5DC", "bisque" => "#FFE4C4", "black" => "#000000", "blanchedalmond" => "#FFEBCD", "blue" => "#0000FF", "blueviolet" => "#8A2BE2", "brown" => "#A52A2A", "burlywood" => "#DEB887", "cadetblue" => "#5F9EA0", "chartreuse" => "#7FFF00", "chocolate" => "#D2691E", "coral" => "#FF7F50", "cornflowerblue" => "#6495ED", "cornsilk" => "#FFF8DC", "crimson" => "#DC143C", "cyan" => "#00FFFF", "darkblue" => "#00008B", "darkcyan" => "#008B8B", "darkgoldenrod" => "#B8860B", "darkgray" => "#A9A9A9", "darkgrey" => "#A9A9A9", "darkgreen" => "#006400", "darkkhaki" => "#BDB76B", "darkmagenta" => "#8B008B", "darkolivegreen" => "#556B2F", "darkorange" => "#FF8C00", "darkorchid" => "#9932CC", "darkred" => "#8B0000", "darksalmon" => "#E9967A", "darkseagreen" => "#8FBC8F", "darkslateblue" => "#483D8B", "darkslategray" => "#2F4F4F", "darkslategrey" => "#2F4F4F", "darkturquoise" => "#00CED1", "darkviolet" => "#9400D3", "deeppink" => "#FF1493", "deepskyblue" => "#00BFFF", "dimgray" => "#696969", "dimgrey" => "#696969", "dodgerblue" => "#1E90FF", "firebrick" => "#B22222", "floralwhite" => "#FFFAF0", "forestgreen" => "#228B22", "fuchsia" => "#FF00FF", "gainsboro" => "#DCDCDC", "ghostwhite" => "#F8F8FF", "gold" => "#FFD700", "goldenrod" => "#DAA520", "gray" => "#808080", "grey" => "#808080", "green" => "#008000", "greenyellow" => "#ADFF2F", "honeydew" => "#F0FFF0", "hotpink" => "#FF69B4", "indianred " => "#CD5C5C", "indigo " => "#4B0082", "ivory" => "#FFFFF0", "khaki" => "#F0E68C", "lavender" => "#E6E6FA", "lavenderblush" => "#FFF0F5", "lawngreen" => "#7CFC00", "lemonchiffon" => "#FFFACD", "lightblue" => "#ADD8E6", "lightcoral" => "#F08080", "lightcyan" => "#E0FFFF", "lightgoldenrodyellow" => "#FAFAD2", "lightgray" => "#D3D3D3", "lightgrey" => "#D3D3D3", "lightgreen" => "#90EE90", "lightpink" => "#FFB6C1", "lightsalmon" => "#FFA07A", "lightseagreen" => "#20B2AA", "lightskyblue" => "#87CEFA", "lightslategray" => "#778899", "lightslategrey" => "#778899", "lightsteelblue" => "#B0C4DE", "lightyellow" => "#FFFFE0", "lime" => "#00FF00", "limegreen" => "#32CD32", "linen" => "#FAF0E6", "magenta" => "#FF00FF", "maroon" => "#800000", "mediumaquamarine" => "#66CDAA", "mediumblue" => "#0000CD", "mediumorchid" => "#BA55D3", "mediumpurple" => "#9370D8", "mediumseagreen" => "#3CB371", "mediumslateblue" => "#7B68EE", "mediumspringgreen" => "#00FA9A", "mediumturquoise" => "#48D1CC", "mediumvioletred" => "#C71585", "midnightblue" => "#191970", "mintcream" => "#F5FFFA", "mistyrose" => "#FFE4E1", "moccasin" => "#FFE4B5", "navajowhite" => "#FFDEAD", "navy" => "#000080", "oldlace" => "#FDF5E6", "olive" => "#808000", "olivedrab" => "#6B8E23", "orange" => "#FFA500", "orangered" => "#FF4500", "orchid" => "#DA70D6", "palegoldenrod" => "#EEE8AA", "palegreen" => "#98FB98", "paleturquoise" => "#AFEEEE", "palevioletred" => "#D87093", "papayawhip" => "#FFEFD5", "peachpuff" => "#FFDAB9", "peru" => "#CD853F", "pink" => "#FFC0CB", "plum" => "#DDA0DD", "powderblue" => "#B0E0E6", "purple" => "#800080", "red" => "#FF0000", "rosybrown" => "#BC8F8F", "royalblue" => "#4169E1", "saddlebrown" => "#8B4513", "salmon" => "#FA8072", "sandybrown" => "#F4A460", "seagreen" => "#2E8B57", "seashell" => "#FFF5EE", "sienna" => "#A0522D", "silver" => "#C0C0C0", "skyblue" => "#87CEEB", "slateblue" => "#6A5ACD", "slategray" => "#708090", "slategrey" => "#708090", "snow" => "#FFFAFA", "springgreen" => "#00FF7F", "steelblue" => "#4682B4", "tan" => "#D2B48C", "teal" => "#008080", "thistle" => "#D8BFD8", "tomato" => "#FF6347", "turquoise" => "#40E0D0", "violet" => "#EE82EE", "wheat" => "#F5DEB3", "white" => "#FFFFFF", "whitesmoke" => "#F5F5F5", "yellow" => "#FFFF00", "yellowgreen" => "#9ACD32");

            print "<div class='classy_table'>";
            print "&nbsp;&nbsp;&nbsp;<span style='font-size:14px;font-weight:bold;'>Manage Company Profiles</span>";
            print "</div>";
            //Member permission
            $sql = "SELECT `type` FROM `branches_profile_permissions` WHERE `user_id`=".$GLOBALS['system_user']->id." AND `branch`=".$GLOBALS['system_user']->branchID;
            $sqlres = mysqli_query($GLOBALS["link"],$sql);
            $row = mysqli_fetch_assoc($sqlres);
            $member_permission = $row['type'];
            
            if($member_permission == "Owner"){
                print "<p style='float:right'><a  href='#' id='editCompanyProfile' name='editCompanyProfile'><u>Edit</u></a> | <a href='#' id='addCompanyLogo' name='addCompanyLogo'><u>Logo</u></a> | <a  href='#' id='addPermissions' name='addPermissions'><u>Permissions</u></a></p>";
            }else{
              print "<p style='float:right'><a  href='#' id='editCompanyProfile' name='editCompanyProfile'><u>Edit</u></a> | <a  href='#' id='addCompanyProfile' name='addCompanyProfile'><u>Add</u></a> | <a href='#' id='addCompanyLogo' name='addCompanyLogo'><u>Logo</u></a> | <a  href='#' id='addPermissions' name='addPermissions'><u>Permissions</u></a></p>";  
            }
            print "<table id='editcompanyrofilesetup' name='editcompanyrofilesetup' cellspacing='0' cellpadding='4' border='0' width='100%'>";
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
                     if($member_permission == "Owner"){
                        print "<td colspan='2' style='text-align: right; font-weight: 400; color: #111111; padding-right: 20px;'>"; 
                        $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
                        $sqlres = mysqli_query($GLOBALS["link"],$sql);
                        $row = mysqli_fetch_assoc($sqlres);
                        $channeltype = $row['type']; 
                        if($channeltype =="Franchises"){
                            print "Franchisee:";
                        }else{
                            print "Channel:";
                        }                                 
                        print "</td>";
                        print "<td>"; 
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
                            print "<label id='branch_name' style='width: 200px;font-weight: bold;'>".$branch."</label>";
                        print "</td>"; 
                    }else{
                        print "<td colspan='2' style='text-align: right; font-weight: 400; color: #111111; padding-right: 20px;'>";
                        $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
                        $sqlres = mysqli_query($GLOBALS["link"],$sql);
                        $row = mysqli_fetch_assoc($sqlres);
                        $channeltype = $row['type']; 
                        if($channeltype =="Franchises")print "Select Franchisee:";  
                        if($channeltype =="Retail")print "Select Channel:";                             
                        if($channeltype =="Commercial")print "Select Channel:";                             
                        print "<input type='hidden' id='channel_id_edit' style='width: 200px' />"; 
                        print "</td>";
                         print "<td>";     
                            print "<select id='channel_edit'>";
                                print "<option value='' selected='selected'>[Please select]</option>";
                                $query  = "SELECT `id`,`name`";
                                $query .= " FROM `channels`";
                                $query .= " WHERE `type` = 'Franchises'";
                                $query .= " ORDER BY `id`";
                                $result = mysqli_query($GLOBALS["link"],$query);
                                while ($arr = mysqli_fetch_assoc($result)) {
                                    print "<option id='".$arr['id']."' value='".$arr['id']."'>";
                                            print ucfirst($arr['name']);
                                    print "</option>";
                                }
                            print "</select>";
                        print "</td>";    
                    } 
                 print "</tr>";
                print "<tr>";
                    print "<td colspan='2' style='text-align: right; font-weight: 400; color: #111111; padding-right: 20px;'>";                                 
                        print "Select Company:";
                        print "<input type='hidden' id='profilechannel_id_edit' style='width: 200px' />"; 
                    print "</td>";
                    if($member_permission == "Owner"){
                        print "<td>";     
                            print "<select id='profilechannel_edit'>";
                                print "<option value='' selected='selected'>[Please select]</option>";
                                $query  = "SELECT `branches`.id,`branches`.branch";
                                $query .= " FROM `branches`";
                                $query .= " WHERE `branches`.channel= ".$GLOBALS['system_user']->retailChannel;
                                $result = mysqli_query($GLOBALS["link"],$query);
                                while ($arr = mysqli_fetch_assoc($result)) {
                                    print "<option id='".$arr['id']."' value='".$arr['id']."'>";
                                        print ucfirst($arr['branch']);
                                    print "</option>";
                                }
                            print "</select>";
                        print "</td>";
                    }else{
                        print "<td>";     
                            print "<select id='profilechannel_edit'>";
                                print "<option value='' selected='selected'>[Please select]</option>";
                            print "</select>";
                        print "</td>";
                    }   
                 print "</tr>";
                 print "<tr>";
                    print "<td colspan='2' style='text-align: right; font-weight: 400; color: #111111; padding-right: 20px;'></td>";
                    print "<td>";
                        print "<p id='nocompany'></p>";
                    print "</td>";
                 print "</tr>";

                print "<tr>";
                    print "<td colspan='4' style='text-align: left; padding: 0'>";
                     print "<div>";
                        print "<table  id='companyInfotable' cellpadding='10'>";
                            print "<tr>";
                                 print "<td>";
                                    print "Name";
                                        print "<input type='hidden' id='companyprofile_id_edit' style='width: 200px' />"; 
                                 print "</td>";
                                 print "<td>";
                                    print "<input type='text' id='companyprofilename_edit' style='width: 200px'  readonly/>";        
                                 print "</td>";
                             print "</tr>";

                             print "<tr>";
                                 print "<td>";
                                    print "Company Code";
                                 print "</td>";
                                 print "<td>";
                                    print "<input type='text' id='companyprofilecompanycode_edit' style='width: 200px' readonly/>";        
                                 print "</td>";
                             print "</tr>";

                             print "<tr>";
                                 print "<td>";
                                    print "Cellphone/Telephone";
                                 print "</td>";
                                 print "<td>";
                                    print "<input type='text' id='companyprofilecellphone_edit' style='width: 200px' readonly/>";        
                                 print "</td>";
                             print "</tr>";


                             print "<tr>";
                                 print "<td>";
                                    print "Email";
                                 print "</td>";
                                 print "<td>";
                                    print "<input type='text' id='companyprofileemail_edit' style='width: 200px'readonly/>";        
                                 print "</td>";
                             print "</tr>";

                            print "<tr>";
                                 print "<td>";
                                    print "Address";
                                 print "</td>";
                                 print "<td>";
                                    print "<textarea id='companyprofileaddress_edit' style='width: 98%; height: 50px; resize: none; overflow: auto;' maxlength='254'></textarea>";           
                                 print "</td>";
                            print "</tr>";

                            print "<tr>";
                                 print "<td>";
                                    print "Theme Colour";
                                 print "</td>";
                                 print "<td>";
                                   // print "<input type='text' id='companyprofiletheme_edit' style='width: 200px' readonly/>";    
                                    print "<select id='companyprofiletheme_edit'>";
                                    print "<option value='' selected='selected'>[Please select]</option>";
                                        foreach($ColourList as $key=>$value){
                                            print "<option id='".$key."' value='".strtolower($key)."'>";
                                                    print ucfirst($key);
                                            print "</option>";
                                        }
                                    print "</select>";       
                                 print "</td>";
                            print "</tr>";


                            print "<tr>";
                                 print "<td>";
                                    print "Approval Setting";
                                 print "</td>";
                                 print "<td>";
                                    //print "<input type='text' id='companyprofileapprovalsetting_edit' style='width: 200px' readonly/>"; 
                                    print "<select id='companyprofileapprovalsetting_edit'>";
                                    print "<option value='' selected='selected'>[Please select]</option>";
                                            print "<option id='No' value='No'>No</option>";
                                            print "<option id='Yes' value='Yes'>Yes</option>";
                                    print "</select>";       
                                 print "</td>";
                            print "</tr>";

                            print "<tr>";
                                 print "<td>";
                                    print "Notes";
                                 print "</td>";
                                 print "<td>";
                                    print "<textarea id='companyprofilenotes_edit' style='width: 98%; height: 50px; resize: none; overflow: auto;' maxlength='254'></textarea>";           
                                 print "</td>";
                            print "</tr>";

                            print "<tr>";
                                 print "<td>";
                                    print "Product Markup(%)";
                                 print "</td>";
                                 print "<td>";
                                    print "<input type='number' step='any' min='0' id='companyprofilemarkup_edit' style='width: 200px' />";        
                                 print "</td>";
                             print "</tr>";

                            print "<tr>";
                                 print "<td>";
                                    $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
                                    $sqlres = mysqli_query($GLOBALS["link"],$sql);
                                    $row = mysqli_fetch_assoc($sqlres);
                                    $channeltype = $row['type']; 
                                    if($channeltype =="Franchises")print "Franchisee:";  
                                    if($channeltype =="Retail")print "Channel:";                             
                                    if($channeltype =="Commercial")print "Channel:"; 
                                 print "</td>";
                                 print "<td>";
                                    print "<input type='text' id='companyprofilechannel_edit' style='width: 200px' readonly/>";           
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
                            if($member_permission != "Owner"){
                              print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button class='ibutton' id='delete-company-profile-button' style='color:red;font-weight: bold;font-size: 12px;'>Delete Company Profile</button>";  
                            }  
                            print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button class='ibutton' id='update-company-profile-button'>Update Company Profile</button>";
                            print "<br/><br/>";
                        print "</td>";
                    print "</tr>";
                print "</tfoot>";
            print "</table>";
            print "<table id='addcompanyprofilesetup' name='addcompanyprofilesetup' cellspacing='0' cellpadding='4' border='0' width='100%'>";
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
                                    print "<input type='text' id='companyprofilename' style='width: 200px' />";        
                                 print "</td>";
                             print "</tr>";

                             print "<tr>";
                                 print "<td>";
                                    print "Company Code";
                                 print "</td>";
                                 print "<td>";
                                    print "<input type='text' id='companyprofilecompanycode' style='width: 200px' />";        
                                 print "</td>";
                             print "</tr>";

                             print "<tr>";
                                 print "<td>";
                                    print "Cellphone/Telephone";
                                 print "</td>";
                                 print "<td>";
                                    print "<input type='text' id='companyprofilecellphone' style='width: 200px' />";        
                                 print "</td>";
                             print "</tr>";

                             print "<tr>";
                                 print "<td>";
                                    print "Email";
                                 print "</td>";
                                 print "<td>";
                                    print "<input type='text' id='companyprofileemail' style='width: 200px' />";        
                                 print "</td>";
                             print "</tr>";

                            print "<tr>";
                                 print "<td>";
                                    print "Address";
                                 print "</td>";
                                 print "<td>";
                                    print "<textarea id='companyprofileaddress' style='width: 98%; height: 50px; resize: none; overflow: auto;' maxlength='254'></textarea>";           
                                 print "</td>";
                            print "</tr>";

                            print "<tr>";
                                 print "<td>";
                                    print "Theme";
                                 print "</td>";
                                 print "<td>";
                                    print "<select id='companyprofiletheme'>";
                                    print "<option value='' selected='selected'>[Please select]</option>";
                                        foreach($ColourList as $key=>$value){
                                            print "<option id='".$key."' value='".$ColourList[$key]."'>";
                                                    print ucfirst($key);
                                            print "</option>";
                                        }
                                    print "</select>";        
                                 print "</td>";
                            print "</tr>";

                             print "<tr>";
                                 print "<td>";
                                    print "Approval Setting";
                                 print "</td>";
                                 print "<td>";
                                    print "<select id='companyprofileapprovalsetting'>";
                                    print "<option value='' selected='selected'>[Please select]</option>";
                                            print "<option id='No' value='No'>No</option>";
                                            print "<option id='Yes' value='Yes'>Yes</option>";
                                    print "</select>";        
                                 print "</td>";
                            print "</tr>";

                            print "<tr>";
                                 print "<td>";
                                    print "Notes";
                                 print "</td>";
                                 print "<td>";
                                    print "<textarea id='companyprofilenotes' style='width: 98%; height: 50px; resize: none; overflow: auto;' maxlength='254'></textarea>";           
                                 print "</td>";
                            print "</tr>"; 

                              print "<tr>";
                                 print "<td>";
                                    print "Product Markup(%)";
                                 print "</td>";
                                 print "<td>";
                                    print "<input type='number' step='any' min='0' id='companyprofilemarkup' style='width: 200px' />";        
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
                                        if($channeltype =="Franchises")print "Franchisee:";  
                                        if($channeltype =="Retail")print "Channel:";                             
                                        if($channeltype =="Commercial")print "Channel:";  
                                     print "</td>";
                                     print "<td>";
                                        print "<select id='companyprofilechannel'>";
                                        print "<option id='' value='' selected='selected'>[Please select]</option>";
                                                $query  = "SELECT `id`,`name`";
                                                $query .= " FROM `channels`";
                                                $query .= " WHERE `type` = 'Franchises'";
                                                $query .= " ORDER BY `id`";
                                                $result = mysqli_query($GLOBALS["link"],$query);
                                                while ($arr = mysqli_fetch_assoc($result)) {
                                                    print "<option id='".$arr['id']."' value='".$arr['id']."'>";
                                                            print ucfirst($arr['name']);
                                                    print "</option>";
                                                }
                                        print "</select>";        
                                     print "</td>";
                                print "</tr>";
                            }else{
                                print "<select id='companyprofilechannel' style='display:none;'>";
                                print "<option id='".$GLOBALS['system_user']->retailChannel."' value='".$GLOBALS['system_user']->retailChannel."' selected></option>";
                                print "</select>";        

                                //print "<input type='hidden' id='companyprofilechannel' style='width: 200px' value='".$GLOBALS['system_user']->retailChannel."'/>";     
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
                            print "<span id='ok-message' style='color:green'></span><span id='error-message'style='color:red'></span>";
                            print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button class='ibutton' id='add-company-profile-button'>Save Company Profile</button>";
                            print "<br/><br/>";
                        print "</td>";
                    print "</tr>";
                print "</tfoot>";
            print "</table>";
            print "<div id='addcompanylogosetup' name='addcompanylogosetup' cellspacing='0' cellpadding='4' border='0' width='100%'>";
                print "<iframe src='uploadlogo.php' width='600' height='600' border='0' frameborder='0'></iframe>"; 
            print "</div>";
            print "<table id='addcompanypermissionssetup' name='addcompanypermissionssetup' cellspacing='0' cellpadding='4' border='0' width='100%'>";
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
                                if($member_permission == "Owner"){
                                    print "<td>"; 
                                    $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
                                    $sqlres = mysqli_query($GLOBALS["link"],$sql);
                                    $row = mysqli_fetch_assoc($sqlres);
                                    $channeltype = $row['type']; 
                                    if($channeltype =="Franchises"){
                                        print "Franchisee:";
                                    }else{
                                        print "Channel:";
                                    }                                 
                                    print "</td>";
                                    print "<td>"; 
                                        $query = "select `name`,`id` from `channels` where id='".$GLOBALS['system_user']->retailChannel."'";
                                        $result = mysqli_query($GLOBALS["link"],$query);
                                        $branch = "";
                                        $id = "";
                                        $channel = "";
                                        while ($row=mysqli_fetch_assoc($result)){
                                            $branch =  ucfirst($row["name"]);
                                            $id =  $row["id"];
                                            $channel =  $row["id"];
                                        }    
                                        print "<label id='branch_name' style='width: 200px;font-weight: bold;'>".$branch."</label>";
                                    print "</td>"; 
                                }else{
                                    print "<td>";
                                    $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
                                    $sqlres = mysqli_query($GLOBALS["link"],$sql);
                                    $row = mysqli_fetch_assoc($sqlres);
                                    $channeltype = $row['type']; 
                                    if($channeltype =="Franchises")print "Select Franchisee:";  
                                    if($channeltype =="Retail")print "Select Channel:";                             
                                    if($channeltype =="Commercial")print "Select Channel:";                                  
                                    print "<input type='hidden' id='channel_id_permissions' style='width: 200px' />"; 
                                    print "</td>";
                                     print "<td>";     
                                        print "<select id='channel_permissions' style='width: 200px'>";
                                            print "<option value='' selected='selected'>[Please select]</option>";
                                            $query  = "SELECT `id`,`name`";
                                            $query .= " FROM `channels`";
                                            $query .= " WHERE `type` = 'Franchises'";
                                            $query .= " ORDER BY `id`";
                                            $result = mysqli_query($GLOBALS["link"],$query);
                                            while ($arr = mysqli_fetch_assoc($result)) {
                                                print "<option id='".$arr['id']."' value='".$arr['id']."'>";
                                                        print ucfirst($arr['name']);
                                                print "</option>";
                                            }
                                        print "</select>";
                                    print "</td>";
                                }
                             print "</tr>";
                            print "<tr>";
                                print "<td>";                                 
                                    print "Select Company:";
                                    print "<input type='hidden' id='profilechannel_id_permissions' style='width: 200px' />"; 
                                print "</td>";
                                if($member_permission == "Owner"){
                                    print "<td>";     
                                        print "<select id='profilechannel_permissions' style='width: 200px'>";
                                            print "<option value='' selected='selected'>[Please select]</option>";
                                            $query  = "SELECT `branches`.id,`branches`.branch";
                                            $query .= " FROM `branches`";
                                            $query .= " WHERE `branches`.channel= ".$GLOBALS['system_user']->retailChannel;
                                            $result = mysqli_query($GLOBALS["link"],$query);
                                            while ($arr = mysqli_fetch_assoc($result)) {
                                                print "<option id='".$arr['id']."' value='".$arr['id']."'>";
                                                        print ucfirst($arr['branch']);
                                                print "</option>";
                                            }
                                        print "</select>";
                                    print "</td>";
                                }else{
                                    print "<td>";     
                                        print "<select id='profilechannel_permissions' style='width: 200px'>";
                                            print "<option value='' selected='selected'>[Please select]</option>";
                                        print "</select>";
                                    print "</td>";
                                }
                            print "</tr>";

                            print "<tr>";
                                 print "<td>";
                                    print "Owner";
                                 print "</td>";
                                 print "<td>";
                                    print "<select id='company_owner' style='width: 200px' >";
                                        print "<option value='' selected='selected'>[Please select]</option>";
                                    print "</select>";
                                 print "</td>";
                             print "</tr>";

                             print "<tr>";
                                 print "<td>";
                                    print "Teams";
                                 print "</td>";
                                 print "<td>";
                                    print "<select id='company_manageteam_user' style='width: 200px' >";
                                        print "<option value='' selected='selected'>[Please select]</option>";
                                    print "</select>";       
                                 print "</td>";
                                 print "<td>";
                                    print "<select id='company_manageteam_user_2' style='width: 200px' >";
                                        print "<option value='' selected='selected'>[Please select]</option>";
                                    print "</select>";         
                                 print "</td>";
                             print "</tr>";

                             print "<tr>";
                                 print "<td>";
                                    print "Newsletter";
                                 print "</td>";
                                 print "<td>";
                                    print "<select id='company_managenewsletter_user' style='width: 200px' >";
                                        print "<option value='' selected='selected'>[Please select]</option>";
                                    print "</select>";         
                                 print "</td>";
                                 print "<td>";
                                    print "<select id='company_managenewsletter_user_2' style='width: 200px' >";
                                        print "<option value='' selected='selected'>[Please select]</option>";
                                    print "</select>";         
                                 print "</td>";
                             print "</tr>";

                             print "<tr>";
                                 print "<td>";
                                    print "Users";
                                 print "</td>";
                                 print "<td>";
                                    print "<select id='company_managenusers_user' style='width: 200px' >";
                                        print "<option value='' selected='selected'>[Please select]</option>";
                                    print "</select>";         
                                 print "</td>";
                                 print "<td>";
                                    print "<select id='company_managenusers_user_2' style='width: 200px' >";
                                        print "<option value='' selected='selected'>[Please select]</option>";
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
                            print "<span id='ok-message-permissions' style='color:green'></span><span id='error-message-permissions'style='color:red'></span>";
                            print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button class='ibutton' id='add-company-permissions-button'>Save Company Permissions</button>";
                            print "<br/><br/>";
                        print "</td>";
                    print "</tr>";
                print "</tfoot>";
            print "</table>";
            //=================================
            print '<script type="text/javascript" language="javascript" class="init">';
            print "jQuery('a#addCompanyProfile').click(function () {
                    $('table#editcompanyrofilesetup').hide();
                    $('div#addcompanylogosetup').hide();
                    $('table#addcompanypermissionssetup').hide();
                    $('table#addcompanyprofilesetup').show(); 
                });\n
            ";

            print "$('table#editcompanyrofilesetup').hide();
                jQuery('a#editCompanyProfile').click(function () {
                    $('table#addcompanyprofilesetup').hide();
                    $('div#addcompanylogosetup').hide();
                    $('table#addcompanypermissionssetup').hide();
                    $('table#editcompanyrofilesetup').show(); 
                });\n
            ";

            print "$('div#addcompanylogosetup').hide();
                    jQuery('a#addCompanyLogo').click(function () {
                    $('table#addcompanyprofilesetup').hide();
                    $('table#editcompanyrofilesetup').hide(); 
                    $('table#addcompanypermissionssetup').hide();        
                    $('div#addcompanylogosetup').show(); 
                });\n
            ";

            print "$('table#addcompanypermissionssetup').hide();
                    jQuery('a#addPermissions').click(function () {
                    $('table#addcompanyprofilesetup').hide();
                    $('table#editcompanyrofilesetup').hide(); 
                    $('div#addcompanylogosetup').hide();               
                    $('table#addcompanypermissionssetup').show(); 
                });\n
            ";

            print "jQuery('#add-company-profile-button').click(function () {
                    var branch  = jQuery('input#companyprofilename').val(); 
                    var prefix  = jQuery('input#companyprofilecompanycode').val(); 
                    var address  = jQuery('textarea#companyprofileaddress').val(); 
                    var email  = jQuery('input#companyprofileemail').val(); 
                    var contact_number  = jQuery('input#companyprofilecellphone').val(); 
                    var logo  = 'none';    
                    var theme_colour  =  $('select#companyprofiletheme').children(':selected').attr('id'); 
                    var approval_setting  = $('select#companyprofileapprovalsetting').children(':selected').attr('id'); 
                    var notes  = jQuery('textarea#companyprofilenotes').val(); 
                    var channel  =  $('select#companyprofilechannel').children(':selected').attr('id');  
                    AJAXCallModuleJSOnly('" . __CLASS__ . "', 'save_company_profile', 'branch='+branch+'&prefix='+prefix+'&address='+address+'&email='+email+'&contact_number='+contact_number+'&logo='+logo+'&theme_colour='+theme_colour+'&approval_setting='+approval_setting+'&notes='+notes+'&channel='+channel);          
               });\n
            "; 

            print "jQuery('select#channel_edit').change(function () { 
                    var id = $(channel_edit).children(':selected').attr('id');               
                    $('input#channel_id_edit').val(id); 
                    AJAXCallCompaniesProfile('" . __CLASS__ . "', 'getCompaniesProfile', 'channelID='+id);  
                });\n
            ";

            print "jQuery('select#profilechannel_edit').change(function () { 
                    var id = $(profilechannel_edit).children(':selected').attr('id');               
                    $('input#profilechannel_id_edit').val(id); 
                    AJAXCallCompanyProfileInfo('" . __CLASS__ . "', 'getCompanyProfileInfo', 'profileID='+id);  
                });\n
            ";


            print "jQuery('#delete-company-profile-button').click(function () {
                    var profile  = jQuery('input#profilechannel_id_edit').val(); 
                    AJAXCallModuleJSOnly('" . __CLASS__ . "', 'delete_company_profile', 'profile='+profile);
                });\n
            ";

            print "jQuery('#update-company-profile-button').click(function () {
                    var branch  = jQuery('input#companyprofilename_edit').val(); 
                    var prefix  = jQuery('input#companyprofilecompanycode_edit').val(); 
                    var address  = jQuery('textarea#companyprofileaddress_edit').val(); 
                    var email  = jQuery('input#companyprofileemail_edit').val(); 
                    var contact_number  = jQuery('input#companyprofilecellphone_edit').val();    
                    var theme_colour  =  $(companyprofiletheme_edit).children(':selected').attr('id'); 
                    var approval_setting  = $(companyprofileapprovalsetting_edit).children(':selected').attr('id'); 
                    var notes  = jQuery('textarea#companyprofilenotes_edit').val(); 
                    var channel  = jQuery('input#companyprofilechannel_edit').val(); 
                    var branch_id  = jQuery('input#companyprofile_id_edit').val(); 
                    AJAXCallModuleJSOnly('" . __CLASS__ . "', 'update_company_profile', 'branch='+branch+'&prefix='+prefix+'&address='+address+'&email='+email+'&contact_number='+contact_number+'&theme_colour='+theme_colour+'&approval_setting='+approval_setting+'&notes='+notes+'&channel='+channel+'&branch_id='+branch_id);          
               });\n
            "; 

            print "jQuery('select#channel_permissions').change(function () { 
                    var id = $('select#channel_permissions').children(':selected').attr('id');               
                    $('input#channel_id_permissions').val(id); 
                    AJAXCallCompanyProfilePermissions('" . __CLASS__ . "', 'getCompaniesProfile', 'channelID='+id);  
                });\n
            ";
            print "jQuery('select#profilechannel_permissions').change(function () { 
                    var id = $('select#profilechannel_permissions').children(':selected').attr('id');               
                    $('input#profilechannel_id_permissions').val(id); 
                    AJAXCallCompanyProfileOwners('" . __CLASS__ . "', 'getCompanyMembers', 'branchID='+id);  
                });
            ";

            print "jQuery('#add-company-permissions-button').click(function () {
                    var owner = $('select#company_owner').children(':selected').attr('id');
                    var team = $('select#company_manageteam_user').children(':selected').attr('id');
                    var team2 = $('select#company_manageteam_user_2').children(':selected').attr('id');
                    var newsletter = $('select#company_managenewsletter_user').children(':selected').attr('id');
                    var newsletter2 = $('select#company_managenewsletter_user_2').children(':selected').attr('id');
                    var user = $('select#company_managenusers_user').children(':selected').attr('id');
                    var user2 = $('select#company_managenusers_user_2').children(':selected').attr('id'); 
                    var branch_id  = jQuery('input#profilechannel_id_permissions').val(); 
                    AJAXCallModuleJSOnly('" . __CLASS__ . "', 'add_company_profile_permissions', 'owner='+owner+'&team='+team+'&team2='+team2+'&newsletter='+newsletter+'&newsletter2='+newsletter2+'&user='+user+'&user2='+user2+'&branch_id='+branch_id);          
               });\n
            "; 
            print '</script>';
            print "<div style='margin: 0 auto; width: 95%; border-bottom: 2px solid #BBBBBB;'>&nbsp;</div>";
        }

        public function view_company_profile ($array) {

            print "<div class='classy_table'>";
            print "&nbsp;&nbsp;&nbsp;<span style='font-size:14px;font-weight:bold;'>View Company Profiles</span>";
            print "</div>";
            print "<br/><br/>";
            print '
                <head>
                    <script type="text/javascript" language="javascript" class="init">
                    $(document).ready(function() {
                        $("#profiles").dataTable( {
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
                    <table id="profiles" class="display" cellspacing="3" width="100%">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Cellphone/Telephone</th>
                                <th>Email</th>
                                <th>Address</th>
                                <th>Logo</th>
                                <th>Theme Colour</th>
                                <th>Approval Setting</th>
                                <th>Notes</th>
                                <th>Channel</th>
                            </tr>
                        </thead>
                        <tbody>';
                    $sysuser = new userType($_SESSION["userid"]);
                    $sysuser->isSuperAdmin;

                    if ($sysuser->isSuperAdmin){
                        $sql = "SELECT * FROM branches INNER JOIN `channels` ON `channels`.id =`branches`.channel WHERE deleted='0' AND `channels`.type = 'Franchises'";
                    }else{
                        $sql = "SELECT * FROM branches WHERE channel ='".$GLOBALS['system_user']->retailChannel."' AND deleted='0' ORDER BY `id` LIMIT 1";
                    }

                    $sqlres = mysqli_query($GLOBALS["link"],$sql);
                    while($row = mysqli_fetch_assoc($sqlres)) {
                        print "<tr>";
                        print "<td align='center'>".$row["branch"]."</td>";
                        print "<td align='center'>".$row["contact_number"]."</td>";
                        print "<td align='center'>".$row["email"]."</td>";
                        print "<td align='center'>".$row["address"]."</td>";
                        print "<td align='center'>".$row["logo"]."</td>";
                        print "<td align='center'>".$row["theme_colour"]."</td>";
                        print "<td align='center'>".$row["approval_setting"]."</td>";
                        print "<td align='center'>".$row["notes"]."</td>";
                        $channel =$this->getChannelName($row["channel"]);
                        print "<td align='center'>".$channel."</td>";
                        print "</tr>";
                    }
            print '</tbody>
                </table>
            </body>';
        }

        public function getChannelName($channel=""){
            $name="";
            if($channel !==""){
                $sql = "SELECT `name` FROM `channels` WHERE `id`=".$channel;
                $sqlres = mysqli_query($GLOBALS["link"],$sql);
                $row = mysqli_fetch_assoc($sqlres);
                $name = $row['name'];
            }
            return $name;
        }
        //========================================================================
        public function save_company_profile  ($array) {

            if ($GLOBALS['system_user']->hasPermission('manage_company_profile')) {

                if($array["channel"] =="" || $array["channel"]=="0"){
                    print "jQuery(\"#error-message\").html(\"Unable to save Company Profile Inforamtion! Please try again.".$array["channel"]."..\").show(0).delay(4000).hide(0);\n";
                }else{
                    $query  = "INSERT INTO `branches` (date_created,branch,prefix,address,email,contact_number,logo,theme_colour,approval_setting,notes,channel) VALUES (";
                    $query  .= "NOW(),";
                    $query  .= " '".$array["branch"]. "',";
                    $query  .= " '".$array["prefix"]. "',";
                    $query  .= " '".$array["address"]. "',";
                    $query  .= " '".$array["email"]. "',";
                    $query  .= " '".$array["contact_number"]. "',";
                    $query  .= " '".$array["logo"]. "',";
                    $query  .= " '".$array["theme_colour"]. "',";
                    $query  .= " '".$array["approval_setting"]. "',";
                    $query  .= " '".$array["notes"]. "',";
                    $query  .= " '".$array["channel"]. "'";
                    $query  .= ") ";
                    $result = mysqli_query($GLOBALS["link"],$query);     
                    if($result) {
                        print "jQuery(\"#ok-message\").html(\"Company Profile saved...  \").show(0).delay(4000).hide(0);\n";
                        logAction("Added Company Profile:$branch");
                    } else {
                        print "jQuery(\"#error-message\").html(\"Unable to save Company Profile Inforamtion! Please try again...\").show(0).delay(4000).hide(0);\n";
                    } 
                }
                exit();
            }else{
                print "You do not have permission to perform this action.";
                exit();            
            }
        }

        public function update_company_profile  ($array) {

            if ($GLOBALS['system_user']->hasPermission('manage_company_profile')) {

                $query = "UPDATE branches SET";
                $query .= " `last_updated` =NOW(),";
                $query  .= "`branch` = '".$array["branch"]. "',";
                $query  .= "`prefix` = '".$array["prefix"]. "',";
                $query  .= "`address` = '".$array["address"]. "',";
                $query  .= "`email` = '".$array["email"]. "',";
                $query  .= "`contact_number` = '".$array["contact_number"]. "',";
                $query  .= "`theme_colour` = '".$array["theme_colour"]. "',";
                $query  .= "`approval_setting` = '".$array["approval_setting"]. "',";
                $query  .= "`notes` = '".$array["notes"]. "',";
                $query  .= "`channel` = '".$array["channel"]. "'";
                $query .= " WHERE id=".$array["branch_id"]." LIMIT 1";
                $result = mysqli_query($GLOBALS["link"],$query);   
                if($result) {
                    print "jQuery(\"#ok-message2\").html(\"Company Profile updated...  \").show(0).delay(4000).hide(0);\n";
                    logAction("Updated Company Profile:$branch");
                } else {
                    print "jQuery(\"#error-message2\").html(\"Unable to update Company Profile Inforamtion! Please try again...\").show(0).delay(4000).hide(0);\n";
                }  
                exit();
            }else{
                print "You do not have permission to perform this action.";
                exit();            
            }
        }

        public function getCompaniesProfile  ($array) {
            $channelID = (int)isset($_POST["channelID"])?$_POST["channelID"]:0;
            $query  = "SELECT `branches`.id,`branches`.branch";
            $query .= " FROM `branches`";
            $query .= " WHERE `branches`.channel= ".$channelID;

            if($GLOBALS['system_user']->hasPermission('manage_company_profile')) {                    
                $result = mysqli_query($GLOBALS["link"],$query);
                $array = array();
                while ($arr = mysqli_fetch_assoc($result)){
                    $array[] = $arr; 
                }
                print json_encode($array);
            } else {
                print "jQuery(\"#error-message2\").html(\"Unable to get Company Profiles information! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
            }
        }

        public function getCompanyProfileInfo  ($array) {
            $profileID = (int)isset($_POST["profileID"])?$_POST["profileID"]:0;
            $query  = "SELECT *,`branches`.id AS branches_id,`channels`.name AS channel_name";
            $query .= " FROM `branches` INNER JOIN `channels` ON `channels`.id = `branches`.channel";
            $query .= " WHERE `branches`.id='".$profileID."' LIMIT 1";

            if($GLOBALS['system_user']->hasPermission('manage_company_profile')) {                    
                $result = mysqli_query($GLOBALS["link"],$query);
                $arr = mysqli_fetch_assoc($result);
                print json_encode($arr);
            } else {
                print "jQuery(\"#error-message2\").html(\"Unable to get Company Profile information! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
            }
        }

        public function getCompanyMembers  ($array) {
            $branchID = (int)isset($_POST["branchID"])?$_POST["branchID"]:0;
            $query  = "SELECT *";
            $query .= " FROM `system_users`";
            $query .= " WHERE `system_users`.branch_id= ".$branchID;

            if($GLOBALS['system_user']->hasPermission('manage_company_profile')) {                    
                $result = mysqli_query($GLOBALS["link"],$query);
                $array = array();
                while ($arr = mysqli_fetch_assoc($result)){
                    $array[] = $arr; 
                }
                $array[] ="permissions";
                $query  = "SELECT user_id,type";
                $query .= " FROM `branches_profile_permissions`";
                $query .= " WHERE deleted='0' AND `branches_profile_permissions`.branch=".$branchID;
                $result = mysqli_query($GLOBALS["link"],$query);
                while ($arr = mysqli_fetch_assoc($result)){
                    $array[] = $arr; 
                }
                
                print json_encode($array);
            } else {
                print "jQuery(\"#error-message-permissions\").html(\"Unable to get Company Members information! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
            }
        }
        public function add_company_profile_permissions ($array) {

            if ($GLOBALS['system_user']->hasPermission('manage_company_profile')) {
                $arrMembers = array();
                $arrType = array();
                $branch = $array["branch_id"];
                $count = 0;

                if($array["owner"] != "")$arrMembers[] = $array["owner"];
                if($array["team"] != ""){
                    $arrMembers[] = $array["team"];
                }elseif($array["owner"] != ""){
                    //default
                    $arrMembers[] = $array["owner"];
                }
                if($array["team2"] != ""){
                    $arrMembers[] = $array["team2"];
                }elseif($array["owner"] != ""){
                    //default
                    $arrMembers[] = $array["owner"];
                }
                if($array["newsletter"] != ""){
                    $arrMembers[] = $array["newsletter"];
                }elseif($array["owner"] != ""){
                    //default
                    $arrMembers[] = $array["owner"];
                }
                if($array["newsletter2"] != ""){
                    $arrMembers[] = $array["newsletter2"];
                }elseif($array["owner"] != ""){
                    //default
                    $arrMembers[] = $array["owner"];
                }
                if($array["user"] != ""){
                    $arrMembers[] = $array["user"];
                }elseif($array["owner"] != ""){
                    //default
                    $arrMembers[] = $array["owner"];
                }
                if($array["user2"] != ""){
                    $arrMembers[] = $array["user2"];
                }elseif($array["owner"] != ""){
                    //default
                    $arrMembers[] = $array["owner"];
                }

                $arrType[] = "Owner";
                $arrType[] = "Teams";
                $arrType[] = "Teams";
                $arrType[] = "Newsletter";
                $arrType[] = "Newsletter";
                $arrType[] = "Users";
                $arrType[] = "Users";

                for ($i=0; $i < count($arrMembers); $i++) { 
                    if($arrMembers[$i] !=""){
                        $query  = "INSERT INTO `branches_profile_permissions` (date_created,user_id,type,branch,deleted) VALUES (";
                        $query  .= "NOW(),";
                        $query  .= " '".$arrMembers[$i]."',";
                        $query  .= " '".$arrType[$i]."',";
                        $query  .= " '".$branch."',";
                        $query  .= " '0'";
                        $query  .= ") ";
                        $result = mysqli_query($GLOBALS["link"],$query);
                        if($result) {
                            if($arrMembers[0] !=""){
                                $query = "UPDATE branches SET";
                                $query .= " `last_updated` =NOW(),";
                                $query  .= "`branch_owner` = '".$arrMembers[0]. "'";
                                $query .= " WHERE id=".$branch." LIMIT 1";
                                mysqli_query($GLOBALS["link"],$query); 
                            }
                            $count += 1;
                        }
                    }
                }
                     
                if($count > 0) {
                    print "jQuery(\"#ok-message-permissions\").html(\"Company Permissions saved...  \").show(0).delay(4000).hide(0);\n";
                    logAction("Added Company Permissions:$branch");
                } else {
                    print "jQuery(\"#error-message-permissions\").html(\"Unable to save Company Permissions Inforamtion! Please try again...\").show(0).delay(4000).hide(0);\n";
                }  
                exit();
            }else{
                print "You do not have permission to perform this action.";
                exit();            
            }
        }
        public function delete_company_profile ($array) {
            if ($GLOBALS['system_user']->hasPermission('manage_company_profile')) {
                $sql = "UPDATE branches SET";
                $sql .= " `last_updated` =NOW(),";
                $sql .= " `deleted` = '1'";
                $sql .= " WHERE id=".$array["profile"]." LIMIT 1";
                $result = mysqli_query($GLOBALS["link"],$sql);
                if($result) {
                    print "jQuery(\"#ok-message2\").html(\"Company Profile deleted...  \").show(0).delay(4000).hide(0);\n";
                    $id = $array["profile"];
                    logAction("Deleted Company Profile:$id");
                } else {
                   print "jQuery(\"#error-message2\").html(\"Unable to delete Company Profile Inforamtion! Please try again...\").show(0).delay(4000).hide(0);\n";
                }  
                exit();
            }else{
                print "You do not have permission to perform this action.";
                exit();            
            }
        }
    }
?>