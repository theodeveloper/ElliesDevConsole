<?php
class QuoteChecklist
{
    public $QuoteID;
    public $QuestID;
    public $Room;
    public $Answer;
    public $Exists = false;
    public $Row;

    //Constructor
    public function __construct()
    {          
    }
    //Reads Checklist
    public function ReadChecklist($QuoteID = 0, $Room = ""){

    	$sql = "SELECT * FROM `quote_checklist` WHERE `quote_id` = '" . mysqli_real_escape_string($GLOBALS["link"],$QuoteID) . "' AND `room` = '" . mysqli_real_escape_string($GLOBALS["link"],$Room) . "'" ;
        $sqlres = mysqli_query($GLOBALS["link"],$sql);

        $checklist = array();
  
        //Record exists
        while($row = mysqli_fetch_assoc($sqlres))
        {
                $this->Row=$row;
                $this->Exists = true;
                $this->QuoteID = $row["quote_id"];
                $this->QuestID = $row["question_id"];
                $this->Room = $row["room"];
                $this->Answer= $row["Value"];
                if($this->Answer ==-1)
                {
                    $this->Answer = "-";
                }
                $details =  $this->QuestID .':'.$this->Answer.';'; 
                $checklist[] = $details;
        }
       return $checklist;
    }

    public function RoomTypesExists($QuoteID = 0){

        $sql = "SELECT * FROM `quote_checklist` WHERE `quote_id` = '" . mysqli_real_escape_string($GLOBALS["link"],$QuoteID) . "'" ;
        $sqlres = mysqli_query($GLOBALS["link"],$sql);

        $arrRooms = array();
  
        //Record exists
        while($row = mysqli_fetch_assoc($sqlres))
        {
                $this->Row=$row;
                $this->Exists = true;
                $this->QuoteID = $row["quote_id"];
                $this->QuestID = $row["question_id"];
                $this->Room = $row["room"];
                $this->Answer= $row["Value"];
                if(!in_array($this->Room, $arrRooms))
                {
                    $arrRooms[] = $this->Room;
                }
        }
       return  $arrRooms;
    }

    //Save to database
    public function Save()
    {     
        if (!$this->Exists) {
            $sql = "INSERT INTO `quote_checklist` (`quote_id`,`question_id`, `room`, `Value`) VALUES ('" . mysqli_real_escape_string($GLOBALS["link"],$this->QuoteID) . "', '" . mysqli_real_escape_string($GLOBALS["link"],$this->QuestID) ."', '"  . mysqli_real_escape_string($GLOBALS["link"],$this->Room) ."', '" . mysqli_real_escape_string($GLOBALS["link"],$this->Answer) . "')";
            //$this->CheckID = mysql_insert_id();
            $this->Exists = true;
            mysqli_query($GLOBALS["link"],$sql);
        }
        if ($this->Exists) {
            $sql = "UPDATE `quote_checklist` SET";
            $sql .= " `quote_id` = '" . mysqli_real_escape_string($GLOBALS["link"],$this->QuoteID) . "'";
            $sql .= ", `question_id` = '" . mysqli_real_escape_string($GLOBALS["link"],$this->QuestID) . "'";
            $sql .= ", `room` = '" . mysqli_real_escape_string($GLOBALS["link"],$this->Room) . "'";
            $sql .= ", `Value` = '" . mysqli_real_escape_string($GLOBALS["link"],$this->Answer) . "'";
            $sql .= " WHERE `id` = '" . mysqli_real_escape_string($GLOBALS["link"],$this->QuoteID) . "'AND `room` = '" . mysqli_real_escape_string($GLOBALS["link"],$Room) . "'" ;
            //$html .= $sql;
            //exit();
            mysqli_query($GLOBALS["link"],$sql);
        }
    }
}
?>