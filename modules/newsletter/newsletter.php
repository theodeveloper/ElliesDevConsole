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

register_menu("Newsletters", "submenu", "Newsletters",
    Array(
        Array("title" => "View Sent Newsletters",  "location" => "view_news_letter",   "acl" => "view_news_letter"),
        Array("title" => "Manage Newsletters",  "location" => "manage_news_letter",   "acl" => "manage_news_letter"),
        Array("title" => "Manage Newsletter Templates",  "location" => "manage_news_letter_types",   "acl" => "manage_news_letter_types")
    )
);

register_permission("Newsletters Permissions", "view_news_letter", "View Sent Newsletters");
register_permission("Newsletters Permissions", "manage_news_letter", "Manage Newsletters");
register_permission("Newsletters Permissions", "manage_news_letter_types",  "Manage Newsletter Templates");


class Newsletters {
    private $items_per_page;
    
    public function __construct () {
        $this->items_per_page = Settings::getSetting(2);
    }

    public function getChannelName($channel=""){
        $channelname="";
        if($channel !==""){
            $sql = "SELECT `name` FROM `channels` WHERE `id`=".$channel;
            $sqlres = mysqli_query($GLOBALS["link"],$sql);
            $row = mysqli_fetch_assoc($sqlres);
            $channelname = $row['name'];
        }
        return $channelname;
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

    public function getTotalSent($newsletter=0){
        $total=0;
        if($newsletter !==0){
            $sql = "SELECT `total_sent` FROM `news_letter_summary_log` WHERE `newslettter`=".$newsletter;
            $sqlres = mysqli_query($GLOBALS["link"],$sql);
            $row = mysqli_fetch_assoc($sqlres);
            $total = $row['total_sent'];
            if($total =="")$total = 0;
        }
        return $total;
    }

    public function view_news_letter ($array) {

        print "<div class='classy_table'>";
        print "&nbsp;&nbsp;&nbsp;<span style='font-size:14px;font-weight:bold;'>View Newsletters</span>";
        print "</div>";
        print "<br/><br/>";
        print '
                <body>
                    <table id="viewnewslettersetup" name="viewnewslettesetup" class="display" cellspacing="2" width="100%">
                        <thead>
                            <tr>
                                <th>Date Created</th>
                                <th>Subject</th>
                                <th>Sent To</th>
                                <th>Newsletter Type</th>
                                <th>Branch</th>
                                <th>Total Sent</th>            
                            </tr>
                        </thead>
                        <tbody>';
                            $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
                            $sqlres = mysqli_query($GLOBALS["link"],$sql);
                            $row = mysqli_fetch_assoc($sqlres);
                            $channeltype = $row['type'];

                            $query  = "";
                            $sysuser = new userType($_SESSION["userid"]);
                            if ($sysuser->isSuperAdmin){
                                $query  = "SELECT `news_letter`.id, `news_letter`.date_created, `news_letter`.subject, `news_letter`.sent_to,`news_letter`.branch,`news_letter_types`.newsletter_type";
                                $query .= " FROM `news_letter` INNER JOIN news_letter_types ON `news_letter_types`.id = `news_letter`.news_letter_type INNER JOIN `channels` ON `channels`.id = `news_letter`.channel AND channels.type='".$channeltype."'";
                                $query .= " WHERE `news_letter`.deleted = '0'";
                            }else{
                                $query  = "SELECT `news_letter`.id, `news_letter`.date_created, `news_letter`.subject, `news_letter`.sent_to,`news_letter`.branch,`news_letter_types`.newsletter_type";
                                $query .= " FROM `news_letter` INNER JOIN news_letter_types ON `news_letter_types`.id = `news_letter`.news_letter_type";  
                                $query .= " WHERE `news_letter`.branch =".$GLOBALS['system_user']->branchID;                          
                            }                            
                            $query .= " ORDER BY `news_letter`.id";
                            $result = mysqli_query($GLOBALS["link"],$query);
                            while ($arr = mysqli_fetch_assoc($result)) {
                               print "<tr>";
                                    print '<td align="center">'.date("Y-m-d", strtotime($arr['date_created'])).'</td>';
                                    print '<td align="center">'.$arr['subject'].'</td>';
                                    print '<td align="center">'.$arr['sent_to'] .'</td>';
                                    print '<td align="center">'.$arr['newsletter_type'] .'</td>';
                                    $branchname = $this->getBranch($arr['branch']);
                                    print '<td align="center">'.$branchname.'</td>';
                                    print '<td align="center">'.$this->getTotalSent($arr['id']).'</td>';                            
                                print "</tr>";
                            }

                    print "</tbody>";
                    print "</table>";
                    print '<script type="text/javascript" language="javascript" class="init">
                        $(document).ready(function() {
                            $("#viewnewslettersetup").dataTable( {
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

    public function manage_news_letter ($array) {

        print "<div class='classy_table'>";
        print "&nbsp;&nbsp;&nbsp;<span style='font-size:14px;font-weight:bold;'>Manage Newsletters</span>";
        print "</div>";
        print "<p style='float:right'><a href='#' id='editNewsLetter' name='editNewsLetter'><u>Edit</u></a> | <a href='#' id='addNewsLetter' name='addNewsLetter'><u>Send</u></a></p>";
        print "<table id='editnewslettersetup' name='editnewslettersetup' cellspacing='0' cellpadding='4' border='0' width='100%'>";
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
                                    print "<select id='branchnewsletter_edit'>";
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
                                    print "<input type='text' id='branch_name' style='width: 200px' value='".$branch_name."' readonly/>";
                                print "</td>";
                            print "</tr>";
                        }

                        print "<tr>";
                        print "<td colspan='2' style='text-align: right; font-weight: 400; color: #111111; padding-right: 20px;'>";                                 
                            print "Select Newsletter";
                            print "<input type='hidden' id='newsletter_id' style='width: 200px' />"; 
                        print "</td>"; 
                        print "<td>";     
                            print "<select id='newsletter_edit'>";
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
                                        print "Newsletter Type";
                                     print "</td>";
                                     print "<td>";
                                        print "<input type='text' id='newslettertype_edit' style='width: 200px' />";        
                                     print "</td>";
                                print "</tr>";

                                print "<tr>";
                                     print "<td>";
                                        print "Subject";
                                     print "</td>";
                                     print "<td>";
                                        print "<input type='text' id='newslettersubject_edit' style='width: 200px' />";        
                                     print "</td>";
                                print "</tr>";

                                print "<tr>";
                                 print "<td>";
                                    print "Sent To";
                                 print "</td>";
                                 print "<td>";
                                    print "<input type='text' id='newslettersentto_edit' style='width: 200px' />"; 
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
                                print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button class='ibutton' id='delete-newsletter-button' style='color:red;font-weight: bold;font-size: 12px;' >Delete Newsletter</button>";
                                print "<br/><br/>";
                            print "</td>";
                        print "</tr>";
                    print "</tfoot>";
        print "</table>";
        print "<table id='addnewslettersetup' name='addnewslettersetup' cellspacing='0' cellpadding='4' border='0' width='100%'>";
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
                                    print "Newsletter Template";
                                 print "</td>";
                                 print "<td>";
                                 $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
                                 $sqlres = mysqli_query($GLOBALS["link"],$sql);
                                 $row = mysqli_fetch_assoc($sqlres);
                                 $channeltype = $row['type'];
                                 $sysuser = new userType($_SESSION["userid"]);

                                 $query = "select id, newsletter_type from news_letter_types where channel='".$GLOBALS['system_user']->retailChannel."' AND deleted='0'";
                                 if ($sysuser->isSuperAdmin){
                                    $query = "select news_letter_types.id, newsletter_type from news_letter_types INNER JOIN channels ON channels.id = news_letter_types.channel AND channels.type='".$channeltype."' where deleted='0'";
                                 } 
                                 $result = mysqli_query($GLOBALS["link"],$query);
                                 print "<select id='addnewsletter_type'>";
                                    print "<option id='' value='' selected='selected'>[Please select]</option>";
                                    while ($row=mysqli_fetch_assoc($result)){
                                        print "<option id='".$row['id']."' value='".$row["id"]."'>";
                                            print ucfirst($row["newsletter_type"]);
                                        print "</option>";
                                    }
                                 print "</select>";
                                print "</td>";
                             print "</tr>";

                             print "<tr>";
                                 print "<td>";
                                    print "Subject";
                                 print "</td>";
                                 print "<td>";
                                    print "<input type='text' id='addnewslettersubject' style='width: 200px'  readonly/>";        
                                 print "</td>";
                             print "</tr>";

                            //Channel Type
                            $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
                            $sqlres = mysqli_query($GLOBALS["link"],$sql);
                            $row = mysqli_fetch_assoc($sqlres);
                            $channeltype = $row['type'];

                            if ($sysuser->isSuperAdmin && $channeltype =="Franchises"){
                                print "<tr id='FranchisesChannelAdmin'>";
                                        print "<td>";
                                            print "Franchise(s)";
                                        print "</td>";
                                       print "<td>";                         
                                            $query = "select id, name from channels where type='Franchises'";
                                            $result = mysqli_query($GLOBALS["link"],$query);
                                            print "<select id='franchiseadmin_channel'>";
                                                print "<option id='' value='' selected='selected'>[Please select]</option>";
                                                print "<option id='All' value='All'>All</option>";
                                                while ($row=mysqli_fetch_assoc($result)){
                                                    print "<option id='".$row['id']."' value='".$row["id"]."'>";
                                                        print ucfirst($row["name"]);
                                                    print "</option>";
                                                }
                                            print "</select>"; 
                                            print "<input type='hidden' id='newsletter_channeladmin' style='width: 200px' value='franchiseadmin' readonly/>";
                                        print "</td>";
                                print "</tr>";
                            }elseif ($sysuser->isSuperAdmin && $channeltype =="Commercial"){
                               /* print "<tr>";
                                        print "<td>";
                                            print "Channel type";
                                        print "</td>";
                                        print "<td>";                         
                                        print "<select id='addnewsletter_channel'>";
                                            print "<option id='' value='' selected='selected'>[Please select]</option>";
                                            print "<option id ='Commercial' value='Commercial'>Commercial</option>";
                                            print "<option id='Retail' value='Retail'>Retail</option>";
                                            print "<option id='Franchises' value='Franchises'>Franchises</option>";
                                        print "</select>"; 
                                         print "<input type='hidden' id='newsletter_channel' style='width: 200px'  readonly/>";
                                        print "</td>";
                                print "</tr>";*/
                                //Channels   
                                print "<tr id='CommercialChannel'>";
                                        print "<td>";
                                            print "Channel";
                                        print "</td>";
                                        print "<td>";                         
                                        $query = "select id, name from channels where type='Commercial'";
                                        $result = mysqli_query($GLOBALS["link"],$query);
                                        print "<select id='commercial_channel'>";
                                            print "<option id='' value='' selected='selected'>[Please select]</option>";
                                            print "<option id='All' value='All'>All</option>";
                                            while ($row=mysqli_fetch_assoc($result)){
                                                print "<option id='".$row['id']."' value='".$row["id"]."'>";
                                                    print ucfirst($row["name"]);
                                                print "</option>";
                                            }
                                        print "</select>"; 
                                        print "</td>";
                                print "</tr>";
                                /*print "<tr id='RetailChannel'>";
                                        print "<td>";
                                            print "Channel";
                                        print "</td>";
                                        print "<td>";                         
                                        $query = "select id, name from channels where type='Retail'";
                                        $result = mysqli_query($GLOBALS["link"],$query);
                                        print "<select id='retail_channel'>";
                                            print "<option id='' value='' selected='selected'>[Please select]</option>";
                                            print "<option id='All' value='All'>All</option>";
                                            while ($row=mysqli_fetch_assoc($result)){
                                                print "<option id='".$row['id']."' value='".$row["id"]."'>";
                                                    print ucfirst($row["name"]);
                                                print "</option>";
                                            }
                                        print "</select>"; 
                                        print "</td>";
                                print "</tr>";*/
                                /*print "<tr id='FranchisesChannel'>";
                                        print "<td>";
                                            print "Channel";
                                        print "</td>";
                                       print "<td>";                         
                                        $query = "select id, name from channels where type='Franchises'";
                                        $result = mysqli_query($GLOBALS["link"],$query);
                                        print "<select id='franchise_channel'>";
                                            print "<option id='' value='' selected='selected'>[Please select]</option>";
                                            print "<option id='All' value='All'>All</option>";
                                            while ($row=mysqli_fetch_assoc($result)){
                                                print "<option id='".$row['id']."' value='".$row["id"]."'>";
                                                    print ucfirst($row["name"]);
                                                print "</option>";
                                            }
                                        print "</select>"; 
                                        print "</td>";
                                print "</tr>";*/
                            }elseif ($sysuser->isSuperAdmin && $channeltype =="Retail"){
                                print "<tr id='RetailChannel'>";
                                        print "<td>";
                                            print "Channel";
                                        print "</td>";
                                        print "<td>";                         
                                        $query = "select id, name from channels where type='Retail'";
                                        $result = mysqli_query($GLOBALS["link"],$query);
                                        print "<select id='retail_channel'>";
                                            print "<option id='' value='' selected='selected'>[Please select]</option>";
                                            print "<option id='All' value='All'>All</option>";
                                            while ($row=mysqli_fetch_assoc($result)){
                                                print "<option id='".$row['id']."' value='".$row["id"]."'>";
                                                    print ucfirst($row["name"]);
                                                print "</option>";
                                            }
                                        print "</select>"; 
                                        print "</td>";
                                print "</tr>";
                            }else{//Non-Admin
                                //print "<tr>";
                                   // print "<td>";
                                       // print "Branch";
                                    //print "</td>";
                                   // print "<td>";                         
                                        print "<select id='addnewsletter_branch' style='display:none;'>";
                                        print "<option id='".$GLOBALS['system_user']->branchID."' value='".$GLOBALS['system_user']->branchID."' selected></option>";
                                        print "</select>"; 
                                        print "<input type='hidden' id='addnewsletter_branch_channel' style='width: 200px' value='".$GLOBALS['system_user']->retailChannel."' readonly/>";
                                    //print "</td>";
                                //print "</tr>";
                            }

                            print "<tr>";
                                 print "<td>";
                                    print "Send to";
                                 print "</td>";
                                 print "<td>";

                                    //Channel Type
                                    $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
                                    $sqlres = mysqli_query($GLOBALS["link"],$sql);
                                    $row = mysqli_fetch_assoc($sqlres);
                                    $channeltype = $row['type'];

                                    //Member permission
                                    $sql = "SELECT `type` FROM `branches_profile_permissions` WHERE `user_id`=".$GLOBALS['system_user']->id." AND `branch`=".$GLOBALS['system_user']->branchID;
                                    $sqlres = mysqli_query($GLOBALS["link"],$sql);
                                    $row = mysqli_fetch_assoc($sqlres);
                                    $member_permission = $row['type'];

                                    $sysuser = new userType($_SESSION["userid"]);
                                    print "<select id='addnewslettersentto'>";
                                        print "<option id='' value='' selected='selected'>[Please select]</option>";

                                        if($channeltype =="Franchises" && $sysuser->isSuperAdmin ==true && ($member_permission == "Owner"  || $member_permission == "Newsletter")){
                                        //if($channeltype =="Franchises" && $sysuser->isSuperAdmin ==true){
                                           
                                            print "<optgroup id='System Users' label='Teams'>";
                                            $sql = "select id,title from teams where deleted='0'";
                                            $sqlres = mysqli_query($GLOBALS["link"],$sql);
                                            while ($row=mysqli_fetch_assoc($sqlres)){
                                                print "<option id='".$row['id']."' value='".$row["id"]."'>";
                                                    print "Team-".ucfirst($row["title"]);
                                                print "</option>";
                                            }
                                            print "</optgroup>";
                                            print "<option id='Users' value='Users'>Users</option>";
                                            print "<option id='Customers' value='Customers'>Customers</option>";
                                            //print "<option id='BothFranchise' value='BothFranchise'>Both</option>";
                                        }elseif($channeltype =="Franchises" && $sysuser->isSuperAdmin ==true){
                                         //}elseif($channeltype =="Franchises"  && ($member_permission == "Owner"  || $member_permission == "Newsletter")){
                                            print "<option id='Owners' value='Owners'>Owners</option>";
                                            print "<option id='Customers' value='Customers'>Customers</option>";

                                        }elseif($channeltype !="Franchises" && $sysuser->isSuperAdmin ==true){
                                            print "<option id='Users' value='Users'>Users</option>";
                                            print "<option id='Customers' value='Customers'>Customers</option>";
                                            print "<option id='Both' value='Both'>Both</option>";
                                        }elseif($member_permission == "Owner"  || $member_permission == "Newsletter"){
                                            print "<optgroup id='System Users' label='Teams'>";
                                            $sql = "select id,title from teams where deleted='0'";
                                            $sqlres = mysqli_query($GLOBALS["link"],$sql);
                                            while ($row=mysqli_fetch_assoc($sqlres)){
                                                print "<option id='".$row['id']."' value='".$row["id"]."'>";
                                                    print "Team-".ucfirst($row["title"]);
                                                print "</option>";
                                            }
                                            print "</optgroup>";
                                            print "<option id='Users' value='Users'>Users</option>";
                                            print "<option id='Customers' value='Customers'>Customers</option>";
                                        }
                                    print "</select>";
                                 print "</td>";
                            print "</tr>"; 

                            print "<tr id='CustomerSelect' style='display:none'>";
                                    print "<td>";
                                        print "Select customer(s)";
                                    print "</td>";
                                    print "<td>";     
                                    $query = "";
                                    if($member_permission == "Owner"  || $member_permission == "Newsletter"){
                                        $query  = "SELECT  *";
                                        $query .= " FROM `customers` WHERE `channel`='".$GLOBALS['system_user']->retailChannel."'";
                                        $query .= " ORDER BY `id`";  
                                        print "<input type='hidden' id='addnewsletter_frachasisechannelOwnerAdmin' style='width: 200px' value='".$GLOBALS['system_user']->retailChannel."' readonly/>";
                                    }

                                    print "<select id='customer_channel'name='customer_channel' multiple='multiple'>";
                                        if($member_permission == "Owner"  || $member_permission == "Newsletter"){
                                            $result = mysqli_query($GLOBALS["link"],$query);  
                                            while ($arr = mysqli_fetch_assoc($result)) {
                                                $Customer = ucfirst($arr['name'])." ".ucfirst($arr['surname']);
                                                print "<option id='".$arr['id']."' value='".$arr['id']."'>";
                                                    print ucfirst($Customer);
                                                print "</option>";
                                            } 
                                        }
                                    print "</select>"; 
                                    print "</td>";
                            print "</tr>";

                            //Branches 
                            if ($sysuser->isSuperAdmin && $channeltype =="Commercial"){  
                                print "<tr id='CommercialBranch'>";
                                        print "<td>";
                                            print "Branch";
                                        print "</td>";
                                        print "<td>";                         
                                        print "<select id='commercial_branch'>";
                                            print "<option id='' value='' selected='selected'>[Please select]</option>";
                                        print "</select>"; 
                                        print "</td>";
                                print "</tr>";
                            }elseif ($sysuser->isSuperAdmin && $channeltype =="Retail"){
                                print "<tr id='RetailBranch'>";
                                        print "<td>";
                                            print "Branch";
                                        print "</td>";
                                        print "<td>";                         
                                        print "<select id='retail_branch'>";
                                            print "<option id='' value='' selected='selected'>[Please select]</option>";
                                        print "</select>"; 
                                        print "</td>";
                                print "</tr>";
                            }
                            /*print "<tr id='FranchisesBranch'>";
                                    print "<td>";
                                        print "Branch";
                                    print "</td>";
                                    print "<td>";                         
                                    print "<select id='franchise_branch'>";
                                        print "<option id='' value='' selected='selected'>[Please select]</option>";
                                    print "</select>"; 
                                    print "</td>";
                            print "</tr>";*/
                            if ($sysuser->isSuperAdmin && $channeltype =="Franchises"){
                                print "<tr id='FranchisesBranchAdmin'>";
                                    print "<td>";
                                        print "Branch";
                                    print "</td>";
                                    print "<td>";                         
                                    print "<select id='franchiseadmin_branch'>";
                                        print "<option id='' value='' selected='selected'>[Please select]</option>";
                                    print "</select>"; 
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
                            print "<span id='ok-message'></span><span id='error-message'style='color:red'></span>";
                            print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button class='ibutton' id='add-newsletter-button'>Send Newsletter</button>";
                            print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a id='previewnewsletter' href='#'><button class='ibutton' id='preview-newsletter-button'>Preview Newsletter</button></a>";

                            print "<br/><br/>";
                        print "</td>";
                    print "</tr>";
                print "</tfoot>";
        print "</table>";
        //==========================================
        print '<script type="text/javascript" language="javascript" class="init">';
        print "jQuery('a#addNewsLetter').click(function () {
                $('table#editnewslettersetup').hide();
                $('table#addnewslettersetup').show(); 
            });\n
        ";
        
        print "$('table#editnewslettersetup').hide();
            jQuery('a#editNewsLetter').click(function () {
                $('table#addnewslettersetup').hide();
                $('table#editnewslettersetup').show(); 
            });\n
        ";

        print "jQuery('select#branchnewsletter_edit').change(function () { 
                    var id = $('branchnewsletter_edit').children(':selected').attr('id');               
                    $('input#branch_id_edit').val(id); 
                    AJAXCallNewsLetter('" . __CLASS__ . "', 'getNewsletters', 'branchID='+id);  
                });\n
        ";

        print "jQuery('select#newsletter_edit').change(function () { 
                    var id = $('newsletter_edit').children(':selected').attr('id');               
                    $('input#newsletter_id').val(id); 
                    AJAXCallNewsLetterInfo('" . __CLASS__ . "', 'getNewsLetterInfo', 'newsletterID='+id);  
                });\n
            ";

        print "jQuery('select#addnewsletter_type').change(function () { 
                var id = $('select#addnewsletter_type').children(':selected').attr('id');               
                AJAXCallNewsLetterType('" . __CLASS__ . "', 'getNewsLetterTypeInfo2', 'newslettertypeID='+id);  
            });\n
        ";

        //Franchise Admin
         print "jQuery('select#franchiseadmin_channel').change(function () { 
                    var id = $('select#franchiseadmin_channel').children(':selected').attr('id'); 
                    var select = $('select#addnewslettersentto');
                    var selectedItem= select.find(':selected');
                    var sent_to = selectedItem.attr('id');
                    if(id =='All' || sent_to =='Customers'){
                        $('tr#FranchisesBranchAdmin').hide(); 
                        $('select#franchiseadmin_branch').val(''); 
                        AJAXCallCustomers('". __CLASS__ . "', 'getCustomersAdmin', 'customers='+id); 
                    }else{
                        $('tr#FranchisesBranchAdmin').show(); 
                        AJAXCallFranchisesBranchesAdmin('" . __CLASS__ . "', 'getChannelBranches', 'channel='+id); 
                        AJAXCallCustomers('". __CLASS__ . "', 'getCustomersAdmin', 'customers='+id);  
                    }
                });\n";

        //Channels and Branches
        /*print " $('tr#RetailChannel').hide();
                $('tr#FranchisesChannel').hide();
                $('tr#CommercialChannel').hide();

                $('tr#RetailBranch').hide();
                $('tr#FranchisesBranch').hide();
                $('tr#CommercialBranch').hide();
                jQuery('select#addnewsletter_channel').change(function () { 
                    var value = $('select#addnewsletter_channel').children(':selected').attr('id'); 
                    $('input#newsletter_channel').val(value);   
                    AJAXCallCustomers('" . __CLASS__ . "', 'getCustomers', 'customers='+value); 
                    if(value == 'Commercial'){
                        $('tr#RetailChannel').hide();
                        $('tr#FranchisesChannel').hide();
                        $('tr#CommercialChannel').show();

                        $('tr#RetailBranch').hide();
                        $('tr#FranchisesBranch').hide();
                        $('tr#CommercialBranch').show();
                    }else if(value == 'Retail'){
                        $('tr#CommercialChannel').hide();
                        $('tr#FranchisesChannel').hide();
                        $('tr#RetailChannel').show();

                        $('tr#CommercialBranch').hide();
                        $('tr#FranchisesBranch').hide();
                        $('tr#RetailBranch').show();
                    }else if(value == 'Franchises'){
                        $('tr#CommercialChannel').hide();
                        $('tr#RetailChannel').hide();
                        $('tr#FranchisesChannel').show();

                        $('tr#CommercialBranch').hide();
                        $('tr#RetailBranch').hide();
                        $('tr#FranchisesBranch').show();
                    }       
                });\n";*/

        print "jQuery('select#commercial_channel').change(function () { 
                var id = $('select#commercial_channel').children(':selected').attr('id'); 
                if(id =='All'){
                   $('tr#CommercialBranch').hide(); 
               }else{
                   $('tr#CommercialBranch').show(); 
                   AJAXCallCommercialBranches('" . __CLASS__ . "', 'getChannelBranches', 'channel='+id);
               }   
            });\n
        ";

        print "jQuery('select#retail_channel').change(function () { 
                var id = $('select#retail_channel').children(':selected').attr('id'); 
                if(id =='All'){
                   $('tr#RetailBranch').hide(); 
                }else{
                   $('tr#RetailBranch').show(); 
                   AJAXCallRetailBranches('" . __CLASS__ . "', 'getChannelBranches', 'channel='+id); 
                } 
            });\n
        ";

        print "jQuery('select#franchise_channel').change(function () { 
                var id = $('select#franchise_channel').children(':selected').attr('id');
                if(id =='All'){
                   $('tr#FranchisesBranch').hide(); 
                }else{ 
                    $('tr#FranchisesBranch').show();
                    AJAXCallFranchisesBranches('" . __CLASS__ . "', 'getChannelBranches', 'channel='+id);   
                }        
            });\n
        ";

        print "jQuery('select#addnewslettersentto').change(function () { 
                    var select = $('select#addnewslettersentto');
                    var selectedItem= select.find(':selected');
                    var sent_to = selectedItem.attr('id');
                    if(sent_to =='Customers'){
                        document.getElementById('CustomerSelect').style.display = '';
                        var id = $('input#addnewsletter_frachasisechannelOwnerAdmin').val(); 
                        if(id == undefined)id =$('select#franchiseadmin_channel').children(':selected').attr('id'); 
                        AJAXCallCustomers('" . __CLASS__ . "', 'getCustomersAdmin', 'customers='+id);

                        $('select#retail_branch').val(''); 
                        $('select#commercial_branch').val('');  
                        $('select#franchise_branch').val(''); 
                        $('select#franchiseadmin_branch').val(''); 

                        $('tr#RetailBranch').hide();
                        $('tr#CommercialBranch').hide();
                        $('tr#FranchisesBranch').hide();
                        $('tr#FranchisesBranchAdmin').hide();
                    }else{
                        document.getElementById('CustomerSelect').style.display = 'none'; 
                        var channeltype =   $('input#newsletter_channel').val(); 
                        var channel =''; 

                        var franchiseadmin = $('select#franchiseadmin_channel').children(':selected').attr('id'); 

                        if(channeltype == 'Commercial'){ 

                            channel = $('select#commercial_channel').children(':selected').attr('id'); 
                            if(channeltype == 'Commercial' && channel !='All') $('tr#CommercialBranch').show();
                        }else if(channeltype == 'Retail'){

                            channel = $('select#retail_channel').children(':selected').attr('id'); 
                            if(channeltype == 'Retail' && channel !='All') $('tr#RetailBranch').show();
                        }else if(channeltype == 'Franchises'){

                            channel = $('select#franchise_channel').children(':selected').attr('id'); 
                            if(channeltype == 'Retail' && channel !='All') $('tr#FranchisesBranch').show();
                        }else if(franchiseadmin != ''){
                            var id =franchiseadmin;
                            AJAXCallFranchisesBranchesAdmin('" . __CLASS__ . "', 'getChannelBranches', 'channel='+id); 
                            AJAXCallCustomers('" . __CLASS__ . "', 'getCustomersAdmin', 'customers='+id); 
                            $('tr#FranchisesBranchAdmin').show(); 
                            //alert(sent_to);
                            if(sent_to =='Customers')AJAXCallCustomers('" . __CLASS__ . "', 'getCustomersAdmin', 'customers='+id);   
                            if(sent_to > 0) {
                                $('tr#FranchisesBranchAdmin').hide();
                                $('select#franchiseadmin_branch').val('');  
                            }

                        }
                    }
                });\n";
        
        print "$('select#customer_channel').multiselect();
                $('select#customer_channel').change(function() {
                var customers = $(this).val();
                var count = customers.length;
                console.log($(this).val());
                console.log(customers[0]);
                });\n";

    
        print "jQuery('#preview-newsletter-button').click(function () {
              var newsletter_type = $('select#addnewsletter_type').children(':selected').attr('id'); 
              var preview = 'viewnewsletter.php?id=' + newsletter_type;
                if(newsletter_type != ''){
                    jQuery('a#previewnewsletter').attr('href', preview);
                }else{
                    jQuery('a#previewnewsletter').attr('href', '#');
                }
            });\n "; 

        print "jQuery('#add-newsletter-button').click(function () {
                    var newsletter_type = $('select#addnewsletter_type').children(':selected').attr('id');  
                    var subject  = jQuery('input#addnewslettersubject').val(); 
                    var channeltype = $('input#newsletter_channel').val();
                    var channelFranchiseAdmin = $('input#newsletter_channeladmin').val();
                    
                    var channel = '';
                    if(channeltype == 'Commercial'){
                        channel = $('select#commercial_channel').children(':selected').attr('id');
                    }else if(channeltype == 'Retail'){
                        channel = $('select#retail_channel').children(':selected').attr('id');
                    }else if(channeltype == 'Franchises'){
                        channel = $('select#franchise_channel').children(':selected').attr('id');
                    }

                    var select = $('select#addnewslettersentto');
                    var selectedItem= select.find(':selected');
                    var sent_to = selectedItem.attr('id');
                    var customers ='';
                    if(channelFranchiseAdmin =='franchiseadmin'){
                        var channel = $('select#franchiseadmin_channel').children(':selected').attr('id');
                        var branch =  $('select#franchiseadmin_branch').children(':selected').attr('id'); 

                        if(sent_to =='Customers'){
                            customers = $('select#customer_channel').val(); 
                            AJAXCallModuleJSOnly('" . __CLASS__ . "', 'save_newsletter', 'newsletter_type='+newsletter_type+'&subject='+subject+'&sent_to='+sent_to+'&customers='+customers+'&channel='+channel);
                        }else if(channel =='All'){
                            var channeltype ='Franchises';
                            AJAXCallModuleJSOnly('" . __CLASS__ . "', 'save_newsletter', 'newsletter_type='+newsletter_type+'&subject='+subject+'&sent_to='+sent_to+'&channel='+channel+'&channeltype='+channeltype);
                        }else{
                          AJAXCallModuleJSOnly('" . __CLASS__ . "', 'save_newsletter', 'newsletter_type='+newsletter_type+'&subject='+subject+'&sent_to='+sent_to+'&branch='+branch+'&channel='+channel);  
                        }     
                    }else if(sent_to =='Customers'){
                        if(channeltype == 'Commercial'){
                            channel = $('select#commercial_channel').children(':selected').attr('id');
                        }else if(channeltype == 'Retail'){
                            channel = $('select#retail_channel').children(':selected').attr('id');
                        }else if(channeltype == 'Franchises'){
                            channel = $('select#franchise_channel').children(':selected').attr('id');
                        }else{
                            channel = $('input#addnewsletter_frachasisechannelOwnerAdmin').val();
                        }

                        customers = $('select#customer_channel').val(); 
                        AJAXCallModuleJSOnly('" . __CLASS__ . "', 'save_newsletter', 'newsletter_type='+newsletter_type+'&subject='+subject+'&sent_to='+sent_to+'&customers='+customers+'&channel='+channel);
                    }else if(channel =='All'){

                        AJAXCallModuleJSOnly('" . __CLASS__ . "', 'save_newsletter', 'newsletter_type='+newsletter_type+'&subject='+subject+'&sent_to='+sent_to+'&channel='+channel+'&channeltype='+channeltype);
                    }else{
                        var branch = '';
                        if(channeltype == 'Commercial'){
                            channel = $('select#commercial_channel').children(':selected').attr('id');
                            branch =  $('select#commercial_branch').children(':selected').attr('id');
                        }else if(channeltype == 'Retail'){
                            channel = $('select#retail_channel').children(':selected').attr('id');
                            branch =  $('select#retail_branch').children(':selected').attr('id');
                        }else if(channeltype == 'Franchises'){
                            branch =  $('select#franchise_branch').children(':selected').attr('id');
                            channel = $('select#franchise_channel').children(':selected').attr('id');
                        }else{
                           branch =  $('select#addnewsletter_branch').children(':selected').attr('id'); 
                           channel =  $('input#addnewsletter_branch_channel').val();     
                        }

                        if(branch == 'All') {
                            AJAXCallModuleJSOnly('" . __CLASS__ . "', 'save_newsletter', 'newsletter_type='+newsletter_type+'&subject='+subject+'&sent_to='+sent_to+'&branch='+branch+'&channel='+channel);
                        }else{
                           AJAXCallModuleJSOnly('" . __CLASS__ . "', 'save_newsletter', 'newsletter_type='+newsletter_type+'&subject='+subject+'&sent_to='+sent_to+'&branch='+branch+'&channel='+channel);
                        }  
                    }

                    //var branch =  $('select#addnewsletter_branch').children(':selected').attr('id'); 
                    //if(branch == '') branch =  $('input#addnewsletter_branch').val(); 
                    //alert(branch);  
                    //AJAXCallModuleJSOnly('" . __CLASS__ . "', 'save_newsletter', 'newsletter_type='+newsletter_type+'&subject='+subject+'&sent_to='+sent_to+'&branch='+branch+'&channel='+channel);

                });\n "; 

        print "jQuery('#delete-newsletter-button').click(function () {
                    var newsletter  = jQuery('input#newsletter_id').val(); 
                    AJAXCallModuleJSOnly('" . __CLASS__ . "', 'delete_newsletter', 'newsletter='+newsletter);
                });\n"; 

        print '</script>';
        print "<div style='margin: 0 auto; width: 95%; border-bottom: 2px solid #BBBBBB;'>&nbsp;</div>";
        print '</div>';
    }

    public function manage_news_letter_types ($array) {

        print "<div class='classy_table'>";
        print "&nbsp;&nbsp;&nbsp;<span style='font-size:14px;font-weight:bold;'>Manage Newsletter Templates</span>";
        print "</div>";
        print "<p style='float:right'><a href='#' id='editNewsLetterTypes' name='editNewsLetterTypes'><u>Edit</u></a> | <a href='#' id='addNewsLetterTypes' name='addNewsLetterTypes'><u>Add</u></a> | <a href='#' id='viewNewsLetterTypes' name='viewNewsLetterTypes'><u>View</u></a></p>";
        print "<br/><br/>";
        print '
                <body>
                    <table id="viewnewslettertypessetup" name="viewnewslettertypessetup" class="display" cellspacing="2" width="100%">
                        <thead>
                            <tr>
                                <th>Date Created</th>
                                <th>Channel</th> 
                                <th>Newsletter Type</th>
                                <th>Newsletter Layout</th>                
                            </tr>
                        </thead>
                        <tbody>';
                            $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
                            $sqlres = mysqli_query($GLOBALS["link"],$sql);
                            $row = mysqli_fetch_assoc($sqlres);
                            $channeltype = $row['type'];
                            $query  = "SELECT `news_letter_types`.id, `news_letter_types`.date_created,`newsletter_type`, `newsletter_layout`, `channel`";
                            $query .= " FROM `news_letter_types` INNER JOIN channels ON channels.id = news_letter_types.channel AND channels.type='".$channeltype."'";
                            $query .= " WHERE `deleted` = '0'";
                            $query .= " ORDER BY `news_letter_types`.id";
                            $result = mysqli_query($GLOBALS["link"],$query);
                            while ($arr = mysqli_fetch_assoc($result)) {
                               print "<tr>";
                                    print '<td align="center">'.date("Y-m-d", strtotime($arr['date_created'])).'</td>';
                                    $channelname = $this->getChannelName($arr['channel']);
                                    print '<td align="center">'.$channelname.'</td>';  
                                    print '<td align="center">'.$arr['newsletter_type'].'</td>';
                                    print "<td align='center'><a href='../../viewnewletterlayout.php?newsletter_type=".$arr["id"]."' target='_blank'><span class='ui-button-text'><img  width='20' height='20' title='View Details'  src='modules/dashboard/images/search.ico' /></span></a></td>";
                                                                      
                                print "</tr>";
                            }

                    print "</tbody>";
                    print "</table>";
                    print '<script type="text/javascript" language="javascript" class="init">
                        $(document).ready(function() {
                            $("#viewnewslettertypessetup").dataTable( {
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
        print "<div id='editnewslettertypes' name='editnewslettertypes' cellspacing='0' cellpadding='4' border='0' width='100%'>";
            print "<iframe src='editnewslettertype.php' width='1500' height='800' border='0' frameborder='0'></iframe>"; 
        print "</div>";
        print "<div id='addnewslettertypessetup' name='addnewslettertypessetup' cellspacing='0' cellpadding='4' border='0' width='100%'>";
            print "<iframe src='addnewslettertype.php' width='1500' height='800' border='0' frameborder='0'></iframe>"; 
        print "</div>";
        //==========================================
        print '<script type="text/javascript" language="javascript" class="init">';
        print "$('div#addnewslettertypessetup').hide();
            jQuery('a#addNewsLetterTypes').click(function () {
                $('div#viewnewslettertypessetup_wrapper').hide();
                $('div#editnewslettertypes').hide();
                $('div#addnewslettertypessetup').show(); 
            });\n
        ";
        
        print "$('div#editnewslettertypes').hide();
            jQuery('a#editNewsLetterTypes').click(function () {
                 $('div#viewnewslettertypessetup_wrapper').hide();
                 $('div#addnewslettertypessetup').hide();
                $('div#editnewslettertypes').show(); 
            });\n
        ";

        print "
            jQuery('a#viewNewsLetterTypes').click(function () {
                $('div#editnewslettertypes').hide();
                $('div#addnewslettertypessetup').hide();    
                $('div#viewnewslettertypessetup_wrapper').show(); 
            });\n
        "; 
        print '</script>';
        print "<div style='margin: 0 auto; width: 95%; border-bottom: 2px solid #BBBBBB;'>&nbsp;</div>";
        print '</div>';
    }
    //=======================================
    //NewsLetter
    public function save_newsletter($array) {

        if ($GLOBALS['system_user']->hasPermission('manage_news_letter')) {

            $branch =$array["branch"];
            if($array["branch"] =="") $branch = $GLOBALS['system_user']->branchID;
            $sent_to = $array["sent_to"];
            $team = (int)$array["sent_to"];
            if($team > 0)$sent_to = "Team-". $this->getTeamName($team);
            $channel = $array["channel"];

            if($branch !="" && $sent_to !="" && $channel !="") {

                if($sent_to =="Customers"){
                    $this->getSendCustomers($array["channel"],$array);
                    exit(); 
                }elseif($channel =="All"){ 
                    $this->getSendAllBranches($array["channeltype"],$array);
                    exit();
                }elseif($branch =="All"){
                    $this->getSendAllChannelBranches($array["channel"],$array);
                    exit();  
                }else{
                    $query  = "INSERT INTO `news_letter` (date_created,created_by,subject,sent_to,news_letter_type,branch,channel) VALUES (";
                    $query  .= "NOW(),";
                    $query  .= " '".$GLOBALS['system_user']->id. "',";
                    $query  .= " '".$array["subject"]. "',";
                    $query  .= " '".$sent_to. "',";
                    $query  .= " '".$array["newsletter_type"]. "',";
                    $query  .= " '".$branch. "',";
                    $query  .= " '".$array["channel"]. "'";
                    $query  .= ") ";
                    $result = mysqli_query($GLOBALS["link"],$query); 

                    if($result) {
                        $array["newsletter_id"] = $GLOBALS["link"]->insert_id;
                        print "jQuery(\"#ok-message\").html(\"Newsletter sent... \").show(0).delay(4000).hide(0);\n";
                        print "jQuery('a#previewnewsletter').attr('href', 'viewnewsletter.php?id=".$array["newsletter_id"]."');";
                        print "jQuery('button#preview-newsletter-button').show();";

                        $title = $array["subject"];
                        logAction("Added Newsletter:$title");

                        //Notification
                        $sql = "SELECT `newsletter_type` FROM news_letter_types WHERE id ='".$array["newsletter_type"]."'";
                        $sqlres = mysqli_query($GLOBALS["link"],$sql);
                        $title = "";
                        while($row = mysqli_fetch_assoc($sqlres)) {
                            $title = $row['newsletter_type'];
                        }
                        if($title == "")$title ="(NONE)";
                        $query  = "INSERT INTO `notifications` (date_created,title,description,news_letter_type,branch,sent_by,status) VALUES (";
                        $query  .= "NOW(),";
                        $query  .= " '".$title. "',";
                        $query  .= " '".$array["subject"]."',";
                        $query  .= " '".$array["newsletter_type"]."',";
                        $query  .= " '".$branch[$i]."',";
                        $query  .= " '".$GLOBALS['system_user']->id."',";
                        $query  .= " 'View'";
                        $query  .= ") ";
                        mysqli_query($GLOBALS["link"],$query);

                        //Save Newsletter Log
                        $this->save_newsletter_log($array);
                    } else {
                        print "jQuery(\"#error-message\").html(\"Unable to save Newsletter send Inforamtion! Please try again...\").show(0).delay(4000).hide(0);\n";
                    }  
                    exit();   
                } 
            }else{
                print "jQuery(\"#error-message\").html(\"Please enter required fields! Please try again...\").show(0).delay(4000).hide(0);\n";
            }
        }else{
            print "You do not have permission to perform this action.";
            exit();            
        }
    }

    public function delete_newsletter  ($array) {
        if ($GLOBALS['system_user']->hasPermission('manage_news_letter')) {
            $sql = "UPDATE news_letter SET";
            $sql .= " `last_updated` =NOW(),";
            $sql .= " `deleted` = '1'";
            $sql .= " WHERE id=".$array["newsletter"]." LIMIT 1";
            $result = mysqli_query($GLOBALS["link"],$sql);
           
            if($result) {
                print "jQuery(\"#ok-message2\").html(\"Newsletter deleted...  \").show(0).delay(4000).hide(0);\n";
                $title = $array["news_letter"];
                logAction("Deleted Newsletter:$title");
            } else {
               print "jQuery(\"#error-message2\").html(\"Unable to delete Newsletter Inforamtion! Please try again...\").show(0).delay(4000).hide(0);\n";
            }  
            exit();
        }else{
            print "You do not have permission to perform this action.";
            exit();            
        }
    }

    public function getNewsletters  ($array) {
        $branchID = (int)isset($_POST["branchID"])?$_POST["branchID"]:0;
        $query  = "SELECT *";
        $query .= " FROM `news_letter`";
        $query .= " WHERE `news_letter`.branch= ".$branchID;

        if($GLOBALS['system_user']->hasPermission('manage_news_letter')) {                    
            $result = mysqli_query($GLOBALS["link"],$query);
            $array = array();
            while ($arr = mysqli_fetch_assoc($result)){
                $array[] = $arr; 
            }
            print json_encode($array);
        } else {
            print "jQuery(\"#error-message2\").html(\"Unable to get Company Newsletters information! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
        }
    }

    public function getChannelBranches(){
        //Channel Type
        $channelID = (int)isset($_POST["channel"])?$_POST["channel"]:0;
        $sql = "SELECT * FROM `branches` WHERE `channel`=".$channelID;
        $result = mysqli_query($GLOBALS["link"],$sql);
        $array = array();
        while ($arr = mysqli_fetch_assoc($result)){
            $array[] = $arr; 
        }
        print json_encode($array);
    }

    public function getCustomers() {
        $channeltype = (int)isset($_POST["customers"])?$_POST["customers"]:"";
        $query  = "SELECT *";
        $query .= " FROM `channels`";
        $query .= " WHERE `channels`.type='".$channeltype."'";
        $result = mysqli_query($GLOBALS["link"],$query);
        $arrChannels = array();
        while ($arr = mysqli_fetch_assoc($result)) {
          $arrChannels[] = $arr;
        }

        $arrCustomers = array();
        for ($i=0; $i <count($arrChannels); $i++) {
            $details = $arrChannels[$i];
            $id = $details['id'];
            $sql = "SELECT * FROM `customers` WHERE active=1 "; 
            $sql .= "AND channel =".$id;
            $sql .= " ORDER BY `date_created` DESC";
            $sqlres = mysqli_query($GLOBALS["link"],$sql);                    
            while($row = mysqli_fetch_assoc($sqlres)) {
                $arrCustomers[] = $row;
            }
        }
        if($GLOBALS['system_user']->hasPermission('manage_news_letter')) {                    
            print json_encode($arrCustomers);
        } else {
            print "jQuery(\"#error-message2\").html(\"Unable to get Customer information! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
        }
    }

    public function getCustomersAdmin() {
        $channel = (int)isset($_POST["customers"])?$_POST["customers"]:0;
        $arrCustomers = array();
        $sql = "SELECT * FROM `customers` WHERE active=1 "; 
        $sql .= "AND channel =".$channel;
        $sql .= " ORDER BY `date_created` DESC";
        $sqlres = mysqli_query($GLOBALS["link"],$sql);                    
        while($row = mysqli_fetch_assoc($sqlres)) {
            $arrCustomers[] = $row;
        }
    
        if($GLOBALS['system_user']->hasPermission('manage_news_letter')) {                    
            print json_encode($arrCustomers);
        } else {
            print "jQuery(\"#error-message2\").html(\"Unable to get Customer information! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
        }
    }

    public function getSendCustomers($channel="",$array=array()){
        $count = 0;
        $customers = $array["customers"];
        for ($i=0; $i < count($customers) ; $i++) { 
            $query  = "INSERT INTO `news_letter` (date_created,created_by,subject,sent_to,news_letter_type,branch,channel) VALUES (";
            $query  .= "NOW(),";
            $query  .= " '".$GLOBALS['system_user']->id. "',";
            $query  .= " '".$array["subject"]. "',";
            $query  .= " 'Customers',";
            $query  .= " '".$array["newsletter_type"]. "',";
            $query  .= " '0',";
            $query  .= " '".$channel. "'";
            $query  .= ") ";
            $result = mysqli_query($GLOBALS["link"],$query); 
            if($result) {
                $count++;
                $array["newsletter_id"] = $GLOBALS["link"]->insert_id;

                //Notification
                $sql = "SELECT `newsletter_type` FROM news_letter_types WHERE id ='".$array["newsletter_type"]."'";
                $sqlres = mysqli_query($GLOBALS["link"],$sql);
                $title = "";
                while($row = mysqli_fetch_assoc($sqlres)) {
                    $title = $row['newsletter_type'];
                }
                if($title == "")$title ="(NONE)";
                $query  = "INSERT INTO `notifications` (date_created,title,description,news_letter_type,branch,sent_by,status) VALUES (";
                $query  .= "NOW(),";
                $query  .= " '".$title. "',";
                $query  .= " '".$array["subject"]."',";
                $query  .= " '".$array["newsletter_type"]."',";
                $query  .= " '".$branch[$i]."',";
                $query  .= " '".$GLOBALS['system_user']->id. "',";
                $query  .= " 'View'";
                $query  .= ") ";
                mysqli_query($GLOBALS["link"],$query);

                //Save Newsletter Log
                $this->save_newsletter_log($array);
            } 
        }
        if($count == count($customers)) {
            print "jQuery(\"#ok-message\").html(\"Newsletter sent... \").show(0).delay(4000).hide(0);\n";
            $title = $array["subject"];
            logAction("Added Newsletter:$title");   
        } else {
            print "jQuery(\"#error-message\").html(\"Unable to save Newsletter send Inforamtion! Please try again...\").show(0).delay(4000).hide(0);\n";
        } 
    }

    public function getAllChannelBranches($channel=""){
        $sql = "SELECT `id` FROM `branches` WHERE `channel`=".$channel;
        $result = mysqli_query($GLOBALS["link"],$sql);
        $array = array();
        while ($arr = mysqli_fetch_assoc($result)){
            $array[] = $arr['id']; 
        }
        return $array;
    }
    public function getSendAllChannelBranches($channel="",$array=array()){
        $branch = $this->getAllChannelBranches($channel);
        $count = 0;

        $sent_to = $array["sent_to"];
        $team = (int)$array["sent_to"];
        if($team > 0)$sent_to = "Team-". $this->getTeamName($team);

        for ($i=0; $i < count($branch) ; $i++) { 
            $query  = "INSERT INTO `news_letter` (date_created,created_by,subject,sent_to,news_letter_type,branch,channel) VALUES (";
            $query  .= "NOW(),";
            $query  .= " '".$GLOBALS['system_user']->id. "',";
            $query  .= " '".$array["subject"]. "',";
            $query  .= " '".$sent_to. "',";
            $query  .= " '".$array["newsletter_type"]. "',";
            $query  .= " '".$branch[$i]. "',";
            $query  .= " '".$array["channel"]. "'";
            $query  .= ") ";
            $result = mysqli_query($GLOBALS["link"],$query); 
            if($result) {
                $count++;
                $array["newsletter_id"] = $GLOBALS["link"]->insert_id;
                $array["branch"] = $branch[$i];


                //Notification
                $sql = "SELECT `newsletter_type` FROM news_letter_types WHERE id ='".$array["newsletter_type"]."'";
                $sqlres = mysqli_query($GLOBALS["link"],$sql);
                $title = "";
                while($row = mysqli_fetch_assoc($sqlres)) {
                    $title = $row['newsletter_type'];
                }
                if($title == "")$title ="(NONE)";
                $query  = "INSERT INTO `notifications` (date_created,title,description,news_letter_type,branch,sent_by,status) VALUES (";
                $query  .= "NOW(),";
                $query  .= " '".$title. "',";
                $query  .= " '".$array["subject"]."',";
                $query  .= " '".$array["newsletter_type"]."',";
                $query  .= " '".$branch[$i]."',";
                $query  .= " '".$GLOBALS['system_user']->id. "',";
                $query  .= " 'View'";
                $query  .= ") ";
                mysqli_query($GLOBALS["link"],$query);

                //Save Newsletter Log
                $this->save_newsletter_log($array);
            } 
        }
        if($count == count($branch)) {
            print "jQuery(\"#ok-message\").html(\"Newsletter sent... \").show(0).delay(4000).hide(0);\n";
            $title = $array["subject"];
            logAction("Added Newsletter:$title");   
        } else {
            print "jQuery(\"#error-message\").html(\"Unable to save Newsletter send Inforamtion! Please try again...\").show(0).delay(4000).hide(0);\n";
        } 
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
            $sql = "SELECT * FROM `branches` WHERE `channel`=".$array[$i];
            $result = mysqli_query($GLOBALS["link"],$sql);
            while ($arr = mysqli_fetch_assoc($result)){
                $arrBranches[] = $arr; 
            }
        }
        return $arrBranches;
    }
    public function getSendAllBranches($channel="",$array=array()){
        $branch = $this->getAllBranches($channel);
        $count = 0;

        $sent_to = $array["sent_to"];
        $team = (int)$array["sent_to"];
        if($team > 0)$sent_to = "Team-". $this->getTeamName($team);

        for ($i=0; $i < count($branch) ; $i++) { 
            $query  = "INSERT INTO `news_letter` (date_created,created_by,subject,sent_to,news_letter_type,branch,channel) VALUES (";
            $query  .= "NOW(),";
            $query  .= " '".$GLOBALS['system_user']->id. "',";
            $query  .= " '".$array["subject"]. "',";
            $query  .= " '".$sent_to. "',";
            $query  .= " '".$array["newsletter_type"]. "',";
            $details = $branch[$i];
            $query  .= " '".$details['id']. "',";
            $query  .= " '".$details["channel"]. "'";
            $query  .= ") ";
            $result = mysqli_query($GLOBALS["link"],$query); 
            if($result) {
                $count++;
                $array["newsletter_id"] = $GLOBALS["link"]->insert_id;
                $array["branch"] = $details['id'];

                //Notification
                $sql = "SELECT `newsletter_type` FROM news_letter_types WHERE id ='".$array["newsletter_type"]."'";
                $sqlres = mysqli_query($GLOBALS["link"],$sql);
                $title = "";
                while($row = mysqli_fetch_assoc($sqlres)) {
                    $title = $row['newsletter_type'];
                }
                if($title == "")$title ="(NONE)";
                $query  = "INSERT INTO `notifications` (date_created,title,description,news_letter_type,branch,sent_by,status) VALUES (";
                $query  .= "NOW(),";
                $query  .= " '".$title. "',";
                $query  .= " '".$array["subject"]."',";
                $query  .= " '".$array["newsletter_type"]."',";
                $query  .= " '".$branch[$i]."',";
                $query  .= " '".$GLOBALS['system_user']->id."',";
                $query  .= " 'View'";
                $query  .= ") ";
                mysqli_query($GLOBALS["link"],$query);

                //Save Newsletter Log
                $this->save_newsletter_log($array);
            } 
        }
        if($count == count($branch)) {
            print "jQuery(\"#ok-message\").html(\"Newsletter sent... \").show(0).delay(4000).hide(0);\n";
            $title = $array["subject"];
            logAction("Added Newsletter:$title");   
        } else {
            print "jQuery(\"#error-message\").html(\"Unable to save Newsletter send Inforamtion! Please try again...\").show(0).delay(4000).hide(0);\n";
        } 
    }

    public function getNewsLetterInfo(){
        $newsletterID = (int)isset($_POST["newsletterID"])?$_POST["newsletterID"]:0;
        $query  = "SELECT *,`news_letter_types`.newsletter_type AS newsletter_type_name";
        $query .= " FROM `news_letter` INNER JOIN `news_letter_types` ON `news_letter_types`.id = `news_letter`.news_letter_type";
        $query .= " WHERE `news_letter`.id= ".$newsletterID;

        if($GLOBALS['system_user']->hasPermission('manage_news_letter')) {                    
            $result = mysqli_query($GLOBALS["link"],$query);
            $arr = mysqli_fetch_assoc($result);
            print json_encode($arr);
         } else {
            print "jQuery(\"#error-message2\").html(\"Unable to get Newsletter information! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
         }
    }

    public function getNewsLetterTypeInfo2(){
        $newslettertypeID = (int)isset($_POST["newslettertypeID"])?$_POST["newslettertypeID"]:0;
        $query  = "SELECT *";
        $query .= " FROM `news_letter_types`";
        $query .= " WHERE `news_letter_types`.id= ".$newslettertypeID;  
        if($GLOBALS['system_user']->hasPermission('manage_news_letter')) {                    
            $result = mysqli_query($GLOBALS["link"],$query);
            $arr = mysqli_fetch_assoc($result);
            print json_encode($arr);
        } else {
            print "jQuery(\"#error-message2\").html(\"Unable to get Newsletter information! Insufficient permissions...\").show(0).delay(4000).hide(0);\n";
        }     
    }

    public function save_newsletter_log($array) {

        if ($GLOBALS['system_user']->hasPermission('manage_news_letter')) {

            $Branch = $array["branch"];
            $Subject = $array["subject"];
            $Newsletter = $array["newsletter_type"];
            $NewsletterID = $array["newsletter_id"];

            $Channel = $this->getChannelBranch($Branch);
            if($Channel =="") $Channel =$array["channel"];

            $Owners = array();
            $Customers = array();
            $Users = array();
            $Teams = array();

            if($array["sent_to"] =="Owners")$Owners = $this->getOwnersEmail($Channel);
            if($array["sent_to"] =="Customers"){ 
                $Customers = $this->getEmails($array["customers"]);
            }
            if($array["sent_to"] =="Users")$Users = $this->getUsersEmail($Branch);
            if($array["sent_to"] =="Both"){
                $Customers = $this->getCustomersEmail($Channel);
                $Users = $this->getUsersEmail($Branch);
            }

            $team = (int)$array["sent_to"];
            if($team > 0)$Teams = $this->getTeamUsersEmail($team);

            if($array["sent_to"] =="BothFranchise"){
                $Teams = $this->getAllTeamUsersEmail();
                $Customers = $this->getCustomersEmail($Channel); 
            }

           /* $testCust = "";
            $testusers = "";

            for($i=0;$i<count($Customers);$i++){
                 $testCust .=$Customers[$i];
            }
            for($i=0;$i<count($Users);$i++){
                 $testusers .=$Users[$i];
            }
            //print "jQuery(\"#error-message\").html(\"testCust:$testCust...\").show(0).delay(4000).hide(0);\n";
            //print "jQuery(\"#error-message\").html(\"testusers:$testusers...\").show(0).delay(4000).hide(0);\n";
           // print_r($array);
            //print_r($Customers);
            //print_r($Users);exit();*/

            //Newsletter
            $sql = "SELECT `newsletter_layout` FROM news_letter_types WHERE id ='".$array["newsletter_type"]."'";
            $sqlres = mysqli_query($GLOBALS["link"],$sql);
            $newsletter_layout = "";
            while($row = mysqli_fetch_assoc($sqlres)) {
                $newsletter_layout  = $row['newsletter_layout'];
            }

            //Branch
            $sql = "SELECT * FROM branches WHERE id ='".$Branch."' LIMIT 1";
            $sqlres = mysqli_query($GLOBALS["link"],$sql);
            $companydetails = "";
            $logo = "";
            while($row = mysqli_fetch_assoc($sqlres)) {
                $companydetails = $GLOBALS['system_user']->first.' '.$GLOBALS['system_user']->last.'<br/>';
                $companydetails .= ''.$row['contact_number'].'<br/>
                                <img src="cid:logoimg"  style="height:75px" align="middle"/><br/>  
                                '.$row['branch'].'<br/>
                                '.$row['address'].'<br/>';
                $logo  = $row['logo'];
            }

            if(count($Owners)>0){
                $totalSent = 0;
                //for($i=0;$i<count($Owners);$i++){
                for($i=0;$i<1;$i++){

                    $mailNewsLetter = new PHPMailer(); 
                    $mailNewsLetter->Mailer = 'smtp';
                    $mailNewsLetter->IsSMTP(); // telling the class to use SMTP

                    $mailNewsLetter->setFrom("noreply@clientassist.co.za","Theo Developer");
                    $mailNewsLetter->Subject = $Subject;

                    //$email = $Owners['email'];
                    //$email = "theo.m@uthgroup.co.za";
                    //$email = "gary@uthgroup.co.za";
                    $email = "ari.salkow@ellies.co.za";
                    $sql = "SELECT  * FROM `system_users` WHERE `email`='".$email."'";        
                    $sqlres = mysqli_query($GLOBALS["link"],$sql); 
                    $row = mysqli_fetch_assoc($sqlres);
                    $to = $row['first'];

                    //message
                    $emailmessage = '
                    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                    <html xmlns="http://www.w3.org/1999/xhtml">
                    <head>
                        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                        <title>Newsletter</title>
                    </head>
                    <body>
                        Dear '.$to.',<br/><br/>
                        '.$newsletter_layout.'<br/><br/>
                          Yours sincerely<br/>
                        '.$companydetails.' 
                    </body>
                    </html>';

                    //Set who the message is to be sent to
                    $mailNewsLetter->addAddress($email,"Ellies Member");
                    //Images
                    if($logo !=""){
                        $logo = "modules/companyprofiles/logos/".$logo;
                        $mailNewsLetter->AddEmbeddedImage($logo,'logoimg', $logo); 
                    }else{
                        $logo = "images/logo.png";
                        $mailNewsLetter->AddEmbeddedImage($logo,'logoimg', $logo); 
                    }

                    $query = "SELECT attachment FROM news_letter_types WHERE  id =".$array['newsletter_type'];
                    $sqlresult = mysqli_query($GLOBALS["link"],$query);
                    $rowResult = mysqli_fetch_assoc($sqlresult);
                    $attachment = $rowResult['attachment'];
                    if($attachment !=="")$mailNewsLetter->addAttachment("modules/newsletter/attachments/".$attachment."");

                    //convert HTML into a basic plain-text alternative body
                    $mailNewsLetter->msgHTML($emailmessage); 
                    // Mail it
                    if($mailNewsLetter->send()){

                        $query  = "INSERT INTO `news_letter_log` (date_created,subject,sent_to_id,email,newsletter,branch,channel,status) VALUES (";
                        $query  .= "NOW(),";
                        $query  .= " '".$Subject. "',";

                        $id = $Owners['id'];
                        $email = $Owners['email'];
                        $query  .= " '".$id. "',";
                        $query  .= " '".$email. "',";

                        $query  .= " '".$Newsletter. "',";
                        $query  .= " '".$Branch. "',";
                        $query  .= " '".$Channel. "',";
                        $query  .= " 'Sent'";
                        $query  .= ") ";
                        $result = mysqli_query($GLOBALS["link"],$query); 
                        if($result) {
                            $title = $array["subject"];
                            logAction("Added News Letter Log:$subject");
                        }
                        $totalSent++;
                    }else{
                        $query  = "INSERT INTO `news_letter_log` (date_created,subject,sent_to_id,email,newsletter,branch,channel,status) VALUES (";
                        $query  .= "NOW(),";
                        $query  .= " '".$Subject. "',";

                        $id = $Owners['id'];
                        $email = $Owners['email'];
                        $query  .= " '".$id. "',";
                        $query  .= " '".$email. "',";

                        $query  .= " '".$Newsletter."',";
                        $query  .= " '".$Branch. "',";
                        $query  .= " '".$Channel. "',";
                        $query  .= " 'Not Sent'";
                        $query  .= ") ";
                        $result = mysqli_query($GLOBALS["link"],$query); 
                        if($result) {
                            $title = $array["subject"];
                            logAction("Added News Letter Log:$subject");
                        }
                    } 
                }
                //Save Newsletter Summary Log
                $this->save_newsletter_summary_log($Subject,$NewsletterID,$Branch,$Channel,$totalSent);
            }

            if(count($Customers)>0){
                //for($i=0;$i<count($Customers);$i++){
                $totalSent = 0;
                for($i=0;$i<1;$i++){

                    $mailNewsLetter = new PHPMailer(); 
                    $mailNewsLetter->Mailer = 'smtp';
                    $mailNewsLetter->IsSMTP(); // telling the class to use SMTP

                    $mailNewsLetter->setFrom("noreply@clientassist.co.za","Theo Developer");
                    $mailNewsLetter->Subject = $Subject;

                    $details = $Customers[$i];
                    $email = $details['email'];
                    //$email = "theo.m@uthgroup.co.za";
                    //$email = "gary@uthgroup.co.za";
                    $email = "ari.salkow@ellies.co.za";
                    $sql = "SELECT  * FROM `system_users` WHERE `email`='".$email."'";        
                    $sqlres = mysqli_query($GLOBALS["link"],$sql); 
                    $row = mysqli_fetch_assoc($sqlres);
                    $to = $row['first'];

                    //message
                    $emailmessage = '
                    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                    <html xmlns="http://www.w3.org/1999/xhtml">
                    <head>
                        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                        <title>Newsletter</title>
                    </head>
                    <body>
                        Dear '.$to.',<br/><br/>
                        '.$newsletter_layout.'<br/><br/>
                          Yours sincerely<br/>
                        '.$companydetails.'     
                    </body>
                    </html>';

                    //Set who the message is to be sent to
                    $mailNewsLetter->addAddress($email,"Ellies Member");
                    //Images
                    if($logo !=""){
                        $logo = "modules/companyprofiles/logos/".$logo;
                        $mailNewsLetter->AddEmbeddedImage($logo,'logoimg', $logo); 
                    }else{
                        $logo = "images/logo.png";
                        $mailNewsLetter->AddEmbeddedImage($logo,'logoimg', $logo); 
                    }

                    $query = "SELECT attachment FROM news_letter_types WHERE  id =".$array['newsletter_type'];
                    $sqlresult = mysqli_query($GLOBALS["link"],$query);
                    $rowResult = mysqli_fetch_assoc($sqlresult);
                    $attachment = $rowResult['attachment'];
                    if($attachment !=="")$mailNewsLetter->addAttachment("modules/newsletter/attachments/".$attachment."");

                    //convert HTML into a basic plain-text alternative body
                    $mailNewsLetter->msgHTML($emailmessage); 
                    // Mail it
                    if($mailNewsLetter->send()){
                        $query  = "INSERT INTO `news_letter_log` (date_created,subject,sent_to_id,email,newsletter,branch,channel,status) VALUES (";
                        $query  .= "NOW(),";
                        $query  .= " '".$Subject. "',";

                        $id = $details['id'];
                        $email = $details['email'];
                        $query  .= " '".$id. "',";
                        $query  .= " '".$email. "',";

                        $query  .= " '".$Newsletter. "',";
                        $query  .= " '".$Branch. "',";
                        $query  .= " '".$Channel. "',";
                        $query  .= " 'Sent'";
                        $query  .= ") ";
                        $result = mysqli_query($GLOBALS["link"],$query); 
                        if($result) {
                            $title = $array["subject"];
                            logAction("Added News Letter Log:$subject");
                        }
                        $totalSent++;
                    }else{
                        $query  = "INSERT INTO `news_letter_log` (date_created,subject,sent_to_id,email,newsletter,branch,channel,status) VALUES (";
                        $query  .= "NOW(),";
                        $query  .= " '".$Subject. "',";

                        $id = $details['id'];
                        $email = $details['email'];
                        $query  .= " '".$id. "',";
                        $query  .= " '".$email. "',";

                        $query  .= " '".$Newsletter."',";
                        $query  .= " '".$Branch. "',";
                        $query  .= " '".$Channel. "',";
                        $query  .= " 'Not Sent'";
                        $query  .= ") ";
                        $result = mysqli_query($GLOBALS["link"],$query); 
                        if($result) {
                            $title = $array["subject"];
                            logAction("Added News Letter Log:$subject");
                        }
                    } 
                }
                //Save Newsletter Summary Log
                $this->save_newsletter_summary_log($Subject,$NewsletterID,$Branch,$Channel,$totalSent);
            }

            if(count($Users)>0){
                $totalSent = 0;
                //for($i=0;$i<count($Users);$i++){
                for($i=0;$i<1;$i++){
                    $mailNewsLetter = new PHPMailer(); 
                    $mailNewsLetter->Mailer = 'smtp';

                    $mailNewsLetter->setFrom("noreply@clientassist.co.za","Theo Developer");
                    $mailNewsLetter->Subject = $Subject;

                    $details = $Users[$i];
                    $email = $details['email'];
                    //$email = "gary@uthgroup.co.za";
                    $email = "ari.salkow@ellies.co.za";
                    $sql = "SELECT  * FROM `system_users` WHERE `email`='".$email."'";        
                    $sqlres = mysqli_query($GLOBALS["link"],$sql); 
                    $row = mysqli_fetch_assoc($sqlres);
                    $to = $row['first'];

                    //message
                    $emailmessage = '
                    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                    <html xmlns="http://www.w3.org/1999/xhtml">
                    <head>
                        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                        <title>Newsletter</title>
                    </head>
                    <body>
                        Dear '.$to.',<br/><br/>
                        '.$newsletter_layout.'<br/><br/>
                          Yours sincerely<br/>
                        '.$companydetails.'     
                    </body>
                    </html>';

                    //Set who the message is to be sent to
                    $mailNewsLetter->addAddress($email,"Ellies Member");

                     //Images
                    if($logo !=""){
                        $logo = "modules/companyprofiles/logos/".$logo;
                        $mailNewsLetter->AddEmbeddedImage($logo,'logoimg', $logo); 
                    }else{
                        $logo = "images/logo.png";
                        $mailNewsLetter->AddEmbeddedImage($logo,'logoimg', $logo); 
                    }

                    $query = "SELECT attachment FROM news_letter_types WHERE  id =".$array['newsletter_type'];
                    $sqlresult = mysqli_query($GLOBALS["link"],$query);
                    $rowResult = mysqli_fetch_assoc($sqlresult);
                    $attachment = $rowResult['attachment'];
                    if($attachment !=="")$mailNewsLetter->addAttachment("modules/newsletter/attachments/".$attachment."");

                    //convert HTML into a basic plain-text alternative body
                    $mailNewsLetter->msgHTML($emailmessage); 
                    // Mail it
                    if($mailNewsLetter->send()){
                        $query  = "INSERT INTO `news_letter_log` (date_created,subject,sent_to_id,email,newsletter,branch,channel,status) VALUES (";
                        $query  .= "NOW(),";
                        $query  .= " '".$array["subject"]. "',";

                        $id = $details['id'];
                        $email = $details['email'];
                        $query  .= " '".$id. "',";
                        $query  .= " '".$email. "',";

                        $query  .= " '".$Newsletter. "',";
                        $query  .= " '".$Branch. "',";
                        $query  .= " '".$Channel. "',";
                        $query  .= " 'Sent'";
                        $query  .= ") ";
                        $result = mysqli_query($GLOBALS["link"],$query); 
                        if($result) {
                            logAction("Added News Letter Log:$Subject");
                        }
                        $totalSent++;
                    }else{
                        $query  = "INSERT INTO `news_letter_log` (date_created,subject,sent_to_id,email,newsletter,branch,channel,status) VALUES (";
                        $query  .= "NOW(),";
                        $query  .= " '".$array["subject"]. "',";

                        $id = $details['id'];
                        $email = $details['email'];
                        $query  .= " '".$id. "',";
                        $query  .= " '".$email. "',";

                        $query  .= " '".$Newsletter. "',";
                        $query  .= " '".$Branch. "',";
                        $query  .= " '".$Channel. "',";
                        $query  .= " Not Sent'";
                        $query  .= ") ";
                        $result = mysqli_query($GLOBALS["link"],$query); 
                        if($result) {
                            logAction("Added News Letter Log:$Subject");
                        }
                    }
                }
                //Save Newsletter Summary Log
                $this->save_newsletter_summary_log($Subject,$NewsletterID,$Branch,$Channel,$totalSent);
            }

            if(count($Teams)>0){
                $totalSent = 0;
                //for($i=0;$i<count($Teams);$i++){
                for($i=0;$i<1;$i++){
                    $mailNewsLetter = new PHPMailer(); 
                    $mailNewsLetter->Mailer = 'smtp';

                    $mailNewsLetter->setFrom("noreply@clientassist.co.za","Theo Developer");
                    $mailNewsLetter->Subject = $Subject;

                    $details = $Teams[$i];
                    $email = $details['email'];
                    //$email = "theo.m@uthgroup.co.za";
                    //$email = "gary@uthgroup.co.za";
                    $email = "ari.salkow@ellies.co.za";

                    $sql = "SELECT  * FROM `system_users` WHERE `email`='".$email."'";        
                    $sqlres = mysqli_query($GLOBALS["link"],$sql); 
                    $row = mysqli_fetch_assoc($sqlres);
                    $to = $row['first'];

                    //message
                    $emailmessage = '
                    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                    <html xmlns="http://www.w3.org/1999/xhtml">
                    <head>
                        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                        <title>Newsletter</title>
                    </head>
                    <body>
                        Dear '.$to.',<br/><br/>
                        '.$newsletter_layout.'<br/><br/>
                          Yours sincerely<br/>
                        '.$companydetails.'     
                    </body>
                    </html>';

                    
                    //Set who the message is to be sent to
                    $mailNewsLetter->addAddress($email,"Ellies Member");

                    //Images
                    if($logo !=""){
                        $logo = "modules/companyprofiles/logos/".$logo;
                        $mailNewsLetter->AddEmbeddedImage($logo,'logoimg', $logo); 
                    }else{
                        $logo = "images/logo.png";
                        $mailNewsLetter->AddEmbeddedImage($logo,'logoimg', $logo); 
                    }

                    $query = "SELECT attachment FROM news_letter_types WHERE  id =".$array['newsletter_type'];
                    $sqlresult = mysqli_query($GLOBALS["link"],$query);
                    $rowResult = mysqli_fetch_assoc($sqlresult);
                    $attachment = $rowResult['attachment'];
                    if($attachment !=="")$mailNewsLetter->addAttachment("modules/newsletter/attachments/".$attachment."");

                    //convert HTML into a basic plain-text alternative body
                    $mailNewsLetter->msgHTML($emailmessage); 
                    // Mail it
                    if($mailNewsLetter->send()){
                        $query  = "INSERT INTO `news_letter_log` (date_created,subject,sent_to_id,email,newsletter,branch,channel,status) VALUES (";
                        $query  .= "NOW(),";
                        $query  .= " '".$array["subject"]. "',";

                        $id = $details['id'];
                        $email = $details['email'];
                        $query  .= " '".$id. "',";
                        $query  .= " '".$email. "',";

                        $query  .= " '".$Newsletter. "',";
                        $query  .= " '".$Branch. "',";
                        $query  .= " '".$Channel. "',";
                        $query  .= " 'Sent'";
                        $query  .= ") ";
                        $result = mysqli_query($GLOBALS["link"],$query); 
                        if($result) {
                            logAction("Added News Letter Log:$Subject");
                        }
                        $totalSent++;
                    }else{
                        $query  = "INSERT INTO `news_letter_log` (date_created,subject,sent_to_id,email,newsletter,branch,channel,status) VALUES (";
                        $query  .= "NOW(),";
                        $query  .= " '".$array["subject"]. "',";

                        $id = $Teams['id'];
                        $email = $Teams['email'];
                        $query  .= " '".$id. "',";
                        $query  .= " '".$email. "',";

                        $query  .= " '".$Newsletter. "',";
                        $query  .= " '".$Branch. "',";
                        $query  .= " '".$Channel. "',";
                        $query  .= " Not Sent'";
                        $query  .= ") ";
                        $result = mysqli_query($GLOBALS["link"],$query); 
                        if($result) {
                            logAction("Added News Letter Log:$Subject");
                        }
                    }
                }
                //Save Newsletter Summary Log
                $this->save_newsletter_summary_log($Subject,$NewsletterID,$Branch,$Channel,$totalSent);
            }
        }
    }

    /*public function preview_newsletter($array) {

        $branch =$array["branch"];
        if($array["branch"] =="") $branch = $GLOBALS['system_user']->branchID;

        $newsletter_type = $array["newsletter_type"];

        //Newsletter
        $sql = "SELECT `newsletter_layout` FROM news_letter_types WHERE id ='".$newsletter_type."' LIMIT 1";
        $sqlres = mysqli_query($GLOBALS["link"],$sql);
        $newsletter_layout = "";
        while($row = mysqli_fetch_assoc($sqlres)) {
            $newsletter_layout  = $row['newsletter_layout'];
        }

        //Branch
        $sql = "SELECT * FROM branches WHERE id ='".$branch."' LIMIT 1";
        $sqlres = mysqli_query($GLOBALS["link"],$sql);
        $companydetails = "";
        $logo = "";
        while($row = mysqli_fetch_assoc($sqlres)) {
            $companydetails = '<b>Company Details:</b><br/>
                            Branch: '.$row['branch'].'<br/>
                            Branch Code: '.$row['prefix'].'<br/>
                            Address: '.$row['address'].'<br/>
                            Contact Number: '.$row['contact_number'].'<br/>';
            $logo  = $row['logo'];
        }
        
        if($logo !=""){
            $logo = "modules/companyprofiles/logos/".$logo;
        }else{
            $logo = "images/logo.png";
        }
        $newsletter = "";
        $newsletter = '
                    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                    <html xmlns="http://www.w3.org/1999/xhtml">
                    <head>
                        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                        <title>Newsletter</title>
                    </head>
                    <body>
                        '.$newsletter_layout.'<br/><br/>
                        '.$companydetails.'<br/><img src="'.$logo.'"  style="width:150px;height:100px" align="middle"/>      
                    </body>
                    </html>';
         print json_encode($newsletter);
    }*/


    function getOwnersEmail($Channel = 0){

        $sql = "SELECT  `branch_owner` FROM `branches` WHERE deleted=0";         
        $sql .= " AND channel =".$Channel;
        $sqlres = mysqli_query($GLOBALS["link"],$sql); 
        $arrOwners = array();                  
        while($row = mysqli_fetch_assoc($sqlres)) {
            $arrOwners[] = $row;
        }

        $array = array();
        for ($i=0; $i <count($arrOwners) ; $i++) {
            $details =  $arrOwners[$i];
            $id =  $details['branch_owner'];
            $sql = "SELECT  `email` FROM `system_users` WHERE `id`=".$id;        
            $sqlres = mysqli_query($GLOBALS["link"],$sql); 
            $row = mysqli_fetch_assoc($sqlres);
            if($row['email'] !=""){
                $array['id'] = $id;
                $array['email'] = $row['email'];
            }
        }
        return $array;
    }

    function getEmails($customers = ""){
        $arrCustomers = array();
        $array = explode(",", $customers);
        for($i=0;$i<count($array);$i++){
            $sql = "SELECT  `id`,`email` FROM `customers` WHERE active=1 ";         
            $sql .= "AND id =".$array[$i];
            $sqlres = mysqli_query($GLOBALS["link"],$sql);               
            while($row = mysqli_fetch_assoc($sqlres)) {
                $arrCustomers[] = $row;
            }
        }
        return $arrCustomers;
    }
    function getCustomersEmail($Channel = 0){
        $sql = "SELECT  `id`,`email` FROM `customers` WHERE active=1 ";         
        $sql .= "AND channel =".$Channel;
        $sqlres = mysqli_query($GLOBALS["link"],$sql); 
        $array = array();                  
        while($row = mysqli_fetch_assoc($sqlres)) {
            $array[] = $row;
        }
        return $array;
    }

    function getChannelBranch($Branch=0){
        $channel = "";
        $sql = "SELECT `channel` FROM `branches` WHERE id=".$Branch;      
        $sqlres = mysqli_query($GLOBALS["link"],$sql);                    
        while($row = mysqli_fetch_assoc($sqlres)) {
            $channel = $row['channel'];
        }
        return $channel;
    } 

    function getChannelStore($Branch=0){
        $sql = "SELECT `id` FROM `stores` WHERE branch_id='".$Branch."'";      
        $sqlres = mysqli_query($GLOBALS["link"],$sql);  
        $stores = array();                   
        while($row = mysqli_fetch_assoc($sqlres)) {
            if(!in_array($row['id'], $stores))$stores[] = $row['id'];
        }
        return $stores;
    } 

    function getUsersEmail($Branch=0){
        $Stores = $this->getChannelStore($Branch);
        $arrEmails = array();  
        for($i=0;$i<count($Stores);$i++){
            $sql = "SELECT `id`,`email` FROM `system_users` WHERE store_id='".$Stores[$i]."'";         
            $sqlres = mysqli_query($GLOBALS["link"],$sql);                        
            while($row = mysqli_fetch_assoc($sqlres)) {
                $arrEmails[] = $row;
            }
        }
        return $arrEmails;
    }

    function getTeamUsersEmail($Team=0){
        $arrEmails = array();  
        $sql = "SELECT `id`,`email` FROM `team_member_profiles` WHERE team='".$Team."' AND deleted='0'";       
        $sqlres = mysqli_query($GLOBALS["link"],$sql);                        
        while($row = mysqli_fetch_assoc($sqlres)) {
            $arrEmails[] = $row;
        }
        return $arrEmails;
    }

    function getAllTeamUsersEmail(){
        $arrEmails = array();  
        $sql = "SELECT `id`,`email` FROM `team_member_profiles` WHERE deleted='0'";       
        $sqlres = mysqli_query($GLOBALS["link"],$sql);                        
        while($row = mysqli_fetch_assoc($sqlres)) {
            $arrEmails[] = $row;
        }
        return $arrEmails;
    }

    function getTeamName($Team=0){
        $sql = "SELECT `title` FROM `teams` WHERE id='".$Team."' AND deleted='0' LIMIT 1";     
        $sqlres = mysqli_query($GLOBALS["link"],$sql); 
        $name ="";                      
        while($row = mysqli_fetch_assoc($sqlres)) {
            $name = $row['title'];
        }
        return $name;
    }

    public function save_newsletter_summary_log($subject="",$newslettter=0,$branch=0,$channel=0,$total_sent=0) {

        if ($GLOBALS['system_user']->hasPermission('manage_news_letter')) {
          
            $query  = "INSERT INTO `news_letter_summary_log` (date_created,subject,newslettter,branch,channel,total_sent) VALUES (";
            $query  .= "NOW(),";
            $query  .= " '".$subject. "',";
            $query  .= " '".$newslettter. "',";
            $query  .= " '".$branch. "',";
            $query  .= " '".$channel. "',";
            $query  .= " '".$total_sent. "'";
            $query  .= ") ";
            $result = mysqli_query($GLOBALS["link"],$query);     
            if($result) {
                logAction("Added News Letter Summary Log:$subject");
                return true;
            } else {
                return false;
            }  
            exit();
        }else{
            return false;
            exit();            
        }
    }
}