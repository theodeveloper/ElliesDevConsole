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
    
    
    class userType {
        
        public $id;
        public $valid;
        public $verified;
        public $active;
        public $email;
        public $username;
        public $first;
        public $last;
        public $isSuperAdmin;
		public $retailChannel;
        public $branchID;
        public $isInstaller;
        
        function __construct($username, $isInstaller = FALSE) {
            
            if ($isInstaller) {
                $query = "SELECT `installers`.`id`, `installers`.`email`, `installers`.`username`, `installers`.`full_name` AS first, '' AS last, `installers`.`active`, 0 AS super_admin, `installer_companies`.`branch_id` AS branch_id FROM `installers`, `installer_companies` WHERE `installers`.`installer_company_id` = `installer_companies`.`id` AND `installers`.`username` = '".mysqli_real_escape_string($GLOBALS["link"],$username)."'";
            } else {
                $query = "SELECT * FROM `system_users` WHERE `username` = \"".mysqli_real_escape_string($GLOBALS["link"],$username)."\"";
            }
            
            $result = mysqli_query($GLOBALS["link"],$query);
            if (!mysqli_num_rows($result)) {
                $this->valid    = 0;
                print "Unable to find user(".$username.") fatal!";
                exit(1);
            }
            $row = mysqli_fetch_assoc($result);
            
            $this->id           = $row['id'];
            $this->email        = $row['email'];
            $this->username     = $row['username'];
            $this->first        = $row['first'];
            $this->last         = $row['last'];
            $this->valid        = 1;
            $this->verified     = 1;
            $this->active       = $row['active'];
            $this->isSuperAdmin = $row['super_admin'] == 1;
            $this->branchID     = $row['branch_id'];
			$this->storeID		= $row['store_id'];
			$this->retailChannel = $this->getChannel();
            $this->isInstaller  = $isInstaller;
        }
		
		function getChannel(){
			$query="select channel from branches where id=".$this->branchID;
			$result = mysqli_query($GLOBALS["link"],$query);
			$row = mysqli_fetch_assoc($result);
			return $row["channel"];
		}
        
        function hasPermission($acl) {
            if($this->isInstaller)  return FALSE;
            if($this->isSuperAdmin) return TRUE;
            $query = "SELECT `user_groups`.`id` FROM `user_groups`, `group_permissions` WHERE `user_groups`.`user_id` = " . mysqli_real_escape_string($GLOBALS["link"],$this->id) . " AND `user_groups`.`group_id` = `group_permissions`.`group_id` AND `group_permissions`.`permission` = \"" . mysqli_real_escape_string($GLOBALS["link"],$acl) . "\"";
            $result = mysqli_query($GLOBALS["link"],$query);
            if(mysqli_num_rows($result) > 0) return TRUE;
            $query = "SELECT `id` FROM `user_permissions` WHERE `user_id` = " . mysqli_real_escape_string($GLOBALS["link"],$this->id) . " AND `permission` = \"" . mysqli_real_escape_string($GLOBALS["link"],$acl) . "\"";
            $result = mysqli_query($GLOBALS["link"],$query);
            return mysqli_num_rows($result) > 0;
        }
    }
?>