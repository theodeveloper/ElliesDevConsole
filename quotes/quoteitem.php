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
require_once("inc/techtype.class.php");
require_once("inc/quote.class.php");
require_once("inc/quoteitem.class.php");
require_once("inc/techitem.class.php");
require_once("inc/functions.php");
require_once("../inc/system_user.php");

//$_SESSION["quoteitemid"] = 3;
//GetInputValue($_SESSION["quoteitemid"], "Daily");
//exit();
$sysuser = new userType($_SESSION["userid"]);

function PrintTechSelectOld($techtypeid, $techid = 0) {
    $sql = "SELECT `old_product` FROM `tech_items` WHERE `id` = '".mysqli_real_escape_string($GLOBALS["link"],$techid)."'";
    $sqlres = mysqli_query($GLOBALS["link"],$sql);
    while ($row = mysqli_fetch_assoc($sqlres)) {
        $oldprod = $row["old_product"];
    }
    
    $sql = "SELECT DISTINCT `tech_items`.`old_product` FROM `tech_items`";
    //$sql.= " INNER JOIN `tech_types` ON `tech_items`.`tech_type_id` = `tech_types`.`id`";
    $sql.= " WHERE `tech_items`.`tech_type_id` = '".mysqli_real_escape_string($GLOBALS["link"],$techtypeid)."'";
    $sql.= " ORDER BY `tech_items`.`old_product`";
    $sqlres = mysqli_query($GLOBALS["link"],$sql);
    
    $tmptechtype = "";
    $techtypeinstance = 0;
    print "<option value=''>[Please Select Old Product]</option>";
    while ($row = mysqli_fetch_assoc($sqlres)) {
        /*
        if ($tmptechtype != $row["tech_type"]) {
            $tmptechtype = $row["tech_type"];
            $techtypeinstance += 1;
            if ($techtypeinstance > 1) { print "</optgroup>"; }
            print "<optgroup label='".$tmptechtype."'>";
        }
        */
        if ($oldprod == $row["old_product"]) {
            print "<option value='".$row["old_product"]."' selected>".$row["old_product"]."</option>";
        }else{
            print "<option value='".$row["old_product"]."'>".$row["old_product"]."</option>";
        }
    }
    //print "</optgroup>";
}

function PrintTechSelectNew($techtypeid, $techid = 0, $oldprod = NULL) {
    //$oldprod = $techid;
    
    if ($techid > 0 && empty($oldprod)) {
        $sql = "SELECT `old_product` FROM `tech_items` WHERE `id` = '".mysqli_real_escape_string($GLOBALS["link"],$techid)."'";
        $sqlres = mysqli_query($GLOBALS["link"],$sql);
        while ($row = mysqli_fetch_assoc($sqlres)) {
            $oldprod = $row["old_product"];
        }
    }
    
    $sql = "SELECT DISTINCT `tech_items`.`id`, `tech_items`.`new_product`, `tech_types`.`tech_type` FROM `tech_items`";
    $sql.= " INNER JOIN `tech_types` ON `tech_items`.`tech_type_id` = `tech_types`.`id`";
    $sql.= " WHERE `old_product` = '".mysqli_real_escape_string($GLOBALS["link"],$oldprod)."';";
    $sqlres = mysqli_query($GLOBALS["link"],$sql);

    $tmptechtype = "";
    $techtypeinstance = 0;    
    print "<option value=''>[Please Select New Product]</option>";
    while ($row = mysqli_fetch_assoc($sqlres)) {
        if ($tmptechtype != $row["tech_type"]) {
            $tmptechtype = $row["tech_type"];
            $techtypeinstance += 1;
            if ($techtypeinstance > 1) { print "</optgroup>"; }
            print "<optgroup label='".$tmptechtype."'>";
        }

        if ($techid == $row["id"]) {
            print "<option value='".$row["id"]."' selected>".$row["new_product"]."</option>";
        }else{
            print "<option value='".$row["id"]."'>".$row["new_product"]."</option>";
        }
    }
    print "</optgroup>";
}


function PrintTechInputForm($techtypeid) {
    if ($techtypeid > 0) {
        $quoteitemid = 0;
        if (!empty($_SESSION["quoteitemid"])) {
            if ($_SESSION["quoteitemid"] > 0) {
                $quoteitemid = $_SESSION["quoteitemid"];
            }
        }
        //print "<br>quoteitemid: ".$quoteitemid;
        $quoteitem = new QuoteItem($quoteitemid);
        $quoteitem->TechTypeID = $techtypeid;
        //$techtype = new TechType($techtypeid);
        foreach($quoteitem->GetInputValues() as $key=>$value) {
            $objid = "inptfrm_".str_replace(" ", "_", $key);
            $label = str_replace("_", " ", $key);
            PrintTextBox($objid, $label, $value);
        }
    }
}

if (POST("step") == "ChangeTechTypeSelection") {
    print '$(document).bind("mobileinit", function(){
        $.extend(  $.mobile , {
        defaultPageTransition: "none"
      });
      $.mobile.dialog.prototype.options.closeBtnText = "close";
    });';

    //print "$('#techid').val('');\n";
    //print "$('#techid').text('Please Select');\n";
    
    print "$('#techidold').html(\"";
    PrintTechSelectOld(POST("id"));
    print "\");\n";
    print "$('#techidold').selectmenu('refresh');\n";

    $prodtypeid = POST("id");
    print "$('#techidnew').html('');\n";
    print "$('#techidnew').selectmenu('refresh');\n";
    
    print "$('#iteminput').html('');\n";
    print "$('#inpstuffs').hide();\n";
    print "$('#btnsave').hide();\n";
    print "$('#techselectnew').hide();\n";
    if (!empty($prodtypeid)) {
        print "$('#techselectold').slideDown();\n";
    }else{
        print "$('#techselectold').slideUp();\n";
    }
    exit();
}

if (POST("step") == "ChangeTechSelectionOld") {
    print '$(document).bind("mobileinit", function(){
        $.extend(  $.mobile , {
        defaultPageTransition: "none"
      });
      $.mobile.dialog.prototype.options.closeBtnText = "close";
    });';
    //$techitem = new TechItem(POST("id"));
    $oldprod = POST("id");
    print "$('#techidnew').html(\"";
    PrintTechSelectNew(0, 0, $oldprod);
    print "\");\n";
    print "$('#techidnew').selectmenu('refresh');\n";

    if (!empty($oldprod)) {
        print "$('#inpstuffs').hide();\n";
        print "$('#btnsave').hide();\n";
        print "$('#techselectnew').slideDown();\n";        
    }else{
        print "$('#techselectnew').slideUp();\n";
        print "$('#inpstuffs').hide();\n";
        print "$('#btnsave').hide();\n";
    }

    //print "$('#techselectold').slideDown();\n";
    exit();
}

if (POST("step") == "ChangeTechSelectionNew") {
    print '$(document).bind("mobileinit", function(){
        $.extend(  $.mobile , {
        defaultPageTransition: "none"
      });
      $.mobile.dialog.prototype.options.closeBtnText = "close";
    });';
    $techitem = new TechItem(POST("id"));
    print "$('#iteminput').html(\"";
    PrintTechInputForm($techitem->TechTypeID);
    print "\");\n";
    print "$('#iteminput').trigger('create');\n";
    $id = 0;
    $id = POST("id");
    if (!empty($id)) {
        print "$('#inpstuffs').slideDown();\n";
        print "$('#btnsave').show();\n";
    }else{
        print "$('#inpstuffs').slideUp();\n";
        print "$('#btnsave').hide();\n";        
    }
    exit();
}


if (POST("step") == "SaveItemInput") {
    $quoteid = $_SESSION["quoteid"];
    $quoteitemid = $_SESSION["quoteitemid"];

    $quoteitem = new QuoteItem($quoteitemid);
    $quoteitem->QuoteID = $quoteid;
    $quoteitem->Room = POST("inp_room");
    $quoteitem->Qty = POST("inp_qty");
    $quoteitem->TechID = POST("id");

    foreach($_POST as $field=>$value) {
        if (substr($field, 0, strlen("inptfrm_")) == "inptfrm_") {
            $field = substr($field, strlen("inptfrm_"));
            $field = str_replace("_", " ", $field);
            $quoteitem->AddInputValue($field, $value);
        }
    }

    $quoteitem->Save();
    $quoteitemid = $quoteitem->QuoteItemID;
    $_SESSION["quoteitemid"] = $quoteitemid;
    //print "$('.ui-dialog').dialog('close');\n";
    print "document.location.href = 'editquote.php?id=".$quoteid."';\n";
    exit();
}

LoginCheck("quoteitem.php");

if (GET("id") > 0 && GET("step") == "remove") {
    $quoteitem = new QuoteItem(GET("id"));
    LogDataChange($sysuser->id, "quote_items", "Item removed from quote ".$quoteitem->QuoteID, $quoteitem, null);
    $quoteitem->Delete();
    header("location: editquote.php?id=".GET("quoteid"));
}

$booktitle = "Ellies InStore App";

require_once("inc/header.php");

print "<script language='javascript'>
function ChangeTechTypeSelection(obj) {
  jQAJAXCall('quoteitem.php', 'step=ChangeTechTypeSelection&id=' + encodeURIComponent($(obj).val()));
}

function ChangeTechSelectionOld(obj) {
  jQAJAXCall('quoteitem.php', 'step=ChangeTechSelectionOld&id=' + encodeURIComponent($(obj).val()));
}

function ChangeTechSelectionNew(obj) {
  jQAJAXCall('quoteitem.php', 'step=ChangeTechSelectionNew&id=' + encodeURIComponent($(obj).val()));
}

function SaveItemInput() {
    var urldata = 'step=SaveItemInput&id=' + $('#techidnew').val();
    urldata += '&quoteitemid=' + $('#quoteitemid').val();
    urldata += '&inp_room=' + $('#inp_room').val();
    urldata += '&inp_qty=' + $('#inp_qty').val();
    $('#iteminput').find('input[type=text],select').each(function() {
        if ($(this).val() != '') {
            urldata += '&' + $(this).attr('id') + '=' + encodeURIComponent($(this).val());
        }
    });
    jQAJAXCall('quoteitem.php', urldata);
}
</script>";

//PrintNavBar($booktitle, true);

$customcolumns[] = array("caption" => "Customer", "command" => "TestIt(\$row[\"hidden_customer_id\"])");
$id = GET("id");
$quoteitem = new QuoteItem($id);
if (empty($id)) {
    $id = 0;
}

$cssvisiblestr = " style='display:none;'";
if ($id > 0) {
    $cssvisiblestr = "";
}

$_SESSION["quoteitemid"] = $id;
$_SESSION["quoteid"] = GET("quoteid");
//DebugPrint($quoteitem);

//===Build Input Form===

print "<select id='techtype' name='techtype' onchange='ChangeTechTypeSelection(this)'>";
print "<option value=''>[Please Select Product Type]</option>";
foreach(TechType::ListTechTypes() as $techtype) {
    $ttid = "";
    if (!empty($techtype["id"])) {
        $ttid = $techtype["id"];
    }
    if ($ttid == $quoteitem->TechTypeID) {
        print "<option value='".$techtype["id"]."' selected>".$techtype["tech_type"]."</option>";
    }else{
        print "<option value='".$techtype["id"]."'>".$techtype["tech_type"]."</option>";
    }
}
print "</select>";

//===Old Product Selection===
print "<div id='techselectold'".$cssvisiblestr.">";
//print "<div id='techselectold'>";
print "<select id='techidold' name='techidold' onchange='ChangeTechSelectionOld(this)'>";
if ($quoteitem->TechTypeID > 0) {
    PrintTechSelectOld($quoteitem->TechTypeID, $quoteitem->TechID);
}
print "</select>";
print "</div>";


//===New Product Selection===
print "<div id='techselectnew'".$cssvisiblestr.">";
print "<select id='techidnew' name='techidnew' onchange='ChangeTechSelectionNew(this)'>";
if ($quoteitem->TechTypeID > 0) {
    PrintTechSelectNew($quoteitem->TechTypeID, $quoteitem->TechID);
}
print "</select>";
print "</div>";

print "<div id='inpstuffs'".$cssvisiblestr.">";    

PrintTextBox("inp_room", "Room", $quoteitem->Room, "Bedroom, Bathroom etc");
PrintTextBox("inp_qty", "Qty", $quoteitem->Qty);
print "<div id='iteminput'>";
if ($quoteitem->TechID == 0) {
    $quoteitem->TechID = 1;
}
PrintTechInputForm($quoteitem->TechTypeID);
print "<input type='hidden' id='quoteitemid' name='quoteitemid' value='".GET("id")."'>";
print "</div>";

print "</div>";

//PrintDBResults("SELECT `id`, `date_created` AS `Created`, `customer_id` AS `hidden_customer_id` FROM `quotes` WHERE `id` = 1", "id", "PrintLink(\$row[\"id\"])", $customcolumns);
print "<table><tr><td width='50%'>";
print '<a href="editquote.php?id='.GET("quoteid").'" data-role="button" data-inline="true" data-icon="back">Back</a>';
print "</td><td>";
print '<a href="#"'.$cssvisiblestr.' onclick="SaveItemInput()" id="btnsave" data-role="button" data-theme="b" data-transition="fade" data-inline="true" data-icon="plus" data-ajax="false">Save</a>';
print "</td></tr></table>";
print "<div id='myPopupDiv' style='display:none;'>Item Saved</div>";
require_once("inc/footer.php");
?>

