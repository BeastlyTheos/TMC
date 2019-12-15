<?php $debug = true;
require_once 'vendor/autoload.php';
$loader = new Twig_Loader_Filesystem('templates');
$twig = new Twig_Environment($loader, array("debug"=>$debug));

include "yoursql.php";
include "TournamentFunctions.php";

$res = yoursql_query("select teams.id as id, teams.name as name, seed from teams join entrants on teams.id = entrants.id where edition = $edition");
$teams = $res->fetch_all(1);

$twig->display("view_entrants.html", array(
		"edition"=>$edition,
		"teams"=>$teams,
	)
);
?>
