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
if (empty($booktitle)) {
    $booktitle = "Ellies In-Store";
}
?>
<!DOCTYPE html> 
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable = no">
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <title><?php  echo  $booktitle?></title>
    <link rel="shortcut icon" href="images/favicon.ico" />
    <link rel="stylesheet" href="jmobile/jquery.mobile-1.3.0.min.css" />
    <link rel="stylesheet" href="css/themes/el3.min.css" />
    <script src="js/jquery-1.8.2.min.js"></script>
    <script src="js/jmobile.customize.js"></script>
    <script src="jmobile/jquery.mobile-1.3.0.js"></script>
    <script src="js/quotes.js"></script>
	<script src="js/jquery.jeditable.js"></script>
    <style>
    body {

    }
    p {
        font-size:18px;
        text-align:justify;
        padding-left:10px;
        padding-right:10px;
    }
    .price-old {
        color:red;
        font-weight:bold;
    }
    .price-new {
        color:#507B51;
        font-weight:bold;        
    }
    .price-saving {
        color:green;
        font-weight:bold;
    }
    
    .grassstrip {
        background-image:url(images/grass_s.jpg);
        background-repeat: repeat-x;
        background-position: bottom;
    }
    .headertexta {
        font-family:arial;
        font-weight:bold;
        font-size:30px;
        color: #79d22a;
        letter-spacing: -1px;
    }
.click {
	font-weight:bold;
	text-decoration:underline;
}

.clickselect {
	font-weight:bold;
	text-decoration:underline;
}

.clickselecthours {
	font-weight:bold;
	text-decoration:underline;
}
.hbargreen {
    background: #aeda4f; /* Old browsers */
    background: -moz-linear-gradient(top,  #aeda4f 0%, #47ae39 49%, #20ad0a 50%, #70c72b 100%); /* FF3.6+ */
    background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#aeda4f), color-stop(49%,#47ae39), color-stop(50%,#20ad0a), color-stop(100%,#70c72b)); /* Chrome,Safari4+ */
    background: -webkit-linear-gradient(top,  #aeda4f 0%,#47ae39 49%,#20ad0a 50%,#70c72b 100%); /* Chrome10+,Safari5.1+ */
    background: -o-linear-gradient(top,  #aeda4f 0%,#47ae39 49%,#20ad0a 50%,#70c72b 100%); /* Opera 11.10+ */
    background: -ms-linear-gradient(top,  #aeda4f 0%,#47ae39 49%,#20ad0a 50%,#70c72b 100%); /* IE10+ */
    background: linear-gradient(to bottom,  #aeda4f 0%,#47ae39 49%,#20ad0a 50%,#70c72b 100%); /* W3C */
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#aeda4f', endColorstr='#70c72b',GradientType=0 ); /* IE6-9 */
    height:100%;
    font-size: 26px;
    font-weight:bold;
    color:white;
    text-align: center;
    line-height:50px;
    
-webkit-border-bottom-right-radius: 8px;
-webkit-border-bottom-left-radius: 8px;
-moz-border-radius-bottomright: 8px;
-moz-border-radius-bottomleft: 8px;
border-bottom-right-radius: 8px;
border-bottom-left-radius: 8px;
border:2px solid #2EAB40;
    
    
text-shadow: 2px 2px 1px rgba(46, 171, 64, 1);
    
}

.quoteheadertable th {
    width:25%;
    background-color: #E9E9E9;
    font-size: 18px;
    font-weight:bold;
    color:#32312F;
    text-align: left;
    padding-left:8px;
   
    -webkit-border-radius: 8px;
    -moz-border-radius: 8px;
    border-radius: 8px;
    border:2px solid #32312F;
}


.quoteheadertable td {
    width:25%;
    background-color: #80CD29;
    font-size: 18px;
    font-weight:bold;
    color:#FFFFFF;
    text-align: left;
    padding-left:8px;
   
    -webkit-border-radius: 8px;
    -moz-border-radius: 8px;
    border-radius: 8px;
    border:0px solid #32312F;
    text-shadow: 0px 0px 0px rgba(46, 171, 64, 1);
}

.quotegridtitle {
    background-color: #57585A;
    font-size: 16px;
    font-weight:bold;
    color:#FFFFFF;
    text-align: left;
    padding-left:8px !important;
    padding-right:0px !important;
    padding-top:0px !important;
    padding-bottom:0px !important;
   
    border:2px solid #32312F;
    text-shadow: 0px 0px 0px rgba(46, 171, 64, 1);    
}

.borad-AB {
    -webkit-border-top-right-radius: 8px;
    -webkit-border-top-left-radius: 8px;
    -moz-border-radius-topright: 8px;
    -moz-border-radius-topleft: 8px;
    border-top-right-radius: 8px;
    border-top-left-radius: 8px;
}

.borad-CD {
    -webkit-border-bottom-right-radius: 8px;
    -webkit-border-bottom-left-radius: 8px;
    -moz-border-radius-bottomright: 8px;
    -moz-border-radius-bottomleft: 8px;
    border-bottom-right-radius: 8px;
    border-bottom-left-radius: 8px;
}

.borad-C {
    -webkit-border-bottom-left-radius: 8px;
    -moz-border-radius-bottomleft: 8px;
    border-bottom-left-radius: 8px;
}

.borad-D {
    -webkit-border-bottom-right-radius: 8px;
    -moz-border-radius-bottomright: 8px;
    border-bottom-right-radius: 8px;
}

.color-A {
    background-color: #E9E9E9;
    font-size: 16px;
    font-weight:bold;
    color:#32312F;
    border:2px solid #32312F;
    text-shadow: 0px 0px 0px rgba(46, 171, 64, 1);
    padding-left: 10px;
}

.color-B {
    background-color: #FFFFFF;
    font-size: 16px;
    font-weight:bold;
    color:#32312F;
    border:2px solid #32312F;
    padding-left:8px;
    text-shadow: 0px 0px 0px rgba(46, 171, 64, 1);
}

.color-C {
    background-color: #ED1B24;
    font-size: 16px;
    font-weight:bold;
    color:#FFFFFF;
    border:0px solid #32312F;
    text-align:left;
    padding-left:8px;
    text-shadow: 0px 0px 0px rgba(46, 171, 64, 1);
}

.color-D {
    background-color: #80CD29;
    font-size: 16px;
    font-weight:bold;
    color:#FFFFFF;
    border:0px solid #32312F;
    text-align:left;
    padding-left:8px;
    text-shadow: 0px 0px 0px rgba(46, 171, 64, 1);
}

.color-E {
    background-color: #2D4B0D;
    font-size: 16px;
    font-weight:bold;
    color:#FFFFFF;
    border:0px solid #32312F;
    text-align:left;
    padding-left:8px;
    text-shadow: 0px 0px 0px rgba(46, 171, 64, 1);
}

    .divlabel {
        background-color:#80CD29;
        text-shadow: 0px 0px 0px rgba(46, 171, 64, 1);
        color:white;
        font-size:16px;
        font-weight:bold;
        -webkit-border-radius: 8px;
        -moz-border-radius: 8px;
        border-radius: 8px;
        padding-left:15px;
        padding-right:8px;
    }

    .divfield {
        align:left;
        width:80%;
        float:right;
        text-align: left;
        border:1px solid red;
    }
	
	.poop{color:red;}
    </style>
    <script language="javascript">
        function EditItem(recordid) {
            alert("here " + recordid);
        }
    </script>
</head> 
<body>
<div data-role="page" id="login" class="ui-body-a">
    <div data-role="content">
