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
    register_menu("Quotations", "submenu", "Quotations",
        Array(
            Array("title" => "Pending Quotations",  "location" => "pending_quotations",   "acl" => "view_quotes"),
            Array("title" => "Sent Quotations",  "location" => "completed_quotations",   "acl" => "view_quotes"),
			Array("title" => "Rejected Quotations",  "location" => "rejected_quotations",   "acl" => "view_quotes"),
            Array("title" => "Approved Quotations",  "location" => "approved_quotations",   "acl" => "view_quotes"),
            Array("title" => "Printed Quotations",  "location" => "printed_quotations",   "acl" => "view_quotes")
        )
    );
    register_menu("Quotations", "parentMenu", "view_quotes");

    register_permission("Quote Permissions", "view_quotes",   "View Quotes");
    register_permission("Quote Permissions", "edit_quotes",   "Edit Quotes");
    register_permission("Quote Permissions", "complete_quotes",   "Complete Quotes");
 

    class Quotations {
        
        private $items_per_page;
        
        public function __construct () {
            $this->items_per_page = Settings::getSetting(2);
        }
		
		    public function getCriteria($channelID ="All"){
          $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
          $sqlres = mysqli_query($GLOBALS["link"],$sql);
          $row = mysqli_fetch_assoc($sqlres);
          $channeltype = $row['type'];
          $cond = "";
          
          if ($channeltype == "Commercial" || $channeltype =="Franchises"){
              if ($GLOBALS['system_user']->isSuperAdmin == FALSE){
                  $cond =" AND `quotes`.created_by in (SELECT id from system_users where store_id = ".$GLOBALS['system_user']->storeID.")";
              } else {
                  //$cond = " AND `quotes`.created_by in (SELECT id from system_users where branch_id =".$GLOBALS['system_user']->branchID.")";
         
                  /*$cond = " AND `quotes`.created_by in (SELECT id from system_users where branch_id IN (SELECT id from branches where channel IN (SELECT id from channels where type='".$channeltype."')))";*/

                  if($channelID == "All"){
                      $cond = " AND `quotes`.created_by in (SELECT id from system_users where branch_id IN (SELECT id from branches where channel IN (SELECT id from channels where type='".$channeltype."')))";
                  }else{
                     $cond = " AND `quotes`.created_by in (SELECT id from system_users where branch_id IN (SELECT id from branches where channel=".$channelID."))"; 
                  }
              }
          } else {
              if ($GLOBALS['system_user']->isSuperAdmin == FALSE){
                  $cond =" AND `quotes`.created_by in (SELECT id from system_users where store_id = ".$GLOBALS['system_user']->storeID.")";
              } elseif($GLOBALS['system_user']->isSuperAdmin){
                  //$cond = " AND `quotes`.created_by in (SELECT id from system_users where branch_id IN (SELECT id from branches where channel IN (SELECT id from channels where type='".$channeltype."')))";

                  if($channelID == "All"){
                      $cond = " AND `quotes`.created_by in (SELECT id from system_users where branch_id IN (SELECT id from branches where channel IN (SELECT id from channels where type='".$channeltype."')))";
                  }else{
                     $cond = " AND `quotes`.created_by in (SELECT id from system_users where branch_id IN (SELECT id from branches where channel=".$channelID."))"; 
                  }
              }else {
                  $cond = " AND `quotes`.created_by in (SELECT id from system_users where branch_id IN (SELECT id from branches where channel=".$GLOBALS['system_user']->retailChannel."))";
              }
          } 
		      return $cond;		  
        }

        public function quotations_entry() {
            print "<br>&nbsp;<button onclick=\"AJAXCallModule('Quotations','view_quotations', '')\"><span class='ui-button-text'>View Quotes</span></button>";
        }

        public function completed_quotations($array) {

          $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
          $sqlres = mysqli_query($GLOBALS["link"],$sql);
          $row = mysqli_fetch_assoc($sqlres);
          $channeltype = $row['type'];

          if($GLOBALS['system_user']->isSuperAdmin){
              print "<div class='classy_table'>";
              print "&nbsp;&nbsp;&nbsp;<span style='font-size:14px;font-weight:bold;'>Sent Quotes</span>";
              //Channels
              $channelname = "All";
              if (!empty($array["Channelname"])) {
                  $channelname = $array["Channelname"];
              }

              $selectedchannel = "All";
              if (!empty($array["Channel"])) {
                  $selectedchannel = $array["Channel"];
              }

              if ($channeltype == "Commercial") {
                print "<table id='Commercial' class='quotations' style='float:right;width: 20%;height: 33px;margin-top: -7px;border:none;'>";
                    print "<tr>";
                        print "<td>";
                            print "<label style='font-weight: bold;'>Select Commercial:</label>";
                        print "</td>";
                        print "<td>";
                                print "<select id='sentcommercialchannelAdmin' onchange='ChangeSentCommercialChannel(this)' >";
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
                print "<table id='Franchises' class='quotations' style='float:right;width: 18%;height: 33px;margin-top: -7px;border:none;'>";
                    print "<tr>";
                        print "<td>";
                            print "<label style='font-weight: bold;'>Select Franchise(s):</label>";
                        print "</td>";
                        print "<td>";
                            print "<select id='sentfranchiseschannelAdmin' onchange='ChangeSentFranchisesChannel(this)' >";
                            print "<option value=''>[Please select]</option>";
                              if ($channelname == 'All' ) {
                                  print "<option id='All' value='All' selected>All</option>";  
                              }else{
                                  print "<option id='All' value='All'>All</option>";  
                              }      
                              $sql = "SELECT * FROM `channels` WHERE `type`='Franchises'";
                              $sqlres = mysqli_query($GLOBALS["link"],$sql);
                              while ($row=mysqli_fetch_assoc($sqlres)){
                                  if ($channelname == $row["name"] || $selectedchannel == $row['id']) {
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
                print "<table id='Retail' class='quotations' style='float:right;width: 18%;height: 33px;margin-top: -7px;border:none;'>";
                    print "<tr>";
                        print "<td>";
                            print "<label style='font-weight: bold;'>Select Retail:</label>";
                        print "</td>";
                        print "<td>";
                            print "<select id='sentretailchannelAdmin' onchange='ChangeSentRetailChannel(this)' >";
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
              print "</div>";
              print "<br/><br/>";

              if ($GLOBALS['system_user']->hasPermission('view_quotes')) {
                  $sql = "SELECT * FROM `quotes` where approved=1 ";
                  $sql.= $this->getCriteria($selectedchannel);
                  $sql .=" ORDER BY `last_updated` DESC";
                  $sqlres = mysqli_query($GLOBALS["link"],$sql);
                  $row = mysqli_fetch_assoc($sqlres);
                  if(empty($row['customer_id'])){
                      print "No Sent quotes";
                  }else{
                      print '
                          <head>
                              <script type="text/javascript" language="javascript" class="init">
                              $(document).ready(function() {
                                  $("table#pending").dataTable( {
                                      columnDefs: [ {
                                          targets: [ 0 ],
                                          orderData: [ 0, 1 ]
                                      }, {
                                          targets: [ 1 ],
                                          orderData: [ 1, 0 ]
                                      }, {
                                          targets: [ 4 ],
                                          orderData: [ 4, 0 ]
                                      } ]
                                  } );
                              } );
                              </script>
                          </head>
                          <body>
                              <table id="pending" class="display" cellspacing="3" width="100%">
                                  <thead>
                                      <tr>
                                          <th width="120">&nbsp;Quote Details</th>
                                          <th>Quote Ref</th>
                                          <th>Date</th>
                                          <th>Location</th>
                                          <th>Customer</th>
                                          <th>Cellphone</th>
                                          <th>Created By</th>                 
                                      </tr>
                                  </thead>
                                  <tbody>';

                      $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
                      $sqlres = mysqli_query($GLOBALS["link"],$sql);
                      $row = mysqli_fetch_assoc($sqlres);
                      $channeltype = $row['type'];
                      if ($channeltype == "Commercial" || $channeltype =="Franchises"){
                          $suffix="comm";
                      }else {
                          $suffix="";
                      }
                      $sql = "SELECT * FROM `quotes` where approved=1 ";
                      $sql.= $this->getCriteria($selectedchannel);
                      $sql .=" ORDER BY `last_updated` DESC";
                      $sqlres = mysqli_query($GLOBALS["link"],$sql);
                      while($row = mysqli_fetch_assoc($sqlres)) {
                          $quote = new Quote($row["id"]);
                          $customer = new Customer($quote->CustomerID);
                          print "<tr>";
                          print "<td align='center'>";
                          print "<a href='quotes/editquote".$suffix.".php?id=".$row["id"]."&reurl=completed_quotations' target='_blank'><span class='ui-button-text'>View Quote</span></a>";
                          print "</td>";
                           print "<td align='center'>".$quote->QuoteReferenceNo."</td>";
                          print "<td align='center'>".date("Y-m-d", strtotime($quote->DateCreated))."</td>";
                          print "<td align='center'>";
                          if ($row["lat"]!=0){
                          print "<a href='http://maps.google.com?q=".$row["lat"].",".$row["lng"]."' target='_blank'><span class='ui-button-text'>View Location</span>";
                          }
                          print "</td>";
                          print "<td align='center'>".$customer->Surname.", ".$customer->Name."</td>";
                          print "<td align='center'>".$customer->CellPhone."</td>";
                          print "<td align='center'>".$quote->CreatedByUserName."</td>";
                          print "</tr>";
                      }
                       print '</tbody>
                              </table>
                          </body>
                          ';
                  }
              }else{
                  print "<p>You do not have permission to view quotes.</p>";
              }
          }else{
              print "<div class='classy_table'>";
              print "&nbsp;&nbsp;&nbsp;<span style='font-size:14px;font-weight:bold;'>Sent Quotes</span>";
              print "</div>";
              print "<br/><br/>";

              if ($GLOBALS['system_user']->hasPermission('view_quotes')) {
                  $sql = "SELECT * FROM `quotes` where approved=1 ";
                  $sql.= $this->getCriteria();
                  $sql .=" ORDER BY `last_updated` DESC";
                  $sqlres = mysqli_query($GLOBALS["link"],$sql);
                  $row = mysqli_fetch_assoc($sqlres);
                  if(empty($row['customer_id'])){
                      print "No Sent quotes";
                  }else{
                      print '
                          <head>
                              <script type="text/javascript" language="javascript" class="init">
                              $(document).ready(function() {
                                  $("table#pending").dataTable( {
                                      columnDefs: [ {
                                          targets: [ 0 ],
                                          orderData: [ 0, 1 ]
                                      }, {
                                          targets: [ 1 ],
                                          orderData: [ 1, 0 ]
                                      }, {
                                          targets: [ 4 ],
                                          orderData: [ 4, 0 ]
                                      } ]
                                  } );
                              } );
                              </script>
                          </head>
                          <body>
                              <table id="pending" class="display" cellspacing="3" width="100%">
                                  <thead>
                                      <tr>
                                          <th width="120">&nbsp;Quote Details</th>
                                          <th>Quote Ref</th>
                                          <th>Date</th>
                                          <th>Location</th>
                                          <th>Customer</th>
                                          <th>Cellphone</th>
                                          <th>Created By</th>                 
                                      </tr>
                                  </thead>
                                  <tbody>';

                      $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
                      $sqlres = mysqli_query($GLOBALS["link"],$sql);
                      $row = mysqli_fetch_assoc($sqlres);
                      $channeltype = $row['type'];
                      if ($channeltype == "Commercial" || $channeltype =="Franchises"){
                          $suffix="comm";
                      }else {
                          $suffix="";
                      }
                      $sql = "SELECT * FROM `quotes` where approved=1 ";
                      $sql.= $this->getCriteria();
                      $sql .=" ORDER BY `last_updated` DESC";
                      $sqlres = mysqli_query($GLOBALS["link"],$sql);
                      while($row = mysqli_fetch_assoc($sqlres)) {
                          $quote = new Quote($row["id"]);
                          $customer = new Customer($quote->CustomerID);
                          print "<tr>";
                          print "<td align='center'>";
                          print "<a href='quotes/editquote".$suffix.".php?id=".$row["id"]."&reurl=completed_quotations' target='_blank'><span class='ui-button-text'>View Quote</span></a>";
                          print "</td>";
                           print "<td align='center'>".$quote->QuoteReferenceNo."</td>";
                          print "<td align='center'>".date("Y-m-d", strtotime($quote->DateCreated))."</td>";
                          print "<td align='center'>";
                          if ($row["lat"]!=0){
                          print "<a href='http://maps.google.com?q=".$row["lat"].",".$row["lng"]."' target='_blank'><span class='ui-button-text'>View Location</span>";
                          }
                          print "</td>";
                          print "<td align='center'>".$customer->Surname.", ".$customer->Name."</td>";
                          print "<td align='center'>".$customer->CellPhone."</td>";
                          print "<td align='center'>".$quote->CreatedByUserName."</td>";
                          print "</tr>";
                      }
                       print '</tbody>
                              </table>
                          </body>
                          ';
                  }
              }else{
                  print "<p>You do not have permission to view quotes.</p>";
              }
          }
        }

		    public function rejected_quotations($array) {

          $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
          $sqlres = mysqli_query($GLOBALS["link"],$sql);
          $row = mysqli_fetch_assoc($sqlres);
          $channeltype = $row['type'];

          if($GLOBALS['system_user']->isSuperAdmin){
              print "<div class='classy_table'>";
              print "&nbsp;&nbsp;&nbsp;<span style='font-size:14px;font-weight:bold;'>Rejected Quotes</span>";
              //Channels
              $channelname = "All";
              if (!empty($array["Channelname"])) {
                  $channelname = $array["Channelname"];
              }

              $selectedchannel = "All";
              if (!empty($array["Channel"])) {
                  $selectedchannel = $array["Channel"];
              }

              if ($channeltype == "Commercial") {
                print "<table id='Commercial' class='quotations' style='float:right;width: 20%;height: 33px;margin-top: -7px;border:none;'>";
                    print "<tr>";
                        print "<td>";
                            print "<label style='font-weight: bold;'>Select Commercial:</label>";
                        print "</td>";
                        print "<td>";
                                print "<select id='rejectedcommercialchannelAdmin' onchange='ChangeRejectedCommercialChannel(this)' >";
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
                print "<table id='Franchises' class='quotations' style='float:right;width: 18%;height: 33px;margin-top: -7px;border:none;'>";
                    print "<tr>";
                        print "<td>";
                            print "<label style='font-weight: bold;'>Select Franchise(s):</label>";
                        print "</td>";
                        print "<td>";
                            print "<select id='rejectedfranchiseschannelAdmin' onchange='ChangeRejectedFranchisesChannel(this)' >";
                            print "<option value=''>[Please select]</option>";
                              if ($channelname == 'All' ) {
                                  print "<option id='All' value='All' selected>All</option>";  
                              }else{
                                  print "<option id='All' value='All'>All</option>";  
                              }      
                              $sql = "SELECT * FROM `channels` WHERE `type`='Franchises'";
                              $sqlres = mysqli_query($GLOBALS["link"],$sql);
                              while ($row=mysqli_fetch_assoc($sqlres)){
                                  if ($channelname == $row["name"] || $selectedchannel == $row['id']) {
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
                print "<table id='Retail' class='quotations' style='float:right;width: 18%;height: 33px;margin-top: -7px;border:none;'>";
                    print "<tr>";
                        print "<td>";
                            print "<label style='font-weight: bold;'>Select Retail:</label>";
                        print "</td>";
                        print "<td>";
                            print "<select id='rejectedretailchannelAdmin' onchange='ChangeRejectedRetailChannel(this)' >";
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
              print "</div>";
              print "<br/><br/>";

              if ($GLOBALS['system_user']->hasPermission('view_quotes')) {
                  $sql = "SELECT * FROM `quotes` where approved=-1";
                  $sql.= $this->getCriteria();
                  $sql.= " ORDER BY `last_updated` DESC";
                  $sqlres = mysqli_query($GLOBALS["link"],$sql);
                  $row = mysqli_fetch_assoc($sqlres);
                  if(empty($row['customer_id'])){
                      print "No Rejected quotes";
                  }else{  
                      print '
                          <head>
                              <script type="text/javascript" language="javascript" class="init">
                              $(document).ready(function() {
                                  $("#pending").dataTable( {
                                      columnDefs: [ {
                                          targets: [ 0 ],
                                          orderData: [ 0, 1 ]
                                      }, {
                                          targets: [ 1 ],
                                          orderData: [ 1, 0 ]
                                      }, {
                                          targets: [ 4 ],
                                          orderData: [ 4, 0 ]
                                      } ]
                                  } );
                              } );
                              </script>
                          </head>
                          <body>
                              <table id="pending" class="display" cellspacing="3" width="100%">
                                  <thead>
                                      <tr>
                                          <th width="120">&nbsp;Quote Details</th>
                                          <th>Quote Ref</th>
                                          <th>Date</th>
                                          <th>Location</th>
                                          <th>Customer</th>
                                          <th>Cellphone</th>
                                          <th>Created By</th>                 
                                      </tr>
                                  </thead>
                                  <tbody>';
                      $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
                      $sqlres = mysqli_query($GLOBALS["link"],$sql);
                      $row = mysqli_fetch_assoc($sqlres);
                      $channeltype = $row['type'];

                      if ($channeltype == "Commercial" || $channeltype =="Franchises"){
                          $suffix="comm";
                      }else {
                          $suffix="";
                      }
                      $sql = "SELECT * FROM `quotes` where approved=-1";
                      $sql.= $this->getCriteria();
                      
                      $sql.= " ORDER BY `last_updated` DESC";
                      $sqlres = mysqli_query($GLOBALS["link"],$sql);
                      while($row = mysqli_fetch_assoc($sqlres)) {
                          $quote = new Quote($row["id"]);
                          $customer = new Customer($quote->CustomerID);
                          print "<tr>";
                          print "<td align='center' bgcolor='#ff0000'>";
                          print "<a href='quotes/editquote".$suffix.".php?id=".$row["id"]."&reurl=completed_quotations' target='_blank'><span class='ui-button-text'>View Quote</span></a>";
                          print "</td>";
                           print "<td align='center'>".$quote->QuoteReferenceNo."</td>";
                          print "<td align='center'>".date("Y-m-d", strtotime($quote->DateCreated))."</td>";
                          print "<td align='center'>";
                          if ($row["lat"]!=0){
                          print "<a href='http://maps.google.com?q=".$row["lat"].",".$row["lng"]."' target='_blank'><span class='ui-button-text'>View Location</span>";
                          }
                          print "</td>";
                          print "<td align='center'>".$customer->Surname.", ".$customer->Name."</td>";
                          print "<td align='center'>".$customer->CellPhone."</td>";
                          print "<td align='center'>".$quote->CreatedByUserName."</td>";
                          print "</tr>";
                      }
                      print '</tbody>
                              </table>
                          </body>
                          ';
                  }
              }else{
                  print "<p>You do not have permission to view quotes.</p>";
              }
          }else{

              print "<div class='classy_table'>";
              print "&nbsp;&nbsp;&nbsp;<span style='font-size:14px;font-weight:bold;'>Rejected Quotes</span>";
              print "</div>";
              print "<br/><br/>";

              if ($GLOBALS['system_user']->hasPermission('view_quotes')) {
                  $sql = "SELECT * FROM `quotes` where approved=-1";
                  $sql.= $this->getCriteria();
                  $sql.= " ORDER BY `last_updated` DESC";
                  $sqlres = mysqli_query($GLOBALS["link"],$sql);
                  $row = mysqli_fetch_assoc($sqlres);
                  if(empty($row['customer_id'])){
                      print "No Rejected quotes";
                  }else{  
                      print '
                          <head>
                              <script type="text/javascript" language="javascript" class="init">
                              $(document).ready(function() {
                                  $("#pending").dataTable( {
                                      columnDefs: [ {
                                          targets: [ 0 ],
                                          orderData: [ 0, 1 ]
                                      }, {
                                          targets: [ 1 ],
                                          orderData: [ 1, 0 ]
                                      }, {
                                          targets: [ 4 ],
                                          orderData: [ 4, 0 ]
                                      } ]
                                  } );
                              } );
                              </script>
                          </head>
                          <body>
                              <table id="pending" class="display" cellspacing="3" width="100%">
                                  <thead>
                                      <tr>
                                          <th width="120">&nbsp;Quote Details</th>
                                          <th>Quote Ref</th>
                                          <th>Date</th>
                                          <th>Location</th>
                                          <th>Customer</th>
                                          <th>Cellphone</th>
                                          <th>Created By</th>                 
                                      </tr>
                                  </thead>
                                  <tbody>';
                      $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
                      $sqlres = mysqli_query($GLOBALS["link"],$sql);
                      $row = mysqli_fetch_assoc($sqlres);
                      $channeltype = $row['type'];

                      if ($channeltype == "Commercial" || $channeltype =="Franchises"){
                          $suffix="comm";
                      }else {
                          $suffix="";
                      }
                      $sql = "SELECT * FROM `quotes` where approved=-1";
                      $sql.= $this->getCriteria();
                      
                      $sql.= " ORDER BY `last_updated` DESC";
                      $sqlres = mysqli_query($GLOBALS["link"],$sql);
                      while($row = mysqli_fetch_assoc($sqlres)) {
                          $quote = new Quote($row["id"]);
                          $customer = new Customer($quote->CustomerID);
                          print "<tr>";
                          print "<td align='center' bgcolor='#ff0000'>";
                          print "<a href='quotes/editquote".$suffix.".php?id=".$row["id"]."&reurl=completed_quotations' target='_blank'><span class='ui-button-text'>View Quote</span></a>";
                          print "</td>";
                           print "<td align='center'>".$quote->QuoteReferenceNo."</td>";
                          print "<td align='center'>".date("Y-m-d", strtotime($quote->DateCreated))."</td>";
                          print "<td align='center'>";
                          if ($row["lat"]!=0){
                          print "<a href='http://maps.google.com?q=".$row["lat"].",".$row["lng"]."' target='_blank'><span class='ui-button-text'>View Location</span>";
                          }
                          print "</td>";
                          print "<td align='center'>".$customer->Surname.", ".$customer->Name."</td>";
                          print "<td align='center'>".$customer->CellPhone."</td>";
                          print "<td align='center'>".$quote->CreatedByUserName."</td>";
                          print "</tr>";
                      }
                      print '</tbody>
                              </table>
                          </body>
                          ';
                  }
              }else{
                  print "<p>You do not have permission to view quotes.</p>";
              }
          }
        }

        public function approved_quotations($array) {

          $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
          $sqlres = mysqli_query($GLOBALS["link"],$sql);
          $row = mysqli_fetch_assoc($sqlres);
          $channeltype = $row['type'];

          if($GLOBALS['system_user']->isSuperAdmin){
              print "<div class='classy_table'>";
              print "&nbsp;&nbsp;&nbsp;<span style='font-size:14px;font-weight:bold;'>Approved Quotes</span>";
              //Channels
              $channelname = "All";
              if (!empty($array["Channelname"])) {
                  $channelname = $array["Channelname"];
              }

              $selectedchannel = "All";
              if (!empty($array["Channel"])) {
                  $selectedchannel = $array["Channel"];
              }

              if ($channeltype == "Commercial") {
                print "<table id='Commercial' class='quotations' style='float:right;width: 20%;height: 33px;margin-top: -7px;border:none;'>";
                    print "<tr>";
                        print "<td>";
                            print "<label style='font-weight: bold;'>Select Commercial:</label>";
                        print "</td>";
                        print "<td>";
                                print "<select id='approvedcommercialchannelAdmin' onchange='ChangeApprovedCommercialChannel(this)' >";
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
                print "<table id='Franchises' class='quotations' style='float:right;width: 18%;height: 33px;margin-top: -7px;border:none;'>";
                    print "<tr>";
                        print "<td>";
                            print "<label style='font-weight: bold;'>Select Franchise(s):</label>";
                        print "</td>";
                        print "<td>";
                            print "<select id='approvedfranchiseschannelAdmin' onchange='ChangeApprovedFranchisesChannel(this)' >";
                            print "<option value=''>[Please select]</option>";
                              if ($channelname == 'All' ) {
                                  print "<option id='All' value='All' selected>All</option>";  
                              }else{
                                  print "<option id='All' value='All'>All</option>";  
                              }      
                              $sql = "SELECT * FROM `channels` WHERE `type`='Franchises'";
                              $sqlres = mysqli_query($GLOBALS["link"],$sql);
                              while ($row=mysqli_fetch_assoc($sqlres)){
                                  if ($channelname == $row["name"] || $selectedchannel == $row['id']) {
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
                print "<table id='Retail'  class='quotations' style='float:right;width: 18%;height: 33px;margin-top: -7px;border:none;'>";
                    print "<tr>";
                        print "<td>";
                            print "<label style='font-weight: bold;'>Select Retail:</label>";
                        print "</td>";
                        print "<td>";
                            print "<select id='approvedretailchannelAdmin' onchange='ChangeApprovedRetailChannel(this)' >";
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
              print "</div>";
              print "<br/><br/>";

              if ($GLOBALS['system_user']->hasPermission('view_quotes')) {
                  $sql = "SELECT * FROM `quotes` where approved=1";
                  $sql.= $this->getCriteria($selectedchannel);
                  $sql.= " ORDER BY `last_updated` DESC";
                  $sqlres = mysqli_query($GLOBALS["link"],$sql);
                  $row = mysqli_fetch_assoc($sqlres);
                  if(empty($row['customer_id'])){
                      print "No Approved quotes";
                  }else{  
                      print '
                          <head>
                              <script type="text/javascript" language="javascript" class="init">
                              $(document).ready(function() {
                                  $("#approved").dataTable( {
                                      columnDefs: [ {
                                          targets: [ 0 ],
                                          orderData: [ 0, 1 ]
                                      }, {
                                          targets: [ 1 ],
                                          orderData: [ 1, 0 ]
                                      }, {
                                          targets: [ 4 ],
                                          orderData: [ 4, 0 ]
                                      } ]
                                  } );
                              } );
                              </script>
                          </head>
                          <body>
                              <table id="approved" class="display" cellspacing="3" width="100%">
                                  <thead>
                                      <tr>
                                          <th width="120">&nbsp;Quote Details</th>
                                          <th>Quote Ref</th>
                                          <th>Date</th>
                                          <th>Location</th>
                                          <th>Customer</th>
                                          <th>Cellphone</th>
                                          <th>Created By</th>                 
                                      </tr>
                                  </thead>
                                  <tbody>';
                      $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
                      $sqlres = mysqli_query($GLOBALS["link"],$sql);
                      $row = mysqli_fetch_assoc($sqlres);
                      $channeltype = $row['type'];

                      if ($channeltype == "Commercial" || $channeltype =="Franchises"){
                          $suffix="comm";
                      }else {
                          $suffix="";
                      }
                      $sql = "SELECT * FROM `quotes` where approved=1";
                      $sql.= $this->getCriteria($selectedchannel);
                      
                      $sql.= " ORDER BY `last_updated` DESC";
                      $sqlres = mysqli_query($GLOBALS["link"],$sql);
                      while($row = mysqli_fetch_assoc($sqlres)) {
                          $quote = new Quote($row["id"]);
                          $customer = new Customer($quote->CustomerID);
                          print "<tr>";
                          print "<td align='center' bgcolor='#0faa17'>";
                          print "<a href='quotes/editquote".$suffix.".php?id=".$row["id"]."&reurl=completed_quotations' target='_blank'><span class='ui-button-text'>View Quote</span></a>";
                          print "</td>";
                           print "<td align='center'>".$quote->QuoteReferenceNo."</td>";
                          print "<td align='center'>".date("Y-m-d", strtotime($quote->DateCreated))."</td>";
                          print "<td align='center'>";
                          if ($row["lat"]!=0){
                          print "<a href='http://maps.google.com?q=".$row["lat"].",".$row["lng"]."' target='_blank'><span class='ui-button-text'>View Location</span>";
                          }
                          print "</td>";
                          print "<td align='center'>".$customer->Surname.", ".$customer->Name."</td>";
                          print "<td align='center'>".$customer->CellPhone."</td>";
                          print "<td align='center'>".$quote->CreatedByUserName."</td>";
                          print "</tr>";
                      }
                      print '</tbody>
                              </table>
                          </body>
                          ';
                  }
              }else{
                  print "<p>You do not have permission to view quotes.</p>";
              }
          }else{

              print "<div class='classy_table'>";
              print "&nbsp;&nbsp;&nbsp;<span style='font-size:14px;font-weight:bold;'>Approved Quotes</span>";
              print "</div>";
              print "<br/><br/>";

              if ($GLOBALS['system_user']->hasPermission('view_quotes')) {
                  $sql = "SELECT * FROM `quotes` where approved=1";
                  $sql.= $this->getCriteria();
                  $sql.= " ORDER BY `last_updated` DESC";
                  $sqlres = mysqli_query($GLOBALS["link"],$sql);
                  $row = mysqli_fetch_assoc($sqlres);
                  if(empty($row['customer_id'])){
                      print "No Approved quotes";
                  }else{  
                      print '
                          <head>
                              <script type="text/javascript" language="javascript" class="init">
                              $(document).ready(function() {
                                  $("#approved").dataTable( {
                                      columnDefs: [ {
                                          targets: [ 0 ],
                                          orderData: [ 0, 1 ]
                                      }, {
                                          targets: [ 1 ],
                                          orderData: [ 1, 0 ]
                                      }, {
                                          targets: [ 4 ],
                                          orderData: [ 4, 0 ]
                                      } ]
                                  } );
                              } );
                              </script>
                          </head>
                          <body>
                              <table id="approved" class="display" cellspacing="3" width="100%">
                                  <thead>
                                      <tr>
                                          <th width="120">&nbsp;Quote Details</th>
                                          <th>Quote Ref</th>
                                          <th>Date</th>
                                          <th>Location</th>
                                          <th>Customer</th>
                                          <th>Cellphone</th>
                                          <th>Created By</th>                 
                                      </tr>
                                  </thead>
                                  <tbody>';
                      $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
                      $sqlres = mysqli_query($GLOBALS["link"],$sql);
                      $row = mysqli_fetch_assoc($sqlres);
                      $channeltype = $row['type'];

                      if ($channeltype == "Commercial" || $channeltype =="Franchises"){
                          $suffix="comm";
                      }else {
                          $suffix="";
                      }
                      $sql = "SELECT * FROM `quotes` where approved=1";
                      $sql.= $this->getCriteria();
                      
                      $sql.= " ORDER BY `last_updated` DESC";
                      $sqlres = mysqli_query($GLOBALS["link"],$sql);
                      while($row = mysqli_fetch_assoc($sqlres)) {
                          $quote = new Quote($row["id"]);
                          $customer = new Customer($quote->CustomerID);
                          print "<tr>";
                          print "<td align='center' bgcolor='#0faa17'>";
                          print "<a href='quotes/editquote".$suffix.".php?id=".$row["id"]."&reurl=completed_quotations' target='_blank'><span class='ui-button-text'>View Quote</span></a>";
                          print "</td>";
                           print "<td align='center'>".$quote->QuoteReferenceNo."</td>";
                          print "<td align='center'>".date("Y-m-d", strtotime($quote->DateCreated))."</td>";
                          print "<td align='center'>";
                          if ($row["lat"]!=0){
                          print "<a href='http://maps.google.com?q=".$row["lat"].",".$row["lng"]."' target='_blank'><span class='ui-button-text'>View Location</span>";
                          }
                          print "</td>";
                          print "<td align='center'>".$customer->Surname.", ".$customer->Name."</td>";
                          print "<td align='center'>".$customer->CellPhone."</td>";
                          print "<td align='center'>".$quote->CreatedByUserName."</td>";
                          print "</tr>";
                      }
                      print '</tbody>
                              </table>
                          </body>
                          ';
                  }
              }else{
                  print "<p>You do not have permission to view quotes.</p>";
              }
          }
        }

        public function printed_quotations() {

          $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
          $sqlres = mysqli_query($GLOBALS["link"],$sql);
          $row = mysqli_fetch_assoc($sqlres);
          $channeltype = $row['type'];

          if($GLOBALS['system_user']->isSuperAdmin){
              print "<div class='classy_table'>";
              print "&nbsp;&nbsp;&nbsp;<span style='font-size:14px;font-weight:bold;'>Printed Quotes</span>";
              //Channels
              $channelname = "All";
              if (!empty($array["Channelname"])) {
                  $channelname = $array["Channelname"];
              }

              $selectedchannel = "All";
              if (!empty($array["Channel"])) {
                  $selectedchannel = $array["Channel"];
              }

              if ($channeltype == "Commercial") {
                print "<table id='Commercial' class='quotations' style='float:right;width: 20%;height: 33px;margin-top: -7px;border:none;'>";
                    print "<tr>";
                        print "<td>";
                            print "<label style='font-weight: bold;'>Select Commercial:</label>";
                        print "</td>";
                        print "<td>";
                                print "<select id='printedcommercialchannelAdmin' onchange='ChangePrintedCommercialChannel(this)' >";
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
                print "<table id='Franchises' class='quotations' style='float:right;width: 18%;height: 33px;margin-top: -7px;border:none;'>";
                    print "<tr>";
                        print "<td>";
                            print "<label style='font-weight: bold;'>Select Franchise(s):</label>";
                        print "</td>";
                        print "<td>";
                            print "<select id='printedfranchiseschannelAdmin' onchange='ChangePrintedFranchisesChannel(this)' >";
                            print "<option value=''>[Please select]</option>";
                              if ($channelname == 'All' ) {
                                  print "<option id='All' value='All' selected>All</option>";  
                              }else{
                                  print "<option id='All' value='All'>All</option>";  
                              }      
                              $sql = "SELECT * FROM `channels` WHERE `type`='Franchises'";
                              $sqlres = mysqli_query($GLOBALS["link"],$sql);
                              while ($row=mysqli_fetch_assoc($sqlres)){
                                  if ($channelname == $row["name"] || $selectedchannel == $row['id']) {
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
                print "<table id='Retail' class='quotations' style='float:right;width: 18%;height: 33px;margin-top: -7px;border:none;'>";
                    print "<tr>";
                        print "<td>";
                            print "<label style='font-weight: bold;'>Select Retail:</label>";
                        print "</td>";
                        print "<td>";
                            print "<select id='printedretailchannelAdmin' onchange='ChangePrintedRetailChannel(this)' >";
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
              print "</div>";
              print "<br/><br/>";

              if ($GLOBALS['system_user']->hasPermission('view_quotes')) {
                  $sql = "SELECT * ";
                  $sql .= " FROM `quotes`";
                  $sql .= " INNER JOIN `quote_submissions` ON `quote_submissions`.subID = `quotes`.id WHERE `approved` = 1  AND `printed` = 'Yes'";
                  $sql.= $this->getCriteria($selectedchannel);
                  $sql.= " ORDER BY `last_updated` DESC";
                  $sqlres = mysqli_query($GLOBALS["link"],$sql);
                  $row = mysqli_fetch_assoc($sqlres);
                  if(empty($row['customer_id'])){
                      print "No Printed quotes";
                  }else{  
                      print '
                          <head>
                              <script type="text/javascript" language="javascript" class="init">
                              $(document).ready(function() {
                                  $("#printed").dataTable( {
                                      columnDefs: [ {
                                          targets: [ 0 ],
                                          orderData: [ 0, 1 ]
                                      }, {
                                          targets: [ 1 ],
                                          orderData: [ 1, 0 ]
                                      }, {
                                          targets: [ 4 ],
                                          orderData: [ 4, 0 ]
                                      } ]
                                  } );
                              } );
                              </script>
                          </head>
                          <body>
                              <table id="printed" class="display" cellspacing="3" width="100%">
                                  <thead>
                                      <tr>
                                          <th width="120">&nbsp;Quote Details</th>
                                          <th>Quote Ref</th>
                                          <th>Date</th>
                                          <th>Location</th>
                                          <th>Customer</th>
                                          <th>Cellphone</th>
                                          <th>Created By</th>                 
                                      </tr>
                                  </thead>
                                  <tbody>';
                      $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
                      $sqlres = mysqli_query($GLOBALS["link"],$sql);
                      $row = mysqli_fetch_assoc($sqlres);
                      $channeltype = $row['type'];

                      if ($channeltype == "Commercial" || $channeltype =="Franchises"){
                          $suffix="comm";
                      }else {
                          $suffix="";
                      }
                      $sql = "SELECT * ";
                      $sql .= " FROM `quotes`";
                      $sql .= " INNER JOIN `quote_submissions` ON `quote_submissions`.subID = `quotes`.id WHERE `approved` = 1  AND `printed` = 'Yes'";
                      $sql.= $this->getCriteria($selectedchannel);
                      $sql.= " ORDER BY `last_updated` DESC";
                      $sqlres = mysqli_query($GLOBALS["link"],$sql);

                      while($row = mysqli_fetch_assoc($sqlres)) {
                          $quote = new Quote($row["id"]);
                          $customer = new Customer($quote->CustomerID);
                          print "<tr>";
                          print "<td align='center' bgcolor='#0faa17'>";
                          print "<a href='quotes/editquote".$suffix.".php?id=".$row["id"]."&reurl=completed_quotations' target='_blank'><span class='ui-button-text'>View Quote</span></a>";
                          print "</td>";
                           print "<td align='center'>".$quote->QuoteReferenceNo."</td>";
                          print "<td align='center'>".date("Y-m-d", strtotime($quote->DateCreated))."</td>";
                          print "<td align='center'>";
                          if ($row["lat"]!=0){
                          print "<a href='http://maps.google.com?q=".$row["lat"].",".$row["lng"]."' target='_blank'><span class='ui-button-text'>View Location</span>";
                          }
                          print "</td>";
                          print "<td align='center'>".$customer->Surname.", ".$customer->Name."</td>";
                          print "<td align='center'>".$customer->CellPhone."</td>";
                          print "<td align='center'>".$quote->CreatedByUserName."</td>";
                          print "</tr>";
                      }
                      print '</tbody>
                              </table>
                          </body>
                          ';
                  }
              }else{
                  print "<p>You do not have permission to view quotes.</p>";
              }
          }else{
              print "<div class='classy_table'>";
              print "&nbsp;&nbsp;&nbsp;<span style='font-size:14px;font-weight:bold;'>Printed Quotes</span>";
              print "</div>";
              print "<br/><br/>";

              if ($GLOBALS['system_user']->hasPermission('view_quotes')) {
                  $sql = "SELECT * ";
                  $sql .= " FROM `quotes`";
                  $sql .= " INNER JOIN `quote_submissions` ON `quote_submissions`.subID = `quotes`.id WHERE `approved` = 1  AND `printed` = 'Yes'";
                  $sql.= $this->getCriteria();
                  $sql.= " ORDER BY `last_updated` DESC";
                  $sqlres = mysqli_query($GLOBALS["link"],$sql);
                  $row = mysqli_fetch_assoc($sqlres);
                  if(empty($row['customer_id'])){
                      print "No Printed quotes";
                  }else{  
                      print '
                          <head>
                              <script type="text/javascript" language="javascript" class="init">
                              $(document).ready(function() {
                                  $("#printed").dataTable( {
                                      columnDefs: [ {
                                          targets: [ 0 ],
                                          orderData: [ 0, 1 ]
                                      }, {
                                          targets: [ 1 ],
                                          orderData: [ 1, 0 ]
                                      }, {
                                          targets: [ 4 ],
                                          orderData: [ 4, 0 ]
                                      } ]
                                  } );
                              } );
                              </script>
                          </head>
                          <body>
                              <table id="printed" class="display" cellspacing="3" width="100%">
                                  <thead>
                                      <tr>
                                          <th width="120">&nbsp;Quote Details</th>
                                          <th>Quote Ref</th>
                                          <th>Date</th>
                                          <th>Location</th>
                                          <th>Customer</th>
                                          <th>Cellphone</th>
                                          <th>Created By</th>                 
                                      </tr>
                                  </thead>
                                  <tbody>';
                      $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
                      $sqlres = mysqli_query($GLOBALS["link"],$sql);
                      $row = mysqli_fetch_assoc($sqlres);
                      $channeltype = $row['type'];

                      if ($channeltype == "Commercial" || $channeltype =="Franchises"){
                          $suffix="comm";
                      }else {
                          $suffix="";
                      }
                      $sql = "SELECT * ";
                      $sql .= " FROM `quotes`";
                      $sql .= " INNER JOIN `quote_submissions` ON `quote_submissions`.subID = `quotes`.id WHERE `approved` = 1  AND `printed` = 'Yes'";
                      $sql.= $this->getCriteria();
                      $sql.= " ORDER BY `last_updated` DESC";
                      $sqlres = mysqli_query($GLOBALS["link"],$sql);

                      while($row = mysqli_fetch_assoc($sqlres)) {
                          $quote = new Quote($row["id"]);
                          $customer = new Customer($quote->CustomerID);
                          print "<tr>";
                          print "<td align='center' bgcolor='#0faa17'>";
                          print "<a href='quotes/editquote".$suffix.".php?id=".$row["id"]."&reurl=completed_quotations' target='_blank'><span class='ui-button-text'>View Quote</span></a>";
                          print "</td>";
                           print "<td align='center'>".$quote->QuoteReferenceNo."</td>";
                          print "<td align='center'>".date("Y-m-d", strtotime($quote->DateCreated))."</td>";
                          print "<td align='center'>";
                          if ($row["lat"]!=0){
                          print "<a href='http://maps.google.com?q=".$row["lat"].",".$row["lng"]."' target='_blank'><span class='ui-button-text'>View Location</span>";
                          }
                          print "</td>";
                          print "<td align='center'>".$customer->Surname.", ".$customer->Name."</td>";
                          print "<td align='center'>".$customer->CellPhone."</td>";
                          print "<td align='center'>".$quote->CreatedByUserName."</td>";
                          print "</tr>";
                      }
                      print '</tbody>
                              </table>
                          </body>
                          ';
                  }
              }else{
                  print "<p>You do not have permission to view quotes.</p>";
              }
          }
        }

        public function pending_quotations($array) {

          $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
          $sqlres = mysqli_query($GLOBALS["link"],$sql);
          $row = mysqli_fetch_assoc($sqlres);
          $channeltype = $row['type'];

          if($GLOBALS['system_user']->isSuperAdmin){

              print "<div class='classy_table'>";
              print "&nbsp;&nbsp;&nbsp;<span style='font-size:14px;font-weight:bold;'>Pending Quotes</span>";
              //Channels
              $channelname = "All";
              if (!empty($array["Channelname"])) {
                  $channelname = $array["Channelname"];
              }

              $selectedchannel = "All";
              if (!empty($array["Channel"])) {
                  $selectedchannel = $array["Channel"];
              }

              if ($channeltype == "Commercial") {
                print "<table id='Commercial' class='quotations' style='float:right;width: 20%;height: 33px;margin-top: -7px;border:none;'>";
                    print "<tr>";
                        print "<td>";
                            print "<label style='font-weight: bold;'>Select Commercial:</label>";
                        print "</td>";
                        print "<td>";
                                print "<select id='pendingcommercialchannelAdmin' onchange='ChangePendingCommercialChannel(this)' >";
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
                print "<table id='Franchises' class='quotations' style='float:right;width: 18%;height: 33px;margin-top: -7px;border:none;'>";
                    print "<tr>";
                        print "<td>";
                            print "<label style='font-weight: bold;'>Select Franchise(s):</label>";
                        print "</td>";
                        print "<td>";
                            print "<select id='pendingfranchiseschannelAdmin' onchange='ChangePendingFranchisesChannel(this)' >";
                            print "<option value=''>[Please select]</option>";
                              if ($channelname == 'All' ) {
                                  print "<option id='All' value='All' selected>All</option>";  
                              }else{
                                  print "<option id='All' value='All'>All</option>";  
                              }      
                              $sql = "SELECT * FROM `channels` WHERE `type`='Franchises'";
                              $sqlres = mysqli_query($GLOBALS["link"],$sql);
                              while ($row=mysqli_fetch_assoc($sqlres)){
                                  if ($channelname == $row["name"] || $selectedchannel == $row['id']) {
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
                print "<table id='Retail' class='quotations' style='float:right;width: 18%;height: 33px;margin-top: -7px;border:none;'>";
                    print "<tr>";
                        print "<td>";
                            print "<label style='font-weight: bold;'>Select Retail:</label>";
                        print "</td>";
                        print "<td>";
                            print "<select id='pendingretailchannelAdmin' onchange='ChangePendingRetailChannel(this)' >";
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
              print "</div>";
              print "<br/><br/>";

              if ($GLOBALS['system_user']->hasPermission('view_quotes')) {
                  $sql = "SELECT * FROM `quotes` where approved=0 and complete=1";
                  $sql.= $this->getCriteria($selectedchannel);
                  $sql .= " ORDER BY `last_updated` ASC";
                  $sqlres = mysqli_query($GLOBALS["link"],$sql);
                  $row = mysqli_fetch_assoc($sqlres);
                  if(empty($row['customer_id'])){
                      print "No Pending quotes";
                  }else{
                       print '
                          <head>
                              <script type="text/javascript" language="javascript" class="init">
                              $(document).ready(function() {
                                  $("#pending").dataTable( {
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
                              <table id="pending" class="display" cellspacing="3" width="100%">
                                  <thead>
                                      <tr>
                                          <th width="120">Date</th>
                                          <th>Quote Ref</th>
                                          <th>Location</th>
                                          <th>Customer</th>
                                          <th>Cellphone</th>
                                          <th>Created By</th> 
                                          <th width="60">&nbsp;View</th>                 
                                      </tr>
                                  </thead>
                                  <tbody>';
                                  $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
                                  $sqlres = mysqli_query($GLOBALS["link"],$sql);
                                  $row = mysqli_fetch_assoc($sqlres);
                                  $channeltype = $row['type'];

                                  if ($channeltype == "Commercial" || $channeltype =="Franchises"){
                                      $suffix="comm";
                                  }else {
                                      $suffix="";
                                  }
                                  $sql = "SELECT * FROM `quotes` where approved=0 and complete=1";
                                  $sql.= $this->getCriteria($selectedchannel);
                                  $sql .= " ORDER BY `date_created` DESC";
                                  $sqlres = mysqli_query($GLOBALS["link"],$sql);

                                  while($row = mysqli_fetch_assoc($sqlres)) {
                                      $quote = new Quote($row["id"]);
                                      $customer = new Customer($quote->CustomerID);

                                      print "<tr>";
                                      if(strtotime($quote->LastUpdated) > strtotime($quote->DateCreated)){
                                        print "<td align='center'  bgcolor='#525656' style='font-weight:bold;'>";
                                      } else{
                                        print "<td align='center'>";//modified
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
                                      print "<td align='center'><a href='quotes/editquote".$suffix.".php?id=".$row["id"]."&reurl=completed_quotations&".time()."' target='_blank'><span class='ui-button-text'><img  width='20' height='20' title='View Details'  src='modules/dashboard/images/search.ico' /></span></a></td>";
                                      print "</tr>";
                                  }
                          print '</tbody>
                              </table>
                          </body>
                          ';
                  }
              }else{
                  print "<p>You do not have permission to view quotes.</p>";
              }
          }else{

              print "<div class='classy_table'>";
              print "&nbsp;&nbsp;&nbsp;<span style='font-size:14px;font-weight:bold;'>Pending Quotes</span>";
              print "</div>";
              print "<br/><br/>";

              if ($GLOBALS['system_user']->hasPermission('view_quotes')) {
                  $sql = "SELECT * FROM `quotes` where approved=0 and complete=1";
                  $sql.= $this->getCriteria();
                  $sql .= " ORDER BY `last_updated` ASC";
                  $sqlres = mysqli_query($GLOBALS["link"],$sql);
                  $row = mysqli_fetch_assoc($sqlres);
                  if(empty($row['customer_id'])){
                      print "No Pending quotes";
                  }else{
                       print '
                          <head>
                              <script type="text/javascript" language="javascript" class="init">
                              $(document).ready(function() {
                                  $("#pending").dataTable( {
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
                              <table id="pending" class="display" cellspacing="3" width="100%">
                                  <thead>
                                      <tr>
                                          <th width="120">Date</th>
                                          <th>Quote Ref</th>
                                          <th>Location</th>
                                          <th>Customer</th>
                                          <th>Cellphone</th>
                                          <th>Created By</th> 
                                          <th width="60">&nbsp;View</th>                 
                                      </tr>
                                  </thead>
                                  <tbody>';
                                  $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
                                  $sqlres = mysqli_query($GLOBALS["link"],$sql);
                                  $row = mysqli_fetch_assoc($sqlres);
                                  $channeltype = $row['type'];

                                  if ($channeltype == "Commercial" || $channeltype =="Franchises"){
                                      $suffix="comm";
                                  }else {
                                      $suffix="";
                                  }
                                  $sql = "SELECT * FROM `quotes` where approved=0 and complete=1";
                                  $sql.= $this->getCriteria();
                                  $sql .= " ORDER BY `date_created` DESC";
                                  $sqlres = mysqli_query($GLOBALS["link"],$sql);

                                  while($row = mysqli_fetch_assoc($sqlres)) {
                                      $quote = new Quote($row["id"]);
                                      $customer = new Customer($quote->CustomerID);

                                      print "<tr>";
                                      if(strtotime($quote->LastUpdated) > strtotime($quote->DateCreated)){
                                        print "<td align='center'  bgcolor='#525656' style='font-weight:bold;'>";
                                      } else{
                                        print "<td align='center'>";//modified
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
                                      print "<td align='center'><a href='quotes/editquote".$suffix.".php?id=".$row["id"]."&reurl=completed_quotations&".time()."' target='_blank'><span class='ui-button-text'><img  width='20' height='20' title='View Details'  src='modules/dashboard/images/search.ico' /></span></a></td>";
                                      print "</tr>";
                                  }
                          print '</tbody>
                              </table>
                          </body>
                          ';
                  }
              }else{
                  print "<p>You do not have permission to view quotes.</p>";
              }
          }
        }

        function DebugPrint($obj) {
            print "<pre>";
            print_r($obj);
            print "</pre>";
        }
        
        function PrintPostArray($array) {
            print "$('#iteminput').html(\"";
            foreach($array as $key=>$value){
                print "<b>".$key.": </b>".$value."<br>";
            }
            print "\");\n";
        }
        
        public function view_quotations ($array) {
            print "\n<!-- scripts , code below be eval()ed by javascript -->\n";
            print "document.location.href = 'quotes/login.php';\n";
            
            exit();
        }
 
    }

?>
