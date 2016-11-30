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

    /*$oldlink = mysql_connect( MYSQL_SERVER, MYSQL_USERNAME, MYSQL_PASSWORD );
    if (!$oldlink || !mysql_select_db( MYSQL_DATABASE )) {
        print "DB connection error.";
        exit(-1);
    }*/

    $newLink = new mysqli(MYSQL_SERVER, MYSQL_USERNAME, MYSQL_PASSWORD,MYSQL_DATABASE);
    if($newLink->connect_errno){
        print "DB connection error.";
        exit(-1);
    }

    //$link = $oldlink;
    $link = $newLink;
    $GLOBALS["link"]=$newLink;
?>