<?php $debug = true;
require_once 'vendor/autoload.php';
  $loader = new Twig_Loader_Filesystem('templates');
 $twig = new Twig_Environment($loader, array("debug"=>$debug));

include "CHPPConnection.php";
include 'TournamentFunctions.php';


$registeredTeamId = null;

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

	$registeredTeamId = $_GET["id"];
	}//end registering team

$twig->display("register.html", array("registeredTeamId"=>$registeredTeamId, "edition"=>$edition, "context"=>$context));
	?>
