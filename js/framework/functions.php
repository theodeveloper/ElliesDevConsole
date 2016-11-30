<?php
	if(isset($_POST['call'])){
    	if($_POST['call'] == "getRegCompaniesInfo"){
    		getRegCompaniesInfo();
    	}elseif($_POST['call'] == "getCompaniesInfo"){
            getCompaniesInfo();
        }
    }

    //Get Region Companies Info
    function getRegCompaniesInfo(){
        //Creates a database connection
        $db_host = "197.242.68.183";
        $db_user = "theo_mysql";
        $db_pass = 'Theo456#';
        $db_name = "joziatwork_mysql";
        $connection = mysqli_connect($db_host,$db_user, $db_pass,$db_name);
        //Test if connection occured
        if(mysqli_connect_errno()){
            die("Database connection failed:".
                mysqli_connect_error().
                "(" . mysqli_connect_errno() . ")"    
            );
        }      

        $regID = (int)isset($_POST["regID"])?$_POST["regID"]:0;
        $query  = "SELECT `companies`.id,`company_name`,company_code";
        $query .= " FROM `companies` INNER JOIN `regions` ON `regions`.id = region_id";
        $query .= " WHERE `companies`.status='Active' AND `region_id`=".$regID;
        $result = mysqli_query($connection,$query);
        $array = array();
        while ($arr = mysqli_fetch_assoc($result)) {
             $array[] = $arr;
        }
        print json_encode($array);
    }

    //Get All Region Companies Info
    function getCompaniesInfo(){
        //Creates a database connection
        $db_host = "197.242.68.183";
        $db_user = "theo_mysql";
        $db_pass = 'Theo456#';
        $db_name = "joziatwork_mysql";
        $connection = mysqli_connect( $db_host,$db_user, $db_pass,$db_name);
        //Test if connection occured
        if(mysqli_connect_errno()){
            die("Database connection failed:".
                mysqli_connect_error().
                "(" . mysqli_connect_errno() . ")"    
            );
        }  

        $compID = (int)isset($_POST["compID"])?$_POST["compID"]:0;
        $query  = "SELECT *";
        $query .= " FROM `companies`";
        $query .= " WHERE `companies`.id=".$compID;
        $result = mysqli_query($connection,$query);
        $arr = mysqli_fetch_assoc($result);
        print json_encode($arr);
    }
?>