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
require_once("inc/customer.class.php");
require_once("inc/quote.class.php");
require_once("inc/techitem.class.php");
require_once("inc/functions.php");
require_once("../inc/system_user.php");

LoginCheck("quotes.php");
$sysuser = new userType($_SESSION["userid"]);
if (!$sysuser->hasPermission("view_quotes")) {
    header("location: index.php?nopermission=View%20quotes");
    exit();
}

require_once("inc/header.php");

function PrintLink($id) {
    $sysuser = new userType($_SESSION["userid"]);
    $quote = new Quote($id);
    if (!$quote->Complete && $sysuser->hasPermission("edit_quotes")) {
        print "<noBr>";
        //print "<a href='editquote.php?id=".$id."'>Edit</a>";
        print "<a href='editquote.php?id=".$id."' data-role='button' data-inline='true' data-icon='arrow-r' data-mini='true'>Edit</a>";
        print "&nbsp;&nbsp;";
        //print "<a href='deletequote.php?id=".$id."'>Delete</a>";
        print "<a href='deletequote.php?id=".$id."' data-role='button' data-inline='true' data-icon='delete' data-mini='true'>Remove</a>";
        print "</noBr>";
    }else{
        PrintViewLink($id);
    }
}

function PrintViewLink($id) {
    print "<a href='editquote.php?id=".$id."' data-role='button' data-inline='true' data-icon='arrow-r' data-mini='true'>View</a>";
}

function TestIt($value) {
    $customer = new Customer($value);
    print $customer->Email.", ".$customer->CellPhone;
}

function CustomerID() {
    $return = 0;
    if (!empty($_GET["customerid"])) {
        $return = $_GET["customerid"];
    }elseif(!empty($_SESSION["Customerid"])) {
        $return = $_SESSION["Customerid"];
    }
    return $return;
}

if (GET("complete") == "1") {
    PrintNavBar("Completed Quotes", true);
    $customcolumns[] = array("caption" => "Customer", "command" => "TestIt(\$row[\"hidden_customer_id\"])");
    PrintDBResults("SELECT `id`, `ref` AS `Reference No`, `date_created` AS `Created`, `customer_id` AS `hidden_customer_id`, IF(`complete`, 'Yes', 'No') AS `Complete`, `property` AS `Property` FROM `quotes` WHERE `complete` = 1", "id", "PrintViewLink(\$row[\"id\"])", $customcolumns);
}else{
    PrintNavBar("Quotes", true);
    print "<a href='editquote.php?id=0&customerid=".CustomerID()."&isnew=1' data-role='button' data-inline='true' data-icon='check' data-theme='b' data-ajax='false'>New Quote</a>";
    $customcolumns[] = array("caption" => "Customer", "command" => "TestIt(\$row[\"hidden_customer_id\"])");
    PrintDBResults("SELECT `id`, `ref` AS `Reference No`, `date_created` AS `Created`, `customer_id` AS `hidden_customer_id`, IF(`complete`, 'Yes', 'No') AS `Complete`, `property` AS `Property` FROM `quotes` WHERE `customer_id` = '".mysqli_real_escape_string($GLOBALS["link"],CustomerID())."'", "id", "PrintLink(\$row[\"id\"])", $customcolumns);
    //print "<br><a href='editquote.php?id=0&customerid=".SESSION("customerid")."'>New Quote</a>";
}


/*
$sql = "SELECT `id`, `date_created` AS `Created`, `customer_id`, IF(`complete`, 'Yes', 'No') AS `Complete` FROM `quotes` WHERE `customer_id` = '".mysqli_real_escape_string($GLOBALS["link"],$_SESSION["customerid"])."'";


print "<table width='100%' cellspacing='0' cellpadding='3' border='1'>";
print "<tr>";
print "<th class='ui-body-a' width='50'>&nbsp;</th>";
print "<th class='ui-body-a'>Created</th>";
print "<th class='ui-body-a'>Complete</th>";
print "<th class='ui-body-a'>Price</th>";
print "</tr>";

$sqlres = mysqli_query($GLOBALS["link"],$sql);
while($row = mysqli_fetch_assoc($sqlres)) {
    print "<tr>";
    if (!$quote->Complete) {
        print "<td class='ui-body-c'><noBr>";
        print "<a href='quoteitem.php?id=".$row["id"]."&quoteid=".$quoteid."' data-role='button' data-rel='dialog' data-close-btn-text='close' data-inline='true' data-icon='arrow-r' data-mini='true'>Edit</a>";
        print "<a href='quoteitem.php?id=".$row["id"]."&quoteid=".$quoteid."&step=remove' data-role='button' data-inline='true' data-icon='delete' data-mini='true'>Remove</a>";
        print "</noBr></td>";        
    }
    $tmpprice = $row["qty"] * $row["price"];
    $totalprice += $tmpprice;
    print "<td class='ui-body-c'>".$row["qty"]."</td>";
    print "<td class='ui-body-c'>".$row["tech_type"]."</td>";
    print "<td class='ui-body-c'>".$row["option"]."</td>";
    print "<td class='ui-body-c'>R ".number_format(($row["qty"] * $row["price"]), 2)."</td>";
    print "</tr>";    
}
print "</table>";
*/




require_once("inc/footer.php");
?>

