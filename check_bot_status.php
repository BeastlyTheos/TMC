 <title>kicking bots </title>
<?php
include "CHPPConnection.php";

$ids = yoursql_query('select id from teams');

while ( $row = $ids->fetch_row())
	{$id = $row[0];
	$team = $HT->getTeam($id);

	if ( $team->isbot())
		{echo $team->getTeamName()." is bot<br/>";
		yoursql_query("update teams set isBot = 1 where id = $id");
		}
	else
		echo $team->getTeamName()." is active<br/>";
		}
?>
