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
    
    session_start();

    include_once("inc/config.php");
    include_once("inc/functions.php");
    include_once("inc/sql.php");
    get_session();
    define('SITE_TITLE', getSetting(1));
    include_once("inc/modules.php");
    include_once("inc/html.php");
    include_once("inc/system_user.php");
    include_once("libs/fpdf/fpdf.php");
  
    // If not authenticated...
	if (!isset($_SESSION["userid"])){
	header("location:./login.php");
}
    if (!is_authenticated()) {
        if (isset($_POST['aj']) && $_POST['aj'] == 1) {
            print "\n<!-- scripts , code below be eval()ed by javascript -->\nalert('You have been signed out! Please relogin');";
        } else {
            header("location: login.php");
            exit(1);
        }
    }
    
    // Get user object
    $system_user = new userType($GLOBALS['SESSION']['username']);
    // If not valid, we have a non continuing error...
    if ($system_user->valid != 1) {
        print "Unable to find system user!";
        exit(1);
    }
    
    // Ajax calls get handled by the modules here
    run_called_modules();

    // HTML the front end out
    html_start();
    idleTimeout();
    
    
    draw_menu();

    print "<div id=\"divContentHeader\" name=\"divContentHeader\" style=\"font-size: 12px;\" class='ui-content'>Welcome, ".ucwords($system_user->first." ".$system_user->last).(!empty($system_user->email) ? " (".$system_user->email.")" : "")."<img id='notification' name='notification' src='images/notification.png' height='25' width='25' style='float:right;padding-right: 4%;'/></div>";
    //print "<div id=\"divContentHeader\" name=\"divContentHeader\" style='float:center;'>";
    //print "&nbsp;&nbsp;&nbsp;<a onclick=\"document.location.href='index.php';\" style='cursor: pointer;'><img src='images/home.png' width='30' align='absmiddle' />&nbsp;<span style='font: bold 12px/32px Arial,sans-serif; color: #3E5706; text-shadow: 1px 1px 1px rgba(255, 255, 255, 0.2); text-decoration: underline;'>Go Home</span></a>\n";
    //print "<a onclick=\"document.location.href='login.php?logout=1';\" style='float: right; cursor: pointer; margin-right: 5%;'><span style='font: bold 12px/32px Arial,sans-serif; color: #3E5706; text-shadow: 1px 1px 1px rgba(255, 255, 255, 0.2); text-decoration: underline;'>Sign out</span></a>\n";
    //print "</div>";

    //Notification
    $sql = "SELECT * FROM notifications WHERE status ='View' AND branch ='".$GLOBALS['system_user']->branchID."'";
    $sqlres = mysqli_query($GLOBALS["link"],$sql);
    $count = mysqli_num_rows($sqlres);
    $notification = "";
    while($row = mysqli_fetch_assoc($sqlres)) {
        $query = "SELECT attachment FROM news_letter_types WHERE  id =".$row['news_letter_type'];
        $sqlresult = mysqli_query($GLOBALS["link"],$query);
        $rowResult = mysqli_fetch_assoc($sqlresult);
        $attachment = $rowResult['attachment'];
        $notification .="<p>";
                            if($row['news_letter_type'] !== "0"){
                                $notification .="<a target='_blank' href='viewnewletterlayout.php?newsletter_type=".$row['news_letter_type']."&id=".$row['id']."' style='text-decoration:none'>";
                            }else{
                                 $notification .="<a target='_blank' href='viewnotification.php?notify=".$row['id']."&id=".$row['id']."' style='text-decoration:none'>";
                            }

                            if($row['news_letter_type'] !== "0"){
                               $notification .=" <label style='color:#8dd03f'>Newsletter:</label> ".$row['title']."<br/>";
                            }else{
                               $notification .=" <label style='color:#8dd03f'>Title:</label>".$row['title']."<br/>";
                            }
                            $notification .="<label style='color:#a5a5a5'>".date('Y-m-d',strtotime($row['date_created']))."</label><br/>";

                    if($row['news_letter_type'] !== "0"){
                        if($attachment  !=="")$notification .="</a></p><a href='modules/newsletter/attachments/".$attachment."' style='float:right;'>View attachment</a>";
                    }else{
                        $notification .="</a></p>";
                    }

    }
    if($notification == ""){
        $notification ="(No Notifications available)";
        $count  = 0;
    }
    print "<input type='hidden' id='notification' style='width: 200px' value='".$count."'/>";
    print "<div id='dialog' style='width:400px;height:250px;overflow-y:auto;' title='Below are {$count} notification(s) for your attention.'>".$notification."</div>";


    
    print "<div id=\"divContentContainer\" name=\"divContentContainer\" style='float: left;'>";
    print " <div id=\"divContentLoader\" name=\"divContentLoader\" style='float: left;'></div>";
    print " <div id=\"divContent\" name=\"divContent\"></div>";
    print "</div>";
    
    print "<script type='text/javascript'>\n";
    print "\$(document).ready(function() {\n";
    
    print "   \$('#divContentLoader').hide();";
    print "   \$('#divContentLoader').html(\"<img src='images/loader.gif' alt='loading' border='0'> <span>Loading...</span>\");";
    print "   dwidth = ((\$(window).width() - 100) - \$('#sidemenu').width());\n";
    print "   \$('#divContentContainer').width (  dwidth+\"px\" );\n";
    print "   dheight = (\$(window).height() - 100);\n";
    print "   \$('#divContentContainer').height (  dheight+\"px\" );\n";
    print "   \$('#divContentLoader').css (  \"margin-top\", Math.round((dheight / 2),0)+\"px\" );\n";
    
    print "   \$(window).resize(function() {\n";
    print "     dwidth = ((\$(window).width() - 100) - \$('#sidemenu').width());\n";
    print "     \$('#divContentContainer').width (  dwidth+\"px\" );\n";
    print "     dheight = (\$(window).height() - 100);\n";
    print "     \$('#divContentContainer').height (  dheight+\"px\" );\n";
    print "     \$('#divContentLoader').css (  \"margin-top\", Math.round((dheight / 2),0)+\"px\" );\n";
    print "   });\n";
    
    print "   if(\$('#menuitem0 a').attr(\"name\") == \"parentMenu\") {\n";
    print "      AJAXCallModule(jQuery('#menuitem0 a').attr('id'), 'main', '');\n";
    print "   }\n";
    print "   else {\n";
    print "    //  \$('#menuitem0 ul li a:first').addClass('active-sub');\n";
    print "    //  eval(\$('#menuitem0 ul li a:first').attr('href'));\n";
    print "   }\n";
    
    print " });\n";
    print "</script>";
    html_end();
?>