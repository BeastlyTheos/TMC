<?php $debug = true;
require_once 'vendor/autoload.php';
$loader = new Twig_Loader_Filesystem('templates');
$twig = new Twig_Environment($loader, array("debug"=>$debug));

include 'CHPPConnection.php';

$teamIdsEliminated = array();
$teamNamesEliminated = array();
$teamNamesInCup = array();

$res = yoursql_query("select id from standings where inNationalCup");

if($res) {
	while($r = $res->fetch()) {
		$team = $HT->getTeam($r['id']);
		if( ! $team->isInCup()) {
			$teamIdsEliminated[] = $team->getTeamId();
			$teamNamesEliminated[] = $team->getTeamName();
		} else
			$teamNamesInCup[] = $team->getTeamName();
	}//end while there is a team left in the SQL result

	if(count($teamIdsEliminated)) //if there was a team that exited the cup
		yoursql_query("update standings set inNationalCup = 0 where id in (".implode(", ", $teamIdsEliminated).")");
}//end if there are teams

$twig->display("check_cup_eliminations.html", array("teamsEliminated"=>$teamNamesEliminated, "teamsInCup"=>$teamNamesInCup));
?>
