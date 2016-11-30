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

    include_once("inc/modules.php");
    include_once("inc/html.php");
    include_once("inc/system_user.php");
    
    // If not authenticated...
    if (!is_authenticated()) {
        if ($_POST['aj'] == 1) {
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

   
    $newsletter = "";
    if(!empty($_REQUEST["id"])){
        $id = $_REQUEST["id"];
       /* $sql = "SELECT * FROM news_letter WHERE id ='".$id."' LIMIT 1";
        $sqlres = mysqli_query($GLOBALS["link"],$sql);
        $branch = "";
        $newsletter_type = "";
        while($row = mysqli_fetch_assoc($sqlres)) {
            $newsletter_type  = $row['news_letter_type'];
            $branch  = $row['branch'];

        }*/

        //Newsletter
        $sql = "SELECT `newsletter_layout` FROM news_letter_types WHERE id ='".$id."' LIMIT 1";
        $sqlres = mysqli_query($GLOBALS["link"],$sql);
        $newsletter_layout = "";
        while($row = mysqli_fetch_assoc($sqlres)) {
            $newsletter_layout  = $row['newsletter_layout'];
        }

        //Branch
        /*$sql = "SELECT * FROM branches WHERE id ='".$branch."' LIMIT 1";
        $sqlres = mysqli_query($GLOBALS["link"],$sql);
        $companydetails = "";
        $logo = "";
        while($row = mysqli_fetch_assoc($sqlres)) {
            $companydetails = '<b>Company Details:</b><br/>
                            Branch: '.$row['branch'].'<br/>
                            Branch Code: '.$row['prefix'].'<br/>
                            Address: '.$row['address'].'<br/>
                            Contact Number: '.$row['contact_number'].'<br/>';
            $logo  = $row['logo'];
        }
        
        if($logo !=""){
            $logo = "modules/companyprofiles/logos/".$logo;
        }else{
            $logo = "images/logo.png";
        }

        $newsletter = '
                    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                    <html xmlns="http://www.w3.org/1999/xhtml">
                    <head>
                        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                        <title>Newsletter</title>
                    </head>
                    <body>
                        '.$newsletter_layout.'<br/><br/>
                        '.$companydetails.'<br/><img src="'.$logo.'"  style="width:150px;height:100px" align="middle"/>      
                    </body>
                    </html>';*/


        $newsletter = '
                    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                    <html xmlns="http://www.w3.org/1999/xhtml">
                    <head>
                        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                        <title>Newsletter</title>
                    </head>
                    <body>
                        '.$newsletter_layout.'     
                    </body>
                    </html>';


    }
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
  </head>
  <body>
    <?php echo $newsletter ;?>
  </body>
</html>