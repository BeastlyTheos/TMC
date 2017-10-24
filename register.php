 <title>register</title>
<?php
include "CHPPConnection.php";
include 'TournamentFunctions.php';


if ( isset($_GET["context"]))
	$context = $_GET["context"];
else
	$context = null;

if ( isset($_GET["edition"]))
	$edition = $_GET["edition"];
else
	$edition = null;

if ( isset($_GET["id"]) && isset($_GET["seed"]) )
	{$id = $_GET["id"];
	$seed = $_GET["seed"];
	$team = $HT->getTeam($id);
	$name = $team->getTeamName();
	$regionId = $team->getRegionId();
	$arenaId = $team->getArenaId();

	yoursql_query("insert into teams set id=$id, name='$name', region=$regionId on duplicate key update name='$name', region=$regionId");
	yoursql_query("insert into entrants set id = $id, edition=$edition, seed=$seed");
	yoursql_query("insert into standings set id=$id, context=$context, seed =$seed");
	yoursql_query("insert into arenas set id=$arenaId, region=$regionId, team = $id on duplicate key update region=$regionId");

	echo "<p>registered ".$_GET["id"]. "</p>";
	}//end registering team
	?>

<form action="register.php" method="get">
	<input title="id" name="id" type="numeric" autofocus/>
	<input title="seed" name="seed" type="numeric"/>
	<input type="submit"/>
	<input title="edition" name="edition" type="numeric" value="<?php echo $edition?>"/>
	<input title="context" name="context" type="numeric" value="<?php echo $context?>"/>
</form>