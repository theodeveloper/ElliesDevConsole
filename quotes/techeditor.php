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
require_once("inc/quote.class.php");
require_once("inc/quoteitem.class.php");
require_once("inc/techtype.class.php");
require_once("inc/techitem.class.php");
require_once("inc/product.class.php");

LoginCheck("techeditor.php");

$booktitle = "Ellies In-Store App";

if (POST("submit") == "Save") {
    print_r($_POST);
    //exit();
    $info = POST("elm1");
    $productid = GET("productid");
    $oldid = GET("oldid");
    //print POST("productid");
    //print mysqli_real_escape_string($GLOBALS["link"],POST("elm1"));
    $product = new Product($productid);
    $product->Information = $info;
    //$product->Save();
    $sql = "UPDATE `products` SET `item_info` = '".mysqli_real_escape_string($GLOBALS["link"],$_POST["elm1"])."' WHERE `id` = '".mysqli_real_escape_string($GLOBALS["link"],$_POST["productid"])."'";
    //mysqli_query($GLOBALS["link"],$sql);
    header("location: techinfo.php?newid=".$_POST["productid"]."&oldid=".$oldid);
    exit();
}

require_once("inc/header.php");

?>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<!-- TinyMCE -->
<script type="text/javascript" src="tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
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
    external_image_list_url : "imagelist.php",
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
<!-- /TinyMCE -->
<?php 
if (GET("productid") > 0) {
    //===Show Tech Item/Product Details===
    $techitem = new TechItem(GET("productid"));
    $product = new Product(GET("productid"));
    PrintNavBar($product->Product, true, "techinfo.php?productid=".GET("productid"));
    //print "<h3>".$techitem->Option."</h3>";
    //$sql = "ALTER TABLE `tech_items` ADD COLUMN `item_info` TEXT DEFAULT NULL";
    //mysqli_query($GLOBALS["link"],$sql);
    print "<form method='post' action='techeditor.php?productid=".GET("productid")."&oldid=".GET("oldid")."' data-ajax='false'>";
    print "<textarea id='elm1' name='elm1' rows='15' cols='80' style='width: 80%'>";
    $product->PrintItemInfo();
    print "</textarea>";
    print "<input type='submit' id='submit' name='submit' value='Save' data-role='button' data-inline='true' data-icon='check' data-theme='b' data-ajax='false'>";
    //print "<a href='te.php?id=0&customerid=".SESSION("customerid")."' data-role='button' data-inline='true' data-icon='check' data-theme='b' data-ajax='false'>New Quote</a>";
    print "<input type='hidden' id='productid' name='productid' value='".GET("productid")."'>";
    print "<input type='hidden' id='oldid' name='oldid' value='".GET("oldid")."'>";
    print "</form>";

    print "<script language='javascript'>\n";
    print "jQuery('#elm1_image').html('eelo');\n";
    print "jQuery('.mceActionPanel').html('ello');\n";
    print "</script>\n";

}
require_once("inc/footer.php");
?>

