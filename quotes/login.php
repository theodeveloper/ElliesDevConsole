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
require_once("../inc/functions.php");
require_once("../inc/system_user.php");
require_once("../modules/settings/settings.php");
get_session();
if (!empty($SESSION["username"])) {
    $_SESSION["userid"] = $SESSION["username"];
}

if (!empty($_SESSION["userid"])) {
    header("location: index.php");
    exit();
}

$loginerror = "";
if (POST("ajsubmit") == "Submit") {
    if (!empty($_POST["username"]) && !empty($_POST["password"])) {

        $query = "SELECT * FROM `system_users` WHERE `username` = '".mysqli_real_escape_string($GLOBALS["link"],$_POST["username"])."' AND `password` = SHA1('".mysqli_real_escape_string($GLOBALS["link"],$_POST["password"])."') AND `active` = 1";
        //print $query;
        //exit();
        $result = mysqli_query($GLOBALS["link"],$query);
        if (mysqli_num_rows($result) == 0) {
            $loginerror = "Invalid Username / Password";
        } else {
            $row = mysqli_fetch_assoc($result);
            if ($row['active'] == 1) {
                if (Settings::getSetting(6) == 0) {
                    $SESSION['auth']         = '1';
                    $SESSION['useragent']    = $_SERVER['HTTP_USER_AGENT'];
                    $SESSION['email']        = $row['email'];
                    $SESSION['first']        = $row['first'];
                    $SESSION['last']         = $row['last'];
                    $SESSION['username']     = $row['username'];
                    $SESSION['ip_address']   = $_SERVER['REMOTE_ADDR'];
                    
                    update_system_user_lastlogin($SESSION['username']);
                    save_session();
                    header("location: index.php");
                    exit(1);
                } else {
                    $loginerror = "System currently down for site maintenance";
                }
            } else {
                $loginerror = "Account is disabled";
            }
        }
        header("location: index.php");
    }
}

$booktitle = "Ellies InStore : Login";
require_once("inc/header.php");
PrintNavBar("Login", false);
//print "username: ".$_SESSION["userid"];
//print "<br>username: ".$SESSION["username"];
if (!empty($loginerror)) {
    print $loginerror;
}

//print "<br><a data-role='button' href='index.php' data-icon='arrow-l' data-iconpos='left' class='ui-btn-left' data-ajax='false' data-theme='a'>Back</a>";

print '<form action="login.php" method="POST">';
print "<br><div class='divlabel'><table border='0' cellpadding='0' cellspacing='0' width='100%'><tr><td width='200'>Username</td><td><input name='username' id='username' value='' type='text'></td></tr></table></div>";

print "<br><div class='divlabel'><table border='0' cellpadding='0' cellspacing='0' width='100%'><tr><td width='200'>Password</td><td><input name='password' id='password' value='' type='password'></td></tr></table></div>";
//print "<div class='divfield'><input name='username' id='username' value='' type='text'></div>";
print "<div style='clear: both;'></div>";
//PrintTextBox("username", "Username", POST("username"), "Please enter Username");
//PrintPasswordBox("password", "Password");
print '<div align="right"><input data-theme="a" value="Submit" type="submit" id="ajsubmit" name="ajsubmit" data-inline="true" style="width:300px;"></p>';

print '</form>';
require_once("inc/footer.php");
?>