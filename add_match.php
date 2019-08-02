<?php $debug = true;
require_once 'C:/xampp/vendor/autoload.php';
  $loader = new Twig_Loader_Filesystem('templates');
 $twig = new Twig_Environment($loader, array("debug"=>$debug));

include "yoursql.php";
include "TournamentFunctions.php";
include "match.php";

//initialise the match object
if ($_POST)
	{//try to save the post data as a match
	$home = $_POST["home"];
	$away = $_POST["away"];
	$context = $_POST["context"];
	$round = $_POST["round"];
	try {
		if ($away)
			yoursql_query("call scheduleMatch($home, $away, $context, $round)");
		else
			yoursql_query("call scheduleBye($home, $context, $round)");
		msg("redirecting");
		header("Location: view_matches.php");
		}
	catch (Exception $e)
		{msg("Error saving new match: ".$e->getMessage());}
	$match = new Match($_POST);
	}
else
	$match = new Match([]);

$res = yoursql_query("select teams.id, teams.name from teams join standings on teams.id = standings.id where context = $context");
$teams = $res->fetch_all(1);
$teams[] = Team::getByID(0);

$twig->display("add_match.html", array(
	"match"=>$match,
	"teams"=>$teams,
	)
);
?>
