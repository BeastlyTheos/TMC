<?php $debug = true;
require_once 'C:/xampp/vendor/autoload.php';
  $loader = new Twig_Loader_Filesystem('templates');
 $twig = new Twig_Environment($loader, array("debug"=>$debug));

include 'CHPPConnection.php';
include 'TournamentFunctions.php';
include 'team.php';
include 'match.php';
include 'pairing_state_machine.php'; //used for pairing_state_machine
$upcomingRound  = CurrentRound;
$context = $_GET['c'];

        try
        {
$res = yoursql_query("call getStandingsWithoutForfits($context)");
$numTeams = $res->num_rows;
$matches = new SplStack();

//load the teams from the sql result into the teams array
for ($i = 0; $i < $numTeams; $i++)
	{$r = $res->fetch_assoc();
	$teams[$i] = new Team($r['id'], $i, $r['name'], $r['gp'], $r['byes']);
	}

$matches = pairing_state_machine::create($teams, $context);
usort($matches, 'teamcmp');

echo "<h1>".count($matches)." matches are:</h1>";
echo '<ul>';
foreach ( $matches as $m )
	echo "<li>{$m->home->name} verses {$m->away->name}</li>";
echo '</ul>';

            //load $matches into sql
$sql->begin_transaction();
foreach ( $matches as $m )
	{//as for convention, insure that the home team is not a bye
	if (0 == $m->home->id)
		{
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

echo "<p>Fixtures saved successfully</p>";
}//end try
        catch (HTError $e)
        { printf($e.GetType() + "<br/>" + $e.Message +"<br/>" + $e.StackTrace); }

function teamcmp( $a, $b)
{return $a->home->rank - $b->home->rank;}
?>
