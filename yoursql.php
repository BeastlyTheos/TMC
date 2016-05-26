<?php //need to declare $db in main file
$host = "localhost";
$mysqlUser = "root";
$db = 'tournament';
$mysqlPW = "";
$sql = new mysqli($host, $mysqlUser, $mysqlPW, $db);
if($sql->connect_error)
	die ('Connect Error ('.$sql->connect_errno.') '.
	$sql->connect_error.'.  '.$sql->sqlstate);
mysqli_query($sql, "set Names 'utf8'");
$queryCounter = 0;

function yoursql_query($query, $act = "")
	{global $queryCounter;
	$queryCounter++;
//	echo "query $queryCounter: $query</br>";
global $sql;
$return = $sql->query($query);
	if(mysqli_errno($sql))
	throw new Exception("|".mysqli_error($sql).' '.mysqli_errno($sql).'.</br>'.$query.'<br>'.$act);

//		echo "|".mysqli_error($sql).' '.mysqli_errno($sql).'.</br>'.$query.'<br>'.$act;
		while ($sql->next_result());
	return $return;
	}
	
	//always returns row offset to beginning
function yoursql_print_sql_result($res)
{//create headings and row format
$fields = $res->fetch_fields();
$headings = "";
$rowFormat = "";
foreach ($fields  as $f)
	{$headings = "$headings<th>$f->name</th>";
	$rowFormat = "$rowFormat<td>%s</td>"; //this block could be changed to accomidate specific formatting for various types, like not showing all decimals in floats
	}//end foreach
$headings = "<tr>$headings</tr>";
$rowFormat = "<tr>$rowFormat</tr>";

//begin printing
//print header
printf("<table>\n");
printf("$headings\n");
foreach(mysqli_fetch_all($res) as $r)
	echo vsprintf($rowFormat, $r)."\n";
printf("</table>\n");
mysqli_data_seek($res,0);
}//end print sql result
?>