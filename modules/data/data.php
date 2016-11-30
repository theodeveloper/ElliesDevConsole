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
	
   // register_menu("data_filtering", "parentMenu", "Data Filtering");
   // register_menu("data_management", "parentMenu", "Data Management");
	
   /* register_menu("Data", "submenu", "Data",
        Array(
            //Array("title" => "New Quotation", "location" => "edit_quotation", "acl" => "edit_quotation"),
            //Array("title" => "Datas",  "location" => "Datas_entry",   "acl" => "Datas_entry"),
            Array("title" => "Data Filtering",  "location" => "data_filtering",   "acl" => "view_data"),
            Array("title" => "Data Queries",  "location" => "data_queries",   "acl" => "view_data")
        )
    );*/
    
    register_permission("Quote Permissions", "view_data",   "View data");
    register_permission("Quote Permissions", "edit_data",   "Edit data");
    register_permission("Quote Permissions", "complete_data",   "Complete data");

    class Data {
        
        private $items_per_page;
        
        public function __construct () {
            $this->items_per_page = Settings::getSetting(2);
        }
        
        public function Datas_entry() {
            //print "<a href='editquote.php?id=".GET("id")."&step=complete'>View data</a>";
            print "<br>&nbsp;<button onclick=\"AJAXCallModule('Datas','data_queries', '')\"><span class='ui-button-text'>View data</span></button>";
        }
        
        public function data_filtering ($array) {
            print "\n<!-- scripts , code below be eval()ed by javascript -->\n";
            //print "document.location.href = './data/filtering/index.php';\n";
			print "window.open('./data/resultsFilter/index.php', '_blank' );";

            exit();
        }
        public function data_queries ($array) {
            print "\n<!-- scripts , code below be eval()ed by javascript -->\n";
			print "window.open('./data/queries/index.php', '_blank' );";
            exit();
        }
 
    }

?>