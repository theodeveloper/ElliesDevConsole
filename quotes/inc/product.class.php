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
class Product {
    public $ProductID = 0;
    public $Product = "";
    public $Price = 0;
    public $NewTechnologyType = "";
    public $RelatedTo = "";
    public $UsesKW = false;
    public $NewKWh = 0;
    public $NewLitres = 0;
    public $Exists = false;
    public $Active = false;
    public $Deleted = false;
    public $Information = "";
    public $Code = "";
    
    public function __construct ($ProductID = 0, $ProductCode = "") {
        if ($ProductID > 0) {
            $sql = "SELECT * FROM `new_products` WHERE `id` = '".mysqli_real_escape_string($GLOBALS["link"],$ProductID)."'";
        }else{
            $sql = "SELECT * FROM `new_products` WHERE `code` = '".mysqli_real_escape_string($GLOBALS["link"],$ProductCode)."'";
        }
        $sqlres = mysqli_query($GLOBALS["link"],$sql);
        while ($row = mysqli_fetch_assoc($sqlres)) {
            $this->Exists = true;
            $this->Product = $row["product"];
            $this->Price = $row["price"];
            $this->NewTechnologyType = $row["new_technology_type"];
            $this->RelatedTo = $row["related_to"];
            $this->UsesKW = $row["kw_yn"];
            $this->NewKWh = $row["new_kwh"];
            $this->NewLitres = $row["new_litres"];
            $this->Active = $row["deleted"];
            $this->Deleted = $row["deleted"];
            $this->Information = "";
            $this->Code = $row["code"];
        }
        //id  | code  | product | price   | new_technology_type | related_to     | kw_yn | new_kwh | new_litres
    }
    
    public function Save() {
        if (!$this->Exists) {
            $sql = "INSERT INTO `new_products` (`active`, `deleted`, `related_to`, `kw_yn`) VALUES (1, 0, '".mysqli_real_escape_string($GLOBALS["link"],$this->RelatedTo)."', '".mysqli_real_escape_string($GLOBALS["link"],$this->UsesKW)."')";
            mysqli_query($GLOBALS["link"],$sql);
            $this->ProductID = mysql_insert_id();
        }
        
        $sql = "UPDATE `new_products` SET ";
        $sql.= " `product` = '".mysqli_real_escape_string($GLOBALS["link"],$this->Product)."'";
        $sql.= ", `price` = '".mysqli_real_escape_string($GLOBALS["link"],$this->Price)."'";
        $sql.= ", `new_technology_type` = '".mysqli_real_escape_string($GLOBALS["link"],$this->NewTechnologyType)."'";
        $sql.= ", `related_to` = '".mysqli_real_escape_string($GLOBALS["link"],$this->RelatedTo)."'";
        $sql.= ", `kw_yn` = '".mysqli_real_escape_string($GLOBALS["link"],$this->UsesKW)."'";
        $sql.= ", `new_kwh` = '".mysqli_real_escape_string($GLOBALS["link"],$this->NewKWh)."'";
        $sql.= ", `new_litres` = '".mysqli_real_escape_string($GLOBALS["link"],$this->NewLitres)."'";
        $sql.= ", `active` = '".mysqli_real_escape_string($GLOBALS["link"],$this->Active)."'";
        $sql.= ", `deleted` = '".mysqli_real_escape_string($GLOBALS["link"],$this->Deleted)."'";
        $sql.= ", `information` = '".mysqli_real_escape_string($GLOBALS["link"],$this->Information)."'";
        $sql.= " WHERE `id` = '".mysqli_real_escape_string($GLOBALS["link"],$this->ProductID)."'";
        print $sql;
        exit();
        mysqli_query($GLOBALS["link"],$sql);
    }
    
    public function Delete() {
        $sql = "UPDATE `new_products` SET ";
        $sql.= " `deleted` = 1";
        $sql.= " WHERE `id` = '".mysqli_real_escape_string($GLOBALS["link"],$this->ProductID)."'";
        mysqli_query($GLOBALS["link"],$sql);
    }
    
    public function PrintItemInfo($medialocation = "") {
        $tempinfo = file_get_contents("temp/".$this->Code.".htm");
        //$tempinfo = $this->Information;
        if (!empty($tempinfo)) {
            if (!empty($medialocation)) {
                $tempinfo = str_replace("quotes/media/", $medialocation, $tempinfo);                    
            }
            print $tempinfo;
        }else{
            print "No information available for this product yet.";
        }
    }

}
?>