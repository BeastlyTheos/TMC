<?php $debug = true;
require_once 'C:/xampp/vendor/autoload.php';
  $loader = new Twig_Loader_Filesystem('templates');
 $twig = new Twig_Environment($loader, array("debug"=>$debug));

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
	$s->goalsRatio = sprintf("%.0f", $r['gr'] *1000);
	$s->winningPercent = sprintf("%03.0f", $r['wp'] *1000);
	$standings[] = $s;
	}

$twig->display("view_standings.html", array("title"=>"standings for $contextName", "standings"=>$standings));
?>
