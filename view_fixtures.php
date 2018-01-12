<?php $debug = true;
require_once 'C:/xampp/vendor/autoload.php';
  $loader = new Twig_Loader_Filesystem('templates');
 $twig = new Twig_Environment($loader, array("debug"=>$debug));

include 'yoursql.php';
include 'TournamentFunctions.php';

$editionID = (int) $_GET['e'];
if(isset($_GET['r']))
	$round = (int) $_GET['r'];
else $round = CupRoundOf($today);
if ( CurrentRound > $round)
	$hasCompleted = true;
else
	$hasCompleted = false;
$contexts = yoursql_query("select * from contexts where edition = $editionID and id in (select distinct(context) from matches where round = $round)");
$tables = array();


while ( $context = $contexts->fetch_assoc() )
	{
		$matches = yoursql_query("call getMatchesByContext( {$context['id']}, $round)");
	$usesNeutralVenues = $context['usesNeutralVenues'];
	$rows = array();

	$headers = array();
	if ( $usesNeutralVenues )
		$headers[] = "Team1";
	else
		$headers[] = "Home";
	if ( $hasCompleted )
		$headers[] = "Score";
	if ( $usesNeutralVenues )
		$headers[] = "Team2";
	else
		$headers[] = "away";
	if ( $hasCompleted )
		$headers[] = "Match IDs";
	else if ( $usesNeutralVenues )
		$headers[] = "Suggested Arena ID";

	//load table data
	foreach ( $matches as $m )
		{
		$row = array();

		//load the name of the home team
		$row [] = $m["homeName"];

		//load score if the match has happened
		if ( $hasCompleted )
			$row[] = sprintf("%d %d", $m["homegoals"], $m["awaygoals"]);

		//load the names of the away teams
		$row[] = $m["awayName"];

		//load either the match id or the areana id
		if ( $hasCompleted )
			$row[] = $m["matchId"];
		else if ( $usesNeutralVenues )
			$row[] = $m["arena"];

		$rows[] = $row;
		}//end for each match

	//add row to table
	$tables[$context["name"]] = $rows;
	}//end iterating through contexts

var_dump($tables);
?>
