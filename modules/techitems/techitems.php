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

register_menu("Techitems", "submenu", "Products",
    Array(
        Array("title" => "View Product Info",  "location" => "view_tech_info",   "acl" => "view_tech_info"),
        Array("title" => "Manage Product Info",  "location" => "edit_tech_info",   "acl" => "edit_tech_info")
    )
);


//require_once("../../inc/techitem.class.php");
//register_permission("System Errors Permissions", "errors",        "Access to Errors module");
//register_permission("System Errors Permissions", "resolve_error", "Mark errors as resolved");
//register_permission("System Errors Permissions", "report_error",  "Report errors");
//register_permission("System Errors Permissions", "view_errors",   "View Reported errors");
register_permission("Tech Items Permissions", "view_tech_info",  "View Tech Items");
register_permission("Tech Items Permissions", "edit_tech_info", "Edit Tech Info");
register_permission("Tech Items Permissions", "import_tech_items",   "Import Tech Items");


class Techitems {
    
    private $items_per_page;
    
    public function __construct () {
        $this->items_per_page = Settings::getSetting(2);
    }

    public function getCriteria(){
        $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
        $sqlres = mysqli_query($GLOBALS["link"],$sql);
        $row = mysqli_fetch_assoc($sqlres);
        $channeltype = $row['type'];


        if ($channeltype == "Commercial" || $channeltype =="Franchises"){
            if ($GLOBALS['system_user']->isSuperAdmin == FALSE){
                $cond =" AND `quotes`.created_by in (SELECT id from system_users where store_id = ".$GLOBALS['system_user']->storeID.")";
            } else {
                $cond = " AND `quotes`.created_by in (SELECT id from system_users where branch_id =".$GLOBALS['system_user']->branchID.")";
            }
        } else {
            if ($GLOBALS['system_user']->isSuperAdmin == FALSE){
                $cond =" AND `quotes`.created_by in (SELECT id from system_users where store_id = ".$GLOBALS['system_user']->storeID.")";
            } else {
                $cond = " AND `quotes`.created_by in (SELECT id from system_users where branch_id IN (SELECT id from branches where channel=".$GLOBALS['system_user']->retailChannel."))";
            }
        } 
      return $cond;       
    }

    public function getProduct($id=""){
        $values = explode(',', $id);
        $newValues = array();
        for($i=0;$i<count($values);$i++){
            if($values[$i] !==""){
                if(!in_array($values[$i], $newValues)){
                  $newValues[] = $values[$i]; 
                }
            }
        }
        $num = 0;
        $occurences = array_count_values($newValues);

        $max = $occurences[$newValues[0]];
        $maxid =$newValues[0];
        for($i=0;$i<count($newValues);$i++){
            if($max < $occurences[$newValues[$i]]){
                $max = $occurences[$newValues[$i]];
                $maxid = $newValues[$i];
            } 
        }

        $sql = "SELECT `product` FROM `new_products`where `id`=". $maxid."";
        $sqlres = mysqli_query($GLOBALS["link"],$sql); 
        while($row = mysqli_fetch_assoc($sqlres)) {
            $product = $row['product'];
        }
        return $product;
    }

    //Manage Tech Information
    public function edit_tech_info ($array) {
        $selected = "SPOTLIGHT";
        if (!empty($array["TechType"])) {
            $selected = $array["TechType"];
        }

        print "<div class='classy_table'>";
        print "&nbsp;&nbsp;&nbsp;<span style='font-size:14px;font-weight:bold;'>Edit Product Info</span>";
        print "</div>";
        print "<br/><br/>";
        print "<label style='font-size:12px;font-weight:bold;'>Select Product Category:</label>";
        print "&nbsp;&nbsp;&nbsp;<select onchange='EditChangeTypeSelection(this)'>";
        $table="new_products";
            $sql = "SELECT DISTINCT `class_of_product` FROM $table where channel=".$GLOBALS['system_user']->retailChannel." and deleted=0 ORDER BY `class_of_product`";
            $sqlres = mysqli_query($GLOBALS["link"],$sql);
            while($row = mysqli_fetch_assoc($sqlres)) {
                if ($selected == $row["class_of_product"]) {
                    print "<option value='".$row["class_of_product"]."' selected>".$row["class_of_product"]."</option>";
                }else{
                    print "<option value='".$row["class_of_product"]."'>".$row["class_of_product"]."</option>";                
                }
            }
            print "</select>";
        print "<br/><br/><br/>";
        print '
                <head>
                    <script type="text/javascript" language="javascript" class="init">
                    $(document).ready(function() {
                        $("#manageproducts").dataTable( {
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
                    <table id="manageproducts" class="display" cellspacing="3" width="100%">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Product</th>
                                <th>Related To</th>
                                <th>Uses KWh</th>
                                <th>Price</th> 
                                <th>View</th> 
                            </tr>
                        </thead>
                        <tbody>';
                    $sql = "SELECT * FROM $table WHERE  channel=".$GLOBALS['system_user']->retailChannel." and  deleted=0 and `class_of_product` = '".mysqli_real_escape_string($GLOBALS["link"],$selected)."' ORDER BY `product`";
                    $sqlres = mysqli_query($GLOBALS["link"],$sql);
                    while($row = mysqli_fetch_assoc($sqlres)) {
                        $bfound = true;
                        print "<tr>";
                        print "<td align='center'>".$row["code"]."</td>";
                        print "<td align='center'>".$row["product"]."</td>";
                        print "<td align='center'>".$row["related_to"]."</td>";
                        if ($row["kw_yn"]) {
                            $row["kw_yn"] = "Yes";
                        }else{
                            $row["kw_yn"] = "No";
                        }
                        print "<td align='center'>".$row["kw_yn"]."</td>";
                        $row["price"] = "R ".number_format($row["price_a"], 2);
                        print "<td align='center'>".$row["price"]."</td>";
                        print "<td align='center'><a id='viewproduct' href='#' onclick='ViewProduct(".$row['id'].","."\"".$selected."\"".")'>
                            <img  width='20' height='20' title='View Details'  src='modules/techitems/images/search.ico' /></a></td>";
                        print "</tr>";
                    }   
            print '</tbody>
                </table>
            </body>';
    }

    //View Tech Information
    public function view_tech_info ($array) {
        $selected = "SPOTLIGHT";
        if (!empty($array["TechType"])) {
            $selected = $array["TechType"];
        }

        print "<div class='classy_table'>";
        print "&nbsp;&nbsp;&nbsp;<span style='font-size:14px;font-weight:bold;'>Product Info</span>";
        print "</div>";
        print "<br/><br/>";
        $table = "new_products";
        $sql = "SELECT DISTINCT `class_of_product` FROM $table where channel=".$GLOBALS['system_user']->retailChannel." and deleted=0 ORDER BY `class_of_product`";
        $sqlres = mysqli_query($GLOBALS["link"],$sql);
        $countRow = mysqli_num_rows($sqlres);
        if($countRow > 0){
            print "<label style='font-size:12px;font-weight:bold;'>Select Product Category:</label>";
            print "&nbsp;&nbsp;&nbsp;<select onchange='ChangeTypeSelection(this)'>";
            $table="new_products";
            $sql = "SELECT DISTINCT `class_of_product` FROM $table where channel=".$GLOBALS['system_user']->retailChannel." and deleted=0 ORDER BY `class_of_product`";
            $sqlres = mysqli_query($GLOBALS["link"],$sql);
            $count = mysqli_num_rows($sqlres);
            while($row = mysqli_fetch_assoc($sqlres)) {
                if ($selected == $row["class_of_product"]) {
                    print "<option value='".$row["class_of_product"]."' selected>".$row["class_of_product"]."</option>";
                }else{
                    print "<option value='".$row["class_of_product"]."'>".$row["class_of_product"]."</option>";                
                }
            }
            print "</select>";
        } 
        print "<br/><br/><br/>";
        print '
                <head>
                    <script type="text/javascript" language="javascript" class="init">
                    $(document).ready(function() {
                        $("#productscategory").dataTable( {
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
                    <table id="productscategory" class="display" cellspacing="3" width="100%">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Product</th>
                                <th>Related To</th>
                                <th>Uses KWh</th>
                                <th>Price</th>              
                            </tr>
                        </thead>
                        <tbody>';
                    $sql = "SELECT * FROM $table WHERE  channel=".$GLOBALS['system_user']->retailChannel." and  deleted=0 and `class_of_product` = '".mysqli_real_escape_string($GLOBALS["link"],$selected)."' ORDER BY `product`";
                    $sqlres = mysqli_query($GLOBALS["link"],$sql);
                    while($row = mysqli_fetch_assoc($sqlres)) {
                        $bfound = true;
                        print "<tr>";
                        print "<td align='center'>".$row["code"]."</td>";
                        print "<td align='center'>".$row["product"]."</td>";
                        print "<td align='center'>".$row["related_to"]."</td>";
                        if ($row["kw_yn"]) {
                            $row["kw_yn"] = "Yes";
                        }else{
                            $row["kw_yn"] = "No";
                        }
                        print "<td align='center'>".$row["kw_yn"]."</td>";
                        $row["price"] = "R ".number_format($row["price_a"], 2);
                        print "<td align='center'>".$row["price"]."</td>";
                        print "</tr>";
                    }
            print '</tbody>
                </table>
            </body>';

        //Popular products per category 
        print "<div style='margin-left: 30px;display:inline-block'>";   
            print "<br/>"; 
            if($countRow > 0){         
                print "<h2 style='font-size:16px;font-weight:bold;color:#8dd03f;margin-top: 32px;margin-left: 2px;'>Popular Products per Category</h2>";
                print "<br/>";
                $arrCategory = array();
                $arrProducts = array();
                $arrProductPerCategory = array();
                $arrQuotes = array();

                $arrProductNum = array();
            
                $arrQuotesProducts = array();

                //Get Products
                $sql = "SELECT DISTINCT `class_of_product` FROM `new_products`where channel=".$GLOBALS['system_user']->retailChannel." and deleted=0 ORDER BY `class_of_product`";
                $sqlres = mysqli_query($GLOBALS["link"],$sql);
                while($row = mysqli_fetch_assoc($sqlres)) {
                    $arrCategory[] = ucwords(strtolower($row["class_of_product"]));             
                }

                //Get all quotes in channel
                $sql = "SELECT `id` FROM `quotes` where approved=1 OR approved=0";
                $sql.= $this->getCriteria();
                $sql .=" ORDER BY `last_updated` DESC";
                $sqlres = mysqli_query($GLOBALS["link"],$sql);
                while($row = mysqli_fetch_assoc($sqlres)){
                    $arrQuotes[] = $row['id'];
                }

                //Get all product ID
                for($i=0;$i<count($arrQuotes);$i++){
                    $sql = "SELECT `new_product_id` FROM `quote_items`where `quote_id`='".$arrQuotes[$i]."'";
                    $sqlres = mysqli_query($GLOBALS["link"],$sql);
                    while($row = mysqli_fetch_assoc($sqlres)) {
                        $arrProducts[] = $row['new_product_id'];
                    }
                }
                //Get all Product Per Category
                for($i=0;$i<count($arrCategory);$i++){
                    $id = "";
                    for($p=0;$p<count($arrProducts);$p++){
                        $sql = "SELECT `id` FROM `new_products`where `id`=".strtoupper($arrProducts[$p])." AND `class_of_product`='".$arrCategory[$i]."' AND  channel=".$GLOBALS['system_user']->retailChannel." AND deleted=0";
                        $sqlres = mysqli_query($GLOBALS["link"],$sql); 
                        while($row = mysqli_fetch_assoc($sqlres)) {
                            $id .= $row['id'].",";
                        }            
                    } 

                    if($id !==""){
                       $arrProductPerCategory[] =  $this->getProduct($id); 
                    }else{
                        $arrProductPerCategory[] = "None";
                    }       
                }       

                $tableProducts = "<table cellpadding='2' style='border: 2px solid black;margin-left: 4px;border-collapse: collapse;'width='100%'>";                        
                                for($i=0;$i<count($arrCategory);$i++){
                                    $tableProducts .= "<tr>
                                                            <td style='border:1px solid black;font-weight:bold;' bgcolor='#828989'  width='180'>".$arrCategory[$i]."</td>
                                                            <td style='border:1px solid black;' width='400' align='center'>".$arrProductPerCategory[$i]."</td>
                                             
                                                        </tr>";         
                                }               
                $tableProducts .=" </table>";
                echo $tableProducts;
            }
        print "</div>";     
    }

    //View Tech Item
    public function view_tech_item ($array) {
        print "<div class='classy_table'>";
        print "&nbsp;&nbsp;&nbsp;<span style='font-size:14px;font-weight:bold;'>Product Info</span>";
        print "</div>";
        print"<br/>";
        print '<img  width="350" height="350" src="modules/techitems/images/product2_icon.png" style="float:left; padding-right:100px;margin-top:80px"/>';
        $table="new_products";
        $sql = "SELECT * FROM $table WHERE  channel=".$GLOBALS['system_user']->retailChannel." AND id=".$array["id"]." AND deleted=0 LIMIT 1";
        $sqlres = mysqli_query($GLOBALS["link"],$sql);
        $productprice = 0;
        $table ='';
        while($row = mysqli_fetch_assoc($sqlres)) {
                $productprice =number_format($row["price_a"], 2);
                print '<table id="editproduct" class="display" cellspacing="10">
                        <tbody>
                            <tr>
                                <td><img src="modules/techitems/images/ellies_icon.png" /></td>
                                <td class="heading">Code</td>
                                <td class="quote_vals">'.$row["code"].'</td>
                            </tr>
                            <tr>
                                <td align="center" valign="middle"><img src="modules/techitems/images/ellies_icon.png" /></td>
                                <td class="heading">Name</td>
                                <td class="quote_vals">'.$row["product"].'</td>
                            </tr>
                            <tr>
                                <td align="center" valign="middle"><img src="modules/techitems/images/ellies_icon.png" /></td>
                                <td class="heading">Category</td>
                                <td class="quote_vals">'.$row["class_of_product"].'</td>
                            </tr>
                            <tr>
                                <td align="center" valign="middle"><img src="modules/techitems/images/ellies_icon.png" /></td>
                                <td class="heading">Replacement Kwh</td> 
                                <td class="quote_vals">'.$row["replacement_kwh"].'</td>
                            </tr>
                            <tr>
                                <td align="center" valign="middle"><img src="modules/techitems/images/ellies_icon.png" /></td>
                                <td class="heading">Supplier</td> 
                                <td class="quote_vals">'.$row["supplier"].'</td>
                            </tr>
                            <tr>
                                <td align="center" valign="middle"><img src="modules/techitems/images/ellies_icon.png" /></td>
                                <td class="heading">Temperature</td> 
                                <td class="quote_vals">'.$row["temperature"].'</td>
                            </tr>
                            <tr>  
                                <td align="center" valign="middle"><img src="modules/techitems/images/ellies_icon.png" /></td>                        
                                <td class="heading">Fitting Type</td>
                                <td class="quote_vals">'.$row["fitting_type"].'</td>
                            </tr> 
                            <tr>
                                <td align="center" valign="middle"><img src="modules/techitems/images/ellies_icon.png" /></td>
                                <td class="heading">Lumen Output</td> 
                                <td class="quote_vals">'.$row["lumen_output"].'</td>
                            </tr>
                            <tr> 
                                <td align="center" valign="middle"><img src="modules/techitems/images/ellies_icon.png" /></td>                                               
                                <td class="heading">Guarantee Term</td> 
                                <td class="quote_vals">'.$row["guarantee_term"].'</td>
                            </tr>
                            <tr>
                               <td align="center" valign="middle"><img src="modules/techitems/images/ellies_icon.png" /></td>                          
                               <td class="heading">Price(R)</td>
                               <td class="quote_vals"><input type="text" class="quote_vals" style="width: 98px; padding-left: 4px;" id="price" name="price" value="'.$productprice.'"></td>                         
                            </td>
                           </tr>
                        </tbody>
                </table>';
        
                print "<div style='text-align:center;margin-top:20px;'>
                        <button type='button' class='btnSave' onclick='SaveProduct("."\"".$row['code']."\"".",".$productprice.")'>Save</button>
                        <button type='button' class='btnSave' onclick='Back("."\"".$array['category']."\"".")'>Back</button>
                       </div>";
        }  
    }
    //Save Tech Item
    public function save_tech_item_info($array) {
        print "<div class='classy_table'>";
        print "&nbsp;&nbsp;&nbsp;<span style='font-size:14px;font-weight:bold;'>Product Info</span>";
        print "</div>";
        print"<br/>";
        print '<img  width="350" height="350" src="modules/techitems/images/product2_icon.png" style="float:left; padding-right:100px;margin-top:80px"/>';
        $sql = "SELECT * FROM new_products WHERE  channel=".$GLOBALS['system_user']->retailChannel." AND code='".$array["code"]."' AND deleted=0 LIMIT 1";
        $sqlres = mysqli_query($GLOBALS["link"],$sql);
        $class_of_product = "";

        while($row = mysqli_fetch_assoc($sqlres)) {
            print '<table id="editproduct" class="display" cellspacing="10">
                    <tbody>;
                        <tr>
                            <td><img src="modules/techitems/images/ellies_icon.png" /></td>
                            <td class="heading">Code</td>
                            <td class="quote_vals">'.$row["code"].'</td>
                        </tr>
                        <tr>
                            <td align="center" valign="middle"><img src="modules/techitems/images/ellies_icon.png" /></td>
                            <td class="heading">Name</td>
                            <td class="quote_vals">'.$row["product"].'</td>
                        </tr>
                        <tr>
                            <td align="center" valign="middle"><img src="modules/techitems/images/ellies_icon.png" /></td>
                            <td class="heading">Category</td>
                            <td class="quote_vals">'.$row["class_of_product"].'</td>
                        </tr>
                        <tr>
                            <td align="center" valign="middle"><img src="modules/techitems/images/ellies_icon.png" /></td>
                            <td class="heading">Replacement Kwh</td> 
                            <td class="quote_vals">'.$row["replacement_kwh"].'</td>
                        </tr>
                        <tr>
                            <td align="center" valign="middle"><img src="modules/techitems/images/ellies_icon.png" /></td>
                            <td class="heading">Supplier</td> 
                            <td class="quote_vals">'.$row["supplier"].'</td>
                        </tr>
                        <tr>
                            <td align="center" valign="middle"><img src="modules/techitems/images/ellies_icon.png" /></td>
                            <td class="heading">Temperature</td> 
                            <td class="quote_vals">'.$row["temperature"].'</td>
                        </tr>
                        <tr>  
                            <td align="center" valign="middle"><img src="modules/techitems/images/ellies_icon.png" /></td>                        
                            <td class="heading">Fitting Type</td>
                            <td class="quote_vals">'.$row["fitting_type"].'</td>
                        </tr> 
                        <tr>
                            <td align="center" valign="middle"><img src="modules/techitems/images/ellies_icon.png" /></td>
                            <td class="heading">Lumen Output</td> 
                            <td class="quote_vals">'.$row["lumen_output"].'</td>
                        </tr>
                        <tr> 
                            <td align="center" valign="middle"><img src="modules/techitems/images/ellies_icon.png" /></td>                                               
                            <td class="heading">Guarantee Term</td> 
                            <td class="quote_vals">'.$row["guarantee_term"].'</td>
                        </tr>
                        <tr>
                            <td align="center" valign="middle"><img src="modules/techitems/images/ellies_icon.png" /></td>                          
                            <td class="heading">Price(R)</td>  
                            <td class="quote_vals">'.number_format($array["price"],2).'</td>                         
                        </tr>
                    </tbody>
            </table>';

            $class_of_product =$row["class_of_product"];

            print "<div style='text-align:center;margin-top:20px;'>
                        <button type='button' class='btnSave' onclick='Back("."\"".$row['class_of_product']."\"".")'>Back</button>
                       </div>";
        }

        if ($GLOBALS['system_user']->hasPermission('edit_tech_info')) {
            $code = $array["code"];
            $price = $array["price"];
            $table="new_products";
            $sql = "UPDATE $table SET";
            $sql .= " `last_updated` =NOW(),";
            $sql .= " `price_a` = '".$price."'";
            $sql .= " WHERE channel=".$GLOBALS['system_user']->retailChannel." AND `code` ='".$code."' AND deleted=0 LIMIT 1";
            $result = mysqli_query($GLOBALS["link"],$sql);

            if($result) { 
                $query  = "INSERT INTO `product_price_list_log` (date_created,product_code,class_of_product,price,channel,edited_by) VALUES (";
                $query  .= "NOW(),";
                $query  .= " '".$code. "',";
                $query  .= " '".$class_of_product. "',";
                $query  .= " '".$price. "',";
                $query  .= " '".$GLOBALS['system_user']->retailChannel. "',";
                $query  .= " '".$GLOBALS['system_user']->id. "'";
                $query  .= ") ";
                $result = mysqli_query($GLOBALS["link"],$query);
                if($result) {
                    print "<label style='color:green'>Save Tech Item Inforamtion</label>";
                    logAction("Edited Tech Item:$code");
                }
            } else {
               print "<label style='color:red'>Unable to save Tech Item Inforamtion!</label>";
            }  
            exit();
        }else{
            print "You do not have permission to perform this action.";
            exit();            
        }
    }
    //=========================================================================
    public function view_type_items($array) {
        $sql = "SELECT * FROM `tech_items` WHERE  channel=".$GLOBALS['system_user']->retailChannel." and `deleted` = 0 AND `tech_type_id` = '".mysqli_real_escape_string($GLOBALS["link"],$array["id"])."'";
        $sqlres = mysqli_query($GLOBALS["link"],$sql);
        $bfound = false;

        print "<h3 style='font-size:14px;font-weight:bold;'>Product Types</h3>";
        print "<div class='classy_table'>";
        print "<table border='0' cellpadding='3' cellspacing='0'>";

        while($row = mysqli_fetch_assoc($sqlres)) {
            $bfound = true;
            print "<tr>";
            print "<td width='20'><button onclick=\"AJAXCallModule('Techitems','view_item_info', 'id=".$row["id"]."')\"><span class='ui-icon ui-icon-search'></span></button>";
            print "<td>".$row["new_product"]."</td>";
            print "</tr>";
        }
        print "</table>";
        print "<br>&nbsp;<button onclick=\"AJAXCallModule('Techitems','view_techitems', '')\"><span class='ui-button-text'>Back</span></button>";
        print "</div>";
        if (!$bfound) {
            print "No product(s) found.";
        }
    }
    
    public function view_item_info($array) {
        //require("../../quotes/inc/product.class.php");
        $product = new Product(0, $array["code"]);
        
        print "<h3 style='font-size:14px;font-weight:bold;'>Product Information</h3>";
        if ($GLOBALS['system_user']->hasPermission('edit_tech_items')) {
            print "<div class='classy_table' id='productinfo'>";
            print "<table border='0' cellpadding='3' cellspacing='0'>";
            
            print "<tr><th colspan='2'>".$product->Product." (".$array["code"].")</th></tr>";
            print "<tr><td colspan='2'><textarea id='tech_info' name='tech_info' rows='15' cols='80' style='width: 80%'>";
            $nfo = file_get_contents("quotes/temp/".$array["code"].".htm");
            print $nfo;
            print "</textarea>";
            print "</td></tr>";
            print "<tr><td colspan='2'>";
            $functionstr = "AJAXCallModule('Techitems', 'save_item_info', 'code=".$array["code"]."&TechType=".$array["TechType"]."&info=' + encodeURIComponent(tinyMCE.activeEditor.getContent()))";
            print "<button onclick=\"".$functionstr."\"><span class='ui-button-icon ui-icon-search'></span><span class='ui-button-text'>Save</span></button>";
            print "</td></tr>";
            print "</table>";
            
            print "</div>";
            
            $this->BindTinyMCE();
        }
        exit();
    }
    
    public function save_item_info($array) {
        if ($GLOBALS['system_user']->hasPermission('edit_tech_info')) {
            //print $array["TechType"];
            //exit();
            $code = $array["code"];
            $type = $array["TechType"];
            $filename = "quotes/temp/".$code.".htm";
            $info = $array["info"];
            $info = urldecode($info);
            if (file_exists($filename)) {
                unlink($filename);
            }
            file_put_contents($filename, $info);
           
            $array = NULL;
            $array["code"] = $code;
            $array["TechType"] = $type;
            $this->edit_tech_info($array);
            
            exit();
        }else{
            print "You do not have permission to perform this action.";
            exit();            
        }
    }
    
    public function delete_item($array) {
        if ($GLOBALS['system_user']->hasPermission('delete_tech_items')) {
            //require("../../quotes/inc/techitem.class.php");
            $techitem = new TechItem($array["id"]);
            $techtypeid = $techitem->TechTypeID;
            $techitem->Delete();
            $array = NULL;
            $array["id"] = $techitem->TechTypeID;
            $this->view_type_items($array);
            exit();
        }else{
            print "You do not have permission to perform this action.";
            exit();
        }
    }
    
    function BindTinyMCE() {
        ?>
        <script type="text/javascript">
        tinyMCE.init({
            // General options
            mode : "textareas",
            theme : "advanced",
            plugins : "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave,visualblocks",
        
            // Theme options
            theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
            theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
            theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
            theme_advanced_buttons4 : "",
            theme_advanced_toolbar_location : "top",
            theme_advanced_toolbar_align : "left",
            theme_advanced_statusbar_location : "bottom",
            theme_advanced_resizing : true,
        
            // Example content CSS (should be your site CSS)
            content_css : "css/content.css",
        
            // Drop lists for link/image/media/template dialogs
            template_external_list_url : "lists/template_list.js",
            external_link_list_url : "lists/link_list.js",
            external_image_list_url : "quotes/imagelist.php",
            media_external_list_url : "lists/media_list.js",
        
            // Style formats
            style_formats : [
                {title : 'Bold text', inline : 'b'},
                {title : 'Red text', inline : 'span', styles : {color : '#ff0000'}},
                {title : 'Red header', block : 'h1', styles : {color : '#ff0000'}},
                {title : 'Example 1', inline : 'span', classes : 'example1'},
                {title : 'Example 2', inline : 'span', classes : 'example2'},
                {title : 'Table styles'},
                {title : 'Table row 1', selector : 'tr', classes : 'tablerow1'}
            ],
        
            // Replace values for the template plugin
            template_replace_values : {
                username : "Some User",
                staffid : "991234"
            }
        });
        </script>
        <?php 
    }
    
    function BindApplicationFormDialog($readonly = false) {
        print "$('#ViewTechDetails').dialog({\n";
        print "    title: 'Application Details',\n";
        print "    dialogClass : 'ViewCustomerApplicationDialog',\n";
        print "    autoOpen: false,\n";
        print "    width: 700,\n";
        print "    height: 450,\n";
        print "    modal: true,\n";
        print "    buttons: {\n";
        /*
        print "        'Customer Details': function() {\n";
        print "            $('#house_details').hide();\n";
        print "            $('#application_details').hide();\n";
        print "            $('#installer_details').hide();\n";
        print "            $('#choose_installer').hide();\n";
        print "            $('#customer_details').show();\n";
        print "        },\n";
        print "        'House Details': function() {\n";
        print "            $('#customer_details').hide();\n";
        print "            $('#application_details').hide();\n";
        print "            $('#installer_details').hide();\n";
        print "            $('#choose_installer').hide();\n";
        print "            $('#house_details').show();\n";
        print "        },\n";
        print "        'Installer Details': function() {\n";
        print "            $('#customer_details').hide();\n";
        print "            $('#application_details').hide();\n";
        print "            $('#house_details').hide();\n";
        print "            $('#choose_installer').hide();\n";
        print "            $('#installer_details').show();\n";
        print "        },\n";
        print "        'Approve Application': function() {\n";
        //print "            $('#application_details').show();\n";
        print "            ApproveApplication();\n";
        print "        },\n";
        */
        print "        Close: function() {\n";
        print "            $( this ).dialog( 'close' );\n";
        print "        }\n";
        print "    }\n";
        print "});\n";
    }
}
?>
