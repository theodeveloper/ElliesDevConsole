<?php 
    session_start();
    require_once("customer.class.php");
    require_once("quote.class.php");
    require_once("quoteitem.class.php");
    require_once("class.phpmailer.php");
    require_once("../../inc/system_user.php");

    class Escalations
    {
        public $EscID;
        public $Escalated_to;
        public $Ref;
        public $Date_Created;
        public $Status;
        public $Exists = false;
        public $Row;
        private $Link;

        //Constructor
        public function __construct(){

        }

        //Save to database
        public function Save(){
    
            $this->Link =  mysqli_connect("dedi48.jnb1.host-h.net","clieng_ellies","S5d453g8","clieng_ellies");
            $sql = "INSERT INTO `quote_escalation` (`date_created`,`ref`, `escalated_to`,`status`) VALUES (NOW(), '" . mysqli_real_escape_string($this->Link,$this->Ref)."', '".mysqli_real_escape_string($this->Link,$this->Escalated_to) ."', '"  . mysqli_real_escape_string($this->Link,$this->Status) ."')";
            $this->Exists = true;
            mysqli_query($this->Link,$sql);
            return true;
        }

        function GetEscalationMembersDetails(){
            $users = "";
            $query  = "SELECT `first`, `last`, `email`,`escalation_users`.member, `escalation_users`.time_escalation,username";
            $query .= " FROM `escalation_users`";
            $query .= " INNER JOIN `system_users` ON `system_users`.id = `escalation_users`.member WHERE `status` = 1";

            $this->Link =  mysqli_connect("dedi48.jnb1.host-h.net","clieng_ellies","S5d453g8","clieng_ellies");
            $result = mysqli_query($this->Link,$query);
            $users = "";
            while ($arr = mysqli_fetch_assoc($result)) {
                $users .= $arr['first'].",". $arr['last'].",". $arr['email'].",".$arr['member'].",".$arr['time_escalation'].",".$arr['username'].";";
            }
            return $users;
        }

        function getCriteria($esc_user_id){
            $this->Link =  mysqli_connect("dedi48.jnb1.host-h.net","clieng_ellies","S5d453g8","clieng_ellies");
            $GLOBALS["link"] =   $this->Link;
            $sysuser = new userType($esc_user_id);
            $sql = "SELECT `type` FROM `channels` WHERE `id`=".$sysuser->retailChannel;
            $sqlres = mysqli_query($this->Link,$sql);
            $row = mysqli_fetch_assoc($sqlres);
            $channeltype = $row['type'];

            if ($channeltype == "Commercial" || $channeltype =="Franchises"){
                if ($sysuser->isSuperAdmin == FALSE){
                    $cond =" AND created_by in (SELECT id from system_users where store_id = ".$sysuser->storeID.")";
                } else {
                    $cond = " AND created_by in (SELECT id from system_users where branch_id =".$sysuser->branchID.")";
                }
            } else {
                if ($sysuser->isSuperAdmin == FALSE){
                    $cond =" AND created_by in (SELECT id from system_users where store_id = ".$sysuser->storeID.")";
                } else {
                    $cond = " AND created_by in (SELECT id from system_users where branch_id IN (SELECT id from branches where channel=".$sysuser->retailChannel."))";
                }
            } 
          return $cond;       
        }

        function countWeekendDays($start, $end) {
            // $start in timestamp
            // $end in timestamp

            $start = (!is_numeric($start)?strtotime($start):$start);
            $end = (!is_numeric($end)?strtotime($end):$end);

            $iter = 24*60*60; // whole day in seconds
            $count = 0; // keep a count of Sats & Suns

            for($i = $start; $i <= $end; $i=$i+$iter)
            {
                if(Date('D',$i) == 'Sat' || Date('D',$i) == 'Sun')
                {
                    $count++;
                }
            }
            return $count;
        }

        public function timeDifferenceArray($start,$end,$return='days',$excludeWeekends=false) {
            //change times to Unix timestamp.
            $start = (!is_numeric($start)?strtotime($start):$start);
            $end = (!is_numeric($end)?strtotime($end):$end);

            if($start > $end) {
                return array('Please make sure the start date is less than the end date.');
            }

            //subtract dates
            $difference = $end - $start;

            if ($excludeWeekends) $weekendDays = $this->countWeekendDays($start,$end);

            $time = array();
            //calculate time difference.
            switch($return) {
                    case 'days':
                         $days = floor($difference/86400);
                            $difference = $difference % 86400;
                                if ($days) {
                                    if ($excludeWeekends)
                                        $time["days"] = $days+$weekendDays;
                                    else
                                        $time["days"] = $days;
                                }
                    case 'hours':
                        $hours = floor($difference/3600);
                            $difference = $difference % 3600;
                                if ($hours) {
                                    if ($excludeWeekends)
                                        $time["hours"] = $hours+($weekendDays*24);
                                    else
                                        $time["hours"] = $hours;
                                }
                    case 'minutes':
                        $minutes = floor($difference/60);
                            $difference = $difference % 60;
                                if ($minutes) $time["minutes"] = $minutes;
                    case 'seconds':
                        $seconds = $difference;
                            $time["seconds"] = $seconds;
            }     
            return $time;   
        }

        function pending_quotations($time_escalation,$esc_user_id) {
            $this->Link =  mysqli_connect("dedi48.jnb1.host-h.net","clieng_ellies","S5d453g8","clieng_ellies");
            $GLOBALS["link"] =   $this->Link;
            $count = 0;
            $pendingquotations2 = '';            
            $sysuser = new userType($esc_user_id);
            $sql = "SELECT `type` FROM `channels` WHERE `id`=".$sysuser->retailChannel;
            $sqlres = mysqli_query($this->Link,$sql);
            $row = mysqli_fetch_assoc($sqlres);
            $channeltype = $row['type'];

            if ($channeltype == "Commercial" || $channeltype =="Franchises"){
                $suffix="comm";
            }else {
                $suffix="";
            }
            $sql = "SELECT * FROM `quotes` where approved=0 and complete=1";
            $sql.= $this->getCriteria($esc_user_id);
            $sql .= " ORDER BY `date_created` DESC";
            $sqlres = mysqli_query($this->Link,$sql);
            $end = date('Y-m-d H:i:s');
            while($row = mysqli_fetch_assoc($sqlres)) {
                $quote = new Quote($row["id"]);
                $customer = new Customer($quote->CustomerID);
                //$start = date("Y-m-d H:i:s", strtotime($quote->DateCreated));
                $start = date("Y-m-d H:i:s", strtotime($quote->LastUpdated));
                $hours = $this->timeDifferenceArray($start,$end,'hours',false);

                if($hours['hours'] > $time_escalation){
                    $pendingquotations2 .= "<tr>
                                <td align='center' width='80'>
                                   <a href='http://ellies.clientassist.co.za/quotes/editquote".$suffix.".php?id=".$row["id"]."&reurl=completed_quotations'>".$quote->QuoteReferenceNo."</a>
                                </td>
                                <td align='center' width='80'>".date("Y-m-d", strtotime($quote->DateCreated))."</td>
                                <td align='center'>".$customer->Surname.", ".$customer->Name."</td>
                                <td align='center'  width='80'>".$customer->CellPhone."</td>
                                <td align='center'>".$quote->CreatedByUserName."</td>
                            </tr>";
                    $count++;
                }       
            }

            $pendingquotations = '
            <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
            <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <title>Ellies Quotes</title>
            </head>
            <body>
                <p>The following '.$count.' quotes have been escalated to you as they require your attention:</p><br/>
                <table cellspacing="3">
                    <thead>
                        <tr>
                            <th style="font-weight:bold;">Quote Ref</th>
                            <th style="font-weight:bold;">Date</th>
                            <th style="font-weight:bold;">Customer</th>
                            <th style="font-weight:bold;">Cellphone</th>
                            <th style="font-weight:bold;">Created By</th>
                        </tr>
                    </thead>
                     <tbody>';
            $pendingquotations2 .= '</table></body></html>';
            $pendingquotations .=  $pendingquotations2 ;
            return $pendingquotations;
        }
  
        //Sends Escalation message
        public function SendEscalations(){
            $this->Link =  mysqli_connect("dedi48.jnb1.host-h.net","clieng_ellies","S5d453g8","clieng_ellies");
            $GLOBALS["link"] =   $this->Link;

            $membersDetails= $this->GetEscalationMembersDetails();
            $users = explode(";",$membersDetails);
            $num = count($users);
            //Sends email to the respective Escalation managers
            $mail = new PHPMailer(); 
            $mail->Mailer = 'smtp';
            for($i=0;$i<$num;$i++){
                $details  = explode(",",$users[$i]); 
                $membersDetails = $details[0]." ".$details[1];
                //$mailmember = $details[2];
                $esc_user_id = $details[3];
                $esc_time_escalation = $details[4];
                $esc_username = $details[5];

                //$mailmember = "cassandra.b@uthgroup.co.za";
                $mailmember = "gary@uthgroup.co.za";
                //$mailmember = "theo.m@uthgroup.co.za";

                $mail->setFrom("noreply@clientassist.co.za","Theo Developer");
                $mail->addAddress($mailmember,"Escalation Member");
                $mail->Subject = "Escalation of Ellies Quote";

                //Message
                $message = $this->pending_quotations($esc_time_escalation,$esc_username);

                //convert HTML into a basic plain-text alternative body
                $mail->msgHTML($message);    
                $sql = "SELECT * FROM `quote_escalation`  WHERE `escalated_to` ='".$esc_user_id ."'";

                $this->Link =  mysqli_connect("dedi48.jnb1.host-h.net","clieng_ellies","S5d453g8","clieng_ellies");
                $sqlres = mysqli_query($this->Link,$sql);
                $status = "";
                while($row = mysqli_fetch_assoc($sqlres)) {
                    $status =  $row["status"];
                    $date_created =  $row["date_created"];  
                }
                
                if($status !== "Sent"){
                    // Mail it
                    if(!$mail->send()){
                        print "<style>html{background:#ededed;color:#A71110;font-family:calibri;font-size: 14px}</style>";
                        print "The email sent was unsuccessful.<a href=javascript:history.go(-1)> << Back</a>" ;
                        print "Mailer Error: " . $mail->ErrorInfo;
                         return false; 
                    }else{
                       $this->Status = "Sent";
                       $this->Escalated_to = $esc_user_id;
                       $this->Save();
                       return true; 
                    }
                }else{
                    return false; 
                }             
            }
        }
    }

    $quoteEsc = new Escalations();
    $Sent = $quoteEsc->SendEscalations();
    if($Sent){
        print "Message Sent";
    }else{
        print "Message already Sent";
    }   
?>