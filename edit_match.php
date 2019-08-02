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
$match = new Match($_POST);
	try {
		$match->save();
		header("Location: view_matches.php");
		}
	catch (Exception $e) 
		{var_dump($e);}
	}
else 
	{//get value of o
	if (isset($_GET["o"]))
		$o = $_GET["o"];
	else
		$o = null;
	$match = Match::getByO($o);
	}

$res = yoursql_query("select teams.id, teams.name from teams join standings on teams.id = standings.id where context = $context");
$teams = $res->fetch_all(1);
$teams[] = Team::getByID(0);

$twig->display("edit_match.html", array(
	"match"=>$match,
	"teams"=>$teams,
	)
);
?>
