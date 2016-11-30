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
    $newsletter_type = "";
    if(!empty($_REQUEST["newsletter_type"])){
        $newsletter_type = $_REQUEST["newsletter_type"];
    }
    $companydetails = "";
    $to = "";
    if(!empty($_REQUEST['id'])){
        $sql = "UPDATE notifications SET";
        $sql .= " `last_updated` =NOW(),";
        $sql .= " `status` = 'Read'";
        $sql .= " WHERE id=".$_REQUEST['id']." LIMIT 1";
        mysqli_query($GLOBALS["link"],$sql);

        $sql = "SELECT  * FROM `system_users` WHERE `id`='".$GLOBALS['system_user']->id."'";        
        $sqlres = mysqli_query($GLOBALS["link"],$sql); 
        $row = mysqli_fetch_assoc($sqlres);
        $to = $row['first'];

        $sql = "SELECT sent_by FROM `notifications` WHERE `id`=".$_REQUEST['id'];    
        $sqlres = mysqli_query($GLOBALS["link"],$sql); 
        $row = mysqli_fetch_assoc($sqlres);
        $sent_by = $row['sent_by'];

        $sql = "SELECT  * FROM `system_users` WHERE `id`=".$sent_by." LIMIT 1";       
        $sqlres = mysqli_query($GLOBALS["link"],$sql); 
        $row = mysqli_fetch_assoc($sqlres);
        $first = "";
        $last = "";
        $branchID = "";
        $first = ucfirst($row['first']);
        $last = ucfirst($row['last']);
        $branchID = $row['branch_id'];

        //Branch
        $sql = "SELECT * FROM branches WHERE id ='".$branchID."' LIMIT 1";
        $sqlres = mysqli_query($GLOBALS["link"],$sql);
        $logo = "";
        $companydetails ="";
        while($row = mysqli_fetch_assoc($sqlres)) {
            $companydetails = $first.' '.$last.'<br/>';
            $logo  = $row['logo'];
            if($logo !=""){
                $logo = "modules/companyprofiles/logos/".$logo;
            }else{
                $logo = "images/logo.png";
            }

            $companydetails .= $row['contact_number'].'<br/>
                            <img src="'.$logo.'"  style="height:75px" align="middle"/><br/>  
                            '.$row['branch'].'<br/>
                            '.$row['address'].'<br/>'; 
        }
    }

    //Newsletter
    $sql = "SELECT * FROM news_letter_types WHERE id ='".$newsletter_type."'";
    $sqlres = mysqli_query($GLOBALS["link"],$sql);
    $newsletter_layout = "";
    $attachment = "";
    while($row = mysqli_fetch_assoc($sqlres)) {
        $newsletter_layout  = $row['newsletter_layout'];
        $attachment  = $row['attachment'];
    }
    $view ="";
    if($companydetails !=""){
        $view = 'Dear '.$to.',<br/><br/>
                '.$newsletter_layout.'' ;
    }elseif($to ==""){
        $view = $newsletter_layout; 
        if($attachment !="")$view = '<p><a href="modules/newsletter/attachments/'.$attachment.'">View Attachment</a></p><br/>'.$newsletter_layout; 
    }else{
       $view = 'Dear '.$to.',<br/><br/>
                '.$newsletter_layout.'<br/><br/>
                  Yours sincerely<br/>
                '.$companydetails.'' ; 
    }
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
  </head>
  <body>
    <?php echo $view ;?>
  </body>
</html>