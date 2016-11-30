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
$projectcode = $_POST["ProjectCode"];

//aj	1
//chk_1	1
//chk_2	1
//projectcode	CLS000001
if ($_POST["step"] == "SaveChecklist") {
    foreach($_POST as $key=>$value) {
        if (substr($key, 0, 4) == "chk_") {
            $id = substr($key, 4);
            if ($value == "1") {
                $sql = "UPDATE `checklist_items` SET `checked` = 1, `date_checked` = NOW() WHERE `id` = '".mysql_real_escape_string($id)."'";
                //print $sql;
                mysql_query($sql);
            }else{
                $sql = "UPDATE `checklist_items` SET `checked` = 0, `date_checked` = NULL WHERE `id` = '".mysql_real_escape_string($id)."'";
                //print $sql;
                mysql_query($sql);
            }
        }
    }
    print "GenerateSignOff();\n";
    exit();
}
?>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable = no">
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<title>Joblists</title>
<link rel="shortcut icon" href="images/favicon.ico" />
<link rel="stylesheet" href="css/jquery.mobile-1.3.1.min.css" />
<script src="js/jquery-1.9.1.min.js"></script>
<script src="js/jquery.mobile-1.3.1.min.js"></script>
<script src="js/myfunctions.js"></script>
</head>
<body>
<!-- Home -->
<div data-role="page" id="page1">
<?php 
/*
DROP TABLE `checklist_items`;
CREATE TABLE `checklist_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `incident_id` int(11) DEFAULT '0',
  `project_code` varchar(50) NOT NULL,
  `description` varchar(500) NOT NULL,
  `date_checked` datetime DEFAULT NULL,
  `checked` tinyint(1) NOT NULL DEFAULT '1',
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

INSERT INTO `checklist_items` (`incident_id`, `project_code`, `description`, `date_checked`, `checked`) VALUES (1, 'CLS000001', 'Check Display', NOW(), 0);
INSERT INTO `checklist_items` (`incident_id`, `project_code`, `description`, `date_checked`, `checked`) VALUES (2, 'CLS000001', 'Check Power', NOW(), 0);
INSERT INTO `checklist_items` (`incident_id`, `project_code`, `description`, `date_checked`, `checked`) VALUES (3, 'CLS000001', 'Check Housing', NOW(), 0);
*/

if (!empty($projectcode)) {
    print "<div data-theme='a' data-role='header'><h3>".$projectcode."</h3></div>";
    print "<div data-role='content'>";
    print "<form method='Post' action='index.php'>";
    print "<fieldset data-role='controlgroup' data-type='vertical'>";
    $sql = "SELECT * FROM `checklist_items` WHERE `project_code` = '".mysql_real_escape_string($projectcode)."' ORDER BY `id`";
    $sqlres = mysql_query($sql);
    while($row = mysql_fetch_assoc($sqlres)) {
        if ($row["checked"]) {
            print "<input id='chk_".$row["id"]."' name='chk_".$row["id"]."' type='checkbox' value='".$row["description"]."' class='checkitem' checked><label for='chk_".$row["id"]."'>".$row["description"]."</label>";            
        }else{
            print "<input id='chk_".$row["id"]."' name='chk_".$row["id"]."' type='checkbox' value='".$row["description"]."' class='checkitem'><label for='chk_".$row["id"]."'>".$row["description"]."</label>";            
        }
    }
    print "</fieldset>";
    print "<br>";
    print "<input data-theme='b' value='Save' type='button' onclick='SaveChecklist()'>";
    print "<input type='hidden' id='ProjectCode' name='ProjectCode' value='".$projectcode."'>";
    print "</form>";
    print "</div>";
    print "</div>";
}else{
    $projectcode = "CLS000001";
    print "<div data-theme='a' data-role='header'><h3>Enter Project Code</h3></div>";
    print "<div data-role='content'>";
    print "<form method='Post' action='index.php'>";
    print "<div data-role='fieldcontain'>";
    print "<input name='ProjectCode' id='ProjectCode' placeholder='' value='".$projectcode."' type='search'>";
    print "</div>";
    print "<input data-theme='b' value='Find Project' type='submit'>";
    print "</form>";
    print "</div>";
    print "</div>";
}
?>
</div>
</body>
</html>

