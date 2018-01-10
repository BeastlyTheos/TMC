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

$matches = pairing_state_machine::create($teams);
echo "<h1>{$matches->count()}matches are:</h1>";
echo '<ul>';
while ($matches->count())
	{$m = $matches->pop();
	echo "<li>{$m->home->name} verses {$m->away->name}</li>";
	}
echo '</ul>';
}//end try
        catch (HTError $e)
        { printf($e.GetType() + "<br/>" + $e.Message +"<br/>" + $e.StackTrace); }
?>