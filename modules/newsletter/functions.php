<?php

	if(isset($_POST['call'])){
    	if($_POST['call'] == "getNewsLetterTypeInfo"){
    		getNewsLetterTypeInfo();
    	}
    }
    function getNewsLetterTypeInfo(){
        define('MYSQL_SERVER' ,'dedi48.jnb1.host-h.net');
        define('MYSQL_USERNAME' ,'cltdrx_ellsdev');
        define('MYSQL_PASSWORD' ,'JzESNju8');
        define('MYSQL_DATABASE' ,'cltdrx_ellsdev');
        $connection = new mysqli(MYSQL_SERVER, MYSQL_USERNAME, MYSQL_PASSWORD,MYSQL_DATABASE);
        if($connection->connect_errno){
            print "DB connection error.";
            exit(-1);
        }

        $newslettertypeID = (int)isset($_POST["newslettertypeID"])?$_POST["newslettertypeID"]:0;
        $query  = "SELECT *";
        $query .= " FROM `news_letter_types`";
        $query .= " WHERE `news_letter_types`.id= ".$newslettertypeID;       
        $result = mysqli_query($connection,$query);
        $arr = mysqli_fetch_assoc($result);
        print json_encode($arr);
    }
?>