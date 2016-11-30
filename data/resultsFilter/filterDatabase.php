<?php 
$mysqHost = "localhost";
$mysqlUsr = "instoreellies";
$mysqlPass = "letmein999";

$pconn = mysql_connect($mysqHost,$mysqlUsr,$mysqlPass);

mysql_select_db("instoreellies") ;

header('Content-type: text/xml');
print "<?php xml version=\"1.0\"?>";
print "<data>";

$result = mysql_query("Select q.ref, q.date_created, CONCAT(customers.name,' ', customers.surname) as customer, CONCAT(system_users.first,' ', 			 	  					    system_users.last) as rep, stores.store, branches.branch from quotes q
						INNER JOIN customers
						ON q.customer_id = customers.id
						INNER JOIN system_users
						ON system_users.id = q.created_by
						INNER JOIN stores 
						ON stores.id = system_users.store_id
						INNER JOIN branches
						ON branches.id = system_users.branch_id;");

	$fieldCount = mysql_num_fields($result);
	while($row = mysql_fetch_array($result))
		{
			print "<row>";

			for($i=0; $i < $fieldCount; $i++)
			{
				$fieldName = mysql_field_name($result, $i);             
				if ($fieldName == "date_created")
				{
					$fieldVar = $row["$fieldName"];
					$dateFormatted =  date("Y-m-d", strtotime($fieldVar));  
					$fieldVarDone = str_replace("\n","<br>",$dateFormatted);
					print "<$fieldName>".htmlspecialchars($fieldVarDone)."</$fieldName>"; 
				}
				
				else
				{       
					$fieldVar = $row["$fieldName"];
					$fieldVarDone = str_replace("\n","<br>",$fieldVar);
					print "<$fieldName>".ucwords(htmlspecialchars($fieldVarDone))."</$fieldName>"; 
				}
			}
			print "</row>";

		}

$resultStore = mysql_query("SELECT DISTINCT(store) as storeList FROM stores;");
			print "<row label=\"hidden\">";
			print "<storeList>";
	while($rowStore = mysql_fetch_array($resultStore))
		{
			print "<store>";
			print $rowStore['storeList'];
			print "</store>";
		}
					print "</storeList>";

					print "</row>";

$resultBranch = mysql_query("SELECT branch as branchList FROM branches;");
			print "<row label=\"hidden\">";
			print "<branchList>";
	while($rowBranch = mysql_fetch_array($resultBranch))
		{
			print "<branch>";
			print $rowBranch['branchList'];
			print "</branch>";
		}
					print "</branchList>";

					print "</row>";

$resultRep = mysql_query("SELECT CONCAT(system_users.first,' ', system_users.last) as repsList FROM system_users order by system_users.first ASC;");
			print "<row label=\"hidden\">";
			print "<repList>";
	while($rowRep = mysql_fetch_array($resultRep))
		{
			print "<rep>";
			print ucwords($rowRep['repsList']);
			print "</rep>";
		}
					print "</repList>";

					print "</row>";

print "</data>";

mysql_close($pconn);

?>