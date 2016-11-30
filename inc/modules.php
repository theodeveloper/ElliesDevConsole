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
    
    if (is_dir('modules/')) {
        $handle = opendir('modules/');
        while (false !== ($entry = readdir($handle))) {
            if ($entry == "." || $entry == ".." ) continue;
            if (is_dir("modules/".$entry)) {
                // add the module directory into the include path...
                add_include_path("modules/".$entry);
                $subhandle = opendir("modules/".$entry);
                while (false !== ($subentry = readdir($subhandle))) {
                    if ($subentry == "." || $subentry == ".." ) continue;
                    if (substr($subentry,-4) == ".php") {
                        try {
                            include_once("modules/".$entry."/".$subentry);
                        } catch (Exception $e) {
                            print "Error importing module '".$entry."/".$subentry."'";
                        }
                    }
               }
            }
        }
        closedir($handle);
    }
    
    function include_module_js_scripts() {
        if (is_dir('modules/')) {
            $handle = opendir('modules/');
            while (false !== ($entry = readdir($handle))) {
                if ($entry == "." || $entry == ".." ) continue;
                if (is_dir("modules/".$entry)) {
                    // add the module directory into the include path...
                    $subhandle = opendir("modules/".$entry);
                    while (false !== ($subentry = readdir($subhandle))) {
                        if ($subentry == "." || $subentry == ".." ) continue;
                        if (substr($subentry,-3) == ".js") {
                            print "  <SCRIPT TYPE='text/javascript' SRC='moduleJS.php?module=".$entry."&file=".$subentry."&version=".VERSION."'></SCRIPT>\n";
                        }
                   }
                }
            }
            closedir($handle);
        }
    }
    
    function include_module_css_files() {
        if (is_dir('modules/')) {
            $handle = opendir('modules/');
            while (false !== ($entry = readdir($handle))) {
                if ($entry == "." || $entry == ".." ) continue;
                if (is_dir("modules/".$entry)) {
                    // add the module directory into the include path...
                    $subhandle = opendir("modules/".$entry);
                    while (false !== ($subentry = readdir($subhandle))) {
                        if ($subentry == "." || $subentry == ".." ) continue;
                        if (substr($subentry,-4) == ".css") {
                            print "  <LINK TYPE='text/css' href='moduleCSS.php?module=".$entry."&file=".$subentry."&version=".VERSION."' rel='stylesheet' media='all' />\n";
                        }
                   }
                }
            }
            closedir($handle);
        }
    }
    
    function run_called_modules() {
        if (@$_POST['aj'] == '1') {
            // if class defined
            if (class_exists($_POST['module'])) {
                
                // Try load module
                try {
                    $module = $_POST['module'];
                    $moduleObj = new $module();
                    
                    // Check if method exists
                    $class_methods = get_class_methods($module);
                    $f = 0;
                    foreach ($class_methods as $method_name) {
                        if ($method_name == $_POST['call']) { $f = 1; break; }
                    }
                    if ($f) {
                        $moduleObj->$method_name($_POST);
                    } else {
                        print "Exception: Module does not have the method called (".$_POST['call'].")\n";
                        print "<!-- scripts , code below be eval()ed by javascript -->\n";
                        print "\n";
                    }
                } catch (Exception $e) {
                    print "Exception: ".$e."\n";
                    print "<!-- scripts , code below be eval()ed by javascript -->\n";
                    print "\n";
                }
            }
            exit();
        }
    } 
?>