<?php

session_start();
require_once("approval.class.php");
require_once("customer.class.php");
require_once("quote.class.php");
require_once("quoteitem.class.php");
require_once("class.phpmailer.php");

class Submissions
{
    public $SubID;
    public $Ref;
    public $Date_Submitted;
    public $Created_by;
    public $Status;
    public $Comments;
    public $Printed;
    public $Exists = false;
    public $Settings;
    public $Row;
    private $quote_approval;
    private $ApprovalMembers;
 
    //Constructor
    public function __construct($ref = 0)
    {
        $sql = "SELECT * FROM `quote_submissions` WHERE `ref` = '" . mysqli_real_escape_string($GLOBALS["link"],$ref) . "'";
        $sqlres = mysqli_query($GLOBALS["link"],$sql);
        //Record exists
        while($row = mysqli_fetch_assoc($sqlres))
        {
                $this->Row=$row;
                $this->Exists = true;
                $this->SubID = $row["subID"];
                $this->Date_Submitted= $row["date_submitted"];
                $this->Ref= $row["ref"];
                $this->Created_by= $row["created_by"];
                $this->Status= $row["status"];
                $this->Comments= $row["comments"];
                $this->Printed= $row["printed"];
        }
    }
    //Save to database
    public function Save()
    {     
        if (!$this->Exists) {
            $this->Status = GetStatus($this->Created_by);
            $sql = "INSERT INTO `quote_submissions` (`date_submitted`,`ref`, `created_by`,`status`,`comments`,`printed`) VALUES (NOW(), '" . mysqli_real_escape_string($GLOBALS["link"],$this->Ref) . "', '" . mysqli_real_escape_string($GLOBALS["link"],$this->Created_by) ."', '"  . mysqli_real_escape_string($GLOBALS["link"],$this->Status) ."', '"  . mysqli_real_escape_string($GLOBALS["link"],$this->Comments) ."', '" . mysqli_real_escape_string($GLOBALS["link"],$this->Printed) . "')";
            $this->SubID = mysql_insert_id();
            $this->Exists = true;
            mysqli_query($GLOBALS["link"],$sql);
        }
        if ($this->Exists) {
            $sql = "UPDATE `quote_submissions` SET";
            $sql .= " `date_submitted` = '" . mysqli_real_escape_string($GLOBALS["link"],$this->Date_Submitted) . "'";
            $sql .= ", `ref` = '" . mysqli_real_escape_string($GLOBALS["link"],$this->Ref) . "'";
            $sql .= ", `created_by` = '" . mysqli_real_escape_string($GLOBALS["link"],$this->Created_by) . "'";
            $sql .= ", `status` = '" . mysqli_real_escape_string($GLOBALS["link"],$this->Status) . "'";
            $sql .= ", `comments` = '" . mysqli_real_escape_string($GLOBALS["link"],$this->Comments) . "'";
            $sql .= ", `printed` = '" . mysqli_real_escape_string($GLOBALS["link"],$this->Printed) . "'";
            $sql .= " WHERE `subID` = '" . mysqli_real_escape_string($GLOBALS["link"],$this->SubID) . "'";
            mysqli_query($GLOBALS["link"],$sql);
        }
    }
    //Get Status
    function GetStatus($userID)
    {
        switch ($userID) {
            case 0:
                 $status = "Pending";
                break;
            case 1:
                 $status = "Approved";
                break;
            case 2:
                 $status = "Open";
                break;
            case 3:
                 $status = "To be confirmed";
                break;
             case 4:
                 $status = "Print";
                break;
             case -1:
                 $status = "Reject";
                break;
            default:
                $status = "To be confirmed";
                break;
        }
        return $status;
    }
                                
    function GetApprovalMembers(){
        $user ="";
        $sql = "SELECT * FROM `approval_users` INNER JOIN `system_users` ON `system_users`.id = `approval_users`.member WHERE `status` = 1 AND `system_users`.branch_id='".$GLOBALS['sysuser']->branchID."'";
        $sqlres = mysqli_query($GLOBALS["link"],$sql);
        while($row = mysqli_fetch_assoc($sqlres))
        {
            $this->ApprovalMembers = $row["member"];
            $user .= $this->ApprovalMembers.";";
        }
        return $user;
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

    function AddQuoteApprovalMembers($ref = 0,$Status=""){
        $approvalMembers= $this->GetApprovalMembers();
        $users = explode(";",$approvalMembers);
        $member = $users[0];
        $member2 = $users[1];
        $member3 = $users[2];

        $sql = "INSERT INTO `quote_approvals` (`approved_by`,`status`, `ref`) VALUES ('" . mysqli_real_escape_string($GLOBALS["link"],$member) . "', '" . mysqli_real_escape_string($GLOBALS["link"],$Status) ."', '"  . mysqli_real_escape_string($GLOBALS["link"],$ref ) ."')";
        mysqli_query($GLOBALS["link"],$sql);
        $sql = "INSERT INTO `quote_approvals` (`approved_by`,`status`, `ref`) VALUES ('" . mysqli_real_escape_string($GLOBALS["link"],$member2) . "', '" . mysqli_real_escape_string($GLOBALS["link"],$Status) ."', '"  . mysqli_real_escape_string($GLOBALS["link"],$ref ) ."')";
        mysqli_query($GLOBALS["link"],$sql);
        $sql = "INSERT INTO `quote_approvals` (`approved_by`,`status`, `ref`) VALUES ('" . mysqli_real_escape_string($GLOBALS["link"],$member3) . "', '" . mysqli_real_escape_string($GLOBALS["link"],$Status) ."', '"  . mysqli_real_escape_string($GLOBALS["link"],$ref ) ."')";
        mysqli_query($GLOBALS["link"],$sql);
    }

    //Sends approval message
    public function SendApprovalSubmissions($ref = 0)
    {
        //Add quote for approval
        $this->AddQuoteApprovalMembers($ref,GetStatus(0));

        $approvalMembers= $this->GetApprovalMembers();
        $users = explode(";",$approvalMembers);
        $member = $users[0];
        $member2 = $users[1];
        $member3 = $users[2];

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

        $this->Sent_to = $member;
        $quote_approval= new Approval($ref,$this->Sent_to);
        $quote_approval->Approved_by =  $member;
        $quote_approval->Ref = $ref;
        $quote_approval->Approved = 0;
        $quote_approval->ApprovID = $approv_user_id;
        $quote_approval->Status = GetStatus(0);
        $quote_approval->Save();

       // echo "<script>window.alert('Quote SENTTO:10');</script>";
        
        $quote_approval->Approved_by = $member2;
        $quote_approval->Ref = $ref;
        $quote_approval->Approved = 0;
        $quote_approval->ApprovID = $approv_user_id2;
        $quote_approval->Status = GetStatus(0);
        $quote_approval->Save();

       /// echo "<script>window.alert('Quote SENTTO:158');</script>";
        
        $quote_approval->Approved_by = $member3;
        $quote_approval->Ref = $ref;
        $quote_approval->Approved = 0;
        $quote_approval->ApprovID = $approv_user_id3;
        $quote_approval->Status = GetStatus(0);
        $quote_approval->Save();

       /// echo "<script>window.alert('Quote SENTTO:2');</script>";

        //Sends email to the respective approval managers
        $mail = new PHPMailer(); 
        $mail->Mailer = 'smtp';
        $sql = "SELECT  `first`,`last` FROM `system_users` WHERE `id` ='".$GLOBALS['sysuser']->id."'";
        $sqlres = mysqli_query($GLOBALS["link"],$sql);
        $name = "";
        while($row = mysqli_fetch_assoc($sqlres)){
            $name= $row["first"]. " ".$row["last"];
        }

        $mail->setFrom($GLOBALS['sysuser']->email,$name);
        ///$mail->setFrom("noreply@clientassist.co.za","Theo Developer");

        //Set who the message is to be sent to
        $mail->addAddress($mailmember,"Approval Member");
        if($mailmember2!="")$mail->AddBCC($mailmember2, "Approval Member");
        if($mailmember3!="")$mail->AddBCC($mailmember3, "Approval Member");

        //email:theomalongete@ymail.com
        //cassandra.b@uthgroup.co.za
        //christiaan.b@uthgroup.co.za
        /*$mail->addAddress("theomalongete@ymail.com","Approval Member 1");
        $mail->AddBCC("cassandra.b@uthgroup.co.za", "Approval Member 2");
        $mail->AddBCC("christiaan.b@uthgroup.co.za", "Approval Member 3");*/

        $mail->Subject = "Approval of Ellies Quote:".$ref;

        //message
        $message = '
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <title>Ellies Quote</title>
        </head>
        <body>
            <p>The following quote needs to be approved:<br/><br/>
            Quote Reference: ' . $_SESSION['quoteID'] .'<br/>
            Quote Link:<a href="'.$_SESSION['quoteLink'].'">Click here to view the quote.</a></p><br/><br/>
            <table>
                    <th style="font-weight:bold;"><u>Members</u></th>
                    <th style="font-weight:bold;"><u>Status</u></th>';
                if($mailmember!=""){
                    $message .= '<tr>
                                    <td>'. $membersDetails.'</td>
                                    <td>'. GetStatus(0).'</td>
                                </tr>';
                }

                if($mailmember2!=""){
                    $message .= '<tr>
                                    <td>'. $membersDetails2.'</td>
                                    <td>'. GetStatus(0).'</td>
                                </tr>';
                }

                if($mailmember3!=""){
                    $message .= '<tr>
                                    <td>'. $membersDetails3.'</td>
                                    <td>'. GetStatus(0).'</td>
                                </tr>';
                } 
           $message .= '</table> 
        </body>
        </html>';

        //convert HTML into a basic plain-text alternative body
        $mail->msgHTML($message);

        // Mail it
        if($mail->send()){
            require_once("header.php");
            $quoteid = $_GET["id"];

            $quote = new Quote($quoteid);

            if ($quoteid == 0) {
                if ($_GET["customerid"] > 0) {
                    //===Alternate Start New Quote==
                    //===This is usually called from the quotes listing page quotes.php==
                    $quote = new Quote();
                    $quote->CustomerID = $_GET["customerid"];
                    $quote->CreatedBy = $sysuser->id;
                    $quote->Save();
                    $quoteid = $quote->QuoteID;
                    $_GET["id"] = $quoteid;
                }
            }
            $booktitle = "Mail Sent";
             $reurl = "";
            if (!empty($_GET["reurl"])) {
                $_SESSION["reurl"] = $_GET["reurl"];
                $reurl = $_GET["reurl"];
            }

            $customerid =  $_GET["id"];

            PrintNavBar($booktitle, true, "quotes.php?customerid=".$customerid, $reurl);
            $customer = new Customer($quote->CustomerID);
          

            print "<h1 data-role='label' data-theme='a' data-transition='fade' data-inline='true' data-icon='search' data-ajax='false' style='color:Orange'>Quote has been sent for approval</h1>";

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