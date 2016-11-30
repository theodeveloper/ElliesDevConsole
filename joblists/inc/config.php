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
    define('MYSQL_SERVER' ,'localhost');
    define('MYSQL_USERNAME' ,'root');
    define('MYSQL_PASSWORD' ,'');
    define('MYSQL_DATABASE' ,'ellies_instore');
    //define('SMTP_Host', 'smtp.saix.net');
    define('SMTP_Host', 'localhost');
    define('VERSION', 1.0);
    
    error_reporting(-1);
    
$conn = mysql_pconnect(MYSQL_SERVER, MYSQL_USERNAME, MYSQL_PASSWORD);
if (!$conn) { exit("Unable to connect to DB: " . mysql_error()); }
if (!mysql_select_db(MYSQL_DATABASE)) { exit("Unable to select mydbname: " . mysql_error()); }
global $conn;

?>