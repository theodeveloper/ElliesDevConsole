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
require_once("inc/config.php");
require_once("inc/functions.php");
//require_once("inc/quote.class.php");
//require_once("inc/quoteitem.class.php");
//require_once("inc/techtype.class.php");
//require_once("inc/techitem.class.php");
require_once("inc/product.class.php");

LoginCheck("techinfo.php");
$openindialog = false;
require_once("inc/header.php");

if (!empty($_GET["newid"])) {
    if (!$openindialog) {
        PrintNavBar("Product Information", true, "techinfo.php");
    }
    $product = new Product($_GET["newid"]);
    print "<ul data-role='listview' data-divider-theme='a' data-inset='true'>";
    print "<li data-role='list-divider' role='heading'>".$product->Product."</li>";
    print "<li>";
    $product->PrintItemInfo("media/");
    print "</li>";
    print "</ul>";
    //print "<br><a href='techeditor.php?productid=".$_GET["newid"]."&oldid=".$_GET["oldid"]."' data-role='button' data-inline='true' data-icon='forward' data-theme='b' data-ajax='false'>Edit</a>";
}elseif (!empty($_GET["oldid"])) {
    //===New Products===
    if (!$openindialog) {
        PrintNavBar("Product Information", true, "techinfo.php");
    }
    $newtechtype = "";
    $sql = "SELECT `new_products`.`id`, `new_products`.`product`, `new_products`.`new_technology_type` FROM `products_mapping`";
    $sql.= " INNER JOIN `new_products` ON `products_mapping`.`newid` = `new_products`.`id`";
    $sql.= " WHERE `products_mapping`.`oldid` = '".mysqli_real_escape_string($GLOBALS["link"],$_GET["oldid"])."'";
    $sql.= " AND NOT (`new_products`.`related_to` IS NULL)";
    $sql.= " ORDER BY `new_products`.`new_technology_type`, `new_products`.`product`";
    $sqlres = mysqli_query($GLOBALS["link"],$sql);
    while($row = mysqli_fetch_assoc($sqlres)) {
        if ($newtechtype != $row["new_technology_type"]) {
            $newtechtype = $row["new_technology_type"];
            //$items[] = array("url" => "", "label" => $newtechtype, "jsfunc" => "", "divider" => true);
        }
        $items[] = array("url" => "techinfo.php?oldid=".$_GET["oldid"]."&newid=".$row["id"], "label" => $row["product"], "jsfunc" => "", "divider" => false);
    }
    PrintList("Select Replacement Product", $items, $openindialog);
}elseif (!empty($_GET["tech_type"])) {
    //===Old Products===
    if (!$openindialog) {
        PrintNavBar("Product Information", true, "techinfo.php");
    }
    $prevsub = "";
    //$sql = "SELECT `id`, `product`, `product_type` FROM `old_products` WHERE `tech_type` = '".mysqli_real_escape_string($GLOBALS["link"],$_GET["tech_type"])."' ORDER BY `tech_type`";
    $sql = "SELECT `id`, `product`, `product_type` FROM `old_products` WHERE `id` IN (SELECT `oldid` FROM `products_mapping` WHERE `tech_type` = '".mysqli_real_escape_string($GLOBALS["link"],$_GET["tech_type"])."') ORDER BY type_for_mobi_site, product_type, product, old_kwh";
    $sqlres = mysqli_query($GLOBALS["link"],$sql);
    while($row = mysqli_fetch_assoc($sqlres)) {
        $subs = explode("-", $row["product"]);
        $icount = sizeof($subs);
        $subs = $subs[0];
        $subs = trim($subs);
        $product = $row["product"];
        if ($prevsub != $subs) {
            $prevsub = $subs;
            if ($icount > 1) {
                $items[] = array("url" => "", "label" => $subs, "jsfunc" => "", "divider" => true);                
            }
        }
        if ($icount > 1) {
            $product = substr($product, strpos($product, "-") + 1);
            $product = trim($product);
        }
        $items[] = array("url" => "techinfo.php?oldid=".$row["id"], "label" => $product, "jsfunc" => "", "divider" => false);
    }
    PrintList("Select Existing Product", $items, $openindialog);
}else{
    //===Tech Types===
    if (!$openindialog) {
        PrintNavBar("Product Information", true, "index.php");
    }
    $sql = "SELECT DISTINCT `tech_type` FROM `products_mapping` ORDER BY `tech_type`";
    $sqlres = mysqli_query($GLOBALS["link"],$sql);
    while($row = mysqli_fetch_assoc($sqlres)) {
        $items[] = array("url" => "techinfo.php?tech_type=".$row["tech_type"], "label" => $row["tech_type"], "jsfunc" => "", "divider" => false);
    }
    PrintList("Select Product Type", $items, $openindialog);
}

require_once("inc/footer.php");
?>

