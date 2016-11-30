<?php 
$mysqHost = "localhost";
$mysqlUsr = "instoreellies";
$mysqlPass = "letmein999";

$pconn = mysql_connect($mysqHost,$mysqlUsr,$mysqlPass);

mysql_select_db("instoreellies") ;

	//$result = mysql_query ("SELECT * FROM `dealerships` WHERE `dealer_email` = '".$_REQUEST['reg_email']."';");
	//$row = mysql_fetch_array($result);
$action = $_REQUEST['action'];
header('Content-type: text/xml');
print "<?php xml version=\"1.0\"?>";

if($action == "populate")
{
	print "<node>";
	print "<action>$action</action>";

	$item = strtolower($_REQUEST['queryItem']);
	if($item == "reps")
	{
		$result = mysql_query ("SELECT * FROM `system_users`;");
	}
	
	else if($item == "stores")
	{
		$result = mysql_query("SELECT * FROM `stores`;");
	}
	
	else if($item == "branches")
	{
		$result = mysql_query("SELECT * FROM `branches`;");
	}
	
	$fieldCount = mysql_num_fields($result);
	while($row = mysql_fetch_array($result))
		{
			print"<$item>";

			for($i=0; $i < $fieldCount; $i++)
			{
				$fieldName = mysql_field_name($result, $i);                    
				$fieldVar = $row["$fieldName"];
				$fieldVarDone = str_replace("\n","<br>",$fieldVar);
				print "<$fieldName>".htmlspecialchars($fieldVarDone)."</$fieldName>"; 
			}
	 		print"</$item>";

		}
	print "</node>";
}

else if($action == "query")
{
	print "<node>";
	print "<action>$action</action>";
	$queryItem = strtolower($_REQUEST['queryItem']);
	$queryDetail = $_REQUEST['queryDetail'];
	$startDate = $_REQUEST['startDate'];
	$endDate = $_REQUEST['endDate'];
	$createdID = $_REQUEST['createdID'];

	if($queryItem == "reps")
	{
		switch($queryDetail)
		{
			case '1': //Rep: number of quotes
			{
				$result = mysql_query("Select COUNT('created_by') from `quotes` where `created_by` = '$createdID' and `date_created` between '$startDate' and '$endDate';");
				$num_rows = mysql_num_rows($result);
				if($num_rows > 0)
				{
					$row = mysql_result($result,0);
					print "<quotes qts = \"Quotes\">".$row."</quotes>";
					print "</node>";
				}
				return;	
			}
			
			case '2': //Rep: total quoted rand value
			{
				$result = mysql_query("SELECT ROUND(SUM(p.price),2) as ttl
										FROM `quotes`
										INNER JOIN `quote_items`
										ON `quotes`.`id` = `quote_items`.`quote_id` 
										INNER JOIN `new_products` p 
										ON p.id = new_product_id
										Where `quotes`.`created_by` = '$createdID' and `quotes`.`date_created` between '$startDate' and '$endDate';");
					
				$num_rows = mysql_num_rows($result);
				if($num_rows > 0)
				{					
					$row = mysql_result($result, 0, 'ttl');
					print "<quotes ttl=\"total\">R".$row."</quotes>";
					print "</node>";
				}
				return;	
			}
			
			case '3': //Rep: Avg quoted rand value
			{
				$result = mysql_query("										SELECT ROUND(AVG(x.quote_price),2) as Avg_value
										FROM
										(
											SELECT  quotes.id, SUM(p.price) as quote_price
											
											FROM    quotes 
										
													inner join quote_items
													ON quote_items.quote_id = quotes.id
											
													inner join new_products p
													ON p.id = new_product_id
													Where `quotes`.`created_by` = '$createdID' and `quotes`.`date_created` between '$startDate' and '$endDate'
											GROUP BY quotes.id
										) 
										x;");
															
				$num_rows = mysql_num_rows($result);
				if($num_rows > 0)
				{					
					$row = mysql_result($result, 0, 'Avg_value');
					print "<quotes ttl = \"average\">R".$row."</quotes>";
					print "</node>";
				}
				return;	
			}
			
			case '4': //Rep: avg % saved
			{
				$result = mysql_query("Select ROUND((sum(q.`old_kwh`) - sum(`p`.`new_kwh`))/sum(q.`old_kwh`)*100) as precentage_saved from quotes 
										inner join quote_items
										on quotes.id = `quote_items`.quote_id
										INNER JOIN `new_products` p 
										ON p.id = new_product_id
										inner join old_products q
										ON q.id = old_product_id
										where quotes.`created_by` = '$createdID' and `quotes`.`date_created` between '$startDate' and '$endDate';");
										
				$num_rows = mysql_num_rows($result);
				if($num_rows > 0)
				{					
					$row = mysql_result($result, 0, 'precentage_saved');
					print "<quotes perc=\"percentage\">".$row."%</quotes>";
					print "</node>";
				}
				
				return;	
			}
			
			case'5': //Rep: most quoted exsiting and replacement product
			{
				//replacement
				$result = mysql_query("SELECT p.product as newprod FROM `quotes` 
										INNER JOIN `quote_items`
										ON `quotes`.`id` = `quote_items`.`quote_id` 
										INNER JOIN `new_products` p 
										ON p.id = new_product_id
										Where `quotes`.`created_by` = '$createdID' and `quotes`.`date_created` between '$startDate' and '$endDate' GROUP BY(p.product) 
										HAVING count(p.product) = 
										(
											SELECT count(p.product) FROM `quotes` 
											INNER JOIN `quote_items`
											ON `quotes`.`id` = `quote_items`.`quote_id` 
											INNER JOIN `new_products` p 
											ON p.id = new_product_id
											Where `quotes`.`created_by` = '$createdID' and `quotes`.`date_created` between '$startDate' and '$endDate' GROUP 				 											BY(p.product) ORDER BY count(p.product) DESC limit 1
										);");
										
				$num_rows = mysql_num_rows($result);
				if($num_rows > 0)
				{
					while($row_new = mysql_fetch_assoc($result))
					{   
						print"<quotes name=\"Most Quoted Replacement Product\">";               
						print $row_new['newprod']; 

						print"</quotes>";
					}	
					
				}
				
				else
				{
					print "<quotes>no product</quotes>";
				}
				//existing
				$result_old = mysql_query("SELECT p.product as oldprod FROM `quotes` 
										INNER JOIN `quote_items`
										ON `quotes`.`id` = `quote_items`.`quote_id` 
										INNER JOIN `old_products` p 
										ON p.id = old_product_id
										Where `quotes`.`created_by` = '$createdID' and `quotes`.`date_created` between '$startDate' and '$endDate' GROUP BY(p.product) 
										HAVING count(p.product) = 
										(
											SELECT count(p.product) FROM `quotes` 
											INNER JOIN `quote_items`
											ON `quotes`.`id` = `quote_items`.`quote_id` 
											INNER JOIN `old_products` p 
											ON p.id = old_product_id
											Where `quotes`.`created_by` = '$createdID' and `quotes`.`date_created` between '$startDate' and '$endDate' GROUP 				 											BY(p.product) ORDER BY count(p.product) DESC limit 1
										);");
										
				$num_rows_old = mysql_num_rows($result_old);
				if($num_rows_old > 0)
				{
					while($row_old = mysql_fetch_assoc($result_old))
					{   
						print"<quotes name=\"Most Quoted Existing Product\">";               
						
							print $row_old['oldprod']; 
						print"</quotes>";
					}	
					
				}
				
				else
				{
					print "<quotes>no product</quotes>";
				}
				print "</node>";
				return;
			}
		}	
	}
	else if($queryItem == "stores")
	{
		switch($queryDetail)
		{
			case '1': //Store: total number of quotes
			{
				$result = mysql_query("SELECT COUNT(`quotes`.`created_by`) as ttl_quotes FROM `quotes` 
										INNER JOIN `system_users`
										ON `quotes`.`created_by` = `system_users`.`id`
										WHERE `system_users`.`store_id` = '$createdID' and `date_created` between '$startDate' and '$endDate';");
				$num_rows = mysql_num_rows($result);
				if($num_rows > 0)
				{
					$row = mysql_result($result,0,'ttl_quotes');
					print "<quotes qts = \"Quotes\">".$row."</quotes>";
					print "</node>";
				}
				
				return;	
			}
			
			case '2': //Store: Total Quoted Rand Value
			{
				$result = mysql_query("SELECT ROUND(SUM(p.price),2) as ttl
										FROM quotes 
										inner join `system_users`
										ON quotes.created_by = system_users.id
										inner join quote_items s
										ON s.quote_id = quotes.id
										inner join new_products p
										ON p.id = new_product_id
										where system_users.store_id = '$createdID' and `quotes`.`date_created` between '$startDate' and '$endDate';");
										
				$num_rows = mysql_num_rows($result);
				if($num_rows > 0)
				{
					$row = mysql_result($result,0,'ttl');
					print "<quotes ttl=\"Total\">R".$row."</quotes>";
					print "</node>";
				}
				return;	
			}
			
			case '3': //Store: Avg quoted rand value
			{
				$result = mysql_query("SELECT ROUND(AVG(x.quote_price),2) as Avg_value
										FROM
										(
											SELECT  quotes.id, SUM(p.price) quote_price
											
											FROM    quotes 
											
													inner join `system_users`
													ON quotes.created_by = system_users.id
											
													inner join quote_items s
													ON s.quote_id = quotes.id
											
													inner join new_products p
													ON p.id = new_product_id
											WHERE system_users.`store_id` = '$createdID' and `quotes`.`date_created` between '$startDate' and '$endDate'
											GROUP BY quotes.id
										) 
										x;");
															
				$num_rows = mysql_num_rows($result);
				if($num_rows > 0)
				{					
					$row = mysql_result($result, 0, 'Avg_value');
					print "<quotes avg=\"Average\">R".$row."</quotes>";
					print "</node>";
				}
				return;	
			}
			
			case '4': //Store:  Avg % saved
			{
				$result = mysql_query("Select ROUND((sum(q.`old_kwh`) - sum(`p`.`new_kwh`))/sum(q.`old_kwh`)*100) as precentage_saved 
										from quotes 
										inner join `system_users`
										ON quotes.created_by = system_users.id
										inner join quote_items s
										on quotes.id = s.quote_id
										INNER JOIN `new_products` p 
										ON p.id = new_product_id
										inner join old_products q
										ON q.id = old_product_id
										WHERE system_users.`store_id` = '$createdID' and `quotes`.`date_created` between '$startDate' and '$endDate'																								 										;");
				$num_rows = mysql_num_rows($result);
				if($num_rows > 0)
				{					
					$row = mysql_result($result, 0, 'precentage_saved');
					print "<quotes perc = \"Percentage\">".$row."%</quotes>";
					print "</node>";
				}
				return;	
			}
			
			case '5': //Store: Most quoted existing and replacement product
			{
				$result_new = mysql_query("SELECT p.product as newprod, count(p.product) as newprodamount
										FROM quotes 
										inner join `system_users`
										ON quotes.created_by = system_users.id
										inner join quote_items s
										ON s.quote_id = quotes.id
										inner join new_products p
										ON p.id = new_product_id
										
										where system_users.store_id = '$createdID' and `quotes`.`date_created` between '$startDate' and '$endDate' group by (p.product)
										Having count(p.product) = 
										(
										SELECT count(p.product)  as newprodamount
										FROM quotes 
										inner join `system_users`
										ON quotes.created_by = system_users.id
										inner join quote_items s
										ON s.quote_id = quotes.id
										inner join new_products p
										ON p.id = new_product_id
										where system_users.store_id = '$createdID' and `quotes`.`date_created` between '$startDate' and '$endDate'
										GROUP BY p.`product` 
		 								ORDER BY newprodamount  DESC
  										LIMIT 1  
										);");	
										
				$num_rows_new = mysql_num_rows($result_new);
				if($num_rows_new > 1)
				{
					while($row_new = mysql_fetch_assoc($result_new))
					{   
						print"<quotes name=\"Most Quoted Replacement Product\">";               
						foreach ($row_new as $col => $val) 
						{
							print "<$col>".$val."</$col>"; 
						}
						print"</quotes>";
					}	
				}
				else if ($num_rows_new == 1)
				{
					$row_new = mysql_result($result_new, 0, 'newprod');
					print "<quotes name=\"Most Quoted Replacement Product\">".$row_new."</quotes>";
				}
				
				else
				{
					print "<quotes name=\"Most Quoted Replacement Product\">no product</quotes>";
				}
				
				$result_old = mysql_query("SELECT p.product as oldprod, count(p.product) as oldprodamount
										FROM quotes 
										inner join `system_users`
										ON quotes.created_by = system_users.id
										inner join quote_items s
										ON s.quote_id = quotes.id
										inner join old_products p
										ON p.id = old_product_id
										
										where system_users.store_id = '$createdID' and `quotes`.`date_created` between '$startDate' and '$endDate' group by (p.product)
										Having count(p.product) = 
										(
										SELECT count(p.product)  as oldprodamount
										FROM quotes 
										inner join `system_users`
										ON quotes.created_by = system_users.id
										inner join quote_items s
										ON s.quote_id = quotes.id
										inner join old_products p
										ON p.id = old_product_id
										where system_users.store_id = '$createdID' and `quotes`.`date_created` between '$startDate' and '$endDate'
										GROUP BY p.`product` 
		 								ORDER BY oldprodamount  DESC
  										LIMIT 1  
										);");	
										
				$num_rows_old = mysql_num_rows($result_old);
				if($num_rows_old > 1)
				{
					while($row_old = mysql_fetch_assoc($result_old))
					{   
						print"<quotes name=\"Most Quoted Exisiting Product\">";               
						foreach ($row_old as $col => $val) 
						{
							print "<$col>".$val."</$col>"; 
						}
						print"</quotes>";
					}	
				}
				else if ($num_rows_old == 1)
				{
					$row_old = mysql_result($result_old, 0, 'oldprod');
					print "<quotes name=\"Most Quoted Exisiting Product\">".$row_old."</quotes>";
				}
				
				else
				{
					print "<quotes name=\"Most Quoted Exisiting Product\">No Product Found</quotes>";
				}
				print "</node>";
				
				return;	
			}
			
			case '6': //Store: Rep with most and least quotes
			{
				//most
				$result_most = mysql_query("SELECT `system_users`.`first`, `system_users`.`last`,  COUNT(`quotes`.`created_by`) as quote_num
										FROM `quotes` 
										INNER JOIN `system_users`
										ON `quotes`.`created_by` = `system_users`.`id`
										where `system_users`.`store_id` = '$createdID' 
										and `quotes`.`date_created` between '$startDate' and '$endDate' group by(`created_by`)
										HAVING count(`quotes`.`created_by`) = 
										(
										SELECT COUNT(`quotes`.`created_by`)
										FROM `quotes` 
										INNER JOIN `system_users`
										ON `quotes`.`created_by` = `system_users`.`id`
										where `system_users`.`store_id` = '$createdID' 
										and `quotes`.`date_created` between '$startDate' and '$endDate' group by(`created_by`) ORDER BY count(`created_by`) DESC limit 1
										)");
											
				$num_rows_most = mysql_num_rows($result_most);
				if($num_rows_most > 0)
				{
					while($row_most = mysql_fetch_assoc($result_most))
					{   
						print"<quotes name=\"Most Quotes\" fname = \"First Name\" lname = \"Last Name\" quotes = \"Quotes\">";               
						foreach ($row_most as $col => $val) 
						{
							print "<$col>".$val."</$col>"; 
						}
						print"</quotes>";
					}	
				}
				
				else
				{
					print "<quotes name=\"Most Quotes\" >No Rep Found</quotes>";
				}
				
				//least
				$result_least = mysql_query("SELECT `system_users`.`first`, `system_users`.`last`,  COUNT(`quotes`.`created_by`) as quote_num
										FROM `quotes` 
										INNER JOIN `system_users`
										ON `quotes`.`created_by` = `system_users`.`id`
										where `system_users`.`store_id` = '$createdID' 
										and `quotes`.`date_created` between '$startDate' and '$endDate' group by(`created_by`)
										HAVING count(`quotes`.`created_by`) = 
										(
										SELECT COUNT(`quotes`.`created_by`)
										FROM `quotes` 
										INNER JOIN `system_users`
										ON `quotes`.`created_by` = `system_users`.`id`
										where `system_users`.`store_id` = '$createdID' 
										and `quotes`.`date_created` between '$startDate' and '$endDate' group by(`created_by`) ORDER BY count(`created_by`) ASC limit 1
										)");
											
				$num_rows_least = mysql_num_rows($result_least);
				if($num_rows_least > 0)
				{
					while($row_least = mysql_fetch_assoc($result_least))
					{    
						print"<quotes name=\"Least Quotes\" fname = \"First Name\" lname = \"Last Name\" quotes = \"Quotes\">";               
						foreach ($row_least as $col => $val) 
						{
							print "<$col>".$val."</$col>"; 
						}
						print"</quotes>";
					}	
				}
				
				else
				{
					print "<quotes name=\"Least Quotes\">no rep</quotes>";
				}
				
				print "</node>";
				return;
			}
			
			case '7': //Store: rep with highest and lowest rand value
			{
				
				//max
				$result_highest = mysql_query("SELECT system_users.first, system_users.last, ROUND(SUM(p.price),2) as ttl
										FROM quotes 
										inner join `system_users`
										ON quotes.created_by = system_users.id
										inner join quote_items s
										ON s.quote_id = quotes.id
										inner join new_products p
										ON p.id = new_product_id
										where system_users.store_id = '$createdID' and `quotes`.`date_created` between '$startDate' and '$endDate' group by (system_users.id)
										HAVING SUM(p.price) =
										(
										SELECT SUM(p.price)
										FROM quotes 
										inner join `system_users`
										ON quotes.created_by = system_users.id
										inner join quote_items s
										ON s.quote_id = quotes.id
										inner join new_products p
										ON p.id = new_product_id
										where system_users.store_id = '$createdID' and `quotes`.`date_created` between '$startDate' and '$endDate' group by (system_users.id) ORDER BY SUM(p.price) DESC limit 1
										)
;");
				$num_rows_highest = mysql_num_rows($result_highest);
				if($num_rows_highest > 0)
				{
					while($row_highest = mysql_fetch_assoc($result_highest))
					{   
						print"<quotes name=\"Highest Rand Value\" fname = \"First Name\" lname = \"Last Name\" val=\"Value (R)\">";               
						foreach ($row_highest as $col => $val) 
						{
							print "<$col>".$val."</$col>"; 
						}
						print"</quotes>";
					}	
						
				
				}
			
				else
				{
					print "<quotes name=\"Highest Rand Value\">no rep</quotes>";
				}
				
				//min
				$result_lowest = mysql_query("SELECT system_users.first, system_users.last, ROUND(SUM(p.price),2) as ttl
										FROM quotes 
										inner join `system_users`
										ON quotes.created_by = system_users.id
										inner join quote_items s
										ON s.quote_id = quotes.id
										inner join new_products p
										ON p.id = new_product_id
										where system_users.store_id = '$createdID' and `quotes`.`date_created` between '$startDate' and '$endDate' group by (system_users.id)
										HAVING SUM(p.price) =
										(
										SELECT SUM(p.price)
										FROM quotes 
										inner join `system_users`
										ON quotes.created_by = system_users.id
										inner join quote_items s
										ON s.quote_id = quotes.id
										inner join new_products p
										ON p.id = new_product_id
										where system_users.store_id = '$createdID' and `quotes`.`date_created` between '$startDate' and '$endDate' group by (system_users.id) ORDER BY SUM(p.price) ASC limit 1
										)
;");
				$num_rows_lowest = mysql_num_rows($result_lowest);
				if($num_rows_lowest > 0)
				{
					while($row_lowest = mysql_fetch_assoc($result_lowest))
					{   
						print"<quotes name=\"Lowest Rand Value\" fname = \"First Name\" lname = \"Last Name\" val=\"Value (R)\">";               
						foreach ($row_lowest as $col => $val) 
						{
							print "<$col>".$val."</$col>"; 
						}
						print"</quotes>";
					}	
						
				
				}
				else
				{
					print "<quotes name=\"Lowest Rand Value\">no rep</quotes>";
				}
				print "</node>";
				return;
					
			}
			
			case '8': //Store: Rep with highest and lowest avg % saved
			{
				//max
					$result_highest = mysql_query("Select system_users.first, system_users.last, ROUND((sum(q.`old_kwh`) - sum(`p`.`new_kwh`))/sum(q.`old_kwh`)*100) as    												avg_saved 
													from quotes
													inner join system_users
													on quotes.created_by = system_users.id
													inner join quote_items
													on quotes.id = `quote_items`.quote_id
													INNER JOIN `new_products` p 
													ON p.id = new_product_id
													inner join old_products q
													ON q.id = old_product_id
													where system_users.store_id = '$createdID' and `quotes`.`date_created` between '$startDate' and '$endDate' 
													group by system_users.id
													
													having ROUND((sum(q.`old_kwh`) - sum(`p`.`new_kwh`))/sum(q.`old_kwh`)*100) =
													(
														Select ROUND((sum(q.`old_kwh`) - sum(`p`.`new_kwh`))/sum(q.`old_kwh`)*100) as avg_saved 
														from quotes
														inner join system_users
														on quotes.created_by = system_users.id
														inner join quote_items
														on quotes.id = `quote_items`.quote_id
														INNER JOIN `new_products` p 
														ON p.id = new_product_id
														inner join old_products q
														ON q.id = old_product_id
														where system_users.store_id = '$createdID' and `quotes`.`date_created` between '$startDate' and '$endDate' 
														group by system_users.id		 								
														ORDER BY avg_saved  DESC
														LIMIT 1 
														);");
													
				$num_rows_highest = mysql_num_rows($result_highest);
				if($num_rows_highest > 0)
				{
					while($row_highest = mysql_fetch_assoc($result_highest))
					{   
						print"<quotes name=\"Highest average saved\" fname = \"First Name\" lname = \"Last Name\" avg=\"Average %\">";               
						foreach ($row_highest as $col => $val) 
						{
							print "<$col>".$val."</$col>"; 
						}
						print"</quotes>";
					}	
						
				
				}
				
				else
				{
					print "<quotes name=\"Highest average saved\" fname = \"First Name\" lname = \"Last Name\" avg=\"Average %\">no rep</quotes>";
				}
				
				//min
					$result_lowest = mysql_query("Select system_users.first, system_users.last, ROUND((sum(q.`old_kwh`) - sum(`p`.`new_kwh`))/sum(q.`old_kwh`)*100) as    												avg_saved 
													from quotes
													inner join system_users
													on quotes.created_by = system_users.id
													inner join quote_items
													on quotes.id = `quote_items`.quote_id
													INNER JOIN `new_products` p 
													ON p.id = new_product_id
													inner join old_products q
													ON q.id = old_product_id
													where system_users.store_id = '$createdID' and `quotes`.`date_created` between '$startDate' and '$endDate' 
													group by system_users.id
													
													having ROUND((sum(q.`old_kwh`) - sum(`p`.`new_kwh`))/sum(q.`old_kwh`)*100) =
													(
														Select ROUND((sum(q.`old_kwh`) - sum(`p`.`new_kwh`))/sum(q.`old_kwh`)*100) as avg_saved 
														from quotes
														inner join system_users
														on quotes.created_by = system_users.id
														inner join quote_items
														on quotes.id = `quote_items`.quote_id
														INNER JOIN `new_products` p 
														ON p.id = new_product_id
														inner join old_products q
														ON q.id = old_product_id
														where system_users.store_id = '$createdID' and `quotes`.`date_created` between '$startDate' and '$endDate' 
														group by system_users.id		 								
														ORDER BY avg_saved  ASC
														LIMIT 1  
													) ;");
													
				$num_rows_lowest = mysql_num_rows($result_lowest);
				if($num_rows_lowest > 0)
				{
					while($row_lowest = mysql_fetch_assoc($result_lowest))
					{   
						print"<quotes name=\"Lowest Average Saved\" fname = \"First Name\" lname = \"Last Name\" avg=\"Average %\">";               
						foreach ($row_lowest as $col => $val) 
						{
							print "<$col>".$val."</$col>"; 
						}
						print"</quotes>";
					}	
						
				
				}
			
				else
				{
					print "<quotes>no rep</quotes>";
				}
				print "</node>";
				return;
					
			}
			
			default:
				return;
		}
	}
	else if($queryItem == "branches")
	{
		switch ($queryDetail)
		{
			case '1': //branch: total number of quotes
			{
				$result = mysql_query("SELECT COUNT(p.`created_by`) as ttl_quotes 
										FROM branches
										inner join system_users
										ON branches.id = system_users.`branch_id`
										inner join quotes p
										ON p.`created_by` = `system_users`.`id`
										WHERE `branches`.`id` = '$createdID' and `p`.`date_created` between '$startDate' and '$endDate';"
										);
				
				$num_rows = mysql_num_rows($result);
				if($num_rows > 0)
				{					
					$row = mysql_result($result, 0, 'ttl_quotes');
					print "<quotes qts=\"Quotes\">".$row."</quotes>";
				}
				print "</node>";
				return;	
			}
			
			case '2': //branch: Total Quoted Rand Value
			{
				$result = mysql_query("SELECT  ROUND(SUM(p.price),2) as ttl
										FROM quotes 
										inner join `system_users`
										ON quotes.created_by = system_users.id
										inner join quote_items s
										ON s.quote_id = quotes.id
										inner join new_products p
										ON p.id = new_product_id
										where system_users.branch_id = '$createdID' and `quotes`.`date_created` between '$startDate' and '$endDate'"
										);
				
				$num_rows = mysql_num_rows($result);
				if($num_rows > 0)
				{					
					$row = mysql_result($result, 0, 'ttl');
					print "<quotes val=\"Value (R)\">".$row."</quotes>";
				}
				print "</node>";
				return;
			}
			
			case '3': //branch: Avg Quoted Rand Value
			{
				$result = mysql_query("SELECT ROUND(AVG(x.quote_price),2) as Avg_value
										FROM
										(
											SELECT  quotes.id, SUM(p.price) quote_price
											
											FROM    quotes 
											
													inner join `system_users`
													ON quotes.created_by = system_users.id
											
													inner join quote_items s
													ON s.quote_id = quotes.id
											
													inner join new_products p
													ON p.id = new_product_id
											WHERE system_users.`store_id` = '$createdID' and `quotes`.`date_created` between '$startDate' and '$endDate'
											GROUP BY quotes.id
										) 
										x");
										
				$num_rows = mysql_num_rows($result);
				if($num_rows > 0)
				{					
					$row = mysql_result($result, 0, 'Avg_value');
					print "<quotes avg=\"Average (R)\">".$row."</quotes>";
				}
				print "</node>";
				return;	
			}
			
			case '4': //branch: Avg % saved
			{
				$result = mysql_query("Select ROUND((sum(q.`old_kwh`) - sum(`p`.`new_kwh`))/sum(q.`old_kwh`)*100) as precentage_saved 
										from quotes 
										inner join `system_users`
										ON quotes.created_by = system_users.id
										inner join quote_items s
										on quotes.id = s.quote_id
										INNER JOIN `new_products` p 
										ON p.id = new_product_id
										inner join old_products q
										ON q.id = old_product_id
										WHERE system_users.`branch_id` = '$createdID' and `quotes`.`date_created` between '$startDate' and '$endDate'																								 										;");
				$num_rows = mysql_num_rows($result);
				if($num_rows > 0)
				{					
					$row = mysql_result($result, 0, 'precentage_saved');
					print "<quotes perc=\"Percentage\">".$row."%</quotes>";
				}
				
				print "</node>";
				return;	
			}
			
			case '5': //branch: Most quoted existing and replacement product
			{
				$result_new = mysql_query("SELECT p.product as newprod, count(p.product) as newprodamount
										FROM quotes 
										inner join `system_users`
										ON quotes.created_by = system_users.id
										inner join quote_items s
										ON s.quote_id = quotes.id
										inner join new_products p
										ON p.id = new_product_id
										where system_users.branch_id = '$createdID' and `quotes`.`date_created` between '$startDate' and '$endDate' group by (p.product)
										Having count(p.product) = 
										(
										SELECT count(p.product)  as newprodamount
										FROM quotes 
										inner join `system_users`
										ON quotes.created_by = system_users.id
										inner join quote_items s
										ON s.quote_id = quotes.id
										inner join new_products p
										ON p.id = new_product_id
										where system_users.branch_id = '$createdID' and `quotes`.`date_created` between '$startDate' and '$endDate'
										GROUP BY p.`product` 
		 								ORDER BY newprodamount  DESC
  										LIMIT 1  
										);");	
										
				$num_rows_new = mysql_num_rows($result_new);
				if($num_rows_new > 1)
				{
					while($row_new = mysql_fetch_assoc($result_new))
					{   
						print"<quotes name=\"Most Quoted Replacement Product\">";               
						foreach ($row_new as $col => $val) 
						{
							print "<$col>".$val."</$col>"; 
						}
						print"</quotes>";
					}	
				}
				else if ($num_rows_new == 1)
				{
					$row_new = mysql_result($result_new, 0, 'newprod');
					print "<quotes name=\"Most Quoted Replacement Product\">".$row_new."</quotes>";
				}
				
				else
				{
					print "<quotes name=\"Most Quoted Replacement Product\">no product</quotes>";
				}
				
				$result_old = mysql_query("SELECT p.product as oldprod, count(p.product) as oldprodamount
										FROM quotes 
										inner join `system_users`
										ON quotes.created_by = system_users.id
										inner join quote_items s
										ON s.quote_id = quotes.id
										inner join old_products p
										ON p.id = old_product_id
										
										where system_users.branch_id = '$createdID' and `quotes`.`date_created` between '$startDate' and '$endDate' group by (p.product)
										Having count(p.product) = 
										(
										SELECT count(p.product)  as oldprodamount
										FROM quotes 
										inner join `system_users`
										ON quotes.created_by = system_users.id
										inner join quote_items s
										ON s.quote_id = quotes.id
										inner join old_products p
										ON p.id = old_product_id
										where system_users.branch_id = '$createdID' and `quotes`.`date_created` between '$startDate' and '$endDate'
										GROUP BY p.`product` 
		 								ORDER BY oldprodamount  DESC
  										LIMIT 1  
										);");	
										
				$num_rows_old = mysql_num_rows($result_old);
				if($num_rows_old > 1)
				{
					while($row_old = mysql_fetch_assoc($result_old))
					{   
						print"<quotes name=\"Most Quoted Existing Product\">";               
						foreach ($row_old as $col => $val) 
						{
							print "<$col>".$val."</$col>"; 
						}
						print"</quotes>";
					}	
				}
				else if ($num_rows_old == 1)
				{
					$row_old = mysql_result($result_old, 0, 'oldprod');
					print "<quotes name=\"Most Quoted Existing Product\">".$row_old."</quotes>";
				}
				
				else
				{
					print "<quotes name=\"Most Quoted Existing Product\">no product</quotes>";
				}
				
				print "</node>";
				return;
			}
			
			case '6': //branch: store with most and least quotes
			{
				//most
				$result_most = mysql_query("SELECT `stores`.store,  COUNT(`q`.`created_by`) as quote_num	
										FROM stores
										INNER JOIN system_users p
										ON stores.id = p.store_id
										INNER JOIN `quotes` q
										ON q.`created_by` = p.`id`
										where p.`branch_id` = '$createdID' and `q`.`date_created` between '$startDate' and '$endDate'
										group by(stores.id)
										HAVING COUNT(`q`.`created_by`) = 
										(
											SELECT COUNT(`q`.`created_by`) as quote_num	
											FROM stores
											INNER JOIN system_users p
											ON stores.id = p.store_id
											INNER JOIN `quotes` q
											ON q.`created_by` = p.`id`
											where p.`branch_id` = '$createdID' and `q`.`date_created` between '$startDate' and '$endDate'
											group by(stores.id)
											order by quote_num DESC limit 1
										);");
											
				$num_rows_most = mysql_num_rows($result_most);
				if($num_rows_most > 0)
				{
					while($row_most = mysql_fetch_assoc($result_most))
					{   
						print"<quotes name=\"Most Quotes\" fname = \"Store Name\" quotes = \"Quotes\">";               
						foreach ($row_most as $col => $val) 
						{
							print "<$col>".$val."</$col>"; 
						}
						print"</quotes>";
					}	
				}
				
				else
				{
					print "<quotes name=\"Most Quotes\" fname = \"Store Name\" quotes = \"Quotes\">no store</quotes>";
				}
				
				//least
				$result_least = mysql_query("SELECT `stores`.store,  COUNT(`q`.`created_by`) as quote_num	
										FROM stores
										INNER JOIN system_users p
										ON stores.id = p.store_id
										INNER JOIN `quotes` q
										ON q.`created_by` = p.`id`
										where p.`branch_id` = '$createdID' and `q`.`date_created` between '$startDate' and '$endDate'
										group by(stores.id)
										HAVING COUNT(`q`.`created_by`) = 
										(
											SELECT COUNT(`q`.`created_by`) as quote_num	
											FROM stores
											INNER JOIN system_users p
											ON stores.id = p.store_id
											INNER JOIN `quotes` q
											ON q.`created_by` = p.`id`
											where p.`branch_id` = '$createdID' and `q`.`date_created` between '$startDate' and '$endDate'
											group by(stores.id)
											order by quote_num ASC limit 1
											
										);");
											
				$num_rows_least = mysql_num_rows($result_least);
				if($num_rows_least > 0)
				{
					while($row_least = mysql_fetch_assoc($result_least))
					{   
						print"<quotes name=\"Least Quotes\" fname = \"Store Name\" quotes = \"Quotes\">";               
						foreach ($row_least as $col => $val) 
						{
							print "<$col>".$val."</$col>"; 
						}
						print"</quotes>";
					}	
				}
				
				else
				{
					print "<quotes name=\"Least Quotes\" fname = \"Store Name\" quotes = \"Quotes\">no store</quotes>";
				}
				print "</node>";
				return;
			}
			
			case '7': //branch: Store with highest and lowest Rand Value
			{
					//max
				$result_highest = mysql_query("SELECT stores.store, ROUND(SUM(p.price),2) as ttl
										FROM stores
										INNER JOIN system_users 
										ON stores.id = system_users.store_id										
										INNER JOIN quotes q
										ON q.created_by = system_users.id
										inner join quote_items s
										ON s.quote_id = q.id
										inner join new_products p
										ON p.id = new_product_id
										where system_users.`branch_id` = '$createdID' and `q`.`date_created` between '$startDate' and '$endDate'
										group by(`stores`.`id`)
										Having 	ROUND(SUM(p.price),2) = 
										(
											SELECT ROUND(SUM(p.price),2) as ttl
											FROM stores
											INNER JOIN system_users 
											ON stores.id = system_users.store_id										
											INNER JOIN quotes q
											ON q.created_by = system_users.id
											inner join quote_items s
											ON s.quote_id = q.id
											inner join new_products p
											ON p.id = new_product_id
											where system_users.`branch_id` = '$createdID' and `q`.`date_created` between '$startDate' and '$endDate'
											group by(`stores`.`id`)
											ORDER BY ttl DESC limit 1
										);");
				$num_rows_highest = mysql_num_rows($result_highest);
				if($num_rows_highest > 0)
				{
					while($row_highest = mysql_fetch_assoc($result_highest))
					{   
						print"<quotes name=\"Highest Value\" fname = \"Store Name\" val = \"Value (R)\">";               
						foreach ($row_highest as $col => $val) 
						{
							print "<$col>".$val."</$col>"; 
						}
						print"</quotes>";
					}	
						
				
				}
			
				else
				{
					print "<quotes name=\"Highest Value\">no store</quotes>";
				}
				
				//min
				$result_lowest = mysql_query("SELECT stores.store, ROUND(SUM(p.price),2) as ttl
										FROM stores
										INNER JOIN system_users 
										ON stores.id = system_users.store_id										
										INNER JOIN quotes q
										ON q.created_by = system_users.id
										inner join quote_items s
										ON s.quote_id = q.id
										inner join new_products p
										ON p.id = new_product_id
										where system_users.`branch_id` = '$createdID' and `q`.`date_created` between '$startDate' and '$endDate'
										group by(`stores`.`id`)
										Having 	ROUND(SUM(p.price),2) = 
										(
											SELECT ROUND(SUM(p.price),2) as ttl
											FROM stores
											INNER JOIN system_users 
											ON stores.id = system_users.store_id										
											INNER JOIN quotes q
											ON q.created_by = system_users.id
											inner join quote_items s
											ON s.quote_id = q.id
											inner join new_products p
											ON p.id = new_product_id
											where system_users.`branch_id` = '$createdID' and `q`.`date_created` between '$startDate' and '$endDate'
											group by(`stores`.`id`)
											ORDER BY ttl ASC limit 1
										);");
				$num_rows_lowest = mysql_num_rows($result_lowest);
				if($num_rows_lowest > 0)
				{
					while($row_lowest = mysql_fetch_assoc($result_lowest))
					{   
						print"<quotes name=\"Lowest Value\" fname = \"Store Name\" val = \"Value (R)\">";               
						foreach ($row_lowest as $col => $val) 
						{
							print "<$col>".$val."</$col>"; 
						}
						print"</quotes>";
					}	
						
				
				}
				else
				{
					print "<quotes name=\"Lowest Value\" >no store</quotes>";
				}
				
				print "</node>";
				return;
			}
			
			case '8': //Branch: Store with highest and lowest avg % saved
			{
				//max
					$result_highest = mysql_query("SELECT `stores`.store, ROUND((sum(op.`old_kwh`) - sum(`np`.`new_kwh`))/sum(op.`old_kwh`)*100) as avg_saved 
													FROM stores
													INNER JOIN system_users 
													ON stores.id = system_users.store_id										
													INNER JOIN quotes q
													ON q.created_by = system_users.id
													inner join quote_items s
													ON s.quote_id = q.id
													inner join new_products np
													ON np.id = s.new_product_id
													inner join old_products op
													ON op.id = s.old_product_id
													where system_users.branch_id = '1'
													group by(`stores`.`id`)
													
													
													Having 	ROUND((sum(op.`old_kwh`) - sum(`np`.`new_kwh`))/sum(op.`old_kwh`)*100)  = 
													(
														SELECT ROUND((sum(op.`old_kwh`) - sum(`np`.`new_kwh`))/sum(op.`old_kwh`)*100) as avg_saved
														FROM stores
														INNER JOIN system_users 
														ON stores.id = system_users.store_id										
														INNER JOIN quotes q
														ON q.created_by = system_users.id
														inner join quote_items s
														ON s.quote_id = q.id
														inner join new_products np
														ON np.id = s.new_product_id
														inner join old_products op
														ON op.id = s.old_product_id
														where system_users.branch_id = '1'
														group by(`stores`.`id`)
														ORDER BY avg_saved DESC limit 1
														);");
													
				$num_rows_highest = mysql_num_rows($result_highest);
				if($num_rows_highest > 0)
				{
					while($row_highest = mysql_fetch_assoc($result_highest))
					{   
						print"<quotes name=\"Highest Average Saved\" store=\"Store Name\" avg= \"Average Saved (%)\">";               
						foreach ($row_highest as $col => $val) 
						{
							print "<$col>".$val."</$col>"; 
						}
						print"</quotes>";
					}	
						
				
				}
				
				else
				{
					print "<quotes name=\"Highest Average Saved\">no store</quotes>";
				}
				
				//min
					$result_lowest = mysql_query("SELECT `stores`.store, ROUND((sum(op.`old_kwh`) - sum(`np`.`new_kwh`))/sum(op.`old_kwh`)*100) as avg_saved 
													FROM stores
													INNER JOIN system_users 
													ON stores.id = system_users.store_id										
													INNER JOIN quotes q
													ON q.created_by = system_users.id
													inner join quote_items s
													ON s.quote_id = q.id
													inner join new_products np
													ON np.id = s.new_product_id
													inner join old_products op
													ON op.id = s.old_product_id
													where system_users.branch_id = '1'
													group by(`stores`.`id`)
													
													
													Having 	ROUND((sum(op.`old_kwh`) - sum(`np`.`new_kwh`))/sum(op.`old_kwh`)*100)  = 
													(
														SELECT ROUND((sum(op.`old_kwh`) - sum(`np`.`new_kwh`))/sum(op.`old_kwh`)*100) as avg_saved
														FROM stores
														INNER JOIN system_users 
														ON stores.id = system_users.store_id										
														INNER JOIN quotes q
														ON q.created_by = system_users.id
														inner join quote_items s
														ON s.quote_id = q.id
														inner join new_products np
														ON np.id = s.new_product_id
														inner join old_products op
														ON op.id = s.old_product_id
														where system_users.branch_id = '1'
														group by(`stores`.`id`)
														ORDER BY avg_saved ASC limit 1
														);");
													
				$num_rows_lowest = mysql_num_rows($result_lowest);
				if($num_rows_lowest > 0)
				{
					while($row_lowest = mysql_fetch_assoc($result_lowest))
					{   
						print"<quotes name=\"Lowest Average Saved\" store=\"Store Name\" avg= \"Average Saved\">";               
						foreach ($row_lowest as $col => $val) 
						{
							print "<$col>".$val."</$col>"; 
						}
						print"</quotes name=\"Lowest Average Saved\" store=\"Store Name\" avg= \"Average Saved\">";
					}	
						
				
				}
			
				else
				{
					print "<quotes name=\"Lowest Average Saved\">no store</quotes>";
				}
				print "</node>";
				return;
					
			}
			
			case '9': //branch: Rep with most and least quotes
			{
				//most
				$result_most = mysql_query("SELECT `system_users`.`first`, `system_users`.`last`,  COUNT(`quotes`.`created_by`) as quote_num
										FROM `quotes` 
										INNER JOIN `system_users`
										ON `quotes`.`created_by` = `system_users`.`id`
										where `system_users`.`branch_id` = '$createdID' 
										and `quotes`.`date_created` between '$startDate' and '$endDate' group by(`created_by`)
										HAVING count(`quotes`.`created_by`) = 
										(
										SELECT COUNT(`quotes`.`created_by`)
										FROM `quotes` 
										INNER JOIN `system_users`
										ON `quotes`.`created_by` = `system_users`.`id`
										where `system_users`.`branch_id` = '$createdID' 
										and `quotes`.`date_created` between '$startDate' and '$endDate' group by(`created_by`) ORDER BY count(`created_by`) DESC limit 1
										);");
											
				$num_rows_most = mysql_num_rows($result_most);
				if($num_rows_most > 0)
				{
					while($row_most = mysql_fetch_assoc($result_most))
					{   
						print"<quotes name=\"Most Quotes\" fname = \"First Name\" lname = \"Last Name\" quotes = \"Quotes\">";               
						foreach ($row_most as $col => $val) 
						{
							print "<$col>".$val."</$col>"; 
						}
						print"</quotes>";
					}	
				}
				
				else
				{
					print "<quotes name=\"Most Quotes\">no rep</quotes>";
				}
				
				//least
				$result_least = mysql_query("SELECT `system_users`.`first`, `system_users`.`last`,  COUNT(`quotes`.`created_by`) as quote_num
										FROM `quotes` 
										INNER JOIN `system_users`
										ON `quotes`.`created_by` = `system_users`.`id`
										where `system_users`.`branch_id` = '$createdID' 
										and `quotes`.`date_created` between '$startDate' and '$endDate' group by(`created_by`)
										HAVING count(`quotes`.`created_by`) = 
										(
										SELECT COUNT(`quotes`.`created_by`)
										FROM `quotes` 
										INNER JOIN `system_users`
										ON `quotes`.`created_by` = `system_users`.`id`
										where `system_users`.`branch_id` = '$createdID' 
										and `quotes`.`date_created` between '$startDate' and '$endDate' group by(`created_by`) ORDER BY count(`created_by`) ASC limit 1
										);");
											
				$num_rows_least = mysql_num_rows($result_least);
				if($num_rows_least > 0)
				{
					while($row_least = mysql_fetch_assoc($result_least))
					{   
						print"<quotes name=\"Least Quotes\" fname = \"First Name\" lname = \"Last Name\" quotes = \"Quotes\">";               
						foreach ($row_least as $col => $val) 
						{
							print "<$col>".$val."</$col>"; 
						}
						print"</quotes>";
					}	
				}
				
				else
				{
					print "<quotes name=\"Least Quotes\">no rep</quotes>";
				}
				
				print "</node>";
				return;
			}
			
			case '10': //branch: rep with highest and lowest rand value
			{
				
				//max
				$result_highest = mysql_query("SELECT system_users.first, system_users.last, ROUND(SUM(p.price),2) as ttl
										FROM quotes 
										inner join `system_users`
										ON quotes.created_by = system_users.id
										inner join quote_items s
										ON s.quote_id = quotes.id
										inner join new_products p
										ON p.id = new_product_id
										where system_users.branch_id = '$createdID' and `quotes`.`date_created` between '$startDate' and '$endDate' group by (system_users.id)
										HAVING SUM(p.price) =
										(
										SELECT SUM(p.price)
										FROM quotes 
										inner join `system_users`
										ON quotes.created_by = system_users.id
										inner join quote_items s
										ON s.quote_id = quotes.id
										inner join new_products p
										ON p.id = new_product_id
										where system_users.branch_id = '$createdID' and `quotes`.`date_created` between '$startDate' and '$endDate' group by (system_users.id) ORDER BY SUM(p.price) DESC limit 1
										)
;");
				$num_rows_highest = mysql_num_rows($result_highest);
				if($num_rows_highest > 0)
				{
					while($row_highest = mysql_fetch_assoc($result_highest))
					{   
						print"<quotes name=\"Highest Value\" fname = \"First Name\" lname = \"Last Name\" val = \"Value (R)\">";               
						foreach ($row_highest as $col => $val) 
						{
							print "<$col>".$val."</$col>"; 
						}
						print"</quotes>";
					}	
						
				
				}
			
				else
				{
					print "<quotes name=\"Highest Value\">no rep</quotes>";
				}
				
				//min
				$result_lowest = mysql_query("SELECT system_users.first, system_users.last, ROUND(SUM(p.price),2) as ttl
										FROM quotes 
										inner join `system_users`
										ON quotes.created_by = system_users.id
										inner join quote_items s
										ON s.quote_id = quotes.id
										inner join new_products p
										ON p.id = new_product_id
										where system_users.branch_id = '$createdID' and `quotes`.`date_created` between '$startDate' and '$endDate' group by (system_users.id)
										HAVING SUM(p.price) =
										(
										SELECT SUM(p.price)
										FROM quotes 
										inner join `system_users`
										ON quotes.created_by = system_users.id
										inner join quote_items s
										ON s.quote_id = quotes.id
										inner join new_products p
										ON p.id = new_product_id
										where system_users.branch_id = '$createdID' and `quotes`.`date_created` between '$startDate' and '$endDate' group by (system_users.id) ORDER BY SUM(p.price) ASC limit 1
										)
;");
				$num_rows_lowest = mysql_num_rows($result_lowest);
				if($num_rows_lowest > 0)
				{
					while($row_lowest = mysql_fetch_assoc($result_lowest))
					{   
						print"<quotes name=\"Lowest Value\" fname = \"First Name\" lname = \"Last Name\" val = \"Value (R)\">";               
						foreach ($row_lowest as $col => $val) 
						{
							print "<$col>".$val."</$col>"; 
						}
						print"</quotes>";
					}	
						
				
				}
				else
				{
					print "<quotes name=\"Lowest Value\">no rep</quotes>";
				}
				
				print "</node>";
				return;
					
			}
			
			case '11': //branch: Rep with highest and lowest avg % saved
			{
				//max
					$result_highest = mysql_query("Select system_users.first, system_users.last, ROUND((sum(q.`old_kwh`) - sum(`p`.`new_kwh`))/sum(q.`old_kwh`)*100) as    												avg_saved 
													from quotes
													inner join system_users
													on quotes.created_by = system_users.id
													inner join quote_items
													on quotes.id = `quote_items`.quote_id
													INNER JOIN `new_products` p 
													ON p.id = new_product_id
													inner join old_products q
													ON q.id = old_product_id
													where system_users.store_id = '$createdID' and `quotes`.`date_created` between '$startDate' and '$endDate' 
													group by system_users.id
													
													having ROUND((sum(q.`old_kwh`) - sum(`p`.`new_kwh`))/sum(q.`old_kwh`)*100) =
													(
														Select ROUND((sum(q.`old_kwh`) - sum(`p`.`new_kwh`))/sum(q.`old_kwh`)*100) as avg_saved 
														from quotes
														inner join system_users
														on quotes.created_by = system_users.id
														inner join quote_items
														on quotes.id = `quote_items`.quote_id
														INNER JOIN `new_products` p 
														ON p.id = new_product_id
														inner join old_products q
														ON q.id = old_product_id
														where system_users.store_id = '$createdID' and `quotes`.`date_created` between '$startDate' and '$endDate' 
														group by system_users.id		 								
														ORDER BY avg_saved  DESC
														LIMIT 1 
														);");
													
				$num_rows_highest = mysql_num_rows($result_highest);
				if($num_rows_highest > 0)
				{
					while($row_highest = mysql_fetch_assoc($result_highest))
					{   
						print"<quotes name=\"Highest Average\" fname = \"First Name\" lname = \"Last Name\" val = \"Average (%)\">";               
						foreach ($row_highest as $col => $val) 
						{
							print "<$col>".$val."</$col>"; 
						}
						print"</quotes>";
					}	
						
				
				}
				
				else
				{
					print "<quotes name=\"Highest Average\">no rep</quotes>";
				}
				
				//min
					$result_lowest = mysql_query("Select system_users.first, system_users.last, ROUND((sum(q.`old_kwh`) - sum(`p`.`new_kwh`))/sum(q.`old_kwh`)*100) as    												avg_saved 
													from quotes
													inner join system_users
													on quotes.created_by = system_users.id
													inner join quote_items
													on quotes.id = `quote_items`.quote_id
													INNER JOIN `new_products` p 
													ON p.id = new_product_id
													inner join old_products q
													ON q.id = old_product_id
													where system_users.store_id = '$createdID' and `quotes`.`date_created` between '$startDate' and '$endDate' 
													group by system_users.id
													
													having ROUND((sum(q.`old_kwh`) - sum(`p`.`new_kwh`))/sum(q.`old_kwh`)*100) =
													(
														Select ROUND((sum(q.`old_kwh`) - sum(`p`.`new_kwh`))/sum(q.`old_kwh`)*100) as avg_saved 
														from quotes
														inner join system_users
														on quotes.created_by = system_users.id
														inner join quote_items
														on quotes.id = `quote_items`.quote_id
														INNER JOIN `new_products` p 
														ON p.id = new_product_id
														inner join old_products q
														ON q.id = old_product_id
														where system_users.store_id = '$createdID' and `quotes`.`date_created` between '$startDate' and '$endDate' 
														group by system_users.id		 								
														ORDER BY avg_saved  ASC
														LIMIT 1  
													) ;");
													
				$num_rows_lowest = mysql_num_rows($result_lowest);
				if($num_rows_lowest > 0)
				{
					while($row_lowest = mysql_fetch_assoc($result_lowest))
					{   
						print"<quotes name=\"Lowest Average\" fname = \"First Name\" lname = \"Last Name\" val = \"Average (%)\">";               
						foreach ($row_lowest as $col => $val) 
						{
							print "<$col>".$val."</$col>"; 
						}
						print"</quotes>";
					}	
						
				
				}
			
				else
				{
					print "<quotes name=\"Lowest Average\">no rep</quotes>";
				}
				
				print "</node>";
				return;
					
			}
					
		}
		
	}
	else if($queryItem == "national")
	{
		switch($queryDetail)
		{
			case '1': //national: Total Number of quotes
			{
				$result = mysql_query("Select COUNT(quotes.id) as ttl from quotes 
										inner join system_users 
										ON quotes.`created_by` = system_users.id and `quotes`.`date_created` between '$startDate' and '$endDate';");
										
				$num_rows = mysql_num_rows($result);
				if($num_rows > 0)
				{					
					$row = mysql_result($result, 0, 'ttl');
					print "<quotes val=\"Quotes\">".$row."</quotes>";
				}
				
				print "</node>";
				return;
			}	
			
			case '2': //national: Total Quoted Rand Value
			{
				$result = mysql_query("SELECT  ROUND(SUM(p.price),2) as ttl
								FROM quotes 
								inner join `system_users`
								ON quotes.created_by = system_users.id
								inner join quote_items s
								ON s.quote_id = quotes.id
								inner join new_products p
								ON p.id = new_product_id
								where `quotes`.`date_created` between '$startDate' and '$endDate';");
							
				$num_rows = mysql_num_rows($result);
				if($num_rows > 0)
				{					
					$row = mysql_result($result, 0, 'ttl');
					print "<quotes val=\"Total Value (R)\">".$row."</quotes>";
				}
				
				print "</node>";
				return;
			}
			
			case '3': //national: avg quoted rand value
			{
				$result = mysql_query("SELECT ROUND(AVG(x.quote_price),2) as Avg_value
										FROM
										(
											SELECT  quotes.id, SUM(p.price) quote_price
											
											FROM    quotes 
											
													inner join `system_users`
													ON quotes.created_by = system_users.id
											
													inner join quote_items s
													ON s.quote_id = quotes.id
											
													inner join new_products p
													ON p.id = new_product_id
											WHERE `quotes`.`date_created` between '$startDate' and '$endDate'
											GROUP BY quotes.id
										) 
										x");
										
				$num_rows = mysql_num_rows($result);
				if($num_rows > 0)
				{					
					$row = mysql_result($result, 0, 'Avg_value');
					print "<quotes val=\"Average Value (R)\">".$row."</quotes>";
				}
			
				print "</node>";
				return;	
			}
			
			case '4': //national: avg % saved
			{
				return;
			}
			
			case '5': //national: most quoted product
			{
				$result_new = mysql_query("SELECT p.product as newprod, count(p.product) as newprodamount
										FROM quotes 
										inner join `system_users`
										ON quotes.created_by = system_users.id
										inner join quote_items s
										ON s.quote_id = quotes.id
										inner join new_products p
										ON p.id = new_product_id
										where `quotes`.`date_created` between '$startDate' and '$endDate' group by (p.product)
										Having count(p.product) = 
										(
										SELECT count(p.product)  as newprodamount
										FROM quotes 
										inner join `system_users`
										ON quotes.created_by = system_users.id
										inner join quote_items s
										ON s.quote_id = quotes.id
										inner join new_products p
										ON p.id = new_product_id
										where `quotes`.`date_created` between '$startDate' and '$endDate'
										GROUP BY p.`product` 
		 								ORDER BY newprodamount  DESC
  										LIMIT 1  
										);");	
										
				$num_rows_new = mysql_num_rows($result_new);
				if($num_rows_new > 1)
				{
					while($row_new = mysql_fetch_assoc($result_new))
					{   
						print"<quotes name=\"Most Quoted Replacement Product\">";               
						foreach ($row_new as $col => $val) 
						{
							print "<$col>".$val."</$col>"; 
						}
						print"</quotes>";
					}	
				}
				else if ($num_rows_new == 1)
				{
					$row_new = mysql_result($result_new, 0, 'newprod');
					print "<quotes name=\"Most Quoted Replacement Product\">".$row_new."</quotes>";
				}
				
				else
				{
					print "<quotes name=\"Most Quoted Replacement Product\">no product</quotes>";
				}
				
				$result_old = mysql_query("SELECT p.product as oldprod, count(p.product) as oldprodamount
										FROM quotes 
										inner join `system_users`
										ON quotes.created_by = system_users.id
										inner join quote_items s
										ON s.quote_id = quotes.id
										inner join old_products p
										ON p.id = old_product_id
										
										where `quotes`.`date_created` between '$startDate' and '$endDate' group by (p.product)
										Having count(p.product) = 
										(
										SELECT count(p.product)  as oldprodamount
										FROM quotes 
										inner join `system_users`
										ON quotes.created_by = system_users.id
										inner join quote_items s
										ON s.quote_id = quotes.id
										inner join old_products p
										ON p.id = old_product_id
										where `quotes`.`date_created` between '$startDate' and '$endDate'
										GROUP BY p.`product` 
		 								ORDER BY oldprodamount  DESC
  										LIMIT 1  
										);");	
										
				$num_rows_old = mysql_num_rows($result_old);
				if($num_rows_old > 1)
				{
					while($row_old = mysql_fetch_assoc($result_old))
					{   
						print"<quotes name=\"Most Quoted Existing Product\">";               
						foreach ($row_old as $col => $val) 
						{
							print "<$col>".$val."</$col>"; 
						}
						print"</quotes>";
					}	
				}
				else if ($num_rows_old == 1)
				{
					$row_old = mysql_result($result_old, 0, 'oldprod');
					print "<quotes name=\"Most Quoted Existing Product\">".$row_old."</quotes>";
				}
				
				else
				{
					print "<quotes name=\"Most Quoted Existing Product\">no product</quotes>";
				}
				
				print "</node>";
				return;	
			}
			
			case '6': //natoinal: Store with most and least quotes
			{
				//most
				$result_most = mysql_query("SELECT `stores`.store,  COUNT(`q`.`created_by`) as quote_num	
										FROM stores
										INNER JOIN system_users p
										ON stores.id = p.store_id
										INNER JOIN `quotes` q
										ON q.`created_by` = p.`id`
										where `q`.`date_created` between '$startDate' and '$endDate'
										group by(stores.id)
										HAVING COUNT(`q`.`created_by`) = 
										(
											SELECT COUNT(`q`.`created_by`) as quote_num	
											FROM stores
											INNER JOIN system_users p
											ON stores.id = p.store_id
											INNER JOIN `quotes` q
											ON q.`created_by` = p.`id`
											where `q`.`date_created` between '$startDate' and '$endDate'
											group by(stores.id)
											order by quote_num DESC limit 1
										);");
											
				$num_rows_most = mysql_num_rows($result_most);
				if($num_rows_most > 0)
				{
					while($row_most = mysql_fetch_assoc($result_most))
					{   
						print"<quotes name=\"Most Quotes\" stre = \"Store\" val=\"Quotes\">";               
						foreach ($row_most as $col => $val) 
						{
							print "<$col>".$val."</$col>"; 
						}
						print"</quotes>";
					}	
				}
				
				else
				{
					print "<quotes name=\"Most Quotes\" stre = \"Store\" val=\"Quotes\">no store</quotes>";
				}
				
				//least
				$result_least = mysql_query("SELECT `stores`.store,  COUNT(`q`.`created_by`) as quote_num	
										FROM stores
										INNER JOIN system_users p
										ON stores.id = p.store_id
										INNER JOIN `quotes` q
										ON q.`created_by` = p.`id`
										where `q`.`date_created` between '$startDate' and '$endDate'
										group by(stores.id)
										HAVING COUNT(`q`.`created_by`) = 
										(
											SELECT COUNT(`q`.`created_by`) as quote_num	
											FROM stores
											INNER JOIN system_users p
											ON stores.id = p.store_id
											INNER JOIN `quotes` q
											ON q.`created_by` = p.`id`
											where `q`.`date_created` between '$startDate' and '$endDate'
											group by(stores.id)
											order by quote_num ASC limit 1
											
										);");
											
				$num_rows_least = mysql_num_rows($result_least);
				if($num_rows_least > 0)
				{
					while($row_least = mysql_fetch_assoc($result_least))
					{   
						print"<quotes name=\"Least Quotes\" stre = \"Store\" val=\"Quotes\">";               
						foreach ($row_least as $col => $val) 
						{
							print "<$col>".$val."</$col>"; 
						}
						print"</quotes>";
					}	
				}
				
				else
				{
					print "<quotes name=\"Least Quotes\">no store</quotes>";
				}
				
				print "</node>";
				return;
			}
			
			case '7': //National: Store with highest and lowest Rand Value
			{
					//max
				$result_highest = mysql_query("SELECT stores.store, ROUND(SUM(p.price),2) as ttl
										FROM stores
										INNER JOIN system_users 
										ON stores.id = system_users.store_id										
										INNER JOIN quotes q
										ON q.created_by = system_users.id
										inner join quote_items s
										ON s.quote_id = q.id
										inner join new_products p
										ON p.id = new_product_id
										where `q`.`date_created` between '$startDate' and '$endDate'
										group by(`stores`.`id`)
										Having 	ROUND(SUM(p.price),2) = 
										(
											SELECT ROUND(SUM(p.price),2) as ttl
											FROM stores
											INNER JOIN system_users 
											ON stores.id = system_users.store_id										
											INNER JOIN quotes q
											ON q.created_by = system_users.id
											inner join quote_items s
											ON s.quote_id = q.id
											inner join new_products p
											ON p.id = new_product_id
											where `q`.`date_created` between '$startDate' and '$endDate'
											group by(`stores`.`id`)
											ORDER BY ttl DESC limit 1
										);");
				$num_rows_highest = mysql_num_rows($result_highest);
				if($num_rows_highest > 0)
				{
					while($row_highest = mysql_fetch_assoc($result_highest))
					{   
						print"<quotes name=\"Highest Value\" str=\"Store\" val=\"Value (R)\">";               
						foreach ($row_highest as $col => $val) 
						{
							print "<$col>".$val."</$col>"; 
						}
						print"</quotes>";
					}	
						
				
				}
			
				else
				{
					print "<quotes name=\"Highest Value\">no store</quotes>";
				}
				
				//min
				$result_lowest = mysql_query("SELECT stores.store, ROUND(SUM(p.price),2) as ttl
										FROM stores
										INNER JOIN system_users 
										ON stores.id = system_users.store_id										
										INNER JOIN quotes q
										ON q.created_by = system_users.id
										inner join quote_items s
										ON s.quote_id = q.id
										inner join new_products p
										ON p.id = new_product_id
										where `q`.`date_created` between '$startDate' and '$endDate'
										group by(`stores`.`id`)
										Having 	ROUND(SUM(p.price),2) = 
										(
											SELECT ROUND(SUM(p.price),2) as ttl
											FROM stores
											INNER JOIN system_users 
											ON stores.id = system_users.store_id										
											INNER JOIN quotes q
											ON q.created_by = system_users.id
											inner join quote_items s
											ON s.quote_id = q.id
											inner join new_products p
											ON p.id = new_product_id
											where `q`.`date_created` between '$startDate' and '$endDate'
											group by(`stores`.`id`)
											ORDER BY ttl ASC limit 1
										);");
				$num_rows_lowest = mysql_num_rows($result_lowest);
				if($num_rows_lowest > 0)
				{
					while($row_lowest = mysql_fetch_assoc($result_lowest))
					{   
						print"<quotes name=\"Lowest Value\" str=\"Store\" val=\"Value (R)\">";               
						foreach ($row_lowest as $col => $val) 
						{
							print "<$col>".$val."</$col>"; 
						}
						print"</quotes>";
					}	
						
				
				}
				else
				{
					print "<quotes name=\"Lowest Value\" str=\"Store\" val=\"Value (R)\">no store</quotes>";
				}
				print "</node>";
				return;	
			}
			
			case '8': //National: Store with highest and lowest avg % saved
			{
				
			}
			
			case '9': //National: Rep with most and least quotes

			{
				//most
				$result_most = mysql_query("SELECT `system_users`.`first`, `system_users`.`last`,  COUNT(`quotes`.`created_by`) as quote_num
										FROM `quotes` 
										INNER JOIN `system_users`
										ON `quotes`.`created_by` = `system_users`.`id`
										where `quotes`.`date_created` between '$startDate' and '$endDate' group by(`created_by`)
										HAVING count(`quotes`.`created_by`) = 
										(
										SELECT COUNT(`quotes`.`created_by`)
										FROM `quotes` 
										INNER JOIN `system_users`
										ON `quotes`.`created_by` = `system_users`.`id`
										where `quotes`.`date_created` between '$startDate' and '$endDate' group by(`created_by`) ORDER BY count(`created_by`) DESC limit 1
										);");
											
				$num_rows_most = mysql_num_rows($result_most);
				if($num_rows_most > 0)
				{
					while($row_most = mysql_fetch_assoc($result_most))
					{   
						print"<quotes name=\"Most Quotes\" fname = \"First Name\" lname = \"Last Name\" quotes = \"Quotes\">";               
						foreach ($row_most as $col => $val) 
						{
							print "<$col>".$val."</$col>"; 
						}
						print"</quotes>";
					}	
				}
				
				else
				{
					print "<quotes name=\"Most Quotes\">no rep</quotes>";
				}
				
				//least
				$result_least = mysql_query("SELECT `system_users`.`first`, `system_users`.`last`,  COUNT(`quotes`.`created_by`) as quote_num
										FROM `quotes` 
										INNER JOIN `system_users`
										ON `quotes`.`created_by` = `system_users`.`id`
										where `quotes`.`date_created` between '$startDate' and '$endDate' group by(`created_by`)
										HAVING count(`quotes`.`created_by`) = 
										(
											SELECT COUNT(`quotes`.`created_by`)
											FROM `quotes` 
											INNER JOIN `system_users`
											ON `quotes`.`created_by` = `system_users`.`id`
											where `quotes`.`date_created` between '$startDate' and '$endDate' group by(`created_by`) ORDER BY count(`created_by`) ASC 			 											limit 1
										);");
											
				$num_rows_least = mysql_num_rows($result_least);
				if($num_rows_least > 0)
				{
					while($row_least = mysql_fetch_assoc($result_least))
					{   
						print"<quotes name=\"Least Quotes\" fname = \"First Name\" lname = \"Last Name\" quotes = \"Quotes\">";               
						foreach ($row_least as $col => $val) 
						{
							print "<$col>".$val."</$col>"; 
						}
						print"</quotes>";
					}	
				}
				
				else
				{
					print "<quotes name=\"Least Quotes\">no rep</quotes>";
				}
				print "</node>";
				
				return;	
			}
			case '10': //national: rep with highest and lowest rand value
			{
				
				//max
				$result_highest = mysql_query("SELECT system_users.first, system_users.last, ROUND(SUM(p.price),2) as ttl
										FROM quotes 
										inner join `system_users`
										ON quotes.created_by = system_users.id
										inner join quote_items s
										ON s.quote_id = quotes.id
										inner join new_products p
										ON p.id = new_product_id
										where `quotes`.`date_created` between '$startDate' and '$endDate' group by (system_users.id)
										HAVING SUM(p.price) =
										(
										SELECT SUM(p.price)
										FROM quotes 
										inner join `system_users`
										ON quotes.created_by = system_users.id
										inner join quote_items s
										ON s.quote_id = quotes.id
										inner join new_products p
										ON p.id = new_product_id
										where `quotes`.`date_created` between '$startDate' and '$endDate' group by (system_users.id) ORDER BY SUM(p.price) DESC limit 1
										)
;");
				$num_rows_highest = mysql_num_rows($result_highest);
				if($num_rows_highest > 0)
				{
					while($row_highest = mysql_fetch_assoc($result_highest))
					{   
						print"<quotes name=\"Highest Value\" fname = \"First Name\" lname = \"Last Name\" val = \"Value (R)\">";               
						foreach ($row_highest as $col => $val) 
						{
							print "<$col>".$val."</$col>"; 
						}
						print"</quotes>";
					}	
						
				
				}
			
				else
				{
					print "<quotes name=\"Highest Value\">no rep</quotes>";
				}
				
				//min
				$result_lowest = mysql_query("SELECT system_users.first, system_users.last, ROUND(SUM(p.price),2) as ttl
										FROM quotes 
										inner join `system_users`
										ON quotes.created_by = system_users.id
										inner join quote_items s
										ON s.quote_id = quotes.id
										inner join new_products p
										ON p.id = new_product_id
										where `quotes`.`date_created` between '$startDate' and '$endDate' group by (system_users.id)
										HAVING SUM(p.price) =
										(
										SELECT SUM(p.price)
										FROM quotes 
										inner join `system_users`
										ON quotes.created_by = system_users.id
										inner join quote_items s
										ON s.quote_id = quotes.id
										inner join new_products p
										ON p.id = new_product_id
										where `quotes`.`date_created` between '$startDate' and '$endDate' group by (system_users.id) ORDER BY SUM(p.price) ASC limit 1
										)
;");
				$num_rows_lowest = mysql_num_rows($result_lowest);
				if($num_rows_lowest > 0)
				{
					while($row_lowest = mysql_fetch_assoc($result_lowest))
					{   
						print"<quotes name=\"Lowest Value\" fname = \"First Name\" lname = \"Last Name\" val = \"Value (R)\">";               
						foreach ($row_lowest as $col => $val) 
						{
							print "<$col>".$val."</$col>"; 
						}
						print"</quotes>";
					}	
						
				
				}
				else
				{
					print "<quotes name=\"Lowest Value\">no rep</quotes>";
				}
				
				print "</node>";
				return;
					
			}
			
			case '11': //national: rep with highest and lowest avg % saved
			{
				
			}
		}	
	
	}
}
mysql_close($pconn);
?>