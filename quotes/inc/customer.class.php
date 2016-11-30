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
class Customer {
    public $Exists = false;
    public $CustomerID = 0;
    public $Name = "";
    public $Surname = "";
    public $CellPhone = "";
    public $Email = "";
    public $CompanyName = "";
    public $Company_Telephone = "";

    public $Active = false;
    public $DateCreated = NULL;
    public $LastAccessed = NULL;
    public $HasOpenQuotes = false;
    public $CreatedBy = 0;
    public $UpdatedBy = 0;
    
    public function __construct ($CustomerID = 0, $CellPhone = "", $Email = "") {
        $this->BuildTables();
        if ($CustomerID > 0) {
            $sql = "SELECT * FROM `customers` WHERE `id` = '".mysqli_real_escape_string($GLOBALS["link"],$CustomerID)."'";                
        }else{
            if (empty($CellPhone) && !empty($Email)){
                $sql = "SELECT * FROM `customers` WHERE `email` = '".mysqli_real_escape_string($GLOBALS["link"],$Email)."' LIMIT 0, 1";
            }else{
                $sql = "SELECT * FROM `customers` WHERE `cellphone` = '".mysqli_real_escape_string($GLOBALS["link"],$CellPhone)."'";                
            }
        }
        $sqlres = mysqli_query($GLOBALS["link"],$sql);
        while($row = mysqli_fetch_assoc($sqlres)) {
            $this->Exists = true;
            $this->CustomerID = $row["id"];
            $this->Name = $row["name"];
            $this->Surname = $row["surname"];
            $this->CellPhone = $row["cellphone"];
            $this->Email = $row["email"];
            $this->CompanyName = $row["company"];
            $this->Company_Telephone = $row["c_telephone"];
            $this->Active = $row["active"];
            $this->DateCreated = $row["date_created"];
            $this->LastAccessed = $row["last_accessed"];
            $this->CreatedBy = $row["created_by"];
            $this->UpdatedBy = $row["updated_by"];

            if ($CustomerID > 0) {
            }else{
                //$this->CellPhone = $CellPhone;
            }
        }
        
        if ($this->Exists) {
            $sql = "SELECT `id` FROM `quotes` WHERE `complete` = 0 AND `customer_id` = '".mysqli_real_escape_string($GLOBALS["link"],$this->CustomerID)."' LIMIT 0, 1";
            $sqlres = mysqli_query($GLOBALS["link"],$sql);
            while($row = mysqli_fetch_assoc($sqlres)) {
                $this->HasOpenQuotes = true;
            }
        }else{
            if (!empty($CellPhone)) {
                $this->CellPhone = $CellPhone;
            }elseif(!empty($Email)) {
                $this->Email = $Email;
            }
        }
    }
    
    public function Save() {
        if (!$this->Exists) {
            if (!empty($this->CellPhone)) {
                $sql = "INSERT INTO `customers` (`date_created`, `active`, `cellphone`, `created_by`) VALUES (NOW(), 1, '".mysqli_real_escape_string($GLOBALS["link"],$this->CellPhone)."', '".mysqli_real_escape_string($GLOBALS["link"],$this->CreatedBy)."')";
                mysqli_query($GLOBALS["link"],$sql);
                $this->CustomerID = mysql_insert_id();
                $this->Exists = true;
            }elseif(!empty($this->Email)) {
                $sql = "INSERT INTO `customers` (`date_created`, `active`, `email`, `created_by`) VALUES (NOW(), 1, '".mysqli_real_escape_string($GLOBALS["link"],$this->Email)."', '".mysqli_real_escape_string($GLOBALS["link"],$this->CreatedBy)."')";
                mysqli_query($GLOBALS["link"],$sql);
                $this->CustomerID = mysql_insert_id();
                $this->Exists = true;
            }
        }
        if ($this->Exists) {
            $sql = "UPDATE `customers` SET";
            $sql.= " `name` = '".mysqli_real_escape_string($GLOBALS["link"],$this->Name)."'";
            $sql.= ", `surname` = '".mysqli_real_escape_string($GLOBALS["link"],$this->Surname)."'";
            $sql.= ", `email` = '".mysqli_real_escape_string($GLOBALS["link"],$this->Email)."'";
            $sql.= ", `cellphone` = '".mysqli_real_escape_string($GLOBALS["link"],$this->CellPhone)."'";
            $sql.= ", `company` = '".mysqli_real_escape_string($GLOBALS["link"],$this->CompanyName)."'";
            $sql.= ", `c_telephone` = '".mysqli_real_escape_string($GLOBALS["link"],$this->Company_Telephone)."'";
            $sql.= ", `updated_by` = '".mysqli_real_escape_string($GLOBALS["link"],$this->UpdatedBy)."'";
            $sql.= ", `last_accessed` = NOW()";
            $sql.= " WHERE `id` = '".mysqli_real_escape_string($GLOBALS["link"],$this->CustomerID)."'";
            mysqli_query($GLOBALS["link"],$sql);
        }
    }
    
    private function BuildTables() {
        $sql = "CREATE TABLE IF NOT EXISTS `customers` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `name` varchar(250) NOT NULL,
          `surname` varchar(250) DEFAULT NULL,
          `cellphone` varchar(250) NOT NULL,
          `email` varchar(250) NOT NULL,
          `company` varchar(250) NOT NULL,
          `c_telephone` varchar(250) NOT NULL,
          `date_created` datetime NOT NULL,
          `last_accessed` datetime NOT NULL,
          `active` tinyint(1) NOT NULL DEFAULT '0',
          UNIQUE KEY `id` (`id`),
          KEY `name` (`name`),
          KEY `surname` (`surname`),
          KEY `cellphone` (`cellphone`),
          KEY `email` (`email`),
          KEY `date_created` (`date_created`),
          KEY `last_accessed` (`last_accessed`),
          KEY `active` (`active`)
        ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;";
        mysqli_query($GLOBALS["link"],$sql);
        //$sql = "ALTER TABLE listings MODIFY COLUMN price VARCHAR(50) DEFAULT NULL;";
    }
    
    public function Quotes() {
        //$quote = new Quote(1);
        //$quote->CustomerID = $this->CustomerID;
        //$quote->Save();

        //$quotes[] = NULL;
        $sql = "SELECT `id` FROM `quotes` WHERE `customer_id` = '".mysqli_real_escape_string($GLOBALS["link"],$this->CustomerID)."'";
        //print $sql;
        $sqlres = mysqli_query($GLOBALS["link"],$sql);
        while($row = mysqli_fetch_assoc($sqlres)) {
            //$quotes[] = new Quote($row["id"]);
            $quotes[] = $row["id"];
        }

        //print "here chana";
        //$quotes[] = 1;
        //$quotes[] = 2;
        return $quotes;
    }
}
?>