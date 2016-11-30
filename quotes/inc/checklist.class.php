<?php
	class Checklist
	{
	    public $CheckID;
	    public $Title;
	    public $Description;
	    public $Type;
	    public $Active;
	    public $Deleted;
	    public $Sort_order;
	    public $Mandatory;
	    public $Exists = false;
	    public $Row;

	    //Constructor
	    public function __construct($CheckID = 0)
	    {
	      $this->$CheckID = $CheckID;
	    }


	    public function Read($CheckID = 0)
	    {
	        $sql = "SELECT * FROM `checklist` WHERE `id` = '" . mysqli_real_escape_string($GLOBALS["link"],$CheckID) . "'";
	        $sqlres = mysqli_query($GLOBALS["link"],$sql);
	  
	        //Record exists
	        while($row = mysqli_fetch_assoc($sqlres))
	        {
	                $this->Row=$row;
	                $this->Exists = true;
	                $this->CheckID = $row["id"];
	                $this->Title = $row["Title"];
	                $this->Description= $row["Description"];
	                $this->Type= $row["Type"];
	                $this->Active= $row["active"];
	                $this->Deleted= $row["deleted"];
	                $this->Sort_order= $row["Sort_order"];
	                $this->Mandatory= $row["Mandatory"];
	        }
	    }

	    public function QuestionsActive(){

	        $sql = "SELECT * FROM `checklist` WHERE `active` = 1 ";
	        $sqlres = mysqli_query($GLOBALS["link"],$sql);

	        $arrQuestions = array();
		  
	        //Record exists
	        while($row = mysqli_fetch_assoc($sqlres))
	        {
	                $this->Row=$row;
	                $this->Exists = true;
	                $this->CheckID = $row["id"];
	                $this->Title = $row["Title"];
	                $this->Description= $row["Description"];
	                $this->Type= $row["Type"];
	                $this->Active= $row["active"];
	                $this->Deleted= $row["deleted"];
	                $this->Sort_order= $row["Sort_order"];
	                $this->Mandatory= $row["Mandatory"]; 
	                $details =  $this->Title . ':' . $this->Type . ';';     
	                $arrQuestions[] = $details;
	        }
	        return  $arrQuestions;
   	 	}

   	 	public function GetQuestion($QuestID){

	        $sql = "SELECT `Title` FROM `checklist` WHERE `id` =".$QuestID;
	        $sqlres = mysqli_query($GLOBALS["link"],$sql);

	        $Questions = "";
		  
	        //Record exists
	        while($row = mysqli_fetch_assoc($sqlres))
	        {
	                $this->Row=$row;
	                $this->Exists = true;
	                $this->Title = $row["Title"];
	                $Questions = $this->Title;

	        }
	        return  $Questions;
   	 	}


	    //Save to database
	    public function Save()
	    { 
	        $this->Read($this->CheckID); 
	        if (!$this->Exists) {
	            $sql = "INSERT INTO `checklist` (`Title`,`Description`, `Type`, `active`, `deleted`,`Sort_order`,`Mandatory`) VALUES ('" . mysqli_real_escape_string($GLOBALS["link"],$this->Title) . "', '" . mysqli_real_escape_string($GLOBALS["link"],$this->Description) ."', '"  . mysqli_real_escape_string($GLOBALS["link"],$this->Type) ."', '" . mysqli_real_escape_string($GLOBALS["link"],$this->Active) ."', '" . mysqli_real_escape_string($GLOBALS["link"],$this->Deleted) ."', '". mysqli_real_escape_string($GLOBALS["link"],$this->Sort_order) ."', '" .mysqli_real_escape_string($GLOBALS["link"],$this->Mandatory) . "')";
	            $this->CheckID = mysql_insert_id();
	            $this->Exists = true;
	            mysqli_query($GLOBALS["link"],$sql);
	        }
	        if ($this->Exists) {
	            $sql = "UPDATE `checklist` SET";
	            $sql .= ", `Title` = '" . mysqli_real_escape_string($GLOBALS["link"],$this->Title) . "'";
	            $sql .= ", `Description` = '" . mysqli_real_escape_string($GLOBALS["link"],$this->Description) . "'";
	            $sql .= ", `Type` = '" . mysqli_real_escape_string($GLOBALS["link"],$this->Type) . "'";
	            $sql .= ", `active` = '" . mysqli_real_escape_string($GLOBALS["link"],$this->Active) . "'";
	            $sql .= ", `deleted` = '" . mysqli_real_escape_string($GLOBALS["link"],$this->Deleted) . "'";
	            $sql .= ", `Sort_order` = '" . mysqli_real_escape_string($GLOBALS["link"],$this->Sort_order) . "'";
	            $sql .= ", `Mandatory` = '" . mysqli_real_escape_string($GLOBALS["link"],$this->Mandatory) . "'";

	            $sql .= " WHERE `id` = '" . mysqli_real_escape_string($GLOBALS["link"],$this->CheckID) . "'";
	            //$html .= $sql;
	            //exit();
	            mysqli_query($GLOBALS["link"],$sql);
	        }
	    }
	}
?>