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
    $notify = "";
    if(!empty($_REQUEST["notify"])){
        $notify = $_REQUEST["notify"];
    }

    if(!empty($_REQUEST['id'])){
        $sql = "UPDATE notifications SET";
        $sql .= " `last_updated` =NOW(),";
        $sql .= " `status` = 'Read'";
        $sql .= " WHERE id=".$_REQUEST['id']." LIMIT 1";
        mysqli_query($GLOBALS["link"],$sql);
    }

    //Notification
    $sql = "SELECT * FROM notifications WHERE id ='".$notify."'";
    $sqlres = mysqli_query($GLOBALS["link"],$sql);
    $Title = "";
    $Description = "";
    while($row = mysqli_fetch_assoc($sqlres)) {
        $Title  = $row['title'];
        $Description  = $row['description'];
    }
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
  </head>
  <body>
    <h1 style='color:#8dd03f'>Title</h1>
    <?php echo $Title ;?>
    <br/>
    <br/>
    <h1 style='color:#8dd03f'>Description</h1>
    <?php echo $Description ;?>
  </body>
</html>