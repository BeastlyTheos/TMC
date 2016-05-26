<?$db = 'tournament';
include 'yoursql.php';
include 'TournamentFunctions.php';
$edition = (int) $_GET['e'];
if(isset($_GET['r']))
	$round = (int) $_GET['r'];
else $round = CupRoundOf($today);
if ( CurrentRound > $round)
	$hasCompleted = true;
else $hasCompleted = false;
$res = yoursql_query("select id from contexts where edition = $edition and id in (select distinct(context) from matches where round = $round)");

echo "<title>edition $edition, round $round, </title>";

//echo "[u] Round ".$round-$context['startingOffset']."[/u]<br/>";

if($res->num_rows)
	while($r = $res->fetch_assoc())//if there is a result
		PrintMatchesByContext( $r['id'], $round);
else
	printf("No matches in round $round");

function HTClub($name, $id)
{return "$name [teamid=$id]";}

function PrintMatchesByContext( $contextId, $round)
{global $hasCompleted;
$context = yoursql_query("select * from contexts where id = $contextId")->fetch_assoc();
$matches = yoursql_query("call getMatchesByContext(${context['id']},$round)");

$neutral = $context['usesNeutralVenues'];
$columnHeader1 = $neutral? "Team": "Home";
$columnHeader2 = "Score";
$columnHeader3 = $neutral? "Team": "Away";
$columnHeader4 = $hasCompleted? "matchid": "suggested arena id";

echo '[table]<br/>';
printf("[tr][td colspan=%d]${context['name']}[/td][/tr]<br/>", $hasCompleted?4:3);
printf("[tr][th]%s[/th][th]%s[/th][th]%s[/th][th]%s[/th][/tr]<br/>",
	$columnHeader1, $columnHeader2, $columnHeader3, $columnHeader4);

while ($m = $matches->fetch_assoc()) //print all matches while in this context
	{$cell1 = HTClub($m['homeName'], $m['homeId']);
$cell2 = fnmatch('*forfit', $m['type'])? $m['type']: null === $m['homegoals']? ' __ - __ ': sprintf(' %.2d - %.2d ', $m['homegoals'], $m['awaygoals']);
$cell3 = 		'bye'==$m['type']? 'bye        ': HTClub($m['awayName'], $m['awayId']);
$cell4 = $m['matchId']? "[matchid=${m['matchId']}]": "${m['arena']}";

printf("[tr][td]%s[/td][td]%s[/td][td]%s[/td][td]%s[/td][/tr]<br>\n",
$cell1, $cell2, $cell3, $cell4);
}//end while there is a match to print

echo '[/table]<br/>';
}
?>