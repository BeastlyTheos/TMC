 <title>TMC</title>
<?php
include 'yoursql.php';
include 'TournamentFunctions.php';
$edition = $_GET['e'];
if(isset($_GET['r']))
	$round = $_GET['r'];
else
	$round = CupRoundOf($today);

$res = yoursql_query("select matches.id from matches inner join contexts  on matches.context = contexts.id where matches.id is not null and type = 'match' and edition = $edition and round = $round order by `o`");

if($res)
	{//$URL = "http://hattrick.org/goto.ashx?path=

	$ids = array();
	while ($r = $res->fetch_assoc())
		$ids[] = $r['id'];
	

	$URL = '/Club/Matches/Live.aspx?matchID=';
	$URL .= implode(',', $ids);
	$URL .= '&actionType=addMatch&SourceSystem=Hattrick';
	
	echo $URL;
	}
else
	echo "No matchIds for $edition";
	?>