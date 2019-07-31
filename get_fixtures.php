<?php $debug = true;
require_once 'C:/xampp/vendor/autoload.php';
  $loader = new Twig_Loader_Filesystem('templates');
 $twig = new Twig_Environment($loader, array("debug"=>$debug));

include 'yoursql.php';
include 'TournamentFunctions.php';

$editionID = (int) $_GET['e'];
$edition = yoursql_query("select * from editions where id = $editionID")->fetch_assoc();
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
		$row [] = sprintf("%32s [teamid=%d]", $m["homeName"], $m["homeId"]);

		//load score if the match has happened
		if ( $hasCompleted )
			if ( "match" == $m["type"] )
				$row[] = sprintf("%2d - %2d", $m["homegoals"], $m["awaygoals"]);
			else
				$row[] = "__ - __";

		//load the names of the away teams
		if ( $m["awayId"] )
			$row [] = sprintf("%32s [teamid=%d]", $m["awayName"], $m["awayId"]);
		else
			$row [] = '[bye]';

		//load either the match id or the areana id
		if ( $hasCompleted )
			{
			if ( "match" == $m["type"] )
				$row[] = sprintf("[matchid=%d]", $m["matchId"]);
			else if ( "homeForfit" == $m["type"] )
				$row[] = "home forfit";
			else if ( "awayForfit" == $m["type"] )
				$row[] = "away forfit";
			}//end if has completed
		else if ( $usesNeutralVenues )
			$row[] = $m["arena"];

		$rows[] = $row;
		}//end for each match

	//add row to table
	$tables[$context["name"]]["title"] = $context["name"];
	$tables[$context["name"]]["data"] = $rows;
	$tables[$context["name"]]["headers"] = $headers;
	}//end iterating through contexts


$twig->display("get_fixtures.html", array(
	"title"=>"Round $round Fixtures",
	"tables"=>$tables,
	"forumThread"=>$edition['forumThread'],
	"standingsPost"=>$edition['standingsPost']
	)
	);
?>
