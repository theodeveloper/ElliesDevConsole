<?php
session_start();
require_once("customer.class.php");
require_once("quote.class.php");
require_once("quoteitem.class.php");
require_once("class.phpmailer.php");

class Approval
{
    public $ApprovID;
    public $Approved_by;
    public $Ref;
    public $Status;
    public $Date_approved;
    public $Comments;
    public $Exists = false;
    public $Row;
    public $Approved;

    //Constructor
    public function __construct($ref = 0,$Approved_by = "")
    {
         $sql = "SELECT * FROM `quote_approvals` WHERE `ref` = '" . mysqli_real_escape_string($GLOBALS["link"],$ref) . "' AND `approved_by` = '" . mysqli_real_escape_string($GLOBALS["link"],$Approved_by) . "'" ;

        $sqlres = mysqli_query($GLOBALS["link"],$sql);

        //Record exists
        while($row = mysqli_fetch_assoc($sqlres))
        {
                $this->Row=$row;
                $this->Exists = true;
                $this->ApprovID = $row["approvID"];
                $this->Approved_by= $row["approved_by"];     
                $this->Ref= $row["ref"];  
                $this->Status= $row["status"];
                $this->Date_approved= $row["date_approved"];
                $this->Comments= $row["comments"];
        }
    }
    //Save to database
    public function Save(){     
        /*if (!$this->Exists) {
            $this->Status = GetStatus($this->Approved);
            $sql = "UPDATE `quote_approvals` SET";
            $sql .= " `ref` = '" . mysqli_real_escape_string($GLOBALS["link"],$this->Ref) . "'";
            $sql .= ", `status` = '" . mysqli_real_escape_string($GLOBALS["link"],$this->Status) . "'";
            $sql .= ", `date_approved` = NOW()";
            $sql .= ", `comments` = '" . mysqli_real_escape_string($GLOBALS["link"],$this->Comments) . "'";
            $sql .= " WHERE `approved_by` = '" . mysqli_real_escape_string($GLOBALS["link"],$this->Approved_by) . "' AND `approvID` = '" . mysqli_real_escape_string($GLOBALS["link"],$this->ApprovID) . "'";
            //$this->Exists = true;
            mysqli_query($GLOBALS["link"],$sql);
        }*/
        if ($this->Exists) {
            $sql = "UPDATE `quote_approvals` SET";
            $sql .= " `ref` = '" . mysqli_real_escape_string($GLOBALS["link"],$this->Ref) . "'";
            $sql .= ", `status` = '" . mysqli_real_escape_string($GLOBALS["link"],$this->Status) . "'";
            $sql .= ", `date_approved` = '" . mysqli_real_escape_string($GLOBALS["link"],$this->Date_approved) . "'";
            $sql .= ", `comments` = '" . mysqli_real_escape_string($GLOBALS["link"],$this->Comments) . "'";
            $sql .= " WHERE `ref` = '" . mysqli_real_escape_string($GLOBALS["link"],$this->Ref) . "' AND `approved_by` = '" . mysqli_real_escape_string($GLOBALS["link"],$this->Approved_by) . "'";
            //exit();
            mysqli_query($GLOBALS["link"],$sql);
        }
    }
    //Get Status
    function GetStatus($Approved)
    {
        switch ($Approved) {
            case 0:
                 $status = "Pending";
                break;
            case 1:
                 $status = "Approved";
                break;
             case -1:
                 $status = "Reject";
                break;
            default:
                $status = "Pending";
                break;
        }
        return $status;
    }
    
    function GetApprovalMembersDetails(){
        $users = "";
        $query  = "SELECT `first`, `last`, `email`,`approv_user_id`";
        $query .= " FROM `approval_users`";
        $query .= " INNER JOIN `system_users` ON `system_users`.id = `approval_users`.member WHERE `status` = 1 AND `system_users`.branch_id='".$GLOBALS['sysuser']->branchID."'";
        $result = mysqli_query($GLOBALS["link"],$query);
        while ($arr = mysqli_fetch_assoc($result)) {
            $users .= $arr['first'].",". $arr['last'].",". $arr['email'].",".$arr['approv_user_id'].";";
        }
        return $users;
    }

    
    //public function sendApprovedMail($Mail = "",$Mail2 = "",$quoteID = "",$member ="",$status ="")
    public function sendApprovedMail($quoteID = "",$member ="",$status ="")
    {

      $membersDetails = $this->GetApprovalMembersDetails();
      $users= explode(";",$membersDetails); 
      //Approval member 1
      $details  = explode(",",$users[0]); 
      $membersDetails = $details[0]." ".$details[1];
      $mailmember = $details[2];
      $approv_user_id = $details[3];
      //Approval member 2
      $details  = explode(",",$users[1]); 
      $membersDetails2 = $details[0]." ".$details[1];
      $mailmember2 = $details[2];
      $approv_user_id2 = $details[3];
      //Approval member 3
      $details  = explode(",",$users[2]); 
      $membersDetails3 = $details[0]." ".$details[1];
      $mailmember3= $details[2];
      $approv_user_id3 = $details[3];
      $fromMail = "";

      if($approv_user_id == $member){
          $fromMail  = $mailmember;
          $approvalbyDetails =  $membersDetails;
      }else if($approv_user_id == $member){
          $fromMail  = $mailmember2;
          $approvalbyDetails =  $membersDetails2;
      }else{
          $fromMail  = $mailmember3;
          $approvalbyDetails =  $membersDetails3;
      }

      //Sends email to the respective approval managers
      $mail = new PHPMailer(); 
      $mail->Mailer = 'smtp';

      //change email to $fromMail 
      $mail->setFrom($GLOBALS['sysuser']->email,$approvalbyDetails);
      //$mail->setFrom("noreply@clientassist.co.za","Approval Member: " . $approvalbyDetails);
      $mail->Subject = "Approval of Ellies Quote:".$quoteID;

      $arrMail = array();
      if($mailmember!="")$arrMail[] = $mailmember;
      if($mailmember2!="")$arrMail[] = $mailmember2;
      if($mailmember3!="")$arrMail[] = $mailmember3;
       ///$ccMail and $ccMail2 and ccMail3
      //$mail->AddBCC($ccMail, "Approval Member");
      //$mail->AddBCC($ccMail2, "Approval Member");
      //$mail->AddBCC($ccMail3, "Approval Member");

       $sql = "SELECT  `email` FROM `system_users` WHERE `id` ='".$GLOBALS['sysuser']->id."'";
       $sqlres = mysqli_query($GLOBALS["link"],$sql);
       $email = "";
       while($row = mysqli_fetch_assoc($sqlres)){
          $email= $row["email"];
       }

       $mail->addAddress($email,"Approval Member");

       for($i=0;$i<count($arrMail);$i++){
        if($arrMail[$i] !== $email && $arrMail[$i] !== ""){
          $mail->AddBCC($arrMail[$i], "Approval Member");
        } 
       }
     
      //$mail->AddBCC($Mail, "Approval Member");
      //$mail->AddBCC($Mail2, "Approval Member");

      // message
      $message = '
      <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
      <html xmlns="http://www.w3.org/1999/xhtml">
      <head>
          <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
          <title>Ellies Quote</title>
      </head>
      <body>
          <p>The following quote have been quote has been ' . $status . ' by ' . $approvalbyDetails. ':</p>
          <p>Ouote Reference: ' . $quoteID .'</p>
      </body>
      </html>';

      //convert HTML into a basic plain-text alternative body
      $mail->msgHTML($message);

      // Mail it
      if($mail->send()){
          require_once("header.php");
            $quoteID = $_GET["id"];
            $quoteid = $quoteID;

            $quote = new Quote($quoteid);

            $booktitle = "Mail Sent";
             $reurl = "";
            if (!empty($_GET["reurl"])) {
                $_SESSION["reurl"] = $_GET["reurl"];
                $reurl = $_GET["reurl"];
            }

            $customerid =  $quoteID;

            PrintNavBar($booktitle, true, "quotes.php?customerid=".$customerid, $reurl);
            $customer = new Customer($quote->CustomerID);
          

            print "<h1 data-role='label' data-theme='a' data-transition='fade' data-inline='true' data-icon='search' data-ajax='false' style='color:green'>Quote is set for approval</h1>";

            //Customer Details
            print '<br/>';
            print "<div id='quote-details'>";
            print '<img src="images/details_icon.png" style="float:left; padding-right:100px;"/>';
            print '<table>';
            print '<tr>';
            print '<td align="center" valign="middle"><img src="images/ellies_icon.png" /></td>';
            print '<td align="left" class="heading">Electricity Escalation Rate (%):</td>';
            print '<td align="left" class="quote_vals" valign="middle"><div class="click" id="escalation_rate" style="display: inline">'.$quote->ElectricityEscalationRatePercentage.'</div></td>';
            print '<td align="left" class="customer_icon" valign="middle"><img src="images/name_icon.png" /></td>';
            print '<td align="left" class="customer_vals" valign="middle">'.$customer->Surname.", ".$customer->Name.'</td>';
            print '</tr>';
            print '<tr>';
            print '<td align="center" valign="middle"><img src="images/ellies_icon.png" /></td>';
            print '<td align="left" class="heading">Quote Reference:</td>';
            print '<td align="left" class="quote_vals" valign="middle">'.$quote->QuoteReferenceNo.'</td>';
            print '<td align="left" class="customer_icon" valign="middle"><img src="images/phone_icon.png" /></td>';
            print '<td align="left" class="customer_vals" valign="middle">'.$customer->CellPhone.'</td>';
            print '</tr>';
            print '<tr>';
            print '<td align="center" valign="middle"><img src="images/ellies_icon.png" /></td>';
            print '<td align="left" class="heading">Price/kWh (R):</td>';
            print '<td align="left" class="quote_vals" valign="middle"><div class="click" id="kwh_price" style="display: inline">'.$quote->KWhPrice.'</div></td>';
            print '<td align="left" class="customer_icon" valign="middle"><img src="images/email_icon.png" /></td>';
            print '<td align="left" class="customer_vals" valign="middle">'.$customer->Email.'</td>';
            print '</tr>';
            print '<tr>';
            print '<td align="center" valign="middle"><img src="images/ellies_icon.png" /></td>';
            print '<td align="left" class="heading">Discount:</td>';
            print '<td align="left" class="quote_vals" valign="middle"><div class="click" id="discount" style="display: inline">'.$quote->Discount.'</div></td>';
            print '<td align="left" class="customer_icon" valign="middle"><img src="images/property_icon.png" /></td>';
            print '<td align="left" class="customer_vals" valign="middle">'.$quote->Property.'</td>';
            print '</tr>';
            print '<tr>';
            print '<td align="center" valign="middle"><img src="images/ellies_icon.png" /></td>';
            print '<td align="left" class="heading">Date:</td>';
            print '<td align="left" class="quote_vals" valign="middle">'.$quote->DateCreated.'</td>';
            print '<td align="left" class="customer_icon" valign="middle"><img src="images/property_icon.png" /></td>';
            $isInstall = ($quote->DoInstall==1)?"Includes Installation":"Excludes Installation";
            print '<td align="left" class="customer_vals" valign="middle"><div class="clickselect" id="do_install" style="display: inline">'.$isInstall.'</td>';
            print '</tr>';
            print '</table>';
            print "</div>";
            print '<br/><br/>';
            print "<a href=\"editquotecomm.php?&id=".$quote->QuoteID."\"  data-role='button' data-theme='a' data-transition='fade' data-inline='true' data-icon='info' data-ajax='false'>Back</a>";

            require_once("footer.php");
      }else{
        print "<style>html{background:#ededed;color:#A71110;font-family:calibri;font-size: 14px}</style>";
        print "The email sent was unsuccessful.<a href=javascript:history.go(-1)> << Back</a>" ;
        print "Mailer Error: " . $mail->ErrorInfo;
      }    
    }
}
?>