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
    
    include_once("inc/config.php");
    include_once("inc/functions.php");
    include_once("inc/sql.php");
    include_once("inc/html.php");
    include_once("inc/modules.php");
    define('SITE_TITLE', getSetting(1) . " : Login");
    
    session_start();
    get_session();
    if (@$_GET['logout']) {
        delete_session();
    }
	
    if (!empty($_POST['tpm_userid'])) {
        $query = "SELECT * FROM `system_users` WHERE `username` = '".mysqli_real_escape_string($GLOBALS["link"],$_POST['tpm_userid'])."' AND `password` = SHA1('".mysqli_real_escape_string($GLOBALS["link"],$_POST['tpm_passwd'])."') AND `active` = 1";
       // $_GET['err']= $query;
        $result = mysqli_query($GLOBALS["link"],$query);
        if (mysqli_num_rows($result) == 0) {
            $_GET['err'] = "Invalid Username / Password";
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
                    $_SESSION["userid"] = $SESSION['username'];
                    update_system_user_lastlogin($SESSION['username']);
                    save_session();
					$_SESSION['allowDataFilter'] = "allowed";

                     if(isset($_GET["id"])){
                       if ($_GET["id"] !==""){
                        header("location: quotes/editquotecomm.php?id=".$_GET["id"]."&reurl=completed_quotations"); 
                        exit(1);
                       }   
                    }else{
                        header("location: index.php");
                        exit(1);
                    }
                    
                } else {
                    $_GET['err'] = "System currently down for site maintenance";
                }
            } else {
                $_GET['err'] = "Account is disabled";
            }
        }
    }

    $id = "";
    if(isset($_GET['id'])){
        $id = $_GET['id'];  
    }

    $hidefield = false;

    $encrypt = "";
    if(isset($_GET['encrypt'])){
        $encrypt = $_GET['encrypt']; 
        $query = "SELECT * FROM recovery_user_passwords where user_id='".$id."' AND encrypt_code='".$encrypt."'";
        $result = mysqli_query($GLOBALS["link"],$query);
        $row = mysqli_fetch_assoc($result);
        $Results = mysqli_fetch_array($result);
        if(count($Results)>=1){
            $message2 = "Your password changed sucessfully";

            $query  = "UPDATE `system_users` SET ";
            $query  .= " `password` = '" .$row['password']."'";
            $query  .= " WHERE `id` = ".$row['user_id'];
            $result = mysqli_query($GLOBALS["link"],$query);
            $hidefield = true;
        }else{
          $_GET['err'] = 'Invalid key please try again. <a href="reset.php>Forget Password?</a>';
        } 
    }
    
    html_start();
?>
<div id='loginpagecontent' name='loginpagecontent'>
    <div id='loginpage' name='loginpage'>
        <br/>
        <img id='logo' name='logo' src='images/logo.png' />
        <br/>
        <?php //=SITE_TITLE?>
        <?php 
            print "<form name='frm' id='frm' action='' method='POST' onSubmit='Javascript: if (checkPrePostLogin() == false) {return false;}'>";
            print "<BR>";
            print "<table cellpadding='0' cellspacing='0' border='0' style='width: 100%;'>";
            
            print "<tr>";
            print " <th colspan='2' align='left'><BR>Please login below:</th>";
            print "</tr>";
            
            print "<tr>";
            print " <td><label for='tpm_userid'>Username:<label></td>";
            print " <td><input type='text' name='tpm_userid' id='tpm_userid' value='".(isset($_POST['tpm_userid']) ? $_POST['tpm_userid'] : "")."' style='font-size:12px;' /></td>";
            print "</tr>";
            
            print "<tr>";
            print " <td><label for='tpm_passwd'>Password:</label></td>";
            print " <td><input type='password' name='tpm_passwd' id='tpm_passwd' value='' style='font-size:12px;' /></td>";
            print "</tr>";
            
            print "<tr>";
            print " <td colspan='2' id='errMsgTD'>".(!empty($_GET['err']) ? "*** ". $_GET['err'] : "")."</td>";
            print "</tr>";

            if($hidefield){
                print "<tr>";
                print " <td colspan='2' id='crrMsgTD'>".$message2."</td>";
                print "</tr>";
            }
            
            print "<tr>";
            print " <td colspan='2' style='text-align:center;'><input type='submit' name='login' id='login' value='Login'></td>";
            print "</tr>";

            print "<tr>";
             print " <td style='text-align:left;'><a href='https://www.dropbox.com/s/t3cdxadhj2cddku/ellies-debug.apk?dl=0' style='color:#797979'>Download App</a></td>";
              print " <td style='text-align:right;'> <a href='recoverypassword/reset.php' style='color:#797979;'>Forgot Password?</a></td>";
            print "</tr>";
            
            print "</table>";
            print "</form>";
        ?>
        
    </div>
</div>
<script type='text/javascript'>
    $(function() {
        $('#tpm_userid').focus();
    });
</script>

<?php 
    html_end();
?>