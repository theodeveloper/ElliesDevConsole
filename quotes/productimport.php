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
require_once("../inc/system_user.php");

LoginCheck("productimport.php");
$sysuser = new userType($_SESSION["userid"]);
if (!$sysuser->hasPermission("view_quotes")) {
    header("location: index.php?nopermission=Edit%20Products");
    exit();
}

require_once("inc/header.php");
PrintNavBar("Import Product Data", true);

//print "<iframe src='importcsv.php' width='600' height='400' border='0' frameborder='0'></iframe>";
require_once("inc/footer.php");
?>