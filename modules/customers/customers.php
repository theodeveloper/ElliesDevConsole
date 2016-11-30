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
register_menu("Customers", "submenu", "Customers",
    Array(
        Array("title" => "View Customers",  "location" => "view_customers",   "acl" => "view_customers"),
        Array("title" => "Manage Customers",  "location" => "manage_customers",   "acl" => "manage_customers")
    )
);

register_permission("Customers Permissions", "view_customers", "View Customers");
register_permission("Customers Permissions", "manage_customers", "Manage Customers");

//register_menu("Customers", "parentMenu", "Customers");
//register_permission("Customers Permissions", "customers", "Access to Customers module");

class Customers {
    
    private $items_per_page;
    
   /* public function __construct () {
        $this->items_per_page = Settings::getSetting(1);
    }*/

    function main () {
        $array['Channel'] = 'All';
        $this->view_customers($array);
    }

    public function view_customers ($array) {
        $selected = "";
        if (!empty($array["Channel"])) {
            $selected = $array["Channel"];
        }

        //Member permission
        $sql = "SELECT `type` FROM `branches_profile_permissions` WHERE `user_id`=".$GLOBALS['system_user']->id." AND `branch`=".$GLOBALS['system_user']->branchID;
        $sqlres = mysqli_query($GLOBALS["link"],$sql);
        $row = mysqli_fetch_assoc($sqlres);
        $member_permission = $row['type'];
        
        print "<div class='classy_table'>";
        if($member_permission == "Owner"){
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
            print "<a href='customerdb.php?branch=".$id."&channel=".$channel."' style='float:right'><u>Export database</u></a>"; 
            print "&nbsp;&nbsp;&nbsp;<span style='font-size:14px;font-weight:bold;'>View Customers</span>";
        }else{
            print "&nbsp;&nbsp;&nbsp;<span style='font-size:14px;font-weight:bold;'>View Customers</span>";
        }
        print "</div>";
        print "<br/><br/>";
        $sysuser = new userType($_SESSION["userid"]);        
        if ($sysuser->isSuperAdmin){

            $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
            $sqlres = mysqli_query($GLOBALS["link"],$sql);
            $row = mysqli_fetch_assoc($sqlres);
            $channeltype = $row['type'];

            if($channeltype =="Commercial"){
                print "<label style='font-size:12px;font-weight:bold;'>Select Commercial:</label>";
            }

            if($channeltype =="Franchises"){
                print "<label style='font-size:12px;font-weight:bold;'>Select Franchise(s):</label>";
            }

            if($channeltype =="Retail"){
                print "<label style='font-size:12px;font-weight:bold;'>Select Retail:</label>";
            }

            print "&nbsp;&nbsp;&nbsp;<select onchange='ChangeChannelSelection(this)'>";
            print "<option value=''>[Please select]</option>";
            if ($selected == 'All') {
                print "<option id='All'  value='All' selected='selected'>All</option>";
            }else{
                print "<option id='All'  value='All'>All</option>";             
            }

            $sql = "SELECT `id`,`name` FROM `channels` WHERE `type`='".$channeltype."'";
            $sqlres = mysqli_query($GLOBALS["link"],$sql);
            while($row = mysqli_fetch_assoc($sqlres)) {
                if ($selected == $row["id"]) {
                    print "<option value='".$row["id"]."' selected>".$row["name"]."</option>";
                }else{
                    print "<option value='".$row["id"]."'>".$row["name"]."</option>";                
                }
            }
            print "</select>";
            print "<br/><br/><br/>";
        } 

        print '<head>
                    <script type="text/javascript" language="javascript" class="init">
                    $(document).ready(function() {
                        $("#viewcustomers").dataTable( {
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
                            "order": [[ 0, "desc" ]]
                        } );
                    } );
                    </script>
                </head>
                <body>
                    <table id="viewcustomers" class="display" cellspacing="3" width="100%">
                       <thead>
                            <tr>
                                <th>Date Created</th>
                                <th>Name</th>
                                <th>Surname</th>
                                <th>Cellphone</th>
                                <th>Email</th>
                                <th>Company</th>                 
                            </tr>
                        </thead>
                        <tbody>';
                        if ($sysuser->isSuperAdmin){
                            if ($selected == 'All') {

                                $sql = "SELECT `id`,`type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
                                $sqlres = mysqli_query($GLOBALS["link"],$sql);
                                $row = mysqli_fetch_assoc($sqlres);
                                $channeltype = $row['type'];

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
                                //Customers
                                for ($i=0; $i <count($arrCustomers); $i++) {
                                    $details = $arrCustomers[$i];
                                    print "<tr>";
                                    $date = date('Y-m-d',strtotime($details['date_created']));
                                    print "<td align='center'>".$date."</td>";
                                    print "<td align='center'>".$details['name']."</td>";
                                    print "<td align='center'>".$details['surname']."</td>";
                                    print "<td align='center'>".$details['cellphone']."</td>";
                                    print "<td align='center'>".$details['email']."</td>";
                                    print "<td align='center'>".$details['company']."</td>";
                                    print "</tr>";  
                                }
                            }else{
                                $sql = "SELECT * FROM `customers` WHERE active=1 "; 
                                $sql .= "AND channel =".mysqli_real_escape_string($GLOBALS["link"],$selected);
                                $sql .= " ORDER BY `date_created` DESC";
                                $sqlres = mysqli_query($GLOBALS["link"],$sql);                    
                                while($row = mysqli_fetch_assoc($sqlres)) {
                                    print "<tr>";
                                    $date = date('Y-m-d',strtotime($row['date_created']));
                                    print "<td align='center'>".$date."</td>";
                                    print "<td align='center'>".$row['name']."</td>";
                                    print "<td align='center'>".$row['surname']."</td>";
                                    print "<td align='center'>".$row['cellphone']."</td>";
                                    print "<td align='center'>".$row['email']."</td>";
                                    print "<td align='center'>".$row['company']."</td>";
                                    print "</tr>";
                                }
                            }
                        }else{
                            $sql = "SELECT * FROM `customers` WHERE active=1 "; 
                            $sql .= "AND channel =".mysqli_real_escape_string($GLOBALS["link"],$GLOBALS['system_user']->retailChannel);
                            $sql .= " ORDER BY `date_created` DESC";
                            $sqlres = mysqli_query($GLOBALS["link"],$sql);                    
                            while($row = mysqli_fetch_assoc($sqlres)) {
                                print "<tr>";
                                $date = date('Y-m-d',strtotime($row['date_created']));
                                print "<td align='center'>".$date."</td>";
                                print "<td align='center'>".$row['name']."</td>";
                                print "<td align='center'>".$row['surname']."</td>";
                                print "<td align='center'>".$row['cellphone']."</td>";
                                print "<td align='center'>".$row['email']."</td>";
                                print "<td align='center'>".$row['company']."</td>";
                                print "</tr>";
                            }
                        }
                print '</tbody>
                </table>
            </body>';
    }

    public function manage_customers($array) {

        print "<div class='classy_table'>";
        print "&nbsp;&nbsp;&nbsp;<span style='font-size:14px;font-weight:bold;'>Manage Customers</span>";
        print "</div>";
        print "<p style='float:right'><a href='#' id='editCustomers' name='editCustomers'><u>Edit</u></a> | <a href='#' id='addCustomers' name='addCustomers'><u>Add</u></a></p>";
        print "<table id='editcustomerssetup' name='editcustomerssetup' cellspacing='0' cellpadding='4' border='0' width='100%'>";
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
                                if($channeltype =="Franchises")print "Select Franchisee:";
                                if($channeltype =="Commercial")print "Select Channel:";
                                if($channeltype =="Retail")print "Select Channel:";
                                print "<input type='hidden' id='channel_id_edit' style='width: 200px' />"; 
                            print "</td>";
                             print "<td>";   
                                $query  = "SELECT *";
                                $query .= " FROM `channels` WHERE `channels`.type='".$channeltype."'";
                                $query .= " ORDER BY id";  
                                print "<select id='channelcustomers_edit'>";
                                    print "<option value='' selected='selected'>[Please select]</option>";
                                    $result = mysqli_query($GLOBALS["link"],$query);
                                    while ($arr = mysqli_fetch_assoc($result)) {
                                        $Channel = $arr['name'];
                                        print "<option id='".$arr['id']."' value='".$arr['name']."'>";
                                            print ucfirst($Channel);
                                        print "</option>";
                                    }
                                print "</select>";
                            print "</td>";
                        print "</tr>";
                    }else{
                        print "<tr>";
                            print "<td colspan='2' style='text-align: right; font-weight: 400; color: #111111; padding-right: 20px;'>";                                 
                                print "Channel:";
                            print "</td>";
                             print "<td>";     
                                print "<input type='hidden' id='channel_id_edit' style='width: 200px' value='".$GLOBALS['system_user']->retailChannel."'/>";
                                $query  = "SELECT  `name`";
                                $query .= " FROM `channels` WHERE `id`='".$GLOBALS['system_user']->retailChannel."'";
                                $query .= " ORDER BY `id`";
                                $result = mysqli_query($GLOBALS["link"],$query);
                                $channel_name = "";
                                while ($arr = mysqli_fetch_assoc($result)) {
                                    $channel_name = $arr['name'];
                                }
                                print "<label id='channel_name' style='width: 200px;font-weight: bold;'>".$channel_name."</label>";
                            print "</td>";
                        print "</tr>";
                    }
                    if ($sysuser->isSuperAdmin){
                        print "<tr>";
                            print "<td colspan='2' style='text-align: right; font-weight: 400; color: #111111; padding-right: 20px;'>";                                 
                                print "Select Customer";
                                print "<input type='hidden' id='customer_id' style='width: 200px' />"; 
                            print "</td>"; 
                            print "<td>";     
                                print "<select id='customer_edit'>";
                                    print "<option value='' selected='selected'>[Please select]</option>";                    
                                print "</select>";
                            print "</td>";
                        print "</tr>";
                    }else{
                        print "<tr>";
                            print "<td colspan='2' style='text-align: right; font-weight: 400; color: #111111; padding-right: 20px;'>";                                 
                                print "Select Customer";
                                print "<input type='hidden' id='customer2_id' style='width: 200px' />"; 
                                print "<input type='hidden' id='channel_customer_id' style='width: 200px' value='".$GLOBALS['system_user']->retailChannel."'/>"; 
                                print "<input type='hidden' id='updated_by_id' style='width: 200px' value='".$GLOBALS['system_user']->id."'/>"; 

                            print "</td>"; 
                            print "<td>";     
                                print "<select id='customer2_edit'>";
                                    print "<option value='' selected='selected'>[Please select]</option>";  
                                    $query  = "SELECT  *";
                                    $query .= " FROM `customers` WHERE `channel`='".$GLOBALS['system_user']->retailChannel."'";
                                    $query .= " ORDER BY `id`";
                                    $result = mysqli_query($GLOBALS["link"],$query);  
                                    while ($arr = mysqli_fetch_assoc($result)) {
                                        $Customer = ucfirst($arr['name'])." ".ucfirst($arr['surname']);
                                        print "<option id='".$arr['id']."' value='".$arr['id']."'>";
                                            print ucfirst($Customer);
                                        print "</option>";
                                    }                
                                print "</select>";
                            print "</td>";
                        print "</tr>";
                    }

                    print "<tr>";
                        print "<td colspan='4' style='text-align: left; padding: 0'>";
                         print "<div>";
                             print "<table cellpadding='10'>";
                                print "<tr>";
                                     print "<td>";
                                        print "Name";
                                     print "</td>";
                                     print "<td>";
                                        print "<input type='text' id='customer_name_edit' style='width: 200px' />";    
                                    print "</td>";
                                print "</tr>";

                                print "<tr>";
                                     print "<td>";
                                        print "Surname";
                                     print "</td>";
                                     print "<td>";
                                        print "<input type='text' id='customer_surname_edit' style='width: 200px' />";    
                                    print "</td>";
                                print "</tr>";

                                 print "<tr>";
                                     print "<td>";
                                        print "Cellphone";
                                     print "</td>";
                                     print "<td>";
                                        print "<input type='text' id='customer_cell_edit' style='width: 200px' />";    
                                    print "</td>";
                                print "</tr>";

                                 print "<tr>";
                                     print "<td>";
                                        print "E-mail";
                                     print "</td>";
                                     print "<td>";
                                        print "<input type='text' id='customer_email_edit' style='width: 200px' />";    
                                    print "</td>";
                                print "</tr>";

                                print "<tr>";
                                 print "<td>";
                                    $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
                                    $sqlres = mysqli_query($GLOBALS["link"],$sql);
                                    $row = mysqli_fetch_assoc($sqlres);
                                    $channeltype = $row['type'];
                                    if($channeltype =="Franchises")print "Franchisee:";
                                    if($channeltype =="Commercial")print "Channel:";
                                    if($channeltype =="Retail")print "hannel:";
                                 print "</td>";
                                 print "<td>";
                                    print "<input type='text' id='customer_channel_edit' style='width: 200px' readonly/>"; 
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
                                print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button class='ibutton' id='update-customer-button'>Update Customer</button>";
                                print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button class='ibutton' id='delete-customer-button' style='color:red;font-weight: bold;font-size: 12px;' >Delete Customer</button>";
                                print "<br/><br/>";
                            print "</td>";
                        print "</tr>";
                    print "</tfoot>";
        print "</table>";
        print "<table id='addcustomerssetup' name='addcustomerssetup' cellspacing='0' cellpadding='4' border='0' width='100%'>";
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
                                    print "<input type='text' id='customer_name' style='width: 200px' />";    
                                print "</td>";
                            print "</tr>";

                            print "<tr>";
                                 print "<td>";
                                    print "Surname";
                                 print "</td>";
                                 print "<td>";
                                    print "<input type='text' id='customer_surname' style='width: 200px' />";    
                                print "</td>";
                            print "</tr>";

                             print "<tr>";
                                 print "<td>";
                                    print "Cellphone";
                                 print "</td>";
                                 print "<td>";
                                    print "<input type='text' id='customer_cell' style='width: 200px' />";    
                                print "</td>";
                            print "</tr>";

                             print "<tr>";
                                 print "<td>";
                                    print "E-mail";
                                 print "</td>";
                                 print "<td>";
                                    print "<input type='text' id='customer_email' style='width: 200px' />";    
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
                                      if($channeltype =="Commercial")print "Channel";
                                      if($channeltype =="Retail")print "Channel";
                                      if($channeltype =="Franchises")print "Franchises";
                                     print "</td>";
                                     print "<td>";
                                    
                                        if($sysuser->isSuperAdmin){
                                            print "<select id='customerchanneltype'>";
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
                                        }
                                        print "<br/><br/>";
                                        print "<select id='customerCommercial' style='display:none;' >";
                                            print "<option value=''>[Please select commercial]</option>";
                                            print "<option id ='All' value='All' >All</option>";               
                                            $sql = "SELECT * FROM `channels` WHERE `type`='Commercial'";
                                            $sqlres = mysqli_query($GLOBALS["link"],$sql);
                                            while ($row=mysqli_fetch_assoc($sqlres)){
                                                if ($channelname == $row["id"]) {
                                                    print "<option id='".$row['id']."' value='".$row["name"]."' selected>".ucfirst($row["name"])."</option>";
                                                }else{
                                                    print "<option id='".$row['id']."' value='".$row["name"]."'>".ucfirst($row["name"])."</option>";                
                                                }                       
                                            }
                                        print "</select>";

                                        print "<select id='customerFranchises' style='display:none;' >";
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

                                        print "<select id='customerRetail' style='display:none; >";
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
                                        //print "Channel";
                                    print "</td>";
                                    print "<td>";

                                        $query = "select id, name from channels where id='".$GLOBALS['system_user']->retailChannel."'";
                                        $result = mysqli_query($GLOBALS["link"],$query);
                                        $channelname = "";
                                        while ($row=mysqli_fetch_assoc($result)){
                                            $channelname =  ucfirst($row["name"]);
                                        }
         
                                        $channelname = str_replace("–", "-", $channelname);
                                        $channelname = str_replace("“", "\"", $channelname);
                                        $channelname = str_replace("”", "\"", $channelname);
                                        $channelname = str_replace("'", "\"", $channelname);
                                        $channelname = str_replace("’", "'", $channelname);
                                        $channelname = str_replace("-\n", "-\n\n", $channelname);
                                        $channelname = str_replace(".\n", ".\n\n", $channelname);
                                        //print "<input type='text' id='addcustomer_branch' style='width: 200px' value='". $channelname."' readonly/>";  
                                        print "<input type='hidden' id='addcustomer_branch_id' style='width: 200px' value='".$GLOBALS['system_user']->retailChannel."' readonly/>"; 
                                        print "<input type='hidden' id='created_by_id' style='width: 200px' value='".$GLOBALS['system_user']->id."' readonly/>"; 
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
                            print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button class='ibutton' id='add-customer-button'>Save Customer</button>";
                            print "<br/><br/>";
                        print "</td>";
                    print "</tr>";
                print "</tfoot>";
        print "</table>";
        //==========================================
        print '<script type="text/javascript" language="javascript" class="init">';
        print "jQuery('a#addCustomers').click(function () {
                $('table#editcustomerssetup').hide();
                $('table#addcustomerssetup').show(); 
            });\n
        ";
        
        print "$('table#editcustomerssetup').hide();
            jQuery('a#editCustomers').click(function () {
                $('table#addcustomerssetup').hide();
                $('table#editcustomerssetup').show(); 
            });\n
        ";

        /*print "jQuery('select#branchcustomer_edit').change(function () { 
                    var id = $('select#branchcustomer_edit').children(':selected').attr('id');               
                    $('input#channel_id_edit').val(id); 
                    AJAXCallCustomers('" . __CLASS__ . "', 'getCustomers', 'channelID='+id);  
                });\n
        ";

        print "jQuery('select#customer_edit').change(function () { 
                    var id = $('select#customer_edit').children(':selected').attr('id');               
                    $('input#customer_id').val(id); 
                    AJAXCallCustomerInfo('" . __CLASS__ . "', 'getCustomerInfo', 'customerID='+id);  
                });\n
            ";*/

        print "jQuery('select#customer_edit').change(function () { 
                    var id = $('select#customer_edit').children(':selected').attr('id');               
                    $('input#customer_id').val(id); 
                    AJAXCallCustomerInfo('" . __CLASS__ . "', 'getCustomerInfo', 'customerID='+id);  
                });\n
            ";

        print "jQuery('select#channelcustomers_edit').change(function () { 
                var id = $('select#channelcustomers_edit').children(':selected').attr('id'); 
                $('input#channel_id_edit').val(id); 
                AJAXCallAllCustomers('" . __CLASS__ . "', 'getAllCustomers', 'channelID='+id);  
            });\n";

        print "jQuery('select#customer2_edit').change(function () { 
                    var id = $('select#customer2_edit').children(':selected').attr('id'); 
                    $('input#customer2_id').val(id); 
                    AJAXCallCustomerInfo('" . __CLASS__ . "', 'getCustomerInfo', 'customerID='+id);  
                });\n";

        print "jQuery('select#customerchanneltype').change(function () { 
                  var type = $('select#customerchanneltype').val();  
                  if(type == 'Commercial'){
                    $('select#customerFranchises').hide();
                    $('select#customerRetail').hide(); 
                    $('select#customerCommercial').val('');  
                    $('select#customerCommercial').toggle();
                  }else if(type == 'Franchises'){
                    $('select#customerFranchises').hide();
                    $('select#customerRetail').hide(); 
                    $('select#customerFranchises').val('');  
                    $('select#customerFranchises').toggle();
                  }else if(type == 'Retail'){
                    $('select#customerCommercial').hide();
                    $('select#customerFranchises').hide(); 
                    $('select#customerRetail').val('');  
                    $('select#customerRetail').toggle();
                  }else if(type == 'All'){
                    $('select#customerCommercial').val('');  
                    $('select#customerRetail').val('');  
                    $('select#customerFranchises').val('');  

                    $('select#customerCommercial').hide();
                    $('select#customerFranchises').hide(); 
                    $('select#customerRetail').hide();
                  }       
                });\n";


        print "jQuery('#add-customer-button').click(function () {  
                    var name  = jQuery('input#customer_name').val(); 
                    var surname  = jQuery('input#customer_surname').val(); 
                    var cell  = jQuery('input#customer_cell').val(); 
                    var email  = jQuery('input#customer_email').val(); 
 
                    var channeltype = $('select#customerchanneltype').attr('id');
                    var channel = '';
                    if(channeltype == 'Commercial'){
                        channel = $('select#customerCommercial').attr('id');
                    }else if(channeltype == 'Retail'){
                        channel = $('select#customerRetail').attr('id');
                    }else if(channeltype == 'Franchises'){
                        channel = $('select#customerFranchises').attr('id');
                    }else if(channeltype == 'All'){
                        channel = 'All';
                    }else{
                        channel =  $('input#addcustomer_branch').val();  
                        channel =  $('input#addcustomer_branch_id').val();  
                    }
                    var created_by = $('input#created_by_id').val();  
                    AJAXCallModuleJSOnly('" . __CLASS__ . "', 'save_customer', 'name='+name+'&surname='+surname+'&cell='+cell+'&email='+email+'&channel='+channel+'&created_by='+created_by);
                });\n";

        print "jQuery('#update-customer-button').click(function () {  
                    var name  = jQuery('input#customer_name_edit').val(); 
                    var surname  = jQuery('input#customer_surname_edit').val(); 
                    var cell  = jQuery('input#customer_cell_edit').val(); 
                    var email  = jQuery('input#customer_email_edit').val(); 
                    var channel  = jQuery('input#channel_customer_id').val(); 
                    var customer  = jQuery('input#customer2_id').val();
                    var updated_by  = jQuery('input#updated_by_id').val(); 
                    AJAXCallModuleJSOnly('" . __CLASS__ . "', 'update_customer', 'name='+name+'&surname='+surname+'&cell='+cell+'&email='+email+'&channel='+channel+'&customer='+customer+'&updated_by='+updated_by);
                });\n
            ";  

        print "jQuery('#delete-customer-button').click(function () {
                    var customer  = jQuery('input#customer_id').val(); 
                    AJAXCallModuleJSOnly('" . __CLASS__ . "', 'delete_customer', 'customer='+customer);
                });\n
            "; 
        print '</script>';
        print "<div style='margin: 0 auto; width: 95%; border-bottom: 2px solid #BBBBBB;'>&nbsp;</div>";
        print '</div>';
    }
    //=======================================
    //Customer
    public function save_customer($array) {

        if ($GLOBALS['system_user']->hasPermission('manage_customers')) {

            $query  = "INSERT INTO `customers` (date_created,name,surname,cellphone,email,channel,active,created_by) VALUES (";
            $query  .= "NOW(),";
            $query  .= " '".$array["name"]."',";
            $query  .= " '".$array["surname"]."',";
            $query  .= " '".$array["cell"]."',";
            $query  .= " '".$array["email"]."',";
            $query  .= " '".$array["channel"]."',";
            $query  .= " '1',";
            $query  .= " '".$array["created_by"]."'";
            $query  .= ") ";
            $result = mysqli_query($GLOBALS["link"],$query);
            if($result) {
                print "jQuery(\"#ok-message\").html(\"Customer saved... \").show(0).delay(4000).hide(0);\n";
                $title = $array["name"]." ".$array["surname"];
                logAction("Added Customer:$title");
            } else {
                print "jQuery(\"#error-message\").html(\"Unable to save Customer saved Inforamtion! Please try again...\").show(0).delay(4000).hide(0);\n";
            }  
            exit();  
     
        }else{
            print "You do not have permission to perform this action.";
            exit();            
        }
    }

     public function getAllCustomers() {
        $channelID = (int)isset($_POST["channelID"])?$_POST["channelID"]:0;
        $query  = "SELECT *";
        $query .= " FROM `customers`";
        $query .= " WHERE `customers`.channel='".$channelID."' LIMIT 1";
        $result = mysqli_query($GLOBALS["link"],$query);
        $array = array();
        while ($arr = mysqli_fetch_assoc($result)) {
          $array[] = $arr;
        }
        print json_encode($array);
    }

    public function getCustomerInfo() {
        $customerID = (int)isset($_POST["customerID"])?$_POST["customerID"]:0;
        $query  = "SELECT *,`customers`.name AS customer_name,`channels`.name AS channel_name";
        $query .= " FROM `customers` INNER JOIN `channels` ON `channels`.id = `customers`.channel";
        $query .= " WHERE `customers`.id='".$customerID."' LIMIT 1";
        $result = mysqli_query($GLOBALS["link"],$query);
        $array = array();
        while ($arr = mysqli_fetch_assoc($result)) {
          $array[] = $arr;
        }
        print json_encode($array);
    }

    public function update_customer ($array) {
        if ($GLOBALS['system_user']->hasPermission('manage_customers')) {
            $sql = "UPDATE customers SET";
            $sql .= " `last_accessed` =NOW(),";
            $sql .= " `name` = '".$array["name"]."',";
            $sql .= " `surname` = '".$array["surname"]."',";
            $sql .= " `cellphone` = '".$array["cell"]."',";
            $sql .= " `email` = '".$array["email"]."',";
            $sql .= " `channel` = '".$array["channel"]."',";
            $sql .= " `updated_by` = '".$array["updated_by"]."'";
            $sql .= " WHERE id=".$array["customer"]." LIMIT 1";
            $result = mysqli_query($GLOBALS["link"],$sql);
            if($result) {
                print "jQuery(\"#ok-message2\").html(\"Customer update... \").show(0).delay(4000).hide(0);\n";
                $title = $array["customer"];
                logAction("Deleted Customer:$title");
            } else {
               print "jQuery(\"#error-message2\").html(\"Unable to update Customer Inforamtion! Please try again...\").show(0).delay(4000).hide(0);\n";
            }  
            exit();
        }else{
            print "You do not have permission to perform this action.";
            exit();            
        }
    }

    public function delete_customer ($array) {
        if ($GLOBALS['system_user']->hasPermission('manage_customers')) {
            $sql = "UPDATE customers SET";
            $sql .= " `last_updated` =NOW(),";
            $sql .= " `active` = '1'";
            $sql .= " WHERE id=".$array["customer"]." LIMIT 1";
            $result = mysqli_query($GLOBALS["link"],$sql);
            if($result) {
                print "jQuery(\"#ok-message2\").html(\"Customer deleted...  \").show(0).delay(4000).hide(0);\n";
                $title = $array["customer"];
                logAction("Deleted Customer:$title");
            } else {
               print "jQuery(\"#error-message2\").html(\"Unable to delete Customer Inforamtion! Please try again...\").show(0).delay(4000).hide(0);\n";
            }  
            exit();
        }else{
            print "You do not have permission to perform this action.";
            exit();            
        }
    }
}
?>