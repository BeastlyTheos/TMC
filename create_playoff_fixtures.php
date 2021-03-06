<?php $debug = true;
require_once 'vendor/autoload.php';
$loader = new Twig_Loader_Filesystem('templates');
$twig = new Twig_Environment($loader, array("debug"=>$debug));

include 'CHPPConnection.php';
include 'TournamentFunctions.php';
include 'team.php';
include 'match.php';
include 'pairing_state_machine.php'; //used for pairing_state_machine
$upcomingRound  = CurrentRound;
$context = $_GET['c'];
include_once "sortTeams.php";

$context = 20;

try {
	$teams = getStandings($context, false);
	$numTeams = count($teams);
	$matches = new SplStack();

	if ( 0 != $numTeams ) {
		//load the teams from the sql result into the teams array
		for ($i = 0; $i < $numTeams; $i++)
			$teams[$i]->rank = $i;

		$matches = pairing_state_machine::create($teams, $context);
		usort($matches, 'compareMatches');

		//load $matches into sql
		$sql->beginTransaction();
		foreach ( $matches as $m ) {
			//as for convention, insure that the home team is not a bye
			if (0 == $m->home->id) {
				$temp = $m->home;
				$m->home = $m->away;
				$m->away = $temp;
			}

			if (0 == $m->away->id)
				yoursql_query("call scheduleBye( " . $m->home->id . ", $context, $upcomingRound)");
			else
				yoursql_query('call ScheduleMatch('.$m->home->id.', '.$m->away->id.", $context, $upcomingRound)");
		}//END EACH MATCH
		$sql->commit();
	}

	$twig->display( "create_fixtures.html", array(
			"teams"=>$teams,
			"matches"=>$matches
		)
	);
}//end try
catch (HTError $e) {
	printf($e.GetType() + "<br/>" + $e.Message +"<br/>" + $e.StackTrace);
}
?>
