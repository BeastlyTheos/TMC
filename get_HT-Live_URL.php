<?php $debug = true;
require_once 'vendor/autoload.php';
  $loader = new Twig_Loader_Filesystem('templates');
 $twig = new Twig_Environment($loader, array("debug"=>$debug));

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
	while ($r = $res->fetch())
		$ids[] = $r['id'];
	

	$URL = '/Club/Matches/Live.aspx?matchID=';
	$URL .= implode(',', $ids);
	$URL .= '&actionType=addMatch&SourceSystem=Hattrick';
	
	}

$twig->display("get_HT-Live_URL.html", array('title'=>"HT-Live round $round", "URL"=>$URL));
	?>
