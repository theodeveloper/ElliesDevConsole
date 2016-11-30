<?php
session_start();

require_once("simpleImage.php");

class QuoteImages
{
    public $QuoteID;
    public $Hash;
    public $Filename;
    public $Room;
    public $Status;
    public $Exists = false;
    public $Row;
    

    //Constructor
    public function __construct()
    {  
    }

    //Checks if Record Exists
    private function CheckExist($quoteID = 0)
    {
        $sql = "SELECT * FROM `quote_images` WHERE `quote_id` = '" . mysqli_real_escape_string($GLOBALS["link"],$quoteID) . "'";
        $sqlres = mysqli_query($GLOBALS["link"],$sql);

        //Record exists
        while($row = mysqli_fetch_assoc($sqlres))
        {
                $this->Row=$row;
                $this->Exists = true;
                $this->QuoteID = $row["quote_id"];
                $this->Hash= $row["hash"];
                $this->Filename= $row["FileName"];
                $this->Room= $row["Room"];
                $this->Status= $row["Status"];
        }
    }

    //Creates a thumbnail
    public function createsThumbnail($quoteID = 0)
    {
    	  $img = new SimpleImage("images/Images/". $this->FileName);
    	  $img->best_fit(250, 250)->save("images/Images/Thumbnails/". $this->FileName);
    }

    //Reads Filename Images from database
    public function ReadImagesA($quoteID = 0)
    {
    	$sql = "SELECT * FROM `quote_images` WHERE `quote_id` = '" . mysqli_real_escape_string($GLOBALS["link"],$quoteID) . "'";
    	$sqlres = mysqli_query($GLOBALS["link"],$sql);
    	$num =0;
        $Images = array();

    	//Record exists
        while($row = mysqli_fetch_assoc($sqlres))
        {
        		$num++;
                $this->Row=$row;
                $this->Exists = true;
                $this->QuoteID = $row["quote_id"];
                $this->Hash= $row["hash"];
                $this->Filename= $row["FileName"];
                $this->Room= $row["Room"];
                $this->Status= $row["Status"];
                $Images[] = $this->Filename;
        }
        return $Images;
    }

     //Reads Filename and Room Images from database
    public function ReadImages($quoteID = 0)
    {
        $sql = "SELECT * FROM `quote_images` WHERE `quote_id` = '" . mysqli_real_escape_string($GLOBALS["link"],$quoteID) . "'";
        $sqlres = mysqli_query($GLOBALS["link"],$sql);
        $num =0;
        $details = null;
        $Images = array();

        //Record exists
        while($row = mysqli_fetch_assoc($sqlres))
        {
            $num++;
            $this->Row=$row;
            $this->Exists = true;
            $this->QuoteID = $row["quote_id"];
            $this->Hash= $row["hash"];
            $this->Filename= $row["FileName"];
            $this->Room= $row["Room"];
            $this->Status= $row["Status"];
            $details = $this->Filename .','. $this->Room .';';
            if($this->Status == 0){
                $Images[] = $details;
            }    
        }
        return $Images;
    }
    
    //Saves to database
    public function Save()
    {     
        $this->Hash = $this->GetHash($this->FileName);
        $sql = "INSERT INTO `quote_images` (`quote_id`,`hash`, `FileName`, `Room`,`Status`) VALUES ('" . mysqli_real_escape_string($GLOBALS["link"],$this->QuoteID) . "', '" . mysqli_real_escape_string($GLOBALS["link"],$this->Hash) ."', '"  . mysqli_real_escape_string($GLOBALS["link"],$this->FileName) ."', '" . mysqli_real_escape_string($GLOBALS["link"],$this->Room) ."', '" . mysqli_real_escape_string($GLOBALS["link"],$this->Status) . "')";
        $this->QuoteID = mysql_insert_id();
        //$this->Exists = true;
        //echo $sql;
        mysqli_query($GLOBALS["link"],$sql);

    }

       //Get Hash
    public function GetHash($filename)
    {
        return md5($filename);
    }

    public function Update()
    {
        if ($this->Exists) {
            $sql = "UPDATE `quote_images` SET";
            $sql .= ", `quote_id` = '" . mysqli_real_escape_string($GLOBALS["link"],$this->QuoteID) . "'";
            $sql .= ", `hash` = '" . mysqli_real_escape_string($GLOBALS["link"],$this->Hash) . "'";
            $sql .= ", `FileName` = '" . mysqli_real_escape_string($GLOBALS["link"],$this->FileName) . "'";
            $sql .= ", `Room` = '" . mysqli_real_escape_string($GLOBALS["link"],$this->Room) . "'";
            $sql .= ", `status` = '" . mysqli_real_escape_string($GLOBALS["link"],$this->Status) . "'";
            $sql .= " WHERE `quote_id` = '" . mysqli_real_escape_string($GLOBALS["link"],$this->QuoteID) . "'";
            mysqli_query($GLOBALS["link"],$sql);
        }
    }   
}
?>