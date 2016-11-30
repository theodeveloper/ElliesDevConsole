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
    
    include_once("../inc/config.php");
    include_once("../inc/functions.php");
    include_once("../inc/sql.php");
    include_once("../inc/html.php");
    include_once("../inc/modules.php");
    require_once("../inc/class.phpmailer.php");
    define('SITE_TITLE', getSetting(1) . " : Login");
    
    session_start();
    get_session();
    if (@$_GET['logout']) {
        delete_session();
    }

    function generateStrongPassword($length = 9, $add_dashes = false, $available_sets = 'luds') {
        $sets = array();
        if(strpos($available_sets, 'l') !== false)
        $sets[] = 'abcdefghjkmnpqrstuvwxyz';
        if(strpos($available_sets, 'u') !== false)
        $sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
        if(strpos($available_sets, 'd') !== false)
        $sets[] = '23456789';
        if(strpos($available_sets, 's') !== false)
        $sets[] = '!@#$%&*?';
         
        $all = '';
        $password = '';
        foreach($sets as $set)
        {
        $password .= $set[array_rand(str_split($set))];
        $all .= $set;
        }
         
        $all = str_split($all);
        for($i = 0; $i < $length - count($sets); $i++)
        $password .= $all[array_rand($all)];
         
        $password = str_shuffle($password);
         
        if(!$add_dashes)
        return $password;
         
        $dash_len = floor(sqrt($length));
        $dash_str = '';
        while(strlen($password) > $dash_len)
        {
        $dash_str .= substr($password, 0, $dash_len) . '-';
        $password = substr($password, $dash_len);
        }
        $dash_str .= $password;
        return $dash_str;
    }

    function generate_token ($len = 32){
        // Array of potential characters, shuffled.
        $chars = array(
            'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 
            'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 
            'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
            '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'
        );
        shuffle($chars);
        $num_chars = count($chars) - 1;
        $token = '';
        // Create random token at the specified length.
        for ($i = 0; $i < $len; $i++)
        {
            $token .= $chars[mt_rand(0, $num_chars)];
        }
        return $token;
    }

	$message = "";
    $message2 = "";
    if (!empty($_REQUEST['tpm_useremail'])) {
        if($_REQUEST['tpm_useremail'] !== "")$query = "SELECT * FROM `system_users` WHERE `email` = '".mysqli_real_escape_string($GLOBALS["link"],$_REQUEST['tpm_useremail'])."' AND `active` = 1";
        if(!empty($_POST['tpm_useremail']))$query = "SELECT * FROM `system_users` WHERE `email` = '".mysqli_real_escape_string($GLOBALS["link"],$_POST['tpm_useremail'])."' AND `active` = 1";

        $email = "";
        if($_REQUEST['tpm_useremail'] !== "")$email = $_REQUEST['tpm_useremail'];
        if(!empty($_POST['tpm_useremail']))$email = $_POST['tpm_useremail'];

        $result = mysqli_query($GLOBALS["link"],$query);
        if ($result->num_rows == 0) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) // Validate email address
            {
                $message =  "Invalid email address please type a valid email!!";
            }else{
                 $message = "Account not found please create account";
            }
        } else {
            $row = mysqli_fetch_assoc($result);

            if ($row['active'] == 1) {
                $token = generate_token();
                $generatePassword = generateStrongPassword(6,false,'lud');
                $encrypt = md5($token+$row['id']);
                $query = "INSERT INTO `recovery_user_passwords`(date_created,user_id,encrypt_code,password) VALUES(NOW(),'".$row['id']."','".$encrypt."','".SHA1($generatePassword)."')";
                mysqli_query($GLOBALS["link"],$query);

                //$email = $_POST['tpm_useremail'];
                $username = $row['username'];

                $fullname = $row['first']." ".$row['last'];

                 //Sends email to the respective user
                $mail = new PHPMailer(); 
                $mail->Mailer = 'smtp';

                $mail->setFrom("noreply@clientassist.co.za","Theo Developer");
                $mail->Subject = "Ellies Forget Password:";

                //message
                $emailmessage = '
                <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                <html xmlns="http://www.w3.org/1999/xhtml">
                <head>
                    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                    <title>Forget Password</title>
                </head>
                <body>
                    <p>Hi '.$fullname.',</p>
                    <p>Your Ellies account details are as follows:<br/>
                       Username: '.$username.'<br/>Email is: '.$email.
                    '</p>
                    <a href="http://elliesdev.clientassist.co.za/login.php?id='.$row['id'].'&encrypt='.$encrypt.'&action=reset">Click here to login</a> 
                    <p>Your new password to login is:&nbsp; '.$generatePassword.'</p>
                </html>';

                //Set who the message is to be sent to
                $mail->addAddress($email,"Ellies Member");

                //convert HTML into a basic plain-text alternative body
                $mail->msgHTML($emailmessage); 
                // Mail it
                if($mail->send()){
                    $message2 = "Your password reset link is sent to your e-mail address.";
                }else{
                    $message = "Your password reset link was not send to your e-mail address.";
                }
            } else {
                $message = "Account is disabled";
            }
        }
    }

    /*$hidefield = false;
    //Reset Password
    if(isset($_GET['action'])){
        $hidefield = true;  
    }

    $encrypt = "";
    if(isset($_GET['encrypt'])){
        $encrypt = $_GET['encrypt'];  
    }

    $id = "";
    if(isset($_GET['id'])){
        $id = $_GET['id'];  
    }*/

    /*if(!empty($_POST['comfirmpassword'])){
        $encrypt  = mysqli_real_escape_string($GLOBALS["link"],$_POST['tpm_encrypt']);
        $password = mysqli_real_escape_string($GLOBALS["link"],$_POST['tpm_resetpassword']);
        $id = mysqli_real_escape_string($GLOBALS["link"],$_POST['tpm_id']);

        $query = "SELECT id FROM recovery_user_passwords where user_id='".$id."' AND encrypt_code='".$encrypt."'";
        $result = mysqli_query($GLOBALS["link"],$query);
        $Results = mysqli_fetch_array($result);
        if(count($Results)>=1){

            $query = "SELECT * FROM `system_users` WHERE id='".$id."' AND `password` = SHA1('".mysqli_real_escape_string($GLOBALS["link"],$password)."') AND `active` = 1";
            $result = mysqli_query($GLOBALS["link"],$query);
            if (mysqli_num_rows($result) == 0) {
                $message = "Invalid reset password";
            }else{
                $query = "UPDATE system_users SET password='".SHA1($password)."' where id='".$id."'";
                mysqli_query($GLOBALS["link"],$query);

                $message2 = "Your password changed sucessfully";
                header("location: ../login.php");
                exit(1);
            }
        }
        else{
            $message = 'Invalid key please try again. <a href="reset.php>Forget Password?</a>';
        }
    }*/
?>
<!DOCTYPE html>
<html> 
    <head>
        <link rel="stylesheet" href="../css/site.css" />
        <script src="../js/jquery-1.7.1.min.js"></script> 
    </head>
    <body>
        <div id='loginpagecontent' name='loginpagecontent'>
            <div id='loginpage' name='loginpage'>
                <br/>
                <img id='logo' name='logo' src='../images/logo.png' />
                <br/>
                <?php 

                    //if(!$hidefield){

                        print "<form name='frm' id='frm' action='reset.php' method='POST'";
                        print "<BR><BR>";
                        print "<h2 align='left'>Please enter email:</h2>";
                        print "<table cellpadding='0' cellspacing='0' border='0>";

                        print "<tr>";
                        print " <td><label align='left'><BR></label></td>";
                        print "</tr>";
    
                        print "<tr>";
                        print " <td><label for='tpm_userid'>E-mail:<label></td>";
                        print " <td><input type='text' name='tpm_useremail' placeholder='Email to get password' id='tpm_useremail' style='font-size:12px;' /></td>";
                        print "</tr>";
                    //}else{
                        /*print "<form name='frm' id='frm' action='reset.php?id=".$id."&encrypt=".$encrypt."&action=reset' method='POST'";
                        print "<BR><BR>";
                        print "<h2 align='left'>Please enter reset password:</h2>";
                        print "<table cellpadding='0' cellspacing='0' border='0>";

                        print "<tr>";
                        print " <td><label align='left'><BR></label></td>";
                        print "</tr>";

                        print "<input type='hidden' name='tpm_encrypt' ' id='tpm_encrypt' style='font-size:12px;' value='".$encrypt."'/>";
                        print "<input type='hidden' name='tpm_id' ' id='tpm_id' style='font-size:12px;' value='".$id."'/>";


                        print "<tr>";
                        print " <td><label for='tpm_resetpassword'>Reset Password:<label><br/><br/><br/><br/></td>";
                        print " <td><input type='password' name='tpm_resetpassword' ' id='tpm_resetpassword' style='font-size:12px;' /></td>";
                        print "</tr>";*/
                    //}
                        
                    print "<tr>";
                    print " <td colspan='2' id='errMsgTD'>".$message."</td>";
                    print "</tr>";

                    /*print "<tr>";
                    print " <td colspan='2' id='crrMsgTD'>".$message2."</td>";
                    print "</tr>";*/

                    ///if(!$hidefield){  
                        print "<tr>";
                        print " <td colspan='2' style='text-align:center;'><input type='submit' name='resetpassword' id='resetpassword' value='Reset Password'></td>";
                        print "</tr>";
                    //}else{
                        /*print "<tr>";
                        print " <td colspan='2' style='text-align:center;'><input type='submit' name='comfirmpassword' id='comfirmpassword' value='Confirm Reset Password '></td>";
                        print "</tr>";*/
                    //}
                    print "<tr>";
                     print " <td></td>";
                      print " <td style='text-align:right;'><a href='../login.php' style='color:#797979'>Login</a></td>";
                    print "</tr>";
                    
                    print "</table>";
                    print "</form>";
                ?>  
            </div>
        </div>

    <script type='text/javascript'>
        $(function() {
            $('#tpm_useremail').focus();
        });
    </script>
    </body>
</html>