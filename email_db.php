# This code is written by Tradepage Pty Ltd for Ellies Pty Ltd (the 'parties' mentioned below)
# The code is provided based on the the terms specified within the agreed NDA between both parties.
# Both parties have agreed the code is strictly confidential
# and only by mutal agreement of both parties may the code be exposed to outside parties.
#
# Any changes made to the code other than by Tradepage Pty Ltd during the NDA Agreement void support of the code
#
#/* --------------------------------------------------------------------------
#* This source code contains confidential information that is proprietary to
#* CloudGroup (Pty) Ltd. No part of its contents may be used,
#* copied, disclosed or conveyed to any party in any manner whatsoever
#* without prior written permission from CloudGroup(Pty) Ltd.
#* No part of this source code may be used, reproduced, stored in a retrieval system,
#* or transmitted, in any form or by any means, electronic, mechanical,
#* photocopying, recording or otherwise, without the prior written permission
#* of the copyright owners.
#* --------------------------------------------------------------------------
#* Copyright CloudGroup (Pty) Ltd
#*/
#!/usr/bin/php
<?php 
require_once("quotes/inc/class.phpmailer.php");
require_once("quotes/inc/exportfunctions.php");
require_once("inc/system_user.php");
require_once("inc/functions.php");
session_start();
get_session();
    if (!is_authenticated()) {
        if (isset($_POST['aj']) && $_POST['aj'] == 1) {
            print "\n<!-- scripts , code below be eval()ed by javascript -->\nalert('You have been signed out! Please relogin');";
        } else {
            die("Not logged in");
            exit(1);
        }
    }
<a href = "downloaddb.php?filename=$file_name">Download CSV</a>
print "\n";
?>