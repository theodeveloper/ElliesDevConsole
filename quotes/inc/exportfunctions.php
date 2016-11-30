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
function deleteDir($dirPath) {
    if (! is_dir($dirPath)) {
        throw new InvalidArgumentException("$dirPath must be a directory");
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
        $dirPath .= '/';
    }
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            self::deleteDir($file);
        } else {
            unlink($file);
        }
    }
    rmdir($dirPath);
}

function ExportDBToCSV() {
 print "<a href = 'downloaddb.php'>Download CSV</a>";
}

function ExportDBToCSV2() {
    //print "ello";
    //exit();
    $excludedtables[] = "user_data_log";
    $excludedtables[] = "audit_log";
    
   /* $link = mysql_connect("127.0.0.1","root","");
    if (!$link || !mysql_select_db("ellies")) {
            print "SQL ERR\n";
            exit(1);
    }*/
    
    $prefix = time();
    
    $q = "SHOW TABLES";
    $table_r = mysqli_query($GLOBALS["link"],$q);
    while ($tablerow = mysqli_fetch_assoc($table_r)) {
            //===Only Show Tables Not in the excluded array===
            if (!in_array($tablerow['Tables_in_ellies'], $excludedtables)) {
                    $header = 0;
                    $q = "SELECT * FROM `".$tablerow['Tables_in_ellies']."`";
                    $r = mysqli_query($GLOBALS["link"],$q);
                    $tmpfolder = "/tmp/tmp_db_".$prefix;
                    if (!file_exists($tmpfolder) ) {
                        mkdir($tmpfolder);
                    }
                    $fp = fopen($tmpfolder."/".$tablerow['Tables_in_ellies'].".csv", 'w');
                    if (!$fp) {
                            print "WRITE ERR\n";
                            exit(1);
                    }
                    while ($row = mysqli_fetch_assoc($r)){
                            if ($header == 0) {
                                    $header = 1;
                                    $headers = array();
                                    foreach ($row as $id=>$val) {
                                            $headers[] = $id;
                                    }
                                    fputcsv($fp, $headers);
                            }
                            $headers = array();
                            foreach ($row as $id=>$val) {
                                    $headers[] = $val;
                            }
                            fputcsv($fp, $headers);
                    }
                    fclose($fp);
                    //print $tablerow['Tables_in_ellies_instore']."\n";
            }
    }
    $last_line = system('zip -9 -y -r -q /tmp/dbexport_'.$prefix.'.zip '.$tmpfolder.'/', $retval);
	$yourfile = '/tmp/dbexport_'.$prefix.'.zip';
    $file_name = basename($yourfile);

  
    header("Content-Length: " . filesize($yourfile));

    readfile($yourfile);
   
    deleteDir($tmpfolder);
    
    
/*    $mail = new PHPMailer(); // defaults to using php "mail()"
    $mail->IsSMTP();
    $mail->Host = "127.0.0.1";
    //$mail->Host = "smtp.saix.net";
    //$mail->Host = SMTP_Host;
    $mail->SMTPAuth = false;
    
    $mail->Helo = "heyyyyyy";
    $mail->SetFrom("support@tradepage.co.za","Do Not Reply");
    $mail->AddAddress("ari.salkow@ellies.co.za", "Ari Salkow");
    //$mail->AddAddress("donavan.cook@tradepage.net", "Donavan");
    //$mail->AddAddress("michael.dasilvapereira@tradepage.net", "Michael");
    
    $mail->Subject    = "Ellies InStore DB export";
    $mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
    $mail->MsgHTML("Please find attached DB export");
    
    $mail->AddAttachment('/tmp/dbexport_'.$prefix.'.zip');
    
    $mail->Send();
    
    if(!empty($mail->ErrorInfo)) {
      //echo "Mailer Error: " . $mail->ErrorInfo;
      print $mail->ErrorInfo;
    } else {
      print "email sent!";
    }*/
}
?>