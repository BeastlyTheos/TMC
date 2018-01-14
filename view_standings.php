<?php $debug = true;
require_once 'C:/xampp/vendor/autoload.php';
  $loader = new Twig_Loader_Filesystem('templates');
 $twig = new Twig_Environment($loader, array("debug"=>$debug));
$twig->addFilter(
	new Twig_Filter('sporting_percent', function ($num )
		{
		if ( 1 > $num )
			return sprintf(" .%03.0d", 1000*$num);
		else
			return sprintf("%.0d", 1000*$num);
		}
	)
);

include 'yoursql.php';

class standing
	{public $rank;
	public $name;
	public $wins;
	public $losses;
	public $goalsFor;
	public $goalsAgainst;
	public $goalsRatio;
	public $winningPercent;
	}


$context = $_GET['c'];
$contextName = yoursql_query("select name from contexts where id = $context")->fetch_row()[0];

$res = yoursql_query("call getStandingsWithForfits($context)");

$standings = array();
for($i = 1 ; $r = $res->fetch_assoc() ; $i++)
	{
	$s = new standing();
	$s->rank = $i;
	$s->name = $r['name'];
	$s->wins = $r['w'];
	$s->losses = $r['l'];
	$s->goalsFor = $r['gf'];
	$s->goalsAgainst = $r['ga'];
	$s->goalsRatio = $r['gr'];
	$s->winningPercent = $r['wp'];

	$standings[] = $s;
	}

$twig->display("view_standings.html", array(
	"title"=>"standings for $contextName",
	"standings"=>$standings
	)
);
?>
