<title>UpdateNames</title>
<?php
include 'CHPPConnection.php';
include 'TournamentFunctions.php';
$res = yoursql_query('select id from teams');

echo 'remember to change $start of season in the functions file';
$j=88881;
while ($j-->0&& $r = $res->fetch_assoc())
	{yoursql_query('update teams set name = "'.$HT->getTeam($r['id'])->getTeamName().'" where id = '.$r['id']);
	}
?>