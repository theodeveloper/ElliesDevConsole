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
     register_menu("Dashboard", "parentMenu", "Dashboard");
     register_permission("Dashboard", "dashboard", "Access to Dashboard module");

     class Dashboard {
        
        private $items_per_page;
        private $approvedQuotations;
        private $rejectedQuotations;
        private $sentQuotations;
        private $printQuotations;
        private $pendingQuotations;
        
        function __construct() {
            $this->items_per_page = Settings::getSetting(2);
        }

        function main () {
            $array = array();
            $sql = "SELECT * FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
            $sqlres = mysqli_query($GLOBALS["link"],$sql);
            $row = mysqli_fetch_assoc($sqlres);
            $channeltype = $row['type'];
            $channelname = $row['name'];


            $sysuser = new userType($_SESSION["userid"]);
            /*if ($sysuser->isSuperAdmin && $channeltype =="Franchises"){//Based on a user that has permissions see more than one channel
                $sql = "SELECT * FROM `channels` WHERE `type`='Franchises' LIMIT 1";
                $sqlres = mysqli_query($GLOBALS["link"],$sql);
                while ($row=mysqli_fetch_assoc($sqlres)){
                    $array['selectedchannel'] = $row['id'];
                    $array['ChannelType'] = "Franchises";
                    $array['Channelname'] = $row['name'];
                }
            }elseif ($sysuser->isSuperAdmin && $channeltype !="Franchises"){//Based on a user that has permissions see more than one channel
                $array['ChannelType'] = $channeltype;
                $array['Channelname'] = "All";
            }else*/
            if ($sysuser->isSuperAdmin){
                $array['ChannelType'] = $channeltype;
                $array['Channelname'] = "All";
            }else{
                if($channeltype =="Commercial"){
                    $sql = "SELECT * FROM `channels` WHERE `type`='Commercial' LIMIT 1";
                    $sqlres = mysqli_query($GLOBALS["link"],$sql);
                    while ($row=mysqli_fetch_assoc($sqlres)){
                        $array['selectedchannel'] = $row['id'];
                        $array['ChannelType'] = "Commercial";
                        $array['Channelname'] = $channelname;
                    }
                }elseif($channeltype =="Franchises"){
                    $sql = "SELECT * FROM `channels` WHERE `type`='Franchises' LIMIT 1";
                    $sqlres = mysqli_query($GLOBALS["link"],$sql);
                    while ($row=mysqli_fetch_assoc($sqlres)){
                        $array['selectedchannel'] = $row['id'];
                        $array['ChannelType'] = "Franchises";
                        $array['Channelname'] = $channelname;
                    }
                }elseif($channeltype =="Retail"){
                    $sql = "SELECT * FROM `channels` WHERE `type`='Retail' LIMIT 1";
                    $sqlres = mysqli_query($GLOBALS["link"],$sql);
                    while ($row=mysqli_fetch_assoc($sqlres)){
                        $array['selectedchannel'] = $row['id'];
                        $array['ChannelType'] = "Retail";
                        $array['Channelname'] = $channelname;
                    }
                }
            }
            $this->dashboardpage($array);
        }

        public function getCriteria($Channel="",$Criteria=0){
            $sql = "SELECT `type` FROM `channels` WHERE `id`=".$Channel;
            $sqlres = mysqli_query($GLOBALS["link"],$sql);
            $row = mysqli_fetch_assoc($sqlres);
            $channeltype = $row['type'];
            $cond = "";

            if($Criteria == 0){
                if ($channeltype == "Commercial" || $channeltype =="Franchises"){
                    if ($GLOBALS['system_user']->isSuperAdmin == FALSE){
                        $cond =" AND `quotes`.created_by in (SELECT id from system_users where store_id = ".$GLOBALS['system_user']->storeID.")";
                    } else {
                        //$cond = " AND `quotes`.created_by in (SELECT id from system_users where branch_id =".$GLOBALS['system_user']->branchID.")";

                        if($Channel == "All"){
                            $cond = " AND `quotes`.created_by in (SELECT id from system_users where branch_id IN (SELECT id from branches where channel IN (SELECT id from channels where type='".$channeltype."')))";
                        }else{
                           $cond = " AND `quotes`.created_by in (SELECT id from system_users where branch_id IN (SELECT id from branches where channel=".$Channel."))"; 
                        }
                    }
                } else {
                    if ($GLOBALS['system_user']->isSuperAdmin == FALSE){
                        $cond =" AND `quotes`.created_by in (SELECT id from system_users where store_id = ".$GLOBALS['system_user']->storeID.")";
                    } else {
                        /*$cond = " AND `quotes`.created_by in (SELECT id from system_users where branch_id IN (SELECT id from branches where channel=".$Channel."))";*/

                        if($Channel == "All"){
                            $cond = " AND `quotes`.created_by in (SELECT id from system_users where branch_id IN (SELECT id from branches where channel IN (SELECT id from channels where type='".$channeltype."')))";
                        }else{
                           $cond = " AND `quotes`.created_by in (SELECT id from system_users where branch_id IN (SELECT id from branches where channel=".$Channel."))"; 
                        }
                    }
                } 
            }else if($Criteria == 1){
                 $cond = " AND channel=".$Channel."";
            }   
            return $cond;       
        }

        function getTotalStatus($Channel="",$Criteria=0){

            //Approved
            $sql = "SELECT * FROM `quotes` where approved=1 ";
            $sql.= $this->getCriteria($Channel,$Criteria);
            $sql .=" ORDER BY `last_updated` DESC";
            $sqlres = mysqli_query($GLOBALS["link"],$sql);
            $this->approvedQuotations = mysqli_num_rows($sqlres);

            //Rejected
            $sql = "SELECT * FROM `quotes` where approved=-1";
            $sql.= $this->getCriteria($Channel,$Criteria);
            $sql.= " ORDER BY `last_updated` DESC";
            $sqlres = mysqli_query($GLOBALS["link"],$sql);
            $this->rejectedQuotations = mysqli_num_rows($sqlres);

            //Printed
            $sql = "SELECT * ";
            $sql .= " FROM `quotes`";
            $sql .= " INNER JOIN `quote_submissions` ON `quote_submissions`.subID = `quotes`.id WHERE `approved` = 1  AND `printed` = 'Yes'";
            $sql.= $this->getCriteria($Channel,$Criteria);
            $sql.= " ORDER BY `last_updated` DESC";
            $sqlres = mysqli_query($GLOBALS["link"],$sql);
            $this->printQuotations = mysqli_num_rows($sqlres);

            //Pending
            $sql = "SELECT * FROM `quotes` where approved=0 and complete=1";
            $sql.= $this->getCriteria($Channel,$Criteria);
            $sql .= " ORDER BY `last_updated` DESC";
            $sqlres = mysqli_query($GLOBALS["link"],$sql);
            $this->pendingQuotations = mysqli_num_rows($sqlres);

            //Sent
            $sql = "SELECT * FROM `quotes` where approved=1 ";
            $sql.= $this->getCriteria($Channel,$Criteria);
            $sql .=" ORDER BY `last_updated` DESC";
            $sqlres = mysqli_query($GLOBALS["link"],$sql);
            $row = mysqli_fetch_assoc($sqlres);
            $this->sentQuotations = mysqli_num_rows($sqlres);
        }

        public function getChannelType($channel=""){
            $channeltype="";
            if($channel !==""){
                $sql = "SELECT `type` FROM `channels` WHERE `id`=".$channel;
                $sqlres = mysqli_query($GLOBALS["link"],$sql);
                $row = mysqli_fetch_assoc($sqlres);
                $channeltype = $row['type'];
            }
            return $channeltype;
        }

        //Not used
        public function dashboardInstallerpage ($array) {
            require_once(dirname(__FILE__)."/../../fusioncharts/includes/fusioncharts.php");
            print "<h3 style='font-size:14px;font-weight:bold;font-weight:bold;color:#8dd03f;'>".$channelname."Dashboard</h3>";

            $channelname = "";
            print "<h1 style='font-size:29px;font-weight:bold;color:#8dd03f;margin-top: 32px;margin-left: 349px;'>".$channelname."</h1>";
            print "<br/><br/><br/>";

            $channel = $GLOBALS['system_user']->retailChannel;

            //Quotes by Status
            $this->getTotalStatus($channel);
            $Total = $this->pendingQuotations+$this->approvedQuotations+$this->sentQuotations+$this->rejectedQuotations+$this->printQuotations;
            $doughnut2dChart = new FusionCharts("doughnut2d", "QuotesStatus" , 340, 300, "quoteStatusGraph", "json", '{  
                   "chart":{  
                      "use3DLighting": "0",
                      "enableSmartLabels": "0",
                      "startingAngle": "310",
                      "showLabels": "0",
                      "showPercentValues": "1",
                      "showLegend": "1",
                      "defaultCenterLabel": "Total Status: '.$Total.'",
                      "centerLabel": "Total $label: $value",
                      "centerLabelBold": "1",
                      "showTooltip": "1",
                      "decimals": "0",
                      "useDataPlotColorForLabels": "1",
                      "theme":"fint"
                   },
                   "data":[  
                      {  
                         "label":"Printed Quotations",
                         "value":"'.$this->printQuotations.'"
                      },
                      {  
                         "label":"Approved Quotations",
                         "value":"'.$this->approvedQuotations.'"
                      },
                      {  
                         "label":"Sent Quotations",
                         "value":"'.$this->sentQuotations.'"
                      },
                      {  
                         "label":"Pending Quotations",
                         "value":"'.$this->pendingQuotations.'"
                      },
                      {  
                         "label":"Rejected Quotations",
                         "value":"'.$this->rejectedQuotations.'"
                      }
                   ]
                }');
            // Render the chart
            $doughnut2dChart->render();
            print "<div style='display:inline-block'>";
                print "<h2 style='font-size:16px;font-weight:bold;color:#8dd03f;margin-top: 32px;margin-left: 34px;'>Quotes by Status</h2>";
                print "<br/>";
                $tableStatus = "<table border='1' cellpadding='10' style='border: 2px solid black;margin-left: 34px;'>
                                 <tr>
                                    <td ><div id='quoteStatusGraph'></div></td>
                                    <td width='350'>
                                        <table style='border: 1px solid black;border-collapse: collapse;' width='100%' cellpadding='20'>
                                            <tr>
                                                <td style='border:1px solid black;font-weight:bold;' bgcolor='#e57f20'>Total Pending Quotations</td>
                                                <td style='border:1px solid black;' width='50' align='center'>".$this->pendingQuotations."</td>
                                            </tr>
                                            <tr>
                                                <td style='border:1px solid black;font-weight:bold;' bgcolor='#ffe900'>Total Sent Quotations</td>
                                                <td style='border:1px solid black;' align='center'>".$this->sentQuotations."</td>
                                            </tr>
                                            <tr>
                                                <td style='border:1px solid black;font-weight:bold;' bgcolor='#ff0000'>Total Rejected Quotations</td>
                                                <td style='border:1px solid black;' align='center'>".$this->rejectedQuotations."</td>
                                            </tr>
                                            <tr>
                                                <td style='border:1px solid black;font-weight:bold;' bgcolor='#0faa17'>Total Approved Quotations</td>
                                                <td style='border:1px solid black;' align='center'>".$this->approvedQuotations."</td>
                                            </tr>
                                            <tr>
                                                <td style='border:1px solid black;font-weight:bold;' bgcolor='#0279ef'>Total Printed Quotations</td>
                                                <td style='border:1px solid black;' align='center'>".$this->printQuotations."</td>
                                            </tr>
                                        </table>
                                    </td>
                                 </tr>
                                </table>";
                echo $tableStatus;
            print "</div>";
            //======================
            //Latest Customers
            print "<div style='display:inline-block;margin-left: 60px;width: 718px'>";       
                $count=0;
                if ($GLOBALS['system_user']->hasPermission('view_quotes')) {
                  $sql = "SELECT * FROM `customers` where active=1 ";
                  $sql .= "AND channel =".$channel;
                  $sql .= " ORDER BY `date_created` DESC LIMIT 7";
                  $sqlres = mysqli_query($GLOBALS["link"],$sql);
                  $count = mysqli_num_rows($sqlres);
                }
                print "<h3 style='font-size:12px;font-weight:bold;color:#8dd03f;margin-top: 32px;margin-left: 3px;float:right;'>Total Customers:".$count."</h3>";
                print "<h2 style='font-size:16px;font-weight:bold;color:#8dd03f;margin-top: 32px;margin-left: 3px;'>Latest Customers</h2>";
                print "<br/>";
                if ($GLOBALS['system_user']->hasPermission('view_quotes')) {
                    $sql = "SELECT * FROM `customers` where active=1 ";
                    $sql .= "AND channel =".$channel;
                    $sql .= " ORDER BY `date_created` DESC LIMIT 7";
                    $sqlres = mysqli_query($GLOBALS["link"],$sql);
                    $row = mysqli_fetch_assoc($sqlres);
                    if($row['id'] == ""){
                        print "No Customers";
                    }else{
                         print '
                            <head>
                                <script type="text/javascript" language="javascript" class="init">
                                $(document).ready(function() {
                                    $("#latestcustomers").dataTable( {
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
                                        "order": [[ 2, "desc" ]]
                                    } );
                                } );
                                </script>
                            </head>
                            <body>
                                <table id="latestcustomers" class="display" cellspacing="3">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Surname</th>
                                            <th>Cellphone</th>
                                            <th>Email</th>
                                            <th>Company</th>                 
                                        </tr>
                                    </thead>
                                    <tbody>';
                                    $sql = "SELECT * FROM `customers` where active=1 ";
                                    $sql .= "AND channel =".$channel;
                                    $sql .= " ORDER BY `date_created` DESC LIMIT 7";
                                    $sqlres = mysqli_query($GLOBALS["link"],$sql);                    

                                    while($row = mysqli_fetch_assoc($sqlres)) {
                                        print "<tr>";
                                        print "<td align='center'>".$row['name']."</td>";
                                        print "<td align='center'>".$row['surname']."</td>";
                                        print "<td align='center'>".$row['cellphone']."</td>";
                                        print "<td align='center'>".$row['email']."</td>";
                                        print "<td align='center'>".$row['company']."</td>";
                                        print "</tr>";
                                    }
                            print '</tbody>
                                </table>
                            </body>';
                    }
                }else{
                    print "<p>You do not have permission to view Customers.</p>";
                }
            print "</div>";
            //======================
            //Latest Quotes
            print "<div style='height: 100px;width: 775px;margin-left: 34px;display:inline-block'>";
                print "<h2 style='font-size:16px;font-weight:bold;color:#8dd03f;margin-top: 32px;margin-left: 2px;'>Latest Quotes</h2>";
                print "<br/>";
                if ($GLOBALS['system_user']->hasPermission('view_quotes')) {
                    $sql = "SELECT * FROM `quotes` where approved=0 and complete=1";
                    $sql.= $this->getCriteria($channel);
                    $sql .= " ORDER BY `date_created` DESC LIMIT 8";
                    $sqlres = mysqli_query($GLOBALS["link"],$sql);
                    $row = mysqli_fetch_assoc($sqlres);
                    if($row['customer_id'] == ""){
                        print "No Pending quotes";
                    }else{
                        print '
                            <head>
                                <script type="text/javascript" language="javascript" class="init">
                                $(document).ready(function() {
                                    $("#lastquotes").dataTable( {
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
                                        "order": [[ 1, "desc" ]]
                                    } );
                                } );
                                </script>
                            </head>
                            <body>
                                <table id="lastquotes" class="display" cellspacing="3">
                                    <thead>
                                        <tr>
                                            <th width="70">Date</th>
                                            <th>Quote Ref</th>              
                                            <th>Location</th>
                                            <th>Customer</th>
                                            <th>Cellphone</th>
                                            <th>Created By</th>
                                            <th width="60">&nbsp;View</th>                
                                        </tr>
                                    </thead>
                                    <tbody>';
                                    $sql = "SELECT `type` FROM `channels` WHERE `id`=".$channel;
                                    $sqlres = mysqli_query($GLOBALS["link"],$sql);
                                    $row = mysqli_fetch_assoc($sqlres);
                                    $channeltype = $row['type'];

                                    if ($channeltype == "Commercial" || $channeltype =="Franchises"){
                                        $suffix="comm";
                                    }else {
                                        $suffix="";
                                    }
                                    $sql = "SELECT * FROM `quotes` where approved=0 and complete=1";
                                    $sql.= $this->getCriteria($channel);
                                    
                                    $sql .= " ORDER BY `date_created` DESC LIMIT 8";
                                    $sqlres = mysqli_query($GLOBALS["link"],$sql);

                                    while($row = mysqli_fetch_assoc($sqlres)) {
                                        $quote = new Quote($row["id"]);
                                        $customer = new Customer($quote->CustomerID);

                                        print "<tr>";
                                        if(strtotime($quote->LastUpdated) > strtotime($quote->DateCreated)){
                                          print "<td align='center'  bgcolor='#525656' style='font-weight:bold;'>";//modified
                                        }else{
                                          print "<td align='center'>"; 
                                        }
                                        
                                        print date("Y-m-d", strtotime($quote->DateCreated));
                                        print "</td>";
                                        print "<td align='center'>".$quote->QuoteReferenceNo."</td>";
                                        
                                        print "<td align='center'>";
                                        if ($row["lat"]!=0){
                                        print "<a href='http://maps.google.com?q=".$row["lat"].",".$row["lng"]."' target='_blank'><span class='ui-button-text'>View Location</span>";
                                        }
                                        print "</td>";
                                        print "<td align='center'>".$customer->Surname.", ".$customer->Name."</td>";
                                        print "<td align='center'>".$customer->CellPhone."</td>";
                                        print "<td align='center'>".$quote->CreatedByUserName."</td>";
                                        print "<td align='center'><a href='quotes/editquote".$suffix.".php?id=".$row["id"]."&reurl=completed_quotations' target='_blank'><span class='ui-button-text'><img  width='20' height='20' title='View Details'  src='modules/dashboard/images/search.ico' /></span></a></td>";
                                        print "</tr>";
                                    }
                            print '</tbody>
                                </table>
                            </body>';
                    }
                }else{
                    print "<p>You do not have permission to view latest quotes.</p>";
                }
            print "</div>";
            //======================
            //Building Types
            print "<div style='width: 717px;margin-left: 28px;display:inline-block'>";
                print "<br/>";
                print "<h2 style='font-size:16px;font-weight:bold;color:#8dd03f;margin-top: 32px;margin-left: 2px;'>Building Types</h2>";
                print "<br/>";
                if ($GLOBALS['system_user']->hasPermission('view_quotes')) {
                    $sql = "SELECT * FROM `property_types` where deleted=0 ";
                    $sqlres = mysqli_query($GLOBALS["link"],$sql);
                    $row = mysqli_fetch_assoc($sqlres);
                    if($row['id'] == ""){
                        print "No Property Types";
                    }else{
                         print '
                            <head>
                                <script type="text/javascript" language="javascript" class="init">
                                $(document).ready(function() {
                                    $("#buildingtypes").dataTable( {
                                        columnDefs: [ {
                                            targets: [ 0 ],
                                            orderData: [ 0, 0 ]
                                        } ],
                                        "order": [[ 0, "desc" ]]
                                    } );
                                } );
                                </script>
                            </head>
                            <body>
                                <table id="buildingtypes" class="display" cellspacing="3">
                                    <thead>
                                        <tr>
                                            <th>Name</th>                
                                        </tr>
                                    </thead>
                                    <tbody>';
                                    $sql = "SELECT * FROM `property_types` where deleted=0 ";
                                    $sqlres = mysqli_query($GLOBALS["link"],$sql);                   

                                    while($row = mysqli_fetch_assoc($sqlres)) {
                                        print "<tr>";
                                        print "<td >".$row['Type']."</td>";;
                                        print "</tr>";
                                    }
                            print '</tbody>
                                </table>
                            </body>';
                    }
                }else{
                    print "<p>You do not have permission to view Building Types.</p>";
                }
            print "</div>";
            //======================
            //Products by Category
            print "<div style='margin-left: 30px;display:inline-block'>";   
                print "<br/>";          
                print "<h2 style='font-size:16px;font-weight:bold;color:#8dd03f;margin-top: 32px;margin-left: 2px;'>Products by Category</h2>";
                print "<br/>";
                $arrProducts = array();
                $arrProductNum = array();

                $sql = "SELECT DISTINCT `class_of_product` FROM `new_products`where channel=".$channel." and deleted=0 ORDER BY `class_of_product`";
                $sqlres = mysqli_query($GLOBALS["link"],$sql);
                while($row = mysqli_fetch_assoc($sqlres)) {
                    $arrProducts[] = ucwords(strtolower($row["class_of_product"]));             
                }

                $Total = 0;
                $data = '';
                for($i=0;$i<count($arrProducts);$i++){
                    $sql = "SELECT * FROM `new_products`where `class_of_product`='".strtoupper($arrProducts[$i])."' AND  channel=".$channel." AND deleted=0 ORDER BY `class_of_product`";
                    $sqlres = mysqli_query($GLOBALS["link"],$sql);
                    $rowcount=mysqli_num_rows($sqlres);
                    $arrProductNum[] = $rowcount;
                    $Total +=$rowcount;
                    //Chart
                    if($i == count($arrProducts)-1){
                        $data .= '{  
                                    "label":"'.$arrProducts[$i].'",
                                    "value":"'.$rowcount.'"
                                  }';
                    }else{
                        $data .= '{  
                                     "label":"'.$arrProducts[$i].'",
                                     "value":"'.$rowcount.'"
                                  },';
                    }          
                }   

                $doughnut3dChart = new FusionCharts("doughnut2d", "ProductsbyCategory" , 390, 400, "productsGraph", "json", '{  
                       "chart":{  
                          "use3DLighting": "0",
                          "enableSmartLabels": "0",
                          "startingAngle": "310",
                          "showLabels": "0",
                          "showPercentValues": "1",
                          "showLegend": "1",
                          "defaultCenterLabel": "Total Products: '.$Total.'",
                          "centerLabel": "Total $label: $value",
                          "centerLabelBold": "1",
                          "showTooltip": "1",
                          "decimals": "0",
                          "useDataPlotColorForLabels": "1",
                          "theme":"fint"
                       },
                       "data":['.$data.']
                }');
                // Render the chart
                $doughnut3dChart->render();
                
                $tableProducts = "<table border='1' cellpadding='10' style='border: 2px solid black;margin-left: 4px;margin-bottom:40px'>
                                  <tr>
                                    <td ><div id='productsGraph'></div></td>  
                                    <td width='350'>
                                            <table style='border: 1px solid black;border-collapse: collapse;' width='100%' >";

                                            for($i=0;$i<count($arrProducts);$i++){
                                                $tableProducts .= "<tr>
                                                                        <td style='border:1px solid black;font-weight:bold;' bgcolor='#828989' width='40'>".$arrProducts[$i]."</td>
                                                                        <td style='border:1px solid black;' width='30' align='center'>".$arrProductNum[$i]."</td>
                                                         
                                                                    </tr>";         
                                            }               
                $tableProducts .="          </table>
                                    </td>
                                   </tr>
                                </table>";
                echo $tableProducts;
            print "</div>";
            //======================
            //Latest Products
            print "<div style='width: 680px;margin-left: 41px;display:inline-block'>";
                print "<br/>";
                print "<h2 style='font-size:16px;font-weight:bold;color:#8dd03f;margin-top: 32px;margin-left: 3px;'>Latest Products</h2>";
                print "<br/>";
                if ($GLOBALS['system_user']->hasPermission('view_quotes')) {
                    $sql = "SELECT * FROM `new_products` where channel=".$channel." and deleted=0";
                    $sql .= " ORDER BY `date_created` DESC LIMIT 11";
                    $sqlres = mysqli_query($GLOBALS["link"],$sql);
                    $row = mysqli_fetch_assoc($sqlres);
                    if($row['id'] == ""){
                        print "No Latest Products";
                    }else{
                         print '
                            <head>
                                <script type="text/javascript" language="javascript" class="init">
                                $(document).ready(function() {
                                    $("#latestproducts").dataTable( {
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
                                </script>
                            </head>
                            <body>
                                <table id="latestproducts" class="display" cellspacing="3">
                                    <thead>
                                        <tr>
                                            <th>Code</th>
                                            <th>Name</th>   
                                            <th>Class of Product</th>                
                                            <th>Supplier</th>                
                                        </tr>
                                    </thead>
                                    <tbody>';
                                    $sql = "SELECT * FROM `new_products` where channel=".$channel." and deleted=0";
                                    $sql .= " ORDER BY `date_created` DESC LIMIT 11";
                                    $sqlres = mysqli_query($GLOBALS["link"],$sql);
                                    $row = mysqli_fetch_assoc($sqlres);                  

                                    while($row = mysqli_fetch_assoc($sqlres)){
                                      print "<tr>";
                                      print "<td align='center'>".$row['code']."</td>";
                                      print "<td align='center'>".$row['product']."</td>";
                                      print "<td align='center'>".$row['class_of_product']."</td>";
                                      print "<td align='center'>".$row['supplier']."</td>";
                                      print "</tr>";
                                    }
                            print '</tbody>
                                </table>
                            </body>';
                    }
                }else{
                    print "<p>You do not have permission to view Latest Products.</p>";
                }
            print "</div>"; 
        }

        public function dashboardChannelTypeAllpage ($selectedchanneltype="") {
            require_once(dirname(__FILE__)."/../../fusioncharts/includes/fusioncharts.php");

            //Get all channels for type
            $sql = "SELECT `id` FROM `channels` where type='".$selectedchanneltype."'";
            $sqlres = mysqli_query($GLOBALS["link"],$sql);
            $arrChannels = array();
            while ($row=mysqli_fetch_assoc($sqlres)){
                $arrChannels[] =$row['id'];
            }

            //Quotes by Status
            $PendingQuotations = 0;
            $ApprovedQuotations = 0;
            $SentQuotations = 0;
            $RejectQuotations = 0;
            $PrintQuotations = 0;

            for($i=0;$i<count($arrChannels);$i++){
                $this->getTotalStatus($arrChannels[$i],1);
                $PendingQuotations += $this->pendingQuotations;
                $ApprovedQuotations += $this->approvedQuotations;
                $SentQuotations += $this->sentQuotations;
                $RejectQuotations += $this->rejectedQuotations;
                $PrintQuotations += $this->printQuotations;
            }
            
            $Total = $PendingQuotations+$ApprovedQuotations+$SentQuotations+$RejectQuotations+$PrintQuotations;
            $doughnut2dChart = new FusionCharts("doughnut2d", "QuotesStatus" , 340, 300, "quoteStatusGraph", "json", '{  
                   "chart":{  
                      "use3DLighting": "0",
                      "enableSmartLabels": "0",
                      "startingAngle": "310",
                      "showLabels": "0",
                      "showPercentValues": "1",
                      "showLegend": "1",
                      "defaultCenterLabel": "Total Status: '.$Total.'",
                      "centerLabel": "Total $label: $value",
                      "centerLabelBold": "1",
                      "showTooltip": "1",
                      "decimals": "0",
                      "useDataPlotColorForLabels": "1",
                      "theme":"fint"
                   },
                   "data":[  
                      {  
                         "label":"Printed Quotations",
                         "value":"'.$PrintQuotations.'"
                      },
                      {  
                         "label":"Approved Quotations",
                         "value":"'.$ApprovedQuotations.'"
                      },
                      {  
                         "label":"Sent Quotations",
                         "value":"'.$SentQuotations.'"
                      },
                      {  
                         "label":"Pending Quotations",
                         "value":"'.$PendingQuotations.'"
                      },
                      {  
                         "label":"Rejected Quotations",
                         "value":"'.$RejectQuotations.'"
                      }
                   ]
                }');
            // Render the chart
            $doughnut2dChart->render();
            print "<div style='display:inline-block'>";
                print "<h2 style='font-size:16px;font-weight:bold;color:#8dd03f;margin-top: 32px;margin-left: 34px;'>Quotes by Status</h2>";
                print "<br/>";
                $tableStatus = "<table border='0' cellpadding='10' style='border: 1px solid #aaa7a7;margin-left: 34px;'>
                                 <tr>
                                    <td><div id='quoteStatusGraph'></div></td>
                                    <td width='350'>
                                        <table style='border: 1px solid black;border-collapse: collapse;' width='100%' cellpadding='20'>
                                            <tr>
                                                <td style='border:1px solid black;font-weight:bold;' bgcolor='#e57f20'>Total Pending Quotations</td>
                                                <td style='border:1px solid black;' width='50' align='center'>".$PendingQuotations."</td>
                                            </tr>
                                            <tr>
                                                <td style='border:1px solid black;font-weight:bold;' bgcolor='#ffe900'>Total Sent Quotations</td>
                                                <td style='border:1px solid black;' align='center'>".$SentQuotations."</td>
                                            </tr>
                                            <tr>
                                                <td style='border:1px solid black;font-weight:bold;' bgcolor='#ff0000'>Total Rejected Quotations</td>
                                                <td style='border:1px solid black;' align='center'>".$RejectQuotations."</td>
                                            </tr>
                                            <tr>
                                                <td style='border:1px solid black;font-weight:bold;' bgcolor='#0faa17'>Total Approved Quotations</td>
                                                <td style='border:1px solid black;' align='center'>".$ApprovedQuotations."</td>
                                            </tr>
                                            <tr>
                                                <td style='border:1px solid black;font-weight:bold;' bgcolor='#0279ef'>Total Printed Quotations</td>
                                                <td style='border:1px solid black;' align='center'>".$PrintQuotations."</td>
                                            </tr>
                                        </table>
                                    </td>
                                 </tr>
                                </table>";
                echo $tableStatus;
            print "</div>";
            //======================
            //Latest Customers
            print "<div style='display:inline-block;margin-left: 43px;width: 718px'>";       
                $count=0;
                $TotalCount=0;

                if ($GLOBALS['system_user']->hasPermission('view_quotes')) {
                    for($i=0;$i<count($arrChannels);$i++){
                      $sql = "SELECT * FROM `customers` where active=1 ";
                      $sql .= "AND channel =".$arrChannels[$i];
                      $sqlres = mysqli_query($GLOBALS["link"],$sql);
                      $count = mysqli_num_rows($sqlres);
                      $TotalCount +=$count;
                    }
                }
                print "<h3 style='font-size:12px;font-weight:bold;color:#8dd03f;margin-top: 32px;margin-left: 3px;float:right;'>Total Customers:".$TotalCount."</h3>";
                print "<h2 style='font-size:16px;font-weight:bold;color:#8dd03f;margin-top: 32px;margin-left: 3px;'>Latest Customers</h2>";
                print "<br/>";
                if ($GLOBALS['system_user']->hasPermission('view_quotes')) {
                    $sql = "SELECT * FROM `customers` where active=1 AND (";
                    for($i=0;$i<count($arrChannels);$i++){
                        if($i==count($arrChannels)-1){
                            $sql .= "channel=".$arrChannels[$i].")"; 
                        }else{
                            $sql .= "channel=".$arrChannels[$i]." OR ";  
                        }   
                    }
                    $sql .= " ORDER BY `date_created` DESC LIMIT 7";
                    $sqlres = mysqli_query($GLOBALS["link"],$sql);
                    $row = mysqli_fetch_assoc($sqlres);
                    if($row['id'] == ""){
                        print "No Customers";
                    }else{
                         print '
                            <head>
                                <script type="text/javascript" language="javascript" class="init">
                                $(document).ready(function() {
                                    $("#latestcustomersall").dataTable( {
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
                                        "order": [[ 2, "desc" ]]
                                    } );
                                } );
                                </script>
                            </head>
                            <body>
                                <table id="latestcustomersall" class="display" cellspacing="3">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Surname</th>
                                            <th>Cellphone</th>
                                            <th>Email</th>
                                            <th>Company</th>                 
                                        </tr>
                                    </thead>
                                    <tbody>';
                                    $sql = "SELECT * FROM `customers` where active=1 AND (";
                                    for($i=0;$i<count($arrChannels);$i++){
                                        if($i==count($arrChannels)-1){
                                            $sql .= "channel=".$arrChannels[$i].")"; 
                                        }else{
                                            $sql .= "channel=".$arrChannels[$i]." OR ";  
                                        }   
                                    }
                                    $sql .= " ORDER BY `date_created` DESC LIMIT 7";
                                    $sqlres = mysqli_query($GLOBALS["link"],$sql);                    
                                    while($row = mysqli_fetch_assoc($sqlres)) {
                                        print "<tr>";
                                        print "<td align='center'>".$row['name']."</td>";
                                        print "<td align='center'>".$row['surname']."</td>";
                                        print "<td align='center'>".$row['cellphone']."</td>";
                                        print "<td align='center'>".$row['email']."</td>";
                                        print "<td align='center'>".$row['company']."</td>";
                                        print "</tr>";
                                    }
                            print '</tbody>
                                </table>
                            </body>';
                    }
                }else{
                    print "<p>You do not have permission to view Customers.</p>";
                }
            print "</div>";
            //======================
            //Latest Quotes       
            print "<div style='height: 100px;width: 775px;margin-left: 34px;display:inline-block;'>";
            print "<h2 style='font-size:16px;font-weight:bold;color:#8dd03f;margin-top: 32px;margin-left: 2px;'>Latest Quotes</h2>";
            print "<br/>";
            if ($GLOBALS['system_user']->hasPermission('view_quotes')) {
                $sql = "SELECT * FROM `quotes` where approved=0 and complete=1 AND (";
                for($i=0;$i<count($arrChannels);$i++){
                    if($i==count($arrChannels)-1){
                        $sql .= "channel=".$arrChannels[$i].")"; 
                    }else{
                        $sql .= "channel=".$arrChannels[$i]." OR ";  
                    }   
                }
                $sql .= " ORDER BY `date_created` DESC LIMIT 50";
                $sqlres = mysqli_query($GLOBALS["link"],$sql);
                $row = mysqli_fetch_assoc($sqlres);
                if($row['customer_id'] == ""){
                    print "No Pending quotes";
                }else{
                    print '
                        <head>
                            <script type="text/javascript" language="javascript" class="init">
                            $(document).ready(function() {
                                $("#lastquotes").dataTable( {
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
                                    "order": [[ 1, "desc" ]]
                                } );
                            } );
                            </script>
                        </head>
                        <body>
                            <table id="lastquotes" class="display" cellspacing="3">
                                <thead>
                                    <tr>
                                        <th width="70">Date</th>
                                        <th>Quote Ref</th>              
                                        <th>Location</th>
                                        <th>Customer</th>
                                        <th>Cellphone</th>
                                        <th>Created By</th>
                                        <th width="60">&nbsp;View</th>                
                                    </tr>
                                </thead>
                                <tbody>';
                                $sql = "SELECT `type` FROM `channels` WHERE `id`=".$selectedchannel;
                                $sqlres = mysqli_query($GLOBALS["link"],$sql);
                                $row = mysqli_fetch_assoc($sqlres);
                                $channeltype = $row['type'];

                                $sql = "SELECT * FROM `quotes` where approved=0 and complete=1 AND (";
                                for($i=0;$i<count($arrChannels);$i++){
                                    if($i==count($arrChannels)-1){
                                        $sql .= "channel=".$arrChannels[$i].")"; 
                                    }else{
                                        $sql .= "channel=".$arrChannels[$i]." OR ";  
                                    }   
                                }
                                //$sql.= $this->getCriteria($selectedchannel,0);
                                $sql .= " ORDER BY `date_created` DESC LIMIT 50";
                                $sqlres = mysqli_query($GLOBALS["link"],$sql);

                                while($row = mysqli_fetch_assoc($sqlres)) {
                                    $quote = new Quote($row["id"]);
                                    $customer = new Customer($quote->CustomerID);

                                    print "<tr>";
                                    if(strtotime($quote->LastUpdated) > strtotime($quote->DateCreated)){
                                      print "<td align='center'  bgcolor='#525656' style='font-weight:bold;'>";//modified
                                    }else{
                                      print "<td align='center'>"; 
                                    }
                                    
                                    print date("Y-m-d", strtotime($quote->DateCreated));
                                    print "</td>";
                                    print "<td align='center'>".$quote->QuoteReferenceNo."</td>";
                                    
                                    print "<td align='center'>";
                                    if ($row["lat"]!=0){
                                    print "<a href='http://maps.google.com?q=".$row["lat"].",".$row["lng"]."' target='_blank'><span class='ui-button-text'>View Location</span>";
                                    }
                                    print "</td>";
                                    print "<td align='center'>".$customer->Surname.", ".$customer->Name."</td>";
                                    print "<td align='center'>".$customer->CellPhone."</td>";
                                    print "<td align='center'>".$quote->CreatedByUserName."</td>";
                                    if($channeltype =="Retail"){
                                        print "<td align='center'><a href='quotes/editquote.php?id=".$row["id"]."&reurl=completed_quotations' target='_blank'><span class='ui-button-text'><img  width='20' height='20' title='View Details'  src='modules/dashboard/images/search.ico' /></span></a></td>";
                                    }else{
                                        print "<td align='center'><a href='quotes/editquotecomm.php?id=".$row["id"]."&reurl=completed_quotations' target='_blank'><span class='ui-button-text'><img  width='20' height='20' title='View Details'  src='modules/dashboard/images/search.ico' /></span></a></td>";
                                        
                                    }
                                    print "</tr>";
                                }
                        print '</tbody>
                            </table>
                        </body>';
                }
            }else{
                print "<p>You do not have permission to view latest quotes.</p>";
            }
            print "</div>";
            //======================
            //Building Types
            print "<div style='width: 717px;margin-left: 53px;display:inline-block;width: 667px'>";
                print "<br/>";
                print "<h2 style='font-size:16px;font-weight:bold;color:#8dd03f;margin-top: 32px;margin-left: 2px;'>Building Types</h2>";
                print "<br/>";
                if ($GLOBALS['system_user']->hasPermission('view_quotes')) {
                    $sql = "SELECT * FROM `property_types` where deleted=0 ";
                    $sqlres = mysqli_query($GLOBALS["link"],$sql);
                    $row = mysqli_fetch_assoc($sqlres);
                    if($row['id'] == ""){
                        print "No Property Types";
                    }else{
                         print '
                            <head>
                                <script type="text/javascript" language="javascript" class="init">
                                $(document).ready(function() {
                                    $("#buildingtypes").dataTable( {
                                        columnDefs: [ {
                                            targets: [ 0 ],
                                            orderData: [ 0, 0 ]
                                        } ],
                                        "order": [[ 0, "desc" ]]
                                    } );
                                } );
                                </script>
                            </head>
                            <body>
                                <table id="buildingtypes" class="display" cellspacing="3">
                                    <thead>
                                        <tr>
                                            <th>Name</th>                
                                        </tr>
                                    </thead>
                                    <tbody>';
                                    $sql = "SELECT * FROM `property_types` where deleted=0 ";
                                    $sqlres = mysqli_query($GLOBALS["link"],$sql);                   

                                    while($row = mysqli_fetch_assoc($sqlres)) {
                                        print "<tr>";
                                        print "<td >".$row['Type']."</td>";;
                                        print "</tr>";
                                    }
                            print '</tbody>
                                </table>
                            </body>';
                    }
                }else{
                    print "<p>You do not have permission to view Building Types.</p>";
                }
            print "</div>";
            //======================
           //Products by Category
            print "<div style='margin-left: 30px;display:inline-block'>";   
                print "<br/>";          
                print "<h2 style='font-size:16px;font-weight:bold;color:#8dd03f;margin-top: 200px;margin-left: 2px;'>Products by Category</h2>";
                print "<br/>";
                $arrProducts = array();
                $arrProductsNames = array();
                $arrProductNum = array();

                $sql = "SELECT DISTINCT `class_of_product` FROM `new_products`where (";
                for($i=0;$i<count($arrChannels);$i++){
                    if($i==count($arrChannels)-1){
                        $sql .= "channel=".$arrChannels[$i].")"; 
                    }else{
                        $sql .= "channel=".$arrChannels[$i]." OR ";  
                    }   
                }
                $sql .= " and deleted=0 ORDER BY `class_of_product`";
                $sqlres = mysqli_query($GLOBALS["link"],$sql);
                while($row = mysqli_fetch_assoc($sqlres)) {
                    $arrProducts[] = $row["class_of_product"];   
                    $arrProductsNames[] = ucwords(strtolower($row["class_of_product"]));             
                }

                $Total = 0;
                $data = '';
                for($i=0;$i<count($arrProducts);$i++){
                    $sql = "SELECT * FROM `new_products`where `class_of_product`='".strtoupper($arrProducts[$i])."' AND (";
                    for($r=0;$r<count($arrChannels);$r++){
                        if($r==count($arrChannels)-1){
                            $sql .= "channel=".$arrChannels[$r].")"; 
                        }else{
                            $sql .= "channel=".$arrChannels[$r]." OR ";  
                        }   
                    }
                    $sql .= " and deleted=0 ORDER BY `class_of_product`";
                    $sqlres = mysqli_query($GLOBALS["link"],$sql);
                    $rowcount=mysqli_num_rows($sqlres);
                    $arrProductNum[] = $rowcount;
                    $Total +=$rowcount;
                    //Chart
                    if($i == count($arrProductsNames)-1){
                        $data .= '{  
                                    "label":"'.$arrProductsNames[$i].'",
                                    "value":"'.$rowcount.'"
                                  }';
                    }else{
                        $data .= '{  
                                     "label":"'.$arrProductsNames[$i].'",
                                     "value":"'.$rowcount.'"
                                  },';
                    }          
                }   

                $doughnut3dChart = new FusionCharts("doughnut2d", "ProductsbyCategory" , 390, 400, "productsGraph", "json", '{  
                       "chart":{  
                          "use3DLighting": "0",
                          "enableSmartLabels": "0",
                          "startingAngle": "310",
                          "showLabels": "0",
                          "showPercentValues": "1",
                          "showLegend": "1",
                          "defaultCenterLabel": "Total Products: '.$Total.'",
                          "centerLabel": "Total $label: $value",
                          "centerLabelBold": "1",
                          "showTooltip": "1",
                          "decimals": "0",
                          "useDataPlotColorForLabels": "1",
                          "theme":"fint"
                       },
                       "data":['.$data.']
                }');
                // Render the chart
                $doughnut3dChart->render();


                if (!empty($array["Channel"]) ){
                    if($array["Channel"] !== $GLOBALS['system_user']->retailChannel){
                        $doughnut3dChart->dispose();
                        $doughnut3dChart = new FusionCharts("doughnut2d", "ProductsbyCategory" , 390, 400, "productsGraph", "json", '{  
                           "chart":{  
                              "use3DLighting": "0",
                              "enableSmartLabels": "0",
                              "startingAngle": "310",
                              "showLabels": "0",
                              "showPercentValues": "1",
                              "showLegend": "1",
                              "defaultCenterLabel": "Total Products: '.$Total.'",
                              "centerLabel": "Total $label: $value",
                              "centerLabelBold": "1",
                              "showTooltip": "1",
                              "decimals": "0",
                              "useDataPlotColorForLabels": "1",
                              "theme":"fint"
                           },
                           "data":['.$data.']
                        }');
                        // Render the chart
                        $doughnut3dChart->render();
                    }else{
                        $doughnut3dChart->dispose();
                        $doughnut3dChart = new FusionCharts("doughnut2d", "ProductsbyCategory" , 390, 400, "productsGraph", "json", '{  
                           "chart":{  
                              "use3DLighting": "0",
                              "enableSmartLabels": "0",
                              "startingAngle": "310",
                              "showLabels": "0",
                              "showPercentValues": "1",
                              "showLegend": "1",
                              "defaultCenterLabel": "Total Products: '.$Total.'",
                              "centerLabel": "Total $label: $value",
                              "centerLabelBold": "1",
                              "showTooltip": "1",
                              "decimals": "0",
                              "useDataPlotColorForLabels": "1",
                              "theme":"fint"
                           },
                           "data":['.$data.']
                        }');
                        // Render the chart
                        $doughnut3dChart->render();
                    }              
                }
                
                $tableProducts = "<table border='0' cellpadding='10' style='border: 1px solid #aaa7a7;margin-left: 4px;margin-bottom: 40px;'>
                                  <tr>
                                    <td ><div id='productsGraph'></div></td>  
                                    <td width='350'>
                                            <table style='border: 1px solid black;border-collapse: collapse;' width='100%' >";

                                            for($i=0;$i<count($arrProductsNames);$i++){
                                                $tableProducts .= "<tr>
                                                                        <td style='border:1px solid black;font-weight:bold;' bgcolor='#d1d1d1' width='40'>".$arrProductsNames[$i]."</td>
                                                                        <td style='border:1px solid black;' bgcolor='#d1d1d1' width='30' align='center'>".$arrProductNum[$i]."</td>
                                                         
                                                                    </tr>";         
                                            }               
                $tableProducts .="          </table>
                                    </td>
                                   </tr>
                                </table>";
                echo $tableProducts;
            print "</div>";
            //======================
            //Latest Products
            print "<div style='width: 680px;margin-left: 41px;display:inline-block;width: 667px'>";
                print "<br/>";
                print "<h2 style='font-size:16px;font-weight:bold;color:#8dd03f;margin-top: 32px;margin-left: 3px;'>Latest Products</h2>";
                print "<br/>";
                if ($GLOBALS['system_user']->hasPermission('view_quotes')) {
                    $sql = "SELECT * FROM `new_products` where (";
                    for($i=0;$i<count($arrChannels);$i++){
                        if($i==count($arrChannels)-1){
                            $sql .= "channel=".$arrChannels[$i].")"; 
                        }else{
                            $sql .= "channel=".$arrChannels[$i]." OR ";  
                        }   
                    }
                    $sql .= " and deleted=0";
                    $sql .= " ORDER BY `date_created` DESC LIMIT 11";
                    $sqlres = mysqli_query($GLOBALS["link"],$sql);
                    $row = mysqli_fetch_assoc($sqlres);
                    if($row['id'] == ""){
                        print "No Latest Products";
                    }else{
                         print '
                            <head>
                                <script type="text/javascript" language="javascript" class="init">
                                $(document).ready(function() {
                                    $("#latestproducts").dataTable( {
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
                                </script>
                            </head>
                            <body>
                                <table id="latestproducts" class="display" cellspacing="3">
                                    <thead>
                                        <tr>
                                            <th>Code</th>
                                            <th>Name</th>   
                                            <th>Class of Product</th>                
                                            <th>Supplier</th>                
                                        </tr>
                                    </thead>
                                    <tbody>';
                                    $sql = "SELECT * FROM `new_products` where (";
                                    for($i=0;$i<count($arrChannels);$i++){
                                        if($i==count($arrChannels)-1){
                                            $sql .= "channel=".$arrChannels[$i].")"; 
                                        }else{
                                            $sql .= "channel=".$arrChannels[$i]." OR ";  
                                        }   
                                    }
                                    $sql .= " and deleted=0";
                                    $sql .= " ORDER BY `date_created` DESC LIMIT 11";
                                    $sqlres = mysqli_query($GLOBALS["link"],$sql);
                                    $row = mysqli_fetch_assoc($sqlres);                  

                                    while($row = mysqli_fetch_assoc($sqlres)){
                                      print "<tr>";
                                      print "<td align='center'>".$row['code']."</td>";
                                      print "<td align='center'>".$row['product']."</td>";
                                      print "<td align='center'>".$row['class_of_product']."</td>";
                                      print "<td align='center'>".$row['supplier']."</td>";
                                      print "</tr>";
                                    }
                            print '</tbody>
                                </table>
                            </body>';
                    }
                }else{
                    print "<p>You do not have permission to view Latest Products.</p>";
                }
            print "</div>";
        }

        public function dashboardAllChannelspage () {
            require_once(dirname(__FILE__)."/../../fusioncharts/includes/fusioncharts.php");

            //Get all channels
            $sql = "SELECT `id` FROM `channels`";
            $sqlres = mysqli_query($GLOBALS["link"],$sql);
            $arrChannels = array();
            while ($row=mysqli_fetch_assoc($sqlres)){
                $arrChannels[] =$row['id'];
            }

            //Quotes by Status
            $PendingQuotations = 0;
            $ApprovedQuotations = 0;
            $SentQuotations = 0;
            $RejectQuotations = 0;
            $PrintQuotations = 0;

            for($i=0;$i<count($arrChannels);$i++){
                $this->getTotalStatus($arrChannels[$i],1);
                $PendingQuotations += $this->pendingQuotations;
                $ApprovedQuotations += $this->approvedQuotations;
                $SentQuotations += $this->sentQuotations;
                $RejectQuotations += $this->rejectedQuotations;
                $PrintQuotations += $this->printQuotations;
            }
            
            $Total = $PendingQuotations+$ApprovedQuotations+$SentQuotations+$RejectQuotations+$PrintQuotations;
            $doughnut2dChart = new FusionCharts("doughnut2d", "QuotesStatus" , 340, 300, "quoteStatusGraph", "json", '{  
                   "chart":{  
                      "use3DLighting": "0",
                      "enableSmartLabels": "0",
                      "startingAngle": "310",
                      "showLabels": "0",
                      "showPercentValues": "1",
                      "showLegend": "1",
                      "defaultCenterLabel": "Total Status: '.$Total.'",
                      "centerLabel": "Total $label: $value",
                      "centerLabelBold": "1",
                      "showTooltip": "1",
                      "decimals": "0",
                      "useDataPlotColorForLabels": "1",
                      "theme":"fint"
                   },
                   "data":[  
                      {  
                         "label":"Printed Quotations",
                         "value":"'.$PrintQuotations.'"
                      },
                      {  
                         "label":"Approved Quotations",
                         "value":"'.$ApprovedQuotations.'"
                      },
                      {  
                         "label":"Sent Quotations",
                         "value":"'.$SentQuotations.'"
                      },
                      {  
                         "label":"Pending Quotations",
                         "value":"'.$PendingQuotations.'"
                      },
                      {  
                         "label":"Rejected Quotations",
                         "value":"'.$RejectQuotations.'"
                      }
                   ]
                }');
            $doughnut2dChart->dispose();
            $doughnut2dChart = new FusionCharts("doughnut2d", "QuotesStatus" , 340, 300, "quoteStatusGraph", "json", '{  
                   "chart":{  
                      "use3DLighting": "0",
                      "enableSmartLabels": "0",
                      "startingAngle": "310",
                      "showLabels": "0",
                      "showPercentValues": "1",
                      "showLegend": "1",
                      "defaultCenterLabel": "Total Status: '.$Total.'",
                      "centerLabel": "Total $label: $value",
                      "centerLabelBold": "1",
                      "showTooltip": "1",
                      "decimals": "0",
                      "useDataPlotColorForLabels": "1",
                      "theme":"fint"
                   },
                   "data":[  
                      {  
                         "label":"Printed Quotations",
                         "value":"'.$PrintQuotations.'"
                      },
                      {  
                         "label":"Approved Quotations",
                         "value":"'.$ApprovedQuotations.'"
                      },
                      {  
                         "label":"Sent Quotations",
                         "value":"'.$SentQuotations.'"
                      },
                      {  
                         "label":"Pending Quotations",
                         "value":"'.$PendingQuotations.'"
                      },
                      {  
                         "label":"Rejected Quotations",
                         "value":"'.$RejectQuotations.'"
                      }
                   ]
                }');

            // Render the chart
            $doughnut2dChart->render();
            print "<div style='display:inline-block'>";
                print "<h2 style='font-size:16px;font-weight:bold;color:#8dd03f;margin-top: 32px;margin-left: 34px;'>Quotes by Status</h2>";
                print "<br/>";
                $tableStatus = "<table border='0' cellpadding='10' style='border: 1px solid #aaa7a7;margin-left: 34px;'>
                                 <tr>
                                    <td ><div id='quoteStatusGraph'></div></td>
                                    <td width='350'>
                                        <table style='border: 1px solid black;border-collapse: collapse;' width='100%' cellpadding='20'>
                                            <tr>
                                                <td style='border:1px solid black;font-weight:bold;' bgcolor='#e57f20'>Total Pending Quotations</td>
                                                <td style='border:1px solid black;' width='50' align='center'>".$PendingQuotations."</td>
                                            </tr>
                                            <tr>
                                                <td style='border:1px solid black;font-weight:bold;' bgcolor='#ffe900'>Total Sent Quotations</td>
                                                <td style='border:1px solid black;' align='center'>".$SentQuotations."</td>
                                            </tr>
                                            <tr>
                                                <td style='border:1px solid black;font-weight:bold;' bgcolor='#ff0000'>Total Rejected Quotations</td>
                                                <td style='border:1px solid black;' align='center'>".$RejectQuotations."</td>
                                            </tr>
                                            <tr>
                                                <td style='border:1px solid black;font-weight:bold;' bgcolor='#0faa17'>Total Approved Quotations</td>
                                                <td style='border:1px solid black;' align='center'>".$ApprovedQuotations."</td>
                                            </tr>
                                            <tr>
                                                <td style='border:1px solid black;font-weight:bold;' bgcolor='#0279ef'>Total Printed Quotations</td>
                                                <td style='border:1px solid black;' align='center'>".$PrintQuotations."</td>
                                            </tr>
                                        </table>
                                    </td>
                                 </tr>
                                </table>";
                echo $tableStatus;
            print "</div>";
            //======================
            //Latest Customers
            print "<div style='display:inline-block;margin-left: 60px;width: 718px'>";       
                $count=0;
                $TotalCount=0;

                if ($GLOBALS['system_user']->hasPermission('view_quotes')) {
                    for($i=0;$i<count($arrChannels);$i++){
                      $sql = "SELECT * FROM `customers` where active=1 ";
                      $sql .= "AND channel =".$arrChannels[$i];
                      $sqlres = mysqli_query($GLOBALS["link"],$sql);
                      $count = mysqli_num_rows($sqlres);
                      $TotalCount +=$count;
                    }
                }
                print "<h3 style='font-size:12px;font-weight:bold;color:#8dd03f;margin-top: 32px;margin-left: 3px;float:right;'>Total Customers:".$TotalCount."</h3>";
                print "<h2 style='font-size:16px;font-weight:bold;color:#8dd03f;margin-top: 32px;margin-left: 3px;'>Latest Customers</h2>";
                print "<br/>";
                if ($GLOBALS['system_user']->hasPermission('view_quotes')) {
                    $sql = "SELECT * FROM `customers` where active=1 ";
                    $sql .= " ORDER BY `date_created` DESC LIMIT 7";
                    $sqlres = mysqli_query($GLOBALS["link"],$sql);
                    $row = mysqli_fetch_assoc($sqlres);
                    if($row['id'] == ""){
                        print "No Customers";
                    }else{
                         print '
                            <head>
                                <script type="text/javascript" language="javascript" class="init">
                                $(document).ready(function() {
                                    $("#latestcustomersall").dataTable( {
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
                                        "order": [[ 2, "desc" ]]
                                    } );
                                } );
                                </script>
                            </head>
                            <body>
                                <table id="latestcustomersall" class="display" cellspacing="3">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Surname</th>
                                            <th>Cellphone</th>
                                            <th>Email</th>
                                            <th>Company</th>                 
                                        </tr>
                                    </thead>
                                    <tbody>';
                                    $sql = "SELECT * FROM `customers` where active=1 ";
                                    $sql .= " ORDER BY `date_created` DESC LIMIT 7";
                                    $sqlres = mysqli_query($GLOBALS["link"],$sql);                    

                                    while($row = mysqli_fetch_assoc($sqlres)) {
                                        print "<tr>";
                                        print "<td align='center'>".$row['name']."</td>";
                                        print "<td align='center'>".$row['surname']."</td>";
                                        print "<td align='center'>".$row['cellphone']."</td>";
                                        print "<td align='center'>".$row['email']."</td>";
                                        print "<td align='center'>".$row['company']."</td>";
                                        print "</tr>";
                                    }
                            print '</tbody>
                                </table>
                            </body>';
                    }
                }else{
                    print "<p>You do not have permission to view Customers.</p>";
                }
            print "</div>";
            //======================
            //Latest Quotes       
            print "<div style='height: 100px;width: 775px;margin-left: 34px;display:inline-block'>";
            print "<h2 style='font-size:16px;font-weight:bold;color:#8dd03f;margin-top: 32px;margin-left: 2px;'>Latest Quotes</h2>";
            print "<br/>";
            if ($GLOBALS['system_user']->hasPermission('view_quotes')) {
                $sql = "SELECT * FROM `quotes` where approved=0 and complete=1 AND (";
                for($i=0;$i<count($arrChannels);$i++){
                    if($i==count($arrChannels)-1){
                        $sql .= "channel=".$arrChannels[$i].")"; 
                    }else{
                        $sql .= "channel=".$arrChannels[$i]." OR ";  
                    }   
                }
                //$sql.= $this->getCriteria($selectedchannel,0);
                $sql .= " ORDER BY `date_created` DESC LIMIT 50";
                $sqlres = mysqli_query($GLOBALS["link"],$sql);
                $row = mysqli_fetch_assoc($sqlres);
                if($row['customer_id'] == ""){
                    print "No Pending quotes";
                }else{
                    print '
                        <head>
                            <script type="text/javascript" language="javascript" class="init">
                            $(document).ready(function() {
                                $("#lastquotes").dataTable( {
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
                                    "order": [[ 1, "desc" ]]
                                } );
                            } );
                            </script>
                        </head>
                        <body>
                            <table id="lastquotes" class="display" cellspacing="3">
                                <thead>
                                    <tr>
                                        <th width="70">Date</th>
                                        <th>Quote Ref</th>              
                                        <th>Location</th>
                                        <th>Customer</th>
                                        <th>Cellphone</th>
                                        <th>Created By</th>
                                        <th width="60">&nbsp;View</th>                
                                    </tr>
                                </thead>
                                <tbody>';
                                $sql = "SELECT `type` FROM `channels` WHERE `id`=".$selectedchannel;
                                $sqlres = mysqli_query($GLOBALS["link"],$sql);
                                $row = mysqli_fetch_assoc($sqlres);
                                $channeltype = $row['type'];

                                $sql = "SELECT * FROM `quotes` where approved=0 and complete=1 AND (";
                                for($i=0;$i<count($arrChannels);$i++){
                                    if($i==count($arrChannels)-1){
                                        $sql .= "channel=".$arrChannels[$i].")"; 
                                    }else{
                                        $sql .= "channel=".$arrChannels[$i]." OR ";  
                                    }   
                                }
                                //$sql.= $this->getCriteria($selectedchannel,0);
                                $sql .= " ORDER BY `date_created` DESC LIMIT 50";
                                $sqlres = mysqli_query($GLOBALS["link"],$sql);

                                while($row = mysqli_fetch_assoc($sqlres)) {
                                    $quote = new Quote($row["id"]);
                                    $customer = new Customer($quote->CustomerID);

                                    print "<tr>";
                                    if(strtotime($quote->LastUpdated) > strtotime($quote->DateCreated)){
                                      print "<td align='center'  bgcolor='#525656' style='font-weight:bold;'>";//modified
                                    }else{
                                      print "<td align='center'>"; 
                                    }
                                    
                                    print date("Y-m-d", strtotime($quote->DateCreated));
                                    print "</td>";
                                    print "<td align='center'>".$quote->QuoteReferenceNo."</td>";
                                    
                                    print "<td align='center'>";
                                    if ($row["lat"]!=0){
                                    print "<a href='http://maps.google.com?q=".$row["lat"].",".$row["lng"]."' target='_blank'><span class='ui-button-text'>View Location</span>";
                                    }
                                    print "</td>";
                                    print "<td align='center'>".$customer->Surname.", ".$customer->Name."</td>";
                                    print "<td align='center'>".$customer->CellPhone."</td>";
                                    print "<td align='center'>".$quote->CreatedByUserName."</td>";
                                    if($channeltype =="Retail"){
                                        print "<td align='center'><a href='quotes/editquote.php?id=".$row["id"]."&reurl=completed_quotations' target='_blank'><span class='ui-button-text'><img  width='20' height='20' title='View Details'  src='modules/dashboard/images/search.ico' /></span></a></td>";
                                    }else{
                                        print "<td align='center'><a href='quotes/editquotecomm.php?id=".$row["id"]."&reurl=completed_quotations' target='_blank'><span class='ui-button-text'><img  width='20' height='20' title='View Details'  src='modules/dashboard/images/search.ico' /></span></a></td>";
                                        
                                    }
                                    print "</tr>";
                                }
                        print '</tbody>
                            </table>
                        </body>';
                }
            }else{
                print "<p>You do not have permission to view latest quotes.</p>";
            }
            print "</div>";
            //======================
            //Building Types
            print "<div style='width: 717px;margin-left: 28px;display:inline-block;width: 667px'>";
                print "<br/>";
                print "<h2 style='font-size:16px;font-weight:bold;color:#8dd03f;margin-top: 32px;margin-left: 2px;'>Building Types</h2>";
                print "<br/>";
                if ($GLOBALS['system_user']->hasPermission('view_quotes')) {
                    $sql = "SELECT * FROM `property_types` where deleted=0 ";
                    $sqlres = mysqli_query($GLOBALS["link"],$sql);
                    $row = mysqli_fetch_assoc($sqlres);
                    if($row['id'] == ""){
                        print "No Property Types";
                    }else{
                         print '
                            <head>
                                <script type="text/javascript" language="javascript" class="init">
                                $(document).ready(function() {
                                    $("#buildingtypesall").dataTable( {
                                        columnDefs: [ {
                                            targets: [ 0 ],
                                            orderData: [ 0, 0 ]
                                        } ],
                                        "order": [[ 0, "desc" ]]
                                    } );
                                } );
                                </script>
                            </head>
                            <body>
                                <table id="buildingtypesall" class="display" cellspacing="3">
                                    <thead>
                                        <tr>
                                            <th>Name</th>                
                                        </tr>
                                    </thead>
                                    <tbody>';
                                    $sql = "SELECT * FROM `property_types` where deleted=0 ";
                                    $sqlres = mysqli_query($GLOBALS["link"],$sql);                   

                                    while($row = mysqli_fetch_assoc($sqlres)) {
                                        print "<tr>";
                                        print "<td >".$row['Type']."</td>";;
                                        print "</tr>";
                                    }
                            print '</tbody>
                                </table>
                            </body>';
                    }
                }else{
                    print "<p>You do not have permission to view Building Types.</p>";
                }
            print "</div>";
            //======================
            //Products by Category
            print "<div style='margin-left: 30px;display:inline-block'>";   
                print "<br/>";          
                print "<h2 style='font-size:16px;font-weight:bold;color:#8dd03f;margin-top: 200px;margin-left: 2px;'>Products by Category</h2>";
                print "<br/>";
                $arrProducts = array();
                $arrProductNum = array();
                $arrProductsNames = array();

                $sql = "SELECT DISTINCT `class_of_product` FROM `new_products`where (";
                for($i=0;$i<count($arrChannels);$i++){
                    if($i==count($arrChannels)-1){
                        $sql .= "channel=".$arrChannels[$i].")"; 
                    }else{
                        $sql .= "channel=".$arrChannels[$i]." OR ";  
                    }   
                }
                $sql .= " and deleted=0 ORDER BY `class_of_product`";
                $sqlres = mysqli_query($GLOBALS["link"],$sql);
                while($row = mysqli_fetch_assoc($sqlres)) {
                    $arrProducts[] = $row["class_of_product"];   
                    $arrProductsNames[] = ucwords(strtolower($row["class_of_product"]));               
                }

                $Total = 0;
                $data = '';
                for($i=0;$i<count($arrProducts);$i++){
                    $sql = "SELECT * FROM `new_products`where `class_of_product`='".strtoupper($arrProducts[$i])."' AND (";
                    for($r=0;$r<count($arrChannels);$r++){
                        if($r==count($arrChannels)-1){
                            $sql .= "channel=".$arrChannels[$r].")"; 
                        }else{
                            $sql .= "channel=".$arrChannels[$r]." OR ";  
                        }   
                    }
                    $sql .= " and deleted=0 ORDER BY `class_of_product`";
                    $sqlres = mysqli_query($GLOBALS["link"],$sql);
                    $rowcount=mysqli_num_rows($sqlres);
                    $arrProductNum[] = $rowcount;
                    $Total +=$rowcount;
                    //Chart
                    if($i == count($arrProducts)-1){
                        $data .= '{  
                                    "label":"'.$arrProductsNames[$i].'",
                                    "value":"'.$rowcount.'"
                                  }';
                    }else{
                        $data .= '{  
                                     "label":"'.$arrProductsNames[$i].'",
                                     "value":"'.$rowcount.'"
                                  },';
                    }          
                }   

                $doughnut3dChart = new FusionCharts("doughnut2d", "ProductsbyCategory" , 390, 400, "productsGraph", "json", '{  
                       "chart":{  
                          "use3DLighting": "0",
                          "enableSmartLabels": "0",
                          "startingAngle": "310",
                          "showLabels": "0",
                          "showPercentValues": "1",
                          "showLegend": "1",
                          "defaultCenterLabel": "Total Products: '.$Total.'",
                          "centerLabel": "Total $label: $value",
                          "centerLabelBold": "1",
                          "showTooltip": "1",
                          "decimals": "0",
                          "useDataPlotColorForLabels": "1",
                          "theme":"fint"
                       },
                       "data":['.$data.']
                }');
                $doughnut3dChart->dispose();
                $doughnut3dChart = new FusionCharts("doughnut2d", "ProductsbyCategory" , 390, 400, "productsGraph", "json", '{  
                       "chart":{  
                          "use3DLighting": "0",
                          "enableSmartLabels": "0",
                          "startingAngle": "310",
                          "showLabels": "0",
                          "showPercentValues": "1",
                          "showLegend": "1",
                          "defaultCenterLabel": "Total Products: '.$Total.'",
                          "centerLabel": "Total $label: $value",
                          "centerLabelBold": "1",
                          "showTooltip": "1",
                          "decimals": "0",
                          "useDataPlotColorForLabels": "1",
                          "theme":"fint"
                       },
                       "data":['.$data.']
                }');


                // Render the chart
                $doughnut3dChart->render();
                
                $tableProducts = "<table border='0' cellpadding='10' style='border: 1px solid #aaa7a7;margin-left: 4px;margin-bottom: 40px;'>
                                          <tr>
                                            <td ><div id='productsGraph'></div></td>  
                                            <td width='350'>
                                                    <table style='border: 1px solid black;border-collapse: collapse;' width='100%' >";

                                                    for($i=0;$i<count($arrProducts);$i++){
                                                        $tableProducts .= "<tr>
                                                                                <td style='border:1px solid black;font-weight:bold;' bgcolor='#d1d1d1' width='40'>".$arrProductsNames[$i]."</td>
                                                                                <td style='border:1px solid black;' bgcolor='#d1d1d1' width='30' align='center'>".$arrProductNum[$i]."</td>
                                                                 
                                                                            </tr>";         
                                                    }               
                        $tableProducts .="          </table>
                                            </td>
                                           </tr>
                                        </table>";
                echo $tableProducts;
            print "</div>";
            //======================
            //Latest Products
            print "<div style='width: 680px;margin-left: 41px;display:inline-block;width: 667px'>";
                print "<br/>";
                print "<h2 style='font-size:16px;font-weight:bold;color:#8dd03f;margin-top: 32px;margin-left: 3px;'>Latest Products</h2>";
                print "<br/>";
                if ($GLOBALS['system_user']->hasPermission('view_quotes')) {
                    $sql = "SELECT * FROM `new_products` where (";
                    for($i=0;$i<count($arrChannels);$i++){
                        if($i==count($arrChannels)-1){
                            $sql .= "channel=".$arrChannels[$i].")"; 
                        }else{
                            $sql .= "channel=".$arrChannels[$i]." OR ";  
                        }   
                    }
                    $sql .= " and deleted=0";
                    $sql .= " ORDER BY `date_created` DESC LIMIT 11";
                    $sqlres = mysqli_query($GLOBALS["link"],$sql);
                    $row = mysqli_fetch_assoc($sqlres);
                    if($row['id'] == ""){
                        print "No Latest Products";
                    }else{
                         print '
                            <head>
                                <script type="text/javascript" language="javascript" class="init">
                                $(document).ready(function() {
                                    $("#latestproductsall").dataTable( {
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
                                </script>
                            </head>
                            <body>
                                <table id="latestproductsall" class="display" cellspacing="3">
                                    <thead>
                                        <tr>
                                            <th>Code</th>
                                            <th>Name</th>   
                                            <th>Class of Product</th>                
                                            <th>Supplier</th>                
                                        </tr>
                                    </thead>
                                    <tbody>';
                                    $sql = "SELECT * FROM `new_products` where (";
                                    for($i=0;$i<count($arrChannels);$i++){
                                        if($i==count($arrChannels)-1){
                                            $sql .= "channel=".$arrChannels[$i].")"; 
                                        }else{
                                            $sql .= "channel=".$arrChannels[$i]." OR ";  
                                        }   
                                    }
                                    $sql .= " and deleted=0";
                                    $sql .= " ORDER BY `date_created` DESC LIMIT 11";
                                    $sqlres = mysqli_query($GLOBALS["link"],$sql);
                                    $row = mysqli_fetch_assoc($sqlres);                  
                                    while($row = mysqli_fetch_assoc($sqlres)){
                                      print "<tr>";
                                      print "<td align='center'>".$row['code']."</td>";
                                      print "<td align='center'>".$row['product']."</td>";
                                      print "<td align='center'>".$row['class_of_product']."</td>";
                                      print "<td align='center'>".$row['supplier']."</td>";
                                      print "</tr>";
                                    }
                            print '</tbody>
                                </table>
                            </body>';
                    }
                }else{
                    print "<p>You do not have permission to view Latest Products.</p>";
                }
            print "</div>"; 
        }

        public function dashboardAllBranchespage(){

            $arrCharts = array();
            $query  = "SELECT `title`, `show_chart`, `channel_type`";
            $query .= " FROM `dashboardchart_settings`";
            $query .= " WHERE `deleted` = '0' AND `show_chart`='Yes' AND `channel_type`='".$this->getChannelType($GLOBALS['system_user']->retailChannel)."'";
            $result = mysqli_query($GLOBALS["link"],$query);
            while ($arr = mysqli_fetch_assoc($result)) {
                 $arrCharts[] =$arr['title'];
            }
            //Average Customers
            $show = false;
            if(in_array("Average Customers",  $arrCharts)){
                $show = true;
            }
            if($show){
                print "<div style='margin-left: 35px;width: inherit;'>";       
                    $count=0;
                    if ($GLOBALS['system_user']->hasPermission('view_quotes')) {
                        $sql = "SELECT * FROM `channels` WHERE `type`='Franchises'";
                        $sqlres = mysqli_query($GLOBALS["link"],$sql);
                        $arrChannels= array();
                        while ($row=mysqli_fetch_assoc($sqlres)){
                             $arrChannels[] = $row['id'];     
                        }
                        $channels = 'AND ';
                        $num = count($arrChannels);
                        for($i=0;$i<$num;$i++){      
                            if($i == ($num-1)){
                              $channels .= "`channel`='".$arrChannels[0]."'";  
                            }else{   
                                $channels .= "`channel`='".$arrChannels[$i]."' OR ";
                            }
                        }
                      $sql = "SELECT * FROM `customers` where active=1 ";
                      $sql .= $channels;
                      $sql .= " ORDER BY `date_created` DESC LIMIT 50";

                      $sqlres = mysqli_query($GLOBALS["link"],$sql);
                      $count = mysqli_num_rows($sqlres);
                    }
                    print "<h3 style='font-size:12px;font-weight:bold;color:#8dd03f;margin-top: 13px;margin-left: 3px;float:right;'>Total Customers:".$count."</h3>";
                    print "<h2 style='font-size:16px;font-weight:bold;color:#8dd03f;margin-top: 32px;margin-left: 3px;'>Average customers per installer</h2>";
                    print "<br/>";
                     if ($GLOBALS['system_user']->hasPermission('view_quotes')) {
                      $sql = "SELECT * FROM `customers` where active=1 ";
                    $sql .= $channels;
                      $sql .= " ORDER BY `date_created` DESC LIMIT 50";
                      $sqlres = mysqli_query($GLOBALS["link"],$sql);
                      $row = mysqli_fetch_assoc($sqlres);
                      if($row['id'] == ""){
                          print "No Customers";
                      }else{
                        //Installers
                        $arrInstallers = array();
                        $arrInstallersID = array();
                        $categories = '';
                        $customers = '';
                        $average  = '';

                        $sql = "SELECT * FROM `channels` WHERE `type`='Franchises'";
                        $sqlres = mysqli_query($GLOBALS["link"],$sql);
                        while ($row=mysqli_fetch_assoc($sqlres)){
                          $arrInstallers[] = $row["name"];
                          $arrInstallersID[] = $row["id"];
                        }

                        //Categories
                        for($i=0;$i<count($arrInstallers);$i++){
                          $sql = "SELECT * FROM `customers`where channel=".$arrInstallersID[$i]." ORDER BY `id`";
                          $sqlres = mysqli_query($GLOBALS["link"],$sql);
                          $rowcount=mysqli_num_rows($sqlres);
                          $arrInstallerNum[] = $rowcount;
                          if($i == count($arrInstallers)-1){
                              $categories .= '{  
                                                "label":"'.$arrInstallers[$i].'"
                                              }';
                          }else{
                              $categories .= '{  
                                                 "label":"'.$arrInstallers[$i].'"
                                              },';
                          } 
                        }

                        //Customers
                        for($i=0;$i<count($arrInstallerNum);$i++){
                          if($i == count($arrInstallerNum)-1){
                              $customers .= '{  
                                                "value":"'.$arrInstallerNum[$i].'"
                                              }';
                          }else{
                              $customers .= '{  
                                                 "value":"'.$arrInstallerNum[$i].'"
                                              },';
                          }
                        }

                        //Averages
                        $numInstallers  = count($arrInstallers);
                        for($i=0;$i<count($arrInstallerNum);$i++){
                          $value = (int)$arrInstallerNum[$i]/$numInstallers;
                          if($i == count($arrInstallerNum)-1){
                              $average .= '{  
                                                "value":"'.$value .'"
                                              }';
                          }else{
                              $average .= '{  
                                                 "value":"'.$$value.'"
                                              },';
                          }
                        }

                        //Combination 2D Chart
                        $combinationCustomers2DChart = new FusionCharts("mscombi2d", "Combination2DChart" , 700, 400, "allcustomers2D", "json", '{  
                           "chart":{  
                                "caption": "Average customers per installer",
                                "xAxisname": "Installers",
                                "yAxisName": "Amount",
                                "theme": "fint"
                            },
                            "categories": [
                                {
                                    "category": ['.$categories.']
                                }
                            ],
                            "dataset": [
                                {
                                    "seriesName": "Actual Customers",
                                    "data": ['.$customers.']
                                },
                                {
                                    "seriesName": "Customer Average",
                                    "renderAs": "line",
                                    "showValues": "0",
                                    "data": ['.$average.']
                                }
                            ]
                        }');
                        // Render the chart
                        $combinationCustomers2DChart->render();


                        $tableCustomers = "<table border='1' cellpadding='10' style='border: 2px solid black;margin-left: 4px;'>
                                      <tr>
                                        <td ><div id='allcustomers2D'></div></td>  
                                        <td width='350'>
                                                <table style='border: 1px solid black;border-collapse: collapse;' width='100%' >";

                                                for($i=0;$i<count($arrInstallers);$i++){
                                                    $tableCustomers .= "<tr>
                                                                            <td style='border:1px solid black;font-weight:bold;' bgcolor='#828989' width='40'>".$arrInstallers[$i]."</td>
                                                                            <td style='border:1px solid black;' width='30' align='center'>".$arrInstallerNum[$i]."</td>
                                                             
                                                                        </tr>";         
                                                }               
                        $tableCustomers .="          </table>
                                            </td>
                                           </tr>
                                        </table>";
                        echo $tableCustomers;     
                      }
                    }
                print "</div>";
            }

            //Average Quotes
            $show = false;
            if(in_array("Average Quotes",  $arrCharts)){
                $show = true;
            }
            if($show){    
                print "<div style='margin-left: 60px;width: 621px'>"; 
                    print "<h2 style='font-size:16px;font-weight:bold;color:#8dd03f;margin-top: 32px;margin-left: 3px;'>Average quote total amount per installer</h2>";
                    print "<br/>";
                    //Installers
                    $arrInstallers = array();
                    $arrInstallersID = array();
                    $categories = '';
                 

                    $Approved = array();
                    $Rejected = array();
                    $Pending = array();
                    $Sent = array();
                    $Print = array();

                    $TotalApproved = 0;
                    $TotalRejected = 0;
                    $TotalPending = 0;
                    $TotalSent = 0;
                    $TotalPrint = 0;

                    $sql = "SELECT * FROM `channels` WHERE `type`='Franchises'";
                    $sqlres = mysqli_query($GLOBALS["link"],$sql);
                    while ($row=mysqli_fetch_assoc($sqlres)){
                      $arrInstallers[] = $row["name"];
                      $arrInstallersID[] = $row["id"];
                    }

                    //Categories
                    for($i=0;$i<count($arrInstallers);$i++){
                      
                      if($i == count($arrInstallers)-1){
                          $categories .= '{  
                                            "label":"'.$arrInstallers[$i].'"
                                          }';
                      }else{
                          $categories .= '{  
                                             "label":"'.$arrInstallers[$i].'"
                                          },';
                      } 

                      //Quotes by Status
                      $this->getTotalStatus($arrInstallersID[$i],0);
                      $TotalApproved += $this->approvedQuotations;
                      $TotalRejected += $this->rejectedQuotations;
                      $TotalPending += $this->pendingQuotations;
                      $TotalSent += $this->sentQuotations;
                      $TotalPrint += $this->printQuotations;

                      $Approved[] = $this->approvedQuotations;
                      $Rejected[] = $this->rejectedQuotations;
                      $Pending[] = $this->pendingQuotations;
                      $Sent[] = $this->sentQuotations;
                      $Print[] = $this->printQuotations;
                    }
                    //============================================
                    $quotes = '';
                    $average  = '';
                    //Quotes
                    for($i=0;$i<count($arrInstallers);$i++){
                      if($i == count($arrInstallers)-1){
                          $quotes .= '{  
                                            "value":"'.$Approved[$i].'"
                                          }';
                      }else{
                          $quotes .= '{  
                                             "value":"'.$Approved[$i].'"
                                          },';
                      }
                    }

                    //Averages Approved
                    for($i=0;$i<count($arrInstallers);$i++){
                      $value = (int)$Approved[$i]/$TotalApproved;
                      if($i == count($arrInstallers)-1){
                          $average .= '{  
                                            "value":"'.$value .'"
                                          }';
                      }else{
                          $average .= '{  
                                             "value":"'.$$value.'"
                                          },';
                      }
                    }

                    //Combination 2D Chart
                    $Approved2DChart = new FusionCharts("mscombi2d", "ApprovedQuotes2DChart" ,700, 500, "ApprovedQuotes2D", "json", '{  
                       "chart":{  
                            "caption": "Average Approved Quotes per installer",
                            "xAxisname": "Installers",
                            "yAxisName": "Amount",
                            "theme": "fint"
                        },
                        "categories": [
                            {
                                "category": ['.$categories.']
                            }
                        ],
                        "dataset": [
                            {
                                "seriesName": "Actual Quotes",
                                "data": ['.$quotes.']
                            },
                            {
                                "seriesName": "Quote Average",
                                "renderAs": "line",
                                "showValues": "0",
                                "data": ['.$average.']
                            }
                        ]
                    }');
                    // Render the chart
                    $Approved2DChart->render();

                    //============================================
                    $quotes = '';
                    $average  = '';

                    //Quotes
                    for($i=0;$i<count($arrInstallers);$i++){
                      if($i == count($arrInstallers)-1){
                          $quotes .= '{  
                                            "value":"'.$Rejected[$i].'"
                                          }';
                      }else{
                          $quotes .= '{  
                                             "value":"'.$Rejected[$i].'"
                                          },';
                      }
                    }

                    //Averages Rejected
                    for($i=0;$i<count($arrInstallers);$i++){
                      $value = (int)$Rejected[$i]/$TotalRejected;
                      if($i == count($arrInstallers)-1){
                          $average .= '{  
                                            "value":"'.$value .'"
                                          }';
                      }else{
                          $average .= '{  
                                             "value":"'.$$value.'"
                                          },';
                      }
                    }

                     //Combination 2D Chart
                    $Rejected2DChart = new FusionCharts("mscombi2d", "RejectedQuotes2DChart" , 700, 500, "RejectedQuotes2D", "json", '{  
                       "chart":{  
                            "caption": "Average Rejected Quotes per installer",
                            "xAxisname": "Installers",
                            "yAxisName": "Amount",
                            "theme": "fint"
                        },
                        "categories": [
                            {
                                "category": ['.$categories.']
                            }
                        ],
                        "dataset": [
                            {
                                "seriesName": "Actual Quotes",
                                "data": ['.$quotes.']
                            },
                            {
                                "seriesName": "Quote Average",
                                "renderAs": "line",
                                "showValues": "0",
                                "data": ['.$average.']
                            }
                        ]
                    }');
                    // Render the chart
                    $Rejected2DChart->render();
                    //============================================
                    $quotes = '';
                    $average  = '';

                    //Quotes
                    for($i=0;$i<count($arrInstallers);$i++){
                      if($i == count($arrInstallers)-1){
                          $quotes .= '{  
                                            "value":"'.$Pending[$i].'"
                                          }';
                      }else{
                          $quotes .= '{  
                                             "value":"'.$Pending[$i].'"
                                          },';
                      }
                    }

                    //Averages Pending
                    for($i=0;$i<count($arrInstallers);$i++){
                      $value = (int)$Pending[$i]/$TotalPending;
                      if($i == count($arrInstallers)-1){
                          $average .= '{  
                                            "value":"'.$value .'"
                                          }';
                      }else{
                          $average .= '{  
                                             "value":"'.$$value.'"
                                          },';
                      }
                    }

                    //Combination 2D Chart
                    $Pending2DChart = new FusionCharts("mscombi2d", "PendingQuotes2DChart" , 700, 500, "PendingQuotes2D", "json", '{  
                       "chart":{  
                            "caption": "Average Pending Quotes per installer",
                            "xAxisname": "Installers",
                            "yAxisName": "Amount",
                            "theme": "fint"
                        },
                        "categories": [
                            {
                                "category": ['.$categories.']
                            }
                        ],
                        "dataset": [
                            {
                                "seriesName": "Actual Quotes",
                                "data": ['.$quotes.']
                            },
                            {
                                "seriesName": "Quote Average",
                                "renderAs": "line",
                                "showValues": "0",
                                "data": ['.$average.']
                            }
                        ]
                    }');
                    // Render the chart
                    $Pending2DChart->render();
                    //============================================
                    $quotes = '';
                    $average  = '';

                    //Quotes
                    for($i=0;$i<count($arrInstallers);$i++){
                      if($i == count($arrInstallers)-1){
                          $quotes .= '{  
                                            "value":"'.$Sent[$i].'"
                                          }';
                      }else{
                          $quotes .= '{  
                                             "value":"'.$Sent[$i].'"
                                          },';
                      }
                    }

                    //Averages Sent
                    for($i=0;$i<count($arrInstallers);$i++){
                      $value = (int)$Sent[$i]/$TotalSent;
                      if($i == count($arrInstallers)-1){
                          $average .= '{  
                                            "value":"'.$value .'"
                                          }';
                      }else{
                          $average .= '{  
                                             "value":"'.$$value.'"
                                          },';
                      }
                    }

                    //Combination 2D Chart
                    $Sent2DChart = new FusionCharts("mscombi2d", "SentQuotes2DChart" , 700, 500, "SentQuotes2D", "json", '{  
                       "chart":{  
                            "caption": "Average Sent Quotes per installer",
                            "xAxisname": "Installers",
                            "yAxisName": "Amount",
                            "theme": "fint"
                        },
                        "categories": [
                            {
                                "category": ['.$categories.']
                            }
                        ],
                        "dataset": [
                            {
                                "seriesName": "Actual Quotes",
                                "data": ['.$quotes.']
                            },
                            {
                                "seriesName": "Quote Average",
                                "renderAs": "line",
                                "showValues": "0",
                                "data": ['.$average.']
                            }
                        ]
                    }');
                    // Render the chart
                    $Sent2DChart->render();
                    //============================================
                    $quotes = '';
                    $average  = '';

                    //Quotes
                    for($i=0;$i<count($arrInstallers);$i++){
                      if($i == count($arrInstallers)-1){
                          $quotes .= '{  
                                            "value":"'.$Print[$i].'"
                                          }';
                      }else{
                          $quotes .= '{  
                                             "value":"'.$Print[$i].'"
                                          },';
                      }
                    }

                    //Averages Print
                    for($i=0;$i<count($arrInstallers);$i++){
                      $value = (int)$Print[$i]/$TotalPrint;
                      if($i == count($arrInstallers)-1){
                          $average .= '{  
                                            "value":"'.$value .'"
                                          }';
                      }else{
                          $average .= '{  
                                             "value":"'.$$value.'"
                                          },';
                      }
                    }

                    //Combination 2D Chart
                    $Print2DChart = new FusionCharts("mscombi2d", "PrintQuotes2DChart" , 700, 500, "PrintQuotes2D", "json", '{  
                       "chart":{  
                            "caption": "Average Print Quotes per installer",
                            "xAxisname": "Installers",
                            "yAxisName": "Amount",
                            "theme": "fint"
                        },
                        "categories": [
                            {
                                "category": ['.$categories.']
                            }
                        ],
                        "dataset": [
                            {
                                "seriesName": "Actual Quotes",
                                "data": ['.$quotes.']
                            },
                            {
                                "seriesName": "Quote Average",
                                "renderAs": "line",
                                "showValues": "0",
                                "data": ['.$average.']
                            }
                        ]
                    }');
                    // Render the chart
                    $Print2DChart->render();

                    $tableQuotations= "<table border='1' cellpadding='10' style='border: 2px solid black;margin-left: 4px;'>
                                        <tr>
                                          <td ><div id='ApprovedQuotes2D'></div></td>
                                          <td ><div id='RejectedQuotes2D'></div></td>  
                                        </tr>
                                        <tr>
                                          <td ><div id='PendingQuotes2D'></div></td>  
                                          <td ><div id='SentQuotes2D'></div></td>
                                         </tr>
                                         <tr>
                                          <td ><div id='PrintQuotes2D'></div></td> 
                                         </tr>
                                      </table>";
                    echo $tableQuotations;  
                print "</div>";
            }
        }

        public function dashboardpage ($array){
          require_once(dirname(__FILE__)."/../../fusioncharts/includes/fusioncharts.php");
          $channelname = "";
          if (!empty($array["Channelname"])) {
              $channelname = $array["Channelname"];
          }

          $selectedchannel = $GLOBALS['system_user']->retailChannel;
          if (!empty($array["Channel"])) {
              $selectedchannel = $array["Channel"];
          }

          $selectedchanneltype = "";
          if (!empty($array["ChannelType"])) {
              $selectedchanneltype = $array["ChannelType"];
          }

          $sysuser = new userType($_SESSION["userid"]);
          print "<table style='float:right'>";
              print "<tr>";
                    $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
                    $sqlres = mysqli_query($GLOBALS["link"],$sql);
                    $row = mysqli_fetch_assoc($sqlres);
                    $channeltype = $row['type'];

                    if ($sysuser->isSuperAdmin && $channeltype =="Franchises"){//Based on a user that has permissions see more than one channel
                        /*$sql = "SELECT * FROM `channels` WHERE `type`='Franchises'";
                        $sqlres = mysqli_query($GLOBALS["link"],$sql);
                        $rowcount=mysqli_num_rows($sqlres);
                        if($rowcount > 1){
                            print "<td>";
                              print "<label style='font-weight: bold;'>Select Franchise(s):</label>";
                            print "</td>";
                            print "<td>";
                                print "<select id='dashboardfranchiseschannel' onchange='ChangeDashboardFranchisesChannel(this)'>";
                                print "<option value=''>[Please select]</option>"; 
                                if ($selectedchannel == "All") {
                                  print "<option id ='All' value='All' selected>All</option>";
                                }else{
                                      print "<option id ='All' value='All' >All</option>";              
                                } 
                                $sql = "SELECT * FROM `channels` WHERE `type`='Franchises'";
                                $sqlres = mysqli_query($GLOBALS["link"],$sql);
                                while ($row=mysqli_fetch_assoc($sqlres)){
                                    if ($selectedchannel == $row["id"]) {
                                        print "<option id='".$row['id']."' value='".$row["name"]."' selected>".ucfirst($row["name"])."</option>";
                                    }else{
                                        print "<option id='".$row['id']."' value='".$row["name"]."'>".ucfirst($row["name"])."</option>";                
                                    }
                                }   
                                print "</select>"; 
                            print "</td>";      
                        }*/        
                    }elseif ($sysuser->isSuperAdmin && $channeltype !="Franchises"){//Based on a user that has permissions see more than one channel

                            /*print "<td>";
                            print "<label style='font-weight: bold;'>Select Channel Type:</label>";
                            print "</td>";
                            print "<td>";

                                if ($selectedchanneltype == "Commercial") {
                                  print "<input type='hidden' id='userselectchanneltype' value='Commercial'/>"; 
                                }elseif ($selectedchanneltype == "Retail") {
                                  print "<input type='hidden' id='userselectchanneltype' value='Retail'/>";
                                }elseif ($selectedchanneltype == "Franchises") {
                                  print "<input type='hidden' id='userselectchanneltype' value='Franchises'/>";
                                }

                                print "<select id='dashboardchanneltype'>";
                                if ($selectedchanneltype == "") {
                                  print "<option value=''selected>[Please select]</option>";
                                }else{
                                  print "<option value=''>[Please select]</option>";           
                                }

                                if ($selectedchanneltype == "All") {
                                  print "<option id ='All' value='All' selected>All</option>";
                                }else{
                                  print "<option id ='All' value='All' >All</option>";              
                                }

                                if ($selectedchanneltype == "Commercial") {
                                    print "<option id ='Commercial' value='Commercial' selected>Commercial</option>";
                                }else{
                                    print "<option id ='Commercial' value='Commercial' >Commercial</option>";              
                                }
            
                                if ($selectedchanneltype == "Retail") {
                                  print "<option id='Retail' value='Retail' selected>Retail</option>";
                                }else{
                                  print "<option id='Retail' value='Retail'>Retail</option>";                
                                }
                        
                                if ($selectedchanneltype == "Franchises") {
                                  print "<option id='Franchises' value='Franchises' selected>Franchises</option>";      
                                }else{
                                    print "<option id='Franchises' value='Franchises'>Franchises</option>";              
                                }
                                print "</select>";
                            print "</td>";*/
                    }else{//Non-Admin
                        if($channeltype =="Commercial"){

                            /*$sql = "SELECT * FROM `channels` WHERE `type`='Commercial'";
                            $sqlres = mysqli_query($GLOBALS["link"],$sql);
                            $rowcount=mysqli_num_rows($sqlres);
                            if($rowcount > 1){
                                print "<td>";
                                    print "<label style='font-weight: bold;'>Select Commercial:</label>";
                                print "</td>";
                                print "<td>";
                                    print "<select id='dashboardcommercialchannel' >";
                                    print "<option value=''>[Please select]</option>";  
                                    $sql = "SELECT * FROM `channels` WHERE `type`='Commercial'";
                                    $sqlres = mysqli_query($GLOBALS["link"],$sql);
                                    while ($row=mysqli_fetch_assoc($sqlres)){
                                        if ($selectedchannel == $row["id"]) {
                                            print "<option id='".$row['id']."' value='".$row["name"]."' selected>".ucfirst($row["name"])."</option>";
                                        }else{
                                            print "<option id='".$row['id']."' value='".$row["name"]."'>".ucfirst($row["name"])."</option>";                
                                        }
                                    }
                                    print "</select>";
                                print "</td>";  
                            }*/
                        }elseif($channeltype =="Franchises"){

                           /* $sql = "SELECT * FROM `channels` WHERE `type`='Franchises'";
                            $sqlres = mysqli_query($GLOBALS["link"],$sql);
                            $rowcount=mysqli_num_rows($sqlres);
                            if($rowcount > 1){
                                print "<td>";
                                    print "<label style='font-weight: bold;'>Select Installer:</label>";
                                print "</td>";
                                print "<td>";
                                  print "<select id='dashboardfranchiseschannel' >";
                                  print "<option value=''>[Please select]</option>";  
                                    $sql = "SELECT * FROM `channels` WHERE `type`='Franchises'";
                                    $sqlres = mysqli_query($GLOBALS["link"],$sql);
                                    while ($row=mysqli_fetch_assoc($sqlres)){
                                        if ($selectedchannel == $row["id"]) {
                                            print "<option id='".$row['id']."' value='".$row["name"]."' selected>".ucfirst($row["name"])."</option>";
                                        }else{
                                            print "<option id='".$row['id']."' value='".$row["name"]."'>".ucfirst($row["name"])."</option>";                
                                        }
                                    }   
                                  print "</select>";
                                print "</td>";     
                            } */
                        }elseif($channeltype =="Retail"){
                            
                            /*$sql = "SELECT * FROM `channels` WHERE `type`='Retail'";
                            $sqlres = mysqli_query($GLOBALS["link"],$sql);
                            $rowcount=mysqli_num_rows($sqlres);
                            if($rowcount > 1){
                                print "<td>";
                                      print "<label style='font-weight: bold;'>Select Retail:</label>";
                                print "</td>";
                                print "<td>";
                                    print "<select id='dashboardretailchannel' >";
                                    print "<option value=''>[Please select]</option>";  
                                    $sql = "SELECT * FROM `channels` WHERE `type`='Retail'";
                                    $sqlres = mysqli_query($GLOBALS["link"],$sql);
                                    while ($row=mysqli_fetch_assoc($sqlres)){
                                        if ($selectedchannel == $row["id"]) {
                                            print "<option id='".$row['id']."' value='".$row["name"]."' selected>".ucfirst($row["name"])."</option>";
                                        }else{
                                            print "<option id='".$row['id']."' value='".$row["name"]."'>".ucfirst($row["name"])."</option>";                
                                        }
                                    }  
                                    print "</select>";
                                print "</td>";
                            }*/
                        }
                    }            
              print "</tr>";
          print "</table>";
          //==============================
           //Channels
            if($sysuser->isSuperAdmin){
                if ($channeltype == "Commercial") {
                  print "<table id='Commercial' style='float:right'>";
                      print "<tr>";
                          print "<td>";
                              print "<label style='font-weight: bold;'>Select Commercial:</label>";
                          print "</td>";
                          print "<td>";
                                  print "<select id='dashboardcommercialchannelAdmin' onchange='ChangeDashboardCommercialChannel(this)' >";
                                  print "<option value=''>[Please select]</option>";
                                  if ($channelname == "All") {
                                      print "<option id ='All' value='All' selected>All</option>";
                                  }else{
                                      print "<option id ='All' value='All' >All</option>";              
                                  } 
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
                          print "</td>";           
                      print "</tr>";
                  print "</table>";
                }
                if ($channeltype == "Franchises") {
                  print "<table id='Franchises' style='float:right'>";
                      print "<tr>";
                          print "<td>";
                              print "<label style='font-weight: bold;'>Select Franchise(s):</label>";
                          print "</td>";
                          print "<td>";
                              print "<select id='dashboardfranchiseschannelAdmin' onchange='ChangeDashboardFranchisesChannel(this)' >";
                              print "<option value=''>[Please select]</option>";
                                if ($channelname == 'All' ) {
                                    print "<option id='All' value='All' selected>All</option>";  
                                }else{
                                    print "<option id='All' value='All'>All</option>";  
                                }      
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
                          print "</td>";         
                      print "</tr>";
                  print "</table>";
                }
                if ($channeltype == "Retail") {
                  print "<table id='Retail' style='float:right'>";
                      print "<tr>";
                          print "<td>";
                              print "<label style='font-weight: bold;'>Select Retail:</label>";
                          print "</td>";
                          print "<td>";
                              print "<select id='dashboardretailchannelAdmin' onchange='ChangeDashboardRetailChannel(this)' >";
                              print "<option value=''>[Please select]</option>";  
                               if ($channelname == "All") {
                                    print "<option id ='All' value='All' selected>All</option>";
                                }else{
                                    print "<option id ='All' value='All' >All</option>";              
                                }
                                $sql = "SELECT * FROM `channels` WHERE `type`='Retail'";
                                $sqlres = mysqli_query($GLOBALS["link"],$sql);
                                while ($row=mysqli_fetch_assoc($sqlres)){
                                   if ($channelname == $row["name"]) {
                                        print "<option id='".$row['id']."' value='".$row["name"]."' selected>".ucfirst($row["name"])."</option>";
                                    }else{
                                        print "<option id='".$row['id']."' value='".$row["name"]."'>".ucfirst($row["name"])."</option>";                
                                    }
                                }  
                              print "</select>";
                          print "</td>";          
                      print "</tr>";
                  print "</table>";
                }
            }

          if($channelname == "All"){
            print "<h3 style='font-size:29px;font-weight:bold;font-weight:bold;color:#8dd03f;margin:0;float:left;'>Dashboard</h3>";  
          }else{
            print "<h3 style='font-size:29px;font-weight:bold;font-weight:bold;color:#8dd03f;margin:0;float:left;'>".ucfirst($channelname)." Dashboard</h3>";  
          }    
          print "<div style='clear:both'></div>";
          //==========================
            //Based on a user that has permissions see more than one channel
            //if($channelname =="All" && ($selectedchanneltype == 'Commercial' || $selectedchanneltype == 'Franchises' || $selectedchanneltype == 'Retail')){
            if($channelname =="All" && ($channeltype == 'Commercial' || $channeltype == 'Franchises' || $channeltype == 'Retail')){
                $this->dashboardChannelTypeAllpage($channeltype);
            }elseif($selectedchanneltype =="All" && $channelname == 'All Ellies Channels'){
                $this->dashboardAllChannelspage();
            }elseif($selectedchanneltype == "Franchises" && $channelname == 'All'){
                $this->dashboardAllBranchespage();
            }elseif($selectedchanneltype !=""){

                $arrCharts = array();
                $query  = "SELECT `title`, `show_chart`, `channel_type`";
                $query .= " FROM `dashboardchart_settings`";
                $query .= " WHERE `deleted` = '0' AND `show_chart`='Yes' AND `channel_type`='".$this->getChannelType($GLOBALS['system_user']->retailChannel)."'";
                $result = mysqli_query($GLOBALS["link"],$query);
                while ($arr = mysqli_fetch_assoc($result)) {
                     $arrCharts[] =$arr['title'];
                }
                //Quotes by Status
                $show = false;
                if(in_array("Quotes by Status",  $arrCharts)){
                    $show = true;
                }
                if($show){
                    $this->getTotalStatus($selectedchannel,1);
                    $Total = $this->pendingQuotations+$this->approvedQuotations+$this->sentQuotations+$this->rejectedQuotations+$this->printQuotations;
                    $doughnut2dChart = new FusionCharts("doughnut2d", "QuotesStatus" , 340, 300, "quoteStatusGraph", "json", '{  
                           "chart":{  
                              "use3DLighting": "0",
                              "enableSmartLabels": "0",
                              "startingAngle": "310",
                              "showLabels": "0",
                              "showPercentValues": "1",
                              "showLegend": "1",
                              "defaultCenterLabel": "Total Status: '.$Total.'",
                              "centerLabel": "Total $label: $value",
                              "centerLabelBold": "1",
                              "showTooltip": "1",
                              "decimals": "0",
                              "useDataPlotColorForLabels": "1",
                              "theme":"fint"
                           },
                           "data":[  
                              {  
                                 "label":"Printed Quotations",
                                 "value":"'.$this->printQuotations.'"
                              },
                              {  
                                 "label":"Approved Quotations",
                                 "value":"'.$this->approvedQuotations.'"
                              },
                              {  
                                 "label":"Sent Quotations",
                                 "value":"'.$this->sentQuotations.'"
                              },
                              {  
                                 "label":"Pending Quotations",
                                 "value":"'.$this->pendingQuotations.'"
                              },
                              {  
                                 "label":"Rejected Quotations",
                                 "value":"'.$this->rejectedQuotations.'"
                              }
                           ]
                        }');
                    // Render the chart
                    $doughnut2dChart->render();
                    if (!empty($array["Channel"])){
                        if($array["Channel"] !== $GLOBALS['system_user']->retailChannel){
                            $doughnut2dChart->dispose();
                            $doughnut2dChart = new FusionCharts("doughnut2d", "QuotesStatus" , 340, 300, "quoteStatusGraph", "json", '{  
                               "chart":{  
                                  "use3DLighting": "0",
                                  "enableSmartLabels": "0",
                                  "startingAngle": "310",
                                  "showLabels": "0",
                                  "showPercentValues": "1",
                                  "showLegend": "1",
                                  "defaultCenterLabel": "Total Status: '.$Total.'",
                                  "centerLabel": "Total $label: $value",
                                  "centerLabelBold": "1",
                                  "showTooltip": "1",
                                  "decimals": "0",
                                  "useDataPlotColorForLabels": "1",
                                  "theme":"fint"
                               },
                               "data":[  
                                  {  
                                     "label":"Printed Quotations",
                                     "value":"'.$this->printQuotations.'"
                                  },
                                  {  
                                     "label":"Approved Quotations",
                                     "value":"'.$this->approvedQuotations.'"
                                  },
                                  {  
                                     "label":"Sent Quotations",
                                     "value":"'.$this->sentQuotations.'"
                                  },
                                  {  
                                     "label":"Pending Quotations",
                                     "value":"'.$this->pendingQuotations.'"
                                  },
                                  {  
                                     "label":"Rejected Quotations",
                                     "value":"'.$this->rejectedQuotations.'"
                                  }
                               ]
                            }');
                            // Render the chart
                            $doughnut2dChart->render();
                        }else{
                            $doughnut2dChart->dispose();
                            $doughnut2dChart = new FusionCharts("doughnut2d", "QuotesStatus" , 340, 300, "quoteStatusGraph", "json", '{  
                               "chart":{  
                                  "use3DLighting": "0",
                                  "enableSmartLabels": "0",
                                  "startingAngle": "310",
                                  "showLabels": "0",
                                  "showPercentValues": "1",
                                  "showLegend": "1",
                                  "defaultCenterLabel": "Total Status: '.$Total.'",
                                  "centerLabel": "Total $label: $value",
                                  "centerLabelBold": "1",
                                  "showTooltip": "1",
                                  "decimals": "0",
                                  "useDataPlotColorForLabels": "1",
                                  "theme":"fint"
                               },
                               "data":[  
                                  {  
                                     "label":"Printed Quotations",
                                     "value":"'.$this->printQuotations.'"
                                  },
                                  {  
                                     "label":"Approved Quotations",
                                     "value":"'.$this->approvedQuotations.'"
                                  },
                                  {  
                                     "label":"Sent Quotations",
                                     "value":"'.$this->sentQuotations.'"
                                  },
                                  {  
                                     "label":"Pending Quotations",
                                     "value":"'.$this->pendingQuotations.'"
                                  },
                                  {  
                                     "label":"Rejected Quotations",
                                     "value":"'.$this->rejectedQuotations.'"
                                  }
                               ]
                            }');
                            // Render the chart
                            $doughnut2dChart->render();
                        }
                    }
                    print "<div style='display:inline-block'>";
                        print "<h2 style='font-size:16px;font-weight:bold;color:#8dd03f;margin-top: 32px;margin-left: 34px;'>Quotes by Status</h2>";
                        print "<br/>";
                        $tableStatus = "<table border='0' cellpadding='10' style='border: 1px solid #aaa7a7;margin-left: 34px;'>
                                         <tr>
                                            <td ><div id='quoteStatusGraph'></div></td>
                                            <td width='350'>
                                                <table style='border: 1px solid black;border-collapse: collapse;' width='100%' cellpadding='20'>
                                                    <tr>
                                                        <td style='border:1px solid black;font-weight:bold;' bgcolor='#e57f20'>Total Pending Quotations</td>
                                                        <td style='border:1px solid black;' width='50' align='center'>".$this->pendingQuotations."</td>
                                                    </tr>
                                                    <tr>
                                                        <td style='border:1px solid black;font-weight:bold;' bgcolor='#ffe900'>Total Sent Quotations</td>
                                                        <td style='border:1px solid black;' align='center'>".$this->sentQuotations."</td>
                                                    </tr>
                                                    <tr>
                                                        <td style='border:1px solid black;font-weight:bold;' bgcolor='#ff0000'>Total Rejected Quotations</td>
                                                        <td style='border:1px solid black;' align='center'>".$this->rejectedQuotations."</td>
                                                    </tr>
                                                    <tr>
                                                        <td style='border:1px solid black;font-weight:bold;' bgcolor='#0faa17'>Total Approved Quotations</td>
                                                        <td style='border:1px solid black;' align='center'>".$this->approvedQuotations."</td>
                                                    </tr>
                                                    <tr>
                                                        <td style='border:1px solid black;font-weight:bold;' bgcolor='#0279ef'>Total Printed Quotations</td>
                                                        <td style='border:1px solid black;' align='center'>".$this->printQuotations."</td>
                                                    </tr>
                                                </table>
                                            </td>
                                         </tr>
                                        </table>";
                        echo $tableStatus;
                    print "</div>";
                }
                //======================
                //Latest Customers
                $show = false;
                if(in_array("Latest Customers",  $arrCharts)){
                    $show = true;
                }
                if($show){
                    print "<div style='display:inline-block;margin-left: 60px;width: 718px'>";       
                        $count=0;
                        if ($GLOBALS['system_user']->hasPermission('view_quotes')) {
                          $sql = "SELECT * FROM `customers` where active=1 ";
                          $sql .= "AND channel =".$selectedchannel;
                          $sql .= " ORDER BY `date_created` DESC LIMIT 7";
                          $sqlres = mysqli_query($GLOBALS["link"],$sql);
                          $count = mysqli_num_rows($sqlres);
                        }
                        print "<h3 style='font-size:12px;font-weight:bold;color:#8dd03f;margin-top: 32px;margin-left: 3px;float:right;'>Total Customers: ".$count."</h3>";
                        print "<h2 style='font-size:16px;font-weight:bold;color:#8dd03f;margin-top: 32px;margin-left: 3px;'>Latest Customers</h2>";
                        print "<br/>";
                        if ($GLOBALS['system_user']->hasPermission('view_quotes')) {
                            $sql = "SELECT * FROM `customers` where active=1 ";
                            $sql .= "AND channel =".$selectedchannel;
                            $sql .= " ORDER BY `date_created` DESC LIMIT 7";
                            $sqlres = mysqli_query($GLOBALS["link"],$sql);
                            $row = mysqli_fetch_assoc($sqlres);
                            if($row['id'] == ""){
                                print "No Customers";
                            }else{
                                 print '
                                    <head>
                                        <script type="text/javascript" language="javascript" class="init">
                                        $(document).ready(function() {
                                            $("#latestcustomers").dataTable( {
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
                                                "order": [[ 2, "desc" ]]
                                            } );
                                        } );
                                        </script>
                                    </head>
                                    <body>
                                        <table id="latestcustomers" class="display" cellspacing="3">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Surname</th>
                                                    <th>Cellphone</th>
                                                    <th>Email</th>
                                                    <th>Company</th>                 
                                                </tr>
                                            </thead>
                                            <tbody>';
                                            $sql = "SELECT * FROM `customers` where active=1 ";
                                            $sql .= "AND channel =".$selectedchannel;
                                            $sql .= " ORDER BY `date_created` DESC LIMIT 7";
                                            $sqlres = mysqli_query($GLOBALS["link"],$sql);                    

                                            while($row = mysqli_fetch_assoc($sqlres)) {
                                                print "<tr>";
                                                print "<td align='center'>".$row['name']."</td>";
                                                print "<td align='center'>".$row['surname']."</td>";
                                                print "<td align='center'>".$row['cellphone']."</td>";
                                                print "<td align='center'>".$row['email']."</td>";
                                                print "<td align='center'>".$row['company']."</td>";
                                                print "</tr>";
                                            }
                                    print '</tbody>
                                        </table>
                                    </body>';
                            }
                        }else{
                            print "<p>You do not have permission to view Customers.</p>";
                        }
                    print "</div>";
                }
                //======================
                //Latest Quotes
                $show = false;
                if(in_array("Latest Quotes",  $arrCharts)){
                    $show = true;
                }          
                if($show){
                    print "<div style='height: 100px;width: 775px;margin-left: 34px;display:inline-block;'>";
                    print "<h2 style='font-size:16px;font-weight:bold;color:#8dd03f;margin-top: 32px;margin-left: 2px;'>Latest Quotes</h2>";
                    print "<br/>";
                    
                    if ($GLOBALS['system_user']->hasPermission('view_quotes')) {
                        $sql = "SELECT * FROM `quotes` where approved=0 and complete=1";
                        $sql.= $this->getCriteria($selectedchannel,1);
                        $sql .= " ORDER BY `date_created` DESC LIMIT 50";
                        $sqlres = mysqli_query($GLOBALS["link"],$sql);
                        $row = mysqli_fetch_assoc($sqlres);
                        if($row['customer_id'] == ""){
                            print "No Pending quotes";
                        }else{
                            print '
                                <head>
                                    <script type="text/javascript" language="javascript" class="init">
                                    $(document).ready(function() {
                                        $("#lastquotes").dataTable( {
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
                                            "order": [[ 1, "desc" ]]
                                        } );
                                    } );
                                    </script>
                                </head>
                                <body>
                                    <table id="lastquotes" class="display" cellspacing="3">
                                        <thead>
                                            <tr>
                                                <th width="70">Date</th>
                                                <th>Quote Ref</th>              
                                                <th>Location</th>
                                                <th>Customer</th>
                                                <th>Cellphone</th>
                                                <th>Created By</th>
                                                <th width="60">&nbsp;View</th>                
                                            </tr>
                                        </thead>
                                        <tbody>';
                                        $sql = "SELECT `type` FROM `channels` WHERE `id`=".$selectedchannel;
                                        $sqlres = mysqli_query($GLOBALS["link"],$sql);
                                        $row = mysqli_fetch_assoc($sqlres);
                                        $channeltype = $row['type'];

                                        $sql = "SELECT * FROM `quotes` where approved=0 and complete=1";
                                        $sql.= $this->getCriteria($selectedchannel,0);
                                        
                                        $sql .= " ORDER BY `date_created` DESC LIMIT 50";
                                        $sqlres = mysqli_query($GLOBALS["link"],$sql);

                                        while($row = mysqli_fetch_assoc($sqlres)) {
                                            $quote = new Quote($row["id"]);
                                            $customer = new Customer($quote->CustomerID);

                                            print "<tr>";
                                            if(strtotime($quote->LastUpdated) > strtotime($quote->DateCreated)){
                                              print "<td align='center'  bgcolor='#525656' style='font-weight:bold;'>";//modified
                                            }else{
                                              print "<td align='center'>"; 
                                            }
                                            
                                            print date("Y-m-d", strtotime($quote->DateCreated));
                                            print "</td>";
                                            print "<td align='center'>".$quote->QuoteReferenceNo."</td>";
                                            
                                            print "<td align='center'>";
                                            if ($row["lat"]!=0){
                                            print "<a href='http://maps.google.com?q=".$row["lat"].",".$row["lng"]."' target='_blank'><span class='ui-button-text'>View Location</span>";
                                            }
                                            print "</td>";
                                            print "<td align='center'>".$customer->Surname.", ".$customer->Name."</td>";
                                            print "<td align='center'>".$customer->CellPhone."</td>";
                                            print "<td align='center'>".$quote->CreatedByUserName."</td>";
                                            if($channeltype =="Retail"){
                                                print "<td align='center'><a href='quotes/editquote.php?id=".$row["id"]."&reurl=completed_quotations' target='_blank'><span class='ui-button-text'><img  width='20' height='20' title='View Details'  src='modules/dashboard/images/search.ico' /></span></a></td>";
                                            }else{
                                                print "<td align='center'><a href='quotes/editquotecomm.php?id=".$row["id"]."&reurl=completed_quotations' target='_blank'><span class='ui-button-text'><img  width='20' height='20' title='View Details'  src='modules/dashboard/images/search.ico' /></span></a></td>";
                                                
                                            }
                                            print "</tr>";
                                        }
                                print '</tbody>
                                    </table>
                                </body>';
                        }
                    }else{
                        print "<p>You do not have permission to view latest quotes.</p>";
                    }
                    print "</div>";
                } 
                //======================
                //Building Types
                $show = false;
                if(in_array("Building Types",  $arrCharts)){
                    $show = true;
                }
                if($show){
                    print "<div style='width: 717px;margin-left: 28px;display:inline-block;width: 667px'>";
                        print "<br/>";
                        print "<h2 style='font-size:16px;font-weight:bold;color:#8dd03f;margin-top: 32px;margin-left: 2px;'>Building Types</h2>";
                        print "<br/>";
                        if ($GLOBALS['system_user']->hasPermission('view_quotes')) {
                            $sql = "SELECT * FROM `property_types` where deleted=0 ";
                            $sqlres = mysqli_query($GLOBALS["link"],$sql);
                            $row = mysqli_fetch_assoc($sqlres);
                            if($row['id'] == ""){
                                print "No Property Types";
                            }else{
                                 print '
                                    <head>
                                        <script type="text/javascript" language="javascript" class="init">
                                        $(document).ready(function() {
                                            $("#buildingtypes").dataTable( {
                                                columnDefs: [ {
                                                    targets: [ 0 ],
                                                    orderData: [ 0, 0 ]
                                                } ],
                                                "order": [[ 0, "desc" ]]
                                            } );
                                        } );
                                        </script>
                                    </head>
                                    <body>
                                        <table id="buildingtypes" class="display" cellspacing="3">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>                
                                                </tr>
                                            </thead>
                                            <tbody>';
                                            $sql = "SELECT * FROM `property_types` where deleted=0 ";
                                            $sqlres = mysqli_query($GLOBALS["link"],$sql);                   

                                            while($row = mysqli_fetch_assoc($sqlres)) {
                                                print "<tr>";
                                                print "<td >".$row['Type']."</td>";;
                                                print "</tr>";
                                            }
                                    print '</tbody>
                                        </table>
                                    </body>';
                            }
                        }else{
                            print "<p>You do not have permission to view Building Types.</p>";
                        }
                    print "</div>";
                } 
                //======================
                //Products by Category
                $show = false;
                if(in_array("Products by Category",  $arrCharts)){
                    $show = true;
                }
                if($show){
                    print "<div style='margin-left: 30px;display:inline-block'>";   
                        print "<br/>";          
                        print "<h2 style='font-size:16px;font-weight:bold;color:#8dd03f;margin-top: 200px;margin-left: 2px;'>Products by Category</h2>";
                        print "<br/>";
                        $arrProducts = array();
                        $arrProductsNames = array();
                        $arrProductNum = array();

                        $sql = "SELECT DISTINCT `class_of_product` FROM `new_products`where channel=".$selectedchannel." and deleted=0 ORDER BY `class_of_product`";
                        $sqlres = mysqli_query($GLOBALS["link"],$sql);
                        while($row = mysqli_fetch_assoc($sqlres)) {
                            $arrProducts[] = $row["class_of_product"];  
                            $arrProductsNames[] = ucwords(strtolower($row["class_of_product"]));             
                        }

                        $Total = 0;
                        $data = '';
                        for($i=0;$i<count($arrProducts);$i++){
                            $sql = "SELECT * FROM `new_products`where `class_of_product`='".strtoupper($arrProducts[$i])."' AND  channel=".$selectedchannel." AND deleted=0 ORDER BY `class_of_product`";
                            $sqlres = mysqli_query($GLOBALS["link"],$sql);
                            $rowcount=mysqli_num_rows($sqlres);
                            $arrProductNum[] = $rowcount;
                            $Total +=$rowcount;
                            //Chart
                            if($i == count($arrProductsNames)-1){
                                $data .= '{  
                                            "label":"'.$arrProductsNames[$i].'",
                                            "value":"'.$rowcount.'"
                                          }';
                            }else{
                                $data .= '{  
                                             "label":"'.$arrProductsNames[$i].'",
                                             "value":"'.$rowcount.'"
                                          },';
                            }          
                        }   

                        $doughnut3dChart = new FusionCharts("doughnut2d", "ProductsbyCategory" , 390, 400, "productsGraph", "json", '{  
                               "chart":{  
                                  "use3DLighting": "0",
                                  "enableSmartLabels": "0",
                                  "startingAngle": "310",
                                  "showLabels": "0",
                                  "showPercentValues": "1",
                                  "showLegend": "1",
                                  "defaultCenterLabel": "Total Products: '.$Total.'",
                                  "centerLabel": "Total $label: $value",
                                  "centerLabelBold": "1",
                                  "showTooltip": "1",
                                  "decimals": "0",
                                  "useDataPlotColorForLabels": "1",
                                  "theme":"fint"
                               },
                               "data":['.$data.']
                        }');
                        // Render the chart
                        $doughnut3dChart->render();


                        if (!empty($array["Channel"]) ){
                            if($array["Channel"] !== $GLOBALS['system_user']->retailChannel){
                                $doughnut3dChart->dispose();
                                $doughnut3dChart = new FusionCharts("doughnut2d", "ProductsbyCategory" , 390, 400, "productsGraph", "json", '{  
                                   "chart":{  
                                      "use3DLighting": "0",
                                      "enableSmartLabels": "0",
                                      "startingAngle": "310",
                                      "showLabels": "0",
                                      "showPercentValues": "1",
                                      "showLegend": "1",
                                      "defaultCenterLabel": "Total Products: '.$Total.'",
                                      "centerLabel": "Total $label: $value",
                                      "centerLabelBold": "1",
                                      "showTooltip": "1",
                                      "decimals": "0",
                                      "useDataPlotColorForLabels": "1",
                                      "theme":"fint"
                                   },
                                   "data":['.$data.']
                                }');
                                // Render the chart
                                $doughnut3dChart->render();
                            }else{
                                $doughnut3dChart->dispose();
                                $doughnut3dChart = new FusionCharts("doughnut2d", "ProductsbyCategory" , 390, 400, "productsGraph", "json", '{  
                                   "chart":{  
                                      "use3DLighting": "0",
                                      "enableSmartLabels": "0",
                                      "startingAngle": "310",
                                      "showLabels": "0",
                                      "showPercentValues": "1",
                                      "showLegend": "1",
                                      "defaultCenterLabel": "Total Products: '.$Total.'",
                                      "centerLabel": "Total $label: $value",
                                      "centerLabelBold": "1",
                                      "showTooltip": "1",
                                      "decimals": "0",
                                      "useDataPlotColorForLabels": "1",
                                      "theme":"fint"
                                   },
                                   "data":['.$data.']
                                }');
                                // Render the chart
                                $doughnut3dChart->render();
                            }              
                        }
                        
                        $tableProducts = "<table border='0' cellpadding='10' style='border: 1px solid #aaa7a7;margin-left: 4px;margin-bottom: 40px;'>
                                          <tr>
                                            <td ><div id='productsGraph'></div></td>  
                                            <td width='350'>
                                                    <table style='border: 1px solid black;border-collapse: collapse;' width='100%' >";

                                                    for($i=0;$i<count($arrProducts);$i++){
                                                        $tableProducts .= "<tr>
                                                                                <td style='border:1px solid black;font-weight:bold;' bgcolor='#d1d1d1' width='40'>".$arrProductsNames[$i]."</td>
                                                                                <td style='border:1px solid black;' bgcolor='#d1d1d1' width='30' align='center'>".$arrProductNum[$i]."</td>
                                                                 
                                                                            </tr>";         
                                                    }               
                        $tableProducts .="          </table>
                                            </td>
                                           </tr>
                                        </table>";
                        echo $tableProducts;
                    print "</div>";
                }
                //======================
                //Latest Products
                $show = false;
                if(in_array("Latest Products",  $arrCharts)){
                    $show = true;
                }
                if($show){
                    print "<div style='width: 680px;margin-left: 41px;display:inline-block;width: 667px'>";
                        print "<br/>";
                        print "<h2 style='font-size:16px;font-weight:bold;color:#8dd03f;margin-top: 32px;margin-left: 3px;'>Latest Products</h2>";
                        print "<br/>";
                        if ($GLOBALS['system_user']->hasPermission('view_quotes')) {
                            $sql = "SELECT * FROM `new_products` where channel=".$selectedchannel." and deleted=0";
                            $sql .= " ORDER BY `date_created` DESC LIMIT 11";
                            $sqlres = mysqli_query($GLOBALS["link"],$sql);
                            $row = mysqli_fetch_assoc($sqlres);
                            if($row['id'] == ""){
                                print "No Latest Products";
                            }else{
                                 print '
                                    <head>
                                        <script type="text/javascript" language="javascript" class="init">
                                        $(document).ready(function() {
                                            $("#latestproducts").dataTable( {
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
                                        </script>
                                    </head>
                                    <body>
                                        <table id="latestproducts" class="display" cellspacing="3">
                                            <thead>
                                                <tr>
                                                    <th>Code</th>
                                                    <th>Name</th>   
                                                    <th>Class of Product</th>                
                                                    <th>Supplier</th>                
                                                </tr>
                                            </thead>
                                            <tbody>';
                                            $sql = "SELECT * FROM `new_products` where channel=".$selectedchannel." and deleted=0";
                                            $sql .= " ORDER BY `date_created` DESC LIMIT 11";
                                            $sqlres = mysqli_query($GLOBALS["link"],$sql);
                                            $row = mysqli_fetch_assoc($sqlres);                  

                                            while($row = mysqli_fetch_assoc($sqlres)){
                                              print "<tr>";
                                              print "<td align='center'>".$row['code']."</td>";
                                              print "<td align='center'>".$row['product']."</td>";
                                              print "<td align='center'>".$row['class_of_product']."</td>";
                                              print "<td align='center'>".$row['supplier']."</td>";
                                              print "</tr>";
                                            }
                                    print '</tbody>
                                        </table>
                                    </body>';
                            }
                        }else{
                            print "<p>You do not have permission to view Latest Products.</p>";
                        }
                    print "</div>";
                }        
              }
              //=================================
                print '<script type="text/javascript" language="javascript" class="init">';
                 //Based on a user that has permissions see more than one channel
                  /*print "$('table#Commercial').hide();";
                  print "$('table#Franchises').hide();";
                  print "$('table#Retail').hide();";

                  print "var channeltype = $('input#userselectchanneltype').val();
                        if(channeltype =='Commercial'){
                          $('table#Commercial').toggle();
                        }else if(channeltype =='Franchises'){
                          $('table#Franchises').toggle();
                        }else if(channeltype =='Retail'){
                            $('table#Retail').toggle();
                        }";*/


                  print "jQuery('select#dashboardchanneltype').change(function () { 
                          var type = $(dashboardchanneltype).val();  
                          if(type == 'Commercial'){
                            $('table#Franchises').hide();
                            $('table#Retail').hide();  
                            $('select#dashboardcommercialchannelAdmin').val(''); 
                            $('table#Commercial').toggle();
                          }else if(type == 'Franchises'){
                            $('table#Commercial').hide();
                            $('table#Retail').hide();  
                            $('select#dashboardfranchiseschannelAdmin').val(''); 
                            $('table#Franchises').toggle();
                          }else if(type == 'Retail'){
                            $('table#Commercial').hide();
                            $('table#Franchises').hide(); 
                            $('select#dashboardretailchannelAdmin').val(''); 
                            $('table#Retail').toggle();
                          }else if(type == 'All'){
                            var channelname = 'All Ellies Channels';
                            var channeltype = 'All';
                            AJAXCallModule('Dashboard','dashboardpage', 'ChannelType='+channeltype+'& Channelname='+channelname);
                          }       
                      });\n";
                print '</script>';
            }
    }
?>