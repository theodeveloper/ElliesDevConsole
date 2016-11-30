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

    if ($_GET['module'] != ""  && $_GET['file'] != "") {
        $mod_js_file = "modules/".$_GET['module']."/".$_GET['file'];
        if (!is_file($mod_js_file)) {
            die('Access Denied');
        } else {
            
            header("Content-Type: application/javascript");
            header("Content-Length: ".filesize($mod_js_file) );
            
            readfile( $mod_js_file );
            exit(0);;
        }
    } else {
        die('Access Denied');
    }
    
?>