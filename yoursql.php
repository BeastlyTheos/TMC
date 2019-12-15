<?php
require "local_variables.php";

try {
	$sql = new PDO("mysql:host=$mysql_host;dbname=$db", $mysql_user, $mysql_pw);
	// set the PDO error mode to exception
	$sql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
	echo "Connection failed: " . $e->getMessage();
}
$sql->query("set Names 'utf8'");

function yoursql_query($query, $act = "") {
	global $sql;
	try {
		return $sql->query($query);
	} catch (PDOException $e) {
		$info = $sql->errorInfo();
		printf("<title>MySQL Error %d</title>\n".
			"<p>%s</p>\n".
			"<p>From query: %s</p>",
			$info[1], $info[2], $query);
		throw $e;
	}

	return;
}

//always returns row offset to beginning
function yoursql_print_sql_result($res) {
	//create headings and row format
	$fields = $res->fetch_fields();
	$headings = "";
	$rowFormat = "";
	foreach ($fields  as $f) {
		$headings = "$headings<th>$f->name</th>";
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