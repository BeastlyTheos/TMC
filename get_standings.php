<?php $debug = true;
require_once 'vendor/autoload.php';
$loader = new Twig_Loader_Filesystem('templates');
$twig = new Twig_Environment($loader, array("debug"=>$debug));
$twig->addFilter(
new Twig_Filter('sporting_percent', function ($num ) {
	if ( 1 > $num )
		return sprintf(" .%03.0d", 1000*$num);
	else
		return sprintf("%.0d", 1000*$num);
}
	)
);

include 'yoursql.php';
include "TournamentFunctions.php";

class standing {
	public $name;
	public $wins;
	public $losses;
	public $goalsFor;
	public $goalsAgainst;
	public $goalsRatio;
	public $winningPercent;
}


if (isset($_GET['c']))
	$context = $_GET['c'];
$contextName = yoursql_query("select name from contexts where id = $context")->fetch()[0];

$res = yoursql_query("call getStandingsWithForfits($context)");

$standings = array();
for($i = 1 ; $r = $res->fetch() ; $i++) {
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

$twig->display("get_standings.html", array(
		"title"=>"standings for $contextName",
		"context"=>$context,
		"standings"=>$standings
	)
);
?>
