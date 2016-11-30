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
get_session();

//$_SESSION["userid"] = 1;

LoginCheck("index.php");

$booktitle = "Ellies InStore App";

require_once("inc/header.php");

$sysuser = new userType($_SESSION["userid"]);


//print "<center><img src='images/logo.png'></center>";
PrintNavBar("Home", false, "");

$nopermission = GET("nopermission");
if (!empty($nopermission)) {
    print "You do not have permission to ".GET("nopermission");
}

print "<ul data-role='listview' data-divider-theme='a' data-inset='true'>";
print "<li data-role='list-divider' role='heading'>In-Store Quotation App</li>";
if ($sysuser->hasPermission("edit_quotes")) {
    print "<li data-theme='c'><a href='newquote.php' data-ajax='false'>Find / New Audit (Quote)</a></li>";
}
/*
if ($sysuser->hasPermission("view_quotes")) {
    print "<li data-theme='c'><a href='quotes.php?complete=1' data-ajax='false'>Completed Quotations</a></li>";    
}
*/
print "<li data-theme='c'><a href='techinfo.php' data-ajax='false'>Product Information</a></li>";

//print "<li data-theme='c'><a href='productimport.php' data-ajax='false'>Import Product Information</a></li>";

if (!empty($SESSION["username"])) {
    print "<li data-theme='c'><a href='../' data-ajax='false' data-icon='gears'>Return to Ellies Management System</a></li>";    
}
print "</ul>";

require_once("inc/footer.php");
?>

