 <title>kicking bots </title>
<?php
include "CHPPConnection.php";

$teams = yoursql_query('select id, name, userid from teams where !isBot');

while ( $row = $teams->fetch())
	{$id = $row['id'];
	$name = $row['name'];
	$userid = $row['userid'];
	$team = $HT->getTeam($id);

	if ( $team->isbot() || $userid != $team->getUserId())
		{echo $name." is bot<br/>";
		yoursql_query("update teams set isBot = 1 where id = $id");
		}
	else
		echo $team->getTeamName()." is active<br/>";

	$name = $team->getTeamName();
	$regionId = $team->getRegionId();
	$arenaId = $team->getArenaId();

	yoursql_query("insert into teams set id=$id, name='$name', region=$regionId on duplicate key update name='$name', region=$regionId");
	yoursql_query("insert into arenas set id=$arenaId, region=$regionId, team = $id on duplicate key update region=$regionId");
	}
?>
